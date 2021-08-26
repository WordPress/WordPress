<?php
/**
 * Block template loader functions.
 *
 * @package WordPress
 */

/**
 * Find a block template with equal or higher specificity than a given PHP template file.
 *
 * Internally, this communicates the block content that needs to be used by the template canvas through a global variable.
 *
 * @since 5.8.0
 *
 * @global string $_wp_current_template_content
 *
 * @param string   $template  Path to the template. See locate_template().
 * @param string   $type      Sanitized filename without extension.
 * @param string[] $templates A list of template candidates, in descending order of priority.
 * @return string The path to the Full Site Editing template canvas file, or the fallback PHP template.
 */
function locate_block_template( $template, $type, array $templates ) {
	global $_wp_current_template_content;

	if ( $template ) {
		/*
		 * locate_template() has found a PHP template at the path specified by $template.
		 * That means that we have a fallback candidate if we cannot find a block template
		 * with higher specificity.
		 *
		 * Thus, before looking for matching block themes, we shorten our list of candidate
		 * templates accordingly.
		 */

		// Locate the index of $template (without the theme directory path) in $templates.
		$relative_template_path = str_replace(
			array( get_stylesheet_directory() . '/', get_template_directory() . '/' ),
			'',
			$template
		);
		$index                  = array_search( $relative_template_path, $templates, true );

		// If the template hiearchy algorithm has successfully located a PHP template file,
		// we will only consider block templates with higher or equal specificity.
		$templates = array_slice( $templates, 0, $index + 1 );
	}

	$block_template = resolve_block_template( $type, $templates );

	if ( $block_template ) {
		if ( empty( $block_template->content ) && is_user_logged_in() ) {
			$_wp_current_template_content =
			sprintf(
				/* translators: %s: Template title */
				__( 'Empty template: %s' ),
				$block_template->title
			);
		} elseif ( ! empty( $block_template->content ) ) {
			$_wp_current_template_content = $block_template->content;
		}
		if ( isset( $_GET['_wp-find-template'] ) ) {
			wp_send_json_success( $block_template );
		}
	} else {
		if ( $template ) {
			return $template;
		}

		if ( 'index' === $type ) {
			if ( isset( $_GET['_wp-find-template'] ) ) {
				wp_send_json_error( array( 'message' => __( 'No matching template found.' ) ) );
			}
		} else {
			return ''; // So that the template loader keeps looking for templates.
		}
	}

	// Add hooks for template canvas.
	// Add viewport meta tag.
	add_action( 'wp_head', '_block_template_viewport_meta_tag', 0 );

	// Render title tag with content, regardless of whether theme has title-tag support.
	remove_action( 'wp_head', '_wp_render_title_tag', 1 );    // Remove conditional title tag rendering...
	add_action( 'wp_head', '_block_template_render_title_tag', 1 ); // ...and make it unconditional.

	// This file will be included instead of the theme's template file.
	return ABSPATH . WPINC . '/template-canvas.php';
}

/**
 * Return the correct 'wp_template' to render for the request template type.
 *
 * @access private
 * @since 5.8.0
 *
 * @param string   $template_type      The current template type.
 * @param string[] $template_hierarchy The current template hierarchy, ordered by priority.
 * @return WP_Block_Template|null template A template object, or null if none could be found.
 */
function resolve_block_template( $template_type, $template_hierarchy ) {
	if ( ! $template_type ) {
		return null;
	}

	if ( empty( $template_hierarchy ) ) {
		$template_hierarchy = array( $template_type );
	}

	$slugs = array_map(
		'_strip_template_file_suffix',
		$template_hierarchy
	);

	// Find all potential templates 'wp_template' post matching the hierarchy.
	$query     = array(
		'theme'    => wp_get_theme()->get_stylesheet(),
		'slug__in' => $slugs,
	);
	$templates = get_block_templates( $query );

	// Order these templates per slug priority.
	// Build map of template slugs to their priority in the current hierarchy.
	$slug_priorities = array_flip( $slugs );

	usort(
		$templates,
		static function ( $template_a, $template_b ) use ( $slug_priorities ) {
			return $slug_priorities[ $template_a->slug ] - $slug_priorities[ $template_b->slug ];
		}
	);

	return count( $templates ) ? $templates[0] : null;
}

/**
 * Displays title tag with content, regardless of whether theme has title-tag support.
 *
 * @access private
 * @since 5.8.0
 *
 * @see _wp_render_title_tag()
 */
function _block_template_render_title_tag() {
	echo '<title>' . wp_get_document_title() . '</title>' . "\n";
}

/**
 * Returns the markup for the current template.
 *
 * @access private
 * @since 5.8.0
 *
 * @global string   $_wp_current_template_content
 * @global WP_Embed $wp_embed
 *
 * @return string Block template markup.
 */
function get_the_block_template_html() {
	global $_wp_current_template_content;
	global $wp_embed;

	if ( ! $_wp_current_template_content ) {
		if ( is_user_logged_in() ) {
			return '<h1>' . esc_html__( 'No matching template found' ) . '</h1>';
		}
		return;
	}

	$content = $wp_embed->run_shortcode( $_wp_current_template_content );
	$content = $wp_embed->autoembed( $content );
	$content = do_blocks( $content );
	$content = wptexturize( $content );
	$content = wp_filter_content_tags( $content );
	$content = str_replace( ']]>', ']]&gt;', $content );

	// Wrap block template in .wp-site-blocks to allow for specific descendant styles
	// (e.g. `.wp-site-blocks > *`).
	return '<div class="wp-site-blocks">' . $content . '</div>';
}

/**
 * Renders a 'viewport' meta tag.
 *
 * This is hooked into {@see 'wp_head'} to decouple its output from the default template canvas.
 *
 * @access private
 * @since 5.8.0
 */
function _block_template_viewport_meta_tag() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Strips .php or .html suffix from template file names.
 *
 * @access private
 * @since 5.8.0
 *
 * @param string $template_file Template file name.
 * @return string Template file name without extension.
 */
function _strip_template_file_suffix( $template_file ) {
	return preg_replace( '/\.(php|html)$/', '', $template_file );
}

/**
 * Removes post details from block context when rendering a block template.
 *
 * @access private
 * @since 5.8.0
 *
 * @param array $context Default context.
 *
 * @return array Filtered context.
 */
function _block_template_render_without_post_block_context( $context ) {
	/*
	 * When loading a template directly and not through a page that resolves it,
	 * the top-level post ID and type context get set to that of the template.
	 * Templates are just the structure of a site, and they should not be available
	 * as post context because blocks like Post Content would recurse infinitely.
	 */
	if ( isset( $context['postType'] ) && 'wp_template' === $context['postType'] ) {
		unset( $context['postId'] );
		unset( $context['postType'] );
	}

	return $context;
}
