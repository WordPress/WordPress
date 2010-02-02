<?php
/**
 * Load mulitsite uploaded media
 *
 * @since 3.0.0
 *
 * @package WordPress
 * @subpackage Multisite
 */

define( 'SHORTINIT', true );
require_once( dirname( dirname( __FILE__) ) . '/wp-load.php' ); // absolute includes are faster
require_once( WP_CONTENT_DIR . '/blogs.php' );
exit();
