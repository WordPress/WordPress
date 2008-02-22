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
// The URL might be the original image, or it might be a resized version.
// returns an array($url, $width, $height)
function image_downsize($id, $size = 'medium') {
	
	$img_url = wp_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);
	$width = $height = 0;
	
	// plugins can use this to provide resize services
	if ( $out = apply_filters('image_downsize', false, $id, $size) )
		return $out;
	
	if ( $size == 'thumb' ) {
		// thumbnail: use the thumb as the displayed image, and constrain based on its dimensions
		$thumb_path = wp_get_attachment_thumb_file($id);
		// the actual thumbnail size isn't stored so we'll have to calculate it
		if ( $thumb_path && ($info = getimagesize($thumb_path)) ) {
			list( $width, $height ) = image_constrain_size_for_editor( $info[0], $info[1], $size );
			$img_url = wp_get_attachment_thumb_url($id);
		}
		// this could be improved to provide a default thumbnail if one doesn't exist
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

	$html = '<img src="'.attribute_escape($img_src).'" alt="'.attribute_escape($alt).'" title="'.attribute_escape($title).'" '.$hwstring.'class="align-'.attribute_escape($align).' size-'.attribute_escape($size).' attachment wp-att-'.attribute_escape($id).'" />';

	$html = apply_filters( 'image_send_to_editor', $html, $id, $alt, $title, $align, $url );

	return $html;
}

?>