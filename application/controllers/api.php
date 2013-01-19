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
			$this->_error(501);
		}
		
		$this->load->view('api/documentation');
	
	}
	
	/**
	 * Member resource.
	 */
	public function member($key = '', $value = '') {
		
		// Require X-Username/Password auth.
		$this->_check_authentication();
			
		// POST: Add new member
		if($this->input->post()) {
		
			$this->_error(501);
		
		// GET: Member by *key* or id
		} else {
			
			// Hack for GET /api/member/*id*
			if(empty($value)) {
				$value = $key;
				$key = 'id';
			}
			
			// Failsafe against empty values
			if(empty($value)) {
				$this->_error(400); // Bad Request
			}
			
			// Get member by key
			$member = $this->Member_model->get_member($key, $value);
			
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
		
	}
	
	/**
	 * Newsletter resource.
	 */
	public function newsletter() {
	
		// Only allow GET
		if($this->input->post()) {
			$this->_error(501);
		}
		
		// Get newsletter by id
		$this->_error(404);
	
	}
	
	/**
	 * HTCPC Protocol (RFC 2324)
	 */
	public function coffee() {
		$this->_error(418, 'I\'m a teapot');
	}

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