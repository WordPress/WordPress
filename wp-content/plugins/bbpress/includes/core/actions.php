<?php

/**
 * bbPress Actions
 *
 * @package bbPress
 * @subpackage Core
 *
 * This file contains the actions that are used through-out bbPress. They are
 * consolidated here to make searching for them easier, and to help developers
 * understand at a glance the order in which things occur.
 *
 * There are a few common places that additional actions can currently be found
 *
 *  - bbPress: In {@link bbPress::setup_actions()} in bbpress.php
 *  - Admin: More in {@link BBP_Admin::setup_actions()} in admin.php
 *
 * @see /core/filters.php
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Attach bbPress to WordPress
 *
 * bbPress uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when bbPress is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions        v--bbPress Sub-actions
 */
add_action( 'plugins_loaded',           'bbp_loaded',                 10    );
add_action( 'init',                     'bbp_init',                   0     ); // Early for bbp_register
add_action( 'parse_query',              'bbp_parse_query',            2     ); // Early for overrides
add_action( 'widgets_init',             'bbp_widgets_init',           10    );
add_action( 'generate_rewrite_rules',   'bbp_generate_rewrite_rules', 10    );
add_action( 'wp_enqueue_scripts',       'bbp_enqueue_scripts',        10    );
add_action( 'wp_head',                  'bbp_head',                   10    );
add_action( 'wp_footer',                'bbp_footer',                 10    );
add_action( 'set_current_user',         'bbp_setup_current_user',     10    );
add_action( 'setup_theme',              'bbp_setup_theme',            10    );
add_action( 'after_setup_theme',        'bbp_after_setup_theme',      10    );
add_action( 'template_redirect',        'bbp_template_redirect',      8     ); // Before BuddyPress's 10 [BB2225]
add_action( 'login_form_login',         'bbp_login_form_login',       10    );
add_action( 'profile_update',           'bbp_profile_update',         10, 2 ); // user_id and old_user_data
add_action( 'user_register',            'bbp_user_register',          10    );

/**
 * bbp_loaded - Attached to 'plugins_loaded' above
 *
 * Attach various loader actions to the bbp_loaded action.
 * The load order helps to execute code at the correct time.
 *                                                         v---Load order
 */
add_action( 'bbp_loaded', 'bbp_constants',                 2  );
add_action( 'bbp_loaded', 'bbp_boot_strap_globals',        4  );
add_action( 'bbp_loaded', 'bbp_includes',                  6  );
add_action( 'bbp_loaded', 'bbp_setup_globals',             8  );
add_action( 'bbp_loaded', 'bbp_setup_option_filters',      10 );
add_action( 'bbp_loaded', 'bbp_setup_user_option_filters', 12 );
add_action( 'bbp_loaded', 'bbp_register_theme_packages',   14 );
add_action( 'bbp_loaded', 'bbp_filter_user_roles_option',  16 );

/**
 * bbp_init - Attached to 'init' above
 *
 * Attach various initialization actions to the init action.
 * The load order helps to execute code at the correct time.
 *                                               v---Load order
 */
add_action( 'bbp_init', 'bbp_load_textdomain',   0   );
add_action( 'bbp_init', 'bbp_register',          0   );
add_action( 'bbp_init', 'bbp_add_rewrite_tags',  20  );
add_action( 'bbp_init', 'bbp_add_rewrite_rules', 30  );
add_action( 'bbp_init', 'bbp_add_permastructs',  40  );
add_action( 'bbp_init', 'bbp_ready',             999 );

/**
 * There is no action API for roles to use, so hook in immediately after
 * everything is included (including the theme's functions.php. This is after
 * the $wp_roles global is set but before $wp->init().
 *
 * If it's hooked in any sooner, role names may not be translated correctly.
 *
 * @link http://bbpress.trac.wordpress.org/ticket/2219
 *
 * This is kind of lame, but is all we have for now.
 */
add_action( 'bbp_after_setup_theme', 'bbp_add_forums_roles', 1 );

