<?php

require("../../laravel/app/Libraries/CurlBrowser.php");

use App\Libraries\CurlBrowser;

define("LOGIN_URL", "https://tictail.com/user/signin");
define("DEBUG", false);

class TictailApiWrapper
{
	var $cookie = [];
	var $html;
	var $html_index;
	var $debug = false;

	/**
	 * Post the login form
	 * @todo Not implemented yet
	 */
	public function Login($username, $password)
	{
		return true;
	}

	/**
	 * Log out from the API
	 * @todo Not implemented yet
	 */
	public function Logout()
	{
	}

	/**
	 * Send an API call and return the data
	 */
	public function ApiCall($url, $data = null)
	{
/*
		// JSON encode the array
		$json = json_encode($data);

		// Debugging
		if(true === DEBUG)
		{
			echo "Sending:\n";
			echo "{$json}\n";
			echo "\n";
		}
*/
		// Send the request to the server
		$curl = new CurlBrowser;
		$curl->Get("https://api.tictail.com/v1/{$url}");

/*
		// Debugging
		if(true === DEBUG)
		{
			echo "Received:\n";
			echo "{$curl->html}\n";
			echo "\n";
		}
*/
		// Parse the resulting JSON into an array
		return $curl->GetJson();
	}
}