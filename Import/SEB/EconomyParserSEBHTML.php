<?php

/**
 * Configuration
 */
define("URL_LOGIN", "https://foretag.ib.seb.se/kgb/api/auth/coll");

/**
 * Requirements
 */
require("../../laravel/app/Libraries/CurlBrowser.php");
use App\Libraries\CurlBrowser;

/**
 * Download an parses data from SEB företagsbank
 */
class EconomyParserSEBHTML
{
	var $html;
	var $verbose = 1;

	/**
	 * Post the login form
	 */
	public function Login($civicregno)
	{
		// Get cookie
		$x = new CurlBrowser;
		$x->Get("https://foretag.ib.seb.se/kgb/1000/1000/kgb1020.aspx");
		$x->Destroy();

		// Logga in med mobilt bankid
		$x = new CurlBrowser;
		$x->Post(URL_LOGIN, [
			"WA1" => $civicregno,
			"WA2" => "6",
			"WA3" => "",
			"WA4" => "",
		]);
		$json = $x->GetJson();
		$x->Destroy();

		// Poll until we get a result that is not "Pending"
		echo "Started BankID poll\n";
		$status = "C";
		while($status == "C")
		{
			sleep(2);
			echo "Polling BankID\n";
			$result = $this->_pollBankId($civicregno, $json->OrderReference);
			$status = $result->Status;

			$this->log("Received Status=$status", 2);
		}

		if($status == "R")
		{
			$this->Log("OK!", 2);
		}
		else
		{
			die("Expected status to be R, server sent {$status}\n");
		}

		$this->Log("OK, processing with login.", 2);

		// Process with login
		$x = new CurlBrowser;
		$x->Post("https://foretag.ib.seb.se/nauth2/Authentication/Auth?SEB_Referer=/427/m3e", [

			"WA1" => $civicregno,
			"WA2" => "6",
			"WA3" => "",
			"WA4" => "",
		]);
		$x->Destroy();


		// TODO: Ifall resultatet blir att den laddar "GET /kgb/kgb1065.htm" har något gått fel
		// https://foretag.ib.seb.se/kgb/kgb1060.htm = Ej inloggad

		// TODO: Betyder ett resultat av kgb1009.aspx att inloggning lyckades?

		$x = new CurlBrowser;
		$x->Get("https://foretag.ib.seb.se/kgb/1000/1000/kgb1010.aspx?M1=SHOW");
		$x->Destroy();

		$this->Log("Successfully logged in");

		return true;
	}

	/**
	 * Download the account history, parse and return an array
	 */
	public function DownloadAccountHistory($next = false)
	{
		$x = new CurlBrowser;

		if($next == true)
		{
			// Get the form and trigger the "Ytterligare kontohändelser" button
			$form = $this->_parseForm();
			$form["__EVENTTARGET"] = 'IKFMaster$MainPlaceHolder$lnk_Next';
			$form["__EVENTARGUMENT"] = "";
			$x->Post("https://foretag.ib.seb.se/kgb/1000/1100/kgb1102.aspx?P1=11010011&P2=8", $form);
		}
		else
		{
			// Just download the first page
			$x->Get("https://foretag.ib.seb.se/kgb/1000/1100/kgb1102.aspx?P1=11010011&P2=8");
		}

		$x->Destroy();
		$this->html = $x->html;
	}

	/**
	 * This posts the form in the "Show more" button to get more valuable data about a transaction
	 *
	 * The state of each "Show more" seems to be cached on the server, so we can click all those buttons before parsing the account history
	 */
	function DownloadAccountHistoryMetadata($transaction)
	{
		$form = $this->_parseForm();

		// Trigger click of the "Show more"
		$form['IKFMaster$MainPlaceHolder$repAccountActivitiesInlan$ctl'.$transaction.'$BTN_INFO.x'] = 5;
		$form['IKFMaster$MainPlaceHolder$repAccountActivitiesInlan$ctl'.$transaction.'$BTN_INFO.y'] = 5;

		// Post the form
		$x = new CurlBrowser;
		$x->Post("https://foretag.ib.seb.se/kgb/1000/1100/kgb1102.aspx?P1=11010011&P2=8", $form);
		$this->html = $x->html;
	}

