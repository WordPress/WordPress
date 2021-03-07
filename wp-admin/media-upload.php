<?php
/**
 * Manage media uploaded file.
 *
 * There are many filters in here for media. Plugins can extend functionality
 * by hooking into the filters.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! isset( $_GET['inline'] ) ) {
	define( 'IFRAME_REQUEST', true );
}

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'upload_files' ) ) {
	wp_die( __( 'Sorry, you are not allowed to upload files.' ), 403 );
}

wp_enqueue_script( 'plupload-handlers' );
wp_enqueue_script( 'image-edit' );
wp_enqueue_script( 'set-post-thumbnail' );
wp_enqueue_style( 'imgareaselect' );
wp_enqueue_script( 'media-gallery' );

header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

// IDs should be integers.
$ID      = isset( $ID ) ? (int) $ID : 0; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
$post_id = isset( $post_id ) ? (int) $post_id : 0;

// Require an ID for the edit screen.
if ( isset( $action ) && 'edit' === $action && ! $ID ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
	wp_die(
		'<h1>' . __( 'Something went wrong.' ) . '</h1>' .
		'<p>' . __( 'Invalid item ID.' ) . '</p>',
		403
	);
}

if ( ! empty( $_REQUEST['post_id'] ) && ! current_user_can( 'edit_post', $_REQUEST['post_id'] ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to edit this item.' ) . '</p>',
		403
	);
}

// Upload type: image, video, file, ...?
if ( isset( $_GET['type'] ) ) {
	$type = (string) $_GET['type'];
} else {
	/**
	 * Filters the default media upload type in the legacy (pre-3.5.0) media popup.
	 *
	 * @since 2.5.0
	 *
	 * @param string $type The default media upload type. Possible values include
	 *                     'image', 'audio', 'video', 'file', etc. Default 'file'.
	 */
	$type = apply_filters( 'media_upload_default_type', 'file' );
}

// Tab: gallery, library, or type-specific.
if ( isset( $_GET['tab'] ) ) {
	$tab = (string) $_GET['tab'];
} else {
	/**
	 * Filters the default tab in the legacy (pre-3.5.0) media popup.
	 *
	 * @since 2.5.0
	 *
	 * @param string $tab The default media popup tab. Default 'type' (From Computer).
	 */
	$tab = apply_filters( 'media_upload_default_tab', 'type' );
}

$body_id = 'media-upload';

// Let the action code decide how to handle the request.
if ( 'type' === $tab || 'type_url' === $tab || ! array_key_exists( $tab, media_upload_tabs() ) ) {
	/**
	 * Fires inside specific upload-type views in the legacy (pre-3.5.0)
	 * media popup based on the current tab.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the specific
	 * media upload type.
	 *
	 * The hook only fires if the current `$tab` is 'type' (From Computer),
	 * 'type_url' (From URL), or, if the tab does not exist (i.e., has not
	 * been registered via the {@see 'media_upload_tabs'} filter.
	 *
	 * Possible hook names include:
	 *
	 *  - `media_upload_audio`
	 *  - `media_upload_file`
	 *  - `media_upload_image`
	 *  - `media_upload_video`
	 *
	 * @since 2.5.0
	 */
	do_action( "media_upload_{$type}" );
} else {
	/**
	 * Fires inside limited and specific upload-tab views in the legacy
	 * (pre-3.5.0) media popup.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the specific
	 * media upload tab. Possible values include 'library' (Media Library),
	 * or any custom tab registered via the {@see 'media_upload_tabs'} filter.
	 *
	 * @since 2.5.0
	 */
	do_action( "media_upload_{$tab}" );
}

