<?php
/**
 * Base WordPress Image Editor
 *
 * @package WordPress
 * @subpackage Image_Editor
 */

/**
 * Base image editor class from which implementations extend
 *
 * @since 3.5.0
 */
abstract class WP_Image_Editor {
	protected $file              = null;
	protected $size              = null;
	protected $mime_type         = null;
	protected $default_mime_type = 'image/jpeg';
	protected $quality           = false;
	protected $default_quality   = 82;

	/**
	 * Each instance handles a single file.
	 *
	 * @param string $file Path to the file to load.
	 */
	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Checks to see if current environment supports the editor chosen.
	 * Must be overridden in a sub-class.
	 *
	 * @since 3.5.0
	 *
	 * @static
	 * @abstract
	 *
	 * @param array $args
	 * @return bool
	 */
	public static function test( $args = array() ) {
		return false;
	}

	/**
	 * Checks to see if editor supports the mime-type specified.
	 * Must be overridden in a sub-class.
	 *
	 * @since 3.5.0
	 *
	 * @static
	 * @abstract
	 *
	 * @param string $mime_type
	 * @return bool
	 */
	public static function supports_mime_type( $mime_type ) {
		return false;
	}

	/**
	 * Loads image from $this->file into editor.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @return bool|WP_Error True if loaded; WP_Error on failure.
	 */
	abstract public function load();

	/**
	 * Saves current image to file.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param string $destfilename
	 * @param string $mime_type
	 * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
	 */
	abstract public function save( $destfilename = null, $mime_type = null );

	/**
	 * Resizes current image.
	 *
	 * At minimum, either a height or width must be provided.
	 * If one of the two is set to null, the resize will
	 * maintain aspect ratio according to the provided dimension.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param  int|null $max_w Image width.
	 * @param  int|null $max_h Image height.
	 * @param  bool     $crop
	 * @return bool|WP_Error
	 */
	abstract public function resize( $max_w, $max_h, $crop = false );

	/**
	 * Resize multiple images from a single source.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param array $sizes {
	 *     An array of image size arrays. Default sizes are 'small', 'medium', 'large'.
	 *
	 *     @type array $size {
	 *         @type int  $width  Image width.
	 *         @type int  $height Image height.
	 *         @type bool $crop   Optional. Whether to crop the image. Default false.
	 *     }
	 * }
	 * @return array An array of resized images metadata by size.
	 */
	abstract public function multi_resize( $sizes );

