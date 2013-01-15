<?php

class Admin extends CI_Controller {
    
	public function __construct() {
		parent::__construct();
		
		admin_gatekeeper();
	}
	
	public function index() {
		redirect('admin/config');
	}
	
	public function config() {
		
		// Update value if posted.
		if ($this->input->post()) {
			
			// Update database
			$this->db->update('config', array('value' => $this->input->post('value')), array('key' => $this->input->post('key')));
			
			if($this->db->affected_rows()) {
				message('Config value successfully saved.');
			}
			
			redirect('admin/config');
		}
		
		$head = array(
			'title' => 'Internal Administration',
		);
		
		$this->load->view('header', $head);
		$this->load->view('admin/config');
		$this->load->view('footer');
	
	}
	
	public function add_config() {
		
		// If POST is valid
		if ($this->form_validation->run()) {
			
			// Insert into database
			$data = array(
				'value' => $this->input->post('value'),
				'key' => $this->input->post('key'),
				'desc' => $this->input->post('desc'),
			);
			$this->db->insert('config', $data);
			
			if($this->db->affected_rows()) {
				message('Config value successfully created.');
			} else {
				error('Couldn\'t save config, is the key unique?');
			}
			
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