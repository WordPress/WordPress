<?php
/**
 * Register the block patterns and block patterns categories
 *
 * @package WordPress
 * @since 5.5.0
 */

add_theme_support( 'core-block-patterns' );

/**
 * Registers the core block patterns and categories.
 *
 * @since 5.5.0
 * @access private
 */
function _register_core_block_patterns_and_categories() {
	$should_register_core_patterns = get_theme_support( 'core-block-patterns' );

	if ( $should_register_core_patterns ) {
		$core_block_patterns = array(
			'query-standard-posts',
			'query-medium-posts',
			'query-small-posts',
			'query-grid-posts',
			'query-large-title-posts',
			'query-offset-posts',
			'social-links-shared-background-color',
		);

		foreach ( $core_block_patterns as $core_block_pattern ) {
			register_block_pattern(
				'core/' . $core_block_pattern,
				require __DIR__ . '/block-patterns/' . $core_block_pattern . '.php'
			);
		}
	}

	register_block_pattern_category( 'buttons', array( 'label' => _x( 'Buttons', 'Block pattern category' ) ) );
	register_block_pattern_category( 'columns', array( 'label' => _x( 'Columns', 'Block pattern category' ) ) );
	register_block_pattern_category( 'gallery', array( 'label' => _x( 'Gallery', 'Block pattern category' ) ) );
	register_block_pattern_category( 'header', array( 'label' => _x( 'Headers', 'Block pattern category' ) ) );
	register_block_pattern_category( 'text', array( 'label' => _x( 'Text', 'Block pattern category' ) ) );
	register_block_pattern_category( 'query', array( 'label' => _x( 'Query', 'Block pattern category' ) ) );
}

/**
 * Register Core's official patterns from wordpress.org/patterns.
 *
 * @since 5.8.0
 * @since 5.9.0 The $current_screen argument was removed.
 *
 * @param WP_Screen $deprecated Unused. Formerly the screen that the current request was triggered from.
 */
function _load_remote_block_patterns( $deprecated = null ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '5.9.0' );
		$current_screen = $deprecated;
		if ( ! $current_screen->is_block_editor ) {
			return;
		}
	}

	$supports_core_patterns = get_theme_support( 'core-block-patterns' );

	/**
	 * Filter to disable remote block patterns.
	 *
	 * @since 5.8.0
	 *
	 * @param bool $should_load_remote
	 */
	$should_load_remote = apply_filters( 'should_load_remote_block_patterns', true );

	if ( $supports_core_patterns && $should_load_remote ) {
		$request         = new WP_REST_Request( 'GET', '/wp/v2/pattern-directory/patterns' );
		$core_keyword_id = 11; // 11 is the ID for "core".
		$request->set_param( 'keyword', $core_keyword_id );
		$response = rest_do_request( $request );
		if ( $response->is_error() ) {
			return;
		}
		$patterns = $response->get_data();

		foreach ( $patterns as $settings ) {
			$pattern_name = 'core/' . sanitize_title( $settings['title'] );
			register_block_pattern( $pattern_name, (array) $settings );
		}
	}
}

/**
 * Register `Featured` (category) patterns from wordpress.org/patterns.
 *
 * @since 5.9.0
 */
function _load_remote_featured_patterns() {
	$supports_core_patterns = get_theme_support( 'core-block-patterns' );

	/** This filter is documented in wp-includes/block-patterns.php */
	$should_load_remote = apply_filters( 'should_load_remote_block_patterns', true );

	if ( ! $should_load_remote || ! $supports_core_patterns ) {
		return;
	}

	if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( 'featured' ) ) {
		register_block_pattern_category( 'featured', array( 'label' => __( 'Featured' ) ) );
	}

	$request         = new WP_REST_Request( 'GET', '/wp/v2/pattern-directory/patterns' );
	$featured_cat_id = 26; // This is the `Featured` category id from pattern directory.
	$request->set_param( 'category', $featured_cat_id );
	$response = rest_do_request( $request );
	if ( $response->is_error() ) {
		return;
	}
	$patterns = $response->get_data();

	foreach ( $patterns as $pattern ) {
		$pattern_name = sanitize_title( $pattern['title'] );
		$registry     = WP_Block_Patterns_Registry::get_instance();
		// Some patterns might be already registered as core patterns with the `core` prefix.
		$is_registered = $registry->is_registered( $pattern_name ) || $registry->is_registered( "core/$pattern_name" );
		if ( ! $is_registered ) {
			register_block_pattern( $pattern_name, (array) $pattern );
		}
	}
}

