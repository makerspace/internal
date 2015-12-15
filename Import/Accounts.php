<?php

require("../laravel/app/Libraries/CurlBrowser.php");
use App\Libraries\CurlBrowser;

foreach(glob("./data/accounts/*.json") as $file)
{
	echo "Importing {$file}\n";
	$data = json_decode(file_get_contents($file));

	$x = new CurlBrowser;
	$x->Post("http://internal.dev/api/v2/economy/2015/account", $data, true);

	if($x->StatusCode() == 200)
	{
//		print_r($x->GetJson());
	}
	else
	{
		echo "Error\n";
	}
}