	/**
	 * The account history have a "Show more" button which provide us with more information.
	 * This function returns the id's of all those buttons.
	 */
	function getShowMoreButtons()
	{
		$page = new DOMDocument();
		@$page->loadHTML($this->html);
		$xpath = new DOMXPath($page);

		// Get the table with the transactions
		$table = $xpath->query("//div[@id='IKFMaster_MainPlaceHolder_pnlActivitiesInlan']/table");

		// Get all <tr>'s except the first one, which is a header
		$rows = $xpath->query("tr[@class='oddrow nodivider']/td/input[@type='image'][@class='collapsibleArrowVisible']", $table[0]);

		// Loop through the rows and extract data
		$data = [];
		foreach($rows as $i => $elem)
		{
			$input = $elem->getAttribute("name");
			$data[] = $this->_parseId($input);
		}

		return $data;
	}

	/**
	 * Loads an external file with HTML data into $this->data
	 *
	 * Parameters:
	 *   $file  The input file with HTML data
	 */
	public function LoadTransactionFile($file)
	{
		$this->html = file_get_contents($file);
	}

	/**
	 * Save the current in-memory HTML to a file
	 *
	 * Parameters:
	 *   $file  The output file where the HTML should be put
	 */
	public function SaveTransactionFile($file)
	{
		file_put_contents($file, $this->html);
	}

	/**
	 * Download the account history, parse data and return as array
	 */
	public function GetAccountHistory($next = false)
	{
		$this->DownloadAccountHistory($next);
		return $this->ParseAccountHistory();
	}

	/**
	 *
	 */
	public function GetTransaction($id)
	{
		$buttons = $this->getShowMoreButtons();

		// Check if the transaction have a "Show more" button and click
		if(in_array($id, $buttons))
		{
			echo "OK, Show more\n";
			$this->DownloadAccountHistoryMetadata($id);
		}

		// Parse the account history (Including metadata from "Show more")
		echo "Parsing account history\n";
		$data = $this->ParseAccountHistory();

		// Return row from account history
		return $data[$id];
	}

	/**
	 * Parse the account history HTML and return an array
	 */
	public function ParseAccountHistory()
	{
		$page = new DOMDocument();
		@$page->loadHTML($this->html);
		$xpath = new DOMXPath($page);

		// Get the table with the transactions
		$table = $xpath->query("//div[@id='IKFMaster_MainPlaceHolder_pnlActivitiesInlan']/table");

		// Get all <tr>'s with transactions, except the first one, which is a header
		$rows = $xpath->query("tr[@class='oddrow nodivider']", $table[0]);

		// Loop through the rows and extract data
		$data = [];
		foreach($rows as $i => $elem)
		{
			// The "Show more" form is always there, but not always visible.
			$showMoreForm = $xpath->query("td/input", $elem)[0];

			// We use it to get the id of the row
			$id = $this->_parseId($showMoreForm->getAttribute("name"));

			// Check if the "Show more" is clickable or not
			$isMore = $showMoreForm->getAttribute("class") == "collapsibleArrowVisible";

			// Get the columns (<td>)
			$cols = $xpath->query("td", $elem);
			{
				$data[$id] = array(
					'date1'     => trim($cols[1]->textContent), // Bokföringsdatum
					'date2'     => trim($cols[2]->textContent), // Valutadatum
					'reference' => trim($cols[3]->textContent), // Verifikationsnummer
					'text'      => trim($cols[4]->textContent), // Text/mottagare
					'amount'    => $this->_parseCurrency(($cols[5]->textContent)), // Belopp
					'total'     => $this->_parseCurrency(($cols[6]->textContent)), // Saldo
				);
			}

			if($isMore)
			{
				$data[$id]["metadata"] = [];

				// Get the key-value <table> in the following <tr>, which contains the data of the "Show more"
				$groups = $xpath->query("following-sibling::tr[1]//table[@class='key-value-table']", $elem);
				foreach($groups as $group)
				{
					$elements = $xpath->query("tr[position()!=1]", $group);
					foreach($elements as $element)
					{
						$td = $xpath->query("td", $element);

						$key   = substr($td[0]->textContent, 0, -1);
						$value = $td[1]->textContent;
						if(!empty($value))
						{
							// From account
							if($key == "Från konto")
							{
								$data[$id]["metadata"]["from"]["account"] = $value;
							}

							else if($key == "Egen notering")
							{
								$data[$id]["metadata"]["note"] = $value;
							}

							else if($key == "Namn och adress")
							{
								$data[$id]["metadata"]["_{$key}"] = $value;
							}
							else if($key == "Krediterat belopp")
							{
								$data[$id]["metadata"]["_{$key}"] = $value;
							}
							else if($key == "")
							{
								$data[$id]["metadata"]["_{$key}"] = $value;
							}
							else if($key == "")
							{
								$data[$id]["metadata"]["_{$key}"] = $value;
							}

							// To account
							else if($key == "Till konto")
							{
								$data[$id]["metadata"]["to"]["type"] = "account";
								$data[$id]["metadata"]["to"]["account"] = $value;
							}
							else if($key == "Text på mottagarens avi")
							{
								$data[$id]["metadata"]["to"]["message"] = $value;
							}
							else if($key == "Bank")
							{
								$data[$id]["metadata"]["to"]["bank"] = $value;
							}


							// Bankgiro / Plusgiro
							else if($key == "Pg-nummer")
							{
								$data[$id]["metadata"]["to"]["type"] = "plusgiro";
								$data[$id]["metadata"]["to"]["account"] = $value;
							}
							else if($key == "Bg-nummer")
							{
								$data[$id]["metadata"]["to"]["type"] = "bankgiro";
								$data[$id]["metadata"]["to"]["account"] = $value;
							}
							else if($key == "Namn")
							{
								$data[$id]["metadata"]["to"]["name"] = $value;
							}
							else if($key == "Mottagaren tillhanda")
							{
								$data[$id]["metadata"]["to"]["received"] = $value;
							}
							else if($key == "OCR/Meddelande")
							{
								$data[$id]["metadata"]["to"]["message"] = $value;
							}

							// Exchange stuff
							else if($key == "Valutakurs")
							{
								$data[$id]["metadata"]["exchange"]["valutakurs"] = $this->_parseCurrency($value);
							}
							else if($key == "Utländskt belopp")
							{
								list($currency, $amount) = explode(" ", $value);
								$data[$id]["metadata"]["exchange"]["original_currency"] = $currency;
								$data[$id]["metadata"]["exchange"]["original_amount"] = $this->_parseCurrency($amount);
							}
							else if($key == "Valutaväxlingspåslag")
							{
								$data[$id]["metadata"]["exchange"]["Valutaväxlingspåslag"] = $value;
							}
							else if($key == "Köpdag")
							{
								$data[$id]["metadata"]["exchange"]["date"] = $value;
							}

							// Ignore
							else if(
								   $key == "Verifikationsnummer"
								|| $key == "Belopp"
								|| $key == "Kategori"
								|| $key == "Bokförings- och valutadag"
								|| $key == "Text på kontoutdrag"
								|| $key == "Överföringsdatum"
								|| trim($key) == "Belopp i SEK"

								// Kortbetalningar, i princip samma som på kontoutdrag
								|| $key == "Text"
								|| $key == "Inköpsställe"
							){}

							// Save all other data
							else
							{
								$data[$id]["metadata"]["_{$key}"] = $value;
		//						die("$key = $value\n");
							}
						}
					}
				}
			}
		}

		return $data;
	}


