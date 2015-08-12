<?php
/**
 * Sets up the default filters and actions for most
 * of the WordPress hooks.
 *
 * If you need to remove a default hook, this file will
 * give you the priority for which to use to remove the
 * hook.
 *
 * Not all of the default hooks are found in default-filters.php
 *
 * @package WordPress
 */

// Strip, trim, kses, special chars for string saves
foreach ( array( 'pre_term_name', 'pre_comment_author_name', 'pre_link_name', 'pre_link_target', 'pre_link_rel', 'pre_user_display_name', 'pre_user_first_name', 'pre_user_last_name', 'pre_user_nickname' ) as $filter ) {
	add_filter( $filter, 'sanitize_text_field'  );
	add_filter( $filter, 'wp_filter_kses'       );
	add_filter( $filter, '_wp_specialchars', 30 );
}

// Strip, kses, special chars for string display
foreach ( array( 'term_name', 'comment_author_name', 'link_name', 'link_target', 'link_rel', 'user_display_name', 'user_first_name', 'user_last_name', 'user_nickname' ) as $filter ) {
	if ( is_admin() ) {
		// These are expensive. Run only on admin pages for defense in depth.
		add_filter( $filter, 'sanitize_text_field'  );
		add_filter( $filter, 'wp_kses_data'       );
	}
	add_filter( $filter, '_wp_specialchars', 30 );
}

// Kses only for textarea saves
foreach ( array( 'pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description' ) as $filter ) {
	add_filter( $filter, 'wp_filter_kses' );
}

// Kses only for textarea admin displays
if ( is_admin() ) {
	foreach ( array( 'term_description', 'link_description', 'link_notes', 'user_description' ) as $filter ) {
		add_filter( $filter, 'wp_kses_data' );
	}
	add_filter( 'comment_text', 'wp_kses_post' );
}

// Email saves
foreach ( array( 'pre_comment_author_email', 'pre_user_email' ) as $filter ) {
	add_filter( $filter, 'trim'           );
	add_filter( $filter, 'sanitize_email' );
	add_filter( $filter, 'wp_filter_kses' );
}

// Email admin display
foreach ( array( 'comment_author_email', 'user_email' ) as $filter ) {
	add_filter( $filter, 'sanitize_email' );
	if ( is_admin() )
		add_filter( $filter, 'wp_kses_data' );
}

// Save URL
foreach ( array( 'pre_comment_author_url', 'pre_user_url', 'pre_link_url', 'pre_link_image',
	'pre_link_rss', 'pre_post_guid' ) as $filter ) {
	add_filter( $filter, 'wp_strip_all_tags' );
	add_filter( $filter, 'esc_url_raw'       );
	add_filter( $filter, 'wp_filter_kses'    );
}

// Display URL
foreach ( array( 'user_url', 'link_url', 'link_image', 'link_rss', 'comment_url', 'post_guid' ) as $filter ) {
	if ( is_admin() )
		add_filter( $filter, 'wp_strip_all_tags' );
	add_filter( $filter, 'esc_url'           );
	if ( is_admin() )
		add_filter( $filter, 'wp_kses_data'    );
}

// Slugs
add_filter( 'pre_term_slug', 'sanitize_title' );

// Keys
foreach ( array( 'pre_post_type', 'pre_post_status', 'pre_post_comment_status', 'pre_post_ping_status' ) as $filter ) {
	add_filter( $filter, 'sanitize_key' );
}

// Mime types
add_filter( 'pre_post_mime_type', 'sanitize_mime_type' );
add_filter( 'post_mime_type', 'sanitize_mime_type' );

// Places to balance tags on input
foreach ( array( 'content_save_pre', 'excerpt_save_pre', 'comment_save_pre', 'pre_comment_content' ) as $filter ) {
	add_filter( $filter, 'convert_invalid_entities' );
	add_filter( $filter, 'balanceTags', 50 );
}

// Format strings for display.
foreach ( array( 'comment_author', 'term_name', 'link_name', 'link_description', 'link_notes', 'bloginfo', 'wp_title', 'widget_title' ) as $filter ) {
	add_filter( $filter, 'wptexturize'   );
	add_filter( $filter, 'convert_chars' );
	add_filter( $filter, 'esc_html'      );
}

