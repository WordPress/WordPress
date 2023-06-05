<?php
/**
 * Send Tracks events on behalf of a user using pixel images in page footer.
 *
 * @package WooCommerce\Tracks
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Tracks_Footer_Pixel class.
 */
class WC_Tracks_Footer_Pixel {
	/**
	 * Singleton instance.
	 *
	 * @var WC_Tracks_Footer_Pixel
	 */
	protected static $instance = null;

	/**
	 * Events to send to Tracks.
	 *
	 * @var array
	 */
	protected $events = array();

	/**
	 * Instantiate the singleton.
	 *
	 * @return WC_Tracks_Footer_Pixel
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new WC_Tracks_Footer_Pixel();
		}

		return self::$instance;
	}

	/**
	 * Constructor - attach hooks to the singleton instance.
	 */
	public function __construct() {
		add_action( 'admin_footer', array( $this, 'render_tracking_pixels' ) );
		add_action( 'shutdown', array( $this, 'send_tracks_requests' ) );
	}

	/**
	 * Record a Tracks event
	 *
	 * @param  array $event Array of event properties.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function record_event( $event ) {
		if ( ! $event instanceof WC_Tracks_Event ) {
			$event = new WC_Tracks_Event( $event );
		}

		if ( is_wp_error( $event ) ) {
			return $event;
		}

		self::instance()->add_event( $event );

		return true;
	}

	/**
	 * Add a Tracks event to the queue.
	 *
	 * @param WC_Tracks_Event $event Event to track.
	 */
	public function add_event( $event ) {
		$this->events[] = $event;
	}

	/**
	 * Add events as tracking pixels to page footer.
	 */
	public function render_tracking_pixels() {
		if ( empty( $this->events ) ) {
			return;
		}

		foreach ( $this->events as $event ) {
			$pixel = $event->build_pixel_url();

			if ( ! $pixel ) {
				continue;
			}

			echo '<img style="position: fixed;" src="', esc_url( $pixel ), '" />';
		}

		$this->events = array();
	}

	/**
	 * Fire off API calls for events that weren't converted to pixels.
	 *
	 * This handles wp_redirect().
	 */
	public function send_tracks_requests() {
		if ( empty( $this->events ) ) {
			return;
		}

		foreach ( $this->events as $event ) {
			WC_Tracks_Client::record_event( $event );
		}
	}

	/**
	 * Get all events.
	 */
	public static function get_events() {
		return self::instance()->events;
	}

	/**
	 * Clear all queued events.
	 */
	public static function clear_events() {
		self::instance()->events = array();
	}
}
