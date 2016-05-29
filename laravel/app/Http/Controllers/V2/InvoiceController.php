<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;
use App\Libraries\Invoice as InvoiceExporter;
use App\Models\Invoice;

use App\Traits\AccountingPeriod;
use App\Traits\Pagination;

class InvoiceController extends Controller
{
	use AccountingPeriod, Pagination;

	/**
	 *
	 */
	function list(Request $request, $accountingperiod)
	{
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}

		// Load data from datbase
		$result = Invoice::list([
			["per_page", $this->per_page($request)],
			["accountingperiod", "=", $accountingperiod],
		]);

		// Return json array
		return $result;
	}

	/**
	 * @todo Error handling: We need to check that all queries were executed
	 */
	function create(Request $request, $accountingperiod)
	{
		$json = $request->json()->all();

		// Get id of accounting period
		$accountingperiod_id = $this->_getAccountingPeriodId($accountingperiod);
		if(null === $accountingperiod_id)
		{
			return Response()->json([
				"message" => "Could not find the specified accounting period",
			], 404);
		}

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

		// Create new entity
		$entity = new Invoice;
		$entity->title             = $json["title"];
		$entity->description       = $json["description"]    ?? null;
		$entity->invoice_number    = $invoice_number;
		$entity->date_invoice      = $json["date_invoice"]   ?? null; // A invoice number of null means the invoice have no assigned invoice number yet and therefore is temporary
		$entity->conditions        = $json["conditions"]     ?? 30; // TODO: Should be read from a configuration file?
		$entity->our_reference     = $json["our_reference"]  ?? null;
		$entity->your_reference    = $json["your_reference"] ?? null;
		$entity->address           = $json["address"]        ?? null;
		$entity->status            = $json["status"]         ?? "created";
		$entity->accounting_period = $accountingperiod_id;
		$entity->posts             = $json["posts"];

		$result = $entity->save();

		// TODO: Standariezed output
		return [
			"status" => "created",
			"entity" => $entity->toArray(),
		];
	}

	/**
	 * @todo Accounting period
	 * @todo Behörighetskontroll
	 */
	function read(Request $request, $accountingperiod, $invoice_number)
	{
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}

		// Load the invoice
		$invoice = Invoice::loadByInvoiceNumber($invoice_number);

		// Generate an error if there is no such invoice
		if(false === $invoice)
		{
			return Response()->json([
				"message" => "No invoice with specified invoice number",
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
	function update(Request $request, $accountingperiod, $id)
	{
		return ["error" => "not implemented"];
	}

	/**
	 * Delete an invoice
	 */
	function delete(Request $request, $accountingperiod, $id)
	{
		$entity = Entity::delete($id);
	}

	/**
	 * @todo Not implemented yet
	 * @todo Should store files in another directory
	 * @todo No hardcoded id
	 */
	function export(Request $request, $accountingperiod, $invoice_number)
	{
		// Load invoice
		$invoice = Invoice::loadByInvoiceNumber($invoice_number);

		// Calculate metadata
		$invoiceExporter = new InvoiceExporter("/var/www/html/invoice/invoice.odt"); // TODO: Hardcoded path
		$invoice = $invoiceExporter->CalculateMetadata($invoice);

		// Generate ODT
		$invoice["currency"] = "SEK";
		$invoiceExporter->Generate($invoice);

		// Send the file to browser
		$invoiceExporter->Send();

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