// Format WordPress
foreach ( array( 'the_content', 'the_title', 'wp_title' ) as $filter )
	add_filter( $filter, 'capital_P_dangit', 11 );
add_filter( 'comment_text', 'capital_P_dangit', 31 );

// Format titles
foreach ( array( 'single_post_title', 'single_cat_title', 'single_tag_title', 'single_month_title', 'nav_menu_attr_title', 'nav_menu_description' ) as $filter ) {
	add_filter( $filter, 'wptexturize' );
	add_filter( $filter, 'strip_tags'  );
}

// Format text area for display.
foreach ( array( 'term_description' ) as $filter ) {
	add_filter( $filter, 'wptexturize'      );
	add_filter( $filter, 'convert_chars'    );
	add_filter( $filter, 'wpautop'          );
	add_filter( $filter, 'shortcode_unautop');
}

// Format for RSS
add_filter( 'term_name_rss', 'convert_chars' );

// Pre save hierarchy
add_filter( 'wp_insert_post_parent', 'wp_check_post_hierarchy_for_loops', 10, 2 );
add_filter( 'wp_update_term_parent', 'wp_check_term_hierarchy_for_loops', 10, 3 );

// Display filters
add_filter( 'the_title', 'wptexturize'   );
add_filter( 'the_title', 'convert_chars' );
add_filter( 'the_title', 'trim'          );

add_filter( 'the_content', 'wptexturize'        );
add_filter( 'the_content', 'convert_smilies'    );
add_filter( 'the_content', 'convert_chars'      );
add_filter( 'the_content', 'wpautop'            );
add_filter( 'the_content', 'shortcode_unautop'  );
add_filter( 'the_content', 'prepend_attachment' );

add_filter( 'the_excerpt',     'wptexturize'      );
add_filter( 'the_excerpt',     'convert_smilies'  );
add_filter( 'the_excerpt',     'convert_chars'    );
add_filter( 'the_excerpt',     'wpautop'          );
add_filter( 'the_excerpt',     'shortcode_unautop');
add_filter( 'get_the_excerpt', 'wp_trim_excerpt'  );

add_filter( 'comment_text', 'wptexturize'            );
add_filter( 'comment_text', 'convert_chars'          );
add_filter( 'comment_text', 'make_clickable',      9 );
add_filter( 'comment_text', 'force_balance_tags', 25 );
add_filter( 'comment_text', 'convert_smilies',    20 );
add_filter( 'comment_text', 'wpautop',            30 );

add_filter( 'comment_excerpt', 'convert_chars' );

add_filter( 'list_cats',         'wptexturize' );

add_filter( 'wp_sprintf', 'wp_sprintf_l', 10, 2 );

// RSS filters
add_filter( 'the_title_rss',      'strip_tags'                    );
add_filter( 'the_title_rss',      'ent2ncr',                    8 );
add_filter( 'the_title_rss',      'esc_html'                      );
add_filter( 'the_content_rss',    'ent2ncr',                    8 );
add_filter( 'the_content_feed',   'wp_staticize_emoji'            );
add_filter( 'the_excerpt_rss',    'convert_chars'                 );
add_filter( 'the_excerpt_rss',    'ent2ncr',                    8 );
add_filter( 'comment_author_rss', 'ent2ncr',                    8 );
add_filter( 'comment_text_rss',   'ent2ncr',                    8 );
add_filter( 'comment_text_rss',   'esc_html'                      );
add_filter( 'comment_text_rss',   'wp_staticize_emoji'            );
add_filter( 'bloginfo_rss',       'ent2ncr',                    8 );
add_filter( 'the_author',         'ent2ncr',                    8 );
add_filter( 'the_guid',           'esc_url'                       );

