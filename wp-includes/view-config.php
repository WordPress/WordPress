<?php
/**
 * Entity view configuration API.
 *
 * Builds the default view configuration for an entity and exposes it through
 * the dynamic `get_entity_view_config_{$kind}_{$name}` filter so core and third
 * parties can provide the configuration for a specific entity.
 *
 * @package WordPress
 * @since 7.1.0
 */

/**
 * Builds the default `form` configuration for post types that don't provide their own.
 *
 * It is a sensible default for `post`, `page`, and custom post types alike rather
 * than being tailored per type. Post types that need a different shape can replace
 * it entirely with a dedicated `form` through their own filter callback.
 *
 * It is intentionally NOT gated by `supports`. The registered fields are the
 * single source of truth for what applies: each field is registered for a post
 * type based on its `supports` (and related flags such as `theme_supports`), and
 * the editor drops any form field whose definition is absent or whose `isVisible`
 * returns `false`.
 *
 * @since 7.1.0
 *
 * @return array The default form configuration.
 */
function _wp_get_default_post_type_form() {
	return array(
		'layout' => array( 'type' => 'panel' ),
		'fields' => array(
			array(
				'id'     => 'featured_media',
				'layout' => array(
					'type'          => 'regular',
					'labelPosition' => 'none',
				),
			),
			array(
				'id'     => 'post-content-info',
				'layout' => array(
					'type'          => 'regular',
					'labelPosition' => 'none',
				),
			),
			array(
				'id'     => 'excerpt',
				'layout' => array(
					'type'          => 'panel',
					'labelPosition' => 'top',
				),
			),
			array(
				'id'       => 'status',
				'label'    => __( 'Status' ),
				'children' => array(
					array(
						'id'     => 'status',
						'layout' => array(
							'type'          => 'regular',
							'labelPosition' => 'none',
						),
					),
					'scheduled_date',
					'password',
					'sticky',
				),
			),
			'date',
			'slug',
			'author',
			'template',
			array(
				'id'       => 'discussion',
				'label'    => __( 'Discussion' ),
				'children' => array(
					array(
						'id'     => 'comment_status',
						'layout' => array(
							'type'          => 'regular',
							'labelPosition' => 'none',
						),
					),
					'ping_status',
				),
			),
			'parent',
			'format',
			'revisions',
		),
	);
}

/**
 * Returns the view configuration for the given entity.
 *
 * Builds the default configuration shared by all entities and then exposes it
 * through the dynamic `get_entity_view_config_{$kind}_{$name}` filter so that core
 * and third parties can provide the configuration for a specific entity.
 *
 * @since 7.1.0
 *
 * @param string $kind The entity kind (e.g. `postType`).
 * @param string $name The entity name (e.g. `page`).
 * @return array {
 *     The view configuration for the entity.
 *
 *     @type array $default_view    Default view configuration.
 *     @type array $default_layouts Default layouts configuration.
 *     @type array $view_list       List of available views.
 *     @type array $form            Form configuration.
 * }
 */
function wp_get_entity_view_config( $kind, $name ) {
	$default_view    = array(
		'type'       => 'table',
		'filters'    => array(),
		'sort'       => array(
			'field'     => 'title',
			'direction' => 'asc',
		),
		'perPage'    => 20,
		'fields'     => array( 'author', 'status' ),
		'titleField' => 'title',
	);
	$default_layouts = array(
		'table' => array(),
		'grid'  => array(),
		'list'  => array(),
	);
	$all_items_title = __( 'All items' );
	if ( 'postType' === $kind ) {
		$post_type_object = get_post_type_object( $name );
		if ( $post_type_object && ! empty( $post_type_object->labels->all_items ) ) {
			$all_items_title = $post_type_object->labels->all_items;
		}
	}
	$view_list = array(
		array(
			'title' => $all_items_title,
			'slug'  => 'all',
		),
	);

	$config = array(
		'default_view'    => $default_view,
		'default_layouts' => $default_layouts,
		'view_list'       => $view_list,
		'form'            => 'postType' === $kind ? _wp_get_default_post_type_form() : array(),
	);

	/**
	 * Filters the view configuration for a given entity.
	 *
	 * The dynamic portions of the hook name, `$kind` and `$name`, refer to the
	 * entity kind (e.g. `postType`) and the entity name (e.g. `page`).
	 *
	 * @since 7.1.0
	 *
	 * @param array $config {
	 *     The view configuration for the entity.
	 *
	 *     @type array $default_view    Default view configuration.
	 *     @type array $default_layouts Default layouts configuration.
	 *     @type array $view_list       List of available views.
	 *     @type array $form            Form configuration.
	 * }
	 * @param array $entity {
	 *     The entity the configuration is built for.
	 *
	 *     @type string $kind The entity kind.
	 *     @type string $name The entity name.
	 * }
	 */
	$filtered_config = apply_filters(
		"get_entity_view_config_{$kind}_{$name}",
		$config,
		array(
			'kind' => $kind,
			'name' => $name,
		)
	);

	if ( ! is_array( $filtered_config ) ) {
		return $config;
	}

	// Backfill any dropped keys with their defaults, then discard any keys the
	// filter introduced that are not part of the documented configuration shape.
	$filtered_config = array_merge( $config, $filtered_config );
	return array_intersect_key( $filtered_config, $config );
}

