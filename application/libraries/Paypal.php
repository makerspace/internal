<?php

/**
 * PayPal Express Checkout Library for CodeIgniter 2.x
 * Totally rewritten to fit "my way", depends on DBConfig CI-model, cURL CI-library and php5-curl.
 * Uses the ENVIRONMENT constant to select which dbconfig vars to use.
 *
 * @methods set_ec($data_array, $custom = false), get_ec_details($token), 
 *			do_ec_payment($array /w token, ipn etc) + two private functions.
 *
 * Based upon code by Khairil Iszuddin Ismail: https://github.com/kidino/paypal_ec
 * Creds to him for creating the base structure (and showing me how it's done).
 *
 * @author Jim Nelin - http://JimNelin.com/
 * @license AGPL (GNU Affero General Public License, version 3)
 *
 * @todo: Rewrite to use http_build_query() instead and rewrite method _deformatNVP() 
 * @todo2: Rewrite to use cURL library for CodeIgniter to strip away code.
 *
 **/
class Paypal {

	// Codeigniter object
	private $CI;
	
	// Vars for API-credentials
	private $api_username;
	private $api_passsword;
	private $api_signature;
	private $api_endpoint;
	private $redirect_url;
	
	// Static vars
	private $api_version = 93; // Version of API to use
	
	/**
	 * Constructor for PayPal Library
	 * Sets vars for usage later.
	 */
	function __construct() {
		$this->CI = & get_instance();
		
		// Use sandbox or not - set $key accordingly to ENVIRONMENT.
		$key = '';
		if (constant('ENVIRONMENT') == 'development') {
			$key = '_dev';
		}
		
		// Use dbconfig to set internal vars
		$this->api_username = $this->dbconfig->{'paypal'.$env.'_username'};
		$this->api_passsword = $this->dbconfig->{'paypal'.$env.'_passsword'};
		$this->api_signature = $this->dbconfig->{'paypal'.$env.'_signature'};
		$this->api_endpoint = $this->dbconfig->{'paypal'.$env.'_endpoint'};
		$this->redirect_url = $this->dbconfig->{'paypal'.$env.'_auth_url'};
	}
	
