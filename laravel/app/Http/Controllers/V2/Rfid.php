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
		$entity->description   = $json["description"] ?? null;
		$entity->tagid         = $json["tagid"]       ?? null;
		$entity->status        = $json["status"]      ?? "inactive";
		$entity->startdate     = $json["startdate"]   ?? null;
		$entity->enddate       = $json["enddate"]     ?? null;

		// Validate input
		$entity->validate();

		// Save entity
		$entity->save();

		// Add relations
		if(!empty($json["relations"]))
		{
			$entity->createRelations($json["relations"]);
		}

		// Send response to client
		return Response()->json([
			"status" => "created",
			"entity" => $entity->toArray(),
		], 201);
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

		// Validate input
		$entity->validate();

		// Save the entity
		$entity->save();

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