<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

/**
 * TermSchema class.
 */
class TermSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'term';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'term';

	/**
	 * Term properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'id'          => array(
				'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'name'        => array(
				'description' => __( 'Term name.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'slug'        => array(
				'description' => __( 'String based identifier for the term.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'description' => array(
				'description' => __( 'Term description.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'parent'      => array(
				'description' => __( 'Parent term ID, if applicable.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'count'       => array(
				'description' => __( 'Number of objects (posts of any type) assigned to the term.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		];
	}

	/**
	 * Convert a term object into an object suitable for the response.
	 *
	 * @param \WP_Term $term Term object.
	 * @return array
	 */
	public function get_item_response( $term ) {
		return [
			'id'          => (int) $term->term_id,
			'name'        => $this->prepare_html_response( $term->name ),
			'slug'        => $term->slug,
			'description' => $this->prepare_html_response( $term->description ),
			'parent'      => (int) $term->parent,
			'count'       => (int) $term->count,
		];
	}
}
