<?php
namespace App\Models;

use App\Models\Entity;

/**
 *
 */
class Group extends Entity
{
	protected $type = "group";
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
		"title" => [
			"column" => "entity.title",
			"select" => "entity.title",
		],
		"description" => [
			"column" => "entity.description",
			"select" => "entity.description",
		],
		// TODO: Count members
/*
		"membercount" => [
			"column" => "7",
			"select" => "7",
		],
*/
	];
	protected $sort = ["title", "asc"];
	protected $validation = [
		"title" => ["required", "unique"],
	];

	public function _search($query, $search)
	{
		$words = explode(" ", $search);
		foreach($words as $word)
		{
			$query = $query->where(function($query) use($word) {
				// Build the search query
				$query
					->  where("entity.title",       "like", "%".$word."%")
					->orWhere("entity.description", "like", "%".$word."%");
			});
		}

		return $query;
	}
}