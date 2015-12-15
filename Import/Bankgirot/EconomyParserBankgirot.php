<?php

namespace App\Libraries;

//use App\Models\AccountingInstruction;
//use App\Models\AccountingTransaction;
//use App\Models\AccountingAccount;

use \DOMDocument;
use \DOMXPath;
use \Exception;

/**
 * Download an parses data from Bankgirot
 */
class EconomyParserBankgirot implements EconomyParser
{
	var $cookie = [];
	var $html;
	var $html_index;

	/**
	 *
	 */
	function DownloadIndex()
	{
		$this->html = $this->_curlDownload("https://insupg.bankgirot.se/Elin_DayList.asp?metod=1&valuta=SEK&search_method=1&from_datum=1999-12-31&tom_datum=9999-12-31");
	}

	/**
	 * Downloads a list of transaction for a given day
	 * Saves the downloaded data in $this->data
	 *
	 * Parameters:
	 *   $date    A date in ISO8601 format (eg 2015-06-23)
	 */
	function DownloadTransactions($date)
	{
		$this->html = $this->_curlDownload("https://insupg.bankgirot.se/Elin_InsUpg.asp?bgimarkering=N&sidnr=1&valuta=SEK&DEB=J&from_Belopp=-99999999999999&date_method=2&sortering=BELOPP&from_datum={$date}&lopnr=000000000&firstrow=1&lastrow=40&typavdebitering=0&lv_from_daylist=true");
	}

	/**
	 * Use a session cookie while communication with the server.
	 *
	 * Parameters:
	 *   $cookie  A cookie for a valid login session (ASPSESSIONID*)
	 */
	function SetCookie($cookie_name, $cookie_value)
	{
		$this->cookie[0] = $cookie_name;
		$this->cookie[1] = $cookie_value;
	}

	function _curlDownload($url)
	{
		$curl_handle = curl_init($url);
		curl_setopt($curl_handle, CURLOPT_COOKIE, "{$this->cookie[0]}={$this->cookie[1]}");
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($curl_handle, CURLOPT_HEADER, true);
		$html = curl_exec($curl_handle);

		// Check HTTP return code
		$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if($http_code != 200)
		{
			print_r($html);
			throw(new Exception("Did not receive a 200 OK from server while trying to downloading data."));
		}

		return $html;
	}

	/**
	 * Loads an external file with HTML data into £this->data
	 *
	 * Parameters:
	 *   $file  The input file with HTML data
	 */
	function LoadTransactionFile($file)
	{
		$this->html = file_get_contents($file);
	}

	/**
	 * Parses the HTML data in $this->data
	 *
	 * Returns:
	 *   An array with transactions
	 */
	function ParseTransactions()
	{
		$page = new DOMDocument();
		@$page->loadHTML($this->html);
		$xpath = new DOMXPath($page);

		// Get the table with the transactions
		$table = $xpath->query("/html/body/form/table[tr[@class='ListBgheader']]");

		// Get all <tr>'s except the first one, which is a header
		$rows = $xpath->query("tr[position()!=1]", $table[0]);

		// Loop through the rows and extract data
		$data = [];
		foreach($rows as $i => $elem)
		{
			// Get the columns (<td>)
			$cols = $xpath->query("td", $elem);
			{
				$data[] = array(
					'amount'    => $this->_stripCurrency($cols[0]->textContent), // Amount
					'person'    => trim($cols[2]->textContent), // Person
					'messsage'  => trim($cols[3]->textContent), // Message / OCR
					'reference' => trim($cols[4]->textContent), // Reference number (id)
				);
			}
		}

		return $data;
	}

	/**
	 *
	 */
	function ParseIndex()
	{
		$page = new DOMDocument();
		@$page->loadHTML($this->data);
		$xpath = new DOMXPath($page);

		// Get the table with the transactions
		$table = $xpath->query("/html/body/table[tr[@class='ListBgheader']]");

		// Get all <tr>'s except the first one, which is a header
		$rows = $xpath->query("tr[position()!=1]", $table[0]);

		// Loop through the rows and extract data
		$data = [];
		foreach($rows as $i => $elem)
		{
			// Get the columns (<td>)
			$cols = $xpath->query("td", $elem);
			{
				$data[] = array(
					'date'   => trim($cols[1]->textContent), // Datum
					'num'    => trim($cols[2]->textContent), // Antal transaktioner
					'amount' => $this->_stripCurrency($cols[3]->textContent), // Summa
					'id'     => trim($cols[4]->textContent), // Löpnummer
				);
			}
		}

		return $data;
	}

	/**
	 * Strip all non numeric characters
	 */
	function _stripCurrency($input)
	{
		return preg_replace("/[^0-9]/", "", $input);
	}

	function Import($data)
	{
	}
}
