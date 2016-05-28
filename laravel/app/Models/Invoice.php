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
		"entity.entity_id"          => "entity.entity_id",
		"entity.created_at"         => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"         => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"              => "entity.title",
		"entity.description"        => "entity.description",
		"invoice.invoice_number"    => "invoice.invoice_number",
		"invoice.date_invoice"      => "invoice.date_invoice",
		"invoice.conditions"        => "invoice.conditions",
		"invoice.our_reference"     => "invoice.our_reference",
		"invoice.your_reference"    => "invoice.your_reference",
		"invoice.address"           => "invoice.address",
		"invoice.status"            => "invoice.status",
		"invoice.currency"          => "invoice.currency",
		"invoice.accounting_period" => "invoice.accounting_period",
	];
	protected $sort = ["invoice_number", "desc"];

	/**
	 * Same as above, but called non-statically
	 */
	public function _list($filters = [])
	{
		// Build base query
		$query = $this->_buildLoadQuery()

			// Calculate total of invoice
			->leftJoin("invoice_post", "invoice_post.entity_id", "=", "entity.entity_id")
			->groupBy("entity.entity_id")
			->selectRaw("SUM(price * amount) as _total");

		// Go through filters
		foreach($filters as $filter)
		{
			// Filter on accounting period
			if("accountingperiod" == $filter[0])
			{
				$query = $query
					->leftJoin("accounting_period", "accounting_period.entity_id", "=", "invoice.accounting_period")
					->where("accounting_period.name", $filter[1], $filter[2]);
			}
		}

		// Paginate
		$per_page = 10; // TODO
		$query->paginate($per_page);

		// Get result
		$invoices = $query->get();

		// Go through invoices and calculate metadata
		foreach($invoices as &$invoice)
		{
			// Calculate expiry date
			$invoice->date_expiry = $this->_calculateExpiryDate($invoice);
		}

		return [
			"data"  => $invoices,
			"count" => $query->count(),
		];
	}

	/**
	 *
	 */
	public static function loadByInvoiceNumber($invoice_number, $show_deleted = false)
	{
		return (new static())->_loadByInvoiceNumber($invoice_number, $show_deleted);
	}

	/**
	 *
	 */
	public function _loadByInvoiceNumber($invoice_number)
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Filter on invoice number
		$query = $query->where("invoice.invoice_number", "=", $invoice_number);

		// Get result
		$invoice = (array)$query->first();

		// Generate an error if there is no such invoice
		if(empty($invoice))
		{
			return false;
		}

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

	/**
	 * Calculate expiry date of an invoice
	 */
	function _calculateExpiryDate($invoice)
	{
		return date("Y-m-d", strtotime($invoice->date_invoice . "+{$invoice->conditions}days"));
	}

	/**
	 *
	 */
	public function save()
	{
		$result = parent::save();

		// Insert posts
		if(!empty($this->posts))
		{
			foreach($this->posts as $i => $post)
			{
				DB::table("invoice_post")->insert([
					"entity_id" => $this->id,
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

		return $result;
	}
}
