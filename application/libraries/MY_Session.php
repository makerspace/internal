<?php

/**
 * Session Class
 *
 * @package		MY_Session
 * @desc		Memcache cache handler, entirly memcache-based - no more huge cookies.
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Jim Nelin
 * @link		http://www.jine.se
 */
 
class MY_Session extends CI_Session {

    var $sess_table_name = '';
    var $sess_expiration = 3600;
    var $sess_expire_on_close = TRUE;
    var $sess_match_ip = TRUE;
    var $sess_match_useragent = TRUE;
    var $sess_cookie_name = 'ci_session';
    var $sess_memcache_name = 'session_';
    var $cookie_prefix = '';
    var $cookie_path = '';
    var $cookie_domain = '';
    var $cookie_secure = FALSE;
    var $sess_time_to_update = 300;
    var $flashdata_key = 'flash_';
    var $userdata = array();
    var $CI;
    var $now;
    var $memcached_port = 11211;
    var $memcached_nodes = array('127.0.0.1');
	var $cookie_written = false;

    /**
     * Session Constructor
     *
     * The constructor runs the session routines automatically
     * whenever the class is instantiated.
     */
    public function __construct($params = array()) {
	
        log_message('debug', "Session constructed");
		
        // Set the super object to a local variable for use throughout the class
        $this->CI = & get_instance();
				
        // Set all the session preferences, which can either be set
        // manually via the $params array above or via the config file
        foreach (array('sess_expiration', 'sess_expire_on_close', 'sess_match_ip', 'sess_match_useragent', 'sess_cookie_name', 'cookie_path', 'cookie_domain', 'cookie_secure', 'sess_time_to_update', 'cookie_prefix') as $key) {
            $this->$key = (isset($params[$key])) ? $params[$key] : $this->CI->config->item($key);
        }

        $this->now = time();

        // Set the cookie name
        $this->sess_cookie_name = $this->cookie_prefix . $this->sess_cookie_name;

		// Get user agent lib
		$this->CI->load->library('user_agent');

		// Check if this is a crawler or it's a json-request or api request.
		if ($this->CI->agent->is_robot() || strpos(current_url(), '.json') !== false || strpos(current_url(), '/api/') !== false) {
			// Don't do shit.
			return;
		}
		
        // Run the Session routine. If a session doesn't exist we'll
        // create a new one.  If it does, we'll update it.
        if (!$this->sess_read()) {
            log_message('info', 'Couldn\'t read session, creating new one.');
            $this->sess_create();
        } else {
            log_message('info','Run sess_update()');
            $this->sess_update();
        }

        log_message('debug', "Session routines successfully run");
    }

    // --------------------------------------------------------------------

    /**
     * Fetch the current session data if it exists
     *
     * @access	public
     * @return	bool
     */
    function sess_read() {
        // Fetch the cookie
        $this->session_id = $this->CI->input->cookie($this->sess_cookie_name);

        // No cookie?  Goodbye cruel world!...
        if ($this->session_id === false) {
            log_message('debug', 'A session cookie was not found.');
            return false;
        }
		
		// Get session from memcache
		$result = $this->CI->memcache->get($this->sess_memcache_name . $this->session_id);
	   
		if ($result === false) {
			$this->sess_destroy();
			log_message('debug', 'Session not found in memcache');
			return false;
		}
		
		// Check session against IP and user-agent
		$user_agent = $this->CI->input->user_agent();
		
		if($result['ip_address'] != ip_address() || $result['user_agent'] != $user_agent) {
			$this->sess_destroy();
			log_message('debug', 'Session does not match! Hacking attempt?');
			return false;
		}
		
        // Session is valid!
        $this->userdata = $result;
        unset($result);
		
		// Update last_activity
        $this->userdata['last_activity'] = $this->now;

		if(!empty($this->userdata['remember_me'])) {
			$this->sess_expiration = 1209600; // 14 days
		}
		
		$this->CI->memcache->replace($this->sess_memcache_name . $this->session_id, $this->userdata, $this->sess_expiration);
		
		log_message('debug', 'Replaced session in memcache');

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Write the session data
     *
     * @access	public
     * @return	void
     */
    function sess_write() {
		$this->userdata['last_activity'] = $this->now;
		
		if(!empty($this->userdata['remember_me'])) {
			$this->sess_expiration = 1209600;
		}
		
		$this->CI->memcache->replace($this->sess_memcache_name . $this->userdata['session_id'], $this->userdata, $this->sess_expiration);
		
		log_message('debug', 'Session written to memcache.');
		
        // Write the cookie
        $this->_set_cookie();
    }

    // --------------------------------------------------------------------

    /**
     * Create a new session
     *
     * @access	public
     * @return	void
     */
    function sess_create() {
	
        $session_id = random_string('alnum', 128);

        $this->userdata = array(
            'session_id' => $session_id,
            'ip_address' => ip_address(),
            'user_agent' => $this->CI->input->user_agent(),
            'last_activity' => $this->now
        );
		
		$this->CI->memcache->set($this->sess_memcache_name . $this->userdata['session_id'], $this->userdata, $this->sess_expiration);
		
		log_message('debug', 'Session created in memcache - session_id = '.$this->userdata['session_id']);

        // Write the cookie
        $this->_set_cookie();
    }

    // --------------------------------------------------------------------

    /**
     * Update an existing session
     *
     * @access	public
     * @return	void
     */
    function sess_update() {
        // We only update the session every five minutes by default
        if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now) {
            log_message('info','not enough time before update');
            return;
        }
		
        log_message('info','Updating session cause of last_activity + sess_time_to_update is more then time()');
        
        // Save the old session id so we know which record to
        // update in the database if we need it
        $old_sessid = $this->userdata['session_id'];

        // Turn it into a hash
        $new_sessid = random_string('alnum', 128);
        log_message('info','Session id generated.');
		
        // Update the session data in the session data array
        $this->userdata['session_id'] = $this->session_id = $new_sessid;
        $this->userdata['last_activity'] = $this->now;

		if(!empty($this->userdata['remember_me'])) {
			$this->sess_expiration = 1209600;
		}
		
		// Add item with new session_id and data to memcached
		// then delete old memcache item
		$this->CI->memcache->add($this->sess_memcache_name . $new_sessid, $this->userdata, $this->sess_expiration);
		log_message('info', 'Adding new session');
		
		$this->CI->memcache->delete($this->sess_memcache_name . $old_sessid, 0);
		log_message('info', 'Old Session Deleted');           

        // Write the cookie
        $this->_set_cookie();
    }

