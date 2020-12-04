<?php
/**
 * WordPress GD Image Editor
 *
 * @package WordPress
 * @subpackage Image_Editor
 */

/**
 * WordPress Image Editor Class for Image Manipulation through GD
 *
 * @since 3.5.0
 *
 * @see WP_Image_Editor
 */
class WP_Image_Editor_GD extends WP_Image_Editor {
	/**
	 * GD Resource.
	 *
	 * @var resource|GdImage
	 */
	protected $image;

	public function __destruct() {
		if ( $this->image ) {
			// We don't need the original in memory anymore.
			imagedestroy( $this->image );
		}
	}

	/**
	 * Checks to see if current environment supports GD.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args
	 * @return bool
	 */
	public static function test( $args = array() ) {
		if ( ! extension_loaded( 'gd' ) || ! function_exists( 'gd_info' ) ) {
			return false;
		}

		// On some setups GD library does not provide imagerotate() - Ticket #11536.
		if ( isset( $args['methods'] ) &&
			in_array( 'rotate', $args['methods'], true ) &&
			! function_exists( 'imagerotate' ) ) {

				return false;
		}

		return true;
	}

	/**
	 * Checks to see if editor supports the mime-type specified.
	 *
	 * @since 3.5.0
	 *
	 * @param string $mime_type
	 * @return bool
	 */
	public static function supports_mime_type( $mime_type ) {
		$image_types = imagetypes();
		switch ( $mime_type ) {
			case 'image/jpeg':
				return ( $image_types & IMG_JPG ) != 0;
			case 'image/png':
				return ( $image_types & IMG_PNG ) != 0;
			case 'image/gif':
				return ( $image_types & IMG_GIF ) != 0;
		}

		return false;
	}

	/**
	 * Loads image from $this->file into new GD Resource.
	 *
	 * @since 3.5.0
	 *
	 * @return bool|WP_Error True if loaded successfully; WP_Error on failure.
	 */
	public function load() {
		if ( $this->image ) {
			return true;
		}

		if ( ! is_file( $this->file ) && ! preg_match( '|^https?://|', $this->file ) ) {
			return new WP_Error( 'error_loading_image', __( 'File doesn&#8217;t exist?' ), $this->file );
		}

		// Set artificially high because GD uses uncompressed images in memory.
		wp_raise_memory_limit( 'image' );

		$file_contents = @file_get_contents( $this->file );

		if ( ! $file_contents ) {
			return new WP_Error( 'error_loading_image', __( 'File doesn&#8217;t exist?' ), $this->file );
		}

		$this->image = @imagecreatefromstring( $file_contents );

		if ( ! is_gd_image( $this->image ) ) {
			return new WP_Error( 'invalid_image', __( 'File is not an image.' ), $this->file );
		}

		$size = @getimagesize( $this->file );

		if ( ! $size ) {
			return new WP_Error( 'invalid_image', __( 'Could not read image size.' ), $this->file );
		}

		if ( function_exists( 'imagealphablending' ) && function_exists( 'imagesavealpha' ) ) {
			imagealphablending( $this->image, false );
			imagesavealpha( $this->image, true );
		}

		$this->update_size( $size[0], $size[1] );
		$this->mime_type = $size['mime'];

		return $this->set_quality();
	}

	/**
	 * Sets or updates current image size.
	 *
	 * @since 3.5.0
	 *
	 * @param int $width
	 * @param int $height
	 * @return true
	 */
	protected function update_size( $width = false, $height = false ) {
		if ( ! $width ) {
			$width = imagesx( $this->image );
		}

		if ( ! $height ) {
			$height = imagesy( $this->image );
		}

		return parent::update_size( $width, $height );
	}

