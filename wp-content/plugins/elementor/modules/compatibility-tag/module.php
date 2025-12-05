<?php
namespace Elementor\Modules\CompatibilityTag;

use Elementor\Plugin;
use Elementor\Core\Utils\Version;
use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Inspired By WooCommerce.
 *
 * @link  https://github.com/woocommerce/woocommerce/blob/master/includes/admin/plugin-updates/class-wc-plugin-updates.php
 */
class Module extends Base_Module {
	/**
	 * This is the header used by extensions to show testing.
	 *
	 * @var string
	 */
	const PLUGIN_VERSION_TESTED_HEADER = 'Elementor tested up to';

	/**
	 * @return string
	 */
	protected function get_plugin_header() {
		return static::PLUGIN_VERSION_TESTED_HEADER;
	}

	/**
	 * @return string
	 */
	protected function get_plugin_label() {
		return esc_html__( 'Elementor', 'elementor' );
	}

	/**
	 * @return string
	 */
	protected function get_plugin_name() {
		return ELEMENTOR_PLUGIN_BASE;
	}

	/**
	 * @return string
	 */
	protected function get_plugin_version() {
		return ELEMENTOR_VERSION;
	}

	/**
	 * @return Collection
	 */
	protected function get_plugins_to_check() {
		return parent::get_plugins_to_check()
			->merge( $this->get_plugins_with_plugin_title_in_their_name() );
	}

	/**
	 * Get all the plugins that has the name of the current plugin in their name.
	 *
	 * @return Collection
	 */
	private function get_plugins_with_plugin_title_in_their_name() {
		return Plugin::$instance->wp
			->get_plugins()
			->except( [
				'elementor/elementor.php',
				'elementor-beta/elementor-beta.php',
				'block-builder/block-builder.php',
			] )
			->filter( function ( array $data ) {
				return false !== strpos( strtolower( $data['Name'] ), 'elementor' );
			} );
	}
}
