<?php

$config = array(
		// Login
        'auth/login' => array(
                array(
                        'field' => 'email',
                        'label' => 'e-mail address',
                        'rules' => 'trim|required|max_length[255]|valid_email'
                ),
                array(
                        'field' => 'password',
                        'label' => 'password',
                        'rules' => 'required|min_length[8]|max_length[255]' // |callback___password_validation
                ),
        ),
		
		// Forgot password
        'auth/forgot' => array(
                array(
                        'field' => 'email',
                        'label' => 'e-mail address',
                        'rules' => 'trim|required|max_length[255]|valid_email'
                ),
        ),
		
		// Reset password
        'auth/reset' => array(
                array(
                        'field' => 'password',
                        'label' => 'password',
                        'rules' => 'trim|required|min_length[8]|matches[password2]'
                ),
                array(
                        'field' => 'password2',
                        'label' => 'repeat new password',
                        'rules' => 'trim|required'
                ),
        ),
		
		// Add new member
		'members/add' => array(
			array(
				'field' => 'email',
				'label' => 'e-mail address',
				'rules' => 'trim|required|max_length[255]|valid_email|is_unique[users.email]'
			),
		),

);