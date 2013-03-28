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
	
	public function get_group_by_name($name) {
		
		// Get group by name
		$query = $this->db->get_where('groups', array('name' => $name));
	
		// Check if we got anything.
		if($query->num_rows() > 0) {
			
			// Return result.
			return $query->row();	
			
		}
		
		// No results.
		return array();
	}
	
	public function newsletter_groups() {
	
		// Get all groups
		$this->db->select('name, description')->order_by('name');
		$query = $this->db->get('groups');
		
		// Default as empty
		$return = array();
		
		// Check if we got anything.
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$return[$row->name] = $row->name.' - '.$row->description;
			}
		}
		
		return $return;
		
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
	
}