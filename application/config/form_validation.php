<?php

$config = array(
		// Login
        'auth/login' => array(
			array(
					'field' => 'email',
					'label' => 'e-mail address',
					'rules' => 'trim|required|min_length[6]|max_length[255]|valid_email'
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
					'rules' => 'trim|required|min_length[6]|max_length[255]|valid_email'
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
				'rules' => 'min_length[8]|max_length[255]'
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
				'rules' => 'trim|exact_length[11]'
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
				'rules' => 'trim|max_length[5]' // ToDo: |callback__normalize_zipcode
			),
			array(
				'field' => 'city',
				'label' => 'city',
				'rules' => 'trim|max_length[64]'
			),
			array(
				'field' => 'country',
				'label' => 'country',
				'rules' => 'trim|exact_length[2]|callback__validate_country'
			),
			array(
				'field' => 'civicregno',
				'label' => 'civic registration number',
				'rules' => 'trim|exact_length[13]' // ToDo: Validate?
			),
			array(
				'field' => 'phone',
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
		),
		
		// Edit member
		'members/edit' => array(
			array(
				'field' => 'email',
				'label' => 'e-mail address',
				'rules' => 'trim|required|min_length[6]|max_length[255]|valid_email' // ToDo: |callback__update_email
			),
			array(
				'field' => 'password',
				'label' => 'password',
				'rules' => 'min_length[8]|max_length[255]'
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
				'rules' => 'trim|exact_length[11]'
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
				'rules' => 'trim|exact_length[5]' // ToDo: |callback__normalize_zipcode
			),
			array(
				'field' => 'city',
				'label' => 'city',
				'rules' => 'trim|max_length[64]'
			),
			array(
				'field' => 'country',
				'label' => 'country',
				'rules' => 'trim|exact_length[2]|callback__validate_country'
			),
			array(
				'field' => 'phone',
				'label' => 'phone',
				'rules' => 'trim|max_length[64]' // ToDo: |callback__normalize_phone
			),
			array(
				'field' => 'civicregno',
				'label' => 'civic registration number',
				'rules' => 'trim|exact_length[13]' // ToDo: Validate?
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
		// Search members
		'members/search' => array(
			array(
				'field' => 'search',
				'label' => 'search',
				'rules' => 'trim|required|min_length[2]'
			),
		),
		// Add new group
		'groups/add_group' => array(
			array(
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'trim|required|min_length[2]|strtolower|is_unique[groups.name]'
			),
			array(
				'field' => 'description',
				'label' => 'Description',
				'rules' => 'trim|required|min_length[2]'
			),
		),
		// Export members
		'members/export' => array(
			array(
				'field' => 'fields',
				'label' => 'select fields',
				'rules' => 'required'
			),
			array(
				'field' => 'order_by',
				'label' => 'order by',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'sort',
				'label' => 'sort',
				'rules' => 'trim|required'
			),
		),

);