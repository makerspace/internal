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
	
	public function add_group() {
	
		// Form validation
		if ($this->form_validation->run('groups/add_group')) {
		
			$name = $this->input->post('name');
			$description = $this->input->post('description');
			
			$result = $this->Group_model->add_group($name, $description);
			
			if($result) {
				message('Group successfully added!');
			} else {
				error('Couldn\'t add group, please try again');
			}
		
		} else {
			error('Couldn\'t add group, please try again');
		}
		
		redirect('groups');
	
	}
}
