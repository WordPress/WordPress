<?php

require_once __DIR__ . '/../utils.php';

function process_woo_bind( $tags, $context ) {
	if ( $tags->is_tag_closer() ) {
		return;
	}

	$prefixed_attributes = $tags->get_attribute_names_with_prefix( 'data-woo-bind:' );

	foreach ( $prefixed_attributes as $attr ) {
		list( , $bound_attr ) = explode( ':', $attr );
		if ( empty( $bound_attr ) ) {
			continue;
		}

		$expr  = $tags->get_attribute( $attr );
		$value = woo_directives_evaluate( $expr, $context->get_context() );
		$tags->set_attribute( $bound_attr, $value );
	}
}
