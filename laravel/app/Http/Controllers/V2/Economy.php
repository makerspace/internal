<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Libraries\EconomyParserSEB;

class Economy extends Controller
{
	/**
	 *
	 */
	function test(Request $request)
	{
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
