<?php
/**
 * Handle the generic WPCode shortcode.
 *
 * @package WPCode
 */

add_shortcode( 'wpcode', 'wpcode_shortcode_handler' );
add_action( 'wpcode_shortcode_before_output', 'wpcode_pass_shortcode_attributes_to_snippet', 10, 4 );

add_filter( 'wpcode_shortcode_attribute_value', 'wp_kses_post' );

/**
 * Generic handler for the shortcode.
 *
 * @param array  $args The shortcode attributes.
 * @param string $content The shortcode content.
 * @param string $tag The shortcode tag.
 *
 * @return string
 */
function wpcode_shortcode_handler( $args, $content, $tag ) {
	$atts = wp_parse_args(
		$args,
		array(
			'id' => 0,
		)
	);

	if ( 0 === $atts['id'] ) {
		return '';
	}

	$snippet = new WPCode_Snippet( absint( $atts['id'] ) );

	if ( ! $snippet->is_active() ) {
		return '';
	}

	// Let's check that conditional logic rules are met.
	if ( $snippet->conditional_rules_enabled() && ! wpcode()->conditional_logic->are_snippet_rules_met( $snippet ) && apply_filters( 'wpcode_shortcode_use_conditional_logic', true ) ) {
		return '';
	}

	$shortcode_location = apply_filters( 'wpcode_get_snippets_for_location', array( $snippet ), 'shortcode' );

	if ( empty( $shortcode_location ) ) {
		return '';
	}

	do_action( 'wpcode_shortcode_before_output', $snippet, $atts, $content, $tag );

	return wpcode()->execute->get_snippet_output( $snippet );
}

/**
 * Before the shortcode output, let's check if we have to load any shortcode attributes to the class instance.
 *
 * @param WPCode_Snippet $snippet The snippet instance.
 * @param array          $atts The shortcode attributes.
 * @param string|null    $content Shortcode content, if any.
 * @param string         $tag The shortcode tag.
 *
 * @return void
 */
function wpcode_pass_shortcode_attributes_to_snippet( $snippet, $atts, $content, $tag ) {
	// Let's see if we have to load any shortcode attributes.
	$shortcode_attributes = $snippet->get_shortcode_attributes();
	if ( ! empty( $shortcode_attributes ) ) {
		foreach ( $shortcode_attributes as $attribute ) {
			$value = isset( $atts[ $attribute ] ) ? $atts[ $attribute ] : '';
			$snippet->set_attribute( $attribute, apply_filters( 'wpcode_shortcode_attribute_value', $value, $attribute ) );
		}
	}
	if ( ! empty( $content ) ) {
		$snippet->set_attribute( 'content', $content );
	}
}
