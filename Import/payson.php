<?php

$raw = file_get_contents("Payson_Export__To_2016-03-20.csv");
$rows = explode("\n", $raw);
array_shift($rows);
array_pop($rows);

// Batch together
$batch = [];
foreach($rows as $row)
{
	$fields = explode(";", $row);
	$id = $fields[3];

	if($id == 13938915)
	{
		continue;
	}

	if($fields[1] == "Transfer")
	{
		$batch[$id][] = explode(";", $row);

	}
}

foreach($batch as $row)
{
	if(count($row) != 2)
	{
		continue;
	}

	if($row[0][6] < 0)
	{
		$fee  = $row[0];
		$order = $row[1];
	}
	else
	{
		$order = $row[0];
		$fee  = $row[1];
	}

	$tot   = str_replace(",", "", $order[6]);
	$fee1  = str_replace(",", "", $order[8]) * -1;
	$fee2  = str_replace(",", "", $fee[6]) * -1;
	$fee_tot = $fee1 + $fee2;
	$price = str_replace(",", "", $order[10]) - $fee2;
	$id = $order[3];
	$title = trim($order[14]);
	$date = substr($order[0], 0, 10);
	$email = $order[4];

	$data = [
		"title" => "Betalning Payson, {$title}, {$email}",
		"accounting_date" => $date,
		"external_id" => $id,
		"importer" => "payson-importer-v0.1",
		"transactions" => [
			[
				"title" => "",
				"account_number" => "1935",
				"amount" => $price,
			],
			[
				"title" => "",
				"account_number" => "3893",
				"amount" => -$tot,
			],
			[
				"title" => "Avgift Payson",
				"account_number" => "6575",
				"amount" => $fee_tot,
			]
		]
	];

	file_put_contents("./Payson/{$data['accounting_date']}_payson_{$data['external_id']}.json", json_encode($data, JSON_PRETTY_PRINT));
}
