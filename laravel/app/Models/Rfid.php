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
		"entity.title"       => "entity.title",
		"entity.description" => "entity.description",
		"rfid.tagid"         => "rfid.tagid",
		"rfid.active"        => "rfid.active",
	];
	protected $sort = ["created_at", "desc"];
	protected $validation = [
		"tagid" => ["unique", "required"],
	];

	protected function _list($filters = [])
	{
		$query = $this->_buildLoadQuery();

		// Go through filters
		foreach($filters as $filter)
		{
			// Pagination
			if("per_page" == $filter[0])
			{
				$this->pagination = $filter[1];
			}
			else if("relation" == $filter[0])
			{
				// Load the related entity and get it's entity_id
				$entity = Entity::Load($filter[1]);
				$entity_id = $entity->entity_id;

				// Get all relation to this entity
				$query2 = DB::table("relation")
					->whereRaw("{$entity_id} IN (entity1, entity2)")
					->get();

				// Filter out related entities
				$relatedEntities = null;
				foreach($query2 as $qw)
				{
					$relatedEntities[] = ($qw->entity1 == $entity_id ? $qw->entity2 : $qw->entity1);
				}

				$query = $query->whereIn("entity.entity_id", $relatedEntities);
			}
		}

		// Paginate
		if($this->pagination != null)
		{
			$query->paginate($this->pagination);
		}

		// Run the MySQL query
		$data = $query->get();

		$result = [
			"data" => $data
		];

		if($this->pagination != null)
		{
			$result["total"]    = $query->count();
			$result["per_page"] = $this->pagination;
			$result["last_page"] = ceil($result["total"] / $result["per_page"]);
		}

		return $result;
	}
}