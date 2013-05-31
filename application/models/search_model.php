<?php

class Search_model extends CI_Model {
	
	/**
	 * Method to search for members (wildcard)
	 * @todo Full-text search or similar? Sphinx?
	 */
	public function member($keyword) {
	
		// UGLY hack to allow "group:" searches.
		// ToDo: PLEASE make this be prettier... !!!
		if(substr($keyword, 0, 6) == 'group:') {
		
			// Get group (if it exists)
			$group = $this->Group_model->get_group_by_name(substr($keyword, 6));
			if(!$group) return array(); // No such group...
			
			
			// Return members based upon group id
			return $this->Group_model->group_members($group->id);
			
		}
		
		
		// **** Actually search for users ****
		
		// Search in these fields
		$search_fields = array(
			'id', 'firstname', 'lastname', 'company', 'orgno',
			'address', 'address2', 'zipcode', 'city', 'country',
			'phone', 'twitter', 'skype', 'civicregno',
		);
		
		// Search in e-mail first.
		$this->db->like('email', $keyword);
		
		// Hack to be able to search for fullnames
		$this->db->or_like('CONCAT_WS(\' \', firstname, lastname)', $keyword, 'both', false);
		
		// Loop, cause it's easier.
		foreach($search_fields as $field) {
			if($field == 'phone') {
				$keyword = normalize_phone($keyword);
			}
			$this->db->or_like($field, $keyword);
		}
		
		// Do query
		$query = $this->db->get('members');
		
		// Did we get anything?
		if($query->num_rows() > 0) {
			
			// Walk the entire result and get groups :)
			array_walk($query->result(), array($this->Member_model, '_get_groups'));
			
			// Return matching users
			return $query->result();
		}
	
		// Nothing found
		return array();
	}
	
}