<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Libraries\Invoice as InvoiceExporter;

use DB;

use App\Models\Invoice;

class InvoiceController extends Controller
{
	/**
	 *
	 */
	function list(Request $request)
	{
		return Invoice::list();
	}

	/**
	 * @todo Error handling: We need to check that all queries were executed
	 */
	function create(Request $request)
	{
		$json = $request->json()->all();

		// Check / generate invoice number
		if(!empty($json["invoice_number"]))
		{
			// If a invoice number is specified we need to check that it is not in conflict with an existing one
			if($this->_invoiceNumberIsExisting($json["invoice_number"]))
			{
				return ["error" => "The specified invoice number does already exist"];
			}
			else
			{
				$invoice_number = $json["invoice_number"];
			}
		}
		else
		{
			// If no invoice number is specified, we need to generate a new one
			$invoice_number = $this->_getNextInvoiceNumber();
		}

		// Insert into entity
		$entity_id = DB::table("entity")->insertGetId([
			"type"        => "invoice",
			"description" => $json["description"] ?? null,
			"title"       => $json["title"]       ?? null,
			"created_at"  => date("c"),
		]);

		// Insert invoice
		DB::table("invoice")->insert([
			"entity_id"      => $entity_id,
			"invoice_number" => $invoice_number,
			"date_invoice"   => $json["date_invoice"]   ?? null, // A invoice number of null means the invoice have no assigned invoice number yet and therefore is temporary
			"conditions"     => $json["conditions"]     ?? 30, // TODO: Should be read from a configuration file?
			"our_reference"  => $json["our_reference"]  ?? null,
			"your_reference" => $json["your_reference"] ?? null,
			"address"        => $json["address"]        ?? null,
			"status"         => $json["status"]         ?? "created",
		]);

		// Insert posts
		if(!empty($json["posts"]))
		{
			foreach($json["posts"] as $i => $post)
			{
				DB::table("invoice_post")->insert([
					"entity_id" => $entity_id,
					"weight"    => $post["weight"] ?? ($i * 10),
					"type"      => $post["type"]   ?? "article",
					"title"     => $post["title"]  ?? "",
					"price"     => $post["price"]  ?? 0,
					"vat"       => $post["vat"]    ?? null,
					"amount"    => $post["amount"] ?? 1,
					"unit"      => $post["unit"]   ?? "st",
				]);
			}
		}

		// Return the id of the inserted invoice
		// TODO: Return invoice object
		return ["invoice_created" => $entity_id];
	}

	/**
	 * @todo Accounting period
	 * @todo Behörighetskontroll
	 */
	function read(Request $request, $invoice_number)
	{
		// Load the invoice
		$invoice = Invoice::loadByInvoiceNumber($invoice_number);

		// Generate an error if there is no such invoice
		if(false === $invoice)
		{
			return Response()->json([
				"message" => "No invoice with specified invoice number in the selected accounting period",
			], 404);
		}
		else
		{
			return $invoice;
		}
	}

	/**
	 * @todo Not implemented yet
	 */
	function update(Request $request, $id)
	{
		return ["error" => "not implemented"];
	}

	/**
	 * Delete an invoice
	 */
	function delete(Request $request, $id)
	{
		$entity = Entity::delete($id);
	}

	/**
	 * @todo Not implemented yet
	 * @todo Should store files in another directory
	 * @todo No hardcoded id
	 */
	function export(Request $request, $invoice_number)
	{
		// Load invoice
		$invoice = Invoice::loadByInvoiceNumber($invoice_number);

		// Calculate metadata
		$invoiceExporter = new InvoiceExporter();
		$invoice = $invoiceExporter->CalculateMetadata($invoice);

		// Generate ODT
		$invoice["currency"] = "SEK";
		$invoiceExporter->Generate($invoice);

		// Send the file to browser
		header("Content-type: application/vnd.oasis.opendocument.text");
		echo file_get_contents("/vagrant/temp/invoice.odt");

		// Convert to PDF
//		$invoiceExporter->exportPdf();
	}

	/**
	 * TODO: Calculate next invoice number
	 */
	function _getNextInvoiceNumber()
	{
		return uniqid();
	}

	/**
	 * Returns true if the specified invoice number exists in the database
	 */
	function _invoiceNumberIsExisting($invoice_number)
	{
		$row = DB::table("invoice")
			->where("invoice_number", $invoice_number)
			->first();
		return $row ? true : false;
	}
}