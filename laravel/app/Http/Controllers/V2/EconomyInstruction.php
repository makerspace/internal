<?php
namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\AccountingInstruction;
use App\Traits\AccountingPeriod;

class EconomyInstruction extends Controller
{
	use AccountingPeriod;
	
	/**
	 *
	 */
	function list(Request $request, $accountingperiod)
	{
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}

		$result = AccountingInstruction::list([
			["accountingperiod", "=", $accountingperiod],
//			["has_voucher", "=", true],
		]);

		// Pagination
		$per_page = (int)($request->get("per_page") ?? 25);

		// Return json array
		return Response()->json([
			"per_page"  => $per_page,
			"last_page" => ceil($result["count"] / $per_page),
			"total"     => $result["count"],
			"data"      => $result["data"],
		]);
	}

	/**
	 *
	 */
	function create(Request $request, $accountingperiod)
	{
		$json = $request->json()->all();

		// Get id of accounting period
		$accountingperiod_id = $this->_getAccountingPeriodId($accountingperiod);
		if(null === $accountingperiod_id)
		{
			return Response()->json([
				"message" => "Could not find the specified accounting period",
			], 404);
		}

		// We need to check that the provided account number is not in conflict with an existing one
/*
		if($this->_accountNumberIsExisting($json["account_number"]))
		{
			return Response()->json([
				"message" => "The specified account number does already exist",
			], 404); // TODO: Another error code
		}
*/
		// Create new accounting instruction
		$entity = new AccountingInstruction;
		$entity->title                         = $json["title"];
		$entity->description                   = $json["description"] ?? null;
		$entity->instruction_number            = $json["instruction_number"]  ?? null;
		$entity->accounting_date               = $json["accounting_date"];
		$entity->accounting_category           = $json["accounting_category"] ?? null;
		$entity->importer                      = $json["importer"]            ?? null;
		$entity->external_id                   = $json["external_id"]         ?? null;
		$entity->external_date                 = $json["external_date"]       ?? null;
		$entity->external_text                 = $json["external_text"]       ?? null;
		/*
		$entity->external_data                 = $json["external_data"]       ?? null;
		*/
		$entity->accounting_verification_serie = $json["accounting_verification_serie"] ?? null;
		$entity->transactions                  = $json["transactions"];
		$entity->accounting_period = $accountingperiod_id;
		$entity->save();

		return [
			"status" => "created",
			"input"  => $json,
			"entity" => $entity->toArray(),
		];
	}

	/**
	 *
	 */
	function read(Request $request, $accountingperiod, $id)
	{
		// Check that the specified accounting period exists
		$x = $this->_accountingPeriodOrFail($accountingperiod);
		if(null !== $x)
		{
			return $x;
		}

		// Load the instruction
		$data = AccountingInstruction::load($id);

		// Append files ("Verifikat")
		$data["files"] = [];
		if(!empty($data["external_id"]))
		{
			$dir = "/var/www/html/vouchers/{$data["external_id"]}";
			if(file_exists($dir))
			{
				foreach(glob("{$dir}/*") as $file)
				{
					$data["files"][] = basename($file);
				}
			}
		}

		// Generate an error if there is no such instruction
		if(false === $data)
		{
			return Response()->json([
				"message" => "No instruction with specified instruction number",
			], 404);
		}
		else
		{
			return $data;
		}
	}

	/**
	 *
	 */
	function update(Request $request, $accountingperiod, $id)
	{
		return ['error' => 'not implemented'];
	}

	/**
	 * Delete an instruction
	 */
	function delete(Request $request, $accountingperiod, $id)
	{
		$entity = Entity::delete($id);
	}
}