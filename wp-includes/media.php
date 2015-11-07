<?php
/**
 * WordPress API for media display.
 *
 * @package WordPress
 * @subpackage Media
 */

/**
 * Scale down the default size of an image.
 *
 * This is so that the image is a better fit for the editor and theme.
 *
 * The `$size` parameter accepts either an array or a string. The supported string
 * values are 'thumb' or 'thumbnail' for the given thumbnail size or defaults at
 * 128 width and 96 height in pixels. Also supported for the string value is
 * 'medium', 'medium_large' and 'full'. The 'full' isn't actually supported, but any value other
 * than the supported will result in the content_width size or 500 if that is
 * not set.
 *
 * Finally, there is a filter named {@see 'editor_max_image_size'}, that will be
 * called on the calculated array for width and height, respectively. The second
 * parameter will be the value that was in the $size parameter. The returned
 * type for the hook is an array with the width as the first element and the
 * height as the second element.
 *
 * @since 2.5.0
 *
 * @global int   $content_width
 * @global array $_wp_additional_image_sizes
 *
 * @param int          $width   Width of the image in pixels.
 * @param int          $height  Height of the image in pixels.
 * @param string|array $size    Optional. Image size. Accepts any valid image size, or an array
 *                              of width and height values in pixels (in that order).
 *                              Default 'medium'.
 * @param string       $context Optional. Could be 'display' (like in a theme) or 'edit'
 *                              (like inserting into an editor). Default null.
 * @return array Width and height of what the result image should resize to.
 */
function image_constrain_size_for_editor( $width, $height, $size = 'medium', $context = null ) {
	global $content_width, $_wp_additional_image_sizes;

	if ( ! $context )
		$context = is_admin() ? 'edit' : 'display';

	if ( is_array($size) ) {
		$max_width = $size[0];
		$max_height = $size[1];
	}
	elseif ( $size == 'thumb' || $size == 'thumbnail' ) {
		$max_width = intval(get_option('thumbnail_size_w'));
		$max_height = intval(get_option('thumbnail_size_h'));
		// last chance thumbnail size defaults
		if ( !$max_width && !$max_height ) {
			$max_width = 128;
			$max_height = 96;
		}
	}
	elseif ( $size == 'medium' ) {
		$max_width = intval(get_option('medium_size_w'));
		$max_height = intval(get_option('medium_size_h'));

	}
	elseif ( $size == 'medium_large' ) {
		$max_width = intval( get_option( 'medium_large_size_w' ) );
		$max_height = intval( get_option( 'medium_large_size_h' ) );

		if ( intval( $content_width ) > 0 ) {
			$max_width = min( intval( $content_width ), $max_width );
		}
	}
	elseif ( $size == 'large' ) {
		/*
		 * We're inserting a large size image into the editor. If it's a really
		 * big image we'll scale it down to fit reasonably within the editor
		 * itself, and within the theme's content width if it's known. The user
		 * can resize it in the editor if they wish.
		 */
		$max_width = intval(get_option('large_size_w'));
		$max_height = intval(get_option('large_size_h'));
		if ( intval($content_width) > 0 ) {
			$max_width = min( intval($content_width), $max_width );
		}
	} elseif ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
		$max_width = intval( $_wp_additional_image_sizes[$size]['width'] );
		$max_height = intval( $_wp_additional_image_sizes[$size]['height'] );
		if ( intval($content_width) > 0 && 'edit' == $context ) // Only in admin. Assume that theme authors know what they're doing.
			$max_width = min( intval($content_width), $max_width );
	}
	// $size == 'full' has no constraint
	else {
		$max_width = $width;
		$max_height = $height;
	}

	/**
	 * Filter the maximum image size dimensions for the editor.
	 *
	 * @since 2.5.0
	 *
	 * @param array        $max_image_size An array with the width as the first element,
	 *                                     and the height as the second element.
	 * @param string|array $size           Size of what the result image should be.
	 * @param string       $context        The context the image is being resized for.
	 *                                     Possible values are 'display' (like in a theme)
	 *                                     or 'edit' (like inserting into an editor).
	 */
	list( $max_width, $max_height ) = apply_filters( 'editor_max_image_size', array( $max_width, $max_height ), $size, $context );

	return wp_constrain_dimensions( $width, $height, $max_width, $max_height );
}

/**
 * Retrieve width and height attributes using given width and height values.
 *
 * Both attributes are required in the sense that both parameters must have a
 * value, but are optional in that if you set them to false or null, then they
 * will not be added to the returned string.
 *
 * You can set the value using a string, but it will only take numeric values.
 * If you wish to put 'px' after the numbers, then it will be stripped out of
 * the return.
 *
 * @since 2.5.0
 *
 * @param int|string $width  Image width in pixels.
 * @param int|string $height Image height in pixels.
 * @return string HTML attributes for width and, or height.
 */
function image_hwstring( $width, $height ) {
	$out = '';
	if ($width)
		$out .= 'width="'.intval($width).'" ';
	if ($height)
		$out .= 'height="'.intval($height).'" ';
	return $out;
}

/**
 * Scale an image to fit a particular size (such as 'thumb' or 'medium').
 *
 * Array with image url, width, height, and whether is intermediate size, in
 * that order is returned on success is returned. $is_intermediate is true if
 * $url is a resized image, false if it is the original.
 *
 * The URL might be the original image, or it might be a resized version. This
 * function won't create a new resized copy, it will just return an already
 * resized one if it exists.
 *
 * A plugin may use the 'image_downsize' filter to hook into and offer image
 * resizing services for images. The hook must return an array with the same
 * elements that are returned in the function. The first element being the URL
 * to the new image that was resized.
 *
 * @since 2.5.0
 *
 * @param int          $id   Attachment ID for image.
 * @param array|string $size Optional. Image size to scale to. Accepts any valid image size,
 *                           or an array of width and height values in pixels (in that order).
 *                           Default 'medium'.
 * @return false|array Array containing the image URL, width, height, and boolean for whether
 *                     the image is an intermediate size. False on failure.
 */
function image_downsize( $id, $size = 'medium' ) {

	if ( !wp_attachment_is_image($id) )
		return false;

	/**
	 * Filter whether to preempt the output of image_downsize().
	 *
	 * Passing a truthy value to the filter will effectively short-circuit
	 * down-sizing the image, returning that value as output instead.
	 *
	 * @since 2.5.0
	 *
	 * @param bool         $downsize Whether to short-circuit the image downsize. Default false.
	 * @param int          $id       Attachment ID for image.
	 * @param array|string $size     Size of image. Image size or array of width and height values (in that order).
	 *                               Default 'medium'.
	 */
	if ( $out = apply_filters( 'image_downsize', false, $id, $size ) ) {
		return $out;
	}

	$img_url = wp_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);
	$width = $height = 0;
	$is_intermediate = false;
	$img_url_basename = wp_basename($img_url);

	// try for a new style intermediate size
	if ( $intermediate = image_get_intermediate_size($id, $size) ) {
		$img_url = str_replace($img_url_basename, $intermediate['file'], $img_url);
		$width = $intermediate['width'];
		$height = $intermediate['height'];
		$is_intermediate = true;
	}
	elseif ( $size == 'thumbnail' ) {
		// fall back to the old thumbnail
		if ( ($thumb_file = wp_get_attachment_thumb_file($id)) && $info = getimagesize($thumb_file) ) {
			$img_url = str_replace($img_url_basename, wp_basename($thumb_file), $img_url);
			$width = $info[0];
			$height = $info[1];
			$is_intermediate = true;
		}
	}
	if ( !$width && !$height && isset( $meta['width'], $meta['height'] ) ) {
		// any other type: use the real image
		$width = $meta['width'];
		$height = $meta['height'];
	}

	if ( $img_url) {
		// we have the actual image size, but might need to further constrain it if content_width is narrower
		list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );

		return array( $img_url, $width, $height, $is_intermediate );
	}
	return false;

}

/**
 * Register a new image size.
 *
 * Cropping behavior for the image size is dependent on the value of $crop:
 * 1. If false (default), images will be scaled, not cropped.
 * 2. If an array in the form of array( x_crop_position, y_crop_position ):
 *    - x_crop_position accepts 'left' 'center', or 'right'.
 *    - y_crop_position accepts 'top', 'center', or 'bottom'.
 *    Images will be cropped to the specified dimensions within the defined crop area.
 * 3. If true, images will be cropped to the specified dimensions using center positions.
 *
 * @since 2.9.0
 *
 * @global array $_wp_additional_image_sizes Associative array of additional image sizes.
 *
 * @param string     $name   Image size identifier.
 * @param int        $width  Image width in pixels.
 * @param int        $height Image height in pixels.
 * @param bool|array $crop   Optional. Whether to crop images to specified width and height or resize.
 *                           An array can specify positioning of the crop area. Default false.
 */
function add_image_size( $name, $width = 0, $height = 0, $crop = false ) {
	global $_wp_additional_image_sizes;

	$_wp_additional_image_sizes[ $name ] = array(
		'width'  => absint( $width ),
		'height' => absint( $height ),
		'crop'   => $crop,
	);
}

/**
 * Check if an image size exists.
 *
 * @since 3.9.0
 *
 * @global array $_wp_additional_image_sizes
 *
 * @param string $name The image size to check.
 * @return bool True if the image size exists, false if not.
 */
function has_image_size( $name ) {
	global $_wp_additional_image_sizes;

	return isset( $_wp_additional_image_sizes[ $name ] );
}

/**
 * Remove a new image size.
 *
 * @since 3.9.0
 *
 * @global array $_wp_additional_image_sizes
 *
 * @param string $name The image size to remove.
 * @return bool True if the image size was successfully removed, false on failure.
 */
function remove_image_size( $name ) {
	global $_wp_additional_image_sizes;

	if ( isset( $_wp_additional_image_sizes[ $name ] ) ) {
		unset( $_wp_additional_image_sizes[ $name ] );
		return true;
	}

	return false;
}

/**
 * Registers an image size for the post thumbnail.
 *
 * @since 2.9.0
 *
 * @see add_image_size() for details on cropping behavior.
 *
 * @param int        $width  Image width in pixels.
 * @param int        $height Image height in pixels.
 * @param bool|array $crop   Optional. Whether to crop images to specified width and height or resize.
 *                           An array can specify positioning of the crop area. Default false.
 */
function set_post_thumbnail_size( $width = 0, $height = 0, $crop = false ) {
	add_image_size( 'post-thumbnail', $width, $height, $crop );
}

/**
 * Gets an img tag for an image attachment, scaling it down if requested.
 *
 * The filter 'get_image_tag_class' allows for changing the class name for the
 * image without having to use regular expressions on the HTML content. The
 * parameters are: what WordPress will use for the class, the Attachment ID,
 * image align value, and the size the image should be.
 *
 * The second filter 'get_image_tag' has the HTML content, which can then be
 * further manipulated by a plugin to change all attribute values and even HTML
 * content.
 *
 * @since 2.5.0
 *
 * @param int          $id    Attachment ID.
 * @param string       $alt   Image Description for the alt attribute.
 * @param string       $title Image Description for the title attribute.
 * @param string       $align Part of the class name for aligning the image.
 * @param string|array $size  Optional. Registered image size to retrieve a tag for. Accepts any
 *                            valid image size, or an array of width and height values in pixels
 *                            (in that order). Default 'medium'.
 * @return string HTML IMG element for given image attachment
 */
function get_image_tag( $id, $alt, $title, $align, $size = 'medium' ) {

	list( $img_src, $width, $height ) = image_downsize($id, $size);
	$hwstring = image_hwstring($width, $height);

	$title = $title ? 'title="' . esc_attr( $title ) . '" ' : '';

	$class = 'align' . esc_attr($align) .' size-' . esc_attr($size) . ' wp-image-' . $id;

	/**
	 * Filter the value of the attachment's image tag class attribute.
	 *
	 * @since 2.6.0
	 *
	 * @param string       $class CSS class name or space-separated list of classes.
	 * @param int          $id    Attachment ID.
	 * @param string       $align Part of the class name for aligning the image.
	 * @param string|array $size  Size of image. Image size or array of width and height values (in that order).
	 *                            Default 'medium'.
	 */
	$class = apply_filters( 'get_image_tag_class', $class, $id, $align, $size );

	$html = '<img src="' . esc_attr($img_src) . '" alt="' . esc_attr($alt) . '" ' . $title . $hwstring . 'class="' . $class . '" />';

	/**
	 * Filter the HTML content for the image tag.
	 *
	 * @since 2.6.0
	 *
	 * @param string       $html  HTML content for the image.
	 * @param int          $id    Attachment ID.
	 * @param string       $alt   Alternate text.
	 * @param string       $title Attachment title.
	 * @param string       $align Part of the class name for aligning the image.
	 * @param string|array $size  Size of image. Image size or array of width and height values (in that order).
	 *                            Default 'medium'.
	 */
	return apply_filters( 'get_image_tag', $html, $id, $alt, $title, $align, $size );
}

/**
 * Calculates the new dimensions for a down-sampled image.
 *
 * If either width or height are empty, no constraint is applied on
 * that dimension.
 *
 * @since 2.5.0
 *
 * @param int $current_width  Current width of the image.
 * @param int $current_height Current height of the image.
 * @param int $max_width      Optional. Max width in pixels to constrain to. Default 0.
 * @param int $max_height     Optional. Max height in pixels to constrain to. Default 0.
 * @return array First item is the width, the second item is the height.
 */
function wp_constrain_dimensions( $current_width, $current_height, $max_width = 0, $max_height = 0 ) {
	if ( !$max_width && !$max_height )
		return array( $current_width, $current_height );

	$width_ratio = $height_ratio = 1.0;
	$did_width = $did_height = false;

	if ( $max_width > 0 && $current_width > 0 && $current_width > $max_width ) {
		$width_ratio = $max_width / $current_width;
		$did_width = true;
	}

	if ( $max_height > 0 && $current_height > 0 && $current_height > $max_height ) {
		$height_ratio = $max_height / $current_height;
		$did_height = true;
	}

	// Calculate the larger/smaller ratios
	$smaller_ratio = min( $width_ratio, $height_ratio );
	$larger_ratio  = max( $width_ratio, $height_ratio );

	if ( (int) round( $current_width * $larger_ratio ) > $max_width || (int) round( $current_height * $larger_ratio ) > $max_height ) {
 		// The larger ratio is too big. It would result in an overflow.
		$ratio = $smaller_ratio;
	} else {
		// The larger ratio fits, and is likely to be a more "snug" fit.
		$ratio = $larger_ratio;
	}

	// Very small dimensions may result in 0, 1 should be the minimum.
	$w = max ( 1, (int) round( $current_width  * $ratio ) );
	$h = max ( 1, (int) round( $current_height * $ratio ) );

	// Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
	// We also have issues with recursive calls resulting in an ever-changing result. Constraining to the result of a constraint should yield the original result.
	// Thus we look for dimensions that are one pixel shy of the max value and bump them up

	// Note: $did_width means it is possible $smaller_ratio == $width_ratio.
	if ( $did_width && $w == $max_width - 1 ) {
		$w = $max_width; // Round it up
	}

	// Note: $did_height means it is possible $smaller_ratio == $height_ratio.
	if ( $did_height && $h == $max_height - 1 ) {
		$h = $max_height; // Round it up
	}

	/**
	 * Filter dimensions to constrain down-sampled images to.
	 *
	 * @since 4.1.0
	 *
	 * @param array $dimensions     The image width and height.
	 * @param int 	$current_width  The current width of the image.
	 * @param int 	$current_height The current height of the image.
	 * @param int 	$max_width      The maximum width permitted.
	 * @param int 	$max_height     The maximum height permitted.
	 */
	return apply_filters( 'wp_constrain_dimensions', array( $w, $h ), $current_width, $current_height, $max_width, $max_height );
}

