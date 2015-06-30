<?php

class Admin extends CI_Controller {
    
	public function __construct() {
		parent::__construct();
		admin_gatekeeper();
		
		$this->load->model('Admin_model');
	}
	
	public function index() {
		redirect('admin/config');
	}
	
	public function config() {
		
		// Update value if posted.
		if ($this->form_validation->run()) {
			
			$key = $this->input->post('key');
			$value = $this->input->post('value');
			
			// Update database
			$this->Admin_model->set_config($key, $value);
						
			redirect('admin/config');
		}
		
		$head = array(
			'title' => 'Internal Administration',
		);
		
		$data = array(
			'dbconfig' => $this->Admin_model->get_dbconfig(),
		);
		
		$this->load->view('header', $head);
		$this->load->view('admin/view_config', $data);
		$this->load->view('footer');
	
	}
	
	public function add_config() {
		
		// If POST is valid
		if ($this->form_validation->run()) {
			
			// Add to database.
			$this->Admin_model->add_config($this->input->post());
			
			redirect('admin/config');
		}
		
		$head = array(
			'title' => 'Add Config',
		);
		
		$this->load->view('header', $head);
		$this->load->view('admin/add_config');
		$this->load->view('footer');
	
	}

} 
