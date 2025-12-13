<?php

// Migrate existing "edited" templates. By existing, it means that the template
// is active.
function gutenberg_get_migrated_active_templates() {
	// Query all templates in the database. See `get_block_templates`.
	$wp_query_args = array(
		'post_status'         => 'publish',
		'post_type'           => 'wp_template',
		'posts_per_page'      => -1,
		'no_found_rows'       => true,
		'lazy_load_term_meta' => false,
		'tax_query'           => array(
			array(
				'taxonomy' => 'wp_theme',
				'field'    => 'name',
				'terms'    => get_stylesheet(),
			),
		),
		// Only get templates that are not inactive by default.
		'meta_query'          => array(
			'relation' => 'OR',
			array(
				'key'     => 'is_inactive_by_default',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'is_inactive_by_default',
				'value'   => false,
				'compare' => '=',
			),
		),
	);

	$template_query   = new WP_Query( $wp_query_args );
	$active_templates = array();

	foreach ( $template_query->posts as $post ) {
		$active_templates[ $post->post_name ] = $post->ID;
	}

	return $active_templates;
}

require_once __DIR__ . '/class-gutenberg-rest-old-templates-controller.php';

// How does this work?
// 1. For wp_template, we remove the custom templates controller, so it becomes
//    a normal posts endpoint, modified slightly to allow auto-drafts.
add_filter( 'register_post_type_args', 'gutenberg_modify_wp_template_post_type_args', 10, 2 );
function gutenberg_modify_wp_template_post_type_args( $args, $post_type ) {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return $args;
	}
	if ( 'wp_template' === $post_type ) {
		$args['rest_base']                       = 'created-templates';
		$args['rest_controller_class']           = 'WP_REST_Posts_Controller';
		$args['autosave_rest_controller_class']  = null;
		$args['revisions_rest_controller_class'] = null;
		$args['supports']                        = array_merge( $args['supports'], array( 'custom-fields' ) );
	}
	return $args;
}

// 2. We maintain the routes for /templates and /templates/lookup. I think we'll
//    need to deprecate /templates eventually, but we'll still want to be able
//    to lookup the active template for a specific slug, and probably get a list
//    of all _active_ templates. For that we can keep /lookup.
// Priority 100, after `create_initial_rest_routes`.
add_action( 'rest_api_init', 'gutenberg_maintain_templates_routes', 100 );

/**
 * @global array $wp_post_types List of post types.
 */
function gutenberg_maintain_templates_routes() {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return;
	}
	// This should later be changed in core so we don't need initialize
	// WP_REST_Templates_Controller with a post type.
	global $wp_post_types;
	// Register the old templates endpoints. The WP_REST_Templates_Controller
	// and sub-controllers used to be linked to the wp_template post type, but
	// are no longer. They still require a post type object when contructing the
	// class. To maintain backward and changes to these controller classes, we
	// make use that the wp_template post type has the right information it
	// needs.
	$wp_post_types['wp_template']->rest_base = 'templates';
	// Store the classes so they can be restored.
	$original_rest_controller_class           = $wp_post_types['wp_template']->rest_controller_class;
	$original_autosave_rest_controller_class  = $wp_post_types['wp_template']->autosave_rest_controller_class;
	$original_revisions_rest_controller_class = $wp_post_types['wp_template']->revisions_rest_controller_class;
	// Temporarily set the old classes.
	$wp_post_types['wp_template']->rest_controller_class           = 'WP_REST_Templates_Controller';
	$wp_post_types['wp_template']->autosave_rest_controller_class  = 'WP_REST_Template_Autosaves_Controller';
	$wp_post_types['wp_template']->revisions_rest_controller_class = 'WP_REST_Template_Revisions_Controller';
	// Initialize the controllers. The order is important: the autosave
	// controller needs both the templates and revisions controllers.
	$controller                                    = new Gutenberg_REST_Old_Templates_Controller( 'wp_template' );
	$wp_post_types['wp_template']->rest_controller = $controller;
	$revisions_controller                          = new WP_REST_Template_Revisions_Controller( 'wp_template' );
	$wp_post_types['wp_template']->revisions_rest_controller = $revisions_controller;
	$autosaves_controller                                    = new WP_REST_Template_Autosaves_Controller( 'wp_template' );
	// Unset the controller cache, it will be re-initialized when
	// get_rest_controller is called.
	$wp_post_types['wp_template']->rest_controller           = null;
	$wp_post_types['wp_template']->revisions_rest_controller = null;
	// Restore the original classes.
	$wp_post_types['wp_template']->rest_controller_class           = $original_rest_controller_class;
	$wp_post_types['wp_template']->autosave_rest_controller_class  = $original_autosave_rest_controller_class;
	$wp_post_types['wp_template']->revisions_rest_controller_class = $original_revisions_rest_controller_class;
	// Restore the original base.
	$wp_post_types['wp_template']->rest_base = 'created-templates';

	// Register the old routes.
	$autosaves_controller->register_routes();
	$revisions_controller->register_routes();
	$controller->register_routes();

	$registered_template_controller = new Gutenberg_REST_Static_Templates_Controller();
	$registered_template_controller->register_routes();

	// Add the same field as wp_registered_template.
	register_rest_field(
		'wp_template',
		'theme',
		array(
			'get_callback' => function ( $post_arr ) {
				// add_additional_fields_to_object is also called for the old
				// templates controller, so we need to check if the id is an
				// integer to make sure it's the proper post type endpoint.
				if ( ! is_int( $post_arr['id'] ) ) {
					$template = get_block_template( $post_arr['id'], 'wp_template' );
					return $template ? $template->theme : null;
				}
				$terms = get_the_terms( $post_arr['id'], 'wp_theme' );
				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					return null;
				}
				return $terms[0]->slug;
			},
		)
	);

	// Allow setting the is_wp_suggestion meta field, which partly determines if
	// a template is a custom template.
	register_post_meta(
		'wp_template',
		'is_wp_suggestion',
		array(
			'type'         => 'boolean',
			'show_in_rest' => true,
			'single'       => true,
		)
	);
}

