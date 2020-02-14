<?php
/**
 * File contains all the administration image manipulation functions.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Crop an Image to a given size.
 *
 * @since 2.1.0
 *
 * @param string|int $src The source file or Attachment ID.
 * @param int $src_x The start x position to crop from.
 * @param int $src_y The start y position to crop from.
 * @param int $src_w The width to crop.
 * @param int $src_h The height to crop.
 * @param int $dst_w The destination width.
 * @param int $dst_h The destination height.
 * @param int $src_abs Optional. If the source crop points are absolute.
 * @param string $dst_file Optional. The destination file to write to.
 * @return string|WP_Error New filepath on success, WP_Error on failure.
 */
function wp_crop_image( $src, $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs = false, $dst_file = false ) {
	$src_file = $src;
	if ( is_numeric( $src ) ) { // Handle int as attachment ID.
		$src_file = get_attached_file( $src );

		if ( ! file_exists( $src_file ) ) {
			// If the file doesn't exist, attempt a URL fopen on the src link.
			// This can occur with certain file replication plugins.
			$src = _load_image_to_edit_path( $src, 'full' );
		} else {
			$src = $src_file;
		}
	}

	$editor = wp_get_image_editor( $src );
	if ( is_wp_error( $editor ) ) {
		return $editor;
	}

	$src = $editor->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs );
	if ( is_wp_error( $src ) ) {
		return $src;
	}

	if ( ! $dst_file ) {
		$dst_file = str_replace( wp_basename( $src_file ), 'cropped-' . wp_basename( $src_file ), $src_file );
	}

	/*
	 * The directory containing the original file may no longer exist when
	 * using a replication plugin.
	 */
	wp_mkdir_p( dirname( $dst_file ) );

	$dst_file = dirname( $dst_file ) . '/' . wp_unique_filename( dirname( $dst_file ), wp_basename( $dst_file ) );

	$result = $editor->save( $dst_file );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return $dst_file;
}

/**
 * Compare the existing image sub-sizes (as saved in the attachment meta)
 * to the currently registered image sub-sizes, and return the difference.
 *
 * Registered sub-sizes that are larger than the image are skipped.
 *
 * @since 5.3.0
 *
 * @param int $attachment_id The image attachment post ID.
 * @return array An array of the image sub-sizes that are currently defined but don't exist for this image.
 */
function wp_get_missing_image_subsizes( $attachment_id ) {
	if ( ! wp_attachment_is_image( $attachment_id ) ) {
		return array();
	}

	$registered_sizes = wp_get_registered_image_subsizes();
	$image_meta       = wp_get_attachment_metadata( $attachment_id );

	// Meta error?
	if ( empty( $image_meta ) ) {
		return $registered_sizes;
	}

	// Use the originally uploaded image dimensions as full_width and full_height.
	if ( ! empty( $image_meta['original_image'] ) ) {
		$image_file = wp_get_original_image_path( $attachment_id );
		$imagesize  = @getimagesize( $image_file );
	}

	if ( ! empty( $imagesize ) ) {
		$full_width  = $imagesize[0];
		$full_height = $imagesize[1];
	} else {
		$full_width  = (int) $image_meta['width'];
		$full_height = (int) $image_meta['height'];
	}

	$possible_sizes = array();

	// Skip registered sizes that are too large for the uploaded image.
	foreach ( $registered_sizes as $size_name => $size_data ) {
		if ( image_resize_dimensions( $full_width, $full_height, $size_data['width'], $size_data['height'], $size_data['crop'] ) ) {
			$possible_sizes[ $size_name ] = $size_data;
		}
	}

	if ( empty( $image_meta['sizes'] ) ) {
		$image_meta['sizes'] = array();
	}

	/*
	 * Remove sizes that already exist. Only checks for matching "size names".
	 * It is possible that the dimensions for a particular size name have changed.
	 * For example the user has changed the values on the Settings -> Media screen.
	 * However we keep the old sub-sizes with the previous dimensions
	 * as the image may have been used in an older post.
	 */
	$missing_sizes = array_diff_key( $possible_sizes, $image_meta['sizes'] );

	/**
	 * Filters the array of missing image sub-sizes for an uploaded image.
	 *
	 * @since 5.3.0
	 *
	 * @param array $missing_sizes Array with the missing image sub-sizes.
	 * @param array $image_meta    The image meta data.
	 * @param int   $attachment_id The image attachment post ID.
	 */
	return apply_filters( 'wp_get_missing_image_subsizes', $missing_sizes, $image_meta, $attachment_id );
}

