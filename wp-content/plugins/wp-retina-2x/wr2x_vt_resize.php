<?php
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * Modified by Jordy Meow for WP Retina 2x
 */

if ( !function_exists('wr2x_vt_resize') ) {
	function wr2x_vt_resize( $file_path, $width, $height, $newfile ) {
		$orig_size = getimagesize( $file_path );
		$image_src[0] = $file_path;
		$image_src[1] = $orig_size[0];
		$image_src[2] = $orig_size[1];
		$file_info = pathinfo( $file_path );
		$extension = '.' . $file_info['extension'];
		$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];
		$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . "-tmp" . $extension;
		$image = wp_get_image_editor( $file_path );
		$image->resize( $width, $height, true );

		$quality = wr2x_getoption( "image_quality", "wr2x_advanced", "80" );
		if ( is_numeric( $quality ) ) {
			$image->set_quality( intval( $quality ) );
		}

		$image->save( $cropped_img_path );
		if ( rename( $cropped_img_path, $newfile ) )
			$cropped_img_path = $newfile;
		$new_img_size = getimagesize( $cropped_img_path );
		$new_img = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
		$vt_image = array ( 'url' => $new_img, 'width' => $new_img_size[0], 'height' => $new_img_size[1] );
		return $vt_image;
	}
}
