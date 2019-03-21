<?php
/**
 * Server-side rendering of the `core/calendar` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/calendar` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the block content.
 */
function render_block_core_calendar( $attributes ) {
	global $monthnum, $year;

	$previous_monthnum = $monthnum;
	$previous_year     = $year;

	if ( isset( $attributes['month'] ) && isset( $attributes['year'] ) ) {
		$permalink_structure = get_option( 'permalink_structure' );
		if (
			strpos( $permalink_structure, '%monthnum%' ) !== false &&
			strpos( $permalink_structure, '%year%' ) !== false
		) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
			$monthnum = $attributes['month'];
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
			$year = $attributes['year'];
		}
	}

	$custom_class_name = empty( $attributes['className'] ) ? '' : ' ' . $attributes['className'];
	$align_class_name  = empty( $attributes['align'] ) ? '' : ' ' . "align{$attributes['align']}";

	return sprintf(
		'<div class="%1$s">%2$s</div>',
		esc_attr( 'wp-block-calendar' . $custom_class_name . $align_class_name ),
		get_calendar( true, false )
	);

	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
	$monthnum = $previous_monthnum;
	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
	$year = $previous_year;
}

/**
 * Registers the `core/calendar` block on server.
 */
function register_block_core_calendar() {
	register_block_type(
		'core/calendar',
		array(
			'attributes'      => array(
				'align'     => array(
					'type' => 'string',
				),
				'className' => array(
					'type' => 'string',
				),
				'month'     => array(
					'type' => 'integer',
				),
				'year'      => array(
					'type' => 'integer',
				),
			),
			'render_callback' => 'render_block_core_calendar',
		)
	);
}

add_action( 'init', 'register_block_core_calendar' );
