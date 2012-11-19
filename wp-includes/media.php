<?php
/**
 * WordPress API for media display.
 *
 * @package WordPress
 */

/**
 * Scale down the default size of an image.
 *
 * This is so that the image is a better fit for the editor and theme.
 *
 * The $size parameter accepts either an array or a string. The supported string
 * values are 'thumb' or 'thumbnail' for the given thumbnail size or defaults at
 * 128 width and 96 height in pixels. Also supported for the string value is
 * 'medium' and 'full'. The 'full' isn't actually supported, but any value other
 * than the supported will result in the content_width size or 500 if that is
 * not set.
 *
 * Finally, there is a filter named 'editor_max_image_size', that will be called
 * on the calculated array for width and height, respectively. The second
 * parameter will be the value that was in the $size parameter. The returned
 * type for the hook is an array with the width as the first element and the
 * height as the second element.
 *
 * @since 2.5.0
 * @uses wp_constrain_dimensions() This function passes the widths and the heights.
 *
 * @param int $width Width of the image
 * @param int $height Height of the image
 * @param string|array $size Size of what the result image should be.
 * @return array Width and height of what the result image should resize to.
 */
function image_constrain_size_for_editor($width, $height, $size = 'medium') {
	global $content_width, $_wp_additional_image_sizes;

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
		// if no width is set, default to the theme content width if available
	}
	elseif ( $size == 'large' ) {
		// We're inserting a large size image into the editor. If it's a really
		// big image we'll scale it down to fit reasonably within the editor
		// itself, and within the theme's content width if it's known. The user
		// can resize it in the editor if they wish.
		$max_width = intval(get_option('large_size_w'));
		$max_height = intval(get_option('large_size_h'));
		if ( intval($content_width) > 0 )
			$max_width = min( intval($content_width), $max_width );
	} elseif ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
		$max_width = intval( $_wp_additional_image_sizes[$size]['width'] );
		$max_height = intval( $_wp_additional_image_sizes[$size]['height'] );
		if ( intval($content_width) > 0 && is_admin() ) // Only in admin. Assume that theme authors know what they're doing.
			$max_width = min( intval($content_width), $max_width );
	}
	// $size == 'full' has no constraint
	else {
		$max_width = $width;
		$max_height = $height;
	}

	list( $max_width, $max_height ) = apply_filters( 'editor_max_image_size', array( $max_width, $max_height ), $size );

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
 * @param int|string $width Optional. Width attribute value.
 * @param int|string $height Optional. Height attribute value.
 * @return string HTML attributes for width and, or height.
 */