// Email filters
add_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Misc filters
add_filter( 'option_ping_sites',        'privacy_ping_filter'                 );
add_filter( 'option_blog_charset',      '_wp_specialchars'                    ); // IMPORTANT: This must not be wp_specialchars() or esc_html() or it'll cause an infinite loop
add_filter( 'option_blog_charset',      '_canonical_charset'                  );
add_filter( 'option_home',              '_config_wp_home'                     );
add_filter( 'option_siteurl',           '_config_wp_siteurl'                  );
add_filter( 'tiny_mce_before_init',     '_mce_set_direction'                  );
add_filter( 'teeny_mce_before_init',    '_mce_set_direction'                  );
add_filter( 'pre_kses',                 'wp_pre_kses_less_than'               );
add_filter( 'sanitize_title',           'sanitize_title_with_dashes',   10, 3 );
add_action( 'check_comment_flood',      'check_comment_flood_db',       10, 3 );
add_filter( 'comment_flood_filter',     'wp_throttle_comment_flood',    10, 3 );
add_filter( 'pre_comment_content',      'wp_rel_nofollow',              15    );
add_filter( 'comment_email',            'antispambot'                         );
add_filter( 'option_tag_base',          '_wp_filter_taxonomy_base'            );
add_filter( 'option_category_base',     '_wp_filter_taxonomy_base'            );
add_filter( 'the_posts',                '_close_comments_for_old_posts', 10, 2);
add_filter( 'comments_open',            '_close_comments_for_old_post', 10, 2 );
add_filter( 'pings_open',               '_close_comments_for_old_post', 10, 2 );
add_filter( 'editable_slug',            'urldecode'                           );
add_filter( 'editable_slug',            'esc_textarea'                        );
add_filter( 'nav_menu_meta_box_object', '_wp_nav_menu_meta_box_object'        );
add_filter( 'pingback_ping_source_uri', 'pingback_ping_source_uri'            );
add_filter( 'xmlrpc_pingback_error',    'xmlrpc_pingback_error'               );
add_filter( 'title_save_pre',           'trim'                                );

add_filter( 'http_request_host_is_external', 'allowed_http_request_hosts', 10, 2 );

// Actions
add_action( 'wp_head',             '_wp_render_title_tag',            1     );
add_action( 'wp_head',             'wp_enqueue_scripts',              1     );
add_action( 'wp_head',             'feed_links',                      2     );
add_action( 'wp_head',             'feed_links_extra',                3     );
add_action( 'wp_head',             'rsd_link'                               );
add_action( 'wp_head',             'wlwmanifest_link'                       );
add_action( 'wp_head',             'adjacent_posts_rel_link_wp_head', 10, 0 );
add_action( 'wp_head',             'locale_stylesheet'                      );
add_action( 'publish_future_post', 'check_and_publish_future_post',   10, 1 );
add_action( 'wp_head',             'noindex',                          1    );
add_action( 'wp_head',             'print_emoji_detection_script',     7    );
add_action( 'wp_head',             'wp_print_styles',                  8    );
add_action( 'wp_head',             'wp_print_head_scripts',            9    );
add_action( 'wp_head',             'wp_generator'                           );
add_action( 'wp_head',             'rel_canonical'                          );
add_action( 'wp_head',             'wp_shortlink_wp_head',            10, 0 );
add_action( 'wp_head',             'wp_site_icon',                    99    );
add_action( 'wp_footer',           'wp_print_footer_scripts',         20    );
add_action( 'template_redirect',   'wp_shortlink_header',             11, 0 );
add_action( 'wp_print_footer_scripts', '_wp_footer_scripts'                 );
add_action( 'init',                'check_theme_switched',            99    );
add_action( 'after_switch_theme',  '_wp_sidebars_changed'                   );
add_action( 'wp_print_styles',     'print_emoji_styles'                     );

if ( isset( $_GET['replytocom'] ) )
    add_action( 'wp_head', 'wp_no_robots' );

// Login actions
add_action( 'login_head',          'wp_print_head_scripts',         9     );
add_action( 'login_footer',        'wp_print_footer_scripts',       20    );
add_action( 'login_init',          'send_frame_options_header',     10, 0 );

// Feed Generator Tags
foreach ( array( 'rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head' ) as $action ) {
	add_action( $action, 'the_generator' );
}

// Feed Site Icon
add_action( 'atom_head', 'atom_site_icon' );
add_action( 'rss2_head', 'rss2_site_icon' );


