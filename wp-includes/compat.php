<?php
/**
 * WordPress implementation for PHP functions missing from older PHP versions.
 *
 * @package PHP
 * @access private
 */

// Added in PHP 5.0

if (!function_exists('http_build_query')) {
	function http_build_query($data, $prefix=null, $sep=null) {
		return _http_build_query($data, $prefix, $sep);
	}
}

// from php.net (modified by Mark Jaquith to behave like the native PHP5 function)
function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode)
			$k = urlencode($k);
		if ( is_int($k) && $prefix != null )
			$k = $prefix.$k;
		if ( !empty($key) )
			$k = $key . '%5B' . $k . '%5D';
		if ( $v === NULL )
			continue;
		elseif ( $v === FALSE )
			$v = '0';

		if ( is_array($v) || is_object($v) )
			array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
		elseif ( $urlencode )
			array_push($ret, $k.'='.urlencode($v));
		else
			array_push($ret, $k.'='.$v);
	}

	if ( NULL === $sep )
		$sep = ini_get('arg_separator.output');

	return implode($sep, $ret);
}

if ( !function_exists('_') ) {
	function _($string) {
		return $string;
	}
}

if (!function_exists('stripos')) {
	function stripos($haystack, $needle, $offset = 0) {
		return strpos(strtolower($haystack), strtolower($needle), $offset);
	}
}

if ( ! function_exists('hash_hmac') ):
function hash_hmac($algo, $data, $key, $raw_output = false) {
	$packs = array('md5' => 'H32', 'sha1' => 'H40');

	if ( !isset($packs[$algo]) )
		return false;

	$pack = $packs[$algo];

	if (strlen($key) > 64)
		$key = pack($pack, $algo($key));
	else if (strlen($key) < 64)
		$key = str_pad($key, 64, chr(0));

	$ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
	$opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));

	return $algo($opad . pack($pack, $algo($ipad . $data)));
}
endif;

if ( ! function_exists('mb_substr') ):
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

if ( !function_exists( 'htmlspecialchars_decode' ) ) {
	// Added in PHP 5.1.0
	// Error checks from PEAR::PHP_Compat
	function htmlspecialchars_decode( $string, $quote_style = ENT_COMPAT )
	{
		if ( !is_scalar( $string ) ) {
			trigger_error( 'htmlspecialchars_decode() expects parameter 1 to be string, ' . gettype( $string ) . ' given', E_USER_WARNING );
			return;
		}

		if ( !is_int( $quote_style ) && $quote_style !== null ) {
			trigger_error( 'htmlspecialchars_decode() expects parameter 2 to be integer, ' . gettype( $quote_style ) . ' given', E_USER_WARNING );
			return;
		}

		return wp_specialchars_decode( $string, $quote_style );
	}
}

?>