    // --------------------------------------------------------------------

    /**
     * Destroy the current session
     *
     * @access	public
     * @return	void
     */
    function sess_destroy() {

		if (isset($this->userdata['session_id'])) {
			$this->CI->memcache->delete($this->sess_memcache_name . $this->userdata['session_id'], 0);
			log_message('debug', 'Session destroyed');
		}

        // Kill the cookie
        setcookie($this->sess_cookie_name, '', ($this->now - 31500000), $this->cookie_path, $this->cookie_domain, true);
		
    }

    // --------------------------------------------------------------------

    /**
     * Write the session cookie
     *
     * @access	public
     * @return	void
     */
    function _set_cookie() {
		
		if($this->cookie_written) return;
		
		$session_id = $this->userdata['session_id'];
		
		if(!empty($this->userdata['remember_me'])) {
			$this->sess_expire_on_close = false;
			$this->sess_expiration = 1209600;
		}
		
        $expire = ($this->sess_expire_on_close === true) ? 0 : time() + $this->sess_expiration;

        // Set the cookie
        setcookie($this->sess_cookie_name, $session_id, $expire, $this->cookie_path, $this->cookie_domain, $this->cookie_secure);
		
		log_message('debug', 'Session coookie set');
			
		// Only do this once for session cookies.
		$this->cookie_written = true;
    }
	
    // --------------------------------------------------------------------
	
    /**
     * Fetch all session data
     *
     * @access	public
     * @return	mixed
     */
    function all_userdata() {
        return (!isset($this->userdata)) ? FALSE : $this->userdata;
    }

    // --------------------------------------------------------------------

    /**
     * Add or change data in the "userdata" array
     *
     * @access	public
     * @param	mixed
     * @param	string
     * @return	void
     */
    function set_userdata($newdata = array(), $newval = '') {
        if (is_string($newdata)) {
            $newdata = array($newdata => $newval);
        }

        if (count($newdata) > 0) {
            foreach ($newdata as $key => $val) {
                $this->userdata[$key] = $val;
            }
        }

		log_message('debug', 'Userdata is set');
		
        $this->sess_write();
    }

    // --------------------------------------------------------------------

    /**
     * Delete a session variable from the "userdata" array
     *
     * @access	array
     * @return	void
     */
    function unset_userdata($newdata = array()) {
        if (is_string($newdata)) {
            $newdata = array($newdata => '');
        }

        if (count($newdata) > 0) {
            foreach ($newdata as $key => $val) {
                unset($this->userdata[$key]);
            }
        }
		
		log_message('debug', 'Userdata is unset');

        $this->sess_write();
    }

    // ------------------------------------------------------------------------

    /**
     * Add or change flashdata, only available
     * until the next request
     *
     * @access	public
     * @param	mixed
     * @param	string
     * @return	void
     */
    function set_flashdata($newdata = array(), $newval = '') {
        if (is_string($newdata)) {
            $newdata = array($newdata => $newval);
        }

        if (count($newdata) > 0) {
            foreach ($newdata as $key => $val) {
                $flashdata_key = $this->flashdata_key . $key;
                $this->set_userdata($flashdata_key, $val);
            }
        }
		
		log_message('debug', 'Setting flashdata with key: '.$flashdata_key);
    }

    // ------------------------------------------------------------------------

    /**
     * Fetch a specific flashdata item from the session array
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function flashdata($key) {
        $flashdata_key = $this->flashdata_key . $key;
        $flashdata = $this->userdata($flashdata_key);
		
		$this->unset_userdata($flashdata_key);
		
		log_message('debug', 'Displaying and unsetting flashdata with key: '.$flashdata_key);
		
		return $flashdata;
    }
	
    // ------------------------------------------------------------------------

    /**
     * Keeps existing flashdata available to next request.
     *
     * @access	public
     * @param	string
     * @return	void
     */
    function keep_flashdata($key) {
	
        $flashdata_key = $this->flashdata_key . $key;
        $flashdata = $this->userdata($flashdata_key);
		
		$this->set_flashdata($flashdata_key, $flashdata);
		
    }

}

/* End of file MY_Session.php */
/* Location: ./application/libraries/MY_Session.php */
