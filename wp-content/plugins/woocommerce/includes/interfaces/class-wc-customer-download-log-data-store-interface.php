<?php
/**
 * Customer Download Log Data Store Interface
 *
 * @version 3.3.0
 * @package WooCommerce\Interface
 */

/**
 * WC Customer Download Log Data Store Interface.
 *
 * @version  3.3.0
 */
interface WC_Customer_Download_Log_Data_Store_Interface {

	/**
	 * Get array of download log ids by specified args.
	 *
	 * @param  array $args Arguments.
	 * @return array of WC_Customer_Download_Log
	 */
	public function get_download_logs( $args = array() );

	/**
	 * Get logs for a specific download permission.
	 *
	 * @param  int $permission_id Permission ID.
	 * @return array
	 */
	public function get_download_logs_for_permission( $permission_id );

}
