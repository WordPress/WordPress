<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

/**
 * ImageAttachmentSchema class.
 */
class ImageAttachmentSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'image';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'image';

	/**
	 * Product schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'id'        => [
				'description' => __( 'Image ID.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => [ 'view', 'edit' ],
			],
			'src'       => [
				'description' => __( 'Full size image URL.', 'woocommerce' ),
				'type'        => 'string',
				'format'      => 'uri',
				'context'     => [ 'view', 'edit' ],
			],
			'thumbnail' => [
				'description' => __( 'Thumbnail URL.', 'woocommerce' ),
				'type'        => 'string',
				'format'      => 'uri',
				'context'     => [ 'view', 'edit' ],
			],
			'srcset'    => [
				'description' => __( 'Thumbnail srcset for responsive images.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'sizes'     => [
				'description' => __( 'Thumbnail sizes for responsive images.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'name'      => [
				'description' => __( 'Image name.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'alt'       => [
				'description' => __( 'Image alternative text.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
		];
	}

	/**
	 * Convert a WooCommerce product into an object suitable for the response.
	 *
	 * @param int $attachment_id Image attachment ID.
	 * @return array|null
	 */
	public function get_item_response( $attachment_id ) {
		if ( ! $attachment_id ) {
			return null;
		}

		$attachment = wp_get_attachment_image_src( $attachment_id, 'full' );

		if ( ! is_array( $attachment ) ) {
			return [];
		}

		$thumbnail = wp_get_attachment_image_src( $attachment_id, 'woocommerce_thumbnail' );

		return [
			'id'        => (int) $attachment_id,
			'src'       => current( $attachment ),
			'thumbnail' => current( $thumbnail ),
			'srcset'    => (string) wp_get_attachment_image_srcset( $attachment_id, 'full' ),
			'sizes'     => (string) wp_get_attachment_image_sizes( $attachment_id, 'full' ),
			'name'      => get_the_title( $attachment_id ),
			'alt'       => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		];
	}

}
