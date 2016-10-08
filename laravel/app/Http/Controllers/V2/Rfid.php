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
		return ["error" => "not implemented"];
	}

	/**
	 *
	 */
	function update(Request $request, $entity_id)
	{
		return ["error" => "not implemented"];
	}

	/**
	 *
	 */
	function delete(Request $request, $entity_id)
	{
		$entity = RfidModel::load([
			"entity.entity_id" => $entity_id
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