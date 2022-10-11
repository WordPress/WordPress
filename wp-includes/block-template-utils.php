<?php
/**
 * Utilities used to fetch and create templates and template parts.
 *
 * @package WordPress
 * @since 5.8.0
 */

// Define constants for supported wp_template_part_area taxonomy.
if ( ! defined( 'WP_TEMPLATE_PART_AREA_HEADER' ) ) {
	define( 'WP_TEMPLATE_PART_AREA_HEADER', 'header' );
}
if ( ! defined( 'WP_TEMPLATE_PART_AREA_FOOTER' ) ) {
	define( 'WP_TEMPLATE_PART_AREA_FOOTER', 'footer' );
}
if ( ! defined( 'WP_TEMPLATE_PART_AREA_SIDEBAR' ) ) {
	define( 'WP_TEMPLATE_PART_AREA_SIDEBAR', 'sidebar' );
}
if ( ! defined( 'WP_TEMPLATE_PART_AREA_UNCATEGORIZED' ) ) {
	define( 'WP_TEMPLATE_PART_AREA_UNCATEGORIZED', 'uncategorized' );
}

/**
 * For backward compatibility reasons,
 * block themes might be using block-templates or block-template-parts,
 * this function ensures we fallback to these folders properly.
 *
 * @since 5.9.0
 *
 * @param string $theme_stylesheet The stylesheet. Default is to leverage the main theme root.
 *
 * @return string[] {
 *     Folder names used by block themes.
 *
 *     @type string $wp_template      Theme-relative directory name for block templates.
 *     @type string $wp_template_part Theme-relative directory name for block template parts.
 * }
 */
function get_block_theme_folders( $theme_stylesheet = null ) {
	$theme_name = null === $theme_stylesheet ? get_stylesheet() : $theme_stylesheet;
	$root_dir   = get_theme_root( $theme_name );
	$theme_dir  = "$root_dir/$theme_name";

	if ( file_exists( $theme_dir . '/block-templates' ) || file_exists( $theme_dir . '/block-template-parts' ) ) {
		return array(
			'wp_template'      => 'block-templates',
			'wp_template_part' => 'block-template-parts',
		);
	}

	return array(
		'wp_template'      => 'templates',
		'wp_template_part' => 'parts',
	);
}

/**
 * Returns a filtered list of allowed area values for template parts.
 *
 * @since 5.9.0
 *
 * @return array The supported template part area values.
 */
function get_allowed_block_template_part_areas() {
	$default_area_definitions = array(
		array(
			'area'        => WP_TEMPLATE_PART_AREA_UNCATEGORIZED,
			'label'       => __( 'General' ),
			'description' => __(
				'General templates often perform a specific role like displaying post content, and are not tied to any particular area.'
			),
			'icon'        => 'layout',
			'area_tag'    => 'div',
		),
		array(
			'area'        => WP_TEMPLATE_PART_AREA_HEADER,
			'label'       => __( 'Header' ),
			'description' => __(
				'The Header template defines a page area that typically contains a title, logo, and main navigation.'
			),
			'icon'        => 'header',
			'area_tag'    => 'header',
		),
		array(
			'area'        => WP_TEMPLATE_PART_AREA_FOOTER,
			'label'       => __( 'Footer' ),
			'description' => __(
				'The Footer template defines a page area that typically contains site credits, social links, or any other combination of blocks.'
			),
			'icon'        => 'footer',
			'area_tag'    => 'footer',
		),
	);

	/**
	 * Filters the list of allowed template part area values.
	 *
	 * @since 5.9.0
	 *
	 * @param array $default_area_definitions An array of supported area objects.
	 */
	return apply_filters( 'default_wp_template_part_areas', $default_area_definitions );
}


/**
 * Returns a filtered list of default template types, containing their
 * localized titles and descriptions.
 *
 * @since 5.9.0
 *
 * @return array The default template types.
 */
