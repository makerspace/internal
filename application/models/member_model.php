<?php

class Member_model extends CI_Model {
	
	public function __construct() {
		parent::__construct();
		
		$this->load->model('Group_model');
	}
	
	public function login($data, $session = true) {
		
		// Check if e-mail exists.
		$member = $this->get_member('email', $data['email']);
		
		if(!$member) {
		
			// Log unsuccessful login to database
			$this->db->insert('logins', array('ip_address' => ip_address(), 'timestamp' => time()));
			
			// That's a negative
			return false;
		}
		
		// Check is member is a admin or boardmember
		if(!$this->is_boardmember($member->id) && !$this->is_admin($member->id)) {
			return false;
		}
		
		// Load password library
		$this->load->library('Pass');
		
		// Verify password
		$result = $this->pass->verify($data['password'], $member->password);
		if(!$result) {
			
			// Log unsuccessful login to database
			$this->db->insert('logins', array('member_id' => $member->id, 'ip_address' => ip_address(), 'timestamp' => time()));
			
			return false;
		}
		
		// Check if wanna start a session or not
		if($session) {
		
			// Set session
			$userdata = array(
				'member_id' => $member->id,
				'email' => $data['email'],
				'logged_in' => true,
			);
			
			if(!empty($data['remember'])) {
				$userdata['remember_me'] = true;
			}
			
			$this->session->set_userdata($userdata);
			
			// Log successful login in database
			$this->db->insert('logins', array('member_id' => $member->id, 'ip_address' => ip_address(), 'timestamp' => time(), 'valid' => 1));
			
			// Failsafe
			return is_loggedin();
		
		} else {
			
			// No session, but return true
			return true;
			
		}
		
		return false;
		
	}
	
	public function logout() {
		
		// Kill session
		$this->session->sess_destroy();
	
		// ... and then recreate it.
		$this->session->__construct();
		
	}
	
	public function forgot_password($email) {
	
		$member = $this->get_member('email', $email);
		
		// If member exists
		if($member) {
		
			// Check is member is a admin or boardmember
			if(!$this->is_boardmember($member->id) && !$this->is_admin($member->id)) {
				error('Access denied.');
				return false;
			}
			
			// Send mail
			$token = random_string('alnum', 34);
			
			$this->load->model('Email_model');
			$email = $this->Email_model->send_forgot_password($member->email, $token, $member->fullname);
			
			
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
		
			// Check is member is a admin or boardmember
			if(!$this->is_boardmember($member->id) && !$this->is_admin($member->id)) {
				return false;
			}
			
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
			
			// Check is member is a admin or boardmember
			if(!$this->is_boardmember($member->id) && !$this->is_admin($member->id)) {
				error('Access denied.');
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
	
	########################## Get members ##########################
	
	public function get_member($where = '', $value = '') {
		
		// ToDo: Memcache!
		
		// Get current member as default
		if(empty($where)) {
			$where = 'id';
			$value = member_id();
			
		// Get where id = $where (a bit ugly hack)
		} elseif(empty($value)) {
			$value = $where;
			$where = 'id';
		} 
		
		$query = $this->db->get_where('members', array($where => $value), 1);
	
		// If user is found.
		if($query->num_rows() > 0) {
			
			// Get member
			$member = $query->row();
			
			// Combine first and lastname and set fullname
			$member->fullname = trim($member->firstname.' '.$member->lastname);
			
			// Get all members groups
			$member->groups = $this->Group_model->member_groups($member->id);
			
			// Now, return member with groups.
			return $member;	
		}
		
		// Non-existent user, return false
		return false;
		
	}
		
	public function get_all($limit = 1000, $offset = 0) {
		
		// Get members
		$this->db->order_by('members.id', 'asc');
		$query = $this->db->limit($limit)->offset($offset)->get('members');
	
		// Check if we got anything.
		if($query->num_rows() > 0) {
		
			// Walk the entire result and get groups :)
			array_walk($query->result(), array($this, '_get_groups'));
			
			// Return result array.
			return $query->result();	
		}
		
		// No results.
		return array();
	}
	
	########################## Add and edit members ##########################
	
	
	/**
	 * Method for adding a new member.
	 *
	 * ToDo: Validate all fields somewhere central!
	 */
	public function add_member($data) {
	
		// Normalize the member-array.
		$data = $this->_normalize($data);
		
		// Add registered timestamp
		$data['registered'] = time();
		
		// Add to database
		$this->db->insert('members', $data);
		
		// Return result
		return (bool)$this->db->affected_rows();
		
	}
	
	/**
	 * Method for updating an existing member.
	 *
	 * ToDo: Validate all fields somewhere central!
	 */
	public function update_member($member_id, $data) {
		
		// Normalize the member-array.
		$data = $this->_normalize($data);
		
		// Add last_updated field
		$data['last_updated'] = time();
		
		// Update member based upon id
		$this->db->update('members', $data, array('id' => $member_id));
		
		// Return result
		return (bool)$this->db->affected_rows();
		
	}
	
	
	########################## Member Group functions ##########################
	
	/**
	 * Quick hack to get groups for a bunch of members.
	 */
	public function _get_groups(&$member, $index = 0) {
	
		$member->groups = $this->Group_model->member_groups($member->id);
		
	}
	
	/**
	 * Short-cuts for a few of the Group_model methods
	 */
	public function is_active($member_id = '') {
		// Failsafe
		if(empty($member_id)) $member_id = member_id();
		
		// Ugly hack for checking if the user is an active member THIS year.
		return $this->Group_model->member_of_group($member_id, 'member'.date('Y'));
	}
	
	public function is_boardmember($member_id = '') {
		// Failsafe
		if(empty($member_id)) $member_id = member_id();
		
		return $this->Group_model->member_of_group($member_id, 'boardmember'.date('Y'));
	}
	
	public function is_admin($member_id = '') {
		// Failsafe
		if(empty($member_id)) $member_id = member_id();
		
		return $this->Group_model->member_of_group($member_id, 'admins');
	}
	
	
	########################## Other methods ##########################
	 
	/**
	 * Function to filter all field of a member array.
	 */
	public function _normalize($array) {
	
		// Remove false/null/0 values
		$array = array_filter($array);
		
		// Allowed form fields
		$fields = array(
			'email', 'password', 'twitter', 'skype', 'mobile', 'phone',
			'firstname', 'lastname', 'company', 'orgno', 'address',
			'address2', 'zipcode', 'city', 'country', 'civicregno',
		);
		
		// Filter out only those fields we allow
		$data = elements($fields, $array, NULL);
		
		// Make an exception for the password field
		if(!empty($data['password'])) {
			
			// Load password library
			$this->load->library('Pass');
			
			// Hash the password 
			$data['password'] = $this->pass->hash($data['password']);
			
		} else {
			// Remove if unset
			unset($data['password']);
		}
		
		// Exception - remove groups for now.
		// ToDo: Make it work?
		unset($data['groups']);
		
		return $data;
	
	}
	
}
