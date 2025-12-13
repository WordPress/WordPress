<?php
/**
 * WordPress 7.0 compatibility functions for the Gutenberg
 * editor plugin changes related to REST API.
 *
 * @package gutenberg
 */

/**
 * Retrieves a single unified template object using its id.
 * Parses pattern blocks in the template content.
 *
 * @param WP_Block_Template|null $block_template The found block template, or null if there isn't one.
 * @param string                 $id             Template unique identifier (example: 'theme_slug//template_slug').
 * @param string                 $template_type  Template type. Either 'wp_template' or 'wp_template_part'.
 */
function gutenberg_parse_pattern_blocks_in_block_template( $block_template, $id, $template_type ) {
	if ( 'wp_template' !== $template_type ) {
		return $block_template;
	}

	if ( ! empty( $block_template->content ) ) {
		$blocks = parse_blocks( $block_template->content );
		if ( ! empty( $blocks ) ) {
			$blocks                  = gutenberg_resolve_pattern_blocks( $blocks );
			$block_template->content = serialize_blocks( $blocks );
		}
	}
	return $block_template;
}

add_filter( 'get_block_template', 'gutenberg_parse_pattern_blocks_in_block_template', 10, 3 );
add_filter( 'get_block_file_template', 'gutenberg_parse_pattern_blocks_in_block_template', 10, 3 );

/**
 * Retrieves a list of unified template objects based on a query.
 * Parses pattern blocks in the template content items.
 *
 * @param WP_Block_Template[] $query_result Array of found block templates.
 * @param array               $query {
 *     Arguments to retrieve templates. All arguments are optional.
 *
 *     @type string[] $slug__in  List of slugs to include.
 *     @type int      $wp_id     Post ID of customized template.
 *     @type string   $area      A 'wp_template_part_area' taxonomy value to filter by (for 'wp_template_part' template type only).
 *     @type string   $post_type Post type to get the templates for.
 * }
 * @param string              $template_type wp_template or wp_template_part.
 */
function gutenberg_parse_pattern_blocks_in_block_templates( $query_result, $query, $template_type ) {
	if ( 'wp_template' !== $template_type ) {
		return $query_result;
	}

	if ( ! empty( $query_result ) ) {
		foreach ( $query_result as $template ) {
			$blocks = parse_blocks( $template->content );
			if ( ! empty( $blocks ) ) {
				$blocks            = gutenberg_resolve_pattern_blocks( $blocks );
				$template->content = serialize_blocks( $blocks );
			}
		}
	}
	return $query_result;
}

add_filter( 'get_block_templates', 'gutenberg_parse_pattern_blocks_in_block_templates', 10, 3 );
