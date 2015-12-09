<?php
namespace App\Models;

use App\Models\Entity;
use DB;

/**
 *
 */
class Invoice extends Entity
{
	protected $join = "invoice";
	protected $columns = [
		"entity.entity_id",
		"DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title",
		"entity.description",
		"invoice.invoice_number",
		"invoice.date_invoice",
		"invoice.conditions",
		"invoice.our_reference",
		"invoice.your_reference",
		"invoice.address",
		"invoice.status",
		"invoice.currency"
	];
	protected $sort = ["invoice_number", "desc"];

	/**
	 * Same as above, but called non-statically
	 */
	public function _list($filter = null)
	{
		// Build base query
		$query = $this->_buildLoadQuery()

			// Calculate total of invoice
			->leftJoin("invoice_post", "invoice_post.entity_id", "=", "entity.entity_id")
			->groupBy("entity.entity_id")
			->selectRaw("SUM(price * amount) as _total");

		// Get result
		$invoices = $query->get();

		// Go through invoices and calculate metadata
		foreach($invoices as &$invoice)
		{
			// Calculate expiry date
			$invoice->date_expiry = $this->_calculateExpiryDate($invoice);
		}

		return $invoices;
	}

	/**
	 *
	 */
	public static function loadByInvoiceNumber($invoice_number, $show_deleted = false)
	{
		return (new static())->loadByInvoiceNumber2($invoice_number, $show_deleted);
	}

	/**
	 *
	 */
	public function loadByInvoiceNumber2($invoice_number, $show_deleted = false)
	{
		// Build base query
		$query = DB::table("entity")
			->join("invoice", "invoice.entity_id", "=", "entity.entity_id")
			->where("invoice.invoice_number", "=", $invoice_number);

		// Show deleted entities or not?
		if($show_deleted === true)
		{
			// Include the deleted_at column in output only when we show deleted content
			$this->columns[] = "entity.deleted_at";
		}
		else
		{
			// The deleted_at should be null, which means it is not yet deleted
			$query = $query->whereNull("entity.deleted_at");
		}

		// Execute the query and get result
		foreach($this->columns as $column)
		{
			$query = $query->selectRaw($column);
		}

		// Get result
		$invoice = (array)$query->first();

		// Generate an error if there is no such invoice
		if(empty($invoice))
		{
			return false;
		}
		else
		{
			// Load invoice posts
			$invoice["posts"] = DB::table("invoice_post")
				->select("id", "weight", "type", "title", "price", "amount", "unit", "vat")
				->where("entity_id", "=", $invoice["entity_id"])
				->get();

			// TODO: Fulhack: Convert to array
			$invoice["posts"] = json_decode(json_encode($invoice["posts"]), true);

			// Calculate totals
			$invoice["_total"] = 0;
			foreach($invoice["posts"] as &$post)
			{
				$post["_total"] = $post["price"] * $post["amount"];
				$invoice["_total"] += $post["_total"];
			}

			// Calculate expiry date
			$invoice["date_expiry"] = date("Y-m-d", strtotime($invoice["date_invoice"] . "+{$invoice["conditions"]}days"));

			// Return invoice
			return $invoice;
		}
	}

	/**
	 * Calculate expiry date of an invoice
	 */
	function _calculateExpiryDate($invoice)
	{
		return date("Y-m-d", strtotime($invoice->date_invoice . "+{$invoice->conditions}days"));
	}
}