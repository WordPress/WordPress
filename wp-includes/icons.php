<?php
/**
 * Icons API: Icon-rendering helper functions.
 *
 * @package WordPress
 * @subpackage Icons
 * @since 7.1.0
 */

/**
 * Returns the SVG markup for a registered icon.
 *
 * @since 7.1.0
 *
 * @param string $name The namespaced icon name (e.g. 'core/plus',
 *                     'core/arrow-down', 'my-plugin/custom-icon').
 * @param array  $args {
 *     Optional. Arguments for the icon. Default empty array.
 *
 *     @type int|null $size  Width and height in pixels. Pass null to leave the
 *                           SVG's intrinsic dimensions untouched. Default 24.
 *     @type string   $class Additional CSS class names. Multiple classes may be
 *                           provided as a space-separated string. Default empty string.
 *     @type string   $label Accessible label. If provided, the SVG gets
 *                           role="img" and aria-label. If omitted, the SVG
 *                           gets aria-hidden="true" and focusable="false".
 *                           Default empty string.
 * }
 * @return string SVG markup for the icon, or empty string if not found.
 */
function wp_get_icon( $name, $args = array() ) {
	$icon = WP_Icons_Registry::get_instance()->get_registered_icon( $name );
	if ( is_null( $icon ) ) {
		return '';
	}

	$svg = $icon['content'];
	if ( empty( $svg ) ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'size'  => 24,
			'class' => '',
			'label' => '',
		)
	);

	$processor = new WP_HTML_Tag_Processor( $svg );
	if ( ! $processor->next_tag( 'svg' ) ) {
		return '';
	}

	if ( is_numeric( $args['size'] ) ) {
		$size = absint( $args['size'] );
		$processor->set_attribute( 'width', (string) $size );
		$processor->set_attribute( 'height', (string) $size );
	}

	if ( ! empty( $args['class'] ) ) {
		foreach ( preg_split( '/\s+/', $args['class'], -1, PREG_SPLIT_NO_EMPTY ) as $class_name ) {
			$processor->add_class( $class_name );
		}
	}

	if ( ! empty( $args['label'] ) ) {
		$processor->set_attribute( 'role', 'img' );
		$processor->set_attribute( 'aria-label', $args['label'] );
		$processor->remove_attribute( 'aria-hidden' );
		$processor->remove_attribute( 'focusable' );
	} else {
		$processor->set_attribute( 'aria-hidden', 'true' );
		$processor->set_attribute( 'focusable', 'false' );
		$processor->remove_attribute( 'role' );
		$processor->remove_attribute( 'aria-label' );
	}

	return $processor->get_updated_html();
}
