CREATE TABLE `config` (
  `key` varchar(64) NOT NULL COMMENT 'Config key-name',
  `value` varchar(2048) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL COMMENT 'Optional description of config',
  PRIMARY KEY (`key`)
);
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` varchar(64) DEFAULT NULL,
  `workgroup` tinyint(4) NOT NULL DEFAULT '0',
  `roles` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);
CREATE TABLE `logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `ip_address` varchar(64) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `valid` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`member_id`)
);
CREATE TABLE `member_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_groups` (`member_id`,`group_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `member_groups_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  CONSTRAINT `member_groups_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
);
CREATE TABLE `member_rfid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `tagid` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `added` int(11) NOT NULL COMMENT 'Timestamp',
  `last_modified` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tagid` (`tagid`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `member_rfid_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
);
CREATE TABLE `member_ssh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `pubkey` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `added` int(11) NOT NULL COMMENT 'Timestamp',
  `last_modified` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `member_ssh_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
);
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `reset_token` char(34) DEFAULT NULL,
  `reset_expire` int(11) DEFAULT NULL,
  `registered` int(11) NOT NULL,
  `last_updated` int(11) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `civicregno` char(13) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `orgno` varchar(12) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `zipcode` int(11) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `country` char(2) DEFAULT 'SE',
  `phone` varchar(64) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `amount` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `data` mediumtext,
  PRIMARY KEY (`id`)
);

INSERT INTO config VALUES('pop3_account', NULL, 'POP3 account used as Return-Path to track bounces');
INSERT INTO config VALUES('pop3_password', NULL, 'POP3 password for pop3_account');
INSERT INTO config VALUES('email_name', NULL, 'PHPMailer From Name');
INSERT INTO config VALUES('email_from', NULL, 'PHPMailer From Address');
INSERT INTO config VALUES('email_return_path', NULL, 'Used as Return-Path in PHPMailer');
INSERT INTO config VALUES('countries', NULL, 'Countries in JSON format');
INSERT INTO config VALUES('paypal_username', NULL, 'PayPal Live API Username');
INSERT INTO config VALUES('paypal_password', NULL, 'PayPal Live API Password');
INSERT INTO config VALUES('paypal_signature', NULL, 'PayPal Live API Signature');
INSERT INTO config VALUES('paypal_endpoint', NULL, 'PayPal Live Endpoint');
INSERT INTO config VALUES('paypal_dev_endpoint', NULL, 'Paypal Sandbox Endpoint');
INSERT INTO config VALUES('paypal_dev_username', NULL, 'PayPal Sandbox API Username');
INSERT INTO config VALUES('paypal_dev_password', NULL, 'PayPal Sandbox API Password');
INSERT INTO config VALUES('paypal_dev_signature', NULL, 'PayPal Sandbox API Signature');
INSERT INTO config VALUES('paypal_auth_url', NULL, 'PayPal Live Express Checkout Redirect URL');
INSERT INTO config VALUES('paypal_dev_auth_url', NULL, 'PayPal Sandbox Express Checkout Redirect URL');
INSERT INTO config VALUES('paypal_ipn', NULL, 'PayPal IPN URL');
INSERT INTO config VALUES('fortnox_dev_token', NULL, 'Fortnox Dev API Token');
INSERT INTO config VALUES('fortnox_dev_db', NULL, 'Fortnox Dev API Database');
