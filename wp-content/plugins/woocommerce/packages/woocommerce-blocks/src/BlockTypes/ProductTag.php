<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ProductTag class.
 */
class ProductTag extends AbstractProductGrid {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-tag';

	/**
	 * Set args specific to this block.
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_block_query_args( &$query_args ) {
		if ( ! empty( $this->attributes['tags'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_tag',
				'terms'    => array_map( 'absint', $this->attributes['tags'] ),
				'field'    => 'term_id',
				'operator' => isset( $this->attributes['tagOperator'] ) && 'any' === $this->attributes['tagOperator'] ? 'IN' : 'AND',
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
			'className'         => $this->get_schema_string(),
			'columns'           => $this->get_schema_number( wc_get_theme_support( 'product_blocks::default_columns', 3 ) ),
			'rows'              => $this->get_schema_number( wc_get_theme_support( 'product_blocks::default_rows', 3 ) ),
			'contentVisibility' => $this->get_schema_content_visibility(),
			'align'             => $this->get_schema_align(),
			'alignButtons'      => $this->get_schema_boolean( false ),
			'orderby'           => $this->get_schema_orderby(),
			'tags'              => $this->get_schema_list_ids(),
			'tagOperator'       => array(
				'type'    => 'string',
				'default' => 'any',
			),
			'isPreview'         => $this->get_schema_boolean( false ),
			'stockStatus'       => array_keys( wc_get_product_stock_status_options() ),
		);
	}

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		parent::enqueue_data( $attributes );

		$tag_count = wp_count_terms( 'product_tag' );

		$this->asset_data_registry->add( 'hasTags', $tag_count > 0, true );
		$this->asset_data_registry->add( 'limitTags', $tag_count > 100, true );
	}
}