function image_hwstring($width, $height) {
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
 * @uses apply_filters() Calls 'image_downsize' on $id and $size to provide
 *		resize services.
 *
 * @param int $id Attachment ID for image.
 * @param array|string $size Optional, default is 'medium'. Size of image, either array or string.
 * @return bool|array False on failure, array on success.
 */
function image_downsize($id, $size = 'medium') {

	if ( !wp_attachment_is_image($id) )
		return false;

	$img_url = wp_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);
	$width = $height = 0;
	$is_intermediate = false;
	$img_url_basename = wp_basename($img_url);

	// plugins can use this to provide resize services
	if ( $out = apply_filters('image_downsize', false, $id, $size) )
		return $out;

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
	if ( !$width && !$height && isset($meta['width'], $meta['height']) ) {
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
 * Registers a new image size
 *
 * @since 2.9.0
 */
function add_image_size( $name, $width = 0, $height = 0, $crop = false ) {
	global $_wp_additional_image_sizes;
	$_wp_additional_image_sizes[$name] = array( 'width' => absint( $width ), 'height' => absint( $height ), 'crop' => (bool) $crop );
}

/**
 * Registers an image size for the post thumbnail
 *
 * @since 2.9.0
 */
function set_post_thumbnail_size( $width = 0, $height = 0, $crop = false ) {
	add_image_size( 'post-thumbnail', $width, $height, $crop );
}

/**
 * An <img src /> tag for an image attachment, scaling it down if requested.
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
 * @uses apply_filters() The 'get_image_tag_class' filter is the IMG element
 *		class attribute.
 * @uses apply_filters() The 'get_image_tag' filter is the full IMG element with
 *		all attributes.
 *
 * @param int $id Attachment ID.
 * @param string $alt Image Description for the alt attribute.
 * @param string $title Image Description for the title attribute.
 * @param string $align Part of the class name for aligning the image.
 * @param string $size Optional. Default is 'medium'.
 * @return string HTML IMG element for given image attachment
 */
function get_image_tag($id, $alt, $title, $align, $size='medium') {

	list( $img_src, $width, $height ) = image_downsize($id, $size);
	$hwstring = image_hwstring($width, $height);

	$class = 'align' . esc_attr($align) .' size-' . esc_attr($size) . ' wp-image-' . $id;
	$class = apply_filters('get_image_tag_class', $class, $id, $align, $size);

	$html = '<img src="' . esc_attr($img_src) . '" alt="' . esc_attr($alt) . '" '.$hwstring.'class="'.$class.'" />';

	$html = apply_filters( 'get_image_tag', $html, $id, $alt, $title, $align, $size );

	return $html;
}

/**
 * Calculates the new dimensions for a downsampled image.
 *
 * If either width or height are empty, no constraint is applied on
 * that dimension.
 *
 * @since 2.5.0
 *
 * @param int $current_width Current width of the image.
 * @param int $current_height Current height of the image.
 * @param int $max_width Optional. Maximum wanted width.
 * @param int $max_height Optional. Maximum wanted height.
 * @return array First item is the width, the second item is the height.
 */
function wp_constrain_dimensions( $current_width, $current_height, $max_width=0, $max_height=0 ) {
	if ( !$max_width and !$max_height )
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

	if ( intval( $current_width * $larger_ratio ) > $max_width || intval( $current_height * $larger_ratio ) > $max_height )
 		// The larger ratio is too big. It would result in an overflow.
		$ratio = $smaller_ratio;
	else
		// The larger ratio fits, and is likely to be a more "snug" fit.
		$ratio = $larger_ratio;

	$w = intval( $current_width  * $ratio );
	$h = intval( $current_height * $ratio );

	// Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
	// We also have issues with recursive calls resulting in an ever-changing result. Constraining to the result of a constraint should yield the original result.
	// Thus we look for dimensions that are one pixel shy of the max value and bump them up
	if ( $did_width && $w == $max_width - 1 )
		$w = $max_width; // Round it up
	if ( $did_height && $h == $max_height - 1 )
		$h = $max_height; // Round it up

	return array( $w, $h );
}

/**
 * Retrieve calculated resized dimensions for use in imagecopyresampled().
 *
 * Calculate dimensions and coordinates for a resized image that fits within a
 * specified width and height. If $crop is true, the largest matching central
 * portion of the image will be cropped out and resized to the required size.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'image_resize_dimensions' on $orig_w, $orig_h, $dest_w, $dest_h and
 *		$crop to provide custom resize dimensions.
 *
 * @param int $orig_w Original width.
 * @param int $orig_h Original height.
 * @param int $dest_w New width.
 * @param int $dest_h New height.
 * @param bool $crop Optional, default is false. Whether to crop image or resize.
 * @return bool|array False on failure. Returned array matches parameters for imagecopyresampled() PHP function.
 */
function image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop = false) {

	if ($orig_w <= 0 || $orig_h <= 0)
		return false;
	// at least one of dest_w or dest_h must be specific
	if ($dest_w <= 0 && $dest_h <= 0)
		return false;

	// plugins can use this to provide custom resize dimensions
	$output = apply_filters( 'image_resize_dimensions', null, $orig_w, $orig_h, $dest_w, $dest_h, $crop );
	if ( null !== $output )
		return $output;

	if ( $crop ) {
		// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
		$aspect_ratio = $orig_w / $orig_h;
		$new_w = min($dest_w, $orig_w);
		$new_h = min($dest_h, $orig_h);

		if ( !$new_w ) {
			$new_w = intval($new_h * $aspect_ratio);
		}

		if ( !$new_h ) {
			$new_h = intval($new_w / $aspect_ratio);
		}

		$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

		$crop_w = round($new_w / $size_ratio);
		$crop_h = round($new_h / $size_ratio);

		$s_x = floor( ($orig_w - $crop_w) / 2 );
		$s_y = floor( ($orig_h - $crop_h) / 2 );
	} else {
		// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
		$crop_w = $orig_w;
		$crop_h = $orig_h;

		$s_x = 0;
		$s_y = 0;

		list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
	}

	// if the resulting image would be the same size or larger we don't want to resize it
	if ( $new_w >= $orig_w && $new_h >= $orig_h )
		return false;

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

}

/**
 * Resize an image to make a thumbnail or intermediate size.
 *
 * The returned array has the file size, the image width, and image height. The
 * filter 'image_make_intermediate_size' can be used to hook in and change the
 * values of the returned array. The only parameter is the resized file path.
 *
 * @since 2.5.0
 *
 * @param string $file File path.
 * @param int $width Image width.
 * @param int $height Image height.
 * @param bool $crop Optional, default is false. Whether to crop image to specified height and width or resize.
 * @return bool|array False, if no image was created. Metadata array on success.
 */
function image_make_intermediate_size( $file, $width, $height, $crop = false ) {
	if ( $width || $height ) {
		$editor = WP_Image_Editor::get_instance( $file );

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
 * Retrieve the image's intermediate size (resized) path, width, and height.
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
 * @see add_image_size()
 *
 * @param int $post_id Attachment ID for image.
 * @param array|string $size Optional, default is 'thumbnail'. Size of image, either array or string.
 * @return bool|array False on failure or array of file path, width, and height on success.
 */
function image_get_intermediate_size($post_id, $size='thumbnail') {
	if ( !is_array( $imagedata = wp_get_attachment_metadata( $post_id ) ) )
		return false;

	// get the best one for a specified set of dimensions
	if ( is_array($size) && !empty($imagedata['sizes']) ) {
		foreach ( $imagedata['sizes'] as $_size => $data ) {
			// already cropped to width or height; so use this size
			if ( ( $data['width'] == $size[0] && $data['height'] <= $size[1] ) || ( $data['height'] == $size[1] && $data['width'] <= $size[0] ) ) {
				$file = $data['file'];
				list($width, $height) = image_constrain_size_for_editor( $data['width'], $data['height'], $size );
				return compact( 'file', 'width', 'height' );
			}
			// add to lookup table: area => size
			$areas[$data['width'] * $data['height']] = $_size;
		}
		if ( !$size || !empty($areas) ) {
			// find for the smallest image not smaller than the desired size
			ksort($areas);
			foreach ( $areas as $_size ) {
				$data = $imagedata['sizes'][$_size];
				if ( $data['width'] >= $size[0] || $data['height'] >= $size[1] ) {
					// Skip images with unexpectedly divergent aspect ratios (crops)
					// First, we calculate what size the original image would be if constrained to a box the size of the current image in the loop
					$maybe_cropped = image_resize_dimensions($imagedata['width'], $imagedata['height'], $data['width'], $data['height'], false );
					// If the size doesn't match within one pixel, then it is of a different aspect ratio, so we skip it, unless it's the thumbnail size
					if ( 'thumbnail' != $_size && ( !$maybe_cropped || ( $maybe_cropped[4] != $data['width'] && $maybe_cropped[4] + 1 != $data['width'] ) || ( $maybe_cropped[5] != $data['height'] && $maybe_cropped[5] + 1 != $data['height'] ) ) )
						continue;
					// If we're still here, then we're going to use this size
					$file = $data['file'];
					list($width, $height) = image_constrain_size_for_editor( $data['width'], $data['height'], $size );
					return compact( 'file', 'width', 'height' );
				}
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
	return $data;
}

/**
 * Get the available image sizes
 * @since 3.0.0
 * @return array Returns a filtered array of image size strings
 */
function get_intermediate_image_sizes() {
	global $_wp_additional_image_sizes;
	$image_sizes = array('thumbnail', 'medium', 'large'); // Standard sizes
	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) )
		$image_sizes = array_merge( $image_sizes, array_keys( $_wp_additional_image_sizes ) );

	return apply_filters( 'intermediate_image_sizes', $image_sizes );
}

/**
 * Retrieve an image to represent an attachment.
 *
 * A mime icon for files, thumbnail or intermediate size for images.
 *
 * @since 2.5.0
 *
 * @param int $attachment_id Image attachment ID.
 * @param string $size Optional, default is 'thumbnail'.
 * @param bool $icon Optional, default is false. Whether it is an icon.
 * @return bool|array Returns an array (url, width, height), or false, if no image is available.
 */
function wp_get_attachment_image_src($attachment_id, $size='thumbnail', $icon = false) {

	// get a thumbnail or intermediate image if there is one
	if ( $image = image_downsize($attachment_id, $size) )
		return $image;

	$src = false;

	if ( $icon && $src = wp_mime_type_icon($attachment_id) ) {
		$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/crystal' );
		$src_file = $icon_dir . '/' . wp_basename($src);
		@list($width, $height) = getimagesize($src_file);
	}
	if ( $src && $width && $height )
		return array( $src, $width, $height );
	return false;
}

/**
 * Get an HTML img element representing an image attachment
 *
 * While $size will accept an array, it is better to register a size with
 * add_image_size() so that a cropped version is generated. It's much more
 * efficient than having to find the closest-sized image and then having the
 * browser scale down the image.
 *
 * @see add_image_size()
 * @uses apply_filters() Calls 'wp_get_attachment_image_attributes' hook on attributes array
 * @uses wp_get_attachment_image_src() Gets attachment file URL and dimensions
 * @since 2.5.0
 *
 * @param int $attachment_id Image attachment ID.
 * @param string $size Optional, default is 'thumbnail'.
 * @param bool $icon Optional, default is false. Whether it is an icon.
 * @return string HTML img element or empty string on failure.
 */
function wp_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = false, $attr = '') {

	$html = '';
	$image = wp_get_attachment_image_src($attachment_id, $size, $icon);
	if ( $image ) {
		list($src, $width, $height) = $image;
		$hwstring = image_hwstring($width, $height);
		if ( is_array($size) )
			$size = join('x', $size);
		$attachment = get_post($attachment_id);
		$default_attr = array(
			'src'	=> $src,
			'class'	=> "attachment-$size",
			'alt'	=> trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) )), // Use Alt field first
		);
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_title )); // Finally, use the title

		$attr = wp_parse_args($attr, $default_attr);
		$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment );
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
 * Adds a 'wp-post-image' class to post thumbnails
 * Uses the begin_fetch_post_thumbnail_html and end_fetch_post_thumbnail_html action hooks to
 * dynamically add/remove itself so as to only filter post thumbnails
 *
 * @since 2.9.0
 * @param array $attr Attributes including src, class, alt, title
 * @return array
 */
