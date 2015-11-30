<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class Member extends Controller
{
	function read()
	{
		return [
			"member_id" => 27,
			"member_number" => 1234,
			"firstname" => "Nisse",
			"lastname" => "Olsson",
			"email" => "hej@olsson.se",
			"created_at" => "2015-01-01 01:15:00",
			"updated_at" => "2015-02-18 17:53:00",
		];
	}

	function list()
	{
		return [
			$this->read(),
			$this->read(),
			$this->read(),
		];
	}
}
