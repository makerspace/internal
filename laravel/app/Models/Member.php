<?php
namespace App\Models;

use App\Models\Entity;

/**
 *
 */
class Member extends Entity
{
	protected $type = "member";
	protected $join = "member";
	protected $columns = [
		"entity.entity_id"        => "entity.entity_id",
		"entity.created_at"       => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"       => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
//		"entity.title"            => "entity.title",
		"entity.description"      => "entity.description",
		"member.member_number"    => "member.member_number",
		"member.email"            => "member.email",
		"member.password"         => "member.password",
		"member.reset_token"      => "member.reset_token",
		"member.reset_expire"     => "member.reset_expire",
		"member.firstname"        => "member.firstname",
		"member.lastname"         => "member.lastname",
		"member.civicregno"       => "member.civicregno",
		"member.company"          => "member.company",
		"member.orgno"            => "member.orgno",
		"member.address_street"   => "member.address_street",
		"member.address_extra"    => "member.address_extra",
		"member.address_zipcode"  => "member.address_zipcode",
		"member.address_city"     => "member.address_city",
		"member.address_country"  => "member.address_country",
		"member.phone"            => "member.phone",
	];
	protected $sort = ["member.member_number", "desc"];
	protected $validation = [
		"firstname" => ["required"],
		"email"     => ["required", "unique"],
	];

	public function _search($query, $search)
	{
		$words = explode(" ", $search);
		foreach($words as $word)
		{
			$query = $query->where(function($query) use($word) {
				// The phone numbers are stored with +46 in database, so strip the first zero in the phone number
				$phone = ltrim($word, "0");
				// Build the search query
				$query
					->  where("member.firstname",       "like", "%".$word."%")
					->orWhere("member.lastname",        "like", "%".$word."%")
					->orWhere("member.email",           "like", "%".$word."%")
					->orWhere("member.address_street",  "like", "%".$word."%")
					->orWhere("member.address_extra",   "like", "%".$word."%")
					->orWhere("member.address_zipcode", "like", "%".$word."%")
					->orWhere("member.address_city",    "like", "%".$word."%")
					->orWhere("member.phone",           "like", "%".$phone."%")
					->orWhere("member.civicregno",      "like", "%".$word."%")
					->orWhere("member.member_number",   "like", "%".$word."%");
			});
		}

		return $query;
	}
}