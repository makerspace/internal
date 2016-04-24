<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;
use Config;

use \mysql_connect;

class Debug extends Controller
{
	function test(Request $request)
	{
		return ['meep meep'];
	}

	/**
	 * Returns all instructions where the sum of all transactions is not zero.
	 */
	function Unbalanced(Request $request)
	{
		$query = "SELECT accounting_instruction.*, SUM(accounting_transaction.amount) AS meep " .
		         "FROM accounting_instruction " .
		         "JOIN accounting_transaction " .
		         "ON accounting_transaction.accounting_instruction = accounting_instruction.entity_id " .
		         "GROUP BY entity_id " .
		         "HAVING meep != 0";
		$result = DB::select($query);

		return ["unbalanced_instructions" => $result];
	}

	/**
	 * This function updates the instruction numbers on all instructions except number 0.
	 */
	function UpdateInstructionNumbers(Request $request)
	{
		DB::statement("SET @number = 0;");

		$result = DB::statement(
			"UPDATE accounting_instruction " .
			"SET instruction_number = (@number :=  1 + @number) " .
			"WHERE instruction_number != 0 OR instruction_number IS NULL " .
			"ORDER BY accounting_date"
		);

		return ["status" => $result];
	}
}
