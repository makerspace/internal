#!/usr/bin/env php7.0
<?php

require(__DIR__ . "/../laravel/app/Libraries/CurlBrowser.php");
use App\Libraries\CurlBrowser;

if(empty($argv[1]))
{
	echo "Usage: {$argv[0]} [function]\n";
	exit(0);
}

$curl = new CurlBrowser;
$data = $curl->Get("http://internal.dev/api/v2/{$argv[1]}");

if($curl->StatusCode() != 200)
{
	echo "Error:\n";
	echo $curl->html;
	echo "\n";
	die();
}

// Prettify JSON
$json = json_encode(json_decode($curl->html), JSON_PRETTY_PRINT);
echo "$json\n";
