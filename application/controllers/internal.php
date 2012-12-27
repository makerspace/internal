<?php

class Internal extends CI_Controller {

	public function index() {
	
		// Force login
		if(!is_loggedin()) redirect('auth/login');
		
		$head = array(
			'title' => 'Internal - Stockholm Makerspace',
		);
		
		$this->load->view('header', $head);
		$this->load->view('internal/index');
		$this->load->view('footer');

	}
}
