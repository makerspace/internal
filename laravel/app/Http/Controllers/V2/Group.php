<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Group as GroupModel;
use App\Models\Entity;

use App\Traits\Pagination;

use LucaDegasperi\OAuth2Server\Authorizer;

class Group extends Controller
{
	use Pagination;

	/**
	 *
	 */
	function list(Request $request, Authorizer $authorizer)
	{
/*
		if($authorizer->getChecker()->getAccessToken() === null)
		{
			echo "Not logged in";
		}
		else
		{
			echo "Logged in";
		}

//		$user_id = $authorizer->getResourceOwnerId(); // the token user_id
*/

		// Load data from datbase
		$result = GroupModel::list([
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
		$json = $request->json()->all();

		// Create a unique group id if not specified
		if(!empty($json["group_id"]))
		{
			$group_id = $json["group_id"];
		}
		else
		{
			$group_id = uniqid();
		}

		// Create new group
		$entity = new GroupModel;
		$entity->group_id    = $group_id;
		$entity->title       = $json["title"]       ?? null;
		$entity->description = $json["description"] ?? null;

		$result = $entity->save();

		// TODO: Standarized output
		return [
			"status" => "created",
			"entity" => $entity->toArray(),
		];
	}

	/**
	 *
	 */
	function read(Request $request, $group_id)
	{
		// Load the group
		$entity = GroupModel::load([
			"group_id" => $group_id
		]);

		// Generate an error if there is no such group
		if(false === $entity)
		{
			return Response()->json([
				"message" => "No group with specified group id",
			], 404);
		}
		else
		{
			return $entity->toArray();
		}
	}

	/**
	 *
	 */
	function update(Request $request, $group_id)
	{
		// Load the group
		$entity = GroupModel::load([
			"group_id" => $group_id
		]);

		// Generate an error if there is no such group
		if(false === $entity)
		{
			return Response()->json([
				"message" => "No group with specified group id",
			], 404);
		}

		$json = $request->json()->all();

		// Create new group
		// TODO: Put in generic function
		$entity->group_id    = $group_id;
		$entity->title       = $json["title"]       ?? null;
		$entity->description = $json["description"] ?? null;

		$result = $entity->save();

		// TODO: Standarized output
		return [
			"status" => "updated",
			"entity" => $entity->toArray(),
		];
	}

	/**
	 * Delete group
	 */
	function delete(Request $request, $group_id)
	{
		$entity = GroupModel::load([
			"group_id" => $group_id
		]);

		if($entity->delete())
		{
			return [
				"status" => "deleted"
			];
		}
		else
		{
			return [
				"status" => "error"
			];
		}
	}
}
