<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class Key extends Controller
{
	function get()
	{
		return Response()->json(array(
			'meep' => 'hai',
		));
	}
}