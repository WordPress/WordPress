<?php
/**
 * Core Metadata API
 *
 * Functions for retrieving and manipulating metadata of various WordPress object types. Metadata
 * for an object is a represented by a simple key-value pair. Objects may contain multiple
 * metadata entries that share the same key and differ only in their value.
 *
 * @package WordPress
 * @subpackage Meta
 * @since 2.9.0
 */

/** Core metdata functionality */
require_once( ABSPATH . WPINC . '/meta-functions.php' );

/** WP_Meta_Query class */
require_once( ABSPATH . WPINC . '/class-wp-meta-query.php' );