/**
 * Provides the view configuration for the `page` post type.
 *
 * @since 7.1.0
 *
 * @param array $config {
 *     The view configuration for the entity.
 * }
 * @return array The filtered view configuration.
 */
function _wp_get_entity_view_config_post_type_page( $config ) {
	$config['default_layouts'] = array(
		'table' => array(
			'layout' => array(
				'styles' => array(
					'author' => array(
						'align' => 'start',
					),
				),
			),
		),
		'grid'  => array(),
		'list'  => array(),
	);

	$config['default_view'] = array(
		'type'       => 'list',
		'filters'    => array(),
		'perPage'    => 20,
		'sort'       => array(
			'field'     => 'title',
			'direction' => 'asc',
		),
		'showLevels' => true,
		'titleField' => 'title',
		'mediaField' => 'featured_media',
		'fields'     => array( 'author', 'status' ),
	);

	$config['view_list'] = array(
		// Reuse the base "all items" view, whose title is derived from the post
		// type's `all_items` label in wp_get_entity_view_config().
		$config['view_list'][0],
		array(
			'title' => __( 'Published' ),
			'slug'  => 'published',
			'view'  => array(
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'isAny',
						'value'    => 'publish',
						'isLocked' => true,
					),
				),
			),
		),
		array(
			'title' => __( 'Scheduled' ),
			'slug'  => 'future',
			'view'  => array(
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'isAny',
						'value'    => 'future',
						'isLocked' => true,
					),
				),
			),
		),
		array(
			'title' => __( 'Drafts' ),
			'slug'  => 'drafts',
			'view'  => array(
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'isAny',
						'value'    => 'draft',
						'isLocked' => true,
					),
				),
			),
		),
		array(
			'title' => __( 'Pending' ),
			'slug'  => 'pending',
			'view'  => array(
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'isAny',
						'value'    => 'pending',
						'isLocked' => true,
					),
				),
			),
		),
		array(
			'title' => __( 'Private' ),
			'slug'  => 'private',
			'view'  => array(
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'isAny',
						'value'    => 'private',
						'isLocked' => true,
					),
				),
			),
		),
		array(
			'title' => __( 'Trash' ),
			'slug'  => 'trash',
			'view'  => array(
				'type'    => 'table',
				'layout'  => $config['default_layouts']['table']['layout'],
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'isAny',
						'value'    => 'trash',
						'isLocked' => true,
					),
				),
			),
		),
	);

	return $config;
}

/**
 * Provides the view configuration for the `wp_block` post type.
 *
 * @since 7.1.0
 *
 * @param array $config {
 *     The view configuration for the entity.
 * }
 * @return array The filtered view configuration.
 */
function _wp_get_entity_view_config_post_type_wp_block( $config ) {
	$config['default_layouts'] = array(
		'table' => array(
			'layout' => array(
				'styles' => array(
					'author' => array(
						'width' => '1%',
					),
				),
			),
		),
		'grid'  => array(
			'layout' => array(
				'badgeFields' => array( 'sync-status' ),
			),
		),
	);

	$config['default_view'] = array(
		'type'       => 'grid',
		'perPage'    => 20,
		'titleField' => 'title',
		'mediaField' => 'preview',
		'fields'     => array( 'sync-status' ),
		'filters'    => array(),
		'layout'     => $config['default_layouts']['grid']['layout'],
	);

	$view_list = array(
		array(
			'title' => __( 'All patterns' ),
			'slug'  => 'all-patterns',
		),
		array(
			'title' => __( 'My patterns' ),
			'slug'  => 'my-patterns',
		),
	);

	// Gather categories from the block pattern categories registry.
	$registry   = WP_Block_Pattern_Categories_Registry::get_instance();
	$categories = array();

	foreach ( $registry->get_all_registered() as $category ) {
		$categories[ $category['name'] ] = $category['label'];
	}

	// Ensure "Uncategorized" is always included for patterns
	// that have no category assigned.
	$categories['uncategorized'] ??= __( 'Uncategorized' );

	// Also gather user-created pattern categories (wp_pattern_category taxonomy).
	$user_terms = get_terms(
		array(
			'taxonomy'   => 'wp_pattern_category',
			'hide_empty' => false,
		)
	);

	if ( ! is_wp_error( $user_terms ) ) {
		foreach ( $user_terms as $term ) {
			$categories[ $term->slug ] = $term->name;
		}
	}

	// Sort categories alphabetically by label.
	asort( $categories, SORT_NATURAL | SORT_FLAG_CASE );

	foreach ( $categories as $category_name => $label ) {
		$view_list[] = array(
			'title' => $label,
			'slug'  => $category_name,
		);
	}

	$config['view_list'] = $view_list;

	$config['form'] = array(
		'layout' => array( 'type' => 'panel' ),
		'fields' => array(
			array(
				'id'     => 'excerpt',
				'layout' => array(
					'type'          => 'panel',
					'labelPosition' => 'top',
				),
			),
			array(
				'id'     => 'post-content-info',
				'layout' => array(
					'type'          => 'regular',
					'labelPosition' => 'none',
				),
			),
			'sync-status',
			'revisions',
		),
	);

	return $config;
}

