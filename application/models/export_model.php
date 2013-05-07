<?php

class Export_model extends CI_Model {

	/**
	 * Export based upon POST
	 */
	public function export_post() {
		
		// Get allowed export fields
		$allowed_fields = $this->Member_model->export_fields();
		
		// Get posted data
		$post_fields = $this->input->post('fields');
		$order_by = $this->input->post('order_by');
		$sort = $this->input->post('sort');
		$post_groups = $this->input->post('groups');
		$filetype = key($this->input->post('export'));
		
		
		// Start by filtering those fields we want
		$fields = array();
		foreach($post_fields as $field) {
			if(is_string($field) && array_key_exists($field, array_flip($allowed_fields))) {
				$fields[] = $field;
			}
		}
		
		// Check if we got any fields, just in case.
		if(empty($fields) || !is_array($fields) || count($fields) < 1) {
			// Opps, we don't... !
			error('You must select at least one field to export');
			redirect('members/export');
		} 
		
		// Security for order_by
		if(!array_key_exists($order_by, array_flip($allowed_fields))) {
			$order_by = 'id';
		}
		
		// Set ordering direction
		if($sort != 'desc') {
			$sort = 'asc';
		}
		
		// Now, select them.
		foreach($fields as $field) {
			$this->db->select('members.'.$field);
		}
		
		// Check if we want members from one or more groups
		if(!empty($post_groups) && is_array($post_groups) && count($post_groups) > 0) {
		
			
			// Join the members of the select groups
			$this->db->join('members', 'members.id = member_groups.member_id');
			
			// ... for those groups we want users from.
			$this->db->where_in('member_groups.group_id', array_values($post_groups));
			
			// Order by member id
			$this->db->order_by('members.'.$order_by, $sort); 
			
			// Get, distinct
			$this->db->distinct();
			$result = $this->db->get('member_groups');
		
		// We don't, just return all with the selected fields 
		} else {
		
			// Order by member id
			$this->db->order_by('members.'.$order_by, $sort); 
		
			// Get all members
			$result = $this->db->get('members');
		
		}

		// ToDo: Allow exports in xml, json and similar to...
		// echo $this->dbutil->xml_from_result($result, $config);
		
		// Export result as selected filetype.
		$this->_export($result, $filetype);
		
	}
	
	/**
	 * Export all members in a group
	 */
	public function export_group() {
		
		// ToDo...
		
	}
	
	/**
	 * Export a single member
	 */
	public function export_member($member_id) {
		
		// Get fields to export
		$fields = $this->Member_model->export_fields();
		
		// Start by selecting the fields...
		$this->db->select($fields);
		
		// and then just get the member :)
		$result = $this->db->get_where('members', array('id' => $member_id));
		
		// And return CSV to the user.
		$this->_return_csv($result);
		
	}
	
	private function _export($result, $filetype = 'csv') {
	
		/**
		 * Hey, look. CI actually got some built in functions for this!
		 */
		 
		$this->load->dbutil(); // Provides functions for result to CSV/XML
		$this->load->helper('download'); // Provides download-functionality.
		
		// Do we want the result in CSV or XML format?
		if($filetype == 'csv' || $filetype == 'xml') {
			
			$config = ($filetype == 'xml' ? array('element' => 'member', 'root' => 'members') : ',');
		
			// Generate csv or xml from result
			$export = $this->dbutil->{$filetype.'_from_result'}($result, $config); 
			
		// Or do we want JSON?
		} elseif($filetype == 'json') {
		
			// Generate json from result
			$export = json_encode($result->result());
		
		// Or do we want PDF?
		} elseif($filetype == 'pdf') {
		
			// Include mPDF
			require_once(APPPATH."third_party/mPDF/mpdf.php");
		
			// Load table lib included in CI.
			$this->load->library('table');
			
			// Set template
			$template = array ('table_open'  => '<table class="table table-bordered">');
			$this->table->set_template($template); 
			
			// Fenerate a HTML-table from result
			$html = $this->table->generate($result);
			$html = str_replace('thead>', 'tbody>', $html);
			
			// Secondly, make it into a PDF.
			$mpdf = new mPDF('c', 'A4-L');
			
			// Add PDF-table CSS
			$mpdf->WriteHTML(file_get_contents(FCPATH.'/assets/css/pdf-table.css'), 1);
			
			// Add table
			$mpdf->WriteHTML($html);
			
			// Export as everything else.
			$export = $mpdf->Output('', 'S');
			
		
		// FATAL ERROR, Unsupported filetype!
		} else {
			error('Unknown export format/submission, please try again.');
			redirect('members/export');
		}
		
		// Create filename
		$filename = 'makerspace-export_'.date('Y-m-d_H:i').'.'.$filetype;
		
		// Returns the actual export to the user.
		force_download($filename, $export);
		
	}
	
}