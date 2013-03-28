<?php

class Payments extends CI_Controller {

	public function __construct() {
		parent::__construct();
		
		gatekeeper();
	}

	public function index() {
		
		$head = array(
			'title' => 'Payments and Finance',
		);
		
		$this->load->view('header', $head);
		//$this->load->view('payments/placeholder');
		$this->load->view('footer');

	}
}
