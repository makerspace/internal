<?php

namespace App\Models;

use App\Models\Entity;
use DB;

/*
TODO

public function Transactions()
{
	return $this->hasMany('App\Models\AccountingTransaction');
}
*/


/**
 *
 */
class AccountingInstruction extends Entity
{
	protected $join = "accounting_instruction";
	protected $columns = [
		"entity.entity_id",
		"DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title",
		"entity.description",
		"accounting_instruction.instruction_number",
		"accounting_instruction.accounting_date",
		"accounting_instruction.accounting_category",
		"accounting_instruction.importer",
		"accounting_instruction.external_id",
		"accounting_instruction.external_date",
		"accounting_instruction.external_text",
		"accounting_instruction.external_data",
		"accounting_instruction.accounting_verification_series"
	];
//	protected $sort = ["verification_number", "desc"];

	/**
	 *
	 */
	public function _list($filter = null)
	{
		// Build base query
		$query = $this->_buildLoadQuery();

//		$filter["account_id"] = 1930;

		// Filter on transaction.account_id
		if($filter !== null && isset($filter["account_id"]))
		{
			$query = $query
				->join("accounting_transaction", "accounting_transaction.accounting_instruction", "=", "entity.entity_id")
				->join("accounting_account", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
				->where("accounting_account.account_number", "=", $filter["account_id"]);
		}

		// Get the balance
//		$query->selectRaw("(SELECT SUM(amount) FROM accounting_transaction WHERE amount > 0 AND accounting_instruction = entity.entity_id) AS balance");

		return $query->get();
	}

	/*
	 * Same as above, but called non-statically
	 */
	public function _load($instruction_number, $show_deleted = false)
	{
		// Load accounting instruction
		$data = $this->_buildLoadQuery()

			// Join transactions table and calculate balance
			->leftJoin("accounting_transaction", "accounting_transaction.accounting_instruction", "=", "entity.entity_id")
			->groupBy("entity.entity_id")
			->where("accounting_transaction.amount", ">", 0)
			->selectRaw("SUM(amount) AS balance")

			// Filter on instruction_number
			->where("accounting_instruction.instruction_number", "=", $instruction_number)

			// Return result
			->first();

		// Load the transactions
		$data->transactions = DB::table("entity")
			->join("accounting_transaction", "accounting_transaction.entity_id", "=", "entity.entity_id")
			->join("accounting_account", "accounting_transaction.accounting_account", "=", "accounting_account.entity_id")
			->join("entity AS e2", "accounting_account.entity_id", "=", "e2.entity_id")
			->where("accounting_instruction", "=", $data->entity_id)
			->select(
				"entity.title",
				"entity.description",
				"accounting_transaction.amount AS balance",
				"accounting_account.account_number",
				"e2.title AS account_title"
			)
			->get();
			// TODO: What is external_id?

		return (array)$data;
	}
}