<?php

class Todo extends CI_Controller {
	
	public function index() {
	
		$head = array(
			'title' => 'ToDo',
		);
		
		$this->load->view('header', $head);
		$this->load->view('todo/listing');
		$this->load->view('footer');
	
	}

} 