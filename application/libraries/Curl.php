
<!-- saved from url=(0078)https://raw.github.com/philsturgeon/codeigniter-curl/master/libraries/Curl.php -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css"></style></head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">&lt;?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Curl Class
 *
 * Work with remote servers via cURL much easier than using the native PHP bindings.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			http://philsturgeon.co.uk/code/codeigniter-curl
 */
class Curl {

	protected $_ci;                 // CodeIgniter instance
	protected $response = '';       // Contains the cURL response for debug
	protected $session;             // Contains the cURL handler for a session
	protected $url;                 // URL of the session
	protected $options = array();   // Populates curl_setopt_array
	protected $headers = array();   // Populates extra HTTP headers
	public $error_code;             // Error code returned as an int
	public $error_string;           // Error message returned as a string
	public $info;                   // Returned after request (elapsed time, etc)

	function __construct($url = '')
	{
		$this-&gt;_ci = &amp; get_instance();
		log_message('debug', 'cURL Class Initialized');

		if ( ! $this-&gt;is_enabled())
		{
			log_message('error', 'cURL Class - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');
		}

		$url AND $this-&gt;create($url);
	}

	public function __call($method, $arguments)
	{
		if (in_array($method, array('simple_get', 'simple_post', 'simple_put', 'simple_delete')))
		{
			// Take off the "simple_" and past get/post/put/delete to _simple_call
			$verb = str_replace('simple_', '', $method);
			array_unshift($arguments, $verb);
			return call_user_func_array(array($this, '_simple_call'), $arguments);
		}
	}

	/* =================================================================================
	 * SIMPLE METHODS
	 * Using these methods you can make a quick and easy cURL call with one line.
	 * ================================================================================= */

	public function _simple_call($method, $url, $params = array(), $options = array())
	{
		// Get acts differently, as it doesnt accept parameters in the same way
		if ($method === 'get')
		{
			// If a URL is provided, create new session
			$this-&gt;create($url.($params ? '?'.http_build_query($params, NULL, '&amp;') : ''));
		}

		else
		{
			// If a URL is provided, create new session
			$this-&gt;create($url);

			$this-&gt;{$method}($params);
		}

		// Add in the specific options provided
		$this-&gt;options($options);

		return $this-&gt;execute();
	}

	public function simple_ftp_get($url, $file_path, $username = '', $password = '')
	{
		// If there is no ftp:// or any protocol entered, add ftp://
		if ( ! preg_match('!^(ftp|sftp)://! i', $url))
		{
			$url = 'ftp://' . $url;
		}

		// Use an FTP login
		if ($username != '')
		{
			$auth_string = $username;

			if ($password != '')
			{
				$auth_string .= ':' . $password;
			}

			// Add the user auth string after the protocol
			$url = str_replace('://', '://' . $auth_string . '@', $url);
		}

		// Add the filepath
		$url .= $file_path;

		$this-&gt;option(CURLOPT_BINARYTRANSFER, TRUE);
		$this-&gt;option(CURLOPT_VERBOSE, TRUE);

		return $this-&gt;execute();
	}

	/* =================================================================================
	 * ADVANCED METHODS
	 * Use these methods to build up more complex queries
	 * ================================================================================= */

	public function post($params = array(), $options = array())
	{
		// If its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&amp;');
		}

		// Add in the specific options provided
		$this-&gt;options($options);

		$this-&gt;http_method('post');

