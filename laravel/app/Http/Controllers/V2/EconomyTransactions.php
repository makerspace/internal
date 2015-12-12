<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;

class EconomyTransactions extends Controller
{
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

		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function create(Request $request, $accountingperiod)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function read(Request $request, $accountingperiod, $id)
	{
		// Get an list with id's of all accounting instructions which have a transaction to this account
		$instructions = DB::table("entity")
			->join("accounting_transaction", "accounting_transaction.entity_id", "=", "entity.entity_id")

			// Load the instruction
			->join("accounting_instruction", "accounting_instruction.entity_id", "=", "accounting_transaction.accounting_instruction")
			->join("entity AS ie", "ie.entity_id", "=", "accounting_instruction.entity_id")

			// Load the account
			->join("accounting_account", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->join("entity AS ae", "ae.entity_id", "=", "accounting_account.entity_id")

//			->where("accounting_transaction.accounting_account", $id)
			->where("accounting_account.account_number", $id)
			->select(
				"accounting_account.account_number",
				"ae.title AS account_name",
				"accounting_instruction.instruction_number",
				"ie.title AS instruction_title",
				"entity.title AS transaction_title",
//				"entity.description AS transaction_description",
				"accounting_instruction.accounting_date",
				"accounting_transaction.amount AS balance"
			);
//			->lists("accounting_instruction");

		return $instructions->get();
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
	}

	/**
	 *
	 */
	function update(Request $request, $accountingperiod, $id)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function delete(Request $request, $accountingperiod, $id)
	{
		return ['error' => 'not implemented'];
	}
}
