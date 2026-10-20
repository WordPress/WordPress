<?php
/**
 * Entity Fields API.
 *
 * A field definition is the serializable subset of the DataViews Field API:
 * every property that is plain data (`id`, `type`, `label`, `elements`,
 * `filterBy`, `isValid`, …). Properties that are JavaScript callbacks or
 * components (`render`, `Edit` as a component, `getValue`, `setValue`, `sort`,
 * `isVisible`, `getElements`, …) cannot be declared here. They are provided by
 * a script module registered for the entity along with its fields: its default
 * export maps field ids to the properties of each field that are JavaScript. An
 * entity can have several modules. Each module applies to the fields it was
 * registered with, see {@see wp_get_registered_entity_field_modules()}.
 *
 * Fields are registered on the `wp_entity_fields_api_init` action, and only there,
 * with {@see wp_register_entity_fields()}, {@see wp_update_entity_fields()}, and
 * {@see wp_unregister_entity_fields()}. The other functions read the registry.
 *
 * Fields can also be declared as files, grouped in a field collection, which
 * a `wp_entity_fields_api_init` callback registers with
 * {@see wp_register_entity_field_collection()}. A collection is for a single
 * entity. WordPress core registers its own fields this way, from
 * packages/core-fields: the defaults every post type derives from its
 * supports in code, the fields of single post types as collections.
 *
 * The registered fields are exposed by the `/wp/v2/entity-fields` REST API
 * route, see {@see WP_REST_Entity_Fields_Controller}.
 *
 * @package WordPress
 * @subpackage Entity Fields API
 * @since 7.2.0
 */

/**
 * Registers fields for an entity, see
 * {@see WP_Entity_Fields_Registry::register()}.
 *
 * It only runs on the `wp_entity_fields_api_init` action.
 *
 * @since 7.2.0
 *
 * @param string      $origin        Who registers the fields: `core`, or
 *                                   the slug of the plugin or theme.
 * @param string      $kind          The entity kind (e.g. `postType`).
 * @param string      $name          The entity name (e.g. `page`).
 * @param array[]     $fields        The list of field definitions, each with an `id`.
 * @param string|null $script_module The id of the script module providing
 *                                   the JavaScript parts of the fields, if any.
 * @return string[] The ids of the fields registered, in the order of the
 *                  call. Empty when none is.
 */
function wp_register_entity_fields( $origin, $kind, $name, $fields, $script_module = null ) {
	return WP_Entity_Fields_Registry::get_instance()->register( $origin, $kind, $name, $fields, $script_module );
}

/**
 * Updates registered fields of an entity, see
 * {@see WP_Entity_Fields_Registry::update()}.
 *
 * It only runs on the `wp_entity_fields_api_init` action.
 *
 * @since 7.2.0
 *
 * @param string      $origin        Who updates the fields: `core`, or
 *                                   the slug of the plugin or theme.
 * @param string      $kind          The entity kind (e.g. `postType`).
 * @param string      $name          The entity name (e.g. `page`).
 * @param array[]     $fields        The list of partial field definitions,
 *                                   each with the `id` of a registered field.
 * @param string|null $script_module The id of the script module providing
 *                                   the JavaScript parts of the fields, if any.
 * @return string[] The ids of the fields updated, in the order of the
 *                  call. Empty when none is.
 */
function wp_update_entity_fields( $origin, $kind, $name, $fields, $script_module = null ) {
	return WP_Entity_Fields_Registry::get_instance()->update( $origin, $kind, $name, $fields, $script_module );
}

/**
 * Unregisters fields of an entity, see
 * {@see WP_Entity_Fields_Registry::unregister()}.
 *
 * It only runs on the `wp_entity_fields_api_init` action.
 *
 * @since 7.2.0
 *
 * @param string        $kind The entity kind (e.g. `postType`).
 * @param string        $name The entity name (e.g. `page`).
 * @param string[]|null $ids  The ids of the fields to unregister. Default
 *                            null, every field of the entity.
 * @return array[] The list of the definitions unregistered, in
 *                 registration order. Empty when none is.
 */
function wp_unregister_entity_fields( $kind, $name, $ids = null ) {
	return WP_Entity_Fields_Registry::get_instance()->unregister( $kind, $name, $ids );
}

/**
 * Returns the fields registered for the given entity.
 *
 * @since 7.2.0
 *
 * @param string $kind The entity kind (e.g. `postType`).
 * @param string $name The entity name (e.g. `page`).
 * @return array[] The list of registered field definitions, in registration
 *                 order. Empty until the `init` action has completed: a
 *                 read before then is refused.
 */
function wp_get_registered_entity_fields( $kind, $name ) {
	return WP_Entity_Fields_Registry::get_instance()->get_registered( $kind, $name );
}

/**
 * Returns the ids of the script modules registered for the given entity.
 *
 * @since 7.2.0
 *
 * @param string $kind The entity kind (e.g. `postType`).
 * @param string $name The entity name (e.g. `page`).
 * @return array<string, string[]> The ids of the fields each module applies
 *                                 to, keyed by module id, in registration
 *                                 order. Empty until the `init` action has
 *                                 completed: a read before then is refused.
 */
