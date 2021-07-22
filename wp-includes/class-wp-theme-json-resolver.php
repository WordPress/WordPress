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
 * @access private
 */
class WP_Theme_JSON_Resolver {

	/**
	 * Container for data coming from core.
	 *
	 * @since 5.8.0
	 * @var WP_Theme_JSON
	 */
	private static $core = null;

	/**
	 * Container for data coming from the theme.
	 *
	 * @since 5.8.0
	 * @var WP_Theme_JSON
	 */
	private static $theme = null;

	/**
	 * Whether or not the theme supports theme.json.
	 *
	 * @since 5.8.0
	 * @var bool
	 */
	private static $theme_has_support = null;

	/**
	 * Structure to hold i18n metadata.
	 *
	 * @since 5.8.0
	 * @var array
	 */
	private static $theme_json_i18n = null;

	/**
	 * Processes a file that adheres to the theme.json schema
	 * and returns an array with its contents, or a void array if none found.
	 *
	 * @since 5.8.0
	 *
	 * @param string $file_path Path to file. Empty if no file.
	 * @return array Contents that adhere to the theme.json schema.
	 */
	private static function read_json_file( $file_path ) {
		$config = array();
		if ( $file_path ) {
			$decoded_file = json_decode(
				file_get_contents( $file_path ),
				true
			);

			$json_decoding_error = json_last_error();
			if ( JSON_ERROR_NONE !== $json_decoding_error ) {
				trigger_error( "Error when decoding a theme.json schema at path $file_path " . json_last_error_msg() );
				return $config;
			}

			if ( is_array( $decoded_file ) ) {
				$config = $decoded_file;
			}
		}
		return $config;
	}

	/**
	 * Converts a tree as in i18n-theme.json into a linear array
	 * containing metadata to translate a theme.json file.
	 *
	 * For example, given this input:
	 *
	 *     {
	 *       "settings": {
	 *         "*": {
	 *           "typography": {
	 *             "fontSizes": [ { "name": "Font size name" } ],
	 *             "fontStyles": [ { "name": "Font size name" } ]
	 *           }
	 *         }
	 *       }
	 *     }
	 *
	 * will return this output:
	 *
	 *     [
	 *       0 => [
	 *         'path'    => [ 'settings', '*', 'typography', 'fontSizes' ],
	 *         'key'     => 'name',
	 *         'context' => 'Font size name'
	 *       ],
	 *       1 => [
	 *         'path'    => [ 'settings', '*', 'typography', 'fontStyles' ],
	 *         'key'     => 'name',
	 *         'context' => 'Font style name'
	 *       ]
	 *     ]
	 *
	 * @since 5.8.0
	 *
	 * @param array $i18n_partial A tree that follows the format of i18n-theme.json.
	 * @param array $current_path Optional. Keeps track of the path as we walk down the given tree.
	 *                            Default empty array.
	 * @return array A linear array containing the paths to translate.
	 */
	private static function extract_paths_to_translate( $i18n_partial, $current_path = array() ) {
		$result = array();
		foreach ( $i18n_partial as $property => $partial_child ) {
			if ( is_numeric( $property ) ) {
				foreach ( $partial_child as $key => $context ) {
					$result[] = array(
						'path'    => $current_path,
						'key'     => $key,
						'context' => $context,
					);
				}
				return $result;
			}
			$result = array_merge(
				$result,
				self::extract_paths_to_translate( $partial_child, array_merge( $current_path, array( $property ) ) )
			);
		}
		return $result;
	}

	/**
	 * Returns a data structure used in theme.json translation.
	 *
	 * @since 5.8.0
	 *
	 * @return array An array of theme.json fields that are translatable and the keys that are translatable.
	 */
	public static function get_fields_to_translate() {
		if ( null === self::$theme_json_i18n ) {
			$file_structure        = self::read_json_file( __DIR__ . '/theme-i18n.json' );
			self::$theme_json_i18n = self::extract_paths_to_translate( $file_structure );
		}
		return self::$theme_json_i18n;
	}

