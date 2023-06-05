<?php
/**
 * Webhook Data Store Interface
 *
 * @version  3.2.0
 * @package  WooCommerce\Interface
 */

/**
 * WooCommerce Webhook data store interface.
 */
interface WC_Webhook_Data_Store_Interface {

	/**
	 * Get API version number.
	 *
	 * @since  3.2.0
	 * @param  string $api_version REST API version.
	 * @return int
	 */
	public function get_api_version_number( $api_version );

	/**
	 * Get all webhooks IDs.
	 *
	 * @since  3.2.0
	 * @throws InvalidArgumentException If a $status value is passed in that is not in the known wc_get_webhook_statuses() keys.
	 * @param  string $status Optional - status to filter results by. Must be a key in return value of @see wc_get_webhook_statuses(). @since 3.6.0.
	 * @return int[]
	 */
	public function get_webhooks_ids( $status = '' );
}
