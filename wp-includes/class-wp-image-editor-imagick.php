<?php
/**
 * WordPress Imagick Image Editor
 *
 * @package WordPress
 * @subpackage Image_Editor
 */

/**
 * WordPress Image Editor Class for Image Manipulation through Imagick PHP Module
 *
 * @since 3.5.0
 * @package WordPress
 * @subpackage Image_Editor
 * @uses WP_Image_Editor Extends class
 */
class WP_Image_Editor_Imagick extends WP_Image_Editor {
	/**
	 * Imagick object.
	 *
	 * @access protected
	 * @var Imagick
	 */
	protected $image;

	public function __destruct() {
		if ( $this->image instanceof Imagick ) {
			// we don't need the original in memory anymore
			$this->image->clear();
			$this->image->destroy();
		}
	}

	/**
	 * Checks to see if current environment supports Imagick.
	 *
	 * We require Imagick 2.2.0 or greater, based on whether the queryFormats()
	 * method can be called statically.
	 *
	 * @since 3.5.0
	 *
	 * @static
	 * @access public
	 *
	 * @param array $args
	 * @return bool
	 */
	public static function test( $args = array() ) {

		// First, test Imagick's extension and classes.
		if ( ! extension_loaded( 'imagick' ) || ! class_exists( 'Imagick', false ) || ! class_exists( 'ImagickPixel', false ) )
			return false;

		if ( version_compare( phpversion( 'imagick' ), '2.2.0', '<' ) )
			return false;

		$required_methods = array(
			'clear',
			'destroy',
			'valid',
			'getimage',
			'writeimage',
			'getimageblob',
			'getimagegeometry',
			'getimageformat',
			'setimageformat',
			'setimagecompression',
			'setimagecompressionquality',
			'setimagepage',
			'scaleimage',
			'cropimage',
			'rotateimage',
			'flipimage',
			'flopimage',
		);

		// Now, test for deep requirements within Imagick.
		if ( ! defined( 'imagick::COMPRESSION_JPEG' ) )
			return false;

		if ( array_diff( $required_methods, get_class_methods( 'Imagick' ) ) )
			return false;

		return true;
	}

	/**
	 * Checks to see if editor supports the mime-type specified.
	 *
	 * @since 3.5.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $mime_type
	 * @return bool
	 */
	public static function supports_mime_type( $mime_type ) {
		$imagick_extension = strtoupper( self::get_extension( $mime_type ) );

		if ( ! $imagick_extension )
			return false;

		// setIteratorIndex is optional unless mime is an animated format.
		// Here, we just say no if you are missing it and aren't loading a jpeg.
		if ( ! method_exists( 'Imagick', 'setIteratorIndex' ) && $mime_type != 'image/jpeg' )
				return false;

		try {
			return ( (bool) @Imagick::queryFormats( $imagick_extension ) );
		}
		catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Loads image from $this->file into new Imagick Object.
	 *
	 * @since 3.5.0
	 * @access protected
	 *
	 * @return true|WP_Error True if loaded; WP_Error on failure.
	 */
	public function load() {
		if ( $this->image instanceof Imagick )
			return true;

		if ( ! is_file( $this->file ) && ! preg_match( '|^https?://|', $this->file ) )
			return new WP_Error( 'error_loading_image', __('File doesn&#8217;t exist?'), $this->file );

		/** This filter is documented in wp-includes/class-wp-image-editor-imagick.php */
		// Even though Imagick uses less PHP memory than GD, set higher limit for users that have low PHP.ini limits
		@ini_set( 'memory_limit', apply_filters( 'image_memory_limit', WP_MAX_MEMORY_LIMIT ) );

		try {
			$this->image = new Imagick( $this->file );

			if ( ! $this->image->valid() )
				return new WP_Error( 'invalid_image', __('File is not an image.'), $this->file);

			// Select the first frame to handle animated images properly
			if ( is_callable( array( $this->image, 'setIteratorIndex' ) ) )
				$this->image->setIteratorIndex(0);

			$this->mime_type = $this->get_mime_type( $this->image->getImageFormat() );
		}
		catch ( Exception $e ) {
			return new WP_Error( 'invalid_image', $e->getMessage(), $this->file );
		}

		$updated_size = $this->update_size();
		if ( is_wp_error( $updated_size ) ) {
			return $updated_size;
		}

		return $this->set_quality();
	}

	/**
	 * Sets Image Compression quality on a 1-100% scale.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param int $quality Compression Quality. Range: [1,100]
	 * @return true|WP_Error True if set successfully; WP_Error on failure.
	 */
	public function set_quality( $quality = null ) {
		$quality_result = parent::set_quality( $quality );
		if ( is_wp_error( $quality_result ) ) {
			return $quality_result;
		} else {
			$quality = $this->get_quality();
		}

		try {
			if ( 'image/jpeg' == $this->mime_type ) {
				$this->image->setImageCompressionQuality( $quality );
				$this->image->setImageCompression( imagick::COMPRESSION_JPEG );
			}
			else {
				$this->image->setImageCompressionQuality( $quality );
			}
		}
		catch ( Exception $e ) {
			return new WP_Error( 'image_quality_error', $e->getMessage() );
		}

		return true;
	}

	/**
	 * Sets or updates current image size.
	 *
	 * @since 3.5.0
	 * @access protected
	 *
	 * @param int $width
	 * @param int $height
	 *
	 * @return true|WP_Error
	 */
	protected function update_size( $width = null, $height = null ) {
		$size = null;
		if ( !$width || !$height ) {
			try {
				$size = $this->image->getImageGeometry();
			}
			catch ( Exception $e ) {
				return new WP_Error( 'invalid_image', __( 'Could not read image size.' ), $this->file );
			}
		}

		if ( ! $width )
			$width = $size['width'];

		if ( ! $height )
			$height = $size['height'];

		return parent::update_size( $width, $height );
	}

	/**
	 * Resizes current image.
	 *
	 * At minimum, either a height or width must be provided.
	 * If one of the two is set to null, the resize will
	 * maintain aspect ratio according to the provided dimension.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param  int|null $max_w Image width.
	 * @param  int|null $max_h Image height.
	 * @param  bool     $crop
	 * @return bool|WP_Error
	 */
	public function resize( $max_w, $max_h, $crop = false ) {
		if ( ( $this->size['width'] == $max_w ) && ( $this->size['height'] == $max_h ) )
			return true;

		$dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );
		if ( ! $dims )
			return new WP_Error( 'error_getting_dimensions', __('Could not calculate resized image dimensions') );
		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

		if ( $crop ) {
			return $this->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h );
		}

