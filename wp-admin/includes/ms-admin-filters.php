<?php
/**
 * Multisite Administration hooks
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.3.0
 */

// Media hooks.
add_filter( 'wp_handle_upload_prefilter', 'check_upload_size' );

// User hooks.
add_action( 'user_admin_notices', 'new_user_email_admin_notice' );
add_action( 'network_admin_notices', 'new_user_email_admin_notice' );

add_action( 'admin_page_access_denied', '_access_denied_splash', 99 );

// Site hooks.
add_action( 'wpmueditblogaction', 'upload_space_setting' );

// Network hooks.
add_action( 'update_site_option_admin_email', 'wp_network_admin_email_change_notification', 10, 4 );

// Post hooks.
add_filter( 'wp_insert_post_data', 'avoid_blog_page_permalink_collision', 10, 2 );

// Tools hooks.
add_filter( 'import_allow_create_users', 'check_import_new_users' );

// Notices hooks.
add_action( 'admin_notices', 'site_admin_notice' );
add_action( 'network_admin_notices', 'site_admin_notice' );

// Update hooks.
add_action( 'network_admin_notices', 'update_nag', 3 );
add_action( 'network_admin_notices', 'maintenance_nag', 10 );

// Network Admin hooks.
add_action( 'add_site_option_new_admin_email', 'update_network_option_new_admin_email', 10, 2 );
add_action( 'update_site_option_new_admin_email', 'update_network_option_new_admin_email', 10, 2 );
