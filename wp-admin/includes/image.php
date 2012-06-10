<?php
/**
 * File contains all the administration image manipulation functions.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Create a thumbnail from an Image given a maximum side size.
 *
 * This function can handle most image file formats which PHP supports. If PHP
 * does not have the functionality to save in a file of the same format, the
 * thumbnail will be created as a jpeg.
 *
 * @since 1.2.0
 *
 * @param mixed $file Filename of the original image, Or attachment id.
 * @param int $max_side Maximum length of a single side for the thumbnail.
 * @param mixed $deprecated Never used.
 * @return string Thumbnail path on success, Error string on failure.
 */
function wp_create_thumbnail( $file, $max_side, $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '1.2' );
	$thumbpath = image_resize( $file, $max_side, $max_side );
	return apply_filters( 'wp_create_thumbnail', $thumbpath );
}

/**
 * Crop an Image to a given size.
 *
 * @since 2.1.0
 *
 * @param string|int $src The source file or Attachment ID.
 * @param int $src_x The start x position to crop from.
 * @param int $src_y The start y position to crop from.
 * @param int $src_w The width to crop.
 * @param int $src_h The height to crop.
 * @param int $dst_w The destination width.
 * @param int $dst_h The destination height.
 * @param int $src_abs Optional. If the source crop points are absolute.
 * @param string $dst_file Optional. The destination file to write to.
 * @return string|WP_Error|false New filepath on success, WP_Error or false on failure.
 */
