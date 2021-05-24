<?php
/**
 * Process of structures that adhere to the theme.json schema.
 *
 * @package WordPress
 */

/**
 * Class that encapsulates the processing of
 * structures that adhere to the theme.json spec.
 *
 * @access private
 */
class WP_Theme_JSON {

	/**
	 * Container of data in theme.json format.
	 *
	 * @var array
	 */
	private $theme_json = null;

	/**
	 * Holds the allowed block names extracted from block.json.
	 * Shared among all instances so we only process it once.
	 *
	 * @var array
	 */
	private static $allowed_block_names = null;

	const ALLOWED_TOP_LEVEL_KEYS = array(
		'version',
		'settings',
	);

	const ALLOWED_SETTINGS = array(
		'color'      => array(
			'custom'         => null,
			'customGradient' => null,
			'duotone'        => null,
			'gradients'      => null,
			'link'           => null,
			'palette'        => null,
		),
		'custom'     => null,
		'layout'     => null,
		'spacing'    => array(
			'customMargin'  => null,
			'customPadding' => null,
			'units'         => null,
		),
		'typography' => array(
			'customFontSize'   => null,
			'customLineHeight' => null,
			'dropCap'          => null,
			'fontSizes'        => null,
		),
	);

	const LATEST_SCHEMA = 1;

	/**
	 * Constructor.
	 *
	 * @param array $theme_json A structure that follows the theme.json schema.
	 */
	public function __construct( $theme_json = array() ) {
		if ( ! isset( $theme_json['version'] ) || self::LATEST_SCHEMA !== $theme_json['version'] ) {
			$this->theme_json = array();
			return;
		}

		$this->theme_json  = self::sanitize( $theme_json );
	}

	/**
	 * Returns the allowed block names.
	 *
	 * @return array
	 */
	private static function get_allowed_block_names() {
		if ( null !== self::$allowed_block_names ) {
			return self::$allowed_block_names;
		}

		self::$allowed_block_names = array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );

