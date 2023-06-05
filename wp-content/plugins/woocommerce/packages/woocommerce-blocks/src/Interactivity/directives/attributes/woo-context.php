<?php

function process_woo_context_attribute( $tags, $context ) {
	if ( $tags->is_tag_closer() ) {
		$context->rewind_context();
		return;
	}

	$value = $tags->get_attribute( 'data-woo-context' );
	if ( null === $value ) {
		// No woo-context directive.
		return;
	}

	$new_context = json_decode( $value, true );

	$context->set_context( $new_context );
}
