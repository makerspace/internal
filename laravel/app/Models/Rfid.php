<?php
namespace App\Models;

use App\Models\Entity;
use DB;

/**
 *
 */
class Rfid extends Entity
{
	protected $type = "rfid";
	protected $join = "rfid";
	protected $columns = [
		"entity_id" => [
			"column" => "entity.entity_id",
			"select" => "entity.entity_id",
		],
		"created_at" => [
			"column" => "entity.created_at",
			"select" => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ')",
		],
		"updated_at" => [
			"column" => "entity.updated_at",
			"select" => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ')",
		],
		"description" => [
			"column" => "entity.description",
			"select" => "entity.description",
		],
		"tagid" => [
			"column" => "rfid.tagid",
			"select" => "rfid.tagid",
		],
		"status" => [
			"column" => "rfid.status",
			"select" => "rfid.status",
		],
		"startdate" => [
			"column" => "rfid.startdate",
			"select" => "DATE_FORMAT(rfid.startdate, '%Y-%m-%dT%H:%i:%sZ')",
		],
		"enddate" => [
			"column" => "rfid.enddate",
			"select" => "DATE_FORMAT(rfid.enddate, '%Y-%m-%dT%H:%i:%sZ')",
		],
	];
	protected $sort = ["created_at", "desc"];
	protected $validation = [
		"tagid" => ["unique", "required"],
	];

	public function _search($query, $search)
	{
		$words = explode(" ", $search);
		foreach($words as $word)
		{
			$query = $query->where(function($query) use($word) {
				// Build the search query
				$query
					->  where("entity.description", "like", "%".$word."%")
					->orWhere("rfid.tagid",         "like", "%".$word."%");
			});
		}

		return $query;
	}
}