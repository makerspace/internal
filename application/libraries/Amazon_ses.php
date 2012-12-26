
<!-- saved from url=(0085)https://raw.github.com/joelcox/codeigniter-amazon-ses/master/libraries/Amazon_ses.php -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css"></style></head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">&lt;?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter Amazon SES
 *
 * A CodeIgniter library to interact with Amazon Web Services (AWS) Simple Email Service (SES)
 *
 * @package        	CodeIgniter
 * @category    	Libraries
 * @author        	Joël Cox
 * @link 			https://github.com/joelcox/codeigniter-amazon-ses
 * @link			http://joelcox.nl		
 * @license         http://www.opensource.org/licenses/mit-license.html
 */
class Amazon_ses {
	
	private $_ci;               		// CodeIgniter instance
 	private $_cert_path;				// Path to SSL certificate
	
	private $_access_key;				// Amazon Access Key
	private $_secret_key;				// Amazon Secret Access Key 
	public $region = 'us-east-1';		// Amazon region your SES service is located
	
	public $from;						// Default from e-mail address
	public $from_name;					// Vanity sender name
	public $reply_to;					// Default reply-to. Same as $from if omitted
	public $recipients = array();		// Contains all recipients (to, cc, bcc)
	public $subject;					// Message subject
	public $message;					// Message body
	public $message_alt;				// Message body alternative in plain-text
	public $charset;					// Character set
	
	public $debug = FALSE;					
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		log_message('debug', 'Amazon SES Class Initialized');
		$this-&gt;_ci =&amp; get_instance();
		
		// Load all config items
		$this-&gt;_ci-&gt;load-&gt;config('amazon_ses');
		$this-&gt;_access_key = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_access_key');
		$this-&gt;_secret_key = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_secret_key');
		$this-&gt;_cert_path = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_cert_path');			
		$this-&gt;from = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_from');
		$this-&gt;from_name = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_from_name');
		$this-&gt;charset = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_charset');
		
		// Check whether reply_to is not set
		if ($this-&gt;_ci-&gt;config-&gt;item('amazon_ses_reply_to') === FALSE)
		{
			$this-&gt;reply_to = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_from');
		}
		else
		{
			$this-&gt;reply_to = $this-&gt;_ci-&gt;config-&gt;item('amazon_ses_reply_to');
		}
		
		// Is our certificate path valid?
		if ( ! file_exists($this-&gt;_cert_path))
		{
			show_error('CA root certificates not found. Please &lt;a href="http://curl.haxx.se/ca/cacert.pem"&gt;download&lt;/a&gt; a bundle of public root certificates and/or specify its location in config/amazon_ses.php');
		}
		
		// Load Phil's cURL library as a Spark or the normal way
		if (method_exists($this-&gt;_ci-&gt;load, 'spark'))
		{
			$this-&gt;_ci-&gt;load-&gt;spark('curl/1.0.0');
		}
		