function get_default_block_template_types() {
	$default_template_types = array(
		'index'          => array(
			'title'       => _x( 'Index', 'Template name' ),
			'description' => __( 'Displays posts.' ),
		),
		'home'           => array(
			'title'       => _x( 'Home', 'Template name' ),
			'description' => __( 'Displays posts on the homepage, or on the Posts page if a static homepage is set.' ),
		),
		'front-page'     => array(
			'title'       => _x( 'Front Page', 'Template name' ),
			'description' => __( 'Displays the homepage.' ),
		),
		'singular'       => array(
			'title'       => _x( 'Singular', 'Template name' ),
			'description' => __( 'Displays a single post or page.' ),
		),
		'single'         => array(
			'title'       => _x( 'Single', 'Template name' ),
			'description' => __( 'The default template for displaying any single post or attachment.' ),
		),
		'page'           => array(
			'title'       => _x( 'Page', 'Template name' ),
			'description' => __( 'Displays a single page.' ),
		),
		'archive'        => array(
			'title'       => _x( 'Archive', 'Template name' ),
			'description' => __( 'Displays post categories, tags, and other archives.' ),
		),
		'author'         => array(
			'title'       => _x( 'Author', 'Template name' ),
			'description' => __( 'Displays latest posts written by a single author.' ),
		),
		'category'       => array(
			'title'       => _x( 'Category', 'Template name' ),
			'description' => __( 'Displays latest posts from a single post category.' ),
		),
		'taxonomy'       => array(
			'title'       => _x( 'Taxonomy', 'Template name' ),
			'description' => __( 'Displays latest posts from a single post taxonomy.' ),
		),
		'date'           => array(
			'title'       => _x( 'Date', 'Template name' ),
			'description' => __( 'Displays posts from a specific date.' ),
		),
		'tag'            => array(
			'title'       => _x( 'Tag', 'Template name' ),
			'description' => __( 'Displays latest posts with a single post tag.' ),
		),
		'attachment'     => array(
			'title'       => __( 'Media' ),
			'description' => __( 'Displays individual media items or attachments.' ),
		),
		'search'         => array(
			'title'       => _x( 'Search', 'Template name' ),
			'description' => __( 'Displays search results.' ),
		),
		'privacy-policy' => array(
			'title'       => __( 'Privacy Policy' ),
			'description' => __( 'Displays the privacy policy page.' ),
		),
		'404'            => array(
			'title'       => _x( '404', 'Template name' ),
			'description' => __( 'Displays when no content is found.' ),
		),
	);

	/**
	 * Filters the list of template types.
	 *
	 * @since 5.9.0
	 *
	 * @param array $default_template_types An array of template types, formatted as [ slug => [ title, description ] ].
	 */
	return apply_filters( 'default_template_types', $default_template_types );
}

/**
 * Checks whether the input 'area' is a supported value.
 * Returns the input if supported, otherwise returns the 'uncategorized' value.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $type Template part area name.
 * @return string Input if supported, else the uncategorized value.
 */
function _filter_block_template_part_area( $type ) {
	$allowed_areas = array_map(
		static function ( $item ) {
			return $item['area'];
		},
		get_allowed_block_template_part_areas()
	);
	if ( in_array( $type, $allowed_areas, true ) ) {
		return $type;
	}

	$warning_message = sprintf(
		/* translators: %1$s: Template area type, %2$s: the uncategorized template area value. */
		__( '"%1$s" is not a supported wp_template_part area value and has been added as "%2$s".' ),
		$type,
		WP_TEMPLATE_PART_AREA_UNCATEGORIZED
	);
	trigger_error( $warning_message, E_USER_NOTICE );
	return WP_TEMPLATE_PART_AREA_UNCATEGORIZED;
}

/**
 * Finds all nested template part file paths in a theme's directory.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $base_directory The theme's file path.
 * @return array A list of paths to all template part files.
 */
function _get_block_templates_paths( $base_directory ) {
	$path_list = array();
	if ( file_exists( $base_directory ) ) {
		$nested_files      = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base_directory ) );
		$nested_html_files = new RegexIterator( $nested_files, '/^.+\.html$/i', RecursiveRegexIterator::GET_MATCH );
		foreach ( $nested_html_files as $path => $file ) {
			$path_list[] = $path;
		}
	}
	return $path_list;
}

/**
 * Retrieves the template file from the theme for a given slug.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $template_type 'wp_template' or 'wp_template_part'.
 * @param string $slug          Template slug.
 * @return array|null Template.
 */
function _get_block_template_file( $template_type, $slug ) {
	if ( 'wp_template' !== $template_type && 'wp_template_part' !== $template_type ) {
		return null;
	}

	$themes = array(
		get_stylesheet() => get_stylesheet_directory(),
		get_template()   => get_template_directory(),
	);
	foreach ( $themes as $theme_slug => $theme_dir ) {
		$template_base_paths = get_block_theme_folders( $theme_slug );
		$file_path           = $theme_dir . '/' . $template_base_paths[ $template_type ] . '/' . $slug . '.html';
		if ( file_exists( $file_path ) ) {
			$new_template_item = array(
				'slug'  => $slug,
				'path'  => $file_path,
				'theme' => $theme_slug,
				'type'  => $template_type,
			);

			if ( 'wp_template_part' === $template_type ) {
				return _add_block_template_part_area_info( $new_template_item );
			}

			if ( 'wp_template' === $template_type ) {
				return _add_block_template_info( $new_template_item );
			}

			return $new_template_item;
		}
	}

	return null;
}

/**
 * Retrieves the template files from the theme.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $template_type 'wp_template' or 'wp_template_part'.
 * @return array Template.
 */
function _get_block_templates_files( $template_type ) {
	if ( 'wp_template' !== $template_type && 'wp_template_part' !== $template_type ) {
		return null;
	}

	$themes         = array(
		get_stylesheet() => get_stylesheet_directory(),
		get_template()   => get_template_directory(),
	);
	$template_files = array();
	foreach ( $themes as $theme_slug => $theme_dir ) {
		$template_base_paths  = get_block_theme_folders( $theme_slug );
		$theme_template_files = _get_block_templates_paths( $theme_dir . '/' . $template_base_paths[ $template_type ] );
		foreach ( $theme_template_files as $template_file ) {
			$template_base_path = $template_base_paths[ $template_type ];
			$template_slug      = substr(
				$template_file,
				// Starting position of slug.
				strpos( $template_file, $template_base_path . DIRECTORY_SEPARATOR ) + 1 + strlen( $template_base_path ),
				// Subtract ending '.html'.
				-5
			);
			$new_template_item = array(
				'slug'  => $template_slug,
				'path'  => $template_file,
				'theme' => $theme_slug,
				'type'  => $template_type,
			);

			if ( 'wp_template_part' === $template_type ) {
				$template_files[] = _add_block_template_part_area_info( $new_template_item );
			}

			if ( 'wp_template' === $template_type ) {
				$template_files[] = _add_block_template_info( $new_template_item );
			}
		}
	}

	return $template_files;
}

