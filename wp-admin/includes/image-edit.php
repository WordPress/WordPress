<?php
/**
 * WordPress Image Editor
 *
 * @package WordPress
 * @subpackage Administration
 */

function wp_image_editor($post_id) {
	$nonce = wp_create_nonce("image_editor-$post_id");
	$image_size_opt = "<option value='all'>" . __('all image sizes') . "</option>\n";
	$image_size_opt .= "<option value='full'>" . __('original image') . "</option>\n";

	$meta = wp_get_attachment_metadata($post_id);
	if ( is_array($meta) && is_array($meta['sizes']) ) {
		$sizes = apply_filters('intermediate_image_sizes', array('thumbnail', 'medium', 'large'));
		$size_names = array(
			'thumbnail' => __('thumbnail'),
			'medium' => __('medium'),
			'large' => __('large')
		);

		foreach ( $sizes as $size ) {
			if ( array_key_exists($size, $meta['sizes']) ) {
				$size_name = isset($size_names[$size]) ? $size_names[$size] : $size;
				$image_size_opt .= "<option value='$size'>$size_name</option>\n";
			}
		}
	} ?>

	<div class="imgedit-wrap">
	<div id="imgedit-panel-<?php echo $post_id; ?>">
		<div class="imgedit-menu">
		<div onclick="imageEdit.crop(<?php echo "$post_id, '$nonce'"; ?>)" class="imgedit-crop" title="<?php echo esc_attr__( 'Crop' ); ?>"></div><?php

	if ( function_exists('imagerotate') ) { ?>

		<div onclick="imageEdit.rotate(90, <?php echo "$post_id, '$nonce'"; ?>)" class="imgedit-rleft" title="<?php echo esc_attr__( 'Rotate couter-clockwise' ); ?>"></div>
		<div onclick="imageEdit.rotate(-90, <?php echo "$post_id, '$nonce'"; ?>)" class="imgedit-rright" title="<?php echo esc_attr__( 'Rotate clockwise' ); ?>"></div><?php

	} ?>

		<div onclick="imageEdit.flip(1, <?php echo "$post_id, '$nonce'"; ?>)" class="imgedit-fliph" title="<?php echo esc_attr__( 'Flip horizontally' ); ?>"></div>
		<div onclick="imageEdit.flip(2, <?php echo "$post_id, '$nonce'"; ?>)" class="imgedit-flipv" title="<?php echo esc_attr__( 'Flip vertically' ); ?>"></div>

		<div id="image-undo-<?php echo $post_id; ?>" onclick="imageEdit.undo(<?php echo "$post_id, '$nonce'"; ?>)" class="imgedit-undo disabled" title="<?php echo esc_attr__( 'Undo' ); ?>"></div>
		<div id="image-redo-<?php echo $post_id; ?>" onclick="imageEdit.redo(<?php echo "$post_id, '$nonce'"; ?>)" class="imgedit-redo disabled" title="<?php echo esc_attr__( 'Redo' ); ?>"></div>
		<br class="clear" />
		</div>

		<p>
		<span id="imgedit-scale-<?php echo $post_id; ?>">
			<input type="checkbox" onchange="imageEdit.scaleSwitched(<?php echo $post_id; ?>)" id="imgedit-scale-switch-<?php echo $post_id; ?>" /><label for="imgedit-scale-switch-<?php echo $post_id; ?>">Scale full size image:</label>
			<span id="imgedit-scale-values-<?php echo $post_id; ?>">
				<input type="text" id="imgedit-scale-width-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleWidthChanged(<?php echo $post_id; ?>)" style="width:4em;" />
				&times;
				<input type="text" id="imgedit-scale-height-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleHeightChanged(<?php echo $post_id; ?>)" style="width:4em;" />
			</span>
		</span>
		</p>

		<input type="hidden" id="imgedit-history-<?php echo $post_id; ?>" value="" />
		<input type="hidden" id="imgedit-undone-<?php echo $post_id; ?>" value="0" />
		<input type="hidden" id="imgedit-selection-<?php echo $post_id; ?>" value="" />
		<input type="hidden" id="imgedit-aspect-x-<?php echo $post_id; ?>" value="" />
		<input type="hidden" id="imgedit-aspect-y-<?php echo $post_id; ?>" value="" />

		<h4><?php _e('Preview Image:'); ?></h4>
		<div id="imgedit-crop-<?php echo $post_id; ?>" style="position:relative;">
		<img src="<?php echo admin_url('admin-ajax.php') . "?action=load-preview-image&amp;_ajax_nonce={$nonce}&amp;postid={$post_id}&amp;ver=" . rand(1, 99999); ?>" id="image-preview-<?php echo $post_id; ?>" />
		</div>

		<p>
		<?php _e('Apply to:'); ?>
		<select id="imgedit-save-target-<?php echo $post_id; ?>" onchange="imageEdit.targetChanged(<?php echo $post_id; ?>)">
		<?php echo $image_size_opt; ?>
		</select>
		</p>

		<p>
		<input type="button" onclick="imageEdit.close(<?php echo "$post_id, '$nonce'"; ?>)" class="button" value="<?php echo esc_attr__( 'Close' ); ?>" />
		<input type="button" onclick="imageEdit.save(<?php echo "$post_id, '$nonce'"; ?>)" class="button-primary" value="<?php echo esc_attr__( 'Save' ); ?>" />
		</p>
		<script type="text/javascript">imageEdit.targetChanged(<?php echo $post_id; ?>);</script>
	</div>
	<div class="imgedit-wait" id="imgedit-wait-<?php echo $post_id; ?>"></div>
	</div>
<?php
}

