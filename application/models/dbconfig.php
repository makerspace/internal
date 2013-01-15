<?php
/**
 * Model for database stored configuration (autoloaded)
 *
 * @author Jim Nelin
 * @original_author http://stackoverflow.com/users/607354/oytunoytun
 */
class DBConfig extends CI_Model {

	function __construct() {
		parent::__construct();

		if ($this->config->item('use_db_config')) {
		
			// Get CI instance so we can set a global var.
			$CI = &get_instance();
			
			// First check memcache, if found use dbconfig from memcache
			if($cached = $this->memcache->get('dbconfig')) {
			
				// Override data origin.
				$cached->_data_origin = 'memcached';
				
				$CI->dbconfig = $cached;
				return true;
			}
			
			// Get config from db
			$result = $this->db->select('key, value')->get('config')->result();

			
			// Create new array and set data origin
			$dbconf = array('_data_origin' => 'database');
			
			// Loop through db-result.
			foreach($result as $conf) {
				$dbconf[addslashes($conf->key)] = (is_json($conf->value) ? json_decode($conf->value) : $conf->value);
			}
			
			// Cache in memcache forever
			$this->memcache->set('dbconfig', (object)$dbconf, 0);

		} else {
			// Default to an empty array
			$dbconf = array();
		}

		// Set dbconfig from database result.
		$CI->dbconfig = (object)$dbconf;
	} 

}