	/**
	 * Resizes current image.
	 *
	 * Wraps `::_resize()` which returns a GD resource or GdImage instance.
	 *
	 * At minimum, either a height or width must be provided. If one of the two is set
	 * to null, the resize will maintain aspect ratio according to the provided dimension.
	 *
	 * @since 3.5.0
	 *
	 * @param int|null $max_w Image width.
	 * @param int|null $max_h Image height.
	 * @param bool     $crop
	 * @return true|WP_Error
	 */
	public function resize( $max_w, $max_h, $crop = false ) {
		if ( ( $this->size['width'] == $max_w ) && ( $this->size['height'] == $max_h ) ) {
			return true;
		}

		$resized = $this->_resize( $max_w, $max_h, $crop );

		if ( is_gd_image( $resized ) ) {
			imagedestroy( $this->image );
			$this->image = $resized;
			return true;

		} elseif ( is_wp_error( $resized ) ) {
			return $resized;
		}

		return new WP_Error( 'image_resize_error', __( 'Image resize failed.' ), $this->file );
	}

	/**
	 * @param int        $max_w
	 * @param int        $max_h
	 * @param bool|array $crop
	 * @return resource|GdImage|WP_Error
	 */
	protected function _resize( $max_w, $max_h, $crop = false ) {
		$dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );

		if ( ! $dims ) {
			return new WP_Error( 'error_getting_dimensions', __( 'Could not calculate resized image dimensions' ), $this->file );
		}

		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

		$resized = wp_imagecreatetruecolor( $dst_w, $dst_h );
		imagecopyresampled( $resized, $this->image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

		if ( is_gd_image( $resized ) ) {
			$this->update_size( $dst_w, $dst_h );
			return $resized;
		}

		return new WP_Error( 'image_resize_error', __( 'Image resize failed.' ), $this->file );
	}

	/**
	 * Create multiple smaller images from a single source.
	 *
	 * Attempts to create all sub-sizes and returns the meta data at the end. This
	 * may result in the server running out of resources. When it fails there may be few
	 * "orphaned" images left over as the meta data is never returned and saved.
	 *
	 * As of 5.3.0 the preferred way to do this is with `make_subsize()`. It creates
	 * the new images one at a time and allows for the meta data to be saved after
	 * each new image is created.
	 *
	 * @since 3.5.0
	 *
	 * @param array $sizes {
	 *     An array of image size data arrays.
	 *
	 *     Either a height or width must be provided.
	 *     If one of the two is set to null, the resize will
	 *     maintain aspect ratio according to the source image.
	 *
	 *     @type array $size {
	 *         Array of height, width values, and whether to crop.
	 *
	 *         @type int  $width  Image width. Optional if `$height` is specified.
	 *         @type int  $height Image height. Optional if `$width` is specified.
	 *         @type bool $crop   Optional. Whether to crop the image. Default false.
	 *     }
	 * }
	 * @return array An array of resized images' metadata by size.
	 */
	public function multi_resize( $sizes ) {
		$metadata = array();

		foreach ( $sizes as $size => $size_data ) {
			$meta = $this->make_subsize( $size_data );

			if ( ! is_wp_error( $meta ) ) {
				$metadata[ $size ] = $meta;
			}
		}

		return $metadata;
	}

	/**
	 * Create an image sub-size and return the image meta data value for it.
	 *
	 * @since 5.3.0
	 *
	 * @param array $size_data {
	 *     Array of size data.
	 *
	 *     @type int  $width  The maximum width in pixels.
	 *     @type int  $height The maximum height in pixels.
	 *     @type bool $crop   Whether to crop the image to exact dimensions.
	 * }
	 * @return array|WP_Error The image data array for inclusion in the `sizes` array in the image meta,
	 *                        WP_Error object on error.
	 */
	public function make_subsize( $size_data ) {
		if ( ! isset( $size_data['width'] ) && ! isset( $size_data['height'] ) ) {
			return new WP_Error( 'image_subsize_create_error', __( 'Cannot resize the image. Both width and height are not set.' ) );
		}

		$orig_size = $this->size;

		if ( ! isset( $size_data['width'] ) ) {
			$size_data['width'] = null;
		}

		if ( ! isset( $size_data['height'] ) ) {
			$size_data['height'] = null;
		}

		if ( ! isset( $size_data['crop'] ) ) {
			$size_data['crop'] = false;
		}

		$resized = $this->_resize( $size_data['width'], $size_data['height'], $size_data['crop'] );

		if ( is_wp_error( $resized ) ) {
			$saved = $resized;
		} else {
			$saved = $this->_save( $resized );
			imagedestroy( $resized );
		}

		$this->size = $orig_size;

		if ( ! is_wp_error( $saved ) ) {
			unset( $saved['path'] );
		}

		return $saved;
	}

