<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Libraries\EconomyParserSEB;

use App\Models\AccountingInstruction;

use App\Models\AccountingTransaction;
use App\Models\AccountingAccount;

use DB;

class Economy extends Controller
{
	function test(Request $request)
	{
//		$data = AccountingInstruction::find(50);
//		$data = AccountingTransaction::with("instructions")->find(99);
		$data = AccountingTransaction::find(99);
//		$data = $data->instruction->data;
//		echo "<pre>";
//print_r($data);
		return $data;
		

//		print_r(AccountingInstruction::with("Posts")->where("accounting_transaction.accounting_account_id", 1), true);

		return [];
	}

	function importSeb()
	{
		$s = new EconomyParserSEB();
		$data = file_get_contents("/vagrant/Bokföring/Kontohändelser.csv");
		$data = $s->Import($data);

		echo "<pre>";
		print_r($data);
	}
	
	/**
	 *
	 */
	function getInstructions(Request $request)
	{
		$per_page = $request->input("per_page");
		if(empty($per_page))
		{
			$per_page = 20;
		}

		// Use model
		$rows = DB::table("entity")
			->join("accounting_instruction", "accounting_instruction.entity_id", "=", "entity.entity_id")
			->select("entity.*", "accounting_instruction.*", DB::raw("(SELECT SUM(amount) FROM accounting_transaction WHERE amount > 0 AND accounting_instruction = entity.entity_id) AS balance"));

		// Filter on transaction.account_id
		if($request->input("account_id"))
		{
			$rows = $rows
				->join("accounting_transaction", "accounting_transaction.accounting_instruction", "=", "entity.entity_id")
				->join("accounting_account", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
				->where("accounting_account.account_number", "=", $request->input("account_id"));
		}

		// Paginate the data
		$data = $rows->paginate($per_page);

		// Return json array
		return $data->toJson();
	}

	function getInstruction(Request $request, $id)
	{
		// Use model
		$data = DB::table("entity")
			->join("accounting_instruction", "accounting_instruction.entity_id", "=", "entity.entity_id")

			// Join transactions table and calculate balance
			->leftJoin("accounting_transaction", "accounting_transaction.accounting_instruction", "=", "entity.entity_id")
			->groupBy("entity.entity_id")
			->where("accounting_transaction.amount", ">", 0)

			// Filter on instruction_number
			->where("accounting_instruction.instruction_number", "=", $id)

			// Return result
			->select("entity.*", "accounting_instruction.*", DB::raw("SUM(amount) AS balance"))
			->first();

		// Load the accounting instructions
		$data->transactions = DB::table("entity")
			->join("accounting_transaction", "accounting_transaction.entity_id", "=", "entity.entity_id")
			->join("accounting_account", "accounting_transaction.accounting_account", "=", "accounting_account.entity_id")
			->join("entity AS e2", "accounting_account.entity_id", "=", "e2.entity_id")
			->where("accounting_instruction", "=", $data->entity_id)
			->select("entity.title", "entity.description", "accounting_transaction.amount AS balance", "accounting_account.account_number", "e2.title AS account_title")
			->get();
			// TODO: What is external_id?

		return (array)$data;
	}

	/**
	 * Returns a list of accounts
	 */
	function getAccounts(Request $request)
	{
		return DB::table("entity")
			->join("accounting_account", "accounting_account.entity_id", "=", "entity.entity_id")
			->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("entity.entity_id")
			->select("entity.*", "accounting_account.*", DB::raw("SUM(amount) AS balance"))
			->get();
	}

	/**
	 * Returns an single account
	 */
	function accountRead(Request $request, $id)
	{
		// Get the account
		$data = DB::table("entity")
			->join("accounting_account", "accounting_account.entity_id", "=", "entity.entity_id")
			->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("accounting_account.entity_id")
			->where("accounting_account.account_number", $id)
			->select("entity.*", "accounting_account.*", DB::raw("SUM(amount) AS balance"))
			->first();

		// Generate an error if there is no account
		if(empty($data))
		{
			return Response()->json(array(
				// TODO: 404
				"message" => "No account with specified account_number",
			));
		}
		else
		{
			return (array)$data;
		}
	}

	function accountCreate(Request $request)
	{
		return [
			"status" => "created",
			"account_number" => $request->input("account_number"),
			"title" => $request->input("title"),
			"description" => $request->input("description"),
		];
	}

	function accountUpdate(Request $request)
	{
		return '{"status": "updated"}';
	}

	function accountDelete(Request $request)
	{
		return '{"status": "deleted"}';
	}

	function getTransactions(Request $request)
	{

	}

	function getTransaction(Request $request, $id)
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

	function getCostCenters(Request $request)
	{
		// TODO: DEBUG: Generate an "500 internal server error"
		sleep(2);
		$x = 9 / 0;

		return [];
	}

	function getCostCenter(Request $request, $id)
	{
		return [];
	}
}