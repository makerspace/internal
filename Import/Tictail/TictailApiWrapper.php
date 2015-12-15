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
	 */
	public function Login($username, $password)
	{
		// Get the login form
		$post = $this->_getLoginForm();

		// This means we got a "302 Found" and are already logged in
		if($post === true)
		{
			return true;
		}

		// Attach username and password to the form
		$post["email"]  = $username;
		$post["passwd"] = $password;

		// Post form
		echo "Logging in\n";
		$login = new CurlBrowser;
		$login->Post(LOGIN_URL, $post);

		// Check the HTTP return code
		// A 200 ok means we probably have an error message
		if($login->StatusCode() == 200)
		{
			// Get the error message
			$xpath = $login->GetXPath();

			// Get the error message
			$message = $xpath->
				query("//form[@action='/user/signin']//div[@class='error-box']/text()")
				[0]->nodeValue;

			// Throw an exception
			if(!empty($message))
			{
				throw(new Exception("Login failed with the following reason: {$message}"));
			}
			else
			{
				throw(new Exception("Login failed for an unknown reason."));
			}

		}

		// "302 Found" = Login successful
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
	public function ApiCall($data)
	{
		// JSON encode the array
		$json = json_encode($data);

		// Debugging
		if(true === DEBUG)
		{
			echo "Sending:\n";
			echo "{$json}\n";
			echo "\n";
		}

		// Send the request to the server
		$curl = new CurlBrowser;
		$curl->Get("https://tictail.com/apiv2/rpc/v1/?jsonrpc={$json}");

		// Debugging
		if(true === DEBUG)
		{
			echo "Received:\n";
			echo "{$curl->html}\n";
			echo "\n";
		}

		// Parse the resulting JSON into an array
		return $curl->GetJson();
	}

	/**
	 * Download the login page and parse out the tokens
	 */
	private function _getLoginForm()
	{
		echo "Downloading login page\n";

		// Get the login page
		$login = new CurlBrowser;
		$login->Get(LOGIN_URL);

		if($login->StatusCode() == 302)
		{
			// We are already logged in
			echo "Already logged in";
			return true;
		}
		else
		{
			// We are not logged in, get all the <input type="hidden"> in the login form
			$xpath = $login->GetXPath();
			$form = $xpath->query("//form[@action='/user/signin']//input[@type='hidden']");
			$post = [];
			foreach($form as $elem)
			{
				$post[$elem->getAttribute("name")] = $elem->getAttribute("value");
			}

			// We should have some data in $post now
			if(empty($post))
			{
				throw(new Exception('Downloading login page failed. Did not findy any <input type="hidden">'));
			}
			else
			{
				// Everything went okay, return the hidden data from the form.
				return $post;
			}
		}
	}
}
