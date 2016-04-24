#!/usr/bin/env php7.0
<?php

function env($a, $b)
{
	return $b;
}

// Load configuration
$config = require(__DIR__ . "/../laravel/config/database.php");
$x = $config["connections"]["mysql"];

// Import new empty database
system("mysql --host={$x["host"]} --user={$x["username"]} --password={$x["password"]} < " . __DIR__ . "/../database/database.sql");

echo "Database imported\n";
