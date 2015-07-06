<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Libraries\EconomyParserSEB;

use App\Models\AccountingInstruction;

class Economy extends Controller
{
	function import()
	{
		$s = new EconomyParserSEB();
		$data = file_get_contents("/vagrant/Bokföring/Kontohändelser.csv");
		$data = $s->Import($data);

		echo "<pre>";
		print_r($data);

/*
		return Response()->json(array(
			'date' => print_r($data, true),
		));
*/
	}
	
	function getInstructions(Request $request)
	{
//		print_r($request->input("page"));
//		die();
		$rows = AccountingInstruction::paginate($request->input("per_page"));
		return $rows->toJson();

/*
//		echo "<pre>";
		$data = [];
		foreach($rows as $row)
		{
			$json = $row->toJson();
			$data[] = $json;
		}

		return "[".implode(",", $data)."]";
*/		
	}
}