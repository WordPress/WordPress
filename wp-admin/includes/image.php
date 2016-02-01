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
	if ( is_numeric( $src ) ) { // Handle int as attachment ID
		$src_file = get_attached_file( $src );

		if ( ! file_exists( $src_file ) ) {
			// If the file doesn't exist, attempt a url fopen on the src link.
			// This can occur with certain file replication plugins.
			$src = _load_image_to_edit_path( $src, 'full' );
		} else {
			$src = $src_file;
		}
	}

	$editor = wp_get_image_editor( $src );
	if ( is_wp_error( $editor ) )
		return $editor;

	$src = $editor->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs );
	if ( is_wp_error( $src ) )
		return $src;

	if ( ! $dst_file )
		$dst_file = str_replace( basename( $src_file ), 'cropped-' . basename( $src_file ), $src_file );

	/*
	 * The directory containing the original file may no longer exist when
	 * using a replication plugin.
	 */
	wp_mkdir_p( dirname( $dst_file ) );

	$dst_file = dirname( $dst_file ) . '/' . wp_unique_filename( dirname( $dst_file ), basename( $dst_file ) );

	$result = $editor->save( $dst_file );
	if ( is_wp_error( $result ) )
		return $result;

	return $dst_file;
}

/**
 * Generate post thumbnail attachment meta data.
 *
 * @since 2.1.0
 *
 * @global array $_wp_additional_image_sizes
 *
 * @param int $attachment_id Attachment Id to process.
 * @param string $file Filepath of the Attached image.
 * @return mixed Metadata for attachment.
 */
