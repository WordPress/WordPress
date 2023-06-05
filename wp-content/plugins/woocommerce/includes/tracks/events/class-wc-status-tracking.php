<?php
/**
 * WooCommerce Status Tracking
 *
 * @package WooCommerce\Tracks
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Orders.
 */
class WC_Status_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'track_status_view' ), 10 );
	}

	/**
	 * Add Tracks events to the status page.
	 */
	public function track_status_view() {
		if ( isset( $_GET['page'] ) && 'wc-status' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {

			$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'status';

			WC_Tracks::record_event(
				'status_view',
				array(
					'tab'       => $tab,
					'tool_used' => isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : null,
				)
			);

			if ( 'status' === $tab ) {
				wc_enqueue_js(
					"
					$( 'a.debug-report' ).on( 'click', function() {
						window.wcTracks.recordEvent( 'status_view_reports' );
					} );
				"
				);
			}
		}
	}
}
