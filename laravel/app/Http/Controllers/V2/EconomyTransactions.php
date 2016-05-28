<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\AccountingTransaction;
use App\Traits\AccountingPeriod;

class EconomyTransactions extends Controller
{
	use AccountingPeriod;

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
	function read(Request $request, $accountingperiod, $account_number)
	{
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}

		$result = AccountingTransaction::list(
			[
				["account_number", "=", $account_number],
				["accountingperiod", "=", $accountingperiod],
			]
		);

		// Generate an error if there is no such instruction
		if(count($result["data"]) == 0)
		{
			return Response()->json([
				"message" => "No transactions found",
			], 404);
		}
		else
		{
			// Pagination
			$per_page = (int)($request->get("per_page") ?? 25);

			// Return json array
			return Response()->json([
				"per_page"  => $per_page,
				"last_page" => ceil($result["count"] / $per_page),
				"total"     => $result["count"],
				"data"      => $result["data"],
			]);
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
		return ['error' => 'not implemented'];
	}
}
