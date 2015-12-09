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

class EconomyAccount
{
	/**
	 * Returns a list of accounts
	 */
	function list(Request $request)
	{
		return DB::table("entity")
			->join("accounting_account", "accounting_account.entity_id", "=", "entity.entity_id")
			->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("entity.entity_id")
			->select("entity.*", "accounting_account.*", DB::raw("SUM(amount) AS balance"))
			->get();
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		return [
			"status" => "created",
			"account_number" => $request->input("account_number"),
			"title" => $request->input("title"),
			"description" => $request->input("description"),
		];
	}

	/**
	 * Returns an single account
	 */
	function read(Request $request, $id)
	{
		// Get the account
		$data = DB::table("entity")
			->join("accounting_account", "accounting_account.entity_id", "=", "entity.entity_id")
			->leftJoin("accounting_transaction", "accounting_account.entity_id", "=", "accounting_transaction.accounting_account")
			->groupBy("entity.entity_id")
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

	/**
	 *
	 */
	function update(Request $request, $id)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function delete(Request $request, $id)
	{
		return ['error' => 'not implemented'];
	}
}