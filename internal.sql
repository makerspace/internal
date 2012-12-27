-- MySQL dump 10.14  Distrib 5.5.28a-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: internal
-- ------------------------------------------------------
-- Server version	5.5.28a-MariaDB-a1~squeeze-log

--
-- Table structure for table `acl`
--

CREATE TABLE `acl` (
  `user_id` int(11) NOT NULL COMMENT 'Member ID',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Ability to login to internal.makerspace.se',
  `labaccess` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Access to the lab',
  `feepaid` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Paid annual fee',
  `boardmember` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Member of the board',
  `founder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Founder of Stockholm Makerspace',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Admin access in internal.makerspace.se',
  PRIMARY KEY (`user_id`)
);

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(64) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
);

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'Member ID',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_token` char(34) DEFAULT NULL,
  `registered` int(11) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `zipcode` int(11) DEFAULT NULL,
  `country` char(2) DEFAULT 'SE',
  `phone` varchar(64) DEFAULT NULL,
  `mobile` varchar(64) DEFAULT NULL,
  `membership` date DEFAULT NULL COMMENT 'Membership Due Date',
  `comment` varchar(255) DEFAULT NULL COMMENT 'Comment on the member (Notes etc)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);


-- Dump completed on 2012-12-27 17:17:54
