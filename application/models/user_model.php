<?php

class User_model extends CI_Model {
	
	public function login($form, $session = true) {
		
		// Check if e-mail exists.
		$user = $this->get_user('email', $form['email']);
		
		if(!$user) {
			// That's a negative
			return false;
		}
		
		// Check ACL (login)
		if($user->active < 1) {
			// Inactive account (or similar)
			return false;
		}
		
		// Verify password
		$this->load->library('Pass');
		$result = $this->pass->verify($form['password'], $user->password);
		if(!$result) {
			return false;
		}
		
		// Check if wanna start a session or not
		if($session) {
		
			// Set session
			$data = array(
				'user_id' => $user->id,
				'email' => $form['email'],
				'logged_in' => true,
			);
			
			if(!empty($form['remember'])) {
				$data['remember_me'] = true;
			}
			
			$this->session->set_userdata($data);
			
			// Update last_login and add to logins.
			$this->db->insert('logins', array('user_id' => $user->id, 'ip_address' => ip_address(), 'timestamp' => time()));
			
			// Failsafe
			return is_loggedin();
		
		} else {
			
			// No session, but return true
			return true;
			
		}
		
		return false;
		
	}
	
	public function logout() {
	
		$this->session->sess_destroy();
	
	}
	
	public function register($form) {
	
		$this->load->library('Pass');
		
		$data = array(
		   'email' => $form['email'],
		   'password' => $this->pass->hash($form['password']),
		   'registered' => time(),
		);

		$result = $this->db->insert('users', $data); 
		
		return $result;
	
	}
	
	public function forgot($email) {
	
		$user = $this->get_user('email', $email);
		
		// If user exists
		if($user) {
			
			// Send mail
			$token = random_string('alnum', 34);
			
			$this->load->model('Email_model');
			$email = $this->Email_model->send_forgot_password($user->email, $token);
			
			
			// Check if sent
			if(!$email) {
				error('The password reset could not be sent out. If this error remains, please contact info@makerspace.se.');
			} else {
				$this->db->update('users', array('password_token' => $token), array('id' => $user->id)); 
				message('An e-mail with more information has been sent to the address you provided.');
			}
	
		} else {
			error('No account with that e-mail was found. Please try again.');
		}
	}
	
	public function valid_token($token) {
			
		if(strlen($token) != 34) {
			error('Invalid password reset token.');
			return false;
		}
		
		// Get user from token
		$user = $this->get_user('password_token', $token);
		
		// If we have a valid token.
		if($user) {
			return $user;
		} else {
			error('Invalid password reset token, please try again.');
		}
		
		return false;
	
	}
		
	public function change_password($user_id = '', $verify_old = true) {
	
			if(empty($user_id)) {
				$user_id = user_id();
			}
			
			$new_password = $this->input->post('new_password');
	
			// Get user by id
			$user = $this->get_user($user_id);
			
			// Verify current_password, if needed
			if($verify_old) {
			
				$this->load->library('Pass');
				$result = $this->pass->verify($this->input->post('current_password'), $user->password);
				
				if(!$result) {
					error('Your current password was wrong, please try again.');
					return false;
				}
				
			}
			// Update password to new one.
			$this->db->update('users', array('password' => $this->pass->hash($new_password)), array('id' => $user_id));
			message('Password sucessfully updated!');
			
			return true;
			
	}
	
	
	public function get_user($where = '', $value = '') {
	
		// Get current user
		if(empty($where)) {
			$where = 'id';
			$value = user_id();
			
		// Get where id = $where (ugly hack)
		} elseif(empty($value)) {
			$value = $where;
			$where = 'id';
		} 
		
		$this->db->join('acl', 'acl.user_id = users.id', 'left');
		$query = $this->db->get_where('users', array($where => $value), 1);
	
		if($query->num_rows() > 0) {
			return $query->row();	
		}
		
		return false;
		
	}
	
}