/**
 * Retrieves calculated resize dimensions for use in WP_Image_Editor.
 *
 * Calculates dimensions and coordinates for a resized image that fits
 * within a specified width and height.
 *
 * Cropping behavior is dependent on the value of $crop:
 * 1. If false (default), images will not be cropped.
 * 2. If an array in the form of array( x_crop_position, y_crop_position ):
 *    - x_crop_position accepts 'left' 'center', or 'right'.
 *    - y_crop_position accepts 'top', 'center', or 'bottom'.
 *    Images will be cropped to the specified dimensions within the defined crop area.
 * 3. If true, images will be cropped to the specified dimensions using center positions.
 *
 * @since 2.5.0
 *
 * @param int        $orig_w Original width in pixels.
 * @param int        $orig_h Original height in pixels.
 * @param int        $dest_w New width in pixels.
 * @param int        $dest_h New height in pixels.
 * @param bool|array $crop   Optional. Whether to crop image to specified width and height or resize.
 *                           An array can specify positioning of the crop area. Default false.
 * @return false|array False on failure. Returned array matches parameters for `imagecopyresampled()`.
 */
function image_resize_dimensions( $orig_w, $orig_h, $dest_w, $dest_h, $crop = false ) {

	if ($orig_w <= 0 || $orig_h <= 0)
		return false;
	// at least one of dest_w or dest_h must be specific
	if ($dest_w <= 0 && $dest_h <= 0)
		return false;

	/**
	 * Filter whether to preempt calculating the image resize dimensions.
	 *
	 * Passing a non-null value to the filter will effectively short-circuit
	 * image_resize_dimensions(), returning that value instead.
	 *
	 * @since 3.4.0
	 *
	 * @param null|mixed $null   Whether to preempt output of the resize dimensions.
	 * @param int        $orig_w Original width in pixels.
	 * @param int        $orig_h Original height in pixels.
	 * @param int        $dest_w New width in pixels.
	 * @param int        $dest_h New height in pixels.
	 * @param bool|array $crop   Whether to crop image to specified width and height or resize.
	 *                           An array can specify positioning of the crop area. Default false.
	 */
	$output = apply_filters( 'image_resize_dimensions', null, $orig_w, $orig_h, $dest_w, $dest_h, $crop );
	if ( null !== $output )
		return $output;

	if ( $crop ) {
		// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
		$aspect_ratio = $orig_w / $orig_h;
		$new_w = min($dest_w, $orig_w);
		$new_h = min($dest_h, $orig_h);

		if ( ! $new_w ) {
			$new_w = (int) round( $new_h * $aspect_ratio );
		}

		if ( ! $new_h ) {
			$new_h = (int) round( $new_w / $aspect_ratio );
		}

		$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

		$crop_w = round($new_w / $size_ratio);
		$crop_h = round($new_h / $size_ratio);

		if ( ! is_array( $crop ) || count( $crop ) !== 2 ) {
			$crop = array( 'center', 'center' );
		}

		list( $x, $y ) = $crop;

		if ( 'left' === $x ) {
			$s_x = 0;
		} elseif ( 'right' === $x ) {
			$s_x = $orig_w - $crop_w;
		} else {
			$s_x = floor( ( $orig_w - $crop_w ) / 2 );
		}

		if ( 'top' === $y ) {
			$s_y = 0;
		} elseif ( 'bottom' === $y ) {
			$s_y = $orig_h - $crop_h;
		} else {
			$s_y = floor( ( $orig_h - $crop_h ) / 2 );
		}
	} else {
		// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
		$crop_w = $orig_w;
		$crop_h = $orig_h;

		$s_x = 0;
		$s_y = 0;

		list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
	}

	// if the resulting image would be the same size or larger we don't want to resize it
	if ( $new_w >= $orig_w && $new_h >= $orig_h && $dest_w != $orig_w && $dest_h != $orig_h ) {
		return false;
	}

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

}

/**
 * Resizes an image to make a thumbnail or intermediate size.
 *
 * The returned array has the file size, the image width, and image height. The
 * filter 'image_make_intermediate_size' can be used to hook in and change the
 * values of the returned array. The only parameter is the resized file path.
 *
 * @since 2.5.0
 *
 * @param string $file   File path.
 * @param int    $width  Image width.
 * @param int    $height Image height.
 * @param bool   $crop   Optional. Whether to crop image to specified width and height or resize.
 *                       Default false.
 * @return false|array False, if no image was created. Metadata array on success.
 */
function image_make_intermediate_size( $file, $width, $height, $crop = false ) {
	if ( $width || $height ) {
		$editor = wp_get_image_editor( $file );

		if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
			return false;

		$resized_file = $editor->save();

		if ( ! is_wp_error( $resized_file ) && $resized_file ) {
			unset( $resized_file['path'] );
			return $resized_file;
		}
	}
	return false;
}

/**
 * Retrieves the image's intermediate size (resized) path, width, and height.
 *
 * The $size parameter can be an array with the width and height respectively.
 * If the size matches the 'sizes' metadata array for width and height, then it
 * will be used. If there is no direct match, then the nearest image size larger
 * than the specified size will be used. If nothing is found, then the function
 * will break out and return false.
 *
 * The metadata 'sizes' is used for compatible sizes that can be used for the
 * parameter $size value.
 *
 * The url path will be given, when the $size parameter is a string.
 *
 * If you are passing an array for the $size, you should consider using
 * add_image_size() so that a cropped version is generated. It's much more
 * efficient than having to find the closest-sized image and then having the
 * browser scale down the image.
 *
 * @since 2.5.0
 *
 * @param int          $post_id Attachment ID.
 * @param array|string $size    Optional. Image size. Accepts any valid image size, or an array
 *                              of width and height values in pixels (in that order).
 *                              Default 'thumbnail'.
 * @return false|array $data {
 *     Array of file relative path, width, and height on success. Additionally includes absolute
 *     path and URL if registered size is passed to $size parameter. False on failure.
 *
 *     @type string $file   Image's path relative to uploads directory
 *     @type int    $width  Width of image
 *     @type int    $height Height of image
 *     @type string $path   Optional. Image's absolute filesystem path. Only returned if registered
 *                          size is passed to `$size` parameter.
 *     @type string $url    Optional. Image's URL. Only returned if registered size is passed to `$size`
 *                          parameter.
 * }
 */
function image_get_intermediate_size( $post_id, $size = 'thumbnail' ) {
	if ( !is_array( $imagedata = wp_get_attachment_metadata( $post_id ) ) )
		return false;

	// get the best one for a specified set of dimensions
	if ( is_array($size) && !empty($imagedata['sizes']) ) {
		$candidates = array();

		foreach ( $imagedata['sizes'] as $_size => $data ) {
			// If there's an exact match to an existing image size, short circuit.
			if ( $data['width'] == $size[0] && $data['height'] == $size[1] ) {
				list( $data['width'], $data['height'] ) = image_constrain_size_for_editor( $data['width'], $data['height'], $size );

				/** This filter is documented in wp-includes/media.php */
				return apply_filters( 'image_get_intermediate_size', $data, $post_id, $size );
			}
			// If it's not an exact match but it's at least the dimensions requested.
			if ( $data['width'] >= $size[0] && $data['height'] >= $size[1] ) {
				$candidates[ $data['width'] * $data['height'] ] = $_size;
			}
		}

		if ( ! empty( $candidates ) ) {
			// find for the smallest image not smaller than the desired size
			ksort( $candidates );
			foreach ( $candidates as $_size ) {
				$data = $imagedata['sizes'][$_size];

				// Skip images with unexpectedly divergent aspect ratios (crops)
				// First, we calculate what size the original image would be if constrained to a box the size of the current image in the loop
				$maybe_cropped = image_resize_dimensions($imagedata['width'], $imagedata['height'], $data['width'], $data['height'], false );
				// If the size doesn't match within one pixel, then it is of a different aspect ratio, so we skip it, unless it's the thumbnail size
				if ( 'thumbnail' != $_size &&
				  ( ! $maybe_cropped
				    || ( $maybe_cropped[4] != $data['width'] && $maybe_cropped[4] + 1 != $data['width'] )
				    || ( $maybe_cropped[5] != $data['height'] && $maybe_cropped[5] + 1 != $data['height'] )
				  ) ) {
				  continue;
				}
				// If we're still here, then we're going to use this size.
				list( $data['width'], $data['height'] ) = image_constrain_size_for_editor( $data['width'], $data['height'], $size );

				/** This filter is documented in wp-includes/media.php */
				return apply_filters( 'image_get_intermediate_size', $data, $post_id, $size );
			}
		}
	}

	if ( is_array($size) || empty($size) || empty($imagedata['sizes'][$size]) )
		return false;

	$data = $imagedata['sizes'][$size];
	// include the full filesystem path of the intermediate file
	if ( empty($data['path']) && !empty($data['file']) ) {
		$file_url = wp_get_attachment_url($post_id);
		$data['path'] = path_join( dirname($imagedata['file']), $data['file'] );
		$data['url'] = path_join( dirname($file_url), $data['file'] );
	}

	/**
	 * Filter the output of image_get_intermediate_size()
	 *
	 * @since 4.4.0
	 *
	 * @see image_get_intermediate_size()
	 *
	 * @param array        $data    Array of file relative path, width, and height on success. May also include
	 *                              file absolute path and URL.
	 * @param int          $post_id The post_id of the image attachment
	 * @param string|array $size    Registered image size or flat array of initially-requested height and width
	 *                              dimensions (in that order).
	 */
	return apply_filters( 'image_get_intermediate_size', $data, $post_id, $size );
}

/**
 * Gets the available intermediate image sizes.
 *
 * @since 3.0.0
 *
 * @global array $_wp_additional_image_sizes
 *
 * @return array Returns a filtered array of image size strings.
 */
function get_intermediate_image_sizes() {
	global $_wp_additional_image_sizes;
	$image_sizes = array('thumbnail', 'medium', 'medium_large', 'large'); // Standard sizes
	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) )
		$image_sizes = array_merge( $image_sizes, array_keys( $_wp_additional_image_sizes ) );

	/**
	 * Filter the list of intermediate image sizes.
	 *
	 * @since 2.5.0
	 *
	 * @param array $image_sizes An array of intermediate image sizes. Defaults
	 *                           are 'thumbnail', 'medium', 'medium_large', 'large'.
	 */
	return apply_filters( 'intermediate_image_sizes', $image_sizes );
}

/**
 * Retrieve an image to represent an attachment.
 *
 * A mime icon for files, thumbnail or intermediate size for images.
 *
 * The returned array contains four values: the URL of the attachment image src,
 * the width of the image file, the height of the image file, and a boolean
 * representing whether the returned array describes an intermediate (generated)
 * image size or the original, full-sized upload.
 *
 * @since 2.5.0
 *
 * @param int          $attachment_id Image attachment ID.
 * @param string|array $size          Optional. Image size. Accepts any valid image size, or an array of width
 *                                    and height values in pixels (in that order). Default 'thumbnail'.
 * @param bool         $icon          Optional. Whether the image should be treated as an icon. Default false.
 * @return false|array Returns an array (url, width, height, is_intermediate), or false, if no image is available.
 */
function wp_get_attachment_image_src( $attachment_id, $size = 'thumbnail', $icon = false ) {
	// get a thumbnail or intermediate image if there is one
	$image = image_downsize( $attachment_id, $size );
	if ( ! $image ) {
		$src = false;

		if ( $icon && $src = wp_mime_type_icon( $attachment_id ) ) {
			/** This filter is documented in wp-includes/post-functions.php */
			$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/media' );

			$src_file = $icon_dir . '/' . wp_basename( $src );
			@list( $width, $height ) = getimagesize( $src_file );
		}

		if ( $src && $width && $height ) {
			$image = array( $src, $width, $height );
		}
	}
	/**
	 * Filter the image src result.
	 *
	 * @since 4.3.0
	 *
	 * @param array|false  $image         Either array with src, width & height, icon src, or false.
	 * @param int          $attachment_id Image attachment ID.
	 * @param string|array $size          Size of image. Image size or array of width and height values
	 *                                    (in that order). Default 'thumbnail'.
	 * @param bool         $icon          Whether the image should be treated as an icon. Default false.
	 */
	return apply_filters( 'wp_get_attachment_image_src', $image, $attachment_id, $size, $icon );
}

/**
 * Get an HTML img element representing an image attachment
 *
 * While `$size` will accept an array, it is better to register a size with
 * add_image_size() so that a cropped version is generated. It's much more
 * efficient than having to find the closest-sized image and then having the
 * browser scale down the image.
 *
 * @since 2.5.0
 *
 * @param int          $attachment_id Image attachment ID.
 * @param string|array $size          Optional. Image size. Accepts any valid image size, or an array of width
 *                                    and height values in pixels (in that order). Default 'thumbnail'.
 * @param bool         $icon          Optional. Whether the image should be treated as an icon. Default false.
 * @param string|array $attr          Optional. Attributes for the image markup. Default empty.
 * @return string HTML img element or empty string on failure.
 */
function wp_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = false, $attr = '') {
	$html = '';
	$image = wp_get_attachment_image_src($attachment_id, $size, $icon);
	if ( $image ) {
		list($src, $width, $height) = $image;
		$hwstring = image_hwstring($width, $height);
		$size_class = $size;
		if ( is_array( $size_class ) ) {
			$size_class = join( 'x', $size_class );
		}
		$attachment = get_post($attachment_id);
		$default_attr = array(
			'src'	=> $src,
			'class'	=> "attachment-$size_class size-$size_class",
			'alt'	=> trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) )), // Use Alt field first
		);
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_title )); // Finally, use the title

		$attr = wp_parse_args( $attr, $default_attr );

		// Generate 'srcset' and 'sizes' if not already present.
		if ( empty( $attr['srcset'] ) ) {
			$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );

			if ( is_array( $image_meta ) ) {
				$size_array = array( absint( $width ), absint( $height ) );
				$srcset = wp_calculate_image_srcset( $src, $size_array, $image_meta, $attachment_id );
				$sizes = wp_get_attachment_image_sizes( $size_array, $image_meta, $attachment_id, $src );

				if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
					$attr['srcset'] = $srcset;

					if ( empty( $attr['sizes'] ) ) {
						$attr['sizes'] = $sizes;
					}
				}
			}
		}

		/**
		 * Filter the list of attachment image attributes.
		 *
		 * @since 2.8.0
		 *
		 * @param array        $attr       Attributes for the image markup.
		 * @param WP_Post      $attachment Image attachment post.
		 * @param string|array $size       Requested size. Image size or array of width and height values
		 *                                 (in that order). Default 'thumbnail'.
		 */
		$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment, $size );
		$attr = array_map( 'esc_attr', $attr );
		$html = rtrim("<img $hwstring");
		foreach ( $attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';
	}

	return $html;
}