function load_image_to_edit($post, $size = 'full') {
	$filename = get_attached_file($post->ID);

	if ( 'full' != $size && ( $data = image_get_intermediate_size($post->ID, $size) ) )
		$filename = path_join( dirname($filename), $data['file'] );

	switch ( $post->post_mime_type ) {
		case 'image/jpeg':
			$image = imagecreatefromjpeg($filename);
			break;
		case 'image/png':
			$image = imagecreatefrompng($filename);
			break;
		case 'image/gif':
			$image = imagecreatefromgif($filename);
			break;
		default:
			$image = false;
			break;
	}
	if ( is_resource($image) ) {
		$image = apply_filters('load_image_to_edit', $image, $post->ID); // allows plugins to remove a watermark
		if ( function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}
	}
	return $image;
}

function wp_stream_image($image, $mime_type, $post_id = 0, $intermediate_size = '') {
	$image = apply_filters('image_save_pre', $image, $post->ID, $intermediate_size);

	switch ( $mime_type ) {
		case 'image/jpeg':
			header('Content-Type: image/jpeg');
			return imagejpeg($image, null, 90);
		case 'image/png':
			header('Content-Type: image/png');
			return imagepng($image);
		case 'image/gif':
			header('Content-Type: image/gif');
			return imagegif($image);
		default:
			return false;
	}
}

function wp_save_image_file($filename, $image, $mime_type, $post_id = 0, $intermediate_size = '') {
	$image = apply_filters('image_save_pre', $image, $post->ID, $intermediate_size);

	switch ( $mime_type ) {
		case 'image/jpeg':
			return imagejpeg( $image, $filename, apply_filters( 'jpeg_quality', 90, 'edit_image' ) );
		case 'image/png':
			return imagepng($image, $filename);
		case 'image/gif':
			return imagegif($image, $filename);
		default:
			return false;
	}
}

function _image_get_preview_ratio($w, $h) {
	$max = max($w, $h);
		return $max > 400 ? (400 / $max) : 1;
}

function _rotate_image_resource($img, $angle) {
	if ( function_exists('imagerotate') ) {
		$rotated = imagerotate($img, $angle, 0);
		if ( is_resource($rotated) ) {
			imagedestroy($img);
			$img = $rotated;
		}
	}
	return $img;
}


function _flip_image_resource($img, $horz, $vert) {
	$w = imagesx($img);
	$h = imagesy($img);
	$dst = wp_imagecreatetruecolor($w, $h);
	if ( is_resource($dst) ) {
		$sx = $vert ? ($w - 1) : 0;
		$sy = $horz ? ($h - 1) : 0;
		$sw = $vert ? -$w : $w;
		$sh = $horz ? -$h : $h;

		if ( imagecopyresampled($dst, $img, 0, 0, $sx, $sy, $w, $h, $sw, $sh) ) {
			imagedestroy($img);
			$img = $dst;
		}
	}
	return $img;
}

