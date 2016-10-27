<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceRegistry extends Controller
{
	public function register(Request $request)
	{
		$json = $request->json()->all();

		// TODO: Register service

		return Response()->json([
			"status"  => "registered",
			"message" => "The service was successfully registered",
			"data"    => $json,
		], 200);
	}

	public function unregister(Request $request)
	{
		$json = $request->json()->all();

		// TODO: Unregister service

		return Response()->json([
			"status"  => "unregistered",
			"message" => "The service was successfully unregistered",
			"data"    => $json,
		], 200);
	}
}