/**
 * Provides the view configuration for the `wp_template_part` post type.
 *
 * @since 7.1.0
 *
 * @param array $config {
 *     The view configuration for the entity.
 * }
 * @return array The filtered view configuration.
 */
function _wp_get_entity_view_config_post_type_wp_template_part( $config ) {
	$config['default_layouts'] = array(
		'table' => array(
			'layout' => array(
				'styles' => array(
					'author' => array(
						'width' => '1%',
					),
				),
			),
		),
		'grid'  => array(
			'layout' => array(),
		),
	);

	$config['default_view'] = array(
		'type'       => 'grid',
		'perPage'    => 20,
		'titleField' => 'title',
		'mediaField' => 'preview',
		'fields'     => array( 'author' ),
		'filters'    => array(),
		'layout'     => $config['default_layouts']['grid']['layout'],
	);

	$view_list = array(
		array(
			'title' => __( 'All template parts' ),
			'slug'  => 'all-parts',
		),
	);

	$areas = get_allowed_block_template_part_areas();

	// Ensure default areas appear in a consistent order.
	$preferred_order = array( 'header', 'footer', 'sidebar', 'navigation-overlay', 'uncategorized' );
	$ordered_areas   = array();
	$remaining_areas = array();
	foreach ( $areas as $area ) {
		$position = array_search( $area['area'], $preferred_order, true );
		if ( false !== $position ) {
			$ordered_areas[ $position ] = $area;
		} else {
			$remaining_areas[] = $area;
		}
	}
	ksort( $ordered_areas );
	$areas = array_merge( array_values( $ordered_areas ), $remaining_areas );

	foreach ( $areas as $area ) {
		$view_list[] = array(
			'title' => $area['label'],
			'slug'  => $area['area'],
			'view'  => array(
				'filters' => array(
					array(
						'field'    => 'area',
						'operator' => 'is',
						'value'    => $area['area'],
						'isLocked' => true,
					),
				),
			),
		);
	}

	$config['view_list'] = $view_list;

	$config['form'] = array(
		'layout' => array( 'type' => 'panel' ),
		'fields' => array(
			array(
				'id'     => 'last_edited_date',
				'layout' => array(
					'type'          => 'panel',
					'labelPosition' => 'none',
				),
			),
			'revisions',
		),
	);

	return $config;
}

/**
 * Provides the view configuration for the `wp_template` post type.
 *
 * @since 7.1.0
 *
 * @param array $config {
 *     The view configuration for the entity.
 * }
 * @return array The filtered view configuration.
 */
