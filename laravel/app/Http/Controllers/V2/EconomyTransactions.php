<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\AccountingTransaction;

use App\Traits\AccountingPeriod;
use App\Traits\Pagination;

class EconomyTransactions extends Controller
{
	use AccountingPeriod, Pagination;

	/**
	 *
	 */
	function list(Request $request, $accountingperiod)
	{
		/*
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}
		*/

		// Paging filter
		$filters = [
			["per_page", $this->per_page($request)],
//			["account_number", "=", $account_number],
//			["accountingperiod", "=", $accountingperiod],
		];

		// Filter on relations
		$relations = $request->get("relation");
		if($relations)
		{
			$relation_filters = [];
			foreach($relations as $key => $value)
			{
				$relation_filters[] = [$key, $value];
			}

			$filters[] = ["relation", $relation_filters];
		}

		// Load data from database
		$result = AccountingTransaction::list($filters);

		// Return json array
		return $result;
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
				["per_page", $this->per_page($request)],
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
			// Return json array
			return $result;
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