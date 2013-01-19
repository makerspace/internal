<?php

class Newsletter extends CI_Controller {

	public function __construct() {
		parent::__construct();
		
		admin_gatekeeper();
		
		// Always load Newsletter model
		$this->load->model('Newsletter_model');
	}
	
	public function index() {
	
		$head = array(
			'title' => 'Manage Newsletters',
		);
		
		// Get all newsletters
		$data = array('newsletters' => $this->Newsletter_model->get_all());
		
		$this->load->view('header', $head);
		$this->load->view('newsletter/manage_newsletters', $data);
		$this->load->view('footer');
	
	}
	
	/**
	 * Create a new newsletter.
	 */
	public function create() {
		
		// Run validation, when posted on this page only
		if($this->input->post('save')) {
		
			// Form validation of newsletter
			if ($this->form_validation->run('newsletter/validate')) {
				
				// Get newsletter data
				$subject = $_POST['subject'];
				$body = $_POST['body'];
				#alwaysempty ;) $recipient_groups = json_decode(base64_decode($this->input->post('groups')));
				
				// Get members ids based on recipient groups
				if(!empty($recipient_groups)) {
					// ToDo: Here!!
					
				} else {
					// If no groups was picked, send to all (get all member ids).
					$query = $this->db->select('id')->get('members');
					$recipients = $query->result_array();
				}
				
				// Save in db
				$result = $this->Newsletter_model->create($recipients, $subject, $body);
				
				// Succesfully saved, view it.
				if($result) {
					redirect('newsletter/view/'.$result);
				} else {
					error('Couldn\'t save the newsletter, please try again...');
				}
			}
		}
		
		$head = array(
			'title' => 'Create New Newsletter',
		);
		
		// Get recipient groups
		$data = array('groups' => $this->input->post('groups'));
		
		$this->load->view('header', $head);
		$this->load->view('newsletter/create_new', $data);
		$this->load->view('footer');
	
	}
	
	public function view($id = 0) {
		
		$head = array(
			'title' => 'Preview Newsletter',
		);
		
		// Get newsletter from id
		$newsletter = $this->Newsletter_model->get($id);
		
		// Failsafe
		if(!$newsletter) {
			error('The selected newsletter doesn\'t exist!');
			redirect('newsletter');
		}
		
		$this->load->view('header', $head);
		$this->load->view('newsletter/view', array('newsletter' => $newsletter));
		$this->load->view('footer');
	
	}
	
	public function edit($id = 0) {
		
		// Get newsletter from id
		$newsletter = $this->Newsletter_model->get($id);
		
		// Failsafe
		if(!$newsletter) {
			error('The selected newsletter doesn\'t exist!');
			redirect('newsletter');
		}
		
		// Form validation of newsletter
		if ($this->form_validation->run('newsletter/validate')) {
			
			// Get post
			$subject = $_POST['subject'];
			$body = $_POST['body'];
				
			// Save updated newsletter
			$result = $this->Newsletter_model->save($id, $subject, $body);
			if($result) {
				message('Successfully updated newsletter!');
				redirect('newsletter/view/'.$id);
			} else {
				error('Couldn\'t update newsletter, please try again.');
			}
			
		}
		
		$head = array(
			'title' => 'Edit Newsletter',
		);
		
		$this->load->view('header', $head);
		$this->load->view('newsletter/edit', array('newsletter' => $newsletter));
		$this->load->view('footer');
	
	}
	
	public function test_send($id = 0) {
	
		// Get newsletter from id
		$newsletter = $this->Newsletter_model->get($id);
		
		// Failsafe
		if(!$newsletter) {
			error('The selected newsletter doesn\'t exist!');
			redirect('newsletter');
		}
		
		// Send a test newsletter to the current signed in user.
		$result = $this->Newsletter_model->test_send($newsletter->subject, $newsletter->body);
		
		if($result) {
			message('A test newsletter has been successfully sent to your e-mail address.');
		} else {
			error('Couldn\'t send test newsletter! Please try again.');
		}
		
		redirect('newsletter/view/'.$id);
		
	}
	
	public function send($id = 0) {
	
		// Get newsletter from id
		$newsletter = $this->Newsletter_model->get($id);
		
		// Failsafe
		if(!$newsletter) {
			error('The selected newsletter doesn\'t exist!');
			redirect('newsletter');
		}
		
		error('Not active yet! Please try again later.');
		redirect('newsletter/view/'.$id);
		
	}
} 