<?php

class Group_model extends CI_Model {

	public function get_all($limit = 1000, $offset = 0) {
		
		// Get all groups
		$this->db->order_by('name');
		$query = $this->db->limit($limit)->offset($offset)->get('groups');
	
		// Check if we got anything.
		if($query->num_rows() > 0) {
			
			// Return result array.
			return $query->result();
			
		}
		
		// No results.
		return array();
	}
	
	public function member_count($group_id) {
		
		$this->db->where('group_id', $group_id)->from('member_groups');
		return (int)$this->db->count_all_results();
		
	}
	
	public function get_group($key = '', $value = '') {
			
		// Hack for key/value
		if(empty($value)) {
			$value = $key;
			$key = 'id';
		}
		
		// Get group by key/value
		$query = $this->db->get_where('groups', array($key => $value));
	
		// Check if we got anything.
		if($query->num_rows() > 0) {
			
			// Return result.
			return $query->row();	
			
		}
		
		// No results.
		return array();
	}
	
	public function get_group_by_name($name = '') {
		return $this->get_group('name', $name);
	}
	
	public function member_groups($member_id = 0) {
	
		$this->db->select('groups.name')->join('groups', 'groups.id = member_groups.group_id');
		$query = $this->db->get_where('member_groups', array('member_id' => $member_id));
	
		$return = array();
		
		// Check if we got anything.
		if($query->num_rows() > 0) {
			
			foreach($query->result() as $row) {
				$return[$row->name] = true;
			}
			
		}
		
		return $return;
		
	}
	
	public function group_members($group_id = 0) {
	
		$this->db->select('members.*')->join('members', 'members.id = member_groups.member_id');
		$query = $this->db->get_where('member_groups', array('group_id' => $group_id));
			
		// Check if we got anything.
		if($query->num_rows() > 0) {
			
			// Walk the entire result and get groups :)
			array_walk($query->result(), array($this->Member_model, '_get_groups'));
			
			// Return result
			return $query->result();
			
		}
		
		// Nothing found.
		return array();
		
	}
	
	public function member_of_group($member_id = 0, $group_name = '') {
	
		$this->db->join('groups', 'groups.id = member_groups.group_id');
		$query = $this->db->get_where('member_groups', array('member_id' => $member_id, 'name' => $group_name));
		
		return (bool)$query->num_rows();
	
	}
	
	public function group_switch($member_id = 0, $group_name = '') {
	
		// Get member
		if(!$member = $this->Member_model->get_member($member_id)) {
			return false; // Failsafe
		}
		
		// Get group
		if(!$group = $this->get_group_by_name($group_name)) {
			return false; // Failsafe
		}
		
		// Don't allow non-admins to set admin-permissions.
		if(!$this->Group_model->member_of_group(member_id(), 'admins') && $group_name == 'admins') {
			return false;
		}
		
		// Check if user is already a member of this group
		if(empty($member->groups[$group->name])) {
		
			// Add member to group
			$this->db->insert('member_groups', array('member_id' => $member->id, 'group_id' => $group->id));
			
		} else {
		
			// Remove member from group
			$this->db->delete('member_groups', array('member_id' => $member->id, 'group_id' => $group->id));
				
		}
	
		return (bool)$this->db->affected_rows();
	}
	
	public function add_group($group_name, $description) {
	
		// Must be unique
		if($group = $this->get_group_by_name($group_name)) {
			return false; // Failsafe
		}
		
		// Add group
		$this->db->insert('groups', array('name' => $group_name, 'description' => $description));
		
		return (bool)$this->db->affected_rows();
		
	}
	
}
