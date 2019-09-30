<?php
/**
 * WordPress Image Editor
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Loads the WP image-editing interface.
 *
 * @since 2.9.0
 *
 * @param int         $post_id Post ID.
 * @param bool|object $msg     Optional. Message to display for image editor updates or errors.
 *                             Default false.
 */
function wp_image_editor( $post_id, $msg = false ) {
	$nonce     = wp_create_nonce( "image_editor-$post_id" );
	$meta      = wp_get_attachment_metadata( $post_id );
	$thumb     = image_get_intermediate_size( $post_id, 'thumbnail' );
	$sub_sizes = isset( $meta['sizes'] ) && is_array( $meta['sizes'] );
	$note      = '';

	if ( isset( $meta['width'], $meta['height'] ) ) {
		$big = max( $meta['width'], $meta['height'] );
	} else {
		die( __( 'Image data does not exist. Please re-upload the image.' ) );
	}

	$sizer = $big > 400 ? 400 / $big : 1;

	$backup_sizes = get_post_meta( $post_id, '_wp_attachment_backup_sizes', true );
	$can_restore  = false;
	if ( ! empty( $backup_sizes ) && isset( $backup_sizes['full-orig'], $meta['file'] ) ) {
		$can_restore = $backup_sizes['full-orig']['file'] != wp_basename( $meta['file'] );
	}

	if ( $msg ) {
		if ( isset( $msg->error ) ) {
			$note = "<div class='error'><p>$msg->error</p></div>";
		} elseif ( isset( $msg->msg ) ) {
			$note = "<div class='updated'><p>$msg->msg</p></div>";
		}
	}

	?>
	<div class="imgedit-wrap wp-clearfix">
	<div id="imgedit-panel-<?php echo $post_id; ?>">

	<div class="imgedit-settings">
	<div class="imgedit-group">
	<div class="imgedit-group-top">
		<h2><?php _e( 'Scale Image' ); ?></h2>
		<button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" aria-expanded="false"><span class="screen-reader-text"><?php esc_html_e( 'Scale Image Help' ); ?></span></button>
		<div class="imgedit-help">
		<p><?php _e( 'You can proportionally scale the original image. For best results, scaling should be done before you crop, flip, or rotate. Images can only be scaled down, not up.' ); ?></p>
		</div>
		<?php if ( isset( $meta['width'], $meta['height'] ) ) : ?>
		<p>
			<?php
			printf(
				/* translators: %s: Image width and height in pixels. */
				__( 'Original dimensions %s' ),
				'<span class="imgedit-original-dimensions">' . $meta['width'] . ' &times; ' . $meta['height'] . '</span>'
			);
			?>
		</p>
		<?php endif ?>
		<div class="imgedit-submit">

		<fieldset class="imgedit-scale">
		<legend><?php _e( 'New dimensions:' ); ?></legend>
		<div class="nowrap">
		<label for="imgedit-scale-width-<?php echo $post_id; ?>" class="screen-reader-text"><?php _e( 'scale width' ); ?></label>
		<input type="text" id="imgedit-scale-width-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleChanged(<?php echo $post_id; ?>, 1, this)" onblur="imageEdit.scaleChanged(<?php echo $post_id; ?>, 1, this)" value="<?php echo isset( $meta['width'] ) ? $meta['width'] : 0; ?>" />
		<span class="imgedit-separator" aria-hidden="true">&times;</span>
		<label for="imgedit-scale-height-<?php echo $post_id; ?>" class="screen-reader-text"><?php _e( 'scale height' ); ?></label>
		<input type="text" id="imgedit-scale-height-<?php echo $post_id; ?>" onkeyup="imageEdit.scaleChanged(<?php echo $post_id; ?>, 0, this)" onblur="imageEdit.scaleChanged(<?php echo $post_id; ?>, 0, this)" value="<?php echo isset( $meta['height'] ) ? $meta['height'] : 0; ?>" />
		<span class="imgedit-scale-warn" id="imgedit-scale-warn-<?php echo $post_id; ?>">!</span>
		<div class="imgedit-scale-button-wrapper"><input id="imgedit-scale-button" type="button" onclick="imageEdit.action(<?php echo "$post_id, '$nonce'"; ?>, 'scale')" class="button button-primary" value="<?php esc_attr_e( 'Scale' ); ?>" /></div>
		</div>
		</fieldset>

		</div>
	</div>
	</div>

	<?php if ( $can_restore ) { ?>

	<div class="imgedit-group">
	<div class="imgedit-group-top">
		<h2><button type="button" onclick="imageEdit.toggleHelp(this);" class="button-link"><?php _e( 'Restore Original Image' ); ?> <span class="dashicons dashicons-arrow-down imgedit-help-toggle"></span></button></h2>
		<div class="imgedit-help">
		<p>
			<?php
			_e( 'Discard any changes and restore the original image.' );

			if ( ! defined( 'IMAGE_EDIT_OVERWRITE' ) || ! IMAGE_EDIT_OVERWRITE ) {
				echo ' ' . __( 'Previously edited copies of the image will not be deleted.' );
			}
			?>
		</p>
		<div class="imgedit-submit">
		<input type="button" onclick="imageEdit.action(<?php echo "$post_id, '$nonce'"; ?>, 'restore')" class="button button-primary" value="<?php esc_attr_e( 'Restore image' ); ?>" <?php echo $can_restore; ?> />
		</div>
		</div>
	</div>
	</div>

	<?php } ?>

	<div class="imgedit-group">
	<div class="imgedit-group-top">
		<h2><?php _e( 'Image Crop' ); ?></h2>
		<button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" aria-expanded="false"><span class="screen-reader-text"><?php esc_html_e( 'Image Crop Help' ); ?></span></button>

		<div class="imgedit-help">
		<p><?php _e( 'To crop the image, click on it and drag to make your selection.' ); ?></p>

		<p><strong><?php _e( 'Crop Aspect Ratio' ); ?></strong><br />
		<?php _e( 'The aspect ratio is the relationship between the width and height. You can preserve the aspect ratio by holding down the shift key while resizing your selection. Use the input box to specify the aspect ratio, e.g. 1:1 (square), 4:3, 16:9, etc.' ); ?></p>

		<p><strong><?php _e( 'Crop Selection' ); ?></strong><br />
		<?php _e( 'Once you have made your selection, you can adjust it by entering the size in pixels. The minimum selection size is the thumbnail size as set in the Media settings.' ); ?></p>
		</div>
	</div>

	<fieldset class="imgedit-crop-ratio">
		<legend><?php _e( 'Aspect ratio:' ); ?></legend>
		<div class="nowrap">
		<label for="imgedit-crop-width-<?php echo $post_id; ?>" class="screen-reader-text"><?php _e( 'crop ratio width' ); ?></label>
		<input type="text" id="imgedit-crop-width-<?php echo $post_id; ?>" onkeyup="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 0, this)" onblur="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 0, this)" />
		<span class="imgedit-separator" aria-hidden="true">:</span>
		<label for="imgedit-crop-height-<?php echo $post_id; ?>" class="screen-reader-text"><?php _e( 'crop ratio height' ); ?></label>
		<input type="text" id="imgedit-crop-height-<?php echo $post_id; ?>" onkeyup="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 1, this)" onblur="imageEdit.setRatioSelection(<?php echo $post_id; ?>, 1, this)" />
		</div>
	</fieldset>

	<fieldset id="imgedit-crop-sel-<?php echo $post_id; ?>" class="imgedit-crop-sel">
		<legend><?php _e( 'Selection:' ); ?></legend>
		<div class="nowrap">
		<label for="imgedit-sel-width-<?php echo $post_id; ?>" class="screen-reader-text"><?php _e( 'selection width' ); ?></label>
		<input type="text" id="imgedit-sel-width-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" onblur="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" />
		<span class="imgedit-separator" aria-hidden="true">&times;</span>
		<label for="imgedit-sel-height-<?php echo $post_id; ?>" class="screen-reader-text"><?php _e( 'selection height' ); ?></label>
		<input type="text" id="imgedit-sel-height-<?php echo $post_id; ?>" onkeyup="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" onblur="imageEdit.setNumSelection(<?php echo $post_id; ?>, this)" />
		</div>
	</fieldset>

	</div>

	<?php
	if ( $thumb && $sub_sizes ) {
		$thumb_img = wp_constrain_dimensions( $thumb['width'], $thumb['height'], 160, 120 );
		?>

	<div class="imgedit-group imgedit-applyto">
	<div class="imgedit-group-top">
		<h2><?php _e( 'Thumbnail Settings' ); ?></h2>
		<button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" aria-expanded="false"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail Settings Help' ); ?></span></button>
		<p class="imgedit-help"><?php _e( 'You can edit the image while preserving the thumbnail. For example, you may wish to have a square thumbnail that displays just a section of the image.' ); ?></p>
	</div>

	<figure class="imgedit-thumbnail-preview">
		<img src="<?php echo $thumb['url']; ?>" width="<?php echo $thumb_img[0]; ?>" height="<?php echo $thumb_img[1]; ?>" class="imgedit-size-preview" alt="" draggable="false" />
		<figcaption class="imgedit-thumbnail-preview-caption"><?php _e( 'Current thumbnail' ); ?></figcaption>
	</figure>

	<div id="imgedit-save-target-<?php echo $post_id; ?>" class="imgedit-save-target">
	<fieldset>
		<legend><?php _e( 'Apply changes to:' ); ?></legend>

		<span class="imgedit-label">
			<input type="radio" id="imgedit-target-all" name="imgedit-target-<?php echo $post_id; ?>" value="all" checked="checked" />
			<label for="imgedit-target-all"><?php _e( 'All image sizes' ); ?></label>
		</span>

		<span class="imgedit-label">
			<input type="radio" id="imgedit-target-thumbnail" name="imgedit-target-<?php echo $post_id; ?>" value="thumbnail" />
			<label for="imgedit-target-thumbnail"><?php _e( 'Thumbnail' ); ?></label>
		</span>

		<span class="imgedit-label">
			<input type="radio" id="imgedit-target-nothumb" name="imgedit-target-<?php echo $post_id; ?>" value="nothumb" />
			<label for="imgedit-target-nothumb"><?php _e( 'All sizes except thumbnail' ); ?></label>
		</span>
	</fieldset>
	</div>
	</div>

	<?php } ?>

	</div>

	<div class="imgedit-panel-content wp-clearfix">
		<?php echo $note; ?>
		<div class="imgedit-menu wp-clearfix">
			<button type="button" onclick="imageEdit.handleCropToolClick( <?php echo "$post_id, '$nonce'"; ?>, this )" class="imgedit-crop button disabled" disabled><?php esc_html_e( 'Crop' ); ?></button>
			<?php

			// On some setups GD library does not provide imagerotate() - Ticket #11536
			if ( wp_image_editor_supports(
				array(
					'mime_type' => get_post_mime_type( $post_id ),
					'methods'   => array( 'rotate' ),
				)
			) ) {
				$note_no_rotate = '';
				?>
				<button type="button" class="imgedit-rleft button" onclick="imageEdit.rotate( 90, <?php echo "$post_id, '$nonce'"; ?>, this)"><?php esc_html_e( 'Rotate left' ); ?></button>
				<button type="button" class="imgedit-rright button" onclick="imageEdit.rotate(-90, <?php echo "$post_id, '$nonce'"; ?>, this)"><?php esc_html_e( 'Rotate right' ); ?></button>
				<?php
			} else {
				$note_no_rotate = '<p class="note-no-rotate"><em>' . __( 'Image rotation is not supported by your web host.' ) . '</em></p>';
				?>
				<button type="button" class="imgedit-rleft button disabled" disabled></button>
				<button type="button" class="imgedit-rright button disabled" disabled></button>
			<?php } ?>

			<button type="button" onclick="imageEdit.flip(1, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-flipv button"><?php esc_html_e( 'Flip vertical' ); ?></button>
			<button type="button" onclick="imageEdit.flip(2, <?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-fliph button"><?php esc_html_e( 'Flip horizontal' ); ?></button>

			<br class="imgedit-undo-redo-separator" />
			<button type="button" id="image-undo-<?php echo $post_id; ?>" onclick="imageEdit.undo(<?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-undo button disabled" disabled><?php esc_html_e( 'Undo' ); ?></button>
			<button type="button" id="image-redo-<?php echo $post_id; ?>" onclick="imageEdit.redo(<?php echo "$post_id, '$nonce'"; ?>, this)" class="imgedit-redo button disabled" disabled><?php esc_html_e( 'Redo' ); ?></button>
			<?php echo $note_no_rotate; ?>
		</div>

		<input type="hidden" id="imgedit-sizer-<?php echo $post_id; ?>" value="<?php echo $sizer; ?>" />
		<input type="hidden" id="imgedit-history-<?php echo $post_id; ?>" value="" />
		<input type="hidden" id="imgedit-undone-<?php echo $post_id; ?>" value="0" />
		<input type="hidden" id="imgedit-selection-<?php echo $post_id; ?>" value="" />
		<input type="hidden" id="imgedit-x-<?php echo $post_id; ?>" value="<?php echo isset( $meta['width'] ) ? $meta['width'] : 0; ?>" />
		<input type="hidden" id="imgedit-y-<?php echo $post_id; ?>" value="<?php echo isset( $meta['height'] ) ? $meta['height'] : 0; ?>" />

		<div id="imgedit-crop-<?php echo $post_id; ?>" class="imgedit-crop-wrap">
		<img id="image-preview-<?php echo $post_id; ?>" onload="imageEdit.imgLoaded('<?php echo $post_id; ?>')" src="<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>?action=imgedit-preview&amp;_ajax_nonce=<?php echo $nonce; ?>&amp;postid=<?php echo $post_id; ?>&amp;rand=<?php echo rand( 1, 99999 ); ?>" alt="" />
		</div>

		<div class="imgedit-submit">
			<input type="button" onclick="imageEdit.close(<?php echo $post_id; ?>, 1)" class="button imgedit-cancel-btn" value="<?php esc_attr_e( 'Cancel' ); ?>" />
			<input type="button" onclick="imageEdit.save(<?php echo "$post_id, '$nonce'"; ?>)" disabled="disabled" class="button button-primary imgedit-submit-btn" value="<?php esc_attr_e( 'Save' ); ?>" />
		</div>
	</div>

	</div>
	<div class="imgedit-wait" id="imgedit-wait-<?php echo $post_id; ?>"></div>
	<div class="hidden" id="imgedit-leaving-<?php echo $post_id; ?>"><?php _e( "There are unsaved changes that will be lost. 'OK' to continue, 'Cancel' to return to the Image Editor." ); ?></div>
	</div>
	<?php
}