		try {
			/**
			 * @TODO: Thumbnail is more efficient, given a newer version of Imagemagick.
			 * $this->image->thumbnailImage( $dst_w, $dst_h );
			 */
			$this->image->scaleImage( $dst_w, $dst_h );
		}
		catch ( Exception $e ) {
			return new WP_Error( 'image_resize_error', $e->getMessage() );
		}

		return $this->update_size( $dst_w, $dst_h );
	}

	/**
	 * Resize multiple images from a single source.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param array $sizes {
	 *     An array of image size arrays. Default sizes are 'small', 'medium', 'medium_large', 'large'.
	 *
	 *     Either a height or width must be provided.
	 *     If one of the two is set to null, the resize will
	 *     maintain aspect ratio according to the provided dimension.
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
		$orig_size = $this->size;
		$orig_image = $this->image->getImage();

		foreach ( $sizes as $size => $size_data ) {
			if ( ! $this->image )
				$this->image = $orig_image->getImage();

			if ( ! isset( $size_data['width'] ) && ! isset( $size_data['height'] ) ) {
				continue;
			}

			if ( ! isset( $size_data['width'] ) ) {
				$size_data['width'] = null;
			}
			if ( ! isset( $size_data['height'] ) ) {
				$size_data['height'] = null;
			}

			if ( ! isset( $size_data['crop'] ) ) {
				$size_data['crop'] = false;
			}

			$resize_result = $this->resize( $size_data['width'], $size_data['height'], $size_data['crop'] );
			$duplicate = ( ( $orig_size['width'] == $size_data['width'] ) && ( $orig_size['height'] == $size_data['height'] ) );

			if ( ! is_wp_error( $resize_result ) && ! $duplicate ) {
				$resized = $this->_save( $this->image );

				$this->image->clear();
				$this->image->destroy();
				$this->image = null;

				if ( ! is_wp_error( $resized ) && $resized ) {
					unset( $resized['path'] );
					$metadata[$size] = $resized;
				}
			}

			$this->size = $orig_size;
		}

		$this->image = $orig_image;

		return $metadata;
	}

	/**
	 * Crops Image.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param int  $src_x The start x position to crop from.
	 * @param int  $src_y The start y position to crop from.
	 * @param int  $src_w The width to crop.
	 * @param int  $src_h The height to crop.
	 * @param int  $dst_w Optional. The destination width.
	 * @param int  $dst_h Optional. The destination height.
	 * @param bool $src_abs Optional. If the source crop points are absolute.
	 * @return bool|WP_Error
	 */
	public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
		if ( $src_abs ) {
			$src_w -= $src_x;
			$src_h -= $src_y;
		}

		try {
			$this->image->cropImage( $src_w, $src_h, $src_x, $src_y );
			$this->image->setImagePage( $src_w, $src_h, 0, 0);

			if ( $dst_w || $dst_h ) {
				// If destination width/height isn't specified, use same as
				// width/height from source.
				if ( ! $dst_w )
					$dst_w = $src_w;
				if ( ! $dst_h )
					$dst_h = $src_h;

				$this->image->scaleImage( $dst_w, $dst_h );
				return $this->update_size();
			}
		}
		catch ( Exception $e ) {
			return new WP_Error( 'image_crop_error', $e->getMessage() );
		}
		return $this->update_size();
	}

	/**
	 * Rotates current image counter-clockwise by $angle.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param float $angle
	 * @return true|WP_Error
	 */
	public function rotate( $angle ) {
		/**
		 * $angle is 360-$angle because Imagick rotates clockwise
		 * (GD rotates counter-clockwise)
		 */
		try {
			$this->image->rotateImage( new ImagickPixel('none'), 360-$angle );

			// Since this changes the dimensions of the image, update the size.
			$result = $this->update_size();
			if ( is_wp_error( $result ) )
				return $result;

			$this->image->setImagePage( $this->size['width'], $this->size['height'], 0, 0 );
		}
		catch ( Exception $e ) {
			return new WP_Error( 'image_rotate_error', $e->getMessage() );
		}
		return true;
	}

	/**
	 * Flips current image.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param bool $horz Flip along Horizontal Axis
	 * @param bool $vert Flip along Vertical Axis
	 * @return true|WP_Error
	 */
	public function flip( $horz, $vert ) {
		try {
			if ( $horz )
				$this->image->flipImage();

			if ( $vert )
				$this->image->flopImage();
		}
		catch ( Exception $e ) {
			return new WP_Error( 'image_flip_error', $e->getMessage() );
		}
		return true;
	}

	/**
	 * Saves current image to file.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $destfilename
	 * @param string $mime_type
	 * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
	 */
	public function save( $destfilename = null, $mime_type = null ) {
		$saved = $this->_save( $this->image, $destfilename, $mime_type );

		if ( ! is_wp_error( $saved ) ) {
			$this->file = $saved['path'];
			$this->mime_type = $saved['mime-type'];

			try {
				$this->image->setImageFormat( strtoupper( $this->get_extension( $this->mime_type ) ) );
			}
			catch ( Exception $e ) {
				return new WP_Error( 'image_save_error', $e->getMessage(), $this->file );
			}
		}

		return $saved;
	}

	/**
	 *
	 * @param Imagick $image
	 * @param string $filename
	 * @param string $mime_type
	 * @return array|WP_Error
	 */
	protected function _save( $image, $filename = null, $mime_type = null ) {
		list( $filename, $extension, $mime_type ) = $this->get_output_format( $filename, $mime_type );

		if ( ! $filename )
			$filename = $this->generate_filename( null, null, $extension );

		try {
			// Store initial Format
			$orig_format = $this->image->getImageFormat();

			$this->image->setImageFormat( strtoupper( $this->get_extension( $mime_type ) ) );
			$this->make_image( $filename, array( $image, 'writeImage' ), array( $filename ) );

			// Reset original Format
			$this->image->setImageFormat( $orig_format );
		}
		catch ( Exception $e ) {
			return new WP_Error( 'image_save_error', $e->getMessage(), $filename );
		}

		// Set correct file permissions
		$stat = stat( dirname( $filename ) );
		$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
		@ chmod( $filename, $perms );

		/** This filter is documented in wp-includes/class-wp-image-editor-gd.php */
		return array(
			'path'      => $filename,
			'file'      => wp_basename( apply_filters( 'image_make_intermediate_size', $filename ) ),
			'width'     => $this->size['width'],
			'height'    => $this->size['height'],
			'mime-type' => $mime_type,
		);
	}

	/**
	 * Streams current image to browser.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $mime_type
	 * @return true|WP_Error
	 */
	public function stream( $mime_type = null ) {
		list( $filename, $extension, $mime_type ) = $this->get_output_format( null, $mime_type );

		try {
			// Temporarily change format for stream
			$this->image->setImageFormat( strtoupper( $extension ) );

			// Output stream of image content
			header( "Content-Type: $mime_type" );
			print $this->image->getImageBlob();

			// Reset Image to original Format
			$this->image->setImageFormat( $this->get_extension( $this->mime_type ) );
		}
		catch ( Exception $e ) {
			return new WP_Error( 'image_stream_error', $e->getMessage() );
		}

		return true;
	}
}
