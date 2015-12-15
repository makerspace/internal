<?php

/**
 * Configuration
 */
define("URL_LOGIN", "https://foretag.ib.seb.se/kgb/api/auth/coll");


/**
 * Download an parses data from Bankgirot
 */
class EconomyParserSEBHTML
{
	var $cookie = [];
	var $html;
	var $html_index;
	var $debug = false;

	/**
	 * Set common parameters for cURL
	 */
	function curl_common($curl_handle)
	{
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_COOKIEFILE, "cookiefile.txt");
		curl_setopt($curl_handle, CURLOPT_COOKIEJAR, "cookiefile.txt");
		curl_setopt($curl_handle, CURLOPT_USERAGENT, "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0");

		if($this->debug === true)
		{
			curl_setopt($curl_handle, CURLOPT_VERBOSE, true);
			curl_setopt($curl_handle, CURLOPT_HEADER, true);
		}
	}

	/**
	 *
	 */
	function _curlDownload($url)
	{
		$curl_handle = curl_init($url);
		$this->curl_common($curl_handle);
		$html = curl_exec($curl_handle);

		// Check HTTP status code
		$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if($http_code != 200)
		{
			echo "$html";
			throw(new Exception("Did not receive a 200 OK from server while trying to downloading data."));
		}

		curl_close($curl_handle);

		return $html;
	}

	/**
	 *
	 */
	function _curlPost($url, $data)
	{
		$curl_handle = curl_init($url);
		$this->curl_common($curl_handle);

		curl_setopt($curl_handle, CURLOPT_POST, true);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($data, "", "&"));

		$html = curl_exec($curl_handle);

		// Check HTTP status code
		$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if($http_code != 200)
		{
			throw(new Exception("Did not receive a 200 OK from server while trying to downloading data."));
		}

		curl_close($curl_handle);

		return $html;
	}

	/**
	 * Post the login form
	 */
	function Login($civicregno)
	{
		$post["WA1"] = $civicregno;
		$post["WA2"] = "6";
		$post["WA3"] = "";
		$post["WA4"] = "";

		// Logga in med mobilt bankid
		$curl_handle = curl_init(URL_LOGIN);
		$this->curl_common($curl_handle);
		curl_setopt($curl_handle, CURLOPT_POST, true);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($post, "", "&"));
		$html = curl_exec($curl_handle);


		// Check HTTP return code
		$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if($http_code != 200)
		{
			throw(new Exception("Did not receive a 200 OK from server while trying to downloading data."));
		}

		curl_close($curl_handle);

		$json = json_decode($html);
		print_r($json);

		// Poll until we get a result that is not "Pending"
		echo "Started BankID poll\n";
		$status = "C";
		while($status == "C")
		{
			sleep(2);
			echo "Polling BankID\n";
			$result = $this->_pollBankId($civicregno, $json->OrderReference);
			$status = $result->Status;
			echo "Received Status=$status\n";
		}

		if($status == "R")
		{
			echo "OK!\n";
		}
		else
		{
			die("Expected status to be R\n");
		}

		// Process with login
		echo "OK, processing with login.\n";
		$curl_handle = curl_init("https://foretag.ib.seb.se/nauth2/Authentication/Auth?SEB_Referer=/427/m3e");
		$this->curl_common($curl_handle);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_handle, CURLOPT_POST, true);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($post, "", "&"));

		$html = curl_exec($curl_handle);

		// Check HTTP status code
		$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if($http_code != 200)
		{
			throw(new Exception("Did not receive a 200 OK from server while trying to downloading data."));
		}

		curl_close($curl_handle);


		$curl_handle = curl_init("https://foretag.ib.seb.se/kgb/1000/1000/kgb1010.aspx?M1=SHOW");
		$this->curl_common($curl_handle);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);

		$html = curl_exec($curl_handle);

		// Check HTTP status code
		$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if($http_code != 200)
		{
			throw(new Exception("Did not receive a 200 OK from server while trying to downloading data."));
		}

		curl_close($curl_handle);

		echo "Successfully logged in?\n";

		return true;
	}

	/**
	 *
	 */
	function _pollBankId($civicregno, $token)
	{
		$post = [];
		$post["WA1"] = $civicregno;
		$post["WA2"] = "6";
		$post["WA3"] = $token;
		$post["WA4"] = "C";

		$curl_handle = curl_init(URL_LOGIN);
		$this->curl_common($curl_handle);
		curl_setopt($curl_handle, CURLOPT_POST, true);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($post, "", "&"));
		$html = curl_exec($curl_handle);

		return json_decode($html);
	}

	/**
	 * Download the account history, parse and return an array
	 */
	function GetAccountHistory()
	{
		$this->html = $this->_curlDownload("https://foretag.ib.seb.se/kgb/1000/1100/kgb1102.aspx?P1=11010011&P2=8");
	}

	/**
	 * This posts the form in the "Show more" button to get more valuable data about a transaction
	 *
	 * The state of each "Show more" seems to be cached on the server, so we can click all those buttons before parsing the account history
	 */
	function GetAccountHistoryMetadata($transaction)
	{
		$form = $this->ParseForm();

		$form['IKFMaster$MainPlaceHolder$repAccountActivitiesInlan$ctl'.$transaction.'$BTN_INFO.x'] = 5;
		$form['IKFMaster$MainPlaceHolder$repAccountActivitiesInlan$ctl'.$transaction.'$BTN_INFO.y'] = 5;

		$curl_handle = curl_init("https://foretag.ib.seb.se/kgb/1000/1100/kgb1102.aspx?P1=11010011&P2=8");
		$this->curl_common($curl_handle);

		curl_setopt($curl_handle, CURLOPT_POST, true);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($form, "", "&"));
		curl_setopt($curl_handle, CURLOPT_REFERER, "https://foretag.ib.seb.se/kgb/1000/1100/kgb1102.aspx?P1=11010011&P2=8");

		$html = curl_exec($curl_handle);

		// Check HTTP status code
		$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if($http_code != 200)
		{
			throw(new Exception("Did not receive a 200 OK from server while trying to downloading data."));
		}

		curl_close($curl_handle);

		$this->html = $html;
	}


	/**
	 * The account history have a "Show more" button which provide us with more information.
	 * This function returns the id's of all those buttons.
	 */
	function GetFoldable()
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
	function LoadTransactionFile($file)
	{
		$this->html = file_get_contents($file);
	}

	/**
	 * Save the current in-memory HTML to a file
	 *
	 * Parameters:
	 *   $file  The output file where the HTML should be put
	 */
	function SaveTransactionFile($file)
	{
		file_put_contents($file, $this->html);
	}

	/**
	 *
	 */
	function ParseForm()
	{
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
	 * Parse the account history HTML and return an array
	 */
	function ParseAccountHistory()
	{
		$page = new DOMDocument();
		@$page->loadHTML($this->html);
		$xpath = new DOMXPath($page);

		// Get the table with the transactions
		$table = $xpath->query("//div[@id='IKFMaster_MainPlaceHolder_pnlActivitiesInlan']/table");

		// Get all <tr>'s except the first one, which is a header
		$rows = $xpath->query("tr[@class='oddrow nodivider']", $table[0]);

		// Loop through the rows and extract data
		$data = [];
		foreach($rows as $i => $elem)
		{
			// Get input
			$parent = $xpath->query("td/input", $elem);

			// Read id
			$id = $this->_parseId($parent[0]->getAttribute("name"));

			// Get the columns (<td>)
			$cols = $xpath->query("td", $elem);
			{
				$data[$id] = array(
					'date1'    => trim($cols[1]->textContent), // Bokföringsdatum
					'date2'    => trim($cols[2]->textContent), // Valutadatum
					'verif'    => trim($cols[3]->textContent), // Verifikationsnummer
					'text'     => trim($cols[4]->textContent), // Text/mottagare
					'amount'   => $this->_parseCurrency(($cols[5]->textContent)), // Belopp
					'total'    => $this->_parseCurrency(($cols[6]->textContent)), // Saldo
				);
			}
		}

		return $data;
	}

	/**
	 *
	 */
	function ParseAccountHistoryMetadata()
	{
//		$this->html = iconv("UTF-8", "ISO-8859-1", $this->html);

		$page = new DOMDocument();
		@$page->loadHTML($this->html);
		$xpath = new DOMXPath($page);

		$transactions = [];
		$groups = $xpath->query("//table[@class='key-value-table']");
		foreach($groups as $group)
		{
			// Get parent div
			$parent = $xpath->query("../div", $group);
			if(empty($parent)) { die(":(\n"); }

			// Read id
			$id = $this->_parseId($parent[0]->getAttribute("id"));

			$elements = $xpath->query("tr[position()!=1]", $group);
			$metadata = [];
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
						$metadata["from"]["account"] = $value;
					}

					else if($key == "Egen notering")
					{
						$metadata["note"] = $value;
					}

					else if($key == "Namn och adress")
					{
						$metadata["_{$key}"] = $value;
					}
					else if($key == "Krediterat belopp")
					{
						$metadata["_{$key}"] = $value;
					}
					else if($key == "")
					{
						$metadata["_{$key}"] = $value;
					}
					else if($key == "")
					{
						$metadata["_{$key}"] = $value;
					}

					// To account
					else if($key == "Till konto")
					{
						$metadata["to"]["type"] = "account";
						$metadata["to"]["account"] = $value;
					}
					else if($key == "Text på mottagarens avi")
					{
						$metadata["to"]["message"] = $value;
					}
					else if($key == "Bank")
					{
						$metadata["to"]["bank"] = $value;
					}


					// Bankgiro / Plusgiro
					else if($key == "Pg-nummer")
					{
						$metadata["to"]["type"] = "plusgiro";
						$metadata["to"]["account"] = $value;
					}
					else if($key == "Bg-nummer")
					{
						$metadata["to"]["type"] = "bankgiro";
						$metadata["to"]["account"] = $value;
					}
					else if($key == "Namn")
					{
						$metadata["to"]["name"] = $value;
					}
					else if($key == "Mottagaren tillhanda")
					{
						$metadata["to"]["received"] = $value;
					}
					else if($key == "OCR/Meddelande")
					{
						$metadata["to"]["message"] = $value;
					}

					// Exchange stuff
					else if($key == "Valutakurs")
					{
						$metadata["exchange"]["valutakurs"] = $this->_parseCurrency($value);
					}
					else if($key == "Utländskt belopp")
					{
						list($currency, $amount) = explode(" ", $value);
						$metadata["exchange"]["original_currency"] = $currency;
						$metadata["exchange"]["original_amount"] = $this->_parseCurrency($amount);
					}
					else if($key == "Valutaväxlingspåslag")
					{
						$metadata["exchange"]["Valutaväxlingspåslag"] = $value;
					}
					else if($key == "Köpdag")
					{
						$metadata["exchange"]["date"] = $value;
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
					)
					{
					}

					// Save all other data
					else
					{
						$metadata["_{$key}"] = $value;
//						die("$key = $value\n");
					}
				}
			}
			$transactions[$id] = $metadata;
		}

		return $transactions;
	}

	/**
	 * Download the complete account history, including the "Show more" data.
	 *
	 * A *.html file is saved for each HTTP request.
	 * The file saved at the end is probably the one wou need.
	 */
	function SaveAccountHistoryRaw()
	{
		// Create a new directory
		$date = date("Y-m-d_H:i:s");
		mkdir("./data/{$date}");

		$data = $this->GetAccountHistory();
		$this->SaveTransactionFile("./data/{$date}/accounthistory.html");

		// Find all "show more" buttons on transactions and go through them
		$foldable = $this->getFoldable();
		foreach($foldable as $fold_id)
		{
			// TODO: Debugging
			echo "Processing fold $fold_id\n";

			// This sends an request to the server to expand the "Show more" section
			// The expand will be permanent for this session
			$this->GetAccountHistoryMetadata($fold_id);

			// Save the metadata to a file
			$this->SaveTransactionFile("./data/{$date}/fold_{$fold_id}.html");
		}

		return $date;
	}

	/**
	 * Compose a complete account history as an array
	 */
	function AccountHistory()
	{
		// Parse data
		$data = $this->ParseAccountHistory();
		$metadata = $this->ParseAccountHistoryMetadata();

		// Go through the account history and append data from each "Show more" section
		foreach($data as $i => &$row)
		{
			// Only if there is a "Show more" section
			if(array_key_exists($i, $metadata))
			{
				$data[$i]["metadata"] = $metadata[$i];
			}
		}

		return $data;
	}

	/**
	 * Parse the currency and make an integer of it
	 */
	function _parseCurrency($input)
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

//		return preg_replace("/[^0-9]/", "", $input);
		return $input;
	}

	/**
	 *
	 */
	function _parseId($input)
	{
		preg_match("/ctl(\d+)/", $input, $matches);
		return $matches[1];
	}
}
