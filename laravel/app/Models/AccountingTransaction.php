<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingTransaction extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'accounting_transaction';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'accounting_verification_id',
		'accounting_account_id',
		'accounting_cost_center_id',
		'amount',
		'external_id',
		'description',
	];

	protected $with = array('Account');

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
//	protected $hidden = ['password', 'remember_token'];

	public function Account()
	{
		return $this->belongsTo("App\Models\AccountingAccount", "account_id");
	}

	public function instruction()
	{
		return $this->belongsTo("App\Models\AccountingInstruction", "accounting_instruction_id");
	}
}
