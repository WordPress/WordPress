<?php

namespace WpMatomo\Ecommerce;

/**
 * this class is required only for phpunit tests.
 * It allow to change the visibility of some methods of the Base class
 * and so allow to test them in the unit tests
 *
 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
 */
class MatomoTestEcommerce extends Base {

	public $should_track_background = false;

	public $supports_delayed_tracking = false;

	public $session_data = [];

	public $tracked_orders = [];

	/**
	 * Render public the wrap_script method. Required for the unit tests
	 *
	 * @param string $script
	 *
	 * @return string
	 * @see Base::wrap_script()
	 */
	public function wrap_script( $script ) {
		return parent::wrap_script( $script );
	}

	/**
	 * Render public the wrap_script method. Required for the unit tests
	 *
	 * @param [] $params
	 *
	 * @return string
	 * @see Base::make_matomo_js_tracker_call()
	 */
	public function make_matomo_js_tracker_call( $params ) {
		return parent::make_matomo_js_tracker_call( $params );
	}

	protected function should_track_background() {
		return $this->should_track_background;
	}

	protected function get_tracking_calls_in_session() {
		return $this->session_data['ajax_calls'];
	}

	protected function add_tracking_calls_to_session( $data ) {
		$this->session_data['ajax_calls'][] = $data;
	}

	protected function remove_tracking_calls_in_session() {
		$this->session_data['ajax_calls'] = [];
	}

	/**
	 * @return bool
	 */
	public function supports_delayed_tracking() {
		return $this->supports_delayed_tracking;
	}

	public function set_order_been_tracked( $order_id ) {
		$this->tracked_orders[] = $order_id;
	}

	public function has_order_been_tracked_already( $order_id ) {
		return in_array( $order_id, $this->tracked_orders, true );
	}
}
