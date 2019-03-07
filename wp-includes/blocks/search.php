<?php
/**
 * Server-side rendering of the `core/search` block.
 *
 * @package WordPress
 */

/**
 * Dynamically renders the `core/search` block.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The search block markup.
 */
function render_block_core_search( $attributes ) {
	static $instance_id = 0;

	$input_id = 'wp-block-search__input-' . ++$instance_id;

	if ( ! empty( $attributes['label'] ) ) {
		$label_markup = sprintf(
			'<label for="%s" class="wp-block-search__label">%s</label>',
			$input_id,
			$attributes['label']
		);
	}

	$input_markup = sprintf(
		'<input type="search" id="%s" class="wp-block-search__input" name="s" value="%s" placeholder="%s" />',
		$input_id,
		esc_attr( get_search_query() ),
		esc_attr( $attributes['placeholder'] )
	);

	if ( ! empty( $attributes['buttonText'] ) ) {
		$button_markup = sprintf(
			'<button type="submit" class="wp-block-search__button">%s</button>',
			$attributes['buttonText']
		);
	}

	$class = 'wp-block-search';
	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}

	return sprintf(
		'<form class="%s" role="search" method="get" action="%s">%s</form>',
		$class,
		esc_url( home_url( '/' ) ),
		$label_markup . $input_markup . $button_markup
	);
}

/**
 * Registers the `core/search` block on the server.
 */
function register_block_core_search() {
	register_block_type(
		'core/search',
		array(
			'attributes'      => array(
				'label'       => array(
					'type'    => 'string',
					'default' => __( 'Search' ),
				),
				'placeholder' => array(
					'type'    => 'string',
					'default' => '',
				),
				'buttonText'  => array(
					'type'    => 'string',
					'default' => __( 'Search' ),
				),
			),

			'render_callback' => 'render_block_core_search',
		)
	);
}

add_action( 'init', 'register_block_core_search' );
