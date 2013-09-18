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
 * @param context Could be 'display' (like in a theme) or 'edit' (like inserting into an editor)
 * @return array Width and height of what the result image should resize to.
 */
function image_constrain_size_for_editor($width, $height, $size = 'medium', $context = null ) {
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
		if ( intval($content_width) > 0 && 'edit' == $context ) // Only in admin. Assume that theme authors know what they're doing.
			$max_width = min( intval($content_width), $max_width );
	}
	// $size == 'full' has no constraint
	else {
		$max_width = $width;
		$max_height = $height;
	}

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

	// plugins can use this to provide resize services
	if ( $out = apply_filters( 'image_downsize', false, $id, $size ) )
		return $out;

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

	$title = $title ? 'title="' . esc_attr( $title ) . '" ' : '';

	$class = 'align' . esc_attr($align) .' size-' . esc_attr($size) . ' wp-image-' . $id;
	$class = apply_filters('get_image_tag_class', $class, $id, $align, $size);

	$html = '<img src="' . esc_attr($img_src) . '" alt="' . esc_attr($alt) . '" ' . $title . $hwstring . 'class="' . $class . '" />';

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
 * Retrieve calculated resized dimensions for use in WP_Image_Editor.
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
 * @param mixed $attr Optional, attributes for the image markup.
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

	$atts = shortcode_atts( array(
		'id'	  => '',
		'align'	  => 'alignnone',
		'width'	  => '',
		'caption' => ''
	), $attr, 'caption' );

	$atts['width'] = (int) $atts['width'];
	if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
		return $content;

	if ( ! empty( $atts['id'] ) )
		$atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';

	$caption_width = 10 + $atts['width'];

	/**
	 * Filter the width of an image's caption.
	 *
	 * By default, the caption is 10 pixels greater than the width of the image,
	 * to prevent post content from running up against a floated image.
	 *
	 * @since 3.7.0
	 *
	 * @param int $caption_width Width in pixels. To remove this inline style, return zero.
	 * @param array $atts {
	 *     The attributes of the caption shortcode.
	 *
	 *     @type string 'id'      The ID of the div element for the caption.
	 *     @type string 'align'   The class name that aligns the caption. Default 'alignnone'.
	 *     @type int    'width'   The width of the image being captioned.
	 *     @type string 'caption' The image's caption.
	 * }
	 * @param string $content The image element, possibly wrapped in a hyperlink.
	 */
	$caption_width = apply_filters( 'img_caption_shortcode_width', $caption_width, $atts, $content );

	$style = '';
	if ( $caption_width )
		$style = 'style="width: ' . (int) $caption_width . 'px" ';

	return '<div ' . $atts['id'] . $style . 'class="wp-caption ' . esc_attr( $atts['align'] ) . '">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">' . $atts['caption'] . '</p></div>';
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

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

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
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr, 'gallery'));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

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
	$icontag = tag_escape($icontag);
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) )
		$itemtag = 'dl';
	if ( ! isset( $valid_tags[ $captiontag ] ) )
		$captiontag = 'dd';
	if ( ! isset( $valid_tags[ $icontag ] ) )
		$icontag = 'dt';

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
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>";
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		if ( ! empty( $attr['link'] ) && 'file' === $attr['link'] )
			$image_output = wp_get_attachment_link( $id, $size, false, false );
		elseif ( ! empty( $attr['link'] ) && 'none' === $attr['link'] )
			$image_output = wp_get_attachment_image( $id, $size, false );
		else
			$image_output = wp_get_attachment_link( $id, $size, true, false );

		$image_meta  = wp_get_attachment_metadata( $id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) )
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
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
 * Provide a No-JS Flash fallback as a last resort for audio / video
 *
 * @since 3.6.0
 *
 * @param string $url
 * @return string Fallback HTML
 */
function wp_mediaelement_fallback( $url ) {
	return apply_filters( 'wp_mediaelement_fallback', sprintf( '<a href="%1$s">%1$s</a>', esc_url( $url ) ), $url );
}

/**
 * Return a filtered list of WP-supported audio formats
 *
 * @since 3.6.0
 * @return array
 */
function wp_get_audio_extensions() {
	return apply_filters( 'wp_audio_extensions', array( 'mp3', 'ogg', 'wma', 'm4a', 'wav' ) );
}

/**
 * The Audio shortcode.
 *
 * This implements the functionality of the Audio Shortcode for displaying
 * WordPress mp3s in a post.
 *
 * @since 3.6.0
 *
 * @param array  $attr    Attributes of the shortcode.
 * @param string $content Optional. Shortcode content.
 * @return string HTML content to display audio.
 */