/**
 * Get the URL of an image attachment.
 *
 * @since 4.4.0
 *
 * @param int          $attachment_id Image attachment ID.
 * @param string|array $size          Optional. Image size to retrieve. Accepts any valid image size, or an array
 *                                    of width and height values in pixels (in that order). Default 'thumbnail'.
 * @param bool         $icon          Optional. Whether the image should be treated as an icon. Default false.
 * @return string|false Attachment URL or false if no image is available.
 */
function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
	$image = wp_get_attachment_image_src( $attachment_id, $size, $icon );
	return isset( $image['0'] ) ? $image['0'] : false;
}

/**
 * Caches and returns the base URL of the uploads directory.
 *
 * @since 4.4.0
 * @access private
 *
 * @return string The base URL, cached.
 */
function _wp_upload_dir_baseurl() {
	static $baseurl = array();

	$blog_id = get_current_blog_id();

	if ( empty( $baseurl[$blog_id] ) ) {
		$uploads_dir = wp_upload_dir();
		$baseurl[$blog_id] = $uploads_dir['baseurl'];
	}

	return $baseurl[$blog_id];
}

/**
 * Get the image size as array from its meta data.
 *
 * Used for responsive images.
 *
 * @since 4.4.0
 * @access private
 *
 * @param string $size_name  Image size. Accepts any valid image size name ('thumbnail', 'medium', etc.).
 * @param array  $image_meta The image meta data.
 * @return array|bool Array of width and height values in pixels (in that order)
 *                    or false if the size doesn't exist.
 */
function _wp_get_image_size_from_meta( $size_name, $image_meta ) {
	if ( $size_name === 'full' ) {
		return array(
			absint( $image_meta['width'] ),
			absint( $image_meta['height'] ),
		);
	} elseif ( ! empty( $image_meta['sizes'][$size_name] ) ) {
		return array(
			absint( $image_meta['sizes'][$size_name]['width'] ),
			absint( $image_meta['sizes'][$size_name]['height'] ),
		);
	}

	return false;
}

/**
 * Retrieves the value for an image attachment's 'srcset' attribute.
 *
 * @since 4.4.0
 *
 * @param int          $attachment_id Image attachment ID.
 * @param array|string $size          Image size. Accepts any valid image size, or an array of width and height
 *                                    values in pixels (in that order). Default 'medium'.
 * @param array        $image_meta    Optional. The image meta data as returned by 'wp_get_attachment_metadata()'.
 * @return string|bool A 'srcset' value string or false.
 */
function wp_get_attachment_image_srcset( $attachment_id, $size = 'medium', $image_meta = null ) {
	if ( ! $image = wp_get_attachment_image_src( $attachment_id, $size ) ) {
		return false;
	}

	if ( ! is_array( $image_meta ) ) {
		$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
	}

	$image_src = $image[0];
	$size_array = array(
		absint( $image[1] ),
		absint( $image[2] )
	);

	return wp_calculate_image_srcset( $image_src, $size_array, $image_meta, $attachment_id );
}

/**
 * A helper function to calculate the image sources to include in a 'srcset' attribute.
 *
 * @since 4.4.0
 *
 * @param string $image_src     The 'src' of the image.
 * @param array  $size_array    Array of width and height values in pixels (in that order).
 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
 * @param int    $attachment_id Optional. The image attachment ID to pass to the filter.
 * @return string|bool          The 'srcset' attribute value. False on error or when only one source exists.
 */
function wp_calculate_image_srcset( $image_src, $size_array, $image_meta, $attachment_id = 0 ) {
	if ( empty( $image_meta['sizes'] ) ) {
		return false;
	}

	$image_sizes = $image_meta['sizes'];

	// Get the width and height of the image.
	$image_width = (int) $size_array[0];
	$image_height = (int) $size_array[1];

	// Bail early if error/no width.
	if ( $image_width < 1 ) {
		return false;
	}

	$image_basename = wp_basename( $image_meta['file'] );
	$image_baseurl = _wp_upload_dir_baseurl();

	/*
	 * WordPress flattens animated GIFs into one frame when generating intermediate sizes.
	 * To avoid hiding animation in user content, if src is a full size GIF, a srcset attribute is not generated.
	 * If src is an intermediate size GIF, the full size is excluded from srcset to keep a flattened GIF from becoming animated.
	 */
	if ( ! isset( $image_sizes['thumbnail']['mime-type'] ) || 'image/gif' !== $image_sizes['thumbnail']['mime-type'] ) {
		$image_sizes['full'] = array(
			'width'  => $image_meta['width'],
			'height' => $image_meta['height'],
			'file'   => $image_basename,
		);
	} elseif ( strpos( $image_src, $image_meta['file'] ) ) {
		return false;
	}

	// Uploads are (or have been) in year/month sub-directories.
	if ( $image_basename !== $image_meta['file'] ) {
		$dirname = dirname( $image_meta['file'] );

		if ( $dirname !== '.' ) {
			$image_baseurl = trailingslashit( $image_baseurl ) . $dirname;
		}
	}

	$image_baseurl = trailingslashit( $image_baseurl );

	// Calculate the image aspect ratio.
	$image_ratio = $image_height / $image_width;

	/*
	 * Images that have been edited in WordPress after being uploaded will
	 * contain a unique hash. Look for that hash and use it later to filter
	 * out images that are leftovers from previous versions.
	 */
	$image_edited = preg_match( '/-e[0-9]{13}/', wp_basename( $image_src ), $image_edit_hash );

	/**
	 * Filter the maximum image width to be included in a 'srcset' attribute.
	 *
	 * @since 4.4.0
	 *
	 * @param int   $max_width  The maximum image width to be included in the 'srcset'. Default '1600'.
	 * @param array $size_array Array of width and height values in pixels (in that order).
	 */
	$max_srcset_image_width = apply_filters( 'max_srcset_image_width', 1600, $size_array );

	// Array to hold URL candidates.
	$sources = array();

	/*
	 * Loop through available images. Only use images that are resized
	 * versions of the same edit.
	 */
	foreach ( $image_sizes as $image ) {

		// Filter out images that are from previous edits.
		if ( $image_edited && ! strpos( $image['file'], $image_edit_hash[0] ) ) {
			continue;
		}

		// Filter out images that are wider than '$max_srcset_image_width'.
		if ( $max_srcset_image_width && $image['width'] > $max_srcset_image_width ) {
			continue;
		}

		// Calculate the new image ratio.
		if ( $image['width'] ) {
			$image_ratio_compare = $image['height'] / $image['width'];
		} else {
			$image_ratio_compare = 0;
		}

		// If the new ratio differs by less than 0.01, use it.
		if ( abs( $image_ratio - $image_ratio_compare ) < 0.01 ) {
			// Add the URL, descriptor, and value to the sources array to be returned.
			$sources[ $image['width'] ] = array(
				'url'        => $image_baseurl . $image['file'],
				'descriptor' => 'w',
				'value'      => $image['width'],
			);
		}
	}

	/**
	 * Filter the output of 'wp_calculate_image_srcset()'.
	 *
	 * @since 4.4.0
	 *
	 * @param array $sources       An array of sources to include in the 'srcset'. Each source
	 *                             consists of an array containing the URL and the descriptor
	 *                             type and value (default: the image width):
	 *
	 *                             image width => array(
	 *                                 'url'        => string,
	 *                                 'descriptor' => string ('w' or 'x'),
	 *                                 'value'      => integer (width or pixel density)
	 *                             },
	 *
	 * @param int   $attachment_id Image attachment ID.
	 * @param array $size_array    Array of width and height values in pixels (in that order).
	 * @param array $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.

	 */
	$sources = apply_filters( 'wp_calculate_image_srcset', $sources, $attachment_id, $size_array, $image_meta );

	// Only return a 'srcset' value if there is more than one source.
	if ( count( $sources ) < 2 ) {
		return false;
	}

	$srcset = '';

	foreach ( $sources as $source ) {
		$srcset .= $source['url'] . ' ' . $source['value'] . $source['descriptor'] . ', ';
	}

	return rtrim( $srcset, ', ' );
}

/**
 * Create 'sizes' attribute value for an image.
 *
 * @since 4.4.0
 *
 * @param array|string $size          Image size. Accepts any valid image size name ('thumbnail', 'medium', etc.),
 *                                    or an array of width and height values in pixels (in that order).
 * @param array        $image_meta    Optional. The image meta data as returned by 'wp_get_attachment_metadata()'.
 * @param int          $attachment_id Optional. Image attachment ID. Either `$image_meta` or `$attachment_id` is needed
 *                                    when using the image size name as argument for `$size`.
 * @param string       $image_src     Optional. The URL to the image file.
 *
 * @return string|bool A valid source size value for use in a 'sizes' attribute or false.
 */
function wp_get_attachment_image_sizes( $size, $image_meta = null, $attachment_id = 0, $image_src = null ) {
	$width = 0;

	if ( is_array( $size ) ) {
		$width = absint( $size[0] );
	} elseif ( is_string( $size ) ) {
		if ( ! $image_meta && $attachment_id ) {
			$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
		}

		if ( is_array( $image_meta ) ) {
			$size_array = _wp_get_image_size_from_meta( $size, $image_meta );
			if ( $size_array ) {
				$width = absint( $size_array[0] );
			}
		}
	}

	if ( ! $width ) {
		return false;
	}

	// Setup the default 'sizes' attribute.
	$sizes = sprintf( '(max-width: %1$dpx) 100vw, %1$dpx', $width );

	/**
	 * Filter the output of 'wp_get_attachment_image_sizes()'.
	 *
	 * @since 4.4.0
	 *
	 * @param string       $sizes         A source size value for use in a 'sizes' attribute.
	 * @param array|string $size          Image size. Image size name, or an array of width and height
	 *                                    values in pixels (in that order).
	 * @param array        $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param int          $attachment_id Image attachment ID of the original image.
	 * @param string       $image_src     Optional. The URL to the image file.
	 */
	return apply_filters( 'wp_get_attachment_image_sizes', $sizes, $size, $image_meta, $attachment_id, $image_src );
}

/**
 * Filters 'img' elements in post content to add 'srcset' and 'sizes' attributes.
 *
 * @since 4.4.0
 *
 * @see 'wp_image_add_srcset_and_sizes()'
 *
 * @param string $content The raw post content to be filtered.
 * @return string Converted content with 'srcset' and 'sizes' attributes added to images.
 */
function wp_make_content_images_responsive( $content ) {
	$images = get_media_embedded_in_content( $content, 'img' );

	$selected_images = $attachment_ids = array();

	foreach( $images as $image ) {
		if ( false === strpos( $image, ' srcset="' ) && preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) &&
			( $attachment_id = absint( $class_id[1] ) ) ) {

			/*
			 * If exactly the same image tag is used more than once, overwrite it.
			 * All identical tags will be replaced later with 'str_replace()'.
			 */
			$selected_images[ $image ] = $attachment_id;
			// Overwrite the ID when the same image is included more than once.
			$attachment_ids[ $attachment_id ] = true;
		}
	}

	if ( count( $attachment_ids ) > 1 ) {
		/*
		 * Warm object cache for use with 'get_post_meta()'.
		 *
		 * To avoid making a database call for each image, a single query
		 * warms the object cache with the meta information for all images.
		 */
		update_meta_cache( 'post', array_keys( $attachment_ids ) );
	}

	foreach ( $selected_images as $image => $attachment_id ) {
		$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
		$content = str_replace( $image, wp_image_add_srcset_and_sizes( $image, $image_meta, $attachment_id ), $content );
	}

	return $content;
}

/**
 * Adds 'srcset' and 'sizes' attributes to an existing 'img' element.
 *
 * @since 4.4.0
 *
 * @see 'wp_get_attachment_image_srcset()'
 * @see 'wp_get_attachment_image_sizes()'
 *
 * @param string $image         An HTML 'img' element to be filtered.
 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
 * @param int    $attachment_id Image attachment ID.
 * @return string Converted 'img' element with 'srcset' and 'sizes' attributes added.
 */
function wp_image_add_srcset_and_sizes( $image, $image_meta, $attachment_id ) {
	// Ensure the image meta exists.
	if ( empty( $image_meta['sizes'] ) ) {
		return $image;
	}

	$src = preg_match( '/src="([^"]+)"/', $image, $match_src ) ? $match_src[1] : '';
	list( $src ) = explode( '?', $src );

	// Return early if we couldn't get the image source.
	if ( ! $src ) {
		return $image;
	}

	// Bail early if an image has been inserted and later edited.
	if ( preg_match( '/-e[0-9]{13}/', $image_meta['file'], $img_edit_hash ) &&
		strpos( wp_basename( $src ), $img_edit_hash[0] ) === false ) {

		return $image;
	}

	$width  = preg_match( '/ width="([0-9]+)"/',  $image, $match_width  ) ? (int) $match_width[1]  : 0;
	$height = preg_match( '/ height="([0-9]+)"/', $image, $match_height ) ? (int) $match_height[1] : 0;

	if ( ! $width || ! $height ) {
		/*
		 * If attempts to parse the size value failed, attempt to use the image meta data to match
		 * the image file name from 'src' against the available sizes for an attachment.
		 */
		$image_filename = wp_basename( $src );

		if ( $image_filename === wp_basename( $image_meta['file'] ) ) {
			$width = (int) $image_meta['width'];
			$height = (int) $image_meta['height'];
		} else {
			foreach( $image_meta['sizes'] as $image_size_data ) {
				if ( $image_filename === $image_size_data['file'] ) {
					$width = (int) $image_size_data['width'];
					$height = (int) $image_size_data['height'];
					break;
				}
			}
		}
	}

	if ( ! $width || ! $height ) {
		return $image;
	}

	$size_array = array( $width, $height );
	$srcset = wp_calculate_image_srcset( $src, $size_array, $image_meta, $attachment_id );

	if ( $srcset ) {
		$sizes = wp_get_attachment_image_sizes( $size_array, $image_meta, $attachment_id, $src );
	}

	if ( $srcset && $sizes ) {
		// Format the 'srcset' and 'sizes' string and escape attributes.
		$srcset_and_sizes = sprintf( ' srcset="%s" sizes="%s"', esc_attr( $srcset ), esc_attr( $sizes ) );

		// Add 'srcset' and 'sizes' attributes to the image markup.
		$image = preg_replace( '/<img ([^>]+?)[\/ ]*>/', '<img $1' . $srcset_and_sizes . ' />', $image );
	}

	return $image;
}

/**
 * Adds a 'wp-post-image' class to post thumbnails. Internal use only.
 *
 * Uses the 'begin_fetch_post_thumbnail_html' and 'end_fetch_post_thumbnail_html' action hooks to
 * dynamically add/remove itself so as to only filter post thumbnails.
 *
 * @ignore
 * @since 2.9.0
 *
 * @param array $attr Thumbnail attributes including src, class, alt, title.
 * @return array Modified array of attributes including the new 'wp-post-image' class.
 */
function _wp_post_thumbnail_class_filter( $attr ) {
	$attr['class'] .= ' wp-post-image';
	return $attr;
}

