<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Entity;
use App\Models\Rfid as RfidModel;

use DB;

class Rfid extends Controller
{
	/**
	 *
	 */
	function list(Request $request)
	{
		$per_page = 100;

		$data = RfidModel::list();

		// Return json array
		return Response()->json([
			"per_page"  => $per_page,
			"total"     => 2,
			"last_page" => 1,
			"data"      => $data,
		]);
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
			// Go through the list of relations
			foreach($json["relations"] as $type => $parameters)
			{
				// Load the specified entity
				$entity2 = Entity::load($parameters);

				// Bail out on error
				if(empty($entity2))
				{
					return Response()->json([
						"errors" => ["Could not create relation: The entity you have specified could not be found"],
					], 404);
				}

				// Create the relation
				$entity1_id = $data["entity_id"];
				$entity2_id = $entity2->entity_id;
				DB::table("relation")->insert([
					"entity1" => $entity1_id,
					"entity2" => $entity2_id
				]);
			}
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
	function read(Request $request, $member_number)
	{
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
		$entity = Entity::delete($id);
	}
}
