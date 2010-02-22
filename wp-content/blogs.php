<?php
/**
 * Deprecated. Update your .htaccess file to use wp-includes/ms-files.php instead.
 *
 * @package WordPress
 * @subpackage Multisite
 */

define( 'SHORTINIT', true );
require_once( dirname( dirname( __FILE__) ) . '/wp-load.php' );

/** Load Multisite upload handler. */
require_once( ABSPATH . WPINC . '/ms-files.php' );

?>
