<?php

namespace App\Libraries;

/**
 * Defines the base functionallity for a importer class used to import account history from an external source
 */
interface EconomyParser
{
	public function Import($data);
}
