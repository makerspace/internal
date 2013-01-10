<?php

class Admin extends CI_Controller {
	
	public function index() {
	
		$head = array(
			'title' => 'Internal Administration',
		);
		
		$this->load->view('header', $head);
		$this->load->view('admin/config');
		$this->load->view('footer');
	
	}

} 