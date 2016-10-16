<?php
namespace App\Models;

use App\Models\Entity;

/**
 *
 */
class Subscription extends Entity
{
	protected $type = "subscription";
	protected $join = "subscription";
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
		"member_id" => [
			"column" => "subscription.member_id",
			"select" => "subscription.member_id",
		],
		"product_id" => [
			"column" => "subscription.product_id",
			"select" => "subscription.product_id",
		],
		"date_start" => [
			"column" => "subscription.date_start",
			"select" => "subscription.date_start",
		],
	];
	protected $sort = ["title", "asc"];
}