		$this-&gt;option(CURLOPT_POST, TRUE);
		$this-&gt;option(CURLOPT_POSTFIELDS, $params);
	}

	public function put($params = array(), $options = array())
	{
		// If its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&amp;');
		}

		// Add in the specific options provided
		$this-&gt;options($options);

		$this-&gt;http_method('put');
		$this-&gt;option(CURLOPT_POSTFIELDS, $params);

		// Override method, I think this overrides $_POST with PUT data but... we'll see eh?
		$this-&gt;option(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));
	}

	public function delete($params, $options = array())
	{
		// If its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&amp;');
		}

		// Add in the specific options provided
		$this-&gt;options($options);

		$this-&gt;http_method('delete');

		$this-&gt;option(CURLOPT_POSTFIELDS, $params);
	}

	public function set_cookies($params = array())
	{
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&amp;');
		}

		$this-&gt;option(CURLOPT_COOKIE, $params);
		return $this;
	}

	public function http_header($header, $content = NULL)
	{
		$this-&gt;headers[] = $content ? $header . ': ' . $content : $header;
		return $this;
	}

	public function http_method($method)
	{
		$this-&gt;options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
		return $this;
	}

	public function http_login($username = '', $password = '', $type = 'any')
	{
		$this-&gt;option(CURLOPT_HTTPAUTH, constant('CURLAUTH_' . strtoupper($type)));
		$this-&gt;option(CURLOPT_USERPWD, $username . ':' . $password);
		return $this;
	}

	public function proxy($url = '', $port = 80)
	{
		$this-&gt;option(CURLOPT_HTTPPROXYTUNNEL, TRUE);
		$this-&gt;option(CURLOPT_PROXY, $url . ':' . $port);
		return $this;
	}

	public function proxy_login($username = '', $password = '')
	{
		$this-&gt;option(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
		return $this;
	}

	public function ssl($verify_peer = TRUE, $verify_host = 2, $path_to_cert = NULL)
	{
		if ($verify_peer)
		{
			$this-&gt;option(CURLOPT_SSL_VERIFYPEER, TRUE);
			$this-&gt;option(CURLOPT_SSL_VERIFYHOST, $verify_host);
			if (isset($path_to_cert)) {
				$path_to_cert = realpath($path_to_cert);
				$this-&gt;option(CURLOPT_CAINFO, $path_to_cert);
			}
		}
		else
		{
			$this-&gt;option(CURLOPT_SSL_VERIFYPEER, FALSE);
		}
		return $this;
	}

	public function options($options = array())
	{
		// Merge options in with the rest - done as array_merge() does not overwrite numeric keys
		foreach ($options as $option_code =&gt; $option_value)
		{
			$this-&gt;option($option_code, $option_value);
		}

		// Set all options provided
		curl_setopt_array($this-&gt;session, $this-&gt;options);

		return $this;
	}

	public function option($code, $value)
	{
		if (is_string($code) &amp;&amp; !is_numeric($code))
		{
			$code = constant('CURLOPT_' . strtoupper($code));
		}

		$this-&gt;options[$code] = $value;
		return $this;
	}

	// Start a session from a URL
	public function create($url)
	{
		// If no a protocol in URL, assume its a CI link
		if ( ! preg_match('!^\w+://! i', $url))
		{
			$this-&gt;_ci-&gt;load-&gt;helper('url');
			$url = site_url($url);
		}

		$this-&gt;url = $url;
		$this-&gt;session = curl_init($this-&gt;url);

		return $this;
	}

	// End a session and return the results
	public function execute()
	{
		// Set two default options, and merge any extra ones in
		if ( ! isset($this-&gt;options[CURLOPT_TIMEOUT]))
		{
			$this-&gt;options[CURLOPT_TIMEOUT] = 30;
		}
		if ( ! isset($this-&gt;options[CURLOPT_RETURNTRANSFER]))
		{
			$this-&gt;options[CURLOPT_RETURNTRANSFER] = TRUE;
		}
		if ( ! isset($this-&gt;options[CURLOPT_FAILONERROR]))
		{
			$this-&gt;options[CURLOPT_FAILONERROR] = TRUE;
		}

		// Only set follow location if not running securely
		if ( ! ini_get('safe_mode') &amp;&amp; ! ini_get('open_basedir'))
		{
			// Ok, follow location is not set already so lets set it to true
			if ( ! isset($this-&gt;options[CURLOPT_FOLLOWLOCATION]))
			{
				$this-&gt;options[CURLOPT_FOLLOWLOCATION] = TRUE;
			}
		}

		if ( ! empty($this-&gt;headers))
		{
			$this-&gt;option(CURLOPT_HTTPHEADER, $this-&gt;headers);
		}

		$this-&gt;options();

		// Execute the request &amp; and hide all output
		$this-&gt;response = curl_exec($this-&gt;session);
		$this-&gt;info = curl_getinfo($this-&gt;session);
		
		// Request failed
		if ($this-&gt;response === FALSE)
		{
			$errno = curl_errno($this-&gt;session);
			$error = curl_error($this-&gt;session);
			
			curl_close($this-&gt;session);
			$this-&gt;set_defaults();
			
			$this-&gt;error_code = $errno;
			$this-&gt;error_string = $error;
			
			return FALSE;
		}

		// Request successful
		else
		{
			curl_close($this-&gt;session);
			$this-&gt;last_response = $this-&gt;response;
			$this-&gt;set_defaults();
			return $this-&gt;last_response;
		}
	}

	public function is_enabled()
	{
		return function_exists('curl_init');
	}

	public function debug()
	{
		echo "=============================================&lt;br/&gt;\n";
		echo "&lt;h2&gt;CURL Test&lt;/h2&gt;\n";
		echo "=============================================&lt;br/&gt;\n";
		echo "&lt;h3&gt;Response&lt;/h3&gt;\n";
		echo "&lt;code&gt;" . nl2br(htmlentities($this-&gt;last_response)) . "&lt;/code&gt;&lt;br/&gt;\n\n";

		if ($this-&gt;error_string)
		{
			echo "=============================================&lt;br/&gt;\n";
			echo "&lt;h3&gt;Errors&lt;/h3&gt;";
			echo "&lt;strong&gt;Code:&lt;/strong&gt; " . $this-&gt;error_code . "&lt;br/&gt;\n";
			echo "&lt;strong&gt;Message:&lt;/strong&gt; " . $this-&gt;error_string . "&lt;br/&gt;\n";
		}

		echo "=============================================&lt;br/&gt;\n";
		echo "&lt;h3&gt;Info&lt;/h3&gt;";
		echo "&lt;pre&gt;";
		print_r($this-&gt;info);
		echo "&lt;/pre&gt;";
	}

	public function debug_request()
	{
		return array(
			'url' =&gt; $this-&gt;url
		);
	}

	public function set_defaults()
	{
		$this-&gt;response = '';
		$this-&gt;headers = array();
		$this-&gt;options = array();
		$this-&gt;error_code = NULL;
		$this-&gt;error_string = '';
		$this-&gt;session = NULL;
	}

}

/* End of file Curl.php */
/* Location: ./application/libraries/Curl.php */</pre></body></html>