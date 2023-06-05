<?php
/**
 * Product Form Traits
 */

namespace Automattic\WooCommerce\Internal\Admin\ProductForm;

defined( 'ABSPATH' ) || exit;

/**
 * ComponentTrait class.
 */
trait ComponentTrait {
	/**
	 * Component ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Plugin ID.
	 *
	 * @var string
	 */
	protected $plugin_id;

	/**
	 * Product form component location.
	 *
	 * @var string
	 */
	protected $location;

	/**
	 * Product form component order.
	 *
	 * @var number
	 */
	protected $order;

	/**
	 * Return id.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Return plugin id.
	 *
	 * @return string
	 */
	public function get_plugin_id() {
		return $this->plugin_id;
	}
}
