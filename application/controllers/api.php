<?php
/**
 * REST Api Controller for Internal
 * 
 * @author Jim Nelin
 * @version 0.2
 */
 
class Api extends CI_Controller {

	private $api_version = '0.3/2013-05-25';
	
	// Allowed Member POST fields
	private $allowed_member_fields = array(
		'email', 'password', 'twitter', 'skype', 'phone',
		'firstname', 'lastname', 'company', 'orgno', 'address',
		'address2', 'zipcode', 'city', 'country', 'civicregno',
	);
	
	// Required Member POST fields
	private $required_member_fields = array(
		'email', 'password', 'firstname', 'lastname',
		'address', 'zipcode', 'city', 'civicregno',
	);
	

	/**
	 * API Constructor
	 */
	public function  __construct() {
		parent::__construct();
		
		// Check Request Method
		if(!in_array($_SERVER['REQUEST_METHOD'], array('POST', 'GET'))) {
			$this->_status(405); // Method Not Allowed
		}
		
		// Check if method exists
		$method = $this->uri->segment(2);
		if(!empty($method) && !method_exists($this, $method)) {
			$this->_status(404); // Not Found
		}
	}
	
	/**
	 * Index: API Documentation
	 */
	public function index() {
		
		// Only allow GET Requests
		$this->_check_method('GET');
		
		// View documentation
		$this->load->view('api/documentation', array('version' => $this->api_version));
	
	}
	
	/**
	 * Auth resource.
	 */
	public function auth() {
		
		// Only allow POST Requests
		$this->_check_method('POST');
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
		
		
		// Check for required fields.
		if(!$email = $this->input->post('email')) {
			$this->_status(400); // Bad Request
		} elseif(!$password = $this->input->post('password')) {
			$this->_status(400); // Bad Request
		}
		
				
		// Try to login but don't set session.
		$login = array('email' => $email, 'password' => $password);
		$result = $this->Member_model->login($login, false);
		
		// Check result
		if($result) {
		
			// Get member
			$member = $this->Member_model->get_member('email', $email);
			
			// Unset password-related fields.
			unset($member->password, $member->reset_token, $member->reset_expire);
			
			// Remove NULL and empty fields (incl. false).
			#$member = (object)array_filter((array)$member);
		
			// Try to get the real user IP
			if(!$ip = $this->input->get_request_header('X-Real-IP')) {
				$ip = '0.0.0.0';
			}
			
			// Log successful login in database
			$this->db->insert('logins', array('member_id' => $member->id, 'ip_address' => $ip, 'timestamp' => time(), 'valid' => 1));
			
			// Return member object
			$this->_json_out($member);
			
		} else {
		
			// Return 404 if not found
			$this->_status(404); // Not Found
		
		}
		
	}
	
