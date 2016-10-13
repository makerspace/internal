<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Mail as MailModel;
use App\Models\Member as MemberModel;
use App\Models\Entity;

use App\Traits\Pagination;

class Mail extends Controller
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
		
		// Load data from database
		$result = MailModel::list($filters);

		// Return json array
		return $result;
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		// TODO: Send borde skippas och hamna här istället?
		$json = $request->json()->all();

		// TODO: Validate
		//   $json["recipients"]
		//   $json["type"]
		//   $json["body"]
		//   subject null if sms or not null if e-mail

		foreach($json["recipients"] as $recipient)
		{
			// TODO: Get entity_id from $recipient
			// TODO: Use entity_id instead of member_number
			$x = MemberModel::Load(
				[
					["member_number", "=", $recipient["value"]]
				]
			);

			// TODO: If entity is a group, populate all users

			// Populate SMS/email
			if($json["type"] == "email")
			{
				$recipient = $x->email;
			}
			else
			{
				$recipient = $x->phone;
			}

			// Create new mail
			$entity = new MailModel;
			$entity->type        = $json["type"];
			$entity->recipient   = $recipient;
			$entity->title       = $json["subject"];
			$entity->description = $json["body"];
			$entity->status      = "queued";
			$entity->date_sent   = null;

			// Validate input
			$entity->validate();

			// Save the entity
			$result = $entity->save();

			// TODO: Create a relation to the recipient Member entity
			$entity->createRelations([$x->entity_id]);
		}

		// Send response to client
		return Response()->json([
			"status" => "created",
//			"entity" => $entity->toArray(),
		], 201);
	}

	/**
	 *
	 */
	function read(Request $request, $product_id)
	{
/*
		// Load the product
		$entity = ProductModel::load([
			["product_id", "=", $product_id]
		]);

		// Generate an error if there is no such product
		if(false === $entity)
		{
			return Response()->json([
				"message" => "No product with specified product id",
			], 404);
		}
		else
		{
			return $entity->toArray();
		}
*/
	}

	/**
	 *
	 */
	function update(Request $request, $product_id)
	{
/*
		// Load the product
		$entity = ProductModel::load([
			"product_id" => $product_id
		]);

		// Generate an error if there is no such product
		if(false === $entity)
		{
			return Response()->json([
				"message" => "No product with specified product id",
			], 404);
		}

		$json = $request->json()->all();

		// Create new product
		// TODO: Put in generic function
		$entity->product_id  = $product_id;
		$entity->title       = $json["title"]       ?? null;
		$entity->description = $json["description"] ?? null;

		// Validate input
		$entity->validate();

		// Save the entity
		$entity->save();

		// TODO: Standarized output
		return [
			"status" => "updated",
			"entity" => $entity->toArray(),
		];
*/
	}

	/**
	 * Delete product
	 */
	function delete(Request $request, $product_id)
	{
/*
		$entity = ProductModel::load([
			"product_id" => $product_id
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
*/
	}

/*
	function send(Request $request)
	{
		$json = $request->json()->all();

		return [
			"status" => "sent",
			"data"   => $json,
		];
	}
*/
}