<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Member as MemberModel;

use App\Traits\Pagination;

class Member extends Controller
{
	use Pagination;

	/**
	 *
	 */
	function list(Request $request)
	{
		// Paging filter
		$filters = [
			["per_page", $this->per_page($request)],
		];

		// Filter on relations
		$relations = $request->get("relations");
		if($relations)
		{
			$new_relations = [];
			foreach($relations as $relation)
			{
				$relation_filters = [];
				foreach($relation as $key => $value)
				{
					$relation_filters[] = [$key, $value];
				}
				$new_relations[] = $relation_filters;
			}

			$filters[] = ["relations", $new_relations];
		}

		// Filter on search
		if(!empty($request->get("search")))
		{
			$filters[] = ["search", $request->get("search")];
		}

		// Sorting
		if(!empty($request->get("sort_by")))
		{
			$order = ($request->get("sort_order") == "desc" ? "desc" : "asc");

			$filters[] = ["sort", [$request->get("sort_by"), $order]];
		}
		
		// Load data from database
		$result = MemberModel::list($filters);

		// Return json array
		return $result;
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		$json = $request->json()->all();

		// TODO: Require E-mail?

		// Create a unique member number if not specified
		if(!empty($json["member_number"]))
		{
			$member_number = $json["member_number"];
		}
		else
		{
			$member_number = uniqid();
		}

		// Create new member
		$entity = new MemberModel;
//		$entity->title           = $json["title"];
//		$entity->description     = $json["description"] ?? null;
		$entity->member_number   = $member_number;
		$entity->email           = $json["email"];
		$entity->password        = $json["password"]        ?? null;
		$entity->firstname       = $json["firstname"]       ?? null;
		$entity->lastname        = $json["lastname"]        ?? null;
		$entity->civicregno      = $json["civicregno"]      ?? null;
		$entity->company         = $json["company"]         ?? null;
		$entity->orgno           = $json["orgno"]           ?? null;
		$entity->address_street  = $json["address_street"]  ?? null;
		$entity->address_extra   = $json["address_extra"]   ?? null;
		$entity->address_zipcode = $json["address_zipcode"] ?? null;
		$entity->address_city    = $json["address_city"]    ?? null;
		$entity->address_country = $json["address_country"] ?? "SE";
		$entity->phone           = $json["phone"]           ?? null;

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
	function read(Request $request, $member_number)
	{
		// Load the entity
		$entity = MemberModel::load([
			["member_number", "=", $member_number]
		]);

		// Generate an error if there is no such member
		if(false === $entity)
		{
			return Response()->json([
				"message" => "No member with specified member number",
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
	function update(Request $request, $member_number)
	{
		// Load the entity
		$entity = MemberModel::load([
			["member_number", "=", $member_number]
		]);

		// Generate an error if there is no such product
		if(false === $entity)
		{
			return Response()->json([
				"message" => "Could not find any member with specified member_number",
			], 404);
		}

		$json = $request->json()->all();

		// Populate the entity with new values
		foreach($json as $key => $value)
		{
			$entity->{$key} = $value ?? null;
		}
/*
		// TODO: Validate input
		// TODO: Validation of tagid will fail because it is already in the database en therefore not unique
		$errors = $entity->validate();
		if(!empty($errors))
		{
			return Response()->json([
				"errors" => $errors,
			], 400);
		}
*/
		// Save the entity
		$result = $entity->save();

		// TODO: Standarized output
		return Response()->json([
			"status" => "updated",
			"entity" => $entity->toArray(),
		], 200);
	}

	/**
	 *
	 */
	function delete(Request $request, $member_number)
	{
		$entity = $this->read($request, $member_number);

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