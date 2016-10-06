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