<?php
/**
 * WP_Theme_JSON class
 *
 * @package WordPress
 * @subpackage Theme
 * @since 5.8.0
 */

/**
 * Class that encapsulates the processing of structures that adhere to the theme.json spec.
 *
 * This class is for internal core usage and is not supposed to be used by extenders (plugins and/or themes).
 * This is a low-level API that may need to do breaking changes. Please,
 * use get_global_settings, get_global_styles, and get_global_stylesheet instead.
 *
 * @access private
 */
class WP_Theme_JSON {

	/**
	 * Container of data in theme.json format.
	 *
	 * @since 5.8.0
	 * @var array
	 */
	protected $theme_json = null;

	/**
	 * Holds block metadata extracted from block.json
	 * to be shared among all instances so we don't
	 * process it twice.
	 *
	 * @since 5.8.0
	 * @var array
	 */
	protected static $blocks_metadata = null;

	/**
	 * The CSS selector for the top-level styles.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	const ROOT_BLOCK_SELECTOR = 'body';

	/**
	 * The sources of data this object can represent.
	 *
	 * @since 5.8.0
	 * @var string[]
	 */
	const VALID_ORIGINS = array(
		'default',
		'theme',
		'custom',
	);

	/**
	 * Presets are a set of values that serve
	 * to bootstrap some styles: colors, font sizes, etc.
	 *
	 * They are a unkeyed array of values such as:
	 *
	 * ```php
	 * array(
	 *   array(
	 *     'slug'      => 'unique-name-within-the-set',
	 *     'name'      => 'Name for the UI',
	 *     <value_key> => 'value'
	 *   ),
	 * )
	 * ```
	 *
	 * This contains the necessary metadata to process them:
	 *
	 * - path              => where to find the preset within the settings section
	 * - override          => whether a theme preset with the same slug as a default preset
	 *                        can override it
	 * - use_default_names => whether to use the default names
	 * - value_key         => the key that represents the value
	 * - value_func        => optionally, instead of value_key, a function to generate
	 *                        the value that takes a preset as an argument
	 *                        (either value_key or value_func should be present)
	 * - css_vars          => template string to use in generating the CSS Custom Property.
	 *                        Example output: "--wp--preset--duotone--blue: <value>" will generate
	 *                        as many CSS Custom Properties as presets defined
	 *                        substituting the $slug for the slug's value for each preset value.
	 * - classes           => array containing a structure with the classes to
	 *                        generate for the presets, where for each array item
	 *                        the key is the class name and the value the property name.
	 *                        The "$slug" substring will be replaced by the slug of each preset.
	 *                        For example:
	 *                        'classes' => array(
	 *                           '.has-$slug-color'            => 'color',
	 *                           '.has-$slug-background-color' => 'background-color',
	 *                           '.has-$slug-border-color'     => 'border-color',
	 *                        )
	 * - properties        => array of CSS properties to be used by kses to
	 *                        validate the content of each preset
	 *                        by means of the remove_insecure_properties method.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `color.duotone` and `typography.fontFamilies` presets,
	 *              `use_default_names` preset key, and simplified the metadata structure.
	 * @var array
	 */
	const PRESETS_METADATA = array(
		array(
			'path'              => array( 'color', 'palette' ),
			'override'          => array( 'color', 'defaultPalette' ),
			'use_default_names' => false,
			'value_key'         => 'color',
			'css_vars'          => '--wp--preset--color--$slug',
			'classes'           => array(
				'.has-$slug-color'            => 'color',
				'.has-$slug-background-color' => 'background-color',
				'.has-$slug-border-color'     => 'border-color',
			),
			'properties'        => array( 'color', 'background-color', 'border-color' ),
		),
		array(
			'path'              => array( 'color', 'gradients' ),
			'override'          => array( 'color', 'defaultGradients' ),
			'use_default_names' => false,
			'value_key'         => 'gradient',
			'css_vars'          => '--wp--preset--gradient--$slug',
			'classes'           => array( '.has-$slug-gradient-background' => 'background' ),
			'properties'        => array( 'background' ),
		),
		array(
			'path'              => array( 'color', 'duotone' ),
			'override'          => true,
			'use_default_names' => false,
			'value_func'        => 'wp_get_duotone_filter_property',
			'css_vars'          => '--wp--preset--duotone--$slug',
			'classes'           => array(),
			'properties'        => array( 'filter' ),
		),
		array(
			'path'              => array( 'typography', 'fontSizes' ),
			'override'          => true,
			'use_default_names' => true,
			'value_key'         => 'size',
			'css_vars'          => '--wp--preset--font-size--$slug',
			'classes'           => array( '.has-$slug-font-size' => 'font-size' ),
			'properties'        => array( 'font-size' ),
		),
		array(
			'path'              => array( 'typography', 'fontFamilies' ),
			'override'          => true,
			'use_default_names' => false,
			'value_key'         => 'fontFamily',
			'css_vars'          => '--wp--preset--font-family--$slug',
			'classes'           => array( '.has-$slug-font-family' => 'font-family' ),
			'properties'        => array( 'font-family' ),
		),
	);

	/**
	 * Metadata for style properties.
	 *
	 * Each element is a direct mapping from the CSS property name to the
	 * path to the value in theme.json & block attributes.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `border-*`, `font-family`, `font-style`, `font-weight`,
	 *              `letter-spacing`, `margin-*`, `padding-*`, `--wp--style--block-gap`,
	 *              `text-decoration`, `text-transform`, and `filter` properties,
	 *              simplified the metadata structure.
	 * @var array
	 */
	const PROPERTIES_METADATA = array(
		'background'                 => array( 'color', 'gradient' ),
		'background-color'           => array( 'color', 'background' ),
		'border-radius'              => array( 'border', 'radius' ),
		'border-top-left-radius'     => array( 'border', 'radius', 'topLeft' ),
		'border-top-right-radius'    => array( 'border', 'radius', 'topRight' ),
		'border-bottom-left-radius'  => array( 'border', 'radius', 'bottomLeft' ),
		'border-bottom-right-radius' => array( 'border', 'radius', 'bottomRight' ),
		'border-color'               => array( 'border', 'color' ),
		'border-width'               => array( 'border', 'width' ),
		'border-style'               => array( 'border', 'style' ),
		'color'                      => array( 'color', 'text' ),
		'font-family'                => array( 'typography', 'fontFamily' ),
		'font-size'                  => array( 'typography', 'fontSize' ),
		'font-style'                 => array( 'typography', 'fontStyle' ),
		'font-weight'                => array( 'typography', 'fontWeight' ),
		'letter-spacing'             => array( 'typography', 'letterSpacing' ),
		'line-height'                => array( 'typography', 'lineHeight' ),
		'margin'                     => array( 'spacing', 'margin' ),
		'margin-top'                 => array( 'spacing', 'margin', 'top' ),
		'margin-right'               => array( 'spacing', 'margin', 'right' ),
		'margin-bottom'              => array( 'spacing', 'margin', 'bottom' ),
		'margin-left'                => array( 'spacing', 'margin', 'left' ),
		'padding'                    => array( 'spacing', 'padding' ),
		'padding-top'                => array( 'spacing', 'padding', 'top' ),
		'padding-right'              => array( 'spacing', 'padding', 'right' ),
		'padding-bottom'             => array( 'spacing', 'padding', 'bottom' ),
		'padding-left'               => array( 'spacing', 'padding', 'left' ),
		'--wp--style--block-gap'     => array( 'spacing', 'blockGap' ),
		'text-decoration'            => array( 'typography', 'textDecoration' ),
		'text-transform'             => array( 'typography', 'textTransform' ),
		'filter'                     => array( 'filter', 'duotone' ),
	);