	/**
	 * Makes a SetExpressCheckout request against PayPal
	 *
	 * @var $data associative array for SetExpressCheckout:
	 *	return_URL 		(REQUIRED)		=> the URL to be redirected from Paypal, which will do DoExpressCheckout and GetExpressCheckout
	 *	cancel_URL 		(REQUIRED)		=> the URL where the customer clicks cancel at Paypal
	 *	currency		(default SEK) 	=> currency code - USD|SEK|GBP|DKK|EUR|AUD|BRL|CAD|CZK|HKD|HUF|ILS|JPY|MYR|MXN|NZD|NOK|PHP|PLN|SGD|CHF|TWD|THB
	 *	type 			(default Sale)	=> 'Sale|Order|Authorization' - normally we use Sale for instant payment
	 *	get_shipping 	(default NOSHIPPING)	=> true or false
	 *	shipping_amount (default 0)		=> total shipping amount
	 *	tax_amount	 	(default 0)		=> total tax amount
	 *	handling_amount	(default 0)		=> total handling amount
	 *
	 *	products		(OPTIONAL)		=> array of product, example:
	 *
	 *	$data['products'] = array(
	 *  	array('name' => 'Lorem', 'amount' => 1, 	'desc' => 'Consectetur adipisicing elit', 		'number' => '101', 		'quantity' => 1),
	 *		array('name' => 'Ipsum', 'amount' => 0.65, 	'desc' => 'Sed do eiusmod tempor incididunt', 	'number' => '1234', 	'quantity' => 2),
	 *		array('name' => 'Dolar', 'amount' => 2.95, 	'desc' => 'Ut enim ad minim veniam', 			'number' => 'SKU1234', 	'quantity' => 1)
	 *	);
	 *
	 * @var $custom_data bool If you want to set your own SetExpressCheckout data (overrides defaults)
	 * @help url https://www.x.com/developers/paypal/documentation-tools/api/setexpresscheckout-api-operation-nvp
	 **/
	function set_ec($data = array(), $custom_data = false) {
		$nvpstr = '';
		
		if ($custom_data === false) {
		
			// Return and cancel url are required
			if (!isset($data['return_URL']) || !isset($data['cancel_URL'])) {
				trigger_error('PayPal API Library: Return and Cancel URL are required!', E_USER_ERROR);
				return false;
			}
			
			// Set accordingly
			$nvpstr.= "&CANCELURL=" . urlencode($data['cancel_URL']);
			$nvpstr.= "&RETURNURL=" . urlencode($data['return_URL']);
			
			// Currency
			if (isset($data['currency'])) {
				$nvpstr.= "&PAYMENTREQUEST_0_CURRENCYCODE=" . urlencode($data['currency']);
			} else {
				// Defaults to SEK
				$nvpstr.= "&PAYMENTREQUEST_0_CURRENCYCODE=SEK";     
			}
			
			// Custom note to buyer (usefull if no product is set)
			if (isset($data['desc'])) {
				$nvpstr.= "&PAYMENTREQUEST_n_DESC=" . urlencode($data['desc']);
			}
			
			// Sale, Authorization or Order
			if (isset($data['type'])) {
				$nvpstr.= "&PAYMENTREQUEST_0_PAYMENTACTION=" . urlencode($data['type']);
			} else {
				// Defaults to Sale
				$nvpstr.= "&PAYMENTREQUEST_0_PAYMENTACTION=Sale";
			}
			
			// Shipping address required, default = not needed
			if (isset($data['get_shipping'])) {
				$nvpstr.= ($data['get_shipping'] === true) ? "&NOSHIPPING=2" : "&NOSHIPPING=1";
			} else {
				$nvpstr.= "&NOSHIPPING=0";
			}
			
			// Shipping fee
			$shipping_amount = 0;
			if (isset($data['shipping_amount'])) {
				$shipping_amount = (float)$data['shipping_amount'];
				$nvpstr.= "&PAYMENTREQUEST_0_SHIPPINGAMT=" . urlencode(sprintf('%.2f', $shipping_amount));
			}
			
			// Handling fee
			$handling_amount = 0;
			if (isset($data['handling_amount'])) {
				$handling_amount = (float)$data['handling_amount'];
				$nvpstr.= "&PAYMENTREQUEST_0_HANDLINGAMT=" . urlencode(sprintf('%.2f', $handling_amount));
			}
			
			// Tax fee
			$tax_amount = 0;
			if (isset($data['tax_amount'])) {
				$tax_amount = (float)$data['tax_amount'];
				$nvpstr.= "&PAYMENTREQUEST_0_TAXAMT=" . urlencode(sprintf('%.2f', $tax_amount));
			}
			
			// Set parameters for all products.
			$total_amount = 0;
			foreach($data['products'] as $k => $v) {
			
				// Product name
				if (isset($v['name'])) {
					$nvpstr.= "&L_PAYMENTREQUEST_0_NAME$k=" . urlencode($v['name']);
				}
				
				// Product desc
				if (isset($v['desc'])) {
					$nvpstr.= "&L_PAYMENTREQUEST_0_DESC$k=" . urlencode($v['desc']);
				}
				
				// Product ID / SKU
				if (isset($v['number'])) {
					$nvpstr.= "&L_PAYMENTREQUEST_0_NUMBER$k=" . urlencode($v['number']);
				}
				
				// Product quantity
				if (isset($v['quantity'])) {
					$nvpstr.= "&L_PAYMENTREQUEST_0_QTY$k=" . urlencode($v['quantity']);
				}
				
				// Product price 
				if (isset($v['amount'])) {
				
					// Set price and update total_amount
					$nvpstr.= "&L_PAYMENTREQUEST_0_AMT$k=" . urlencode($v['amount']);
					
					// Base on quantity
					if (isset($v['quantity'])) {
						$total_amount+= (float)($v['amount'] * (int)$v['quantity']);
					} else {
						$total_amount+= (float)$v['amount'];
					}
				
				// Default to a free product.
				} else {
					$nvpstr.= "&L_PAYMENTREQUEST_0_AMT$k=" . urlencode('0.00');
					$total_amount+= 0;
				}
				
			}
			
			// Set total amounts
			$nvpstr.= "&PAYMENTREQUEST_0_ITEMAMT=" . urlencode(sprintf('%.2f', $total_amount));
			$nvpstr.= "&PAYMENTREQUEST_0_AMT=" . urlencode(sprintf('%.2f', $total_amount + $shipping_amount + $tax_amount + $handling_amount));
		
		// If custom data params are required 
		} else {
		
			// Walk through all array items and set them.
			foreach($data as $k => $v) {
				$nvpstr.= "&$k=" . urlencode($v);
			}
			
		}
		
		// Do actual SetExpressCheckout request against PayPal
		$return = $this->_api_call("SetExpressCheckout", $nvpstr);
		
		// Check return status
		$ack = strtoupper($return["ACK"]);
		if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
			$return['ec_status'] = true;
		} else {
			$return['ec_status'] = false;
		}
		
