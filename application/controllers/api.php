<?php
/**
 * REST Api Controller for Internal
 * 
 * @author Jim Nelin
 * @version 0.2
 */
 
class Api extends CI_Controller {

	private $api_version = '0.2';

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
		
		// Only allow GET
		if($this->input->post()) {
			$this->_status(405); // Method Not Allowed
		}
		
		$this->load->view('api/documentation', array('version' => $this->api_version));
	
	}
	
	/**
	 * Auth resource.
	 */
	public function auth() {
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Only allow POST
		if($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->_status(405); // Method Not Allowed
		}
		
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
			$member = (object)array_filter((array)$member);
		
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
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_status(405); // Method Not Allowed
		}
			
		// Hack for GET /api/get_member/*id*
		if(empty($value)) {
			$value = $key;
			$key = 'id';
		}
		
		// Failsafe against empty values, invalid inputs and/or similar
		if(empty($value) || ($key == 'id' && (!is_numeric($value) || $value < 1000))) {
			$this->_status(400); // Bad Request
		}
		
		// Get member by key
		$member = $this->Member_model->get_member($key, urldecode($value));
		
		// Return 404 if not found
		if(!$member) {
			$this->_status(404); // Not found
		}
		
		// Unset password-related fields.
		unset($member->password, $member->reset_token, $member->reset_expire);
		
		// Remove NULL and empty fields (incl. false).
		$member = (object)array_filter((array)$member);
		
		// And return as JSON
		$this->_json_out($member);
		
	}
	
	/**
	 * Get Member Groups resource.
	 */
	public function get_member_groups($member_id = 0) {
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_status(405); // Method Not Allowed
		}
		
		// Check member_id input
		if(empty($member_id) || $member_id < 1000) {
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
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Only allow POST
		if($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->_status(405); // Method Not Allowed
		}
		
		// ToDo: Check for required fields
		// ToDo: Check if e-mail exits
		// ToDo: Check all POST fields and remove those we don't want (validation/normalize)
		// ToDo: Save to database
		
	}
	
	/**
	 * Update Member resource.
	 */
	public function update_member($member_id = 0) {
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Only allow POST
		if($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->_status(405); // Method Not Allowed
		}
		
		// ToDo: Check if member_id exists (try to get_member)
		// ToDo: Check all POST fields and remove those we don't want (validation/normalize)
		// ToDo: Save to database
		
	}
	
	
	/**
	 * Get Groups resource.
	 */
	public function get_groups() {
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_status(405); // Method Not Allowed
		}
		
		$groups = $this->Group_model->get_all();
		
		// Return groups as JSON
		$this->_json_out($groups);
		
	}
	
	/**
	 * Get Groups Members resource.
	 */
	public function get_group_members($group_id = 0) {
		
		// Require X-Email/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_status(405); // Method Not Allowed
		}
		
		// Try to get group
		$group = $this->Group_model->get_group($group_id);
		
		// Check if group exists
		if(!$group) {
			$this->_status(404); // Not Found
		}
		
		$members = $this->Group_model->group_members($group_id);
		
		if($members) {
			// Strip passwords from result.
			
			// Walk the entire result and get groups :)
			array_walk($members, create_function('&$m', 'unset($m->password, $m->reset_token, $m->reset_expire); $m = (object)array_filter((array)$m);'));
			
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
	
		// Only allow GET
		if($this->input->post()) {
			$this->_status(405); // Method Not Allowed
		}
		
		// Get newsletter by id
		$this->_status(404);
	
	}
	

	 
	/**
	 *******************************************************
	 ******************* PRIVATE METHODS *******************
	 *******************************************************
	 */
	 
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
	 * Normalize a add_member/update_member POST request.
	 */
	private function _normalize($array) {
		
		// Allowed form fields
		$fields = array(
			'email', 'password', 'twitter', 'skype', 'mobile', 'phone',
			'firstname', 'lastname', 'company', 'orgno', 'address',
			'address2', 'zipcode', 'city', 'country', 
		);
		
		// Filter out only those fields we allow
		$data = elements($fields, $array, NULL);
	
		// Remove false/null/0 values
		$data = array_filter($data);
		
		// Make an exception for the password field
		if(!empty($data['password'])) {
			
			// Load password library
			$this->load->library('Pass');
			
			// Hash the password 
			$data['password'] = $this->pass->hash($data['password']);
			
		}
		
		return $data;
	
	}
	
} 