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
		gatekeeper();
		
		$head = array(
			'title' => 'View Member',
		);
		
		$this->load->view('header', $head);
		#$this->load->view('members/view', array('user' => $this->User_model->get_user($user_id)));
		$this->load->view('footer');
		
	
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
	
	public function edit($user_id = '') {
		gatekeeper();
		
		// Failsafes
		if(empty($user_id)) redirect('members/edit/'.user_id());
		if(($user_id != user_id()) && !$this->User_model->is_admin()) {
			// Not you!
			error('Access denied.');
			redirect();
		}
		
		$head = array(
			'title' => 'Edit member',
		);
		
		$this->load->view('header', $head);
		$this->load->view('members/edit', array('user' => $this->User_model->get_user($user_id)));
		$this->load->view('footer');
	
	}
	
}
