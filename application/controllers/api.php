<?php
/**
 * REST Api Controller for Internal
 * 
 * @author Jim Nelin
 **/
 
class Api extends CI_Controller {

	/**
	 * Constructor
	 **/
	public function  __construct() {
		parent::__construct();
		
		// Check Request Method
		if(!in_array($_SERVER['REQUEST_METHOD'], array('POST', 'GET'))) {
			$this->_error(405); // Method not allowed
		}
	}
	
	/**
	 * Index: API Documentation
	 **/
	public function index() {
		
		// Only allow GET
		if($this->input->post()) {
			$this->_error(501);
		}
		
		$this->load->view('api/documentation');
	
	}
	
	/**
	 * Member resource.
	 **/
	public function member($key = '', $value = '') {
	
		// ToDo: This should be protected by authentication.
		// ... so until that's implemented, return service unavailable.
		$this->_error(503);
			
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
			
			// Get member
			$member = $this->Member_model->get_member($key, $value);
			
			// If not found, 404
			if(!$member) {
				$this->_error(404);
			}
			
			// Unset password-related fields.
			unset($member->password, $member->reset_token, $member->reset_expire);
			
			// Unset comment-field
			unset($member->comment);
			
			// Remove NULL and empty fields (incl. false).
			$member = (object) array_filter((array) $member);
			
			// And return as JSON
			$this->_out($member);
		
		}
		
	}
	
	/**
	 * Newsletter resource.
	 **/
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
	 **/
	public function coffee() {
		$this->_error(418, 'I\'m a teapot');
	}

	/**
	 * Set HTTP Status and exit
	 **/
	private function _error($code, $str = '') {
		$this->output->set_status_header($code, $str);
		exit;
	}
	
	/**
	 * JSON Output Method
	 */
	private function _out($data) {

		// Encode as JSON
		$json = json_encode($data);
		
		// Set Content-Type and output
		$this->output->set_content_type('application/json')->set_output($json);

	}
} 