/**
 * Registers patterns from Pattern Directory provided by a theme's
 * `theme.json` file.
 *
 * @since 6.0.0
 * @access private
 */
function _register_remote_theme_patterns() {
	if ( ! get_theme_support( 'core-block-patterns' ) ) {
		return;
	}

	/** This filter is documented in wp-includes/block-patterns.php */
	if ( ! apply_filters( 'should_load_remote_block_patterns', true ) ) {
		return;
	}

	if ( ! WP_Theme_JSON_Resolver::theme_has_support() ) {
		return;
	}

	$pattern_settings = WP_Theme_JSON_Resolver::get_theme_data()->get_patterns();
	if ( empty( $pattern_settings ) ) {
		return;
	}

	$request         = new WP_REST_Request( 'GET', '/wp/v2/pattern-directory/patterns' );
	$request['slug'] = implode( ',', $pattern_settings );
	$response        = rest_do_request( $request );
	if ( $response->is_error() ) {
		return;
	}
	$patterns          = $response->get_data();
	$patterns_registry = WP_Block_Patterns_Registry::get_instance();
	foreach ( $patterns as $pattern ) {
		$pattern_name = sanitize_title( $pattern['title'] );
		// Some patterns might be already registered as core patterns with the `core` prefix.
		$is_registered = $patterns_registry->is_registered( $pattern_name ) || $patterns_registry->is_registered( "core/$pattern_name" );
		if ( ! $is_registered ) {
			register_block_pattern( $pattern_name, (array) $pattern );
		}
	}
}

/**
 * Register any patterns that the active theme may provide under its
 * `./patterns/` directory. Each pattern is defined as a PHP file and defines
 * its metadata using plugin-style headers. The minimum required definition is:
 *
 *     /**
 *      * Title: My Pattern
 *      * Slug: my-theme/my-pattern
 *      *
 *
 * The output of the PHP source corresponds to the content of the pattern, e.g.:
 *
 *     <main><p><?php echo "Hello"; ?></p></main>
 *
 * If applicable, this will collect from both parent and child theme.
 *
 * Other settable fields include:
 *
 *   - Description
 *   - Viewport Width
 *   - Categories       (comma-separated values)
 *   - Keywords         (comma-separated values)
 *   - Block Types      (comma-separated values)
 *   - Inserter         (yes/no)
 *
 * @since 6.0.0
 * @access private
 */
