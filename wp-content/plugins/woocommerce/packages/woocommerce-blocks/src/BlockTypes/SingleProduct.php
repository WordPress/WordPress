<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * SingleProduct class.
 */
class SingleProduct extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'single-product';

	/**
	 * Product ID of the current product to be displayed in the Single Product block.
	 * This is used to replace the global post for the Single Product inner blocks.
	 *
	 * @var int
	 */
	protected $product_id = 0;

	/**
	 * Single Product inner blocks names.
	 * This is used to map all the inner blocks for a Single Product block.
	 *
	 * @var array
	 */
	protected $single_product_inner_blocks_names = [];

	/**
	 * Initialize the block and Hook into the `render_block_context` filter
	 * to update the context with the correct data.
	 *
	 * @var string
	 */
	protected function initialize() {
		parent::initialize();
		add_filter( 'render_block_context', array( $this, 'update_context' ), 10, 3 );
	}


	/**
	 * Render the Single Product block
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content Block content.
	 * @param WP_Block $block Block instance.
	 *
	 * @return string Rendered block output.
	 */
	protected function render( $attributes, $content, $block ) {
		$classname          = $attributes['className'] ?? '';
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classname ) );

		$html = sprintf(
			'<div %1$s>
				%2$s
			</div>',
			$wrapper_attributes,
			$content
		);

		return $html;
	}

	/**
	 * Update the context by injecting the correct post data
	 * for each one of the Single Product inner blocks.
	 *
	 * @param array    $context Block context.
	 * @param array    $block Block attributes.
	 * @param WP_Block $parent_block Block instance.
	 *
	 * @return array Updated block context.
	 */
	public function update_context( $context, $block, $parent_block ) {
		if ( 'woocommerce/single-product' === $block['blockName']
			&& isset( $block['attrs']['productId'] ) ) {
				$this->product_id = $block['attrs']['productId'];

				$this->single_product_inner_blocks_names = array_reverse(
					$this->extract_single_product_inner_block_names( $block )
				);
		}

		$this->replace_post_for_single_product_inner_block( $block, $context );

		return $context;
	}

	/**
	 * Extract the inner block names for the Single Product block. This way it's possible
	 * to map all the inner blocks for a Single Product block and manipulate the data as needed.
	 *
	 * @param array $block The Single Product block or its inner blocks.
	 * @param array $result Array of inner block names.
	 *
	 * @return array Array containing all the inner block names of a Single Product block.
	 */
	protected function extract_single_product_inner_block_names( $block, &$result = [] ) {
		if ( isset( $block['blockName'] ) ) {
			$result[] = $block['blockName'];
		}

		if ( isset( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $inner_block ) {
				$this->extract_single_product_inner_block_names( $inner_block, $result );
			}
		}
		return $result;
	}

	/**
	 * Replace the global post for the Single Product inner blocks and reset it after.
	 *
	 * This is needed because some of the inner blocks may use the global post
	 * instead of fetching the product through the `productId` attribute, so even if the
	 * `productId` is passed to the inner block, it will still use the global post.
	 *
	 * @param array $block Block attributes.
	 * @param array $context Block context.
	 */
	protected function replace_post_for_single_product_inner_block( $block, &$context ) {
		if ( $this->single_product_inner_blocks_names ) {
			$block_name = array_pop( $this->single_product_inner_blocks_names );

			if ( $block_name === $block['blockName'] ) {
				/**
				 * This is a temporary fix to ensure the Post Title and Excerpt blocks work as expected
				 * until Gutenberg versions 15.2 and 15.6 are included in the core of WordPress.
				 *
				 * @see https://github.com/WordPress/gutenberg/pull/48001
				 * @see https://github.com/WordPress/gutenberg/pull/49495
				 */
				if ( 'core/post-excerpt' === $block_name || 'core/post-title' === $block_name ) {
					global $post;
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$post = get_post( $this->product_id );
					setup_postdata( $post );
				}
				$context['postId'] = $this->product_id;
			}

			if ( ! $this->single_product_inner_blocks_names ) {
				wp_reset_postdata();
			}
		}
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 *
	 * @return null This block has no frontend script.
	 */
	protected function get_block_type_script( $key = null ) {
		return null;
	}
}