/**
 * Adds '_wp_post_thumbnail_class_filter' callback to the 'wp_get_attachment_image_attributes'
 * filter hook. Internal use only.
 *
 * @ignore
 * @since 2.9.0
 *
 * @param array $attr Thumbnail attributes including src, class, alt, title.
 */
function _wp_post_thumbnail_class_filter_add( $attr ) {
	add_filter( 'wp_get_attachment_image_attributes', '_wp_post_thumbnail_class_filter' );
}

/**
 * Removes the '_wp_post_thumbnail_class_filter' callback from the 'wp_get_attachment_image_attributes'
 * filter hook. Internal use only.
 *
 * @ignore
 * @since 2.9.0
 *
 * @param array $attr Thumbnail attributes including src, class, alt, title.
 */
function _wp_post_thumbnail_class_filter_remove( $attr ) {
	remove_filter( 'wp_get_attachment_image_attributes', '_wp_post_thumbnail_class_filter' );
}

add_shortcode('wp_caption', 'img_caption_shortcode');
add_shortcode('caption', 'img_caption_shortcode');

/**
 * Builds the Caption shortcode output.
 *
 * Allows a plugin to replace the content that would otherwise be returned. The
 * filter is 'img_caption_shortcode' and passes an empty string, the attr
 * parameter and the content parameter values.
 *
 * The supported attributes for the shortcode are 'id', 'align', 'width', and
 * 'caption'.
 *
 * @since 2.6.0
 *
 * @param array  $attr {
 *     Attributes of the caption shortcode.
 *
 *     @type string $id      ID of the div element for the caption.
 *     @type string $align   Class name that aligns the caption. Default 'alignnone'. Accepts 'alignleft',
 *                           'aligncenter', alignright', 'alignnone'.
 *     @type int    $width   The width of the caption, in pixels.
 *     @type string $caption The caption text.
 *     @type string $class   Additional class name(s) added to the caption container.
 * }
 * @param string $content Shortcode content.
 * @return string HTML content to display the caption.
 */
function img_caption_shortcode( $attr, $content = null ) {
	// New-style shortcode with the caption inside the shortcode with the link and image tags.
	if ( ! isset( $attr['caption'] ) ) {
		if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches ) ) {
			$content = $matches[1];
			$attr['caption'] = trim( $matches[2] );
		}
	} elseif ( strpos( $attr['caption'], '<' ) !== false ) {
		$attr['caption'] = wp_kses( $attr['caption'], 'post' );
	}

	/**
	 * Filter the default caption shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating
	 * the default caption template.
	 *
	 * @since 2.6.0
	 *
	 * @see img_caption_shortcode()
	 *
	 * @param string $output  The caption output. Default empty.
	 * @param array  $attr    Attributes of the caption shortcode.
	 * @param string $content The image element, possibly wrapped in a hyperlink.
	 */
	$output = apply_filters( 'img_caption_shortcode', '', $attr, $content );
	if ( $output != '' )
		return $output;

	$atts = shortcode_atts( array(
		'id'	  => '',
		'align'	  => 'alignnone',
		'width'	  => '',
		'caption' => '',
		'class'   => '',
	), $attr, 'caption' );

	$atts['width'] = (int) $atts['width'];
	if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
		return $content;

	if ( ! empty( $atts['id'] ) )
		$atts['id'] = 'id="' . esc_attr( sanitize_html_class( $atts['id'] ) ) . '" ';

	$class = trim( 'wp-caption ' . $atts['align'] . ' ' . $atts['class'] );

	$html5 = current_theme_supports( 'html5', 'caption' );
	// HTML5 captions never added the extra 10px to the image width
	$width = $html5 ? $atts['width'] : ( 10 + $atts['width'] );

	/**
	 * Filter the width of an image's caption.
	 *
	 * By default, the caption is 10 pixels greater than the width of the image,
	 * to prevent post content from running up against a floated image.
	 *
	 * @since 3.7.0
	 *
	 * @see img_caption_shortcode()
	 *
	 * @param int    $width    Width of the caption in pixels. To remove this inline style,
	 *                         return zero.
	 * @param array  $atts     Attributes of the caption shortcode.
	 * @param string $content  The image element, possibly wrapped in a hyperlink.
	 */
	$caption_width = apply_filters( 'img_caption_shortcode_width', $width, $atts, $content );

	$style = '';
	if ( $caption_width )
		$style = 'style="width: ' . (int) $caption_width . 'px" ';

	$html = '';
	if ( $html5 ) {
		$html = '<figure ' . $atts['id'] . $style . 'class="' . esc_attr( $class ) . '">'
		. do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption></figure>';
	} else {
		$html = '<div ' . $atts['id'] . $style . 'class="' . esc_attr( $class ) . '">'
		. do_shortcode( $content ) . '<p class="wp-caption-text">' . $atts['caption'] . '</p></div>';
	}

	return $html;
}

add_shortcode('gallery', 'gallery_shortcode');

/**
 * Builds the Gallery shortcode output.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * @since 2.5.0
 *
 * @staticvar int $instance
 *
 * @param array $attr {
 *     Attributes of the gallery shortcode.
 *
 *     @type string       $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
 *     @type string       $orderby    The field to use when ordering the images. Default 'menu_order ID'.
 *                                    Accepts any valid SQL ORDERBY statement.
 *     @type int          $id         Post ID.
 *     @type string       $itemtag    HTML tag to use for each image in the gallery.
 *                                    Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
 *     @type string       $icontag    HTML tag to use for each image's icon.
 *                                    Default 'dt', or 'div' when the theme registers HTML5 gallery support.
 *     @type string       $captiontag HTML tag to use for each image's caption.
 *                                    Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
 *     @type int          $columns    Number of columns of images to display. Default 3.
 *     @type string|array $size       Size of the images to display. Accepts any valid image size, or an array of width
 *                                    and height values in pixels (in that order). Default 'thumbnail'.
 *     @type string       $ids        A comma-separated list of IDs of attachments to display. Default empty.
 *     @type string       $include    A comma-separated list of IDs of attachments to include. Default empty.
 *     @type string       $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
 *     @type string       $link       What to link each image to. Default empty (links to the attachment page).
 *                                    Accepts 'file', 'none'.
 * }
 * @return string HTML content to display gallery.
 */
function gallery_shortcode( $attr ) {
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	/**
	 * Filter the default gallery shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating
	 * the default gallery template.
	 *
	 * @since 2.5.0
	 * @since 4.2.0 The `$instance` parameter was added.
	 *
	 * @see gallery_shortcode()
	 *
	 * @param string $output   The gallery output. Default empty.
	 * @param array  $attr     Attributes of the gallery shortcode.
	 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
	 */
	$output = apply_filters( 'post_gallery', '', $attr, $instance );
	if ( $output != '' ) {
		return $output;
	}

	$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery' );

	$id = intval( $atts['id'] );

	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	} else {
		$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
		}
		return $output;
	}

	$itemtag = tag_escape( $atts['itemtag'] );
	$captiontag = tag_escape( $atts['captiontag'] );
	$icontag = tag_escape( $atts['icontag'] );
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) ) {
		$itemtag = 'dl';
	}
	if ( ! isset( $valid_tags[ $captiontag ] ) ) {
		$captiontag = 'dd';
	}
	if ( ! isset( $valid_tags[ $icontag ] ) ) {
		$icontag = 'dt';
	}

	$columns = intval( $atts['columns'] );
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = '';

	/**
	 * Filter whether to print default gallery styles.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $print Whether to print default gallery styles.
	 *                    Defaults to false if the theme supports HTML5 galleries.
	 *                    Otherwise, defaults to true.
	 */
	if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
	}

	$size_class = sanitize_html_class( $atts['size'] );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

	/**
	 * Filter the default gallery shortcode CSS styles.
	 *
	 * @since 2.5.0
	 *
	 * @param string $gallery_style Default CSS styles and opening HTML div container
	 *                              for the gallery shortcode output.
	 */
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {

		$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
		if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
			$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
		} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
			$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
		} else {
			$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
		}
		$image_meta  = wp_get_attachment_metadata( $id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
		}
		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
			$output .= '<br style="clear: both" />';
		}
	}

	if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
		$output .= "
			<br style='clear: both' />";
	}

	$output .= "
		</div>\n";

	return $output;
}

/**
 * Outputs the templates used by playlists.
 *
 * @since 3.9.0
 */
function wp_underscore_playlist_templates() {
?>
<script type="text/html" id="tmpl-wp-playlist-current-item">
	<# if ( data.image ) { #>
	<img src="{{ data.thumb.src }}" alt="" />
	<# } #>
	<div class="wp-playlist-caption">
		<span class="wp-playlist-item-meta wp-playlist-item-title"><?php
			/* translators: playlist item title */
			printf( _x( '&#8220;%s&#8221;', 'playlist item title' ), '{{ data.title }}' );
		?></span>
		<# if ( data.meta.album ) { #><span class="wp-playlist-item-meta wp-playlist-item-album">{{ data.meta.album }}</span><# } #>
		<# if ( data.meta.artist ) { #><span class="wp-playlist-item-meta wp-playlist-item-artist">{{ data.meta.artist }}</span><# } #>
	</div>
</script>
<script type="text/html" id="tmpl-wp-playlist-item">
	<div class="wp-playlist-item">
		<a class="wp-playlist-caption" href="{{ data.src }}">
			{{ data.index ? ( data.index + '. ' ) : '' }}
			<# if ( data.caption ) { #>
				{{ data.caption }}
			<# } else { #>
				<span class="wp-playlist-item-title"><?php
					/* translators: playlist item title */
					printf( _x( '&#8220;%s&#8221;', 'playlist item title' ), '{{{ data.title }}}' );
				?></span>
				<# if ( data.artists && data.meta.artist ) { #>
				<span class="wp-playlist-item-artist"> &mdash; {{ data.meta.artist }}</span>
				<# } #>
			<# } #>
		</a>
		<# if ( data.meta.length_formatted ) { #>
		<div class="wp-playlist-item-length">{{ data.meta.length_formatted }}</div>
		<# } #>
	</div>
</script>
<?php
}

/**
 * Outputs and enqueue default scripts and styles for playlists.
 *
 * @since 3.9.0
 *
 * @param string $type Type of playlist. Accepts 'audio' or 'video'.
 */
function wp_playlist_scripts( $type ) {
	wp_enqueue_style( 'wp-mediaelement' );
	wp_enqueue_script( 'wp-playlist' );
?>
<!--[if lt IE 9]><script>document.createElement('<?php echo esc_js( $type ) ?>');</script><![endif]-->
<?php
	add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
	add_action( 'admin_footer', 'wp_underscore_playlist_templates', 0 );
}

/**
 * Builds the Playlist shortcode output.
 *
 * This implements the functionality of the playlist shortcode for displaying
 * a collection of WordPress audio or video files in a post.
 *
 * @since 3.9.0
 *
 * @global int $content_width
 * @staticvar int $instance
 *
 * @param array $attr {
 *     Array of default playlist attributes.
 *
 *     @type string  $type         Type of playlist to display. Accepts 'audio' or 'video'. Default 'audio'.
 *     @type string  $order        Designates ascending or descending order of items in the playlist.
 *                                 Accepts 'ASC', 'DESC'. Default 'ASC'.
 *     @type string  $orderby      Any column, or columns, to sort the playlist. If $ids are
 *                                 passed, this defaults to the order of the $ids array ('post__in').
 *                                 Otherwise default is 'menu_order ID'.
 *     @type int     $id           If an explicit $ids array is not present, this parameter
 *                                 will determine which attachments are used for the playlist.
 *                                 Default is the current post ID.
 *     @type array   $ids          Create a playlist out of these explicit attachment IDs. If empty,
 *                                 a playlist will be created from all $type attachments of $id.
 *                                 Default empty.
 *     @type array   $exclude      List of specific attachment IDs to exclude from the playlist. Default empty.
 *     @type string  $style        Playlist style to use. Accepts 'light' or 'dark'. Default 'light'.
 *     @type bool    $tracklist    Whether to show or hide the playlist. Default true.
 *     @type bool    $tracknumbers Whether to show or hide the numbers next to entries in the playlist. Default true.
 *     @type bool    $images       Show or hide the video or audio thumbnail (Featured Image/post
 *                                 thumbnail). Default true.
 *     @type bool    $artists      Whether to show or hide artist name in the playlist. Default true.
 * }
 *
 * @return string Playlist output. Empty string if the passed type is unsupported.
 */
function wp_playlist_shortcode( $attr ) {
	global $content_width;
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	/**
	 * Filter the playlist output.
	 *
	 * Passing a non-empty value to the filter will short-circuit generation
	 * of the default playlist output, returning the passed value instead.
	 *
	 * @since 3.9.0
	 * @since 4.2.0 The `$instance` parameter was added.
	 *
	 * @param string $output   Playlist output. Default empty.
	 * @param array  $attr     An array of shortcode attributes.
	 * @param int    $instance Unique numeric ID of this playlist shortcode instance.
	 */
	$output = apply_filters( 'post_playlist', '', $attr, $instance );
	if ( $output != '' ) {
		return $output;
	}

	$atts = shortcode_atts( array(
		'type'		=> 'audio',
		'order'		=> 'ASC',
		'orderby'	=> 'menu_order ID',
		'id'		=> $post ? $post->ID : 0,
		'include'	=> '',
		'exclude'   => '',
		'style'		=> 'light',
		'tracklist' => true,
		'tracknumbers' => true,
		'images'	=> true,
		'artists'	=> true
	), $attr, 'playlist' );

	$id = intval( $atts['id'] );

	if ( $atts['type'] !== 'audio' ) {
		$atts['type'] = 'video';
	}

	$args = array(
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		'post_mime_type' => $atts['type'],
		'order' => $atts['order'],
		'orderby' => $atts['orderby']
	);

	if ( ! empty( $atts['include'] ) ) {
		$args['include'] = $atts['include'];
		$_attachments = get_posts( $args );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$args['post_parent'] = $id;
		$args['exclude'] = $atts['exclude'];
		$attachments = get_children( $args );
	} else {
		$args['post_parent'] = $id;
		$attachments = get_children( $args );
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id ) . "\n";
		}
		return $output;
	}

	$outer = 22; // default padding and border of wrapper

	$default_width = 640;
	$default_height = 360;

	$theme_width = empty( $content_width ) ? $default_width : ( $content_width - $outer );
	$theme_height = empty( $content_width ) ? $default_height : round( ( $default_height * $theme_width ) / $default_width );

	$data = array(
		'type' => $atts['type'],
		// don't pass strings to JSON, will be truthy in JS
		'tracklist' => wp_validate_boolean( $atts['tracklist'] ),
		'tracknumbers' => wp_validate_boolean( $atts['tracknumbers'] ),
		'images' => wp_validate_boolean( $atts['images'] ),
		'artists' => wp_validate_boolean( $atts['artists'] ),
	);

	$tracks = array();
	foreach ( $attachments as $attachment ) {
		$url = wp_get_attachment_url( $attachment->ID );
		$ftype = wp_check_filetype( $url, wp_get_mime_types() );
		$track = array(
			'src' => $url,
			'type' => $ftype['type'],
			'title' => $attachment->post_title,
			'caption' => $attachment->post_excerpt,
			'description' => $attachment->post_content
		);

		$track['meta'] = array();
		$meta = wp_get_attachment_metadata( $attachment->ID );
		if ( ! empty( $meta ) ) {

			foreach ( wp_get_attachment_id3_keys( $attachment ) as $key => $label ) {
				if ( ! empty( $meta[ $key ] ) ) {
					$track['meta'][ $key ] = $meta[ $key ];
				}
			}

			if ( 'video' === $atts['type'] ) {
				if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
					$width = $meta['width'];
					$height = $meta['height'];
					$theme_height = round( ( $height * $theme_width ) / $width );
				} else {
					$width = $default_width;
					$height = $default_height;
				}

				$track['dimensions'] = array(
					'original' => compact( 'width', 'height' ),
					'resized' => array(
						'width' => $theme_width,
						'height' => $theme_height
					)
				);
			}
		}

		if ( $atts['images'] ) {
			$thumb_id = get_post_thumbnail_id( $attachment->ID );
			if ( ! empty( $thumb_id ) ) {
				list( $src, $width, $height ) = wp_get_attachment_image_src( $thumb_id, 'full' );
				$track['image'] = compact( 'src', 'width', 'height' );
				list( $src, $width, $height ) = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
				$track['thumb'] = compact( 'src', 'width', 'height' );
			} else {
				$src = wp_mime_type_icon( $attachment->ID );
				$width = 48;
				$height = 64;
				$track['image'] = compact( 'src', 'width', 'height' );
				$track['thumb'] = compact( 'src', 'width', 'height' );
			}
		}

		$tracks[] = $track;
	}
	$data['tracks'] = $tracks;

	$safe_type = esc_attr( $atts['type'] );
	$safe_style = esc_attr( $atts['style'] );

	ob_start();

	if ( 1 === $instance ) {
		/**
		 * Print and enqueue playlist scripts, styles, and JavaScript templates.
		 *
		 * @since 3.9.0
		 *
		 * @param string $type  Type of playlist. Possible values are 'audio' or 'video'.
		 * @param string $style The 'theme' for the playlist. Core provides 'light' and 'dark'.
		 */
		do_action( 'wp_playlist_scripts', $atts['type'], $atts['style'] );
	} ?>
