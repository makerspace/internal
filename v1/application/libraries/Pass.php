<?php

class Pass {
	
	/**
	 * Wrapper function for the PBKDF2 hashing method.
	 */
	public function hash($password, $salt = '', $i = 65536) {
		
		// Generate new salt.
		if(empty($salt)) $salt = $this->_gen_salt();
		
		// Get derived key
		$dk = $this->pbkdf2($password, $salt, $i);
		
		// Build a string similar to crypt()-hashes
		// Format: $version$iterations$salt_hex$derived_key
		
		$str = '$v1$'; // Version 1
		$str .= $i.'$'; // Iterations
		$str .= bin2hex($salt).'$'; // Salt
		$str .= bin2hex($dk); // Derived key
		
		// Return hash
		return $str;
		
	}
	
	/**
	 * Verify Passwords against Hash
	 *
	 * @param string password The input password
	 * @param string hash Hash from database to match against
	 * @return bool Valid or not.
	 */
	public function verify($password, $hash) {
		// Get settings
		list(,$ver,$iterations, $salt) = explode('$', $hash);
		
		// Failsafe against non-v1 hashes.
		if($ver != 'v1') return false;
		
		// Salt is in hex, so decode it...
		$salt = pack("H*", $salt);
		
		$test_hash = $this->hash($password, $salt, $iterations);
		
		// Compare hashes, return true if valid
		if($test_hash == $hash) return true;
	
		return false;
	}
	
	/** PBKDF2 Implementation (described in RFC 2898)
	 *
	 * @param string p password
	 * @param string s salt
	 * @param int c iteration count (use 5000 or more)
	 * @param int kl derived key length (default: 64 bit)
	 * @param string a hash algorithm (default: sha256)
	 *
	 * @return string derived key
	*/
	private function pbkdf2($p, $s, $c, $kl = 64, $a = 'sha256') {
		$hl = strlen(hash($a, null, true)); // Hash length
		$kb = ceil($kl / $hl); // Key blocks to compute
		$dk = ''; // Derived key
		
		// Create key
		for ( $block = 1; $block <= $kb; $block ++ ) {
			// Initial hash for this block
			$ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
			// Perform block iterations
			for ( $i = 1; $i < $c; $i ++ )
	 
				// XOR each iterate
				$ib ^= ($b = hash_hmac($a, $b, $p, true));
	 
			$dk .= $ib; // Append iterated block
		}
		
		// Return derived key of correct length
		return substr($dk, 0, $kl);
	}
	
	/**
	 * Generate salt with mcrypt
	 *
	 * @param int len Length of salt to return
	 * @return string Returns salt in binary form
	 */
	private function _gen_salt($len = 8) {
		return mcrypt_create_iv($len, MCRYPT_DEV_URANDOM);
	}
}
