<?php
/**
 * Customer Download Data Store Interface
 *
 * @version 3.0.0
 * @package WooCommerce\Interface
 */

/**
 * WC Customer Download Data Store Interface.
 *
 * @version  3.0.0
 */
interface WC_Customer_Download_Data_Store_Interface {

	/**
	 * Method to delete a download permission from the database by ID.
	 *
	 * @param int $id Download Permission ID.
	 */
	public function delete_by_id( $id );

	/**
	 * Method to delete a download permission from the database by order ID.
	 *
	 * @param int $id Order ID.
	 */
	public function delete_by_order_id( $id );

	/**
	 * Method to delete a download permission from the database by download ID.
	 *
	 * @param int $id Download ID.
	 */
	public function delete_by_download_id( $id );

	/**
	 * Get array of download ids by specified args.
	 *
	 * @param  array $args Arguments.
	 * @return array of WC_Customer_Download
	 */
	public function get_downloads( $args = array() );

	/**
	 * Update download ids if the hash changes.
	 *
	 * @param  int    $product_id Product ID.
	 * @param  string $old_id Old ID.
	 * @param  string $new_id New ID.
	 */
	public function update_download_id( $product_id, $old_id, $new_id );

	/**
	 * Get a customers downloads.
	 *
	 * @param  int $customer_id Customer ID.
	 * @return array
	 */
	public function get_downloads_for_customer( $customer_id );

	/**
	 * Update user prop for downloads based on order id.
	 *
	 * @param  int    $order_id Order ID.
	 * @param  int    $customer_id Customer ID.
	 * @param  string $email Email Address.
	 */
	public function update_user_by_order_id( $order_id, $customer_id, $email );
}
