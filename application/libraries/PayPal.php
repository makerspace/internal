<?php

/**
 * PayPal Express Checkout Library for CodeIgniter 2.x
 * Totally rewritten to fit "my way", depends on DBConfig CI-model, cURL CI-library and php5-curl.
 * Uses the ENVIRONMENT constant to select which dbconfig vars to use.
 *
 * @methods set_ec($nvpdata, $custom = false), get_ec_details($nvptoken), 
 *			do_ec_payment($nvpdata /w paymentdetails and ipn, $type)
 *
 * Based upon code by Khairil Iszuddin Ismail: https://github.com/kidino/paypal_ec
 * Creds to him for creating the base structure (and showing me how it's done).
 *
 * @author Jim Nelin - http://JimNelin.com/
 * @license AGPL (GNU Affero General Public License, version 3)
 *
 * @todo: Rewrite to use cURL library for CodeIgniter to strip away code.
 **/
class PayPal {
	
	// Turn on/off verbose output
	public $debug = true;

	// Codeigniter object
	private $CI;
	
	// Vars for API-credentials
	private $api_username;
	private $api_passsword;
	private $api_signature;
	private $api_endpoint;
	private $redirect_url;
	private $notify_url;
	
	// Static vars
	private $api_version = 93; // Version of API to use
	
	/**
	 * Constructor for PayPal Library
	 * Sets vars for usage later.
	 */
	function __construct() {
		$this->CI =& get_instance();
		
		// Default to live environment
		$env = '';
		
		// If ENV is development, set $env to _dev to use sandbox vars.
		if (constant('ENVIRONMENT') == 'development') {
			$env = '_dev';
		}
		
		// Use dbconfig to set internal vars
		$this->api_username = $this->CI->dbconfig->{'paypal'.$env.'_username'};
		$this->api_password = $this->CI->dbconfig->{'paypal'.$env.'_password'};
		$this->api_signature = $this->CI->dbconfig->{'paypal'.$env.'_signature'};
		$this->api_endpoint = $this->CI->dbconfig->{'paypal'.$env.'_endpoint'};
		$this->redirect_url = $this->CI->dbconfig->{'paypal'.$env.'_auth_url'};
		$this->notify_url = $this->CI->dbconfig->{'paypal_ipn'};
	}
	
