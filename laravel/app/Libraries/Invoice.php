<?php

namespace App\Libraries;

// OdfPhp library depends on PclZip, which is not updated to use __construct() and __destruct() methods in classes.
// We need to turn off the deprecated warnings in PHP7.
error_reporting(E_ALL ^ E_DEPRECATED);

/**
 * This class extends the Odf class and adds a new method.
 */
class InvoiceTemplate extends \Odf
{
	/**
	 * This method checks if a placeholder exists in the *.odt template.
	 * If we try to use setVars() on a non existing placeholder, we will get an error.
	 * As placeholders are optional we need to check for their existance before trying to set them.
	 */
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

/**
 *
 */
class Invoice
{
	protected $template;
	protected $odf;
	protected $output;
	protected $tempfile;

	/**
	 * Constructor
	 *
	 * Loads the template and creates a temporary file name for output
	 */
	public function __construct($template)
	{
		// Load template
		if(!file_exists($template))
		{
			// TODO: Throw error
		}
		$this->template = $template;
		$this->odf = new InvoiceTemplate($this->template);

		// Temporary file name for output file
		$this->tempfile = tempnam(null, md5(uniqid()));
	}

	/**
	 * Destructor
	 *
	 * Removes the temp file, if any
	 */
	public function __destruct()
	{
		if($this->tempfile !== null && file_exists($this->tempfile))
		{
			unlink($this->tempfile);
		}
	}

	/**
	 * Calculate som metadata needed when generating the invoice, like vat and total sum.
	 */
	public function CalculateMetadata($invoice)
	{
		$invoice['priceNet']       = 0;
		$invoice['priceVat']       = 0;
		$invoice['priceGross']     = 0;

		$invoice['date_invoice'] = date("Y-m-d", strtotime($invoice["date_invoice"]));
		$invoice['date_expiry']  = date("Y-m-d", strtotime($invoice["date_invoice"] . '+' . $invoice["conditions"] . "days"));

		foreach($invoice["posts"] as &$article)
		{
			$vat = $article['vat'] / 100;

			// öre -> kr
			$article['price'] /= 100;

			$article['priceNet']      = $article['price'];
			$article['priceVat']      = $article['price'] * $vat;
			$article['priceGross']    = $article['price'] * (1 + $vat);
			$article['priceSumNet']   = $article['price'] * $article['amount'];
			$article['priceSumVat']   = $article['price'] * $article['amount'] * $vat;
			$article['priceSumGross'] = $article['price'] * $article['amount'] * (1 + $vat);

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

	/**
	 * Generate a *.odt invoice from the *.odt template
	 */
	public function Generate($invoice)
	{
		// Replace all variables in article table
		$segment = $this->odf->setSegment('articles');
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

				if($this->odf->varExists($key))
				{
//					$value = utf8_encode($value);
					$segment->setVars($key, $value, true, 'UTF-8');
				}
			}
			$segment->merge();
		}
		$this->odf->mergeSegment($segment);

		// Format currencies
		$invoice['priceNet']   = number_format($invoice['priceNet'],   0, 0, ' ');
		$invoice['priceVat']   = number_format($invoice['priceVat'],   0, 0, ' ');
		$invoice['priceGross'] = number_format($invoice['priceGross'], 0, 0, ' ');

		// Replace all variables in document
		foreach($invoice as $key => $value)
		{
			if($this->odf->varExists($key))
			{
//				echo mb_detect_encoding($value, "ISO-8859-1, UTF-8, ASCII")."\n";
//				$value = utf8_encode($value);
				$this->odf->setVars($key, $value, true, 'UTF-8');
			}
		}

		// The *.odt will be saved in a temp file, which is removed in the desctructor.
		// For permanent storage the method Save() needs to be called
		$this->odf->saveToDisk($this->tempfile);
		$this->output = $this->tempfile;
	}

	/**
	 * Save the generated document to a file
	 */
	public function Save($file)
	{
		rename($this->tempfile, $file);
		$this->tempfile = null;
		$this->output   = $file;
	}

	/**
	 * Send file to user
	 */
	public function Send()
	{
		header("Content-type: application/vnd.oasis.opendocument.text");
		echo file_get_contents($this->output);
	}

	/**
	 * Export the generated *.odt to a *.pdf
	 */
	public function ExportPdf()
	{
		// TODO: Should be executed in a Docker container.
		system("libreoffice --headless --invisible --convert-to pdf /tmp/invoice.odt --outdir /tmp");
	}

	/**
	 * TODO: Check if there is a ODT already, generate if not
	 * TODO: Check if there is an PDF already, export if not
	 * TODO: Send PDF
	 */
	public function SendPdf()
	{
		header('Content-type: application/pdf');
		echo file_get_contents($this->output);
	}
}
