<?php

class Memcache extends Memcached {
	
	public function __construct() {
	
		parent::__construct('internal.makerspace.se');
		
		// Do NOT spawn millions of connections...
		if (!count($this->getServerList())) {
			parent::addServer('127.0.0.1', 11211);
		}
		
	}

}