/**
 * If any of the currently registered image sub-sizes are missing,
 * create them and update the image meta data.
 *
 * @since 5.3.0
 *
 * @param int $attachment_id The image attachment post ID.
 * @return array|WP_Error The updated image meta data array or WP_Error object
 *                        if both the image meta and the attached file are missing.
 */
function wp_update_image_subsizes( $attachment_id ) {
	$image_meta = wp_get_attachment_metadata( $attachment_id );
	$image_file = wp_get_original_image_path( $attachment_id );

	if ( empty( $image_meta ) || ! is_array( $image_meta ) ) {
		// Previously failed upload?
		// If there is an uploaded file, make all sub-sizes and generate all of the attachment meta.
		if ( ! empty( $image_file ) ) {
			$image_meta = wp_create_image_subsizes( $image_file, $attachment_id );
		} else {
			return new WP_Error( 'invalid_attachment', __( 'The attached file cannot be found.' ) );
		}
	} else {
		$missing_sizes = wp_get_missing_image_subsizes( $attachment_id );

		if ( empty( $missing_sizes ) ) {
			return $image_meta;
		}

		// This also updates the image meta.
		$image_meta = _wp_make_subsizes( $missing_sizes, $image_file, $image_meta, $attachment_id );
	}

	/** This filter is documented in wp-admin/includes/image.php */
	$image_meta = apply_filters( 'wp_generate_attachment_metadata', $image_meta, $attachment_id, 'update' );

	// Save the updated metadata.
	wp_update_attachment_metadata( $attachment_id, $image_meta );

	return $image_meta;
}

/**
 * Updates the attached file and image meta data when the original image was edited.
 *
 * @since 5.3.0
 * @access private
 *
 * @param array  $saved_data    The data returned from WP_Image_Editor after successfully saving an image.
 * @param string $original_file Path to the original file.
 * @param array  $image_meta    The image meta data.
 * @param int    $attachment_id The attachment post ID.
 * @return array The updated image meta data.
 */
function _wp_image_meta_replace_original( $saved_data, $original_file, $image_meta, $attachment_id ) {
	$new_file = $saved_data['path'];

	// Update the attached file meta.
	update_attached_file( $attachment_id, $new_file );

	// Width and height of the new image.
	$image_meta['width']  = $saved_data['width'];
	$image_meta['height'] = $saved_data['height'];

	// Make the file path relative to the upload dir.
	$image_meta['file'] = _wp_relative_upload_path( $new_file );

	// Store the original image file name in image_meta.
	$image_meta['original_image'] = wp_basename( $original_file );

	return $image_meta;
}

/**
 * Creates image sub-sizes, adds the new data to the image meta `sizes` array, and updates the image metadata.
 *
 * Intended for use after an image is uploaded. Saves/updates the image metadata after each
 * sub-size is created. If there was an error, it is added to the returned image metadata array.
 *
 * @since 5.3.0
 *
 * @param string $file          Full path to the image file.
 * @param int    $attachment_id Attachment Id to process.
 * @return array The image attachment meta data.
 */
