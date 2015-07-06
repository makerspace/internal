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
	protected $table = 'accounting_transactions';

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

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
//	protected $hidden = ['password', 'remember_token'];

/*
	public function Transactions()
	{
		return $this->hasMany('EconomyAccountingTransaction');
	}
*/

	public function Instruction()
	{
		return $this->belongsToOne('App\Models\AccountingInstruction');
	}
}
