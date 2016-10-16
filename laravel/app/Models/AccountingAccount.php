<?php
namespace App\Models;

use App\Models\Entity;

//	protected $with = array("transactions");

// TODO: Ta hänsyn till accounting_period

class AccountingAccount extends Entity
{
	protected $type = "accounting_account";
	protected $join = "accounting_account";
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
		"account_number" => [
			"column" => "accounting_account.account_number",
			"select" => "accounting_account.account_number",
		],
		"accounting_period" => [
			"column" => "accounting_account.accounting_period",
			"select" => "accounting_account.accounting_period",
		],
	];
	protected $sort = ["account_number", "asc"];

	/**
	 *
	 */
	public function _list($filters = [])
	{
		// Preprocessing (join or type and sorting)
		$this->_preprocessFilters($filters);

		// Build base query
		$query = $this->_buildLoadQuery();

		// Get account balance
		$query = $query->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("entity.entity_id")
			->selectRaw("COALESCE(SUM(amount), 0) AS balance");

		// Go through filters
		foreach($filters as $id => $filter)
		{
			// Filter on accounting period
			if("accountingperiod" == $id)
			{
				$query = $query
					->leftJoin("accounting_period", "accounting_period.entity_id", "=", "accounting_account.accounting_period")
					->where("accounting_period.name", $filter[0], $filter[1]);
				unset($filters[$id]);
			}
			// Filter on account balance
			else if("balance" == $id)
			{
				$query = $query->having("balance", $filter[0], $filter[1]);
				unset($filters[$id]);
			}
			// Filter on number of transactions
			else if("transactions" == $id)
			{
				$query = $query->selectRaw("COUNT(accounting_transaction.entity_id) AS num_transactions");
				$query = $query->having("num_transactions", $filter[0], $filter[1]);
				unset($filters[$id]);
			}
/*
			// Filter on account_number
			else if("account_number" == $id)
			{
				$query = $query->where("accounting_account.account_number", $filter[0], $filter[1]);
				unset($filters[$id]);
			}
*/
			// Pagination
			else if("per_page" == $id)
			{
				$this->pagination = $filter;
				unset($filters[$id]);
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

		// Get result from database
		$data = $query->get();

		// Get ingoing balance
		foreach($data as &$row)
		{
			// Instruction with number 0 is always ingoing balance
			$ingoing = AccountingInstruction::load(
				[
					["instruction_number", "=", 0]
				]
			);
/*
			echo "<pre>a";
			print_r($ingoing);
			die();
			$row->balance_in = 0;
*/
			if(empty($ingoing))
			{
				continue;
			}

			// TODO: Vänt rätt på kredit/debet
			if($row->account_number >= 3000 && $row->account_number <= 8311)
			{
				$row->balance *= -1;
			}
			foreach($ingoing["transactions"] as $balance)
			{
				if($balance->account_number == $row->account_number)
				{
					$row->balance_in = $balance->balance;
				}
			}
		}
		unset($row);

		/*
		foreach($data as &$row)
		{
//			if(file_exists())
			{
				$row->files = "meep";
			}
		}
		*/

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
/*
	public static function loadByAccountNumber($account_number, $show_deleted = false)
	{
		return (new static())->_loadByAccountNumber($account_number, $show_deleted);
	}
*/

	/**
	 *
	 */
	public function _load($filters, $show_deleted = false)
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Go through filters
		foreach($filters as $filter)
		{
			// Filter on accounting period
			if("accountingperiod" == $filter[0])
			{
				$query = $query
					->leftJoin("accounting_period", "accounting_period.entity_id", "=", "accounting_account.accounting_period")
					->where("accounting_period.name", $filter[1], $filter[2]);
			}
			// Filter on entity_id
			else if("entity_id" == $filter[0])
			{
				$query = $query->where("entity.entity_id", $filter[1], $filter[2]);
			}
			// Filter on account_number
			else if("account_number" == $filter[0])
			{
				$query = $query->where("accounting_account.account_number", $filter[1], $filter[2]);
			}
		}

		// Apply standard filters like entity_id, relations, etc
		$query = $this->_applyFilter($query, $filters);

		// Calculate sum of transactions
		$query = $query
			->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("entity.entity_id")
			->selectRaw("SUM(amount) AS balance");

		// Return account
		return (array)$query->first();
	}
}