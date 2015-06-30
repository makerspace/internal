<?php
/**
 * E-mail model for internal.makerspace.se
 * @author Jim Nelin
 *
 * ToDo: 
 * - Store e-mails in db as config-key/value.
 * - Store config in db
 * - Handle bounces
 */

class Email_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	
		$this->load->library('PHPMailerLite');
	}

	public function send_forgot_password($to, $token, $recipient_name = '') {
	
		$subject = 'Password recovery for internal.makerspace.se';
		$template = 'Hello!

A password recovery reset has been sent from IP-address: %s.
If you did not ask for a password reset, you can safely ignore this email.

To reset your password, please visit this page:
https://internal.makerspace.se/auth/reset/%s

--
Regards, E-mail Robot
Stockholm Makerspace';

		// New email
		$email = $this->new_email($to, $recipient_name);
		$body = sprintf($template, ip_address(), $token);
		
		// Set subject
		$email->Subject = $subject;
		
		// Set body.
		$email->Body = $body;
		
		return $email->Send();

	}
	
	public function new_email($to, $recipient_name = '') {
			
			$mail = new PHPMailerLite();
			
			$mail->SetFrom($this->dbconfig->email_from, $this->dbconfig->email_name);
			$mail->AddAddress($to, $recipient_name);
			
			// VERP
			$mail->Sender = sprintf($this->dbconfig->email_return_path, str_replace('@', '=', $to));
			
			return $mail;
	}

}