// 3. We need a route to get that raw static templates from themes and plugins.
//    I registered this as a post type route because right now the
//    EditorProvider assumes templates are posts.
add_action( 'init', 'gutenberg_setup_static_template' );

/**
 * @global array $wp_post_types List of post types.
 */
function gutenberg_setup_static_template() {
	register_setting(
		'reading',
		'active_templates',
		array(
			'type'         => 'object',
			// Do not set the default value to an empty array! For some reason
			// that will prevent the option from being set to an empty array.
			'show_in_rest' => array(
				'schema' => array(
					'type'                 => 'object',
					// Properties can be integers, strings, or false
					// (deactivated).
					'additionalProperties' => true,
				),
			),
			'label'        => 'Active Templates',
		)
	);
}

add_filter( 'pre_wp_unique_post_slug', 'gutenberg_allow_template_slugs_to_be_duplicated', 10, 5 );
function gutenberg_allow_template_slugs_to_be_duplicated( $override, $slug, $post_id, $post_status, $post_type ) {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return $override;
	}
	return 'wp_template' === $post_type ? $slug : $override;
}

function gutenberg_get_registered_block_templates( $query ) {
	$template_files = _get_block_templates_files( 'wp_template', $query );
	$query_result   = array();

	// _get_block_templates_files seems broken in core, it does not object the
	// query.
	if ( isset( $query['slug__in'] ) && is_array( $query['slug__in'] ) ) {
		$template_files = array_filter(
			$template_files,
			function ( $template_file ) use ( $query ) {
				return in_array( $template_file['slug'], $query['slug__in'], true );
			}
		);
	}

	foreach ( $template_files as $template_file ) {
		$query_result[] = _build_block_template_result_from_file( $template_file, 'wp_template' );
	}

	// Add templates registered in the template registry. Filtering out the ones which have a theme file.
	$registered_templates          = WP_Block_Templates_Registry::get_instance()->get_by_query( $query );
	$matching_registered_templates = array_filter(
		$registered_templates,
		function ( $registered_template ) use ( $template_files ) {
			foreach ( $template_files as $template_file ) {
				if ( $template_file['slug'] === $registered_template->slug ) {
					return false;
				}
			}
			return true;
		}
	);

	$query_result = array_merge( $query_result, $matching_registered_templates );

	/**
	 * Filters the array of queried block templates array after they've been fetched.
	 *
	 * @since 5.9.0
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
	return apply_filters( 'get_block_templates', $query_result, $query, 'wp_template' );
}

$template_types = array(
	'index',
	'404',
	'search',
	'frontpage',
	'home',
	'privacypolicy',
	'taxonomy',
	'attachment',
	'single',
	'page',
	'singular',
	'category',
	'tag',
	'author',
	'date',
	'archive',
);

// Unfortunately, there is no general filter.
foreach ( $template_types as $template_type ) {
	add_filter( "{$template_type}_template", 'gutenberg_get_template', 0, 3 );
}

function gutenberg_get_template( $template, $type, $templates ) {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return $template;
	}
	$template = locate_template( $templates );
	return gutenberg_locate_block_template( $template, $type, $templates );
}

///////////////////////////////////////////////////////////////////////
// This function is a copy of core's, except for the marked section. //
///////////////////////////////////////////////////////////////////////
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
function gutenberg_resolve_block_template( $template_type, $template_hierarchy, $fallback_template ) {
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

	//////////////////////////////
	// START CORE MODIFICATIONS //
	//////////////////////////////

	$object_id         = get_queried_object_id();
	$specific_template = $object_id ? get_page_template_slug( $object_id ) : null;
	$active_templates  = (array) get_option( 'active_templates', array() );

	// We expect one template for each slug. Use the active template if it is
	// set and exists. Otherwise use the static template.
	$templates       = array();
	$remaining_slugs = array();

	foreach ( $slugs as $slug ) {
		if ( $slug === $specific_template || empty( $active_templates[ $slug ] ) ) {
			$remaining_slugs[] = $slug;
			continue;
		}

		// TODO: it need to be possible to set a static template as active.
		$post = get_post( $active_templates[ $slug ] );
		if ( ! $post || 'publish' !== $post->post_status ) {
			$remaining_slugs[] = $slug;
			continue;
		}

		$template = _build_block_template_result_from_post( $post );

		// Ensure the active templates are associated with the active theme.
		// See _build_block_template_object_from_post_object.
		if ( get_stylesheet() !== $template->theme ) {
			$remaining_slugs[] = $slug;
			continue;
		}

		$templates[] = $template;
	}

	// Apply the filter to the active templates for backward compatibility.
	if ( ! empty( $templates ) ) {
		$templates = apply_filters(
			'get_block_templates',
			$templates,
			array(
				'slug__in' => array_map(
					function ( $template ) {
						return $template->slug;
					},
					$templates
				),
			),
			'wp_template'
		);
	}

	// For any remaining slugs, use the static template.
	if ( ! empty( $remaining_slugs ) ) {
		$query     = array(
			'slug__in' => $remaining_slugs,
		);
		$templates = array_merge( $templates, gutenberg_get_registered_block_templates( $query ) );
	}

	if ( $specific_template && in_array( $specific_template, $remaining_slugs, true ) ) {
		$templates = array_merge( $templates, get_block_templates( array( 'slug__in' => array( $specific_template ) ) ) );
	}

	////////////////////////////
	// END CORE MODIFICATIONS //
	////////////////////////////

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

///////////////////////////////////////////////////////////////////////
// This function is a copy of core's, except for the marked section. //
///////////////////////////////////////////////////////////////////////
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
function gutenberg_locate_block_template( $template, $type, array $templates ) {
	global $_wp_current_template_content, $_wp_current_template_id;

	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return $template;
	}

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

	//////////////////////////////
	// START CORE MODIFICATIONS //
	//////////////////////////////
	$block_template = gutenberg_resolve_block_template( $type, $templates, $template );
	////////////////////////////
	// END CORE MODIFICATIONS //
	////////////////////////////

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

// We need to set the theme for the template when it's created. See:
// https://github.com/WordPress/wordpress-develop/blob/b2c8d8d2c8754cab5286b06efb4c11e2b6aa92d5/src/wp-includes/rest-api/endpoints/class-wp-rest-templates-controller.php#L571-L578
// Priority 9 so it runs before default hooks like
// `inject_ignored_hooked_blocks_metadata_attributes`.
remove_action( 'rest_pre_insert_wp_template', 'wp_assign_new_template_to_theme', 9 );
add_action( 'rest_pre_insert_wp_template', 'gutenberg_set_active_template_theme', 9, 2 );
function gutenberg_set_active_template_theme( $changes, $request ) {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return $changes;
	}
	if ( str_starts_with( $request->get_route(), '/wp/v2/templates' ) ) {
		return $changes;
	}
	$changes->tax_input = array(
		'wp_theme' => isset( $request['theme'] ) ? $request['theme'] : get_stylesheet(),
	);
	// All new templates saved will receive meta so we can distinguish between
	// templates created the old way as edits and templates created the new way.
	$changes->meta_input = array(
		'is_inactive_by_default' => true,
	);
	return $changes;
}

add_action( 'save_post_wp_template', 'gutenberg_maybe_update_active_templates' );
function gutenberg_maybe_update_active_templates( $post_id ) {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return;
	}
	$post                   = get_post( $post_id );
	$is_inactive_by_default = get_post_meta( $post_id, 'is_inactive_by_default', true );
	if ( $is_inactive_by_default ) {
		return;
	}
	$active_templates                     = get_option( 'active_templates', array() );
	$active_templates[ $post->post_name ] = $post->ID;
	update_option( 'active_templates', $active_templates );
}

add_action( 'pre_get_block_template', 'gutenberg_get_block_template', 10, 3 );

///////////////////////////////////////////////////////////////////////
// This function is a copy of core's, except for the marked section. //
///////////////////////////////////////////////////////////////////////
function gutenberg_get_block_template( $output, $id, $template_type ) {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return $output;
	}
	if ( 'wp_template' !== $template_type ) {
		return $output;
	}
	$parts = explode( '//', $id, 2 );
	if ( count( $parts ) < 2 ) {
		return null;
	}
	list( $theme, $slug ) = $parts;

	//////////////////////////////
	// START CORE MODIFICATIONS //
	//////////////////////////////
	$active_templates = get_option( 'active_templates', array() );

	if ( ! empty( $active_templates[ $slug ] ) ) {
		if ( is_int( $active_templates[ $slug ] ) ) {
			$post = get_post( $active_templates[ $slug ] );
			if ( $post && 'publish' === $post->post_status ) {
				$template = _build_block_template_result_from_post( $post );

				if ( ! is_wp_error( $template ) && $theme === $template->theme ) {
					return $template;
				}
			}
		}
	}
	////////////////////////////
	// END CORE MODIFICATIONS //
	////////////////////////////

	$wp_query_args  = array(
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
	$template_query = new WP_Query( $wp_query_args );
	$posts          = $template_query->posts;

	if ( count( $posts ) > 0 ) {
		$template = _build_block_template_result_from_post( $posts[0] );

		//////////////////////////////
		// START CORE MODIFICATIONS //
		//////////////////////////////
		// Custom templates don't need to be activated, so if it's a custom
		// template, return it.
		if ( ! is_wp_error( $template ) && $template->is_custom ) {
			return $template;
		}
		////////////////////////////
		// END CORE MODIFICATIONS //
		////////////////////////////
	}

	$block_template = get_block_file_template( $id, $template_type );

	/**
	 * Filters the queried block template object after it's been fetched.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Block_Template|null $block_template The found block template, or null if there isn't one.
	 * @param string                 $id             Template unique identifier (example: 'theme_slug//template_slug').
	 * @param string                 $template_type  Template type. Either 'wp_template' or 'wp_template_part'.
	 */
	return apply_filters( 'get_block_template', $block_template, $id, $template_type );
}

