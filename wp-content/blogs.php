<?php
/**
 * Deprecated. Update your .htaccess file to use wp-includes/ms-files.php instead.
 *
 * @package WordPress
 * @subpackage Multisite
 */
  
define( 'SHORTINIT', true );
require_once( dirname( dirname( __FILE__) ) . '/wp-load.php' );
/* l10n is not loaded on SHORTINIT */  
_deprecated_file( basename( __FILE__ ), '3.0', null, sprintf( 'Change your rewrite rules to use <code>%1$s</code> instead of <code>%2$s</code>.', 'wp-includes/ms-files.php', 'wp-content/blogs.php' ) );
  
/** Load Multisite upload handler. */
require_once( ABSPATH . WPINC . '/ms-files.php' );
  
?>