	/**
	 * Crops Image.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param int $src_x The start x position to crop from.
	 * @param int $src_y The start y position to crop from.
	 * @param int $src_w The width to crop.
	 * @param int $src_h The height to crop.
	 * @param int $dst_w Optional. The destination width.
	 * @param int $dst_h Optional. The destination height.
	 * @param bool $src_abs Optional. If the source crop points are absolute.
	 * @return bool|WP_Error
	 */
	abstract public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false );

	/**
	 * Rotates current image counter-clockwise by $angle.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param float $angle
	 * @return bool|WP_Error
	 */
	abstract public function rotate( $angle );

	/**
	 * Flips current image.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param bool $horz Flip along Horizontal Axis
	 * @param bool $vert Flip along Vertical Axis
	 * @return bool|WP_Error
	 */
	abstract public function flip( $horz, $vert );

	/**
	 * Streams current image to browser.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param string $mime_type The mime type of the image.
	 * @return bool|WP_Error True on success, WP_Error object or false on failure.
	 */
	abstract public function stream( $mime_type = null );

	/**
	 * Gets dimensions of image.
	 *
	 * @since 3.5.0
	 *
	 * @return array {'width'=>int, 'height'=>int}
	 */
	public function get_size() {
		return $this->size;
	}

	/**
	 * Sets current image size.
	 *
	 * @since 3.5.0
	 *
	 * @param int $width
	 * @param int $height
	 * @return true
	 */
	protected function update_size( $width = null, $height = null ) {
		$this->size = array(
			'width'  => (int) $width,
			'height' => (int) $height,
		);
		return true;
	}

	/**
	 * Gets the Image Compression quality on a 1-100% scale.
	 *
	 * @since 4.0.0
	 *
	 * @return int $quality Compression Quality. Range: [1,100]
	 */
	public function get_quality() {
		if ( ! $this->quality ) {
			$this->set_quality();
		}

		return $this->quality;
	}

	/**
	 * Sets Image Compression quality on a 1-100% scale.
	 *
	 * @since 3.5.0
	 *
	 * @param int $quality Compression Quality. Range: [1,100]
	 * @return true|WP_Error True if set successfully; WP_Error on failure.
	 */
	public function set_quality( $quality = null ) {
		if ( null === $quality ) {
			/**
			 * Filters the default image compression quality setting.
			 *
			 * Applies only during initial editor instantiation, or when set_quality() is run
			 * manually without the `$quality` argument.
			 *
			 * set_quality() has priority over the filter.
			 *
			 * @since 3.5.0
			 *
			 * @param int    $quality   Quality level between 1 (low) and 100 (high).
			 * @param string $mime_type Image mime type.
			 */
			$quality = apply_filters( 'wp_editor_set_quality', $this->default_quality, $this->mime_type );

			if ( 'image/jpeg' == $this->mime_type ) {
				/**
				 * Filters the JPEG compression quality for backward-compatibility.
				 *
				 * Applies only during initial editor instantiation, or when set_quality() is run
				 * manually without the `$quality` argument.
				 *
				 * set_quality() has priority over the filter.
				 *
				 * The filter is evaluated under two contexts: 'image_resize', and 'edit_image',
				 * (when a JPEG image is saved to file).
				 *
				 * @since 2.5.0
				 *
				 * @param int    $quality Quality level between 0 (low) and 100 (high) of the JPEG.
				 * @param string $context Context of the filter.
				 */
				$quality = apply_filters( 'jpeg_quality', $quality, 'image_resize' );
			}

			if ( $quality < 0 || $quality > 100 ) {
				$quality = $this->default_quality;
			}
		}

		// Allow 0, but squash to 1 due to identical images in GD, and for backward compatibility.
		if ( 0 === $quality ) {
			$quality = 1;
		}

		if ( ( $quality >= 1 ) && ( $quality <= 100 ) ) {
			$this->quality = $quality;
			return true;
		} else {
			return new WP_Error( 'invalid_image_quality', __( 'Attempted to set image quality outside of the range [1,100].' ) );
		}
	}

	/**
	 * Returns preferred mime-type and extension based on provided
	 * file's extension and mime, or current file's extension and mime.
	 *
	 * Will default to $this->default_mime_type if requested is not supported.
	 *
	 * Provides corrected filename only if filename is provided.
	 *
	 * @since 3.5.0
	 *
	 * @param string $filename
	 * @param string $mime_type
	 * @return array { filename|null, extension, mime-type }
	 */
	protected function get_output_format( $filename = null, $mime_type = null ) {
		$new_ext = null;

		// By default, assume specified type takes priority
		if ( $mime_type ) {
			$new_ext = $this->get_extension( $mime_type );
		}

		if ( $filename ) {
			$file_ext  = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
			$file_mime = $this->get_mime_type( $file_ext );
		} else {
			// If no file specified, grab editor's current extension and mime-type.
			$file_ext  = strtolower( pathinfo( $this->file, PATHINFO_EXTENSION ) );
			$file_mime = $this->mime_type;
		}

		// Check to see if specified mime-type is the same as type implied by
		// file extension.  If so, prefer extension from file.
		if ( ! $mime_type || ( $file_mime == $mime_type ) ) {
			$mime_type = $file_mime;
			$new_ext   = $file_ext;
		}

		// Double-check that the mime-type selected is supported by the editor.
		// If not, choose a default instead.
		if ( ! $this->supports_mime_type( $mime_type ) ) {
			/**
			 * Filters default mime type prior to getting the file extension.
			 *
			 * @see wp_get_mime_types()
			 *
			 * @since 3.5.0
			 *
			 * @param string $mime_type Mime type string.
			 */
			$mime_type = apply_filters( 'image_editor_default_mime_type', $this->default_mime_type );
			$new_ext   = $this->get_extension( $mime_type );
		}

		if ( $filename ) {
			$dir = pathinfo( $filename, PATHINFO_DIRNAME );
			$ext = pathinfo( $filename, PATHINFO_EXTENSION );

			$filename = trailingslashit( $dir ) . wp_basename( $filename, ".$ext" ) . ".{$new_ext}";
		}

		return array( $filename, $new_ext, $mime_type );
	}

	/**
	 * Builds an output filename based on current file, and adding proper suffix
	 *
	 * @since 3.5.0
	 *
	 * @param string $suffix
	 * @param string $dest_path
	 * @param string $extension
	 * @return string filename
	 */
	public function generate_filename( $suffix = null, $dest_path = null, $extension = null ) {
		// $suffix will be appended to the destination filename, just before the extension
		if ( ! $suffix ) {
			$suffix = $this->get_suffix();
		}

		$dir = pathinfo( $this->file, PATHINFO_DIRNAME );
		$ext = pathinfo( $this->file, PATHINFO_EXTENSION );

		$name    = wp_basename( $this->file, ".$ext" );
		$new_ext = strtolower( $extension ? $extension : $ext );

		if ( ! is_null( $dest_path ) && $_dest_path = realpath( $dest_path ) ) {
			$dir = $_dest_path;
		}

		return trailingslashit( $dir ) . "{$name}-{$suffix}.{$new_ext}";
	}

	/**
	 * Builds and returns proper suffix for file based on height and width.
	 *
	 * @since 3.5.0
	 *
	 * @return false|string suffix
	 */
	public function get_suffix() {
		if ( ! $this->get_size() ) {
			return false;
		}

		return "{$this->size['width']}x{$this->size['height']}";
	}

	/**
	 * Either calls editor's save function or handles file as a stream.
	 *
	 * @since 3.5.0
	 *
	 * @param string|stream $filename
	 * @param callable $function
	 * @param array $arguments
	 * @return bool
	 */
	protected function make_image( $filename, $function, $arguments ) {
		if ( $stream = wp_is_stream( $filename ) ) {
			ob_start();
		} else {
			// The directory containing the original file may no longer exist when using a replication plugin.
			wp_mkdir_p( dirname( $filename ) );
		}

		$result = call_user_func_array( $function, $arguments );

		if ( $result && $stream ) {
			$contents = ob_get_contents();

			$fp = fopen( $filename, 'w' );

			if ( ! $fp ) {
				return false;
			}

			fwrite( $fp, $contents );
			fclose( $fp );
		}

		if ( $stream ) {
			ob_end_clean();
		}

		return $result;
	}

	/**
	 * Returns first matched mime-type from extension,
	 * as mapped from wp_get_mime_types()
	 *
	 * @since 3.5.0
	 *
	 * @static
	 *
	 * @param string $extension
	 * @return string|false
	 */
	protected static function get_mime_type( $extension = null ) {
		if ( ! $extension ) {
			return false;
		}

		$mime_types = wp_get_mime_types();
		$extensions = array_keys( $mime_types );

		foreach ( $extensions as $_extension ) {
			if ( preg_match( "/{$extension}/i", $_extension ) ) {
				return $mime_types[ $_extension ];
			}
		}

		return false;
	}

	/**
	 * Returns first matched extension from Mime-type,
	 * as mapped from wp_get_mime_types()
	 *
	 * @since 3.5.0
	 *
	 * @static
	 *
	 * @param string $mime_type
	 * @return string|false
	 */
	protected static function get_extension( $mime_type = null ) {
		$extensions = explode( '|', array_search( $mime_type, wp_get_mime_types() ) );

		if ( empty( $extensions[0] ) ) {
			return false;
		}

		return $extensions[0];
	}
}

