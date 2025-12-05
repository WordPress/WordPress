<?php

function wpcf7_current_action() {
	foreach ( array( 'action', 'action2' ) as $var ) {
		$action = wpcf7_superglobal_request( $var, null );

		if ( isset( $action ) and -1 !== $action ) {
			return $action;
		}
	}

	return false;
}

function wpcf7_admin_has_edit_cap() {
	return current_user_can( 'wpcf7_edit_contact_forms' );
}

function wpcf7_add_tag_generator( $name, $title, $elm_id, $callback, $options = array() ) {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	return $tag_generator->add( $name, $title, $callback, $options );
}