// WP Cron
if ( !defined( 'DOING_CRON' ) )
	add_action( 'init', 'wp_cron' );

// 2 Actions 2 Furious
add_action( 'do_feed_rdf',                'do_feed_rdf',                             10, 1 );
add_action( 'do_feed_rss',                'do_feed_rss',                             10, 1 );
add_action( 'do_feed_rss2',               'do_feed_rss2',                            10, 1 );
add_action( 'do_feed_atom',               'do_feed_atom',                            10, 1 );
add_action( 'do_pings',                   'do_all_pings',                            10, 1 );
add_action( 'do_robots',                  'do_robots'                                      );
add_action( 'set_comment_cookies',        'wp_set_comment_cookies',                  10, 2 );
add_action( 'sanitize_comment_cookies',   'sanitize_comment_cookies'                       );
add_action( 'admin_print_scripts',        'print_emoji_detection_script'                   );
add_action( 'admin_print_scripts',        'print_head_scripts',                      20    );
add_action( 'admin_print_footer_scripts', '_wp_footer_scripts'                             );
add_action( 'admin_print_styles',         'print_emoji_styles'                             );
add_action( 'admin_print_styles',         'print_admin_styles',                      20    );
add_action( 'init',                       'smilies_init',                             5    );
add_action( 'plugins_loaded',             'wp_maybe_load_widgets',                    0    );
add_action( 'plugins_loaded',             'wp_maybe_load_embeds',                     0    );
add_action( 'shutdown',                   'wp_ob_end_flush_all',                      1    );
// Create a revision whenever a post is updated.
add_action( 'post_updated',               'wp_save_post_revision',                   10, 1 );
add_action( 'publish_post',               '_publish_post_hook',                       5, 1 );
add_action( 'transition_post_status',     '_transition_post_status',                  5, 3 );
add_action( 'transition_post_status',     '_update_term_count_on_transition_post_status', 10, 3 );
add_action( 'comment_form',               'wp_comment_form_unfiltered_html_nonce'          );
add_action( 'wp_scheduled_delete',        'wp_scheduled_delete'                            );
add_action( 'wp_scheduled_auto_draft_delete', 'wp_delete_auto_drafts'                      );
add_action( 'admin_init',                 'send_frame_options_header',               10, 0 );
add_action( 'importer_scheduled_cleanup', 'wp_delete_attachment'                           );
add_action( 'upgrader_scheduled_cleanup', 'wp_delete_attachment'                           );
add_action( 'welcome_panel',              'wp_welcome_panel'                               );

// Navigation menu actions
add_action( 'delete_post',                '_wp_delete_post_menu_item'         );
add_action( 'delete_term',                '_wp_delete_tax_menu_item',   10, 3 );
add_action( 'transition_post_status',     '_wp_auto_add_pages_to_menu', 10, 3 );

// Post Thumbnail CSS class filtering
add_action( 'begin_fetch_post_thumbnail_html', '_wp_post_thumbnail_class_filter_add'    );
add_action( 'end_fetch_post_thumbnail_html',   '_wp_post_thumbnail_class_filter_remove' );

// Redirect Old Slugs
add_action( 'template_redirect', 'wp_old_slug_redirect'              );
add_action( 'post_updated',      'wp_check_for_changed_slugs', 12, 3 );

// Nonce check for Post Previews
add_action( 'init', '_show_post_preview' );

// Output JS to reset window.name for previews
add_action( 'wp_head', 'wp_post_preview_js', 1 );

// Timezone
add_filter( 'pre_option_gmt_offset','wp_timezone_override_offset' );

// Admin Color Schemes
add_action( 'admin_init', 'register_admin_color_schemes', 1);
add_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

// If the upgrade hasn't run yet, assume link manager is used.
add_filter( 'default_option_link_manager_enabled', '__return_true' );

// This option no longer exists; tell plugins we always support auto-embedding.
add_filter( 'default_option_embed_autourls', '__return_true' );

// Default settings for heartbeat
add_filter( 'heartbeat_settings', 'wp_heartbeat_settings' );