function wp_create_image_subsizes( $file, $attachment_id ) {
	$imagesize = @getimagesize( $file );

	if ( empty( $imagesize ) ) {
		// File is not an image.
		return array();
	}

	// Default image meta.
	$image_meta = array(
		'width'  => $imagesize[0],
		'height' => $imagesize[1],
		'file'   => _wp_relative_upload_path( $file ),
		'sizes'  => array(),
	);

	// Fetch additional metadata from EXIF/IPTC.
	$exif_meta = wp_read_image_metadata( $file );

	if ( $exif_meta ) {
		$image_meta['image_meta'] = $exif_meta;
	}

	// Do not scale (large) PNG images. May result in sub-sizes that have greater file size than the original. See #48736.
	if ( 'image/png' !== $imagesize['mime'] ) {

		/**
		 * Filters the "BIG image" threshold value.
		 *
		 * If the original image width or height is above the threshold, it will be scaled down. The threshold is
		 * used as max width and max height. The scaled down image will be used as the largest available size, including
		 * the `_wp_attached_file` post meta value.
		 *
		 * Returning `false` from the filter callback will disable the scaling.
		 *
		 * @since 5.3.0
		 *
		 * @param int    $threshold     The threshold value in pixels. Default 2560.
		 * @param array  $imagesize     {
		 *     Indexed array of the image width and height in pixels.
		 *
		 *     @type int $0 The image width.
		 *     @type int $1 The image height.
		 * }
		 * @param string $file          Full path to the uploaded image file.
		 * @param int    $attachment_id Attachment post ID.
		 */
		$threshold = (int) apply_filters( 'big_image_size_threshold', 2560, $imagesize, $file, $attachment_id );

		// If the original image's dimensions are over the threshold,
		// scale the image and use it as the "full" size.
		if ( $threshold && ( $image_meta['width'] > $threshold || $image_meta['height'] > $threshold ) ) {
			$editor = wp_get_image_editor( $file );

			if ( is_wp_error( $editor ) ) {
				// This image cannot be edited.
				return $image_meta;
			}

			// Resize the image.
			$resized = $editor->resize( $threshold, $threshold );
			$rotated = null;

			// If there is EXIF data, rotate according to EXIF Orientation.
			if ( ! is_wp_error( $resized ) && is_array( $exif_meta ) ) {
				$resized = $editor->maybe_exif_rotate();
				$rotated = $resized;
			}

			if ( ! is_wp_error( $resized ) ) {
				// Append "-scaled" to the image file name. It will look like "my_image-scaled.jpg".
				// This doesn't affect the sub-sizes names as they are generated from the original image (for best quality).
				$saved = $editor->save( $editor->generate_filename( 'scaled' ) );

				if ( ! is_wp_error( $saved ) ) {
					$image_meta = _wp_image_meta_replace_original( $saved, $file, $image_meta, $attachment_id );

					// If the image was rotated update the stored EXIF data.
					if ( true === $rotated && ! empty( $image_meta['image_meta']['orientation'] ) ) {
						$image_meta['image_meta']['orientation'] = 1;
					}
				} else {
					// TODO: Log errors.
				}
			} else {
				// TODO: Log errors.
			}
		} elseif ( ! empty( $exif_meta['orientation'] ) && 1 !== (int) $exif_meta['orientation'] ) {
			// Rotate the whole original image if there is EXIF data and "orientation" is not 1.

			$editor = wp_get_image_editor( $file );

			if ( is_wp_error( $editor ) ) {
				// This image cannot be edited.
				return $image_meta;
			}

			// Rotate the image.
			$rotated = $editor->maybe_exif_rotate();

			if ( true === $rotated ) {
				// Append `-rotated` to the image file name.
				$saved = $editor->save( $editor->generate_filename( 'rotated' ) );

				if ( ! is_wp_error( $saved ) ) {
					$image_meta = _wp_image_meta_replace_original( $saved, $file, $image_meta, $attachment_id );

					// Update the stored EXIF data.
					if ( ! empty( $image_meta['image_meta']['orientation'] ) ) {
						$image_meta['image_meta']['orientation'] = 1;
					}
				} else {
					// TODO: Log errors.
				}
			}
		}
	}

	/*
	 * Initial save of the new metadata.
	 * At this point the file was uploaded and moved to the uploads directory
	 * but the image sub-sizes haven't been created yet and the `sizes` array is empty.
	 */
	wp_update_attachment_metadata( $attachment_id, $image_meta );

	$new_sizes = wp_get_registered_image_subsizes();

	/**
	 * Filters the image sizes automatically generated when uploading an image.
	 *
	 * @since 2.9.0
	 * @since 4.4.0 Added the `$image_meta` argument.
	 * @since 5.3.0 Added the `$attachment_id` argument.
	 *
	 * @param array $new_sizes     Associative array of image sizes to be created.
	 * @param array $image_meta    The image meta data: width, height, file, sizes, etc.
	 * @param int   $attachment_id The attachment post ID for the image.
	 */
	$new_sizes = apply_filters( 'intermediate_image_sizes_advanced', $new_sizes, $image_meta, $attachment_id );

	return _wp_make_subsizes( $new_sizes, $file, $image_meta, $attachment_id );
}

/**
 * Low-level function to create image sub-sizes.
 *
 * Updates the image meta after each sub-size is created.
 * Errors are stored in the returned image metadata array.
 *
 * @since 5.3.0
 * @access private
 *
 * @param array  $new_sizes     Array defining what sizes to create.
 * @param string $file          Full path to the image file.
 * @param array  $image_meta    The attachment meta data array.
 * @param int    $attachment_id Attachment Id to process.
 * @return array The attachment meta data with updated `sizes` array. Includes an array of errors encountered while resizing.
 */
