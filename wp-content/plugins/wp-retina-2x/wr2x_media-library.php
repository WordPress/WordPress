<?php

add_filter( 'manage_media_columns', 'wr2x_manage_media_columns' );
add_action( 'manage_media_custom_column', 'wr2x_manage_media_custom_column', 10, 2 );

/**
 *
 * MEDIA LIBRARY
 *
 */
 
function wr2x_manage_media_columns( $cols ) {
	$cols["Retina"] = "Retina";
	return $cols;
}

function wr2x_manage_media_custom_column( $column_name, $id ) {
	if ( $column_name != 'Retina' )
		return;
	
	if ( wr2x_is_ignore( $id ) ) {
		echo "<img style='margin-top: -2px; margin-bottom: 2px; width: 16px; height: 16px;' src='" . trailingslashit( WP_PLUGIN_URL ) . trailingslashit( 'wp-retina-2x/img') . "tick-circle.png' />";
		return;
	}
	
	// Check if the attachment is an image
	$meta = wp_get_attachment_metadata($id);
	if ( !($meta && isset( $meta['width'] ) && isset( $meta['height'] )) ) {
		return;
	}
	
	$isAlright = true;
	$info = wr2x_retina_info( $id );
	foreach ( $info as $name => $attr ) {
		if ( $attr == 'PENDING' || is_array( $attr ) )
			$isAlright = false;
	}

	// Displays the result
	echo "<p id='wr2x_attachment_$id' style='margin-bottom: 2px;'>";
	if ( $isAlright ) {
		echo "<img style='margin-top: -2px; margin-bottom: 2px; width: 16px; height: 16px;' src='" . trailingslashit( WP_PLUGIN_URL ) . trailingslashit( 'wp-retina-2x/img') . "tick-circle.png' />";
	}
	else {
		echo "<a href='upload.php?page=wp-retina-2x'><img style='margin-top: -2px; margin-bottom: 2px; width: 16px; height: 16px;' src='" . trailingslashit( WP_PLUGIN_URL ) . trailingslashit( 'wp-retina-2x/img') . "exclamation.png' /></a>";
	}
	echo "</p>";
	
}

?>