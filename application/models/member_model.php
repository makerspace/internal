<?php

class Member_model extends CI_Model {
	
	public function login($form, $session = true) {
		
		// Check if e-mail exists.
		$member = $this->get_member('email', $form['email']);
		
		if(!$member) {
			// That's a negative
			return false;
		}
		
		// Check ACL (login)
		if($member->active < 1) {
			// Inactive account (or similar)
			return false;
		}
		
		// Load password library
		$this->load->library('Pass');
		
		// Verify password
		$result = $this->pass->verify($form['password'], $member->password);
		if(!$result) {
			return false;
		}
		
		// Check if wanna start a session or not
		if($session) {
		
			// Set session
			$data = array(
				'member_id' => $member->id,
				'email' => $form['email'],
				'logged_in' => true,
			);
			
			if(!empty($form['remember'])) {
				$data['remember_me'] = true;
			}
			
			$this->session->set_userdata($data);
			
			// Update last_login and add to logins.
			$this->db->insert('logins', array('member_id' => $member->id, 'ip_address' => ip_address(), 'timestamp' => time()));
			
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
	
		// Add member to db
		$result = $this->db->insert('members', $data); 
		
		return $result;
	
	}
	
	public function forgot($email) {
	
		$member = $this->get_member('email', $email);
		
		// If member exists
		if($member) {
			
			// Send mail
			$token = random_string('alnum', 34);
			
			$this->load->model('Email_model');
			$email = $this->Email_model->send_forgot_password($member->email, $token);
			
			
			// Check if sent
			if(!$email) {
				error('The password reset could not be sent out. If this error remains, please contact info@makerspace.se.');
			} else {
				$this->db->update('members', array('reset_token' => $token, 'reset_expire' => strtotime('+3 days')), array('id' => $member->id)); 
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
		
		// Get member from token
		$member = $this->get_member('reset_token', $token);
		
		// If we have a valid token.
		if($member) {
		
			// Check if token has expired
			if($member->reset_expire < time()) {
			
				// Expired token, update db
				$this->db->update('members', array('reset_token' => null, 'reset_expire' => null), array('id' => $member->id));
				
				// ... and notify member.
				error('The password reset token has expired!');
				
				return false;
				
			// Valid, return member
			} else {
				return $member;
			}
			
		} else {
			error('Invalid password reset token!');
		}
		
		return false;
	
	}
		
	public function change_password($member_id = '', $new_password = '', $verify_old = true) {
	
			if(empty($member_id)) {
				$member_id = $this->current_id();
			}
			
			// Use post
			if(empty($new_password)) {
				$new_password = $this->input->post('password');
			}
			
			// Get member by id
			$member = $this->get_member($member_id);
			
			// Check if invalid member id
			if(!$member) {
				error('Invalid member id, please try again.');
				redirect();
			}
			
			// Load password library
			$this->load->library('Pass');
			
			// Verify current password, if needed
			if($verify_old) {
				
				$current_password = $this->input->post('current_password');
				$result = $this->pass->verify($current_password, $member->password);
				
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
			
			$this->db->update('members', $data, array('id' => $member->id));
			
			message('Password sucessfully updated!');
			
			return true;
			
	}
	
	
	public function get_member($where = '', $value = '') {
	
		// ToDo: Memcache this.
		// ToDo2: Switch structure of ACL
		
		// Get current member
		if(empty($where)) {
			$where = 'id';
			$value = member_id();
			
		// Get where id = $where (ugly hack)
		} elseif(empty($value)) {
			$value = $where;
			$where = 'id';
		} 
		
		$this->db->join('acl', 'acl.member_id = members.id', 'left');
		$query = $this->db->get_where('members', array($where => $value), 1);
	
		if($query->num_rows() > 0) {
			return $query->row();	
		}
		
		return false;
		
	}
	
	public function is_admin($member_id = '') {
	
		$member = $this->get_member($member_id);
		if($member->admin == 1) return true;
		
		return false;
	
	}
	
	public function get_all($limit = 1000, $offset = 0) {
		
		$this->db->join('acl', 'acl.member_id = members.id', 'left')->order_by('members.id', 'asc');
		$query = $this->db->limit($limit)->offset($offset)->get('members');
	
		if($query->num_rows() > 0) {
			return $query->result();	
		}
		
		return array();
	}
	
	public function acl_switch($member_id, $acl) {
	
		$member = $this->get_member($member_id);
		if(!$member) return false;
		
		if($member->{$acl}) {
			// Set to false
			$data = array($acl => 0);
		} else {
			// Set to true 
			$data = array($acl => 1);
		}
		
		
		$this->db->update('acl', $data, array('member_id' => $member_id));
	
		return true;
		
	}
	
}
