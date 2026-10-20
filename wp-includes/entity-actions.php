<?php
/**
 * Entity actions API.
 *
 * An action definition is the serializable subset of the DataViews Action
 * API: every property that is plain data (`id`, `label`, `isPrimary`,
 * `supportsBulk`, `context`, `modalHeader`, …). Properties that are
 * JavaScript callbacks, components, or elements (`callback`, `RenderModal`,
 * `isEligible`, `icon`, a `label` that depends on the items, …) cannot be
 * declared here. They are provided by a script module registered for the
 * entity along with its actions: its default export maps action ids to the
 * properties of each action that are JavaScript. An entity can have several
 * modules. Each module applies to the actions it was registered with, see
 * {@see wp_get_registered_entity_action_modules()}.
 *
 * Actions are registered on the `wp_entity_actions_api_init` action, and only there,
 * with {@see wp_register_entity_actions()} and {@see wp_unregister_entity_actions()}. The
 * other functions read the registry. WordPress core registers its own
 * actions the same way, from packages/core-actions.
 *
 * @package WordPress
 * @subpackage Entity Actions
 * @since 7.2.0
 */

/**
 * Registers actions for an entity, see
 * {@see WP_Entity_Actions_Registry::register()}.
 *
 * It only runs on the `wp_entity_actions_api_init` action.
 *
 * @since 7.2.0
 *
 * @param string      $origin        Who registers the actions: `core`, or
 *                                   the slug of the plugin or theme.
 * @param string      $kind          The entity kind (e.g. `postType`).
 * @param string      $name          The entity name (e.g. `page`).
 * @param array[]     $actions       The list of action definitions, each with an `id`.
 * @param string|null $script_module The id of the script module providing
 *                                   the JavaScript parts of the actions, if any.
 * @return string[] The ids of the actions registered, in the order of the
 *                  call. Empty when none is.
 */
function wp_register_entity_actions( $origin, $kind, $name, $actions, $script_module = null ) {
	return WP_Entity_Actions_Registry::get_instance()->register( $origin, $kind, $name, $actions, $script_module );
}

/**
 * Unregisters actions of an entity, see
 * {@see WP_Entity_Actions_Registry::unregister()}.
 *
 * It only runs on the `wp_entity_actions_api_init` action.
 *
 * @since 7.2.0
 *
 * @param string        $kind The entity kind (e.g. `postType`).
 * @param string        $name The entity name (e.g. `page`).
 * @param string[]|null $ids  The ids of the actions to unregister. Default
 *                            null, every action of the entity.
 * @return array[] The list of the definitions unregistered, in
 *                 registration order. Empty when none is.
 */
function wp_unregister_entity_actions( $kind, $name, $ids = null ) {
	return WP_Entity_Actions_Registry::get_instance()->unregister( $kind, $name, $ids );
}

/**
 * Returns the actions registered for the given entity.
 *
 * @since 7.2.0
 *
 * @param string $kind The entity kind (e.g. `postType`).
 * @param string $name The entity name (e.g. `page`).
 * @return array[] The list of registered action definitions, in
 *                 registration order. Empty until the `init` action has
 *                 completed: a read before then is refused.
 */
function wp_get_registered_entity_actions( $kind, $name ) {
	return WP_Entity_Actions_Registry::get_instance()->get_registered( $kind, $name );
}

/**
 * Returns the ids of the script modules registered for the given entity.
 *
 * @since 7.2.0
 *
 * @param string $kind The entity kind (e.g. `postType`).
 * @param string $name The entity name (e.g. `page`).
 * @return array<string, string[]> The ids of the actions each module
 *                                 applies to, keyed by module id, in
 *                                 registration order. Empty until the
 *                                 `init` action has completed: a read
 *                                 before then is refused.
 */
function wp_get_registered_entity_action_modules( $kind, $name ) {
	return WP_Entity_Actions_Registry::get_instance()->get_registered_action_modules( $kind, $name );
}

/**
 * Returns the ids of all script modules registered for any entity.
 *
 * @since 7.2.0
 *
 * @return array<string, array<string, string[]>> The lists of module ids,
 *                                                keyed by kind, then by
 *                                                name. Empty until the
 *                                                `init` action has
 *                                                completed: a read before
 *                                                then is refused.
 */
function wp_get_all_registered_entity_action_modules() {
	return WP_Entity_Actions_Registry::get_instance()->get_all_registered_action_modules();
}

/**
 * Declares the script modules of the entity actions as dependencies of the
 * editor script.
 *
 * The editor imports the script modules of an entity on demand, with a dynamic
 * `import()` the browser resolves through the import map of the page. Listing
 * the modules as dynamic `module_dependencies` of the `wp-editor` script adds
 * them to the import map of every page that loads the editor script: the post
 * editor, the site editor, and the pages booted by `@wordpress/boot`, whose
 * prerequisites depend on it. A dynamic dependency is only listed in the
 * import map; the module is fetched when it is imported.
 *
 * Reading the registry fires `wp_entity_actions_api_init`, so the actions and their
 * script modules are registered on demand. It only reads the registry on the
 * pages that load the editor script, not on every admin request (heartbeat,
 * Ajax, other screens). It runs on `admin_footer`, the last action before the
 * import map is printed that fires both in the admin template and in the pages
 * rendered outside it, which print the import map themselves. By then `init`
 * has completed, so the post types and supports the actions derive from are
 * final.
 * The registry only accepts registrations while the action fires, so the
 * modules declared here are the modules of every action the page can show.
 *
 * The `admin_footer` action passes it no argument, so the global registry is
 * used. In other scenarios (tests), the scripts are given directly.
 *
 * @since 7.2.0
 *
 * @param WP_Scripts|null $scripts The scripts registry. Defaults to the global one.
 */
function _wp_add_entity_action_modules_to_editor_script( $scripts = null ) {
	if ( ! $scripts instanceof WP_Scripts ) {
		$scripts = wp_scripts();
	}

	// The actions are loaded by `usePostActions()` of the editor script, so
	// every screen showing them loads it.
	if ( ! $scripts->query( 'wp-editor', 'enqueued' ) && ! $scripts->query( 'wp-editor', 'done' ) ) {
		return;
	}

	$kinds = wp_get_all_registered_entity_action_modules();
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

/**
 * Preloads the actions of the post types the editors list.
 *
 * The editors read them from the `/wp/v2/entity-actions` route as they load,
 * see `useEntityActions()` in
 * packages/editor/src/dataviews/entity-actions-loader.ts in Gutenberg. The
 * post editor reads those of the edited post in its actions menu. The site
 * editor reads those of the edited post, if any, and of the post types of the
 * screen it opens on, whose lists show the actions of their items. A path must
 * match the request of the `getEntityActionsConfig` core data resolver, see
 * packages/core-data/src/resolvers.js in Gutenberg.
 *
 * @since 7.2.0
 *
 * @param array                   $paths   REST API paths to preload.
 * @param WP_Block_Editor_Context $context Block editor context.
 * @return array Filtered preload paths.
 */
function _wp_preload_entity_actions( $paths, $context ) {
	$post_types = array();
	if ( 'core/edit-post' === $context->name && isset( $context->post ) ) {
		$post_types[] = $context->post->post_type;
	} elseif ( 'core/edit-site' === $context->name ) {
		if ( isset( $context->post ) ) {
			$post_types[] = $context->post->post_type;
		}
		$post_types = array_merge( $post_types, _wp_get_site_editor_screen_post_types() );
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
			'/wp/v2/entity-actions'
		);
	}

	return $paths;
}
