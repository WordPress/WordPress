<?php
/**
 * Block template loader functions.
 *
 * @package WordPress
 */

/**
 * Adds necessary hooks to resolve '_wp-find-template' requests.
 *
 * @access private
 * @since 5.9.0
 */
function _add_template_loader_filters() {
	if ( isset( $_GET['_wp-find-template'] ) && current_theme_supports( 'block-templates' ) ) {
		add_action( 'pre_get_posts', '_resolve_template_for_new_post' );
	}
}

/**
 * Renders a warning screen for empty block templates.
 *
 * @since 6.8.0
 *
 * @param WP_Block_Template $block_template The block template object.
 * @return string The warning screen HTML.
 */
function wp_render_empty_block_template_warning( $block_template ) {
	wp_enqueue_style( 'wp-empty-template-alert' );
	return sprintf(
		/* translators: %1$s: Block template title. %2$s: Empty template warning message. %3$s: Edit template link. %4$s: Edit template button label. */
		'<div id="wp-empty-template-alert">
			<h2>%1$s</h2>
			<p>%2$s</p>
			<a href="%3$s" class="wp-element-button">
				%4$s
			</a>
		</div>',
		esc_html( $block_template->title ),
		__( 'This page is blank because the template is empty. You can reset or customize it in the Site Editor.' ),
		get_edit_post_link( $block_template->wp_id, 'site-editor' ),
		__( 'Edit template' )
	);
}

/**
 * Finds a block template with equal or higher specificity than a given PHP template file.
 *
 * Internally, this communicates the block content that needs to be used by the template canvas through a global variable.
 *
 * @since 5.8.0
 * @since 6.3.0 Added `$_wp_current_template_id` global for editing of current template directly from the admin bar.
 *
 * @global string $_wp_current_template_content
 * @global string $_wp_current_template_id
 *
 * @param string   $template  Path to the template. See locate_template().
 * @param string   $type      Sanitized filename without extension.
 * @param string[] $templates A list of template candidates, in descending order of priority.
 * @return string The path to the Site Editor template canvas file, or the fallback PHP template.
 */
