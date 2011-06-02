<?php
/**
 * WordPress implementation for PHP functions either missing from older PHP versions or not included by default.
 *
 * @package PHP
 * @access private
 */

// If gettext isn't available
if ( !function_exists('_') ) {
	function _($string) {
		return $string;
	}
}

if ( !function_exists('mb_substr') ):
	function mb_substr( $str, $start, $length=null, $encoding=null ) {
		return _mb_substr($str, $start, $length, $encoding);
	}
endif;

function _mb_substr( $str, $start, $length=null, $encoding=null ) {
	// the solution below, works only for utf-8, so in case of a different
	// charset, just use built-in substr
	$charset = get_option( 'blog_charset' );
	if ( !in_array( $charset, array('utf8', 'utf-8', 'UTF8', 'UTF-8') ) ) {
		return is_null( $length )? substr( $str, $start ) : substr( $str, $start, $length);
	}
	// use the regex unicode support to separate the UTF-8 characters into an array
	preg_match_all( '/./us', $str, $match );
	$chars = is_null( $length )? array_slice( $match[0], $start ) : array_slice( $match[0], $start, $length );
	return implode( '', $chars );
}

if ( !function_exists('hash_hmac') ):
function hash_hmac($algo, $data, $key, $raw_output = false) {
	return _hash_hmac($algo, $data, $key, $raw_output);
}
endif;

function _hash_hmac($algo, $data, $key, $raw_output = false) {
	$packs = array('md5' => 'H32', 'sha1' => 'H40');

	if ( !isset($packs[$algo]) )
		return false;

	$pack = $packs[$algo];

	if (strlen($key) > 64)
		$key = pack($pack, $algo($key));

	$key = str_pad($key, 64, chr(0));

	$ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
	$opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));

	$hmac = $algo($opad . pack($pack, $algo($ipad . $data)));

	if ( $raw_output )
		return pack( $pack, $hmac );
	return $hmac;
}
