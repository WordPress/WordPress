<?php
namespace Automattic\WooCommerce\Blocks\Integrations;

/**
 * Integration.Interface
 *
 * Integrations must use this interface when registering themselves with blocks,
 */
interface IntegrationInterface {
	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize();

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles();

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles();

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data();
}
