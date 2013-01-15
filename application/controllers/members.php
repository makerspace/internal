<?php

class Members extends CI_Controller {

	public function index() {
		admin_gatekeeper();
		
		$head = array(
			'title' => 'Members',
		);
		
		// ToDo: Pagination? Tablesorter?
		$members = $this->Member_model->get_all();

		$this->load->view('header', $head);
		$this->load->view('members/list', array('members' => $members));
		$this->load->view('footer');

	}
	
	public function view($member_id = '') {
		gatekeeper();
		
		$head = array(
			'title' => 'View Member',
		);
		
		$this->load->view('header', $head);
		$this->load->view('members/view', array('member' => $this->Member_model->get_member($member_id)));
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
			$data = $this->db->post();
			$this->Member_model->add_member($data);
			
		}
	}
	
	public function edit($member_id = '') {
		gatekeeper();
		
		// Failsafes
		if(empty($member_id)) redirect('members/edit/'.member_id());
		if(($member_id != member_id()) && !$this->Member_model->is_admin()) {
			// Not you!
			error('Access denied.');
			redirect();
		}
		
		if ($this->form_validation->run() == false) {
		
			$head = array(
				'title' => 'Edit member',
			);
			
			$this->load->view('header', $head);
			$this->load->view('members/edit', array('member' => $this->Member_model->get_member($member_id)));
			$this->load->view('footer');
	
		} else {
		
			// Update member in db
			$data = $this->db->post();
			$this->Member_model->update_member($data);
			
		}
	}
	
	public function acl_switch($member_id = '', $acl = '') {
		admin_gatekeeper();
	
		if(empty($member_id)) {
			error('Invalid member id');
			redirect();
		} elseif(empty($acl)) {
			error('Invalid access level');
			redirect();
		}
		
		$return = $this->Member_model->acl_switch($member_id, $acl);
		
		if(!$return) {
			error('Couldn\'t update the member ACL!');
			redirect();
		}
		
		message('Member ACL successfully updated.');
		redirect('/members/view/'.$member_id);
	
	}
	
}
