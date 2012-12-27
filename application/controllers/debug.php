<?php

class Debug extends CI_Controller {
    
    public function index() {
		gatekeeper();
		
		$head = array(
			'title' => 'Debug - Stockholm Makerspace',
		);
		
		$this->load->view('header', $head);
		
		#$this->load->library('Pass');
		#$this->db->update('users', array('password' => $this->pass->hash('lol123lol123')), array('id' => 1000));
		
		$this->load->view('debug');
		$this->load->view('footer');

    }
}