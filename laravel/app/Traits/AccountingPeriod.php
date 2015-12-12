<?php
namespace App\Traits;

use Illuminate\Http\Response;
use DB;

trait AccountingPeriod
{
	/**
	 * Get the entity_id of the specified accounting period
	 */
	protected function _getAccountingPeriodId($period)
	{
		return DB::table("accounting_period")
			->where("name", "=", $period)
			->value("entity_id");
	}

	/**
	 * Check if the accounting period exists and return an error message if not
	 */
	protected function _accountingPeriodOrFail($period)
	{
		$accountingperiod_id = $this->_getAccountingPeriodId($period);
		if(null === $accountingperiod_id)
		{
			return Response()->json([
				"message" => "Could not find the specified accounting period",
			], 404);
		}
	}
}