function _wp_make_subsizes( $new_sizes, $file, $image_meta, $attachment_id ) {
	if ( empty( $image_meta ) || ! is_array( $image_meta ) ) {
		// Not an image attachment.
		return array();
	}

	// Check if any of the new sizes already exist.
	if ( isset( $image_meta['sizes'] ) && is_array( $image_meta['sizes'] ) ) {
		foreach ( $image_meta['sizes'] as $size_name => $size_meta ) {
			/*
			 * Only checks "size name" so we don't override existing images even if the dimensions
			 * don't match the currently defined size with the same name.
			 * To change the behavior, unset changed/mismatched sizes in the `sizes` array in image meta.
			 */
			if ( array_key_exists( $size_name, $new_sizes ) ) {
				unset( $new_sizes[ $size_name ] );
			}
		}
	} else {
		$image_meta['sizes'] = array();
	}

	if ( empty( $new_sizes ) ) {
		// Nothing to do...
		return $image_meta;
	}

	/*
	 * Sort the image sub-sizes in order of priority when creating them.
	 * This ensures there is an appropriate sub-size the user can access immediately
	 * even when there was an error and not all sub-sizes were created.
	 */
	$priority = array(
		'medium'       => null,
		'large'        => null,
		'thumbnail'    => null,
		'medium_large' => null,
	);

	$new_sizes = array_filter( array_merge( $priority, $new_sizes ) );

	$editor = wp_get_image_editor( $file );

	if ( is_wp_error( $editor ) ) {
		// The image cannot be edited.
		return $image_meta;
	}

	// If stored EXIF data exists, rotate the source image before creating sub-sizes.
	if ( ! empty( $image_meta['image_meta'] ) ) {
		$rotated = $editor->maybe_exif_rotate();

		if ( is_wp_error( $rotated ) ) {
			// TODO: Log errors.
		}
	}

	if ( method_exists( $editor, 'make_subsize' ) ) {
		foreach ( $new_sizes as $new_size_name => $new_size_data ) {
			$new_size_meta = $editor->make_subsize( $new_size_data );

			if ( is_wp_error( $new_size_meta ) ) {
				// TODO: Log errors.
			} else {
				// Save the size meta value.
				$image_meta['sizes'][ $new_size_name ] = $new_size_meta;
				wp_update_attachment_metadata( $attachment_id, $image_meta );
			}
		}
	} else {
		// Fall back to `$editor->multi_resize()`.
		$created_sizes = $editor->multi_resize( $new_sizes );

		if ( ! empty( $created_sizes ) ) {
			$image_meta['sizes'] = array_merge( $image_meta['sizes'], $created_sizes );
			wp_update_attachment_metadata( $attachment_id, $image_meta );
		}
	}

	return $image_meta;
}

/**
 * Generate attachment meta data and create image sub-sizes for images.
 *
 * @since 2.1.0
 *
 * @param int $attachment_id Attachment Id to process.
 * @param string $file Filepath of the Attached image.
 * @return mixed Metadata for attachment.
 */
