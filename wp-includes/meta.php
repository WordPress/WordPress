<?php
/**
 * Metadata API
 *
 * Functions for retrieving and manipulating metadata of various WordPress object types. Metadata
 * for an object is a represented by a simple key-value pair. Objects may contain multiple
 * metadata entries that share the same key and differ only in their value.
 *
 * @package WordPress
 * @subpackage Meta
 * @since 2.9.0
 */

require_once( ABSPATH . WPINC . '/meta-functions.php' );
require_once( ABSPATH . WPINC . '/class-wp-meta-query.php' );
