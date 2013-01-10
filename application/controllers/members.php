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
		$this->load->view('members/view', array('member' => $this->User_model->get_user($user_id)));
		$this->load->view('footer');
		
	
	}
	
	
	public function add() {
		admin_gatekeeper();
		
		if ($this->form_validation->run() == false) {
		
			$head = array(
				'title' => 'Add new member',
			);
			
			$this->load->view('header', $head);
			$this->load->view('members/add');
			$this->load->view('footer');
			
		} else {
		
			// Add member to db
		
		}
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
		
		if ($this->form_validation->run() == false) {
		
			$head = array(
				'title' => 'Edit member',
			);
			
			$this->load->view('header', $head);
			$this->load->view('members/edit', array('user' => $this->User_model->get_user($user_id)));
			$this->load->view('footer');
	
		} else {
		
			// Update member in db
		
		}
	}
	
	public function acl_switch($user_id = '', $acl = '') {
		admin_gatekeeper();
	
		if(empty($user_id)) {
			error('Invalid user id');
			redirect();
		} elseif(empty($acl)) {
			error('Invalid access level');
			redirect();
		}
		
		$return = $this->User_model->acl_switch($user_id, $acl);
		
		if(!$return) {
			error('Couldn\'t update the user ACL!');
			redirect();
		}
		
		message('User ACL successfully updated.');
		redirect('/members/view/'.$user_id);
	
	}
	
}
