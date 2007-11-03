<?php
/**
 * File contains all the administration image manipulation functions.
 *
 * @package WordPress
 */

/**
 * wp_create_thumbnail() - Create a thumbnail from an Image given a maximum side size.
 *
 * @package WordPress
 * @param	mixed	$file	Filename of the original image, Or attachment id
 * @param	int		$max_side	Maximum length of a single side for the thumbnail
 * @return	string			Thumbnail path on success, Error string on failure
 *
 * This function can handle most image file formats which PHP supports.
 * If PHP does not have the functionality to save in a file of the same format, the thumbnail will be created as a jpeg.
 */
function wp_create_thumbnail( $file, $max_side, $depreciated = '' ) {
	if ( ctype_digit( $file ) ) // Handle int as attachment ID
		$file = get_attached_file( $file );

	$image = wp_load_image( $file );
	
	if ( !is_resource( $image ) )
		return $image;

	list($sourceImageWidth, $sourceImageHeight, $sourceImageType) = getimagesize( $file );

	if ( function_exists( 'imageantialias' ))
		imageantialias( $image, true );

	list($image_new_width, $image_new_height) = wp_shrink_dimensions( $sourceImageWidth, $sourceImageHeight, $max_side, $max_side);

	$thumbnail = imagecreatetruecolor( $image_new_width, $image_new_height);
	@ imagecopyresampled( $thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $sourceImageWidth, $sourceImageHeight );

	imagedestroy( $image ); // Free up memory 

	// If no filters change the filename, we'll do a default transformation.
	if ( basename( $file ) == $thumb = apply_filters( 'thumbnail_filename', basename( $file ) ) )
		$thumb = preg_replace( '!(\.[^.]+)?$!', '.thumbnail$1', basename( $file ), 1 );

	$thumbpath = str_replace( basename( $file ), $thumb, $file );

	switch( $sourceImageType ){
		default: // We'll create a Jpeg if we cant use its native file format
			$thumb = preg_replace( '/\\.[^\\.]+$/', '.jpg', $thumb ); //Change file extension to Jpg
		case IMAGETYPE_JPEG:
			if (!imagejpeg( $thumbnail, $thumbpath ) )
				return __( 'Thumbnail path invalid' );
			break;
		case IMAGETYPE_GIF:
			if (!imagegif( $thumbnail, $thumbpath ) )
				return __( 'Thumbnail path invalid' );
			break;
		case IMAGETYPE_PNG:
			if (!imagepng( $thumbnail, $thumbpath ) )
				return __( 'Thumbnail path invalid' );
			break;
	}

	imagedestroy( $thumbnail ); // Free up memory 
	
	// Set correct file permissions 
	$stat = stat( dirname( $thumbpath )); 
	$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
	@ chmod( $thumbpath, $perms ); 

	return apply_filters( 'wp_create_thumbnail', $thumbpath );
}

/**
 * wp_crop_image() - Crop an Image to a given size.
 *
 * @package WordPress
 * @internal Missing Long Description
 * @param	int	$src_file	The source file
 * @param	int	$src_x		The start x position to crop from
 * @param	int	$src_y		The start y position to crop from
 * @param	int	$src_w		The width to crop
 * @param	int	$src_h		The height to crop
 * @param	int	$dst_w		The destination width
 * @param	int	$dst_h		The destination height
 * @param	int	$src_abs	If the source crop points are absolute
 * @param	int	$dst_file	The destination file to write to
 * @return	string			New filepath on success, String error message on failure
 *
 */
