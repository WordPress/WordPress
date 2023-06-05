<?php

require_once __DIR__ . '/class-woo-directive-context.php';

function woo_process_directives( $tags, $prefix, $tag_directives, $attribute_directives ) {
	$context = new Woo_Directive_Context;

	$tag_stack = array();
	while ( $tags->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
		$tag_name = strtolower( $tags->get_tag() );
		if ( array_key_exists( $tag_name, $tag_directives ) ) {
			call_user_func( $tag_directives[ $tag_name ], $tags, $context );
		} else {
			// Components can't have directives (unless we change our mind about this).

			// Is this a tag that closes the latest opening tag?
			if ( $tags->is_tag_closer() ) {
				if ( 0 === count( $tag_stack ) ) {
					continue;
				}

				list( $latest_opening_tag_name, $attributes ) = end( $tag_stack );
				if ( $latest_opening_tag_name === $tag_name ) {
					array_pop( $tag_stack );

					// If the matching opening tag didn't have any attribute directives,
					// we move on.
					if ( 0 === count( $attributes ) ) {
						continue;
					}
				}
			} else {
				// Helper that removes the part after the colon before looking
				// for the directive processor inside `$attribute_directives`.
				$get_directive_type = function ( $attr ) {
					return strtok( $attr, ':' );
				};

				$attributes = $tags->get_attribute_names_with_prefix( $prefix );
				$attributes = array_map( $get_directive_type, $attributes );
				$attributes = array_intersect( $attributes, array_keys( $attribute_directives ) );

				// If this is an open tag, and if it either has attribute directives,
				// or if we're inside a tag that does, take note of this tag and its attribute
				// directives so we can call its directive processor once we encounter the
				// matching closing tag.
				if (
					! woo_directives_is_html_void_element( $tags->get_tag() ) &&
					( 0 !== count( $attributes ) || 0 !== count( $tag_stack ) )
				) {
					$tag_stack[] = array( $tag_name, $attributes );
				}
			}

			foreach ( $attributes as $attribute ) {
				call_user_func( $attribute_directives[ $attribute ], $tags, $context );
			}
		}
	}

	return $tags;
}

// See e.g. https://github.com/WordPress/gutenberg/pull/47573.
function woo_directives_is_html_void_element( $tag_name ) {
	switch ( $tag_name ) {
		case 'AREA':
		case 'BASE':
		case 'BR':
		case 'COL':
		case 'EMBED':
		case 'HR':
		case 'IMG':
		case 'INPUT':
		case 'LINK':
		case 'META':
		case 'SOURCE':
		case 'TRACK':
		case 'WBR':
			return true;

		default:
			return false;
	}
}
