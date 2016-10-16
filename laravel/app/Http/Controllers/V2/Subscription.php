<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Subscription as SubscriptionModel;

use App\Traits\Pagination;
use App\Traits\EntityStandardFiltering;

class Subscription extends Controller
{
	use Pagination, EntityStandardFiltering;

	/**
	 *
	 */
	function list(Request $request)
	{
		return $this->_applyStandardFilters("Subscription", $request);

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