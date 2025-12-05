<?php
namespace Elementor\Modules\Home\Transformations\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Isolation\Elementor_Adapter;
use Elementor\Core\Isolation\Elementor_Adapter_Interface;
use Elementor\Core\Isolation\Plugin_Status_Adapter;
use Elementor\Core\Isolation\Plugin_Status_Adapter_Interface;
use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Wordpress_Adapter_Interface;

abstract class Transformations_Abstract {

	protected Wordpress_Adapter_Interface $wordpress_adapter;
	protected Plugin_Status_Adapter_Interface $plugin_status_adapter;
	protected Elementor_Adapter_Interface $elementor_adapter;

	/**
	 * @param $args ?array{
	 *     wordpress_adapter: Wordpress_Adapter_Interface,
	 *     plugin_status_adapter: Plugin_Status_Adapter_Interface,
	 *     elementor_adapter: Elementor_Adapter_Interface,
	 * } the adapters to use in the transformations
	 */
	public function __construct( array $args = [] ) {
		$this->wordpress_adapter = $args['wordpress_adapter'] ?? new Wordpress_Adapter();
		$this->plugin_status_adapter = $args['plugin_status_adapter'] ?? new Plugin_Status_Adapter( $this->wordpress_adapter );
		$this->elementor_adapter = $args['elementor_adapter'] ?? new Elementor_Adapter();
	}

	protected function get_tier() {
		$tier = $this->elementor_adapter->get_tier();

		return apply_filters( 'elementor/admin/homescreen_promotion_tier', $tier ) ?? $tier;
	}

	abstract public function transform( array $home_screen_data ): array;
}
