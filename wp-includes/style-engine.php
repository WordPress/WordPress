<?php
/**
 * Style engine: Public functions
 *
 * This file contains a variety of public functions developers can use to interact with
 * the Style Engine API.
 *
 * @package WordPress
 * @subpackage StyleEngine
 * @since 6.1.0
 */

/**
 * Global public interface method to generate styles from a single style object,
 * e.g. the value of a block's attributes.style object or the top level styles in theme.json.
 *
 * Example usage:
 *
 *     $styles = wp_style_engine_get_styles(
 *         array(
 *             'color' => array( 'text' => '#cccccc' ),
 *         )
 *     );
 *
 * Returns:
 *
 *     array(
 *         'css'          => 'color: #cccccc',
 *         'declarations' => array( 'color' => '#cccccc' ),
 *         'classnames'   => 'has-color',
 *     )
 *
 * @since 6.1.0
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/theme-json-reference/theme-json-living/#styles
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
 *
 * @param array $block_styles The style object.
 * @param array $options {
 *     Optional. An array of options. Default empty array.
 *
 *     @type string|null $context                    An identifier describing the origin of the style object,
 *                                                   e.g. 'block-supports' or 'global-styles'. Default null.
 *                                                   When set, the style engine will attempt to store the CSS rules,
 *                                                   where a selector is also passed.
 *     @type bool        $convert_vars_to_classnames Whether to skip converting incoming CSS var patterns,
 *                                                   e.g. `var:preset|<PRESET_TYPE>|<PRESET_SLUG>`,
 *                                                   to `var( --wp--preset--* )` values. Default false.
 *     @type string      $selector                   Optional. When a selector is passed,
 *                                                   the value of `$css` in the return value will comprise
 *                                                   a full CSS rule `$selector { ...$css_declarations }`,
 *                                                   otherwise, the value will be a concatenated string
 *                                                   of CSS declarations.
 * }
 * @return array {
 *     @type string   $css          A CSS ruleset or declarations block
 *                                  formatted to be placed in an HTML `style` attribute or tag.
 *     @type string[] $declarations An associative array of CSS definitions,
 *                                  e.g. `array( "$property" => "$value", "$property" => "$value" )`.
 *     @type string   $classnames   Classnames separated by a space.
 * }
 */
function wp_style_engine_get_styles( $block_styles, $options = array() ) {
	$options = wp_parse_args(
		$options,
		array(
			'selector'                   => null,
			'context'                    => null,
			'convert_vars_to_classnames' => false,
		)
	);

	$parsed_styles = WP_Style_Engine::parse_block_styles( $block_styles, $options );

	// Output.
	$styles_output = array();

	if ( ! empty( $parsed_styles['declarations'] ) ) {
		$styles_output['css']          = WP_Style_Engine::compile_css( $parsed_styles['declarations'], $options['selector'] );
		$styles_output['declarations'] = $parsed_styles['declarations'];
		if ( ! empty( $options['context'] ) ) {
			WP_Style_Engine::store_css_rule( $options['context'], $options['selector'], $parsed_styles['declarations'] );
		}
	}

	if ( ! empty( $parsed_styles['classnames'] ) ) {
		$styles_output['classnames'] = implode( ' ', array_unique( $parsed_styles['classnames'] ) );
	}

	return array_filter( $styles_output );
}

/**
 * Returns compiled CSS from a collection of selectors and declarations.
 * Useful for returning a compiled stylesheet from any collection of CSS selector + declarations.
 *
 * Example usage:
 *
 *     $css_rules = array(
 *         array(
 *             'selector'     => '.elephant-are-cool',
 *             'declarations' => array(
 *                 'color' => 'gray',
 *                 'width' => '3em',
 *             ),
 *         ),
 *     );
 *
 *     $css = wp_style_engine_get_stylesheet_from_css_rules( $css_rules );
 *
 * Returns:
 *
 *     .elephant-are-cool{color:gray;width:3em}
 *
 * @since 6.1.0
 *
 * @param array $css_rules {
 *     Required. A collection of CSS rules.
 *
 *     @type array ...$0 {
 *         @type string   $selector     A CSS selector.
 *         @type string[] $declarations An associative array of CSS definitions,
 *                                      e.g. `array( "$property" => "$value", "$property" => "$value" )`.
 *     }
 * }
 * @param array $options {
 *     Optional. An array of options. Default empty array.
 *
 *     @type string|null $context  An identifier describing the origin of the style object,
 *                                 e.g. 'block-supports' or 'global-styles'. Default 'block-supports'.
 *                                 When set, the style engine will attempt to store the CSS rules.
 *     @type bool        $optimize Whether to optimize the CSS output, e.g. combine rules.
 *                                 Default false.
 *     @type bool        $prettify Whether to add new lines and indents to output.
 *                                 Defaults to whether the `SCRIPT_DEBUG` constant is defined.
 * }
 * @return string A string of compiled CSS declarations, or empty string.
 */
function wp_style_engine_get_stylesheet_from_css_rules( $css_rules, $options = array() ) {
	if ( empty( $css_rules ) ) {
		return '';
	}

	$options = wp_parse_args(
		$options,
		array(
			'context' => null,
		)
	);

	$css_rule_objects = array();
	foreach ( $css_rules as $css_rule ) {
		if ( empty( $css_rule['selector'] ) || empty( $css_rule['declarations'] ) || ! is_array( $css_rule['declarations'] ) ) {
			continue;
		}

		if ( ! empty( $options['context'] ) ) {
			WP_Style_Engine::store_css_rule( $options['context'], $css_rule['selector'], $css_rule['declarations'] );
		}

		$css_rule_objects[] = new WP_Style_Engine_CSS_Rule( $css_rule['selector'], $css_rule['declarations'] );
	}

	if ( empty( $css_rule_objects ) ) {
		return '';
	}

	return WP_Style_Engine::compile_stylesheet_from_css_rules( $css_rule_objects, $options );
}

/**
 * Returns compiled CSS from a store, if found.
 *
 * @since 6.1.0
 *
 * @param string $context A valid context name, corresponding to an existing store key.
 * @param array  $options {
 *     Optional. An array of options. Default empty array.
 *
 *     @type bool $optimize Whether to optimize the CSS output, e.g. combine rules.
 *                          Default false.
 *     @type bool $prettify Whether to add new lines and indents to output.
 *                          Defaults to whether the `SCRIPT_DEBUG` constant is defined.
 * }
 * @return string A compiled CSS string.
 */
function wp_style_engine_get_stylesheet_from_context( $context, $options = array() ) {
	return WP_Style_Engine::compile_stylesheet_from_css_rules( WP_Style_Engine::get_store( $context )->get_all_rules(), $options );
}
