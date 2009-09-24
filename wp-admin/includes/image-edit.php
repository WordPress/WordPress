<?php
/**
 * WordPress Image Editor
 *
 * @package WordPress
 * @subpackage Administration
 */

function wp_image_editor($post_id, $msg = false) {
	$nonce = wp_create_nonce("image_editor-$post_id");
	$meta = wp_get_attachment_metadata($post_id);
	$thumb = image_get_intermediate_size($post_id, 'thumbnail');
	$note = '';

	if ( is_array($meta) && isset($meta['width']) )
		$big = max( $meta['width'], $meta['height'] );
	else
		wp_die( __('Image data does not exist. Please re-upload the image.') );

	$sizer = $big > 400 ? 400 / $big : 1;

	$backup_sizes = get_post_meta( $post_id, '_wp_attachment_backup_sizes', true );
	$can_restore = !empty($backup_sizes) && isset($backup_sizes['full-orig']);


	// temp convert backup sizes
	if ( isset($meta['sizes']) && is_array($meta['sizes']) ) {
		$update = false;
		foreach ( $meta['sizes'] as $name => $val ) {
			if ( strpos($name, 'backup-') === 0 ) {
				$m = array();
				preg_match('/backup-([0-9]+)-(.*)/', $name, $m);
				if ( !isset($backup_sizes["{$m[2]}-orig"]) )
					$n = "{$m[2]}-orig";
				else
					$n = "{$m[2]}-{$m[1]}";
				$backup_sizes[$n] = $val;
				unset($meta['sizes'][$name]);
				$update = true;
			}
		}
		if ( $update ) {
			wp_update_attachment_metadata( $post_id, $meta );
			update_post_meta( $post_id, '_wp_attachment_backup_sizes', $backup_sizes);
		}
	}
	// end temp


	if ( $msg ) {
		if ( isset($msg->error) )
			$note = "<div class='error'><p>$msg->error</p></div>";
		elseif ( isset($msg->msg) )
			$note = "<div class='updated'><p>$msg->msg</p></div>";
	}

	?>
	<div class="imgedit-wrap">
	<?php echo $note; ?>
	<table id="imgedit-panel-<?php echo $post_id; ?>"><tbody>
	<tr><td>
	<div class="imgedit-menu">
		<div onclick="imageEdit.crop(<?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-crop disabled" title="<?php echo esc_attr__( 'Crop' ); ?>"></div><?php

	if ( function_exists('imagerotate') ) { ?>

		<div onclick="imageEdit.rotate(90, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-rleft" title="<?php echo esc_attr__( 'Rotate couter-clockwise' ); ?>"></div>
		<div onclick="imageEdit.rotate(-90, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-rright" title="<?php echo esc_attr__( 'Rotate clockwise' ); ?>"></div><?php

	} ?>

		<div onclick="imageEdit.flip(1, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-fliph" title="<?php echo esc_attr__( 'Flip horizontally' ); ?>"></div>
		<div onclick="imageEdit.flip(2, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-flipv" title="<?php echo esc_attr__( 'Flip vertically' ); ?>"></div>

		<div id="image-undo-<?php echo $post_id; ?>" onclick="imageEdit.undo(<?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-undo disabled" title="<?php echo esc_attr__( 'Undo' ); ?>"></div>
		<div id="image-redo-<?php echo $post_id; ?>" onclick="imageEdit.redo(<?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-redo disabled" title="<?php echo esc_attr__( 'Redo' ); ?>"></div>
		<br class="clear" />
	</div>

	<input type="hidden" id="imgedit-sizer-<?php echo $post_id; ?>" value="<?php echo $sizer; ?>" />
	<input type="hidden" id="imgedit-minthumb-<?php echo $post_id; ?>" value="<?php echo ( get_option('thumbnail_size_w') . ':' . get_option('thumbnail_size_h') ); ?>" />
	<input type="hidden" id="imgedit-history-<?php echo $post_id; ?>" value="" />
	<input type="hidden" id="imgedit-undone-<?php echo $post_id; ?>" value="0" />
	<input type="hidden" id="imgedit-selection-<?php echo $post_id; ?>" value="" />
	<input type="hidden" id="imgedit-x-<?php echo $post_id; ?>" value="<?php echo $meta['width']; ?>" />
	<input type="hidden" id="imgedit-y-<?php echo $post_id; ?>" value="<?php echo $meta['height']; ?>" />

	<div id="imgedit-crop-<?php echo $post_id; ?>" class="imgedit-crop-wrap">
	<img id="image-preview-<?php echo $post_id; ?>" onload="imageEdit.imgLoaded('<?php echo $post_id; ?>')" src="<?php echo admin_url('admin-ajax.php'); ?>?action=imgedit-preview&amp;_ajax_nonce=<?php echo $nonce; ?>&amp;postid=<?php echo $post_id; ?>&amp;rand=<?php echo rand(1, 99999); ?>" />
	</div>

	<div class="imgedit-submit">
		<input type="button" onclick="imageEdit.close(<?php echo "$post_id, '$nonce'"; ?>)" class="button" value="<?php echo esc_attr__( 'Cancel' ); ?>" />
		<input type="button" onclick="imageEdit.save(<?php echo "$post_id, '$nonce'"; ?>)" class="button-primary imgedit-submit-btn" value="<?php echo esc_attr__( 'Save' ); ?>" />
	</div>
	</td>

	<td class="imgedit-settings">
	<div class="imgedit-group">
	<div class="imgedit-group-top">
		<a class="imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" href="#"><strong><?php _e('Scale Image'); ?></strong></a>
		<div class="imgedit-help">
		<p><?php _e('You can proportionally scale the original image. For best results the scaling should be done before performing any other operations on it like crop, rotate, etc. Note that if you make the image larger it may become fuzzy.'); ?></p>
		<p><?php printf( __('Original dimensions %s'), $meta['width'] . '&times;' . $meta['height'] ); ?></p>
		<div class="imgedit-submit">
		<span class="nowrap"><input type="text" id="imgedit-scale-width-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleChanged(<?php echo $post_id; ?>, 1)" onblur="imageEdit.scaleChanged(<?php echo $post_id; ?>, 1)" style="width:4em;" value="<?php echo $meta['width']; ?>" />&times;<input type="text" id="imgedit-scale-height-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleChanged(<?php echo $post_id; ?>, 0)" onblur="imageEdit.scaleChanged(<?php echo $post_id; ?>, 0)" style="width:4em;" value="<?php echo $meta['height']; ?>" />
		<span class="imgedit-scale-warn" id="imgedit-scale-warn-<?php echo $post_id; ?>">!</span></span>
		<input type="button" onclick="imageEdit.action(<?php echo "$post_id, '$nonce'"; ?>, 'scale')" class="button-primary" value="<?php echo esc_attr__( 'Scale' ); ?>" />
		</div>
		</div>
	</div>

<?php if ( $can_restore ) { ?>

	<div class="imgedit-group-top">
		<a class="imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" href="#"><strong><?php _e('Restore Original Image'); ?></strong></a>
		<div class="imgedit-help">
		<p><?php _e('Discard any changes and restore the original image. Previously edited copies of the image will not be deleted.'); ?></p>
		<div class="imgedit-submit">
		<input type="button" onclick="imageEdit.action(<?php echo "$post_id, '$nonce'"; ?>, 'restore')" class="button-primary" value="<?php echo esc_attr__( 'Restore image' ); ?>" <?php echo $can_restore; ?> />
		</div>
		</div>
	</div>

<?php } ?>

	</div>

	<div class="imgedit-group">
	<div class="imgedit-group-top">
		<strong><?php _e('Image Crop'); ?></strong>
		<a class="imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" href="#"><?php _e('(help)'); ?></a>
		<div class="imgedit-help">
		<p><?php _e('The image can be cropped by clicking on it and dragging to select the desired part. While dragging the dimensions of the selection are displayed below.'); ?></p>
		<strong><?php _e('Keyboard shortcuts'); ?></strong>
		<ul>
		<li><?php _e('Arrow: move by 10px'); ?></li>
		<li><?php _e('Shift + arrow: move by 1px'); ?></li>
		<li><?php _e('Ctrl + arrow: resize by 10px'); ?></li>
		<li><?php _e('Ctrl + Shift + arrow: resize by 1px'); ?></li>
		<li><?php _e('Shift + drag: lock aspect ratio'); ?></li>
		</ul>

		<p><strong><?php _e('Crop Aspect Ratio'); ?></strong><br />
		<?php _e('You can specify the crop selection aspect ratio then hold down the Shift key while dragging to lock it. The values can be 1:1 (square), 4:3, 16:9, etc. If there is a selection, specifying aspect ratio will set it immediately.'); ?></p>

		<p><strong><?php _e('Crop Selection'); ?></strong><br />
		<?php _e('Once started, the selection can be adjusted by entering new values (in pixels). Note that these values are scaled to approximately match the original image dimensions. The minimum selection size equals the thumbnail size as set in the Media settings.'); ?></p>
		</div>
	</div>

	<p>
		<?php _e('Aspect ratio:'); ?>
		<span  class="nowrap">
		<input type="text" id="imgedit-crop-width-<?php echo $post_id; ?>" onkeyup="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 0, this)" style="width:3em;" />
		:
		<input type="text" id="imgedit-crop-height-<?php echo $post_id; ?>" onkeyup="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 1, this)" style="width:3em;" />
		</span>
	</p>

	<p id="imgedit-crop-sel-<?php echo $post_id; ?>">
		<?php _e('Selection:'); ?>
		<span  class="nowrap">
		<input type="text" id="imgedit-sel-width-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>)" style="width:4em;" />
		:
		<input type="text" id="imgedit-sel-height-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>)" style="width:4em;" />
		</span>
	</p>
	</div>

	<?php if ( $thumb ) {
		$thumb_img = wp_constrain_dimensions( $thumb['width'], $thumb['height'], 160, 120 );
	?>

	<div class="imgedit-group imgedit-applyto">
	<div class="imgedit-group-top">
		<strong><?php _e('Thumbnail Settings'); ?></strong>
		<a class="imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" href="#"><?php _e('(help)'); ?></a>
		<p class="imgedit-help"><?php _e('The thumbnail image can be cropped differently. For example it can be square or contain only a portion of the original image to showcase it better. Here you can select whether to apply changes to all image sizes or make the thumbnail different.'); ?></p>
	</div>

	<p>
		<img src="<?php echo $thumb['url']; ?>" width="<?php echo $thumb_img[0]; ?>" height="<?php echo $thumb_img[1]; ?>" class="imgedit-size-preview" alt="" /><br /><?php _e('Current thumbnail'); ?>
	</p>

	<p id="imgedit-save-target-<?php echo $post_id; ?>">
		<strong><?php _e('Apply changes to:'); ?></strong><br />

		<label class="imgedit-label">
		<input type="radio" name="imgedit-target-<?php echo $post_id; ?>" value="all" checked="checked" />
		<?php _e('All image sizes'); ?></label>

		<label class="imgedit-label">
		<input type="radio" name="imgedit-target-<?php echo $post_id; ?>" value="thumbnail" />
		<?php _e('Thumbnail'); ?></label>

		<label class="imgedit-label">
		<input type="radio" name="imgedit-target-<?php echo $post_id; ?>" value="nothumb" />
		<?php _e('All sizes except thumbnail'); ?></label>
	</p>
	</div>

	<?php } ?>

	</td></tr>
	</tbody></table>
	<div class="imgedit-wait" id="imgedit-wait-<?php echo $post_id; ?>"></div>
	<script type="text/javascript">imageEdit.init(<?php echo $post_id; ?>);</script>
	<div class="hidden" id="imgedit-leaving-<?php echo $post_id; ?>"><?php _e("There are unsaved changes that will be lost.  'OK' to continue, 'Cancel' to return to the Image Editor."); ?></div>
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
		$image = apply_filters('load_image_to_edit', $image, $post->ID);
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

function wp_restore_image($post_id) {
	$meta = wp_get_attachment_metadata($post_id);
	$backup_sizes = get_post_meta( $post_id, '_wp_attachment_backup_sizes', true );
	$restored = false;
	$msg = '';

	if ( !is_array($backup_sizes) ) {
		$msg->error = __('Cannot load image metadata.');
		return $msg;
	}

	$parts = pathinfo($meta['file']);
	$suffix = time() . rand(100, 999);
	$default_sizes = apply_filters( 'intermediate_image_sizes', array('large', 'medium', 'thumbnail') );
	$default_sizes[] = 'full';

	foreach ( $default_sizes as $default_size ) {
		if ( isset($backup_sizes["$default_size-orig"]) ) {
			$data = $backup_sizes["$default_size-orig"];
			if ( 'full' == $default_size ) {
				$backup_sizes["full-$suffix"] = array('width' => $meta['width'], 'height' => $meta['height'], 'file' => $parts['basename']);

				$meta['file'] = path_join($parts['dirname'], $data['file']);
				$meta['width'] = $data['width'];
				$meta['height'] = $data['height'];

				list ( $uwidth, $uheight ) = wp_shrink_dimensions($meta['width'], $meta['height']);
				$meta['hwstring_small'] = "height='$uheight' width='$uwidth'";
				$restored = update_attached_file($post_id, $meta['file']);
			} else {
				if ( isset($meta['sizes'][$default_size]) )
					$backup_sizes["$default_size-{$suffix}"] = $meta['sizes'][$default_size];

				$meta['sizes'][$default_size] = $data;
			}
		}
	}

	if ( !wp_update_attachment_metadata($post_id, $meta) || !update_post_meta( $post_id, '_wp_attachment_backup_sizes', $backup_sizes) ) {
		$msg->error = __('Cannot save image metadata.');
		return $msg;
	}

	if ( !$restored )
		$msg->error = __('Image metadata is inconsistent.');
	else
		$msg->msg = __('Image restored successfully.');

	return $msg;
}

function wp_save_image($post_id) {
	$return = '';
	$success = $delete = $scaled = $nocrop = false;
	$post = get_post($post_id);
	@ini_set('memory_limit', '256M');
	$img = load_image_to_edit($post);

	if ( !is_resource($img) ) {
		$return->error = esc_js( __('Unable to create new image.') );
		return $return;
	}

	$fwidth = !empty($_REQUEST['fwidth']) ? intval($_REQUEST['fwidth']) : 0;
	$fheight = !empty($_REQUEST['fheight']) ? intval($_REQUEST['fheight']) : 0;
	$target = !empty($_REQUEST['target']) ? preg_replace('/[^a-z0-9_-]+/i', '', $_REQUEST['target']) : '';
	$scale = !empty($_REQUEST['do']) && 'scale' == $_REQUEST['do'];

	if ( $scale && $fwidth > 0 && $fheight > 0 ) {
		$sX = imagesx($img);
		$sY = imagesy($img);

		// check if it has roughly the same w / h ratio
		if ( round($sX / $sY, 2) == round($fwidth / $fheight, 2) ) {
			// scale the full size image
			$dst = wp_imagecreatetruecolor($fwidth, $fheight);
			if ( imagecopyresampled( $dst, $img, 0, 0, 0, 0, $fwidth, $fheight, $sX, $sY ) ) {
				imagedestroy($img);
				$img = $dst;
				$scaled = true;
			}
		}

		if ( !$scaled ) {
			$return->error = esc_js( __('Error while saving the scaled image. Please reload the page and try again.') );
			return $return;
		}
	} elseif ( !empty($_REQUEST['history']) ) {
		$changes = json_decode( stripslashes($_REQUEST['history']) );
		if ( $changes )
			$img = image_edit_apply_changes($img, $changes);
	} else {
		$return->error = esc_js( __('Nothing to save, the image has not changed.') );
		return $return;
	}

	$meta = wp_get_attachment_metadata($post_id);
	$backup_sizes = get_post_meta( $post->ID, '_wp_attachment_backup_sizes', true );

	if ( !is_array($meta) ) {
		$return->error = esc_js( __('Image data does not exist. Please re-upload the image.') );
		return $return;
	}

	if ( !is_array($backup_sizes) )
		$backup_sizes = array();

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
	if ( !wp_save_image_file($new_path, $img, $post->post_mime_type, $post_id) ) {
		$return->error = esc_js( __('Unable to save the image.') );
		return $return;
	}

	if ( 'nothumb' == $target || 'all' == $target || $scaled ) {
		$tag = !isset($backup_sizes['full-orig']) ? 'full-orig' : "full-$suffix";
		$backup_sizes[$tag] = array('width' => $meta['width'], 'height' => $meta['height'], 'file' => $path_parts['basename']);

		$success = update_attached_file($post_id, $new_path);
		$meta['file'] = get_attached_file($post_id, true); // get the path unfiltered
		$meta['width'] = imagesx($img);
		$meta['height'] = imagesy($img);

		list ( $uwidth, $uheight ) = wp_shrink_dimensions($meta['width'], $meta['height']);
		$meta['hwstring_small'] = "height='$uheight' width='$uwidth'";

		if ( $success && ('nothumb' == $target || 'all' == $target) ) {
			$sizes = apply_filters( 'intermediate_image_sizes', array('large', 'medium', 'thumbnail') );
			if ( 'nothumb' == $target )
				$sizes = array_diff( $sizes, array('thumbnail') );
		}

		$return->fw = $meta['width'];
		$return->fh = $meta['height'];
	} elseif ( 'thumbnail' == $target ) {
		$sizes = array( 'thumbnail' );
		$success = $delete = $nocrop = true;
	}

	if ( isset($sizes) ) {
		foreach ( $sizes as $size ) {
			if ( isset($meta['sizes'][$size]) ) {
				$tag = !isset($backup_sizes["$size-orig"]) ? "$size-orig" : "$size-$suffix";
				$backup_sizes[$tag] = $meta['sizes'][$size];
			}

			$crop = $nocrop ? false : get_option("{$size}_crop");
			$resized = image_make_intermediate_size($new_path, get_option("{$size}_size_w"), get_option("{$size}_size_h"), $crop );

			if ( $resized )
				$meta['sizes'][$size] = $resized;
			else
				unset($meta['sizes'][$size]);
		}
	}

	if ( $success ) {
		wp_update_attachment_metadata($post_id, $meta);
		update_post_meta( $post_id, '_wp_attachment_backup_sizes', $backup_sizes);

		if ( $target == 'thumbnail' || $target == 'all' ) {
			if ( $thumb = $meta['sizes']['thumbnail'] ) {
				$file_url = wp_get_attachment_url($post_id);
				$return->thumbnail = path_join( dirname($file_url), $thumb['file'] );
			}
		}
	} else {
		$delete = true;
	}

	if ( $delete ) {
		$delpath = apply_filters('wp_delete_file', $new_path);
		@unlink($delpath);
	}

	imagedestroy($img);

	$return->msg = esc_js( __('Image saved') );
	return $return;
}

