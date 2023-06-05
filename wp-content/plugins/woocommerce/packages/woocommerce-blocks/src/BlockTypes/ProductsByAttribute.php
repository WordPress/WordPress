<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ProductsByAttribute class.
 */
class ProductsByAttribute extends AbstractProductGrid {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'products-by-attribute';

	/**
	 * Set args specific to this block
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_block_query_args( &$query_args ) {
		if ( ! empty( $this->attributes['attributes'] ) ) {
			$taxonomy = sanitize_title( $this->attributes['attributes'][0]['attr_slug'] );
			$terms    = wp_list_pluck( $this->attributes['attributes'], 'id' );

			$query_args['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'terms'    => array_map( 'absint', $terms ),
				'field'    => 'term_id',
				'operator' => 'all' === $this->attributes['attrOperator'] ? 'AND' : 'IN',
			);
		}
	}

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_block_type_attributes() {
		return array(
			'align'             => $this->get_schema_align(),
			'alignButtons'      => $this->get_schema_boolean( false ),
			'attributes'        => array(
				'type'    => 'array',
				'items'   => array(
					'type'       => 'object',
					'properties' => array(
						'id'        => array(
							'type' => 'number',
						),
						'attr_slug' => array(
							'type' => 'string',
						),
					),
				),
				'default' => array(),
			),
			'attrOperator'      => array(
				'type'    => 'string',
				'default' => 'any',
			),
			'className'         => $this->get_schema_string(),
			'columns'           => $this->get_schema_number( wc_get_theme_support( 'product_blocks::default_columns', 3 ) ),
			'contentVisibility' => $this->get_schema_content_visibility(),
			'orderby'           => $this->get_schema_orderby(),
			'rows'              => $this->get_schema_number( wc_get_theme_support( 'product_blocks::default_rows', 3 ) ),
			'isPreview'         => $this->get_schema_boolean( false ),
			'stockStatus'       => array(
				'type'    => 'array',
				'default' => array_keys( wc_get_product_stock_status_options() ),
			),
		);
	}
}
