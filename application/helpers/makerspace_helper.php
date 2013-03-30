<?php

/**
 * Auth/Member Helpers
 * @author Jim Nelin
 */
function is_loggedin() {
	$CI =& get_instance();
	return ($CI->session->userdata('logged_in') == true);
}

function gatekeeper() {
	if(!is_loggedin()) {
		error('You have to login to access this page.');
		redirect();
	}
}

function no_gatekeeper() {
	if(is_loggedin()) {
		error('You have to be logged out to access this page.');
		redirect();
	}
}

function member_id() {
	$CI =& get_instance();
	
	if(is_loggedin()) {
		return (int)$CI->session->userdata('member_id');
	}
	
	// Failsafe, shouldn't happen.
	error('ERROR! Requested member_id but no member is signed in.');
	redirect();
}

/**
 * Flashmessage helpers for CI
 */
function get_flashdata() {
	$CI =& get_instance();

	$flash_message = $CI->session->flashdata('flash_message');
	$flash_error = $CI->session->flashdata('flash_error');
	
	if(!empty($flash_message)) {
		return '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'.htmlspecialchars($flash_message).'</div>';
	} elseif(!empty($flash_error)) {
		return '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'.htmlspecialchars($flash_error).'</div>';
	}

}

function error($error) {
	$CI =& get_instance();
	$CI->session->set_flashdata('flash_error', $error);
}

function message($message) {
	$CI =& get_instance();
	$CI->session->set_flashdata('flash_message', $message);
}



/**
 * Error helper for CI
 * @author Jim Nelin
 */ 
function get_errors() {

	$CI =& get_instance();
	$arr = $CI->form_validation->error_array();
	
	if(!empty($arr)) {
		$output = '';
		foreach($arr as $msg) {
			$output .= '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'.htmlspecialchars($msg).'</div>';
		}
	
		return $output;
	}	
}

/**
 * String helpers for CI
 * @author Jim Nelin
 */
function ip_address() {
	return $_SERVER['REMOTE_ADDR'];
}

function is_json($str) {

	// Speed things up.
	if($str[0] != '{' && $str[0] != '[') {
		return false;
	}
	
	@json_decode($str);
	return (json_last_error() == JSON_ERROR_NONE);
}

function when($timestamp) {
	return date('Y-m-d H:i:s', $timestamp);
}

/**
 * Strip whitespaces (and similar)
 */
function strim($str) {
	return preg_replace('/\s+/', '', $str);
}

function normalize_phone($str) {

	$str = preg_replace('/[^\d]/', '', $str);

	if($str[0] == '0') {
		return '+46'.substr($str, 1);
	} elseif(substr($str, 0, 1) == '46') {
		return '+'.$str;
	} elseif($str[0] == '7') {
		return '+46'.$str;
	} elseif($str[0] == '8') {
		return '+46'.$str;
	}
	
	return '+'.$str;
	
}


/**
 * Check if array keys exists in array.
 */
function array_keys_exist($keys, $array) {
    if (count(array_intersect($keys, array_keys($array))) == count($keys)) {
        return true;
    }
	
	return false;
}

/**
 * Function to generate v4 UUID
 */
function uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

/**
 * Gravatar Helper
 * @author Jim Nelin
 */
function gravatar($email, $size = 32, $rating = 'pg', $default = 'mm') {
	
	// Optional options
	$options = array();
	if ($rating) $options[] = "rating=$rating";
	if ($size) $options[] = "size=$size";
	if ($default) $options[] = "default=$default";
	
	// Return gravatar URL
	return 'https://secure.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?'. implode($options, '&');
}

/**
 * Menu helper
 */
function menu_active($controller) {
	
	$CI =& get_instance();
	
	if($CI->router->fetch_class() == $controller) {
		return ' class="active"';
	}
	
}

/**
 * XML Helpers
 */

/**
 * array_to_xml - Converts an PHP array to XML. 
 * From: http://www.kerstner.at/en/2011/12/php-array-to-xml-conversion/
 *
 * @param array $array the array to be converted
 * @param string $root if specified will be taken as root element, defaults to <root>
 * @param SimpleXMLElement? if specified content will be appended, used for recursion
 * @return string XML version of $array
 */
function array_to_xml($array, $root = 'root', $xml = null) {
	$_xml = $xml;

	if ($_xml === null) {
		$_xml = new SimpleXMLElement('<'.$root.'/>');
	}

	foreach ($array as $k => $v) {
		if (is_array($v)) { //nested array
			array_to_xml($v, $k, $_xml->addChild($k));
		} else {
			$_xml->addChild($k, $v);
		}
	}

	return $_xml->asXML();
}
