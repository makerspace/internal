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

    /**
     * Valid Date (ISO format)
     *
     * @access    public
     * @param    string
     * @return    bool
	 * @todo Rewrite to not use ereg (deprecated)
     */
    function valid_date($str) {
	
	/*
		$CI =& get_instance();
		$CI->form_validation->set_message('valid_date', 'The %s field must be in format: YYYY-MM-DD.');
	
        if ( ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $str) ) 
        {
            $arr = split("-", $str);    // splitting the array
            $yyyy = $arr[0];            // first element of the array is year
            $mm = $arr[1];              // second element is month
            $dd = $arr[2];              // third element is days
            return ( checkdate($mm, $dd, $yyyy) );
        } 
        else 
        {
            return FALSE;
        }
	*/
	
		return true;
	
    }

}