		$this-&gt;_ci-&gt;load-&gt;library('curl');
		
	}
	
	/**
	 * From
	 *
	 * Sets the from address.
	 * @param 	string 	email address the message is from
	 * @param 	string 	vanity name from which the message is sent
	 * @return 	mixed
	 */
	public function from($from, $name = FALSE)
	{
		
		$this-&gt;_ci-&gt;load-&gt;helper('email');
		
		if ($name)
		{
			$this-&gt;from_name = $name;
		}
		
		if (valid_email($from))
		{
			$this-&gt;from = $from;			
			return $this;
		}
	
		log_message('debug', 'From address is not valid');
		return FALSE;
	
	}
	
	/**
	 * To
	 *
	 * Sets the to address.
	 * @param 	string 	to email address
	 * @return 	mixed 
	 */
	public function to($to)
	{
		$this-&gt;_add_address($to, 'to');
		return $this;
	}
	
	/**
	 * CC
	 *
	 * Sets the cc address.
	 * @param 	string 	cc email address
	 * @return 	mixed 
	 */
	public function cc($cc)
	{	
		$this-&gt;_add_address($cc, 'cc');
		return $this;
	}
	
	/**
	 * BBC
	 *
	 * Sets the bcc address.
	 * @param 	string 	bcc email address
	 * @return 	mixed 
	 */
	public function bcc($bcc)
	{
		$this-&gt;_add_address($bcc, 'bcc');
		return $this;
	}
	
	/**
	 * Subject
	 *
	 * Sets the email subject.
	 * @param 	string	the subject
	 * @return 	mixed
	 */
	public function subject($subject)
	{
		$this-&gt;subject = $subject;
		return $this;
	}
	
	/**
	 * Message
	 *
	 * Sets the message.
	 * @param 	string	the message to be sent
	 * @return 	mixed
	 */
	public function message($message)
	{
		$this-&gt;message = $message;
		return $this;
	}
	
	/**
	 * Message alt
	 *
	 * Sets the alternative message (plain-text) for when HTML email is not supported by email client.
	 * @param 	string 	the alternative message to be sent
	 * @return 	mixed
	 */
	public function message_alt($message_alt)
	{
		$this-&gt;message_alt = $message_alt;
		return $this;
	}
	
	/**
	 * Send
	 *
	 * Sends off the email and make the API request.
	 * @param 	bool	whether to empty the recipients array on success
	 * @return 	bool
	 */
	public function send($destroy = TRUE)
	{
		
		// Create the message query string
		$query_string = $this-&gt;_format_query_string();
		
		// Pass it to the Amazon API	
		$response = $this-&gt;_api_request($query_string);		
		
		// Destroy recipients if set
		if ($destroy === TRUE)
		{
			unset($this-&gt;recipients);
		}
	
		return $response;
	
	}

	/**
	 * Verify address
	 *
	 * Verifies a from address as a valid sender
	 * @link 	http://docs.amazonwebservices.com/ses/latest/GettingStartedGuide/index.html?VerifyEmailAddress.html
	 * @param 	string	email address to verify as a sender
	 * @return 	bool
     * @author 	Ben Hartard
	 */
	public function verify_address($address)
	{
		
		// Prep our query string
		$query_string = array(
			'Action' =&gt; 'VerifyEmailAddress',
			'EmailAddress' =&gt; $address
		);
		
		// Hand it off to Amazon		
		return $this-&gt;_api_request($query_string);
		
	}
	
	/**
	 * Address is verified
	 *
	 * Checks whether the supplied email address is verified with Amazon.
	 * @param	string	email address to be checked
	 * @return 	bool
	 */
	public function address_is_verified($address)
	{
		// Prep our query string
		$query_string = array(
			'Action' =&gt; 'ListVerifiedEmailAddresses'
		);

		// Get our list with verified addresses
		$response = $this-&gt;_api_request($query_string, TRUE);

		// Just return the text response when we're in debug mode
		if ($this-&gt;debug === TRUE)
		{
			return $response;
		}

		/**
		 * We don't want to introduce another dependency (a XML parser)
	     * so we just check if the address is present in the response
		 * instead of returning an array with all addresses.
		 */
		if (strpos($response, $address) === FALSE)
		{
			return FALSE;
		}
		
		return TRUE;	
		
	}
	
	/**
	 * Debug
	 *
	 * Makes send() return the actual API response instead of a bool
	 * @param 	bool
	 * @return 	void
	 */
	public function debug($bool)
	{
		$this-&gt;debug = (bool) $bool;
	}
	
	/**
	 * Add address
	 *
	 * Add a new address to arecipients list.
	 * @param 	string 	email address
	 * @param	string 	recipient type (e.g, to, cc, bcc)
	 */
	private function _add_address($address, $type)
	{
		
		$this-&gt;_ci-&gt;load-&gt;helper('email');
		
		// Take care of arrays and comma delimitered lists	
		if ( ! $this-&gt;_format_addresses($address, $type))	
		{	
			$this-&gt;_ci-&gt;load-&gt;helper('email');
						
			if (valid_email($address))
			{
				$this-&gt;recipients[$type][] = $address;
			}
			else
			{
				log_message('debug', ucfirst($type) . ' e-mail address is not valid');
				return FALSE;	
			}
			
		}
		
	}
	
	/**
	 * Format addresses
	 *
	 * Formats arrays and comma delimertered lists.
	 * @param 	mixed 	the list with addresses
	 * @param 	string 	recipient type (e.g, to, cc, bcc)
	 */
	private function _format_addresses($addresses, $type)
	{
		// Make sure we're dealing with a proper type
		if (in_array($type, array('to', 'cc', 'bcc'), TRUE) === FALSE)
		{
			log_message('debug', 'Unknow type queue.');
			return FALSE;
		}
		
		// Check if the input is an array
		if (is_array($addresses))
		{
			foreach ($addresses as $address)
			{
				$this-&gt;{$type}($address);
			}
			
			return TRUE;
		}
		// Check if we're dealing with a comma seperated list
		elseif (strpos($addresses, ', ') !== FALSE)
		{
			
			// Write each element
			$addresses = explode(', ', $addresses);
			
			foreach ($addresses as $address)
			{
				$this-&gt;{$type}($address);
			}
			
			return TRUE;	
		}
			
		return FALSE;
			
	}
	
	/**
	 * Format query string
	 *
	 * Generates the query string for email
	 * @return	array
	 */
	private function _format_query_string()
	{
		$query_string = array(
			'Action' =&gt; 'SendEmail',
			'Source' =&gt; ($this-&gt;from_name ? $this-&gt;from_name . ' &lt;' . $this-&gt;from . '&gt;' : $this-&gt;from),
			'Message.Subject.Data' =&gt; $this-&gt;subject,
			'Message.Body.Text.Data' =&gt; (empty($this-&gt;message_alt) ? strip_tags($this-&gt;message) : $this-&gt;message_alt),
			'Message.Body.Html.Data' =&gt; $this-&gt;message
		);
		
		// Add all recipients to array
		if (isset($this-&gt;recipients['to']))
		{
			for ($i = 0; $i &lt; count($this-&gt;recipients['to']); $i++)
			{
				$query_string['Destination.ToAddresses.member.' . ($i + 1)] = $this-&gt;recipients['to'][$i]; 
			}	
		}
		
		if (isset($this-&gt;recipients['cc']))
		{
			for ($i = 0; $i &lt; count($this-&gt;recipients['cc']); $i++)
			{
				$query_string['Destination.CcAddresses.member.' . ($i + 1)] = $this-&gt;recipients['cc'][$i]; 
			}
		}
		
		if (isset($this-&gt;recipients['bcc']))
		{
			for ($i = 0; $i &lt; count($this-&gt;recipients['bcc']); $i++)
			{
				$query_string['Destination.BccAddresses.member.' . ($i + 1)] = $this-&gt;recipients['bcc'][$i]; 
			}
		}
		
		if (isset($this-&gt;reply_to) AND ( ! empty($this-&gt;reply_to))) 
		{
			$query_string['ReplyToAddresses.member'] = $this-&gt;reply_to;
		}
		
		
		// Add character encoding if set
		if ( ! empty($this-&gt;charset))
		{
			$query_string['Message.Body.Html.Charset'] = $this-&gt;charset;
			$query_string['Message.Body.Text.Charset'] = $this-&gt;charset;
			$query_string['Message.Subject.Charset'] = $this-&gt;charset;	
		}
				
		return $query_string;
		
	}
	
	/**
	 * Set headers
	 *
	 * Generates the X-Amzn headers
	 * @return 	string	headers including signed signature
	 */
	private function _set_headers()
	{
		$date = date(DATE_RSS);
		$signature = $this-&gt;_sign_signature($date);
		
		$this-&gt;_ci-&gt;curl-&gt;http_header('Content-Type', 'application/x-www-form-urlencoded');
		$this-&gt;_ci-&gt;curl-&gt;http_header('Date', $date);
		$this-&gt;_ci-&gt;curl-&gt;http_header('X-Amzn-Authorization', 'AWS3-HTTPS AWSAccessKeyId=' . $this-&gt;_access_key . ', Algorithm=HmacSHA256, Signature=' . $signature);
		
	}
	
	/**
	 * Sign signature
	 *
	 * Calculate signature using HMAC.
	 * @param	string	date used in the header
	 * @return	string 	RFC 2104-compliant HMAC hash
	 */
	private function _sign_signature($date)
	{
		$hash = hash_hmac('sha256', $date, $this-&gt;_secret_key, TRUE);	
		return base64_encode($hash);
	}
	
	/**
	 * Endpoint
	 *
	 * Generates the API endpoint.
	 * @return 	string	URL to the SES endpoint for the region
	 */
	private function _endpoint()
	{		
		return 'https://email.' . $this-&gt;region . '.amazonaws.com';
	}
	
	/**
	 * API request
	 *
	 * Send a request to the Amazon SES API using Phil's cURL lib.
	 * @param arra		query parameters that have to be added
	 * @param bool		whether to return the actual response
	 * @return mixed
	 */
	private function _api_request($query_string, $return = FALSE)
	{
		
		// Set the endpoint		
		$this-&gt;_ci-&gt;curl-&gt;create($this-&gt;_endpoint());
				
		$this-&gt;_ci-&gt;curl-&gt;post($query_string);
		$this-&gt;_set_headers();
		
		// Make sure we connect over HTTPS and verify
		if( ! isset($_SERVER['HTTPS']))
		{
			$this-&gt;_ci-&gt;curl-&gt;ssl(TRUE, 2, $this-&gt;_cert_path);
		}
		
		// Show headers when in debug mode		
		if($this-&gt;debug === TRUE)
		{
			$this-&gt;_ci-&gt;curl-&gt;option(CURLOPT_FAILONERROR, FALSE);
			$this-&gt;_ci-&gt;curl-&gt;option(CURLINFO_HEADER_OUT, TRUE);
		}
			
		$response = $this-&gt;_ci-&gt;curl-&gt;execute();

		// Return the actual response when in debug or if requested specifically
		if($this-&gt;debug === TRUE OR $return === TRUE)
		{
			return $response;
		}
				
		// Check if everything went okay
		if ($response === FALSE)
		{
			log_message('debug', 'API request failed.');
			return FALSE;
		}
		
		return TRUE;				
		
	}
		
}</pre></body></html>