<div class="wp-playlist wp-<?php echo $safe_type ?>-playlist wp-playlist-<?php echo $safe_style ?>">
	<?php if ( 'audio' === $atts['type'] ): ?>
	<div class="wp-playlist-current-item"></div>
	<?php endif ?>
	<<?php echo $safe_type ?> controls="controls" preload="none" width="<?php
		echo (int) $theme_width;
	?>"<?php if ( 'video' === $safe_type ):
		echo ' height="', (int) $theme_height, '"';
	else:
		echo ' style="visibility: hidden"';
	endif; ?>></<?php echo $safe_type ?>>
	<div class="wp-playlist-next"></div>
	<div class="wp-playlist-prev"></div>
	<noscript>
	<ol><?php
	foreach ( $attachments as $att_id => $attachment ) {
		printf( '<li>%s</li>', wp_get_attachment_link( $att_id ) );
	}
	?></ol>
	</noscript>
	<script type="application/json" class="wp-playlist-script"><?php echo wp_json_encode( $data ) ?></script>
</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'playlist', 'wp_playlist_shortcode' );

/**
 * Provides a No-JS Flash fallback as a last resort for audio / video.
 *
 * @since 3.6.0
 *
 * @param string $url The media element URL.
 * @return string Fallback HTML.
 */
function wp_mediaelement_fallback( $url ) {
	/**
	 * Filter the Mediaelement fallback output for no-JS.
	 *
	 * @since 3.6.0
	 *
	 * @param string $output Fallback output for no-JS.
	 * @param string $url    Media file URL.
	 */
	return apply_filters( 'wp_mediaelement_fallback', sprintf( '<a href="%1$s">%1$s</a>', esc_url( $url ) ), $url );
}

/**
 * Returns a filtered list of WP-supported audio formats.
 *
 * @since 3.6.0
 *
 * @return array Supported audio formats.
 */
function wp_get_audio_extensions() {
	/**
	 * Filter the list of supported audio formats.
	 *
	 * @since 3.6.0
	 *
	 * @param array $extensions An array of support audio formats. Defaults are
	 *                          'mp3', 'ogg', 'wma', 'm4a', 'wav'.
	 */
	return apply_filters( 'wp_audio_extensions', array( 'mp3', 'ogg', 'wma', 'm4a', 'wav' ) );
}

/**
 * Returns useful keys to use to lookup data from an attachment's stored metadata.
 *
 * @since 3.9.0
 *
 * @param WP_Post $attachment The current attachment, provided for context.
 * @param string  $context    Optional. The context. Accepts 'edit', 'display'. Default 'display'.
 * @return array Key/value pairs of field keys to labels.
 */
function wp_get_attachment_id3_keys( $attachment, $context = 'display' ) {
	$fields = array(
		'artist' => __( 'Artist' ),
		'album' => __( 'Album' ),
	);

	if ( 'display' === $context ) {
		$fields['genre']            = __( 'Genre' );
		$fields['year']             = __( 'Year' );
		$fields['length_formatted'] = _x( 'Length', 'video or audio' );
	} elseif ( 'js' === $context ) {
		$fields['bitrate']          = __( 'Bitrate' );
		$fields['bitrate_mode']     = __( 'Bitrate Mode' );
	}

	/**
	 * Filter the editable list of keys to look up data from an attachment's metadata.
	 *
	 * @since 3.9.0
	 *
	 * @param array   $fields     Key/value pairs of field keys to labels.
	 * @param WP_Post $attachment Attachment object.
	 * @param string  $context    The context. Accepts 'edit', 'display'. Default 'display'.
	 */
	return apply_filters( 'wp_get_attachment_id3_keys', $fields, $attachment, $context );
}
/**
 * Builds the Audio shortcode output.
 *
 * This implements the functionality of the Audio Shortcode for displaying
 * WordPress mp3s in a post.
 *
 * @since 3.6.0
 *
 * @staticvar int $instance
 *
 * @param array  $attr {
 *     Attributes of the audio shortcode.
 *
 *     @type string $src      URL to the source of the audio file. Default empty.
 *     @type string $loop     The 'loop' attribute for the `<audio>` element. Default empty.
 *     @type string $autoplay The 'autoplay' attribute for the `<audio>` element. Default empty.
 *     @type string $preload  The 'preload' attribute for the `<audio>` element. Default empty.
 *     @type string $class    The 'class' attribute for the `<audio>` element. Default 'wp-audio-shortcode'.
 *     @type string $style    The 'style' attribute for the `<audio>` element. Default 'width: 100%'.
 * }
 * @param string $content Shortcode content.
 * @return string|void HTML content to display audio.
 */
function wp_audio_shortcode( $attr, $content = '' ) {
	$post_id = get_post() ? get_the_ID() : 0;

	static $instance = 0;
	$instance++;

	/**
	 * Filter the default audio shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating the default audio template.
	 *
	 * @since 3.6.0
	 *
	 * @param string $html     Empty variable to be replaced with shortcode markup.
	 * @param array  $attr     Attributes of the shortcode. @see wp_audio_shortcode()
	 * @param string $content  Shortcode content.
	 * @param int    $instance Unique numeric ID of this audio shortcode instance.
	 */
	$override = apply_filters( 'wp_audio_shortcode_override', '', $attr, $content, $instance );
	if ( '' !== $override ) {
		return $override;
	}

	$audio = null;

	$default_types = wp_get_audio_extensions();
	$defaults_atts = array(
		'src'      => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'none'
	);
	foreach ( $default_types as $type ) {
		$defaults_atts[$type] = '';
	}

	$atts = shortcode_atts( $defaults_atts, $attr, 'audio' );

	$primary = false;
	if ( ! empty( $atts['src'] ) ) {
		$type = wp_check_filetype( $atts['src'], wp_get_mime_types() );
		if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
			return sprintf( '<a class="wp-embedded-audio" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
		}
		$primary = true;
		array_unshift( $default_types, 'src' );
	} else {
		foreach ( $default_types as $ext ) {
			if ( ! empty( $atts[ $ext ] ) ) {
				$type = wp_check_filetype( $atts[ $ext ], wp_get_mime_types() );
				if ( strtolower( $type['ext'] ) === $ext ) {
					$primary = true;
				}
			}
		}
	}

	if ( ! $primary ) {
		$audios = get_attached_media( 'audio', $post_id );
		if ( empty( $audios ) ) {
			return;
		}

		$audio = reset( $audios );
		$atts['src'] = wp_get_attachment_url( $audio->ID );
		if ( empty( $atts['src'] ) ) {
			return;
		}

		array_unshift( $default_types, 'src' );
	}

	/**
	 * Filter the media library used for the audio shortcode.
	 *
	 * @since 3.6.0
	 *
	 * @param string $library Media library used for the audio shortcode.
	 */
	$library = apply_filters( 'wp_audio_shortcode_library', 'mediaelement' );
	if ( 'mediaelement' === $library && did_action( 'init' ) ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	/**
	 * Filter the class attribute for the audio shortcode output container.
	 *
	 * @since 3.6.0
	 *
	 * @param string $class CSS class or list of space-separated classes.
	 */
	$html_atts = array(
		'class'    => apply_filters( 'wp_audio_shortcode_class', 'wp-audio-shortcode' ),
		'id'       => sprintf( 'audio-%d-%d', $post_id, $instance ),
		'loop'     => wp_validate_boolean( $atts['loop'] ),
		'autoplay' => wp_validate_boolean( $atts['autoplay'] ),
		'preload'  => $atts['preload'],
		'style'    => 'width: 100%; visibility: hidden;',
	);

	// These ones should just be omitted altogether if they are blank
	foreach ( array( 'loop', 'autoplay', 'preload' ) as $a ) {
		if ( empty( $html_atts[$a] ) ) {
			unset( $html_atts[$a] );
		}
	}

	$attr_strings = array();
	foreach ( $html_atts as $k => $v ) {
		$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
	}

	$html = '';
	if ( 'mediaelement' === $library && 1 === $instance ) {
		$html .= "<!--[if lt IE 9]><script>document.createElement('audio');</script><![endif]-->\n";
	}
	$html .= sprintf( '<audio %s controls="controls">', join( ' ', $attr_strings ) );

	$fileurl = '';
	$source = '<source type="%s" src="%s" />';
	foreach ( $default_types as $fallback ) {
		if ( ! empty( $atts[ $fallback ] ) ) {
			if ( empty( $fileurl ) ) {
				$fileurl = $atts[ $fallback ];
			}
			$type = wp_check_filetype( $atts[ $fallback ], wp_get_mime_types() );
			$url = add_query_arg( '_', $instance, $atts[ $fallback ] );
			$html .= sprintf( $source, $type['type'], esc_url( $url ) );
		}
	}

	if ( 'mediaelement' === $library ) {
		$html .= wp_mediaelement_fallback( $fileurl );
	}
	$html .= '</audio>';

	/**
	 * Filter the audio shortcode output.
	 *
	 * @since 3.6.0
	 *
	 * @param string $html    Audio shortcode HTML output.
	 * @param array  $atts    Array of audio shortcode attributes.
	 * @param string $audio   Audio file.
	 * @param int    $post_id Post ID.
	 * @param string $library Media library used for the audio shortcode.
	 */
	return apply_filters( 'wp_audio_shortcode', $html, $atts, $audio, $post_id, $library );
}
add_shortcode( 'audio', 'wp_audio_shortcode' );

/**
 * Returns a filtered list of WP-supported video formats.
 *
 * @since 3.6.0
 *
 * @return array List of supported video formats.
 */
function wp_get_video_extensions() {
	/**
	 * Filter the list of supported video formats.
	 *
	 * @since 3.6.0
	 *
	 * @param array $extensions An array of support video formats. Defaults are
	 *                          'mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv'.
	 */
	return apply_filters( 'wp_video_extensions', array( 'mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv' ) );
}

/**
 * Builds the Video shortcode output.
 *
 * This implements the functionality of the Video Shortcode for displaying
 * WordPress mp4s in a post.
 *
 * @since 3.6.0
 *
 * @global int $content_width
 * @staticvar int $instance
 *
 * @param array  $attr {
 *     Attributes of the shortcode.
 *
 *     @type string $src      URL to the source of the video file. Default empty.
 *     @type int    $height   Height of the video embed in pixels. Default 360.
 *     @type int    $width    Width of the video embed in pixels. Default $content_width or 640.
 *     @type string $poster   The 'poster' attribute for the `<video>` element. Default empty.
 *     @type string $loop     The 'loop' attribute for the `<video>` element. Default empty.
 *     @type string $autoplay The 'autoplay' attribute for the `<video>` element. Default empty.
 *     @type string $preload  The 'preload' attribute for the `<video>` element.
 *                            Default 'metadata'.
 *     @type string $class    The 'class' attribute for the `<video>` element.
 *                            Default 'wp-video-shortcode'.
 * }
 * @param string $content Shortcode content.
 * @return string|void HTML content to display video.
 */
function wp_video_shortcode( $attr, $content = '' ) {
	global $content_width;
	$post_id = get_post() ? get_the_ID() : 0;

	static $instance = 0;
	$instance++;

	/**
	 * Filter the default video shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating
	 * the default video template.
	 *
	 * @since 3.6.0
	 *
	 * @see wp_video_shortcode()
	 *
	 * @param string $html     Empty variable to be replaced with shortcode markup.
	 * @param array  $attr     Attributes of the video shortcode.
	 * @param string $content  Video shortcode content.
	 * @param int    $instance Unique numeric ID of this video shortcode instance.
	 */
	$override = apply_filters( 'wp_video_shortcode_override', '', $attr, $content, $instance );
	if ( '' !== $override ) {
		return $override;
	}

	$video = null;

	$default_types = wp_get_video_extensions();
	$defaults_atts = array(
		'src'      => '',
		'poster'   => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'metadata',
		'width'    => 640,
		'height'   => 360,
	);

	foreach ( $default_types as $type ) {
		$defaults_atts[$type] = '';
	}

	$atts = shortcode_atts( $defaults_atts, $attr, 'video' );

	if ( is_admin() ) {
		// shrink the video so it isn't huge in the admin
		if ( $atts['width'] > $defaults_atts['width'] ) {
			$atts['height'] = round( ( $atts['height'] * $defaults_atts['width'] ) / $atts['width'] );
			$atts['width'] = $defaults_atts['width'];
		}
	} else {
		// if the video is bigger than the theme
		if ( ! empty( $content_width ) && $atts['width'] > $content_width ) {
			$atts['height'] = round( ( $atts['height'] * $content_width ) / $atts['width'] );
			$atts['width'] = $content_width;
		}
	}

	$is_vimeo = $is_youtube = false;
	$yt_pattern = '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#';
	$vimeo_pattern = '#^https?://(.+\.)?vimeo\.com/.*#';

	$primary = false;
	if ( ! empty( $atts['src'] ) ) {
		$is_vimeo = ( preg_match( $vimeo_pattern, $atts['src'] ) );
		$is_youtube = (  preg_match( $yt_pattern, $atts['src'] ) );
		if ( ! $is_youtube && ! $is_vimeo ) {
			$type = wp_check_filetype( $atts['src'], wp_get_mime_types() );
			if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
				return sprintf( '<a class="wp-embedded-video" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
			}
		}

		if ( $is_vimeo ) {
			wp_enqueue_script( 'froogaloop' );
		}

		$primary = true;
		array_unshift( $default_types, 'src' );
	} else {
		foreach ( $default_types as $ext ) {
			if ( ! empty( $atts[ $ext ] ) ) {
				$type = wp_check_filetype( $atts[ $ext ], wp_get_mime_types() );
				if ( strtolower( $type['ext'] ) === $ext ) {
					$primary = true;
				}
			}
		}
	}

	if ( ! $primary ) {
		$videos = get_attached_media( 'video', $post_id );
		if ( empty( $videos ) ) {
			return;
		}

		$video = reset( $videos );
		$atts['src'] = wp_get_attachment_url( $video->ID );
		if ( empty( $atts['src'] ) ) {
			return;
		}

		array_unshift( $default_types, 'src' );
	}

	/**
	 * Filter the media library used for the video shortcode.
	 *
	 * @since 3.6.0
	 *
	 * @param string $library Media library used for the video shortcode.
	 */
	$library = apply_filters( 'wp_video_shortcode_library', 'mediaelement' );
	if ( 'mediaelement' === $library && did_action( 'init' ) ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	/**
	 * Filter the class attribute for the video shortcode output container.
	 *
	 * @since 3.6.0
	 *
	 * @param string $class CSS class or list of space-separated classes.
	 */
	$html_atts = array(
		'class'    => apply_filters( 'wp_video_shortcode_class', 'wp-video-shortcode' ),
		'id'       => sprintf( 'video-%d-%d', $post_id, $instance ),
		'width'    => absint( $atts['width'] ),
		'height'   => absint( $atts['height'] ),
		'poster'   => esc_url( $atts['poster'] ),
		'loop'     => wp_validate_boolean( $atts['loop'] ),
		'autoplay' => wp_validate_boolean( $atts['autoplay'] ),
		'preload'  => $atts['preload'],
	);

	// These ones should just be omitted altogether if they are blank
	foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $a ) {
		if ( empty( $html_atts[$a] ) ) {
			unset( $html_atts[$a] );
		}
	}

	$attr_strings = array();
	foreach ( $html_atts as $k => $v ) {
		$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
	}

	$html = '';
	if ( 'mediaelement' === $library && 1 === $instance ) {
		$html .= "<!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->\n";
	}
	$html .= sprintf( '<video %s controls="controls">', join( ' ', $attr_strings ) );

	$fileurl = '';
	$source = '<source type="%s" src="%s" />';
	foreach ( $default_types as $fallback ) {
		if ( ! empty( $atts[ $fallback ] ) ) {
			if ( empty( $fileurl ) ) {
				$fileurl = $atts[ $fallback ];
			}
			if ( 'src' === $fallback && $is_youtube ) {
				$type = array( 'type' => 'video/youtube' );
			} elseif ( 'src' === $fallback && $is_vimeo ) {
				$type = array( 'type' => 'video/vimeo' );
			} else {
				$type = wp_check_filetype( $atts[ $fallback ], wp_get_mime_types() );
			}
			$url = add_query_arg( '_', $instance, $atts[ $fallback ] );
			$html .= sprintf( $source, $type['type'], esc_url( $url ) );
		}
	}

	if ( ! empty( $content ) ) {
		if ( false !== strpos( $content, "\n" ) ) {
			$content = str_replace( array( "\r\n", "\n", "\t" ), '', $content );
		}
		$html .= trim( $content );
	}

	if ( 'mediaelement' === $library ) {
		$html .= wp_mediaelement_fallback( $fileurl );
	}
	$html .= '</video>';

	$width_rule = '';
	if ( ! empty( $atts['width'] ) ) {
		$width_rule = sprintf( 'width: %dpx; ', $atts['width'] );
	}
	$output = sprintf( '<div style="%s" class="wp-video">%s</div>', $width_rule, $html );

	/**
	 * Filter the output of the video shortcode.
	 *
	 * @since 3.6.0
	 *
	 * @param string $output  Video shortcode HTML output.
	 * @param array  $atts    Array of video shortcode attributes.
	 * @param string $video   Video file.
	 * @param int    $post_id Post ID.
	 * @param string $library Media library used for the video shortcode.
	 */
	return apply_filters( 'wp_video_shortcode', $output, $atts, $video, $post_id, $library );
}
add_shortcode( 'video', 'wp_video_shortcode' );

/**
 * Displays previous image link that has the same post parent.
 *
 * @since 2.5.0
 *
 * @see adjacent_image_link()
 *
 * @param string|array $size Optional. Image size. Accepts any valid image size, an array of width and
 *                           height values in pixels (in that order), 0, or 'none'. 0 or 'none' will
 *                           default to 'post_title' or `$text`. Default 'thumbnail'.
 * @param string       $text Optional. Link text. Default false.
 */
function previous_image_link( $size = 'thumbnail', $text = false ) {
	adjacent_image_link(true, $size, $text);
}

/**
 * Displays next image link that has the same post parent.
 *
 * @since 2.5.0
 *
 * @see adjacent_image_link()
 *
 * @param string|array $size Optional. Image size. Accepts any valid image size, an array of width and
 *                           height values in pixels (in that order), 0, or 'none'. 0 or 'none' will
 *                           default to 'post_title' or `$text`. Default 'thumbnail'.
 * @param string       $text Optional. Link text. Default false.
 */
function next_image_link( $size = 'thumbnail', $text = false ) {
	adjacent_image_link(false, $size, $text);
}

/**
 * Displays next or previous image link that has the same post parent.
 *
 * Retrieves the current attachment object from the $post global.
 *
 * @since 2.5.0
 *
 * @param bool         $prev Optional. Whether to display the next (false) or previous (true) link. Default true.
 * @param string|array $size Optional. Image size. Accepts any valid image size, or an array of width and height
 *                           values in pixels (in that order). Default 'thumbnail'.
 * @param bool         $text Optional. Link text. Default false.
 */
function adjacent_image_link( $prev = true, $size = 'thumbnail', $text = false ) {
	$post = get_post();
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );

	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID ) {
			break;
		}
	}

	$output = '';
	$attachment_id = 0;

	if ( $attachments ) {
		$k = $prev ? $k - 1 : $k + 1;

		if ( isset( $attachments[ $k ] ) ) {
			$attachment_id = $attachments[ $k ]->ID;
			$output = wp_get_attachment_link( $attachment_id, $size, true, false, $text );
		}
	}

	$adjacent = $prev ? 'previous' : 'next';

	/**
	 * Filter the adjacent image link.
	 *
	 * The dynamic portion of the hook name, `$adjacent`, refers to the type of adjacency,
	 * either 'next', or 'previous'.
	 *
	 * @since 3.5.0
	 *
	 * @param string $output        Adjacent image HTML markup.
	 * @param int    $attachment_id Attachment ID
	 * @param string $size          Image size.
	 * @param string $text          Link text.
	 */
	echo apply_filters( "{$adjacent}_image_link", $output, $attachment_id, $size, $text );
}

