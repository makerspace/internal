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
		// Load data from datbase
		$result = MemberModel::list([
			["per_page", $this->per_page($request)],
		]);

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
//		$entity->title         = $json["title"];
//		$entity->description   = $json["description"] ?? null;
		$entity->member_number = $member_number;
		$entity->email         = $json["email"];
		$entity->password      = $json["password"]   ?? null;
		$entity->firstname     = $json["firstname"]  ?? null;
		$entity->lastname      = $json["lastname"]   ?? null;
		$entity->civicregno    = $json["civicregno"] ?? null;
		$entity->company       = $json["company"]    ?? null;
		$entity->orgno         = $json["orgno"]      ?? null;
		$entity->address       = $json["address"]    ?? null;
		$entity->address2      = $json["address2"]   ?? null;
		$entity->zipcode       = $json["zipcode"]    ?? null;
		$entity->city          = $json["city"]       ?? null;
		$entity->country       = $json["country"]    ?? "SE";
		$entity->phone         = $json["phone"]      ?? null;

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
		// Load the invoice
		$member = MemberModel::load([
			"member_number" => $member_number
		]);

		// Generate an error if there is no such member
		if(false === $member)
		{
			return Response()->json([
				"message" => "No member with specified member number",
			], 404);
		}
		else
		{
			return $member;
		}
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
