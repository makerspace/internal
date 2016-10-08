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
		// Paging filter
		$filters = [
			["per_page", $this->per_page($request)],
		];

		// Filter on relations
		$relation = $request->get("relation");
		if($relation)
		{
			$filters[] = ["relation", 
				[
					// TODO: Not hardcoded
					["type", "=", $relation["type"]],
					["member_number", "=", $relation["member_number"]],
				]
			];
		}

		// Load data from database
		$result = GroupModel::list($filters);

		// Return json array
		return $result;
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		$json = $request->json()->all();

		// Create new group
		$entity = new GroupModel;
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
	function read(Request $request, $entity_id)
	{
		// Load the group
		$entity = GroupModel::load([
			["entity_id", "=", $entity_id]
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
	function update(Request $request, $entity_id)
	{
		// Load the group
		$entity = GroupModel::load([
			"entity_id" => $entity_id
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
		$entity->title       = $json["title"]       ?? null;
		$entity->description = $json["description"] ?? null;

		$result = $entity->save();

		if($result)
		{
			// TODO: Standarized output
			return [
				"status" => "updated",
				"entity" => $entity->toArray(),
			];
		}
		else
		{
			return [
				"status" => "error"
			];
		}
	}

	/**
	 * Delete group
	 */
	function delete(Request $request, $entity_id)
	{
		$entity = GroupModel::load([
			"entity_id" => $entity_id
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