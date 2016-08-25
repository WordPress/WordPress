<?php
/**
 * Feed API
 *
 * @package WordPress
 * @subpackage Feed
 */
if ( ! class_exists( 'SimplePie', false ) ) {
	require_once( ABSPATH . WPINC . '/class-simplepie.php' );
}

require_once( ABSPATH . WPINC . '/class-wp-feed-cache.php' );
require_once( ABSPATH . WPINC . '/class-wp-feed-cache-transient.php' );
require_once( ABSPATH . WPINC . '/class-wp-simplepie-file.php' );
require_once( ABSPATH . WPINC . '/class-wp-simplepie-sanitize-kses.php' );