function wp_crop_image( $src, $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs = false, $dst_file = false ) {
	if ( is_numeric( $src ) ) { // Handle int as attachment ID
		$src_file = get_attached_file( $src );
		if ( ! file_exists( $src_file ) ) {
			// If the file doesn't exist, attempt a url fopen on the src link.
			// This can occur with certain file replication plugins.
			$post = get_post( $src );
			$image_type = $post->post_mime_type;
			$src = load_image_to_edit( $src, $post->post_mime_type, 'full' );
		} else {
			$size = @getimagesize( $src_file );
			$image_type = ( $size ) ? $size['mime'] : '';
			$src = wp_load_image( $src_file );
		}
	} else {
		$size = @getimagesize( $src );
		$image_type = ( $size ) ? $size['mime'] : '';
		$src = wp_load_image( $src );
	}

	if ( ! is_resource( $src ) )
		return new WP_Error( 'error_loading_image', $src, $src_file );

	$dst = wp_imagecreatetruecolor( $dst_w, $dst_h );

	if ( $src_abs ) {
		$src_w -= $src_x;
		$src_h -= $src_y;
	}

	if ( function_exists( 'imageantialias' ) )
		imageantialias( $dst, true );

	imagecopyresampled( $dst, $src, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

	imagedestroy( $src ); // Free up memory

	if ( ! $dst_file )
		$dst_file = str_replace( basename( $src_file ), 'cropped-' . basename( $src_file ), $src_file );

	if ( 'image/png' != $image_type )
		$dst_file = preg_replace( '/\\.[^\\.]+$/', '.jpg', $dst_file );

	// The directory containing the original file may no longer exist when
	// using a replication plugin.
	wp_mkdir_p( dirname( $dst_file ) );

	$dst_file = dirname( $dst_file ) . '/' . wp_unique_filename( dirname( $dst_file ), basename( $dst_file ) );

	if ( 'image/png' == $image_type && imagepng( $dst, $dst_file ) )
		return $dst_file;
	elseif ( imagejpeg( $dst, $dst_file, apply_filters( 'jpeg_quality', 90, 'wp_crop_image' ) ) )
		return $dst_file;
	else
		return false;
}

/**
 * Generate post thumbnail attachment meta data.
 *
 * @since 2.1.0
 *
 * @param int $attachment_id Attachment Id to process.
 * @param string $file Filepath of the Attached image.
 * @return mixed Metadata for attachment.
 */
function wp_generate_attachment_metadata( $attachment_id, $file ) {
	$attachment = get_post( $attachment_id );

	$metadata = array();
	if ( preg_match('!^image/!', get_post_mime_type( $attachment )) && file_is_displayable_image($file) ) {
		$imagesize = getimagesize( $file );
		$metadata['width'] = $imagesize[0];
		$metadata['height'] = $imagesize[1];
		list($uwidth, $uheight) = wp_constrain_dimensions($metadata['width'], $metadata['height'], 128, 96);
		$metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";

		// Make the file path relative to the upload dir
		$metadata['file'] = _wp_relative_upload_path($file);

		// make thumbnails and other intermediate sizes
		global $_wp_additional_image_sizes;

		foreach ( get_intermediate_image_sizes() as $s ) {
			$sizes[$s] = array( 'width' => '', 'height' => '', 'crop' => false );
			if ( isset( $_wp_additional_image_sizes[$s]['width'] ) )
				$sizes[$s]['width'] = intval( $_wp_additional_image_sizes[$s]['width'] ); // For theme-added sizes
			else
				$sizes[$s]['width'] = get_option( "{$s}_size_w" ); // For default sizes set in options
			if ( isset( $_wp_additional_image_sizes[$s]['height'] ) )
				$sizes[$s]['height'] = intval( $_wp_additional_image_sizes[$s]['height'] ); // For theme-added sizes
			else
				$sizes[$s]['height'] = get_option( "{$s}_size_h" ); // For default sizes set in options
			if ( isset( $_wp_additional_image_sizes[$s]['crop'] ) )
				$sizes[$s]['crop'] = intval( $_wp_additional_image_sizes[$s]['crop'] ); // For theme-added sizes
			else
				$sizes[$s]['crop'] = get_option( "{$s}_crop" ); // For default sizes set in options
		}

		$sizes = apply_filters( 'intermediate_image_sizes_advanced', $sizes );

		foreach ($sizes as $size => $size_data ) {
			$resized = image_make_intermediate_size( $file, $size_data['width'], $size_data['height'], $size_data['crop'] );
			if ( $resized )
				$metadata['sizes'][$size] = $resized;
		}

		// fetch additional metadata from exif/iptc
		$image_meta = wp_read_image_metadata( $file );
		if ( $image_meta )
			$metadata['image_meta'] = $image_meta;

	}

	return apply_filters( 'wp_generate_attachment_metadata', $metadata, $attachment_id );
}

/**
 * Calculated the new dimensions for a downsampled image.
 *
 * @since 2.0.0
 * @see wp_constrain_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @return mixed Array(height,width) of shrunk dimensions.
 */
function get_udims( $width, $height) {
	return wp_constrain_dimensions( $width, $height, 128, 96 );
}

/**
 * Convert a fraction string to a decimal.
 *
 * @since 2.5.0
 *
 * @param string $str
 * @return int|float
 */
function wp_exif_frac2dec($str) {
	@list( $n, $d ) = explode( '/', $str );
	if ( !empty($d) )
		return $n / $d;
	return $str;
}

/**
 * Convert the exif date format to a unix timestamp.
 *
 * @since 2.5.0
 *
 * @param string $str
 * @return int
 */
function wp_exif_date2ts($str) {
	@list( $date, $time ) = explode( ' ', trim($str) );
	@list( $y, $m, $d ) = explode( ':', $date );

	return strtotime( "{$y}-{$m}-{$d} {$time}" );
}

/**
 * Get extended image metadata, exif or iptc as available.
 *
 * Retrieves the EXIF metadata aperture, credit, camera, caption, copyright, iso
 * created_timestamp, focal_length, shutter_speed, and title.
 *
 * The IPTC metadata that is retrieved is APP13, credit, byline, created date
 * and time, caption, copyright, and title. Also includes FNumber, Model,
 * DateTimeDigitized, FocalLength, ISOSpeedRatings, and ExposureTime.
 *
 * @todo Try other exif libraries if available.
 * @since 2.5.0
 *
 * @param string $file
 * @return bool|array False on failure. Image metadata array on success.
 */
function wp_read_image_metadata( $file ) {
	if ( ! file_exists( $file ) )
		return false;

	list( , , $sourceImageType ) = getimagesize( $file );

	// exif contains a bunch of data we'll probably never need formatted in ways
	// that are difficult to use. We'll normalize it and just extract the fields
	// that are likely to be useful. Fractions and numbers are converted to
	// floats, dates to unix timestamps, and everything else to strings.
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

	// read iptc first, since it might contain data not available in exif such
	// as caption, description etc
	if ( is_callable( 'iptcparse' ) ) {
		getimagesize( $file, $info );

		if ( ! empty( $info['APP13'] ) ) {
			$iptc = iptcparse( $info['APP13'] );

			// headline, "A brief synopsis of the caption."
			if ( ! empty( $iptc['2#105'][0] ) )
				$meta['title'] = utf8_encode( trim( $iptc['2#105'][0] ) );
			// title, "Many use the Title field to store the filename of the image, though the field may be used in many ways."
			elseif ( ! empty( $iptc['2#005'][0] ) )
				$meta['title'] = utf8_encode( trim( $iptc['2#005'][0] ) );

			if ( ! empty( $iptc['2#120'][0] ) ) { // description / legacy caption
				$caption = utf8_encode( trim( $iptc['2#120'][0] ) );
				if ( empty( $meta['title'] ) ) {
					// Assume the title is stored in 2:120 if it's short.
					if ( strlen( $caption ) < 80 )
						$meta['title'] = $caption;
					else
						$meta['caption'] = $caption;
				} elseif ( $caption != $meta['title'] ) {
					$meta['caption'] = $caption;
				}
			}

			if ( ! empty( $iptc['2#110'][0] ) ) // credit
				$meta['credit'] = utf8_encode(trim($iptc['2#110'][0]));
			elseif ( ! empty( $iptc['2#080'][0] ) ) // creator / legacy byline
				$meta['credit'] = utf8_encode(trim($iptc['2#080'][0]));

			if ( ! empty( $iptc['2#055'][0] ) and ! empty( $iptc['2#060'][0] ) ) // created date and time
				$meta['created_timestamp'] = strtotime( $iptc['2#055'][0] . ' ' . $iptc['2#060'][0] );

			if ( ! empty( $iptc['2#116'][0] ) ) // copyright
				$meta['copyright'] = utf8_encode( trim( $iptc['2#116'][0] ) );
		 }
	}

	// fetch additional info from exif if available
	if ( is_callable( 'exif_read_data' ) && in_array( $sourceImageType, apply_filters( 'wp_read_image_metadata_types', array( IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ) ) ) ) {
		$exif = @exif_read_data( $file );

		if ( !empty( $exif['Title'] ) )
			$meta['title'] = utf8_encode( trim( $exif['Title'] ) );

		if ( ! empty( $exif['ImageDescription'] ) ) {
			if ( empty( $meta['title'] ) && strlen( $exif['ImageDescription'] ) < 80 ) {
				// Assume the title is stored in ImageDescription
				$meta['title'] = utf8_encode( trim( $exif['ImageDescription'] ) );
				if ( ! empty( $exif['COMPUTED']['UserComment'] ) && trim( $exif['COMPUTED']['UserComment'] ) != $meta['title'] )
					$meta['caption'] = utf8_encode( trim( $exif['COMPUTED']['UserComment'] ) );
			} elseif ( trim( $exif['ImageDescription'] ) != $meta['title'] ) {
				$meta['caption'] = utf8_encode( trim( $exif['ImageDescription'] ) );
			}
		} elseif ( ! empty( $exif['Comments'] ) && trim( $exif['Comments'] ) != $meta['title'] ) {
			$meta['caption'] = utf8_encode( trim( $exif['Comments'] ) );
		}

		if ( ! empty( $exif['Artist'] ) )
			$meta['credit'] = utf8_encode( trim( $exif['Artist'] ) );
		elseif ( ! empty($exif['Author'] ) )
			$meta['credit'] = utf8_encode( trim( $exif['Author'] ) );

		if ( ! empty( $exif['Copyright'] ) )
			$meta['copyright'] = utf8_encode( trim( $exif['Copyright'] ) );
		if ( ! empty($exif['FNumber'] ) )
			$meta['aperture'] = round( wp_exif_frac2dec( $exif['FNumber'] ), 2 );
		if ( ! empty($exif['Model'] ) )
			$meta['camera'] = utf8_encode( trim( $exif['Model'] ) );
		if ( ! empty($exif['DateTimeDigitized'] ) )
			$meta['created_timestamp'] = wp_exif_date2ts($exif['DateTimeDigitized'] );
		if ( ! empty($exif['FocalLength'] ) )
			$meta['focal_length'] = wp_exif_frac2dec( $exif['FocalLength'] );
		if ( ! empty($exif['ISOSpeedRatings'] ) ) {
			$meta['iso'] = is_array( $exif['ISOSpeedRatings'] ) ? reset( $exif['ISOSpeedRatings'] ) : $exif['ISOSpeedRatings'];
			$meta['iso'] = utf8_encode( trim( $meta['iso'] ) );
		}
		if ( ! empty($exif['ExposureTime'] ) )
			$meta['shutter_speed'] = wp_exif_frac2dec( $exif['ExposureTime'] );
	}

	return apply_filters( 'wp_read_image_metadata', $meta, $file, $sourceImageType );

}

/**
 * Validate that file is an image.
 *
 * @since 2.5.0
 *
 * @param string $path File path to test if valid image.
 * @return bool True if valid image, false if not valid image.
 */
function file_is_valid_image($path) {
	$size = @getimagesize($path);
	return !empty($size);
}

/**
 * Validate that file is suitable for displaying within a web page.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'file_is_displayable_image' on $result and $path.
 *
 * @param string $path File path to test.
 * @return bool True if suitable, false if not suitable.
 */
function file_is_displayable_image($path) {
	$info = @getimagesize($path);
	if ( empty($info) )
		$result = false;
	elseif ( !in_array($info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)) )	// only gif, jpeg and png images can reliably be displayed
		$result = false;
	else
		$result = true;

	return apply_filters('file_is_displayable_image', $result, $path);
}

