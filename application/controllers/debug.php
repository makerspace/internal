<?php

class Debug extends CI_Controller {
    
	public function __construct() {
		parent::__construct();
		
		admin_gatekeeper();
	}
	
    public function index() {

		/*
		// Testing of PayPal lib
		$this->load->library('PayPal');
		
		// POST data for this "order" (to send to paypal), use defaults for most...
		$checkout = array(
			'return_url' => 'https://internal.makerspace.se/debug/paypal_return',
			'cancel_url' => 'https://internal.makerspace.se/debug/get_vars',
			
			// array with products (each product has it's own array)
			'products' => array(
				array(
					'name' => 'Medlemskapsavgift',
					'desc' => 'För hela 2013',
					'price' => 150, // SEK
					'quantity' => 1,
				),
			),
		);
		
		// Return (should) contains token to use in next step.
		$return = $this->paypal->set_ec($checkout);
		
		// Redirect on success
		if($return['status'] == true) {
			$this->paypal->redirect_to_paypal($return['TOKEN']);
		} else {
			$this->_error($return);
		}
		*/
		
		$this->load->view('header');
		$this->load->view('debug');
		$this->load->view('footer');

    }
	
	/*
	public function paypal_return() {
	
		// Load PayPal lib
		$this->load->library('PayPal');
	
		$token = $this->input->get('token');
		$payerid = $this->input->get('PayerID');
		
		// Get payment details from paypal 
		$details = $this->paypal->get_ec_details($token);
	
		// Do the actual transaction
		$return = $this->paypal->do_ec_payment($details);
		
		echo '<pre>';
		var_dump($details);
		var_dump($return);
	
	}
	
	public function ipn() {
		// ToDo: Use this function to add payment to db etc.
		
		$post = $this->input->post();
		
		$str = var_export($post, true);
		
		file_put_contents('/tmp/ipn.log', $str."\n\n\n\n", FILE_APPEND);
		
	}*/
	
	public function get_vars() {
	
		#phpinfo();
		echo '<pre>';
		
		// var_dump entire POST-array
		echo '<b style="text-decoration:underline;"> POST-array (xss filtered): </b>'."\n";
		var_dump($this->input->post());
		
		// var_dump entire GET-array
		echo "\n\n\n".'<b style="text-decoration:underline;"> GET-array (xss filtered): </b>'."\n";
		var_dump($this->input->get());
		
		// var_dump entire $_GET-array
		echo "\n\n\n".'<b style="text-decoration:underline;"> $_GET-array (raw, no filtering): </b>'."\n";
		var_dump($_GET);
		
	
	}
}
