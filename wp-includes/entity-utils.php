<?php
/**
 * Utilities shared by the Entity Actions and Entity Fields APIs.
 *
 * @package WordPress
 * @since 7.2.0
 */

/**
 * Adds the paths of an entity route for the post types the editors list.
 *
 * The post editor lists the post type of the edited post. The site editor
 * lists that of the edited post, if any, and those of the screen it opens on.
 * Post types that are not registered are skipped, and each post type is added
 * once.
 *
 * @since 7.2.0
 * @access private
 *
 * @param mixed  $paths   REST API paths to preload.
 * @param mixed  $context Block editor context.
 * @param string $route   The entity route, such as `/wp/v2/entity-fields`.
 * @return mixed The paths with one path per post type added. An empty value
 *               that is not an array becomes an array. Any other value that
 *               is not an array, or a context that is not a block editor
 *               context, returns `$paths` unchanged.
 */
function _wp_add_entity_preload_paths( $paths, $context, $route ) {
	if ( empty( $paths ) && ! is_array( $paths ) ) {
		$paths = array();
	}

	if ( ! is_array( $paths ) || ! $context instanceof WP_Block_Editor_Context ) {
		return $paths;
	}

	$post_types = array();
	if ( 'core/edit-post' === $context->name && isset( $context->post ) ) {
		$post_types[] = $context->post->post_type;
	} elseif ( 'core/edit-site' === $context->name ) {
		if ( isset( $context->post ) ) {
			$post_types[] = $context->post->post_type;
		}

		/*
		 * Adds the post types the screen lists or edits, from the first
		 * segment of its `p` query arg. The screens are the routes in
		 * packages/edit-site/src/components/site-editor-routes in Gutenberg.
		 * The patterns screen lists both patterns and template parts.
		 */
		$screen_post_types = array(
			'page'             => array( 'page' ),
			'template'         => array( 'wp_template' ),
			'wp_template'      => array( 'wp_template' ),
			'pattern'          => array( 'wp_block', 'wp_template_part' ),
			'wp_block'         => array( 'wp_block' ),
			'wp_template_part' => array( 'wp_template_part' ),
		);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only picks what to preload.
		$screen   = isset( $_GET['p'] ) && is_string( $_GET['p'] ) ? sanitize_text_field( wp_unslash( $_GET['p'] ) ) : '';
		$segments = explode( '/', trim( $screen, '/' ) );
		if ( isset( $screen_post_types[ $segments[0] ] ) ) {
			$post_types = array_merge( $post_types, $screen_post_types[ $segments[0] ] );
		}
	}

	foreach ( array_unique( $post_types ) as $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}
		$paths[] = add_query_arg(
			array(
				'kind' => 'postType',
				'name' => $post_type,
			),
			$route
		);
	}

	return $paths;
}

/**
 * Declares the script modules of an entity API as dependencies of the editor
 * script.
 *
 * The editor imports the script modules of an entity on demand, with a dynamic
 * `import()` the browser resolves through the import map of the page. Listing
 * the modules as dynamic `module_dependencies` of the `wp-editor` script adds
 * them to the import map of every page that loads the editor script: the post
 * editor, the site editor, and the pages booted by `@wordpress/boot`, whose
 * prerequisites depend on it. A dynamic dependency is only listed in the
 * import map; the module is fetched when it is imported.
 *
 * The modules are read with `$get_modules`, which reads the registry of the
 * API and so fires its init action, registering the entities and their script
 * modules on demand. It is only called on the pages that load the editor
 * script, not on every admin request (heartbeat, Ajax, other screens). The
 * callers run on `admin_footer`, the last action before the import map is
 * printed that fires both in the admin template and in the pages rendered
 * outside it, which print the import map themselves. By then `init` has
 * completed, so the post types and supports the registrations derive from are
 * final. The registries only accept registrations while their action fires,
 * so the modules declared here are those of everything the page can show.
 *
 * @since 7.2.0
 * @access private
 *
 * @param WP_Scripts|null $scripts     The scripts registry. Null for the global one.
 * @param callable        $get_modules Returns the module ids of the API, keyed
 *                                     by kind, then by name.
 */
function _wp_add_entity_modules_to_editor_script( $scripts, $get_modules ) {
	if ( ! $scripts instanceof WP_Scripts ) {
		$scripts = wp_scripts();
	}

	if ( ! $scripts->query( 'wp-editor', 'enqueued' ) && ! $scripts->query( 'wp-editor', 'done' ) ) {
		return;
	}

	$kinds = call_user_func( $get_modules );
	if ( empty( $kinds ) ) {
		return;
	}

	$dependencies = $scripts->get_data( 'wp-editor', 'module_dependencies' );
	$dependencies = is_array( $dependencies ) ? $dependencies : array();

	$declared = array();
	foreach ( $dependencies as $dependency ) {
		$declared[] = is_array( $dependency ) ? $dependency['id'] : $dependency;
	}

	$added = false;
	foreach ( $kinds as $entities ) {
		foreach ( $entities as $modules ) {
			foreach ( $modules as $module ) {
				if ( ! in_array( $module, $declared, true ) ) {
					$dependencies[] = array(
						'id'     => $module,
						'import' => 'dynamic',
					);
					$declared[]     = $module;
					$added          = true;
				}
			}
		}
	}

	if ( $added ) {
		// `add_data()` replaces the value, hence the merge above.
		$scripts->add_data( 'wp-editor', 'module_dependencies', $dependencies );
	}
}
