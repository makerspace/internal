<?php
namespace App\Models;

use App\Models\Entity;
use DB;

/**
 *
 */
class Mail extends Entity
{
	protected $type = "mail";
	protected $join = "mail";
	protected $columns = [
		"entity.entity_id"        => "entity.entity_id",
		"entity.created_at"       => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"       => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"            => "entity.title",
		"entity.description"      => "entity.description",
		"mail.type"               => "mail.type",
		"mail.recipient"          => "mail.recipient",
		"mail.status"             => "mail.status",
		// An ugly way to sort unsent messages first
		"mail.date_sent"          => "IF(`date_sent` IS NULL, '2030-01-01 00:00:00', DATE_FORMAT(mail.date_sent, '%Y-%m-%dT%H:%i:%sZ')) AS date_sent",
	];
	protected $sort = [
		["date_sent", "desc"],
//		["mail.date_sent", "desc"],
		["entity.created_at", "desc"]
	];

	/**
	 *
	 */
	public function _list($filters = [])
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Apply standard filters like entity_id, relations, etc
		$query = $this->_applyFilter($query, $filters);

		// Paginate
		if($this->pagination != null)
		{
			$query->paginate($this->pagination);
		}

		// Run the MySQL query
		$data = $query->get();

		foreach($data as $row)
		{
			// An ugly way to sort unsent messages first
			if($row->date_sent == "2030-01-01 00:00:00")
			{
				$row->date_sent = null;
			}
		}

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
}