	/**
	 * Crops Image.
	 *
	 * @since 3.5.0
	 *
	 * @param int  $src_x   The start x position to crop from.
	 * @param int  $src_y   The start y position to crop from.
	 * @param int  $src_w   The width to crop.
	 * @param int  $src_h   The height to crop.
	 * @param int  $dst_w   Optional. The destination width.
	 * @param int  $dst_h   Optional. The destination height.
	 * @param bool $src_abs Optional. If the source crop points are absolute.
	 * @return bool|WP_Error
	 */
	public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
		// If destination width/height isn't specified,
		// use same as width/height from source.
		if ( ! $dst_w ) {
			$dst_w = $src_w;
		}
		if ( ! $dst_h ) {
			$dst_h = $src_h;
		}

		foreach ( array( $src_w, $src_h, $dst_w, $dst_h ) as $value ) {
			if ( ! is_numeric( $value ) || (int) $value <= 0 ) {
				return new WP_Error( 'image_crop_error', __( 'Image crop failed.' ), $this->file );
			}
		}

		$dst = wp_imagecreatetruecolor( (int) $dst_w, (int) $dst_h );

		if ( $src_abs ) {
			$src_w -= $src_x;
			$src_h -= $src_y;
		}

		if ( function_exists( 'imageantialias' ) ) {
			imageantialias( $dst, true );
		}

		imagecopyresampled( $dst, $this->image, 0, 0, (int) $src_x, (int) $src_y, (int) $dst_w, (int) $dst_h, (int) $src_w, (int) $src_h );

		if ( is_gd_image( $dst ) ) {
			imagedestroy( $this->image );
			$this->image = $dst;
			$this->update_size();
			return true;
		}