function wp_get_registered_entity_field_modules( $kind, $name ) {
	return WP_Entity_Fields_Registry::get_instance()->get_registered_field_modules( $kind, $name );
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
function wp_get_all_registered_entity_field_modules() {
	return WP_Entity_Fields_Registry::get_instance()->get_all_registered_field_modules();
}

/**
 * Declares the script modules of the entity fields as dependencies of the
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
 * Reading the registry fires `wp_entity_fields_api_init`, so the fields and their
 * script modules are registered on demand. It only reads the registry on the
 * pages that load the editor script, not on every admin request (heartbeat,
 * Ajax, other screens). It runs on `admin_footer`, the last action before the
 * import map is printed that fires both in the admin template and in the pages
 * rendered outside it, which print the import map themselves. By then `init`
 * has completed, so the supports the default fields derive from are final.
 * The registry only accepts registrations while the action fires, so the
 * modules declared here are the modules of every field the page can show.
 *
 * It is hooked to `admin_footer` without arguments, so the global scripts
 * registry is used. In other scenarios (tests), the scripts are given directly.
 *
 * @since 7.2.0
 * @access private
 *
 * @param WP_Scripts|null $scripts Optional. The scripts registry. Default null,
 *                                 the global one.
 */
function _wp_add_entity_field_modules_to_editor_script( $scripts = null ) {
	if ( ! $scripts instanceof WP_Scripts ) {
		$scripts = wp_scripts();
	}

	/*
	 * The fields are loaded by the editor package, see
	 * packages/editor/src/dataviews/store/private-resolvers.ts in Gutenberg,
	 * which means any screen that wants to use this mechanism needs to load
	 * the editor script.
	 */
	if ( ! $scripts->query( 'wp-editor', 'enqueued' ) && ! $scripts->query( 'wp-editor', 'done' ) ) {
		return;
	}

	$kinds = wp_get_all_registered_entity_field_modules();
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
 * Returns the fields of a field collection, see
 * {@see WP_Entity_Fields_Registry::get_collection_fields()}.
 *
 * @since 7.2.0
 *
 * @param string $directory The directory of the collection.
 * @return array<string, array> The fields of the collection, keyed by id, in
 *                              the alphabetical order of their folders.
 */
function wp_get_entity_field_collection_fields( $directory ) {
	return WP_Entity_Fields_Registry::get_instance()->get_collection_fields( $directory );
}

/**
 * Registers the fields of a field collection, see
 * {@see WP_Entity_Fields_Registry::register_collection()} for the format of a
 * collection.
 *
 * Like wp_register_entity_fields(), it only runs on the `wp_entity_fields_api_init`
 * action.
 *
 * @since 7.2.0
 *
 * @param string $directory The directory of the collection.
 * @return bool Whether the collection is valid and all of its fields are
 *              registered. True as well when it has no fields.
 */
function wp_register_entity_field_collection( $directory ) {
	return WP_Entity_Fields_Registry::get_instance()->register_collection( $directory );
}

/**
 * Returns the paths that serve the fields of the given post types, skipping
 * the post types that are not registered.
 *
 * A path must match the request of the `getFieldsConfig` core data resolver,
 * see packages/core-data/src/resolvers.js in Gutenberg.
 *
 * @since 7.2.0
 * @access private
 *
 * @param string[] $post_types The post types.
 * @return string[] One path of the `/wp/v2/entity-fields` route per post type,
 *                  in the order the post types are given, without duplicates.
 */
function _wp_get_post_type_fields_preload_paths( $post_types ) {
	$paths = array();
	foreach ( array_unique( $post_types ) as $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}
		$paths[] = add_query_arg(
			array(
				'kind' => 'postType',
				'name' => $post_type,
			),
			'/wp/v2/entity-fields'
		);
	}

	return $paths;
}

/**
 * Returns the post types whose items the screen of the site editor being
 * loaded lists or edits, from its `p` query arg.
 *
 * The screens are the routes in
 * packages/edit-site/src/components/site-editor-routes in Gutenberg: the
 * lists and the editors of pages, templates, patterns, and template parts.
 * The patterns screen lists both patterns and template parts.
 *
 * @since 7.2.0
 * @access private
 *
 * @return string[] The post types, empty for any other screen.
 */
function _wp_get_site_editor_screen_post_types() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only picks what to preload.
	$path     = isset( $_GET['p'] ) && is_string( $_GET['p'] ) ? sanitize_text_field( wp_unslash( $_GET['p'] ) ) : '';
	$segments = explode( '/', trim( $path, '/' ) );

	switch ( $segments[0] ) {
		case 'page':
			return array( 'page' );
		case 'template':
		case 'wp_template':
			return array( 'wp_template' );
		case 'pattern':
			return array( 'wp_block', 'wp_template_part' );
		case 'wp_block':
			return array( 'wp_block' );
		case 'wp_template_part':
			return array( 'wp_template_part' );
	}
	return array();
}

/**
 * Preloads the fields of the post types the editors list.
 *
 * The editors read them from the `/wp/v2/entity-fields` route, see `useFields`
 * in packages/editor/src/dataviews/fields-loader/index.ts in Gutenberg. The
 * post editor reads those of the edited post in the post summary of its
 * inspector. The site editor reads those of the edited post, if any, and of
 * the post types of the screen it opens on.
 *
 * @since 7.2.0
 * @access private
 *
 * @param array                   $paths   REST API paths to preload.
 * @param WP_Block_Editor_Context $context Block editor context.
 * @return array Filtered preload paths.
 */
function _wp_preload_entity_fields( $paths, $context ) {
	$post_types = array();
	if ( 'core/edit-post' === $context->name && isset( $context->post ) ) {
		$post_types[] = $context->post->post_type;
	} elseif ( 'core/edit-site' === $context->name ) {
		if ( isset( $context->post ) ) {
			$post_types[] = $context->post->post_type;
		}
		$post_types = array_merge( $post_types, _wp_get_site_editor_screen_post_types() );
	}

	return array_merge(
		$paths,
		_wp_get_post_type_fields_preload_paths( $post_types )
	);
}
