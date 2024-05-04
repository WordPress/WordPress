<?php
/**
 * WP_Theme_JSON_Resolver class
 *
 * @package WordPress
 * @subpackage Theme
 * @since 5.8.0
 */

/**
 * Class that abstracts the processing of the different data sources
 * for site-level config and offers an API to work with them.
 *
 * This class is for internal core usage and is not supposed to be used by extenders (plugins and/or themes).
 * This is a low-level API that may need to do breaking changes. Please,
 * use get_global_settings(), get_global_styles(), and get_global_stylesheet() instead.
 *
 * @access private
 */
#[AllowDynamicProperties]
class WP_Theme_JSON_Resolver {

	/**
	 * Container for keep track of registered blocks.
	 *
	 * @since 6.1.0
	 * @var array
	 */
	protected static $blocks_cache = array(
		'core'   => array(),
		'blocks' => array(),
		'theme'  => array(),
		'user'   => array(),
	);

	/**
	 * Container for data coming from core.
	 *
	 * @since 5.8.0
	 * @var WP_Theme_JSON
	 */
	protected static $core = null;

	/**
	 * Container for data coming from the blocks.
	 *
	 * @since 6.1.0
	 * @var WP_Theme_JSON
	 */
	protected static $blocks = null;

	/**
	 * Container for data coming from the theme.
	 *
	 * @since 5.8.0
	 * @var WP_Theme_JSON
	 */
	protected static $theme = null;

	/**
	 * Container for data coming from the user.
	 *
	 * @since 5.9.0
	 * @var WP_Theme_JSON
	 */
	protected static $user = null;

	/**
	 * Stores the ID of the custom post type
	 * that holds the user data.
	 *
	 * @since 5.9.0
	 * @var int
	 */
	protected static $user_custom_post_type_id = null;

	/**
	 * Container to keep loaded i18n schema for `theme.json`.
	 *
	 * @since 5.8.0 As `$theme_json_i18n`.
	 * @since 5.9.0 Renamed from `$theme_json_i18n` to `$i18n_schema`.
	 * @var array
	 */
	protected static $i18n_schema = null;

	/**
	 * `theme.json` file cache.
	 *
	 * @since 6.1.0
	 * @var array
	 */
	protected static $theme_json_file_cache = array();

	/**
	 * Processes a file that adheres to the theme.json schema
	 * and returns an array with its contents, or a void array if none found.
	 *
	 * @since 5.8.0
	 * @since 6.1.0 Added caching.
	 *
	 * @param string $file_path Path to file. Empty if no file.
	 * @return array Contents that adhere to the theme.json schema.
	 */
	protected static function read_json_file( $file_path ) {
		if ( $file_path ) {
			if ( array_key_exists( $file_path, static::$theme_json_file_cache ) ) {
				return static::$theme_json_file_cache[ $file_path ];
			}

			$decoded_file = wp_json_file_decode( $file_path, array( 'associative' => true ) );
			if ( is_array( $decoded_file ) ) {
				static::$theme_json_file_cache[ $file_path ] = $decoded_file;
				return static::$theme_json_file_cache[ $file_path ];
			}
		}

		return array();
	}

	/**
	 * Returns a data structure used in theme.json translation.
	 *
	 * @since 5.8.0
	 * @deprecated 5.9.0
	 *
	 * @return array An array of theme.json fields that are translatable and the keys that are translatable.
	 */
	public static function get_fields_to_translate() {
		_deprecated_function( __METHOD__, '5.9.0' );
		return array();
	}

	/**
	 * Given a theme.json structure modifies it in place to update certain values
	 * by its translated strings according to the language set by the user.
	 *
	 * @since 5.8.0
	 *
	 * @param array  $theme_json The theme.json to translate.
	 * @param string $domain     Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                           Default 'default'.
	 * @return array Returns the modified $theme_json_structure.
	 */
	protected static function translate( $theme_json, $domain = 'default' ) {
		if ( null === static::$i18n_schema ) {
			$i18n_schema         = wp_json_file_decode( __DIR__ . '/theme-i18n.json' );
			static::$i18n_schema = null === $i18n_schema ? array() : $i18n_schema;
		}

		return translate_settings_using_i18n_schema( static::$i18n_schema, $theme_json, $domain );
	}

