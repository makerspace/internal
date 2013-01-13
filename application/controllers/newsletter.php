<?php

class Newsletter extends CI_Controller {
	
	public function index() {
	
		$head = array(
			'title' => 'Send Member Newsletter',
		);
		
		$this->load->view('header', $head);
		$this->load->view('newsletter/select_members');
		$this->load->view('footer');
	
	}

} 