function _wp_post_thumbnail_class_filter( $attr ) {
	$attr['class'] .= ' wp-post-image';
	return $attr;
}

/**
 * Adds _wp_post_thumbnail_class_filter to the wp_get_attachment_image_attributes filter
 *
 * @since 2.9.0
 */
function _wp_post_thumbnail_class_filter_add( $attr ) {
	add_filter( 'wp_get_attachment_image_attributes', '_wp_post_thumbnail_class_filter' );
}

/**
 * Removes _wp_post_thumbnail_class_filter from the wp_get_attachment_image_attributes filter
 *
 * @since 2.9.0
 */
function _wp_post_thumbnail_class_filter_remove( $attr ) {
	remove_filter( 'wp_get_attachment_image_attributes', '_wp_post_thumbnail_class_filter' );
}

add_shortcode('wp_caption', 'img_caption_shortcode');
add_shortcode('caption', 'img_caption_shortcode');

/**
 * The Caption shortcode.
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
 * @param array $attr Attributes attributed to the shortcode.
 * @param string $content Optional. Shortcode content.
 * @return string
 */
function img_caption_shortcode($attr, $content = null) {
	// New-style shortcode with the caption inside the shortcode with the link and image tags.
	if ( ! isset( $attr['caption'] ) ) {
		if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches ) ) {
			$content = $matches[1];
			$attr['caption'] = trim( $matches[2] );
		}
	}

	// Allow plugins/themes to override the default caption template.
	$output = apply_filters('img_caption_shortcode', '', $attr, $content);
	if ( $output != '' )
		return $output;

	extract(shortcode_atts(array(
		'id'	=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''
	), $attr));

	if ( 1 > (int) $width || empty($caption) )
		return $content;

	if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

	return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
}

add_shortcode('gallery', 'gallery_shortcode');