/**
 * Retrieves taxonomies attached to given the attachment.
 *
 * @since 2.5.0
 *
 * @param int|array|object $attachment Attachment ID, data array, or data object.
 * @return array Empty array on failure. List of taxonomies on success.
 */
function get_attachment_taxonomies( $attachment ) {
	if ( is_int( $attachment ) ) {
		$attachment = get_post( $attachment );
	} elseif ( is_array( $attachment ) ) {
		$attachment = (object) $attachment;
	}
	if ( ! is_object($attachment) )
		return array();

	$file = get_attached_file( $attachment->ID );
	$filename = basename( $file );

	$objects = array('attachment');

	if ( false !== strpos($filename, '.') )
		$objects[] = 'attachment:' . substr($filename, strrpos($filename, '.') + 1);
	if ( !empty($attachment->post_mime_type) ) {
		$objects[] = 'attachment:' . $attachment->post_mime_type;
		if ( false !== strpos($attachment->post_mime_type, '/') )
			foreach ( explode('/', $attachment->post_mime_type) as $token )
				if ( !empty($token) )
					$objects[] = "attachment:$token";
	}

	$taxonomies = array();
	foreach ( $objects as $object )
		if ( $taxes = get_object_taxonomies($object) )
			$taxonomies = array_merge($taxonomies, $taxes);

	return array_unique($taxonomies);
}

/**
 * Retrieves all of the taxonomy names that are registered for attachments.
 *
 * Handles mime-type-specific taxonomies such as attachment:image and attachment:video.
 *
 * @since 3.5.0
 *
 * @see get_taxonomies()
 *
 * @param string $output Optional. The type of taxonomy output to return. Accepts 'names' or 'objects'.
 *                       Default 'names'.
 * @return array The names of all taxonomy of $object_type.
 */
function get_taxonomies_for_attachments( $output = 'names' ) {
	$taxonomies = array();
	foreach ( get_taxonomies( array(), 'objects' ) as $taxonomy ) {
		foreach ( $taxonomy->object_type as $object_type ) {
			if ( 'attachment' == $object_type || 0 === strpos( $object_type, 'attachment:' ) ) {
				if ( 'names' == $output )
					$taxonomies[] = $taxonomy->name;
				else
					$taxonomies[ $taxonomy->name ] = $taxonomy;
				break;
			}
		}
	}

	return $taxonomies;
}

/**
 * Create new GD image resource with transparency support
 *
 * @todo: Deprecate if possible.
 *
 * @since 2.9.0
 *
 * @param int $width  Image width in pixels.
 * @param int $height Image height in pixels..
 * @return resource The GD image resource.
 */
function wp_imagecreatetruecolor($width, $height) {
	$img = imagecreatetruecolor($width, $height);
	if ( is_resource($img) && function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
		imagealphablending($img, false);
		imagesavealpha($img, true);
	}
	return $img;
}

/**
 * Based on a supplied width/height example, return the biggest possible dimensions based on the max width/height.
 *
 * @since 2.9.0
 *
 * @see wp_constrain_dimensions()
 *
 * @param int $example_width  The width of an example embed.
 * @param int $example_height The height of an example embed.
 * @param int $max_width      The maximum allowed width.
 * @param int $max_height     The maximum allowed height.
 * @return array The maximum possible width and height based on the example ratio.
 */
function wp_expand_dimensions( $example_width, $example_height, $max_width, $max_height ) {
	$example_width  = (int) $example_width;
	$example_height = (int) $example_height;
	$max_width      = (int) $max_width;
	$max_height     = (int) $max_height;

	return wp_constrain_dimensions( $example_width * 1000000, $example_height * 1000000, $max_width, $max_height );
}

/**
 * Converts a shorthand byte value to an integer byte value.
 *
 * @since 2.3.0
 *
 * @param string $size A shorthand byte value.
 * @return int An integer byte value.
 */
function wp_convert_hr_to_bytes( $size ) {
	$size  = strtolower( $size );
	$bytes = (int) $size;
	if ( strpos( $size, 'k' ) !== false )
		$bytes = intval( $size ) * KB_IN_BYTES;
	elseif ( strpos( $size, 'm' ) !== false )
		$bytes = intval($size) * MB_IN_BYTES;
	elseif ( strpos( $size, 'g' ) !== false )
		$bytes = intval( $size ) * GB_IN_BYTES;
	return $bytes;
}

/**
 * Determines the maximum upload size allowed in php.ini.
 *
 * @since 2.5.0
 *
 * @return int Allowed upload size.
 */
function wp_max_upload_size() {
	$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );

	/**
	 * Filter the maximum upload size allowed in php.ini.
	 *
	 * @since 2.5.0
	 *
	 * @param int $size    Max upload size limit in bytes.
	 * @param int $u_bytes Maximum upload filesize in bytes.
	 * @param int $p_bytes Maximum size of POST data in bytes.
	 */
	return apply_filters( 'upload_size_limit', min( $u_bytes, $p_bytes ), $u_bytes, $p_bytes );
}

/**
 * Returns a WP_Image_Editor instance and loads file into it.
 *
 * @since 3.5.0
 *
 * @param string $path Path to the file to load.
 * @param array  $args Optional. Additional arguments for retrieving the image editor.
 *                     Default empty array.
 * @return WP_Image_Editor|WP_Error The WP_Image_Editor object if successful, an WP_Error
 *                                  object otherwise.
 */
function wp_get_image_editor( $path, $args = array() ) {
	$args['path'] = $path;

	if ( ! isset( $args['mime_type'] ) ) {
		$file_info = wp_check_filetype( $args['path'] );

		// If $file_info['type'] is false, then we let the editor attempt to
		// figure out the file type, rather than forcing a failure based on extension.
		if ( isset( $file_info ) && $file_info['type'] )
			$args['mime_type'] = $file_info['type'];
	}

	$implementation = _wp_image_editor_choose( $args );

	if ( $implementation ) {
		$editor = new $implementation( $path );
		$loaded = $editor->load();

		if ( is_wp_error( $loaded ) )
			return $loaded;

		return $editor;
	}

	return new WP_Error( 'image_no_editor', __('No editor could be selected.') );
}

/**
 * Tests whether there is an editor that supports a given mime type or methods.
 *
 * @since 3.5.0
 *
 * @param string|array $args Optional. Array of arguments to retrieve the image editor supports.
 *                           Default empty array.
 * @return bool True if an eligible editor is found; false otherwise.
 */
function wp_image_editor_supports( $args = array() ) {
	return (bool) _wp_image_editor_choose( $args );
}

/**
 * Tests which editors are capable of supporting the request.
 *
 * @ignore
 * @since 3.5.0
 *
 * @param array $args Optional. Array of arguments for choosing a capable editor. Default empty array.
 * @return string|false Class name for the first editor that claims to support the request. False if no
 *                     editor claims to support the request.
 */
function _wp_image_editor_choose( $args = array() ) {
	require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
	require_once ABSPATH . WPINC . '/class-wp-image-editor-gd.php';
	require_once ABSPATH . WPINC . '/class-wp-image-editor-imagick.php';

	/**
	 * Filter the list of image editing library classes.
	 *
	 * @since 3.5.0
	 *
	 * @param array $image_editors List of available image editors. Defaults are
	 *                             'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD'.
	 */
	$implementations = apply_filters( 'wp_image_editors', array( 'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD' ) );

	foreach ( $implementations as $implementation ) {
		if ( ! call_user_func( array( $implementation, 'test' ), $args ) )
			continue;

		if ( isset( $args['mime_type'] ) &&
			! call_user_func(
				array( $implementation, 'supports_mime_type' ),
				$args['mime_type'] ) ) {
			continue;
		}

		if ( isset( $args['methods'] ) &&
			 array_diff( $args['methods'], get_class_methods( $implementation ) ) ) {
			continue;
		}

		return $implementation;
	}

	return false;
}

/**
 * Prints default plupload arguments.
 *
 * @since 3.4.0
 */
