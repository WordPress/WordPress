<?php

/**
 * Adds the x-wav mime type to the list of mime types.
 * This is necessary for Firefox as it uses the identifier for uploaded .wav files.
 * Core backport should add the following to the default mime_types filters in
 * `wp_get_mime_types()` in wp-includes/functions.php:
 *
 * `'wav|x-wav' => 'audio/wav'`
 *
 * @since 6.8.0
 *
 * @param string[] $mime_types Mime types.
 * @return string[] Mime types keyed by the file extension regex corresponding to those types.
*/
function gutenberg_get_mime_types_6_8( $mime_types ) {
	/*
	 * Only add support if there is existing support for 'wav'.
	 * Some plugins may have deliberately disabled it.
	*/
	if ( ! isset( $mime_types['wav'] ) && ! isset( $mime_types['wav|x-wav'] ) ) {
		return $mime_types;
	}
	/*
	 * Also, given that other themes or plugins may have already
	 * tried to add x-wav type support, only
	 * add the mime type if it doesn't already exist
	 * to avoid overriding any customizations.
	 */
	if ( ! isset( $mime_types['x-wav'] ) && ! isset( $mime_types['wav|x-wav'] ) ) {
		$mime_types['x-wav'] = 'audio/wav';
	}
	return $mime_types;
}
add_filter( 'mime_types', 'gutenberg_get_mime_types_6_8', 99 );
