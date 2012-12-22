<?php

class Internal extends CI_Controller {

	public function index() {
		
		$head = array(
			'title' => 'Internal - Stockholm Makerspace',
		);
		
		$this->load->view('header', $head);

		if(!is_loggedin()) {
			$this->load->view('login');
		} else {
			$this->load->view('index');
		}

		$this->load->view('footer');

	}
}