	/**
	 * Protected style properties.
	 *
	 * These style properties are only rendered if a setting enables it
	 * via a value other than `null`.
	 *
	 * Each element maps the style property to the corresponding theme.json
	 * setting key.
	 *
	 * @since 5.9.0
	 */
	const PROTECTED_PROPERTIES = array(
		'spacing.blockGap' => array( 'spacing', 'blockGap' ),
	);

	/**
	 * The top-level keys a theme.json can have.
	 *
	 * @since 5.8.0 As `ALLOWED_TOP_LEVEL_KEYS`.
	 * @since 5.9.0 Renamed from `ALLOWED_TOP_LEVEL_KEYS` to `VALID_TOP_LEVEL_KEYS`,
	 *              added the `customTemplates` and `templateParts` values.
	 * @var string[]
	 */
	const VALID_TOP_LEVEL_KEYS = array(
		'customTemplates',
		'settings',
		'styles',
		'templateParts',
		'version',
	);

	/**
	 * The valid properties under the settings key.
	 *
	 * @since 5.8.0 As `ALLOWED_SETTINGS`.
	 * @since 5.9.0 Renamed from `ALLOWED_SETTINGS` to `VALID_SETTINGS`,
	 *              added new properties for `border`, `color`, `spacing`,
	 *              and `typography`, and renamed others according to the new schema.
	 * @var array
	 */
	const VALID_SETTINGS = array(
		'appearanceTools' => null,
		'border'          => array(
			'color'  => null,
			'radius' => null,
			'style'  => null,
			'width'  => null,
		),
		'color'           => array(
			'background'       => null,
			'custom'           => null,
			'customDuotone'    => null,
			'customGradient'   => null,
			'defaultGradients' => null,
			'defaultPalette'   => null,
			'duotone'          => null,
			'gradients'        => null,
			'link'             => null,
			'palette'          => null,
			'text'             => null,
		),
		'custom'          => null,
		'layout'          => array(
			'contentSize' => null,
			'wideSize'    => null,
		),
		'spacing'         => array(
			'blockGap' => null,
			'margin'   => null,
			'padding'  => null,
			'units'    => null,
		),
		'typography'      => array(
			'customFontSize' => null,
			'dropCap'        => null,
			'fontFamilies'   => null,
			'fontSizes'      => null,
			'fontStyle'      => null,
			'fontWeight'     => null,
			'letterSpacing'  => null,
			'lineHeight'     => null,
			'textDecoration' => null,
			'textTransform'  => null,
		),
	);

	/**
	 * The valid properties under the styles key.
	 *
	 * @since 5.8.0 As `ALLOWED_STYLES`.
	 * @since 5.9.0 Renamed from `ALLOWED_STYLES` to `VALID_STYLES`,
	 *              added new properties for `border`, `filter`, `spacing`,
	 *              and `typography`.
	 * @var array
	 */
	const VALID_STYLES = array(
		'border'     => array(
			'color'  => null,
			'radius' => null,
			'style'  => null,
			'width'  => null,
		),
		'color'      => array(
			'background' => null,
			'gradient'   => null,
			'text'       => null,
		),
		'filter'     => array(
			'duotone' => null,
		),
		'spacing'    => array(
			'margin'   => null,
			'padding'  => null,
			'blockGap' => 'top',
		),
		'typography' => array(
			'fontFamily'     => null,
			'fontSize'       => null,
			'fontStyle'      => null,
			'fontWeight'     => null,
			'letterSpacing'  => null,
			'lineHeight'     => null,
			'textDecoration' => null,
			'textTransform'  => null,
		),
	);

	/**
	 * The valid elements that can be found under styles.
	 *
	 * @since 5.8.0
	 * @var string[]
	 */
	const ELEMENTS = array(
		'link' => 'a',
		'h1'   => 'h1',
		'h2'   => 'h2',
		'h3'   => 'h3',
		'h4'   => 'h4',
		'h5'   => 'h5',
		'h6'   => 'h6',
	);

	/**
	 * The latest version of the schema in use.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Changed value from 1 to 2.
	 * @var int
	 */
	const LATEST_SCHEMA = 2;

	/**
	 * Constructor.
	 *
	 * @since 5.8.0
	 *
	 * @param array  $theme_json A structure that follows the theme.json schema.
	 * @param string $origin     Optional. What source of data this object represents.
	 *                           One of 'default', 'theme', or 'custom'. Default 'theme'.
	 */
	public function __construct( $theme_json = array(), $origin = 'theme' ) {
		if ( ! in_array( $origin, static::VALID_ORIGINS, true ) ) {
			$origin = 'theme';
		}

		$this->theme_json    = WP_Theme_JSON_Schema::migrate( $theme_json );
		$valid_block_names   = array_keys( static::get_blocks_metadata() );
		$valid_element_names = array_keys( static::ELEMENTS );
		$theme_json          = static::sanitize( $this->theme_json, $valid_block_names, $valid_element_names );
		$this->theme_json    = static::maybe_opt_in_into_settings( $theme_json );

		// Internally, presets are keyed by origin.
		$nodes = static::get_setting_nodes( $this->theme_json );
		foreach ( $nodes as $node ) {
			foreach ( static::PRESETS_METADATA as $preset_metadata ) {
				$path   = array_merge( $node['path'], $preset_metadata['path'] );
				$preset = _wp_array_get( $this->theme_json, $path, null );
				if ( null !== $preset ) {
					// If the preset is not already keyed by origin.
					if ( isset( $preset[0] ) || empty( $preset ) ) {
						_wp_array_set( $this->theme_json, $path, array( $origin => $preset ) );
					}
				}
			}
		}
	}

	/**
	 * Enables some opt-in settings if theme declared support.
	 *
	 * @since 5.9.0
	 *
	 * @param array $theme_json A theme.json structure to modify.
	 * @return array The modified theme.json structure.
	 */
	protected static function maybe_opt_in_into_settings( $theme_json ) {
		$new_theme_json = $theme_json;

		if (
			isset( $new_theme_json['settings']['appearanceTools'] ) &&
			true === $new_theme_json['settings']['appearanceTools']
		) {
			static::do_opt_in_into_settings( $new_theme_json['settings'] );
		}

		if ( isset( $new_theme_json['settings']['blocks'] ) && is_array( $new_theme_json['settings']['blocks'] ) ) {
			foreach ( $new_theme_json['settings']['blocks'] as &$block ) {
				if ( isset( $block['appearanceTools'] ) && ( true === $block['appearanceTools'] ) ) {
					static::do_opt_in_into_settings( $block );
				}
			}
		}

		return $new_theme_json;
	}

	/**
	 * Enables some settings.
	 *
	 * @since 5.9.0
	 *
	 * @param array $context The context to which the settings belong.
	 */
	protected static function do_opt_in_into_settings( &$context ) {
		$to_opt_in = array(
			array( 'border', 'color' ),
			array( 'border', 'radius' ),
			array( 'border', 'style' ),
			array( 'border', 'width' ),
			array( 'color', 'link' ),
			array( 'spacing', 'blockGap' ),
			array( 'spacing', 'margin' ),
			array( 'spacing', 'padding' ),
			array( 'typography', 'lineHeight' ),
		);

		foreach ( $to_opt_in as $path ) {
			// Use "unset prop" as a marker instead of "null" because
			// "null" can be a valid value for some props (e.g. blockGap).
			if ( 'unset prop' === _wp_array_get( $context, $path, 'unset prop' ) ) {
				_wp_array_set( $context, $path, true );
			}
		}

		unset( $context['appearanceTools'] );
	}

