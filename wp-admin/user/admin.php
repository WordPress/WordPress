<?php
/**
 * WordPress User Administration Bootstrap
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

define('WP_USER_ADMIN', TRUE);

require_once( dirname(dirname(__FILE__)) . '/admin.php');

if ( ! is_multisite() ) {
	wp_redirect( admin_url() );
	exit;
}

if ( ( $current_blog->domain != $current_site->domain ) || ( $current_blog->path != $current_site->path ) ) {
	wp_redirect( user_admin_url() );
	exit;
}
?>
