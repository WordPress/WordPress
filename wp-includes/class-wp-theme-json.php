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
 * @access private
 */
class WP_Theme_JSON {

	/**
	 * Container of data in theme.json format.
	 *
	 * @since 5.8.0
	 * @var array
	 */
	private $theme_json = null;

	/**
	 * Holds block metadata extracted from block.json
	 * to be shared among all instances so we don't
	 * process it twice.
	 *
	 * @since 5.8.0
	 * @var array
	 */
	private static $blocks_metadata = null;

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
	 * @var array
	 */
	const VALID_ORIGINS = array(
		'core',
		'theme',
		'user',
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
	 * - path          => where to find the preset within the settings section
	 *
	 * - value_key     => the key that represents the value
	 *
	 * - css_var_infix => infix to use in generating the CSS Custom Property. Example:
	 *                   --wp--preset--<preset_infix>--<slug>: <preset_value>
	 *
	 * - classes      => array containing a structure with the classes to
	 *                   generate for the presets. Each class should have
	 *                   the class suffix and the property name. Example:
	 *
	 *                   .has-<slug>-<class_suffix> {
	 *                       <property_name>: <preset_value>
	 *                   }
	 *
	 * @since 5.8.0
	 * @var array
	 */
	const PRESETS_METADATA = array(
		array(
			'path'          => array( 'color', 'palette' ),
			'value_key'     => 'color',
			'css_var_infix' => 'color',
			'classes'       => array(
				array(
					'class_suffix'  => 'color',
					'property_name' => 'color',
				),
				array(
					'class_suffix'  => 'background-color',
					'property_name' => 'background-color',
				),
			),
		),
		array(
			'path'          => array( 'color', 'gradients' ),
			'value_key'     => 'gradient',
			'css_var_infix' => 'gradient',
			'classes'       => array(
				array(
					'class_suffix'  => 'gradient-background',
					'property_name' => 'background',
				),
			),
		),
		array(
			'path'          => array( 'typography', 'fontSizes' ),
			'value_key'     => 'size',
			'css_var_infix' => 'font-size',
			'classes'       => array(
				array(
					'class_suffix'  => 'font-size',
					'property_name' => 'font-size',
				),
			),
		),
	);

	/**
	 * Metadata for style properties.
	 *
	 * Each property declares:
	 *
	 * - 'value': path to the value in theme.json and block attributes.
	 *
	 * @since 5.8.0
	 * @var array
	 */
	const PROPERTIES_METADATA = array(
		'background'       => array(
			'value' => array( 'color', 'gradient' ),
		),
		'background-color' => array(
			'value' => array( 'color', 'background' ),
		),
		'color'            => array(
			'value' => array( 'color', 'text' ),
		),
		'font-size'        => array(
			'value' => array( 'typography', 'fontSize' ),
		),
		'line-height'      => array(
			'value' => array( 'typography', 'lineHeight' ),
		),
		'margin'           => array(
			'value'      => array( 'spacing', 'margin' ),
			'properties' => array( 'top', 'right', 'bottom', 'left' ),
		),
		'padding'          => array(
			'value'      => array( 'spacing', 'padding' ),
			'properties' => array( 'top', 'right', 'bottom', 'left' ),
		),
	);

	/**
	 * @since 5.8.0
	 * @var array
	 */
	const ALLOWED_TOP_LEVEL_KEYS = array(
		'settings',
		'styles',
		'version',
	);

	/**
	 * @since 5.8.0
	 * @var array
	 */
	const ALLOWED_SETTINGS = array(
		'border'     => array(
			'customRadius' => null,
		),
		'color'      => array(
			'custom'         => null,
			'customDuotone'  => null,
			'customGradient' => null,
			'duotone'        => null,
			'gradients'      => null,
			'link'           => null,
			'palette'        => null,
		),
		'custom'     => null,
		'layout'     => array(
			'contentSize' => null,
			'wideSize'    => null,
		),
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

	/**
	 * @since 5.8.0
	 * @var array
	 */
	const ALLOWED_STYLES = array(
		'border'     => array(
			'radius' => null,
		),
		'color'      => array(
			'background' => null,
			'gradient'   => null,
			'text'       => null,
		),
		'spacing'    => array(
			'margin'  => array(
				'top'    => null,
				'right'  => null,
				'bottom' => null,
				'left'   => null,
			),
			'padding' => array(
				'bottom' => null,
				'left'   => null,
				'right'  => null,
				'top'    => null,
			),
		),
		'typography' => array(
			'fontSize'   => null,
			'lineHeight' => null,
		),
	);

	/**
	 * @since 5.8.0
	 * @var array
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
	 * @since 5.8.0
	 * @var int
	 */
	const LATEST_SCHEMA = 1;

	/**
	 * Constructor.
	 *
	 * @since 5.8.0
	 *
	 * @param array $theme_json A structure that follows the theme.json schema.
	 * @param string $origin    Optional. What source of data this object represents.
	 *                          One of 'core', 'theme', or 'user'. Default 'theme'.
	 */
	public function __construct( $theme_json = array(), $origin = 'theme' ) {
		if ( ! in_array( $origin, self::VALID_ORIGINS, true ) ) {
			$origin = 'theme';
		}

		if ( ! isset( $theme_json['version'] ) || self::LATEST_SCHEMA !== $theme_json['version'] ) {
			$this->theme_json = array();
			return;
		}

		$this->theme_json = self::sanitize( $theme_json );

		// Internally, presets are keyed by origin.
		$nodes = self::get_setting_nodes( $this->theme_json );
		foreach ( $nodes as $node ) {
			foreach ( self::PRESETS_METADATA as $preset ) {
				$path   = array_merge( $node['path'], $preset['path'] );
				$preset = _wp_array_get( $this->theme_json, $path, null );
				if ( null !== $preset ) {
					_wp_array_set( $this->theme_json, $path, array( $origin => $preset ) );
				}
			}
		}
	}

	/**
	 * Sanitizes the input according to the schemas.
	 *
	 * @since 5.8.0
	 *
	 * @param array $input Structure to sanitize.
	 * @return array The sanitized output.
	 */
	private static function sanitize( $input ) {
		$output = array();

		if ( ! is_array( $input ) ) {
			return $output;
		}

		$allowed_top_level_keys = self::ALLOWED_TOP_LEVEL_KEYS;
		$allowed_settings       = self::ALLOWED_SETTINGS;
		$allowed_styles         = self::ALLOWED_STYLES;
		$allowed_blocks         = array_keys( self::get_blocks_metadata() );
		$allowed_elements       = array_keys( self::ELEMENTS );

		$output = array_intersect_key( $input, array_flip( $allowed_top_level_keys ) );

		// Build the schema.
		$schema                 = array();
		$schema_styles_elements = array();
		foreach ( $allowed_elements as $element ) {
			$schema_styles_elements[ $element ] = $allowed_styles;
		}
		$schema_styles_blocks   = array();
		$schema_settings_blocks = array();
		foreach ( $allowed_blocks as $block ) {
			$schema_settings_blocks[ $block ]           = $allowed_settings;
			$schema_styles_blocks[ $block ]             = $allowed_styles;
			$schema_styles_blocks[ $block ]['elements'] = $schema_styles_elements;
		}
		$schema['styles']             = $allowed_styles;
		$schema['styles']['blocks']   = $schema_styles_blocks;
		$schema['styles']['elements'] = $schema_styles_elements;
		$schema['settings']           = $allowed_settings;
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
	 *       }
	 *       'core/group': {
	 *         'selector': '.wp-block-group',
	 *         'elements': {}
	 *       }
	 *     }
	 *
	 * @since 5.8.0
	 *
	 * @return array Block metadata.
	 */
	private static function get_blocks_metadata() {
		if ( null !== self::$blocks_metadata ) {
			return self::$blocks_metadata;
		}

		self::$blocks_metadata = array();

		$registry = WP_Block_Type_Registry::get_instance();
		$blocks   = $registry->get_all_registered();
		foreach ( $blocks as $block_name => $block_type ) {
			if (
				isset( $block_type->supports['__experimentalSelector'] ) &&
				is_string( $block_type->supports['__experimentalSelector'] )
			) {
				self::$blocks_metadata[ $block_name ]['selector'] = $block_type->supports['__experimentalSelector'];
			} else {
				self::$blocks_metadata[ $block_name ]['selector'] = '.wp-block-' . str_replace( '/', '-', str_replace( 'core/', '', $block_name ) );
			}

			/*
			 * Assign defaults, then overwrite those that the block sets by itself.
			 * If the block selector is compounded, will append the element to each
			 * individual block selector.
			 */
			$block_selectors = explode( ',', self::$blocks_metadata[ $block_name ]['selector'] );
			foreach ( self::ELEMENTS as $el_name => $el_selector ) {
				$element_selector = array();
				foreach ( $block_selectors as $selector ) {
					$element_selector[] = $selector . ' ' . $el_selector;
				}
				self::$blocks_metadata[ $block_name ]['elements'][ $el_name ] = implode( ',', $element_selector );
			}
		}

		return self::$blocks_metadata;
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
	 *
	 * @param string $type Optional. Type of stylesheet we want. Accepts 'all',
	 *                     'block_styles', and 'css_variables'. Default 'all'.
	 * @return string Stylesheet.
	 */
	public function get_stylesheet( $type = 'all' ) {
		$blocks_metadata = self::get_blocks_metadata();
		$style_nodes     = self::get_style_nodes( $this->theme_json, $blocks_metadata );
		$setting_nodes   = self::get_setting_nodes( $this->theme_json, $blocks_metadata );

		switch ( $type ) {
			case 'block_styles':
				return $this->get_block_styles( $style_nodes, $setting_nodes );
			case 'css_variables':
				return $this->get_css_variables( $setting_nodes );
			default:
				return $this->get_css_variables( $setting_nodes ) . $this->get_block_styles( $style_nodes, $setting_nodes );
		}

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
	 * Additionally, it'll also create new rulesets
	 * as classes for each preset value such as:
	 *
	 *     .has-value-color {
	 *       color: value;
	 *     }
	 *
	 *     .has-value-background-color {
	 *       background-color: value;
	 *     }
	 *
	 *     .has-value-font-size {
	 *       font-size: value;
	 *     }
	 *
	 *     .has-value-gradient-background {
	 *       background: value;
	 *     }
	 *
	 *     p.has-value-gradient-background {
	 *       background: value;
	 *     }
	 *
	 * @since 5.8.0
	 *
	 * @param array $style_nodes   Nodes with styles.
	 * @param array $setting_nodes Nodes with settings.
	 * @return string The new stylesheet.
	 */
	private function get_block_styles( $style_nodes, $setting_nodes ) {
		$block_rules = '';
		foreach ( $style_nodes as $metadata ) {
			if ( null === $metadata['selector'] ) {
				continue;
			}

			$node         = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			$selector     = $metadata['selector'];
			$declarations = self::compute_style_properties( $node );
			$block_rules .= self::to_ruleset( $selector, $declarations );
		}

		$preset_rules = '';
		foreach ( $setting_nodes as $metadata ) {
			if ( null === $metadata['selector'] ) {
				continue;
			}

			$selector      = $metadata['selector'];
			$node          = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			$preset_rules .= self::compute_preset_classes( $node, $selector );
		}

		return $block_rules . $preset_rules;
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
	 *
	 * @param array $nodes Nodes with settings.
	 * @return string The new stylesheet.
	 */
	private function get_css_variables( $nodes ) {
		$stylesheet = '';
		foreach ( $nodes as $metadata ) {
			if ( null === $metadata['selector'] ) {
				continue;
			}

			$selector = $metadata['selector'];

			$node         = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			$declarations = array_merge( self::compute_preset_vars( $node ), self::compute_theme_vars( $node ) );

			$stylesheet .= self::to_ruleset( $selector, $declarations );
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
	private static function to_ruleset( $selector, $declarations ) {
		if ( empty( $declarations ) ) {
			return '';
		}

		$declaration_block = array_reduce(
			$declarations,
			function ( $carry, $element ) {
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
	private static function append_to_selector( $selector, $to_append ) {
		$new_selectors = array();
		$selectors     = explode( ',', $selector );
		foreach ( $selectors as $sel ) {
			$new_selectors[] = $sel . $to_append;
		}

		return implode( ',', $new_selectors );
	}

	/**
	 * Given an array of presets keyed by origin and the value key of the preset,
	 * it returns an array where each key is the preset slug and each value the preset value.
	 *
	 * @since 5.8.0
	 *
	 * @param array  $preset_per_origin Array of presets keyed by origin.
	 * @param string $value_key         The property of the preset that contains its value.
	 * @return array Array of presets where each key is a slug and each value is the preset value.
	 */
	private static function get_merged_preset_by_slug( $preset_per_origin, $value_key ) {
		$result = array();
		foreach ( self::VALID_ORIGINS as $origin ) {
			if ( ! isset( $preset_per_origin[ $origin ] ) ) {
				continue;
			}
			foreach ( $preset_per_origin[ $origin ] as $preset ) {
				/*
				 * We don't want to use kebabCase here,
				 * see https://github.com/WordPress/gutenberg/issues/32347
				 * However, we need to make sure the generated class or CSS variable
				 * doesn't contain spaces.
				 */
				$result[ preg_replace( '/\s+/', '-', $preset['slug'] ) ] = $preset[ $value_key ];
			}
		}
		return $result;
	}

	/**
	 * Given a settings array, it returns the generated rulesets
	 * for the preset classes.
	 *
	 * @since 5.8.0
	 *
	 * @param array  $settings Settings to process.
	 * @param string $selector Selector wrapping the classes.
	 * @return string The result of processing the presets.
	 */
	private static function compute_preset_classes( $settings, $selector ) {
		if ( self::ROOT_BLOCK_SELECTOR === $selector ) {
			// Classes at the global level do not need any CSS prefixed,
			// and we don't want to increase its specificity.
			$selector = '';
		}

		$stylesheet = '';
		foreach ( self::PRESETS_METADATA as $preset ) {
			$preset_per_origin = _wp_array_get( $settings, $preset['path'], array() );
			$preset_by_slug    = self::get_merged_preset_by_slug( $preset_per_origin, $preset['value_key'] );
			foreach ( $preset['classes'] as $class ) {
				foreach ( $preset_by_slug as $slug => $value ) {
					$stylesheet .= self::to_ruleset(
						self::append_to_selector( $selector, '.has-' . _wp_to_kebab_case( $slug ) . '-' . $class['class_suffix'] ),
						array(
							array(
								'name'  => $class['property_name'],
								'value' => 'var(--wp--preset--' . $preset['css_var_infix'] . '--' . _wp_to_kebab_case( $slug ) . ') !important',
							),
						)
					);
				}
			}
		}

		return $stylesheet;
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
	 *
	 * @param array $settings Settings to process.
	 * @return array Returns the modified $declarations.
	 */
	private static function compute_preset_vars( $settings ) {
		$declarations = array();
		foreach ( self::PRESETS_METADATA as $preset ) {
			$preset_per_origin = _wp_array_get( $settings, $preset['path'], array() );
			$preset_by_slug    = self::get_merged_preset_by_slug( $preset_per_origin, $preset['value_key'] );
			foreach ( $preset_by_slug as $slug => $value ) {
				$declarations[] = array(
					'name'  => '--wp--preset--' . $preset['css_var_infix'] . '--' . _wp_to_kebab_case( $slug ),
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
	private static function compute_theme_vars( $settings ) {
		$declarations  = array();
		$custom_values = _wp_array_get( $settings, array( 'custom' ), array() );
		$css_vars      = self::flatten_tree( $custom_values );
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
	private static function flatten_tree( $tree, $prefix = '', $token = '--' ) {
		$result = array();
		foreach ( $tree as $property => $value ) {
			$new_key = $prefix . str_replace(
				'/',
				'-',
				strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $property ) ) // CamelCase to kebab-case.
			);

			if ( is_array( $value ) ) {
				$new_prefix = $new_key . $token;
				$result     = array_merge(
					$result,
					self::flatten_tree( $value, $new_prefix, $token )
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
	 *
	 * @param array $styles Styles to process.
	 * @return array Returns the modified $declarations.
	 */
	private static function compute_style_properties( $styles ) {
		$declarations = array();
		if ( empty( $styles ) ) {
			return $declarations;
		}

		$properties = array();
		foreach ( self::PROPERTIES_METADATA as $name => $metadata ) {
			/*
			 * Some properties can be shorthand properties, meaning that
			 * they contain multiple values instead of a single one.
			 * An example of this is the padding property.
			 */
			if ( self::has_properties( $metadata ) ) {
				foreach ( $metadata['properties'] as $property ) {
					$properties[] = array(
						'name'  => $name . '-' . $property,
						'value' => array_merge( $metadata['value'], array( $property ) ),
					);
				}
			} else {
				$properties[] = array(
					'name'  => $name,
					'value' => $metadata['value'],
				);
			}
		}

		foreach ( $properties as $prop ) {
			$value = self::get_property_value( $styles, $prop['value'] );
			if ( empty( $value ) ) {
				continue;
			}

			$declarations[] = array(
				'name'  => $prop['name'],
				'value' => $value,
			);
		}

		return $declarations;
	}

	/**
	 * Whether the metadata contains a key named properties.
	 *
	 * @since 5.8.0
	 *
	 * @param array $metadata Description of the style property.
	 * @return bool True if properties exists, false otherwise.
	 */
	private static function has_properties( $metadata ) {
		if ( array_key_exists( 'properties', $metadata ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the style property for the given path.
	 *
	 * It also converts CSS Custom Property stored as
	 * "var:preset|color|secondary" to the form
	 * "--wp--preset--color--secondary".
	 *
	 * @since 5.8.0
	 *
	 * @param array $styles Styles subtree.
	 * @param array $path   Which property to process.
	 * @return string Style property value.
	 */
	private static function get_property_value( $styles, $path ) {
		$value = _wp_array_get( $styles, $path, '' );

		if ( '' === $value ) {
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
	private static function get_setting_nodes( $theme_json, $selectors = array() ) {
		$nodes = array();
		if ( ! isset( $theme_json['settings'] ) ) {
			return $nodes;
		}

		// Top-level.
		$nodes[] = array(
			'path'     => array( 'settings' ),
			'selector' => self::ROOT_BLOCK_SELECTOR,
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
	 *         'selector' => 'CSS selector for some node'
	 *       ],
	 *       [
	 *         'path'     => ['path', 'to', 'other', 'node' ],
	 *         'selector' => 'CSS selector for other node'
	 *       ],
	 *     ]
	 *
	 * @since 5.8.0
	 *
	 * @param array $theme_json The tree to extract style nodes from.
	 * @param array $selectors  List of selectors per block.
	 * @return array
	 */
	private static function get_style_nodes( $theme_json, $selectors = array() ) {
		$nodes = array();
		if ( ! isset( $theme_json['styles'] ) ) {
			return $nodes;
		}

		// Top-level.
		$nodes[] = array(
			'path'     => array( 'styles' ),
			'selector' => self::ROOT_BLOCK_SELECTOR,
		);

		if ( isset( $theme_json['styles']['elements'] ) ) {
			foreach ( $theme_json['styles']['elements'] as $element => $node ) {
				$nodes[] = array(
					'path'     => array( 'styles', 'elements', $element ),
					'selector' => self::ELEMENTS[ $element ],
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

			$nodes[] = array(
				'path'     => array( 'styles', 'blocks', $name ),
				'selector' => $selector,
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
	 *
	 * @param WP_Theme_JSON $incoming Data to merge.
	 */
	public function merge( $incoming ) {
		$incoming_data    = $incoming->get_raw_data();
		$this->theme_json = array_replace_recursive( $this->theme_json, $incoming_data );

		/*
		 * The array_replace_recursive() algorithm merges at the leaf level.
		 * For leaf values that are arrays it will use the numeric indexes for replacement.
		 * In those cases, we want to replace the existing with the incoming value, if it exists.
		 */
		$to_replace   = array();
		$to_replace[] = array( 'spacing', 'units' );
		$to_replace[] = array( 'color', 'duotone' );
		foreach ( self::VALID_ORIGINS as $origin ) {
			$to_replace[] = array( 'color', 'palette', $origin );
			$to_replace[] = array( 'color', 'gradients', $origin );
			$to_replace[] = array( 'typography', 'fontSizes', $origin );
			$to_replace[] = array( 'typography', 'fontFamilies', $origin );
		}

		$nodes = self::get_setting_nodes( $this->theme_json );
		foreach ( $nodes as $metadata ) {
			foreach ( $to_replace as $property_path ) {
				$path = array_merge( $metadata['path'], $property_path );
				$node = _wp_array_get( $incoming_data, $path, null );
				if ( isset( $node ) ) {
					_wp_array_set( $this->theme_json, $path, $node );
				}
			}
		}
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
