<?php
/**
 * REST Api Controller for Internal
 * 
 * @author Jim Nelin
 */
 
class Api extends CI_Controller {

	/**
	 * Constructor
	 */
	public function  __construct() {
		parent::__construct();
		
		// Check Request Method
		if(!in_array($_SERVER['REQUEST_METHOD'], array('POST', 'GET'))) {
			$this->_error(405); // Method not allowed
		}
	}
	
	/**
	 * Index: API Documentation
	 */
	public function index() {
		
		// Only allow GET
		if($this->input->post()) {
			$this->_error(405);
		}
		
		$this->load->view('api/documentation');
	
	}
	
	/**
	 * Auth resource.
	 */
	public function auth() {
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// Only allow POST
		if(!$this->input->post()) {
			$this->_error(405);
		}
		
		// ToDo: Try authentication here
		
	}
	 
	 
	/**
	 * Get Member resource.
	 */
	public function get_member($key = '', $value = '') {
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_error(405);
		}
			
		// Hack for GET /api/get_member/*id*
		if(empty($value)) {
			$value = $key;
			$key = 'id';
		}
		
		// Failsafe against empty values
		if(empty($value)) {
			$this->_error(400); // Bad Request
		}
		
		// Get member by key
		$member = $this->Member_model->get_member($key, urldecode($value));
		
		// Return 404 if not found
		if(!$member) {
			$this->_error(404);
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
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_error(501);
		}
		
		// Check member_id input
		if(empty($member_id) || $member_id < 1000) {
			$this->_error(400); // Bad request
		}
		
		// ToDo: Check if member_id exists (try to get_member)
		// ToDo: Get Member Groups for member
		
	}
	
	/**
	 * Add Member resource.
	 */
	public function add_member() {
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// Only allow POST
		if(!$this->input->post()) {
			$this->_error(405);
		}
		
		// ToDo: Check if e-mail exits
		// ToDo: Check all fields (validation)
		// ToDo: Save to database
		
	}
	
	/**
	 * Update Member resource.
	 */
	public function update_member($member_id = 0) {
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// Only allow POST
		if(!$this->input->post()) {
			$this->_error(405);
		}
		
		// ToDo: Check if member_id exists (try to get_member)
		// ToDo: Check all POST fields and remove those we don't want (validation/normalize)
		// ToDo: Save to database
		
	}
	
	
	/**
	 * Get Groups resource.
	 */
	public function get_groups() {
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_error(501);
		}
		
		// ToDo: Get All Groups
		
	}
	
	/**
	 * Get Groups Members resource.
	 */
	public function get_group_members($group_id) {
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// Only allow GET
		if($this->input->post()) {
			$this->_error(501);
		}
		
		// ToDo: Check if group_id exists (try to get_group)
		// ToDo: Get members in group by id
		
	}
	
	/**
	 * HTCPC Protocol (RFC 2324)
	 */
	public function coffee() {
		$this->_error(418, 'I\'m a teapot');
	}
	
	
	/**
	 * Newsletter resource.
	public function newsletter() {
	
		// Only allow GET
		if($this->input->post()) {
			$this->_error(501);
		}
		
		// Get newsletter by id
		$this->_error(404);
	
	}
	 */

	/**
	 * Set HTTP Status and exit
	 */
	private function _error($code, $str = '') {
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
	 * Check authentication headers (X-Username, X-Password)
	 * @using login method in member model.
	 */
	private function _check_authentication($check_acl = true) {
	
		$username = $this->input->get_request_header('X-Username');
		$password = $this->input->get_request_header('X-Password');
		
		// Check input
		if(empty($username) || empty($password)) {
			$this->_error(403); // Forbidden
		}
		
		
		// Try to login but don't set session.
		$login = array('email' => $username, 'password' => $password);
		$result = $this->Member_model->login($login, false);
		
		// Check result
		if($result) {
			
			// Check ACL (if needed)
			if($check_acl) {
				## Here.
			}
			
			return true;
		}
	
		// Default to Forbidden
		$this->_error(403);
		
	}
} 