<?php
namespace App\Models;

use App\Models\Entity;

/**
 *
 */
class Subscription extends Entity
{
	protected $join = "subscription";
	protected $columns = [
		"entity.entity_id"        => "entity.entity_id",
		"entity.created_at"       => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"       => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"            => "entity.title",
		"subscription.member_id"  => "subscription.member_id",
		"subscription.product_id" => "subscription.product_id",
		"subscription.date_start" => "subscription.date_start",
	];
//	protected $sort = ["invoice_number", "desc"];
}