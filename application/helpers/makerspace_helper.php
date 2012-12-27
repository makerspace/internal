<?php

/**
 * Auth/User Helpers
 * @author Jim Nelin
 **/
function is_loggedin() {
	$CI =& get_instance();
	return ($CI->session->userdata('logged_in') == true);
}

function gatekeeper() {
	if(!is_loggedin()) {
		error('You have to login to access this page.');
		redirect('/');
	}
}

function no_gatekeeper() {
	if(is_loggedin()) {
		error('You have to be logged out to access this page.');
		redirect('/');
	}
}

/**
 * Flashmessage helpers for CI
 **/
function get_flashdata() {
	$CI =& get_instance();

	$flash_message = $CI->session->flashdata('flash_message');
	$flash_error = $CI->session->flashdata('flash_error');
	
	if(!empty($flash_message)) {
		return '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'.htmlspecialchars($flash_message).'</div>';
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
 * Output helpers for CI
 * @author Jim Nelin
 **/ 
function json_output($json) {

	$CI =& get_instance();
	$CI->output->set_content_type('application/json')->set_output(json_encode($json));

}

/**
 * String helpers for CI
 * @author Jim Nelin
 **/
function ip_address() {
	return $_SERVER['REMOTE_ADDR'];
}

/**
 * Gravatar Helper
 * @author Jim Nelin
 **/
function gravatar($email, $rating = 'pg', $size = 32, $default = 'retro') {
	
	// Optional options
	$options = array();
	if ($rating) $options[] = "rating=$rating";
	if ($size) $options[] = "size=$size";
	if ($default) $options[] = "default=$default";
	
	// Return gravatar URL
	return 'https://secure.gravatar.com/avatar/' . md5(strtolower(trim($email))) . implode($options, '&');
}
