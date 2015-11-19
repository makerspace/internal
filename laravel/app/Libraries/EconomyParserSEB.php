<?php

namespace App\Libraries;

use App\Models\AccountingInstruction;
use App\Models\AccountingTransaction;
use App\Models\AccountingAccount;

/**
 * Imports account history from *.csv provided SEB
 */
class EconomyParserSEB implements EconomyParser
{
	/**
	 *
	 */
	function __construct()
	{
	}

	/**
	 * This function takes comma separated data (*.csv) as input, parses it and make EconomyAccountingInstruction objects of the rows that is not already imported to the database.
	 *
	 * Arguments:
	 *   $data Should be comma separated data exported by the SEB web GUI
	 *
	 * Returns:
	 *   false on error
	 *   array of MonetaryTransaction objects on success
	 *
	 * TODO:
	 *   Make error handling work
	 */
	public function Import($data)
	{
		// The file exported by SEB is in ISO-8859-1, however we prefer UTF-8
		// @todo iconv();

		// Maker sure the line breaks are in Unix format
		$data = str_replace("\r\n", "\n", $data);
		$data = str_replace("\r",   "\n", $data);

		// Split rows into array
		$rows = explode("\n", $data);

		// Remove the csv header
		array_shift($rows);

		// Loop through the data and make objects
		$transactions = array();
		foreach($rows as $row)
		{
			// Ignore empty rows (normally one at EOF)
			if(empty($row))
			{
				continue;
			}

			// Get data from the csv row
			list(
				$date_accounting, // Bokföringsdatum
				$date_currency,   // Valutadatum
				$id,              // Verifikationsnummer
				$description,     // Text/mottagare
				$amount,          // Belopp
				$balance          // Saldo
			) = explode(",", $row);

			// Create a unique id number for the database
			$db_id = "seb_" . $date_accounting . "_" . $id;

			// TODO: Check if it already is in the database
			// SELECT * FROM accounting_instruction WHERE external_id = 

			// Create a new accounting instruction
			$instruction = new AccountingInstruction();
//			$instruction->id            = $date_accounting . $id; // The "Verifikationsdatum" provided by SEB is not unique per row. I *GUESS* that a combination of date and their id will be unique per row.
			$instruction->accounting_date = $date_accounting;
			$instruction->external_date   = $date_accounting;
			$instruction->external_id     = $id;
			$instruction->external_text   = $description;
			$instruction->external_data   = serialize($row);
			$instruction->title           = $description;
			$instruction->amount          = 123;
//			$instruction->verification_number = null;
//			$instruction->accounting_category_id = null;
//			$instruction->accounting_verification_series_id = null;
			$instruction->save();

			// TODO: Classificate transactions


			// Add transactions to the accounting instruction
			$transactions = [];

			// Get the account
			$account_id = 1930;
			$account = AccountingAccount::where('account_id', $account_id)->get();
			if($account->isEmpty())
			{
				die("Error: The requested account {$account_id} does not exist");
				// TODO: Error handling
			}

			// Create the transaction
			$transactions[] = new AccountingTransaction([
				'accounting_account_id' => $account[0]->id,
				'amount' =>  $amount
			]);

			// Get the account
			$account_id = 2999;
			$account = AccountingAccount::where('account_id', $account_id)->get();
			if($account->isEmpty())
			{
				die("Error: The requested account {$account_id} does not exist");
				// TODO: Error handling
			}

			// Create the transaction
			$transactions[] = new AccountingTransaction([
				'accounting_account_id' => $account[0]->id,
				'amount' => -$amount
			]);

			// Save transactions
			$instruction->Transactions()->saveMany($transactions);
		}

		return true;
	}
}