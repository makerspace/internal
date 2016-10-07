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
}