	/**
	 * Makes a SetExpressCheckout request against PayPal
	 * @help url https://www.x.com/developers/paypal/documentation-tools/api/setexpresscheckout-api-operation-nvp
	 *
	 * @var $data associative array for SetExpressCheckout:
	 *	return_url 		(REQUIRED)		=> Return URL after PayPal authorization, which should do DoExpressCheckout and GetExpressCheckout
	 *	cancel_url 		(REQUIRED)		=> Return URL it the customer clicks cancel on PayPal
	 *	currency		(default SEK) 	=> Currency code - SEK|USD|GBP|DKK|EUR|AUD|BRL|CAD|CZK|HKD|HUF|ILS|JPY|MYR|MXN|NZD|NOK|PHP|PLN|SGD|CHF|TWD|THB
	 *	type 			(default Sale)	=> 'Sale|Order|Authorization' - Normally we use Sale for instant payment
	 *	get_shipping 	(default 1) 	=> Do we want the shipping address? 0 = Prompt, 1 = Don't prompt, 2 = Require
	 *	shipping_amount (default 0)		=> Total shipping fee
	 *	tax_amount	 	(default 0)		=> Total tax fee
	 *	handling_amount	(default 0)		=> Total handling fee
	 *	allow_note		(default 0)		=> Allow note to seller
	 *
	 *	products		(ALL OPTIONAL)	=> Product array, example:
	 *
	 *	$data['products'] = array(
	 *  	array('name' => 'Lorem', 'price' => 1, 		'desc' => 'Consectetur adipisicing elit', 		'number' => '101', 		'quantity' => 1),
	 *		array('name' => 'Ipsum', 'price' => 0.65, 	'desc' => 'Sed do eiusmod tempor incididunt', 	'number' => '1234', 	'quantity' => 2),
	 *		array('name' => 'Dolar', 'price' => 2.95, 	'desc' => 'Ut enim ad minim veniam', 			'number' => 'SKU1234', 	'quantity' => 1)
	 *	);
	 *
	 * @var $custom_data bool Set to true to use custom SetExpressCheckout POST-data (overrides everything)
	 * @return array PayPal response
	 **/
	function set_ec($data = array(), $custom_data = false) {
		$nvpdata = array();
		
		if (!$custom_data) {
		
			// Return and cancel url are required
			if (!isset($data['return_url']) || !isset($data['cancel_url'])) {
				trigger_error('PayPal API Library: Return and Cancel URL are required!', E_USER_ERROR);
				return false;
			}
			
			// Set return & cancel URLs
			$nvpdata['RETURNURL'] = $data['return_url'];
			$nvpdata['CANCELURL'] = $data['cancel_url'];
			
			// Currency
			$nvpdata['PAYMENTREQUEST_0_CURRENCYCODE'] = 'SEK'; // SEK as default
			if (isset($data['currency'])) {
				$nvpdata['PAYMENTREQUEST_0_CURRENCYCODE'] = $data['currency'];
			}
			
			// Allow buyer to enter a note to seller, default to no.
			if (empty($data['allow_note']) && $data['allow_note'] == 0) {
				$nvpdata['ALLOWNOTE'] = 0;
			}
			
			// Description of purchase to buyer
			if (isset($data['desc'])) {
				$nvpdata['PAYMENTREQUEST_0_DESC'] = $data['desc'];
			}
			
			// Type of payment: Sale, Authorization or Order (See @help for more info)
			$nvpdata['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale'; // Default to Sale
			if (isset($data['type']) && in_array(strtolower($data['type']), array('sale', 'authorization', 'order'))) {
				$nvpdata['PAYMENTREQUEST_0_PAYMENTACTION'] = $data['type'];
			}
			
			// Shipping address, default = don't prompt
			// 0 = prompt but don't requrire
			// 1 = don't prompt for address
			// 2 = promt and require
			$nvpdata['NOSHIPPING'] = 1; // Default to don't prompt
			if (isset($data['get_shipping'])) {
				$nvpdata['NOSHIPPING'] = (int)$data['get_shipping'];
			}
			
			// Shipping fee
			$shipping_amount = 0;
			if (isset($data['shipping_amount'])) {
				$shipping_amount = (float)$data['shipping_amount'];
				$nvpdata['PAYMENTREQUEST_0_SHIPPINGAMT'] = sprintf('%.2f', $shipping_amount);
			}
			
			// Handling fee
			$handling_amount = 0;
			if (isset($data['handling_amount'])) {
				$handling_amount = (float)$data['handling_amount'];
				$nvpdata['PAYMENTREQUEST_0_HANDLINGAMT'] = sprintf('%.2f', $handling_amount);
			}
			
			// Tax fee
			$tax_amount = 0;
			if (isset($data['tax_amount'])) {
				$tax_amount = (float)$data['tax_amount'];
				$nvpdata['PAYMENTREQUEST_0_TAXAMT'] = sprintf('%.2f', $tax_amount);
			}
			
			// Set parameters for all products.
			$total_amount = 0;
			foreach($data['products'] as $k => $v) {
			
				// Product name
				if (isset($v['name'])) {
					$nvpdata['L_PAYMENTREQUEST_0_NAME'.$k] = $v['name'];
				}
				
				// Product desc
				if (isset($v['desc'])) {
					$nvpdata['L_PAYMENTREQUEST_0_DESC'.$k] = $v['desc'];
				}
				
				// Product ID / SKU
				if (isset($v['number'])) {
					$nvpdata['L_PAYMENTREQUEST_0_NUMBER'.$k] = $v['number'];
				}
				
				// Product quantity
				if (isset($v['quantity'])) {
					$nvpdata['L_PAYMENTREQUEST_0_QTY'.$k] = $v['quantity'];
				}
				
				// Product price 
				if (isset($v['price'])) {
				
					// Set price and update total_amount
					$nvpdata['L_PAYMENTREQUEST_0_AMT'.$k] = $v['price'];
					
					// Base total_amount on quantity and product price
					if (isset($v['quantity'])) {
						$total_amount+= (float)($v['price'] * (int)$v['quantity']);
					} else {
						$total_amount+= (float)$v['price'];
					}
				
				// Default to a free product.
				} else {
					$nvpdata['L_PAYMENTREQUEST_0_AMT'.$k] = '0.00';
					$total_amount+= 0;
				}
				
			}
			
			// Set total amounts
			$nvpdata['PAYMENTREQUEST_0_ITEMAMT'] = sprintf('%.2f', $total_amount);
			$nvpdata['PAYMENTREQUEST_0_AMT'] = sprintf('%.2f', $total_amount + $shipping_amount + $tax_amount + $handling_amount);
		
		// If we want to set custom data, let us!
		} else {
		
			// Just copy data into nvpdata.
			$nvpdata = $data;
			
		}
		
		// Do actual SetExpressCheckout request against PayPal
		$return = $this->_api_call("SetExpressCheckout", $nvpdata);
		
		// Check return status
		$ack = strtoupper($return["ACK"]);
		if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
			$return['status'] = true;
		} else {
			$return['status'] = false;
		}
		
		return $return;
	}
	
