<?php

// functions for media display

// scale down the default size of an image so it's a better fit for the editor and theme
function image_constrain_size_for_editor($width, $height, $size = 'medium') {

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
	else { // $size == 'full'
		// we're inserting a full size image into the editor.  if it's a really big image we'll scale it down to fit reasonably
		// within the editor itself, and within the theme's content width if it's known.  the user can resize it in the editor
		// if they wish.
		if ( !empty($GLOBALS['content_width']) ) {
			$max_width = $GLOBALS['content_width'];
		}
		else
			$max_width = 500;
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

	if ( !wp_attachment_is_image($id) )
		return false;

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
	}
	if ( !$width && !$height && isset($meta['width'], $meta['height']) ) {
		// any other type: use the real image and constrain it
		list( $width, $height ) = image_constrain_size_for_editor( $meta['width'], $meta['height'], $size );
	}

	if ( $img_url)
		return array( $img_url, $width, $height );
	return false;

}

// return an <img src /> tag for the given image attachment, scaling it down if requested
function get_image_tag($id, $alt, $title, $align, $size='medium') {

	list( $img_src, $width, $height ) = image_downsize($id, $size);
	$hwstring = image_hwstring($width, $height);

	$html = '<img src="'.attribute_escape($img_src).'" alt="'.attribute_escape($alt).'" title="'.attribute_escape($title).'" '.$hwstring.'class="align'.attribute_escape($align).' size-'.attribute_escape($size).' wp-image-'.$id.'" />';

	$url = '';
	$html = apply_filters( 'image_send_to_editor', $html, $id, $alt, $title, $align, $url, $size );

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
function image_resize( $file, $max_w, $max_h, $crop=false, $suffix=null, $dest_path=null, $jpeg_quality=90) {

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
		if (!imagejpeg( $newimage, $destfilename, apply_filters( 'jpeg_quality', $jpeg_quality ) ) )
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

function image_get_intermediate_size($post_id, $size='thumbnail') {
	if ( !$imagedata = wp_get_attachment_metadata( $post_id ) )
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

// get an image to represent an attachment - a mime icon for files, thumbnail or intermediate size for images
// returns an array (url, width, height), or false if no image is available
function wp_get_attachment_image_src($attachment_id, $size='thumbnail', $icon = false) {
	
	// get a thumbnail or intermediate image if there is one
	if ( $image = image_downsize($attachment_id, $size) )
		return $image;

	if ( $icon && $src = wp_mime_type_icon($attachment_id) ) {
		$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/crystal' );
		$src_file = $icon_dir . '/' . basename($src);
		@list($width, $height) = getimagesize($src_file);
	}
	if ( $src && $width && $height )
		return array( $src, $width, $height );
	return false;
}

// as per wp_get_attachment_image_src, but returns an <img> tag
function wp_get_attachment_image($attachment_id, $size='thumbnail', $icon = false) {

	$html = '';
	$image = wp_get_attachment_image_src($attachment_id, $size, $icon);
	if ( $image ) {
		list($src, $width, $height) = $image;
		$hwstring = image_hwstring($width, $height);
		if ( is_array($size) )
			$size = join('x', $size);
		$html = '<img src="'.attribute_escape($src).'" '.$hwstring.'class="attachment-'.attribute_escape($size).'" alt="" />';
	}
	
	return $html;
}

add_shortcode('gallery', 'gallery_shortcode');

function gallery_shortcode($attr) {
	global $post;

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
		'orderby'    => 'menu_order ASC, ID ASC',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
	), $attr));

	$id = intval($id);
	$attachments = get_children("post_parent=$id&post_type=attachment&post_mime_type=image&orderby={$orderby}");

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link($id, $size, true) . "\n";
		return $output;
	}

	$listtag = tag_escape($listtag);
	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	
	$output = apply_filters('gallery_style', "
		<style type='text/css'>
			.gallery {
				margin: auto;
			}
			.gallery-item {
				float: left;
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;			}
			.gallery img {
				border: 2px solid #cfcfcf;
			}
			.gallery-caption {
				margin-left: 0;
			}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->
		<div class='gallery'>");

	foreach ( $attachments as $id => $attachment ) {
		$link = wp_get_attachment_link($id, $size, true);
		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon'>
				$link
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='gallery-caption'>
				{$attachment->post_excerpt}
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

function previous_image_link() {
	adjacent_image_link(true);
}

function next_image_link() {
	adjacent_image_link(false);
}

function adjacent_image_link($prev = true) {
	global $post;
	$post = get_post($post);
	$attachments = array_values(get_children("post_parent=$post->post_parent&post_type=attachment&post_mime_type=image&orderby=menu_order ASC, ID ASC"));

	foreach ( $attachments as $k => $attachment )
		if ( $attachment->ID == $post->ID )
			break;

	$k = $prev ? $k - 1 : $k + 1;

	if ( isset($attachments[$k]) )
		echo wp_get_attachment_link($attachments[$k]->ID, 'thumbnail', true);
}

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

?>
