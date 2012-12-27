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

);