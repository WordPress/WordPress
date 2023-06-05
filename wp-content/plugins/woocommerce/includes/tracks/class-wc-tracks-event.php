<?php
/**
 * This class represents an event used to record a Tracks event
 *
 * @package WooCommerce\Tracks
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Tracks_Event class.
 */
class WC_Tracks_Event {

	/**
	 * Event name regex.
	 */
	public const EVENT_NAME_REGEX = '/^(([a-z0-9]+)_){1}([a-z0-9_]+)$/';

	/**
	 * Property name regex.
	 */
	public const PROP_NAME_REGEX = '/^[a-z_][a-z0-9_]*$/';

	/**
	 * Error message as WP_Error.
	 *
	 * @var WP_Error
	 */
	public $error;

	/**
	 * WC_Tracks_Event constructor.
	 *
	 * @param array $event Event properties.
	 */
	public function __construct( $event ) {
		$_event = self::validate_and_sanitize( $event );
		if ( is_wp_error( $_event ) ) {
			$this->error = $_event;
			return;
		}

		foreach ( $_event as $key => $value ) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Record Tracks event
	 *
	 * @return bool Always returns true.
	 */
	public function record() {
		if ( wp_doing_ajax() || Constants::is_true( 'REST_REQUEST' ) ) {
			return WC_Tracks_Client::record_event( $this );
		}

		return WC_Tracks_Footer_Pixel::record_event( $this );
	}

	/**
	 * Annotate the event with all relevant info.
	 *
	 * @param  array $event Event arguments.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function validate_and_sanitize( $event ) {
		$event = (object) $event;

		// Required.
		if ( ! $event->_en ) {
			return new WP_Error( 'invalid_event', 'A valid event must be specified via `_en`', 400 );
		}

		// Delete non-routable addresses otherwise geoip will discard the record entirely.
		if ( property_exists( $event, '_via_ip' ) && preg_match( '/^192\.168|^10\./', $event->_via_ip ) ) {
			unset( $event->_via_ip );
		}

		$validated = array(
			'browser_type' => WC_Tracks_Client::BROWSER_TYPE,
		);

		$_event = (object) array_merge( (array) $event, $validated );

		// If you want to block property names, do it here.
		// Make sure we have an event timestamp.
		if ( ! isset( $_event->_ts ) ) {
			$_event->_ts = WC_Tracks_Client::build_timestamp();
		}

		if ( ! self::event_name_is_valid( $_event->_en ) ) {
			return new WP_Error( 'invalid_event_name', __( 'A valid event name must be specified.', 'woocommerce' ) );
		}

		foreach ( array_keys( (array) $_event ) as $key ) {
			if ( ! self::prop_name_is_valid( $key ) && '_en' !== $key ) {
				return new WP_Error( 'invalid_prop_name', __( 'A valid prop name must be specified', 'woocommerce' ) );
			}
		}

		return $_event;
	}

	/**
	 * Build a pixel URL that will send a Tracks event when fired.
	 * On error, returns an empty string ('').
	 *
	 * @return string A pixel URL or empty string ('') if there were invalid args.
	 */
	public function build_pixel_url() {
		if ( $this->error ) {
			return '';
		}

		$args = get_object_vars( $this );

		// Request Timestamp and URL Terminator must be added just before the HTTP request or not at all.
		unset( $args['_rt'], $args['_'] );

		$validated = self::validate_and_sanitize( $args );

		if ( is_wp_error( $validated ) ) {
			return '';
		}

		return esc_url_raw( WC_Tracks_Client::PIXEL . '?' . http_build_query( $validated ) );
	}

	/**
	 * Check if event name is valid.
	 *
	 * @param string $name Event name.
	 * @return false|int
	 */
	public static function event_name_is_valid( $name ) {
		return preg_match( self::EVENT_NAME_REGEX, $name );
	}

	/**
	 * Check if a property name is valid.
	 *
	 * @param string $name Event property.
	 * @return false|int
	 */
	public static function prop_name_is_valid( $name ) {
		return preg_match( self::PROP_NAME_REGEX, $name );
	}

	/**
	 * Check event names
	 *
	 * @param object $event An event object.
	 */
	public static function scrutinize_event_names( $event ) {
		if ( ! self::event_name_is_valid( $event->_en ) ) {
			return;
		}

		$allowed_key_names = array(
			'anonId',
			'Browser_Type',
		);

		foreach ( array_keys( (array) $event ) as $key ) {
			if ( in_array( $key, $allowed_key_names, true ) ) {
				continue;
			}
			if ( ! self::prop_name_is_valid( $key ) ) {
				return;
			}
		}
	}
}
