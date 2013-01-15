<?php

class Memcache extends Memcached {
	
	public function __construct() {
	
		parent::__construct('internal.makerspace.se');
		
		// Do NOT spawn millions of connections...
		if (!count($this->getServerList())) {
			parent::addServer('127.0.0.1', 11211);
		}
		
	}
	
	/**
	 * Override the Memcached::set default expiration (0 = item never expires)
	 * Default is now 60 seconds, unless specified, which should good for most.
	 */
	public function set($key, $value = '', $expire = 60) {
		parent::set($key, $value, $expire);
	}

}