function wp_generate_attachment_metadata( $attachment_id, $file ) {
	$attachment = get_post( $attachment_id );

	$metadata = array();
	$support = false;
	if ( preg_match('!^image/!', get_post_mime_type( $attachment )) && file_is_displayable_image($file) ) {
		$imagesize = getimagesize( $file );
		$metadata['width'] = $imagesize[0];
		$metadata['height'] = $imagesize[1];

		// Make the file path relative to the upload dir.
		$metadata['file'] = _wp_relative_upload_path($file);

		// Make thumbnails and other intermediate sizes.
		global $_wp_additional_image_sizes;

		$sizes = array();
		foreach ( get_intermediate_image_sizes() as $s ) {
			$sizes[$s] = array( 'width' => '', 'height' => '', 'crop' => false );
			if ( isset( $_wp_additional_image_sizes[$s]['width'] ) )
				$sizes[$s]['width'] = intval( $_wp_additional_image_sizes[$s]['width'] ); // For theme-added sizes
			else
				$sizes[$s]['width'] = get_option( "{$s}_size_w" ); // For default sizes set in options
			if ( isset( $_wp_additional_image_sizes[$s]['height'] ) )
				$sizes[$s]['height'] = intval( $_wp_additional_image_sizes[$s]['height'] ); // For theme-added sizes
			else
				$sizes[$s]['height'] = get_option( "{$s}_size_h" ); // For default sizes set in options
			if ( isset( $_wp_additional_image_sizes[$s]['crop'] ) )
				$sizes[$s]['crop'] = $_wp_additional_image_sizes[$s]['crop']; // For theme-added sizes
			else
				$sizes[$s]['crop'] = get_option( "{$s}_crop" ); // For default sizes set in options
		}

		/**
		 * Filter the image sizes automatically generated when uploading an image.
		 *
		 * @since 2.9.0
		 * @since 4.4.0 Added the `$metadata` argument.
		 *
		 * @param array $sizes    An associative array of image sizes.
		 * @param array $metadata An associative array of image metadata: width, height, file.
		 */
		$sizes = apply_filters( 'intermediate_image_sizes_advanced', $sizes, $metadata );

		if ( $sizes ) {
			$editor = wp_get_image_editor( $file );

			if ( ! is_wp_error( $editor ) )
				$metadata['sizes'] = $editor->multi_resize( $sizes );
		} else {
			$metadata['sizes'] = array();
		}

		// Fetch additional metadata from EXIF/IPTC.
		$image_meta = wp_read_image_metadata( $file );
		if ( $image_meta )
			$metadata['image_meta'] = $image_meta;

	} elseif ( wp_attachment_is( 'video', $attachment ) ) {
		$metadata = wp_read_video_metadata( $file );
		$support = current_theme_supports( 'post-thumbnails', 'attachment:video' ) || post_type_supports( 'attachment:video', 'thumbnail' );
	} elseif ( wp_attachment_is( 'audio', $attachment ) ) {
		$metadata = wp_read_audio_metadata( $file );
		$support = current_theme_supports( 'post-thumbnails', 'attachment:audio' ) || post_type_supports( 'attachment:audio', 'thumbnail' );
	}

	if ( $support && ! empty( $metadata['image']['data'] ) ) {
		// Check for existing cover.
		$hash = md5( $metadata['image']['data'] );
		$posts = get_posts( array(
			'fields' => 'ids',
			'post_type' => 'attachment',
			'post_mime_type' => $metadata['image']['mime'],
			'post_status' => 'inherit',
			'posts_per_page' => 1,
			'meta_key' => '_cover_hash',
			'meta_value' => $hash
		) );
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
			$basename = str_replace( '.', '-', basename( $file ) ) . '-image' . $ext;
			$uploaded = wp_upload_bits( $basename, '', $metadata['image']['data'] );
			if ( false === $uploaded['error'] ) {
				$image_attachment = array(
					'post_mime_type' => $metadata['image']['mime'],
					'post_type' => 'attachment',
					'post_content' => '',
				);
				/**
				 * Filter the parameters for the attachment thumbnail creation.
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
	}

	// Remove the blob of binary data from the array.
	if ( $metadata ) {
		unset( $metadata['image']['data'] );
	}

	/**
	 * Filter the generated attachment meta data.
	 *
	 * @since 2.1.0
	 *
	 * @param array $metadata      An array of attachment meta data.
	 * @param int   $attachment_id Current attachment ID.
	 */
	return apply_filters( 'wp_generate_attachment_metadata', $metadata, $attachment_id );
}

/**
 * Convert a fraction string to a decimal.
 *
 * @since 2.5.0
 *
 * @param string $str
 * @return int|float
 */
function wp_exif_frac2dec($str) {
	@list( $n, $d ) = explode( '/', $str );
	if ( !empty($d) )
		return $n / $d;
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
function wp_exif_date2ts($str) {
	@list( $date, $time ) = explode( ' ', trim($str) );
	@list( $y, $m, $d ) = explode( ':', $date );

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
	if ( ! file_exists( $file ) )
		return false;

	list( , , $sourceImageType ) = getimagesize( $file );

	/*
	 * EXIF contains a bunch of data we'll probably never need formatted in ways
	 * that are difficult to use. We'll normalize it and just extract the fields
	 * that are likely to be useful. Fractions and numbers are converted to
	 * floats, dates to unix timestamps, and everything else to strings.
	 */
	$meta = array(
		'aperture' => 0,
		'credit' => '',
		'camera' => '',
		'caption' => '',
		'created_timestamp' => 0,
		'copyright' => '',
		'focal_length' => 0,
		'iso' => 0,
		'shutter_speed' => 0,
		'title' => '',
		'orientation' => 0,
		'keywords' => array(),
	);

	$iptc = array();
	/*
	 * Read IPTC first, since it might contain data not available in exif such
	 * as caption, description etc.
	 */
	if ( is_callable( 'iptcparse' ) ) {
		getimagesize( $file, $info );

		if ( ! empty( $info['APP13'] ) ) {
			$iptc = iptcparse( $info['APP13'] );

			// Headline, "A brief synopsis of the caption."
			if ( ! empty( $iptc['2#105'][0] ) ) {
				$meta['title'] = trim( $iptc['2#105'][0] );
			/*
			 * Title, "Many use the Title field to store the filename of the image,
			 * though the field may be used in many ways."
			 */
			} elseif ( ! empty( $iptc['2#005'][0] ) ) {
				$meta['title'] = trim( $iptc['2#005'][0] );
			}

			if ( ! empty( $iptc['2#120'][0] ) ) { // description / legacy caption
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

			if ( ! empty( $iptc['2#110'][0] ) ) // credit
				$meta['credit'] = trim( $iptc['2#110'][0] );
			elseif ( ! empty( $iptc['2#080'][0] ) ) // creator / legacy byline
				$meta['credit'] = trim( $iptc['2#080'][0] );

			if ( ! empty( $iptc['2#055'][0] ) && ! empty( $iptc['2#060'][0] ) ) // created date and time
				$meta['created_timestamp'] = strtotime( $iptc['2#055'][0] . ' ' . $iptc['2#060'][0] );

			if ( ! empty( $iptc['2#116'][0] ) ) // copyright
				$meta['copyright'] = trim( $iptc['2#116'][0] );

			if ( ! empty( $iptc['2#025'][0] ) ) { // keywords array
				$meta['keywords'] = array_values( $iptc['2#025'] );
			}
		 }
	}

	/**
	 * Filter the image types to check for exif data.
	 *
	 * @since 2.5.0
	 *
	 * @param array $image_types Image types to check for exif data.
	 */
	if ( is_callable( 'exif_read_data' ) && in_array( $sourceImageType, apply_filters( 'wp_read_image_metadata_types', array( IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ) ) ) ) {
		$exif = @exif_read_data( $file );

		if ( ! empty( $exif['ImageDescription'] ) ) {
			mbstring_binary_safe_encoding();
			$description_length = strlen( $exif['ImageDescription'] );
			reset_mbstring_encoding();

			if ( empty( $meta['title'] ) && $description_length < 80 ) {
				// Assume the title is stored in ImageDescription
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
			} elseif ( ! empty($exif['Author'] ) ) {
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
	 * Filter the array of meta data read from an image's exif data.
	 *
	 * @since 2.5.0
	 * @since 4.4.0 The `$iptc` parameter was added.
	 *
	 * @param array  $meta            Image meta data.
	 * @param string $file            Path to image file.
	 * @param int    $sourceImageType Type of image.
	 * @param array  $iptc            IPTC data.
	 */
	return apply_filters( 'wp_read_image_metadata', $meta, $file, $sourceImageType, $iptc );

}

/**
 * Validate that file is an image.
 *
 * @since 2.5.0
 *
 * @param string $path File path to test if valid image.
 * @return bool True if valid image, false if not valid image.
 */
function file_is_valid_image($path) {
	$size = @getimagesize($path);
	return !empty($size);
}

/**
 * Validate that file is suitable for displaying within a web page.
 *
 * @since 2.5.0
 *
 * @param string $path File path to test.
 * @return bool True if suitable, false if not suitable.
 */
function file_is_displayable_image($path) {
	$displayable_image_types = array( IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP );

	$info = @getimagesize( $path );
	if ( empty( $info ) ) {
		$result = false;
	} elseif ( ! in_array( $info[2], $displayable_image_types ) ) {
		$result = false;
	} else {
		$result = true;
	}

	/**
	 * Filter whether the current image is displayable in the browser.
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
	if ( empty( $filepath ) )
		return false;

	switch ( $mime_type ) {
		case 'image/jpeg':
			$image = imagecreatefromjpeg($filepath);
			break;
		case 'image/png':
			$image = imagecreatefrompng($filepath);
			break;
		case 'image/gif':
			$image = imagecreatefromgif($filepath);
			break;
		default:
			$image = false;
			break;
	}
	if ( is_resource($image) ) {
		/**
		 * Filter the current image being loaded for editing.
		 *
		 * @since 2.9.0
		 *
		 * @param resource $image         Current image.
		 * @param string   $attachment_id Attachment ID.
		 * @param string   $size          Image size.
		 */
		$image = apply_filters( 'load_image_to_edit', $image, $attachment_id, $size );
		if ( function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
			imagealphablending($image, false);
			imagesavealpha($image, true);
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
		if ( 'full' != $size && ( $data = image_get_intermediate_size( $attachment_id, $size ) ) ) {
			/**
			 * Filter the path to the current image.
			 *
			 * The filter is evaluated for all image sizes except 'full'.
			 *
			 * @since 3.1.0
			 *
			 * @param string $path          Path to the current image.
			 * @param string $attachment_id Attachment ID.
			 * @param string $size          Size of the image.
			 */
			$filepath = apply_filters( 'load_image_to_edit_filesystempath', path_join( dirname( $filepath ), $data['file'] ), $attachment_id, $size );
		}
	} elseif ( function_exists( 'fopen' ) && function_exists( 'ini_get' ) && true == ini_get( 'allow_url_fopen' ) ) {
		/**
		 * Filter the image URL if not in the local filesystem.
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
	 * Filter the returned path or URL of the current image.
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
	$dst_file = $src_file = get_attached_file( $attachment_id );
	if ( ! file_exists( $src_file ) )
		$src_file = _load_image_to_edit_path( $attachment_id );

	if ( $src_file ) {
		$dst_file = str_replace( basename( $dst_file ), 'copy-' . basename( $dst_file ), $dst_file );
		$dst_file = dirname( $dst_file ) . '/' . wp_unique_filename( dirname( $dst_file ), basename( $dst_file ) );

		/*
		 * The directory containing the original file may no longer
		 * exist when using a replication plugin.
		 */
		wp_mkdir_p( dirname( $dst_file ) );

		if ( ! @copy( $src_file, $dst_file ) )
			$dst_file = false;
	} else {
		$dst_file = false;
	}

	return $dst_file;
}
