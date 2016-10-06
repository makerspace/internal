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
		"entity.entity_id"        => "entity.entity_id",
		"entity.created_at"       => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"       => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"            => "entity.title",
		"entity.description"      => "entity.description",
	];
	protected $sort = ["entity.title", "desc"];

	/**
	 *
	 */
	public function _list($filters = [])
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Go through filters
		foreach($filters as $filter)
		{
			// Pagination
			if("per_page" == $filter[0])
			{
				$this->pagination = $filter[1];
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
			$result["total"]     = $query->count();
			$result["per_page"]  = $this->pagination;
			$result["last_page"] = ceil($result["total"] / $result["per_page"]);
		}

		return $result;
	}

	/**
	 *
	 */
	public function _load($filters, $show_deleted = false)
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Go through filters
		foreach($filters as $filter)
		{
			// Filter on entity_id
			if("entity_id" == $filter[0])
			{
				$query = $query->where("entity.entity_id", $filter[1], $filter[2]);
			}
		}

		// TODO
		$data = (array)$query->first();
		$entity = new Group;
		$entity->entity_id   = $data["entity_id"];
		$entity->title       = $data["title"];
		$entity->description = $data["description"];
		return $entity;
	}
}
