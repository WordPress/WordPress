<?php
/**
 * WooCommerce Coupons Tracking
 *
 * @package WooCommerce\Tracks
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Orders.
 */
class WC_Coupons_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'load-edit.php', array( $this, 'tracks_coupons_events' ), 10 );
	}

	/**
	 * Add a listener on the "Apply" button to track bulk actions.
	 */
	public function tracks_coupons_bulk_actions() {
		wc_enqueue_js(
			"
			function onApplyBulkActions( event ) {
				var id = event.data.id;
				var action = $( '#' + id ).val();
				
				if ( action && '-1' !== action ) {
					window.wcTracks.recordEvent( 'coupons_view_bulk_action', {
						action: action
					} );
				}
			}
			$( '#doaction' ).on( 'click', { id: 'bulk-action-selector-top' }, onApplyBulkActions );
			$( '#doaction2' ).on( 'click', { id: 'bulk-action-selector-bottom' }, onApplyBulkActions );
		"
		);
	}

	/**
	 * Track page view events.
	 */
	public function tracks_coupons_events() {
		if ( isset( $_GET['post_type'] ) && 'shop_coupon' === $_GET['post_type'] ) {

			$this->tracks_coupons_bulk_actions();

			WC_Tracks::record_event(
				'coupons_view',
				array(
					'status' => isset( $_GET['post_status'] ) ? sanitize_text_field( wp_unslash( $_GET['post_status'] ) ) : 'all',
				)
			);

			if ( isset( $_GET['filter_action'] ) && 'Filter' === sanitize_text_field( wp_unslash( $_GET['filter_action'] ) ) && isset( $_GET['coupon_type'] ) ) {
				WC_Tracks::record_event(
					'coupons_filter',
					array(
						'filter' => 'coupon_type',
						'value'  => sanitize_text_field( wp_unslash( $_GET['coupon_type'] ) ),
					)
				);
			}

			if ( isset( $_GET['s'] ) && 0 < strlen( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) ) {
				WC_Tracks::record_event( 'coupons_search' );
			}
		}
	}
}
