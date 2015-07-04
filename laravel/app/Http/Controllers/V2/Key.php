<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;

class Key extends Controller
{
	function get()
	{
		echo Response::json(array(
			'meep' => 'hai',
		));
	}
}