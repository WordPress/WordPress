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
 * Finally, there is a filter named, 'editor_max_image_size' that will be called
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
		// we're inserting a large size image into the editor.  if it's a really
		// big image we'll scale it down to fit reasonably within the editor
		// itself, and within the theme's content width if it's known.  the user
		// can resize it in the editor if they wish.
		$max_width = intval(get_option('large_size_w'));
		$max_height = intval(get_option('large_size_h'));
		if ( intval($content_width) > 0 )
			$max_width = min( intval($content_width), $max_width );
	} elseif ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
		$max_width = intval( $_wp_additional_image_sizes[$size]['width'] );
		$max_height = intval( $_wp_additional_image_sizes[$size]['height'] );
		if ( intval($content_width) > 0 )
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
 * @param string $size Optional, default is 'medium'. Size of image, can be 'thumbnail'.
 * @return bool|array False on failure, array on success.
 */
function image_downsize($id, $size = 'medium') {

	if ( !wp_attachment_is_image($id) )
		return false;

	$img_url = wp_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);
	$width = $height = 0;
	$is_intermediate = false;

	// plugins can use this to provide resize services
	if ( $out = apply_filters('image_downsize', false, $id, $size) )
		return $out;

	// try for a new style intermediate size
	if ( $intermediate = image_get_intermediate_size($id, $size) ) {
		$img_url = str_replace(basename($img_url), $intermediate['file'], $img_url);
		$width = $intermediate['width'];
		$height = $intermediate['height'];
		$is_intermediate = true;
	}
	elseif ( $size == 'thumbnail' ) {
		// fall back to the old thumbnail
		if ( ($thumb_file = wp_get_attachment_thumb_file($id)) && $info = getimagesize($thumb_file) ) {
			$img_url = str_replace(basename($img_url), basename($thumb_file), $img_url);
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
 */
function add_image_size( $name, $width = 0, $height = 0, $crop = FALSE ) {
	global $_wp_additional_image_sizes;
	$_wp_additional_image_sizes[$name] = array( 'width' => absint( $width ), 'height' => absint( $height ), 'crop' => !!$crop );
}

/**
 * Registers an image size for the post thumbnail
 */
function set_post_thumbnail_size( $width = 0, $height = 0, $crop = FALSE ) {
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

	$html = '<img src="' . esc_attr($img_src) . '" alt="' . esc_attr($alt) . '" title="' . esc_attr($title).'" '.$hwstring.'class="'.$class.'" />';

	$html = apply_filters( 'get_image_tag', $html, $id, $alt, $title, $align, $size );

	return $html;
}

/**
 * Calculates the new dimentions for a downsampled image.
 *
 * Same as {@link wp_shrink_dimensions()}, except the max parameters are
 * optional. If either width or height are empty, no constraint is applied on
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

	if ( $max_width > 0 && $current_width > 0 && $current_width > $max_width )
		$width_ratio = $max_width / $current_width;

	if ( $max_height > 0 && $current_height > 0 && $current_height > $max_height )
		$height_ratio = $max_height / $current_height;

	// the smaller ratio is the one we need to fit it to the constraining box
	$ratio = min( $width_ratio, $height_ratio );

	return array( intval($current_width * $ratio), intval($current_height * $ratio) );
}

/**
 * Retrieve calculated resized dimensions for use in imagecopyresampled().
 *
 * Calculate dimensions and coordinates for a resized image that fits within a
 * specified width and height. If $crop is true, the largest matching central
 * portion of the image will be cropped out and resized to the required size.
 *
 * @since 2.5.0
 *
 * @param int $orig_w Original width.
 * @param int $orig_h Original height.
 * @param int $dest_w New width.
 * @param int $dest_h New height.
 * @param bool $crop Optional, default is false. Whether to crop image or resize.
 * @return bool|array False, on failure. Returned array matches parameters for imagecopyresampled() PHP function.
 */
function image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop = false) {

	if ($orig_w <= 0 || $orig_h <= 0)
		return false;
	// at least one of dest_w or dest_h must be specific
	if ($dest_w <= 0 && $dest_h <= 0)
		return false;

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
 * Scale down an image to fit a particular size and save a new copy of the image.
 *
 * The PNG transparency will be preserved using the function, as well as the
 * image type. If the file going in is PNG, then the resized image is going to
 * be PNG. The only supported image types are PNG, GIF, and JPEG.
 *
 * Some functionality requires API to exist, so some PHP version may lose out
 * support. This is not the fault of WordPress (where functionality is
 * downgraded, not actual defects), but of your PHP version.
 *
 * @since 2.5.0
 *
 * @param string $file Image file path.
 * @param int $max_w Maximum width to resize to.
 * @param int $max_h Maximum height to resize to.
 * @param bool $crop Optional. Whether to crop image or resize.
 * @param string $suffix Optional. File Suffix.
 * @param string $dest_path Optional. New image file path.
 * @param int $jpeg_quality Optional, default is 90. Image quality percentage.
 * @return mixed WP_Error on failure. String with new destination path. Array of dimensions from {@link image_resize_dimensions()}
 */
function image_resize( $file, $max_w, $max_h, $crop = false, $suffix = null, $dest_path = null, $jpeg_quality = 90 ) {

	$image = wp_load_image( $file );
	if ( !is_resource( $image ) )
		return new WP_Error( 'error_loading_image', $image, $file );

	$size = @getimagesize( $file );
	if ( !$size )
		return new WP_Error('invalid_image', __('Could not read image size'), $file);
	list($orig_w, $orig_h, $orig_type) = $size;

	$dims = image_resize_dimensions($orig_w, $orig_h, $max_w, $max_h, $crop);
	if ( !$dims )
		return $dims;
	list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

	$newimage = wp_imagecreatetruecolor( $dst_w, $dst_h );

	imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	// convert from full colors to index colors, like original PNG.
	if ( IMAGETYPE_PNG == $orig_type && !imageistruecolor( $image ) )
		imagetruecolortopalette( $newimage, false, imagecolorstotal( $image ) );

	// we don't need the original in memory anymore
	imagedestroy( $image );

	// $suffix will be appended to the destination filename, just before the extension
	if ( !$suffix )
		$suffix = "{$dst_w}x{$dst_h}";

	$info = pathinfo($file);
	$dir = $info['dirname'];
	$ext = $info['extension'];
	$name = basename($file, ".{$ext}");
	if ( !is_null($dest_path) and $_dest_path = realpath($dest_path) )
		$dir = $_dest_path;
	$destfilename = "{$dir}/{$name}-{$suffix}.{$ext}";

	if ( IMAGETYPE_GIF == $orig_type ) {
		if ( !imagegif( $newimage, $destfilename ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	} elseif ( IMAGETYPE_PNG == $orig_type ) {
		if ( !imagepng( $newimage, $destfilename ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	} else {
		// all other formats are converted to jpg
		$destfilename = "{$dir}/{$name}-{$suffix}.jpg";
		if ( !imagejpeg( $newimage, $destfilename, apply_filters( 'jpeg_quality', $jpeg_quality, 'image_resize' ) ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	}

	imagedestroy( $newimage );

	// Set correct file permissions
	$stat = stat( dirname( $destfilename ));
	$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
	@ chmod( $destfilename, $perms );

	return $destfilename;
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
function image_make_intermediate_size($file, $width, $height, $crop=false) {
	if ( $width || $height ) {
		$resized_file = image_resize($file, $width, $height, $crop);
		if ( !is_wp_error($resized_file) && $resized_file && $info = getimagesize($resized_file) ) {
			$resized_file = apply_filters('image_make_intermediate_size', $resized_file);
			return array(
				'file' => basename( $resized_file ),
				'width' => $info[0],
				'height' => $info[1],
			);
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
 * @since 2.5.0
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
 * @since 3.0
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
		$src_file = $icon_dir . '/' . basename($src);
		@list($width, $height) = getimagesize($src_file);
	}
	if ( $src && $width && $height )
		return array( $src, $width, $height );
	return false;
}

/**
 * Get an HTML img element representing an image attachment
 *
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
		$attachment =& get_post($attachment_id);
		$default_attr = array(
			'src'	=> $src,
			'class'	=> "attachment-$size",
			'alt'	=> trim(strip_tags( $attachment->post_excerpt )),
			'title'	=> trim(strip_tags( $attachment->post_title )),
		);
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
 * Adds a 'wp-post-image' class to post thumbnail thumbnails
 * Uses the begin_fetch_post_thumbnail_html and end_fetch_post_thumbnail_html action hooks to
 * dynamically add/remove itself so as to only filter post thumbnail thumbnails
 *
 * @author Mark Jaquith
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
 * @author Mark Jaquith
 * @since 2.9.0
 */
function _wp_post_thumbnail_class_filter_add( $attr ) {
	add_filter( 'wp_get_attachment_image_attributes', '_wp_post_thumbnail_class_filter' );
}

/**
 * Removes _wp_post_thumbnail_class_filter from the wp_get_attachment_image_attributes filter
 *
 * @author Mark Jaquith
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
 * @param array $attr Attributes attributed to the shortcode.
 * @return string HTML content to display gallery.
 */
function gallery_shortcode($attr) {
	global $post, $wp_locale;

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
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
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
	$float = $wp_locale->text_direction == 'rtl' ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$output = apply_filters('gallery_style', "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->
		<div id='$selector' class='gallery galleryid-{$id}'>");

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
				<{$captiontag} class='gallery-caption'>
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
 * @param bool $prev Optional. Default is true to display previous link, true for next.
 */
function adjacent_image_link($prev = true, $size = 'thumbnail', $text = false) {
	global $post;
	$post = get_post($post);
	$attachments = array_values(get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') ));

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
 * Check if the installed version of GD supports particular image type
 *
 * @since 2.9.0
 *
 * @param $mime_type string
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
 *
 * @since 2.9.0
 *
 * @param $width
 * @param $height
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
 * API for easily embedding rich media such as videos and images into content.
 *
 * @package WordPress
 * @subpackage Embed
 * @since 2.9.0
 */
class WP_Embed {
	var $handlers = array();
	var $post_ID;
	var $usecache = true;
	var $linkifunknown = true;

	/**
	 * PHP4 constructor
	 */
	function WP_Embed() {
		return $this->__construct();
	}

	/**
	 * PHP5 constructor
	 */
	function __construct() {
		// Hack to get the [embed] shortcode to run before wpautop()
		add_filter( 'the_content', array(&$this, 'run_shortcode'), 8 );

		// Attempts to embed all URLs in a post
		if ( get_option('embed_autourls') )
			add_filter( 'the_content', array(&$this, 'autoembed'), 8 );

		// After a post is saved, invalidate the oEmbed cache
		add_action( 'save_post', array(&$this, 'delete_oembed_caches') );

		// After a post is saved, cache oEmbed items via AJAX
		add_action( 'edit_form_advanced', array(&$this, 'maybe_run_ajax_cache') );
	}

	/**
	 * Process the [embed] shortcode.
	 *
	 * Since the [embed] shortcode needs to be run earlier than other shortcodes,
	 * this function removes all existing shortcodes, registers the [embed] shortcode,
	 * calls {@link do_shortcode()}, and then re-registers the old shortcodes.
	 *
	 * @uses $shortcode_tags
	 * @uses remove_all_shortcodes()
	 * @uses add_shortcode()
	 * @uses do_shortcode()
	 *
	 * @param string $content Content to parse
	 * @return string Content with shortcode parsed
	 */
	function run_shortcode( $content ) {
		global $shortcode_tags;

		// Backup current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		remove_all_shortcodes();

		add_shortcode( 'embed', array(&$this, 'shortcode') );

		// Do the shortcode (only the [embed] one is registered)
		$content = do_shortcode( $content );

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}

	/**
	 * If a post/page was saved, then output Javascript to make
	 * an AJAX request that will call WP_Embed::cache_oembed().
	 */
	function maybe_run_ajax_cache() {
		global $post_ID;

		if ( empty($post_ID) || empty($_GET['message']) || 1 != $_GET['message'] )
			return;

?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($){
		$.get("<?php echo admin_url( 'admin-ajax.php?action=oembed-cache&post=' . $post_ID ); ?>");
	});
/* ]]> */
</script>
<?php
	}

	/**
	 * Register an embed handler. Do not use this function directly, use {@link wp_embed_register_handler()} instead.
	 * This function should probably also only be used for sites that do not support oEmbed.
	 *
	 * @param string $id An internal ID/name for the handler. Needs to be unique.
	 * @param string $regex The regex that will be used to see if this handler should be used for a URL.
	 * @param callback $callback The callback function that will be called if the regex is matched.
	 * @param int $priority Optional. Used to specify the order in which the registered handlers will be tested (default: 10). Lower numbers correspond with earlier testing, and handlers with the same priority are tested in the order in which they were added to the action.
	 */
	function register_handler( $id, $regex, $callback, $priority = 10 ) {
		$this->handlers[$priority][$id] = array(
			'regex'    => $regex,
			'callback' => $callback,
		);
	}

	/**
	 * Unregister a previously registered embed handler. Do not use this function directly, use {@link wp_embed_unregister_handler()} instead.
	 *
	 * @param string $id The handler ID that should be removed.
	 * @param int $priority Optional. The priority of the handler to be removed (default: 10).
	 */
	function unregister_handler( $id, $priority = 10 ) {
		if ( isset($this->handlers[$priority][$id]) )
			unset($this->handlers[$priority][$id]);
	}

	/**
	 * The {@link do_shortcode()} callback function.
	 *
	 * Attempts to convert a URL into embed HTML. Starts by checking the URL against the regex of the registered embed handlers.
	 * If none of the regex matches and it's enabled, then the URL will be given to the {@link WP_oEmbed} class.
	 *
	 * @uses wp_oembed_get()
	 * @uses wp_parse_args()
	 * @uses wp_embed_defaults()
	 * @uses WP_Embed::maybe_make_link()
	 * @uses get_option()
	 * @uses current_user_can()
	 * @uses wp_cache_get()
	 * @uses wp_cache_set()
	 * @uses get_post_meta()
	 * @uses update_post_meta()
	 *
	 * @param array $attr Shortcode attributes.
	 * @param string $url The URL attempting to be embeded.
	 * @return string The embed HTML on success, otherwise the original URL.
	 */
	function shortcode( $attr, $url = '' ) {
		global $post;

		if ( empty($url) )
			return '';

		$rawattr = $attr;
		$attr = wp_parse_args( $attr, wp_embed_defaults() );

		// Look for known internal handlers
		ksort( $this->handlers );
		foreach ( $this->handlers as $priority => $handlers ) {
			foreach ( $handlers as $id => $handler ) {
				if ( preg_match( $handler['regex'], $url, $matches ) && is_callable( $handler['callback'] ) ) {
					if ( false !== $return = call_user_func( $handler['callback'], $matches, $attr, $url, $rawattr ) )
						return apply_filters( 'embed_handler_html', $return, $url, $attr );
				}
			}
		}

		$post_ID = ( !empty($post->ID) ) ? $post->ID : null;
		if ( !empty($this->post_ID) ) // Potentially set by WP_Embed::cache_oembed()
			$post_ID = $this->post_ID;

		// Unknown URL format. Let oEmbed have a go.
		if ( $post_ID ) {

			// Check for a cached result (stored in the post meta)
			$cachekey = '_oembed_' . md5( $url . serialize( $attr ) );
			if ( $this->usecache ) {
				$cache = get_post_meta( $post_ID, $cachekey, true );

				// Failures are cached
				if ( '{{unknown}}' === $cache )
					return $this->maybe_make_link( $url );

				if ( !empty($cache) )
					return apply_filters( 'embed_oembed_html', $cache, $url, $attr );
			}

			// Use oEmbed to get the HTML
			$attr['discover'] = ( apply_filters('embed_oembed_discover', false) && author_can( $post_ID, 'unfiltered_html' ) ) ? true : false;
			$html = wp_oembed_get( $url, $attr );

			// Cache the result
			$cache = ( $html ) ? $html : '{{unknown}}';
			update_post_meta( $post_ID, $cachekey, $cache );

			// If there was a result, return it
			if ( $html )
				return apply_filters( 'embed_oembed_html', $html, $url, $attr );
		}

		// Still unknown
		return $this->maybe_make_link( $url );
	}

	/**
	 * Delete all oEmbed caches.
	 *
	 * @param int $post_ID Post ID to delete the caches for.
	 */
	function delete_oembed_caches( $post_ID ) {
		$post_metas = get_post_custom_keys( $post_ID );
		if ( empty($post_metas) )
			return;

		foreach( $post_metas as $post_meta_key ) {
			if ( '_oembed_' == substr( $post_meta_key, 0, 8 ) )
				delete_post_meta( $post_ID, $post_meta_key );
		}
	}

	/**
	 * Triggers a caching of all oEmbed results.
	 *
	 * @param int $post_ID Post ID to do the caching for.
	 */
	function cache_oembed( $post_ID ) {
		$post = get_post( $post_ID );

		if ( empty($post->ID) || !in_array( $post->post_type, apply_filters( 'embed_cache_oembed_types', array( 'post', 'page' ) ) ) )
			return;

		// Trigger a caching
		if ( !empty($post->post_content) ) {
			$this->post_ID = $post->ID;
			$this->usecache = false;

			$content = $this->run_shortcode( $post->post_content );
			if ( get_option('embed_autourls') )
				$this->autoembed( $content );

			$this->usecache = true;
		}
	}

	/**
	 * Passes any unlinked URLs that are on their own line to {@link WP_Embed::shortcode()} for potential embedding.
	 *
	 * @uses WP_Embed::autoembed_callback()
	 *
	 * @param string $content The content to be searched.
	 * @return string Potentially modified $content.
	 */
	function autoembed( $content ) {
		return preg_replace_callback( '|^\s*(https?://[^\s"]+)\s*$|im', array(&$this, 'autoembed_callback'), $content );
	}

	/**
	 * Callback function for {@link WP_Embed::autoembed()}.
	 *
	 * @uses WP_Embed::shortcode()
	 *
	 * @param array $match A regex match array.
	 * @return string The embed HTML on success, otherwise the original URL.
	 */
	function autoembed_callback( $match ) {
		$oldval = $this->linkifunknown;
		$this->linkifunknown = false;
		$return = $this->shortcode( array(), $match[1] );
		$this->linkifunknown = $oldval;

		return "\n$return\n";
	}

	/**
	 * Conditionally makes a hyperlink based on an internal class variable.
	 *
	 * @param string $url URL to potentially be linked.
	 * @return string Linked URL or the original URL.
	 */
	function maybe_make_link( $url ) {
		$output = ( $this->linkifunknown ) ? '<a href="' . esc_attr($url) . '">' . esc_html($url) . '</a>' : $url;
		return apply_filters( 'embed_maybe_make_link', $output, $url );
	}
}
$wp_embed = new WP_Embed();

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
 * @since 2.9.0
 *
 * @return array Default embed parameters.
 */
function wp_embed_defaults() {
	if ( !empty($GLOBALS['content_width']) )
		$theme_width = (int) $GLOBALS['content_width'];

	$width = get_option('embed_size_w');

	if ( !$width && !empty($theme_width) )
		$width = $theme_width;

	if ( !$width )
		$width = 500;

	return apply_filters( 'embed_defaults', array(
		'width' => $width,
		'height' => 700,
	) );
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
 * @param string $url The URL that should be embeded.
 * @param array $args Addtional arguments and parameters.
 * @return string The original URL on failure or the embed HTML on success.
 */
function wp_oembed_get( $url, $args = '' ) {
	require_once( 'class-oembed.php' );
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
 * @param boolean $regex Whether the $format parameter is in a regex format or not.
 */
function wp_oembed_add_provider( $format, $provider, $regex = false ) {
	require_once( 'class-oembed.php' );
	$oembed = _wp_oembed_get_object();
	$oembed->providers[$format] = array( $provider, $regex );
}
