<?php
namespace App\Models;
use DB;

/*
	protected $with = array('Account');
	public function Account()
	{
		return $this->belongsTo("App\Models\AccountingAccount", "account_id");
	}

	public function instruction()
	{
		return $this->belongsTo("App\Models\AccountingInstruction", "accounting_instruction_id");
	}
*/

class AccountingTransaction extends Entity
{
	protected $type = "accounting_transaction";
	protected $join = "accounting_transaction";
	protected $columns = [
		"entity.entity_id"                              => "entity.entity_id",
		"entity.created_at"                             => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"                             => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"                                  => "entity.title AS transaction_title",
		"entity.description"                            => "entity.description AS transaction_description",
		"accounting_transaction.accounting_instruction" => "accounting_transaction.accounting_instruction",
		"accounting_transaction.accounting_account"     => "accounting_transaction.accounting_account",
		"accounting_transaction.accounting_cost_center" => "accounting_transaction.accounting_cost_center",
		"accounting_transaction.amount"                 => "accounting_transaction.amount",
		"accounting_transaction.external_id"            => "accounting_transaction.external_id",
	];

	/**
	 *
	 */
	public function _list($filters = [])
	{
//		DB::statement(DB::raw('SET @runtot = 0'));
//		$query = $query

		// Build base query
		$this->columns[] = "ie.title AS instruction_title";
		$this->columns[] = "accounting_instruction.instruction_number";
//		$this->columns[] = "12356 AS balance";
//		$this->columns[] = "accounting_account.account_number";
//		$this->columns[] = "ae.title AS account_name";
		$this->columns[] = "accounting_instruction.accounting_date";

		$this->columns[] = "accounting_instruction.external_id AS extid";

		$query = $this->_buildLoadQuery()
//			->selectRaw("(@runtot :=  amount + @runtot) AS balance")

			// Load the instruction
			->leftJoin("accounting_instruction", "accounting_instruction.entity_id", "=", "accounting_transaction.accounting_instruction")
			->leftJoin("entity AS ie", "ie.entity_id", "=", "accounting_instruction.entity_id");

			// Load the account
//			->leftJoin("accounting_account", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
//			->leftJoin("entity AS ae", "ae.entity_id", "=", "accounting_account.entity_id");


		// Go through filters
		foreach($filters as $filter)
		{
			// Filter on accounting period
			if("accountingperiod" == $filter[0])
			{
				$query = $query
					->leftJoin("accounting_period", "accounting_period.entity_id", "=", "accounting_instruction.accounting_period")
					->where("accounting_period.name", $filter[1], $filter[2]);
			}
			// Filter on accounting period
			if("account_number" == $filter[0])
			{
				$query = $query
					->leftJoin("accounting_account", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
					->where("accounting_account.account_number", $filter[1], $filter[2]);
			}
			// Pagination
			else if("per_page" == $filter[0])
			{
				$this->pagination = $filter[1];
			}
		}

		// Sort by accounting date
		// TODO: This should not be used, sorting should be done via instruction number
		$query = $query->orderBy("accounting_instruction.accounting_date", "asc");

		// Paginate
		if($this->pagination != null)
		{
			$query->paginate($this->pagination);
		}

		// Get result
		$result = $query->get();
		$data = [];
		$balance = 0;
		foreach($result as $row)
		{
/*
			if(empty($row->instruction_number))
			{
				$row->instruction_number = "entity:".$row->entity_id;
			}
*/
			if(!empty($row->extid))
			{
				$dir = "/var/www/html/vouchers/{$row->extid}";
				if(file_exists($dir))
				{
					$row->files = "x";
				}
			}

			$balance += $row->amount;
			$row->balance = $balance;
			$data[] = $row;
		}

		$result = [
			"data" => $data
		];

		if($this->pagination != null)
		{
			$result["total"]     = $query->count();
			$result["per_page"]  = $this->pagination;
			$result["last_page"] = ceil($result["total"] / $result["per_page"]);
		}

		return $result;
	}
}

/*
		// Load the accounting instructions
		$data->instructions = DB::table("accounting_instruction")
			->leftJoin("accounting_transaction", "accounting_instruction.id", "=", "accounting_transaction.accounting_instruction_id")
			->groupBy("accounting_instruction.id")
			->where("accounting_transaction.amount", ">", 0)
			->select(
				"accounting_instruction.id",
				"accounting_instruction.accounting_date",
				"accounting_instruction.verification_number",
				"accounting_instruction.title",
				"accounting_instruction.description",
				DB::raw("SUM(accounting_transaction.amount) AS amount")
			)
			->get();
			*/
