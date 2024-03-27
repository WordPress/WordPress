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
#[AllowDynamicProperties]
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
	 * @since 6.1.0 Initialize as an empty array.
	 * @var array
	 */
	protected static $blocks_metadata = array();

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
	 * @since 6.1.0 Added 'blocks'.
	 * @var string[]
	 */
	const VALID_ORIGINS = array(
		'default',
		'blocks',
		'theme',
		'custom',
	);

	/**
	 * Presets are a set of values that serve
	 * to bootstrap some styles: colors, font sizes, etc.
	 *
	 * They are a unkeyed array of values such as:
	 *
	 *     array(
	 *       array(
	 *         'slug'      => 'unique-name-within-the-set',
	 *         'name'      => 'Name for the UI',
	 *         <value_key> => 'value'
	 *       ),
	 *     )
	 *
	 * This contains the necessary metadata to process them:
	 *
	 * - path             => Where to find the preset within the settings section.
	 * - prevent_override => Disables override of default presets by theme presets.
	 *                       The relationship between whether to override the defaults
	 *                       and whether the defaults are enabled is inverse:
	 *                         - If defaults are enabled  => theme presets should not be overridden
	 *                         - If defaults are disabled => theme presets should be overridden
	 *                       For example, a theme sets defaultPalette to false,
	 *                       making the default palette hidden from the user.
	 *                       In that case, we want all the theme presets to be present,
	 *                       so they should override the defaults by setting this false.
	 * - use_default_names => whether to use the default names
	 * - value_key        => the key that represents the value
	 * - value_func       => optionally, instead of value_key, a function to generate
	 *                       the value that takes a preset as an argument
	 *                       (either value_key or value_func should be present)
	 * - css_vars         => template string to use in generating the CSS Custom Property.
	 *                       Example output: "--wp--preset--duotone--blue: <value>" will generate as many CSS Custom Properties as presets defined
	 *                       substituting the $slug for the slug's value for each preset value.
	 * - classes          => array containing a structure with the classes to
	 *                       generate for the presets, where for each array item
	 *                       the key is the class name and the value the property name.
	 *                       The "$slug" substring will be replaced by the slug of each preset.
	 *                       For example:
	 *                       'classes' => array(
	 *                         '.has-$slug-color'            => 'color',
	 *                         '.has-$slug-background-color' => 'background-color',
	 *                         '.has-$slug-border-color'     => 'border-color',
	 *                       )
	 * - properties       => array of CSS properties to be used by kses to
	 *                       validate the content of each preset
	 *                       by means of the remove_insecure_properties method.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `color.duotone` and `typography.fontFamilies` presets,
	 *              `use_default_names` preset key, and simplified the metadata structure.
	 * @since 6.0.0 Replaced `override` with `prevent_override` and updated the
	 *              `prevent_override` value for `color.duotone` to use `color.defaultDuotone`.
	 * @since 6.2.0 Added 'shadow' presets.
	 * @since 6.3.0 Replaced value_func for duotone with `null`. Custom properties are handled by class-wp-duotone.php.
	 * @var array
	 */
	const PRESETS_METADATA = array(
		array(
			'path'              => array( 'color', 'palette' ),
			'prevent_override'  => array( 'color', 'defaultPalette' ),
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
			'prevent_override'  => array( 'color', 'defaultGradients' ),
			'use_default_names' => false,
			'value_key'         => 'gradient',
			'css_vars'          => '--wp--preset--gradient--$slug',
			'classes'           => array( '.has-$slug-gradient-background' => 'background' ),
			'properties'        => array( 'background' ),
		),
		array(
			'path'              => array( 'color', 'duotone' ),
			'prevent_override'  => array( 'color', 'defaultDuotone' ),
			'use_default_names' => false,
			'value_func'        => null, // CSS Custom Properties for duotone are handled by block supports in class-wp-duotone.php.
			'css_vars'          => null,
			'classes'           => array(),
			'properties'        => array( 'filter' ),
		),
		array(
			'path'              => array( 'typography', 'fontSizes' ),
			'prevent_override'  => false,
			'use_default_names' => true,
			'value_func'        => 'wp_get_typography_font_size_value',
			'css_vars'          => '--wp--preset--font-size--$slug',
			'classes'           => array( '.has-$slug-font-size' => 'font-size' ),
			'properties'        => array( 'font-size' ),
		),
		array(
			'path'              => array( 'typography', 'fontFamilies' ),
			'prevent_override'  => false,
			'use_default_names' => false,
			'value_key'         => 'fontFamily',
			'css_vars'          => '--wp--preset--font-family--$slug',
			'classes'           => array( '.has-$slug-font-family' => 'font-family' ),
			'properties'        => array( 'font-family' ),
		),
		array(
			'path'              => array( 'spacing', 'spacingSizes' ),
			'prevent_override'  => false,
			'use_default_names' => true,
			'value_key'         => 'size',
			'css_vars'          => '--wp--preset--spacing--$slug',
			'classes'           => array(),
			'properties'        => array( 'padding', 'margin' ),
		),
		array(
			'path'              => array( 'shadow', 'presets' ),
			'prevent_override'  => array( 'shadow', 'defaultPresets' ),
			'use_default_names' => false,
			'value_key'         => 'shadow',
			'css_vars'          => '--wp--preset--shadow--$slug',
			'classes'           => array(),
			'properties'        => array( 'box-shadow' ),
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
	 * @since 6.1.0 Added the `border-*-color`, `border-*-width`, `border-*-style`,
	 *              `--wp--style--root--padding-*`, and `box-shadow` properties,
	 *              removed the `--wp--style--block-gap` property.
	 * @since 6.2.0 Added `outline-*`, and `min-height` properties.
	 * @since 6.3.0 Added `column-count` property.
	 * @since 6.4.0 Added `writing-mode` property.
	 * @since 6.5.0 Added `aspect-ratio` property.
	 *
	 * @var array
	 */
	const PROPERTIES_METADATA = array(
		'aspect-ratio'                      => array( 'dimensions', 'aspectRatio' ),
		'background'                        => array( 'color', 'gradient' ),
		'background-color'                  => array( 'color', 'background' ),
		'border-radius'                     => array( 'border', 'radius' ),
		'border-top-left-radius'            => array( 'border', 'radius', 'topLeft' ),
		'border-top-right-radius'           => array( 'border', 'radius', 'topRight' ),
		'border-bottom-left-radius'         => array( 'border', 'radius', 'bottomLeft' ),
		'border-bottom-right-radius'        => array( 'border', 'radius', 'bottomRight' ),
		'border-color'                      => array( 'border', 'color' ),
		'border-width'                      => array( 'border', 'width' ),
		'border-style'                      => array( 'border', 'style' ),
		'border-top-color'                  => array( 'border', 'top', 'color' ),
		'border-top-width'                  => array( 'border', 'top', 'width' ),
		'border-top-style'                  => array( 'border', 'top', 'style' ),
		'border-right-color'                => array( 'border', 'right', 'color' ),
		'border-right-width'                => array( 'border', 'right', 'width' ),
		'border-right-style'                => array( 'border', 'right', 'style' ),
		'border-bottom-color'               => array( 'border', 'bottom', 'color' ),
		'border-bottom-width'               => array( 'border', 'bottom', 'width' ),
		'border-bottom-style'               => array( 'border', 'bottom', 'style' ),
		'border-left-color'                 => array( 'border', 'left', 'color' ),
		'border-left-width'                 => array( 'border', 'left', 'width' ),
		'border-left-style'                 => array( 'border', 'left', 'style' ),
		'color'                             => array( 'color', 'text' ),
		'column-count'                      => array( 'typography', 'textColumns' ),
		'font-family'                       => array( 'typography', 'fontFamily' ),
		'font-size'                         => array( 'typography', 'fontSize' ),
		'font-style'                        => array( 'typography', 'fontStyle' ),
		'font-weight'                       => array( 'typography', 'fontWeight' ),
		'letter-spacing'                    => array( 'typography', 'letterSpacing' ),
		'line-height'                       => array( 'typography', 'lineHeight' ),
		'margin'                            => array( 'spacing', 'margin' ),
		'margin-top'                        => array( 'spacing', 'margin', 'top' ),
		'margin-right'                      => array( 'spacing', 'margin', 'right' ),
		'margin-bottom'                     => array( 'spacing', 'margin', 'bottom' ),
		'margin-left'                       => array( 'spacing', 'margin', 'left' ),
		'min-height'                        => array( 'dimensions', 'minHeight' ),
		'outline-color'                     => array( 'outline', 'color' ),
		'outline-offset'                    => array( 'outline', 'offset' ),
		'outline-style'                     => array( 'outline', 'style' ),
		'outline-width'                     => array( 'outline', 'width' ),
		'padding'                           => array( 'spacing', 'padding' ),
		'padding-top'                       => array( 'spacing', 'padding', 'top' ),
		'padding-right'                     => array( 'spacing', 'padding', 'right' ),
		'padding-bottom'                    => array( 'spacing', 'padding', 'bottom' ),
		'padding-left'                      => array( 'spacing', 'padding', 'left' ),
		'--wp--style--root--padding'        => array( 'spacing', 'padding' ),
		'--wp--style--root--padding-top'    => array( 'spacing', 'padding', 'top' ),
		'--wp--style--root--padding-right'  => array( 'spacing', 'padding', 'right' ),
		'--wp--style--root--padding-bottom' => array( 'spacing', 'padding', 'bottom' ),
		'--wp--style--root--padding-left'   => array( 'spacing', 'padding', 'left' ),
		'text-decoration'                   => array( 'typography', 'textDecoration' ),
		'text-transform'                    => array( 'typography', 'textTransform' ),
		'filter'                            => array( 'filter', 'duotone' ),
		'box-shadow'                        => array( 'shadow' ),
		'writing-mode'                      => array( 'typography', 'writingMode' ),
	);

	/**
	 * Indirect metadata for style properties that are not directly output.
	 *
	 * Each element maps from a CSS property name to an array of
	 * paths to the value in theme.json & block attributes.
	 *
	 * Indirect properties are not output directly by `compute_style_properties`,
	 * but are used elsewhere in the processing of global styles. The indirect
	 * property is used to validate whether or not a style value is allowed.
	 *
	 * @since 6.2.0
	 *
	 * @var array
	 */
	const INDIRECT_PROPERTIES_METADATA = array(
		'gap'        => array(
			array( 'spacing', 'blockGap' ),
		),
		'column-gap' => array(
			array( 'spacing', 'blockGap', 'left' ),
		),
		'row-gap'    => array(
			array( 'spacing', 'blockGap', 'top' ),
		),
		'max-width'  => array(
			array( 'layout', 'contentSize' ),
			array( 'layout', 'wideSize' ),
		),
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
	 * @since 6.3.0 Added the `description` value.
	 * @var string[]
	 */
	const VALID_TOP_LEVEL_KEYS = array(
		'customTemplates',
		'description',
		'patterns',
		'settings',
		'styles',
		'templateParts',
		'title',
		'version',
	);

	/**
	 * The valid properties under the settings key.
	 *
	 * @since 5.8.0 As `ALLOWED_SETTINGS`.
	 * @since 5.9.0 Renamed from `ALLOWED_SETTINGS` to `VALID_SETTINGS`,
	 *              added new properties for `border`, `color`, `spacing`,
	 *              and `typography`, and renamed others according to the new schema.
	 * @since 6.0.0 Added `color.defaultDuotone`.
	 * @since 6.1.0 Added `layout.definitions` and `useRootPaddingAwareAlignments`.
	 * @since 6.2.0 Added `dimensions.minHeight`, 'shadow.presets', 'shadow.defaultPresets',
	 *              `position.fixed` and `position.sticky`.
	 * @since 6.3.0 Added support for `typography.textColumns`, removed `layout.definitions`.
	 * @since 6.4.0 Added support for `layout.allowEditing`, `background.backgroundImage`,
	 *              `typography.writingMode`, `lightbox.enabled` and `lightbox.allowEditing`.
	 * @since 6.5.0 Added support for `layout.allowCustomContentAndWideSize`,
	 *              `background.backgroundSize` and `dimensions.aspectRatio`.
	 * @var array
	 */
	const VALID_SETTINGS = array(
		'appearanceTools'               => null,
		'useRootPaddingAwareAlignments' => null,
		'background'                    => array(
			'backgroundImage' => null,
			'backgroundSize'  => null,
		),
		'border'                        => array(
			'color'  => null,
			'radius' => null,
			'style'  => null,
			'width'  => null,
		),
		'color'                         => array(
			'background'       => null,
			'custom'           => null,
			'customDuotone'    => null,
			'customGradient'   => null,
			'defaultDuotone'   => null,
			'defaultGradients' => null,
			'defaultPalette'   => null,
			'duotone'          => null,
			'gradients'        => null,
			'link'             => null,
			'heading'          => null,
			'button'           => null,
			'caption'          => null,
			'palette'          => null,
			'text'             => null,
		),
		'custom'                        => null,
		'dimensions'                    => array(
			'aspectRatio' => null,
			'minHeight'   => null,
		),
		'layout'                        => array(
			'contentSize'                   => null,
			'wideSize'                      => null,
			'allowEditing'                  => null,
			'allowCustomContentAndWideSize' => null,
		),
		'lightbox'                      => array(
			'enabled'      => null,
			'allowEditing' => null,
		),
		'position'                      => array(
			'fixed'  => null,
			'sticky' => null,
		),
		'spacing'                       => array(
			'customSpacingSize' => null,
			'spacingSizes'      => null,
			'spacingScale'      => null,
			'blockGap'          => null,
			'margin'            => null,
			'padding'           => null,
			'units'             => null,
		),
		'shadow'                        => array(
			'presets'        => null,
			'defaultPresets' => null,
		),
		'typography'                    => array(
			'fluid'          => null,
			'customFontSize' => null,
			'dropCap'        => null,
			'fontFamilies'   => null,
			'fontSizes'      => null,
			'fontStyle'      => null,
			'fontWeight'     => null,
			'letterSpacing'  => null,
			'lineHeight'     => null,
			'textColumns'    => null,
			'textDecoration' => null,
			'textTransform'  => null,
			'writingMode'    => null,
		),
	);

	/*
	 * The valid properties for fontFamilies under settings key.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	const FONT_FAMILY_SCHEMA = array(
		array(
			'fontFamily' => null,
			'name'       => null,
			'slug'       => null,
			'fontFace'   => array(
				array(
					'ascentOverride'        => null,
					'descentOverride'       => null,
					'fontDisplay'           => null,
					'fontFamily'            => null,
					'fontFeatureSettings'   => null,
					'fontStyle'             => null,
					'fontStretch'           => null,
					'fontVariationSettings' => null,
					'fontWeight'            => null,
					'lineGapOverride'       => null,
					'sizeAdjust'            => null,
					'src'                   => null,
					'unicodeRange'          => null,
				),
			),
		),
	);

	/**
	 * The valid properties under the styles key.
	 *
	 * @since 5.8.0 As `ALLOWED_STYLES`.
	 * @since 5.9.0 Renamed from `ALLOWED_STYLES` to `VALID_STYLES`,
	 *              added new properties for `border`, `filter`, `spacing`,
	 *              and `typography`.
	 * @since 6.1.0 Added new side properties for `border`,
	 *              added new property `shadow`,
	 *              updated `blockGap` to be allowed at any level.
	 * @since 6.2.0 Added `outline`, and `minHeight` properties.
	 * @since 6.3.0 Added support for `typography.textColumns`.
	 * @since 6.5.0 Added support for `dimensions.aspectRatio`.
	 *
	 * @var array
	 */
	const VALID_STYLES = array(
		'border'     => array(
			'color'  => null,
			'radius' => null,
			'style'  => null,
			'width'  => null,
			'top'    => null,
			'right'  => null,
			'bottom' => null,
			'left'   => null,
		),
		'color'      => array(
			'background' => null,
			'gradient'   => null,
			'text'       => null,
		),
		'dimensions' => array(
			'aspectRatio' => null,
			'minHeight'   => null,
		),
		'filter'     => array(
			'duotone' => null,
		),
		'outline'    => array(
			'color'  => null,
			'offset' => null,
			'style'  => null,
			'width'  => null,
		),
		'shadow'     => null,
		'spacing'    => array(
			'margin'   => null,
			'padding'  => null,
			'blockGap' => null,
		),
		'typography' => array(
			'fontFamily'     => null,
			'fontSize'       => null,
			'fontStyle'      => null,
			'fontWeight'     => null,
			'letterSpacing'  => null,
			'lineHeight'     => null,
			'textColumns'    => null,
			'textDecoration' => null,
			'textTransform'  => null,
			'writingMode'    => null,
		),
		'css'        => null,
	);

	/**
	 * Defines which pseudo selectors are enabled for which elements.
	 *
	 * The order of the selectors should be: link, any-link, visited, hover, focus, active.
	 * This is to ensure the user action (hover, focus and active) styles have a higher
	 * specificity than the visited styles, which in turn have a higher specificity than
	 * the unvisited styles.
	 *
	 * See https://core.trac.wordpress.org/ticket/56928.
	 * Note: this will affect both top-level and block-level elements.
	 *
	 * @since 6.1.0
	 * @since 6.2.0 Added support for ':link' and ':any-link'.
	 */
	const VALID_ELEMENT_PSEUDO_SELECTORS = array(
		'link'   => array( ':link', ':any-link', ':visited', ':hover', ':focus', ':active' ),
		'button' => array( ':link', ':any-link', ':visited', ':hover', ':focus', ':active' ),
	);

	/**
	 * The valid elements that can be found under styles.
	 *
	 * @since 5.8.0
	 * @since 6.1.0 Added `heading`, `button`, and `caption` elements.
	 * @var string[]
	 */
	const ELEMENTS = array(
		'link'    => 'a:where(:not(.wp-element-button))', // The `where` is needed to lower the specificity.
		'heading' => 'h1, h2, h3, h4, h5, h6',
		'h1'      => 'h1',
		'h2'      => 'h2',
		'h3'      => 'h3',
		'h4'      => 'h4',
		'h5'      => 'h5',
		'h6'      => 'h6',
		// We have the .wp-block-button__link class so that this will target older buttons that have been serialized.
		'button'  => '.wp-element-button, .wp-block-button__link',
		// The block classes are necessary to target older content that won't use the new class names.
		'caption' => '.wp-element-caption, .wp-block-audio figcaption, .wp-block-embed figcaption, .wp-block-gallery figcaption, .wp-block-image figcaption, .wp-block-table figcaption, .wp-block-video figcaption',
		'cite'    => 'cite',
	);

	const __EXPERIMENTAL_ELEMENT_CLASS_NAMES = array(
		'button'  => 'wp-element-button',
		'caption' => 'wp-element-caption',
	);

	/**
	 * List of block support features that can have their related styles
	 * generated under their own feature level selector rather than the block's.
	 *
	 * @since 6.1.0
	 * @var string[]
	 */
	const BLOCK_SUPPORT_FEATURE_LEVEL_SELECTORS = array(
		'__experimentalBorder' => 'border',
		'color'                => 'color',
		'spacing'              => 'spacing',
		'typography'           => 'typography',
	);

	/**
	 * Return the input schema at the root and per origin.
	 *
	 * @since 6.5.0
	 *
	 * @param array $schema The base schema.
	 * @return array The schema at the root and per origin.
	 *
	 * Example:
	 * schema_in_root_and_per_origin(
	 *   array(
	 *    'fontFamily' => null,
	 *    'slug' => null,
	 *   )
	 * )
	 *
	 * Returns:
	 * array(
	 *  'fontFamily' => null,
	 *  'slug' => null,
	 *  'default' => array(
	 *    'fontFamily' => null,
	 *    'slug' => null,
	 *  ),
	 *  'blocks' => array(
	 *    'fontFamily' => null,
	 *    'slug' => null,
	 *  ),
	 *  'theme' => array(
	 *     'fontFamily' => null,
	 *     'slug' => null,
	 *  ),
	 *  'custom' => array(
	 *     'fontFamily' => null,
	 *     'slug' => null,
	 *  ),
	 * )
	 */
	protected static function schema_in_root_and_per_origin( $schema ) {
		$schema_in_root_and_per_origin = $schema;
		foreach ( static::VALID_ORIGINS as $origin ) {
			$schema_in_root_and_per_origin[ $origin ] = $schema;
		}
		return $schema_in_root_and_per_origin;
	}

	/**
	 * Returns a class name by an element name.
	 *
	 * @since 6.1.0
	 *
	 * @param string $element The name of the element.
	 * @return string The name of the class.
	 */
	public static function get_element_class_name( $element ) {
		$class_name = '';

		if ( isset( static::__EXPERIMENTAL_ELEMENT_CLASS_NAMES[ $element ] ) ) {
			$class_name = static::__EXPERIMENTAL_ELEMENT_CLASS_NAMES[ $element ];
		}

		return $class_name;
	}

	/**
	 * Options that settings.appearanceTools enables.
	 *
	 * @since 6.0.0
	 * @since 6.2.0 Added `dimensions.minHeight` and `position.sticky`.
	 * @since 6.4.0 Added `background.backgroundImage`.
	 * @since 6.5.0 Added `background.backgroundSize` and `dimensions.aspectRatio`.
	 * @var array
	 */
	const APPEARANCE_TOOLS_OPT_INS = array(
		array( 'background', 'backgroundImage' ),
		array( 'background', 'backgroundSize' ),
		array( 'border', 'color' ),
		array( 'border', 'radius' ),
		array( 'border', 'style' ),
		array( 'border', 'width' ),
		array( 'color', 'link' ),
		array( 'color', 'heading' ),
		array( 'color', 'button' ),
		array( 'color', 'caption' ),
		array( 'dimensions', 'aspectRatio' ),
		array( 'dimensions', 'minHeight' ),
		array( 'position', 'sticky' ),
		array( 'spacing', 'blockGap' ),
		array( 'spacing', 'margin' ),
		array( 'spacing', 'padding' ),
		array( 'typography', 'lineHeight' ),
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
		$valid_variations    = array();
		foreach ( self::get_blocks_metadata() as $block_name => $block_meta ) {
			if ( ! isset( $block_meta['styleVariations'] ) ) {
				continue;
			}
			$valid_variations[ $block_name ] = array_keys( $block_meta['styleVariations'] );
		}
		$theme_json       = static::sanitize( $this->theme_json, $valid_block_names, $valid_element_names, $valid_variations );
		$this->theme_json = static::maybe_opt_in_into_settings( $theme_json );

		// Internally, presets are keyed by origin.
		$nodes = static::get_setting_nodes( $this->theme_json );
		foreach ( $nodes as $node ) {
			foreach ( static::PRESETS_METADATA as $preset_metadata ) {
				$path = $node['path'];
				foreach ( $preset_metadata['path'] as $subpath ) {
					$path[] = $subpath;
				}
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
		foreach ( static::APPEARANCE_TOOLS_OPT_INS as $path ) {
			/*
			 * Use "unset prop" as a marker instead of "null" because
			 * "null" can be a valid value for some props (e.g. blockGap).
			 */
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
	 * @since 6.3.0 Added the `$valid_variations` parameter.
	 *
	 * @param array $input               Structure to sanitize.
	 * @param array $valid_block_names   List of valid block names.
	 * @param array $valid_element_names List of valid element names.
	 * @param array $valid_variations    List of valid variations per block.
	 * @return array The sanitized output.
	 */
	protected static function sanitize( $input, $valid_block_names, $valid_element_names, $valid_variations ) {

		$output = array();

		if ( ! is_array( $input ) ) {
			return $output;
		}

		// Preserve only the top most level keys.
		$output = array_intersect_key( $input, array_flip( static::VALID_TOP_LEVEL_KEYS ) );

		/*
		 * Remove any rules that are annotated as "top" in VALID_STYLES constant.
		 * Some styles are only meant to be available at the top-level (e.g.: blockGap),
		 * hence, the schema for blocks & elements should not have them.
		 */
		$styles_non_top_level = static::VALID_STYLES;
		foreach ( array_keys( $styles_non_top_level ) as $section ) {
			// array_key_exists() needs to be used instead of isset() because the value can be null.
			if ( array_key_exists( $section, $styles_non_top_level ) && is_array( $styles_non_top_level[ $section ] ) ) {
				foreach ( array_keys( $styles_non_top_level[ $section ] ) as $prop ) {
					if ( 'top' === $styles_non_top_level[ $section ][ $prop ] ) {
						unset( $styles_non_top_level[ $section ][ $prop ] );
					}
				}
			}
		}

		// Build the schema based on valid block & element names.
		$schema                 = array();
		$schema_styles_elements = array();

		/*
		 * Set allowed element pseudo selectors based on per element allow list.
		 * Target data structure in schema:
		 * e.g.
		 * - top level elements: `$schema['styles']['elements']['link'][':hover']`.
		 * - block level elements: `$schema['styles']['blocks']['core/button']['elements']['link'][':hover']`.
		 */
		foreach ( $valid_element_names as $element ) {
			$schema_styles_elements[ $element ] = $styles_non_top_level;

			if ( isset( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $element ] ) ) {
				foreach ( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $element ] as $pseudo_selector ) {
					$schema_styles_elements[ $element ][ $pseudo_selector ] = $styles_non_top_level;
				}
			}
		}

		$schema_styles_blocks   = array();
		$schema_settings_blocks = array();
		foreach ( $valid_block_names as $block ) {
			// Build the schema for each block style variation.
			$style_variation_names = array();
			if (
				! empty( $input['styles']['blocks'][ $block ]['variations'] ) &&
				is_array( $input['styles']['blocks'][ $block ]['variations'] ) &&
				isset( $valid_variations[ $block ] )
			) {
				$style_variation_names = array_intersect(
					array_keys( $input['styles']['blocks'][ $block ]['variations'] ),
					$valid_variations[ $block ]
				);
			}

			$schema_styles_variations = array();
			if ( ! empty( $style_variation_names ) ) {
				$schema_styles_variations = array_fill_keys( $style_variation_names, $styles_non_top_level );
			}

			$schema_settings_blocks[ $block ]             = static::VALID_SETTINGS;
			$schema_styles_blocks[ $block ]               = $styles_non_top_level;
			$schema_styles_blocks[ $block ]['elements']   = $schema_styles_elements;
			$schema_styles_blocks[ $block ]['variations'] = $schema_styles_variations;
		}

		$schema['styles']                                 = static::VALID_STYLES;
		$schema['styles']['blocks']                       = $schema_styles_blocks;
		$schema['styles']['elements']                     = $schema_styles_elements;
		$schema['settings']                               = static::VALID_SETTINGS;
		$schema['settings']['blocks']                     = $schema_settings_blocks;
		$schema['settings']['typography']['fontFamilies'] = static::schema_in_root_and_per_origin( static::FONT_FAMILY_SCHEMA );

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
				$output[ $subtree ] = static::resolve_custom_css_format( $result );
			}
		}

		return $output;
	}

	/**
	 * Appends a sub-selector to an existing one.
	 *
	 * Given the compounded $selector "h1, h2, h3"
	 * and the $to_append selector ".some-class" the result will be
	 * "h1.some-class, h2.some-class, h3.some-class".
	 *
	 * @since 5.8.0
	 * @since 6.1.0 Added append position.
	 * @since 6.3.0 Removed append position parameter.
	 *
	 * @param string $selector  Original selector.
	 * @param string $to_append Selector to append.
	 * @return string The new selector.
	 */
	protected static function append_to_selector( $selector, $to_append ) {
		if ( ! str_contains( $selector, ',' ) ) {
			return $selector . $to_append;
		}
		$new_selectors = array();
		$selectors     = explode( ',', $selector );
		foreach ( $selectors as $sel ) {
			$new_selectors[] = $sel . $to_append;
		}
		return implode( ',', $new_selectors );
	}

	/**
	 * Prepends a sub-selector to an existing one.
	 *
	 * Given the compounded $selector "h1, h2, h3"
	 * and the $to_prepend selector ".some-class " the result will be
	 * ".some-class h1, .some-class  h2, .some-class  h3".
	 *
	 * @since 6.3.0
	 *
	 * @param string $selector   Original selector.
	 * @param string $to_prepend Selector to prepend.
	 * @return string The new selector.
	 */
	protected static function prepend_to_selector( $selector, $to_prepend ) {
		if ( ! str_contains( $selector, ',' ) ) {
			return $to_prepend . $selector;
		}
		$new_selectors = array();
		$selectors     = explode( ',', $selector );
		foreach ( $selectors as $sel ) {
			$new_selectors[] = $to_prepend . $sel;
		}
		return implode( ',', $new_selectors );
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
	 * @since 6.1.0 Added `features` key with block support feature level selectors.
	 * @since 6.3.0 Refactored and stabilized selectors API.
	 *
	 * @return array Block metadata.
	 */
	protected static function get_blocks_metadata() {
		$registry = WP_Block_Type_Registry::get_instance();
		$blocks   = $registry->get_all_registered();

		// Is there metadata for all currently registered blocks?
		$blocks = array_diff_key( $blocks, static::$blocks_metadata );
		if ( empty( $blocks ) ) {
			return static::$blocks_metadata;
		}

		foreach ( $blocks as $block_name => $block_type ) {
			$root_selector = wp_get_block_css_selector( $block_type );

			static::$blocks_metadata[ $block_name ]['selector']  = $root_selector;
			static::$blocks_metadata[ $block_name ]['selectors'] = static::get_block_selectors( $block_type, $root_selector );

			$elements = static::get_block_element_selectors( $root_selector );
			if ( ! empty( $elements ) ) {
				static::$blocks_metadata[ $block_name ]['elements'] = $elements;
			}

			// The block may or may not have a duotone selector.
			$duotone_selector = wp_get_block_css_selector( $block_type, 'filter.duotone' );

			// Keep backwards compatibility for support.color.__experimentalDuotone.
			if ( null === $duotone_selector ) {
				$duotone_support = isset( $block_type->supports['color']['__experimentalDuotone'] )
					? $block_type->supports['color']['__experimentalDuotone']
					: null;

				if ( $duotone_support ) {
					$root_selector    = wp_get_block_css_selector( $block_type );
					$duotone_selector = static::scope_selector( $root_selector, $duotone_support );
				}
			}

			if ( null !== $duotone_selector ) {
				static::$blocks_metadata[ $block_name ]['duotone'] = $duotone_selector;
			}

			// If the block has style variations, append their selectors to the block metadata.
			if ( ! empty( $block_type->styles ) ) {
				$style_selectors = array();
				foreach ( $block_type->styles as $style ) {
					$style_selectors[ $style['name'] ] = static::get_block_style_variation_selector( $style['name'], static::$blocks_metadata[ $block_name ]['selector'] );
				}
				static::$blocks_metadata[ $block_name ]['styleVariations'] = $style_selectors;
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
	 * @return array The modified $tree.
	 */
	protected static function remove_keys_not_in_schema( $tree, $schema ) {
		if ( ! is_array( $tree ) ) {
			return $tree;
		}

		foreach ( $tree as $key => $value ) {
			// Remove keys not in the schema or with null/empty values.
			if ( ! array_key_exists( $key, $schema ) ) {
				unset( $tree[ $key ] );
				continue;
			}

			if ( is_array( $schema[ $key ] ) ) {
				if ( ! is_array( $value ) ) {
					unset( $tree[ $key ] );
				} elseif ( wp_is_numeric_array( $value ) ) {
					// If indexed, process each item in the array.
					foreach ( $value as $item_key => $item_value ) {
						if ( isset( $schema[ $key ][0] ) && is_array( $schema[ $key ][0] ) ) {
							$tree[ $key ][ $item_key ] = self::remove_keys_not_in_schema( $item_value, $schema[ $key ][0] );
						} else {
							// If the schema does not define a further structure, keep the value as is.
							$tree[ $key ][ $item_key ] = $item_value;
						}
					}
				} else {
					// If associative, process as a single object.
					$tree[ $key ] = self::remove_keys_not_in_schema( $value, $schema[ $key ] );

					if ( empty( $tree[ $key ] ) ) {
						unset( $tree[ $key ] );
					}
				}
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
	 * @since 5.9.0 Removed the `$type` parameter, added the `$types` and `$origins` parameters.
	 * @since 6.3.0 Add fallback layout styles for Post Template when block gap support isn't available.
	 *
	 * @param string[] $types   Types of styles to load. Will load all by default. It accepts:
	 *                          - `variables`: only the CSS Custom Properties for presets & custom ones.
	 *                          - `styles`: only the styles section in theme.json.
	 *                          - `presets`: only the classes for the presets.
	 * @param string[] $origins A list of origins to include. By default it includes VALID_ORIGINS.
	 * @param array    $options An array of options for now used for internal purposes only (may change without notice).
	 *                          The options currently supported are 'scope' that makes sure all style are scoped to a
	 *                          given selector, and root_selector which overwrites and forces a given selector to be
	 *                          used on the root node.
	 * @return string The resulting stylesheet.
	 */
	public function get_stylesheet( $types = array( 'variables', 'styles', 'presets' ), $origins = null, $options = array() ) {
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

		$root_style_key    = array_search( static::ROOT_BLOCK_SELECTOR, array_column( $style_nodes, 'selector' ), true );
		$root_settings_key = array_search( static::ROOT_BLOCK_SELECTOR, array_column( $setting_nodes, 'selector' ), true );

		if ( ! empty( $options['scope'] ) ) {
			foreach ( $setting_nodes as &$node ) {
				$node['selector'] = static::scope_selector( $options['scope'], $node['selector'] );
			}
			foreach ( $style_nodes as &$node ) {
				$node['selector'] = static::scope_selector( $options['scope'], $node['selector'] );
			}
			unset( $node );
		}

		if ( ! empty( $options['root_selector'] ) ) {
			if ( false !== $root_settings_key ) {
				$setting_nodes[ $root_settings_key ]['selector'] = $options['root_selector'];
			}
			if ( false !== $root_style_key ) {
				$style_nodes[ $root_style_key ]['selector'] = $options['root_selector'];
			}
		}

		$stylesheet = '';

		if ( in_array( 'variables', $types, true ) ) {
			$stylesheet .= $this->get_css_variables( $setting_nodes, $origins );
		}

		if ( in_array( 'styles', $types, true ) ) {
			if ( false !== $root_style_key ) {
				$stylesheet .= $this->get_root_layout_rules( $style_nodes[ $root_style_key ]['selector'], $style_nodes[ $root_style_key ] );
			}
			$stylesheet .= $this->get_block_classes( $style_nodes );
		} elseif ( in_array( 'base-layout-styles', $types, true ) ) {
			$root_selector          = static::ROOT_BLOCK_SELECTOR;
			$columns_selector       = '.wp-block-columns';
			$post_template_selector = '.wp-block-post-template';
			if ( ! empty( $options['scope'] ) ) {
				$root_selector          = static::scope_selector( $options['scope'], $root_selector );
				$columns_selector       = static::scope_selector( $options['scope'], $columns_selector );
				$post_template_selector = static::scope_selector( $options['scope'], $post_template_selector );
			}
			if ( ! empty( $options['root_selector'] ) ) {
				$root_selector = $options['root_selector'];
			}
			/*
			 * Base layout styles are provided as part of `styles`, so only output separately if explicitly requested.
			 * For backwards compatibility, the Columns block is explicitly included, to support a different default gap value.
			 */
			$base_styles_nodes = array(
				array(
					'path'     => array( 'styles' ),
					'selector' => $root_selector,
				),
				array(
					'path'     => array( 'styles', 'blocks', 'core/columns' ),
					'selector' => $columns_selector,
					'name'     => 'core/columns',
				),
				array(
					'path'     => array( 'styles', 'blocks', 'core/post-template' ),
					'selector' => $post_template_selector,
					'name'     => 'core/post-template',
				),
			);

			foreach ( $base_styles_nodes as $base_style_node ) {
				$stylesheet .= $this->get_layout_styles( $base_style_node );
			}
		}

		if ( in_array( 'presets', $types, true ) ) {
			$stylesheet .= $this->get_preset_classes( $setting_nodes, $origins );
		}

		return $stylesheet;
	}

	/**
	 * Processes the CSS, to apply nesting.
	 *
	 * @since 6.2.0
	 *
	 * @param string $css      The CSS to process.
	 * @param string $selector The selector to nest.
	 * @return string The processed CSS.
	 */
	protected function process_blocks_custom_css( $css, $selector ) {
		$processed_css = '';

		// Split CSS nested rules.
		$parts = explode( '&', $css );
		foreach ( $parts as $part ) {
			$is_root_css = ( ! str_contains( $part, '{' ) );
			if ( $is_root_css ) {
				// If the part doesn't contain braces, it applies to the root level.
				$processed_css .= trim( $selector ) . '{' . trim( $part ) . '}';
			} else {
				// If the part contains braces, it's a nested CSS rule.
				$part = explode( '{', str_replace( '}', '', $part ) );
				if ( count( $part ) !== 2 ) {
					continue;
				}
				$nested_selector = $part[0];
				$css_value       = $part[1];
				$part_selector   = str_starts_with( $nested_selector, ' ' )
					? static::scope_selector( $selector, $nested_selector )
					: static::append_to_selector( $selector, $nested_selector );
				$processed_css  .= $part_selector . '{' . trim( $css_value ) . '}';
			}
		}
		return $processed_css;
	}

	/**
	 * Returns the global styles custom CSS.
	 *
	 * @since 6.2.0
	 *
	 * @return string The global styles custom CSS.
	 */
	public function get_custom_css() {
		// Add the global styles root CSS.
		$stylesheet = isset( $this->theme_json['styles']['css'] ) ? $this->theme_json['styles']['css'] : '';

		// Add the global styles block CSS.
		if ( isset( $this->theme_json['styles']['blocks'] ) ) {
			foreach ( $this->theme_json['styles']['blocks'] as $name => $node ) {
				$custom_block_css = isset( $this->theme_json['styles']['blocks'][ $name ]['css'] )
					? $this->theme_json['styles']['blocks'][ $name ]['css']
					: null;
				if ( $custom_block_css ) {
					$selector    = static::$blocks_metadata[ $name ]['selector'];
					$stylesheet .= $this->process_blocks_custom_css( $custom_block_css, $selector );
				}
			}
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
	 * @since 6.1.0 Moved most internal logic to `get_styles_for_block()`.
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
			$block_rules .= static::get_styles_for_block( $metadata );
		}

		return $block_rules;
	}

	/**
	 * Gets the CSS layout rules for a particular block from theme.json layout definitions.
	 *
	 * @since 6.1.0
	 * @since 6.3.0 Reduced specificity for layout margin rules.
	 *
	 * @param array $block_metadata Metadata about the block to get styles for.
	 * @return string Layout styles for the block.
	 */
	protected function get_layout_styles( $block_metadata ) {
		$block_rules = '';
		$block_type  = null;

		// Skip outputting layout styles if explicitly disabled.
		if ( current_theme_supports( 'disable-layout-styles' ) ) {
			return $block_rules;
		}

		if ( isset( $block_metadata['name'] ) ) {
			$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block_metadata['name'] );
			if ( ! block_has_support( $block_type, 'layout', false ) && ! block_has_support( $block_type, '__experimentalLayout', false ) ) {
				return $block_rules;
			}
		}

		$selector                 = isset( $block_metadata['selector'] ) ? $block_metadata['selector'] : '';
		$has_block_gap_support    = isset( $this->theme_json['settings']['spacing']['blockGap'] );
		$has_fallback_gap_support = ! $has_block_gap_support; // This setting isn't useful yet: it exists as a placeholder for a future explicit fallback gap styles support.
		$node                     = _wp_array_get( $this->theme_json, $block_metadata['path'], array() );
		$layout_definitions       = wp_get_layout_definitions();
		$layout_selector_pattern  = '/^[a-zA-Z0-9\-\.\ *+>:\(\)]*$/'; // Allow alphanumeric classnames, spaces, wildcard, sibling, child combinator and pseudo class selectors.

		/*
		 * Gap styles will only be output if the theme has block gap support, or supports a fallback gap.
		 * Default layout gap styles will be skipped for themes that do not explicitly opt-in to blockGap with a `true` or `false` value.
		 */
		if ( $has_block_gap_support || $has_fallback_gap_support ) {
			$block_gap_value = null;
			// Use a fallback gap value if block gap support is not available.
			if ( ! $has_block_gap_support ) {
				$block_gap_value = static::ROOT_BLOCK_SELECTOR === $selector ? '0.5em' : null;
				if ( ! empty( $block_type ) ) {
					$block_gap_value = isset( $block_type->supports['spacing']['blockGap']['__experimentalDefault'] )
						? $block_type->supports['spacing']['blockGap']['__experimentalDefault']
						: null;
				}
			} else {
				$block_gap_value = static::get_property_value( $node, array( 'spacing', 'blockGap' ) );
			}

			// Support split row / column values and concatenate to a shorthand value.
			if ( is_array( $block_gap_value ) ) {
				if ( isset( $block_gap_value['top'] ) && isset( $block_gap_value['left'] ) ) {
					$gap_row         = static::get_property_value( $node, array( 'spacing', 'blockGap', 'top' ) );
					$gap_column      = static::get_property_value( $node, array( 'spacing', 'blockGap', 'left' ) );
					$block_gap_value = $gap_row === $gap_column ? $gap_row : $gap_row . ' ' . $gap_column;
				} else {
					// Skip outputting gap value if not all sides are provided.
					$block_gap_value = null;
				}
			}

			// If the block should have custom gap, add the gap styles.
			if ( null !== $block_gap_value && false !== $block_gap_value && '' !== $block_gap_value ) {
				foreach ( $layout_definitions as $layout_definition_key => $layout_definition ) {
					// Allow outputting fallback gap styles for flex and grid layout types when block gap support isn't available.
					if ( ! $has_block_gap_support && 'flex' !== $layout_definition_key && 'grid' !== $layout_definition_key ) {
						continue;
					}

					$class_name    = isset( $layout_definition['className'] ) ? $layout_definition['className'] : false;
					$spacing_rules = isset( $layout_definition['spacingStyles'] ) ? $layout_definition['spacingStyles'] : array();

					if (
						! empty( $class_name ) &&
						! empty( $spacing_rules )
					) {
						foreach ( $spacing_rules as $spacing_rule ) {
							$declarations = array();
							if (
								isset( $spacing_rule['selector'] ) &&
								preg_match( $layout_selector_pattern, $spacing_rule['selector'] ) &&
								! empty( $spacing_rule['rules'] )
							) {
								// Iterate over each of the styling rules and substitute non-string values such as `null` with the real `blockGap` value.
								foreach ( $spacing_rule['rules'] as $css_property => $css_value ) {
									$current_css_value = is_string( $css_value ) ? $css_value : $block_gap_value;
									if ( static::is_safe_css_declaration( $css_property, $current_css_value ) ) {
										$declarations[] = array(
											'name'  => $css_property,
											'value' => $current_css_value,
										);
									}
								}

								if ( ! $has_block_gap_support ) {
									// For fallback gap styles, use lower specificity, to ensure styles do not unintentionally override theme styles.
									$format          = static::ROOT_BLOCK_SELECTOR === $selector ? ':where(.%2$s%3$s)' : ':where(%1$s.%2$s%3$s)';
									$layout_selector = sprintf(
										$format,
										$selector,
										$class_name,
										$spacing_rule['selector']
									);
								} else {
									$format          = static::ROOT_BLOCK_SELECTOR === $selector ? ':where(%s .%s) %s' : '%s-%s%s';
									$layout_selector = sprintf(
										$format,
										$selector,
										$class_name,
										$spacing_rule['selector']
									);
								}
								$block_rules .= static::to_ruleset( $layout_selector, $declarations );
							}
						}
					}
				}
			}
		}

		// Output base styles.
		if (
			static::ROOT_BLOCK_SELECTOR === $selector
		) {
			$valid_display_modes = array( 'block', 'flex', 'grid' );
			foreach ( $layout_definitions as $layout_definition ) {
				$class_name       = isset( $layout_definition['className'] ) ? $layout_definition['className'] : false;
				$base_style_rules = isset( $layout_definition['baseStyles'] ) ? $layout_definition['baseStyles'] : array();

				if (
					! empty( $class_name ) &&
					is_array( $base_style_rules )
				) {
					// Output display mode. This requires special handling as `display` is not exposed in `safe_style_css_filter`.
					if (
						! empty( $layout_definition['displayMode'] ) &&
						is_string( $layout_definition['displayMode'] ) &&
						in_array( $layout_definition['displayMode'], $valid_display_modes, true )
					) {
						$layout_selector = sprintf(
							'%s .%s',
							$selector,
							$class_name
						);
						$block_rules    .= static::to_ruleset(
							$layout_selector,
							array(
								array(
									'name'  => 'display',
									'value' => $layout_definition['displayMode'],
								),
							)
						);
					}

					foreach ( $base_style_rules as $base_style_rule ) {
						$declarations = array();

						if (
							isset( $base_style_rule['selector'] ) &&
							preg_match( $layout_selector_pattern, $base_style_rule['selector'] ) &&
							! empty( $base_style_rule['rules'] )
						) {
							foreach ( $base_style_rule['rules'] as $css_property => $css_value ) {
								if ( static::is_safe_css_declaration( $css_property, $css_value ) ) {
									$declarations[] = array(
										'name'  => $css_property,
										'value' => $css_value,
									);
								}
							}

							$layout_selector = sprintf(
								'%s .%s%s',
								$selector,
								$class_name,
								$base_style_rule['selector']
							);
							$block_rules    .= static::to_ruleset( $layout_selector, $declarations );
						}
					}
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
	 * @param array    $setting_nodes Nodes with settings.
	 * @param string[] $origins       List of origins to process presets from.
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
	 * @param array    $nodes   Nodes with settings.
	 * @param string[] $origins List of origins to process.
	 * @return string The new stylesheet.
	 */
	protected function get_css_variables( $nodes, $origins ) {
		$stylesheet = '';
		foreach ( $nodes as $metadata ) {
			if ( null === $metadata['selector'] ) {
				continue;
			}

			$selector = $metadata['selector'];

			$node                    = _wp_array_get( $this->theme_json, $metadata['path'], array() );
			$declarations            = static::compute_preset_vars( $node, $origins );
			$theme_vars_declarations = static::compute_theme_vars( $node );
			foreach ( $theme_vars_declarations as $theme_vars_declaration ) {
				$declarations[] = $theme_vars_declaration;
			}

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
	 * @return string The resulting CSS ruleset.
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
	 * Given a settings array, returns the generated rulesets
	 * for the preset classes.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added the `$origins` parameter.
	 *
	 * @param array    $settings Settings to process.
	 * @param string   $selector Selector wrapping the classes.
	 * @param string[] $origins  List of origins to process.
	 * @return string The result of processing the presets.
	 */
	protected static function compute_preset_classes( $settings, $selector, $origins ) {
		if ( static::ROOT_BLOCK_SELECTOR === $selector ) {
			/*
			 * Classes at the global level do not need any CSS prefixed,
			 * and we don't want to increase its specificity.
			 */
			$selector = '';
		}

		$stylesheet = '';
		foreach ( static::PRESETS_METADATA as $preset_metadata ) {
			if ( empty( $preset_metadata['classes'] ) ) {
				continue;
			}
			$slugs = static::get_settings_slugs( $settings, $preset_metadata, $origins );
			foreach ( $preset_metadata['classes'] as $class => $property ) {
				foreach ( $slugs as $slug ) {
					$css_var    = static::replace_slug_in_string( $preset_metadata['css_vars'], $slug );
					$class_name = static::replace_slug_in_string( $class, $slug );

					// $selector is often empty, so we can save ourselves the `append_to_selector()` call then.
					$new_selector = '' === $selector ? $class_name : static::append_to_selector( $selector, $class_name );
					$stylesheet  .= static::to_ruleset(
						$new_selector,
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
	public static function scope_selector( $scope, $selector ) {
		$scopes    = explode( ',', $scope );
		$selectors = explode( ',', $selector );

		$selectors_scoped = array();
		foreach ( $scopes as $outer ) {
			foreach ( $selectors as $inner ) {
				$outer = trim( $outer );
				$inner = trim( $inner );
				if ( ! empty( $outer ) && ! empty( $inner ) ) {
					$selectors_scoped[] = $outer . ' ' . $inner;
				} elseif ( empty( $outer ) ) {
					$selectors_scoped[] = $inner;
				} elseif ( empty( $inner ) ) {
					$selectors_scoped[] = $outer;
				}
			}
		}

		$result = implode( ', ', $selectors_scoped );
		return $result;
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
	 * @param array    $settings        Settings to process.
	 * @param array    $preset_metadata One of the PRESETS_METADATA values.
	 * @param string[] $origins         List of origins to process.
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
	 * @param array    $settings        Settings to process.
	 * @param array    $preset_metadata One of the PRESETS_METADATA values.
	 * @param string[] $origins         List of origins to process.
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
	 * Transforms a slug into a CSS Custom Property.
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
	 * Given the block settings, extracts the CSS Custom Properties
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
	 * @param array    $settings Settings to process.
	 * @param string[] $origins  List of origins to process.
	 * @return array The modified $declarations.
	 */
	protected static function compute_preset_vars( $settings, $origins ) {
		$declarations = array();
		foreach ( static::PRESETS_METADATA as $preset_metadata ) {
			if ( empty( $preset_metadata['css_vars'] ) ) {
				continue;
			}
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
	 * Given an array of settings, extracts the CSS Custom Properties
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
	 * @return array The modified $declarations.
	 */
	protected static function compute_theme_vars( $settings ) {
		$declarations  = array();
		$custom_values = isset( $settings['custom'] ) ? $settings['custom'] : array();
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
				$new_prefix        = $new_key . $token;
				$flattened_subtree = static::flatten_tree( $value, $new_prefix, $token );
				foreach ( $flattened_subtree as $subtree_key => $subtree_value ) {
					$result[ $subtree_key ] = $subtree_value;
				}
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
	 * @since 6.1.0 Added `$theme_json`, `$selector`, and `$use_root_padding` parameters.
	 * @since 6.5.0 Output a `min-height: unset` rule when `aspect-ratio` is set.
	 *
	 * @param array   $styles Styles to process.
	 * @param array   $settings Theme settings.
	 * @param array   $properties Properties metadata.
	 * @param array   $theme_json Theme JSON array.
	 * @param string  $selector The style block selector.
	 * @param boolean $use_root_padding Whether to add custom properties at root level.
	 * @return array  Returns the modified $declarations.
	 */
	protected static function compute_style_properties( $styles, $settings = array(), $properties = null, $theme_json = null, $selector = null, $use_root_padding = null ) {
		if ( null === $properties ) {
			$properties = static::PROPERTIES_METADATA;
		}

		$declarations = array();
		if ( empty( $styles ) ) {
			return $declarations;
		}

		$root_variable_duplicates = array();

		foreach ( $properties as $css_property => $value_path ) {
			$value = static::get_property_value( $styles, $value_path, $theme_json );

			if ( str_starts_with( $css_property, '--wp--style--root--' ) && ( static::ROOT_BLOCK_SELECTOR !== $selector || ! $use_root_padding ) ) {
				continue;
			}
			/*
			 * Root-level padding styles don't currently support strings with CSS shorthand values.
			 * This may change: https://github.com/WordPress/gutenberg/issues/40132.
			 */
			if ( '--wp--style--root--padding' === $css_property && is_string( $value ) ) {
				continue;
			}

			if ( str_starts_with( $css_property, '--wp--style--root--' ) && $use_root_padding ) {
				$root_variable_duplicates[] = substr( $css_property, strlen( '--wp--style--root--' ) );
			}

			/*
			 * Look up protected properties, keyed by value path.
			 * Skip protected properties that are explicitly set to `null`.
			 */
			if ( is_array( $value_path ) ) {
				$path_string = implode( '.', $value_path );
				if (
					isset( static::PROTECTED_PROPERTIES[ $path_string ] ) &&
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

			// Calculates fluid typography rules where available.
			if ( 'font-size' === $css_property ) {
				/*
				 * wp_get_typography_font_size_value() will check
				 * if fluid typography has been activated and also
				 * whether the incoming value can be converted to a fluid value.
				 * Values that already have a clamp() function will not pass the test,
				 * and therefore the original $value will be returned.
				 */
				$value = wp_get_typography_font_size_value( array( 'size' => $value ) );
			}

			if ( 'aspect-ratio' === $css_property ) {
				// For aspect ratio to work, other dimensions rules must be unset.
				// This ensures that a fixed height does not override the aspect ratio.
				$declarations[] = array(
					'name'  => 'min-height',
					'value' => 'unset',
				);
			}

			$declarations[] = array(
				'name'  => $css_property,
				'value' => $value,
			);
		}

		// If a variable value is added to the root, the corresponding property should be removed.
		foreach ( $root_variable_duplicates as $duplicate ) {
			$discard = array_search( $duplicate, array_column( $declarations, 'name' ), true );
			if ( is_numeric( $discard ) ) {
				array_splice( $declarations, $discard, 1 );
			}
		}

		return $declarations;
	}

	/**
	 * Returns the style property for the given path.
	 *
	 * It also converts references to a path to the value
	 * stored at that location, e.g.
	 * { "ref": "style.color.background" } => "#fff".
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added support for values of array type, which are returned as is.
	 * @since 6.1.0 Added the `$theme_json` parameter.
	 * @since 6.3.0 It no longer converts the internal format "var:preset|color|secondary"
	 *              to the standard form "--wp--preset--color--secondary".
	 *              This is already done by the sanitize method,
	 *              so every property will be in the standard form.
	 *
	 * @param array $styles Styles subtree.
	 * @param array $path   Which property to process.
	 * @param array $theme_json Theme JSON array.
	 * @return string|array Style property value.
	 */
	protected static function get_property_value( $styles, $path, $theme_json = null ) {
		$value = _wp_array_get( $styles, $path, '' );

		if ( '' === $value || null === $value ) {
			// No need to process the value further.
			return '';
		}

		/*
		 * This converts references to a path to the value at that path
		 * where the values is an array with a "ref" key, pointing to a path.
		 * For example: { "ref": "style.color.background" } => "#fff".
		 */
		if ( is_array( $value ) && isset( $value['ref'] ) ) {
			$value_path = explode( '.', $value['ref'] );
			$ref_value  = _wp_array_get( $theme_json, $value_path );
			// Only use the ref value if we find anything.
			if ( ! empty( $ref_value ) && is_string( $ref_value ) ) {
				$value = $ref_value;
			}

			if ( is_array( $ref_value ) && isset( $ref_value['ref'] ) ) {
				$path_string      = json_encode( $path );
				$ref_value_string = json_encode( $ref_value );
				_doing_it_wrong(
					'get_property_value',
					sprintf(
						/* translators: 1: theme.json, 2: Value name, 3: Value path, 4: Another value name. */
						__( 'Your %1$s file uses a dynamic value (%2$s) for the path at %3$s. However, the value at %3$s is also a dynamic value (pointing to %4$s) and pointing to another dynamic value is not supported. Please update %3$s to point directly to %4$s.' ),
						'theme.json',
						$ref_value_string,
						$path_string,
						$ref_value['ref']
					),
					'6.1.0'
				);
			}
		}

		if ( is_array( $value ) ) {
			return $value;
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
	 * @return array An array of setting nodes metadata.
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
	 * @return array An array of style nodes metadata.
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
			foreach ( self::ELEMENTS as $element => $selector ) {
				if ( ! isset( $theme_json['styles']['elements'][ $element ] ) ) {
					continue;
				}
				$nodes[] = array(
					'path'     => array( 'styles', 'elements', $element ),
					'selector' => static::ELEMENTS[ $element ],
				);

				// Handle any pseudo selectors for the element.
				if ( isset( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $element ] ) ) {
					foreach ( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $element ] as $pseudo_selector ) {

						if ( isset( $theme_json['styles']['elements'][ $element ][ $pseudo_selector ] ) ) {
							$nodes[] = array(
								'path'     => array( 'styles', 'elements', $element ),
								'selector' => static::append_to_selector( static::ELEMENTS[ $element ], $pseudo_selector ),
							);
						}
					}
				}
			}
		}

		// Blocks.
		if ( ! isset( $theme_json['styles']['blocks'] ) ) {
			return $nodes;
		}

		$block_nodes = static::get_block_nodes( $theme_json );
		foreach ( $block_nodes as $block_node ) {
			$nodes[] = $block_node;
		}

		/**
		 * Filters the list of style nodes with metadata.
		 *
		 * This allows for things like loading block CSS independently.
		 *
		 * @since 6.1.0
		 *
		 * @param array $nodes Style nodes with metadata.
		 */
		return apply_filters( 'wp_theme_json_get_style_nodes', $nodes );
	}

	/**
	 * A public helper to get the block nodes from a theme.json file.
	 *
	 * @since 6.1.0
	 *
	 * @return array The block nodes in theme.json.
	 */
	public function get_styles_block_nodes() {
		return static::get_block_nodes( $this->theme_json );
	}

	/**
	 * Returns a filtered declarations array if there is a separator block with only a background
	 * style defined in theme.json by adding a color attribute to reflect the changes in the front.
	 *
	 * @since 6.1.1
	 *
	 * @param array $declarations List of declarations.
	 * @return array $declarations List of declarations filtered.
	 */
	private static function update_separator_declarations( $declarations ) {
		$background_color     = '';
		$border_color_matches = false;
		$text_color_matches   = false;

		foreach ( $declarations as $declaration ) {
			if ( 'background-color' === $declaration['name'] && ! $background_color && isset( $declaration['value'] ) ) {
				$background_color = $declaration['value'];
			} elseif ( 'border-color' === $declaration['name'] ) {
				$border_color_matches = true;
			} elseif ( 'color' === $declaration['name'] ) {
				$text_color_matches = true;
			}

			if ( $background_color && $border_color_matches && $text_color_matches ) {
				break;
			}
		}

		if ( $background_color && ! $border_color_matches && ! $text_color_matches ) {
			$declarations[] = array(
				'name'  => 'color',
				'value' => $background_color,
			);
		}

		return $declarations;
	}

	/**
	 * An internal method to get the block nodes from a theme.json file.
	 *
	 * @since 6.1.0
	 * @since 6.3.0 Refactored and stabilized selectors API.
	 *
	 * @param array $theme_json The theme.json converted to an array.
	 * @return array The block nodes in theme.json.
	 */
	private static function get_block_nodes( $theme_json ) {
		$selectors = static::get_blocks_metadata();
		$nodes     = array();
		if ( ! isset( $theme_json['styles'] ) ) {
			return $nodes;
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

			$feature_selectors = null;
			if ( isset( $selectors[ $name ]['selectors'] ) ) {
				$feature_selectors = $selectors[ $name ]['selectors'];
			}

			$variation_selectors = array();
			if ( isset( $node['variations'] ) ) {
				foreach ( $node['variations'] as $variation => $node ) {
					$variation_selectors[] = array(
						'path'     => array( 'styles', 'blocks', $name, 'variations', $variation ),
						'selector' => $selectors[ $name ]['styleVariations'][ $variation ],
					);
				}
			}

			$nodes[] = array(
				'name'       => $name,
				'path'       => array( 'styles', 'blocks', $name ),
				'selector'   => $selector,
				'selectors'  => $feature_selectors,
				'duotone'    => $duotone_selector,
				'features'   => $feature_selectors,
				'variations' => $variation_selectors,
			);

			if ( isset( $theme_json['styles']['blocks'][ $name ]['elements'] ) ) {
				foreach ( $theme_json['styles']['blocks'][ $name ]['elements'] as $element => $node ) {
					$nodes[] = array(
						'path'     => array( 'styles', 'blocks', $name, 'elements', $element ),
						'selector' => $selectors[ $name ]['elements'][ $element ],
					);

					// Handle any pseudo selectors for the element.
					if ( isset( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $element ] ) ) {
						foreach ( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $element ] as $pseudo_selector ) {
							if ( isset( $theme_json['styles']['blocks'][ $name ]['elements'][ $element ][ $pseudo_selector ] ) ) {
								$nodes[] = array(
									'path'     => array( 'styles', 'blocks', $name, 'elements', $element ),
									'selector' => static::append_to_selector( $selectors[ $name ]['elements'][ $element ], $pseudo_selector ),
								);
							}
						}
					}
				}
			}
		}

		return $nodes;
	}

	/**
	 * Gets the CSS rules for a particular block from theme.json.
	 *
	 * @since 6.1.0
	 *
	 * @param array $block_metadata Metadata about the block to get styles for.
	 *
	 * @return string Styles for the block.
	 */
	public function get_styles_for_block( $block_metadata ) {
		$node                 = _wp_array_get( $this->theme_json, $block_metadata['path'], array() );
		$use_root_padding     = isset( $this->theme_json['settings']['useRootPaddingAwareAlignments'] ) && true === $this->theme_json['settings']['useRootPaddingAwareAlignments'];
		$selector             = $block_metadata['selector'];
		$settings             = isset( $this->theme_json['settings'] ) ? $this->theme_json['settings'] : array();
		$feature_declarations = static::get_feature_declarations_for_node( $block_metadata, $node );

		// If there are style variations, generate the declarations for them, including any feature selectors the block may have.
		$style_variation_declarations = array();
		if ( ! empty( $block_metadata['variations'] ) ) {
			foreach ( $block_metadata['variations'] as $style_variation ) {
				$style_variation_node           = _wp_array_get( $this->theme_json, $style_variation['path'], array() );
				$clean_style_variation_selector = trim( $style_variation['selector'] );

				// Generate any feature/subfeature style declarations for the current style variation.
				$variation_declarations = static::get_feature_declarations_for_node( $block_metadata, $style_variation_node );

				// Combine selectors with style variation's selector and add to overall style variation declarations.
				foreach ( $variation_declarations as $current_selector => $new_declarations ) {
					// If current selector includes block classname, remove it but leave the whitespace in.
					$shortened_selector = str_replace( $block_metadata['selector'] . ' ', ' ', $current_selector );

					// Prepend the variation selector to the current selector.
					$split_selectors    = explode( ',', $shortened_selector );
					$updated_selectors  = array_map(
						static function ( $split_selector ) use ( $clean_style_variation_selector ) {
							return $clean_style_variation_selector . $split_selector;
						},
						$split_selectors
					);
					$combined_selectors = implode( ',', $updated_selectors );

					// Add the new declarations to the overall results under the modified selector.
					$style_variation_declarations[ $combined_selectors ] = $new_declarations;
				}

				// Compute declarations for remaining styles not covered by feature level selectors.
				$style_variation_declarations[ $style_variation['selector'] ] = static::compute_style_properties( $style_variation_node, $settings, null, $this->theme_json );
			}
		}
		/*
		 * Get a reference to element name from path.
		 * $block_metadata['path'] = array( 'styles','elements','link' );
		 * Make sure that $block_metadata['path'] describes an element node, like [ 'styles', 'element', 'link' ].
		 * Skip non-element paths like just ['styles'].
		 */
		$is_processing_element = in_array( 'elements', $block_metadata['path'], true );

		$current_element = $is_processing_element ? $block_metadata['path'][ count( $block_metadata['path'] ) - 1 ] : null;

		$element_pseudo_allowed = array();

		if ( isset( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $current_element ] ) ) {
			$element_pseudo_allowed = static::VALID_ELEMENT_PSEUDO_SELECTORS[ $current_element ];
		}

		/*
		 * Check for allowed pseudo classes (e.g. ":hover") from the $selector ("a:hover").
		 * This also resets the array keys.
		 */
		$pseudo_matches = array_values(
			array_filter(
				$element_pseudo_allowed,
				static function ( $pseudo_selector ) use ( $selector ) {
					return str_contains( $selector, $pseudo_selector );
				}
			)
		);

		$pseudo_selector = isset( $pseudo_matches[0] ) ? $pseudo_matches[0] : null;

		/*
		 * If the current selector is a pseudo selector that's defined in the allow list for the current
		 * element then compute the style properties for it.
		 * Otherwise just compute the styles for the default selector as normal.
		 */
		if ( $pseudo_selector && isset( $node[ $pseudo_selector ] ) &&
			isset( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $current_element ] )
			&& in_array( $pseudo_selector, static::VALID_ELEMENT_PSEUDO_SELECTORS[ $current_element ], true )
		) {
			$declarations = static::compute_style_properties( $node[ $pseudo_selector ], $settings, null, $this->theme_json, $selector, $use_root_padding );
		} else {
			$declarations = static::compute_style_properties( $node, $settings, null, $this->theme_json, $selector, $use_root_padding );
		}

		$block_rules = '';

		/*
		 * 1. Separate the declarations that use the general selector
		 * from the ones using the duotone selector.
		 */
		$declarations_duotone = array();
		foreach ( $declarations as $index => $declaration ) {
			if ( 'filter' === $declaration['name'] ) {
				unset( $declarations[ $index ] );
				$declarations_duotone[] = $declaration;
			}
		}

		// Update declarations if there are separators with only background color defined.
		if ( '.wp-block-separator' === $selector ) {
			$declarations = static::update_separator_declarations( $declarations );
		}

		// 2. Generate and append the rules that use the general selector.
		$block_rules .= static::to_ruleset( $selector, $declarations );

		// 3. Generate and append the rules that use the duotone selector.
		if ( isset( $block_metadata['duotone'] ) && ! empty( $declarations_duotone ) ) {
			$block_rules .= static::to_ruleset( $block_metadata['duotone'], $declarations_duotone );
		}

		// 4. Generate Layout block gap styles.
		if (
			static::ROOT_BLOCK_SELECTOR !== $selector &&
			! empty( $block_metadata['name'] )
		) {
			$block_rules .= $this->get_layout_styles( $block_metadata );
		}

		// 5. Generate and append the feature level rulesets.
		foreach ( $feature_declarations as $feature_selector => $individual_feature_declarations ) {
			$block_rules .= static::to_ruleset( $feature_selector, $individual_feature_declarations );
		}

		// 6. Generate and append the style variation rulesets.
		foreach ( $style_variation_declarations as $style_variation_selector => $individual_style_variation_declarations ) {
			$block_rules .= static::to_ruleset( $style_variation_selector, $individual_style_variation_declarations );
		}

		return $block_rules;
	}

	/**
	 * Outputs the CSS for layout rules on the root.
	 *
	 * @since 6.1.0
	 *
	 * @param string $selector The root node selector.
	 * @param array  $block_metadata The metadata for the root block.
	 * @return string The additional root rules CSS.
	 */
	public function get_root_layout_rules( $selector, $block_metadata ) {
		$css              = '';
		$settings         = isset( $this->theme_json['settings'] ) ? $this->theme_json['settings'] : array();
		$use_root_padding = isset( $this->theme_json['settings']['useRootPaddingAwareAlignments'] ) && true === $this->theme_json['settings']['useRootPaddingAwareAlignments'];

		/*
		* Reset default browser margin on the root body element.
		* This is set on the root selector **before** generating the ruleset
		* from the `theme.json`. This is to ensure that if the `theme.json` declares
		* `margin` in its `spacing` declaration for the `body` element then these
		* user-generated values take precedence in the CSS cascade.
		* @link https://github.com/WordPress/gutenberg/issues/36147.
		*/
		$css .= 'body { margin: 0;';

		/*
		* If there are content and wide widths in theme.json, output them
		* as custom properties on the body element so all blocks can use them.
		*/
		if ( isset( $settings['layout']['contentSize'] ) || isset( $settings['layout']['wideSize'] ) ) {
			$content_size = isset( $settings['layout']['contentSize'] ) ? $settings['layout']['contentSize'] : $settings['layout']['wideSize'];
			$content_size = static::is_safe_css_declaration( 'max-width', $content_size ) ? $content_size : 'initial';
			$wide_size    = isset( $settings['layout']['wideSize'] ) ? $settings['layout']['wideSize'] : $settings['layout']['contentSize'];
			$wide_size    = static::is_safe_css_declaration( 'max-width', $wide_size ) ? $wide_size : 'initial';
			$css         .= '--wp--style--global--content-size: ' . $content_size . ';';
			$css         .= '--wp--style--global--wide-size: ' . $wide_size . ';';
		}

		$css .= ' }';

		if ( $use_root_padding ) {
			// Top and bottom padding are applied to the outer block container.
			$css .= '.wp-site-blocks { padding-top: var(--wp--style--root--padding-top); padding-bottom: var(--wp--style--root--padding-bottom); }';
			// Right and left padding are applied to the first container with `.has-global-padding` class.
			$css .= '.has-global-padding { padding-right: var(--wp--style--root--padding-right); padding-left: var(--wp--style--root--padding-left); }';
			// Nested containers with `.has-global-padding` class do not get padding.
			$css .= '.has-global-padding :where(.has-global-padding:not(.wp-block-block)) { padding-right: 0; padding-left: 0; }';
			// Alignfull children of the container with left and right padding have negative margins so they can still be full width.
			$css .= '.has-global-padding > .alignfull { margin-right: calc(var(--wp--style--root--padding-right) * -1); margin-left: calc(var(--wp--style--root--padding-left) * -1); }';
			// The above rule is negated for alignfull children of nested containers.
			$css .= '.has-global-padding :where(.has-global-padding:not(.wp-block-block)) > .alignfull { margin-right: 0; margin-left: 0; }';
			// Some of the children of alignfull blocks without content width should also get padding: text blocks and non-alignfull container blocks.
			$css .= '.has-global-padding > .alignfull:where(:not(.has-global-padding):not(.is-layout-flex):not(.is-layout-grid)) > :where([class*="wp-block-"]:not(.alignfull):not([class*="__"]),p,h1,h2,h3,h4,h5,h6,ul,ol) { padding-right: var(--wp--style--root--padding-right); padding-left: var(--wp--style--root--padding-left); }';
			// The above rule also has to be negated for blocks inside nested `.has-global-padding` blocks.
			$css .= '.has-global-padding :where(.has-global-padding) > .alignfull:where(:not(.has-global-padding)) > :where([class*="wp-block-"]:not(.alignfull):not([class*="__"]),p,h1,h2,h3,h4,h5,h6,ul,ol) { padding-right: 0; padding-left: 0; }';
		}

		$css .= '.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }';
		$css .= '.wp-site-blocks > .alignright { float: right; margin-left: 2em; }';
		$css .= '.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

		$block_gap_value       = isset( $this->theme_json['styles']['spacing']['blockGap'] ) ? $this->theme_json['styles']['spacing']['blockGap'] : '0.5em';
		$has_block_gap_support = isset( $this->theme_json['settings']['spacing']['blockGap'] );
		if ( $has_block_gap_support ) {
			$block_gap_value = static::get_property_value( $this->theme_json, array( 'styles', 'spacing', 'blockGap' ) );
			$css            .= ":where(.wp-site-blocks) > * { margin-block-start: $block_gap_value; margin-block-end: 0; }";
			$css            .= ':where(.wp-site-blocks) > :first-child:first-child { margin-block-start: 0; }';
			$css            .= ':where(.wp-site-blocks) > :last-child:last-child { margin-block-end: 0; }';

			// For backwards compatibility, ensure the legacy block gap CSS variable is still available.
			$css .= "$selector { --wp--style--block-gap: $block_gap_value; }";
		}
		$css .= $this->get_layout_styles( $block_metadata );

		return $css;
	}

	/**
	 * For metadata values that can either be booleans or paths to booleans, gets the value.
	 *
	 *     $data = array(
	 *       'color' => array(
	 *         'defaultPalette' => true
	 *       )
	 *     );
	 *
	 *     static::get_metadata_boolean( $data, false );
	 *     // => false
	 *
	 *     static::get_metadata_boolean( $data, array( 'color', 'defaultPalette' ) );
	 *     // => true
	 *
	 * @since 6.0.0
	 *
	 * @param array      $data          The data to inspect.
	 * @param bool|array $path          Boolean or path to a boolean.
	 * @param bool       $default_value Default value if the referenced path is missing.
	 *                                  Default false.
	 * @return bool Value of boolean metadata.
	 */
	protected static function get_metadata_boolean( $data, $path, $default_value = false ) {
		if ( is_bool( $path ) ) {
			return $path;
		}

		if ( is_array( $path ) ) {
			$value = _wp_array_get( $data, $path );
			if ( null !== $value ) {
				return $value;
			}
		}

		return $default_value;
	}

	/**
	 * Merges new incoming data.
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
			// Replace the spacing.units.
			$path   = $node['path'];
			$path[] = 'spacing';
			$path[] = 'units';

			$content = _wp_array_get( $incoming_data, $path, null );
			if ( isset( $content ) ) {
				_wp_array_set( $this->theme_json, $path, $content );
			}

			// Replace the presets.
			foreach ( static::PRESETS_METADATA as $preset ) {
				$override_preset = ! static::get_metadata_boolean( $this->theme_json['settings'], $preset['prevent_override'], true );

				foreach ( static::VALID_ORIGINS as $origin ) {
					$base_path = $node['path'];
					foreach ( $preset['path'] as $leaf ) {
						$base_path[] = $leaf;
					}

					$path   = $base_path;
					$path[] = $origin;

					$content = _wp_array_get( $incoming_data, $path, null );
					if ( ! isset( $content ) ) {
						continue;
					}

					if ( 'theme' === $origin && $preset['use_default_names'] ) {
						foreach ( $content as $key => $item ) {
							if ( ! isset( $item['name'] ) ) {
								$name = static::get_name_from_defaults( $item['slug'], $base_path );
								if ( null !== $name ) {
									$content[ $key ]['name'] = $name;
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
						$slugs_node = static::get_default_slugs( $this->theme_json, $node['path'] );
						$slugs      = array_merge_recursive( $slugs_global, $slugs_node );

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
	 * Determines whether a presets should be overridden or not.
	 *
	 * @since 5.9.0
	 * @deprecated 6.0.0 Use {@see 'get_metadata_boolean'} instead.
	 *
	 * @param array      $theme_json The theme.json like structure to inspect.
	 * @param array      $path       Path to inspect.
	 * @param bool|array $override   Data to compute whether to override the preset.
	 * @return bool
	 */
	protected static function should_override_preset( $theme_json, $path, $override ) {
		_deprecated_function( __METHOD__, '6.0.0', 'get_metadata_boolean' );

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
	 *     array(
	 *       'color' => array(
	 *         'palette'   => array( 'slug-1', 'slug-2' ),
	 *         'gradients' => array( 'slug-3', 'slug-4' ),
	 *       ),
	 *     )
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
			$path = $node_path;
			foreach ( $metadata['path'] as $leaf ) {
				$path[] = $leaf;
			}
			$path[] = 'default';

			$preset = _wp_array_get( $data, $path, null );
			if ( ! isset( $preset ) ) {
				continue;
			}

			$slugs_for_preset = array();
			foreach ( $preset as $item ) {
				if ( isset( $item['slug'] ) ) {
					$slugs_for_preset[] = $item['slug'];
				}
			}

			_wp_array_set( $slugs, $metadata['path'], $slugs_for_preset );
		}

		return $slugs;
	}

	/**
	 * Gets a `default`'s preset name by a provided slug.
	 *
	 * @since 5.9.0
	 *
	 * @param string $slug The slug we want to find a match from default presets.
	 * @param array  $base_path The path to inspect. It's 'settings' by default.
	 * @return string|null
	 */
	protected function get_name_from_defaults( $slug, $base_path ) {
		$path            = $base_path;
		$path[]          = 'default';
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
	 * @since 6.3.2 Preserves global styles block variations when securing styles.
	 *
	 * @param array $theme_json Structure to sanitize.
	 * @return array Sanitized structure.
	 */
	public static function remove_insecure_properties( $theme_json ) {
		$sanitized = array();

		$theme_json = WP_Theme_JSON_Schema::migrate( $theme_json );

		$valid_block_names   = array_keys( static::get_blocks_metadata() );
		$valid_element_names = array_keys( static::ELEMENTS );
		$valid_variations    = array();
		foreach ( self::get_blocks_metadata() as $block_name => $block_meta ) {
			if ( ! isset( $block_meta['styleVariations'] ) ) {
				continue;
			}
			$valid_variations[ $block_name ] = array_keys( $block_meta['styleVariations'] );
		}

		$theme_json = static::sanitize( $theme_json, $valid_block_names, $valid_element_names, $valid_variations );

		$blocks_metadata = static::get_blocks_metadata();
		$style_nodes     = static::get_style_nodes( $theme_json, $blocks_metadata );

		foreach ( $style_nodes as $metadata ) {
			$input = _wp_array_get( $theme_json, $metadata['path'], array() );
			if ( empty( $input ) ) {
				continue;
			}

			// The global styles custom CSS is not sanitized, but can only be edited by users with 'edit_css' capability.
			if ( isset( $input['css'] ) && current_user_can( 'edit_css' ) ) {
				$output = $input;
			} else {
				$output = static::remove_insecure_styles( $input );
			}

			/*
			 * Get a reference to element name from path.
			 * $metadata['path'] = array( 'styles', 'elements', 'link' );
			 */
			$current_element = $metadata['path'][ count( $metadata['path'] ) - 1 ];

			/*
			 * $output is stripped of pseudo selectors. Re-add and process them
			 * or insecure styles here.
			 */
			if ( isset( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $current_element ] ) ) {
				foreach ( static::VALID_ELEMENT_PSEUDO_SELECTORS[ $current_element ] as $pseudo_selector ) {
					if ( isset( $input[ $pseudo_selector ] ) ) {
						$output[ $pseudo_selector ] = static::remove_insecure_styles( $input[ $pseudo_selector ] );
					}
				}
			}

			if ( ! empty( $output ) ) {
				_wp_array_set( $sanitized, $metadata['path'], $output );
			}

			if ( isset( $metadata['variations'] ) ) {
				foreach ( $metadata['variations'] as $variation ) {
					$variation_input = _wp_array_get( $theme_json, $variation['path'], array() );
					if ( empty( $variation_input ) ) {
						continue;
					}

					$variation_output = static::remove_insecure_styles( $variation_input );
					if ( ! empty( $variation_output ) ) {
						_wp_array_set( $sanitized, $variation['path'], $variation_output );
					}
				}
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
				$path_with_origin   = $preset_metadata['path'];
				$path_with_origin[] = $origin;
				$presets            = _wp_array_get( $input, $path_with_origin, null );
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

		// Ensure indirect properties not included in any `PRESETS_METADATA` value are allowed.
		static::remove_indirect_properties( $input, $output );

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

				/*
				 * Check the value isn't an array before adding so as to not
				 * double up shorthand and longhand styles.
				 */
				$value = _wp_array_get( $input, $path, array() );
				if ( ! is_array( $value ) ) {
					_wp_array_set( $output, $path, $value );
				}
			}
		}

		// Ensure indirect properties not handled by `compute_style_properties` are allowed.
		static::remove_indirect_properties( $input, $output );

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
	 * Removes indirect properties from the given input node and
	 * sets in the given output node.
	 *
	 * @since 6.2.0
	 *
	 * @param array $input  Node to process.
	 * @param array $output The processed node. Passed by reference.
	 */
	private static function remove_indirect_properties( $input, &$output ) {
		foreach ( static::INDIRECT_PROPERTIES_METADATA as $property => $paths ) {
			foreach ( $paths as $path ) {
				$value = _wp_array_get( $input, $path );
				if (
					is_string( $value ) &&
					static::is_safe_css_declaration( $property, $value )
				) {
					_wp_array_set( $output, $path, $value );
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

	/**
	 * Returns the current theme's wanted patterns(slugs) to be
	 * registered from Pattern Directory.
	 *
	 * @since 6.0.0
	 *
	 * @return string[]
	 */
	public function get_patterns() {
		if ( isset( $this->theme_json['patterns'] ) && is_array( $this->theme_json['patterns'] ) ) {
			return $this->theme_json['patterns'];
		}
		return array();
	}

	/**
	 * Returns a valid theme.json as provided by a theme.
	 *
	 * Unlike get_raw_data() this returns the presets flattened, as provided by a theme.
	 * This also uses appearanceTools instead of their opt-ins if all of them are true.
	 *
	 * @since 6.0.0
	 *
	 * @return array
	 */
	public function get_data() {
		$output = $this->theme_json;
		$nodes  = static::get_setting_nodes( $output );

		/**
		 * Flatten the theme & custom origins into a single one.
		 *
		 * For example, the following:
		 *
		 * {
		 *   "settings": {
		 *     "color": {
		 *       "palette": {
		 *         "theme": [ {} ],
		 *         "custom": [ {} ]
		 *       }
		 *     }
		 *   }
		 * }
		 *
		 * will be converted to:
		 *
		 * {
		 *   "settings": {
		 *     "color": {
		 *       "palette": [ {} ]
		 *     }
		 *   }
		 * }
		 */
		foreach ( $nodes as $node ) {
			foreach ( static::PRESETS_METADATA as $preset_metadata ) {
				$path = $node['path'];
				foreach ( $preset_metadata['path'] as $preset_metadata_path ) {
					$path[] = $preset_metadata_path;
				}
				$preset = _wp_array_get( $output, $path, null );
				if ( null === $preset ) {
					continue;
				}

				$items = array();
				if ( isset( $preset['theme'] ) ) {
					foreach ( $preset['theme'] as $item ) {
						$slug = $item['slug'];
						unset( $item['slug'] );
						$items[ $slug ] = $item;
					}
				}
				if ( isset( $preset['custom'] ) ) {
					foreach ( $preset['custom'] as $item ) {
						$slug = $item['slug'];
						unset( $item['slug'] );
						$items[ $slug ] = $item;
					}
				}
				$flattened_preset = array();
				foreach ( $items as $slug => $value ) {
					$flattened_preset[] = array_merge( array( 'slug' => (string) $slug ), $value );
				}
				_wp_array_set( $output, $path, $flattened_preset );
			}
		}

		/*
		 * If all of the static::APPEARANCE_TOOLS_OPT_INS are true,
		 * this code unsets them and sets 'appearanceTools' instead.
		 */
		foreach ( $nodes as $node ) {
			$all_opt_ins_are_set = true;
			foreach ( static::APPEARANCE_TOOLS_OPT_INS as $opt_in_path ) {
				$full_path = $node['path'];
				foreach ( $opt_in_path as $opt_in_path_item ) {
					$full_path[] = $opt_in_path_item;
				}
				/*
				 * Use "unset prop" as a marker instead of "null" because
				 * "null" can be a valid value for some props (e.g. blockGap).
				 */
				$opt_in_value = _wp_array_get( $output, $full_path, 'unset prop' );
				if ( 'unset prop' === $opt_in_value ) {
					$all_opt_ins_are_set = false;
					break;
				}
			}

			if ( $all_opt_ins_are_set ) {
				$node_path_with_appearance_tools   = $node['path'];
				$node_path_with_appearance_tools[] = 'appearanceTools';
				_wp_array_set( $output, $node_path_with_appearance_tools, true );
				foreach ( static::APPEARANCE_TOOLS_OPT_INS as $opt_in_path ) {
					$full_path = $node['path'];
					foreach ( $opt_in_path as $opt_in_path_item ) {
						$full_path[] = $opt_in_path_item;
					}
					/*
					 * Use "unset prop" as a marker instead of "null" because
					 * "null" can be a valid value for some props (e.g. blockGap).
					 */
					$opt_in_value = _wp_array_get( $output, $full_path, 'unset prop' );
					if ( true !== $opt_in_value ) {
						continue;
					}

					/*
					 * The following could be improved to be path independent.
					 * At the moment it relies on a couple of assumptions:
					 *
					 * - all opt-ins having a path of size 2.
					 * - there's two sources of settings: the top-level and the block-level.
					 */
					if (
						( 1 === count( $node['path'] ) ) &&
						( 'settings' === $node['path'][0] )
					) {
						// Top-level settings.
						unset( $output['settings'][ $opt_in_path[0] ][ $opt_in_path[1] ] );
						if ( empty( $output['settings'][ $opt_in_path[0] ] ) ) {
							unset( $output['settings'][ $opt_in_path[0] ] );
						}
					} elseif (
						( 3 === count( $node['path'] ) ) &&
						( 'settings' === $node['path'][0] ) &&
						( 'blocks' === $node['path'][1] )
					) {
						// Block-level settings.
						$block_name = $node['path'][2];
						unset( $output['settings']['blocks'][ $block_name ][ $opt_in_path[0] ][ $opt_in_path[1] ] );
						if ( empty( $output['settings']['blocks'][ $block_name ][ $opt_in_path[0] ] ) ) {
							unset( $output['settings']['blocks'][ $block_name ][ $opt_in_path[0] ] );
						}
					}
				}
			}
		}

		wp_recursive_ksort( $output );

		return $output;
	}

	/**
	 * Sets the spacingSizes array based on the spacingScale values from theme.json.
	 *
	 * @since 6.1.0
	 *
	 * @return null|void
	 */
	public function set_spacing_sizes() {
		$spacing_scale = isset( $this->theme_json['settings']['spacing']['spacingScale'] )
			? $this->theme_json['settings']['spacing']['spacingScale']
			: array();

		if ( ! isset( $spacing_scale['steps'] )
			|| ! is_numeric( $spacing_scale['steps'] )
			|| ! isset( $spacing_scale['mediumStep'] )
			|| ! isset( $spacing_scale['unit'] )
			|| ! isset( $spacing_scale['operator'] )
			|| ! isset( $spacing_scale['increment'] )
			|| ! isset( $spacing_scale['steps'] )
			|| ! is_numeric( $spacing_scale['increment'] )
			|| ! is_numeric( $spacing_scale['mediumStep'] )
			|| ( '+' !== $spacing_scale['operator'] && '*' !== $spacing_scale['operator'] ) ) {
			if ( ! empty( $spacing_scale ) ) {
				trigger_error(
					sprintf(
						/* translators: 1: theme.json, 2: settings.spacing.spacingScale */
						__( 'Some of the %1$s %2$s values are invalid' ),
						'theme.json',
						'settings.spacing.spacingScale'
					),
					E_USER_NOTICE
				);
			}
			return null;
		}

		// If theme authors want to prevent the generation of the core spacing scale they can set their theme.json spacingScale.steps to 0.
		if ( 0 === $spacing_scale['steps'] ) {
			return null;
		}

		$unit            = '%' === $spacing_scale['unit'] ? '%' : sanitize_title( $spacing_scale['unit'] );
		$current_step    = $spacing_scale['mediumStep'];
		$steps_mid_point = round( $spacing_scale['steps'] / 2, 0 );
		$x_small_count   = null;
		$below_sizes     = array();
		$slug            = 40;
		$remainder       = 0;

		for ( $below_midpoint_count = $steps_mid_point - 1; $spacing_scale['steps'] > 1 && $slug > 0 && $below_midpoint_count > 0; $below_midpoint_count-- ) {
			if ( '+' === $spacing_scale['operator'] ) {
				$current_step -= $spacing_scale['increment'];
			} elseif ( $spacing_scale['increment'] > 1 ) {
				$current_step /= $spacing_scale['increment'];
			} else {
				$current_step *= $spacing_scale['increment'];
			}

			if ( $current_step <= 0 ) {
				$remainder = $below_midpoint_count;
				break;
			}

			$below_sizes[] = array(
				/* translators: %s: Digit to indicate multiple of sizing, eg. 2X-Small. */
				'name' => $below_midpoint_count === $steps_mid_point - 1 ? __( 'Small' ) : sprintf( __( '%sX-Small' ), (string) $x_small_count ),
				'slug' => (string) $slug,
				'size' => round( $current_step, 2 ) . $unit,
			);

			if ( $below_midpoint_count === $steps_mid_point - 2 ) {
				$x_small_count = 2;
			}

			if ( $below_midpoint_count < $steps_mid_point - 2 ) {
				++$x_small_count;
			}

			$slug -= 10;
		}

		$below_sizes = array_reverse( $below_sizes );

		$below_sizes[] = array(
			'name' => __( 'Medium' ),
			'slug' => '50',
			'size' => $spacing_scale['mediumStep'] . $unit,
		);

		$current_step  = $spacing_scale['mediumStep'];
		$x_large_count = null;
		$above_sizes   = array();
		$slug          = 60;
		$steps_above   = ( $spacing_scale['steps'] - $steps_mid_point ) + $remainder;

		for ( $above_midpoint_count = 0; $above_midpoint_count < $steps_above; $above_midpoint_count++ ) {
			$current_step = '+' === $spacing_scale['operator']
				? $current_step + $spacing_scale['increment']
				: ( $spacing_scale['increment'] >= 1 ? $current_step * $spacing_scale['increment'] : $current_step / $spacing_scale['increment'] );

			$above_sizes[] = array(
				/* translators: %s: Digit to indicate multiple of sizing, eg. 2X-Large. */
				'name' => 0 === $above_midpoint_count ? __( 'Large' ) : sprintf( __( '%sX-Large' ), (string) $x_large_count ),
				'slug' => (string) $slug,
				'size' => round( $current_step, 2 ) . $unit,
			);

			if ( 1 === $above_midpoint_count ) {
				$x_large_count = 2;
			}

			if ( $above_midpoint_count > 1 ) {
				++$x_large_count;
			}

			$slug += 10;
		}

		$spacing_sizes = $below_sizes;
		foreach ( $above_sizes as $above_sizes_item ) {
			$spacing_sizes[] = $above_sizes_item;
		}

		// If there are 7 or fewer steps in the scale revert to numbers for labels instead of t-shirt sizes.
		if ( $spacing_scale['steps'] <= 7 ) {
			for ( $spacing_sizes_count = 0; $spacing_sizes_count < count( $spacing_sizes ); $spacing_sizes_count++ ) {
				$spacing_sizes[ $spacing_sizes_count ]['name'] = (string) ( $spacing_sizes_count + 1 );
			}
		}

		_wp_array_set( $this->theme_json, array( 'settings', 'spacing', 'spacingSizes', 'default' ), $spacing_sizes );
	}

	/**
	 * This is used to convert the internal representation of variables to the CSS representation.
	 * For example, `var:preset|color|vivid-green-cyan` becomes `var(--wp--preset--color--vivid-green-cyan)`.
	 *
	 * @since 6.3.0
	 * @param string $value The variable such as var:preset|color|vivid-green-cyan to convert.
	 * @return string The converted variable.
	 */
	private static function convert_custom_properties( $value ) {
		$prefix     = 'var:';
		$prefix_len = strlen( $prefix );
		$token_in   = '|';
		$token_out  = '--';
		if ( str_starts_with( $value, $prefix ) ) {
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
	 * Given a tree, converts the internal representation of variables to the CSS representation.
	 * It is recursive and modifies the input in-place.
	 *
	 * @since 6.3.0
	 * @param array $tree   Input to process.
	 * @return array The modified $tree.
	 */
	private static function resolve_custom_css_format( $tree ) {
		$prefix = 'var:';

		foreach ( $tree as $key => $data ) {
			if ( is_string( $data ) && str_starts_with( $data, $prefix ) ) {
				$tree[ $key ] = self::convert_custom_properties( $data );
			} elseif ( is_array( $data ) ) {
				$tree[ $key ] = self::resolve_custom_css_format( $data );
			}
		}

		return $tree;
	}

	/**
	 * Returns the selectors metadata for a block.
	 *
	 * @since 6.3.0
	 *
	 * @param object $block_type    The block type.
	 * @param string $root_selector The block's root selector.
	 *
	 * @return array The custom selectors set by the block.
	 */
	protected static function get_block_selectors( $block_type, $root_selector ) {
		if ( ! empty( $block_type->selectors ) ) {
			return $block_type->selectors;
		}

		$selectors = array( 'root' => $root_selector );
		foreach ( static::BLOCK_SUPPORT_FEATURE_LEVEL_SELECTORS as $key => $feature ) {
			$feature_selector = wp_get_block_css_selector( $block_type, $key );
			if ( null !== $feature_selector ) {
				$selectors[ $feature ] = array( 'root' => $feature_selector );
			}
		}

		return $selectors;
	}

	/**
	 * Generates all the element selectors for a block.
	 *
	 * @since 6.3.0
	 *
	 * @param string $root_selector The block's root CSS selector.
	 * @return array The block's element selectors.
	 */
	protected static function get_block_element_selectors( $root_selector ) {
		/*
		 * Assign defaults, then override those that the block sets by itself.
		 * If the block selector is compounded, will append the element to each
		 * individual block selector.
		 */
		$block_selectors   = explode( ',', $root_selector );
		$element_selectors = array();
		foreach ( static::ELEMENTS as $el_name => $el_selector ) {
			$element_selector = array();
			foreach ( $block_selectors as $selector ) {
				if ( $selector === $el_selector ) {
					$element_selector = array( $el_selector );
					break;
				}
				$element_selector[] = static::prepend_to_selector( $el_selector, $selector . ' ' );
			}
			$element_selectors[ $el_name ] = implode( ',', $element_selector );
		}

		return $element_selectors;
	}

	/**
	 * Generates style declarations for a node's features e.g., color, border,
	 * typography etc. that have custom selectors in their related block's
	 * metadata.
	 *
	 * @since 6.3.0
	 *
	 * @param object $metadata The related block metadata containing selectors.
	 * @param object $node     A merged theme.json node for block or variation.
	 *
	 * @return array The style declarations for the node's features with custom
	 * selectors.
	 */
	protected function get_feature_declarations_for_node( $metadata, &$node ) {
		$declarations = array();

		if ( ! isset( $metadata['selectors'] ) ) {
			return $declarations;
		}

		$settings = isset( $this->theme_json['settings'] )
			? $this->theme_json['settings']
			: array();

		foreach ( $metadata['selectors'] as $feature => $feature_selectors ) {
			/*
			 * Skip if this is the block's root selector or the block doesn't
			 * have any styles for the feature.
			 */
			if ( 'root' === $feature || empty( $node[ $feature ] ) ) {
				continue;
			}

			if ( is_array( $feature_selectors ) ) {
				foreach ( $feature_selectors as $subfeature => $subfeature_selector ) {
					if ( 'root' === $subfeature || empty( $node[ $feature ][ $subfeature ] ) ) {
						continue;
					}

					/*
					 * Create temporary node containing only the subfeature data
					 * to leverage existing `compute_style_properties` function.
					 */
					$subfeature_node = array(
						$feature => array(
							$subfeature => $node[ $feature ][ $subfeature ],
						),
					);

					// Generate style declarations.
					$new_declarations = static::compute_style_properties( $subfeature_node, $settings, null, $this->theme_json );

					// Merge subfeature declarations into feature declarations.
					if ( isset( $declarations[ $subfeature_selector ] ) ) {
						foreach ( $new_declarations as $new_declaration ) {
							$declarations[ $subfeature_selector ][] = $new_declaration;
						}
					} else {
						$declarations[ $subfeature_selector ] = $new_declarations;
					}

					/*
					 * Remove the subfeature from the block's node now its
					 * styles will be included under its own selector not the
					 * block's.
					 */
					unset( $node[ $feature ][ $subfeature ] );
				}
			}

			/*
			 * Now subfeatures have been processed and removed we can process
			 * feature root selector or simple string selector.
			 */
			if (
				is_string( $feature_selectors ) ||
				( isset( $feature_selectors['root'] ) && $feature_selectors['root'] )
			) {
				$feature_selector = is_string( $feature_selectors ) ? $feature_selectors : $feature_selectors['root'];

				/*
				 * Create temporary node containing only the feature data
				 * to leverage existing `compute_style_properties` function.
				 */
				$feature_node = array( $feature => $node[ $feature ] );

				// Generate the style declarations.
				$new_declarations = static::compute_style_properties( $feature_node, $settings, null, $this->theme_json );

				/*
				 * Merge new declarations with any that already exist for
				 * the feature selector. This may occur when multiple block
				 * support features use the same custom selector.
				 */
				if ( isset( $declarations[ $feature_selector ] ) ) {
					foreach ( $new_declarations as $new_declaration ) {
						$declarations[ $feature_selector ][] = $new_declaration;
					}
				} else {
					$declarations[ $feature_selector ] = $new_declarations;
				}

				/*
				 * Remove the feature from the block's node now its styles
				 * will be included under its own selector not the block's.
				 */
				unset( $node[ $feature ] );
			}
		}

		return $declarations;
	}

	/**
	 * Replaces CSS variables with their values in place.
	 *
	 * @since 6.3.0
	 * @since 6.5.0 Check for empty style before processing its value.
	 *
	 * @param array $styles CSS declarations to convert.
	 * @param array $values key => value pairs to use for replacement.
	 * @return array
	 */
	private static function convert_variables_to_value( $styles, $values ) {
		foreach ( $styles as $key => $style ) {
			if ( empty( $style ) ) {
				continue;
			}

			if ( is_array( $style ) ) {
				$styles[ $key ] = self::convert_variables_to_value( $style, $values );
				continue;
			}

			if ( 0 <= strpos( $style, 'var(' ) ) {
				// find all the variables in the string in the form of var(--variable-name, fallback), with fallback in the second capture group.

				$has_matches = preg_match_all( '/var\(([^),]+)?,?\s?(\S+)?\)/', $style, $var_parts );

				if ( $has_matches ) {
					$resolved_style = $styles[ $key ];
					foreach ( $var_parts[1] as $index => $var_part ) {
						$key_in_values   = 'var(' . $var_part . ')';
						$rule_to_replace = $var_parts[0][ $index ]; // the css rule to replace e.g. var(--wp--preset--color--vivid-green-cyan).
						$fallback        = $var_parts[2][ $index ]; // the fallback value.
						$resolved_style  = str_replace(
							array(
								$rule_to_replace,
								$fallback,
							),
							array(
								isset( $values[ $key_in_values ] ) ? $values[ $key_in_values ] : $rule_to_replace,
								isset( $values[ $fallback ] ) ? $values[ $fallback ] : $fallback,
							),
							$resolved_style
						);
					}
					$styles[ $key ] = $resolved_style;
				}
			}
		}

		return $styles;
	}

	/**
	 * Resolves the values of CSS variables in the given styles.
	 *
	 * @since 6.3.0
	 * @param WP_Theme_JSON $theme_json The theme json resolver.
	 *
	 * @return WP_Theme_JSON The $theme_json with resolved variables.
	 */
	public static function resolve_variables( $theme_json ) {
		$settings    = $theme_json->get_settings();
		$styles      = $theme_json->get_raw_data()['styles'];
		$preset_vars = static::compute_preset_vars( $settings, static::VALID_ORIGINS );
		$theme_vars  = static::compute_theme_vars( $settings );
		$vars        = array_reduce(
			array_merge( $preset_vars, $theme_vars ),
			function ( $carry, $item ) {
				$name                    = $item['name'];
				$carry[ "var({$name})" ] = $item['value'];
				return $carry;
			},
			array()
		);

		$theme_json->theme_json['styles'] = self::convert_variables_to_value( $styles, $vars );
		return $theme_json;
	}

	/**
	 * Generates a selector for a block style variation.
	 *
	 * @since 6.5.0
	 *
	 * @param string $variation_name Name of the block style variation.
	 * @param string $block_selector CSS selector for the block.
	 * @return string Block selector with block style variation selector added to it.
	 */
	protected static function get_block_style_variation_selector( $variation_name, $block_selector ) {
		$variation_class = ".is-style-$variation_name";

		if ( ! $block_selector ) {
			return $variation_class;
		}

		$limit          = 1;
		$selector_parts = explode( ',', $block_selector );
		$result         = array();

		foreach ( $selector_parts as $part ) {
			$result[] = preg_replace_callback(
				'/((?::\([^)]+\))?\s*)([^\s:]+)/',
				function ( $matches ) use ( $variation_class ) {
					return $matches[1] . $matches[2] . $variation_class;
				},
				$part,
				$limit
			);
		}

		return implode( ',', $result );
	}
}