/**
 * Attempts to add custom template information to the template item.
 *
 * @since 5.9.0
 * @access private
 *
 * @param array $template_item Template to add information to (requires 'slug' field).
 * @return array Template item.
 */
function _add_block_template_info( $template_item ) {
	if ( ! WP_Theme_JSON_Resolver::theme_has_support() ) {
		return $template_item;
	}

	$theme_data = WP_Theme_JSON_Resolver::get_theme_data()->get_custom_templates();
	if ( isset( $theme_data[ $template_item['slug'] ] ) ) {
		$template_item['title']     = $theme_data[ $template_item['slug'] ]['title'];
		$template_item['postTypes'] = $theme_data[ $template_item['slug'] ]['postTypes'];
	}

	return $template_item;
}

/**
 * Attempts to add the template part's area information to the input template.
 *
 * @since 5.9.0
 * @access private
 *
 * @param array $template_info Template to add information to (requires 'type' and 'slug' fields).
 * @return array Template info.
 */
function _add_block_template_part_area_info( $template_info ) {
	if ( WP_Theme_JSON_Resolver::theme_has_support() ) {
		$theme_data = WP_Theme_JSON_Resolver::get_theme_data()->get_template_parts();
	}

	if ( isset( $theme_data[ $template_info['slug'] ]['area'] ) ) {
		$template_info['title'] = $theme_data[ $template_info['slug'] ]['title'];
		$template_info['area']  = _filter_block_template_part_area( $theme_data[ $template_info['slug'] ]['area'] );
	} else {
		$template_info['area'] = WP_TEMPLATE_PART_AREA_UNCATEGORIZED;
	}

	return $template_info;
}

/**
 * Returns an array containing the references of
 * the passed blocks and their inner blocks.
 *
 * @since 5.9.0
 * @access private
 *
 * @param array $blocks array of blocks.
 * @return array block references to the passed blocks and their inner blocks.
 */
function _flatten_blocks( &$blocks ) {
	$all_blocks = array();
	$queue      = array();
	foreach ( $blocks as &$block ) {
		$queue[] = &$block;
	}

	while ( count( $queue ) > 0 ) {
		$block = &$queue[0];
		array_shift( $queue );
		$all_blocks[] = &$block;

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as &$inner_block ) {
				$queue[] = &$inner_block;
			}
		}
	}

	return $all_blocks;
}

/**
 * Parses wp_template content and injects the active theme's
 * stylesheet as a theme attribute into each wp_template_part
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $template_content serialized wp_template content.
 * @return string Updated 'wp_template' content.
 */
function _inject_theme_attribute_in_block_template_content( $template_content ) {
	$has_updated_content = false;
	$new_content         = '';
	$template_blocks     = parse_blocks( $template_content );

	$blocks = _flatten_blocks( $template_blocks );
	foreach ( $blocks as &$block ) {
		if (
			'core/template-part' === $block['blockName'] &&
			! isset( $block['attrs']['theme'] )
		) {
			$block['attrs']['theme'] = wp_get_theme()->get_stylesheet();
			$has_updated_content     = true;
		}
	}

	if ( $has_updated_content ) {
		foreach ( $template_blocks as &$block ) {
			$new_content .= serialize_block( $block );
		}

		return $new_content;
	}

	return $template_content;
}

/**
 * Parses a block template and removes the theme attribute from each template part.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $template_content Serialized block template content.
 * @return string Updated block template content.
 */
function _remove_theme_attribute_in_block_template_content( $template_content ) {
	$has_updated_content = false;
	$new_content         = '';
	$template_blocks     = parse_blocks( $template_content );

	$blocks = _flatten_blocks( $template_blocks );
	foreach ( $blocks as $key => $block ) {
		if ( 'core/template-part' === $block['blockName'] && isset( $block['attrs']['theme'] ) ) {
			unset( $blocks[ $key ]['attrs']['theme'] );
			$has_updated_content = true;
		}
	}

	if ( ! $has_updated_content ) {
		return $template_content;
	}

	foreach ( $template_blocks as $block ) {
		$new_content .= serialize_block( $block );
	}

	return $new_content;
}

/**
 * Builds a unified template object based on a theme file.
 *
 * @since 5.9.0
 * @access private
 *
 * @param array  $template_file Theme file.
 * @param string $template_type 'wp_template' or 'wp_template_part'.
 * @return WP_Block_Template Template.
 */
