<?php
/**
** Retrieve list data from the Listo plugin.
** Listo http://wordpress.org/plugins/listo/
**/

add_filter( 'wpcf7_form_tag_data_option', 'wpcf7_listo', 10, 3 );

function wpcf7_listo( $data, $options, $args ) {
	if ( ! function_exists( 'listo' ) ) {
		return $data;
	}

	$args = wp_parse_args( $args, array() );

	if ( $contact_form = wpcf7_get_current_contact_form() ) {
		$args['locale'] = $contact_form->locale();
	}

	foreach ( (array) $options as $option ) {
		$option = explode( '.', $option );
		$type = $option[0];

		if ( isset( $option[1] ) ) {
			$args['group'] = $option[1];
		} else {
			unset( $args['group'] );
		}

		if ( $list = listo( $type, $args ) ) {
			$data = array_merge( (array) $data, $list );
		}
	}

	return $data;
}
