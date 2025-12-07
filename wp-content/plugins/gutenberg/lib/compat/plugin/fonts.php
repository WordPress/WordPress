<?php
/**
 * A special compat layer for legacy font files upload directory.
 *
 * @see https://github.com/WordPress/gutenberg/pull/57688#issuecomment-2259037546
 *
 * @package gutenberg
 */

// @core-merge: Do not merge this function, it is for deleting fonts from the wp-content/fonts directory only used in Gutenberg.
/**
 * Deletes associated font files from wp-content/fonts, when a font face is deleted.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function gutenberg_before_delete_font_face( $post_id, $post ) {
	if ( 'wp_font_face' !== $post->post_type ) {
		return;
	}

	$font_files = get_post_meta( $post_id, '_wp_font_face_file', false );

	if ( empty( $font_files ) ) {
		return;
	}

	$site_path = '';
	if ( is_multisite() && ! ( is_main_network() && is_main_site() ) ) {
		$site_path = '/sites/' . get_current_blog_id();
	}

	$font_dir = path_join( WP_CONTENT_DIR, 'fonts' ) . $site_path;

	foreach ( $font_files as $font_file ) {
		$font_path = $font_dir . '/' . $font_file;

		if ( file_exists( $font_path ) ) {
			wp_delete_file( $font_path );
		}
	}
}
add_action( 'before_delete_post', 'gutenberg_before_delete_font_face', 10, 2 );
