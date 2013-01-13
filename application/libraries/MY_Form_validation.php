<?php

/**
 * MY Form Validation Class by Jim Nelin
 * Adds a third way to get errors - as an array.
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