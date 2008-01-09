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
function wp_create_thumbnail( $file, $max_side, $deprecated = '' ) {
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

	// preserve PNG transparency
	if ( IMAGETYPE_PNG == $sourceImageType && function_exists( 'imagealphablending' ) && function_exists( 'imagesavealpha' ) ) {
		imagealphablending( $thumbnail, false);
		imagesavealpha( $thumbnail, true);
	}
	
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
		
		// fetch additional metadata from exif/iptc
		$image_meta = wp_read_image_metadata( $file );
		if ($image_meta)
			$metadata['image_meta'] = $image_meta;

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

// convert a fraction string to a decimal
function wp_exif_frac2dec($str) {
	@list( $n, $d ) = explode( '/', $str );
	if ( !empty($d) )
		return $n / $d;
	return $str;
}

// convert the exif date format to a unix timestamp
function wp_exif_date2ts($str) {
	// seriously, who formats a date like 'YYYY:MM:DD hh:mm:ss'?
	@list( $date, $time ) = explode( ' ', trim($str) );
	@list( $y, $m, $d ) = explode( ':', $date );

	return strtotime( "{$y}-{$m}-{$d} {$time}" );
}

// get extended image metadata, exif or iptc as available
function wp_read_image_metadata( $file ) {
	if ( !file_exists( $file ) )
		return false;

	list(,,$sourceImageType) = getimagesize( $file );

	// exif contains a bunch of data we'll probably never need formatted in ways that are difficult to use.
	// We'll normalize it and just extract the fields that are likely to be useful.  Fractions and numbers
	// are converted to floats, dates to unix timestamps, and everything else to strings.
	$meta = array(
		'aperture' => 0,
		'credit' => '',
		'camera' => '',
		'caption' => '',
		'created_timestamp' => 0,
		'copyright' => '',
		'focal_length' => 0,
		'iso' => 0,
		'shutter_speed' => 0,
		'title' => '',
	);

	// read iptc first, since it might contain data not available in exif such as caption, description etc
	if ( is_callable('iptcparse') ) {
		getimagesize($file, $info);
		if ( !empty($info['APP13']) ) {
			$iptc = iptcparse($info['APP13']);
			if ( !empty($iptc['2#110'][0]) ) // credit
				$meta['credit'] = trim( $iptc['2#110'][0] );
			elseif ( !empty($iptc['2#080'][0]) ) // byline
				$meta['credit'] = trim( $iptc['2#080'][0] );
			if ( !empty($iptc['2#055'][0]) and !empty($iptc['2#060'][0]) ) // created datee and time
				$meta['created_timestamp'] = strtotime($iptc['2#055'][0] . ' ' . $iptc['2#060'][0]);
			if ( !empty($iptc['2#120'][0]) ) // caption
				$meta['caption'] = trim( $iptc['2#120'][0] );
			if ( !empty($iptc['2#116'][0]) ) // copyright
				$meta['copyright'] = trim( $iptc['2#116'][0] );
			if ( !empty($iptc['2#005'][0]) ) // title
				$meta['title'] = trim( $iptc['2#005'][0] );
		 }
	}

	// fetch additional info from exif if available
	if ( is_callable('exif_read_data') && in_array($sourceImageType, apply_filters('wp_read_image_metadata_types', array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM)) ) ) {
		$exif = exif_read_data( $file );
		if (!empty($exif['FNumber']))
			$meta['aperture'] = round( wp_exif_frac2dec( $exif['FNumber'] ), 2 );
		if (!empty($exif['Model']))
			$meta['camera'] = trim( $exif['Model'] );
		if (!empty($exif['DateTimeDigitized']))
			$meta['created_timestamp'] = wp_exif_date2ts($exif['DateTimeDigitized']);
		if (!empty($exif['FocalLength']))
			$meta['focal_length'] = wp_exif_frac2dec( $exif['FocalLength'] );
		if (!empty($exif['ISOSpeedRatings']))
			$meta['iso'] = $exif['ISOSpeedRatings'];
		if (!empty($exif['ExposureTime']))
			$meta['shutter_speed'] = wp_exif_frac2dec( $exif['ExposureTime'] );
	}
	// FIXME: try other exif libraries if available

	return apply_filters( 'wp_read_image_metadata', $meta, $file, $sourceImageType );

}


?>
