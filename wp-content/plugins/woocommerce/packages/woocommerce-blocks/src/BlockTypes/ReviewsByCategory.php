<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ReviewsByCategory class.
 */
class ReviewsByCategory extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'reviews-by-category';

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @see $this->register_block_type()
	 * @param string $key Data to get, or default to everything.
	 * @return array|string
	 */
	protected function get_block_type_script( $key = null ) {
		$script = [
			'handle'       => 'wc-reviews-block-frontend',
			'path'         => $this->asset_api->get_block_asset_build_path( 'reviews-frontend' ),
			'dependencies' => [],
		];
		return $key ? $script[ $key ] : $script;
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
		$this->asset_data_registry->add( 'reviewRatingsEnabled', wc_review_ratings_enabled(), true );
		$this->asset_data_registry->add( 'showAvatars', '1' === get_option( 'show_avatars' ), true );
	}
}
