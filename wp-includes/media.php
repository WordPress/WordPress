<?php

// functions for media display

// scale down the default size of an image so it's a better fit for the editor and theme
function image_constrain_size_for_editor($width, $height, $size = 'medium') {

	if ( $size == 'thumb' ) {
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
		if ( !$max_width ) {
			// $content_width might be set in the current theme's functions.php
			if ( !empty($GLOBALS['content_width']) ) {
				$max_width = $GLOBALS['content_width'];
			}
			else
				$max_width = 500;
		}
	}
	else { // $size == 'full'
		$max_width = 0;
		$max_height = 0;
	}

	list( $max_width, $max_height ) = apply_filters( 'editor_max_image_size', array( $max_width, $max_height ), $size );

	return wp_constrain_dimensions( $width, $height, $max_width, $max_height );
}

// return a width/height string for use in an <img /> tag.  Empty values will be omitted.
function image_hwstring($width, $height) {
	$out = '';
	if ($width)
		$out .= 'width="'.intval($width).'" ';
	if ($height)
		$out .= 'height="'.intval($height).'" ';
	return $out;
}

// Scale an image to fit a particular size (such as 'thumb' or 'medium'), and return an image URL, height and width.
// The URL might be the original image, or it might be a resized version.  This function won't create a new resized copy, it will just return an already resized one if it exists.
// returns an array($url, $width, $height)
function image_downsize($id, $size = 'medium') {

	$img_url = wp_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);
	$width = $height = 0;

	// plugins can use this to provide resize services
	if ( $out = apply_filters('image_downsize', false, $id, $size) )
		return $out;

	// try for a new style intermediate size
	if ( $intermediate = image_get_intermediate_size($id, $size) ) {
		$img_url = str_replace(basename($img_url), $intermediate['file'], $img_url);
		$width = $intermediate['width'];
		$height = $intermediate['height'];
	}
	elseif ( $size == 'thumbnail' ) {
		// fall back to the old thumbnail
		if ( $thumb_file = wp_get_attachment_thumb_file() && $info = getimagesize($thumb_file) ) {
			$img_url = str_replace(basename($img_url), basename($thumb_file), $img_url);
			$width = $info[0];
			$height = $info[1];
		}
		else
			return false;
	}
	elseif ( isset($meta['width'], $meta['height']) ) {
		// any other type: use the real image and constrain it
		list( $width, $height ) = image_constrain_size_for_editor( $meta['width'], $meta['height'], $size );
	}

	return array( $img_url, $width, $height );

}

// return an <img src /> tag for the given image attachment, scaling it down if requested
function get_image_tag($id, $alt, $title, $align, $rel = false, $size='medium') {

	list( $img_src, $width, $height ) = image_downsize($id, $size);
	$hwstring = image_hwstring($width, $height);

	$html = '<img src="'.attribute_escape($img_src).'" alt="'.attribute_escape($alt).'" title="'.attribute_escape($title).'" '.$hwstring.'class="align'.attribute_escape($align).' size-'.attribute_escape($size).' attachment wp-att-'.attribute_escape($id).'" />';

	$html = apply_filters( 'image_send_to_editor', $html, $id, $alt, $title, $align, $url );

	return $html;
}

// same as wp_shrink_dimensions, except the max parameters are optional.
// if either width or height are empty, no constraint is applied on that dimension.
function wp_constrain_dimensions( $current_width, $current_height, $max_width=0, $max_height=0 ) {
	if ( !$max_width and !$max_height )
		return array( $current_width, $current_height );

	$width_ratio = $height_ratio = 1.0;

	if ( $max_width > 0 && $current_width > $max_width )
		$width_ratio = $max_width / $current_width;

	if ( $max_height > 0 && $current_height > $max_height )
		$height_ratio = $max_height / $current_height;

	// the smaller ratio is the one we need to fit it to the constraining box
	$ratio = min( $width_ratio, $height_ratio );

	return array( intval($current_width * $ratio), intval($current_height * $ratio) );
}

// calculate dimensions and coordinates for a resized image that fits within a specified width and height
// if $crop is true, the largest matching central portion of the image will be cropped out and resized to the required size
function image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop=false) {

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
		if (!$new_w) {
			$new_w = intval($new_h * $aspect_ratio);
		}
		if (!$new_h) {
			$new_h = intval($new_w / $aspect_ratio);
		}

		$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

		$crop_w = ceil($new_w / $size_ratio);
		$crop_h = ceil($new_h / $size_ratio);

		$s_x = floor(($orig_w - $crop_w)/2);
		$s_y = floor(($orig_h - $crop_h)/2);
	}
	else {
		// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
		$crop_w = $orig_w;
		$crop_h = $orig_h;

		$s_x = 0;
		$s_y = 0;

		list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
	}

	// if the resulting image would be the same size or larger we don't want to resize it
	if ($new_w >= $orig_w && $new_h >= $orig_h)
		return false;

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array(0, 0, $s_x, $s_y, $new_w, $new_h, $crop_w, $crop_h);

}

// Scale down an image to fit a particular size and save a new copy of the image
function image_resize( $file, $max_w, $max_h, $crop=false, $suffix=null, $dest_path=null, $jpeg_quality=75) {

	$image = wp_load_image( $file );
	if ( !is_resource( $image ) )
		return new WP_Error('error_loading_image', $image);

	list($orig_w, $orig_h, $orig_type) = getimagesize( $file );
	$dims = image_resize_dimensions($orig_w, $orig_h, $max_w, $max_h, $crop);
	if (!$dims)
		return $dims;
	list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

	$newimage = imagecreatetruecolor( $dst_w, $dst_h);

	// preserve PNG transparency
	if ( IMAGETYPE_PNG == $orig_type && function_exists( 'imagealphablending' ) && function_exists( 'imagesavealpha' ) ) {
		imagealphablending( $newimage, false);
		imagesavealpha( $newimage, true);
	}

	imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

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

	if ( $orig_type == IMAGETYPE_GIF ) {
		if (!imagegif( $newimage, $destfilename ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	}
	elseif ( $orig_type == IMAGETYPE_PNG ) {
		if (!imagepng( $newimage, $destfilename ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	}
	else {
		// all other formats are converted to jpg
		$destfilename = "{$dir}/{$name}-{$suffix}.jpg";
		if (!imagejpeg( $newimage, $destfilename, $jpeg_quality ) )
			return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
	}

	imagedestroy( $newimage );

	// Set correct file permissions
	$stat = stat( dirname( $destfilename ));
	$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
	@ chmod( $destfilename, $perms );

	return $destfilename;
}

// resize an image to make a thumbnail or intermediate size, and return metadata describing the new copy
// returns false if no image was created
function image_make_intermediate_size($file, $width, $height, $crop=false) {
	if ( $width || $height ) {
		$resized_file = image_resize($file, $width, $height, $crop);
		if ( $resized_file && $info = getimagesize($resized_file) ) {
			return array(
				'file' => basename( $resized_file ),
				'width' => $info[0],
				'height' => $info[1],
			);
		}
	}
	return false;
}

function image_get_intermediate_size($post_id, $size='thumbnail') {
	if ( !$imagedata = wp_get_attachment_metadata( $post_id ) )
		return false;
		
	if ( empty($imagedata['sizes'][$size]) )
		return false;
		
	return $imagedata['sizes'][$size];
}


?>
