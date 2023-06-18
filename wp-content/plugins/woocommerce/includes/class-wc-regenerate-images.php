<?php
/**
 * Regenerate Images Functionality
 *
 * All functionality pertaining to regenerating product images in realtime.
 *
 * @package WooCommerce\Classes
 * @version 3.5.0
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Regenerate Images Class
 */
class WC_Regenerate_Images {

	/**
	 * Background process to regenerate all images
	 *
	 * @var WC_Regenerate_Images_Request
	 */
	protected static $background_process;

	/**
	 * Stores size being generated on the fly.
	 *
	 * @var string
	 */
	protected static $regenerate_size;

	/**
	 * Init function
	 */
	public static function init() {
		add_action( 'image_get_intermediate_size', array( __CLASS__, 'filter_image_get_intermediate_size' ), 10, 3 );
		add_filter( 'wp_generate_attachment_metadata', array( __CLASS__, 'add_uncropped_metadata' ) );
		add_filter( 'wp_get_attachment_image_src', array( __CLASS__, 'maybe_resize_image' ), 10, 4 );

		// Not required when Jetpack Photon is in use.
		if ( method_exists( 'Jetpack', 'is_module_active' ) && Jetpack::is_module_active( 'photon' ) ) {
			return;
		}

		if ( apply_filters( 'woocommerce_background_image_regeneration', true ) ) {
			include_once WC_ABSPATH . 'includes/class-wc-regenerate-images-request.php';

			self::$background_process = new WC_Regenerate_Images_Request();

			add_action( 'admin_init', array( __CLASS__, 'regenerating_notice' ) );
			add_action( 'woocommerce_hide_regenerating_thumbnails_notice', array( __CLASS__, 'dismiss_regenerating_notice' ) );

			// Regenerate thumbnails in the background after settings changes. Not ran on multisite to avoid multiple simultaneous jobs.
			if ( ! is_multisite() ) {
				add_action( 'customize_save_after', array( __CLASS__, 'maybe_regenerate_images' ) );
				add_action( 'after_switch_theme', array( __CLASS__, 'maybe_regenerate_images' ) );
			}
		}
	}

	/**
	 * If an intermediate size meta differs from the actual image size (settings were changed?) return false so the wrong size is not used.
	 *
	 * @param array  $data Size data.
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size Size name.
	 * @return array
	 */
	public static function filter_image_get_intermediate_size( $data, $attachment_id, $size ) {
		if ( ! is_string( $size ) || ! in_array( $size, apply_filters( 'woocommerce_image_sizes_to_resize', array( 'woocommerce_thumbnail', 'woocommerce_gallery_thumbnail', 'woocommerce_single' ) ), true ) ) {
			return $data;
		}

		// If we don't have sizes, we cannot proceed.
		if ( ! isset( $data['width'], $data['height'] ) ) {
			return $data;
		}

		// See if the image size has changed from our settings.
		if ( ! self::image_size_matches_settings( $data, $size ) ) {
			// If Photon is running we can just return false and let Jetpack handle regeneration.
			if ( method_exists( 'Jetpack', 'is_module_active' ) && Jetpack::is_module_active( 'photon' ) ) {
				return false;
			} else {
				// If we get here, Jetpack is not running and we don't have the correct image sized stored. Try to return closest match.
				$size_data = wc_get_image_size( $size );
				return image_get_intermediate_size( $attachment_id, array( absint( $size_data['width'] ), absint( $size_data['height'] ) ) );
			}
		}
		return $data;
	}

	/**
	 * We need to track if uncropped was on or off when generating the images.
	 *
	 * @param array $meta_data Array of meta data.
	 * @return array
	 */
	public static function add_uncropped_metadata( $meta_data ) {
		$size_data = wc_get_image_size( 'woocommerce_thumbnail' );
		if ( isset( $meta_data['sizes'], $meta_data['sizes']['woocommerce_thumbnail'] ) ) {
			$meta_data['sizes']['woocommerce_thumbnail']['uncropped'] = empty( $size_data['height'] );
		}
		return $meta_data;
	}

