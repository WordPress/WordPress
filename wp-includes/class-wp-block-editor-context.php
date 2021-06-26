<?php
/**
 * Blocks API: WP_Block_Editor_Context class
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Class representing a current block editor context.
 *
 * The expectation is that block editor can have a different set
 * of requirements on every screen where it is used. This class
 * allows to define supporting settings that can be used with filters.
 *
 * @since 5.8.0
 */
final class WP_Block_Editor_Context {
	/**
	 * Post being edited. Optional.
	 *
	 * @since 5.8.0
	 *
	 * @var WP_Post|null
	 */
	public $post = null;

	/**
	 * Constructor.
	 *
	 * Populates optional properties for a given block editor context.
	 *
	 * @since 5.8.0
	 *
	 * @param array $settings The list of optional settings to expose in a given context.
	 */
	public function __construct( array $settings = array() ) {
		if ( isset( $settings['post'] ) ) {
			$this->post = $settings['post'];
		}
	}
}