	/**
	 * Makes a GetExpressCheckoutDetails request against PayPal	
	 * Should be called after redirection from Paypal to return_url to get transaction details.
	 *
	 * @var string $token Token from $_GET['TOKEN'] passed by Paypal when redirected to return_url
	 * @return associative array With response from paypal.
	 **/
	public function get_ec_details($token) {
	
		// Set token
		$data = array('TOKEN' => $token);
		
		// Do GetExpressCheckoutDetails request against PayPal
		$return = $this->_api_call("GetExpressCheckoutDetails", $data);
		
		// Check return status
		$ack = strtoupper($return["ACK"]);
		if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
			$return['status'] = true;
		} else {
			$return['status'] = false;
		}
		
		return $return;
	}
	
	/**
	 * Makes a DoExpressCheckoutPayment request against PayPal	
	 * - which is called after redirected from Paypal back to return_URL
	 * - most of the data can be obtained from GetExpressCheckoutDetails
	 *
	 * @return associative array With response from paypal.
	 **/
	public function do_ec_payment($details, $type = 'Sale') {		
		$nvpdata = array();
		
		// We need these fields.
		$nvpfields = array('TOKEN', 'PAYERID', 'PAYMENTREQUEST_0_AMT', 'PAYMENTREQUEST_0_CURRENCYCODE');

		// Get all NVP-fields from GetECDetails-array
		foreach($nvpfields as $nvp) {
			$nvpdata[$nvp] = $details[$nvp];
		}
		
		// Set PayPal IPN from dbconfig.
		$nvpdata['PAYMENTREQUEST_0_NOTIFYURL'] = $this->notify_url;
		
		// Set payment-type from args as it's not in the details return-array.
		$nvpdata['PAYMENTREQUEST_0_PAYMENTACTION'] = $type;
				
		// Do the actual request against PayPal
		$return = $this->_api_call("DoExpressCheckoutPayment", $nvpdata);
		
		$ack = strtoupper($return["ACK"]);
		if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
			$return['status'] = true;
		} else {
			$return['status'] = false;
		}
		return $return;
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
		$redirect = sprintf($url, $token);
		
		// Redirect to PayPal
		redirect($redirect);
		
		exit;
	}
	
	/**
	 * Function to actually perform API calls to PayPal using API signature and $nvpStr
	 *
	 * @ToDo: Rewrite to use cURL-lib for CodeIgniter
	 *
	 * @var $method string Name of API method to call.
	 * @var $data array NVP-data to POST.
	 *
	 * @return associtive array Containing the response from the server.
	 **/
	private function _api_call($method, $data) {
	
		// Init curl
		$ch = curl_init();
		
		// Set endpoint URL
		curl_setopt($ch, CURLOPT_URL, $this->api_endpoint);
		
		// Default CURL opts
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Basic NVP vars to POST to API
		$nvp = array(
			'METHOD' => $method,
			'VERSION' => $this->api_version,
			'PWD' => $this->api_password,
			'USER' => $this->api_username,
			'SIGNATURE' => $this->api_signature,
		);
		
		// Merge with request data array
		$nvp = array_merge($nvp, $data);
		
		// Build post-data querystring
		$postfields = http_build_query($nvp);
		
		// Datafields to post /w curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		
		// Get response from server
		$response = curl_exec($ch);
		
		// Make response array
		parse_str($response, $responsearray);
		
		// Check if request succeded.
		if (curl_errno($ch)) {
			
			var_dump($ch);		
		
			// Display error?
			// error(curl_error($ch));
		}
		
		
		// Close cURL handler
		curl_close($ch);
		
		// Return result
		return $responsearray;
	}
	
}
	
