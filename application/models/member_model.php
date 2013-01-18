<?php

class Member_model extends CI_Model {
	
	public function login($data, $session = true) {
		
		// Check if e-mail exists.
		$member = $this->get_member('email', $data['email']);
		
		if(!$member) {
		
			// Log unsuccessful login to database
			$this->db->insert('logins', array('ip_address' => ip_address(), 'timestamp' => time()));
			
			// That's a negative
			return false;
		}
		
		// TEMPORARY Check ACL (Currently only allow admins to login)
		if(!$this->is_admin($member->id)) {
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
	
	public function forgot($email) {
	
		$member = $this->get_member('email', $email);
		
		// If member exists
		if($member) {
		
			// Check ACL (Currently only allow admins to use)
			if(!$this->is_admin($member->id)) {
				error('No account with that e-mail was found. Please try again.');
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
			
			// Add ACL to object
			$this->_add_acl($member);
			
			// Now, return member with ACL.
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
		
			// Walk the entire result set and add ACLs :)
			array_walk($query->result(), array($this, '_add_acl'));
			
			// Return result array.
			return $query->result();	
		}
		
		// No results.
		return array();
	}
	
	/**
	 * Method for adding a new member.
	 *
	 * ToDo: Validate all fields somewhere central!
	 **/
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
	 **/
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
	
	
	/***********************
	 * Member ACL functions
	 ***********************/
	
	/**
	 * Private method to add ACL to member.
	 * ToDo: REWRITE THIS (agaiiiiiin!)
	 */
	private function _add_acl(&$member, $index = 0) {
		
		// Try memcache first
		if($cached = $this->memcache->get('acl_'.$member->id)) {
		
			// Found, use it!
			$member->acl = $cached;
			
			return;
		}
	
		
		/*******************************************************************
		 * Clone database ACLs to member temporarily...
		 * @note According to PHP-manual, it's not allowed to change the
		 *		 structure of the referenced var, but it seems to work???
		 * @see http://php.net/manual/en/function.array-walk.php#refsect1-function.array-walk-parameters
		 *******************************************************************/
		$member->acl = clone $this->dbconfig->acl;
			
		
		// PRIORITIZED TODO:
		// - Improve look-up so we don't make more then one db query!
		
		// Set ACL value from db and/or remove description.
		foreach($member->acl as $acl => $desc) {
		
			// Default all to 0 (int equals non-existant acl).
			$member->acl->{$acl} = 0;
				
			// Get ACL from db.
			$query = $this->db->select('value')->get_where('acl', array('member_id' => $member->id, 'acl' => $acl), 1); 
			
			// If found, set to value
			if($query->num_rows()) {
				$member->acl->{$acl} = (bool)$query->row()->value;
			}
		}
		
		// Save to memcache, store forever.
		$this->memcache->set('acl_'.$member->id, $member->acl, 0);
		
	}
	
	/**
	 * Update a ACL for member.
	 **/
	public function acl_switch($member_id = '', $acl) {
	
		$member = $this->get_member($member_id);
		if(!$member) return false; // Failsafe
		
		// Check if acl is valid
		if(!empty($acl) && isset($member->acl->{$acl})) {
		
			// Remove memcached acl
			$this->memcache->delete('acl_'.$member_id);
		
			// Flip value
			$data = array('value' => (int)!$member->acl->{$acl});
			
			// Update if ACL key exists, otherwise insert new row.
			// This is a bit of a ugly, check if we got a bool or int. (bool = exists)
			if(is_bool($member->acl->{$acl})) {
				$this->db->update('acl', $data, array('member_id' => $member->id, 'acl' => $acl));
				
			// It's not bool, which means it doesn't exist yet.
			} else {
				// Insert $data combined with acl key and member.
				$this->db->insert('acl', array_merge($data, array('member_id' => $member->id, 'acl' => $acl)));
			}
			
			// Return query result for update or insert.
			return (bool)$this->db->affected_rows();
		}
		
		// False as default
		return false;
		
	}
	
	/**
	 * Get a specific ACL for member
	 * if $acl is empty, return full acl object
	 */
	public function get_acl($member_id = '', $acl = '') {
		
		// Get member
		$member = $this->get_member($member_id);
		
		// Failsafe
		if(!$member) {
			return false;
		}
			
		// Return all ACLs if $acl is empty
		if(empty($acl)) {
			return $member->acl;
		}
		
		// Return ACL if it's set
		return (isset($member->acl->{$acl}) && $member->acl->{$acl} == '1');
	}
	
	/**
	 * Short-cuts for get_acl(...)
	 */
	public function is_admin($member_id = '') {
		return $this->get_acl($member_id, 'admin');
	}
	
	public function is_active($member_id = '') {
		return $this->get_acl($member_id, 'active');
	}
	
	/**
	 * Get member projects
	 * ToDo: Move this to Project_model(?)
	 */
	public function get_projects($member_id, $limit = 100, $offset = 0) {
		$query = $this->db->get_where('projects', array('member_id' => $member_id));
		
		if($query->num_rows() > 0) {
			return $query->result();
		}
		
		return array();
	}
	
	/**
	 * Function to filter all field of a member array.
	 */
	private function _normalize($array) {
	
		// Allowed form fields
		$fields = array(
			'email', 'password', 'membership', 'twitter', 'skype', 
			'firstname', 'lastname', 'company', 'orgno', 'address',
			'address2', 'zipcode', 'city', 'country', 'mobile', 'phone', 'acl'
		);
		
		// Filter out only those fields we allow
		$data = array_intersect_key($array, array_flip($fields));
		
		// Remove false/null/0 values
		$data = array_filter($data);
		
		// Add those items removed in previous step
		$diff = array_diff_key(array_flip($fields), $data);
		foreach($diff as $key => $val) {
			$data[$key] = null;
		}
		
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
		
		// ACL Exception - remove ACL for now.
		// ToDo: Make it work!
		unset($data['acl']);
		
		return $data;
	
	}
	
}
