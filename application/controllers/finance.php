<?php

class Finance extends CI_Controller {

	public function __construct() {
		parent::__construct();
		
		gatekeeper();
	}

	public function index() {
		
		$head = array(
			'title' => 'Finance',
		);
		
		$this->load->view('header', $head);
		$this->load->view('finance/overview');
		$this->load->view('footer');

	}
}
