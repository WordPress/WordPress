<?php
/**
 * Utilities used to fetch and create templates.
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Build a unified template object based a post Object.
 *
 * @access private
 * @since 5.8.0
 *
 * @param WP_Post $post Template post.
 *
 * @return WP_Block_Template|WP_Error Template.
 */
function _build_template_result_from_post( $post ) {
	$terms = get_the_terms( $post, 'wp_theme' );

	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	if ( ! $terms ) {
		return new WP_Error( 'template_missing_theme', __( 'No theme is defined for this template.' ) );
	}

	$theme = $terms[0]->name;

	$template                 = new WP_Block_Template();
	$template->wp_id          = $post->ID;
	$template->id             = $theme . '//' . $post->post_name;
	$template->theme          = $theme;
	$template->content        = $post->post_content;
	$template->slug           = $post->post_name;
	$template->source         = 'custom';
	$template->type           = $post->post_type;
	$template->description    = $post->post_excerpt;
	$template->title          = $post->post_title;
	$template->status         = $post->post_status;
	$template->has_theme_file = false;

	return $template;
}

/**
 * Retrieves a list of unified template objects based on a query.
 *
 * @since 5.8.0
 *
 * @param array $query {
 *     Optional. Arguments to retrieve templates.
 *
 *     @type array  $slug__in List of slugs to include.
 *     @type int    $wp_id Post ID of customized template.
 * }
 * @param string $template_type Optional. The template type (post type). Default 'wp_template'.
 * @return WP_Block_Template[] Block template objects.
 */
function get_block_templates( $query = array(), $template_type = 'wp_template' ) {
	$wp_query_args = array(
		'post_status'    => array( 'auto-draft', 'draft', 'publish' ),
		'post_type'      => $template_type,
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'tax_query'      => array(
			array(
				'taxonomy' => 'wp_theme',
				'field'    => 'name',
				'terms'    => wp_get_theme()->get_stylesheet(),
			),
		),
	);

	if ( isset( $query['slug__in'] ) ) {
		$wp_query_args['post_name__in'] = $query['slug__in'];
	}

	// This is only needed for the regular templates CPT listing and editor.
	if ( isset( $query['wp_id'] ) ) {
		$wp_query_args['p'] = $query['wp_id'];
	} else {
		$wp_query_args['post_status'] = 'publish';
	}

	$template_query = new WP_Query( $wp_query_args );
	$query_result   = array();
	foreach ( $template_query->posts as $post ) {
		$template = _build_template_result_from_post( $post );

		if ( ! is_wp_error( $template ) ) {
			$query_result[] = $template;
		}
	}

	return $query_result;
}

/**
 * Retrieves a single unified template object using its id.
 *
 * @since 5.8.0
 *
 * @param string $id Template unique identifier (example: theme_slug//template_slug).
 * @param string $template_type wp_template.
 *
 * @return WP_Block_Template|null Template.
 */
function get_block_template( $id, $template_type = 'wp_template' ) {
	$parts = explode( '//', $id, 2 );
	if ( count( $parts ) < 2 ) {
		return null;
	}
	list( $theme, $slug ) = $parts;
	$wp_query_args        = array(
		'post_name__in'  => array( $slug ),
		'post_type'      => $template_type,
		'post_status'    => array( 'auto-draft', 'draft', 'publish', 'trash' ),
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'tax_query'      => array(
			array(
				'taxonomy' => 'wp_theme',
				'field'    => 'name',
				'terms'    => $theme,
			),
		),
	);
	$template_query       = new WP_Query( $wp_query_args );
	$posts                = $template_query->posts;

	if ( count( $posts ) > 0 ) {
		$template = _build_template_result_from_post( $posts[0] );

		if ( ! is_wp_error( $template ) ) {
			return $template;
		}
	}

	return null;
}