add_action( 'pre_get_block_templates', 'gutenberg_get_block_templates', 10, 3 );

///////////////////////////////////////////////////////////////////////
// This function is a copy of core's, except for the marked section. //
///////////////////////////////////////////////////////////////////////
function gutenberg_get_block_templates( $output, $query = array(), $template_type = 'wp_template' ) {
	// Check active_templates experiment status.
	if ( ! gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		return $output;
	}
	if ( 'wp_template' !== $template_type ) {
		return $output;
	}

	$post_type     = isset( $query['post_type'] ) ? $query['post_type'] : '';
	$wp_query_args = array(
		'post_status'         => array( 'auto-draft', 'draft', 'publish' ),
		'post_type'           => $template_type,
		'posts_per_page'      => -1,
		'no_found_rows'       => true,
		'lazy_load_term_meta' => false,
		'tax_query'           => array(
			array(
				'taxonomy' => 'wp_theme',
				'field'    => 'name',
				'terms'    => get_stylesheet(),
			),
		),
	);

	if ( ! empty( $query['slug__in'] ) ) {
		$wp_query_args['post_name__in']  = $query['slug__in'];
		$wp_query_args['posts_per_page'] = count( array_unique( $query['slug__in'] ) );
	}

	// This is only needed for the regular templates/template parts post type listing and editor.
	if ( isset( $query['wp_id'] ) ) {
		$wp_query_args['p'] = $query['wp_id'];
	} else {
		$wp_query_args['post_status'] = 'publish';
	}

	//////////////////////////////
	// START CORE MODIFICATIONS //
	//////////////////////////////
	$active_templates = get_option( 'active_templates', array() );
	////////////////////////////
	// END CORE MODIFICATIONS //
	////////////////////////////

	$template_query = new WP_Query( $wp_query_args );
	$query_result   = array();
	foreach ( $template_query->posts as $post ) {
		$template = _build_block_template_result_from_post( $post );

		if ( is_wp_error( $template ) ) {
			continue;
		}

		if ( $post_type && ! $template->is_custom ) {
			continue;
		}

		if (
			$post_type &&
			isset( $template->post_types ) &&
			! in_array( $post_type, $template->post_types, true )
		) {
			continue;
		}

		//////////////////////////////
		// START CORE MODIFICATIONS //
		//////////////////////////////
		if ( $template->is_custom || isset( $query['wp_id'] ) ) {
			// Custom templates don't need to be activated, leave them be.
			// Also don't filter out templates when querying by wp_id.
			$query_result[] = $template;
		} elseif ( isset( $active_templates[ $template->slug ] ) && $active_templates[ $template->slug ] === $post->ID ) {
			// Only include active templates.
			$query_result[] = $template;
		}
		////////////////////////////
		// END CORE MODIFICATIONS //
		////////////////////////////
	}

	if ( ! isset( $query['wp_id'] ) ) {
		/*
		 * If the query has found some user templates, those have priority
		 * over the theme-provided ones, so we skip querying and building them.
		 */
		$query['slug__not_in'] = wp_list_pluck( $query_result, 'slug' );
		/*
		 * We need to unset the post_type query param because some templates
		 * would be excluded otherwise, like `page.html` when looking for
		 * `page` templates. We need all templates so we can exclude duplicates
		 * from plugin-registered templates.
		 * See: https://github.com/WordPress/gutenberg/issues/65584
		 */
		$template_files_query = $query;
		unset( $template_files_query['post_type'] );
		$template_files = _get_block_templates_files( $template_type, $template_files_query );
		foreach ( $template_files as $template_file ) {
			// If the query doesn't specify a post type, or it does and the template matches the post type, add it.
			if (
				! isset( $query['post_type'] ) ||
				(
					isset( $template_file['postTypes'] ) &&
					in_array( $query['post_type'], $template_file['postTypes'], true )
				)
			) {
				$query_result[] = _build_block_template_result_from_file( $template_file, $template_type );
			} elseif ( ! isset( $template_file['postTypes'] ) ) {
				// The custom templates with no associated post types are available for all post types as long
				// as they are not default templates.
				$candidate              = _build_block_template_result_from_file( $template_file, $template_type );
				$default_template_types = get_default_block_template_types();
				if ( ! isset( $default_template_types[ $candidate->slug ] ) ) {
					$query_result[] = $candidate;
				}
			}
		}

		if ( 'wp_template' === $template_type ) {
			// Add templates registered in the template registry. Filtering out the ones which have a theme file.
			$registered_templates          = WP_Block_Templates_Registry::get_instance()->get_by_query( $query );
			$matching_registered_templates = array_filter(
				$registered_templates,
				function ( $registered_template ) use ( $template_files ) {
					foreach ( $template_files as $template_file ) {
						if ( $template_file['slug'] === $registered_template->slug ) {
							return false;
						}
					}
					return true;
				}
			);

			$matching_registered_templates = array_map(
				function ( $template ) {
					$template->content = apply_block_hooks_to_content(
						$template->content,
						$template,
						'insert_hooked_blocks_and_set_ignored_hooked_blocks_metadata'
					);
					return $template;
				},
				$matching_registered_templates
			);

			$query_result = array_merge( $query_result, $matching_registered_templates );
		}
	}

	/**
	 * Filters the array of queried block templates array after they've been fetched.
	 *
	 * @since 5.9.0
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
	return apply_filters( 'get_block_templates', $query_result, $query, $template_type );
}
