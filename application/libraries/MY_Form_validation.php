<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MY Form Validation Class by Jim Nelin
 * Adds a third way to get errors, as an array
 *
 * @author		Jim Nelin
 */
class MY_Form_validation extends CI_Form_validation {

	protected $_error_array			= array();

	/**
	 * Error Array
	 *
	 * Returns the error messages as an array
	 *
	 * @access	public
	 * @return	array
	 */
	public function error_array()
	{
		// No errrors, validation passes!
		if (count($this->_error_array) === 0)
		{
			return '';
		}

		return $this->_error_array;
	}

	

}