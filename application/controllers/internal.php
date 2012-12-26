<?php

class Internal extends CI_Controller {

	public function index() {
		
		$head = array(
			'title' => 'Internal - Stockholm Makerspace',
		);
		
		$this->load->view('header', $head);

		if(!$this->ion_auth->logged_in()) {
			$this->load->view('auth/login');
		} else {
			$this->load->view('internal/index');
		}

		$this->load->view('footer');

	}
}
