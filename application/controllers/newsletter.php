<?php

class Newsletter extends CI_Controller {
	
	public function index() {
	
		$head = array(
			'title' => 'Send Newsletter',
		);
		
		$this->load->view('header', $head);
		$this->load->view('newsletter/select_users');
		$this->load->view('footer');
	
	}

} 