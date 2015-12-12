<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Member extends Controller
{
	/**
	 *
	 */
	function list(Request $request)
	{
		$per_page = 100;
		$data = [
			$this->read($request, 1),
			$this->read($request, 2),
			$this->read($request, 3),
		];

		// Return json array
		return [
			"per_page"  => $per_page,
			"total"     => 2,
			"last_page" => 1,
			"data"      => $data,
		];
	}

	/**
	 *
	 */
	function create(Request $request)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 *
	 */
	function read(Request $request, $id)
	{
		return [
			"member_id" => 27,
			"member_number" => 1234,
			"firstname" => "Nisse",
			"lastname" => "Olsson",
			"email" => "hej@olsson.se",
			"created_at" => "2015-01-01 01:15:00",
			"updated_at" => "2015-02-18 17:53:00",
		];
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
		return ['error' => 'not implemented'];
	}
}