	/**
	 * Sanitizes the input according to the schemas.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `$valid_block_names` and `$valid_element_name` parameters.
	 *
	 * @param array $input               Structure to sanitize.
	 * @param array $valid_block_names   List of valid block names.
	 * @param array $valid_element_names List of valid element names.
	 * @return array The sanitized output.
	 */
	protected static function sanitize( $input, $valid_block_names, $valid_element_names ) {
		$output = array();

		if ( ! is_array( $input ) ) {
			return $output;
		}

		$output = array_intersect_key( $input, array_flip( static::VALID_TOP_LEVEL_KEYS ) );

		// Some styles are only meant to be available at the top-level (e.g.: blockGap),
		// hence, the schema for blocks & elements should not have them.
		$styles_non_top_level = static::VALID_STYLES;
		foreach ( array_keys( $styles_non_top_level ) as $section ) {
			foreach ( array_keys( $styles_non_top_level[ $section ] ) as $prop ) {
				if ( 'top' === $styles_non_top_level[ $section ][ $prop ] ) {
					unset( $styles_non_top_level[ $section ][ $prop ] );
				}
			}
		}

		// Build the schema based on valid block & element names.
		$schema                 = array();
		$schema_styles_elements = array();
		foreach ( $valid_element_names as $element ) {
			$schema_styles_elements[ $element ] = $styles_non_top_level;
		}
		$schema_styles_blocks   = array();
		$schema_settings_blocks = array();
		foreach ( $valid_block_names as $block ) {
			$schema_settings_blocks[ $block ]           = static::VALID_SETTINGS;
			$schema_styles_blocks[ $block ]             = $styles_non_top_level;
			$schema_styles_blocks[ $block ]['elements'] = $schema_styles_elements;
		}
		$schema['styles']             = static::VALID_STYLES;
		$schema['styles']['blocks']   = $schema_styles_blocks;
		$schema['styles']['elements'] = $schema_styles_elements;
		$schema['settings']           = static::VALID_SETTINGS;
		$schema['settings']['blocks'] = $schema_settings_blocks;

		// Remove anything that's not present in the schema.
		foreach ( array( 'styles', 'settings' ) as $subtree ) {
			if ( ! isset( $input[ $subtree ] ) ) {
				continue;
			}

			if ( ! is_array( $input[ $subtree ] ) ) {
				unset( $output[ $subtree ] );
				continue;
			}

			$result = static::remove_keys_not_in_schema( $input[ $subtree ], $schema[ $subtree ] );

			if ( empty( $result ) ) {
				unset( $output[ $subtree ] );
			} else {
				$output[ $subtree ] = $result;
			}
		}

		return $output;
	}

	/**
	 * Returns the metadata for each block.
	 *
	 * Example:
	 *
	 *     {
	 *       'core/paragraph': {
	 *         'selector': 'p',
	 *         'elements': {
	 *           'link' => 'link selector',
	 *           'etc'  => 'element selector'
	 *         }
	 *       },
	 *       'core/heading': {
	 *         'selector': 'h1',
	 *         'elements': {}
	 *       },
	 *       'core/image': {
	 *         'selector': '.wp-block-image',
	 *         'duotone': 'img',
	 *         'elements': {}
	 *       }
	 *     }
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added `duotone` key with CSS selector.
	 *
	 * @return array Block metadata.
	 */
	protected static function get_blocks_metadata() {
		if ( null !== static::$blocks_metadata ) {
			return static::$blocks_metadata;
		}

		static::$blocks_metadata = array();

		$registry = WP_Block_Type_Registry::get_instance();
		$blocks   = $registry->get_all_registered();
		foreach ( $blocks as $block_name => $block_type ) {
			if (
				isset( $block_type->supports['__experimentalSelector'] ) &&
				is_string( $block_type->supports['__experimentalSelector'] )
			) {
				static::$blocks_metadata[ $block_name ]['selector'] = $block_type->supports['__experimentalSelector'];
			} else {
				static::$blocks_metadata[ $block_name ]['selector'] = '.wp-block-' . str_replace( '/', '-', str_replace( 'core/', '', $block_name ) );
			}

			if (
				isset( $block_type->supports['color']['__experimentalDuotone'] ) &&
				is_string( $block_type->supports['color']['__experimentalDuotone'] )
			) {
				static::$blocks_metadata[ $block_name ]['duotone'] = $block_type->supports['color']['__experimentalDuotone'];
			}

			// Assign defaults, then overwrite those that the block sets by itself.
			// If the block selector is compounded, will append the element to each
			// individual block selector.
			$block_selectors = explode( ',', static::$blocks_metadata[ $block_name ]['selector'] );
			foreach ( static::ELEMENTS as $el_name => $el_selector ) {
				$element_selector = array();
				foreach ( $block_selectors as $selector ) {
					$element_selector[] = $selector . ' ' . $el_selector;
				}
				static::$blocks_metadata[ $block_name ]['elements'][ $el_name ] = implode( ',', $element_selector );
			}
		}

		return static::$blocks_metadata;
	}

