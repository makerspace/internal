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
		"entity.entity_id"   => "entity.entity_id",
		"entity.created_at"  => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"  => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.description" => "entity.description",
		"rfid.tagid"         => "rfid.tagid",
		"rfid.status"        => "rfid.status",
		"rfid.startdate"     => "DATE_FORMAT(rfid.startdate, '%Y-%m-%dT%H:%i:%sZ') AS startdate",
		"rfid.enddate"       => "DATE_FORMAT(rfid.enddate, '%Y-%m-%dT%H:%i:%sZ') AS enddate",
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