/**
 * The Gallery shortcode.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * @since 2.5.0
 *
 * @param array $attr Attributes of the shortcode.
 * @return string HTML content to display gallery.
 */
function gallery_shortcode($attr) {
	$post = get_post();

	static $instance = 0;
	$instance++;

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'ids'        => '',
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty( $ids ) ) {
		// 'ids' is explicitly ordered
		$orderby = 'post__in';
		$include = $ids;
	}

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
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
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->";
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon'>
				$link
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<br style="clear: both" />';
	}

	$output .= "
			<br style='clear: both;' />
		</div>\n";

	return $output;
}

/**
 * Display previous image link that has the same post parent.
 *
 * @since 2.5.0
 * @param string $size Optional, default is 'thumbnail'. Size of image, either array or string. 0 or 'none' will default to post_title or $text;
 * @param string $text Optional, default is false. If included, link will reflect $text variable.
 * @return string HTML content.
 */
function previous_image_link($size = 'thumbnail', $text = false) {
	adjacent_image_link(true, $size, $text);
}

/**
 * Display next image link that has the same post parent.
 *
 * @since 2.5.0
 * @param string $size Optional, default is 'thumbnail'. Size of image, either array or string. 0 or 'none' will default to post_title or $text;
 * @param string $text Optional, default is false. If included, link will reflect $text variable.
 * @return string HTML content.
 */
function next_image_link($size = 'thumbnail', $text = false) {
	adjacent_image_link(false, $size, $text);
}

/**
 * Display next or previous image link that has the same post parent.
 *
 * Retrieves the current attachment object from the $post global.
 *
 * @since 2.5.0
 *
 * @param bool $prev Optional. Default is true to display previous link, false for next.
 */
function adjacent_image_link($prev = true, $size = 'thumbnail', $text = false) {
	$post = get_post();
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );

	foreach ( $attachments as $k => $attachment )
		if ( $attachment->ID == $post->ID )
			break;

	$k = $prev ? $k - 1 : $k + 1;

	if ( isset($attachments[$k]) )
		echo wp_get_attachment_link($attachments[$k]->ID, $size, true, false, $text);
}

/**
 * Retrieve taxonomies attached to the attachment.
 *
 * @since 2.5.0
 *
 * @param int|array|object $attachment Attachment ID, Attachment data array, or Attachment data object.
 * @return array Empty array on failure. List of taxonomies on success.
 */
function get_attachment_taxonomies($attachment) {
	if ( is_int( $attachment ) )
		$attachment = get_post($attachment);
	else if ( is_array($attachment) )
		$attachment = (object) $attachment;

	if ( ! is_object($attachment) )
		return array();

	$filename = basename($attachment->guid);

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
 * Return all of the taxonomy names that are registered for attachments.
 *
 * Handles mime-type-specific taxonomies such as attachment:image and attachment:video.
 *
 * @since 3.5.0
 * @see get_attachment_taxonomies()
 * @uses get_taxonomies()
 *
 * @param string $output The type of output to return, either taxonomy 'names' or 'objects'. 'names' is the default.
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
 * Check if the installed version of GD supports particular image type
 *
 * @since 2.9.0
 *
 * @param string $mime_type
 * @return bool
 */
function gd_edit_image_support($mime_type) {
	if ( function_exists('imagetypes') ) {
		switch( $mime_type ) {
			case 'image/jpeg':
				return (imagetypes() & IMG_JPG) != 0;
			case 'image/png':
				return (imagetypes() & IMG_PNG) != 0;
			case 'image/gif':
				return (imagetypes() & IMG_GIF) != 0;
		}
	} else {
		switch( $mime_type ) {
			case 'image/jpeg':
				return function_exists('imagecreatefromjpeg');
			case 'image/png':
				return function_exists('imagecreatefrompng');
			case 'image/gif':
				return function_exists('imagecreatefromgif');
		}
	}
	return false;
}

/**
 * Create new GD image resource with transparency support
 * @TODO: Deprecate if possible.
 *
 * @since 2.9.0
 *
 * @param int $width Image width
 * @param int $height Image height
 * @return image resource
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
 * Register an embed handler. This function should probably only be used for sites that do not support oEmbed.
 *
 * @since 2.9.0
 * @see WP_Embed::register_handler()
 */
function wp_embed_register_handler( $id, $regex, $callback, $priority = 10 ) {
	global $wp_embed;
	$wp_embed->register_handler( $id, $regex, $callback, $priority );
}

/**
 * Unregister a previously registered embed handler.
 *
 * @since 2.9.0
 * @see WP_Embed::unregister_handler()
 */
function wp_embed_unregister_handler( $id, $priority = 10 ) {
	global $wp_embed;
	$wp_embed->unregister_handler( $id, $priority );
}

/**
 * Create default array of embed parameters.
 *
 * The width defaults to the content width as specified by the theme. If the
 * theme does not specify a content width, then 500px is used.
 *
 * The default height is 1.5 times the width, or 1000px, whichever is smaller.
 *
 * The 'embed_defaults' filter can be used to adjust either of these values.
 *
 * @since 2.9.0
 *
 * @return array Default embed parameters.
 */
function wp_embed_defaults() {
	if ( ! empty( $GLOBALS['content_width'] ) )
		$width = (int) $GLOBALS['content_width'];

	if ( empty( $width ) )
		$width = 500;

	$height = min( ceil( $width * 1.5 ), 1000 );

	return apply_filters( 'embed_defaults', compact( 'width', 'height' ) );
}

/**
 * Based on a supplied width/height example, return the biggest possible dimensions based on the max width/height.
 *
 * @since 2.9.0
 * @uses wp_constrain_dimensions() This function passes the widths and the heights.
 *
 * @param int $example_width The width of an example embed.
 * @param int $example_height The height of an example embed.
 * @param int $max_width The maximum allowed width.
 * @param int $max_height The maximum allowed height.
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
 * Attempts to fetch the embed HTML for a provided URL using oEmbed.
 *
 * @since 2.9.0
 * @see WP_oEmbed
 *
 * @uses _wp_oembed_get_object()
 * @uses WP_oEmbed::get_html()
 *
 * @param string $url The URL that should be embedded.
 * @param array $args Additional arguments and parameters.
 * @return bool|string False on failure or the embed HTML on success.
 */
function wp_oembed_get( $url, $args = '' ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$oembed = _wp_oembed_get_object();
	return $oembed->get_html( $url, $args );
}

/**
 * Adds a URL format and oEmbed provider URL pair.
 *
 * @since 2.9.0
 * @see WP_oEmbed
 *
 * @uses _wp_oembed_get_object()
 *
 * @param string $format The format of URL that this provider can handle. You can use asterisks as wildcards.
 * @param string $provider The URL to the oEmbed provider.
 * @param boolean $regex Whether the $format parameter is in a regex format.
 */
function wp_oembed_add_provider( $format, $provider, $regex = false ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$oembed = _wp_oembed_get_object();
	$oembed->providers[$format] = array( $provider, $regex );
}

/**
 * Removes an oEmbed provider.
 *
 * @since 3.5
 * @see WP_oEmbed
 *
 * @uses _wp_oembed_get_object()
 *
 * @param string $format The URL format for the oEmbed provider to remove.
 */
function wp_oembed_remove_provider( $format ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );

	$oembed = _wp_oembed_get_object();

	if ( isset( $oembed->providers[ $format ] ) ) {
		unset( $oembed->providers[ $format ] );
		return true;
	}

	return false;
}