/**
 * When setting up the current user, make sure they have a role for the forums.
 *
 * This is multisite aware, thanks to bbp_filter_user_roles_option(), hooked to
 * the 'bbp_loaded' action above.
 */
add_action( 'bbp_setup_current_user', 'bbp_set_current_user_default_role' );

/**
 * bbp_register - Attached to 'init' above on 0 priority
 *
 * Attach various initialization actions early to the init action.
 * The load order helps to execute code at the correct time.
 *                                                         v---Load order
 */
add_action( 'bbp_register', 'bbp_register_post_types',     2  );
add_action( 'bbp_register', 'bbp_register_post_statuses',  4  );
add_action( 'bbp_register', 'bbp_register_taxonomies',     6  );
add_action( 'bbp_register', 'bbp_register_views',          8  );
add_action( 'bbp_register', 'bbp_register_shortcodes',     10 );

// Autoembeds
add_action( 'bbp_init', 'bbp_reply_content_autoembed', 8 );
add_action( 'bbp_init', 'bbp_topic_content_autoembed', 8 );

/**
 * bbp_ready - attached to end 'bbp_init' above
 *
 * Attach actions to the ready action after bbPress has fully initialized.
 * The load order helps to execute code at the correct time.
 *                                                v---Load order
 */
add_action( 'bbp_ready',  'bbp_setup_akismet',    2  ); // Spam prevention for topics and replies
add_action( 'bp_include', 'bbp_setup_buddypress', 10 ); // Social network integration

// Try to load the bbpress-functions.php file from the active themes
add_action( 'bbp_after_setup_theme', 'bbp_load_theme_functions', 10 );

// Widgets
add_action( 'bbp_widgets_init', array( 'BBP_Login_Widget',   'register_widget' ), 10 );
add_action( 'bbp_widgets_init', array( 'BBP_Views_Widget',   'register_widget' ), 10 );
add_action( 'bbp_widgets_init', array( 'BBP_Search_Widget',  'register_widget' ), 10 );
add_action( 'bbp_widgets_init', array( 'BBP_Forums_Widget',  'register_widget' ), 10 );
add_action( 'bbp_widgets_init', array( 'BBP_Topics_Widget',  'register_widget' ), 10 );
add_action( 'bbp_widgets_init', array( 'BBP_Replies_Widget', 'register_widget' ), 10 );
add_action( 'bbp_widgets_init', array( 'BBP_Stats_Widget',   'register_widget' ), 10 );

// Notices (loaded after bbp_init for translations)
add_action( 'bbp_head',             'bbp_login_notices'    );
add_action( 'bbp_head',             'bbp_topic_notices'    );
add_action( 'bbp_template_notices', 'bbp_template_notices' );

// Always exclude private/hidden forums if needed
add_action( 'pre_get_posts', 'bbp_pre_get_posts_normalize_forum_visibility', 4 );

// Profile Page Messages
add_action( 'bbp_template_notices', 'bbp_notice_edit_user_success'           );
add_action( 'bbp_template_notices', 'bbp_notice_edit_user_is_super_admin', 2 );

// Before Delete/Trash/Untrash Topic
add_action( 'wp_trash_post', 'bbp_trash_forum'   );
add_action( 'trash_post',    'bbp_trash_forum'   );
add_action( 'untrash_post',  'bbp_untrash_forum' );
add_action( 'delete_post',   'bbp_delete_forum'  );

// After Deleted/Trashed/Untrashed Topic
add_action( 'trashed_post',   'bbp_trashed_forum'   );
add_action( 'untrashed_post', 'bbp_untrashed_forum' );
add_action( 'deleted_post',   'bbp_deleted_forum'   );

// Auto trash/untrash/delete a forums topics
add_action( 'bbp_delete_forum',  'bbp_delete_forum_topics',  10 );
add_action( 'bbp_trash_forum',   'bbp_trash_forum_topics',   10 );
add_action( 'bbp_untrash_forum', 'bbp_untrash_forum_topics', 10 );

// New/Edit Forum
add_action( 'bbp_new_forum',  'bbp_update_forum', 10 );
add_action( 'bbp_edit_forum', 'bbp_update_forum', 10 );

