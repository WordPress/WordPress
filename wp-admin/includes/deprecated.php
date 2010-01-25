<?php

/**
 * Calculates the new dimentions for a downsampled image.
 *
 * @since 2.0.0
 * @deprecated 3.0.0
 * @deprecated Use wp_constrain_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @param int $wmax Maximum wanted width
 * @param int $hmax Maximum wanted height
 * @return mixed Array(height,width) of shrunk dimensions.
 */
function wp_shrink_dimensions( $width, $height, $wmax = 128, $hmax = 96 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_constrain_dimensions()' );
	return wp_constrain_dimensions( $width, $height, $wmax, $hmax );
}

?>