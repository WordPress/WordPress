<?php
/**
 * Register custom post type and taxonomies.
 *
 * @package wpcode
 */

add_action( 'init', 'wpcode_register_post_type', - 5 );
add_action( 'init', 'wpcode_register_taxonomies', - 5 );
add_filter( 'update_post_term_count_statuses', 'wpcode_taxonomies_count_drafts', 10, 2 );
add_action( 'wpcode_before_snippet_save', 'wpcode_maybe_remove_core_content_filters' );
add_action( 'wpcode_snippet_after_update', 'wpcode_restore_core_content_filters' );

/**
 * Register the post type for snippets.
 *
 * @return void
 */
function wpcode_register_post_type() {
	register_post_type(
		'wpcode',
		array(
			'public'  => false,
			'show_ui' => false,
		)
	);
}

/**
 * Register the custom taxonomies used for snippets.
 *
 * @return void
 */
function wpcode_register_taxonomies() {
	register_taxonomy(
		'wpcode_type',
		'wpcode',
		array(
			'public' => false,
		)
	);
	register_taxonomy(
		'wpcode_location',
		'wpcode',
		array(
			'public' => false,
		)
	);
	register_taxonomy(
		'wpcode_tags',
		'wpcode',
		array(
			'public' => false,
		)
	);
}

/**
 * Count draft (inactive) snippets as part of our custom taxonomies count.
 *
 * @param array       $statuses The statuses to include in the count.
 * @param WP_Taxonomy $taxonomy The taxonomy object.
 *
 * @return array
 */
function wpcode_taxonomies_count_drafts( $statuses, $taxonomy ) {
	$taxonomies = array(
		'wpcode_type',
		'wpcode_location',
		'wpcode_tags',
	);
	if ( in_array( $taxonomy->name, $taxonomies, true ) ) {
		$statuses[] = 'draft';
	}

	return $statuses;
}

/**
 * Remove core filters that may interfere with snippet saving.
 *
 * @param WPCode_Snippet $snippet The snippet being saved.
 *
 * @return void
 */
function wpcode_maybe_remove_core_content_filters( $snippet ) {
	if ( ! function_exists( 'wp_remove_targeted_link_rel_filters' ) ) {
		// This function is only available in WP 5.1+.
		return;
	}
	/**
	 * Filters the code types that should keep the core filters.
	 *
	 * @param array $code_types_to_keep_filters The code types that should keep the core filters.
	 */
	$code_types_to_keep_filters = apply_filters(
		'wpcode_code_types_to_keep_core_content_filters',
		array(
			'text',
			'html',
		)
	);
	if ( ! in_array( $snippet->get_code_type(), $code_types_to_keep_filters, true ) ) {
		wp_remove_targeted_link_rel_filters();
	}
}

/**
 * Add back the core filters that were removed when saving a snippet.
 *
 * @return void
 */
function wpcode_restore_core_content_filters() {
	if ( ! function_exists( 'wp_init_targeted_link_rel_filters' ) ) {
		// This function is only available in WP 5.1+.
		return;
	}
	wp_init_targeted_link_rel_filters();
}
