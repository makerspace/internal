<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Subscription as SubscriptionModel;

class Subscription extends Controller
{
	/**
	 *
	 */
	function list()
	{
		return Response()->json(array(
			'data' => SubscriptionModel::all(),
		));
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
		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function update(Request $request, $id)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function delete(Request $request, $id)
	{
		return ['error' => 'not implemented'];
	}
}
