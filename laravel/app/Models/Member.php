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
		"entity.title"            => "entity.title",
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
		"member.address"          => "member.address",
		"member.address2"         => "member.address2",
		"member.zipcode"          => "member.zipcode",
		"member.city"             => "member.city",
		"member.country"          => "member.country",
		"member.phone"            => "member.phone",
	];
	protected $sort = ["member.member_number", "desc"];

	/**
	 *
	 */
	public function _list($filters = [])
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Go through filters
		foreach($filters as $filter)
		{
			// Pagination
			if("per_page" == $filter[0])
			{
				$this->pagination = $filter[1];
			}
			else if("search" == $filter[0])
			{
				$words = explode(" ", $filter[1]);
				foreach($words as $word)
				{
					$query = $query->where(function($query) use($word) {
						// The phone numbers are stored with +46 in database, so strip the first zero in the phone number
						$phone = ltrim($word, "0");

						// Build the search query
						$query
							->  where("member.firstname",     "like", "%".$word."%")
							->orWhere("member.lastname",      "like", "%".$word."%")
							->orWhere("member.email",         "like", "%".$word."%")
							->orWhere("member.address",       "like", "%".$word."%")
							->orWhere("member.address",       "like", "%".$word."%")
							->orWhere("member.zipcode",       "like", "%".$word."%")
							->orWhere("member.city",          "like", "%".$word."%")
							->orWhere("member.phone",         "like", "%".$phone."%")
							->orWhere("member.civicregno",    "like", "%".$word."%")
							->orWhere("member.member_number", "like", "%".$word."%");
					});
				}
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
			$result["total"]     = $query->count();
			$result["per_page"]  = $this->pagination;
			$result["last_page"] = ceil($result["total"] / $result["per_page"]);
		}

		return $result;
	}

	/**
	 *
	 */
	public function _load($filters, $show_deleted = false)
	{
		// Build base query
		$query = $this->_buildLoadQuery();

		// Go through filters
		foreach($filters as $filter)
		{
			// Filter on entity_id
			if("entity_id" == $filter[0])
			{
				$query = $query->where("entity.entity_id", $filter[1], $filter[2]);
			}
			// Filter on member_number
			else if("member_number" == $filter[0])
			{
				$query = $query->where("member.member_number", $filter[1], $filter[2]);
			}
		}

		// Return account
		return (array)$query->first();
	}
}