/**
 * Streams image in WP_Image_Editor to browser.
 *
 * @since 2.9.0
 *
 * @param WP_Image_Editor $image         The image editor instance.
 * @param string          $mime_type     The mime type of the image.
 * @param int             $attachment_id The image's attachment post ID.
 * @return bool True on success, false on failure.
 */
function wp_stream_image( $image, $mime_type, $attachment_id ) {
	if ( $image instanceof WP_Image_Editor ) {

		/**
		 * Filters the WP_Image_Editor instance for the image to be streamed to the browser.
		 *
		 * @since 3.5.0
		 *
		 * @param WP_Image_Editor $image         The image editor instance.
		 * @param int             $attachment_id The attachment post ID.
		 */
		$image = apply_filters( 'image_editor_save_pre', $image, $attachment_id );

		if ( is_wp_error( $image->stream( $mime_type ) ) ) {
			return false;
		}

		return true;
	} else {
		/* translators: 1: $image, 2: WP_Image_Editor */
		_deprecated_argument( __FUNCTION__, '3.5.0', sprintf( __( '%1$s needs to be a %2$s object.' ), '$image', 'WP_Image_Editor' ) );

		/**
		 * Filters the GD image resource to be streamed to the browser.
		 *
		 * @since 2.9.0
		 * @deprecated 3.5.0 Use image_editor_save_pre instead.
		 *
		 * @param resource $image         Image resource to be streamed.
		 * @param int      $attachment_id The attachment post ID.
		 */
		$image = apply_filters( 'image_save_pre', $image, $attachment_id );

		switch ( $mime_type ) {
			case 'image/jpeg':
				header( 'Content-Type: image/jpeg' );
				return imagejpeg( $image, null, 90 );
			case 'image/png':
				header( 'Content-Type: image/png' );
				return imagepng( $image );
			case 'image/gif':
				header( 'Content-Type: image/gif' );
				return imagegif( $image );
			default:
				return false;
		}
	}
}