/**
 * Load an image resource for editing.
 *
 * @since 2.9.0
 *
 * @param string $attachment_id Attachment ID.
 * @param string $mime_type Image mime type.
 * @param string $size Optional. Image size, defaults to 'full'.
 * @return resource|false The resulting image resource on success, false on failure.
 */
function load_image_to_edit( $attachment_id, $mime_type, $size = 'full' ) {
	$filepath = _load_image_to_edit_path( $attachment_id, $size );
	if ( empty( $filepath ) )
		return false;

	switch ( $mime_type ) {
		case 'image/jpeg':
			$image = imagecreatefromjpeg($filepath);
			break;
		case 'image/png':
			$image = imagecreatefrompng($filepath);
			break;
		case 'image/gif':
			$image = imagecreatefromgif($filepath);
			break;
		default:
			$image = false;
			break;
	}
	if ( is_resource($image) ) {
		$image = apply_filters('load_image_to_edit', $image, $attachment_id, $size);
		if ( function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}
	}
	return $image;
}

/**
 * Retrieve the path or url of an attachment's attached file.
 *
 * If the attached file is not present on the local filesystem (usually due to replication plugins),
 * then the url of the file is returned if url fopen is supported.
 *
 * @since 3.4.0
 * @access private
 *
 * @param string $attachment_id Attachment ID.
 * @param string $size Optional. Image size, defaults to 'full'.
 * @return string|false File path or url on success, false on failure.
 */