function wp_plupload_default_settings() {
	$wp_scripts = wp_scripts();

	$data = $wp_scripts->get_data( 'wp-plupload', 'data' );
	if ( $data && false !== strpos( $data, '_wpPluploadSettings' ) )
		return;

	$max_upload_size = wp_max_upload_size();

	$defaults = array(
		'runtimes'            => 'html5,flash,silverlight,html4',
		'file_data_name'      => 'async-upload', // key passed to $_FILE.
		'url'                 => admin_url( 'async-upload.php', 'relative' ),
		'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		'filters' => array(
			'max_file_size'   => $max_upload_size . 'b',
		),
	);

	// Currently only iOS Safari supports multiple files uploading but iOS 7.x has a bug that prevents uploading of videos
	// when enabled. See #29602.
	if ( wp_is_mobile() && strpos( $_SERVER['HTTP_USER_AGENT'], 'OS 7_' ) !== false &&
		strpos( $_SERVER['HTTP_USER_AGENT'], 'like Mac OS X' ) !== false ) {

		$defaults['multi_selection'] = false;
	}

	/**
	 * Filter the Plupload default settings.
	 *
	 * @since 3.4.0
	 *
	 * @param array $defaults Default Plupload settings array.
	 */
	$defaults = apply_filters( 'plupload_default_settings', $defaults );

	$params = array(
		'action' => 'upload-attachment',
	);

	/**
	 * Filter the Plupload default parameters.
	 *
	 * @since 3.4.0
	 *
	 * @param array $params Default Plupload parameters array.
	 */
	$params = apply_filters( 'plupload_default_params', $params );
	$params['_wpnonce'] = wp_create_nonce( 'media-form' );
	$defaults['multipart_params'] = $params;

	$settings = array(
		'defaults' => $defaults,
		'browser'  => array(
			'mobile'    => wp_is_mobile(),
			'supported' => _device_can_upload(),
		),
		'limitExceeded' => is_multisite() && ! is_upload_space_available()
	);

	$script = 'var _wpPluploadSettings = ' . wp_json_encode( $settings ) . ';';

	if ( $data )
		$script = "$data\n$script";

	$wp_scripts->add_data( 'wp-plupload', 'data', $script );
}

/**
 * Prepares an attachment post object for JS, where it is expected
 * to be JSON-encoded and fit into an Attachment model.
 *
 * @since 3.5.0
 *
 * @param mixed $attachment Attachment ID or object.
 * @return array|void Array of attachment details.
 */
function wp_prepare_attachment_for_js( $attachment ) {
	if ( ! $attachment = get_post( $attachment ) )
		return;

	if ( 'attachment' != $attachment->post_type )
		return;

	$meta = wp_get_attachment_metadata( $attachment->ID );
	if ( false !== strpos( $attachment->post_mime_type, '/' ) )
		list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
	else
		list( $type, $subtype ) = array( $attachment->post_mime_type, '' );

	$attachment_url = wp_get_attachment_url( $attachment->ID );

	$response = array(
		'id'          => $attachment->ID,
		'title'       => $attachment->post_title,
		'filename'    => wp_basename( get_attached_file( $attachment->ID ) ),
		'url'         => $attachment_url,
		'link'        => get_attachment_link( $attachment->ID ),
		'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'author'      => $attachment->post_author,
		'description' => $attachment->post_content,
		'caption'     => $attachment->post_excerpt,
		'name'        => $attachment->post_name,
		'status'      => $attachment->post_status,
		'uploadedTo'  => $attachment->post_parent,
		'date'        => strtotime( $attachment->post_date_gmt ) * 1000,
		'modified'    => strtotime( $attachment->post_modified_gmt ) * 1000,
		'menuOrder'   => $attachment->menu_order,
		'mime'        => $attachment->post_mime_type,
		'type'        => $type,
		'subtype'     => $subtype,
		'icon'        => wp_mime_type_icon( $attachment->ID ),
		'dateFormatted' => mysql2date( get_option('date_format'), $attachment->post_date ),
		'nonces'      => array(
			'update' => false,
			'delete' => false,
			'edit'   => false
		),
		'editLink'   => false,
		'meta'       => false,
	);

	$author = new WP_User( $attachment->post_author );
	$response['authorName'] = $author->display_name;

	if ( $attachment->post_parent ) {
		$post_parent = get_post( $attachment->post_parent );
	} else {
		$post_parent = false;
	}

	if ( $post_parent ) {
		$parent_type = get_post_type_object( $post_parent->post_type );
		if ( $parent_type && $parent_type->show_ui && current_user_can( 'edit_post', $attachment->post_parent ) ) {
			$response['uploadedToLink'] = get_edit_post_link( $attachment->post_parent, 'raw' );
		}
		$response['uploadedToTitle'] = $post_parent->post_title ? $post_parent->post_title : __( '(no title)' );
	}

	$attached_file = get_attached_file( $attachment->ID );

	if ( isset( $meta['filesize'] ) ) {
		$bytes = $meta['filesize'];
	} elseif ( file_exists( $attached_file ) ) {
		$bytes = filesize( $attached_file );
	} else {
		$bytes = '';
	}

	if ( $bytes ) {
		$response['filesizeInBytes'] = $bytes;
		$response['filesizeHumanReadable'] = size_format( $bytes );
	}

	if ( current_user_can( 'edit_post', $attachment->ID ) ) {
		$response['nonces']['update'] = wp_create_nonce( 'update-post_' . $attachment->ID );
		$response['nonces']['edit'] = wp_create_nonce( 'image_editor-' . $attachment->ID );
		$response['editLink'] = get_edit_post_link( $attachment->ID, 'raw' );
	}

	if ( current_user_can( 'delete_post', $attachment->ID ) )
		$response['nonces']['delete'] = wp_create_nonce( 'delete-post_' . $attachment->ID );

	if ( $meta && 'image' === $type ) {
		$sizes = array();

		/** This filter is documented in wp-admin/includes/media.php */
		$possible_sizes = apply_filters( 'image_size_names_choose', array(
			'thumbnail' => __('Thumbnail'),
			'medium'    => __('Medium'),
			'large'     => __('Large'),
			'full'      => __('Full Size'),
		) );
		unset( $possible_sizes['full'] );

		// Loop through all potential sizes that may be chosen. Try to do this with some efficiency.
		// First: run the image_downsize filter. If it returns something, we can use its data.
		// If the filter does not return something, then image_downsize() is just an expensive
		// way to check the image metadata, which we do second.
		foreach ( $possible_sizes as $size => $label ) {

			/** This filter is documented in wp-includes/media.php */
			if ( $downsize = apply_filters( 'image_downsize', false, $attachment->ID, $size ) ) {
				if ( ! $downsize[3] )
					continue;
				$sizes[ $size ] = array(
					'height'      => $downsize[2],
					'width'       => $downsize[1],
					'url'         => $downsize[0],
					'orientation' => $downsize[2] > $downsize[1] ? 'portrait' : 'landscape',
				);
			} elseif ( isset( $meta['sizes'][ $size ] ) ) {
				if ( ! isset( $base_url ) )
					$base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );

				// Nothing from the filter, so consult image metadata if we have it.
				$size_meta = $meta['sizes'][ $size ];

				// We have the actual image size, but might need to further constrain it if content_width is narrower.
				// Thumbnail, medium, and full sizes are also checked against the site's height/width options.
				list( $width, $height ) = image_constrain_size_for_editor( $size_meta['width'], $size_meta['height'], $size, 'edit' );

				$sizes[ $size ] = array(
					'height'      => $height,
					'width'       => $width,
					'url'         => $base_url . $size_meta['file'],
					'orientation' => $height > $width ? 'portrait' : 'landscape',
				);
			}
		}

		$sizes['full'] = array( 'url' => $attachment_url );

		if ( isset( $meta['height'], $meta['width'] ) ) {
			$sizes['full']['height'] = $meta['height'];
			$sizes['full']['width'] = $meta['width'];
			$sizes['full']['orientation'] = $meta['height'] > $meta['width'] ? 'portrait' : 'landscape';
		}

		$response = array_merge( $response, array( 'sizes' => $sizes ), $sizes['full'] );
	} elseif ( $meta && 'video' === $type ) {
		if ( isset( $meta['width'] ) )
			$response['width'] = (int) $meta['width'];
		if ( isset( $meta['height'] ) )
			$response['height'] = (int) $meta['height'];
	}

	if ( $meta && ( 'audio' === $type || 'video' === $type ) ) {
		if ( isset( $meta['length_formatted'] ) )
			$response['fileLength'] = $meta['length_formatted'];

		$response['meta'] = array();
		foreach ( wp_get_attachment_id3_keys( $attachment, 'js' ) as $key => $label ) {
			$response['meta'][ $key ] = false;

			if ( ! empty( $meta[ $key ] ) ) {
				$response['meta'][ $key ] = $meta[ $key ];
			}
		}

		$id = get_post_thumbnail_id( $attachment->ID );
		if ( ! empty( $id ) ) {
			list( $src, $width, $height ) = wp_get_attachment_image_src( $id, 'full' );
			$response['image'] = compact( 'src', 'width', 'height' );
			list( $src, $width, $height ) = wp_get_attachment_image_src( $id, 'thumbnail' );
			$response['thumb'] = compact( 'src', 'width', 'height' );
		} else {
			$src = wp_mime_type_icon( $attachment->ID );
			$width = 48;
			$height = 64;
			$response['image'] = compact( 'src', 'width', 'height' );
			$response['thumb'] = compact( 'src', 'width', 'height' );
		}
	}

	if ( function_exists('get_compat_media_markup') )
		$response['compat'] = get_compat_media_markup( $attachment->ID, array( 'in_modal' => true ) );

	/**
	 * Filter the attachment data prepared for JavaScript.
	 *
	 * @since 3.5.0
	 *
	 * @param array      $response   Array of prepared attachment data.
	 * @param int|object $attachment Attachment ID or object.
	 * @param array      $meta       Array of attachment meta data.
	 */
	return apply_filters( 'wp_prepare_attachment_for_js', $response, $attachment, $meta );
}

/**
 * Enqueues all scripts, styles, settings, and templates necessary to use
 * all media JS APIs.
 *
 * @since 3.5.0
 *
 * @global int       $content_width
 * @global wpdb      $wpdb
 * @global WP_Locale $wp_locale
 *
 * @param array $args {
 *     Arguments for enqueuing media scripts.
 *
 *     @type int|WP_Post A post object or ID.
 * }
 */
