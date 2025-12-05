<?php
namespace Elementor\Core;

use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This class is responsible for the interaction with WordPress Core API.
 * The main benefit is making it easy to mock in testing
 * and it can help to create unit tests without the hustle of mocking WordPress itself.
 */
class Wp_Api {
	/**
	 * @var Collection
	 */
	private $plugins;

	/**
	 * @return Collection
	 */
	public function get_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! $this->plugins ) {
			$this->plugins = new Collection( get_plugins() );
		}

		return $this->plugins;
	}

	/**
	 * @return Collection
	 */
	public function get_active_plugins() {
		return $this->get_plugins()
			->only( get_option( 'active_plugins' ) );
	}

	/**
	 * @return object|array
	 */
	public function plugins_api( $action, $args ) {
		return plugins_api( $action, $args );
	}

	/**
	 * @return bool
	 */
	public function is_plugin_active( $plugin ) {
		return is_plugin_active( $plugin );
	}

	/**
	 * @return bool|int|null|true
	 */
	public function activate_plugin( $plugin ) {
		return activate_plugin( $plugin );
	}

	public function wp_attachment_is_image( $post = null ) {
		return wp_attachment_is_image( $post );
	}
}