function _crop_image_resource($img, $x, $y, $w, $h) {
	$dst = wp_imagecreatetruecolor($w, $h);
	if ( is_resource($dst) ) {
		if ( imagecopy($dst, $img, 0, 0, $x, $y, $w, $h) ) {
			imagedestroy($img);
			$img = $dst;
		}
	}
	return $img;
}

function image_edit_apply_changes($img, $changes) {

	if ( !is_array($changes) )
		return $img;

	// expand change operations
	foreach ( $changes as $key => $obj ) {
		if ( isset($obj->r) ) {
			$obj->type = 'rotate';
			$obj->angle = $obj->r;
			unset($obj->r);
		} elseif ( isset($obj->f) ) {
			$obj->type = 'flip';
			$obj->axis = $obj->f;
			unset($obj->f);
		} elseif ( isset($obj->c) ) {
			$obj->type = 'crop';
			$obj->sel = $obj->c;
			unset($obj->c);
		}
		$changes[$key] = $obj;
	}

	// combine operations
	if ( count($changes) > 1 ) {
		$filtered = array($changes[0]);
		for ( $i = 0, $j = 1; $j < count($changes); $j++ ) {
			$combined = false;
			if ( $filtered[$i]->type == $changes[$j]->type ) {
				switch ( $filtered[$i]->type ) {
					case 'rotate':
						$filtered[$i]->angle += $changes[$j]->angle;
						$combined = true;
						break;
					case 'flip':
						$filtered[$i]->axis ^= $changes[$j]->axis;
						$combined = true;
						break;
				}
			}
			if ( !$combined )
				$filtered[++$i] = $changes[$j];
		}
		$changes = $filtered;
		unset($filtered);
	}

	// image resource before applying the changes
	$img = apply_filters('image_edit_before_change', $img, $changes);

	foreach ( $changes as $operation ) {
		switch ( $operation->type ) {
			case 'rotate':
				if ( $operation->angle != 0 )
					$img = _rotate_image_resource($img, $operation->angle);
				break;
			case 'flip':
				if ( $operation->axis != 0 )
					$img = _flip_image_resource($img, ($operation->axis & 1) != 0, ($operation->axis & 2) != 0);
				break;
			case 'crop':
				$sel = $operation->sel;
				$scale = 1 / _image_get_preview_ratio( imagesx($img), imagesy($img) ); // discard preview scaling
				$img = _crop_image_resource($img, $sel->x * $scale, $sel->y * $scale, $sel->w * $scale, $sel->h * $scale);
				break;
		}
	}

	return $img;
}


function stream_preview_image($post_id) {
	$post = get_post($post_id);
	@ini_set('memory_limit', '256M');
	$img = load_image_to_edit( $post, array(400, 400) );

	if ( !is_resource($img) )
		return false;

	$changes = !empty($_REQUEST['history']) ? json_decode( stripslashes($_REQUEST['history']) ) : null;
	if ( $changes )
		$img = image_edit_apply_changes($img, $changes);

	// scale the image
	$w = imagesx($img);
	$h = imagesy($img);
	$ratio = _image_get_preview_ratio($w, $h);
	$w2 = $w * $ratio;
	$h2 = $h * $ratio;

	$preview = wp_imagecreatetruecolor($w2, $h2);
	imagecopyresampled( $preview, $img, 0, 0, 0, 0, $w2, $h2, $w, $h );
	wp_stream_image($preview, $post->post_mime_type, $post_id);

	imagedestroy($preview);
	imagedestroy($img);
	return true;
}