/**
 * Saves image to file.
 *
 * @since 2.9.0
 *
 * @param string $filename
 * @param WP_Image_Editor $image
 * @param string $mime_type
 * @param int $post_id
 * @return bool
 */
function wp_save_image_file( $filename, $image, $mime_type, $post_id ) {
	if ( $image instanceof WP_Image_Editor ) {

		/** This filter is documented in wp-admin/includes/image-edit.php */
		$image = apply_filters( 'image_editor_save_pre', $image, $post_id );

		/**
		 * Filters whether to skip saving the image file.
		 *
		 * Returning a non-null value will short-circuit the save method,
		 * returning that value instead.
		 *
		 * @since 3.5.0
		 *
		 * @param mixed           $override  Value to return instead of saving. Default null.
		 * @param string          $filename  Name of the file to be saved.
		 * @param WP_Image_Editor $image     WP_Image_Editor instance.
		 * @param string          $mime_type Image mime type.
		 * @param int             $post_id   Post ID.
		 */
		$saved = apply_filters( 'wp_save_image_editor_file', null, $filename, $image, $mime_type, $post_id );

		if ( null !== $saved ) {
			return $saved;
		}

		return $image->save( $filename, $mime_type );
	} else {
		/* translators: 1: $image, 2: WP_Image_Editor */
		_deprecated_argument( __FUNCTION__, '3.5.0', sprintf( __( '%1$s needs to be a %2$s object.' ), '$image', 'WP_Image_Editor' ) );

		/** This filter is documented in wp-admin/includes/image-edit.php */
		$image = apply_filters( 'image_save_pre', $image, $post_id );

		/**
		 * Filters whether to skip saving the image file.
		 *
		 * Returning a non-null value will short-circuit the save method,
		 * returning that value instead.
		 *
		 * @since 2.9.0
		 * @deprecated 3.5.0 Use wp_save_image_editor_file instead.
		 *
		 * @param mixed           $override  Value to return instead of saving. Default null.
		 * @param string          $filename  Name of the file to be saved.
		 * @param WP_Image_Editor $image     WP_Image_Editor instance.
		 * @param string          $mime_type Image mime type.
		 * @param int             $post_id   Post ID.
		 */
		$saved = apply_filters( 'wp_save_image_file', null, $filename, $image, $mime_type, $post_id );

		if ( null !== $saved ) {
			return $saved;
		}

		switch ( $mime_type ) {
			case 'image/jpeg':
				/** This filter is documented in wp-includes/class-wp-image-editor.php */
				return imagejpeg( $image, $filename, apply_filters( 'jpeg_quality', 90, 'edit_image' ) );
			case 'image/png':
				return imagepng( $image, $filename );
			case 'image/gif':
				return imagegif( $image, $filename );
			default:
				return false;
		}
	}
}

