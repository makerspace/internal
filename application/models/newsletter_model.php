<?php
/**
 * Newsletter Module
 * @author Jim Nelin
 **/

class Newsletter_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	
		// Load e-mail model
		$this->load->model('Email_model');
	}

	/**
	 * Create newsletter in db.
	 **/
	public function create($recipients, $subject, $body) {
	
		$data = array(
			'created_by' => member_id(),
			'recipients' => json_encode($recipients),
			'subject' => $subject,
			'body' => $body,
			'created' => time(),
			'last_updated' => time(),
		);
		
		// Save to database
		$this->db->insert('newsletters', $data);

		// Return id if successfull
		if($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		}
		
		return false;
		
	}
	/**
	 * Save updated newsletter to db.
	 **/
	public function save($id, $subject, $body) {
	
		$data = array(
			'subject' => $subject,
			'body' => $body,
			'last_updated' => time(),
		);
		
		// Save to database
		$this->db->update('newsletters', $data, array('id' => $id), 1);

		// Return result
		return (bool)$this->db->affected_rows();
		
	}
	
	/**
	 * Get newsletter based based upon id.
	 */
	public function get($id) {
		$query = $this->db->get_where('newsletters', array('id' => $id), 1);
		if($query->num_rows() > 0) return $query->row();
		
		return false;
	}
	
	/**
	 * Get all newsletters.
	 */
	public function get_all() {
		$query = $this->db->order_by('id', 'desc')->get('newsletters');
		if($query->num_rows() > 0) return $query->result();
		
		return array();
	}
	
	/**
	 * Send a Test Newsletter to current member.
	 **/
	public function test_send($subject, $body) {
		
		// Get current member
		$member = $this->Member_model->get_member();
		
		// Create a new e-mail to member.
		$email = $this->Email_model->new_email($member->email, $member->fullname);
		$email->IsHTML(true); // Turn on HTML
		 
		// Replace placeholders and set subject and body.
		$email->Subject = $this->_replace_placeholders($subject, $member);
		$email->Body = $this->_replace_placeholders($body, $member);
		
		// Send e-mail.
		return $email->Send();
		
	}
	
	/**
	 * Private method for replacement of {placeholders} to actual data.
	 **/
	private function _replace_placeholders($str, $member) {
		// Not implemented yet.
		return $str; 
	}
	
}
