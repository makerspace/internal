<?php

require("../laravel/app/Libraries/CurlBrowser.php");
use App\Libraries\CurlBrowser;

foreach(glob("./data/invoices/*/") as $directory)
{
	list(,,,$period) = explode("/", $directory);
	echo "Accounting period: $period\n";
	foreach(glob("./data/invoices/{$period}/*.json") as $file)
	{
		echo "Importing {$file}\n";
		$data = json_decode(file_get_contents($file));

		$x = new CurlBrowser;
		$x->Post("http://internal.dev/api/v2/economy/{$period}/invoice", $data, true);

		if($x->StatusCode() == 200)
		{
			print_r($x->GetJson());
		}
		else
		{
			echo "Error\n";
		}
	}
}