	/**
	 * Returns core's origin config.
	 *
	 * @since 5.8.0
	 *
	 * @return WP_Theme_JSON Entity that holds core data.
	 */
	public static function get_core_data() {
		if ( null !== static::$core && static::has_same_registered_blocks( 'core' ) ) {
			return static::$core;
		}

		$config = static::read_json_file( __DIR__ . '/theme.json' );
		$config = static::translate( $config );

		/**
		 * Filters the default data provided by WordPress for global styles & settings.
		 *
		 * @since 6.1.0
		 *
		 * @param WP_Theme_JSON_Data $theme_json Class to access and update the underlying data.
		 */
		$theme_json   = apply_filters( 'wp_theme_json_data_default', new WP_Theme_JSON_Data( $config, 'default' ) );
		$config       = $theme_json->get_data();
		static::$core = new WP_Theme_JSON( $config, 'default' );

		return static::$core;
	}

	/**
	 * Checks whether the registered blocks were already processed for this origin.
	 *
	 * @since 6.1.0
	 *
	 * @param string $origin Data source for which to cache the blocks.
	 *                       Valid values are 'core', 'blocks', 'theme', and 'user'.
	 * @return bool True on success, false otherwise.
	 */
	protected static function has_same_registered_blocks( $origin ) {
		// Bail out if the origin is invalid.
		if ( ! isset( static::$blocks_cache[ $origin ] ) ) {
			return false;
		}

		$registry = WP_Block_Type_Registry::get_instance();
		$blocks   = $registry->get_all_registered();

		// Is there metadata for all currently registered blocks?
		$block_diff = array_diff_key( $blocks, static::$blocks_cache[ $origin ] );
		if ( empty( $block_diff ) ) {
			return true;
		}

		foreach ( $blocks as $block_name => $block_type ) {
			static::$blocks_cache[ $origin ][ $block_name ] = true;
		}

		return false;
	}