function wp_audio_shortcode( $attr, $content = '' ) {
	$post_id = get_post() ? get_the_ID() : 0;

	static $instances = 0;
	$instances++;

	/**
	 * Override the default audio shortcode.
	 *
	 * @since 3.7.0
	 *
	 * @param null              Empty variable to be replaced with shortcode markup.
	 * @param array  $attr      Attributes of the shortcode.
	 * @param string $content   Shortcode content.
	 * @param int    $instances Unique numeric ID of this audio shortcode instance.
	 */
	$html = apply_filters( 'wp_audio_shortcode_override', '', $attr, $content, $instances );
	if ( '' !== $html )
		return $html;

	$audio = null;

	$default_types = wp_get_audio_extensions();
	$defaults_atts = array(
		'src'      => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'none'
	);
	foreach ( $default_types as $type )
		$defaults_atts[$type] = '';

	$atts = shortcode_atts( $defaults_atts, $attr, 'audio' );
	extract( $atts );

	$primary = false;
	if ( ! empty( $src ) ) {
		$type = wp_check_filetype( $src, wp_get_mime_types() );
		if ( ! in_array( strtolower( $type['ext'] ), $default_types ) )
			return sprintf( '<a class="wp-embedded-audio" href="%s">%s</a>', esc_url( $src ), esc_html( $src ) );
		$primary = true;
		array_unshift( $default_types, 'src' );
	} else {
		foreach ( $default_types as $ext ) {
			if ( ! empty( $$ext ) ) {
				$type = wp_check_filetype( $$ext, wp_get_mime_types() );
				if ( strtolower( $type['ext'] ) === $ext )
					$primary = true;
			}
		}
	}

	if ( ! $primary ) {
		$audios = get_attached_media( 'audio', $post_id );
		if ( empty( $audios ) )
			return;

		$audio = reset( $audios );
		$src = wp_get_attachment_url( $audio->ID );
		if ( empty( $src ) )
			return;

		array_unshift( $default_types, 'src' );
	}

	$library = apply_filters( 'wp_audio_shortcode_library', 'mediaelement' );
	if ( 'mediaelement' === $library && did_action( 'init' ) ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	$atts = array(
		'class'    => apply_filters( 'wp_audio_shortcode_class', 'wp-audio-shortcode' ),
		'id'       => sprintf( 'audio-%d-%d', $post_id, $instances ),
		'loop'     => $loop,
		'autoplay' => $autoplay,
		'preload'  => $preload,
		'style'    => 'width: 100%',
	);

	// These ones should just be omitted altogether if they are blank
	foreach ( array( 'loop', 'autoplay', 'preload' ) as $a ) {
		if ( empty( $atts[$a] ) )
			unset( $atts[$a] );
	}

	$attr_strings = array();
	foreach ( $atts as $k => $v ) {
		$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
	}

	$html = '';
	if ( 'mediaelement' === $library && 1 === $instances )
		$html .= "<!--[if lt IE 9]><script>document.createElement('audio');</script><![endif]-->\n";
	$html .= sprintf( '<audio %s controls="controls">', join( ' ', $attr_strings ) );

	$fileurl = '';
	$source = '<source type="%s" src="%s" />';
	foreach ( $default_types as $fallback ) {
		if ( ! empty( $$fallback ) ) {
			if ( empty( $fileurl ) )
				$fileurl = $$fallback;
			$type = wp_check_filetype( $$fallback, wp_get_mime_types() );
			$html .= sprintf( $source, $type['type'], esc_url( $$fallback ) );
		}
	}

	if ( 'mediaelement' === $library )
		$html .= wp_mediaelement_fallback( $fileurl );
	$html .= '</audio>';

	return apply_filters( 'wp_audio_shortcode', $html, $atts, $audio, $post_id, $library );
}
add_shortcode( 'audio', 'wp_audio_shortcode' );

/**
 * Return a filtered list of WP-supported video formats
 *
 * @since 3.6.0
 * @return array
 */
function wp_get_video_extensions() {
	return apply_filters( 'wp_video_extensions', array( 'mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv' ) );
}

/**
 * The Video shortcode.
 *
 * This implements the functionality of the Video Shortcode for displaying
 * WordPress mp4s in a post.
 *
 * @since 3.6.0
 *
 * @param array  $attr    Attributes of the shortcode.
 * @param string $content Optional. Shortcode content.
 * @return string HTML content to display video.
 */
function wp_video_shortcode( $attr, $content = '' ) {
	global $content_width;
	$post_id = get_post() ? get_the_ID() : 0;

	static $instances = 0;
	$instances++;

	/**
	 * Override the default video shortcode.
	 *
	 * @since 3.7.0
	 *
	 * @param null              Empty variable to be replaced with shortcode markup.
	 * @param array  $attr      Attributes of the shortcode.
	 * @param string $content   Shortcode content.
	 * @param int    $instances Unique numeric ID of this video shortcode instance.
	 */
	$html = apply_filters( 'wp_video_shortcode_override', '', $attr, $content, $instances );
	if ( '' !== $html )
		return $html;

	$video = null;

	$default_types = wp_get_video_extensions();
	$defaults_atts = array(
		'src'      => '',
		'poster'   => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'metadata',
		'height'   => 360,
		'width'    => empty( $content_width ) ? 640 : $content_width,
	);

	foreach ( $default_types as $type )
		$defaults_atts[$type] = '';

	$atts = shortcode_atts( $defaults_atts, $attr, 'video' );
	extract( $atts );

	$w = $width;
	$h = $height;
	if ( is_admin() && $width > 600 )
		$w = 600;
	elseif ( ! is_admin() && $w > $defaults_atts['width'] )
		$w = $defaults_atts['width'];

	if ( $w < $width )
		$height = round( ( $h * $w ) / $width );

	$width = $w;

	$primary = false;
	if ( ! empty( $src ) ) {
		$type = wp_check_filetype( $src, wp_get_mime_types() );
		if ( ! in_array( strtolower( $type['ext'] ), $default_types ) )
			return sprintf( '<a class="wp-embedded-video" href="%s">%s</a>', esc_url( $src ), esc_html( $src ) );
		$primary = true;
		array_unshift( $default_types, 'src' );
	} else {
		foreach ( $default_types as $ext ) {
			if ( ! empty( $$ext ) ) {
				$type = wp_check_filetype( $$ext, wp_get_mime_types() );
				if ( strtolower( $type['ext'] ) === $ext )
					$primary = true;
			}
		}
	}

	if ( ! $primary ) {
		$videos = get_attached_media( 'video', $post_id );
		if ( empty( $videos ) )
			return;

		$video = reset( $videos );
		$src = wp_get_attachment_url( $video->ID );
		if ( empty( $src ) )
			return;

		array_unshift( $default_types, 'src' );
	}

	$library = apply_filters( 'wp_video_shortcode_library', 'mediaelement' );
	if ( 'mediaelement' === $library && did_action( 'init' ) ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	$atts = array(
		'class'    => apply_filters( 'wp_video_shortcode_class', 'wp-video-shortcode' ),
		'id'       => sprintf( 'video-%d-%d', $post_id, $instances ),
		'width'    => absint( $width ),
		'height'   => absint( $height ),
		'poster'   => esc_url( $poster ),
		'loop'     => $loop,
		'autoplay' => $autoplay,
		'preload'  => $preload,
	);

	// These ones should just be omitted altogether if they are blank
	foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $a ) {
		if ( empty( $atts[$a] ) )
			unset( $atts[$a] );
	}

	$attr_strings = array();
	foreach ( $atts as $k => $v ) {
		$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
	}

	$html = '';
	if ( 'mediaelement' === $library && 1 === $instances )
		$html .= "<!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->\n";
	$html .= sprintf( '<video %s controls="controls">', join( ' ', $attr_strings ) );

	$fileurl = '';
	$source = '<source type="%s" src="%s" />';
	foreach ( $default_types as $fallback ) {
		if ( ! empty( $$fallback ) ) {
			if ( empty( $fileurl ) )
				$fileurl = $$fallback;
			$type = wp_check_filetype( $$fallback, wp_get_mime_types() );
			// m4v sometimes shows up as video/mpeg which collides with mp4
			if ( 'm4v' === $type['ext'] )
				$type['type'] = 'video/m4v';
			$html .= sprintf( $source, $type['type'], esc_url( $$fallback ) );
		}
	}
	if ( 'mediaelement' === $library )
		$html .= wp_mediaelement_fallback( $fileurl );
	$html .= '</video>';

	$html = sprintf( '<div style="width: %dpx; max-width: 100%%;">%s</div>', $width, $html );
	return apply_filters( 'wp_video_shortcode', $html, $atts, $video, $post_id, $library );
}
add_shortcode( 'video', 'wp_video_shortcode' );

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

	$output = $attachment_id = null;
	if ( isset( $attachments[ $k ] ) ) {
		$attachment_id = $attachments[ $k ]->ID;
		$output = wp_get_attachment_link( $attachment_id, $size, true, false, $text );
	}

	$adjacent = $prev ? 'previous' : 'next';
	echo apply_filters( "{$adjacent}_image_link", $output, $attachment_id, $size, $text );
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
 * @since 3.5.0
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
	wp_embed_register_handler( 'audio', '#^https?://.+?\.(' . join( '|', wp_get_audio_extensions() ) . ')$#i', apply_filters( 'wp_audio_embed_handler', 'wp_embed_handler_audio' ), 9999 );
	wp_embed_register_handler( 'video', '#^https?://.+?\.(' . join( '|', wp_get_video_extensions() ) . ')$#i', apply_filters( 'wp_video_embed_handler', 'wp_embed_handler_video' ), 9999 );
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
 * Audio embed handler callback.
 *
 * @since 3.6.0
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_audio( $matches, $attr, $url, $rawattr ) {
	$audio = sprintf( '[audio src="%s" /]', esc_url( $url ) );
	return apply_filters( 'wp_embed_handler_audio', $audio, $attr, $url, $rawattr );
}

/**
 * Video embed handler callback.
 *
 * @since 3.6.0
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_video( $matches, $attr, $url, $rawattr ) {
	$dimensions = '';
	if ( ! empty( $rawattr['width'] ) && ! empty( $rawattr['height'] ) ) {
		$dimensions .= sprintf( 'width="%d" ', (int) $rawattr['width'] );
		$dimensions .= sprintf( 'height="%d" ', (int) $rawattr['height'] );
	}
	$video = sprintf( '[video %s src="%s" /]', $dimensions, esc_url( $url ) );
	return apply_filters( 'wp_embed_handler_video', $video, $attr, $url, $rawattr );
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
		$bytes = intval( $size ) * 1024;
	elseif ( strpos( $size, 'm' ) !== false )
		$bytes = intval($size) * 1024 * 1024;
	elseif ( strpos( $size, 'g' ) !== false )
		$bytes = intval( $size ) * 1024 * 1024 * 1024;
	return $bytes;
}

/**
 * Determine the maximum upload size allowed in php.ini.
 *
 * @since 2.5.0
 *
 * @return int Allowed upload size.
 */
function wp_max_upload_size() {
	$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
	$bytes   = apply_filters( 'upload_size_limit', min( $u_bytes, $p_bytes ), $u_bytes, $p_bytes );
	return $bytes;
}

/**
 * Returns a WP_Image_Editor instance and loads file into it.
 *
 * @since 3.5.0
 * @access public
 *
 * @param string $path Path to file to load
 * @param array $args Additional data. Accepts { 'mime_type'=>string, 'methods'=>{string, string, ...} }
 * @return WP_Image_Editor|WP_Error
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
 * @access public
 *
 * @param string|array $args Array of requirements. Accepts { 'mime_type'=>string, 'methods'=>{string, string, ...} }
 * @return boolean true if an eligible editor is found; false otherwise
 */
function wp_image_editor_supports( $args = array() ) {
	return (bool) _wp_image_editor_choose( $args );
}

/**
 * Tests which editors are capable of supporting the request.
 *
 * @since 3.5.0
 * @access private
 *
 * @param array $args Additional data. Accepts { 'mime_type'=>string, 'methods'=>{string, string, ...} }
 * @return string|bool Class name for the first editor that claims to support the request. False if no editor claims to support the request.
 */
function _wp_image_editor_choose( $args = array() ) {
	require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
	require_once ABSPATH . WPINC . '/class-wp-image-editor-gd.php';
	require_once ABSPATH . WPINC . '/class-wp-image-editor-imagick.php';

	$implementations = apply_filters( 'wp_image_editors',
		array( 'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD' ) );

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
		'url'                 => admin_url( 'async-upload.php', 'relative' ),
		'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		'filters'             => array( array( 'title' => __( 'Allowed Files' ), 'extensions' => '*') ),
		'multipart'           => true,
		'urlstream_upload'    => true,
	);

	// Multi-file uploading doesn't currently work in iOS Safari,
	// single-file allows the built-in camera to be used as source for images
	if ( wp_is_mobile() )
		$defaults['multi_selection'] = false;

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
		'limitExceeded' => is_multisite() && ! is_upload_space_available()
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
	if ( false !== strpos( $attachment->post_mime_type, '/' ) )
		list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
	else
		list( $type, $subtype ) = array( $attachment->post_mime_type, '' );

	$attachment_url = wp_get_attachment_url( $attachment->ID );

	$response = array(
		'id'          => $attachment->ID,
		'title'       => $attachment->post_title,
		'filename'    => wp_basename( $attachment->guid ),
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
		),
		'editLink'   => false,
	);

	if ( current_user_can( 'edit_post', $attachment->ID ) ) {
		$response['nonces']['update'] = wp_create_nonce( 'update-post_' . $attachment->ID );
		$response['editLink'] = get_edit_post_link( $attachment->ID, 'raw' );
	}

	if ( current_user_can( 'delete_post', $attachment->ID ) )
		$response['nonces']['delete'] = wp_create_nonce( 'delete-post_' . $attachment->ID );

	if ( $meta && 'image' === $type ) {
		$sizes = array();
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
	}

	if ( function_exists('get_compat_media_markup') )
		$response['compat'] = get_compat_media_markup( $attachment->ID, array( 'in_modal' => true ) );

	return apply_filters( 'wp_prepare_attachment_for_js', $response, $attachment, $meta );
}

