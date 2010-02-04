<?php
// Users
add_filter ( 'wpmu_validate_user_signup', 'signup_nonce_check' );
add_action ( 'init', 'maybe_add_existing_user_to_blog' );
add_action ( 'wpmu_new_user', 'newuser_notify_siteadmin' );
add_action ( 'wpmu_activate_user', 'add_new_user_to_blog', 10, 3 );
add_action ( 'sanitize_user', 'strtolower' );

// Blogs
add_filter ( 'wpmu_validate_blog_signup', 'signup_nonce_check' );
add_action ( 'wpmu_new_blog', 'wpmu_log_new_registrations', 10, 2 );
add_action ( 'wpmu_new_blog', 'newblog_notify_siteadmin', 10, 2 );

// Register Nonce
add_action ( 'signup_hidden_fields', 'signup_nonce_fields' );

// Template
add_action ( 'template_redirect', 'maybe_redirect_404' );
add_filter ( 'allowed_redirect_hosts', 'redirect_this_site' );

// Administration
add_filter ( 'mce_buttons_2', 'remove_tinymce_media_button' );
add_filter ( 'term_id_filter', 'global_terms', 10, 2 );
add_action ( 'publish_post', 'update_posts_count' );
add_action ( 'delete_post', 'wpmu_update_blogs_date' );
add_action ( 'private_to_published', 'wpmu_update_blogs_date' );
add_action ( 'publish_phone', 'wpmu_update_blogs_date' );
add_action ( 'publish_post', 'wpmu_update_blogs_date' );

// Files
add_filter ( 'wp_upload_bits', 'upload_is_file_too_big' );
add_filter ( 'import_upload_size_limit', 'fix_import_form_size' );
add_filter ( 'upload_mimes', 'check_upload_mimes' );

// Mail
add_filter ( 'wp_mail_from', 'wordpressmu_wp_mail_from' );


add_action( "phpmailer_init", "fix_phpmailer_messageid" );
?>
