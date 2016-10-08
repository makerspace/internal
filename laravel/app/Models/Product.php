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
		"entity.entity_id"        => "entity.entity_id",
		"entity.created_at"       => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"       => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"            => "entity.title",
		"entity.description"      => "entity.description",
		"product.expiry_date"     => "product.expiry_date",
		"product.auto_extend"     => "product.auto_extend",
		"product.price"           => "product.price",
		"product.interval"        => "product.interval",
	];
	protected $sort = ["entity.title", "asc"];
}