	/**
	 * Given a tree, removes the keys that are not present in the schema.
	 *
	 * It is recursive and modifies the input in-place.
	 *
	 * @since 5.8.0
	 *
	 * @param array $tree   Input to process.
	 * @param array $schema Schema to adhere to.
	 * @return array Returns the modified $tree.
	 */
	protected static function remove_keys_not_in_schema( $tree, $schema ) {
		$tree = array_intersect_key( $tree, $schema );

		foreach ( $schema as $key => $data ) {
			if ( ! isset( $tree[ $key ] ) ) {
				continue;
			}

			if ( is_array( $schema[ $key ] ) && is_array( $tree[ $key ] ) ) {
				$tree[ $key ] = static::remove_keys_not_in_schema( $tree[ $key ], $schema[ $key ] );

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
	 *     {
	 *       'root': {
	 *         'color': {
	 *           'custom': true
	 *         }
	 *       },
	 *       'core/paragraph': {
	 *         'spacing': {
	 *           'customPadding': true
	 *         }
	 *       }
	 *     }
	 *
	 * @since 5.8.0
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
	 * Returns the stylesheet that results of processing
	 * the theme.json structure this object represents.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Removed the `$type` parameter`, added the `$types` and `$origins` parameters.
	 *
	 * @param array $types   Types of styles to load. Will load all by default. It accepts:
	 *                       - `variables`: only the CSS Custom Properties for presets & custom ones.
	 *                       - `styles`: only the styles section in theme.json.
	 *                       - `presets`: only the classes for the presets.
	 * @param array $origins A list of origins to include. By default it includes VALID_ORIGINS.
	 * @return string Stylesheet.
	 */
	public function get_stylesheet( $types = array( 'variables', 'styles', 'presets' ), $origins = null ) {
		if ( null === $origins ) {
			$origins = static::VALID_ORIGINS;
		}

		if ( is_string( $types ) ) {
			// Dispatch error and map old arguments to new ones.
			_deprecated_argument( __FUNCTION__, '5.9.0' );
			if ( 'block_styles' === $types ) {
				$types = array( 'styles', 'presets' );
			} elseif ( 'css_variables' === $types ) {
				$types = array( 'variables' );
			} else {
				$types = array( 'variables', 'styles', 'presets' );
			}
		}

		$blocks_metadata = static::get_blocks_metadata();
		$style_nodes     = static::get_style_nodes( $this->theme_json, $blocks_metadata );
		$setting_nodes   = static::get_setting_nodes( $this->theme_json, $blocks_metadata );

		$stylesheet = '';

		if ( in_array( 'variables', $types, true ) ) {
			$stylesheet .= $this->get_css_variables( $setting_nodes, $origins );
		}

		if ( in_array( 'styles', $types, true ) ) {
			$stylesheet .= $this->get_block_classes( $style_nodes );
		}

		if ( in_array( 'presets', $types, true ) ) {
			$stylesheet .= $this->get_preset_classes( $setting_nodes, $origins );
		}

		return $stylesheet;
	}

	/**
	 * Returns the page templates of the active theme.
	 *
	 * @since 5.9.0
	 *
	 * @return array
	 */
	public function get_custom_templates() {
		$custom_templates = array();
		if ( ! isset( $this->theme_json['customTemplates'] ) || ! is_array( $this->theme_json['customTemplates'] ) ) {
			return $custom_templates;
		}

		foreach ( $this->theme_json['customTemplates'] as $item ) {
			if ( isset( $item['name'] ) ) {
				$custom_templates[ $item['name'] ] = array(
					'title'     => isset( $item['title'] ) ? $item['title'] : '',
					'postTypes' => isset( $item['postTypes'] ) ? $item['postTypes'] : array( 'page' ),
				);
			}
		}
		return $custom_templates;
	}

	/**
	 * Returns the template part data of active theme.
	 *
	 * @since 5.9.0
	 *
	 * @return array
	 */
	public function get_template_parts() {
		$template_parts = array();
		if ( ! isset( $this->theme_json['templateParts'] ) || ! is_array( $this->theme_json['templateParts'] ) ) {
			return $template_parts;
		}

		foreach ( $this->theme_json['templateParts'] as $item ) {
			if ( isset( $item['name'] ) ) {
				$template_parts[ $item['name'] ] = array(
					'title' => isset( $item['title'] ) ? $item['title'] : '',
					'area'  => isset( $item['area'] ) ? $item['area'] : '',
				);
			}
		}
		return $template_parts;
	}

	/**
	 * Converts each style section into a list of rulesets
	 * containing the block styles to be appended to the stylesheet.
	 *
	 * See glossary at https://developer.mozilla.org/en-US/docs/Web/CSS/Syntax
	 *
	 * For each section this creates a new ruleset such as:
	 *
	 *   block-selector {
	 *     style-property-one: value;
	 *   }
	 *
	 * @since 5.8.0 As `get_block_styles()`.
	 * @since 5.9.0 Renamed from `get_block_styles()` to `get_block_classes()`
	 *              and no longer returns preset classes.
	 *              Removed the `$setting_nodes` parameter.
	 *
	 * @param array $style_nodes Nodes with styles.
	 * @return string The new stylesheet.
	 */
	protected function get_block_classes( $style_nodes ) {
		$block_rules = '';

		foreach ( $style_nodes as $metadata ) {
			if ( null === $metadata['selector'] ) {
				continue;
			}

			$node         = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			$selector     = $metadata['selector'];
			$settings     = _wp_array_get( $this->theme_json, array( 'settings' ) );
			$declarations = static::compute_style_properties( $node, $settings );

			// 1. Separate the ones who use the general selector
			// and the ones who use the duotone selector.
			$declarations_duotone = array();
			foreach ( $declarations as $index => $declaration ) {
				if ( 'filter' === $declaration['name'] ) {
					unset( $declarations[ $index ] );
					$declarations_duotone[] = $declaration;
				}
			}

			/*
			 * Reset default browser margin on the root body element.
			 * This is set on the root selector **before** generating the ruleset
			 * from the `theme.json`. This is to ensure that if the `theme.json` declares
			 * `margin` in its `spacing` declaration for the `body` element then these
			 * user-generated values take precedence in the CSS cascade.
			 * @link https://github.com/WordPress/gutenberg/issues/36147.
			 */
			if ( static::ROOT_BLOCK_SELECTOR === $selector ) {
				$block_rules .= 'body { margin: 0; }';
			}

			// 2. Generate the rules that use the general selector.
			$block_rules .= static::to_ruleset( $selector, $declarations );

			// 3. Generate the rules that use the duotone selector.
			if ( isset( $metadata['duotone'] ) && ! empty( $declarations_duotone ) ) {
				$selector_duotone = static::scope_selector( $metadata['selector'], $metadata['duotone'] );
				$block_rules     .= static::to_ruleset( $selector_duotone, $declarations_duotone );
			}

			if ( static::ROOT_BLOCK_SELECTOR === $selector ) {
				$block_rules .= '.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }';
				$block_rules .= '.wp-site-blocks > .alignright { float: right; margin-left: 2em; }';
				$block_rules .= '.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

				$has_block_gap_support = _wp_array_get( $this->theme_json, array( 'settings', 'spacing', 'blockGap' ) ) !== null;
				if ( $has_block_gap_support ) {
					$block_rules .= '.wp-site-blocks > * { margin-top: 0; margin-bottom: 0; }';
					$block_rules .= '.wp-site-blocks > * + * { margin-top: var( --wp--style--block-gap ); }';
				}
			}
		}

		return $block_rules;
	}

	/**
	 * Creates new rulesets as classes for each preset value such as:
	 *
	 *   .has-value-color {
	 *     color: value;
	 *   }
	 *
	 *   .has-value-background-color {
	 *     background-color: value;
	 *   }
	 *
	 *   .has-value-font-size {
	 *     font-size: value;
	 *   }
	 *
	 *   .has-value-gradient-background {
	 *     background: value;
	 *   }
	 *
	 *   p.has-value-gradient-background {
	 *     background: value;
	 *   }
	 *
	 * @since 5.9.0
	 *
	 * @param array $setting_nodes Nodes with settings.
	 * @param array $origins       List of origins to process presets from.
	 * @return string The new stylesheet.
	 */
	protected function get_preset_classes( $setting_nodes, $origins ) {
		$preset_rules = '';

		foreach ( $setting_nodes as $metadata ) {
			if ( null === $metadata['selector'] ) {
				continue;
			}

			$selector      = $metadata['selector'];
			$node          = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			$preset_rules .= static::compute_preset_classes( $node, $selector, $origins );
		}

		return $preset_rules;
	}

	/**
	 * Converts each styles section into a list of rulesets
	 * to be appended to the stylesheet.
	 * These rulesets contain all the css variables (custom variables and preset variables).
	 *
	 * See glossary at https://developer.mozilla.org/en-US/docs/Web/CSS/Syntax
	 *
	 * For each section this creates a new ruleset such as:
	 *
	 *     block-selector {
	 *       --wp--preset--category--slug: value;
	 *       --wp--custom--variable: value;
	 *     }
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `$origins` parameter.
	 *
	 * @param array $nodes   Nodes with settings.
	 * @param array $origins List of origins to process.
	 * @return string The new stylesheet.
	 */
	protected function get_css_variables( $nodes, $origins ) {
		$stylesheet = '';
		foreach ( $nodes as $metadata ) {
			if ( null === $metadata['selector'] ) {
				continue;
			}

			$selector = $metadata['selector'];

			$node         = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			$declarations = array_merge( static::compute_preset_vars( $node, $origins ), static::compute_theme_vars( $node ) );

			$stylesheet .= static::to_ruleset( $selector, $declarations );
		}

		return $stylesheet;
	}

	/**
	 * Given a selector and a declaration list,
	 * creates the corresponding ruleset.
	 *
	 * @since 5.8.0
	 *
	 * @param string $selector     CSS selector.
	 * @param array  $declarations List of declarations.
	 * @return string CSS ruleset.
	 */
	protected static function to_ruleset( $selector, $declarations ) {
		if ( empty( $declarations ) ) {
			return '';
		}

		$declaration_block = array_reduce(
			$declarations,
			static function ( $carry, $element ) {
				return $carry .= $element['name'] . ': ' . $element['value'] . ';'; },
			''
		);

		return $selector . '{' . $declaration_block . '}';
	}

	/**
	 * Function that appends a sub-selector to a existing one.
	 *
	 * Given the compounded $selector "h1, h2, h3"
	 * and the $to_append selector ".some-class" the result will be
	 * "h1.some-class, h2.some-class, h3.some-class".
	 *
	 * @since 5.8.0
	 *
	 * @param string $selector  Original selector.
	 * @param string $to_append Selector to append.
	 * @return string
	 */
	protected static function append_to_selector( $selector, $to_append ) {
		$new_selectors = array();
		$selectors     = explode( ',', $selector );
		foreach ( $selectors as $sel ) {
			$new_selectors[] = $sel . $to_append;
		}

		return implode( ',', $new_selectors );
	}

	/**
	 * Given a settings array, it returns the generated rulesets
	 * for the preset classes.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `$origins` parameter.
	 *
	 * @param array  $settings Settings to process.
	 * @param string $selector Selector wrapping the classes.
	 * @param array  $origins  List of origins to process.
	 * @return string The result of processing the presets.
	 */
	protected static function compute_preset_classes( $settings, $selector, $origins ) {
		if ( static::ROOT_BLOCK_SELECTOR === $selector ) {
			// Classes at the global level do not need any CSS prefixed,
			// and we don't want to increase its specificity.
			$selector = '';
		}

		$stylesheet = '';
		foreach ( static::PRESETS_METADATA as $preset_metadata ) {
			$slugs = static::get_settings_slugs( $settings, $preset_metadata, $origins );
			foreach ( $preset_metadata['classes'] as $class => $property ) {
				foreach ( $slugs as $slug ) {
					$css_var     = static::replace_slug_in_string( $preset_metadata['css_vars'], $slug );
					$class_name  = static::replace_slug_in_string( $class, $slug );
					$stylesheet .= static::to_ruleset(
						static::append_to_selector( $selector, $class_name ),
						array(
							array(
								'name'  => $property,
								'value' => 'var(' . $css_var . ') !important',
							),
						)
					);
				}
			}
		}

		return $stylesheet;
	}

	/**
	 * Function that scopes a selector with another one. This works a bit like
	 * SCSS nesting except the `&` operator isn't supported.
	 *
	 * <code>
	 * $scope = '.a, .b .c';
	 * $selector = '> .x, .y';
	 * $merged = scope_selector( $scope, $selector );
	 * // $merged is '.a > .x, .a .y, .b .c > .x, .b .c .y'
	 * </code>
	 *
	 * @since 5.9.0
	 *
	 * @param string $scope    Selector to scope to.
	 * @param string $selector Original selector.
	 * @return string Scoped selector.
	 */
	protected static function scope_selector( $scope, $selector ) {
		$scopes    = explode( ',', $scope );
		$selectors = explode( ',', $selector );

		$selectors_scoped = array();
		foreach ( $scopes as $outer ) {
			foreach ( $selectors as $inner ) {
				$selectors_scoped[] = trim( $outer ) . ' ' . trim( $inner );
			}
		}

		return implode( ', ', $selectors_scoped );
	}

	/**
	 * Gets preset values keyed by slugs based on settings and metadata.
	 *
	 * <code>
	 * $settings = array(
	 *     'typography' => array(
	 *         'fontFamilies' => array(
	 *             array(
	 *                 'slug'       => 'sansSerif',
	 *                 'fontFamily' => '"Helvetica Neue", sans-serif',
	 *             ),
	 *             array(
	 *                 'slug'   => 'serif',
	 *                 'colors' => 'Georgia, serif',
	 *             )
	 *         ),
	 *     ),
	 * );
	 * $meta = array(
	 *    'path'      => array( 'typography', 'fontFamilies' ),
	 *    'value_key' => 'fontFamily',
	 * );
	 * $values_by_slug = get_settings_values_by_slug();
	 * // $values_by_slug === array(
	 * //   'sans-serif' => '"Helvetica Neue", sans-serif',
	 * //   'serif'      => 'Georgia, serif',
	 * // );
	 * </code>
	 *
	 * @since 5.9.0
	 *
	 * @param array $settings        Settings to process.
	 * @param array $preset_metadata One of the PRESETS_METADATA values.
	 * @param array $origins         List of origins to process.
	 * @return array Array of presets where each key is a slug and each value is the preset value.
	 */
	protected static function get_settings_values_by_slug( $settings, $preset_metadata, $origins ) {
		$preset_per_origin = _wp_array_get( $settings, $preset_metadata['path'], array() );

		$result = array();
		foreach ( $origins as $origin ) {
			if ( ! isset( $preset_per_origin[ $origin ] ) ) {
				continue;
			}
			foreach ( $preset_per_origin[ $origin ] as $preset ) {
				$slug = _wp_to_kebab_case( $preset['slug'] );

				$value = '';
				if ( isset( $preset_metadata['value_key'], $preset[ $preset_metadata['value_key'] ] ) ) {
					$value_key = $preset_metadata['value_key'];
					$value     = $preset[ $value_key ];
				} elseif (
					isset( $preset_metadata['value_func'] ) &&
					is_callable( $preset_metadata['value_func'] )
				) {
					$value_func = $preset_metadata['value_func'];
					$value      = call_user_func( $value_func, $preset );
				} else {
					// If we don't have a value, then don't add it to the result.
					continue;
				}

				$result[ $slug ] = $value;
			}
		}
		return $result;
	}

	/**
	 * Similar to get_settings_values_by_slug, but doesn't compute the value.
	 *
	 * @since 5.9.0
	 *
	 * @param array $settings        Settings to process.
	 * @param array $preset_metadata One of the PRESETS_METADATA values.
	 * @param array $origins         List of origins to process.
	 * @return array Array of presets where the key and value are both the slug.
	 */
	protected static function get_settings_slugs( $settings, $preset_metadata, $origins = null ) {
		if ( null === $origins ) {
			$origins = static::VALID_ORIGINS;
		}

		$preset_per_origin = _wp_array_get( $settings, $preset_metadata['path'], array() );

		$result = array();
		foreach ( $origins as $origin ) {
			if ( ! isset( $preset_per_origin[ $origin ] ) ) {
				continue;
			}
			foreach ( $preset_per_origin[ $origin ] as $preset ) {
				$slug = _wp_to_kebab_case( $preset['slug'] );

				// Use the array as a set so we don't get duplicates.
				$result[ $slug ] = $slug;
			}
		}
		return $result;
	}

	/**
	 * Transform a slug into a CSS Custom Property.
	 *
	 * @since 5.9.0
	 *
	 * @param string $input String to replace.
	 * @param string $slug  The slug value to use to generate the custom property.
	 * @return string The CSS Custom Property. Something along the lines of `--wp--preset--color--black`.
	 */
	protected static function replace_slug_in_string( $input, $slug ) {
		return strtr( $input, array( '$slug' => $slug ) );
	}

	/**
	 * Given the block settings, it extracts the CSS Custom Properties
	 * for the presets and adds them to the $declarations array
	 * following the format:
	 *
	 *     array(
	 *       'name'  => 'property_name',
	 *       'value' => 'property_value,
	 *     )
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `$origins` parameter.
	 *
	 * @param array $settings Settings to process.
	 * @param array $origins  List of origins to process.
	 * @return array Returns the modified $declarations.
	 */
	protected static function compute_preset_vars( $settings, $origins ) {
		$declarations = array();
		foreach ( static::PRESETS_METADATA as $preset_metadata ) {
			$values_by_slug = static::get_settings_values_by_slug( $settings, $preset_metadata, $origins );
			foreach ( $values_by_slug as $slug => $value ) {
				$declarations[] = array(
					'name'  => static::replace_slug_in_string( $preset_metadata['css_vars'], $slug ),
					'value' => $value,
				);
			}
		}

		return $declarations;
	}

	/**
	 * Given an array of settings, it extracts the CSS Custom Properties
	 * for the custom values and adds them to the $declarations
	 * array following the format:
	 *
	 *     array(
	 *       'name'  => 'property_name',
	 *       'value' => 'property_value,
	 *     )
	 *
	 * @since 5.8.0
	 *
	 * @param array $settings Settings to process.
	 * @return array Returns the modified $declarations.
	 */
	protected static function compute_theme_vars( $settings ) {
		$declarations  = array();
		$custom_values = _wp_array_get( $settings, array( 'custom' ), array() );
		$css_vars      = static::flatten_tree( $custom_values );
		foreach ( $css_vars as $key => $value ) {
			$declarations[] = array(
				'name'  => '--wp--custom--' . $key,
				'value' => $value,
			);
		}

		return $declarations;
	}

	/**
	 * Given a tree, it creates a flattened one
	 * by merging the keys and binding the leaf values
	 * to the new keys.
	 *
	 * It also transforms camelCase names into kebab-case
	 * and substitutes '/' by '-'.
	 *
	 * This is thought to be useful to generate
	 * CSS Custom Properties from a tree,
	 * although there's nothing in the implementation
	 * of this function that requires that format.
	 *
	 * For example, assuming the given prefix is '--wp'
	 * and the token is '--', for this input tree:
	 *
	 *     {
	 *       'some/property': 'value',
	 *       'nestedProperty': {
	 *         'sub-property': 'value'
	 *       }
	 *     }
	 *
	 * it'll return this output:
	 *
	 *     {
	 *       '--wp--some-property': 'value',
	 *       '--wp--nested-property--sub-property': 'value'
	 *     }
	 *
	 * @since 5.8.0
	 *
	 * @param array  $tree   Input tree to process.
	 * @param string $prefix Optional. Prefix to prepend to each variable. Default empty string.
	 * @param string $token  Optional. Token to use between levels. Default '--'.
	 * @return array The flattened tree.
	 */
	protected static function flatten_tree( $tree, $prefix = '', $token = '--' ) {
		$result = array();
		foreach ( $tree as $property => $value ) {
			$new_key = $prefix . str_replace(
				'/',
				'-',
				strtolower( _wp_to_kebab_case( $property ) )
			);

			if ( is_array( $value ) ) {
				$new_prefix = $new_key . $token;
				$result     = array_merge(
					$result,
					static::flatten_tree( $value, $new_prefix, $token )
				);
			} else {
				$result[ $new_key ] = $value;
			}
		}
		return $result;
	}

	/**
	 * Given a styles array, it extracts the style properties
	 * and adds them to the $declarations array following the format:
	 *
	 *     array(
	 *       'name'  => 'property_name',
	 *       'value' => 'property_value,
	 *     )
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `$settings` and `$properties` parameters.
	 *
	 * @param array $styles    Styles to process.
	 * @param array $settings  Theme settings.
	 * @param array $properties Properties metadata.
	 * @return array Returns the modified $declarations.
	 */
	protected static function compute_style_properties( $styles, $settings = array(), $properties = null ) {
		if ( null === $properties ) {
			$properties = static::PROPERTIES_METADATA;
		}

		$declarations = array();
		if ( empty( $styles ) ) {
			return $declarations;
		}

		foreach ( $properties as $css_property => $value_path ) {
			$value = static::get_property_value( $styles, $value_path );

			// Look up protected properties, keyed by value path.
			// Skip protected properties that are explicitly set to `null`.
			if ( is_array( $value_path ) ) {
				$path_string = implode( '.', $value_path );
				if (
					array_key_exists( $path_string, static::PROTECTED_PROPERTIES ) &&
					_wp_array_get( $settings, static::PROTECTED_PROPERTIES[ $path_string ], null ) === null
				) {
					continue;
				}
			}

			// Skip if empty and not "0" or value represents array of longhand values.
			$has_missing_value = empty( $value ) && ! is_numeric( $value );
			if ( $has_missing_value || is_array( $value ) ) {
				continue;
			}

			$declarations[] = array(
				'name'  => $css_property,
				'value' => $value,
			);
		}

		return $declarations;
	}

	/**
	 * Returns the style property for the given path.
	 *
	 * It also converts CSS Custom Property stored as
	 * "var:preset|color|secondary" to the form
	 * "--wp--preset--color--secondary".
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added support for values of array type, which are returned as is.
	 *
	 * @param array $styles Styles subtree.
	 * @param array $path   Which property to process.
	 * @return string|array Style property value.
	 */
	protected static function get_property_value( $styles, $path ) {
		$value = _wp_array_get( $styles, $path, '' );

		if ( '' === $value || is_array( $value ) ) {
			return $value;
		}

		$prefix     = 'var:';
		$prefix_len = strlen( $prefix );
		$token_in   = '|';
		$token_out  = '--';
		if ( 0 === strncmp( $value, $prefix, $prefix_len ) ) {
			$unwrapped_name = str_replace(
				$token_in,
				$token_out,
				substr( $value, $prefix_len )
			);
			$value          = "var(--wp--$unwrapped_name)";
		}

		return $value;
	}

	/**
	 * Builds metadata for the setting nodes, which returns in the form of:
	 *
	 *     [
	 *       [
	 *         'path'     => ['path', 'to', 'some', 'node' ],
	 *         'selector' => 'CSS selector for some node'
	 *       ],
	 *       [
	 *         'path'     => [ 'path', 'to', 'other', 'node' ],
	 *         'selector' => 'CSS selector for other node'
	 *       ],
	 *     ]
	 *
	 * @since 5.8.0
	 *
	 * @param array $theme_json The tree to extract setting nodes from.
	 * @param array $selectors  List of selectors per block.
	 * @return array
	 */
	protected static function get_setting_nodes( $theme_json, $selectors = array() ) {
		$nodes = array();
		if ( ! isset( $theme_json['settings'] ) ) {
			return $nodes;
		}

		// Top-level.
		$nodes[] = array(
			'path'     => array( 'settings' ),
			'selector' => static::ROOT_BLOCK_SELECTOR,
		);

		// Calculate paths for blocks.
		if ( ! isset( $theme_json['settings']['blocks'] ) ) {
			return $nodes;
		}

		foreach ( $theme_json['settings']['blocks'] as $name => $node ) {
			$selector = null;
			if ( isset( $selectors[ $name ]['selector'] ) ) {
				$selector = $selectors[ $name ]['selector'];
			}

			$nodes[] = array(
				'path'     => array( 'settings', 'blocks', $name ),
				'selector' => $selector,
			);
		}

		return $nodes;
	}

	/**
	 * Builds metadata for the style nodes, which returns in the form of:
	 *
	 *     [
	 *       [
	 *         'path'     => [ 'path', 'to', 'some', 'node' ],
	 *         'selector' => 'CSS selector for some node',
	 *         'duotone'  => 'CSS selector for duotone for some node'
	 *       ],
	 *       [
	 *         'path'     => ['path', 'to', 'other', 'node' ],
	 *         'selector' => 'CSS selector for other node',
	 *         'duotone'  => null
	 *       ],
	 *     ]
	 *
	 * @since 5.8.0
	 *
	 * @param array $theme_json The tree to extract style nodes from.
	 * @param array $selectors  List of selectors per block.
	 * @return array
	 */
	protected static function get_style_nodes( $theme_json, $selectors = array() ) {
		$nodes = array();
		if ( ! isset( $theme_json['styles'] ) ) {
			return $nodes;
		}

		// Top-level.
		$nodes[] = array(
			'path'     => array( 'styles' ),
			'selector' => static::ROOT_BLOCK_SELECTOR,
		);

		if ( isset( $theme_json['styles']['elements'] ) ) {
			foreach ( $theme_json['styles']['elements'] as $element => $node ) {
				$nodes[] = array(
					'path'     => array( 'styles', 'elements', $element ),
					'selector' => static::ELEMENTS[ $element ],
				);
			}
		}

		// Blocks.
		if ( ! isset( $theme_json['styles']['blocks'] ) ) {
			return $nodes;
		}

		foreach ( $theme_json['styles']['blocks'] as $name => $node ) {
			$selector = null;
			if ( isset( $selectors[ $name ]['selector'] ) ) {
				$selector = $selectors[ $name ]['selector'];
			}

			$duotone_selector = null;
			if ( isset( $selectors[ $name ]['duotone'] ) ) {
				$duotone_selector = $selectors[ $name ]['duotone'];
			}

			$nodes[] = array(
				'path'     => array( 'styles', 'blocks', $name ),
				'selector' => $selector,
				'duotone'  => $duotone_selector,
			);

			if ( isset( $theme_json['styles']['blocks'][ $name ]['elements'] ) ) {
				foreach ( $theme_json['styles']['blocks'][ $name ]['elements'] as $element => $node ) {
					$nodes[] = array(
						'path'     => array( 'styles', 'blocks', $name, 'elements', $element ),
						'selector' => $selectors[ $name ]['elements'][ $element ],
					);
				}
			}
		}

		return $nodes;
	}

	/**
	 * Merge new incoming data.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Duotone preset also has origins.
	 *
	 * @param WP_Theme_JSON $incoming Data to merge.
	 */
	public function merge( $incoming ) {
		$incoming_data    = $incoming->get_raw_data();
		$this->theme_json = array_replace_recursive( $this->theme_json, $incoming_data );

		/*
		 * The array_replace_recursive algorithm merges at the leaf level,
		 * but we don't want leaf arrays to be merged, so we overwrite it.
		 *
		 * For leaf values that are sequential arrays it will use the numeric indexes for replacement.
		 * We rather replace the existing with the incoming value, if it exists.
		 * This is the case of spacing.units.
		 *
		 * For leaf values that are associative arrays it will merge them as expected.
		 * This is also not the behavior we want for the current associative arrays (presets).
		 * We rather replace the existing with the incoming value, if it exists.
		 * This happens, for example, when we merge data from theme.json upon existing
		 * theme supports or when we merge anything coming from the same source twice.
		 * This is the case of color.palette, color.gradients, color.duotone,
		 * typography.fontSizes, or typography.fontFamilies.
		 *
		 * Additionally, for some preset types, we also want to make sure the
		 * values they introduce don't conflict with default values. We do so
		 * by checking the incoming slugs for theme presets and compare them
		 * with the equivalent default presets: if a slug is present as a default
		 * we remove it from the theme presets.
		 */
		$nodes        = static::get_setting_nodes( $incoming_data );
		$slugs_global = static::get_default_slugs( $this->theme_json, array( 'settings' ) );
		foreach ( $nodes as $node ) {
			$slugs_node = static::get_default_slugs( $this->theme_json, $node['path'] );
			$slugs      = array_merge_recursive( $slugs_global, $slugs_node );

			// Replace the spacing.units.
			$path    = array_merge( $node['path'], array( 'spacing', 'units' ) );
			$content = _wp_array_get( $incoming_data, $path, null );
			if ( isset( $content ) ) {
				_wp_array_set( $this->theme_json, $path, $content );
			}

			// Replace the presets.
			foreach ( static::PRESETS_METADATA as $preset ) {
				$override_preset = static::should_override_preset( $this->theme_json, $node['path'], $preset['override'] );

				foreach ( static::VALID_ORIGINS as $origin ) {
					$base_path = array_merge( $node['path'], $preset['path'] );
					$path      = array_merge( $base_path, array( $origin ) );
					$content   = _wp_array_get( $incoming_data, $path, null );
					if ( ! isset( $content ) ) {
						continue;
					}

					if ( 'theme' === $origin && $preset['use_default_names'] ) {
						foreach ( $content as &$item ) {
							if ( ! array_key_exists( 'name', $item ) ) {
								$name = static::get_name_from_defaults( $item['slug'], $base_path );
								if ( null !== $name ) {
									$item['name'] = $name;
								}
							}
						}
					}

					if (
						( 'theme' !== $origin ) ||
						( 'theme' === $origin && $override_preset )
					) {
						_wp_array_set( $this->theme_json, $path, $content );
					} else {
						$slugs_for_preset = _wp_array_get( $slugs, $preset['path'], array() );
						$content          = static::filter_slugs( $content, $slugs_for_preset );
						_wp_array_set( $this->theme_json, $path, $content );
					}
				}
			}
		}
	}

	/**
	 * Converts all filter (duotone) presets into SVGs.
	 *
	 * @since 5.9.1
	 *
	 * @param array $origins List of origins to process.
	 * @return string SVG filters.
	 */
	public function get_svg_filters( $origins ) {
		$blocks_metadata = static::get_blocks_metadata();
		$setting_nodes   = static::get_setting_nodes( $this->theme_json, $blocks_metadata );

		$filters = '';
		foreach ( $setting_nodes as $metadata ) {
			$node = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			if ( empty( $node['color']['duotone'] ) ) {
				continue;
			}

			$duotone_presets = $node['color']['duotone'];

			foreach ( $origins as $origin ) {
				if ( ! isset( $duotone_presets[ $origin ] ) ) {
					continue;
				}
				foreach ( $duotone_presets[ $origin ] as $duotone_preset ) {
					$filters .= wp_get_duotone_filter_svg( $duotone_preset );
				}
			}
		}

		return $filters;
	}

	/**
	 * Returns whether a presets should be overridden or not.
	 *
	 * @since 5.9.0
	 *
	 * @param array      $theme_json The theme.json like structure to inspect.
	 * @param array      $path Path to inspect.
	 * @param bool|array $override Data to compute whether to override the preset.
	 * @return boolean
	 */
	protected static function should_override_preset( $theme_json, $path, $override ) {
		if ( is_bool( $override ) ) {
			return $override;
		}

		/*
		 * The relationship between whether to override the defaults
		 * and whether the defaults are enabled is inverse:
		 *
		 * - If defaults are enabled  => theme presets should not be overridden
		 * - If defaults are disabled => theme presets should be overridden
		 *
		 * For example, a theme sets defaultPalette to false,
		 * making the default palette hidden from the user.
		 * In that case, we want all the theme presets to be present,
		 * so they should override the defaults.
		 */
		if ( is_array( $override ) ) {
			$value = _wp_array_get( $theme_json, array_merge( $path, $override ) );
			if ( isset( $value ) ) {
				return ! $value;
			}

			// Search the top-level key if none was found for this node.
			$value = _wp_array_get( $theme_json, array_merge( array( 'settings' ), $override ) );
			if ( isset( $value ) ) {
				return ! $value;
			}

			return true;
		}
	}

	/**
	 * Returns the default slugs for all the presets in an associative array
	 * whose keys are the preset paths and the leafs is the list of slugs.
	 *
	 * For example:
	 *
	 *  array(
	 *   'color' => array(
	 *     'palette'   => array( 'slug-1', 'slug-2' ),
	 *     'gradients' => array( 'slug-3', 'slug-4' ),
	 *   ),
	 * )
	 *
	 * @since 5.9.0
	 *
	 * @param array $data      A theme.json like structure.
	 * @param array $node_path The path to inspect. It's 'settings' by default.
	 * @return array
	 */
	protected static function get_default_slugs( $data, $node_path ) {
		$slugs = array();

		foreach ( static::PRESETS_METADATA as $metadata ) {
			$path   = array_merge( $node_path, $metadata['path'], array( 'default' ) );
			$preset = _wp_array_get( $data, $path, null );
			if ( ! isset( $preset ) ) {
				continue;
			}

			$slugs_for_preset = array();
			$slugs_for_preset = array_map(
				static function( $value ) {
					return isset( $value['slug'] ) ? $value['slug'] : null;
				},
				$preset
			);
			_wp_array_set( $slugs, $metadata['path'], $slugs_for_preset );
		}

		return $slugs;
	}

	/**
	 * Get a `default`'s preset name by a provided slug.
	 *
	 * @since 5.9.0
	 *
	 * @param string $slug The slug we want to find a match from default presets.
	 * @param array  $base_path The path to inspect. It's 'settings' by default.
	 * @return string|null
	 */
	protected function get_name_from_defaults( $slug, $base_path ) {
		$path            = array_merge( $base_path, array( 'default' ) );
		$default_content = _wp_array_get( $this->theme_json, $path, null );
		if ( ! $default_content ) {
			return null;
		}
		foreach ( $default_content as $item ) {
			if ( $slug === $item['slug'] ) {
				return $item['name'];
			}
		}
		return null;
	}

	/**
	 * Removes the preset values whose slug is equal to any of given slugs.
	 *
	 * @since 5.9.0
	 *
	 * @param array $node  The node with the presets to validate.
	 * @param array $slugs The slugs that should not be overridden.
	 * @return array The new node.
	 */
	protected static function filter_slugs( $node, $slugs ) {
		if ( empty( $slugs ) ) {
			return $node;
		}

		$new_node = array();
		foreach ( $node as $value ) {
			if ( isset( $value['slug'] ) && ! in_array( $value['slug'], $slugs, true ) ) {
				$new_node[] = $value;
			}
		}

		return $new_node;
	}

	/**
	 * Removes insecure data from theme.json.
	 *
	 * @since 5.9.0
	 *
	 * @param array $theme_json Structure to sanitize.
	 * @return array Sanitized structure.
	 */
	public static function remove_insecure_properties( $theme_json ) {
		$sanitized = array();

		$theme_json = WP_Theme_JSON_Schema::migrate( $theme_json );

		$valid_block_names   = array_keys( static::get_blocks_metadata() );
		$valid_element_names = array_keys( static::ELEMENTS );
		$theme_json          = static::sanitize( $theme_json, $valid_block_names, $valid_element_names );

		$blocks_metadata = static::get_blocks_metadata();
		$style_nodes     = static::get_style_nodes( $theme_json, $blocks_metadata );
		foreach ( $style_nodes as $metadata ) {
			$input = _wp_array_get( $theme_json, $metadata['path'], array() );
			if ( empty( $input ) ) {
				continue;
			}

			$output = static::remove_insecure_styles( $input );
			if ( ! empty( $output ) ) {
				_wp_array_set( $sanitized, $metadata['path'], $output );
			}
		}

		$setting_nodes = static::get_setting_nodes( $theme_json );
		foreach ( $setting_nodes as $metadata ) {
			$input = _wp_array_get( $theme_json, $metadata['path'], array() );
			if ( empty( $input ) ) {
				continue;
			}

			$output = static::remove_insecure_settings( $input );
			if ( ! empty( $output ) ) {
				_wp_array_set( $sanitized, $metadata['path'], $output );
			}
		}

		if ( empty( $sanitized['styles'] ) ) {
			unset( $theme_json['styles'] );
		} else {
			$theme_json['styles'] = $sanitized['styles'];
		}

		if ( empty( $sanitized['settings'] ) ) {
			unset( $theme_json['settings'] );
		} else {
			$theme_json['settings'] = $sanitized['settings'];
		}

		return $theme_json;
	}

	/**
	 * Processes a setting node and returns the same node
	 * without the insecure settings.
	 *
	 * @since 5.9.0
	 *
	 * @param array $input Node to process.
	 * @return array
	 */
	protected static function remove_insecure_settings( $input ) {
		$output = array();
		foreach ( static::PRESETS_METADATA as $preset_metadata ) {
			foreach ( static::VALID_ORIGINS as $origin ) {
				$path_with_origin = array_merge( $preset_metadata['path'], array( $origin ) );
				$presets          = _wp_array_get( $input, $path_with_origin, null );
				if ( null === $presets ) {
					continue;
				}

				$escaped_preset = array();
				foreach ( $presets as $preset ) {
					if (
						esc_attr( esc_html( $preset['name'] ) ) === $preset['name'] &&
						sanitize_html_class( $preset['slug'] ) === $preset['slug']
					) {
						$value = null;
						if ( isset( $preset_metadata['value_key'], $preset[ $preset_metadata['value_key'] ] ) ) {
							$value = $preset[ $preset_metadata['value_key'] ];
						} elseif (
							isset( $preset_metadata['value_func'] ) &&
							is_callable( $preset_metadata['value_func'] )
						) {
							$value = call_user_func( $preset_metadata['value_func'], $preset );
						}

						$preset_is_valid = true;
						foreach ( $preset_metadata['properties'] as $property ) {
							if ( ! static::is_safe_css_declaration( $property, $value ) ) {
								$preset_is_valid = false;
								break;
							}
						}

						if ( $preset_is_valid ) {
							$escaped_preset[] = $preset;
						}
					}
				}

				if ( ! empty( $escaped_preset ) ) {
					_wp_array_set( $output, $path_with_origin, $escaped_preset );
				}
			}
		}
		return $output;
	}

	/**
	 * Processes a style node and returns the same node
	 * without the insecure styles.
	 *
	 * @since 5.9.0
	 *
	 * @param array $input Node to process.
	 * @return array
	 */
	protected static function remove_insecure_styles( $input ) {
		$output       = array();
		$declarations = static::compute_style_properties( $input );

		foreach ( $declarations as $declaration ) {
			if ( static::is_safe_css_declaration( $declaration['name'], $declaration['value'] ) ) {
				$path = static::PROPERTIES_METADATA[ $declaration['name'] ];

				// Check the value isn't an array before adding so as to not
				// double up shorthand and longhand styles.
				$value = _wp_array_get( $input, $path, array() );
				if ( ! is_array( $value ) ) {
					_wp_array_set( $output, $path, $value );
				}
			}
		}
		return $output;
	}

	/**
	 * Checks that a declaration provided by the user is safe.
	 *
	 * @since 5.9.0
	 *
	 * @param string $property_name  Property name in a CSS declaration, i.e. the `color` in `color: red`.
	 * @param string $property_value Value in a CSS declaration, i.e. the `red` in `color: red`.
	 * @return bool
	 */
	protected static function is_safe_css_declaration( $property_name, $property_value ) {
		$style_to_validate = $property_name . ': ' . $property_value;
		$filtered          = esc_html( safecss_filter_attr( $style_to_validate ) );
		return ! empty( trim( $filtered ) );
	}

	/**
	 * Returns the raw data.
	 *
	 * @since 5.8.0
	 *
	 * @return array Raw data.
	 */
	public function get_raw_data() {
		return $this->theme_json;
	}

	/**
	 * Transforms the given editor settings according the
	 * add_theme_support format to the theme.json format.
	 *
	 * @since 5.8.0
	 *
	 * @param array $settings Existing editor settings.
	 * @return array Config that adheres to the theme.json schema.
	 */
	public static function get_from_editor_settings( $settings ) {
		$theme_settings = array(
			'version'  => static::LATEST_SCHEMA,
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
			$theme_settings['settings']['typography']['lineHeight'] = $settings['enableCustomLineHeight'];
		}

		if ( isset( $settings['enableCustomUnits'] ) ) {
			if ( ! isset( $theme_settings['settings']['spacing'] ) ) {
				$theme_settings['settings']['spacing'] = array();
			}
			$theme_settings['settings']['spacing']['units'] = ( true === $settings['enableCustomUnits'] ) ?
				array( 'px', 'em', 'rem', 'vh', 'vw', '%' ) :
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

		if ( isset( $settings['enableCustomSpacing'] ) ) {
			if ( ! isset( $theme_settings['settings']['spacing'] ) ) {
				$theme_settings['settings']['spacing'] = array();
			}
			$theme_settings['settings']['spacing']['padding'] = $settings['enableCustomSpacing'];
		}

		return $theme_settings;
	}

}