/**
 * Image preview ratio. Internal use only.
 *
 * @since 2.9.0
 *
 * @ignore
 * @param int $w Image width in pixels.
 * @param int $h Image height in pixels.
 * @return float|int Image preview ratio.
 */
function _image_get_preview_ratio( $w, $h ) {
	$max = max( $w, $h );
	return $max > 400 ? ( 400 / $max ) : 1;
}

/**
 * Returns an image resource. Internal use only.
 *
 * @since 2.9.0
 * @deprecated 3.5.0 Use WP_Image_Editor::rotate()
 * @see WP_Image_Editor::rotate()
 *
 * @ignore
 * @param resource  $img   Image resource.
 * @param float|int $angle Image rotation angle, in degrees.
 * @return resource|false GD image resource, false otherwise.
 */
function _rotate_image_resource( $img, $angle ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'WP_Image_Editor::rotate()' );
	if ( function_exists( 'imagerotate' ) ) {
		$rotated = imagerotate( $img, $angle, 0 );
		if ( is_resource( $rotated ) ) {
			imagedestroy( $img );
			$img = $rotated;
		}
	}
	return $img;
}

/**
 * Flips an image resource. Internal use only.
 *
 * @since 2.9.0
 * @deprecated 3.5.0 Use WP_Image_Editor::flip()
 * @see WP_Image_Editor::flip()
 *
 * @ignore
 * @param resource $img  Image resource.
 * @param bool     $horz Whether to flip horizontally.
 * @param bool     $vert Whether to flip vertically.
 * @return resource (maybe) flipped image resource.
 */