// Save forum extra metadata
add_action( 'bbp_new_forum_post_extras',         'bbp_save_forum_extras', 2 );
add_action( 'bbp_edit_forum_post_extras',        'bbp_save_forum_extras', 2 );
add_action( 'bbp_forum_attributes_metabox_save', 'bbp_save_forum_extras', 2 );

// New/Edit Reply
add_action( 'bbp_new_reply',  'bbp_update_reply', 10, 7 );
add_action( 'bbp_edit_reply', 'bbp_update_reply', 10, 7 );

// Before Delete/Trash/Untrash Reply
add_action( 'wp_trash_post', 'bbp_trash_reply'   );
add_action( 'trash_post',    'bbp_trash_reply'   );
add_action( 'untrash_post',  'bbp_untrash_reply' );
add_action( 'delete_post',   'bbp_delete_reply'  );

// After Deleted/Trashed/Untrashed Reply
add_action( 'trashed_post',   'bbp_trashed_reply'   );
add_action( 'untrashed_post', 'bbp_untrashed_reply' );
add_action( 'deleted_post',   'bbp_deleted_reply'   );

// New/Edit Topic
add_action( 'bbp_new_topic',  'bbp_update_topic', 10, 5 );
add_action( 'bbp_edit_topic', 'bbp_update_topic', 10, 5 );

// Split/Merge Topic
add_action( 'bbp_merged_topic',     'bbp_merge_topic_count', 1, 3 );
add_action( 'bbp_post_split_topic', 'bbp_split_topic_count', 1, 3 );

// Move Reply
add_action( 'bbp_post_move_reply', 'bbp_move_reply_count', 1, 3 );

// Before Delete/Trash/Untrash Topic
add_action( 'wp_trash_post', 'bbp_trash_topic'   );
add_action( 'trash_post',    'bbp_trash_topic'   );
add_action( 'untrash_post',  'bbp_untrash_topic' );
add_action( 'delete_post',   'bbp_delete_topic'  );

// After Deleted/Trashed/Untrashed Topic
add_action( 'trashed_post',   'bbp_trashed_topic'   );
add_action( 'untrashed_post', 'bbp_untrashed_topic' );
add_action( 'deleted_post',   'bbp_deleted_topic'   );

// Favorites
add_action( 'bbp_trash_topic',  'bbp_remove_topic_from_all_favorites' );
add_action( 'bbp_delete_topic', 'bbp_remove_topic_from_all_favorites' );

// Subscriptions
add_action( 'bbp_trash_topic',  'bbp_remove_topic_from_all_subscriptions'       );
add_action( 'bbp_delete_topic', 'bbp_remove_topic_from_all_subscriptions'       );
add_action( 'bbp_trash_forum',  'bbp_remove_forum_from_all_subscriptions'       );
add_action( 'bbp_delete_forum', 'bbp_remove_forum_from_all_subscriptions'       );
add_action( 'bbp_new_reply',    'bbp_notify_subscribers',                 11, 5 );
add_action( 'bbp_new_topic',    'bbp_notify_forum_subscribers',           11, 4 );

// Sticky
add_action( 'bbp_trash_topic',  'bbp_unstick_topic' );
add_action( 'bbp_delete_topic', 'bbp_unstick_topic' );

// Update topic branch
add_action( 'bbp_trashed_topic',   'bbp_update_topic_walker' );
add_action( 'bbp_untrashed_topic', 'bbp_update_topic_walker' );
add_action( 'bbp_deleted_topic',   'bbp_update_topic_walker' );
add_action( 'bbp_spammed_topic',   'bbp_update_topic_walker' );
add_action( 'bbp_unspammed_topic', 'bbp_update_topic_walker' );

// Update reply branch
add_action( 'bbp_trashed_reply',   'bbp_update_reply_walker' );
add_action( 'bbp_untrashed_reply', 'bbp_update_reply_walker' );
add_action( 'bbp_deleted_reply',   'bbp_update_reply_walker' );
add_action( 'bbp_spammed_reply',   'bbp_update_reply_walker' );
add_action( 'bbp_unspammed_reply', 'bbp_update_reply_walker' );

