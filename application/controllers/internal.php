<?php

class Internal extends CI_Controller {

	public function index() {
	
		// Force login
		if(!is_loggedin()) redirect('auth/login');
		
		// Redirect to member overview for now...
		redirect('members');
		
		$head = array(
			'title' => 'Internal',
		);
		
		$this->load->view('header', $head);
		$this->load->view('internal/index');
		$this->load->view('footer');

	}
}
