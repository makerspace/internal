<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingAccount extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = "entity";
	protected $relations = array("accounting_account");

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'account_number',
//		'title',
//		'description',
	];

//	protected $with = array("transactions");

/*
	public function transactions()
	{
		return $this->hasMany("App\Models\AccountingTransaction");
	}
*/
}
