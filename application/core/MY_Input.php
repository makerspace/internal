<?php

/**
 * CI Input extension library
 * Adds is_* functions among others.
 *
 * @author Jim Nelin
 */
class MY_Input extends CI_Input {

	public function __construct() {
		parent::__construct();
	}
   
	/**
	 * Detect posts (shouldn't be needed, but we do it anyway)
	 **/
    public function is_post()  {
		return ($_SERVER['REQUEST_METHOD'] === 'POST');
	}
	
	/**
	 * Detect ajax requests using GET/POST or HTTP-header
	 **/
	public function is_ajax()  {
	
		// First check querystring
		if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
			return true;
		}
		
		// Sencondly, check post data (if post)
		if (!empty($_POST) && isset($_POST['ajax']) && $_POST['ajax'] == true) {
			return true;
		}
	
		// Thirdly, check HTTP field
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
			return true;
		}
		
		// Default to false
		return false;
	}

}  