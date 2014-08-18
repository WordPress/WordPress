<?php
/**
** A base module for [response]
**/

/* Shortcode handler */

wpcf7_add_shortcode( 'response', 'wpcf7_response_shortcode_handler' );

function wpcf7_response_shortcode_handler( $tag ) {
	if ( $contact_form = wpcf7_get_current_contact_form() ) {
		return $contact_form->form_response_output();
	}
}

?>