	/**
	 *
	 */
	protected function _pollBankId($civicregno, $token)
	{
		$x = new CurlBrowser;
		$x->Post(URL_LOGIN, [
			"WA1" => $civicregno,
			"WA2" => "6",
			"WA3" => $token,
			"WA4" => "C",
		]);
		$x->Destroy();
		return $x->GetJson();
	}

	/**
	 * Parse the form and get all <input type="hidden">
	 */
	protected function _parseForm()
	{
		// Parse the form
		$page = new DOMDocument();
		@$page->loadHTML($this->html);
		$xpath = new DOMXPath($page);

		$elements = $xpath->query("//input");
		$form = [];
		foreach($elements as $element)
		{
			if($element->getAttribute("type") == "image" || $element->getAttribute("type") == "submit")
			{
			}
			else
			{
				$form[$element->getAttribute("name")] = $element->getAttribute("value");
			}
		}

		return $form;
	}

	/**
	 * Parse the currency and make an integer of it
	 */
	protected function _parseCurrency($input)
	{
		// Trim
		$input = trim($input);

		// Remove thousand separators
		$input = str_replace(".", "", $input);

		// Change decimal separator into correct one
		$input = str_replace(",", ".", $input);

		// Remove currency
		$input = str_replace(" SEK", "", $input);

		// Cast to float
		$input = (float)$input;

		// Convert from kr till öre
		$input = $input * 100;

		// Round to non decimal number
//		$input = round($input);

		return $input;
	}

	/**
	 *
	 */
	protected function _parseId($input)
	{
		preg_match("/ctl(\d+)/", $input, $matches);
		return $matches[1];
	}


	protected function Log($message, $verbose_level = 1)
	{
		if($this->verbose >= $verbose_level)
		{
			$date = date("c");
			echo "[{$date}] {$message}\n";
		}
	}
}
