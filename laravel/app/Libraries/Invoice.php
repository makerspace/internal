<?php

namespace App\Libraries;

require("/vagrant/laravel/invoice/odtphp/library/Odf.php"); // TODO

class InvoiceTemplate extends \Odf
{
	public function varExists($key)
	{
		if(strpos($this->contentXml, $this->config['DELIMITER_LEFT'] . $key . $this->config['DELIMITER_RIGHT']) === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}

class Invoice
{
	function CalculateMetadata($invoice)
	{
		$invoice['priceNet']       = 0;
		$invoice['priceVat']       = 0;
		$invoice['priceGross']     = 0;

		foreach($invoice["posts"] as &$article)
		{
			$vat = $article['vat'] / 100;
			$article['priceNet']      = $article['price'];
			$article['priceVat']      = $article['price'] * $vat;
			$article['priceGross']    = $article['price'] * (1+$vat);
			$article['priceSumNet']   = $article['price'] * $article['amount'];
			$article['priceSumVat']   = $article['price'] * $article['amount'] * $vat;
			$article['priceSumGross'] = $article['price'] * $article['amount'] * (1+$vat);

			$invoice['priceNet']   += $article['priceSumNet'];
			$invoice['priceVat']   += $article['priceSumVat'];
			$invoice['priceGross'] += $article['priceSumGross'];

			$article['priceNet']      = number_format($article['priceNet'],      0, '.', ' ');
			$article['priceVat']      = number_format($article['priceVat'],      0, '.', ' ');
			$article['priceGross']    = number_format($article['priceGross'],    0, '.', ' ');
			$article['priceSumNet']   = number_format($article['priceSumNet'],   0, '.', ' ');
			$article['priceSumVat']   = number_format($article['priceSumVat'],   0, '.', ' ');
			$article['priceSumGross'] = number_format($article['priceSumGross'], 0, '.', ' ');
		}

		return $invoice;
	}

	function Generate($invoice)
	{
		$invoice['date_invoice'] = date("Y-m-d", strtotime($invoice["date_invoice"]));
		$invoice['date_expiry']  = date("Y-m-d", strtotime($invoice["date_invoice"] . '+' . $invoice["conditions"] . "days"));

		// TODO: Hardcoded path
//		$odf = new Odf(dirname(__FILE__)."/invoice.odt"); //, array('PATH_TO_TMP' => '/tmp/')
		$odf = new InvoiceTemplate("/vagrant/laravel/invoice/invoice.odt");

		// Replace all variables in article table
		$segment = $odf->setSegment('articles');
		foreach($invoice["posts"] as $article)
		{
			// TODO: Fulhack för att ignorera variabler som inte finns pga att odf inte kan detektera korrekt
			unset($article['priceVat']);
			unset($article['priceGross']);
			unset($article['priceSumVat']);
			unset($article['priceSumGross']);

			foreach($article as $key => $value)
			{
				if($article['type'] == 'separator')
				{
					$value = '';
				}
				else if($article['type'] == 'title' && $key != 'title')
				{
					$value = '';
				}

				if($odf->varExists($key))
				{
//					$value = utf8_encode($value);
					$segment->setVars($key, $value, true, 'UTF-8');
				}
			}
			$segment->merge();
		}
		$odf->mergeSegment($segment);

		// Format currencies
		$invoice['priceNet']   = number_format($invoice['priceNet'],   0, 0, ' ');
		$invoice['priceVat']   = number_format($invoice['priceVat'],   0, 0, ' ');
		$invoice['priceGross'] = number_format($invoice['priceGross'], 0, 0, ' ');

		// Replace all variables in document
		foreach($invoice as $key => $value)
		{
			if($odf->varExists($key))
			{
//				echo mb_detect_encoding($value, "ISO-8859-1, UTF-8, ASCII")."\n";
//				$value = utf8_encode($value);
				$odf->setVars($key, $value, true, 'UTF-8');
			}
		}

		// Save *.odt to disk
		$odf->saveToDisk("/vagrant/temp/invoice.odt");

		// Send file to user
//		header('Content-type: application/pdf');
//		echo file_get_contents("/tmp/invoice.pdf");

		// Remove temp files
		//unlink("{$file}");
		//unlink("{$file}.pdf");
	}

	function ExportPdf()
	{
		system("libreoffice --headless --invisible --convert-to pdf /tmp/invoice.odt --outdir /tmp");
	}
}
