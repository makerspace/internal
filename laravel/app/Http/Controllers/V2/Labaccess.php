<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\Labaccess as LabaccessModel;

class Labaccess extends Controller
{
	function get()
	{
		return Response()->json(array(
			'data' => LabaccessModel::all(),
		));
	}
}
