<?php
namespace App\Models;

use App\Models\Entity;

//	protected $with = array("transactions");

// TODO: Ta hänsyn till accounting_period

class AccountingAccount extends Entity
{
	protected $join = "accounting_account";
	protected $columns = [
		"entity.entity_id"                     => "entity.entity_id",
		"entity.created_at"                    => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"                    => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"                         => "entity.title",
		"entity.description"                   => "entity.description",
		"accounting_account.account_number"    => "accounting_account.account_number",
		"accounting_account.accounting_period" => "accounting_account.accounting_period",
	];
	protected $sort = ["accounting_account.account_number", "asc"];

	/**
	 *
	 */
	public function _list($filters = [])
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Get account balance
		$query = $query->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("entity.entity_id")
			->selectRaw("COALESCE(SUM(amount), 0) AS balance");

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
			// Filter on account balance
			else if("balance" == $filter[0])
			{
				$query = $query->having("balance", $filter[1], $filter[2]);
			}
			// Filter on number of transactions
			else if("transactions" == $filter[0])
			{
				$query = $query->selectRaw("COUNT(accounting_transaction.entity_id) AS num_transactions");
				$query = $query->having("num_transactions", $filter[1], $filter[2]);
			}
		}

		$data = $query->get();
		/*
		foreach($data as &$row)
		{
//			if(file_exists())
			{
				$row->files = "meep";
			}
		}
		*/

		// Return result
		return $data;
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

		// Calculate sum of transactions
		$query = $query
			->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("entity.entity_id")
			->selectRaw("SUM(amount) AS balance");

		// Return account
		return (array)$query->first();
	}
}