function _register_theme_block_patterns() {
	$default_headers = array(
		'title'         => 'Title',
		'slug'          => 'Slug',
		'description'   => 'Description',
		'viewportWidth' => 'Viewport Width',
		'categories'    => 'Categories',
		'keywords'      => 'Keywords',
		'blockTypes'    => 'Block Types',
		'inserter'      => 'Inserter',
	);

	/*
	 * Register patterns for the active theme. If the theme is a child theme,
	 * let it override any patterns from the parent theme that shares the same slug.
	 */
	$themes     = array();
	$stylesheet = get_stylesheet();
	$template   = get_template();
	if ( $stylesheet !== $template ) {
		$themes[] = wp_get_theme( $stylesheet );
	}
	$themes[] = wp_get_theme( $template );

	foreach ( $themes as $theme ) {
		$dirpath = $theme->get_stylesheet_directory() . '/patterns/';
		if ( ! is_dir( $dirpath ) || ! is_readable( $dirpath ) ) {
			continue;
		}
		if ( file_exists( $dirpath ) ) {
			$files = glob( $dirpath . '*.php' );
			if ( $files ) {
				foreach ( $files as $file ) {
					$pattern_data = get_file_data( $file, $default_headers );

					if ( empty( $pattern_data['slug'] ) ) {
						_doing_it_wrong(
							'_register_theme_block_patterns',
							sprintf(
								/* translators: %s: file name. */
								__( 'Could not register file "%s" as a block pattern ("Slug" field missing)' ),
								$file
							),
							'6.0.0'
						);
						continue;
					}

					if ( ! preg_match( '/^[A-z0-9\/_-]+$/', $pattern_data['slug'] ) ) {
						_doing_it_wrong(
							'_register_theme_block_patterns',
							sprintf(
								/* translators: %1s: file name; %2s: slug value found. */
								__( 'Could not register file "%1$s" as a block pattern (invalid slug "%2$s")' ),
								$file,
								$pattern_data['slug']
							),
							'6.0.0'
						);
					}

					if ( WP_Block_Patterns_Registry::get_instance()->is_registered( $pattern_data['slug'] ) ) {
						continue;
					}

					// Title is a required property.
					if ( ! $pattern_data['title'] ) {
						_doing_it_wrong(
							'_register_theme_block_patterns',
							sprintf(
								/* translators: %1s: file name; %2s: slug value found. */
								__( 'Could not register file "%s" as a block pattern ("Title" field missing)' ),
								$file
							),
							'6.0.0'
						);
						continue;
					}

					// For properties of type array, parse data as comma-separated.
					foreach ( array( 'categories', 'keywords', 'blockTypes' ) as $property ) {
						if ( ! empty( $pattern_data[ $property ] ) ) {
							$pattern_data[ $property ] = array_filter(
								preg_split(
									'/[\s,]+/',
									(string) $pattern_data[ $property ]
								)
							);
						} else {
							unset( $pattern_data[ $property ] );
						}
					}

					// Parse properties of type int.
					foreach ( array( 'viewportWidth' ) as $property ) {
						if ( ! empty( $pattern_data[ $property ] ) ) {
							$pattern_data[ $property ] = (int) $pattern_data[ $property ];
						} else {
							unset( $pattern_data[ $property ] );
						}
					}

					// Parse properties of type bool.
					foreach ( array( 'inserter' ) as $property ) {
						if ( ! empty( $pattern_data[ $property ] ) ) {
							$pattern_data[ $property ] = in_array(
								strtolower( $pattern_data[ $property ] ),
								array( 'yes', 'true' ),
								true
							);
						} else {
							unset( $pattern_data[ $property ] );
						}
					}

					// Translate the pattern metadata.
					$text_domain = $theme->get( 'TextDomain' );
					//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralContext, WordPress.WP.I18n.NonSingularStringLiteralDomain, WordPress.WP.I18n.LowLevelTranslationFunction
					$pattern_data['title'] = translate_with_gettext_context( $pattern_data['title'], 'Pattern title', $text_domain );
					if ( ! empty( $pattern_data['description'] ) ) {
						//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralContext, WordPress.WP.I18n.NonSingularStringLiteralDomain, WordPress.WP.I18n.LowLevelTranslationFunction
						$pattern_data['description'] = translate_with_gettext_context( $pattern_data['description'], 'Pattern description', $text_domain );
					}

					// The actual pattern content is the output of the file.
					ob_start();
					include $file;
					$pattern_data['content'] = ob_get_clean();
					if ( ! $pattern_data['content'] ) {
						continue;
					}

					register_block_pattern( $pattern_data['slug'], $pattern_data );
				}
			}
		}
	}
}
add_action( 'init', '_register_theme_block_patterns' );
