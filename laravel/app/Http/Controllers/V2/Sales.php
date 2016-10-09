<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

//use App\Models\Product as ProductModel;
use App\Models\Entity;

use App\Traits\Pagination;

class Sales extends Controller
{
	use Pagination;

	/**
	 *
	 */
	function list(Request $request)
	{
		return Response()->json([
			"error" => "Not implemented",
		], 501);
/*
		// Load data from datbase
		$result = ProductModel::list([
			["per_page", $this->per_page($request)],
		]);

		// Return json array
		return $result;
*/
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		return Response()->json([
			"error" => "Not implemented",
		], 501);
/*
		$json = $request->json()->all();

		// Create a unique product id if not specified
		if(!empty($json["product_id"]))
		{
			$product_id = $json["product_id"];
		}
		else
		{
			$product_id = uniqid();
		}

		// Create new product
		$entity = new ProductModel;
		$entity->product_id  = $product_id;
		$entity->title       = $json["title"]       ?? null;
		$entity->description = $json["description"] ?? null;

		$result = $entity->save();

		// TODO: Standarized output
		return [
			"status" => "created",
			"entity" => $entity->toArray(),
		];
*/
	}

	/**
	 *
	 */
	function read(Request $request, $product_id)
	{
		return Response()->json([
			"error" => "Not implemented",
		], 501);
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
		return Response()->json([
			"error" => "Not implemented",
		], 501);
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

		$result = $entity->save();

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
		return Response()->json([
			"error" => "Not implemented",
		], 501);
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
}