function _build_block_template_result_from_file( $template_file, $template_type ) {
	$default_template_types = get_default_block_template_types();
	$template_content       = file_get_contents( $template_file['path'] );
	$theme                  = wp_get_theme()->get_stylesheet();

	$template                 = new WP_Block_Template();
	$template->id             = $theme . '//' . $template_file['slug'];
	$template->theme          = $theme;
	$template->content        = _inject_theme_attribute_in_block_template_content( $template_content );
	$template->slug           = $template_file['slug'];
	$template->source         = 'theme';
	$template->type           = $template_type;
	$template->title          = ! empty( $template_file['title'] ) ? $template_file['title'] : $template_file['slug'];
	$template->status         = 'publish';
	$template->has_theme_file = true;
	$template->is_custom      = true;

	if ( 'wp_template' === $template_type && isset( $default_template_types[ $template_file['slug'] ] ) ) {
		$template->description = $default_template_types[ $template_file['slug'] ]['description'];
		$template->title       = $default_template_types[ $template_file['slug'] ]['title'];
		$template->is_custom   = false;
	}

	if ( 'wp_template' === $template_type && isset( $template_file['postTypes'] ) ) {
		$template->post_types = $template_file['postTypes'];
	}

	if ( 'wp_template_part' === $template_type && isset( $template_file['area'] ) ) {
		$template->area = $template_file['area'];
	}

	return $template;
}

/**
 * Builds the title and description of a post-specific template based on the underlying referenced post.
 *
 * Mutates the underlying template object.
 *
 * @since 6.1.0
 * @access private
 *
 * @param string            $post_type Post type, e.g. page, post, product.
 * @param string            $slug      Slug of the post, e.g. a-story-about-shoes.
 * @param WP_Block_Template $template  Template to mutate adding the description and title computed.
 * @return bool Returns true if the referenced post was found and false otherwise.
 */
function _wp_build_title_and_description_for_single_post_type_block_template( $post_type, $slug, WP_Block_Template $template ) {
	$post_type_object = get_post_type_object( $post_type );

	$default_args = array(
		'post_type'              => $post_type,
		'post_status'            => 'publish',
		'posts_per_page'         => 1,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
	);

	$args  = array(
		'name' => $slug,
	);
	$args  = wp_parse_args( $args, $default_args );
	$posts_query = new WP_Query( $args );

	if ( empty( $posts_query->posts ) ) {
		$template->title = sprintf(
			/* translators: Custom template title in the Site Editor referencing a post that was not found. 1: Post type singular name, 2: Post type slug. */
			__( 'Not found: %1$s (%2$s)' ),
			$post_type_object->labels->singular_name,
			$slug
		);

		return false;
	}

	$post_title = $posts_query->posts[0]->post_title;

	$template->title = sprintf(
		/* translators: Custom template title in the Site Editor. 1: Post type singular name, 2: Post title. */
		__( '%1$s: %2$s' ),
		$post_type_object->labels->singular_name,
		$post_title
	);

	$template->description = sprintf(
		/* translators: Custom template description in the Site Editor. %s: Post title. */
		__( 'Template for %s' ),
		$post_title
	);

	$args = array(
		'title' => $post_title,
	);
	$args                        = wp_parse_args( $args, $default_args );
	$posts_with_same_title_query = new WP_Query( $args );

	if ( count( $posts_with_same_title_query->posts ) > 1 ) {
		$template->title = sprintf(
			/* translators: Custom template title in the Site Editor. 1: Template title, 2: Post type slug. */
			__( '%1$s (%2$s)' ),
			$template->title,
			$slug
		);
	}

	return true;
}

/**
 * Builds the title and description of a taxonomy-specific template based on the underlying entity referenced.
 *
 * Mutates the underlying template object.
 *
 * @since 6.1.0
 * @access private
 *
 * @param string            $taxonomy Identifier of the taxonomy, e.g. category.
 * @param string            $slug     Slug of the term, e.g. shoes.
 * @param WP_Block_Template $template Template to mutate adding the description and title computed.
 * @return bool True if the term referenced was found and false otherwise.
 */
function _wp_build_title_and_description_for_taxonomy_block_template( $taxonomy, $slug, WP_Block_Template $template ) {
	$taxonomy_object = get_taxonomy( $taxonomy );

	$default_args = array(
		'taxonomy'               => $taxonomy,
		'hide_empty'             => false,
		'update_term_meta_cache' => false,
	);

	$term_query = new WP_Term_Query();

	$args  = array(
		'number' => 1,
		'slug'   => $slug,
	);
	$args        = wp_parse_args( $args, $default_args );
	$terms_query = $term_query->query( $args );

	if ( empty( $terms_query->terms ) ) {
		$template->title = sprintf(
			/* translators: Custom template title in the Site Editor, referencing a taxonomy term that was not found. 1: Taxonomy singular name, 2: Term slug. */
			__( 'Not found: %1$s (%2$s)' ),
			$taxonomy_object->labels->singular_name,
			$slug
		);
		return false;
	}

	$term_title = $terms_query->terms[0]->name;

	$template->title = sprintf(
		/* translators: Custom template title in the Site Editor. 1: Taxonomy singular name, 2: Term title. */
		__( '%1$s: %2$s' ),
		$taxonomy_object->labels->singular_name,
		$term_title
	);

	$template->description = sprintf(
		/* translators: Custom template description in the Site Editor. %s: Term title. */
		__( 'Template for %s' ),
		$term_title
	);

	$term_query = new WP_Term_Query();

	$args = array(
		'number' => 2,
		'name'   => $term_title,
	);
	$args                        = wp_parse_args( $args, $default_args );
	$terms_with_same_title_query = $term_query->query( $args );

	if ( count( $terms_with_same_title_query->terms ) > 1 ) {
		$template->title = sprintf(
			/* translators: Custom template title in the Site Editor. 1: Template title, 2: Term slug. */
			__( '%1$s (%2$s)' ),
			$template->title,
			$slug
		);
	}

	return true;
}

