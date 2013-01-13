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
		
		// Load password library
		$this->load->library('Pass');
		
		// Verify password
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
	
		// Load password library
		$this->load->library('Pass');
		
		$data = array(
		   'email' => $form['email'],
		   'password' => $this->pass->hash($form['password']),
		   'registered' => time(),
		);
	
		// Add user to db
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
				$this->db->update('users', array('reset_token' => $token, 'reset_expire' => strtotime('+3 days')), array('id' => $user->id)); 
				message('An password reset link has been sent to your e-mail. Please note that the link expires in 3 days.');
			}
	
		} else {
			error('No account with that e-mail was found. Please try again.');
		}
	}
	
	public function valid_token($token) {
			
		if(strlen($token) != 34) {
			error('Invalid password reset token!');
			return false;
		}
		
		// Get user from token
		$user = $this->get_user('reset_token', $token);
		
		// If we have a valid token.
		if($user) {
		
			// Check if token has expired
			if($user->reset_expire < time()) {
			
				// Expired token, update db
				$this->db->update('users', array('reset_token' => null, 'reset_expire' => null), array('id' => $user->id));
				
				// ... and notify user.
				error('The password reset token has expired!');
				
				return false;
				
			// Valid, return user
			} else {
				return $user;
			}
			
		} else {
			error('Invalid password reset token!');
		}
		
		return false;
	
	}
		
	public function change_password($user_id = '', $new_password = '', $verify_old = true) {
	
			if(empty($user_id)) {
				$user_id = user_id();
			}
			
			// Use post
			if(empty($new_password)) {
				$new_password = $this->input->post('password');
			}
			
			// Get user by id
			$user = $this->get_user($user_id);
			
			// Check if invalid user
			if(!$user) {
				error('Invalid user, please try again.');
				redirect();
			}
			
			// Load password library
			$this->load->library('Pass');
			
			// Verify current password, if needed
			if($verify_old) {
				
				$current_password = $this->input->post('current_password');
				$result = $this->pass->verify($current_password, $user->password);
				
				if(!$result) {
					error('Your current password was wrong, please try again.');
					return false;
				}
				
			}
			
			// Update password and remove reset token
			$data = array(
				'password' => $this->pass->hash($new_password),
				'reset_token' => null, 'reset_expire' => null
			);
			
			$this->db->update('users', $data, array('id' => $user->id));
			
			message('Password sucessfully updated!');
			
			return true;
			
	}
	
	
	public function get_user($where = '', $value = '') {
	
		// ToDO: Memcache this.
		
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
	
	public function is_admin($user_id = '') {
	
		$user = $this->get_user($user_id);
		if($user->admin == 1) return true;
		
		return false;
	
	}
	
	public function get_all($limit = 1000, $offset = 0) {
		
		$this->db->join('acl', 'acl.user_id = users.id', 'left')->order_by('users.id', 'asc');
		$query = $this->db->limit($limit)->offset($offset)->get('users');
	
		if($query->num_rows() > 0) {
			return $query->result();	
		}
		
		return array();
	}
	
	public function acl_switch($user_id, $acl) {
	
		$user = $this->get_user($user_id);
		if(!$user) return false;
		
		if($user->{$acl}) {
			// Set to false
			$data = array($acl => 0);
		} else {
			// Set to true 
			$data = array($acl => 1);
		}
		
		
		$this->db->update('acl', $data, array('user_id' => $user_id));
	
		return true;
		
	}
	
}
