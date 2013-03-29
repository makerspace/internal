<?php

class Groups extends CI_Controller {

	public function __construct() {
		parent::__construct();
		
		gatekeeper();
		
		$this->load->model('Group_model');
	}

	public function index() {
		
		$head = array(
			'title' => 'Group Management',
		);
		
		$this->load->view('header', $head);
		$this->load->view('groups/manage', array('groups' => $this->Group_model->get_all()));
		$this->load->view('footer');

	}
}