/**
 * Builds a unified template object based a post Object.
 *
 * @since 5.9.0
 * @access private
 *
 * @param WP_Post $post Template post.
 * @return WP_Block_Template|WP_Error Template.
 */
function _build_block_template_result_from_post( $post ) {
	$default_template_types = get_default_block_template_types();
	$terms                  = get_the_terms( $post, 'wp_theme' );

	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	if ( ! $terms ) {
		return new WP_Error( 'template_missing_theme', __( 'No theme is defined for this template.' ) );
	}

	$theme          = $terms[0]->name;
	$template_file  = _get_block_template_file( $post->post_type, $post->post_name );
	$has_theme_file = wp_get_theme()->get_stylesheet() === $theme && null !== $template_file;

	$origin           = get_post_meta( $post->ID, 'origin', true );
	$is_wp_suggestion = get_post_meta( $post->ID, 'is_wp_suggestion', true );

	$template                 = new WP_Block_Template();
	$template->wp_id          = $post->ID;
	$template->id             = $theme . '//' . $post->post_name;
	$template->theme          = $theme;
	$template->content        = $post->post_content;
	$template->slug           = $post->post_name;
	$template->source         = 'custom';
	$template->origin         = ! empty( $origin ) ? $origin : null;
	$template->type           = $post->post_type;
	$template->description    = $post->post_excerpt;
	$template->title          = $post->post_title;
	$template->status         = $post->post_status;
	$template->has_theme_file = $has_theme_file;
	$template->is_custom      = empty( $is_wp_suggestion );
	$template->author         = $post->post_author;

	if ( 'wp_template' === $post->post_type && $has_theme_file && isset( $template_file['postTypes'] ) ) {
		$template->post_types = $template_file['postTypes'];
	}

	if ( 'wp_template' === $post->post_type && isset( $default_template_types[ $template->slug ] ) ) {
		$template->is_custom = false;
	}

	if ( 'wp_template_part' === $post->post_type ) {
		$type_terms = get_the_terms( $post, 'wp_template_part_area' );
		if ( ! is_wp_error( $type_terms ) && false !== $type_terms ) {
			$template->area = $type_terms[0]->name;
		}
	}

	// Check for a block template without a description and title or with a title equal to the slug.
	if ( 'wp_template' === $post->post_type && empty( $template->description ) && ( empty( $template->title ) || $template->title === $template->slug ) ) {
		$matches = array();

		// Check for a block template for a single author, page, post, tag, category, custom post type, or custom taxonomy.
		if ( preg_match( '/(author|page|single|tag|category|taxonomy)-(.+)/', $template->slug, $matches ) ) {
			$type           = $matches[1];
			$slug_remaining = $matches[2];

			switch ( $type ) {
				case 'author':
					$nice_name = $slug_remaining;
					$users     = get_users(
						array(
							'capability'     => 'edit_posts',
							'search'         => $nice_name,
							'search_columns' => array( 'user_nicename' ),
							'fields'         => 'display_name',
						)
					);

					if ( empty( $users ) ) {
						$template->title = sprintf(
							/* translators: Custom template title in the Site Editor, referencing a deleted author. %s: Author nicename. */
							__( 'Deleted author: %s' ),
							$nice_name
						);
					} else {
						$author_name = $users[0];

						$template->title = sprintf(
							/* translators: Custom template title in the Site Editor. %s: Author name. */
							__( 'Author: %s' ),
							$author_name
						);

						$template->description = sprintf(
							/* translators: Custom template description in the Site Editor. %s: Author name. */
							__( 'Template for %s' ),
							$author_name
						);

						$users_with_same_name = get_users(
							array(
								'capability'     => 'edit_posts',
								'search'         => $author_name,
								'search_columns' => array( 'display_name' ),
								'fields'         => 'display_name',
							)
						);

						if ( count( $users_with_same_name ) > 1 ) {
							$template->title = sprintf(
								/* translators: Custom template title in the Site Editor. 1: Template title of an author template, 2: Author nicename. */
								__( '%1$s (%2$s)' ),
								$template->title,
								$nice_name
							);
						}
					}
					break;
				case 'page':
					_wp_build_title_and_description_for_single_post_type_block_template( 'page', $slug_remaining, $template );
					break;
				case 'single':
					$post_types = get_post_types();

					foreach ( $post_types as $post_type ) {
						$post_type_length = strlen( $post_type ) + 1;

						// If $slug_remaining starts with $post_type followed by a hyphen.
						if ( 0 === strncmp( $slug_remaining, $post_type . '-', $post_type_length ) ) {
							$slug  = substr( $slug_remaining, $post_type_length, strlen( $slug_remaining ) );
							$found = _wp_build_title_and_description_for_single_post_type_block_template( $post_type, $slug, $template );

							if ( $found ) {
								break;
							}
						}
					}
					break;
				case 'tag':
					_wp_build_title_and_description_for_taxonomy_block_template( 'post_tag', $slug_remaining, $template );
					break;
				case 'category':
					_wp_build_title_and_description_for_taxonomy_block_template( 'category', $slug_remaining, $template );
					break;
				case 'taxonomy':
					$taxonomies = get_taxonomies();

					foreach ( $taxonomies as $taxonomy ) {
						$taxonomy_length = strlen( $taxonomy ) + 1;

						// If $slug_remaining starts with $taxonomy followed by a hyphen.
						if ( 0 === strncmp( $slug_remaining, $taxonomy . '-', $taxonomy_length ) ) {
							$slug  = substr( $slug_remaining, $taxonomy_length, strlen( $slug_remaining ) );
							$found = _wp_build_title_and_description_for_taxonomy_block_template( $taxonomy, $slug, $template );

							if ( $found ) {
								break;
							}
						}
					}
					break;
			}
		}
	}

	return $template;
}