	/**
	 * Translates a chunk of the loaded theme.json structure.
	 *
	 * @since 5.8.0
	 *
	 * @param array  $array_to_translate The chunk of theme.json to translate.
	 * @param string $key                The key of the field that contains the string to translate.
	 * @param string $context            The context to apply in the translation call.
	 * @param string $domain             Text domain. Unique identifier for retrieving translated strings.
	 * @return array Returns the modified $theme_json chunk.
	 */
	private static function translate_theme_json_chunk( array $array_to_translate, $key, $context, $domain ) {
		foreach ( $array_to_translate as $item_key => $item_to_translate ) {
			if ( empty( $item_to_translate[ $key ] ) ) {
				continue;
			}

			// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralContext,WordPress.WP.I18n.NonSingularStringLiteralDomain
			$array_to_translate[ $item_key ][ $key ] = translate_with_gettext_context( $array_to_translate[ $item_key ][ $key ], $context, $domain );
		}

		return $array_to_translate;
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
	private static function translate( $theme_json, $domain = 'default' ) {
		$fields = self::get_fields_to_translate();
		foreach ( $fields as $field ) {
			$path    = $field['path'];
			$key     = $field['key'];
			$context = $field['context'];

			/*
			 * We need to process the paths that include '*' separately.
			 * One example of such a path would be:
			 * [ 'settings', 'blocks', '*', 'color', 'palette' ]
			 */
			$nodes_to_iterate = array_keys( $path, '*', true );
			if ( ! empty( $nodes_to_iterate ) ) {
				/*
				 * At the moment, we only need to support one '*' in the path, so take it directly.
				 * - base will be [ 'settings', 'blocks' ]
				 * - data will be [ 'color', 'palette' ]
				 */
				$base_path = array_slice( $path, 0, $nodes_to_iterate[0] );
				$data_path = array_slice( $path, $nodes_to_iterate[0] + 1 );
				$base_tree = _wp_array_get( $theme_json, $base_path, array() );
				foreach ( $base_tree as $node_name => $node_data ) {
					$array_to_translate = _wp_array_get( $node_data, $data_path, null );
					if ( is_null( $array_to_translate ) ) {
						continue;
					}

					// Whole path will be [ 'settings', 'blocks', 'core/paragraph', 'color', 'palette' ].
					$whole_path       = array_merge( $base_path, array( $node_name ), $data_path );
					$translated_array = self::translate_theme_json_chunk( $array_to_translate, $key, $context, $domain );
					_wp_array_set( $theme_json, $whole_path, $translated_array );
				}
			} else {
				$array_to_translate = _wp_array_get( $theme_json, $path, null );
				if ( is_null( $array_to_translate ) ) {
					continue;
				}

				$translated_array = self::translate_theme_json_chunk( $array_to_translate, $key, $context, $domain );
				_wp_array_set( $theme_json, $path, $translated_array );
			}
		}

		return $theme_json;
	}

	/**
	 * Return core's origin config.
	 *
	 * @since 5.8.0
	 *
	 * @return WP_Theme_JSON Entity that holds core data.
	 */
	public static function get_core_data() {
		if ( null !== self::$core ) {
			return self::$core;
		}

		$config     = self::read_json_file( __DIR__ . '/theme.json' );
		$config     = self::translate( $config );
		self::$core = new WP_Theme_JSON( $config, 'core' );

		return self::$core;
	}

	/**
	 * Returns the theme's data.
	 *
	 * Data from theme.json can be augmented via the $theme_support_data variable.
	 * This is useful, for example, to backfill the gaps in theme.json that a theme
	 * has declared via add_theme_supports.
	 *
	 * Note that if the same data is present in theme.json and in $theme_support_data,
	 * the theme.json's is not overwritten.
	 *
	 * @since 5.8.0
	 *
	 * @param array $theme_support_data Optional. Theme support data in theme.json format.
	 *                                  Default empty array.
	 * @return WP_Theme_JSON Entity that holds theme data.
	 */
	public static function get_theme_data( $theme_support_data = array() ) {
		if ( null === self::$theme ) {
			$theme_json_data = self::read_json_file( self::get_file_path_from_theme( 'theme.json' ) );
			$theme_json_data = self::translate( $theme_json_data, wp_get_theme()->get( 'TextDomain' ) );
			self::$theme     = new WP_Theme_JSON( $theme_json_data );
		}

		if ( empty( $theme_support_data ) ) {
			return self::$theme;
		}

		/*
		 * We want the presets and settings declared in theme.json
		 * to override the ones declared via add_theme_support.
		 */
		$with_theme_supports = new WP_Theme_JSON( $theme_support_data );
		$with_theme_supports->merge( self::$theme );

		return $with_theme_supports;
	}

	/**
	 * There are different sources of data for a site: core and theme.
	 *
	 * While the getters {@link get_core_data}, {@link get_theme_data} return the raw data
	 * from the respective origins, this method merges them all together.
	 *
	 * If the same piece of data is declared in different origins (core and theme),
	 * the last origin overrides the previous. For example, if core disables custom colors
	 * but a theme enables them, the theme config wins.
	 *
	 * @since 5.8.0
	 *
	 * @param array $settings Optional. Existing block editor settings. Default empty array.
	 * @return WP_Theme_JSON
	 */
	public static function get_merged_data( $settings = array() ) {
		$theme_support_data = WP_Theme_JSON::get_from_editor_settings( $settings );

		$result = new WP_Theme_JSON();
		$result->merge( self::get_core_data() );
		$result->merge( self::get_theme_data( $theme_support_data ) );

		return $result;
	}

	/**
	 * Whether the current theme has a theme.json file.
	 *
	 * @since 5.8.0
	 *
	 * @return bool
	 */
	public static function theme_has_support() {
		if ( ! isset( self::$theme_has_support ) ) {
			self::$theme_has_support = (bool) self::get_file_path_from_theme( 'theme.json' );
		}

		return self::$theme_has_support;
	}

	/**
	 * Builds the path to the given file and checks that it is readable.
	 *
	 * If it isn't, returns an empty string, otherwise returns the whole file path.
	 *
	 * @since 5.8.0
	 *
	 * @param string $file_name Name of the file.
	 * @return string The whole file path or empty if the file doesn't exist.
	 */
	private static function get_file_path_from_theme( $file_name ) {
		/*
		 * This used to be a locate_template call. However, that method proved problematic
		 * due to its use of constants (STYLESHEETPATH) that threw errors in some scenarios.
		 *
		 * When the theme.json merge algorithm properly supports child themes,
		 * this should also fall back to the template path, as locate_template did.
		 */
		$located   = '';
		$candidate = get_stylesheet_directory() . '/' . $file_name;
		if ( is_readable( $candidate ) ) {
			$located = $candidate;
		}
		return $located;
	}

	/**
	 * Cleans the cached data so it can be recalculated.
	 *
	 * @since 5.8.0
	 */
	public static function clean_cached_data() {
		self::$core              = null;
		self::$theme             = null;
		self::$theme_has_support = null;
		self::$theme_json_i18n   = null;
	}

}