/**
 * Enqueues all scripts, styles, settings, and templates necessary to use
 * all media JS APIs.
 *
 * @since 3.5.0
 */
function wp_enqueue_media( $args = array() ) {

	// Enqueue me just once per page, please.
	if ( did_action( 'wp_enqueue_media' ) )
		return;

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

	$props = array(
		'link'  => get_option( 'image_default_link_type' ), // db default is 'file'
		'align' => get_option( 'image_default_align' ), // empty default
		'size'  => get_option( 'image_default_size' ),  // empty default
	);

	$settings = array(
		'tabs'      => $tabs,
		'tabUrl'    => add_query_arg( array( 'chromeless' => true ), admin_url('media-upload.php') ),
		'mimeTypes' => wp_list_pluck( get_post_mime_types(), 0 ),
		'captions'  => ! apply_filters( 'disable_captions', '' ),
		'nonce'     => array(
			'sendToEditor' => wp_create_nonce( 'media-send-to-editor' ),
		),
		'post'    => array(
			'id' => 0,
		),
		'defaultProps' => $props,
		'embedExts'    => array_merge( wp_get_audio_extensions(), wp_get_video_extensions() ),
	);

	$post = null;
	if ( isset( $args['post'] ) ) {
		$post = get_post( $args['post'] );
		$settings['post'] = array(
			'id' => $post->ID,
			'nonce' => wp_create_nonce( 'update-post_' . $post->ID ),
		);

		if ( current_theme_supports( 'post-thumbnails', $post->post_type ) && post_type_supports( $post->post_type, 'thumbnail' ) ) {
			$featured_image_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			$settings['post']['featuredImageId'] = $featured_image_id ? $featured_image_id : -1;
		}
	}

	$hier = $post && is_post_type_hierarchical( $post->post_type );

	$strings = array(
		// Generic
		'url'         => __( 'URL' ),
		'addMedia'    => __( 'Add Media' ),
		'search'      => __( 'Search' ),
		'select'      => __( 'Select' ),
		'cancel'      => __( 'Cancel' ),
		/* translators: This is a would-be plural string used in the media manager.
		   If there is not a word you can use in your language to avoid issues with the
		   lack of plural support here, turn it into "selected: %d" then translate it.
		 */
		'selected'    => __( '%d selected' ),
		'dragInfo'    => __( 'Drag and drop to reorder images.' ),

		// Upload
		'uploadFilesTitle'  => __( 'Upload Files' ),
		'uploadImagesTitle' => __( 'Upload Images' ),

		// Library
		'mediaLibraryTitle'  => __( 'Media Library' ),
		'insertMediaTitle'   => __( 'Insert Media' ),
		'createNewGallery'   => __( 'Create a new gallery' ),
		'returnToLibrary'    => __( '&#8592; Return to library' ),
		'allMediaItems'      => __( 'All media items' ),
		'noItemsFound'       => __( 'No items found.' ),
		'insertIntoPost'     => $hier ? __( 'Insert into page' ) : __( 'Insert into post' ),
		'uploadedToThisPost' => $hier ? __( 'Uploaded to this page' ) : __( 'Uploaded to this post' ),
		'warnDelete' =>      __( "You are about to permanently delete this item.\n  'Cancel' to stop, 'OK' to delete." ),

		// From URL
		'insertFromUrlTitle' => __( 'Insert from URL' ),

		// Featured Images
		'setFeaturedImageTitle' => __( 'Set Featured Image' ),
		'setFeaturedImage'    => __( 'Set featured image' ),

		// Gallery
		'createGalleryTitle' => __( 'Create Gallery' ),
		'editGalleryTitle'   => __( 'Edit Gallery' ),
		'cancelGalleryTitle' => __( '&#8592; Cancel Gallery' ),
		'insertGallery'      => __( 'Insert gallery' ),
		'updateGallery'      => __( 'Update gallery' ),
		'addToGallery'       => __( 'Add to gallery' ),
		'addToGalleryTitle'  => __( 'Add to Gallery' ),
		'reverseOrder'       => __( 'Reverse order' ),
	);

	$settings = apply_filters( 'media_view_settings', $settings, $post );
	$strings  = apply_filters( 'media_view_strings',  $strings,  $post );

	$strings['settings'] = $settings;

	wp_localize_script( 'media-views', '_wpMediaViewsL10n', $strings );

	wp_enqueue_script( 'media-editor' );
	wp_enqueue_style( 'media-views' );
	wp_plupload_default_settings();

	require_once ABSPATH . WPINC . '/media-template.php';
	add_action( 'admin_footer', 'wp_print_media_templates' );
	add_action( 'wp_footer', 'wp_print_media_templates' );

	do_action( 'wp_enqueue_media' );
}