function wp_generate_attachment_metadata( $attachment_id, $file ) {
	$attachment = get_post( $attachment_id );

	$metadata  = array();
	$support   = false;
	$mime_type = get_post_mime_type( $attachment );

	if ( preg_match( '!^image/!', $mime_type ) && file_is_displayable_image( $file ) ) {
		// Make thumbnails and other intermediate sizes.
		$metadata = wp_create_image_subsizes( $file, $attachment_id );
	} elseif ( wp_attachment_is( 'video', $attachment ) ) {
		$metadata = wp_read_video_metadata( $file );
		$support  = current_theme_supports( 'post-thumbnails', 'attachment:video' ) || post_type_supports( 'attachment:video', 'thumbnail' );
	} elseif ( wp_attachment_is( 'audio', $attachment ) ) {
		$metadata = wp_read_audio_metadata( $file );
		$support  = current_theme_supports( 'post-thumbnails', 'attachment:audio' ) || post_type_supports( 'attachment:audio', 'thumbnail' );
	}

	if ( $support && ! empty( $metadata['image']['data'] ) ) {
		// Check for existing cover.
		$hash   = md5( $metadata['image']['data'] );
		$posts  = get_posts(
			array(
				'fields'         => 'ids',
				'post_type'      => 'attachment',
				'post_mime_type' => $metadata['image']['mime'],
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'meta_key'       => '_cover_hash',
				'meta_value'     => $hash,
			)
		);
		$exists = reset( $posts );

		if ( ! empty( $exists ) ) {
			update_post_meta( $attachment_id, '_thumbnail_id', $exists );
		} else {
			$ext = '.jpg';
			switch ( $metadata['image']['mime'] ) {
				case 'image/gif':
					$ext = '.gif';
					break;
				case 'image/png':
					$ext = '.png';
					break;
			}
			$basename = str_replace( '.', '-', wp_basename( $file ) ) . '-image' . $ext;
			$uploaded = wp_upload_bits( $basename, '', $metadata['image']['data'] );
			if ( false === $uploaded['error'] ) {
				$image_attachment = array(
					'post_mime_type' => $metadata['image']['mime'],
					'post_type'      => 'attachment',
					'post_content'   => '',
				);
				/**
				 * Filters the parameters for the attachment thumbnail creation.
				 *
				 * @since 3.9.0
				 *
				 * @param array $image_attachment An array of parameters to create the thumbnail.
				 * @param array $metadata         Current attachment metadata.
				 * @param array $uploaded         An array containing the thumbnail path and url.
				 */
				$image_attachment = apply_filters( 'attachment_thumbnail_args', $image_attachment, $metadata, $uploaded );

				$sub_attachment_id = wp_insert_attachment( $image_attachment, $uploaded['file'] );
				add_post_meta( $sub_attachment_id, '_cover_hash', $hash );
				$attach_data = wp_generate_attachment_metadata( $sub_attachment_id, $uploaded['file'] );
				wp_update_attachment_metadata( $sub_attachment_id, $attach_data );
				update_post_meta( $attachment_id, '_thumbnail_id', $sub_attachment_id );
			}
		}
	} elseif ( 'application/pdf' === $mime_type ) {
		// Try to create image thumbnails for PDFs.

		$fallback_sizes = array(
			'thumbnail',
			'medium',
			'large',
		);

		/**
		 * Filters the image sizes generated for non-image mime types.
		 *
		 * @since 4.7.0
		 *
		 * @param string[] $fallback_sizes An array of image size names.
		 * @param array    $metadata       Current attachment metadata.
		 */
		$fallback_sizes = apply_filters( 'fallback_intermediate_image_sizes', $fallback_sizes, $metadata );

		$registered_sizes = wp_get_registered_image_subsizes();
		$merged_sizes     = array_intersect_key( $registered_sizes, array_flip( $fallback_sizes ) );

		// Force thumbnails to be soft crops.
		if ( isset( $merged_sizes['thumbnail'] ) && is_array( $merged_sizes['thumbnail'] ) ) {
			$merged_sizes['thumbnail']['crop'] = false;
		}

		// Only load PDFs in an image editor if we're processing sizes.
		if ( ! empty( $merged_sizes ) ) {
			$editor = wp_get_image_editor( $file );

			if ( ! is_wp_error( $editor ) ) { // No support for this type of file.
				/*
				 * PDFs may have the same file filename as JPEGs.
				 * Ensure the PDF preview image does not overwrite any JPEG images that already exist.
				 */
				$dirname      = dirname( $file ) . '/';
				$ext          = '.' . pathinfo( $file, PATHINFO_EXTENSION );
				$preview_file = $dirname . wp_unique_filename( $dirname, wp_basename( $file, $ext ) . '-pdf.jpg' );

				$uploaded = $editor->save( $preview_file, 'image/jpeg' );
				unset( $editor );

				// Resize based on the full size image, rather than the source.
				if ( ! is_wp_error( $uploaded ) ) {
					$image_file = $uploaded['path'];
					unset( $uploaded['path'] );

					$metadata['sizes'] = array(
						'full' => $uploaded,
					);

					// Save the meta data before any image post-processing errors could happen.
					wp_update_attachment_metadata( $attachment_id, $metadata );

					// Create sub-sizes saving the image meta after each.
					$metadata = _wp_make_subsizes( $merged_sizes, $image_file, $metadata, $attachment_id );
				}
			}
		}
	}

	// Remove the blob of binary data from the array.
	if ( $metadata ) {
		unset( $metadata['image']['data'] );
	}

	/**
	 * Filters the generated attachment meta data.
	 *
	 * @since 2.1.0
	 * @since 5.3.0 The `$context` parameter was added.
	 *
	 * @param array  $metadata      An array of attachment meta data.
	 * @param int    $attachment_id Current attachment ID.
	 * @param string $context       Additional context. Can be 'create' when metadata was initially created for new attachment
	 *                              or 'update' when the metadata was updated.
	 */
	return apply_filters( 'wp_generate_attachment_metadata', $metadata, $attachment_id, 'create' );
}

/**
 * Convert a fraction string to a decimal.
 *
 * @since 2.5.0
 *
 * @param string $str
 * @return int|float
 */
function wp_exif_frac2dec( $str ) {
	if ( false === strpos( $str, '/' ) ) {
		return $str;
	}

	list( $numerator, $denominator ) = explode( '/', $str );
	if ( ! empty( $denominator ) ) {
		return $numerator / $denominator;
	}
	return $str;
}

/**
 * Convert the exif date format to a unix timestamp.
 *
 * @since 2.5.0
 *
 * @param string $str
 * @return int
 */
function wp_exif_date2ts( $str ) {
	list( $date, $time ) = explode( ' ', trim( $str ) );
	list( $y, $m, $d )   = explode( ':', $date );

	return strtotime( "{$y}-{$m}-{$d} {$time}" );
}

