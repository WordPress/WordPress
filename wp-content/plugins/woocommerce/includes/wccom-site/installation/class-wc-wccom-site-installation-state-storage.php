<?php
/**
 * State storage for the WCCOM Site installation process.
 *
 * @package WooCommerce\WCCOM
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_WCCOM_Site_Installation_State_Storage class
 */
class WC_WCCOM_Site_Installation_State_Storage {

	/**
	 * Get state from storage.
	 *
	 * @param int $product_id The product ID.
	 * @return WC_WCCOM_Site_Installation_State|null
	 */
	public static function get_state( $product_id ) : ?WC_WCCOM_Site_Installation_State {
		$storage_key = self::get_storage_key( $product_id );
		$data        = get_option( $storage_key );

		if ( ! is_array( $data ) ) {
			return null;
		}

		$installation_state = WC_WCCOM_Site_Installation_State::initiate_existing(
			$product_id,
			$data['idempotency_key'],
			$data['last_step_name'],
			$data['last_step_status'],
			$data['last_step_error'],
			$data['started_date']
		);

		$installation_state->set_product_type( $data['product_type'] ?? null );
		$installation_state->set_product_name( $data['product_name'] ?? null );
		$installation_state->set_download_url( $data['download_url'] ?? null );
		$installation_state->set_download_path( $data['download_path'] ?? null );
		$installation_state->set_unpacked_path( $data['unpacked_path'] ?? null );
		$installation_state->set_installed_path( $data['installed_path'] ?? null );
		$installation_state->set_already_installed_plugin_info( $data['already_installed_plugin_info'] ?? null );

		return $installation_state;
	}

	/**
	 * Save state to storage.
	 *
	 * @param WC_WCCOM_Site_Installation_State $state The state to save.
	 * @return bool
	 */
	public static function save_state( WC_WCCOM_Site_Installation_State $state ) : bool {
		$storage_key = self::get_storage_key( $state->get_product_id() );

		return update_option(
			$storage_key,
			array(
				'product_id'                    => $state->get_product_id(),
				'idempotency_key'               => $state->get_idempotency_key(),
				'last_step_name'                => $state->get_last_step_name(),
				'last_step_status'              => $state->get_last_step_status(),
				'last_step_error'               => $state->get_last_step_error(),
				'product_type'                  => $state->get_product_type(),
				'product_name'                  => $state->get_product_name(),
				'download_url'                  => $state->get_download_url(),
				'download_path'                 => $state->get_download_path(),
				'unpacked_path'                 => $state->get_unpacked_path(),
				'installed_path'                => $state->get_installed_path(),
				'already_installed_plugin_info' => $state->get_already_installed_plugin_info(),
				'started_date'                  => $state->get_started_date(),
			)
		);
	}

	/**
	 * Delete state from storage.
	 *
	 * @param WC_WCCOM_Site_Installation_State $state The state to delete.
	 * @return bool
	 */
	public static function delete_state( WC_WCCOM_Site_Installation_State $state ) : bool {
		$storage_key = self::get_storage_key( $state->get_product_id() );

		return delete_option( $storage_key );
	}

	/**
	 * Get the storage key for a product ID.
	 *
	 * @param int $product_id The product ID.
	 * @return string
	 */
	protected static function get_storage_key( $product_id ) : string {
		return sprintf( 'wccom-product-installation-state-%d', $product_id );
	}
}

