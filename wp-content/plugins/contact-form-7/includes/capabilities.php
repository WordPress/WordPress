<?php

add_filter( 'map_meta_cap', 'wpcf7_map_meta_cap', 10, 4 );

function wpcf7_map_meta_cap( $caps, $cap, $user_id, $args ) {
	$meta_caps = array(
		'wpcf7_edit_contact_form' => WPCF7_ADMIN_READ_WRITE_CAPABILITY,
		'wpcf7_edit_contact_forms' => WPCF7_ADMIN_READ_WRITE_CAPABILITY,
		'wpcf7_read_contact_form' => WPCF7_ADMIN_READ_CAPABILITY,
		'wpcf7_read_contact_forms' => WPCF7_ADMIN_READ_CAPABILITY,
		'wpcf7_delete_contact_form' => WPCF7_ADMIN_READ_WRITE_CAPABILITY,
		'wpcf7_delete_contact_forms' => WPCF7_ADMIN_READ_WRITE_CAPABILITY,
		'wpcf7_manage_integration' => 'manage_options',
		'wpcf7_submit' => 'read',
	);

	$meta_caps = apply_filters( 'wpcf7_map_meta_cap', $meta_caps );

	$caps = array_diff( $caps, array_keys( $meta_caps ) );

	if ( isset( $meta_caps[$cap] ) ) {
		$caps[] = $meta_caps[$cap];
	}

	return $caps;
}
