<?php
/**
 * Model for database stored configuration (autoloaded)
 *
 * @author Jim Nelin
 * @original_author http://stackoverflow.com/users/607354/oytunoytun
 * @todo Memcache this.
 */
class DBConfig extends CI_Model {

	function __construct() {
		parent::__construct();

		if ($this->config->item('use_db_config')) {

			$dbconf = array();
			
			// Get config from db
			$result = $this->db->select('key, value')->get('config')->result();

			foreach($result as $conf) {
				$dbconf[addslashes($conf->key)] = (is_json($conf->value)) ? json_decode($conf->value) : $conf->value;
			}

		} else {
			$dbconf = (object)array();
		}

		$CI = &get_instance();
		$CI->dbconfig = (object)$dbconf;      
	} 

}