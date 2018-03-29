<?php

/**
 * Redirect admin post to FB builder if set.
 *
 * @since 3.0.0
 *
 * @param string $location Parameter passed by the 'redirect_post_location' filter.
 * @return string $_POST['et-fb-builder-redirect'] if set, $location otherwise.
 */
function et_fb_redirect_post_location( $location ) {
	if ( is_admin() && isset( $_POST['et-fb-builder-redirect'] ) ) {
		return $_POST['et-fb-builder-redirect'];
	}

	return $location;
}
add_filter( 'redirect_post_location', 'et_fb_redirect_post_location' );

function et_fb_enabled() {
	if ( defined( 'ET_FB_ENABLED' ) ) {
		return ET_FB_ENABLED;
	}

	if ( empty( $_GET['et_fb'] ) ) {
		return false;
	}

	if ( is_customize_preview() ) {
		return false;
	}

	if ( ! is_single() && ! is_page() ) {
		return false;
	}

	if ( ! et_fb_is_user_can_edit() ) {
		return false;
	}

	if ( ! et_pb_is_allowed( 'use_visual_builder' ) ) {
		return false;
	}

	return true;
}

function et_fb_is_user_can_edit() {

	if ( is_page() ) {
		if ( ! current_user_can( 'edit_pages' ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_pages' ) && ! current_user_can( 'edit_page', get_the_ID() ) ) {
			return false;
		}

		if ( ( ! current_user_can( 'publish_pages' ) || ! current_user_can( 'edit_published_pages' ) ) && 'publish' === get_post_status() ) {
			return false;
		}

		if ( ( ! current_user_can( 'edit_private_pages' ) || ! current_user_can( 'read_private_pages' ) ) && 'private' === get_post_status() ) {
			return false;
		}
	} else {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_post', get_the_ID() ) ) {
			return false;
		}

		if ( ( ! current_user_can( 'publish_posts' ) || ! current_user_can( 'edit_published_posts' ) ) && 'publish' === get_post_status() ) {
			return false;
		}

		if ( ( ! current_user_can( 'edit_private_posts' ) || ! current_user_can( 'read_private_posts' ) ) && 'private' === get_post_status() ) {
			return false;
		}
	}

	return true;
}

define( 'ET_FB_ENABLED', et_fb_enabled() );

// Stop here if the front end builder isn't enabled.
if ( ! ET_FB_ENABLED ) {
	return;
}

define( 'ET_FB_URI', ET_BUILDER_URI . '/frontend-builder' );
define( 'ET_FB_ASSETS_URI', ET_FB_URI . '/assets' );

require_once ET_BUILDER_DIR . 'frontend-builder/view.php';
require_once ET_BUILDER_DIR . 'frontend-builder/assets.php';
require_once ET_BUILDER_DIR . 'frontend-builder/helpers.php';
require_once ET_BUILDER_DIR . 'frontend-builder/rtl.php';

do_action( 'et_fb_framework_loaded' );

if ( 'on' === et_get_option( 'divi_disable_translations', 'off' ) ) {
	add_filter( 'locale_stylesheet_uri', 'et_fb_remove_rtl_stylesheet' );
	add_filter( 'language_attributes',   'et_fb_remove_html_rtl_dir' );
}

et_fb_fix_plugin_conflicts();
