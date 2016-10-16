<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Product as ProductModel;
use App\Models\Entity;

use App\Traits\Pagination;
use App\Traits\EntityStandardFiltering;

class Product extends Controller
{
	use Pagination, EntityStandardFiltering;

	/**
	 *
	 */
	function list(Request $request)
	{
		return $this->_applyStandardFilters("Product", $request);
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		$json = $request->json()->all();

		// Create new product
		$entity = new ProductModel;
		$entity->title       = $json["title"]       ?? null;
		$entity->description = $json["description"] ?? null;
		$entity->expiry_date = $json["expiry_date"] ?? null;
		$entity->price       = $json["price"]       ?? null;
		$entity->auto_extend = $json["auto_extend"] ?? null;
		$entity->interval    = $json["interval"]    ?? null;

		// Validate input
		$entity->validate();

		// Save the entity
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
		// Load the product
		$entity = ProductModel::load($entity_id);

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
	}

	/**
	 *
	 */
	function update(Request $request, $entity_id)
	{
		// Load the product
		$entity = ProductModel::load($entity_id);

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
	}

	/**
	 * Delete product
	 */
	function delete(Request $request, $entity_id)
	{
		$entity = ProductModel::load($entity_id);

		// Generate an error if there is no such product
		if(false === $entity)
		{
			return Response()->json([
				"message" => "No product with specified product id",
			], 404);
		}

		if($entity->delete())
		{
			return Response()->json([
				"status" => "deleted"
			], 200);
		}
		else
		{
			return Response()->json([
				"status" => "error"
			], 409);
		}
	}
}