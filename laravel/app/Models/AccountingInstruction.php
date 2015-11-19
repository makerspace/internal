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
	protected $table = 'accounting_instruction';

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
		'external_data',
		'data',
		'accounting_verification_series_id'
	];

//	protected $with = array('Transactions');

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
/*
	protected function getDateFormat()
	{
//		return \DateTime::ISO8601;//"Y-m-dTH:i:sZ";
		return "Y-m-d\TH:i:s";
	}
	*/
}
