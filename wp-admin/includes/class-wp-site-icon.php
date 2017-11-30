<?php
/**
 * Administration API: WP_Site_Icon class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.3.0
 */

/**
 * Core class used to implement site icon functionality.
 *
 * @since 4.3.0
 */
class WP_Site_Icon {

	/**
	 * The minimum size of the site icon.
	 *
	 * @since 4.3.0
	 * @var int
	 */
	public $min_size = 512;

	/**
	 * The size to which to crop the image so that we can display it in the UI nicely.
	 *
	 * @since 4.3.0
	 * @var int
	 */
	public $page_crop = 512;

	/**
	 * List of site icon sizes.
	 *
	 * @since 4.3.0
	 * @var array
	 */
	public $site_icon_sizes = array(
		/*
		 * Square, medium sized tiles for IE11+.
		 *
		 * See https://msdn.microsoft.com/library/dn455106(v=vs.85).aspx
		 */
		270,

		/*
		 * App icon for Android/Chrome.
		 *
		 * @link https://developers.google.com/web/updates/2014/11/Support-for-theme-color-in-Chrome-39-for-Android
		 * @link https://developer.chrome.com/multidevice/android/installtohomescreen
		 */
		192,

		/*
		 * App icons up to iPhone 6 Plus.
		 *
		 * See https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Conceptual/MobileHIG/IconMatrix.html
		 */
		180,

		// Our regular Favicon.
		32,
	);

	/**
	 * Registers actions and filters.
	 *
	 * @since 4.3.0
	 */
	public function __construct() {
		add_action( 'delete_attachment', array( $this, 'delete_attachment_data' ) );
		add_filter( 'get_post_metadata', array( $this, 'get_post_metadata' ), 10, 4 );
	}

	/**
	 * Creates an attachment 'object'.
	 *
	 * @since 4.3.0
	 *
	 * @param string $cropped              Cropped image URL.
	 * @param int    $parent_attachment_id Attachment ID of parent image.
	 * @return array Attachment object.
	 */
	public function create_attachment_object( $cropped, $parent_attachment_id ) {
		$parent     = get_post( $parent_attachment_id );
		$parent_url = wp_get_attachment_url( $parent->ID );
		$url        = str_replace( basename( $parent_url ), basename( $cropped ), $parent_url );

		$size       = @getimagesize( $cropped );
		$image_type = ( $size ) ? $size['mime'] : 'image/jpeg';

		$object = array(
			'ID'             => $parent_attachment_id,
			'post_title'     => basename( $cropped ),
			'post_content'   => $url,
			'post_mime_type' => $image_type,
			'guid'           => $url,
			'context'        => 'site-icon',
		);

		return $object;
	}

	/**
	 * Inserts an attachment.
	 *
	 * @since 4.3.0
	 *
	 * @param array  $object Attachment object.
	 * @param string $file   File path of the attached image.
	 * @return int           Attachment ID
	 */
	public function insert_attachment( $object, $file ) {
		$attachment_id = wp_insert_attachment( $object, $file );
		$metadata      = wp_generate_attachment_metadata( $attachment_id, $file );

		/**
		 * Filters the site icon attachment metadata.
		 *
		 * @since 4.3.0
		 *
		 * @see wp_generate_attachment_metadata()
		 *
		 * @param array $metadata Attachment metadata.
		 */
		$metadata = apply_filters( 'site_icon_attachment_metadata', $metadata );
		wp_update_attachment_metadata( $attachment_id, $metadata );

		return $attachment_id;
	}

	/**
	 * Adds additional sizes to be made when creating the site_icon images.
	 *
	 * @since 4.3.0
	 *
	 * @param array $sizes List of additional sizes.
	 * @return array Additional image sizes.
	 */
	public function additional_sizes( $sizes = array() ) {
		$only_crop_sizes = array();

		/**
		 * Filters the different dimensions that a site icon is saved in.
		 *
		 * @since 4.3.0
		 *
		 * @param array $site_icon_sizes Sizes available for the Site Icon.
		 */
		$this->site_icon_sizes = apply_filters( 'site_icon_image_sizes', $this->site_icon_sizes );

		// Use a natural sort of numbers.
		natsort( $this->site_icon_sizes );
		$this->site_icon_sizes = array_reverse( $this->site_icon_sizes );

		// ensure that we only resize the image into
		foreach ( $sizes as $name => $size_array ) {
			if ( isset( $size_array['crop'] ) ) {
				$only_crop_sizes[ $name ] = $size_array;
			}
		}

		foreach ( $this->site_icon_sizes as $size ) {
			if ( $size < $this->min_size ) {
				$only_crop_sizes[ 'site_icon-' . $size ] = array(
					'width ' => $size,
					'height' => $size,
					'crop'   => true,
				);
			}
		}

		return $only_crop_sizes;
	}

	/**
	 * Adds Site Icon sizes to the array of image sizes on demand.
	 *
	 * @since 4.3.0
	 *
	 * @param array $sizes List of image sizes.
	 * @return array List of intermediate image sizes.
	 */
	public function intermediate_image_sizes( $sizes = array() ) {
		/** This filter is documented in wp-admin/includes/class-wp-site-icon.php */
		$this->site_icon_sizes = apply_filters( 'site_icon_image_sizes', $this->site_icon_sizes );
		foreach ( $this->site_icon_sizes as $size ) {
			$sizes[] = 'site_icon-' . $size;
		}

		return $sizes;
	}

	/**
	 * Deletes the Site Icon when the image file is deleted.
	 *
	 * @since 4.3.0
	 *
	 * @param int $post_id Attachment ID.
	 */
	public function delete_attachment_data( $post_id ) {
		$site_icon_id = get_option( 'site_icon' );

		if ( $site_icon_id && $post_id == $site_icon_id ) {
			delete_option( 'site_icon' );
		}
	}

	/**
	 * Adds custom image sizes when meta data for an image is requested, that happens to be used as Site Icon.
	 *
	 * @since 4.3.0
	 *
	 * @param null|array|string $value    The value get_metadata() should return a single metadata value, or an
	 *                                    array of values.
	 * @param int               $post_id  Post ID.
	 * @param string            $meta_key Meta key.
	 * @param string|array      $single   Meta value, or an array of values.
	 * @return array|null|string The attachment metadata value, array of values, or null.
	 */
	public function get_post_metadata( $value, $post_id, $meta_key, $single ) {
		if ( $single && '_wp_attachment_backup_sizes' === $meta_key ) {
			$site_icon_id = get_option( 'site_icon' );

			if ( $post_id == $site_icon_id ) {
				add_filter( 'intermediate_image_sizes', array( $this, 'intermediate_image_sizes' ) );
			}
		}

		return $value;
	}
}
