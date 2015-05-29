<?php
/**
 * Multisite Administration hooks
 *
 * @package WordPress
 *
 * @since 4.3.0
 */

add_filter( 'wp_handle_upload_prefilter', 'check_upload_size' );

add_action( 'update_option_new_admin_email', 'update_option_new_admin_email', 10, 2 );
add_action( 'add_option_new_admin_email', 'update_option_new_admin_email', 10, 2 );

add_action( 'personal_options_update', 'send_confirmation_on_profile_email' );

add_action( 'admin_notices', 'new_user_email_admin_notice' );

add_action( 'wpmueditblogaction', 'upload_space_setting' );

add_filter( 'get_term', 'sync_category_tag_slugs', 10, 2 );

add_action( 'admin_page_access_denied', '_access_denied_splash', 99 );

add_filter( 'import_allow_create_users', 'check_import_new_users' );

add_action( 'admin_notices', 'site_admin_notice' );
add_action( 'network_admin_notices', 'site_admin_notice' );

add_filter( 'wp_insert_post_data', 'avoid_blog_page_permalink_collision', 10, 2 );