/**
 * Get extended image metadata, exif or iptc as available.
 *
 * Retrieves the EXIF metadata aperture, credit, camera, caption, copyright, iso
 * created_timestamp, focal_length, shutter_speed, and title.
 *
 * The IPTC metadata that is retrieved is APP13, credit, byline, created date
 * and time, caption, copyright, and title. Also includes FNumber, Model,
 * DateTimeDigitized, FocalLength, ISOSpeedRatings, and ExposureTime.
 *
 * @todo Try other exif libraries if available.
 * @since 2.5.0
 *
 * @param string $file
 * @return bool|array False on failure. Image metadata array on success.
 */
function wp_read_image_metadata( $file ) {
	if ( ! file_exists( $file ) ) {
		return false;
	}

	list( , , $image_type ) = @getimagesize( $file );

	/*
	 * EXIF contains a bunch of data we'll probably never need formatted in ways
	 * that are difficult to use. We'll normalize it and just extract the fields
	 * that are likely to be useful. Fractions and numbers are converted to
	 * floats, dates to unix timestamps, and everything else to strings.
	 */
	$meta = array(
		'aperture'          => 0,
		'credit'            => '',
		'camera'            => '',
		'caption'           => '',
		'created_timestamp' => 0,
		'copyright'         => '',
		'focal_length'      => 0,
		'iso'               => 0,
		'shutter_speed'     => 0,
		'title'             => '',
		'orientation'       => 0,
		'keywords'          => array(),
	);

	$iptc = array();
	/*
	 * Read IPTC first, since it might contain data not available in exif such
	 * as caption, description etc.
	 */
	if ( is_callable( 'iptcparse' ) ) {
		@getimagesize( $file, $info );

		if ( ! empty( $info['APP13'] ) ) {
			$iptc = @iptcparse( $info['APP13'] );

			// Headline, "A brief synopsis of the caption".
			if ( ! empty( $iptc['2#105'][0] ) ) {
				$meta['title'] = trim( $iptc['2#105'][0] );
				/*
				* Title, "Many use the Title field to store the filename of the image,
				* though the field may be used in many ways".
				*/
			} elseif ( ! empty( $iptc['2#005'][0] ) ) {
				$meta['title'] = trim( $iptc['2#005'][0] );
			}

			if ( ! empty( $iptc['2#120'][0] ) ) { // Description / legacy caption.
				$caption = trim( $iptc['2#120'][0] );

				mbstring_binary_safe_encoding();
				$caption_length = strlen( $caption );
				reset_mbstring_encoding();

				if ( empty( $meta['title'] ) && $caption_length < 80 ) {
					// Assume the title is stored in 2:120 if it's short.
					$meta['title'] = $caption;
				}

				$meta['caption'] = $caption;
			}

			if ( ! empty( $iptc['2#110'][0] ) ) { // Credit.
				$meta['credit'] = trim( $iptc['2#110'][0] );
			} elseif ( ! empty( $iptc['2#080'][0] ) ) { // Creator / legacy byline.
				$meta['credit'] = trim( $iptc['2#080'][0] );
			}

			if ( ! empty( $iptc['2#055'][0] ) && ! empty( $iptc['2#060'][0] ) ) { // Created date and time.
				$meta['created_timestamp'] = strtotime( $iptc['2#055'][0] . ' ' . $iptc['2#060'][0] );
			}

			if ( ! empty( $iptc['2#116'][0] ) ) { // Copyright.
				$meta['copyright'] = trim( $iptc['2#116'][0] );
			}

			if ( ! empty( $iptc['2#025'][0] ) ) { // Keywords array.
				$meta['keywords'] = array_values( $iptc['2#025'] );
			}
		}
	}

	$exif = array();

	/**
	 * Filters the image types to check for exif data.
	 *
	 * @since 2.5.0
	 *
	 * @param array $image_types Image types to check for exif data.
	 */
	$exif_image_types = apply_filters( 'wp_read_image_metadata_types', array( IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ) );

	if ( is_callable( 'exif_read_data' ) && in_array( $image_type, $exif_image_types, true ) ) {
		$exif = @exif_read_data( $file );

		if ( ! empty( $exif['ImageDescription'] ) ) {
			mbstring_binary_safe_encoding();
			$description_length = strlen( $exif['ImageDescription'] );
			reset_mbstring_encoding();

			if ( empty( $meta['title'] ) && $description_length < 80 ) {
				// Assume the title is stored in ImageDescription.
				$meta['title'] = trim( $exif['ImageDescription'] );
			}

			if ( empty( $meta['caption'] ) && ! empty( $exif['COMPUTED']['UserComment'] ) ) {
				$meta['caption'] = trim( $exif['COMPUTED']['UserComment'] );
			}

			if ( empty( $meta['caption'] ) ) {
				$meta['caption'] = trim( $exif['ImageDescription'] );
			}
		} elseif ( empty( $meta['caption'] ) && ! empty( $exif['Comments'] ) ) {
			$meta['caption'] = trim( $exif['Comments'] );
		}

		if ( empty( $meta['credit'] ) ) {
			if ( ! empty( $exif['Artist'] ) ) {
				$meta['credit'] = trim( $exif['Artist'] );
			} elseif ( ! empty( $exif['Author'] ) ) {
				$meta['credit'] = trim( $exif['Author'] );
			}
		}

		if ( empty( $meta['copyright'] ) && ! empty( $exif['Copyright'] ) ) {
			$meta['copyright'] = trim( $exif['Copyright'] );
		}
		if ( ! empty( $exif['FNumber'] ) ) {
			$meta['aperture'] = round( wp_exif_frac2dec( $exif['FNumber'] ), 2 );
		}
		if ( ! empty( $exif['Model'] ) ) {
			$meta['camera'] = trim( $exif['Model'] );
		}
		if ( empty( $meta['created_timestamp'] ) && ! empty( $exif['DateTimeDigitized'] ) ) {
			$meta['created_timestamp'] = wp_exif_date2ts( $exif['DateTimeDigitized'] );
		}
		if ( ! empty( $exif['FocalLength'] ) ) {
			$meta['focal_length'] = (string) wp_exif_frac2dec( $exif['FocalLength'] );
		}
		if ( ! empty( $exif['ISOSpeedRatings'] ) ) {
			$meta['iso'] = is_array( $exif['ISOSpeedRatings'] ) ? reset( $exif['ISOSpeedRatings'] ) : $exif['ISOSpeedRatings'];
			$meta['iso'] = trim( $meta['iso'] );
		}
		if ( ! empty( $exif['ExposureTime'] ) ) {
			$meta['shutter_speed'] = (string) wp_exif_frac2dec( $exif['ExposureTime'] );
		}
		if ( ! empty( $exif['Orientation'] ) ) {
			$meta['orientation'] = $exif['Orientation'];
		}
	}

	foreach ( array( 'title', 'caption', 'credit', 'copyright', 'camera', 'iso' ) as $key ) {
		if ( $meta[ $key ] && ! seems_utf8( $meta[ $key ] ) ) {
			$meta[ $key ] = utf8_encode( $meta[ $key ] );
		}
	}

	foreach ( $meta['keywords'] as $key => $keyword ) {
		if ( ! seems_utf8( $keyword ) ) {
			$meta['keywords'][ $key ] = utf8_encode( $keyword );
		}
	}

	$meta = wp_kses_post_deep( $meta );

	/**
	 * Filters the array of meta data read from an image's exif data.
	 *
	 * @since 2.5.0
	 * @since 4.4.0 The `$iptc` parameter was added.
	 * @since 5.0.0 The `$exif` parameter was added.
	 *
	 * @param array  $meta       Image meta data.
	 * @param string $file       Path to image file.
	 * @param int    $image_type Type of image, one of the `IMAGETYPE_XXX` constants.
	 * @param array  $iptc       IPTC data.
	 * @param array  $exif       EXIF data.
	 */
	return apply_filters( 'wp_read_image_metadata', $meta, $file, $image_type, $iptc, $exif );

}

