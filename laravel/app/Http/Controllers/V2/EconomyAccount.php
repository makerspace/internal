<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\AccountingAccount;
use App\Traits\AccountingPeriod;

class EconomyAccount extends Controller
{
	use AccountingPeriod;

	/**
	 * Returns the masterledger
	 *
	 * The masterledger is basically a list of accounts, but we only show accounts with a balance != 0
	 */
	function masterledger(Request $request, $accountingperiod)
	{
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}

		// Return all account that have a balance not equal to 0
		return AccountingAccount::list(
			[
				["balance", "!=", 0],
				["accountingperiod", "=", $accountingperiod],
			]
		);
	}

	/**
	 * Returns a list of accounts
	 */
	function list(Request $request, $accountingperiod)
	{
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}

		// Return all account that have a balance not equal to 0
		return AccountingAccount::list([
			["accountingperiod", "=", $accountingperiod],
		]);
	}

	/**
	 * Create account
	 */
	function create(Request $request, $accountingperiod)
	{
		$json = $request->json()->all();

		// Get id of accounting period
		$accountingperiod_id = $this->_getAccountingPeriodId($accountingperiod);
		if(null === $accountingperiod_id)
		{
			return Response()->json([
				"message" => "Could not find the specified accounting period",
			], 404);
		}

		// We need to check that the provided account number is not in conflict with an existing one
		if($this->_accountNumberIsExisting($json["account_number"]))
		{
			return Response()->json([
				"message" => "The specified account number does already exist",
			], 404); // TODO: Another error code
		}

		// Create new entity
		$entity = new AccountingAccount;
		$entity->account_number     = $json["account_number"];
		$entity->title              = $json["title"];
		$entity->description        = $json["description"] ?? null;
		$entity->accounting_period  = $accountingperiod_id;
		$result = $entity->save();

		return [
			"status" => "created",
			"entity" => $entity->toArray(),
		];
	}

	/**
	 * Returns an single account
	 */
	function read(Request $request, $accountingperiod, $account_number)
	{
		// Get id of accounting period
		$accountingperiod_id = $this->_getAccountingPeriodId($accountingperiod);
		if(null === $accountingperiod_id)
		{
			return Response()->json([
				"message" => "Could not find the specified accounting period",
			], 404);
		}

		// Load the account
		$account = AccountingAccount::load([
			["accountingperiod", "=", $accountingperiod],
			["account_number",   "=", $account_number],
		]);

		// Generate an error if there is no such account
		if(false === $account)
		{
			return Response()->json([
				"message" => "No account with specified account number in the selected accounting period",
			], 404);
		}
		else
		{
			return $account;
		}
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
		$entity = Entity::delete($id);
	}

	/**
	 * TODO: Kolla om kontot finns redan
	 */
	function _accountNumberIsExisting($account_number)
	{
		return false;
	}
}