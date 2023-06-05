<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * AbstractInnerBlock class.
 */
abstract class AbstractInnerBlock extends AbstractBlock {

	/**
	 * Is this inner block lazy loaded? this helps us know if we should load its frontend script ot not.
	 *
	 * @var boolean
	 */
	protected $is_lazy_loaded = true;

	/**
	 * Registers the block type with WordPress using the metadata file.
	 *
	 * The registration using metadata is now recommended. And it's required for "Inner Blocks" to
	 * fix the issue of missing translations in the inspector (in the Editor mode)
	 */
	protected function register_block_type() {
		$block_settings = [
			'render_callback' => $this->get_block_type_render_callback(),
			'editor_style'    => $this->get_block_type_editor_style(),
			'style'           => $this->get_block_type_style(),
		];

		if ( isset( $this->api_version ) && '2' === $this->api_version ) {
			$block_settings['api_version'] = 2;
		}

		$metadata_path = $this->asset_api->get_block_metadata_path( $this->block_name, 'inner-blocks/' );
		// Prefer to register with metadata if the path is set in the block's class.
		register_block_type_from_metadata(
			$metadata_path,
			$block_settings
		);
	}

	/**
	 * For lazy loaded inner blocks, we don't want to enqueue the script but rather leave it for webpack to do that.
	 *
	 * @see $this->register_block_type()
	 * @param string $key Data to get, or default to everything.
	 * @return array|string|null
	 */
	protected function get_block_type_script( $key = null ) {

		if ( $this->is_lazy_loaded ) {
			return null;
		}

		return parent::get_block_type_script( $key );
	}

}
