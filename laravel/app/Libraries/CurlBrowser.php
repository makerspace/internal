<?php

namespace App\Libraries;

use \DOMDocument;
use \DOMXPath;
use \Exception;

class CurlBrowser
{
	var $curl_handle;
	var $debug = false;
	var $http_code = null;
	var $html = null;

	/**
	 * Initialize curl and use cookies
	 * @todo Unique name for cookie files
	 */
	public function __construct()
	{
		$this->curl_handle = curl_init();
		curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl_handle, CURLOPT_COOKIEFILE, "cookiefile.txt");
		curl_setopt($this->curl_handle, CURLOPT_COOKIEJAR,  "cookiefile.txt");
		curl_setopt($this->curl_handle, CURLOPT_USERAGENT,  "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0");

		if($this->debug === true)
		{
			curl_setopt($this->curl_handle, CURLOPT_VERBOSE, true);
			curl_setopt($this->curl_handle, CURLOPT_HEADER, true);
		}
	}

	/**
	 * Close the curl handler when the object is destructed
	 */
	public function __destruct()
	{
		curl_close($this->curl_handle);
	}

	/**
	 * HTTP GET request
	 */
	public function Get($url)
	{
		// Set the URL and clear POST data
		curl_setopt($this->curl_handle, CURLOPT_URL,  $url);
		curl_setopt($this->curl_handle, CURLOPT_POST, false);

		// Execute the request
		$this->html = curl_exec($this->curl_handle);

		// Check HTTP status code
		$this->_checkStatusCode();
	}

	/**
	 * HTTP POST request
	 */
	public function Post($url, $post, $json = false)
	{
		// Set the URL
		curl_setopt($this->curl_handle, CURLOPT_URL,  $url);

		// JSON or POST data?
		if($json === true)
		{
			$data = json_encode($post);
			curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data))
			);
		}
		else
		{
			$data = http_build_query($post, "", "&");
		}

		// Set POST data
		curl_setopt($this->curl_handle, CURLOPT_POST, true);
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $data);

		// Execute the request
		try
		{
			$this->html = curl_exec($this->curl_handle);
		}
		catch(Exception $e)
		{
			echo "Error: ".$e->GetMessage()."\n";
		}

		// Check HTTP status code
		$this->_checkStatusCode();
	}

	/**
	 * The returned data is supposed to be a JSON array. Parse it and return it as an PHP array
	 */
	public function GetJson()
	{
		return json_decode($this->html);
	}

	/**
	 * Return the latest HTTP status code
	 */
	public function StatusCode()
	{
		return $this->http_code;
	}

	/**
	 * Parse the HTML into A DOM and return a new DOMXPath object
	 */
	public function getXPath()
	{
		$page = new DOMDocument();
		@$page->loadHTML($this->html);
		return new DOMXPath($page);
	}

	private function _checkStatusCode()
	{
		// Check HTTP status code
		$this->http_code = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
		if($this->http_code == 200)
		{
			// A 200 means everything went OK
			return true;
		}
		else if($this->http_code == 302)
		{
			// A 302 means a redirect, then we should save the URL
			$this->last_url = curl_getinfo($this->curl_handle, CURLINFO_EFFECTIVE_URL);
			echo "Redirect: {$this->last_url}\n";
		}
		else
		{
			// Other status codes should throw an error
			echo $this->html;
			throw(new Exception("Received a {$this->http_code} HTTP status code. Expected a 200 or 302 from server."));
		}
	}
}