/**
 * Retrieves a list of unified template objects based on a query.
 *
 * @since 5.8.0
 *
 * @param array  $query {
 *     Optional. Arguments to retrieve templates.
 *
 *     @type array  $slug__in  List of slugs to include.
 *     @type int    $wp_id     Post ID of customized template.
 *     @type string $area      A 'wp_template_part_area' taxonomy value to filter by (for wp_template_part template type only).
 *     @type string $post_type Post type to get the templates for.
 * }
 * @param string $template_type 'wp_template' or 'wp_template_part'.
 * @return array Templates.
 */
function get_block_templates( $query = array(), $template_type = 'wp_template' ) {
	/**
	 * Filters the block templates array before the query takes place.
	 *
	 * Return a non-null value to bypass the WordPress queries.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Block_Template[]|null $block_templates Return an array of block templates to short-circuit the default query,
	 *                                                  or null to allow WP to run it's normal queries.
	 * @param array  $query {
	 *     Optional. Arguments to retrieve templates.
	 *
	 *     @type array  $slug__in List of slugs to include.
	 *     @type int    $wp_id Post ID of customized template.
	 *     @type string $post_type Post type to get the templates for.
	 * }
	 * @param string $template_type wp_template or wp_template_part.
	 */
	$templates = apply_filters( 'pre_get_block_templates', null, $query, $template_type );
	if ( ! is_null( $templates ) ) {
		return $templates;
	}

	$post_type     = isset( $query['post_type'] ) ? $query['post_type'] : '';
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

	if ( 'wp_template_part' === $template_type && isset( $query['area'] ) ) {
		$wp_query_args['tax_query'][]           = array(
			'taxonomy' => 'wp_template_part_area',
			'field'    => 'name',
			'terms'    => $query['area'],
		);
		$wp_query_args['tax_query']['relation'] = 'AND';
	}

	if ( isset( $query['slug__in'] ) ) {
		$wp_query_args['post_name__in'] = $query['slug__in'];
	}

	// This is only needed for the regular templates/template parts post type listing and editor.
	if ( isset( $query['wp_id'] ) ) {
		$wp_query_args['p'] = $query['wp_id'];
	} else {
		$wp_query_args['post_status'] = 'publish';
	}

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

		$query_result[] = $template;
	}

	if ( ! isset( $query['wp_id'] ) ) {
		$template_files = _get_block_templates_files( $template_type );
		foreach ( $template_files as $template_file ) {
			$template = _build_block_template_result_from_file( $template_file, $template_type );

			if ( $post_type && ! $template->is_custom ) {
				continue;
			}

			if ( $post_type &&
				isset( $template->post_types ) &&
				! in_array( $post_type, $template->post_types, true )
			) {
				continue;
			}

			$is_not_custom   = false === array_search(
				wp_get_theme()->get_stylesheet() . '//' . $template_file['slug'],
				wp_list_pluck( $query_result, 'id' ),
				true
			);
			$fits_slug_query =
				! isset( $query['slug__in'] ) || in_array( $template_file['slug'], $query['slug__in'], true );
			$fits_area_query =
				! isset( $query['area'] ) || $template_file['area'] === $query['area'];
			$should_include  = $is_not_custom && $fits_slug_query && $fits_area_query;
			if ( $should_include ) {
				$query_result[] = $template;
			}
		}
	}

	/**
	 * Filters the array of queried block templates array after they've been fetched.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Block_Template[] $query_result Array of found block templates.
	 * @param array  $query {
	 *     Optional. Arguments to retrieve templates.
	 *
	 *     @type array  $slug__in List of slugs to include.
	 *     @type int    $wp_id Post ID of customized template.
	 * }
	 * @param string $template_type wp_template or wp_template_part.
	 */
	return apply_filters( 'get_block_templates', $query_result, $query, $template_type );
}

/**
 * Retrieves a single unified template object using its id.
 *
 * @since 5.8.0
 *
 * @param string $id            Template unique identifier (example: theme_slug//template_slug).
 * @param string $template_type Optional. Template type: `'wp_template'` or '`wp_template_part'`.
 *                              Default `'wp_template'`.
 * @return WP_Block_Template|null Template.
 */
