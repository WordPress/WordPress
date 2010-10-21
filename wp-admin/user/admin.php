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

if ( ! is_main_site() )
	wp_redirect( user_admin_url() );

?>