	/**
	 * Get Member resource.
	 */
	public function get_member($key = '', $value = '') {
		
		// Only allow GET Requests
		$this->_check_method('GET');
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Hack for GET /api/get_member/*id*
		if(empty($value)) {
			$value = $key;
			$key = 'id';
		}
		
		// Failsafe against empty values, invalid inputs and/or similar
		if(empty($value) || ($key == 'id' && (!is_numeric($value) || $value < 1000 || $value > 10000))) {
			$this->_status(400); // Bad Request
		}
		
		// Get member by key
		$member = $this->Member_model->get_member($key, urldecode($value));
		
		// Return 404 if not found
		if(!$member) {
			$this->_status(404); // Not found
		}
		
		// Unset password-fields
		unset($member->password, $member->reset_token, $member->reset_expire);
		
		// Remove NULL and empty fields (incl. false).
		#$member = (object)array_filter((array)$member);
		
		// And return as JSON
		$this->_json_out($member);
		
	}
	
	/**
	 * Get Member Groups resource.
	 */
	public function get_member_groups($member_id = 0) {
		
		// Only allow GET Requests
		$this->_check_method('GET');
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
		
		// Check member_id input
		if(empty($member_id) || !is_numeric($member_id) || $member_id < 1000 || $member_id > 10000) {
			$this->_status(400); // Bad request
		}
		
		// Get member by key
		$member = $this->Member_model->get_member($member_id);
		
		// Return 404 if not found or no groups
		if($member && !empty($member->groups)) {
		
			// Return groups as JSON
			$this->_json_out((object)$member->groups);
			
		} else {
		
			$this->_status(404); // Not found
			
		}
	}
	
	/**
	 * Add Member resource.
	 */
	public function add_member() {
	
		// Only allow POST Requests
		$this->_check_method('POST');
		
		// Check that we got any POST-data
		if(!$post = $this->input->post()) {
			$this->_status(400); // Bad Request
		}
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
		
		// Remove all POST fields that we don't want.
		$post = $this->_filter_member($post);
		
		// Check after required fields
		if(!array_keys_exist($this->required_member_fields, $post)) {
			// ... we're missing some fields!
			$this->_status(400); // Bad Request
		}
		
		// Check if e-mail already is registered (try to get_member)
		$result = $this->Member_model->get_member('email', $post['email']);
		if($result) {
			// User already exists, abort!
			$this->_status(409); // Conflict
		}
		
		// Validate and normalize all POST fields.
		$post = $this->_control_fields($post);
		
		// Something failed or was wrong...
		// ToDo: Return WHAT was wrong.
		if(!$post) {
			$this->_status(400); // Bad request
		}
		
		// Save to database
		$result = $this->Member_model->add_member($post);
		if(!$result) {
			$this->_status(400); // Bad request
		}
		
		// Get new member object
		$member_id = $this->db->insert_id();
		$member = $this->Member_model->get_member($member_id);
		
		// Unset password-fields
		unset($member->password, $member->reset_token, $member->reset_expire);
		
		// Remove NULL and empty fields (incl. false).
		#$member = (object)array_filter((array)$member);
		
		// Return it!
		$this->_json_out($member);
		
	}
	
	/**
	 * Update Member resource.
	 */
	public function update_member($member_id = 0) {
		
		// Only allow POST Requests
		$this->_check_method('POST');
		
		// Check that we got any POST-data
		if(!$post = $this->input->post()) {			
			$this->_status(400); // Bad Request
		}
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
		
		
		// Check member_id input
		if(empty($member_id) || !is_numeric($member_id) || $member_id < 1000 || $member_id > 10000) {
			$this->_status(400); // Bad request
		}
		
		// Check if member_id exists (try to get_member)
		$exists = $this->Member_model->get_member($member_id);
		if(!$exists) {
			$this->_status(404); // Not Found
		}
		
		// Remove all POST fields that we don't want.
		$post = $this->_filter_member($post, false); // DO NOT remove empty fields
		
		// Validate and normalize all POST fields.
		$post = $this->_control_fields($post);
		
		// Something failed or was wrong...
		// ToDo: Return WHAT was wrong.
		if(!$post || count($post) < 1) {
			$this->_status(400); // Bad request
		}
		
		// Save to database
		$result = $this->Member_model->update_member($member_id, $post);
		if(!$result) {
			$this->_status(400); // Bad request
		}
		
		// Get updated member object and unset password fields.
		$member = $this->Member_model->get_member($member_id);
		
		// Unset password-fields
		unset($member->password, $member->reset_token, $member->reset_expire);
		
		// Remove NULL and empty fields (incl. false).
		#$member = (object)array_filter((array)$member);
		
		// Return it!
		$this->_json_out($member);
		
	}
	
	
	/**
	 * Get Groups resource.
	 */
	public function get_groups() {
	
		// Only allow GET Requests
		$this->_check_method('GET');
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
		
		$groups = $this->Group_model->get_all();
		
		// Return groups as JSON
		$this->_json_out($groups);
		
	}
	
	/**
	 * Get Groups Members resource.
	 */
	public function get_group_members($group_id = 0) {
		
		// Only allow GET Requests
		$this->_check_method('GET');
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
		
		// Try to get group
		$group = $this->Group_model->get_group($group_id);
		
		// Check if group exists
		if(!$group) {
			$this->_status(404); // Not Found
		}
		
		$members = $this->Group_model->group_members($group_id);
		
		if($members) {
			// Strip password-fields from result.
			array_walk($members, create_function('&$m', 'unset($m->password, $m->reset_token, $m->reset_expire);')); // $m = (object)array_filter((array)$m);'));
			
			// ToDo: Walk the entire result and get groups :)
			
			// Return members as JSON
			$this->_json_out($members);
			
		} else {
		
			$this->_status(404); // Not Found
			
		}
		
	}
	
	/**
	 * HTCPC Protocol (RFC 2324)
	 */
	public function coffee() {
		$this->_status(418, 'I\'m a teapot');
	}
	
	
	/**
	 * Newsletter resource.
	 * ToDo: Should we even have this?
	 */
	public function newsletter() {
	
		// Only allow GET Requests
		$this->_check_method('GET');
		
		// Get newsletter by id
		$this->_status(404);
	
	}
	

	 
	/******************************************************************/
	/************************* PRIVATE METHODS ************************/
	/******************************************************************/
	
	/**
	 * Validate and Normalize all POST fields.
	 * ***ToDo** - Use Form_validation library instead!
	 *
	 * @return array Containing the final data or false on fail.
	 */
	 private function _control_fields($array) {
				
		// Loop through all fields.
		foreach($array as $key => $value) {
		
			if($key == 'email') {
				$array[$key] = strtolower($value);
				
				if(!filter_var($array[$key], FILTER_VALIDATE_EMAIL)) {
					return false;
				}
				
			} elseif($key == 'password') {
				if(strlen($value) < 8) {
					return false;
				}
				
			} elseif($key == 'firstname') {				
				if(strlen($value) < 2) {
					return false;
				}
				
				$array[$key] = ucfirst($value);
			} elseif($key == 'lastname') {
				if(strlen($value) < 2) {
					return false;
				}
				
			} elseif($key == 'company') {
				if(strlen($value) < 2) {
					return false;
				}
				
			} elseif($key == 'orgno') {
				$array[$key] = strim($value);
				
				if(strlen($array[$key]) != 11) {
					return false;
				}
				
			} elseif($key == 'address') {
				if(strlen($value) < 3) {
					return false;
				}
				
				$array[$key] = ucfirst($value);
			} elseif($key == 'address2') {
				if(strlen($value) < 3) {
					return false;
				}
				
				$array[$key] = ucfirst($value);
			} elseif($key == 'zipcode') {
				$array[$key] = strim($value);
				
				if(strlen($array[$key]) != 5) {
					return false;
				}
				
			} elseif($key == 'city') {
				if(strlen($value) < 2) {
					return false;
				}
				
				$array[$key] = ucfirst($value);
			} elseif($key == 'country') {
				if(strlen($value) != 2) {
					return false;
				}
				
				$array[$key] = strtoupper($value);
			} elseif($key == 'civicregno') {
			
				if(strlen($array[$key]) != 13) {
					return false;
				}
				
				if(!$this->_validate_civicregno($array[$key])) {
				#	return false;
				}
				
			} elseif($key == 'phone') {
				$array[$key] = normalize_phone($value);
				
				if(strlen($array[$key]) < 8) {
					return false;
				}
				
			}
			
		}
		
		return $array;
		
	 }
	
	/**
	 * Short-cut to check HTTP Method (POST/GET)
	 */
	private function _check_method($method) {
		if($_SERVER['REQUEST_METHOD'] != strtoupper($method)) {
			$this->_status(405); // Method Not Allowed
		}
	}
	
	/**
	 * Set HTTP Status and exit
	 */
	private function _status($code, $str = '') {
		$this->output->set_status_header($code, $str);
		exit;
	}
	
	/**
	 * JSON Output Method
	 */
	private function _json_out($data) {

		// Encode as JSON
		$json = json_encode($data);
		
		// Set Content-Type and output
		$this->output->set_content_type('application/json')->set_output($json);

	}

	/**
	 * Check authentication headers (X-Email, X-Password)
	 * @using login method in member model.
	 */
	private function _check_authentication($check_acl = true) {
	
		$email = $this->input->get_request_header('X-Email');
		$password = $this->input->get_request_header('X-Password');
		
		// Check input
		if(empty($email) || empty($password)) {
			$this->_status(403); // Forbidden
		}
		
		// Try to login but don't set session.
		$login = array('email' => $email, 'password' => $password);
		$result = $this->Member_model->login($login, false);
		
		// Check result
		if($result) {
			
			// Check API access (if needed)
			if($check_acl) {
				$member = $this->Member_model->get_member('email', $email);
				
				// Check if member have API access
				if(!empty($member->groups) && !empty($member->groups['api'])) {
					return true;
				}
				
			}
			
		}
	
		// Default to Forbidden
		$this->_status(403);
		
	}
	
	/**
	 * Normalize update/add_member requests.
	 */
	private function _filter_member($array, $remove_empty = true) {
		
		// Filter out only those fields we allow
		$data = array();
		foreach ($this->allowed_member_fields as $field) {
			if(array_key_exists($field, $array)) {
				if(empty($array[$field])) {
					$data[$field] = NULL;
				} else {
					$data[$field] = $array[$field];
				}
			}
		}
		
		// Trim all fields EXCEPT password
		if(!empty($data['password'])) {
			// Store temporarly
			$temp_pass = $data['password'];
		}
		
		array_walk($data, create_function('&$val', '$val = trim($val);'));
		
		// Restore original password field
		if(!empty($data['password'])) {
			$data['password'] = $temp_pass;
		}
		
		if($remove_empty) {
			// Remove false/null/0 values
			$data = array_filter($data);
		}
		
		return $data;
	
	}
	
	/**
	 * Validates a Swedish "personnummer" (social security number) or "samordningsnummer" (co-ordination number for non citizens)
	 */
	private function _validate_civicregno($data) {
			// Strip all non digits
			$data = preg_replace('/[^0-9]/s', '', $data);

			// Make sure there is 10 digits (strip 12 down to 10)
			if(!preg_match("/([0-9]{0,2})([0-9]{10})/", $data, $m))
			{
					return false;
			}
			$year = $m[1];
			$data = $m[2];

			// Validate the date
			list($y, $m, $d, $x1, $x2) = str_split($data, 2);
			if(!checkdate($m, $d, $y))
			{
					// If the date does not validate, try subtracting 60 from the day too see if it is an Swedish "samordningsnummer"
					$d -= 60;
					$data = "$y$m$d$x1$x2";
					if(!checkdate($m, $d, $y))
					{
							return false;
					}
			}

			// Validate the control digit
			$sum = 0;
			foreach(str_split($data) as $i => $number)
			{
					if($i % 2 == 1)
					{
							$sum += $number;
					}
					else
					{
							$sum += $number * 2;
							if(($number * 2)  > 9)
							{
									$sum -= 9;
							}
					}
			}

			// If the sum is dividable with 10 the control number is correct
			// Return the formatted number
			if(($sum % 10) == 0) {
					return $year.preg_replace("/([0-9]{6})([0-9]{4})/", "$1-$2", $data);
			} else {
					return false;
			}
	}

	
} 