/**
 * Determines if default embed handlers should be loaded.
 *
 * Checks to make sure that the embeds library hasn't already been loaded. If
 * it hasn't, then it will load the embeds library.
 *
 * @since 2.9.0
 */
function wp_maybe_load_embeds() {
	if ( ! apply_filters( 'load_default_embeds', true ) )
		return;
	wp_embed_register_handler( 'googlevideo', '#http://video\.google\.([A-Za-z.]{2,5})/videoplay\?docid=([\d-]+)(.*?)#i', 'wp_embed_handler_googlevideo' );
}

/**
 * The Google Video embed handler callback. Google Video does not support oEmbed.
 *
 * @see WP_Embed::register_handler()
 * @see WP_Embed::shortcode()
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_googlevideo( $matches, $attr, $url, $rawattr ) {
	// If the user supplied a fixed width AND height, use it
	if ( !empty($rawattr['width']) && !empty($rawattr['height']) ) {
		$width  = (int) $rawattr['width'];
		$height = (int) $rawattr['height'];
	} else {
		list( $width, $height ) = wp_expand_dimensions( 425, 344, $attr['width'], $attr['height'] );
	}

	return apply_filters( 'embed_googlevideo', '<embed type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docid=' . esc_attr($matches[2]) . '&amp;hl=en&amp;fs=true" style="width:' . esc_attr($width) . 'px;height:' . esc_attr($height) . 'px" allowFullScreen="true" allowScriptAccess="always" />', $matches, $attr, $url, $rawattr );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.3.0
 *
 * @param unknown_type $size
 * @return unknown
 */