function get_block_template( $id, $template_type = 'wp_template' ) {
	/**
	 * Filters the block template object before the query takes place.
	 *
	 * Return a non-null value to bypass the WordPress queries.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Block_Template|null $block_template Return block template object to short-circuit the default query,
	 *                                               or null to allow WP to run its normal queries.
	 * @param string                 $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param string                 $template_type  Template type: `'wp_template'` or '`wp_template_part'`.
	 */
	$block_template = apply_filters( 'pre_get_block_template', null, $id, $template_type );
	if ( ! is_null( $block_template ) ) {
		return $block_template;
	}

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
		$template = _build_block_template_result_from_post( $posts[0] );

		if ( ! is_wp_error( $template ) ) {
			return $template;
		}
	}

	$block_template = get_block_file_template( $id, $template_type );

	/**
	 * Filters the queried block template object after it's been fetched.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Block_Template|null $block_template The found block template, or null if there isn't one.
	 * @param string                 $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param array                  $template_type  Template type: `'wp_template'` or '`wp_template_part'`.
	 */
	return apply_filters( 'get_block_template', $block_template, $id, $template_type );
}

/**
 * Retrieves a unified template object based on a theme file.
 *
 * This is a fallback of get_block_template(), used when no templates are found in the database.
 *
 * @since 5.9.0
 *
 * @param string $id            Template unique identifier (example: theme_slug//template_slug).
 * @param string $template_type Optional. Template type: `'wp_template'` or '`wp_template_part'`.
 *                              Default `'wp_template'`.
 * @return WP_Block_Template|null The found block template, or null if there isn't one.
 */
function get_block_file_template( $id, $template_type = 'wp_template' ) {
	/**
	 * Filters the block template object before the theme file discovery takes place.
	 *
	 * Return a non-null value to bypass the WordPress theme file discovery.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Block_Template|null $block_template Return block template object to short-circuit the default query,
	 *                                               or null to allow WP to run its normal queries.
	 * @param string                 $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param string                 $template_type  Template type: `'wp_template'` or '`wp_template_part'`.
	 */
	$block_template = apply_filters( 'pre_get_block_file_template', null, $id, $template_type );
	if ( ! is_null( $block_template ) ) {
		return $block_template;
	}

	$parts = explode( '//', $id, 2 );
	if ( count( $parts ) < 2 ) {
		/** This filter is documented in wp-includes/block-template-utils.php */
		return apply_filters( 'get_block_file_template', null, $id, $template_type );
	}
	list( $theme, $slug ) = $parts;

	if ( wp_get_theme()->get_stylesheet() !== $theme ) {
		/** This filter is documented in wp-includes/block-template-utils.php */
		return apply_filters( 'get_block_file_template', null, $id, $template_type );
	}

	$template_file = _get_block_template_file( $template_type, $slug );
	if ( null === $template_file ) {
		/** This filter is documented in wp-includes/block-template-utils.php */
		return apply_filters( 'get_block_file_template', null, $id, $template_type );
	}

	$block_template = _build_block_template_result_from_file( $template_file, $template_type );

	/**
	 * Filters the block template object after it has been (potentially) fetched from the theme file.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Block_Template|null $block_template The found block template, or null if there is none.
	 * @param string                 $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param string                 $template_type  Template type: `'wp_template'` or '`wp_template_part'`.
	 */
	return apply_filters( 'get_block_file_template', $block_template, $id, $template_type );
}

/**
 * Prints a block template part.
 *
 * @since 5.9.0
 *
 * @param string $part The block template part to print. Use "header" or "footer".
 */
function block_template_part( $part ) {
	$template_part = get_block_template( get_stylesheet() . '//' . $part, 'wp_template_part' );
	if ( ! $template_part || empty( $template_part->content ) ) {
		return;
	}
	echo do_blocks( $template_part->content );
}

/**
 * Prints the header block template part.
 *
 * @since 5.9.0
 */
function block_header_area() {
	block_template_part( 'header' );
}

/**
 * Prints the footer block template part.
 *
 * @since 5.9.0
 */
function block_footer_area() {
	block_template_part( 'footer' );
}

/**
 * Determines whether a theme directory should be ignored during export.
 *
 * @since 6.0.0
 *
 * @param string $path The path of the file in the theme.
 * @return Bool Whether this file is in an ignored directory.
 */