	/**
	 * Returns the theme's data.
	 *
	 * Data from theme.json will be backfilled from existing
	 * theme supports, if any. Note that if the same data
	 * is present in theme.json and in theme supports,
	 * the theme.json takes precedence.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Theme supports have been inlined and the `$theme_support_data` argument removed.
	 * @since 6.0.0 Added an `$options` parameter to allow the theme data to be returned without theme supports.
	 *
	 * @param array $deprecated Deprecated. Not used.
	 * @param array $options {
	 *     Options arguments.
	 *
	 *     @type bool $with_supports Whether to include theme supports in the data. Default true.
	 * }
	 * @return WP_Theme_JSON Entity that holds theme data.
	 */
	public static function get_theme_data( $deprecated = array(), $options = array() ) {
		if ( ! empty( $deprecated ) ) {
			_deprecated_argument( __METHOD__, '5.9.0' );
		}

		$options = wp_parse_args( $options, array( 'with_supports' => true ) );

		if ( null === static::$theme || ! static::has_same_registered_blocks( 'theme' ) ) {
			$wp_theme        = wp_get_theme();
			$theme_json_file = $wp_theme->get_file_path( 'theme.json' );
			if ( is_readable( $theme_json_file ) ) {
				$theme_json_data = static::read_json_file( $theme_json_file );
				$theme_json_data = static::translate( $theme_json_data, $wp_theme->get( 'TextDomain' ) );
			} else {
				$theme_json_data = array();
			}

			/**
			 * Filters the data provided by the theme for global styles and settings.
			 *
			 * @since 6.1.0
			 *
			 * @param WP_Theme_JSON_Data $theme_json Class to access and update the underlying data.
			 */
			$theme_json      = apply_filters( 'wp_theme_json_data_theme', new WP_Theme_JSON_Data( $theme_json_data, 'theme' ) );
			$theme_json_data = $theme_json->get_data();
			static::$theme   = new WP_Theme_JSON( $theme_json_data );

			if ( $wp_theme->parent() ) {
				// Get parent theme.json.
				$parent_theme_json_file = $wp_theme->parent()->get_file_path( 'theme.json' );
				if ( $theme_json_file !== $parent_theme_json_file && is_readable( $parent_theme_json_file ) ) {
					$parent_theme_json_data = static::read_json_file( $parent_theme_json_file );
					$parent_theme_json_data = static::translate( $parent_theme_json_data, $wp_theme->parent()->get( 'TextDomain' ) );
					$parent_theme           = new WP_Theme_JSON( $parent_theme_json_data );

					/*
					 * Merge the child theme.json into the parent theme.json.
					 * The child theme takes precedence over the parent.
					 */
					$parent_theme->merge( static::$theme );
					static::$theme = $parent_theme;
				}
			}
		}

		if ( ! $options['with_supports'] ) {
			return static::$theme;
		}

		/*
		 * We want the presets and settings declared in theme.json
		 * to override the ones declared via theme supports.
		 * So we take theme supports, transform it to theme.json shape
		 * and merge the static::$theme upon that.
		 */
		$theme_support_data = WP_Theme_JSON::get_from_editor_settings( get_classic_theme_supports_block_editor_settings() );
		if ( ! wp_theme_has_theme_json() ) {
			if ( ! isset( $theme_support_data['settings']['color'] ) ) {
				$theme_support_data['settings']['color'] = array();
			}

			$default_palette = false;
			if ( current_theme_supports( 'default-color-palette' ) ) {
				$default_palette = true;
			}
			if ( ! isset( $theme_support_data['settings']['color']['palette'] ) ) {
				// If the theme does not have any palette, we still want to show the core one.
				$default_palette = true;
			}
			$theme_support_data['settings']['color']['defaultPalette'] = $default_palette;

			$default_gradients = false;
			if ( current_theme_supports( 'default-gradient-presets' ) ) {
				$default_gradients = true;
			}
			if ( ! isset( $theme_support_data['settings']['color']['gradients'] ) ) {
				// If the theme does not have any gradients, we still want to show the core ones.
				$default_gradients = true;
			}
			$theme_support_data['settings']['color']['defaultGradients'] = $default_gradients;

			if ( ! isset( $theme_support_data['settings']['shadow'] ) ) {
				$theme_support_data['settings']['shadow'] = array();
			}
			/*
			 * Shadow presets are explicitly disabled for classic themes until a
			 * decision is made for whether the default presets should match the
			 * other presets or if they should be disabled by default in classic
			 * themes. See https://github.com/WordPress/gutenberg/issues/59989.
			 */
			$theme_support_data['settings']['shadow']['defaultPresets'] = false;

			// Allow themes to enable link color setting via theme_support.
			if ( current_theme_supports( 'link-color' ) ) {
				$theme_support_data['settings']['color']['link'] = true;
			}

			// Allow themes to enable all border settings via theme_support.
			if ( current_theme_supports( 'border' ) ) {
				$theme_support_data['settings']['border']['color']  = true;
				$theme_support_data['settings']['border']['radius'] = true;
				$theme_support_data['settings']['border']['style']  = true;
				$theme_support_data['settings']['border']['width']  = true;
			}

			// Allow themes to enable appearance tools via theme_support.
			if ( current_theme_supports( 'appearance-tools' ) ) {
				$theme_support_data['settings']['appearanceTools'] = true;
			}
		}
		$with_theme_supports = new WP_Theme_JSON( $theme_support_data );
		$with_theme_supports->merge( static::$theme );
		return $with_theme_supports;
	}

	/**
	 * Gets the styles for blocks from the block.json file.
	 *
	 * @since 6.1.0
	 *
	 * @return WP_Theme_JSON
	 */
	public static function get_block_data() {
		$registry = WP_Block_Type_Registry::get_instance();
		$blocks   = $registry->get_all_registered();

		if ( null !== static::$blocks && static::has_same_registered_blocks( 'blocks' ) ) {
			return static::$blocks;
		}

		$config = array( 'version' => 2 );
		foreach ( $blocks as $block_name => $block_type ) {
			if ( isset( $block_type->supports['__experimentalStyle'] ) ) {
				$config['styles']['blocks'][ $block_name ] = static::remove_json_comments( $block_type->supports['__experimentalStyle'] );
			}

			if (
				isset( $block_type->supports['spacing']['blockGap']['__experimentalDefault'] ) &&
				! isset( $config['styles']['blocks'][ $block_name ]['spacing']['blockGap'] )
			) {
				/*
				 * Ensure an empty placeholder value exists for the block, if it provides a default blockGap value.
				 * The real blockGap value to be used will be determined when the styles are rendered for output.
				 */
				$config['styles']['blocks'][ $block_name ]['spacing']['blockGap'] = null;
			}
		}

		/**
		 * Filters the data provided by the blocks for global styles & settings.
		 *
		 * @since 6.1.0
		 *
		 * @param WP_Theme_JSON_Data $theme_json Class to access and update the underlying data.
		 */
		$theme_json = apply_filters( 'wp_theme_json_data_blocks', new WP_Theme_JSON_Data( $config, 'blocks' ) );
		$config     = $theme_json->get_data();

		static::$blocks = new WP_Theme_JSON( $config, 'blocks' );
		return static::$blocks;
	}

