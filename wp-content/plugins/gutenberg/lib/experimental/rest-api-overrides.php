<?php
/**
 * Boot package REST API overrides.
 *
 * @package gutenberg
 */

/**
 * Allow auto-draft status in WordPress REST API for all post types.
 *
 * By default, WordPress REST API doesn't include 'auto-draft' in the
 * allowed status values. This function adds it to the schema enum for
 * all eligible post types.
 */
function gutenberg_boot_allow_auto_draft_in_rest_api() {
	$auto_draft_filter = function ( $schema ) {
		if ( isset( $schema['properties']['status']['enum'] ) ) {
			$schema['properties']['status']['enum'][] = 'auto-draft';
		}
		return $schema;
	};

	// Get post types that should support auto-draft.
	$post_types = get_post_types(
		array(
			'show_in_rest' => true,
			'show_ui'      => true,
		),
		'objects'
	);

	foreach ( $post_types as $post_type ) {
		$supports_editor   = post_type_supports( $post_type->name, 'editor' );
		$supports_title    = post_type_supports( $post_type->name, 'title' );
		$is_wp_core_system = strpos( $post_type->name, 'wp_' ) === 0;
		$is_attachment     = 'attachment' === $post_type->name;

		// Only add auto-draft to content post types:
		// - Must support either editor or title (content editing capabilities).
		// - Exclude WordPress core system types (wp_block, wp_navigation, etc.).
		// - Exclude attachments (media files don't need auto-draft status).
		$should_support_auto_draft = ( $supports_editor || $supports_title ) &&
									! $is_wp_core_system &&
									! $is_attachment;

		if ( $should_support_auto_draft ) {
			add_filter( "rest_{$post_type->name}_item_schema", $auto_draft_filter );
		}
	}
}

add_action( 'rest_api_init', 'gutenberg_boot_allow_auto_draft_in_rest_api' );
