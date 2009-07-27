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
 * @return string Thumbnail path on success, Error string on failure.
 */
function wp_create_thumbnail( $file, $max_side, $deprecated = '' ) {
	$thumbpath = image_resize( $file, $max_side, $max_side );
	return apply_filters( 'wp_create_thumbnail', $thumbpath );
}

/**
 * Crop an Image to a given size.
 *
 * @since 2.1.0
 *
 * @param string|int $src_file The source file or Attachment ID.
 * @param int $src_x The start x position to crop from.
 * @param int $src_y The start y position to crop from.
 * @param int $src_w The width to crop.
 * @param int $src_h The height to crop.
 * @param int $dst_w The destination width.
 * @param int $dst_h The destination height.
 * @param int $src_abs Optional. If the source crop points are absolute.
 * @param string $dst_file Optional. The destination file to write to.
 * @return string New filepath on success, String error message on failure.
 */
function wp_crop_image( $src_file, $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs = false, $dst_file = false ) {
	if ( is_numeric( $src_file ) ) // Handle int as attachment ID
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

	if ( imagejpeg( $dst, $dst_file, apply_filters( 'jpeg_quality', 90, 'wp_crop_image' ) ) )
		return $dst_file;
	else
		return false;
}

/**
 * Generate post image attachment meta data.
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
		$full_path_file = $file;
		$imagesize = getimagesize( $full_path_file );
		$metadata['width'] = $imagesize[0];
		$metadata['height'] = $imagesize[1];
		list($uwidth, $uheight) = wp_shrink_dimensions($metadata['width'], $metadata['height']);
		$metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";

		// Make the file path relative to the upload dir
		if ( ($uploads = wp_upload_dir()) && false === $uploads['error'] ) { // Get upload directory
			if ( 0 === strpos($file, $uploads['basedir']) ) {// Check that the upload base exists in the file path
				$file = str_replace($uploads['basedir'], '', $file); // Remove upload dir from the file path
				$file = ltrim($file, '/');
			}
		}
		$metadata['file'] = $file;

		// make thumbnails and other intermediate sizes
		$sizes = array('thumbnail', 'medium', 'large');
		$sizes = apply_filters('intermediate_image_sizes', $sizes);

		foreach ($sizes as $size) {
			$resized = image_make_intermediate_size( $full_path_file, get_option("{$size}_size_w"), get_option("{$size}_size_h"), get_option("{$size}_crop") );
			if ( $resized )
				$metadata['sizes'][$size] = $resized;
		}

		// fetch additional metadata from exif/iptc
		$image_meta = wp_read_image_metadata( $full_path_file );
		if ($image_meta)
			$metadata['image_meta'] = $image_meta;

	}

	return apply_filters( 'wp_generate_attachment_metadata', $metadata, $attachment_id );
}

/**
 * Load an image from a string, if PHP supports it.
 *
 * @since 2.1.0
 *
 * @param string $file Filename of the image to load.
 * @return resource The resulting image resource on success, Error string on failure.
 */
function wp_load_image( $file ) {
	if ( is_numeric( $file ) )
		$file = get_attached_file( $file );

	if ( ! file_exists( $file ) )
		return sprintf(__('File &#8220;%s&#8221; doesn&#8217;t exist?'), $file);

	if ( ! function_exists('imagecreatefromstring') )
		return __('The GD image library is not installed.');

	// Set artificially high because GD uses uncompressed images in memory
	@ini_set('memory_limit', '256M');
	$image = imagecreatefromstring( file_get_contents( $file ) );

	if ( !is_resource( $image ) )
		return sprintf(__('File &#8220;%s&#8221; is not an image.'), $file);

	return $image;
}

/**
 * Calculated the new dimentions for a downsampled image.
 *
 * @since 2.0.0
 * @see wp_shrink_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @return mixed Array(height,width) of shrunk dimensions.
 */
function get_udims( $width, $height) {
	return wp_shrink_dimensions( $width, $height );
}

/**
 * Calculates the new dimentions for a downsampled image.
 *
 * @since 2.0.0
 * @see wp_constrain_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @param int $wmax Maximum wanted width
 * @param int $hmax Maximum wanted height
 * @return mixed Array(height,width) of shrunk dimensions.
 */
function wp_shrink_dimensions( $width, $height, $wmax = 128, $hmax = 96 ) {
	return wp_constrain_dimensions( $width, $height, $wmax, $hmax );
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
	if ( !file_exists( $file ) )
		return false;

	list(,,$sourceImageType) = getimagesize( $file );

	// exif contains a bunch of data we'll probably never need formatted in ways
	// that are difficult to use. We'll normalize it and just extract the fields
	// that are likely to be useful.  Fractions and numbers are converted to
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
	if ( is_callable('iptcparse') ) {
		getimagesize($file, $info);
		if ( !empty($info['APP13']) ) {
			$iptc = iptcparse($info['APP13']);
			if ( !empty($iptc['2#110'][0]) ) // credit
				$meta['credit'] = utf8_encode(trim($iptc['2#110'][0]));
			elseif ( !empty($iptc['2#080'][0]) ) // byline
				$meta['credit'] = utf8_encode(trim($iptc['2#080'][0]));
			if ( !empty($iptc['2#055'][0]) and !empty($iptc['2#060'][0]) ) // created date and time
				$meta['created_timestamp'] = strtotime($iptc['2#055'][0] . ' ' . $iptc['2#060'][0]);
			if ( !empty($iptc['2#120'][0]) ) // caption
				$meta['caption'] = utf8_encode(trim($iptc['2#120'][0]));
			if ( !empty($iptc['2#116'][0]) ) // copyright
				$meta['copyright'] = utf8_encode(trim($iptc['2#116'][0]));
			if ( !empty($iptc['2#005'][0]) ) // title
				$meta['title'] = utf8_encode(trim($iptc['2#005'][0]));
		 }
	}

	// fetch additional info from exif if available
	if ( is_callable('exif_read_data') && in_array($sourceImageType, apply_filters('wp_read_image_metadata_types', array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM)) ) ) {
		$exif = @exif_read_data( $file );
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

?>