// Check if the user is logged out
add_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );
add_filter( 'heartbeat_send',        'wp_auth_check' );
add_filter( 'heartbeat_nopriv_send', 'wp_auth_check' );

// Default authentication filters
add_filter( 'authenticate', 'wp_authenticate_username_password',  20, 3 );
add_filter( 'authenticate', 'wp_authenticate_spam_check',         99    );
add_filter( 'determine_current_user', 'wp_validate_auth_cookie'          );
add_filter( 'determine_current_user', 'wp_validate_logged_in_cookie', 20 );

// Split term updates.
add_action( 'split_shared_term', '_wp_check_split_default_terms',  10, 4 );
add_action( 'split_shared_term', '_wp_check_split_terms_in_menus', 10, 4 );
add_action( 'split_shared_term', '_wp_check_split_nav_menu_terms', 10, 4 );

/**
 * Filters formerly mixed into wp-includes
 */
// Theme
add_action( 'wp_loaded', '_custom_header_background_just_in_time' );
add_action( 'plugins_loaded', '_wp_customize_include' );
add_action( 'admin_enqueue_scripts', '_wp_customize_loader_settings' );
add_action( 'delete_attachment', '_delete_attachment_theme_mod' );

// Calendar widget cache
add_action( 'save_post', 'delete_get_calendar_cache' );
add_action( 'delete_post', 'delete_get_calendar_cache' );
add_action( 'update_option_start_of_week', 'delete_get_calendar_cache' );
add_action( 'update_option_gmt_offset', 'delete_get_calendar_cache' );

// Author
add_action( 'transition_post_status', '__clear_multi_author_cache' );

// Post
add_action( 'init', 'create_initial_post_types', 0 ); // highest priority
add_action( 'admin_menu', '_add_post_type_submenus' );
add_action( 'before_delete_post', '_reset_front_page_settings_for_post' );
add_action( 'wp_trash_post',      '_reset_front_page_settings_for_post' );

// Post Formats
add_filter( 'request', '_post_format_request' );
add_filter( 'term_link', '_post_format_link', 10, 3 );
add_filter( 'get_post_format', '_post_format_get_term' );
add_filter( 'get_terms', '_post_format_get_terms', 10, 3 );
add_filter( 'wp_get_object_terms', '_post_format_wp_get_object_terms' );

// KSES
add_action( 'init', 'kses_init' );
add_action( 'set_current_user', 'kses_init' );

// Script Loader
add_action( 'wp_default_scripts', 'wp_default_scripts' );
add_filter( 'wp_print_scripts', 'wp_just_in_time_script_localization' );
add_filter( 'print_scripts_array', 'wp_prototype_before_jquery' );

add_action( 'wp_default_styles', 'wp_default_styles' );
add_filter( 'style_loader_src', 'wp_style_loader_src', 10, 2 );

// Taxonomy
add_action( 'init', 'create_initial_taxonomies', 0 ); // highest priority

// Canonical
add_action( 'template_redirect', 'redirect_canonical' );
add_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );

// Shortcodes
add_filter( 'the_content', 'do_shortcode', 11 ); // AFTER wpautop()

// Media
add_action( 'wp_playlist_scripts', 'wp_playlist_scripts' );
add_action( 'customize_controls_enqueue_scripts', 'wp_plupload_default_settings' );

// Nav menu
add_filter( 'nav_menu_item_id', '_nav_menu_item_id_use_once', 10, 2 );

// Widgets
add_action( 'init', 'wp_widgets_init', 1 );

// Admin Bar
// Don't remove. Wrong way to disable.
add_action( 'template_redirect', '_wp_admin_bar_init', 0 );
add_action( 'admin_init', '_wp_admin_bar_init' );
add_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
add_action( 'in_admin_header', 'wp_admin_bar_render', 0 );

// Former admin filters that can also be hooked on the front end
add_action( 'media_buttons', 'media_buttons' );
add_filter( 'image_send_to_editor', 'image_add_caption', 20, 8 );
add_filter( 'media_send_to_editor', 'image_media_send_to_editor', 10, 3 );

unset( $filter, $action );
