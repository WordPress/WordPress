<?php
namespace Elementor\Core\Editor\Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

interface Editor_Loader_Interface {
	/**
	 * Init function purpose is to prepare some stuff that should be available for other methods
	 * and register some hooks
	 *
	 * @return void
	 */
	public function init();

	/**
	 * Register all the scripts for the editor.
	 *
	 * @return void
	 */
	public function register_scripts();

	/**
	 * Enqueue all the scripts for the editor.
	 *
	 * @return void
	 */
	public function enqueue_scripts();

	/**
	 * Register all the styles for the editor.
	 *
	 * @return void
	 */
	public function register_styles();

	/**
	 * Enqueue all the styles for the editor.
	 *
	 * @return void
	 */
	public function enqueue_styles();

	/**
	 * Print the actual initial html for the editor, later on, the scripts takeover and renders the JS apps.
	 *
	 * @return void
	 */
	public function print_root_template();

	/**
	 * Register additional templates that are required for the marionette part of the application
	 *
	 * @return void
	 */
	public function register_additional_templates();
}