function wp_crop_image( $src_file, $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs = false, $dst_file = false ) {
	if ( ctype_digit( $src_file ) ) // Handle int as attachment ID
		$src_file = get_attached_file( $src_file );

	$src = wp_load_image( $src_file );

	if ( !is_resource( $src ))
		return $src;

	$dst = imagecreatetruecolor( $dst_w, $dst_h );

	if ( $src_abs ) {
		$src_w -= $src_x;
		$src_h -= $src_y;
	}

	if (function_exists('imageantialias'))
		imageantialias( $dst, true );

	imagecopyresampled( $dst, $src, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
	
	imagedestroy( $src ); // Free up memory 

	if ( ! $dst_file )
		$dst_file = str_replace( basename( $src_file ), 'cropped-' . basename( $src_file ), $src_file );

	$dst_file = preg_replace( '/\\.[^\\.]+$/', '.jpg', $dst_file );

	if ( imagejpeg( $dst, $dst_file ) )
		return $dst_file;
	else
		return false;
}

/**
 * wp_generate_attachment_metadata() - Generate post Image attachment Metadata
 *
 * @package WordPress
 * @internal Missing Long Description
 * @param	int		$attachment_id	Attachment Id to process
 * @param	string	$file	Filepath of the Attached image
 * @return	mixed			Metadata for attachment
 *
 */
function wp_generate_attachment_metadata( $attachment_id, $file ) {
	$attachment = get_post( $attachment_id );

	$metadata = array();
	if ( preg_match('!^image/!', get_post_mime_type( $attachment )) ) {
		$imagesize = getimagesize( $file );
		$metadata['width'] = $imagesize[0];
		$metadata['height'] = $imagesize[1];
		list($uwidth, $uheight) = wp_shrink_dimensions($metadata['width'], $metadata['height']);
		$metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";
		$metadata['file'] = $file;

		$max = apply_filters( 'wp_thumbnail_creation_size_limit', 3 * 1024 * 1024, $attachment_id, $file );

		if ( $max < 0 || $metadata['width'] * $metadata['height'] < $max ) {
			$max_side = apply_filters( 'wp_thumbnail_max_side_length', 128, $attachment_id, $file );
			$thumb = wp_create_thumbnail( $file, $max_side );

			if ( @file_exists($thumb) )
				$metadata['thumb'] = basename($thumb);
		}
	}
	return apply_filters( 'wp_generate_attachment_metadata', $metadata );
}

/**
 * wp_load_image() - Load an image which PHP Supports.
 *
 * @package WordPress
 * @internal Missing Long Description
 * @param	string	$file	Filename of the image to load
 * @return	resource		The resulting image resource on success, Error string on failure.
 *
 */
function wp_load_image( $file ) {
	if ( ctype_digit( $file ) )
		$file = get_attached_file( $file );

	if ( ! file_exists( $file ) )
		return sprintf(__("File '%s' doesn't exist?"), $file);

	if ( ! function_exists('imagecreatefromstring') )
		return __('The GD image library is not installed.');

	$image = @imagecreatefromstring( @file_get_contents( $file ) );

	if ( !is_resource( $image ) )
		return sprintf(__("File '%s' is not an image."), $file);

	return $image;
}

/**
 * get_udims() - Calculated the new dimentions for downsampled images
 *
 * @package WordPress
 * @internal Missing Description
 * @see wp_shrink_dimensions()
 * @param	int		$width	Current width of the image
 * @param	int 	$height	Current height of the image
 * @return	mixed			Array(height,width) of shrunk dimensions.
 *
 */
function get_udims( $width, $height) {
	return wp_shrink_dimensions( $width, $height );
}
/**
 * wp_shrink_dimensions() - Calculates the new dimentions for a downsampled image.
 *
 * @package WordPress
 * @internal Missing Long Description
 * @param	int		$width	Current width of the image
 * @param	int 	$height	Current height of the image
 * @param	int		$wmax	Maximum wanted width
 * @param	int		$hmax	Maximum wanted height
 * @return	mixed			Array(height,width) of shrunk dimensions.
 *
 */
function wp_shrink_dimensions( $width, $height, $wmax = 128, $hmax = 96 ) {
	if ( $height <= $hmax && $width <= $wmax ){
		//Image is smaller than max
		return array( $width, $height);
	} elseif ( $width / $height > $wmax / $hmax ) {
		//Image Width will be greatest
		return array( $wmax, (int) ($height / $width * $wmax ));
	} else {
		//Image Height will be greatest
		return array( (int) ($width / $height * $hmax ), $hmax );
	}
}

?>
