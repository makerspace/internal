<?php
/**
 * HTML5 Form helper for CI
 * Adds HTML5-form elements.
 *
 * @author Unknown
 **/
 
/**
 * Common Input Field
 *
 * @access    public
 * @param    string
 * @param    mixed
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('form_common'))
{
    function form_common($type = 'text', $data = '', $value = '', $extra = '')
    {
        $defaults = array('type' => $type, 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

        return "<input "._parse_form_attributes($data, $defaults).$extra." />";
    }
}

/**
 * Email Input Field
 *
 * @access    public
 * @param    mixed
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('form_email'))
{
    function form_email($data = '', $value = '', $extra = '')
    {
        return form_common($type = 'email', $data, $value, $extra);
    }
}

/**
 * Url Input Field
 *
 * @access    public
 * @param    mixed
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('form_url'))
{
    function form_url($data = '', $value = '', $extra = '')
    {
        return form_common($type = 'url', $data, $value, $extra);
    }
}

/**
 * Number Input Field
 *
 * @access    public
 * @param    mixed
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('form_number'))
{
    function form_number($data = '', $value = '', $extra = '')
    {
        return form_common($type = 'number', $data, $value, $extra);
    }
}

/**
 * Number Input Field
 *
 * @access    public
 * @param    mixed
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('form_range'))
{
    function form_range($data = '', $value = '', $extra = '')
    {
        return form_common($type = 'range', $data, $value, $extra);
    }
}


/* End of file MY_form_helper.php */
/* Location: ./application/helpers/MY_form_helper.php */ 