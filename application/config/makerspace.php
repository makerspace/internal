<?php

// Group access levels
$config['acl'] = array('login', 'labaccess', 'feepaid', 'boardmember', 'founder', 'admin');

// E-mail config
$config['email_name'] = 'Stockholm Makerspace';
$config['email_from'] = 'info@makerspace.se';
$config['email_return_path'] = 'robot+%s@makerspace.se';

// E-mail POP3 account (For monitoring bounces)
$config['pop3_account'] = 'robot@makerspace.se';
$config['pop3_password'] = '';