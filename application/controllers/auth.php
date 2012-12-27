<?php

class Auth extends CI_Controller {

	public function login() {
		no_gatekeeper();

		if (!$this->form_validation->run()) {

			$header = array(
				'title' => 'Login',
			);
			
			$this->load->view('header', $header);
			$this->load->view('auth/login');
			$this->load->view('footer');
			
		} else {
		
			$login = $this->input->post();
			$result = $this->User_model->login($login);

			if(!$result) {
				// Invalid credentials.
				error('Incorrect e-mail address and/or password. Please try again.');
				redirect('auth/login');
			}
			
			// Redirect on success
			redirect();
			
		}
		
	}
	
	public function logout() {
		gatekeeper();
		
		// Logout and redirect to frontpage
		$this->User_model->logout();
		redirect();
	}
	
	public function forgot() {
		no_gatekeeper();
	
	}
	
	public function reset() {
		no_gatekeeper();
	
	}

}
