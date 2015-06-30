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
		
		// ToDo: Move to model!
		$data = array(
			'transactions' => $this->db->get('transactions')->result(),
		);
		
		$this->load->view('header', $head);
		$this->load->view('finance/overview', $data);
		$this->load->view('footer');

		
	}
	public function unpaid() {
		
		$head = array(
			'title' => 'Finance',
		);
		
		// ToDo: Move to model!
		$data = array(
			'transactions' => $this->db->get_where('transactions', array('status' => 'unpaid'))->result(),
		);
		
		$this->load->view('header', $head);
		$this->load->view('finance/overview', $data);
		$this->load->view('footer');

	}
}
