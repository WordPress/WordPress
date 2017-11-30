<?php
/**
 * Administration API: Default admin hooks
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.3.0
 */

// Bookmark hooks.
add_action( 'admin_page_access_denied', 'wp_link_manager_disabled_message' );

// Dashboard hooks.
add_action( 'activity_box_end', 'wp_dashboard_quota' );

// Media hooks.
add_action( 'attachment_submitbox_misc_actions', 'attachment_submitbox_metadata' );

add_action( 'media_upload_image', 'wp_media_upload_handler' );
add_action( 'media_upload_audio', 'wp_media_upload_handler' );
add_action( 'media_upload_video', 'wp_media_upload_handler' );
add_action( 'media_upload_file', 'wp_media_upload_handler' );

add_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );

add_action( 'post-html-upload-ui', 'media_upload_html_bypass' );

add_filter( 'async_upload_image', 'get_media_item', 10, 2 );
add_filter( 'async_upload_audio', 'get_media_item', 10, 2 );
add_filter( 'async_upload_video', 'get_media_item', 10, 2 );
add_filter( 'async_upload_file', 'get_media_item', 10, 2 );

add_filter( 'attachment_fields_to_save', 'image_attachment_fields_to_save', 10, 2 );

add_filter( 'media_upload_gallery', 'media_upload_gallery' );
add_filter( 'media_upload_library', 'media_upload_library' );

add_filter( 'media_upload_tabs', 'update_gallery_tab' );

// Misc hooks.
add_action( 'admin_init', 'wp_admin_headers' );
add_action( 'login_init', 'wp_admin_headers' );
add_action( 'admin_head', 'wp_admin_canonical_url' );
add_action( 'admin_head', 'wp_color_scheme_settings' );
add_action( 'admin_head', 'wp_site_icon' );
add_action( 'admin_head', '_ipad_meta' );

// Prerendering.
if ( ! is_customize_preview() ) {
	add_filter( 'admin_print_styles', 'wp_resource_hints', 1 );
}

add_action( 'admin_print_scripts-post.php', 'wp_page_reload_on_back_button_js' );
add_action( 'admin_print_scripts-post-new.php', 'wp_page_reload_on_back_button_js' );

add_action( 'update_option_home', 'update_home_siteurl', 10, 2 );
add_action( 'update_option_siteurl', 'update_home_siteurl', 10, 2 );
add_action( 'update_option_page_on_front', 'update_home_siteurl', 10, 2 );
add_action( 'update_option_admin_email', 'wp_site_admin_email_change_notification', 10, 3 );

add_action( 'add_option_new_admin_email', 'update_option_new_admin_email', 10, 2 );
add_action( 'update_option_new_admin_email', 'update_option_new_admin_email', 10, 2 );

add_filter( 'heartbeat_received', 'wp_check_locked_posts', 10, 3 );
add_filter( 'heartbeat_received', 'wp_refresh_post_lock', 10, 3 );
add_filter( 'wp_refresh_nonces', 'wp_refresh_post_nonces', 10, 3 );
add_filter( 'heartbeat_received', 'heartbeat_autosave', 500, 2 );

add_filter( 'heartbeat_settings', 'wp_heartbeat_set_suspension' );

// Nav Menu hooks.
add_action( 'admin_head-nav-menus.php', '_wp_delete_orphaned_draft_menu_items' );

// Plugin hooks.
add_filter( 'whitelist_options', 'option_update_filter' );

// Plugin Install hooks.
add_action( 'install_plugins_featured', 'install_dashboard' );
add_action( 'install_plugins_upload', 'install_plugins_upload' );
add_action( 'install_plugins_search', 'display_plugins_table' );
add_action( 'install_plugins_popular', 'display_plugins_table' );
add_action( 'install_plugins_recommended', 'display_plugins_table' );
add_action( 'install_plugins_new', 'display_plugins_table' );
add_action( 'install_plugins_beta', 'display_plugins_table' );
add_action( 'install_plugins_favorites', 'display_plugins_table' );
add_action( 'install_plugins_pre_plugin-information', 'install_plugin_information' );

// Template hooks.
add_action( 'admin_enqueue_scripts', array( 'WP_Internal_Pointers', 'enqueue_scripts' ) );
add_action( 'user_register', array( 'WP_Internal_Pointers', 'dismiss_pointers_for_new_users' ) );

// Theme hooks.
add_action( 'customize_controls_print_footer_scripts', 'customize_themes_print_templates' );

// Theme Install hooks.
// add_action('install_themes_dashboard', 'install_themes_dashboard');
// add_action('install_themes_upload', 'install_themes_upload', 10, 0);
// add_action('install_themes_search', 'display_themes');
// add_action('install_themes_featured', 'display_themes');
// add_action('install_themes_new', 'display_themes');
// add_action('install_themes_updated', 'display_themes');
add_action( 'install_themes_pre_theme-information', 'install_theme_information' );

// User hooks.
add_action( 'admin_init', 'default_password_nag_handler' );

add_action( 'admin_notices', 'default_password_nag' );
add_action( 'admin_notices', 'new_user_email_admin_notice' );

add_action( 'profile_update', 'default_password_nag_edit_user', 10, 2 );

add_action( 'personal_options_update', 'send_confirmation_on_profile_email' );

// Update hooks.
add_action( 'load-plugins.php', 'wp_plugin_update_rows', 20 ); // After wp_update_plugins() is called.
add_action( 'load-themes.php', 'wp_theme_update_rows', 20 ); // After wp_update_themes() is called.

add_action( 'admin_notices', 'update_nag', 3 );
add_action( 'admin_notices', 'maintenance_nag', 10 );

add_filter( 'update_footer', 'core_update_footer' );

// Update Core hooks.
add_action( '_core_updated_successfully', '_redirect_to_about_wordpress' );

// Upgrade hooks.
add_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
add_action( 'upgrader_process_complete', 'wp_version_check', 10, 0 );
add_action( 'upgrader_process_complete', 'wp_update_plugins', 10, 0 );
add_action( 'upgrader_process_complete', 'wp_update_themes', 10, 0 );
