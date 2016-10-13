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
		"entity.type"        => "entity.type",
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
	 * Do some preprocessing before we can start building our query:
	 *   1) Figure out if we need to join another table to get our result
	 *   2) Apply sorting
	 */
	protected function _preprocessFilters(&$filters)
	{
		if(is_array($filters))
		{
			foreach($filters as $id => $filter)
			{
				if($filter[0] == "type")
				{
					$type = $filter[1];
					$this->type = $type;
					// TODO: Hardcoded list
					if(in_array($type, ["accounting_transaction", "accounting_period", "accounting_instruction", "accounting_account", "member", "mail", "product", "rfid", "subscription", "invoice"]))
					{
						$this->join = $type;
					}

					// Remove the filter to prevent further processing
					unset($filters[$id]);
				}
				// Sorting
				else if("sort" == $filter[0])
				{
					$this->sort = $filter[1];
					unset($filters[$id]);
				}
			}
		}
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
	 *
	 */
	protected function _applyFilter($query, &$filters)
	{
		// Filter on entity_id
		if(!is_array($filters))
		{
			$query = $query->where("entity.entity_id", "=", $filters);
		}
		// Filter in arbitrary parameters
		else
		{
			// Go through filters
			foreach($filters as $id => $filter)
			{
				// Pagination
				if("per_page" == $filter[0])
				{
					$this->pagination = $filter[1];
					unset($filters[$id]);
				}
				// Search filter
				else if("search" == $filter[0])
				{
					// Check if there is a search function in the model
					if(method_exists($this, "_search"))
					{
						$this->_search($query, $filter[1]);
					}
					unset($filters[$id]);
				}
				// Relations
				else if("relations" == $filter[0])
				{
					foreach($filter[1] as $relation)
					{
						// Load the related entity and get it's entity_id
						$entity = Entity::Load($relation);
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
					unset($filters[$id]);
				}
				// Filter on arbritrary columns
				else
				{
					if(count($filter) == 2)
					{
						$query = $query->where($filter[0], "=", $filter[1]);
					}
					else
					{
						$query = $query->where($filter[0], $filter[1], $filter[2]);
					}
					unset($filters[$id]);
				}
			}
		}

		return $query;
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
		// A type filter should create a SQL join
		$this->_preprocessFilters($filters);

		// Build base query
		$query = $this->_buildLoadQuery($filters);

		// Apply standard filters like entity_id, relations, etc
		$query = $this->_applyFilter($query, $filters);

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
	 * Load an entity (called statically)
	 *
	 * Create an instance of the class and call the function non-static
	 */
	public static function load($filters, $show_deleted = false)
	{
		return (new static())->_load($filters, $show_deleted);
	}

	/**
	 *
	 */
	protected function _load($filters, $show_deleted = false)
	{
		$this->_preprocessFilters($filters);

		$query = $this->_buildLoadQuery();
		$query = $this->_applyFilter($query, $filters);

		// Get data from database
		$data = (array)$query->first();

		// Return false if no entity was found in database
		if(empty($data))
		{
			return false;
		}
		
		// Create a new entity based on type
		$type = ($this->type !== null ? $this->type : $data["type"]);
		switch($type)
		{
			case "accounting_account":
				$entity = new AccountingAccount;
				break;

			case "accounting_instruction":
				$entity = new AccountingInstruction;
				break;

			case "accounting_transaction":
				$entity = new AccountingTransaction;
				break;

			case "group":
				$entity = new Group;
				break;

			case "invoice":
				$entity = new Invoice;
				break;

			case "mail":
				$entity = new Mail;
				break;

			case "member":
				$entity = new Member;
				break;

			case "product":
				$entity = new Product;
				break;

			case "rfid":
				$entity = new Rfid;
				break;

			case "subscription":
				$entity = new Subscription;
				break;

			default:
				$entity = new Entity;
		}

		// Populate the entity with data
		foreach($data as $key => $value)
		{
			$entity->{$key} = $value;
		}

		return $entity;
	}

	/**
	 *
	 */
	public function createRelations($relations)
	{

		// Go through the list of relations
		foreach($relations as $i => $relation)
		{
			$parameters = [];
			foreach($relation as $key => $value)
			{
				$parameters[] = [$key, $value];
			}

			// Load the specified entity
			$entity2 = Entity::load($parameters);// TODO: Format?

			// Bail out on error
			if(empty($entity2))
			{
				return false;
				// TODO: Throw new exception
/*
				return Response()->json([
					"errors" => ["Could not create relation: The entity you have specified could not be found"],
				], 404);
*/
			}

			// Create the relation
			$entity1_id = $this->entity_id;
			$entity2_id = $entity2->entity_id;
			DB::table("relation")->insert([
				"entity1" => $entity1_id,
				"entity2" => $entity2_id
			]);
			// TODO: Error handling
		}
	}

	/**
	 * Save an entity
	 */
	public function save()
	{
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

		// Update an existing entity
		if($this->entity_id !== null)
		{
			// Update a row in the entity table
			DB::table("entity")
				->where("entity_id", $this->entity_id)
				->update([
					"updated_at"  => date("c"),
					"title"       => $this->data["title"]       ?? null,
					"description" => $this->data["description"] ?? null,
				]);

			// Update a row in the relation table
			if(!empty($inserts))
			{
				DB::table($this->join)
					->where("entity_id", $this->entity_id)
					->update($inserts);
			}
		}
		// Create a new entity
		else
		{
			// Create a new row in the entity table
			$this->entity_id = DB::table("entity")->insertGetId([
				"type"        => $this->type,
				"title"       => $this->data["title"]       ?? null,
				"description" => $this->data["description"] ?? null,
				"created_at"  => date("c"),
				"updated_at"  => date("c"),
			]);

			// Create a row in the relation table
			if(!empty($inserts))
			{
				$inserts["entity_id"] = $this->entity_id;
				DB::table($this->join)->insert($inserts);
			}
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
				if($rule == "required")
				{
					if(empty($this->data[$field]))
					{
						throw new EntityValidationException($field, "The value can not be empty");
					}
				}
				else if($rule == "unique")
				{
					// Check if there is anything in the database
					$result = Entity::load([
						["type", $this->join],
						[$field, $this->data[$field]]
					]);

					// A unique value collision is not fatal if this is the same entity... or else we could not save an entity
					if(!empty($result) && ($result->entity_id != $this->entity_id))
					{
						throw new EntityValidationException($field, "The value needs to be unique in the database");
					}
				}

				else
				{
					throw new EntityValidationException($field, "Unknown validation rule {$rule}");
				}
			}
		}
	}
}

/**
 * Thrown when a Entity::validate() fails and catched in app/Exceptions/Handler.php to return a standardized validation error result
 */
class EntityValidationException extends \Exception
{
	protected $column;

	function __construct($column, $message)
	{
		$this->column = $column;
		$this->message = $message;
	}

	function getColumn()
	{
		return $this->column;
	}
}