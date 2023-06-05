<?php
/**
 * Legacy Webhook
 *
 * Legacy and deprecated functions are here to keep the WC_Legacy_Webhook class clean.
 * This class will be removed in future versions.
 *
 * @version  3.2.0
 * @package  WooCommerce\Classes
 * @category Class
 * @author   Automattic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy Webhook class.
 */
abstract class WC_Legacy_Webhook extends WC_Data {

	/**
	 * Magic __isset method for backwards compatibility. Legacy properties which could be accessed directly in the past.
	 *
	 * @param  string $key Item to check.
	 * @return bool
	 */
	public function __isset( $key ) {
		$legacy_keys = array(
			'id',
			'status',
			'post_data',
			'delivery_url',
			'secret',
			'topic',
			'hooks',
			'resource',
			'event',
			'failure_count',
			'api_version',
		);

		if ( in_array( $key, $legacy_keys, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic __get method for backwards compatibility. Maps legacy vars to new getters.
	 *
	 * @param  string $key Item to get.
	 * @return mixed
	 */
	public function __get( $key ) {
		wc_doing_it_wrong( $key, 'Webhook properties should not be accessed directly.', '3.2' );

		switch ( $key ) {
			case 'id' :
				$value = $this->get_id();
				break;
			case 'status' :
				$value = $this->get_status();
				break;
			case 'post_data' :
				$value = null;
				break;
			case 'delivery_url' :
				$value = $this->get_delivery_url();
				break;
			case 'secret' :
				$value = $this->get_secret();
				break;
			case 'topic' :
				$value = $this->get_topic();
				break;
			case 'hooks' :
				$value = $this->get_hooks();
				break;
			case 'resource' :
				$value = $this->get_resource();
				break;
			case 'event' :
				$value = $this->get_event();
				break;
			case 'failure_count' :
				$value = $this->get_failure_count();
				break;
			case 'api_version' :
				$value = $this->get_api_version();
				break;

			default :
				$value = '';
				break;
		} // End switch().

		return $value;
	}

	/**
	 * Get the post data for the webhook.
	 *
	 * @deprecated 3.2.0
	 * @since      2.2
	 * @return     null|WP_Post
	 */
	public function get_post_data() {
		wc_deprecated_function( 'WC_Webhook::get_post_data', '3.2' );

		return null;
	}

	/**
	 * Update the webhook status.
	 *
	 * @deprecated 3.2.0
	 * @since      2.2.0
	 * @param      string $status Status to set.
	 */
	public function update_status( $status ) {
		wc_deprecated_function( 'WC_Webhook::update_status', '3.2', 'WC_Webhook::set_status' );

		$this->set_status( $status );
		$this->save();
	}
}
