<?php
/**
 * WordPress API providing Unicode-relevant utilities.
 *
 * @since 6.6.0
 *
 * @package WordPress
 */

/**
 * Indicates if a given slug for a character set represents the UTF-8
 * text encoding. If not provided, examines the current blog's charset.
 *
 * A charset is considered to represent UTF-8 if it is a case-insensitive
 * match of "UTF-8" with or without the hyphen.
 *
 * Example:
 *
 *     true  === is_utf8_charset( 'UTF-8' );
 *     true  === is_utf8_charset( 'utf8' );
 *     false === is_utf8_charset( 'latin1' );
 *     false === is_utf8_charset( 'UTF 8' );
 *
 *     // Only strings match.
 *     false === is_utf8_charset( [ 'charset' => 'utf-8' ] );
 *
 *     // Without a given charset, it depends on the site option "blog_charset".
 *     $is_utf8 = is_utf8_charset();
 *
 * @since 6.6.0
 *
 * @param ?string $blog_charset Slug representing a text character encoding, or "charset".
 *                              E.g. "UTF-8", "Windows-1252", "ISO-8859-1", "SJIS".
 * @return bool Whether the slug represents the UTF-8 encoding.
 */
function is_utf8_charset( $blog_charset = null ) {
	$charset_to_examine = $blog_charset ?? get_option( 'blog_charset' );

	/*
	 * Only valid string values count: the absence of a charset
	 * does not imply any charset, let alone UTF-8.
	 */
	if ( ! is_string( $charset_to_examine ) ) {
		return false;
	}

	return (
		0 === strcasecmp( 'UTF-8', $charset_to_examine ) ||
		0 === strcasecmp( 'UTF8', $charset_to_examine )
	);
}
