<?php
/**
 * WooCommerce Coupon Tracking
 *
 * @package WooCommerce\Tracks
 */

/**
 * This class adds actions to track usage of a WooCommerce Coupon.
 */
class WC_Coupon_Tracking {

	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'woocommerce_coupon_object_updated_props', array( $this, 'track_coupon_updated' ), 10, 2 );
	}

	/**
	 * Send a Tracks event when a coupon is updated.
	 *
	 * @param WC_Coupon $coupon        The coupon that has been updated.
	 * @param Array     $updated_props The props of the coupon that have been updated.
	 */
	public function track_coupon_updated( $coupon, $updated_props ) {
		$properties = array(
			'discount_code'        => $coupon->get_code(),
			'free_shipping'        => $coupon->get_free_shipping(),
			'individual_use'       => $coupon->get_individual_use(),
			'exclude_sale_items'   => $coupon->get_exclude_sale_items(),
			'usage_limits_applied' => 0 < intval( $coupon->get_usage_limit() )
									|| 0 < intval( $coupon->get_usage_limit_per_user() )
									|| 0 < intval( $coupon->get_limit_usage_to_x_items() ),
		);

		WC_Tracks::record_event( 'coupon_updated', $properties );
	}
}
