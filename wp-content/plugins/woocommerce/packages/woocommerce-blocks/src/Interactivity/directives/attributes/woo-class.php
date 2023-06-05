<?php

require_once __DIR__ . '/../utils.php';

function process_woo_class( $tags, $context ) {
	if ( $tags->is_tag_closer() ) {
		return;
	}

	$prefixed_attributes = $tags->get_attribute_names_with_prefix( 'data-woo-class:' );

	foreach ( $prefixed_attributes as $attr ) {
		list( , $class_name ) = explode( ':', $attr );
		if ( empty( $class_name ) ) {
			continue;
		}

		$expr      = $tags->get_attribute( $attr );
		$add_class = woo_directives_evaluate( $expr, $context->get_context() );
		if ( $add_class ) {
			$tags->add_class( $class_name );
		} else {
			$tags->remove_class( $class_name );
		}
	}
}
