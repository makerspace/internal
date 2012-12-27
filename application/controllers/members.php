<?php

class Members extends CI_Controller {

	public function index() {
		admin_gatekeeper();
		
		$head = array(
			'title' => 'Members',
		);
		
		// ToDo: Pagination
		$users = $this->User_model->get_all();

		$this->load->view('header', $head);
		$this->load->view('members/index', array('members' => $users));
		$this->load->view('footer');

	}
	
	public function view($user_id = '') {
	
		
	
	}
	
	
	public function add() {
		admin_gatekeeper();
		
		$head = array(
			'title' => 'Add new member',
		);
		
		$this->load->view('header', $head);
		$this->load->view('members/add');
		$this->load->view('footer');
	
	}
	
}
