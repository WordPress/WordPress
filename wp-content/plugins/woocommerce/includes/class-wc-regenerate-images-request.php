<?php
/**
 * All functionality to regenerate images in the background when settings change.
 *
 * @package WooCommerce\Classes
 * @version 3.3.0
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once dirname( __FILE__ ) . '/abstracts/class-wc-background-process.php';
}

/**
 * Class that extends WC_Background_Process to process image regeneration in the background.
 */
class WC_Regenerate_Images_Request extends WC_Background_Process {

	/**
	 * Stores the attachment ID being processed.
	 *
	 * @var integer
	 */
	protected $attachment_id = 0;

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_regenerate_images';

		// Limit Imagick to only use 1 thread to avoid memory issues with OpenMP.
		if ( extension_loaded( 'imagick' ) && method_exists( Imagick::class, 'setResourceLimit' ) ) {
			if ( defined( 'Imagick::RESOURCETYPE_THREAD' ) ) {
				Imagick::setResourceLimit( Imagick::RESOURCETYPE_THREAD, 1 );
			} else {
				Imagick::setResourceLimit( 6, 1 );
			}
		}

		parent::__construct();
	}

	/**
	 * Is job running?
	 *
	 * @return boolean
	 */
	public function is_running() {
		return $this->is_queue_empty();
	}

	/**
	 * Limit each task ran per batch to 1 for image regen.
	 *
	 * @return bool
	 */
	protected function batch_limit_exceeded() {
		return true;
	}

	/**
	 * Determines whether an attachment can have its thumbnails regenerated.
	 *
	 * Adapted from Regenerate Thumbnails by Alex Mills.
	 *
	 * @param WP_Post $attachment An attachment's post object.
	 * @return bool Whether the given attachment can have its thumbnails regenerated.
	 */
	protected function is_regeneratable( $attachment ) {
		if ( 'site-icon' === get_post_meta( $attachment->ID, '_wp_attachment_context', true ) ) {
			return false;
		}

		if ( wp_attachment_is_image( $attachment ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Code to execute for each item in the queue
	 *
	 * @param mixed $item Queue item to iterate over.
	 * @return bool
	 */
	protected function task( $item ) {
		if ( ! is_array( $item ) && ! isset( $item['attachment_id'] ) ) {
			return false;
		}

		$this->attachment_id = absint( $item['attachment_id'] );
		$attachment          = get_post( $this->attachment_id );

		if ( ! $attachment || 'attachment' !== $attachment->post_type || ! $this->is_regeneratable( $attachment ) ) {
			return false;
		}

		if ( ! function_exists( 'wp_crop_image' ) ) {
			include ABSPATH . 'wp-admin/includes/image.php';
		}

		$log = wc_get_logger();

		$log->info(
			sprintf(
				// translators: %s: ID of the attachment.
				__( 'Regenerating images for attachment ID: %s', 'woocommerce' ),
				$this->attachment_id
			),
			array(
				'source' => 'wc-image-regeneration',
			)
		);

		$fullsizepath = get_attached_file( $this->attachment_id );

		// Check if the file exists, if not just remove item from queue.
		if ( false === $fullsizepath || is_wp_error( $fullsizepath ) || ! file_exists( $fullsizepath ) ) {
			return false;
		}

		$old_metadata = wp_get_attachment_metadata( $this->attachment_id );

		// We only want to regen WC images.
		add_filter( 'intermediate_image_sizes', array( $this, 'adjust_intermediate_image_sizes' ) );

		// We only want to resize images if they do not already exist.
		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'filter_image_sizes_to_only_missing_thumbnails' ), 10, 3 );

		// This function will generate the new image sizes.
		$new_metadata = wp_generate_attachment_metadata( $this->attachment_id, $fullsizepath );

		// Remove custom filters.
		remove_filter( 'intermediate_image_sizes', array( $this, 'adjust_intermediate_image_sizes' ) );
		remove_filter( 'intermediate_image_sizes_advanced', array( $this, 'filter_image_sizes_to_only_missing_thumbnails' ), 10, 3 );

		// If something went wrong lets just remove the item from the queue.
		if ( is_wp_error( $new_metadata ) || empty( $new_metadata ) ) {
			return false;
		}

		if ( ! empty( $old_metadata ) && ! empty( $old_metadata['sizes'] ) && is_array( $old_metadata['sizes'] ) ) {
			foreach ( $old_metadata['sizes'] as $old_size => $old_size_data ) {
				if ( empty( $new_metadata['sizes'][ $old_size ] ) ) {
					$new_metadata['sizes'][ $old_size ] = $old_metadata['sizes'][ $old_size ];
				}
			}
		}

		// Update the meta data with the new size values.
		wp_update_attachment_metadata( $this->attachment_id, $new_metadata );

		// We made it till the end, now lets remove the item from the queue.
		return false;
	}

	/**
	 * Filters the list of thumbnail sizes to only include those which have missing files.
	 *
	 * @param array $sizes         An associative array of registered thumbnail image sizes.
	 * @param array $metadata      An associative array of fullsize image metadata: width, height, file.
	 * @param int   $attachment_id Attachment ID. Only passed from WP 5.0+.
	 * @return array An associative array of image sizes.
	 */
	public function filter_image_sizes_to_only_missing_thumbnails( $sizes, $metadata, $attachment_id = null ) {
		$attachment_id = is_null( $attachment_id ) ? $this->attachment_id : $attachment_id;

		if ( ! $sizes || ! $attachment_id ) {
			return $sizes;
		}

		$fullsizepath = get_attached_file( $attachment_id );
		$editor       = wp_get_image_editor( $fullsizepath );

		if ( is_wp_error( $editor ) ) {
			return $sizes;
		}

		$metadata = wp_get_attachment_metadata( $attachment_id );

		// This is based on WP_Image_Editor_GD::multi_resize() and others.
		foreach ( $sizes as $size => $size_data ) {
			if ( empty( $metadata['sizes'][ $size ] ) ) {
				continue;
			}
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

			$image_sizes = getimagesize( $fullsizepath );
			if ( false === $image_sizes ) {
				continue;
			}
			list( $orig_w, $orig_h ) = $image_sizes;

			$dimensions = image_resize_dimensions( $orig_w, $orig_h, $size_data['width'], $size_data['height'], $size_data['crop'] );

			if ( ! $dimensions || ! is_array( $dimensions ) ) {
				continue;
			}

			$info         = pathinfo( $fullsizepath );
			$ext          = $info['extension'];
			$dst_w        = $dimensions[4];
			$dst_h        = $dimensions[5];
			$suffix       = "{$dst_w}x{$dst_h}";
			$dst_rel_path = str_replace( '.' . $ext, '', $fullsizepath );
			$thumbnail    = "{$dst_rel_path}-{$suffix}.{$ext}";

			if ( $dst_w === $metadata['sizes'][ $size ]['width'] && $dst_h === $metadata['sizes'][ $size ]['height'] && file_exists( $thumbnail ) ) {
				unset( $sizes[ $size ] );
			}
		}

		return $sizes;
	}

	/**
	 * Returns the sizes we want to regenerate.
	 *
	 * @param array $sizes Sizes to generate.
	 * @return array
	 */
	public function adjust_intermediate_image_sizes( $sizes ) {
		// Prevent a filter loop.
		$unfiltered_sizes = array( 'woocommerce_thumbnail', 'woocommerce_gallery_thumbnail', 'woocommerce_single' );
		static $in_filter = false;
		if ( $in_filter ) {
			return $unfiltered_sizes;
		}
		$in_filter      = true;
		$filtered_sizes = apply_filters( 'woocommerce_regenerate_images_intermediate_image_sizes', $unfiltered_sizes );
		$in_filter      = false;
		return $filtered_sizes;
	}

	/**
	 * This runs once the job has completed all items on the queue.
	 *
	 * @return void
	 */
	protected function complete() {
		parent::complete();
		$log = wc_get_logger();
		$log->info(
			__( 'Completed product image regeneration job.', 'woocommerce' ),
			array(
				'source' => 'wc-image-regeneration',
			)
		);
	}
}