		return $return;
	}
	
	/**
	 * Makes a GetExpressCheckoutDetails request against PayPal	
	 * Should be called after redirection from Paypal to return_url to get transaction details.
	 *
	 * @var string $token Token from $_GET['TOKEN'] passed by Paypal when redirected to return_url
	 *
	 **/
	public function get_ec_details($token) {
	
		// Set token
		$nvpstr = "&TOKEN=" . $token;
		
		// Do GetExpressCheckoutDetails request against PayPal
		$return = $this->_api_call("GetExpressCheckoutDetails", $nvpstr);
		
		// Check return status
		$ack = strtoupper($return["ACK"]);
		if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
			$return['ec_status'] = true;
		} else {
			$return['ec_status'] = false;
		}
		
		return $return;
	}
	
	/**
	 *
	 * @ToDo: Rewrite, remove array from arg, restructure etc...
	 *
	 * Does the DoExpressCheckoutPayment
	 * - which is called after redirected from Paypal back to return_URL
	 * - most of the data can be obtained from GetExpressCheckoutDetails
	 * - make sure that you use the same "type" as you did during SetExpressCheckout
	 *
	 * @ec_details - associative array that contains the data for DoExpressCheckoutPayment
	 **/
	public function do_ec_payment($ec_details = array('token' => '','payer_id' => '','currency' => '','amount' => '','IPN_URL' => '','type' => 'Sale')) {
		$nvpstr = '';
		
		if (isset($ec_details['token'])) {
			$nvpstr.= '&TOKEN=' . urlencode($ec_details['token']);
		}
		if (isset($ec_details['payer_id'])) {
			$nvpstr.= '&PAYERID=' . urlencode($ec_details['payer_id']);
		}
		if (isset($ec_details['type'])) {
			$nvpstr.= '&PAYMENTREQUEST_0_PAYMENTACTION=' . urlencode($ec_details['type']);
		}
		if (isset($ec_details['amount'])) {
			$nvpstr.= '&PAYMENTREQUEST_0_AMT=' . urlencode($ec_details['amount']);
		}
		if (isset($ec_details['currency'])) {
			$nvpstr.= '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($ec_details['currency']);
		}
		if (isset($ec_details['IPN_URL'])) {
			$nvpstr.= '&NOTIFYURL=' . urlencode($ec_details['IPN_URL']);
		}
		$nvpstr.= '&IPADDRESS=' . urlencode($_SERVER['SERVER_NAME']);
		$resArray = $this->_api_call("DoExpressCheckoutPayment", $nvpstr);
		$ack = strtoupper($resArray["ACK"]);
		if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
			$resArray['ec_status'] = true;
		} else {
			$resArray['ec_status'] = false;
		}
		return $resArray;
	}
	
	/**
	 * Redirect to PayPal.com checkout.
	 *
	 * @var $token string NVP Token.
	 * @var $mobile bool Redirect to mobile-site or not. (default false)
	 **/
	public function redirect_to_paypal($token, $mobile = false) {
	
		// str-replace if $mobile != false
		$url = ($mobile != false ? str_replace("_express-checkout", "_express-checkout-mobile", $this->redirect_url) : $this->redirect_url);
		
		// Redirect to paypal.com here
		$location = $url . $token;
		header("Location: " . $location);
		exit;
	}
	
	/**
	 *
	 * Function to actually perform API calls to PayPal using API signature and $nvpStr
	 *
	 * @ToDo: Rewrite to use http_build_query() and the cURL-lib for CodeIgniter
	 *
	 * @var $method string Name of API method to call.
	 * @var $nvpStr string The NVP string to use.
	 *
	 * @return associtive array Containing the response from the server.
	 **/
	private function _api_call($method, $nvpStr) {
	
		// Init curl
		$ch = curl_init();
		
		// Set endpoint URL
		curl_setopt($ch, CURLOPT_URL, $this->api_endpoint);
		
		// Default CURL opts
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Tturning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		// Basic NVP vars to POST to API
		$nvpreq = "METHOD=" . urlencode($method);
		$nvpreq.= "&VERSION=" . urlencode($this->api_version);
		$nvpreq.= "&PWD=" . urlencode($this->api_password);
		$nvpreq.= "&USER=" . urlencode($this->api_username);
		$nvpreq.= "&SIGNATURE=" . urlencode($this->api_signature) . $nvpStr;
		
		// Setting the nvpreq to post /w curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
		// Get response from server
		$response = curl_exec($ch);
		
		// Convert NVPResponse to an assoc array
		$nvpResArray = $this->_deformatNVP($response);
		$nvpReqArray = $this->_deformatNVP($nvpreq);
		
		// Store in session (ToDo: why?)
		$this->CI->session->set_userdata(array('nvpReqArray' => $nvpReqArray));
		
		// If curl-error, store in session (ToDo: Don't!)
		if (curl_errno($ch)) {
			// moving to display page to display curl errors
			$this->CI->session->set_userdata(array('curl_error_no' => curl_errno($ch)));
			$this->CI->session->set_userdata(array('curl_error_msg' => curl_error($ch)));
			//Execute the Error handling module to display errors.
		}
		
		// Close cURL handler
		curl_close($ch);
		
		// Return result
		return $nvpResArray;
	}
	
	/**
	 * Takes NVPString and convert it to an associative array and also used for decoding the responses.
	 * It is usefull to search for a particular key and displaying arrays. (wat? // Jine)
	 *
	 * @IMPORTANT_TODO: Rewrite lib so this not needed anymore... (!)
	 *
	 * @nvpstr is NVPString.
	 * @nvpArray is Associative Array.
	 **/
	private function _deformatNVP($nvpstr) {
		$intial = 0;
		$nvpArray = array();
		
		while (strlen($nvpstr)) {
		
			//postion of Key
			$keypos = strpos($nvpstr, '=');
			
			//position of value
			$valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
			
			/*getting the Key and Value values and storing in a Associative Array */
			$keyval = substr($nvpstr, $intial, $keypos);
			$valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
			
			//decoding the respose
			$nvpArray[urldecode($keyval) ] = urldecode($valval);
			$nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
		}
		
		return $nvpArray;
	}
}
	
