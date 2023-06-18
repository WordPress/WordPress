<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * RelatedProducts class.
 */
class RelatedProducts extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'related-products';

	/**
	 * The Block with its attributes before it gets rendered
	 *
	 * @var array
	 */
	protected $parsed_block;

	/**
	 * Initialize this block type.
	 *
	 * - Hook into WP lifecycle.
	 * - Register the block with WordPress.
	 * - Hook into pre_render_block to update the query.
	 */
	protected function initialize() {
		parent::initialize();
		add_filter(
			'pre_render_block',
			array( $this, 'update_query' ),
			10,
			2
		);

		add_filter(
			'render_block',
			array( $this, 'render_block' ),
			10,
			2
		);

	}

	/**
	 * It isn't necessary register block assets because it is a server side block.
	 */
	protected function register_block_type_assets() {
		return null;
	}

	/**
	 * Update the query for the product query block.
	 *
	 * @param string|null $pre_render   The pre-rendered content. Default null.
	 * @param array       $parsed_block The block being rendered.
	 */
	public function update_query( $pre_render, $parsed_block ) {
		if ( 'core/query' !== $parsed_block['blockName'] ) {
			return;
		}

		$this->parsed_block = $parsed_block;

		if ( ProductQuery::is_woocommerce_variation( $parsed_block ) && 'woocommerce/related-products' === $parsed_block['attrs']['namespace'] ) {
			// Set this so that our product filters can detect if it's a PHP template.
			add_filter(
				'query_loop_block_query_vars',
				array( $this, 'build_query' ),
				10,
				1
			);
		}
	}

	/**
	 * Return a custom query based on attributes, filters and global WP_Query.
	 *
	 * @param WP_Query $query The WordPress Query.
	 * @return array
	 */
	public function build_query( $query ) {
		$parsed_block = $this->parsed_block;
		if ( ! $this->is_related_products_block( $parsed_block ) ) {
			return $query;
		}

		$related_products_ids = $this->get_related_products_ids( $query['posts_per_page'] );
		if ( count( $related_products_ids ) < 1 ) {
			return array();
		}

		return array(
			'post_type'      => 'product',
			'post__in'       => $related_products_ids,
			'post_status'    => 'publish',
			'posts_per_page' => $query['posts_per_page'],
		);
	}

	/**
	 * If there are no related products, return an empty string.
	 *
	 * @param string $content The block content.
	 * @param array  $block The block.
	 *
	 * @return string The block content.
	 */
	public function render_block( string $content, array $block ) {
		if ( ! $this->is_related_products_block( $block ) ) {
			return $content;
		}

		// If there are no related products, render nothing.
		$related_products_ids = $this->get_related_products_ids();
		if ( count( $related_products_ids ) < 1 ) {
			return '';
		}

		return $content;
	}



	/**
	 * Determines whether the block is a related products block.
	 *
	 * @param array $block The block.
	 *
	 * @return bool Whether the block is a related products block.
	 */
	private function is_related_products_block( $block ) {
		if ( ProductQuery::is_woocommerce_variation( $block ) && isset( $block['attrs']['namespace'] ) && 'woocommerce/related-products' === $block['attrs']['namespace'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Get related products ids.
	 * The logic is copied from the core function woocommerce_related_products. https://github.com/woocommerce/woocommerce/blob/ca49caabcba84ce9f60a03c6d3534ec14b350b80/plugins/woocommerce/includes/wc-template-functions.php/#L2039-L2074
	 *
	 * @param number $product_per_page Products per page.
	 * @return array Products ids.
	 */
	private function get_related_products_ids( $product_per_page = 5 ) {
		global $post;

		$product = wc_get_product( $post->ID );

		$related_products = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $product_per_page, $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
		$related_products = wc_products_array_orderby( $related_products, 'rand', 'desc' );

		$related_product_ids = array_map(
			function( $product ) {
				return $product->get_id();
			},
			$related_products
		);

		return $related_product_ids;
	}

}