function wp_is_theme_directory_ignored( $path ) {
	$directories_to_ignore = array( '.DS_Store', '.svn', '.git', '.hg', '.bzr', 'node_modules', 'vendor' );

	foreach ( $directories_to_ignore as $directory ) {
		if ( str_starts_with( $path, $directory ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Creates an export of the current templates and
 * template parts from the site editor at the
 * specified path in a ZIP file.
 *
 * @since 5.9.0
 * @since 6.0.0 Adds the whole theme to the export archive.
 *
 * @return WP_Error|string Path of the ZIP file or error on failure.
 */
function wp_generate_block_templates_export_file() {
	if ( ! class_exists( 'ZipArchive' ) ) {
		return new WP_Error( 'missing_zip_package', __( 'Zip Export not supported.' ) );
	}

	$obscura    = wp_generate_password( 12, false, false );
	$theme_name = basename( get_stylesheet() );
	$filename   = get_temp_dir() . $theme_name . $obscura . '.zip';

	$zip = new ZipArchive();
	if ( true !== $zip->open( $filename, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
		return new WP_Error( 'unable_to_create_zip', __( 'Unable to open export file (archive) for writing.' ) );
	}

	$zip->addEmptyDir( 'templates' );
	$zip->addEmptyDir( 'parts' );

	// Get path of the theme.
	$theme_path = wp_normalize_path( get_stylesheet_directory() );

	// Create recursive directory iterator.
	$theme_files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $theme_path ),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	// Make a copy of the current theme.
	foreach ( $theme_files as $file ) {
		// Skip directories as they are added automatically.
		if ( ! $file->isDir() ) {
			// Get real and relative path for current file.
			$file_path     = wp_normalize_path( $file );
			$relative_path = substr( $file_path, strlen( $theme_path ) + 1 );

			if ( ! wp_is_theme_directory_ignored( $relative_path ) ) {
				$zip->addFile( $file_path, $relative_path );
			}
		}
	}

	// Load templates into the zip file.
	$templates = get_block_templates();
	foreach ( $templates as $template ) {
		$template->content = _remove_theme_attribute_in_block_template_content( $template->content );

		$zip->addFromString(
			'templates/' . $template->slug . '.html',
			$template->content
		);
	}

	// Load template parts into the zip file.
	$template_parts = get_block_templates( array(), 'wp_template_part' );
	foreach ( $template_parts as $template_part ) {
		$zip->addFromString(
			'parts/' . $template_part->slug . '.html',
			$template_part->content
		);
	}

	// Load theme.json into the zip file.
	$tree = WP_Theme_JSON_Resolver::get_theme_data( array(), array( 'with_supports' => false ) );
	// Merge with user data.
	$tree->merge( WP_Theme_JSON_Resolver::get_user_data() );

	$theme_json_raw = $tree->get_data();
	// If a version is defined, add a schema.
	if ( $theme_json_raw['version'] ) {
		global $wp_version;
		$theme_json_version = 'wp/' . substr( $wp_version, 0, 3 );
		$schema             = array( '$schema' => 'https://schemas.wp.org/' . $theme_json_version . '/theme.json' );
		$theme_json_raw     = array_merge( $schema, $theme_json_raw );
	}

	// Convert to a string.
	$theme_json_encoded = wp_json_encode( $theme_json_raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	// Replace 4 spaces with a tab.
	$theme_json_tabbed = preg_replace( '~(?:^|\G)\h{4}~m', "\t", $theme_json_encoded );

	// Add the theme.json file to the zip.
	$zip->addFromString(
		'theme.json',
		$theme_json_tabbed
	);

	// Save changes to the zip file.
	$zip->close();

	return $filename;
}

/**
 * Gets the template hierarchy for the given template slug to be created.
 *
 *
 * Note: Always add `index` as the last fallback template.
 *
 * @since 6.1.0
 *
 * @param string  $slug           The template slug to be created.
 * @param boolean $is_custom      Optional. Indicates if a template is custom or
 *                                part of the template hierarchy. Default false.
 * @param string $template_prefix Optional. The template prefix for the created template.
 *                                Used to extract the main template type, e.g.
 *                                in `taxonomy-books` the `taxonomy` is extracted.
 *                                Default empty string.
 * @return string[] The template hierarchy.
 */
function get_template_hierarchy( $slug, $is_custom = false, $template_prefix = '' ) {
	if ( 'index' === $slug ) {
		return array( 'index' );
	}
	if ( $is_custom ) {
		return array( 'page', 'singular', 'index' );
	}
	if ( 'front-page' === $slug ) {
		return array( 'front-page', 'home', 'index' );
	}

	$template_hierarchy = array( $slug );

	// Most default templates don't have `$template_prefix` assigned.
	if ( $template_prefix ) {
		list( $type ) = explode( '-', $template_prefix );
		// These checks are needed because the `$slug` above is always added.
		if ( ! in_array( $template_prefix, array( $slug, $type ), true ) ) {
			$template_hierarchy[] = $template_prefix;
		}
		if ( $slug !== $type ) {
			$template_hierarchy[] = $type;
		}
	}

	// Handle `archive` template.
	if (
		str_starts_with( $slug, 'author' ) ||
		str_starts_with( $slug, 'taxonomy' ) ||
		str_starts_with( $slug, 'category' ) ||
		str_starts_with( $slug, 'tag' ) ||
		'date' === $slug
	) {
		$template_hierarchy[] = 'archive';
	}
	// Handle `single` template.
	if ( 'attachment' === $slug ) {
		$template_hierarchy[] = 'single';
	}

	// Handle `singular` template.
	if (
		str_starts_with( $slug, 'single' ) ||
		str_starts_with( $slug, 'page' ) ||
		'attachment' === $slug
	) {
		$template_hierarchy[] = 'singular';
	}

	$template_hierarchy[] = 'index';

	return $template_hierarchy;
};