function locate_block_template( $template, $type, array $templates ) {
	global $_wp_current_template_content, $_wp_current_template_id;

	if ( ! current_theme_supports( 'block-templates' ) ) {
		return $template;
	}

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

		// If the template hierarchy algorithm has successfully located a PHP template file,
		// we will only consider block templates with higher or equal specificity.
		$templates = array_slice( $templates, 0, $index + 1 );
	}

	$block_template = resolve_block_template( $type, $templates, $template );

	if ( $block_template ) {
		$_wp_current_template_id = $block_template->id;

		if ( empty( $block_template->content ) ) {
			if ( is_user_logged_in() ) {
				$_wp_current_template_content = wp_render_empty_block_template_warning( $block_template );
			} else {
				if ( $block_template->has_theme_file ) {
					// Show contents from theme template if user is not logged in.
					$theme_template               = _get_block_template_file( 'wp_template', $block_template->slug );
					$_wp_current_template_content = file_get_contents( $theme_template['path'] );
				} else {
					$_wp_current_template_content = $block_template->content;
				}
			}
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
 * Returns the correct 'wp_template' to render for the request template type.
 *
 * @access private
 * @since 5.8.0
 * @since 5.9.0 Added the `$fallback_template` parameter.
 *
 * @param string   $template_type      The current template type.
 * @param string[] $template_hierarchy The current template hierarchy, ordered by priority.
 * @param string   $fallback_template  A PHP fallback template to use if no matching block template is found.
 * @return WP_Block_Template|null template A template object, or null if none could be found.
 */
function resolve_block_template( $template_type, $template_hierarchy, $fallback_template ) {
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

	$theme_base_path        = get_stylesheet_directory() . DIRECTORY_SEPARATOR;
	$parent_theme_base_path = get_template_directory() . DIRECTORY_SEPARATOR;

	// Is the active theme a child theme, and is the PHP fallback template part of it?
	if (
		str_starts_with( $fallback_template, $theme_base_path ) &&
		! str_contains( $fallback_template, $parent_theme_base_path )
	) {
		$fallback_template_slug = substr(
			$fallback_template,
			// Starting position of slug.
			strpos( $fallback_template, $theme_base_path ) + strlen( $theme_base_path ),
			// Remove '.php' suffix.
			-4
		);

		// Is our candidate block template's slug identical to our PHP fallback template's?
		if (
			count( $templates ) &&
			$fallback_template_slug === $templates[0]->slug &&
			'theme' === $templates[0]->source
		) {
			// Unfortunately, we cannot trust $templates[0]->theme, since it will always
			// be set to the active theme's slug by _build_block_template_result_from_file(),
			// even if the block template is really coming from the active theme's parent.
			// (The reason for this is that we want it to be associated with the active theme
			// -- not its parent -- once we edit it and store it to the DB as a wp_template CPT.)
			// Instead, we use _get_block_template_file() to locate the block template file.
			$template_file = _get_block_template_file( 'wp_template', $fallback_template_slug );
			if ( $template_file && get_template() === $template_file['theme'] ) {
				// The block template is part of the parent theme, so we
				// have to give precedence to the child theme's PHP template.
				array_shift( $templates );
			}
		}
	}

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
 * @global string   $_wp_current_template_id
 * @global string   $_wp_current_template_content
 * @global WP_Embed $wp_embed                     WordPress Embed object.
 * @global WP_Query $wp_query                     WordPress Query object.
 *
 * @return string Block template markup.
 */
function get_the_block_template_html() {
	global $_wp_current_template_id, $_wp_current_template_content, $wp_embed, $wp_query;

	if ( ! $_wp_current_template_content ) {
		if ( is_user_logged_in() ) {
			return '<h1>' . esc_html__( 'No matching template found' ) . '</h1>';
		}
		return;
	}

	$content = $wp_embed->run_shortcode( $_wp_current_template_content );
	$content = $wp_embed->autoembed( $content );
	$content = shortcode_unautop( $content );
	$content = do_shortcode( $content );

	/*
	 * Most block themes omit the `core/query` and `core/post-template` blocks in their singular content templates.
	 * While this technically still works since singular content templates are always for only one post, it results in
	 * the main query loop never being entered which causes bugs in core and the plugin ecosystem.
	 *
	 * The workaround below ensures that the loop is started even for those singular templates. The while loop will by
	 * definition only go through a single iteration, i.e. `do_blocks()` is only called once. Additional safeguard
	 * checks are included to ensure the main query loop has not been tampered with and really only encompasses a
	 * single post.
	 *
	 * Even if the block template contained a `core/query` and `core/post-template` block referencing the main query
	 * loop, it would not cause errors since it would use a cloned instance and go through the same loop of a single
	 * post, within the actual main query loop.
	 *
	 * This special logic should be skipped if the current template does not come from the current theme, in which case
	 * it has been injected by a plugin by hijacking the block template loader mechanism. In that case, entirely custom
	 * logic may be applied which is unpredictable and therefore safer to omit this special handling on.
	 */
	if (
		$_wp_current_template_id &&
		str_starts_with( $_wp_current_template_id, get_stylesheet() . '//' ) &&
		is_singular() &&
		1 === $wp_query->post_count &&
		have_posts()
	) {
		while ( have_posts() ) {
			the_post();
			$content = do_blocks( $content );
		}
	} else {
		$content = do_blocks( $content );
	}

	$content = wptexturize( $content );
	$content = convert_smilies( $content );
	$content = wp_filter_content_tags( $content, 'template' );
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

/**
 * Sets the current WP_Query to return auto-draft posts.
 *
 * The auto-draft status indicates a new post, so allow the the WP_Query instance to
 * return an auto-draft post for template resolution when editing a new post.
 *
 * @access private
 * @since 5.9.0
 *
 * @param WP_Query $wp_query Current WP_Query instance, passed by reference.
 */
function _resolve_template_for_new_post( $wp_query ) {
	if ( ! $wp_query->is_main_query() ) {
		return;
	}

	remove_filter( 'pre_get_posts', '_resolve_template_for_new_post' );

	// Pages.
	$page_id = isset( $wp_query->query['page_id'] ) ? $wp_query->query['page_id'] : null;

	// Posts, including custom post types.
	$p = isset( $wp_query->query['p'] ) ? $wp_query->query['p'] : null;

	$post_id = $page_id ? $page_id : $p;
	$post    = get_post( $post_id );

	if (
		$post &&
		'auto-draft' === $post->post_status &&
		current_user_can( 'edit_post', $post->ID )
	) {
		$wp_query->set( 'post_status', 'auto-draft' );
	}
}

/**
 * Register a block template.
 *
 * @since 6.7.0
 *
 * @param string       $template_name  Template name in the form of `plugin_uri//template_name`.
 * @param array|string $args           {
 *     @type string        $title                 Optional. Title of the template as it will be shown in the Site Editor
 *                                                and other UI elements.
 *     @type string        $description           Optional. Description of the template as it will be shown in the Site
 *                                                Editor.
 *     @type string        $content               Optional. Default content of the template that will be used when the
 *                                                template is rendered or edited in the editor.
 *     @type string[]      $post_types            Optional. Array of post types to which the template should be available.
 *     @type string        $plugin                Optional. Slug of the plugin that registers the template.
 * }
 * @return WP_Block_Template|WP_Error The registered template object on success, WP_Error object on failure.
 */
function register_block_template( $template_name, $args = array() ) {
	return WP_Block_Templates_Registry::get_instance()->register( $template_name, $args );
}

/**
 * Unregister a block template.
 *
 * @since 6.7.0
 *
 * @param string $template_name Template name in the form of `plugin_uri//template_name`.
 * @return WP_Block_Template|WP_Error The unregistered template object on success, WP_Error object on failure or if the
 *                                    template doesn't exist.
 */
function unregister_block_template( $template_name ) {
	return WP_Block_Templates_Registry::get_instance()->unregister( $template_name );
}
