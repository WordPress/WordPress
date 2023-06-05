<?php
/**
 * WooCommerce Import Tracking
 *
 * @package WooCommerce\Tracks
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Imports.
 */
class WC_Importer_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'product_page_product_importer', array( $this, 'track_product_importer' ) );
	}

	/**
	 * Route product importer action to the right callback.
	 *
	 * @return void
	 */
	public function track_product_importer() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['step'] ) ) {
			return;
		}

		if ( 'import' === $_REQUEST['step'] ) {
			return $this->track_product_importer_start();
		}

		if ( 'done' === $_REQUEST['step'] ) {
			return $this->track_product_importer_complete();
		}
		// phpcs:enable
	}

	/**
	 * Send a Tracks event when the product importer is started.
	 *
	 * @return void
	 */
	public function track_product_importer_start() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['file'] ) || ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		$properties = array(
			'update_existing' => isset( $_REQUEST['update_existing'] ) ? (bool) $_REQUEST['update_existing'] : false,
			'delimiter'       => empty( $_REQUEST['delimiter'] ) ? ',' : wc_clean( wp_unslash( $_REQUEST['delimiter'] ) ),
		);
		// phpcs:enable

		WC_Tracks::record_event( 'product_import_start', $properties );
	}

	/**
	 * Send a Tracks event when the product importer has finished.
	 *
	 * @return void
	 */
	public function track_product_importer_complete() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['nonce'] ) ) {
			return;
		}

		$properties = array(
			'imported' => isset( $_GET['products-imported'] ) ? absint( $_GET['products-imported'] ) : 0,
			'updated'  => isset( $_GET['products-updated'] ) ? absint( $_GET['products-updated'] ) : 0,
			'failed'   => isset( $_GET['products-failed'] ) ? absint( $_GET['products-failed'] ) : 0,
			'skipped'  => isset( $_GET['products-skipped'] ) ? absint( $_GET['products-skipped'] ) : 0,
		);
		// phpcs:enable

		WC_Tracks::record_event( 'product_import_complete', $properties );
	}
}
