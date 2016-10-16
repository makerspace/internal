<?php
namespace App\Models;

use App\Models\Entity;
use DB;

/**
 *
 */
class Invoice extends Entity
{
	protected $type = "invoice";
	protected $join = "invoice";
	protected $columns = [
		"entity_id" => [
			"column" => "entity.entity_id",
			"select" => "entity.entity_id",
		],
		"created_at" => [
			"column" => "entity.created_at",
			"select" => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ')",
		],
		"updated_at" => [
			"column" => "entity.updated_at",
			"select" => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ')",
		],
		"title" => [
			"column" => "entity.title",
			"select" => "entity.title",
		],
		"description" => [
			"column" => "entity.description",
			"select" => "entity.description",
		],
		"invoice_number" => [
			"column" => "invoice.invoice_number",
			"select" => "invoice.invoice_number",
		],
		"date_invoice" => [
			"column" => "invoice.date_invoice",
			"select" => "invoice.date_invoice",
		],
		"conditions" => [
			"column" => "invoice.conditions",
			"select" => "invoice.conditions",
		],
		"our_reference" => [
			"column" => "invoice.our_reference",
			"select" => "invoice.our_reference",
		],
		"your_reference" => [
			"column" => "invoice.your_reference",
			"select" => "invoice.your_reference",
		],
		"address" => [
			"column" => "invoice.address",
			"select" => "invoice.address",
		],
		"status" => [
			"column" => "invoice.status",
			"select" => "invoice.status",
		],
		"currency" => [
			"column" => "invoice.currency",
			"select" => "invoice.currency",
		],
		"accounting_period" => [
			"column" => "invoice.accounting_period",
			"select" => "invoice.accounting_period",
		],
	];
	protected $sort = ["invoice_number", "desc"];

	/**
	 * Same as above, but called non-statically
	 */
	public function _list($filters = [])
	{
		// Preprocessing (join or type and sorting)
		$this->_preprocessFilters($filters);

		// Build base query
		$query = $this->_buildLoadQuery()

		// Calculate total of invoice
		$query = $query
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
			// Pagination
			else if("per_page" == $filter[0])
			{
				$this->pagination = $filter[1];
			}
		}

		// Apply standard filters like entity_id, relations, etc
		$query = $this->_applyFilter($query, $filters);

		// Sort
		$query = $this->_applySorting($query);

		// Paginate
		if($this->pagination != null)
		{
			$query->paginate($this->pagination);
		}

		// Get result
		$data = $query->get();

		// Go through invoices and calculate metadata
		foreach($data as &$invoice)
		{
			// Calculate expiry date
			$invoice->date_expiry = $this->_calculateExpiryDate($invoice);
		}

		$result = [
			"data" => $data
		];

		if($this->pagination != null)
		{
			$result["total"]    = $query->count();
			$result["per_page"] = $this->pagination;
			$result["last_page"] = ceil($result["total"] / $result["per_page"]);
		}

		return $result;
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
					"entity_id" => $this->entity_id,
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