/**
 * Retrieve media attached to the passed post
 *
 * @since 3.6.0
 *
 * @param string $type (Mime) type of media desired
 * @param mixed $post Post ID or object
 * @return array Found attachments
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

	$args = apply_filters( 'get_attached_media_args', $args, $type, $post );

	$children = get_children( $args );

	return (array) apply_filters( 'get_attached_media', $children, $type, $post );
}

/**
 * Check the content blob for an <audio>, <video> <object>, <embed>, or <iframe>
 *
 * @since 3.6.0
 *
 * @param string $content A string which might contain media data.
 * @param array $types array of media types: 'audio', 'video', 'object', 'embed', or 'iframe'
 * @return array A list of found HTML media embeds
 */
function get_media_embedded_in_content( $content, $types = null ) {
	$html = array();
	$allowed_media_types = array( 'audio', 'video', 'object', 'embed', 'iframe' );
	if ( ! empty( $types ) ) {
		if ( ! is_array( $types ) )
			$types = array( $types );
		$allowed_media_types = array_intersect( $allowed_media_types, $types );
	}

	foreach ( $allowed_media_types as $tag ) {
		if ( preg_match( '#' . get_tag_regex( $tag ) . '#', $content, $matches ) ) {
			$html[] = $matches[0];
		}
	}

	return $html;
}