function wp_save_image($post_id) {
	$msg = '';
	$success = $delete = $full_resized = false;
	$post = get_post($post_id);
	@ini_set('memory_limit', '256M');
	$img = load_image_to_edit($post);

	if ( !is_resource($img) )
		return 'error=' . __('Unable to create new image.');

	$fwidth = !empty($_REQUEST['fwidth']) ? intval($_REQUEST['fwidth']) : 0;
	$fheight = !empty($_REQUEST['fheight']) ? intval($_REQUEST['fheight']) : 0;
	$target = !empty($_REQUEST['target']) ? preg_replace('/[^a-z0-9_-]+/i', '', $_REQUEST['target']) : '';

	if ( !empty($_REQUEST['history']) ) {
		$changes = json_decode( stripslashes($_REQUEST['history']) );
		if ( $changes )
			$img = image_edit_apply_changes($img, $changes);
	}

	if ( $fwidth > 0 && $fheight > 0 ) {
		// scale the full size image
		$dst = wp_imagecreatetruecolor($fwidth, $fheight);
		if ( imagecopyresampled( $dst, $img, 0, 0, 0, 0, $fwidth, $fheight, imagesx($img), imagesy($img) ) ) {
			imagedestroy($img);
			$img = $dst;
			$full_resized = true;
		}
	}

	if ( !$changes && !$full_resized )
		return 'error=' . __('Nothing to save, the image is not changed.');

	$meta = wp_get_attachment_metadata($post_id, false, false);
	if ( !is_array($meta) )
		$meta = array();

	if ( !isset($meta['sizes']) || !is_array($meta['sizes']) )
		$meta['sizes'] = array();

	// generate new filename
	$path = get_attached_file($post_id);
	$path_parts = pathinfo52( $path );
	$filename = $path_parts['filename'];
	$suffix = time() . rand(100, 999);

	while( true ) {
		$filename = preg_replace( '/-e([0-9]+)$/', '', $filename );
		$filename .= "-e{$suffix}";
		$new_filename = "{$filename}.{$path_parts['extension']}";
		$new_path = "{$path_parts['dirname']}/$new_filename";
		if ( file_exists($new_path) )
			$suffix++;
		else
			break;
	}

	// save the full-size file, also needed to create sub-sizes
	if ( !wp_save_image_file($new_path, $img, $post->post_mime_type, $post_id) )
		return 'error=' . __('Unable to save the image.');

	if ( 'full' == $target || 'all' == $target || $full_resized ) {
		$meta['sizes']["backup-{$suffix}-full"] = array('width' => $meta['width'], 'height' => $meta['height'], 'file' => $path_parts['basename']);

		$success = update_attached_file($post_id, $new_path);
		$meta['file'] = get_attached_file($post_id, true); // get the path unfiltered
		$meta['width'] = imagesx($img);
		$meta['height'] = imagesy($img);

		list ( $uwidth, $uheight ) = wp_shrink_dimensions($meta['width'], $meta['height']);
		$meta['hwstring_small'] = "height='$uheight' width='$uwidth'";

		if ( $success && $target == 'all' )
			$sizes = apply_filters( 'intermediate_image_sizes', array('large', 'medium', 'thumbnail') );

		$msg .= "full={$meta['width']}x{$meta['height']}!";
	} elseif ( array_key_exists($target, $meta['sizes']) ) {
		$sizes = array( $target );
		$success = $delete = true;
	}

	if ( isset($sizes) ) {
		foreach ( $sizes as $size ) {
			if ( isset($meta['sizes'][$size]) )
				$meta['sizes']["backup-{$suffix}-$size"] = $meta['sizes'][$size];

			$resized = image_make_intermediate_size($new_path, get_option("{$size}_size_w"), get_option("{$size}_size_h"), get_option("{$size}_crop") );

			if ( $resized )
				$meta['sizes'][$size] = $resized;
			else
				unset($meta['sizes'][$size]);
		}
	}

	if ( $success ) {
		wp_update_attachment_metadata($post_id, $meta);

		if ( $target == 'thumbnail' || $target == 'all' || ( $target == 'full' && !array_key_exists('thumbnail', $meta['sizes']) ) ) {
			if ( $thumb_url = get_attachment_icon_src($post_id) )
				$msg .= "thumbnail={$thumb_url[0]}";
		}
	} else {
		$delete = true;
	}

	if ( $delete ) {
		$delpath = apply_filters('wp_delete_file', $new_path);
		@unlink($delpath);
	}

	imagedestroy($img);
	return $msg;
}