/**
 * Validate that file is an image.
 *
 * @since 2.5.0
 *
 * @param string $path File path to test if valid image.
 * @return bool True if valid image, false if not valid image.
 */
function file_is_valid_image( $path ) {
	$size = @getimagesize( $path );
	return ! empty( $size );
}

/**
 * Validate that file is suitable for displaying within a web page.
 *
 * @since 2.5.0
 *
 * @param string $path File path to test.
 * @return bool True if suitable, false if not suitable.
 */
function file_is_displayable_image( $path ) {
	$displayable_image_types = array( IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP, IMAGETYPE_ICO );

	$info = @getimagesize( $path );
	if ( empty( $info ) ) {
		$result = false;
	} elseif ( ! in_array( $info[2], $displayable_image_types, true ) ) {
		$result = false;
	} else {
		$result = true;
	}

	/**
	 * Filters whether the current image is displayable in the browser.
	 *
	 * @since 2.5.0
	 *
	 * @param bool   $result Whether the image can be displayed. Default true.
	 * @param string $path   Path to the image.
	 */
	return apply_filters( 'file_is_displayable_image', $result, $path );
}

/**
 * Load an image resource for editing.
 *
 * @since 2.9.0
 *
 * @param string $attachment_id Attachment ID.
 * @param string $mime_type Image mime type.
 * @param string $size Optional. Image size, defaults to 'full'.
 * @return resource|false The resulting image resource on success, false on failure.
 */
