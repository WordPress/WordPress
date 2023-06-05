<?php
/**
 * Helper to upload files via the REST API.
 *
 * @package WooCommerce\Utilities
 */

namespace Automattic\WooCommerce\RestApi\Utilities;

/**
 * ImageAttachment class.
 */
class ImageAttachment {

	/**
	 * Attachment ID.
	 *
	 * @var integer
	 */
	public $id = 0;

	/**
	 * Object attached to.
	 *
	 * @var integer
	 */
	public $object_id = 0;

	/**
	 * Constructor.
	 *
	 * @param integer $id Attachment ID.
	 * @param integer $object_id Object ID.
	 */
	public function __construct( $id = 0, $object_id = 0 ) {
		$this->id        = (int) $id;
		$this->object_id = (int) $object_id;
	}

	/**
	 * Upload an attachment file.
	 *
	 * @throws \WC_REST_Exception REST API exceptions.
	 * @param string $src URL to file.
	 */
	public function upload_image_from_src( $src ) {
		$upload = wc_rest_upload_image_from_url( esc_url_raw( $src ) );

		if ( is_wp_error( $upload ) ) {
			if ( ! apply_filters( 'woocommerce_rest_suppress_image_upload_error', false, $upload, $this->object_id, $images ) ) {
				throw new \WC_REST_Exception( 'woocommerce_product_image_upload_error', $upload->get_error_message(), 400 );
			} else {
				return;
			}
		}

		$this->id = wc_rest_set_uploaded_image_as_attachment( $upload, $this->object_id );

		if ( ! wp_attachment_is_image( $this->id ) ) {
			/* translators: %s: image ID */
			throw new \WC_REST_Exception( 'woocommerce_product_invalid_image_id', sprintf( __( '#%s is an invalid image ID.', 'woocommerce' ), $this->id ), 400 );
		}
	}

	/**
	 * Update attachment alt text.
	 *
	 * @param string $text Text to set.
	 */
	public function update_alt_text( $text ) {
		if ( ! $this->id ) {
			return;
		}
		update_post_meta( $this->id, '_wp_attachment_image_alt', wc_clean( $text ) );
	}

	/**
	 * Update attachment name.
	 *
	 * @param string $text Text to set.
	 */
	public function update_name( $text ) {
		if ( ! $this->id ) {
			return;
		}
		wp_update_post(
			array(
				'ID'         => $this->id,
				'post_title' => $text,
			)
		);
	}
}