function wp_convert_hr_to_bytes( $size ) {
	$size  = strtolower( $size );
	$bytes = (int) $size;
	if ( strpos( $size, 'k' ) !== false )
		$bytes = intval( $size ) * 1024;
	elseif ( strpos( $size, 'm' ) !== false )
		$bytes = intval($size) * 1024 * 1024;
	elseif ( strpos( $size, 'g' ) !== false )
		$bytes = intval( $size ) * 1024 * 1024 * 1024;
	return $bytes;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.3.0
 *
 * @param unknown_type $bytes
 * @return unknown
 */
function wp_convert_bytes_to_hr( $bytes ) {
	$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
	$log   = log( $bytes, 1024 );
	$power = (int) $log;
	$size  = pow( 1024, $log - $power );
	return $size . $units[$power];
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @return unknown
 */
function wp_max_upload_size() {
	$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
	$bytes   = apply_filters( 'upload_size_limit', min( $u_bytes, $p_bytes ), $u_bytes, $p_bytes );
	return $bytes;
}

/**
 * Prints default plupload arguments.
 *
 * @since 3.4.0
 */
function wp_plupload_default_settings() {
	global $wp_scripts;

	$data = $wp_scripts->get_data( 'wp-plupload', 'data' );
	if ( $data && false !== strpos( $data, '_wpPluploadSettings' ) )
		return;

	$max_upload_size = wp_max_upload_size();

	$defaults = array(
		'runtimes'            => 'html5,silverlight,flash,html4',
		'file_data_name'      => 'async-upload', // key passed to $_FILE.
		'multiple_queues'     => true,
		'max_file_size'       => $max_upload_size . 'b',
		'url'                 => admin_url( 'admin-ajax.php', 'relative' ),
		'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		'filters'             => array( array( 'title' => __( 'Allowed Files' ), 'extensions' => '*') ),
		'multipart'           => true,
		'urlstream_upload'    => true,
	);

	$defaults = apply_filters( 'plupload_default_settings', $defaults );

	$params = array(
		'action' => 'upload-attachment',
	);

	$params = apply_filters( 'plupload_default_params', $params );
	$params['_wpnonce'] = wp_create_nonce( 'media-form' );
	$defaults['multipart_params'] = $params;

	$settings = array(
		'defaults' => $defaults,
		'browser'  => array(
			'mobile'    => wp_is_mobile(),
			'supported' => _device_can_upload(),
		),
	);

	$script = 'var _wpPluploadSettings = ' . json_encode( $settings ) . ';';

	if ( $data )
		$script = "$data\n$script";

	$wp_scripts->add_data( 'wp-plupload', 'data', $script );
}
add_action( 'customize_controls_enqueue_scripts', 'wp_plupload_default_settings' );

/**
 * Prepares an attachment post object for JS, where it is expected
 * to be JSON-encoded and fit into an Attachment model.
 *
 * @since 3.5.0
 *
 * @param mixed $attachment Attachment ID or object.
 * @return array Array of attachment details.
 */
function wp_prepare_attachment_for_js( $attachment ) {
	if ( ! $attachment = get_post( $attachment ) )
	   return;

	if ( 'attachment' != $attachment->post_type )
	   return;

	$meta = wp_get_attachment_metadata( $attachment->ID );
	list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );

	$attachment_url = wp_get_attachment_url( $attachment->ID );

	$response = array(
		'id'          => $attachment->ID,
		'title'       => $attachment->post_title,
		'filename'    => basename( $attachment->guid ),
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
		'mime'        => $attachment->post_mime_type,
		'type'        => $type,
		'subtype'     => $subtype,
		'icon'        => wp_mime_type_icon( $attachment->ID ),
		'dateFormatted' => mysql2date( get_option('date_format'), $attachment->post_date ),
	);

	if ( $meta && 'image' === $type ) {
		$sizes = array();
		$base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );

		if ( isset( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $slug => $size ) {
				$sizes[ $slug ] = array(
					'height'      => $size['height'],
					'width'       => $size['width'],
					'url'         => $base_url . $size['file'],
					'orientation' => $size['height'] > $size['width'] ? 'portrait' : 'landscape',
				);
			}
		}

		$sizes['full'] = array(
			'height'      => $meta['height'],
			'width'       => $meta['width'],
			'url'         => $attachment_url,
			'orientation' => $meta['height'] > $meta['width'] ? 'portrait' : 'landscape',
		);

		$response = array_merge( $response, array( 'sizes' => $sizes ), $sizes['full'] );
	}

	if ( function_exists('get_compat_media_markup') )
		$response['compat'] = get_compat_media_markup( $attachment->ID );

	return apply_filters( 'wp_prepare_attachment_for_js', $response, $attachment, $meta );
}

/**
 * Enqueues all scripts, styles, settings, and templates necessary to use
 * all media JS APIs.
 *
 * @since 3.5.0
 */
function wp_enqueue_media( $args = array() ) {
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

	$tabs = apply_filters( 'media_upload_tabs', $tabs );
	unset( $tabs['type'], $tabs['type_url'], $tabs['gallery'], $tabs['library'] );

	$settings = array(
		'tabs'   => $tabs,
		'tabUrl' => add_query_arg( array(
			'chromeless' => true
		), admin_url('media-upload.php') ),
	);

	if ( isset( $args['post'] ) )
		$settings['postId'] = get_post( $args['post'] )->ID;

	wp_localize_script( 'media-views', '_wpMediaViewsL10n', array(
		// Settings
		'settings' => $settings,

		// Generic
		'url'         => __( 'URL' ),
		'insertMedia' => __( 'Insert Media' ),
		'search'      => __( 'Search' ),
		'select'      => __( 'Select' ),
		'cancel'      => __( 'Cancel' ),
		'addImages'   => __( 'Add images' ),
		'selected'    => __( 'selected' ),
		'dragInfo'    => __( 'Drag and drop to reorder images.' ),

		// Upload
		'uploadFilesTitle'  => __( 'Upload Files' ),
		'selectFiles'       => __( 'Select files' ),
		'uploadImagesTitle' => __( 'Upload Images' ),
		'uploadMoreFiles'   => __( 'Upload more files' ),

		// Library
		'mediaLibraryTitle' => __( 'Media Library' ),
		'createNewGallery'  => __( 'Create a new gallery' ),
		'insertIntoPost'    => __( 'Insert into post' ),
		'returnToLibrary'   => __( '&#8592; Return to library' ),

		// Embed
		'embedFromUrlTitle' => __( 'Embed From URL' ),
		'insertEmbed'       => __( 'Insert embed' ),

		// Gallery
		'createGalleryTitle' => __( 'Create Gallery' ),
		'editGalleryTitle'   => __( 'Edit Gallery' ),
		'cancelGalleryTitle' => __( '&#8592; Cancel Gallery' ),
		'insertGallery'      => __( 'Insert gallery' ),
		'updateGallery'      => __( 'Update gallery' ),
		'continueEditing'    => __( 'Continue editing' ),
		'addToGallery'       => __( 'Add to gallery' ),
	) );

	wp_enqueue_script( 'media-upload' );
	wp_enqueue_style( 'media-views' );
	wp_plupload_default_settings();
	add_action( 'admin_footer', 'wp_print_media_templates' );
	add_action( 'wp_footer', 'wp_print_media_templates' );
}

/**
 * Prints the templates used in the media manager.
 *
 * @since 3.5.0
 */
