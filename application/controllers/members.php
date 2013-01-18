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
		
		// Check if member exists.
		if(!$member = $this->Member_model->get_member($member_id)) {
				error('The member doesn\'t exist!');
				redirect();
		}
		
		$head = array(
			'title' => 'View Member',
		);
		
		$data = array(
			'member' => $member,
			'projects' => $this->Member_model->get_projects($member_id),
		);
		
		$this->load->view('header', $head);
		$this->load->view('members/view', $data);
		$this->load->view('footer');
		
	
	}
	
	
	public function add() {
		admin_gatekeeper();
		
		// If POST is valid
		if ($this->form_validation->run()) {
		
			// Add member to db
			$data = $this->input->post();
			$result = $this->Member_model->add_member($data);
			
			if($result) {
				message('Successfully added new member.');
				redirect('members/view/'.$this->db->insert_id());
			} else {
				error('Couldn\'t add new member, please try again.');
			}
		}
		
		$head = array(
			'title' => 'Add new member',
		);
		
		$this->load->view('header', $head);
		$this->load->view('members/add');
		$this->load->view('footer');
		
	}
	
	public function edit($member_id = '') {
		gatekeeper();
		
		// No member selected
		if(empty($member_id)) redirect('members/edit/'.member_id());
		
		// Get and validate member
		$member = $this->Member_model->get_member($member_id);
		if(!$member) {
			error('That member doesn\'t exist!');
			redirect();
		}
		
		// Check access
		if(($member_id != member_id() && !$this->Member_model->is_admin())) {
			// Not you!
			error('Access denied! You tried to edit a member that\'s not you.');
			redirect();
		}
		
		// If POST is valid
		if ($this->form_validation->run('members/edit')) {
		
			// Get POST-data fields.
			$data = $this->input->post();
			
			// Update member in database.
			$result = $this->Member_model->update_member($member_id, $data);
			
			if($result) {
				message('Successfully updated member.');
				redirect('members/view/'.$member_id);
			} else {
				error('Couldn\'t update member, please try again.');
			}
		}
		
		$head = array(
			'title' => 'Edit member',
		);
		
		$this->load->view('header', $head);
		$this->load->view('members/edit', array('member' => $member));
		$this->load->view('footer');
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
	
	public function _validate_country($str) {
	
		// Validate country against dbconfig
		if(!empty($str)) {
		
			// Get countries
			$countries = (array)$this->dbconfig->countries;
			
			// Remove first item (null option)
			$countries = array_slice($countries, 1);
			
			// Validate country
			if(array_key_exists($str, $countries)) {
				return true;
			}
			
			$this->form_validation->set_message('_validate_country', 'The field %s doesn\'t contain a valid option.');
			return false;
			
		}
		
	}
	
}
