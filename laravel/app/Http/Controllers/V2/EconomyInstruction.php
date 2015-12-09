<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Libraries\EconomyParserSEB;

use App\Models\AccountingInstruction;

//use App\Models\AccountingTransaction;
//use App\Models\AccountingAccount;

use DB;

class EconomyInstruction extends Controller
{
	/**
	 *
	 */
	function list(Request $request)
	{
		$per_page = $request->input("per_page");
		if(empty($per_page))
		{
			$per_page = 20;
		}

		// Paginate the data
//		$data = $rows->paginate($per_page);

		$data = (new AccountingInstruction())->list();

		foreach($data as &$row)
		{
//			$row->balance = 123;
//			$row->accounting_date = date("c");
		}

		// Return json array
		return [
			"per_page"  => $per_page,
			"total"     => 2,
			"last_page" => 1,
			"data"      => $data,
		];
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function read(Request $request, $id)
	{
		return AccountingInstruction::load($id);
	}

	/**
	 *
	 */
	function update(Request $request, $id)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 * Delete an instruction
	 */
	function delete(Request $request, $id)
	{
		$entity = Entity::delete($id);
	}
}