function _flip_image_resource( $img, $horz, $vert ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'WP_Image_Editor::flip()' );
	$w   = imagesx( $img );
	$h   = imagesy( $img );
	$dst = wp_imagecreatetruecolor( $w, $h );
	if ( is_resource( $dst ) ) {
		$sx = $vert ? ( $w - 1 ) : 0;
		$sy = $horz ? ( $h - 1 ) : 0;
		$sw = $vert ? -$w : $w;
		$sh = $horz ? -$h : $h;

		if ( imagecopyresampled( $dst, $img, 0, 0, $sx, $sy, $w, $h, $sw, $sh ) ) {
			imagedestroy( $img );
			$img = $dst;
		}
	}
	return $img;
}

/**
 * Crops an image resource. Internal use only.
 *
 * @since 2.9.0
 *
 * @ignore
 * @param resource $img Image resource.
 * @param float    $x   Source point x-coordinate.
 * @param float    $y   Source point y-cooredinate.
 * @param float    $w   Source width.
 * @param float    $h   Source height.
 * @return resource (maybe) cropped image resource.
 */
function _crop_image_resource( $img, $x, $y, $w, $h ) {
	$dst = wp_imagecreatetruecolor( $w, $h );
	if ( is_resource( $dst ) ) {
		if ( imagecopy( $dst, $img, 0, 0, $x, $y, $w, $h ) ) {
			imagedestroy( $img );
			$img = $dst;
		}
	}
	return $img;
}

/**
 * Performs group of changes on Editor specified.
 *
 * @since 2.9.0
 *
 * @param WP_Image_Editor $image   WP_Image_Editor instance.
 * @param array           $changes Array of change operations.
 * @return WP_Image_Editor WP_Image_Editor instance with changes applied.
 */
function image_edit_apply_changes( $image, $changes ) {
	if ( is_resource( $image ) ) {
		/* translators: 1: $image, 2: WP_Image_Editor */
		_deprecated_argument( __FUNCTION__, '3.5.0', sprintf( __( '%1$s needs to be a %2$s object.' ), '$image', 'WP_Image_Editor' ) );
	}

	if ( ! is_array( $changes ) ) {
		return $image;
	}

	// Expand change operations.
	foreach ( $changes as $key => $obj ) {
		if ( isset( $obj->r ) ) {
			$obj->type  = 'rotate';
			$obj->angle = $obj->r;
			unset( $obj->r );
		} elseif ( isset( $obj->f ) ) {
			$obj->type = 'flip';
			$obj->axis = $obj->f;
			unset( $obj->f );
		} elseif ( isset( $obj->c ) ) {
			$obj->type = 'crop';
			$obj->sel  = $obj->c;
			unset( $obj->c );
		}
		$changes[ $key ] = $obj;
	}

	// Combine operations.
	if ( count( $changes ) > 1 ) {
		$filtered = array( $changes[0] );
		for ( $i = 0, $j = 1, $c = count( $changes ); $j < $c; $j++ ) {
			$combined = false;
			if ( $filtered[ $i ]->type == $changes[ $j ]->type ) {
				switch ( $filtered[ $i ]->type ) {
					case 'rotate':
						$filtered[ $i ]->angle += $changes[ $j ]->angle;
						$combined               = true;
						break;
					case 'flip':
						$filtered[ $i ]->axis ^= $changes[ $j ]->axis;
						$combined              = true;
						break;
				}
			}
			if ( ! $combined ) {
				$filtered[ ++$i ] = $changes[ $j ];
			}
		}
		$changes = $filtered;
		unset( $filtered );
	}

	// Image resource before applying the changes.
	if ( $image instanceof WP_Image_Editor ) {

		/**
		 * Filters the WP_Image_Editor instance before applying changes to the image.
		 *
		 * @since 3.5.0
		 *
		 * @param WP_Image_Editor $image   WP_Image_Editor instance.
		 * @param array           $changes Array of change operations.
		 */
		$image = apply_filters( 'wp_image_editor_before_change', $image, $changes );
	} elseif ( is_resource( $image ) ) {

		/**
		 * Filters the GD image resource before applying changes to the image.
		 *
		 * @since 2.9.0
		 * @deprecated 3.5.0 Use wp_image_editor_before_change instead.
		 *
		 * @param resource $image   GD image resource.
		 * @param array    $changes Array of change operations.
		 */
		$image = apply_filters( 'image_edit_before_change', $image, $changes );
	}

	foreach ( $changes as $operation ) {
		switch ( $operation->type ) {
			case 'rotate':
				if ( $operation->angle != 0 ) {
					if ( $image instanceof WP_Image_Editor ) {
						$image->rotate( $operation->angle );
					} else {
						$image = _rotate_image_resource( $image, $operation->angle );
					}
				}
				break;
			case 'flip':
				if ( $operation->axis != 0 ) {
					if ( $image instanceof WP_Image_Editor ) {
						$image->flip( ( $operation->axis & 1 ) != 0, ( $operation->axis & 2 ) != 0 );
					} else {
						$image = _flip_image_resource( $image, ( $operation->axis & 1 ) != 0, ( $operation->axis & 2 ) != 0 );
					}
				}
				break;
			case 'crop':
				$sel = $operation->sel;

				if ( $image instanceof WP_Image_Editor ) {
					$size = $image->get_size();
					$w    = $size['width'];
					$h    = $size['height'];

					$scale = 1 / _image_get_preview_ratio( $w, $h ); // discard preview scaling
					$image->crop( $sel->x * $scale, $sel->y * $scale, $sel->w * $scale, $sel->h * $scale );
				} else {
					$scale = 1 / _image_get_preview_ratio( imagesx( $image ), imagesy( $image ) ); // discard preview scaling
					$image = _crop_image_resource( $image, $sel->x * $scale, $sel->y * $scale, $sel->w * $scale, $sel->h * $scale );
				}
				break;
		}
	}

	return $image;
}


