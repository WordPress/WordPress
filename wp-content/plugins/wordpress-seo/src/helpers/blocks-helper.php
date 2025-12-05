<?php

namespace Yoast\WP\SEO\Helpers;

use WP_Block_Parser_Block;

/**
 * A helper object for blocks.
 */
class Blocks_Helper {

	/**
	 * Holds the Post_Helper instance.
	 *
	 * @var Post_Helper
	 */
	private $post;

	/**
	 * Constructs a Blocks_Helper instance.
	 *
	 * @codeCoverageIgnore It handles dependencies.
	 *
	 * @param Post_Helper $post The post helper.
	 */
	public function __construct( Post_Helper $post ) {
		$this->post = $post;
	}

	/**
	 * Returns all blocks in a given post.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array The blocks in a block-type => WP_Block_Parser_Block[] format.
	 */
	public function get_all_blocks_from_post( $post_id ) {
		if ( ! $this->has_blocks_support() ) {
			return [];
		}

		$post = $this->post->get_post( $post_id );
		return $this->get_all_blocks_from_content( $post->post_content );
	}

	/**
	 * Returns all blocks in a given content.
	 *
	 * @param string $content The content.
	 *
	 * @return array The blocks in a block-type => WP_Block_Parser_Block[] format.
	 */
	public function get_all_blocks_from_content( $content ) {
		if ( ! $this->has_blocks_support() ) {
			return [];
		}

		$collection = [];
		$blocks     = \parse_blocks( $content );
		return $this->collect_blocks( $blocks, $collection );
	}

	/**
	 * Checks if the installation has blocks support.
	 *
	 * @codeCoverageIgnore It only checks if a WordPress function exists.
	 *
	 * @return bool True when function parse_blocks exists.
	 */
	protected function has_blocks_support() {
		return \function_exists( 'parse_blocks' );
	}

	/**
	 * Collects an array of blocks into an organised collection.
	 *
	 * @param WP_Block_Parser_Block[] $blocks     The blocks.
	 * @param array                   $collection The collection.
	 *
	 * @return array The blocks in a block-type => WP_Block_Parser_Block[] format.
	 */
	private function collect_blocks( $blocks, $collection ) {
		foreach ( $blocks as $block ) {
			if ( empty( $block['blockName'] ) ) {
				continue;
			}

			if ( ! isset( $collection[ $block['blockName'] ] ) || ! \is_array( $collection[ $block['blockName'] ] ) ) {
				$collection[ $block['blockName'] ] = [];
			}
			$collection[ $block['blockName'] ][] = $block;

			if ( isset( $block['innerBlocks'] ) ) {
				$collection = $this->collect_blocks( $block['innerBlocks'], $collection );
			}
		}

		return $collection;
	}
}
