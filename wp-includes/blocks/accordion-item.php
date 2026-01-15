<?php

/**
 * Server-side rendering of the `core/accordion-item` block.
 *
 * @package WordPress
 * @since 6.9.0
 *
 * @param array $attributes The block attributes.
 * @param string $content The block content.
 *
 * @return string Returns the updated markup.
 */
function block_core_accordion_item_render( $attributes, $content ) {
	if ( ! $content ) {
		return $content;
	}

	$p         = new WP_HTML_Tag_Processor( $content );
	$unique_id = wp_unique_id( 'accordion-item-' );

	// Initialize the state of the item on the server using a closure,
	// since we need to get derived state based on the current context.
	wp_interactivity_state(
		'core/accordion',
		array(
			'isOpen' => function () {
				$context = wp_interactivity_get_context();
				return $context['openByDefault'];
			},
		)
	);

	if ( $p->next_tag( array( 'class_name' => 'wp-block-accordion-item' ) ) ) {
		$open_by_default = $attributes['openByDefault'] ? 'true' : 'false';
		$p->set_attribute( 'data-wp-context', '{ "id": "' . $unique_id . '", "openByDefault": ' . $open_by_default . ' }' );
		$p->set_attribute( 'data-wp-class--is-open', 'state.isOpen' );
		$p->set_attribute( 'data-wp-init', 'callbacks.initAccordionItems' );
		$p->set_attribute( 'data-wp-on-window--hashchange', 'callbacks.hashChange' );

		if ( $p->next_tag( array( 'class_name' => 'wp-block-accordion-heading__toggle' ) ) ) {
			$p->set_attribute( 'data-wp-on--click', 'actions.toggle' );
			$p->set_attribute( 'data-wp-on--keydown', 'actions.handleKeyDown' );
			$p->set_attribute( 'id', $unique_id );
			$p->set_attribute( 'aria-controls', $unique_id . '-panel' );
			$p->set_attribute( 'data-wp-bind--aria-expanded', 'state.isOpen' );

			if ( $p->next_tag( array( 'class_name' => 'wp-block-accordion-panel' ) ) ) {
				$p->set_attribute( 'id', $unique_id . '-panel' );
				$p->set_attribute( 'aria-labelledby', $unique_id );
				$p->set_attribute( 'data-wp-bind--inert', '!state.isOpen' );

				// Only modify content if all directives have been set.
				$content = $p->get_updated_html();
			}
		}
	}

	return $content;
}

/**
 * Registers the `core/accordion-item` block on server.
 *
 * @since 6.9.0
 */
function register_block_core_accordion_item() {
	register_block_type_from_metadata(
		__DIR__ . '/accordion-item',
		array(
			'render_callback' => 'block_core_accordion_item_render',
		)
	);
}
add_action( 'init', 'register_block_core_accordion_item' );