/**
 * Streams image in post to browser, along with enqueued changes
 * in $_REQUEST['history']
 *
 * @since 2.9.0
 *
 * @param int $post_id
 * @return bool
 */
function stream_preview_image( $post_id ) {
	$post = get_post( $post_id );

	wp_raise_memory_limit( 'admin' );

	$img = wp_get_image_editor( _load_image_to_edit_path( $post_id ) );

	if ( is_wp_error( $img ) ) {
		return false;
	}

	$changes = ! empty( $_REQUEST['history'] ) ? json_decode( wp_unslash( $_REQUEST['history'] ) ) : null;
	if ( $changes ) {
		$img = image_edit_apply_changes( $img, $changes );
	}

	// Scale the image.
	$size = $img->get_size();
	$w    = $size['width'];
	$h    = $size['height'];

	$ratio = _image_get_preview_ratio( $w, $h );
	$w2    = max( 1, $w * $ratio );
	$h2    = max( 1, $h * $ratio );

	if ( is_wp_error( $img->resize( $w2, $h2 ) ) ) {
		return false;
	}

	return wp_stream_image( $img, $post->post_mime_type, $post_id );
}

/**
 * Restores the metadata for a given attachment.
 *
 * @since 2.9.0
 *
 * @param int $post_id Attachment post ID.
 * @return stdClass Image restoration message object.
 */
function wp_restore_image( $post_id ) {
	$meta             = wp_get_attachment_metadata( $post_id );
	$file             = get_attached_file( $post_id );
	$backup_sizes     = get_post_meta( $post_id, '_wp_attachment_backup_sizes', true );
	$old_backup_sizes = $backup_sizes;
	$restored         = false;
	$msg              = new stdClass;

	if ( ! is_array( $backup_sizes ) ) {
		$msg->error = __( 'Cannot load image metadata.' );
		return $msg;
	}

	$parts         = pathinfo( $file );
	$suffix        = time() . rand( 100, 999 );
	$default_sizes = get_intermediate_image_sizes();

	if ( isset( $backup_sizes['full-orig'] ) && is_array( $backup_sizes['full-orig'] ) ) {
		$data = $backup_sizes['full-orig'];

		if ( $parts['basename'] != $data['file'] ) {
			if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE ) {

				// Delete only if it's an edited image.
				if ( preg_match( '/-e[0-9]{13}\./', $parts['basename'] ) ) {
					wp_delete_file( $file );
				}
			} elseif ( isset( $meta['width'], $meta['height'] ) ) {
				$backup_sizes[ "full-$suffix" ] = array(
					'width'  => $meta['width'],
					'height' => $meta['height'],
					'file'   => $parts['basename'],
				);
			}
		}

		$restored_file = path_join( $parts['dirname'], $data['file'] );
		$restored      = update_attached_file( $post_id, $restored_file );

		$meta['file']   = _wp_relative_upload_path( $restored_file );
		$meta['width']  = $data['width'];
		$meta['height'] = $data['height'];
	}

	foreach ( $default_sizes as $default_size ) {
		if ( isset( $backup_sizes[ "$default_size-orig" ] ) ) {
			$data = $backup_sizes[ "$default_size-orig" ];
			if ( isset( $meta['sizes'][ $default_size ] ) && $meta['sizes'][ $default_size ]['file'] != $data['file'] ) {
				if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE ) {

					// Delete only if it's an edited image.
					if ( preg_match( '/-e[0-9]{13}-/', $meta['sizes'][ $default_size ]['file'] ) ) {
						$delete_file = path_join( $parts['dirname'], $meta['sizes'][ $default_size ]['file'] );
						wp_delete_file( $delete_file );
					}
				} else {
					$backup_sizes[ "$default_size-{$suffix}" ] = $meta['sizes'][ $default_size ];
				}
			}

			$meta['sizes'][ $default_size ] = $data;
		} else {
			unset( $meta['sizes'][ $default_size ] );
		}
	}

	if ( ! wp_update_attachment_metadata( $post_id, $meta ) ||
		( $old_backup_sizes !== $backup_sizes && ! update_post_meta( $post_id, '_wp_attachment_backup_sizes', $backup_sizes ) ) ) {

		$msg->error = __( 'Cannot save image metadata.' );
		return $msg;
	}

	if ( ! $restored ) {
		$msg->error = __( 'Image metadata is inconsistent.' );
	} else {
		$msg->msg = __( 'Image restored successfully.' );
	}

	return $msg;
}

