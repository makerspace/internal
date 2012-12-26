<?php

/**
 * Gravatar Helper
 * @author Jim Nelin
 */
function gravatar($email, $rating = 'pg', $size = 32, $default = 'retro') {
	
	// optional options
	$options = array();
	if ($rating) $options[] = "rating=$rating";
	if ($size) $options[] = "size=$size";
	if ($default) $options[] = "default=$default";
	
	// put together the URL and return it
	return 'https://secure.gravatar.com/avatar/' . md5(strtolower(trim($email))) . implode($options, '&');
}

