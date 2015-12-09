<?php

namespace App\Models;

use DB;

/**
 * Entity class
 */
class Entity
{
	public $id = null;
	protected $sort = null;
	protected $join = null;
	protected $columns = [
		"entity.entity_id",
		"DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title",
		"entity.description",
	];

	/**
	 * Constructor
	 */
	function __construct($id = null)
	{
		$this->id = $id;
	}

	/**
	 * Get a list of entities (called statically)
	 *
	 * Create an instance of the class and call the function non-static
	 */
	public static function list($filter = null)
	{
		return (new static())->_list($filter);
	}

	/**
	 * Same as above, but called non-statically
	 */
	public function _list($filter = null)
	{
		return $this->_buildLoadQuery()->get();
	}

	/**
	 *
	 */
	protected function _buildLoadQuery()
	{
		// Get all invoices
		$query = DB::table("entity");

		// Join data table
		if($this->join !== null)
		{
			$query = $query->join($this->join, "{$this->join}.entity_id", "=", "entity.entity_id");
		}

		// Get columns
		foreach($this->columns as $column)
		{
			$query = $query->selectRaw($column);
		}

		// Do not show soft deleted entities
		$query = $query->whereNull("entity.deleted_at");

		// Sort result
		if($this->sort !== null)
		{
			$query = $query->orderBy($this->sort[0], $this->sort[1]);
		}

		return $query;
	}

	/**
	 * Load an entity (called statically)
	 *
	 * Create an instance of the class and call the function non-static
	 */
	public static function load($entity_id, $show_deleted = false)
	{
		return (new static())->_load($entity_id, $show_deleted);
	}

	/**
	 *
	 */
	public function _load($entity_id, $show_deleted = false)
	{
		return
			// Build base query
			$this->_buildLoadQuery()

			// Filter on entity id
			->where("entity.entity_id", "=", $entity_id)

			// Get result
			->first();

	}

	/**
	 * Save an entity
	 */
	function save()
	{
		// TODO
	}

	/**
	 * Delete an entity
	 */
	public static function delete($entity_id, $permanent = false)
	{
		// Check that we have a id provided
/*
		if($this->id === null)
		{
			return false;
		}
*/

		if($permanent === true)
		{
			// Permanent delete
			DB::table("entity")
				->where("entity_id", $entity_id)
				->delete();
		}
		else
		{
			// Soft delete
			DB::table("entity")
				->where("entity_id", $entity_id)
				->update(["deleted_at" => date("c")]);
		}
	}
}