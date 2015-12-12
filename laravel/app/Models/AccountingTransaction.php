<?php
namespace App\Models;

/*
	protected $with = array('Account');
	public function Account()
	{
		return $this->belongsTo("App\Models\AccountingAccount", "account_id");
	}

	public function instruction()
	{
		return $this->belongsTo("App\Models\AccountingInstruction", "accounting_instruction_id");
	}
*/

class AccountingTransaction extends Entity
{
	protected $join = "accounting_transaction";
	protected $columns = [
		"entity.entity_id"                              => "entity.entity_id",
		"entity.created_at"                             => "DATE_FORMAT(entity.created_at, '%Y-%m-%dT%H:%i:%sZ') AS created_at",
		"entity.updated_at"                             => "DATE_FORMAT(entity.updated_at, '%Y-%m-%dT%H:%i:%sZ') AS updated_at",
		"entity.title"                                  => "entity.title",
		"entity.description"                            => "entity.description",
		'accounting_transaction.accounting_instruction' => 'accounting_transaction.accounting_instruction',
		'accounting_transaction.accounting_account'     => 'accounting_transaction.accounting_account',
		'accounting_transaction.accounting_cost_center' => 'accounting_transaction.accounting_cost_center',
		'accounting_transaction.amount'                 => 'accounting_transaction.amount',
		'accounting_transaction.external_id'            => 'accounting_transaction.external_id',
	];
//	protected $sort = ["invoice_number", "desc"];

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
			// Filter on accounting period
			if("accountingperiod" == $filter[0])
			{
				$query = $query
					->leftJoin("accounting_period", "accounting_period.entity_id", "=", "accounting_instruction.accounting_period")
					->where("accounting_period.name", $filter[1], $filter[2]);
			}
		}

		return $query->get();
	}
}