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
	/**
	 *
	 */
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

	/**
	 *
	 */
	function importSeb()
	{
		$s = new EconomyParserSEB();
		$data = file_get_contents("/vagrant/Bokföring/Kontohändelser.csv");
		$data = $s->Import($data);

		echo "<pre>";
		print_r($data);
	}
}
