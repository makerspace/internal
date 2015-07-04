<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;

class Member extends Controller
{
	function get()
	{
		echo Response::json(array(
			'meep' => 'hai',
		));
	}
}