function load_image_to_edit( $attachment_id, $mime_type, $size = 'full' ) {
	$filepath = _load_image_to_edit_path( $attachment_id, $size );
	if ( empty( $filepath ) ) {
		return false;
	}

	switch ( $mime_type ) {
		case 'image/jpeg':
			$image = imagecreatefromjpeg( $filepath );
			break;
		case 'image/png':
			$image = imagecreatefrompng( $filepath );
			break;
		case 'image/gif':
			$image = imagecreatefromgif( $filepath );
			break;
		default:
			$image = false;
			break;
	}
	if ( is_resource( $image ) ) {
		/**
		 * Filters the current image being loaded for editing.
		 *
		 * @since 2.9.0
		 *
		 * @param resource $image         Current image.
		 * @param string   $attachment_id Attachment ID.
		 * @param string   $size          Image size.
		 */
		$image = apply_filters( 'load_image_to_edit', $image, $attachment_id, $size );
		if ( function_exists( 'imagealphablending' ) && function_exists( 'imagesavealpha' ) ) {
			imagealphablending( $image, false );
			imagesavealpha( $image, true );
		}
	}
	return $image;
}

/**
 * Retrieve the path or url of an attachment's attached file.
 *
 * If the attached file is not present on the local filesystem (usually due to replication plugins),
 * then the url of the file is returned if url fopen is supported.
 *
 * @since 3.4.0
 * @access private
 *
 * @param string $attachment_id Attachment ID.
 * @param string $size Optional. Image size, defaults to 'full'.
 * @return string|false File path or url on success, false on failure.
 */
function _load_image_to_edit_path( $attachment_id, $size = 'full' ) {
	$filepath = get_attached_file( $attachment_id );

	if ( $filepath && file_exists( $filepath ) ) {
		if ( 'full' !== $size ) {
			$data = image_get_intermediate_size( $attachment_id, $size );

			if ( $data ) {
				$filepath = path_join( dirname( $filepath ), $data['file'] );

				/**
				 * Filters the path to the current image.
				 *
				 * The filter is evaluated for all image sizes except 'full'.
				 *
				 * @since 3.1.0
				 *
				 * @param string $path          Path to the current image.
				 * @param string $attachment_id Attachment ID.
				 * @param string $size          Size of the image.
				 */
				$filepath = apply_filters( 'load_image_to_edit_filesystempath', $filepath, $attachment_id, $size );
			}
		}
	} elseif ( function_exists( 'fopen' ) && ini_get( 'allow_url_fopen' ) ) {
		/**
		 * Filters the image URL if not in the local filesystem.
		 *
		 * The filter is only evaluated if fopen is enabled on the server.
		 *
		 * @since 3.1.0
		 *
		 * @param string $image_url     Current image URL.
		 * @param string $attachment_id Attachment ID.
		 * @param string $size          Size of the image.
		 */
		$filepath = apply_filters( 'load_image_to_edit_attachmenturl', wp_get_attachment_url( $attachment_id ), $attachment_id, $size );
	}

	/**
	 * Filters the returned path or URL of the current image.
	 *
	 * @since 2.9.0
	 *
	 * @param string|bool $filepath      File path or URL to current image, or false.
	 * @param string      $attachment_id Attachment ID.
	 * @param string      $size          Size of the image.
	 */
	return apply_filters( 'load_image_to_edit_path', $filepath, $attachment_id, $size );
}

/**
 * Copy an existing image file.
 *
 * @since 3.4.0
 * @access private
 *
 * @param string $attachment_id Attachment ID.
 * @return string|false New file path on success, false on failure.
 */
function _copy_image_file( $attachment_id ) {
	$dst_file = get_attached_file( $attachment_id );
	$src_file = $dst_file;

	if ( ! file_exists( $src_file ) ) {
		$src_file = _load_image_to_edit_path( $attachment_id );
	}

	if ( $src_file ) {
		$dst_file = str_replace( wp_basename( $dst_file ), 'copy-' . wp_basename( $dst_file ), $dst_file );
		$dst_file = dirname( $dst_file ) . '/' . wp_unique_filename( dirname( $dst_file ), wp_basename( $dst_file ) );

		/*
		 * The directory containing the original file may no longer
		 * exist when using a replication plugin.
		 */
		wp_mkdir_p( dirname( $dst_file ) );

		if ( ! copy( $src_file, $dst_file ) ) {
			$dst_file = false;
		}
	} else {
		$dst_file = false;
	}

	return $dst_file;
}
