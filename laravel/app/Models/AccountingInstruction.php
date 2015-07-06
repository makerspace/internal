<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingInstruction extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'accounting_instructions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'verification_number',
		'accounting_category_id',
		'external_id',
		'external_date',
		'external_text',
		'data',
		'accounting_verification_series_id'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
//	protected $hidden = ['password', 'remember_token'];

	public function Transactions()
	{
		return $this->hasMany('App\Models\AccountingTransaction');
	}
}