function wp_print_media_templates( $attachment ) {
	?>
	<script type="text/html" id="tmpl-media-frame">
		<div class="media-frame-menu"></div>
		<div class="media-frame-content"></div>
		<div class="media-frame-sidebar"></div>
		<div class="media-frame-toolbar"></div>
	</script>

	<script type="text/html" id="tmpl-media-modal">
		<div class="media-modal">
			<h3 class="media-modal-title">{{ data.title }}</h3>
			<a class="media-modal-close" href="" title="<?php esc_attr_e('Close'); ?>">&times;</a>
		</div>
		<div class="media-modal-backdrop">
			<div></div>
		</div>
	</script>

	<script type="text/html" id="tmpl-uploader-window">
		<div class="uploader-window-content">
			<h3><?php _e( 'Drop files to upload' ); ?></h3>
		</div>
	</script>

	<script type="text/html" id="tmpl-uploader-inline">
		<div class="uploader-inline-content">
			<div class="pre-upload-ui">
				<?php do_action( 'pre-upload-ui' ); ?>
				<?php do_action( 'pre-plupload-upload-ui' ); ?>
			</div>

			<div class="upload-ui">
				<h3><?php _e( 'Drop files anywhere to upload' ); ?></h3>
				<a href="#" class="browser button button-hero"><?php _e( 'Select Files' ); ?></a>
			</div>

			<div class="post-upload-ui">
				<?php do_action( 'post-plupload-upload-ui' ); ?>
				<?php do_action( 'post-upload-ui' ); ?>
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-attachment">
		<div class="attachment-preview type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
			<# if ( data.uploading ) { #>
				<div class="media-progress-bar"><div></div></div>
			<# } else if ( 'image' === data.type ) { #>
				<div class="thumbnail">
					<div class="centered">
						<img src="{{ data.size.url }}" draggable="false" />
					</div>
				</div>
			<# } else { #>
				<img src="{{ data.icon }}" class="icon" draggable="false" />
				<div class="filename">{{ data.filename }}</div>
			<# } #>

			<# if ( data.buttons.close ) { #>
				<a class="close button" href="#">&times;</a>
			<# } #>
		</div>
		<# if ( data.describe ) { #>
			<# if ( 'image' === data.type ) { #>
				<input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
					placeholder="<?php esc_attr_e('Describe this image&hellip;'); ?>" />
			<# } else { #>
				<input type="text" value="{{ data.title }}" class="describe" data-setting="title"
					<# if ( 'video' === data.type ) { #>
						placeholder="<?php esc_attr_e('Describe this video&hellip;'); ?>"
					<# } else if ( 'audio' === data.type ) { #>
						placeholder="<?php esc_attr_e('Describe this audio file&hellip;'); ?>"
					<# } else { #>
						placeholder="<?php esc_attr_e('Describe this media file&hellip;'); ?>"
					<# } #> />
			<# } #>
		<# } #>
	</script>

	<script type="text/html" id="tmpl-attachment-details">
		<h3><?php _e('Attachment Details'); ?></h3>
		<div class="attachment-info">
			<div class="thumbnail">
				<# if ( data.uploading ) { #>
					<div class="media-progress-bar"><div></div></div>
				<# } else if ( 'image' === data.type ) { #>
					<img src="{{ data.size.url }}" draggable="false" />
				<# } else { #>
					<img src="{{ data.icon }}" class="icon" draggable="false" />
				<# } #>
			</div>
			<div class="details">
				<div class="filename">{{ data.filename }}</div>
				<div class="uploaded">{{ data.dateFormatted }}</div>
				<# if ( 'image' === data.type && ! data.uploading ) { #>
					<div class="dimensions">{{ data.width }} &times; {{ data.height }}</div>
				<# } #>
			</div>
			<div class="compat-meta">
				<# if ( data.compat && data.compat.meta ) { #>
					{{{ data.compat.meta }}}
				<# } #>
			</div>
		</div>

		<# if ( 'image' === data.type ) { #>
			<label class="setting" data-setting="title">
				<span><?php _e('Title'); ?></span>
				<input type="text" value="{{ data.title }}" />
			</label>
			<label class="setting" data-setting="caption">
				<span><?php _e('Caption'); ?></span>
				<textarea
					placeholder="<?php esc_attr_e('Describe this image&hellip;'); ?>"
					>{{ data.caption }}</textarea>
			</label>
			<label class="setting" data-setting="alt">
				<span><?php _e('Alt Text'); ?></span>
				<input type="text" value="{{ data.alt }}" />
			</label>
		<# } else { #>
			<label class="setting" data-setting="title">
				<span><?php _e('Title'); ?></span>
				<input type="text" value="{{ data.title }}"
				<# if ( 'video' === data.type ) { #>
					placeholder="<?php esc_attr_e('Describe this video&hellip;'); ?>"
				<# } else if ( 'audio' === data.type ) { #>
					placeholder="<?php esc_attr_e('Describe this audio file&hellip;'); ?>"
				<# } else { #>
					placeholder="<?php esc_attr_e('Describe this media file&hellip;'); ?>"
				<# } #>/>
			</label>
		<# } #>
	</script>

	<script type="text/html" id="tmpl-media-selection">
		<div class="selection-info">
			<span class="count"></span>
			<# if ( data.editable ) { #>
				<a class="edit-selection" href="#"><?php _e('Edit'); ?></a>
			<# } #>
			<# if ( data.clearable ) { #>
				<a class="clear-selection" href="#"><?php _e('Clear'); ?></a>
			<# } #>
		</div>
		<div class="selection-view"></div>
	</script>

	<script type="text/html" id="tmpl-media-selection-preview">
		<div class="selected-img selected-count-{{ data.count }}">
			<# if ( data.thumbnail ) { #>
				<img src="{{ data.thumbnail }}" draggable="false" />
			<# } #>

			<span class="count">{{ data.count }}</span>
		</div>
		<# if ( data.clearable ) { #>
			<a class="clear-selection" href="#"><?php _e('Clear selection'); ?></a>
		<# } #>
	</script>

	<script type="text/html" id="tmpl-attachment-display-settings">
		<h3><?php _e('Attachment Display Settings'); ?></h3>

		<label class="setting">
			<span><?php _e('Alignment'); ?></span>
			<select class="alignment"
				data-setting="align"
				<# if ( data.userSettings ) { #>
					data-user-setting="align"
				<# } #>>

				<option value="left">
					<?php esc_attr_e('Left'); ?>
				</option>
				<option value="center">
					<?php esc_attr_e('Center'); ?>
				</option>
				<option value="right">
					<?php esc_attr_e('Right'); ?>
				</option>
				<option value="none" selected>
					<?php esc_attr_e('None'); ?>
				</option>
			</select>
		</label>

		<div class="setting">
			<label>
				<span><?php _e('Link To'); ?></span>
				<select class="link-to"
					data-setting="link"
					<# if ( data.userSettings ) { #>
						data-user-setting="urlbutton"
					<# } #>>

					<option value="custom">
						<?php esc_attr_e('Custom URL'); ?>
					</option>
					<option value="post" selected>
						<?php esc_attr_e('Attachment Page'); ?>
					</option>
					<option value="file">
						<?php esc_attr_e('Media File'); ?>
					</option>
					<option value="none">
						<?php esc_attr_e('None'); ?>
					</option>
				</select>
			</label>
			<input type="text" class="link-to-custom" data-setting="linkUrl" />
		</div>

		<# if ( 'undefined' !== typeof data.sizes ) { #>
			<label class="setting">
				<span><?php _e('Size'); ?></span>
				<select class="size" name="size"
					data-setting="size"
					<# if ( data.userSettings ) { #>
						data-user-setting="imgsize"
					<# } #>>
					<?php

					$sizes = apply_filters( 'image_size_names_choose', array(
						'thumbnail' => __('Thumbnail'),
						'medium'    => __('Medium'),
						'large'     => __('Large'),
						'full'      => __('Full Size'),
					) );

					foreach ( $sizes as $value => $name ) : ?>
						<#
						var size = data.sizes['<?php echo esc_js( $value ); ?>'];
						if ( size ) { #>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'medium' ); ?>>
								<?php echo esc_html( $name ); ?> &ndash; {{ size.width }} &times; {{ size.height }}
							</option>
						<# } #>>
					<?php endforeach; ?>
				</select>
			</label>
		<# } #>
	</script>

	<script type="text/html" id="tmpl-gallery-settings">
		<h3><?php _e('Gallery Settings'); ?></h3>

		<label class="setting">
			<span><?php _e('Link To'); ?></span>
			<select class="link-to"
				data-setting="link"
				<# if ( data.userSettings ) { #>
					data-user-setting="urlbutton"
				<# } #>>

				<option value="post" selected>
					<?php esc_attr_e('Attachment Page'); ?>
				</option>
				<option value="file">
					<?php esc_attr_e('Media File'); ?>
				</option>
			</select>
		</label>

		<label class="setting">
			<span><?php _e('Columns'); ?></span>
			<select class="columns" name="columns"
				data-setting="columns">
				<?php for ( $i = 1; $i <= 9; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, 3 ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</label>
	</script>

	<script type="text/html" id="tmpl-embed-link-settings">
		<label class="setting">
			<span><?php _e('Title'); ?></span>
			<input type="text" class="alignment" data-setting="title" />
		</label>
	</script>

	<script type="text/html" id="tmpl-embed-image-settings">
		<div class="thumbnail">
			<img src="{{ data.model.url }}" draggable="false" />
		</div>

		<label class="setting caption">
			<span><?php _e('Caption'); ?></span>
			<textarea data-setting="caption" />
		</label>

		<label class="setting alt-text">
			<span><?php _e('Alt Text'); ?></span>
			<input type="text" data-setting="alt" />
		</label>

		<div class="setting align">
			<span><?php _e('Align'); ?></span>
			<div class="button-group button-large" data-setting="align">
				<button class="button" value="left">
					<?php esc_attr_e('Left'); ?>
				</button>
				<button class="button" value="center">
					<?php esc_attr_e('Center'); ?>
				</button>
				<button class="button" value="right">
					<?php esc_attr_e('Right'); ?>
				</button>
				<button class="button active" value="none">
					<?php esc_attr_e('None'); ?>
				</button>
			</div>
		</div>

		<div class="setting link-to">
			<span><?php _e('Link To'); ?></span>
			<div class="button-group button-large" data-setting="link">
				<button class="button" value="file">
					<?php esc_attr_e('Image URL'); ?>
				</button>
				<button class="button" value="custom">
					<?php esc_attr_e('Custom URL'); ?>
				</button>
				<button class="button active" value="none">
					<?php esc_attr_e('None'); ?>
				</button>
			</div>
			<input type="text" class="link-to-custom" data-setting="linkUrl" />
		</div>
	</script>

	<script type="text/html" id="tmpl-attachments-css">
		<style type="text/css" id="{{ data.id }}-css">
			#{{ data.id }} {
				padding: 0 {{ data.gutter }}px;
			}

			#{{ data.id }} .attachment {
				margin: {{ data.gutter }}px;
				width: {{ data.edge }}px;
			}

			#{{ data.id }} .attachment-preview,
			#{{ data.id }} .attachment-preview .thumbnail {
				width: {{ data.edge }}px;
				height: {{ data.edge }}px;
			}

			#{{ data.id }} .portrait .thumbnail img {
				width: {{ data.edge }}px;
				height: auto;
			}

			#{{ data.id }} .landscape .thumbnail img {
				width: auto;
				height: {{ data.edge }}px;
			}
		</style>
	</script>
	<?php
}
