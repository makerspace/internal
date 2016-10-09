<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Subscription as SubscriptionModel;

use App\Traits\Pagination;

class Subscription extends Controller
{
	use Pagination;

	/**
	 *
	 */
	function list(Request $request)
	{
		// Load data from datbase
		$result = SubscriptionModel::list([
			["per_page", $this->per_page($request)],
		]);

		// Return json array
		return $result;
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		return Response()->json([
			"error" => "Not implemented",
		], 501);
	}

	/**
	 *
	 */
	function read(Request $request, $id)
	{
		return Response()->json([
			"error" => "Not implemented",
		], 501);
	}

	/**
	 *
	 */
	function update(Request $request, $id)
	{
		return Response()->json([
			"error" => "Not implemented",
		], 501);
	}

	/**
	 *
	 */
	function delete(Request $request, $id)
	{
		return Response()->json([
			"error" => "Not implemented",
		], 501);
	}
}