	/**
	 * When given an array, this will remove any keys with the name `//`.
	 *
	 * @since 6.1.0
	 *
	 * @param array $input_array The array to filter.
	 * @return array The filtered array.
	 */
	private static function remove_json_comments( $input_array ) {
		unset( $input_array['//'] );
		foreach ( $input_array as $k => $v ) {
			if ( is_array( $v ) ) {
				$input_array[ $k ] = static::remove_json_comments( $v );
			}
		}

		return $input_array;
	}

	/**
	 * Returns the custom post type that contains the user's origin config
	 * for the active theme or an empty array if none are found.
	 *
	 * This can also create and return a new draft custom post type.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Theme $theme              The theme object. If empty, it
	 *                                     defaults to the active theme.
	 * @param bool     $create_post        Optional. Whether a new custom post
	 *                                     type should be created if none are
	 *                                     found. Default false.
	 * @param array    $post_status_filter Optional. Filter custom post type by
	 *                                     post status. Default `array( 'publish' )`,
	 *                                     so it only fetches published posts.
	 * @return array Custom Post Type for the user's origin config.
	 */
	public static function get_user_data_from_wp_global_styles( $theme, $create_post = false, $post_status_filter = array( 'publish' ) ) {
		if ( ! $theme instanceof WP_Theme ) {
			$theme = wp_get_theme();
		}

		/*
		 * Bail early if the theme does not support a theme.json.
		 *
		 * Since wp_theme_has_theme_json() only supports the active
		 * theme, the extra condition for whether $theme is the active theme is
		 * present here.
		 */
		if ( $theme->get_stylesheet() === get_stylesheet() && ! wp_theme_has_theme_json() ) {
			return array();
		}

		$user_cpt         = array();
		$post_type_filter = 'wp_global_styles';
		$stylesheet       = $theme->get_stylesheet();
		$args             = array(
			'posts_per_page'         => 1,
			'orderby'                => 'date',
			'order'                  => 'desc',
			'post_type'              => $post_type_filter,
			'post_status'            => $post_status_filter,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy' => 'wp_theme',
					'field'    => 'name',
					'terms'    => $stylesheet,
				),
			),
		);

		$global_style_query = new WP_Query();
		$recent_posts       = $global_style_query->query( $args );
		if ( count( $recent_posts ) === 1 ) {
			$user_cpt = get_object_vars( $recent_posts[0] );
		} elseif ( $create_post ) {
			$cpt_post_id = wp_insert_post(
				array(
					'post_content' => '{"version": ' . WP_Theme_JSON::LATEST_SCHEMA . ', "isGlobalStylesUserThemeJSON": true }',
					'post_status'  => 'publish',
					'post_title'   => 'Custom Styles', // Do not make string translatable, see https://core.trac.wordpress.org/ticket/54518.
					'post_type'    => $post_type_filter,
					'post_name'    => sprintf( 'wp-global-styles-%s', urlencode( $stylesheet ) ),
					'tax_input'    => array(
						'wp_theme' => array( $stylesheet ),
					),
				),
				true
			);
			if ( ! is_wp_error( $cpt_post_id ) ) {
				$user_cpt = get_object_vars( get_post( $cpt_post_id ) );
			}
		}

		return $user_cpt;
	}

	/**
	 * Returns the user's origin config.
	 *
	 * @since 5.9.0
	 *
	 * @return WP_Theme_JSON Entity that holds styles for user data.
	 */
	public static function get_user_data() {
		if ( null !== static::$user && static::has_same_registered_blocks( 'user' ) ) {
			return static::$user;
		}

		$config   = array();
		$user_cpt = static::get_user_data_from_wp_global_styles( wp_get_theme() );

		if ( array_key_exists( 'post_content', $user_cpt ) ) {
			$decoded_data = json_decode( $user_cpt['post_content'], true );

			$json_decoding_error = json_last_error();
			if ( JSON_ERROR_NONE !== $json_decoding_error ) {
				trigger_error( 'Error when decoding a theme.json schema for user data. ' . json_last_error_msg() );
				/**
				 * Filters the data provided by the user for global styles & settings.
				 *
				 * @since 6.1.0
				 *
				 * @param WP_Theme_JSON_Data $theme_json Class to access and update the underlying data.
				 */
				$theme_json = apply_filters( 'wp_theme_json_data_user', new WP_Theme_JSON_Data( $config, 'custom' ) );
				$config     = $theme_json->get_data();
				return new WP_Theme_JSON( $config, 'custom' );
			}

			/*
			 * Very important to verify that the flag isGlobalStylesUserThemeJSON is true.
			 * If it's not true then the content was not escaped and is not safe.
			 */
			if (
				is_array( $decoded_data ) &&
				isset( $decoded_data['isGlobalStylesUserThemeJSON'] ) &&
				$decoded_data['isGlobalStylesUserThemeJSON']
			) {
				unset( $decoded_data['isGlobalStylesUserThemeJSON'] );
				$config = $decoded_data;
			}
		}

		/** This filter is documented in wp-includes/class-wp-theme-json-resolver.php */
		$theme_json   = apply_filters( 'wp_theme_json_data_user', new WP_Theme_JSON_Data( $config, 'custom' ) );
		$config       = $theme_json->get_data();
		static::$user = new WP_Theme_JSON( $config, 'custom' );

		return static::$user;
	}

	/**
	 * Returns the data merged from multiple origins.
	 *
	 * There are four sources of data (origins) for a site:
	 *
	 * - default => WordPress
	 * - blocks  => each one of the blocks provides data for itself
	 * - theme   => the active theme
	 * - custom  => data provided by the user
	 *
	 * The custom's has higher priority than the theme's, the theme's higher than blocks',
	 * and block's higher than default's.
	 *
	 * Unlike the getters
	 * {@link https://developer.wordpress.org/reference/classes/wp_theme_json_resolver/get_core_data/ get_core_data},
	 * {@link https://developer.wordpress.org/reference/classes/wp_theme_json_resolver/get_theme_data/ get_theme_data},
	 * and {@link https://developer.wordpress.org/reference/classes/wp_theme_json_resolver/get_user_data/ get_user_data},
	 * this method returns data after it has been merged with the previous origins.
	 * This means that if the same piece of data is declared in different origins
	 * (default, blocks, theme, custom), the last origin overrides the previous.
	 *
	 * For example, if the user has set a background color
	 * for the paragraph block, and the theme has done it as well,
	 * the user preference wins.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added user data, removed the `$settings` parameter,
	 *              added the `$origin` parameter.
	 * @since 6.1.0 Added block data and generation of spacingSizes array.
	 * @since 6.2.0 Changed ' $origin' parameter values to 'default', 'blocks', 'theme' or 'custom'.
	 *
	 * @param string $origin Optional. To what level should we merge data: 'default', 'blocks', 'theme' or 'custom'.
	 *                       'custom' is used as default value as well as fallback value if the origin is unknown.
	 * @return WP_Theme_JSON
	 */
	public static function get_merged_data( $origin = 'custom' ) {
		if ( is_array( $origin ) ) {
			_deprecated_argument( __FUNCTION__, '5.9.0' );
		}

		$result = new WP_Theme_JSON();
		$result->merge( static::get_core_data() );
		if ( 'default' === $origin ) {
			$result->set_spacing_sizes();
			return $result;
		}

		$result->merge( static::get_block_data() );
		if ( 'blocks' === $origin ) {
			return $result;
		}

		$result->merge( static::get_theme_data() );
		if ( 'theme' === $origin ) {
			$result->set_spacing_sizes();
			return $result;
		}

		$result->merge( static::get_user_data() );
		$result->set_spacing_sizes();

		return $result;
	}

	/**
	 * Returns the ID of the custom post type
	 * that stores user data.
	 *
	 * @since 5.9.0
	 *
	 * @return integer|null
	 */
	public static function get_user_global_styles_post_id() {
		if ( null !== static::$user_custom_post_type_id ) {
			return static::$user_custom_post_type_id;
		}

		$user_cpt = static::get_user_data_from_wp_global_styles( wp_get_theme(), true );

		if ( array_key_exists( 'ID', $user_cpt ) ) {
			static::$user_custom_post_type_id = $user_cpt['ID'];
		}

		return static::$user_custom_post_type_id;
	}

	/**
	 * Determines whether the active theme has a theme.json file.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added a check in the parent theme.
	 * @deprecated 6.2.0 Use wp_theme_has_theme_json() instead.
	 *
	 * @return bool
	 */
	public static function theme_has_support() {
		_deprecated_function( __METHOD__, '6.2.0', 'wp_theme_has_theme_json()' );

		return wp_theme_has_theme_json();
	}

	/**
	 * Builds the path to the given file and checks that it is readable.
	 *
	 * If it isn't, returns an empty string, otherwise returns the whole file path.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Adapted to work with child themes, added the `$template` argument.
	 *
	 * @param string $file_name Name of the file.
	 * @param bool   $template  Optional. Use template theme directory. Default false.
	 * @return string The whole file path or empty if the file doesn't exist.
	 */
	protected static function get_file_path_from_theme( $file_name, $template = false ) {
		$path      = $template ? get_template_directory() : get_stylesheet_directory();
		$candidate = $path . '/' . $file_name;

		return is_readable( $candidate ) ? $candidate : '';
	}

	/**
	 * Cleans the cached data so it can be recalculated.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `$user`, `$user_custom_post_type_id`,
	 *              and `$i18n_schema` variables to reset.
	 * @since 6.1.0 Added the `$blocks` and `$blocks_cache` variables
	 *              to reset.
	 */
	public static function clean_cached_data() {
		static::$core                     = null;
		static::$blocks                   = null;
		static::$blocks_cache             = array(
			'core'   => array(),
			'blocks' => array(),
			'theme'  => array(),
			'user'   => array(),
		);
		static::$theme                    = null;
		static::$user                     = null;
		static::$user_custom_post_type_id = null;
		static::$i18n_schema              = null;
	}

	/**
	 * Returns an array of all nested JSON files within a given directory.
	 *
	 * @since 6.2.0
	 *
	 * @param string $dir The directory to recursively iterate and list files of.
	 * @return array The merged array.
	 */
	private static function recursively_iterate_json( $dir ) {
		$nested_files      = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );
		$nested_json_files = iterator_to_array( new RegexIterator( $nested_files, '/^.+\.json$/i', RecursiveRegexIterator::GET_MATCH ) );
		return $nested_json_files;
	}


	/**
	 * Returns the style variations defined by the theme.
	 *
	 * @since 6.0.0
	 * @since 6.2.0 Returns parent theme variations if theme is a child.
	 *
	 * @return array
	 */
	public static function get_style_variations() {
		$variation_files    = array();
		$variations         = array();
		$base_directory     = get_stylesheet_directory() . '/styles';
		$template_directory = get_template_directory() . '/styles';
		if ( is_dir( $base_directory ) ) {
			$variation_files = static::recursively_iterate_json( $base_directory );
		}
		if ( is_dir( $template_directory ) && $template_directory !== $base_directory ) {
			$variation_files_parent = static::recursively_iterate_json( $template_directory );
			// If the child and parent variation file basename are the same, only include the child theme's.
			foreach ( $variation_files_parent as $parent_path => $parent ) {
				foreach ( $variation_files as $child_path => $child ) {
					if ( basename( $parent_path ) === basename( $child_path ) ) {
						unset( $variation_files_parent[ $parent_path ] );
					}
				}
			}
			$variation_files = array_merge( $variation_files, $variation_files_parent );
		}
		ksort( $variation_files );
		foreach ( $variation_files as $path => $file ) {
			$decoded_file = wp_json_file_decode( $path, array( 'associative' => true ) );
			if ( is_array( $decoded_file ) ) {
				$translated = static::translate( $decoded_file, wp_get_theme()->get( 'TextDomain' ) );
				$variation  = ( new WP_Theme_JSON( $translated ) )->get_raw_data();
				if ( empty( $variation['title'] ) ) {
					$variation['title'] = basename( $path, '.json' );
				}
				$variations[] = $variation;
			}
		}
		return $variations;
	}
}
