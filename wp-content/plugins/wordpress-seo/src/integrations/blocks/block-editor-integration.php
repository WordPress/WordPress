<?php

namespace Yoast\WP\SEO\Integrations\Blocks;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin\Post_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Block_Editor_Integration class to enqueue the block editor assets also for the iframe.
 */
class Block_Editor_Integration implements Integration_Interface {

	/**
	 * Represents the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * {@inheritDoc}
	 *
	 * @return array<Post_Conditional>
	 */
	public static function get_conditionals() {
		return [ Post_Conditional::class ];
	}

	/**
	 * Constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager The asset manager.
	 */
	public function __construct( WPSEO_Admin_Asset_Manager $asset_manager ) {
		$this->asset_manager = $asset_manager;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'enqueue_block_assets', [ $this, 'enqueue' ] );
	}

	/**
	 * Enqueues the assets for the block editor.
	 *
	 * @return void
	 */
	public function enqueue() {
		$this->asset_manager->enqueue_style( 'block-editor' );
	}
}
