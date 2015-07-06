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
	protected $table = 'accounting_accounts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'account_id',
		'title',
		'description',
	];

	public function AccountingInstructions()
	{
		return $this->belongsToMany('App\Models\AccountingInstruction');
	}
}