function _load_image_to_edit_path( $attachment_id, $size = 'full' ) {
	$filepath = get_attached_file( $attachment_id );

	if ( $filepath && file_exists( $filepath ) ) {
		if ( 'full' != $size && ( $data = image_get_intermediate_size( $attachment_id, $size ) ) ) {
			$filepath = apply_filters( 'load_image_to_edit_filesystempath', path_join( dirname( $filepath ), $data['file'] ), $attachment_id, $size );
		}
	} elseif ( function_exists( 'fopen' ) && function_exists( 'ini_get' ) && true == ini_get( 'allow_url_fopen' ) ) {
		$filepath = apply_filters( 'load_image_to_edit_attachmenturl', wp_get_attachment_url( $attachment_id ), $attachment_id, $size );
	}

	return apply_filters( 'load_image_to_edit_path', $filepath, $attachment_id, $size );
}

/**
 * Copy an existing image file.
 *
 * @since 3.4.0
 * @access private
 *
 * @param string $attachment_id Attachment ID.
 * @return string|false New file path on success, false on failure.
 */
function _copy_image_file( $attachment_id ) {
	$dst_file = $src_file = get_attached_file( $attachment_id );
	if ( ! file_exists( $src_file ) )
		$src_file = _load_image_to_edit_path( $attachment_id );

	if ( $src_file ) {
		$dst_file = str_replace( basename( $dst_file ), 'copy-' . basename( $dst_file ), $dst_file );
		$dst_file = dirname( $dst_file ) . '/' . wp_unique_filename( dirname( $dst_file ), basename( $dst_file ) );

		// The directory containing the original file may no longer exist when
		// using a replication plugin.
		wp_mkdir_p( dirname( $dst_file ) );

		if ( ! @copy( $src_file, $dst_file ) )
			$dst_file = false;
	} else {
		$dst_file = false;
	}

	return $dst_file;
}
