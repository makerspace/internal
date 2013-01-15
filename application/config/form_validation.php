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
					'rules' => 'required|min_length[8]|max_length[255]' // ToDo: |callback__validate_password
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
					'rules' => 'required|min_length[8]|max_length[255]|matches[password2]' // ToDo: |callback__validate_password
			),
			array(
					'field' => 'password2',
					'label' => 'repeat new password',
					'rules' => 'required'
			),
        ),
		
		// Add new member
		'members/add' => array(
			array(
				'field' => 'email',
				'label' => 'e-mail address',
				'rules' => 'trim|required|min_length[6]|max_length[255]|valid_email|is_unique[members.email]'
			),
			array(
				'field' => 'password',
				'label' => 'password',
				'rules' => 'max_length[255]'
			),
			array(
				'field' => 'firstname',
				'label' => 'firstname',
				'rules' => 'trim|required|max_length[255]'
			),
			array(
				'field' => 'lastname',
				'label' => 'lastname',
				'rules' => 'trim|required|max_length[255]'
			),
			array(
				'field' => 'company',
				'label' => 'company',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'orgno',
				'label' => 'orgno',
				'rules' => 'trim|max_length[12]'
			),
			array(
				'field' => 'address',
				'label' => 'address',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'address2',
				'label' => 'address 2',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'zipcode',
				'label' => 'zipcode',
				'rules' => 'trim|max_length[8]' // ToDo: |callback__normalize_zipcode
			),
			array(
				'field' => 'city',
				'label' => 'city',
				'rules' => 'trim|max_length[64]'
			),
			array(
				'field' => 'country',
				'label' => 'country',
				'rules' => 'trim'
			),
			array(
				'field' => 'birthday',
				'label' => 'birthday',
				'rules' => 'trim|max_length[10]|valid_date'
			),
			array(
				'field' => 'mobile',
				'label' => 'mobile',
				'rules' => 'trim|max_length[64]' // ToDo: |callback__normalize_phone
			),
			array(
				'field' => 'alt. phone',
				'label' => 'phone',
				'rules' => 'trim|max_length[64]' // ToDo: |callback__normalize_phone
			),
			array(
				'field' => 'twitter',
				'label' => 'twitter',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'skype',
				'label' => 'skype',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'membership',
				'label' => 'membership due',
				'rules' => 'trim|max_length[10]|valid_date'
			),
		),
		
		// Edit member
		'members/edit' => array(
			array(
				'field' => 'email',
				'label' => 'e-mail address',
				'rules' => 'trim|required|min_length[6]|max_length[255]|valid_email'
			),
			array(
				'field' => 'password',
				'label' => 'password',
				'rules' => 'max_length[255]'
			),
			array(
				'field' => 'firstname',
				'label' => 'firstname',
				'rules' => 'trim|required|max_length[255]'
			),
			array(
				'field' => 'lastname',
				'label' => 'lastname',
				'rules' => 'trim|required|max_length[255]'
			),
			array(
				'field' => 'company',
				'label' => 'company',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'orgno',
				'label' => 'orgno',
				'rules' => 'trim|max_length[12]'
			),
			array(
				'field' => 'address',
				'label' => 'address',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'address2',
				'label' => 'address 2',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'zipcode',
				'label' => 'zipcode',
				'rules' => 'trim|max_length[8]' // ToDo: |callback__normalize_zipcode
			),
			array(
				'field' => 'city',
				'label' => 'city',
				'rules' => 'trim|max_length[64]'
			),
			array(
				'field' => 'country',
				'label' => 'country',
				'rules' => 'trim'
			),
			array(
				'field' => 'mobile',
				'label' => 'mobile',
				'rules' => 'trim|max_length[64]' // ToDo: |callback__normalize_phone
			),
			array(
				'field' => 'alt. phone',
				'label' => 'phone',
				'rules' => 'trim|max_length[64]' // ToDo: |callback__normalize_phone
			),
			array(
				'field' => 'birthday',
				'label' => 'birthday',
				'rules' => 'trim|max_length[10]|valid_date'
			),
			array(
				'field' => 'twitter',
				'label' => 'twitter',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'skype',
				'label' => 'skype',
				'rules' => 'trim|max_length[255]'
			),
			array(
				'field' => 'membership',
				'label' => 'membership due',
				'rules' => 'trim|max_length[10]|valid_date'
			),
		),
		
		// Create/edit newsletter
		'newsletter/validate' => array(
			array(
				'field' => 'subject',
				'label' => 'newsletter subject',
				'rules' => 'trim|required|min_length[10]|max_length[255]'
			),
			array(
				'field' => 'body',
				'label' => 'newsletter body',
				'rules' => 'trim|required|min_length[50]'
			),
		),
		
		// Admin - add config
		'admin/add_config' => array(
			array(
				'field' => 'key',
				'label' => 'config key',
				'rules' => 'trim|required|max_length[64]|is_unique[config.key]'
			),
			array(
				'field' => 'value',
				'label' => 'config value',
				'rules' => 'trim|required|max_length[2048]'
			),
			array(
				'field' => 'desc',
				'label' => 'config description',
				'rules' => 'trim|max_length[255]'
			),
		),
		
		// Admin - update config
		'admin/config' => array(
			array(
				'field' => 'key',
				'label' => 'config key',
				'rules' => 'trim|required|max_length[64]'
			),
			array(
				'field' => 'value',
				'label' => 'config value',
				'rules' => 'trim|required|max_length[2048]'
			),
		),

);