/**
 * Saves image to post along with enqueued changes
 * in $_REQUEST['history']
 *
 * @since 2.9.0
 *
 * @param int $post_id
 * @return \stdClass
 */
function wp_save_image( $post_id ) {
	$_wp_additional_image_sizes = wp_get_additional_image_sizes();

	$return  = new stdClass;
	$success = false;
	$delete  = false;
	$scaled  = false;
	$nocrop  = false;
	$post    = get_post( $post_id );

	$img = wp_get_image_editor( _load_image_to_edit_path( $post_id, 'full' ) );
	if ( is_wp_error( $img ) ) {
		$return->error = esc_js( __( 'Unable to create new image.' ) );
		return $return;
	}

	$fwidth  = ! empty( $_REQUEST['fwidth'] ) ? intval( $_REQUEST['fwidth'] ) : 0;
	$fheight = ! empty( $_REQUEST['fheight'] ) ? intval( $_REQUEST['fheight'] ) : 0;
	$target  = ! empty( $_REQUEST['target'] ) ? preg_replace( '/[^a-z0-9_-]+/i', '', $_REQUEST['target'] ) : '';
	$scale   = ! empty( $_REQUEST['do'] ) && 'scale' == $_REQUEST['do'];

	if ( $scale && $fwidth > 0 && $fheight > 0 ) {
		$size = $img->get_size();
		$sX   = $size['width'];
		$sY   = $size['height'];

		// Check if it has roughly the same w / h ratio.
		$diff = round( $sX / $sY, 2 ) - round( $fwidth / $fheight, 2 );
		if ( -0.1 < $diff && $diff < 0.1 ) {
			// Scale the full size image.
			if ( $img->resize( $fwidth, $fheight ) ) {
				$scaled = true;
			}
		}

		if ( ! $scaled ) {
			$return->error = esc_js( __( 'Error while saving the scaled image. Please reload the page and try again.' ) );
			return $return;
		}
	} elseif ( ! empty( $_REQUEST['history'] ) ) {
		$changes = json_decode( wp_unslash( $_REQUEST['history'] ) );
		if ( $changes ) {
			$img = image_edit_apply_changes( $img, $changes );
		}
	} else {
		$return->error = esc_js( __( 'Nothing to save, the image has not changed.' ) );
		return $return;
	}

	$meta         = wp_get_attachment_metadata( $post_id );
	$backup_sizes = get_post_meta( $post->ID, '_wp_attachment_backup_sizes', true );

	if ( ! is_array( $meta ) ) {
		$return->error = esc_js( __( 'Image data does not exist. Please re-upload the image.' ) );
		return $return;
	}

	if ( ! is_array( $backup_sizes ) ) {
		$backup_sizes = array();
	}

	// Generate new filename.
	$path = get_attached_file( $post_id );

	$basename = pathinfo( $path, PATHINFO_BASENAME );
	$dirname  = pathinfo( $path, PATHINFO_DIRNAME );
	$ext      = pathinfo( $path, PATHINFO_EXTENSION );
	$filename = pathinfo( $path, PATHINFO_FILENAME );
	$suffix   = time() . rand( 100, 999 );

	if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE &&
		isset( $backup_sizes['full-orig'] ) && $backup_sizes['full-orig']['file'] != $basename ) {

		if ( 'thumbnail' == $target ) {
			$new_path = "{$dirname}/{$filename}-temp.{$ext}";
		} else {
			$new_path = $path;
		}
	} else {
		while ( true ) {
			$filename     = preg_replace( '/-e([0-9]+)$/', '', $filename );
			$filename    .= "-e{$suffix}";
			$new_filename = "{$filename}.{$ext}";
			$new_path     = "{$dirname}/$new_filename";
			if ( file_exists( $new_path ) ) {
				$suffix++;
			} else {
				break;
			}
		}
	}

	// Save the full-size file, also needed to create sub-sizes.
	if ( ! wp_save_image_file( $new_path, $img, $post->post_mime_type, $post_id ) ) {
		$return->error = esc_js( __( 'Unable to save the image.' ) );
		return $return;
	}

	if ( 'nothumb' === $target || 'all' === $target || 'full' === $target || $scaled ) {
		$tag = false;
		if ( isset( $backup_sizes['full-orig'] ) ) {
			if ( ( ! defined( 'IMAGE_EDIT_OVERWRITE' ) || ! IMAGE_EDIT_OVERWRITE ) && $backup_sizes['full-orig']['file'] !== $basename ) {
				$tag = "full-$suffix";
			}
		} else {
			$tag = 'full-orig';
		}

		if ( $tag ) {
			$backup_sizes[ $tag ] = array(
				'width'  => $meta['width'],
				'height' => $meta['height'],
				'file'   => $basename,
			);
		}
		$success = ( $path === $new_path ) || update_attached_file( $post_id, $new_path );

		$meta['file'] = _wp_relative_upload_path( $new_path );

		$size           = $img->get_size();
		$meta['width']  = $size['width'];
		$meta['height'] = $size['height'];

		if ( $success && ( 'nothumb' == $target || 'all' == $target ) ) {
			$sizes = get_intermediate_image_sizes();
			if ( 'nothumb' == $target ) {
				$sizes = array_diff( $sizes, array( 'thumbnail' ) );
			}
		}

		$return->fw = $meta['width'];
		$return->fh = $meta['height'];
	} elseif ( 'thumbnail' == $target ) {
		$sizes   = array( 'thumbnail' );
		$success = true;
		$delete  = true;
		$nocrop  = true;
	}

	/*
	 * We need to remove any existing resized image files because
	 * a new crop or rotate could generate different sizes (and hence, filenames),
	 * keeping the new resized images from overwriting the existing image files.
	 * https://core.trac.wordpress.org/ticket/32171
	 */
	if ( defined( 'IMAGE_EDIT_OVERWRITE' ) && IMAGE_EDIT_OVERWRITE && ! empty( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $size ) {
			if ( ! empty( $size['file'] ) && preg_match( '/-e[0-9]{13}-/', $size['file'] ) ) {
				$delete_file = path_join( $dirname, $size['file'] );
				wp_delete_file( $delete_file );
			}
		}
	}

	if ( isset( $sizes ) ) {
		$_sizes = array();

		foreach ( $sizes as $size ) {
			$tag = false;
			if ( isset( $meta['sizes'][ $size ] ) ) {
				if ( isset( $backup_sizes[ "$size-orig" ] ) ) {
					if ( ( ! defined( 'IMAGE_EDIT_OVERWRITE' ) || ! IMAGE_EDIT_OVERWRITE ) && $backup_sizes[ "$size-orig" ]['file'] != $meta['sizes'][ $size ]['file'] ) {
						$tag = "$size-$suffix";
					}
				} else {
					$tag = "$size-orig";
				}

				if ( $tag ) {
					$backup_sizes[ $tag ] = $meta['sizes'][ $size ];
				}
			}

			if ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
				$width  = intval( $_wp_additional_image_sizes[ $size ]['width'] );
				$height = intval( $_wp_additional_image_sizes[ $size ]['height'] );
				$crop   = ( $nocrop ) ? false : $_wp_additional_image_sizes[ $size ]['crop'];
			} else {
				$height = get_option( "{$size}_size_h" );
				$width  = get_option( "{$size}_size_w" );
				$crop   = ( $nocrop ) ? false : get_option( "{$size}_crop" );
			}

			$_sizes[ $size ] = array(
				'width'  => $width,
				'height' => $height,
				'crop'   => $crop,
			);
		}

		$meta['sizes'] = array_merge( $meta['sizes'], $img->multi_resize( $_sizes ) );
	}

	unset( $img );

	if ( $success ) {
		wp_update_attachment_metadata( $post_id, $meta );
		update_post_meta( $post_id, '_wp_attachment_backup_sizes', $backup_sizes );

		if ( $target == 'thumbnail' || $target == 'all' || $target == 'full' ) {
			// Check if it's an image edit from attachment edit screen
			if ( ! empty( $_REQUEST['context'] ) && 'edit-attachment' == $_REQUEST['context'] ) {
				$thumb_url         = wp_get_attachment_image_src( $post_id, array( 900, 600 ), true );
				$return->thumbnail = $thumb_url[0];
			} else {
				$file_url = wp_get_attachment_url( $post_id );
				if ( ! empty( $meta['sizes']['thumbnail'] ) ) {
					$thumb             = $meta['sizes']['thumbnail'];
					$return->thumbnail = path_join( dirname( $file_url ), $thumb['file'] );
				} else {
					$return->thumbnail = "$file_url?w=128&h=128";
				}
			}
		}
	} else {
		$delete = true;
	}

	if ( $delete ) {
		wp_delete_file( $new_path );
	}

	$return->msg = esc_js( __( 'Image saved' ) );
	return $return;
}
