<?php
namespace Elementor\TemplateLibrary\Classes;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Files\Uploads_Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library import images.
 *
 * Elementor template library import images handler class is responsible for
 * importing remote images used by the template library.
 *
 * @since 1.0.0
 */
class Import_Images {

	/**
	 * Replaced images IDs.
	 *
	 * The IDs of all the new imported images. An array containing the old
	 * attachment ID and the new attachment ID generated after the import.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $_replace_image_ids = [];

	/**
	 * Get image hash.
	 *
	 * Retrieve the sha1 hash of the image URL.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param string $attachment_url The attachment URL.
	 *
	 * @return string Image hash.
	 */
	private function get_hash_image( $attachment_url ) {
		return sha1( $attachment_url );
	}

	/**
	 * Get saved image.
	 *
	 * Retrieve new image ID, if the image has a new ID after the import.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param array $attachment The attachment.
	 *
	 * @return false|array New image ID  or false.
	 */
	private function get_saved_image( $attachment ) {
		global $wpdb;

		if ( isset( $this->_replace_image_ids[ $attachment['id'] ] ) ) {
			return $this->_replace_image_ids[ $attachment['id'] ];
		}

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT `post_id` FROM `' . $wpdb->postmeta . '`
					WHERE `meta_key` = \'_elementor_source_image_hash\'
						AND `meta_value` = %s
				;',
				$this->get_hash_image( $attachment['url'] )
			)
		);

		if ( $post_id ) {
			$new_attachment = [
				'id' => $post_id,
				'url' => wp_get_attachment_url( $post_id ),
			];
			$this->_replace_image_ids[ $attachment['id'] ] = $new_attachment;

			return $new_attachment;
		}

		return false;
	}

	/**
	 * Import image.
	 *
	 * Import a single image from a remote server, upload the image WordPress
	 * uploads folder, create a new attachment in the database and updates the
	 * attachment metadata.
	 *
	 * @since 1.0.0
	 * @since 3.2.0 New `$parent_post_id` option added
	 * @access public
	 *
	 * @param array $attachment The attachment.
	 * @param int   $parent_post_id Optional.
	 *
	 * @return false|array Imported image data, or false.
	 */
	public function import( $attachment, $parent_post_id = null ) {
		if ( isset( $attachment['tmp_name'] ) ) {
			// Used when called to import a directly-uploaded file.
			$filename = $attachment['name'];
			$file_content = false;

			// security validation in case the tmp_name has been tampered with
			if ( is_uploaded_file( $attachment['tmp_name'] ) ) {
				$file_content = Utils::file_get_contents( $attachment['tmp_name'] );
			}
		} else {
			// Used when attachment information is passed to this method.
			if ( ! empty( $attachment['id'] ) ) {
				$saved_image = $this->get_saved_image( $attachment );

				if ( $saved_image ) {
					return $saved_image;
				}
			}

			// Extract the file name and extension from the url.
			$filename = basename( $attachment['url'] );

			$request = wp_safe_remote_get( $attachment['url'] );

			// Make sure the request returns a valid result.
			if ( is_wp_error( $request ) || ( ! empty( $request['response']['code'] ) && 200 !== (int) $request['response']['code'] ) ) {
				return false;
			}

			$file_content = wp_remote_retrieve_body( $request );
		}

		if ( empty( $file_content ) ) {
			return false;
		}

		$filetype = wp_check_filetype( $filename );

		// If the file type is not recognized by WordPress, exit here to avoid creation of an empty attachment document.
		if ( ! $filetype['ext'] ) {
			return false;
		}

		if ( 'svg' === $filetype['ext'] ) {
			// In case that unfiltered-files upload is not enabled, SVG images should not be imported.
			if ( ! Uploads_Manager::are_unfiltered_uploads_enabled() ) {
				return false;
			}

			$svg_handler = Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );

			$file_content = $svg_handler->sanitizer( $file_content );
		}

		$upload = wp_upload_bits(
			$filename,
			null,
			$file_content
		);

		$post = [
			'post_title' => $filename,
			'guid' => $upload['url'],
		];

		$info = wp_check_filetype( $upload['file'] );

		if ( $info ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			// For now just return the origin attachment
			return $attachment;
			// return new \WP_Error( 'attachment_processing_error', esc_html__( 'Invalid file type.', 'elementor' ) );
		}

		$post_id = wp_insert_attachment( $post, $upload['file'], $parent_post_id );

		apply_filters( 'elementor/template_library/import_images/new_attachment', $post_id );

		// On REST requests.
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . '/wp-admin/includes/image.php';
		}

		if ( ! function_exists( 'wp_read_video_metadata' ) ) {
			require_once ABSPATH . '/wp-admin/includes/media.php';
		}

		wp_update_attachment_metadata(
			$post_id,
			wp_generate_attachment_metadata( $post_id, $upload['file'] )
		);
		update_post_meta( $post_id, '_elementor_source_image_hash', $this->get_hash_image( $attachment['url'] ) );

		$new_attachment = [
			'id' => $post_id,
			'url' => $upload['url'],
		];

		if ( ! empty( $attachment['id'] ) ) {
			$this->_replace_image_ids[ $attachment['id'] ] = $new_attachment;
		}

		return $new_attachment;
	}

	/**
	 * Import local file.
	 *
	 * Import a local file directly to WordPress media library.
	 * Used for importing files that are already downloaded locally (e.g., from extracted ZIP).
	 *
	 * @since 3.33.0
	 * @access public
	 *
	 * @param string $local_file_path The local file path.
	 * @param int    $parent_post_id  Optional. Parent post ID.
	 *
	 * @return false|array Imported image data, or false on failure.
	 */
	public function import_local_file( $local_file_path, $parent_post_id = null ) {
		global $wp_filesystem;

		if ( ! $wp_filesystem->exists( $local_file_path ) ) {
			return false;
		}

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$file_array = [
			'name' => basename( $local_file_path ),
			'tmp_name' => $local_file_path,
		];

		$attachment_id = media_handle_sideload( $file_array, $parent_post_id );

		if ( is_wp_error( $attachment_id ) ) {
			return false;
		}

		apply_filters( 'elementor/template_library/import_images/new_attachment', $attachment_id );

		return [
			'id' => $attachment_id,
			'url' => wp_get_attachment_url( $attachment_id ),
		];
	}

	/**
	 * Template library import images constructor.
	 *
	 * Initializing the images import class used by the template library through
	 * the WordPress Filesystem API.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();
	}
}