// User status
// @todo make these sub-actions
add_action( 'make_ham_user',  'bbp_make_ham_user'  );
add_action( 'make_spam_user', 'bbp_make_spam_user' );

// User role
add_action( 'bbp_profile_update', 'bbp_profile_update_role' );

// Hook WordPress admin actions to bbPress profiles on save
add_action( 'bbp_user_edit_after', 'bbp_user_edit_after' );

// Caches
add_action( 'bbp_new_forum_pre_extras',  'bbp_clean_post_cache' );
add_action( 'bbp_new_forum_post_extras', 'bbp_clean_post_cache' );
add_action( 'bbp_new_topic_pre_extras',  'bbp_clean_post_cache' );
add_action( 'bbp_new_topic_post_extras', 'bbp_clean_post_cache' );
add_action( 'bbp_new_reply_pre_extras',  'bbp_clean_post_cache' );
add_action( 'bbp_new_reply_post_extras', 'bbp_clean_post_cache' );

/**
 * bbPress needs to redirect the user around in a few different circumstances:
 *
 * 1. POST and GET requests
 * 2. Accessing private or hidden content (forums/topics/replies)
 * 3. Editing forums, topics, replies, users, and tags
 * 4. bbPress specific AJAX requests
 */
add_action( 'bbp_template_redirect', 'bbp_forum_enforce_blocked', 1  );
add_action( 'bbp_template_redirect', 'bbp_forum_enforce_hidden',  1  );
add_action( 'bbp_template_redirect', 'bbp_forum_enforce_private', 1  );
add_action( 'bbp_template_redirect', 'bbp_post_request',          10 );
add_action( 'bbp_template_redirect', 'bbp_get_request',           10 );
add_action( 'bbp_template_redirect', 'bbp_check_user_edit',       10 );
add_action( 'bbp_template_redirect', 'bbp_check_forum_edit',      10 );
add_action( 'bbp_template_redirect', 'bbp_check_topic_edit',      10 );
add_action( 'bbp_template_redirect', 'bbp_check_reply_edit',      10 );
add_action( 'bbp_template_redirect', 'bbp_check_topic_tag_edit',  10 );

// Theme-side POST requests
add_action( 'bbp_post_request', 'bbp_do_ajax',                1  );
add_action( 'bbp_post_request', 'bbp_edit_topic_tag_handler', 1  );
add_action( 'bbp_post_request', 'bbp_edit_user_handler',      1  );
add_action( 'bbp_post_request', 'bbp_edit_forum_handler',     1  );
add_action( 'bbp_post_request', 'bbp_edit_reply_handler',     1  );
add_action( 'bbp_post_request', 'bbp_edit_topic_handler',     1  );
add_action( 'bbp_post_request', 'bbp_merge_topic_handler',    1  );
add_action( 'bbp_post_request', 'bbp_split_topic_handler',    1  );
add_action( 'bbp_post_request', 'bbp_move_reply_handler',     1  );
add_action( 'bbp_post_request', 'bbp_new_forum_handler',      10 );
add_action( 'bbp_post_request', 'bbp_new_reply_handler',      10 );
add_action( 'bbp_post_request', 'bbp_new_topic_handler',      10 );

// Theme-side GET requests
add_action( 'bbp_get_request', 'bbp_toggle_topic_handler',        1  );
add_action( 'bbp_get_request', 'bbp_toggle_reply_handler',        1  );
add_action( 'bbp_get_request', 'bbp_favorites_handler',           1  );
add_action( 'bbp_get_request', 'bbp_subscriptions_handler',       1  );
add_action( 'bbp_get_request', 'bbp_forum_subscriptions_handler', 1  );
add_action( 'bbp_get_request', 'bbp_search_results_redirect',     10 );

// Maybe convert the users password
add_action( 'bbp_login_form_login', 'bbp_user_maybe_convert_pass' );

add_action( 'bbp_activation', 'bbp_add_activation_redirect' );