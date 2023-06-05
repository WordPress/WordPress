<?php
/**
 * Marketplace suggestions updater
 *
 * Uses WC_Queue to ensure marketplace suggestions data is up to date and cached locally.
 *
 * @package WooCommerce\Classes
 * @since   3.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Marketplace Suggestions Updater
 */
class WC_Marketplace_Updater {

	/**
	 * Setup.
	 */
	public static function load() {
		add_action( 'init', array( __CLASS__, 'init' ) );
	}

	/**
	 * Schedule events and hook appropriate actions.
	 */
	public static function init() {
		add_action( 'woocommerce_update_marketplace_suggestions', array( __CLASS__, 'update_marketplace_suggestions' ) );
	}

	/**
	 * Fetches new marketplace data, updates wc_marketplace_suggestions.
	 */
	public static function update_marketplace_suggestions() {
		$data = get_option(
			'woocommerce_marketplace_suggestions',
			array(
				'suggestions' => array(),
				'updated'     => time(),
			)
		);

		$data['updated'] = time();

		$url     = 'https://woocommerce.com/wp-json/wccom/marketplace-suggestions/1.0/suggestions.json';
		$request = wp_safe_remote_get(
			$url,
			array(
				'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
			)
		);

		if ( is_wp_error( $request ) ) {
			self::retry();
			return update_option( 'woocommerce_marketplace_suggestions', $data, false );
		}

		$body = wp_remote_retrieve_body( $request );
		if ( empty( $body ) ) {
			self::retry();
			return update_option( 'woocommerce_marketplace_suggestions', $data, false );
		}

		$body = json_decode( $body, true );
		if ( empty( $body ) || ! is_array( $body ) ) {
			self::retry();
			return update_option( 'woocommerce_marketplace_suggestions', $data, false );
		}

		$data['suggestions'] = $body;
		return update_option( 'woocommerce_marketplace_suggestions', $data, false );
	}

	/**
	 * Used when an error has occurred when fetching suggestions.
	 * Re-schedules the job earlier than the main weekly one.
	 */
	public static function retry() {
		WC()->queue()->cancel_all( 'woocommerce_update_marketplace_suggestions' );
		WC()->queue()->schedule_single( time() + DAY_IN_SECONDS, 'woocommerce_update_marketplace_suggestions' );
	}
}

WC_Marketplace_Updater::load();
