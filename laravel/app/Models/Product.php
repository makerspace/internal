<?php
namespace App\Models;

use App\Models\Entity;

/**
 *
 */
class Product extends Entity
{
	protected $type = "product";
	protected $join = "product";
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
		"expiry_date" => [
			"column" => "product.expiry_date",
			"select" => "product.expiry_date",
		],
		"auto_extend" => [
			"column" => "product.auto_extend",
			"select" => "product.auto_extend",
		],
		"price" => [
			"column" => "product.price",
			"select" => "product.price",
		],
		"interval" => [
			"column" => "product.interval",
			"select" => "product.interval",
		],
	];
	protected $sort = ["title", "asc"];
}