	/**
	 * See if an image's dimensions match actual settings.
	 *
	 * @param array  $image Image dimensions array.
	 * @param string $size Named size.
	 * @return bool True if they match. False if they do not (may trigger regen).
	 */
	protected static function image_size_matches_settings( $image, $size ) {
		$target_size = wc_get_image_size( $size );
		$uncropped   = '' === $target_size['width'] || '' === $target_size['height'];

		if ( ! $uncropped ) {
			$ratio_match = wp_image_matches_ratio( $image['width'], $image['height'], $target_size['width'], $target_size['height'] );

			// Size is invalid if the widths or crop setting don't match.
			if ( $ratio_match && $target_size['width'] !== $image['width'] ) {
				return false;
			}

			// Size is invalid if the heights don't match.
			if ( $ratio_match && $target_size['height'] && $target_size['height'] !== $image['height'] ) {
				return false;
			}
		}

		// If cropping mode has changed, regenerate the image.
		if ( $uncropped && empty( $image['uncropped'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Show notice when job is running in background.
	 */
	public static function regenerating_notice() {
		if ( ! self::$background_process->is_running() ) {
			WC_Admin_Notices::add_notice( 'regenerating_thumbnails' );
		} else {
			WC_Admin_Notices::remove_notice( 'regenerating_thumbnails' );
		}
	}

	/**
	 * Dismiss notice and cancel jobs.
	 */
	public static function dismiss_regenerating_notice() {
		if ( self::$background_process ) {
			self::$background_process->kill_process();

			$log = wc_get_logger();
			$log->info(
				__( 'Cancelled product image regeneration job.', 'woocommerce' ),
				array(
					'source' => 'wc-image-regeneration',
				)
			);
		}
		WC_Admin_Notices::remove_notice( 'regenerating_thumbnails' );
	}

	/**
	 * Regenerate images if the settings have changed since last re-generation.
	 *
	 * @return void
	 */
	public static function maybe_regenerate_images() {
		$size_hash = md5(
			wp_json_encode(
				array(
					wc_get_image_size( 'thumbnail' ),
					wc_get_image_size( 'single' ),
					wc_get_image_size( 'gallery_thumbnail' ),
				)
			)
		);

		if ( update_option( 'woocommerce_maybe_regenerate_images_hash', $size_hash ) ) {
			// Size settings have changed. Trigger regen.
			self::queue_image_regeneration();
		}
	}

	/**
	 * Check if we should maybe generate a new image size if not already there.
	 *
	 * @param array        $image Properties of the image.
	 * @param int          $attachment_id Attachment ID.
	 * @param string|array $size Image size.
	 * @param bool         $icon If icon or not.
	 * @return array
	 */
	public static function maybe_resize_image( $image, $attachment_id, $size, $icon ) {
		if ( ! apply_filters( 'woocommerce_resize_images', true ) ) {
			return $image;
		}

		// List of sizes we want to resize. Ignore others.
		if ( ! $image || ! in_array( $size, apply_filters( 'woocommerce_image_sizes_to_resize', array( 'woocommerce_thumbnail', 'woocommerce_gallery_thumbnail', 'woocommerce_single' ) ), true ) ) {
			return $image;
		}

		$target_size      = wc_get_image_size( $size );
		$image_width      = $image[1];
		$image_height     = $image[2];
		$ratio_match      = false;
		$target_uncropped = '' === $target_size['width'] || '' === $target_size['height'] || ! $target_size['crop'];

		// If '' is passed to either size, we test ratios against the original file. It's uncropped.
		if ( $target_uncropped ) {
			$full_size = self::get_full_size_image_dimensions( $attachment_id );

			if ( ! $full_size || ! $full_size['width'] || ! $full_size['height'] ) {
				return $image;
			}

			$ratio_match = wp_image_matches_ratio( $image_width, $image_height, $full_size['width'], $full_size['height'] );
		} else {
			$ratio_match = wp_image_matches_ratio( $image_width, $image_height, $target_size['width'], $target_size['height'] );
		}

		if ( ! $ratio_match ) {
			$full_size = self::get_full_size_image_dimensions( $attachment_id );

			if ( ! $full_size ) {
				return $image;
			}

			// Check if the actual image has a larger dimension than the requested image size. Smaller images are not zoom-cropped.
			if ( $image_width === $target_size['width'] && $full_size['height'] < $target_size['height'] ) {
				return $image;
			}

			if ( $image_height === $target_size['height'] && $full_size['width'] < $target_size['width'] ) {
				return $image;
			}

			// If the full size image is smaller both ways, don't scale it up.
			if ( $full_size['height'] < $target_size['height'] && $full_size['width'] < $target_size['width'] ) {
				return $image;
			}

			return self::resize_and_return_image( $attachment_id, $image, $size, $icon );
		}

		return $image;
	}

	/**
	 * Get full size image dimensions.
	 *
	 * @param int $attachment_id Attachment ID of image.
	 * @return array Width and height. Empty array if the dimensions cannot be found.
	 */
	private static function get_full_size_image_dimensions( $attachment_id ) {
		$imagedata = wp_get_attachment_metadata( $attachment_id );

		if ( ! $imagedata ) {
			return array();
		}

		if ( ! isset( $imagedata['file'] ) && isset( $imagedata['sizes']['full'] ) ) {
			$imagedata['height'] = $imagedata['sizes']['full']['height'];
			$imagedata['width']  = $imagedata['sizes']['full']['width'];
		}

		return array(
			'width'  => $imagedata['width'],
			'height' => $imagedata['height'],
		);
	}

	/**
	 * Ensure we are dealing with the correct image attachment
	 *
	 * @param int|WP_Post $attachment Attachment object or ID.
	 * @return boolean
	 */
	public static function is_regeneratable( $attachment ) {
		if ( 'site-icon' === get_post_meta( is_object( $attachment ) ? $attachment->ID : $attachment, '_wp_attachment_context', true ) ) {
			return false;
		}

		if ( wp_attachment_is_image( $attachment ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Only regenerate images for the requested size.
	 *
	 * @param array $sizes Array of image sizes.
	 * @return array
	 */
	public static function adjust_intermediate_image_sizes( $sizes ) {
		return array( self::$regenerate_size );
	}

	/**
	 * Generate the thumbnail filename and dimensions for a given file.
	 *
	 * @param string $fullsizepath Path to full size image.
	 * @param int    $thumbnail_width  The width of the thumbnail.
	 * @param int    $thumbnail_height The height of the thumbnail.
	 * @param bool   $crop             Whether to crop or not.
	 * @return array|false An array of the filename, thumbnail width, and thumbnail height, or false on failure to resize such as the thumbnail being larger than the fullsize image.
	 */
	private static function get_image( $fullsizepath, $thumbnail_width, $thumbnail_height, $crop ) {
		list( $fullsize_width, $fullsize_height ) = getimagesize( $fullsizepath );

		$dimensions = image_resize_dimensions( $fullsize_width, $fullsize_height, $thumbnail_width, $thumbnail_height, $crop );
		$editor     = wp_get_image_editor( $fullsizepath );

		if ( is_wp_error( $editor ) ) {
			return false;
		}

		if ( ! $dimensions || ! is_array( $dimensions ) ) {
			return false;
		}

		list( , , , , $dst_w, $dst_h ) = $dimensions;
		$suffix                        = "{$dst_w}x{$dst_h}";
		$file_ext                      = strtolower( pathinfo( $fullsizepath, PATHINFO_EXTENSION ) );

		return array(
			'filename' => $editor->generate_filename( $suffix, null, $file_ext ),
			'width'    => $dst_w,
			'height'   => $dst_h,
		);
	}

	/**
	 * Regenerate the image according to the required size
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param array  $image Original Image.
	 * @param string $size Size to return for new URL.
	 * @param bool   $icon If icon or not.
	 * @return string
	 */
	private static function resize_and_return_image( $attachment_id, $image, $size, $icon ) {
		if ( ! self::is_regeneratable( $attachment_id ) ) {
			return $image;
		}

		$fullsizepath = get_attached_file( $attachment_id );

		if ( false === $fullsizepath || is_wp_error( $fullsizepath ) || ! file_exists( $fullsizepath ) ) {
			return $image;
		}

		if ( ! function_exists( 'wp_crop_image' ) ) {
			include ABSPATH . 'wp-admin/includes/image.php';
		}

		self::$regenerate_size = is_customize_preview() ? $size . '_preview' : $size;

		if ( is_customize_preview() ) {
			$image_size = wc_get_image_size( $size );

			// Make sure registered image size matches the size we're requesting.
			add_image_size( self::$regenerate_size, absint( $image_size['width'] ), absint( $image_size['height'] ), $image_size['crop'] );

			$thumbnail = self::get_image( $fullsizepath, absint( $image_size['width'] ), absint( $image_size['height'] ), $image_size['crop'] );

			// If the file is already there perhaps just load it if we're using the customizer. No need to store in meta data.
			if ( $thumbnail && file_exists( $thumbnail['filename'] ) ) {
				$wp_uploads     = wp_upload_dir( null, false );
				$wp_uploads_dir = $wp_uploads['basedir'];
				$wp_uploads_url = $wp_uploads['baseurl'];

				return array(
					0 => str_replace( $wp_uploads_dir, $wp_uploads_url, $thumbnail['filename'] ),
					1 => $thumbnail['width'],
					2 => $thumbnail['height'],
				);
			}
		}

		$metadata = wp_get_attachment_metadata( $attachment_id );

		// Fix for images with no metadata.
		if ( ! is_array( $metadata ) ) {
			$metadata = array();
		}

		// We only want to regen a specific image size.
		add_filter( 'intermediate_image_sizes', array( __CLASS__, 'adjust_intermediate_image_sizes' ) );

		// This function will generate the new image sizes.
		$new_metadata = wp_generate_attachment_metadata( $attachment_id, $fullsizepath );

		// Remove custom filter.
		remove_filter( 'intermediate_image_sizes', array( __CLASS__, 'adjust_intermediate_image_sizes' ) );

		// If something went wrong lets just return the original image.
		if ( is_wp_error( $new_metadata ) || empty( $new_metadata ) ) {
			return $image;
		}

		if ( isset( $new_metadata['sizes'][ self::$regenerate_size ] ) ) {
			$metadata['sizes'][ self::$regenerate_size ] = $new_metadata['sizes'][ self::$regenerate_size ];
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		// Now we've done our regen, attempt to return the new size.
		$new_image = self::unfiltered_image_downsize( $attachment_id, self::$regenerate_size );

		return $new_image ? $new_image : $image;
	}

	/**
	 * Image downsize, without this classes filtering on the results.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size Size to downsize to.
	 * @return string New image URL.
	 */
	private static function unfiltered_image_downsize( $attachment_id, $size ) {
		remove_action( 'image_get_intermediate_size', array( __CLASS__, 'filter_image_get_intermediate_size' ), 10, 3 );

		$return = image_downsize( $attachment_id, $size );

		add_action( 'image_get_intermediate_size', array( __CLASS__, 'filter_image_get_intermediate_size' ), 10, 3 );

		return $return;
	}

	/**
	 * Get list of images and queue them for regeneration
	 *
	 * @return void
	 */
	public static function queue_image_regeneration() {
		global $wpdb;
		// First lets cancel existing running queue to avoid running it more than once.
		self::$background_process->kill_process();

		// Now lets find all product image attachments IDs and pop them onto the queue.
		$images = $wpdb->get_results( // @codingStandardsIgnoreLine
			"SELECT ID
			FROM $wpdb->posts
			WHERE post_type = 'attachment'
			AND post_mime_type LIKE 'image/%'
			ORDER BY ID DESC"
		);
		foreach ( $images as $image ) {
			self::$background_process->push_to_queue(
				array(
					'attachment_id' => $image->ID,
				)
			);
		}

		// Lets dispatch the queue to start processing.
		self::$background_process->save()->dispatch();
	}
}

add_action( 'init', array( 'WC_Regenerate_Images', 'init' ) );
