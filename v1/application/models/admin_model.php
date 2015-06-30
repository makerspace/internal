<?php

/**
 * Administration Model
 * @author Jim Nelin
 */
class Admin_model extends CI_Model {

	/**
	 * Get all dbconfig items
	 */
	public function get_dbconfig() {
	
		// Take config-item into consideration.
		if ($this->config->item('use_db_config')) {
			
			// Get all from db
			$query = $this->db->order_by('key')->get('config');
			
			// Return if results
			if($query->num_rows()) {
				return $query->result();
			}
		}
		
		// Default to an empty array
		return array();
	}
	
	/**
	 * Add new config item.
	 */
	public function add_config($post) {
	
		$data = array(
			'key' => $post['key'],
			'value' => $post['value'],
			'desc' => (empty($post['desc']) ? NULL : $post['desc']),
		);
		
		// Insert info db
		$this->db->insert('config', $data);
		
		// Check result
		if($this->db->affected_rows()) {
		
			// Delete any memcache object
			$this->memcache->delete('dbconfig');
			
			// Return
			message('Successfully created new config item.');
			return true;
			
		} else {
			error('Couldn\'t create config item, please try again.');
		}
		
		return false;
		
	}
	
	/**
	 * Add new config key/value and desc
	 */
	public function set_config($key, $value) {
	
		// Update config key
		$this->db->update('config', array('value' => $value), array('key' => $key));
		
		// Check result
		if($this->db->affected_rows()) {
		
			// Delete any memcache object
			$this->memcache->delete('dbconfig');
			
			// Return
			message('Successfully updated config item.');
			return true;
			
		} else {
			error('Couldn\'t update config, please try again.');
		}
		
		return false;
		
	}
}
