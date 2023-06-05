<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * AtomicBlock class.
 *
 * @internal
 */
class AtomicBlock extends AbstractBlock {
	/**
	 * Get the editor script data for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 * @return null
	 */
	protected function get_block_type_editor_script( $key = null ) {
		return null;
	}

	/**
	 * Get the editor style handle for this block type.
	 *
	 * @return null
	 */
	protected function get_block_type_editor_style() {
		return null;
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 * @return null
	 */
	protected function get_block_type_script( $key = null ) {
		return null;
	}

	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @return null
	 */
	protected function get_block_type_style() {
		return null;
	}
}
