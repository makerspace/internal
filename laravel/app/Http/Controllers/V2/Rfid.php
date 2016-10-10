<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Entity;
use App\Models\Rfid as RfidModel;

use App\Traits\Pagination;

use DB;

class Rfid extends Controller
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
		$relations = $request->get("relation");
		if($relations)
		{
			$relation_filters = [];
			foreach($relations as $key => $value)
			{
				$relation_filters[] = [$key, $value];
			}

			$filters[] = ["relation", $relation_filters];
		}

		// Filter on search
		if(!empty($request->get("search")))
		{
			$filters[] = ["search", $request->get("search")];
		}

		// Load data from database
		$result = RfidModel::list($filters);

		// Return json array
		return $result;
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		$json = $request->json()->all();

		// Create new RFID entity
		$entity = new RfidModel;
		$entity->title         = $json["title"]       ?? null;
		$entity->description   = $json["description"] ?? null;
		$entity->tagid         = $json["tagid"]       ?? null;
		$entity->active        = $json["active"]      ?? 1;

		// Validate input
		$errors = $entity->validate();
		if(!empty($errors))
		{
			return Response()->json([
				"errors" => $errors,
			], 400);
		}

		// Save entity
		$result = $entity->save();
		$data = $entity->toArray();

		// Add relations
		if(!empty($json["relations"]))
		{
			$entity->createRelations($json["relations"]);
		}

		// TODO: Standariezed output
		return [
			"status" => "created",
			"entity" => $data,
		];
	}

	/**
	 *
	 */
	function read(Request $request, $entity_id)
	{
		// Load the invoice
		$entity = RfidModel::load($entity_id);

		// Generate an error if there is no such member
		if(false === $entity)
		{
			return Response()->json([
				"message" => "Could not find any entity with specified entity_id",
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
		// Load the entity
		$entity = RfidModel::load($entity_id);

		// Generate an error if there is no such product
		if(false === $entity)
		{
			return Response()->json([
				"message" => "Could not find any entity with specified entity_id",
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
	function delete(Request $request, $entity_id)
	{
		$entity = RfidModel::load($entity_id);

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