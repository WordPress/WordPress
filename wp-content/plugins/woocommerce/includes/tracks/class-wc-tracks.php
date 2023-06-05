<?php
/**
 * PHP Tracks Client
 *
 * @package WooCommerce\Tracks
 */

/**
 * WC_Tracks class.
 */
class WC_Tracks {

	/**
	 * Tracks event name prefix.
	 */
	const PREFIX = 'wcadmin_';

	/**
	 * Get total product counts.
	 *
	 * @return int Number of products.
	 */
	public static function get_products_count() {
		$product_counts = WC_Tracker::get_product_counts();
		return $product_counts['total'];
	}

	/**
	 * Gather blog related properties.
	 *
	 * @param int $user_id User id.
	 * @return array Blog details.
	 */
	public static function get_blog_details( $user_id ) {
		$blog_details = get_transient( 'wc_tracks_blog_details' );
		if ( false === $blog_details ) {
			$blog_details = array(
				'url'            => home_url(),
				'blog_lang'      => get_user_locale( $user_id ),
				'blog_id'        => class_exists( 'Jetpack_Options' ) ? Jetpack_Options::get_option( 'id' ) : null,
				'products_count' => self::get_products_count(),
				'wc_version'     => WC()->version,
			);
			set_transient( 'wc_tracks_blog_details', $blog_details, DAY_IN_SECONDS );
		}
		return $blog_details;
	}

	/**
	 * Gather details from the request to the server.
	 *
	 * @return array Server details.
	 */
	public static function get_server_details() {
		$data = array();

		$data['_via_ua'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? wc_clean( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		$data['_via_ip'] = isset( $_SERVER['REMOTE_ADDR'] ) ? wc_clean( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$data['_lg']     = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? wc_clean( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) : '';
		$data['_dr']     = isset( $_SERVER['HTTP_REFERER'] ) ? wc_clean( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';

		$uri         = isset( $_SERVER['REQUEST_URI'] ) ? wc_clean( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$host        = isset( $_SERVER['HTTP_HOST'] ) ? wc_clean( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$data['_dl'] = isset( $_SERVER['REQUEST_SCHEME'] ) ? wc_clean( wp_unslash( $_SERVER['REQUEST_SCHEME'] ) ) . '://' . $host . $uri : '';

		return $data;
	}

	/**
	 * Record an event in Tracks - this is the preferred way to record events from PHP.
	 * Note: the event request won't be made if $properties has a member called `error`.
	 *
	 * @param string $event_name The name of the event.
	 * @param array  $event_properties Custom properties to send with the event.
	 * @return bool|WP_Error True for success or WP_Error if the event pixel could not be fired.
	 */
	public static function record_event( $event_name, $event_properties = array() ) {
		/**
		 * Don't track users who don't have tracking enabled.
		 */
		if ( ! WC_Site_Tracking::is_tracking_enabled() ) {
			return false;
		}

		$user = wp_get_current_user();

		// We don't want to track user events during unit tests/CI runs.
		if ( $user instanceof WP_User && 'wptests_capabilities' === $user->cap_key ) {
			return false;
		}
		$prefixed_event_name = self::PREFIX . $event_name;
		$properties          = self::get_properties( $prefixed_event_name, $event_properties );
		$event_obj           = new WC_Tracks_Event( $properties );

		if ( is_wp_error( $event_obj->error ) ) {
			return $event_obj->error;
		}

		return $event_obj->record();
	}

	/**
	 * Get all properties for the event including filtered and identity properties.
	 *
	 * @param string $event_name Event name.
	 * @param array  $event_properties Event specific properties.
	 * @return array
	 */
	public static function get_properties( $event_name, $event_properties ) {
		/**
		 * Allow event props to be filtered to enable adding site-wide props.
		 *
		 * @since 4.1.0
		 */
		$properties = apply_filters( 'woocommerce_tracks_event_properties', $event_properties, $event_name );
		$user       = wp_get_current_user();
		$identity   = WC_Tracks_Client::get_identity( $user->ID );

		// Delete _ui and _ut protected properties.
		unset( $properties['_ui'] );
		unset( $properties['_ut'] );

		$data = $event_name
			? array(
				'_en' => $event_name,
				'_ts' => WC_Tracks_Client::build_timestamp(),
			)
			: array();

		$server_details = self::get_server_details();
		$blog_details   = self::get_blog_details( $user->ID );

		return array_merge( $properties, $data, $server_details, $identity, $blog_details );
	}
}
