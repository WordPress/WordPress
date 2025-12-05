<?php
/**
** A base module for [response]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_response', 10, 0 );

function wpcf7_add_form_tag_response() {
	wpcf7_add_form_tag( 'response',
		'wpcf7_response_form_tag_handler',
		array(
			'display-block' => true,
		)
	);
}

function wpcf7_response_form_tag_handler( $tag ) {
	if ( $contact_form = wpcf7_get_current_contact_form() ) {
		return $contact_form->form_response_output();
	}
}