function wp_enqueue_media( $args = array() ) {
	// Enqueue me just once per page, please.
	if ( did_action( 'wp_enqueue_media' ) )
		return;

	global $content_width, $wpdb, $wp_locale;

	$defaults = array(
		'post' => null,
	);
	$args = wp_parse_args( $args, $defaults );

	// We're going to pass the old thickbox media tabs to `media_upload_tabs`
	// to ensure plugins will work. We will then unset those tabs.
	$tabs = array(
		// handler action suffix => tab label
		'type'     => '',
		'type_url' => '',
		'gallery'  => '',
		'library'  => '',
	);

	/** This filter is documented in wp-admin/includes/media.php */
	$tabs = apply_filters( 'media_upload_tabs', $tabs );
	unset( $tabs['type'], $tabs['type_url'], $tabs['gallery'], $tabs['library'] );

	$props = array(
		'link'  => get_option( 'image_default_link_type' ), // db default is 'file'
		'align' => get_option( 'image_default_align' ), // empty default
		'size'  => get_option( 'image_default_size' ),  // empty default
	);

	$exts = array_merge( wp_get_audio_extensions(), wp_get_video_extensions() );
	$mimes = get_allowed_mime_types();
	$ext_mimes = array();
	foreach ( $exts as $ext ) {
		foreach ( $mimes as $ext_preg => $mime_match ) {
			if ( preg_match( '#' . $ext . '#i', $ext_preg ) ) {
				$ext_mimes[ $ext ] = $mime_match;
				break;
			}
		}
	}

	$has_audio = $wpdb->get_var( "
		SELECT ID
		FROM $wpdb->posts
		WHERE post_type = 'attachment'
		AND post_mime_type LIKE 'audio%'
		LIMIT 1
	" );
	$has_video = $wpdb->get_var( "
		SELECT ID
		FROM $wpdb->posts
		WHERE post_type = 'attachment'
		AND post_mime_type LIKE 'video%'
		LIMIT 1
	" );
	$months = $wpdb->get_results( $wpdb->prepare( "
		SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
		FROM $wpdb->posts
		WHERE post_type = %s
		ORDER BY post_date DESC
	", 'attachment' ) );
	foreach ( $months as $month_year ) {
		$month_year->text = sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month_year->month ), $month_year->year );
	}

	$settings = array(
		'tabs'      => $tabs,
		'tabUrl'    => add_query_arg( array( 'chromeless' => true ), admin_url('media-upload.php') ),
		'mimeTypes' => wp_list_pluck( get_post_mime_types(), 0 ),
		/** This filter is documented in wp-admin/includes/media.php */
		'captions'  => ! apply_filters( 'disable_captions', '' ),
		'nonce'     => array(
			'sendToEditor' => wp_create_nonce( 'media-send-to-editor' ),
		),
		'post'    => array(
			'id' => 0,
		),
		'defaultProps' => $props,
		'attachmentCounts' => array(
			'audio' => ( $has_audio ) ? 1 : 0,
			'video' => ( $has_video ) ? 1 : 0
		),
		'embedExts'    => $exts,
		'embedMimes'   => $ext_mimes,
		'contentWidth' => $content_width,
		'months'       => $months,
		'mediaTrash'   => MEDIA_TRASH ? 1 : 0
	);

	$post = null;
	if ( isset( $args['post'] ) ) {
		$post = get_post( $args['post'] );
		$settings['post'] = array(
			'id' => $post->ID,
			'nonce' => wp_create_nonce( 'update-post_' . $post->ID ),
		);

		$thumbnail_support = current_theme_supports( 'post-thumbnails', $post->post_type ) && post_type_supports( $post->post_type, 'thumbnail' );
		if ( ! $thumbnail_support && 'attachment' === $post->post_type && $post->post_mime_type ) {
			if ( wp_attachment_is( 'audio', $post ) ) {
				$thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
			} elseif ( wp_attachment_is( 'video', $post ) ) {
				$thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
			}
		}

		if ( $thumbnail_support ) {
			$featured_image_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			$settings['post']['featuredImageId'] = $featured_image_id ? $featured_image_id : -1;
		}
	}

	if ( $post ) {
		$post_type_object = get_post_type_object( $post->post_type );
	} else {
		$post_type_object = get_post_type_object( 'post' );
	}

	$strings = array(
		// Generic
		'url'         => __( 'URL' ),
		'addMedia'    => __( 'Add Media' ),
		'search'      => __( 'Search' ),
		'select'      => __( 'Select' ),
		'cancel'      => __( 'Cancel' ),
		'update'      => __( 'Update' ),
		'replace'     => __( 'Replace' ),
		'remove'      => __( 'Remove' ),
		'back'        => __( 'Back' ),
		/* translators: This is a would-be plural string used in the media manager.
		   If there is not a word you can use in your language to avoid issues with the
		   lack of plural support here, turn it into "selected: %d" then translate it.
		 */
		'selected'    => __( '%d selected' ),
		'dragInfo'    => __( 'Drag and drop to reorder media files.' ),

		// Upload
		'uploadFilesTitle'  => __( 'Upload Files' ),
		'uploadImagesTitle' => __( 'Upload Images' ),

		// Library
		'mediaLibraryTitle'      => __( 'Media Library' ),
		'insertMediaTitle'       => __( 'Insert Media' ),
		'createNewGallery'       => __( 'Create a new gallery' ),
		'createNewPlaylist'      => __( 'Create a new playlist' ),
		'createNewVideoPlaylist' => __( 'Create a new video playlist' ),
		'returnToLibrary'        => __( '&#8592; Return to library' ),
		'allMediaItems'          => __( 'All media items' ),
		'allDates'               => __( 'All dates' ),
		'noItemsFound'           => __( 'No items found.' ),
		'insertIntoPost'         => $post_type_object->labels->insert_into_item,
		'unattached'             => __( 'Unattached' ),
		'trash'                  => _x( 'Trash', 'noun' ),
		'uploadedToThisPost'     => $post_type_object->labels->uploaded_to_this_item,
		'warnDelete'             => __( "You are about to permanently delete this item.\n  'Cancel' to stop, 'OK' to delete." ),
		'warnBulkDelete'         => __( "You are about to permanently delete these items.\n  'Cancel' to stop, 'OK' to delete." ),
		'warnBulkTrash'          => __( "You are about to trash these items.\n  'Cancel' to stop, 'OK' to delete." ),
		'bulkSelect'             => __( 'Bulk Select' ),
		'cancelSelection'        => __( 'Cancel Selection' ),
		'trashSelected'          => __( 'Trash Selected' ),
		'untrashSelected'        => __( 'Untrash Selected' ),
		'deleteSelected'         => __( 'Delete Selected' ),
		'deletePermanently'      => __( 'Delete Permanently' ),
		'apply'                  => __( 'Apply' ),
		'filterByDate'           => __( 'Filter by date' ),
		'filterByType'           => __( 'Filter by type' ),
		'searchMediaLabel'       => __( 'Search Media' ),
		'noMedia'                => __( 'No media attachments found.' ),

		// Library Details
		'attachmentDetails'  => __( 'Attachment Details' ),

		// From URL
		'insertFromUrlTitle' => __( 'Insert from URL' ),

		// Featured Images
		'setFeaturedImageTitle' => $post_type_object->labels->featured_image,
		'setFeaturedImage'      => $post_type_object->labels->set_featured_image,

		// Gallery
		'createGalleryTitle' => __( 'Create Gallery' ),
		'editGalleryTitle'   => __( 'Edit Gallery' ),
		'cancelGalleryTitle' => __( '&#8592; Cancel Gallery' ),
		'insertGallery'      => __( 'Insert gallery' ),
		'updateGallery'      => __( 'Update gallery' ),
		'addToGallery'       => __( 'Add to gallery' ),
		'addToGalleryTitle'  => __( 'Add to Gallery' ),
		'reverseOrder'       => __( 'Reverse order' ),

		// Edit Image
		'imageDetailsTitle'     => __( 'Image Details' ),
		'imageReplaceTitle'     => __( 'Replace Image' ),
		'imageDetailsCancel'    => __( 'Cancel Edit' ),
		'editImage'             => __( 'Edit Image' ),

		// Crop Image
		'chooseImage' => __( 'Choose Image' ),
		'selectAndCrop' => __( 'Select and Crop' ),
		'skipCropping' => __( 'Skip Cropping' ),
		'cropImage' => __( 'Crop Image' ),
		'cropYourImage' => __( 'Crop your image' ),
		'cropping' => __( 'Cropping&hellip;' ),
		'suggestedDimensions' => __( 'Suggested image dimensions:' ),
		'cropError' => __( 'There has been an error cropping your image.' ),

		// Edit Audio
		'audioDetailsTitle'     => __( 'Audio Details' ),
		'audioReplaceTitle'     => __( 'Replace Audio' ),
		'audioAddSourceTitle'   => __( 'Add Audio Source' ),
		'audioDetailsCancel'    => __( 'Cancel Edit' ),

		// Edit Video
		'videoDetailsTitle'     => __( 'Video Details' ),
		'videoReplaceTitle'     => __( 'Replace Video' ),
		'videoAddSourceTitle'   => __( 'Add Video Source' ),
		'videoDetailsCancel'    => __( 'Cancel Edit' ),
		'videoSelectPosterImageTitle' => __( 'Select Poster Image' ),
		'videoAddTrackTitle'	=> __( 'Add Subtitles' ),

 		// Playlist
 		'playlistDragInfo'    => __( 'Drag and drop to reorder tracks.' ),
 		'createPlaylistTitle' => __( 'Create Audio Playlist' ),
 		'editPlaylistTitle'   => __( 'Edit Audio Playlist' ),
 		'cancelPlaylistTitle' => __( '&#8592; Cancel Audio Playlist' ),
 		'insertPlaylist'      => __( 'Insert audio playlist' ),
 		'updatePlaylist'      => __( 'Update audio playlist' ),
 		'addToPlaylist'       => __( 'Add to audio playlist' ),
 		'addToPlaylistTitle'  => __( 'Add to Audio Playlist' ),

 		// Video Playlist
 		'videoPlaylistDragInfo'    => __( 'Drag and drop to reorder videos.' ),
 		'createVideoPlaylistTitle' => __( 'Create Video Playlist' ),
 		'editVideoPlaylistTitle'   => __( 'Edit Video Playlist' ),
 		'cancelVideoPlaylistTitle' => __( '&#8592; Cancel Video Playlist' ),
 		'insertVideoPlaylist'      => __( 'Insert video playlist' ),
 		'updateVideoPlaylist'      => __( 'Update video playlist' ),
 		'addToVideoPlaylist'       => __( 'Add to video playlist' ),
 		'addToVideoPlaylistTitle'  => __( 'Add to Video Playlist' ),
	);

	/**
	 * Filter the media view settings.
	 *
	 * @since 3.5.0
	 *
	 * @param array   $settings List of media view settings.
	 * @param WP_Post $post     Post object.
	 */
	$settings = apply_filters( 'media_view_settings', $settings, $post );

	/**
	 * Filter the media view strings.
	 *
	 * @since 3.5.0
	 *
	 * @param array   $strings List of media view strings.
	 * @param WP_Post $post    Post object.
	 */
	$strings = apply_filters( 'media_view_strings', $strings,  $post );

	$strings['settings'] = $settings;

	// Ensure we enqueue media-editor first, that way media-views is
	// registered internally before we try to localize it. see #24724.
	wp_enqueue_script( 'media-editor' );
	wp_localize_script( 'media-views', '_wpMediaViewsL10n', $strings );

	wp_enqueue_script( 'media-audiovideo' );
	wp_enqueue_style( 'media-views' );
	if ( is_admin() ) {
		wp_enqueue_script( 'mce-view' );
		wp_enqueue_script( 'image-edit' );
	}
	wp_enqueue_style( 'imgareaselect' );
	wp_plupload_default_settings();

	require_once ABSPATH . WPINC . '/media-template.php';
	add_action( 'admin_footer', 'wp_print_media_templates' );
	add_action( 'wp_footer', 'wp_print_media_templates' );
	add_action( 'customize_controls_print_footer_scripts', 'wp_print_media_templates' );

	/**
	 * Fires at the conclusion of wp_enqueue_media().
	 *
	 * @since 3.5.0
	 */
	do_action( 'wp_enqueue_media' );
}

/**
 * Retrieves media attached to the passed post.
 *
 * @since 3.6.0
 *
 * @param string      $type Mime type.
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return array Found attachments.
 */
function get_attached_media( $type, $post = 0 ) {
	if ( ! $post = get_post( $post ) )
		return array();

	$args = array(
		'post_parent' => $post->ID,
		'post_type' => 'attachment',
		'post_mime_type' => $type,
		'posts_per_page' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC',
	);

	/**
	 * Filter arguments used to retrieve media attached to the given post.
	 *
	 * @since 3.6.0
	 *
	 * @param array  $args Post query arguments.
	 * @param string $type Mime type of the desired media.
	 * @param mixed  $post Post ID or object.
	 */
	$args = apply_filters( 'get_attached_media_args', $args, $type, $post );

	$children = get_children( $args );

	/**
	 * Filter the list of media attached to the given post.
	 *
	 * @since 3.6.0
	 *
	 * @param array  $children Associative array of media attached to the given post.
	 * @param string $type     Mime type of the media desired.
	 * @param mixed  $post     Post ID or object.
	 */
	return (array) apply_filters( 'get_attached_media', $children, $type, $post );
}

/**
 * Check the content blob for an audio, video, object, embed, or iframe tags.
 *
 * @since 3.6.0
 *
 * @param string $content A string which might contain media data.
 * @param array  $types   An array of media types: 'audio', 'video', 'object', 'embed', or 'iframe'.
 * @return array A list of found HTML media embeds.
 */
function get_media_embedded_in_content( $content, $types = null ) {
	$html = array();

	/**
	 * Filter the embedded media types that are allowed to be returned from the content blob.
	 *
	 * @since 4.2.0
	 * @since 4.4.0 Added 'img' to the allowed types.
	 *
	 * @param array $allowed_media_types An array of allowed media types. Default media types are
	 *                                   'audio', 'video', 'object', 'embed', 'iframe', and 'img'.
	 */
	$allowed_media_types = apply_filters( 'media_embedded_in_content_allowed_types', array( 'audio', 'video', 'object', 'embed', 'iframe', 'img' ) );

	if ( ! empty( $types ) ) {
		if ( ! is_array( $types ) ) {
			$types = array( $types );
		}

		$allowed_media_types = array_intersect( $allowed_media_types, $types );
	}

	$tags = implode( '|', $allowed_media_types );

	if ( preg_match_all( '#<(?P<tag>' . $tags . ')[^<]*?(?:>[\s\S]*?<\/(?P=tag)>|\s*\/>)#', $content, $matches ) ) {
		foreach ( $matches[0] as $match ) {
			$html[] = $match;
		}
	}

	return $html;
}

/**
 * Retrieves galleries from the passed post's content.
 *
 * @since 3.6.0
 *
 * @param int|WP_Post $post Post ID or object.
 * @param bool        $html Optional. Whether to return HTML or data in the array. Default true.
 * @return array A list of arrays, each containing gallery data and srcs parsed
 *               from the expanded shortcode.
 */
function get_post_galleries( $post, $html = true ) {
	if ( ! $post = get_post( $post ) )
		return array();

	if ( ! has_shortcode( $post->post_content, 'gallery' ) )
		return array();

	$galleries = array();
	if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $shortcode ) {
			if ( 'gallery' === $shortcode[2] ) {
				$srcs = array();

				$gallery = do_shortcode_tag( $shortcode );
				if ( $html ) {
					$galleries[] = $gallery;
				} else {
					preg_match_all( '#src=([\'"])(.+?)\1#is', $gallery, $src, PREG_SET_ORDER );
					if ( ! empty( $src ) ) {
						foreach ( $src as $s )
							$srcs[] = $s[2];
					}

					$data = shortcode_parse_atts( $shortcode[3] );
					$data['src'] = array_values( array_unique( $srcs ) );
					$galleries[] = $data;
				}
			}
		}
	}

	/**
	 * Filter the list of all found galleries in the given post.
	 *
	 * @since 3.6.0
	 *
	 * @param array   $galleries Associative array of all found post galleries.
	 * @param WP_Post $post      Post object.
	 */
	return apply_filters( 'get_post_galleries', $galleries, $post );
}

/**
 * Check a specified post's content for gallery and, if present, return the first
 *
 * @since 3.6.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @param bool        $html Optional. Whether to return HTML or data. Default is true.
 * @return string|array Gallery data and srcs parsed from the expanded shortcode.
 */
function get_post_gallery( $post = 0, $html = true ) {
	$galleries = get_post_galleries( $post, $html );
	$gallery = reset( $galleries );

	/**
	 * Filter the first-found post gallery.
	 *
	 * @since 3.6.0
	 *
	 * @param array       $gallery   The first-found post gallery.
	 * @param int|WP_Post $post      Post ID or object.
	 * @param array       $galleries Associative array of all found post galleries.
	 */
	return apply_filters( 'get_post_gallery', $gallery, $post, $galleries );
}

/**
 * Retrieve the image srcs from galleries from a post's content, if present
 *
 * @since 3.6.0
 *
 * @see get_post_galleries()
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 * @return array A list of lists, each containing image srcs parsed.
 *               from an expanded shortcode
 */
function get_post_galleries_images( $post = 0 ) {
	$galleries = get_post_galleries( $post, false );
	return wp_list_pluck( $galleries, 'src' );
}

/**
 * Checks a post's content for galleries and return the image srcs for the first found gallery
 *
 * @since 3.6.0
 *
 * @see get_post_gallery()
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 * @return array A list of a gallery's image srcs in order.
 */
function get_post_gallery_images( $post = 0 ) {
	$gallery = get_post_gallery( $post, false );
	return empty( $gallery['src'] ) ? array() : $gallery['src'];
}

/**
 * Maybe attempts to generate attachment metadata, if missing.
 *
 * @since 3.9.0
 *
 * @param WP_Post $attachment Attachment object.
 */
function wp_maybe_generate_attachment_metadata( $attachment ) {
	if ( empty( $attachment ) || ( empty( $attachment->ID ) || ! $attachment_id = (int) $attachment->ID ) ) {
		return;
	}

	$file = get_attached_file( $attachment_id );
	$meta = wp_get_attachment_metadata( $attachment_id );
	if ( empty( $meta ) && file_exists( $file ) ) {
		$_meta = get_post_meta( $attachment_id );
		$regeneration_lock = 'wp_generating_att_' . $attachment_id;
		if ( ! array_key_exists( '_wp_attachment_metadata', $_meta ) && ! get_transient( $regeneration_lock ) ) {
			set_transient( $regeneration_lock, $file );
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
			delete_transient( $regeneration_lock );
		}
	}
}

/**
 * Tries to convert an attachment URL into a post ID.
 *
 * @since 4.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $url The URL to resolve.
 * @return int The found post ID, or 0 on failure.
 */
function attachment_url_to_postid( $url ) {
	global $wpdb;

	$dir = wp_upload_dir();
	$path = $url;

	$site_url = parse_url( $dir['url'] );
	$image_path = parse_url( $path );

	//force the protocols to match if needed
	if ( isset( $image_path['scheme'] ) && ( $image_path['scheme'] !== $site_url['scheme'] ) ) {
		$path = str_replace( $image_path['scheme'], $site_url['scheme'], $path );
	}

	if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
		$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
	}

	$sql = $wpdb->prepare(
		"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = %s",
		$path
	);
	$post_id = $wpdb->get_var( $sql );

	/**
	 * Filter an attachment id found by URL.
	 *
	 * @since 4.2.0
	 *
	 * @param int|null $post_id The post_id (if any) found by the function.
	 * @param string   $url     The URL being looked up.
	 */
	return (int) apply_filters( 'attachment_url_to_postid', $post_id, $url );
}

/**
 * Returns the URLs for CSS files used in an iframe-sandbox'd TinyMCE media view.
 *
 * @since 4.0.0
 *
 * @global string $wp_version
 *
 * @return array The relevant CSS file URLs.
 */
function wpview_media_sandbox_styles() {
 	$version = 'ver=' . $GLOBALS['wp_version'];
 	$mediaelement = includes_url( "js/mediaelement/mediaelementplayer.min.css?$version" );
 	$wpmediaelement = includes_url( "js/mediaelement/wp-mediaelement.css?$version" );

	return array( $mediaelement, $wpmediaelement );
}
