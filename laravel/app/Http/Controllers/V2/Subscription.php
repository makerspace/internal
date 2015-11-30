<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\Subscription as SubscriptionModel;

class Subscription extends Controller
{
	function list()
	{
		return Response()->json(array(
			'data' => SubscriptionModel::all(),
		));
	}
}
