<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Entity;

class Relation extends Controller
{
	public function create(Request $request)
	{
		$json = $request->json()->all();

		echo "TODO: Create relation\n";

		print_r($json["entity1"]);
		$entity1 = Entity::load($json["entity1"]);
		print_r($entity1);

		// TODO: One to one
		// TODO: One to many

		return "";
	}

	public function delete()
	{
		return "TODO: Create relation";
	}
}