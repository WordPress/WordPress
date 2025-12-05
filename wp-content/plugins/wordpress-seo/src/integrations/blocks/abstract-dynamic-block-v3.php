<?php

namespace Yoast\WP\SEO\Integrations\Blocks;

use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Dynamic_Block class.
 */
abstract class Dynamic_Block_V3 implements Integration_Interface {

	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	protected $block_name;

	/**
	 * The editor script for the block.
	 *
	 * @var string
	 */
	protected $script;

	/**
	 * The base path for the block.
	 *
	 * @var string
	 */
	protected $base_path;

	/**
	 *  Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [];
	}

	/**
	 * Initializes the integration.
	 *
	 * Integrations hooking on `init` need to have a priority of 11 or higher to
	 * ensure that they run, as priority 10 is used by the loader to load the integrations.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'init', [ $this, 'register_block' ], 11 );
	}

	/**
	 * Registers the block.
	 *
	 * @return void
	 */
	public function register_block() {
		\register_block_type(
			$this->base_path . $this->block_name . '/block.json',
			[
				'editor_script'   => $this->script,
				'render_callback' => [ $this, 'present' ],
			]
		);
	}

	/**
	 * Presents the block output. This is abstract because in the loop we need to be able to build the data for the
	 * presenter in the last moment.
	 *
	 * @param array<string, bool|string|int|array> $attributes The block attributes.
	 *
	 * @return string The block output.
	 */
	abstract public function present( $attributes );

	/**
	 * Checks whether the links in the block should have target="blank".
	 *
	 * This is needed because when the editor is loaded in an Iframe the link needs to open in a different browser window.
	 * We don't want this behaviour in the front-end and the way to check this is to check if the block is rendered in a REST request with the `context` set as 'edit'. Thus being in the editor.
	 *
	 * @return bool returns if the block should be opened in another window.
	 */
	protected function should_link_target_blank(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['context'] ) && \is_string( $_GET['context'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are only strictly comparing.
			if ( \wp_unslash( $_GET['context'] ) === 'edit' ) {
				return true;
			}
		}
		return false;
	}
}
