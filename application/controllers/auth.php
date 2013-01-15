<?php

class Auth extends CI_Controller {

	public function login() {
		no_gatekeeper();

		// If POST is valid
		if ($this->form_validation->run()) {

			// Try to login
			$login = $this->input->post();
			$result = $this->Member_model->login($login);

			if(!$result) {
				// Invalid credentials.
				error('Incorrect e-mail address and/or password. Please try again.');
				redirect('auth/login');
			}
			
			// Redirect on success
			redirect();
		}
		
		$header = array(
			'title' => 'Login',
		);
		
		$this->load->view('header', $header);
		$this->load->view('auth/login');
		$this->load->view('footer');
		
	}
	
	public function logout() {
		gatekeeper();
		
		// Logout and redirect to frontpage
		$this->Member_model->logout();
		
		message('Successfully signed out.');
		redirect('auth/login');
	}
	
	public function forgot() {
		no_gatekeeper();
		
		// If POST is valid
		if ($this->form_validation->run()) {

			// Send reset e-mail 
			$email = $this->input->post('email');
			$this->Member_model->forgot($email);
			
			redirect();
		}
		
		$header = array(
			'title' => 'Forgot password',
		);
		
		$this->load->view('header', $header);
		$this->load->view('auth/forgot_password');
		$this->load->view('footer');
	
	}
	
	public function reset($token = '') {
		no_gatekeeper();
		
		// Validate token and get member
		$member = $this->Member_model->valid_token($token);
		
		// If invalid token
		if(!$member) {
			redirect('/auth/forgot');
		}
		
		// If submitted and POST is valid
		if ($this->form_validation->run('auth/reset')) {
		
			// Actually update password (uses password field)
			$this->Member_model->change_password($member->id, null, false);
			
			redirect();	
		}
		
		$header = array(
			'title' => 'Reset password',
		);
		
		// View reset password form
		$this->load->view('header', $header);
		$this->load->view('auth/reset_password', array('token' => $token));
		$this->load->view('footer');
		
	}

}