		return self::$allowed_block_names;
	}

	/**
	 * Sanitizes the input according to the schemas.
	 *
	 * @param array $input Structure to sanitize.
	 *
	 * @return array The sanitized output.
	 */
	private static function sanitize( $input ) {
		$output = array();

		if ( ! is_array( $input ) ) {
			return $output;
		}

		$allowed_blocks = self::get_allowed_block_names();

		$output = array_intersect_key( $input, array_flip( self::ALLOWED_TOP_LEVEL_KEYS ) );

		// Build the schema.
		$schema                 = array();
		$schema_settings_blocks = array();
		foreach ( $allowed_blocks as $block ) {
			$schema_settings_blocks[ $block ] = self::ALLOWED_SETTINGS;
		}
		$schema['settings']           = self::ALLOWED_SETTINGS;
		$schema['settings']['blocks'] = $schema_settings_blocks;

		// Remove anything that's not present in the schema.
		foreach ( array( 'settings' ) as $subtree ) {
			if ( ! isset( $input[ $subtree ] ) ) {
				continue;
			}

			if ( ! is_array( $input[ $subtree ] ) ) {
				unset( $output[ $subtree ] );
				continue;
			}

			$result = self::remove_keys_not_in_schema( $input[ $subtree ], $schema[ $subtree ] );

			if ( empty( $result ) ) {
				unset( $output[ $subtree ] );
			} else {
				$output[ $subtree ] = $result;
			}
		}

		return $output;
	}

	/**
	 * Given a tree, removes the keys that are not present in the schema.
	 *
	 * It is recursive and modifies the input in-place.
	 *
	 * @param array $tree Input to process.
	 * @param array $schema Schema to adhere to.
	 *
	 * @return array Returns the modified $tree.
	 */
	private static function remove_keys_not_in_schema( $tree, $schema ) {
		$tree = array_intersect_key( $tree, $schema );

		foreach ( $schema as $key => $data ) {
			if ( ! isset( $tree[ $key ] ) ) {
				continue;
			}

			if ( is_array( $schema[ $key ] ) && is_array( $tree[ $key ] ) ) {
				$tree[ $key ] = self::remove_keys_not_in_schema( $tree[ $key ], $schema[ $key ] );

				if ( empty( $tree[ $key ] ) ) {
					unset( $tree[ $key ] );
				}
			} elseif ( is_array( $schema[ $key ] ) && ! is_array( $tree[ $key ] ) ) {
				unset( $tree[ $key ] );
			}
		}

		return $tree;
	}

	/**
	 * Returns the existing settings for each block.
	 *
	 * Example:
	 *
	 * {
	 *   'root': {
	 *     'color': {
	 *       'custom': true
	 *     }
	 *   },
	 *   'core/paragraph': {
	 *     'spacing': {
	 *       'customPadding': true
	 *     }
	 *   }
	 * }
	 *
	 * @return array Settings per block.
	 */
	public function get_settings() {
		if ( ! isset( $this->theme_json['settings'] ) ) {
			return array();
		} else {
			return $this->theme_json['settings'];
		}
	}

	/**
	 * Builds metadata for the setting nodes, which returns in the form of:
	 *
	 * [
	 *   [
	 *     'path' => ['path', 'to', 'some', 'node' ]
	 *   ],
	 *   [
	 *     'path' => [ 'path', 'to', 'other', 'node' ]
	 *   ],
	 * ]
	 *
	 * @param array $theme_json The tree to extract setting nodes from.
	 *
	 * @return array
	 */
	private static function get_setting_nodes( $theme_json ) {
		$nodes = array();
		if ( ! isset( $theme_json['settings'] ) ) {
			return $nodes;
		}

		// Top-level.
		$nodes[] = array(
			'path' => array( 'settings' ),
		);

		// Calculate paths for blocks.
		if ( ! isset( $theme_json['settings']['blocks'] ) ) {
			return $nodes;
		}

		foreach ( $theme_json['settings']['blocks'] as $name => $node ) {
			$nodes[] = array(
				'path' => array( 'settings', 'blocks', $name ),
			);
		}

		return $nodes;
	}

	/**
	 * Merge new incoming data.
	 *
	 * @param WP_Theme_JSON $incoming Data to merge.
	 */
	public function merge( $incoming ) {
		$incoming_data    = $incoming->get_raw_data();
		$this->theme_json = array_replace_recursive( $this->theme_json, $incoming_data );

		// The array_replace_recursive algorithm merges at the leaf level.
		// For leaf values that are arrays it will use the numeric indexes for replacement.
		// In those cases, what we want is to use the incoming value, if it exists.
		//
		// These are the cases that have array values at the leaf levels.
		$properties   = array();
		$properties[] = array( 'color', 'palette' );
		$properties[] = array( 'color', 'gradients' );
		$properties[] = array( 'custom' );
		$properties[] = array( 'spacing', 'units' );
		$properties[] = array( 'typography', 'fontSizes' );
		$properties[] = array( 'typography', 'fontFamilies' );

		$nodes = self::get_setting_nodes( $this->theme_json );
		foreach ( $nodes as $metadata ) {
			foreach ( $properties as $property_path ) {
				$path = array_merge( $metadata['path'], $property_path );
				$node = _wp_array_get( $incoming_data, $path, array() );
				if ( ! empty( $node ) ) {
					_wp_array_set( $this->theme_json, $path, $node );
				}
			}
		}

	}

	/**
	 * Returns the raw data.
	 *
	 * @return array Raw data.
	 */
	public function get_raw_data() {
		return $this->theme_json;
	}

	/**
	 *
	 * Transforms the given editor settings according the
	 * add_theme_support format to the theme.json format.
	 *
	 * @param array $settings Existing editor settings.
	 *
	 * @return array Config that adheres to the theme.json schema.
	 */
	public static function get_from_editor_settings( $settings ) {
		$theme_settings = array(
			'version'  => self::LATEST_SCHEMA,
			'settings' => array(),
		);

		// Deprecated theme supports.
		if ( isset( $settings['disableCustomColors'] ) ) {
			if ( ! isset( $theme_settings['settings']['color'] ) ) {
				$theme_settings['settings']['color'] = array();
			}
			$theme_settings['settings']['color']['custom'] = ! $settings['disableCustomColors'];
		}

		if ( isset( $settings['disableCustomGradients'] ) ) {
			if ( ! isset( $theme_settings['settings']['color'] ) ) {
				$theme_settings['settings']['color'] = array();
			}
			$theme_settings['settings']['color']['customGradient'] = ! $settings['disableCustomGradients'];
		}

		if ( isset( $settings['disableCustomFontSizes'] ) ) {
			if ( ! isset( $theme_settings['settings']['typography'] ) ) {
				$theme_settings['settings']['typography'] = array();
			}
			$theme_settings['settings']['typography']['customFontSize'] = ! $settings['disableCustomFontSizes'];
		}

		if ( isset( $settings['enableCustomLineHeight'] ) ) {
			if ( ! isset( $theme_settings['settings']['typography'] ) ) {
				$theme_settings['settings']['typography'] = array();
			}
			$theme_settings['settings']['typography']['customLineHeight'] = $settings['enableCustomLineHeight'];
		}

		if ( isset( $settings['enableCustomUnits'] ) ) {
			if ( ! isset( $theme_settings['settings']['spacing'] ) ) {
				$theme_settings['settings']['spacing'] = array();
			}
			$theme_settings['settings']['spacing']['units'] = ( true === $settings['enableCustomUnits'] ) ?
				array( 'px', 'em', 'rem', 'vh', 'vw' ) :
				$settings['enableCustomUnits'];
		}

		if ( isset( $settings['colors'] ) ) {
			if ( ! isset( $theme_settings['settings']['color'] ) ) {
				$theme_settings['settings']['color'] = array();
			}
			$theme_settings['settings']['color']['palette'] = $settings['colors'];
		}

		if ( isset( $settings['gradients'] ) ) {
			if ( ! isset( $theme_settings['settings']['color'] ) ) {
				$theme_settings['settings']['color'] = array();
			}
			$theme_settings['settings']['color']['gradients'] = $settings['gradients'];
		}

		if ( isset( $settings['fontSizes'] ) ) {
			$font_sizes = $settings['fontSizes'];
			// Back-compatibility for presets without units.
			foreach ( $font_sizes as $key => $font_size ) {
				if ( is_numeric( $font_size['size'] ) ) {
					$font_sizes[ $key ]['size'] = $font_size['size'] . 'px';
				}
			}
			if ( ! isset( $theme_settings['settings']['typography'] ) ) {
				$theme_settings['settings']['typography'] = array();
			}
			$theme_settings['settings']['typography']['fontSizes'] = $font_sizes;
		}

		// This allows to make the plugin work with WordPress 5.7 beta
		// as well as lower versions. The second check can be removed
		// as soon as the minimum WordPress version for the plugin
		// is bumped to 5.7.
		if ( isset( $settings['enableCustomSpacing'] ) ) {
			if ( ! isset( $theme_settings['settings']['spacing'] ) ) {
				$theme_settings['settings']['spacing'] = array();
			}
			$theme_settings['settings']['spacing']['customPadding'] = $settings['enableCustomSpacing'];
		}

		// Things that didn't land in core yet, so didn't have a setting assigned.
		if ( current( (array) get_theme_support( 'experimental-link-color' ) ) ) {
			if ( ! isset( $theme_settings['settings']['color'] ) ) {
				$theme_settings['settings']['color'] = array();
			}
			$theme_settings['settings']['color']['link'] = true;
		}

		return $theme_settings;
	}

}