		return new WP_Error( 'image_crop_error', __( 'Image crop failed.' ), $this->file );
	}

	/**
	 * Rotates current image counter-clockwise by $angle.
	 * Ported from image-edit.php
	 *
	 * @since 3.5.0
	 *
	 * @param float $angle
	 * @return true|WP_Error
	 */
	public function rotate( $angle ) {
		if ( function_exists( 'imagerotate' ) ) {
			$transparency = imagecolorallocatealpha( $this->image, 255, 255, 255, 127 );
			$rotated      = imagerotate( $this->image, $angle, $transparency );

			if ( is_gd_image( $rotated ) ) {
				imagealphablending( $rotated, true );
				imagesavealpha( $rotated, true );
				imagedestroy( $this->image );
				$this->image = $rotated;
				$this->update_size();
				return true;
			}
		}

		return new WP_Error( 'image_rotate_error', __( 'Image rotate failed.' ), $this->file );
	}

	/**
	 * Flips current image.
	 *
	 * @since 3.5.0
	 *
	 * @param bool $horz Flip along Horizontal Axis.
	 * @param bool $vert Flip along Vertical Axis.
	 * @return true|WP_Error
	 */
	public function flip( $horz, $vert ) {
		$w   = $this->size['width'];
		$h   = $this->size['height'];
		$dst = wp_imagecreatetruecolor( $w, $h );

		if ( is_gd_image( $dst ) ) {
			$sx = $vert ? ( $w - 1 ) : 0;
			$sy = $horz ? ( $h - 1 ) : 0;
			$sw = $vert ? -$w : $w;
			$sh = $horz ? -$h : $h;

			if ( imagecopyresampled( $dst, $this->image, 0, 0, $sx, $sy, $w, $h, $sw, $sh ) ) {
				imagedestroy( $this->image );
				$this->image = $dst;
				return true;
			}
		}

		return new WP_Error( 'image_flip_error', __( 'Image flip failed.' ), $this->file );
	}

	/**
	 * Saves current in-memory image to file.
	 *
	 * @since 3.5.0
	 *
	 * @param string|null $filename
	 * @param string|null $mime_type
	 * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
	 */
	public function save( $filename = null, $mime_type = null ) {
		$saved = $this->_save( $this->image, $filename, $mime_type );

		if ( ! is_wp_error( $saved ) ) {
			$this->file      = $saved['path'];
			$this->mime_type = $saved['mime-type'];
		}

		return $saved;
	}

	/**
	 * @param resource|GdImage $image
	 * @param string|null      $filename
	 * @param string|null      $mime_type
	 * @return array|WP_Error
	 */
	protected function _save( $image, $filename = null, $mime_type = null ) {
		list( $filename, $extension, $mime_type ) = $this->get_output_format( $filename, $mime_type );

		if ( ! $filename ) {
			$filename = $this->generate_filename( null, null, $extension );
		}

		if ( 'image/gif' === $mime_type ) {
			if ( ! $this->make_image( $filename, 'imagegif', array( $image, $filename ) ) ) {
				return new WP_Error( 'image_save_error', __( 'Image Editor Save Failed' ) );
			}
		} elseif ( 'image/png' === $mime_type ) {
			// Convert from full colors to index colors, like original PNG.
			if ( function_exists( 'imageistruecolor' ) && ! imageistruecolor( $image ) ) {
				imagetruecolortopalette( $image, false, imagecolorstotal( $image ) );
			}

			if ( ! $this->make_image( $filename, 'imagepng', array( $image, $filename ) ) ) {
				return new WP_Error( 'image_save_error', __( 'Image Editor Save Failed' ) );
			}
		} elseif ( 'image/jpeg' === $mime_type ) {
			if ( ! $this->make_image( $filename, 'imagejpeg', array( $image, $filename, $this->get_quality() ) ) ) {
				return new WP_Error( 'image_save_error', __( 'Image Editor Save Failed' ) );
			}
		} else {
			return new WP_Error( 'image_save_error', __( 'Image Editor Save Failed' ) );
		}

		// Set correct file permissions.
		$stat  = stat( dirname( $filename ) );
		$perms = $stat['mode'] & 0000666; // Same permissions as parent folder, strip off the executable bits.
		chmod( $filename, $perms );

		return array(
			'path'      => $filename,
			/**
			 * Filters the name of the saved image file.
			 *
			 * @since 2.6.0
			 *
			 * @param string $filename Name of the file.
			 */
			'file'      => wp_basename( apply_filters( 'image_make_intermediate_size', $filename ) ),
			'width'     => $this->size['width'],
			'height'    => $this->size['height'],
			'mime-type' => $mime_type,
		);
	}

	/**
	 * Returns stream of current image.
	 *
	 * @since 3.5.0
	 *
	 * @param string $mime_type The mime type of the image.
	 * @return bool True on success, false on failure.
	 */
	public function stream( $mime_type = null ) {
		list( $filename, $extension, $mime_type ) = $this->get_output_format( null, $mime_type );

		switch ( $mime_type ) {
			case 'image/png':
				header( 'Content-Type: image/png' );
				return imagepng( $this->image );
			case 'image/gif':
				header( 'Content-Type: image/gif' );
				return imagegif( $this->image );
			default:
				header( 'Content-Type: image/jpeg' );
				return imagejpeg( $this->image, null, $this->get_quality() );
		}
	}

	/**
	 * Either calls editor's save function or handles file as a stream.
	 *
	 * @since 3.5.0
	 *
	 * @param string|stream $filename
	 * @param callable      $function
	 * @param array         $arguments
	 * @return bool
	 */
	protected function make_image( $filename, $function, $arguments ) {
		if ( wp_is_stream( $filename ) ) {
			$arguments[1] = null;
		}

		return parent::make_image( $filename, $function, $arguments );
	}
}
