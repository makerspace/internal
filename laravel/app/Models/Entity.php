<?php
namespace App\Models;

use DB;

/**
 * Entity class
 */
class Entity
{
	public $entity_id = null;
	protected $sort = null;
	protected $type = null;
	protected $join = null;
	protected $columns = [
		"entity.entity_id"   => "entity.entity_id",
		"entity.created_at"  => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"  => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"       => "entity.title",
		"entity.description" => "entity.description",
	];
	protected $data = [];
	protected $pagination = null; // No pagination by default

	/**
	 * Constructor
	 */
	function __construct($entity_id = null)
	{
		$this->entity_id = $entity_id;
	}

	/**
	 * Get a list of entities (called statically)
	 *
	 * Create an instance of the class and call the function non-static
	 */
	public static function list($filters = [])
	{
		return (new static())->_list($filters);
	}

	/**
	 * Same as above, but called non-statically
	 */
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

	/**
	 *
	 */
	protected function _buildLoadQuery($show_deleted = false)
	{
		// Get all entities
		$query = DB::table("entity");

		// Type
		if($this->type !== null)
		{
			$query = $query->where("entity.type", "=", $this->type);
		}

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

		// Show deleted entities or not?
		if($show_deleted === true)
		{
			// Include the deleted_at column in output only when we show deleted content
			$this->columns["entity.deleted_at"] = "entity.deleted_at";
		}
		else
		{
			// The deleted_at should be null, which means it is not yet deleted
			$query = $query->whereNull("entity.deleted_at");
		}

		// Sort result
		if($this->sort !== null)
		{
			// Sort on multiple columns
			if(is_array($this->sort[0]))
			{
				foreach($this->sort as $s)
				{
					$query = $query->orderBy($s[0], $s[1]);
				}
			}
			// Sort on single column
			else
			{
				$query = $query->orderBy($this->sort[0], $this->sort[1]);
			}
		}

		// Return the query
		return $query;
	}

	/**
	 * Load an entity (called statically)
	 *
	 * Create an instance of the class and call the function non-static
	 */
	public static function load($parameters, $show_deleted = false)
	{
		return (new static())->_load($parameters, $show_deleted);
	}

	/**
	 *
	 */
	protected function _load($parameters, $show_deleted = false)
	{
		// Filter in arbitrary parameters
		if(is_array($parameters))
		{
			// A type filter should create a SQL join
			if(array_key_exists("type", $parameters))
			{
				$this->join = $parameters["type"];
				unset($parameters["type"]);
			}

			// Build base query
			$query = $this->_buildLoadQuery();

			// Filter on content of $parameters
			foreach($parameters as $key => $value)
			{
				$query = $query->where($key, "=", $value);
			}

			// Return result
			return $query->first();
		}
		// Filter on entity_id
		else
		{
			return $this->_buildLoadQuery()
					->where("entity.entity_id", "=", $parameters)
					->first();
		}
	}

	/**
	 * Save an entity
	 */
	public function save()
	{
		// Insert into entity table
		$this->entity_id = DB::table("entity")->insertGetId([
			"type"        => $this->type,
			"description" => $this->data["description"] ?? null,
			"title"       => $this->data["title"]       ?? null,
			"created_at"  => date("c"),
		]);


		// Get the data to insert into the relation table
		$inserts = [];
		foreach($this->columns as $name => $query)
		{
			list($table, $column) = explode(".", $name);
			if($table == $this->join && array_key_exists($column, $this->data))
			{
				$inserts[$column] = $this->data[$column];
			}
		}

		// Create a row in the relation table
		if(!empty($inserts))
		{
			$inserts["entity_id"] = $this->entity_id;
			DB::table($this->join)->insert($inserts);
		}

		return true;
	}

	/**
	 * Delete an entity
	 */
	public function delete($permanent = false)
	{
		// Check that we have a id provided
		if($this->entity_id === null)
		{
			return false;
		}

		// TODO: Delete relations

		if($permanent === true)
		{
			// Permanent delete
			DB::table("entity")
				->where("entity_id", $this->entity_id)
				->delete();
		}
		else
		{
			// Soft delete
			DB::table("entity")
				->where("entity_id", $this->entity_id)
				->update(["deleted_at" => date("c")]);
		}

		return true;
	}

	/**
	 *
	 */
	public function __get($name)
	{
		if(array_key_exists($name, $this->data))
		{
			return $this->data[$name];
		}

		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);
		return null;
	}

	/**
	 *
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 *
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	/**
	 * Convert the entity into an array
	 */
	public function toArray()
	{
		$x = $this->data;
		$x["entity_id"] = $this->entity_id;
		return $x;
	}

	/**
	 * Validate the data based on the filters in $this->validation
	 */
	public function validate()
	{
		$errors = [];

		// Go through the filters
		foreach($this->validation as $field => $rules)
		{
			// Do not apply a filter if the key does not exist
			if(!array_key_exists($field, $this->data))
			{
				continue;
			}

			// Each field can have multiple rules
			foreach($rules as $rule)
			{
				if($rule == "unique")
				{
					// Check if there is anything in the database
					$result = Entity::load([
						"type" => $this->join,
						$field => $this->data[$field]
					]);

					// Return error if there is
					if(!empty($result))
					{
						$errors[] = [$field => "Is not unique"];
					}
				}
				else if($rule == "required")
				{
					if(empty($this->data[$field]))
					{
						$errors = [$field => "Is empty"];
					}
				}
				else
				{
					die("Unknown rule");
				}
			}
		}
		return $errors;
	}
}