function _wp_get_entity_view_config_post_type_wp_template( $config ) {
	$config['default_view'] = array(
		'type'             => 'grid',
		'perPage'          => 20,
		'sort'             => array(
			'field'     => 'title',
			'direction' => 'asc',
		),
		'titleField'       => 'title',
		'descriptionField' => 'description',
		'mediaField'       => 'preview',
		'fields'           => array( 'author', 'active', 'slug', 'theme' ),
		'filters'          => array(),
		'showMedia'        => true,
	);

	$config['default_layouts'] = array(
		'table' => array( 'showMedia' => false ),
		'grid'  => array( 'showMedia' => true ),
		'list'  => array( 'showMedia' => false ),
	);

	$view_list = array(
		array(
			'title' => __( 'All templates' ),
			'slug'  => 'all',
		),
	);

	$templates = get_block_templates( array(), 'wp_template' );

	// Collect unique authors, tracking whether they come from a registered
	// source (theme, plugin, site) so we can sort those before user ones.
	$seen_authors       = array();
	$registered_authors = array();
	$user_authors       = array();
	foreach ( $templates as $template ) {
		/*
		 * Determine the original source of the template ('theme', 'plugin',
		 * 'site', or 'user').
		 */
		$original_source = 'user';
		if ( 'wp_template' === $template->type || 'wp_template_part' === $template->type ) {
			if ( $template->has_theme_file &&
				( 'theme' === $template->origin || (
					empty( $template->origin ) && in_array(
						$template->source,
						array(
							'theme',
							'custom',
						),
						true
					) )
				)
			) {
				/*
				 * Added by theme.
				 * Template originally provided by a theme, but customized by a user.
				 * Templates originally didn't have the 'origin' field so identify
				 * older customized templates by checking for no origin and a 'theme'
				 * or 'custom' source.
				 */
				$original_source = 'theme';
			} elseif ( 'plugin' === $template->origin ) {
				// Added by plugin.
				$original_source = 'plugin';
			} elseif ( empty( $template->has_theme_file ) && 'custom' === $template->source && empty( $template->author ) ) {
				/*
				 * Added by site.
				 * Template was created from scratch, but has no author. Author support
				 * was only added to templates in WordPress 5.9. Fallback to showing the
				 * site logo and title.
				 */
				$original_source = 'site';
			}
		}

		// Determine a human readable text for the author of the template.
		$author_text = '';
		switch ( $original_source ) {
			case 'theme':
				$theme_name  = wp_get_theme( $template->theme )->get( 'Name' );
				$author_text = empty( $theme_name ) ? $template->theme : $theme_name;
				break;
			case 'plugin':
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				$plugin_name = '';
				if ( isset( $template->plugin ) ) {
					$plugins = wp_get_active_and_valid_plugins();

					foreach ( $plugins as $plugin_file ) {
						$plugin_basename      = plugin_basename( $plugin_file );
						list( $plugin_slug, ) = explode( '/', $plugin_basename );

						if ( $plugin_slug === $template->plugin ) {
							$plugin_data = get_plugin_data( $plugin_file );

							if ( ! empty( $plugin_data['Name'] ) ) {
								$plugin_name = $plugin_data['Name'];
							}

							break;
						}
					}
				}

				/*
				 * Fall back to the theme name if the plugin is not defined. That's needed to keep backwards
				 * compatibility with templates that were registered before the plugin attribute was added.
				 */
				if ( '' === $plugin_name ) {
					$plugins         = get_plugins();
					$plugin_basename = plugin_basename( sanitize_text_field( $template->theme . '.php' ) );
					if ( isset( $plugins[ $plugin_basename ] ) && isset( $plugins[ $plugin_basename ]['Name'] ) ) {
						$plugin_name = $plugins[ $plugin_basename ]['Name'];
					} else {
						$plugin_name = $template->plugin ?? $template->theme;
					}
				}
				$author_text = $plugin_name;
				break;
			case 'site':
				$author_text = get_bloginfo( 'name' );
				break;
			case 'user':
				$author = get_user_by( 'id', $template->author );
				if ( ! $author ) {
					$author_text = __( 'Unknown author' );
				} else {
					$author_text = $author->get( 'display_name' );
				}
				break;
		}

		if ( ! empty( $author_text ) && ! isset( $seen_authors[ $author_text ] ) ) {
			$seen_authors[ $author_text ] = true;
			$entry                        = array(
				'title' => $author_text,
				'slug'  => $author_text,
				'view'  => array(
					'filters' => array(
						array(
							'field'    => 'author',
							'operator' => 'is',
							'value'    => $author_text,
							'isLocked' => true,
						),
					),
				),
			);
			if ( 'user' === $original_source ) {
				$user_authors[] = $entry;
			} else {
				$registered_authors[] = $entry;
			}
		}
	}

	$config['view_list'] = array_merge( $view_list, $registered_authors, $user_authors );

	$config['form'] = array(
		'layout' => array( 'type' => 'panel' ),
		'fields' => array(
			array(
				'id'     => 'description',
				'layout' => array(
					'type'          => 'panel',
					'labelPosition' => 'top',
				),
			),
			array(
				'id'     => 'description_readonly',
				'layout' => array(
					'type'          => 'regular',
					'labelPosition' => 'none',
				),
			),
			array(
				'id'     => 'last_edited_date',
				'layout' => array(
					'type'          => 'panel',
					'labelPosition' => 'none',
				),
			),
			'revisions',
			// The following fields are only meaningful in the `home`/`index`
			// template summary. They edit other entities (`root/site` and the
			// posts page); the editor merges those records into the form data
			// under a namespace and controls when the fields are shown.
			'posts_page_title',
			'posts_per_page',
			'default_comment_status',
		),
	);

	return $config;
}
