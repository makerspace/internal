<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Entity;
use App\Models\Rfid as RfidModel;

use App\Traits\Pagination;
use App\Traits\EntityStandardFiltering;

use DB;

class Rfid extends Controller
{
	use Pagination, EntityStandardFiltering;

	/**
	 *
	 */
	function list(Request $request)
	{
		return $this->_applyStandardFilters("Rfid", $request);
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		$json = $request->json()->all();

		// Create new RFID entity
		$entity = new RfidModel;
		$entity->description = $json["description"] ?? null;
		$entity->tagid       = $json["tagid"]       ?? null;
		$entity->status      = $json["status"]      ?? "inactive";
		$entity->startdate   = $json["startdate"]   ?? null;
		$entity->enddate     = $json["enddate"]     ?? null;

		// Add relations
		if(!empty($json["relations"]))
		{
			$entity->addRelations($json["relations"]);
		}

		// Validate input
		$entity->validate();

		// Save entity
		$entity->save();

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