/**
 * Retrieve galleries from the passed post's content
 *
 * @since 3.6.0
 *
 * @param mixed $post Optional. Post ID or object.
 * @param boolean $html Whether to return HTML or data in the array
 * @return array A list of arrays, each containing gallery data and srcs parsed
 *		from the expanded shortcode
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
				$count = 1;

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

	return apply_filters( 'get_post_galleries', $galleries, $post );
}

/**
 * Check a specified post's content for gallery and, if present, return the first
 *
 * @since 3.6.0
 *
 * @param mixed $post Optional. Post ID or object.
 * @param boolean $html Whether to return HTML or data
 * @return string|array Gallery data and srcs parsed from the expanded shortcode
 */
function get_post_gallery( $post = 0, $html = true ) {
	$galleries = get_post_galleries( $post, $html );
	$gallery = reset( $galleries );

	return apply_filters( 'get_post_gallery', $gallery, $post, $galleries );
}

/**
 * Retrieve the image srcs from galleries from a post's content, if present
 *
 * @since 3.6.0
 *
 * @param mixed $post Optional. Post ID or object.
 * @return array A list of lists, each containing image srcs parsed
 *		from an expanded shortcode
 */
function get_post_galleries_images( $post = 0 ) {
	$galleries = get_post_galleries( $post, false );
	return wp_list_pluck( $galleries, 'src' );
}

/**
 * Check a post's content for galleries and return the image srcs for the first found gallery
 *
 * @since 3.6.0
 *
 * @param mixed $post Optional. Post ID or object.
 * @return array A list of a gallery's image srcs in order
 */
function get_post_gallery_images( $post = 0 ) {
	$gallery = get_post_gallery( $post, false );
	return empty( $gallery['src'] ) ? array() : $gallery['src'];
}
