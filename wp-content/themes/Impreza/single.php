<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying all single posts and attachments
 *
 * Please do not overload this file directly. Instead have a look at framework/templates/single.php: you should find all
 * the needed hooks there.
 */

$supported_custom_post_types = us_get_option( 'custom_post_types_support', array() );
if ( is_array( $supported_custom_post_types ) AND count( $supported_custom_post_types ) > 0 AND is_singular ( $supported_custom_post_types ) ) {
	us_load_template( 'templates/page' );
} else {
	us_load_template( 'templates/single' );
}

