<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Tracking
 */

use Yoast\WP\SEO\Analytics\Application\Missing_Indexables_Collector;
use Yoast\WP\SEO\Analytics\Application\To_Be_Cleaned_Indexables_Collector;

/**
 * This class handles the tracking routine.
 */
class WPSEO_Tracking implements WPSEO_WordPress_Integration {

	/**
	 * The tracking option name.
	 *
	 * @var string
	 */
	protected $option_name = 'wpseo_tracking_last_request';

	/**
	 * The limit for the option.
	 *
	 * @var int
	 */
	protected $threshold = 0;

	/**
	 * The endpoint to send the data to.
	 *
	 * @var string
	 */
	protected $endpoint = '';

	/**
	 * The current time.
	 *
	 * @var int
	 */
	private $current_time;

	/**
	 * WPSEO_Tracking constructor.
	 *
	 * @param string $endpoint  The endpoint to send the data to.
	 * @param int    $threshold The limit for the option.
	 */
	public function __construct( $endpoint, $threshold ) {
		if ( ! $this->tracking_enabled() ) {
			return;
		}

		$this->endpoint     = $endpoint;
		$this->threshold    = $threshold;
		$this->current_time = time();
	}

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! $this->tracking_enabled() ) {
			return;
		}

		// Send tracking data on `admin_init`.
		add_action( 'admin_init', [ $this, 'send' ], 1 );

		// Add an action hook that will be triggered at the specified time by `wp_schedule_single_event()`.
		add_action( 'wpseo_send_tracking_data_after_core_update', [ $this, 'send' ] );
		// Call `wp_schedule_single_event()` after a WordPress core update.
		add_action( 'upgrader_process_complete', [ $this, 'schedule_tracking_data_sending' ], 10, 2 );
	}

	/**
	 * Schedules a new sending of the tracking data after a WordPress core update.
	 *
	 * @param bool|WP_Upgrader $upgrader Optional. WP_Upgrader instance or false.
	 *                                   Depending on context, it might be a Theme_Upgrader,
	 *                                   Plugin_Upgrader, Core_Upgrade, or Language_Pack_Upgrader.
	 *                                   instance. Default false.
	 * @param array            $data     Array of update data.
	 *
	 * @return void
	 */
	public function schedule_tracking_data_sending( $upgrader = false, $data = [] ) {
		// Return if it's not a WordPress core update.
		if ( ! $upgrader || ! isset( $data['type'] ) || $data['type'] !== 'core' ) {
			return;
		}

		/*
		 * To uniquely identify the scheduled cron event, `wp_next_scheduled()`
		 * needs to receive the same arguments as those used when originally
		 * scheduling the event otherwise it will always return false.
		 */
		if ( ! wp_next_scheduled( 'wpseo_send_tracking_data_after_core_update', [ true ] ) ) {
			/*
			 * Schedule sending of data tracking 6 hours after a WordPress core
			 * update. Pass a `true` parameter for the callback `$force` argument.
			 */
			wp_schedule_single_event( ( time() + ( HOUR_IN_SECONDS * 6 ) ), 'wpseo_send_tracking_data_after_core_update', [ true ] );
		}
	}

	/**
	 * Sends the tracking data.
	 *
	 * @param bool $force Whether to send the tracking data ignoring the two
	 *                    weeks time threshold. Default false.
	 *
	 * @return void
	 */
	public function send( $force = false ) {
		if ( ! $this->should_send_tracking( $force ) ) {
			return;
		}

		// Set a 'content-type' header of 'application/json'.
		$tracking_request_args = [
			'headers' => [
				'content-type:' => 'application/json',
			],
		];

		$collector = $this->get_collector();

		$request = new WPSEO_Remote_Request( $this->endpoint, $tracking_request_args );
		$request->set_body( $collector->get_as_json() );
		$request->send();

		update_option( $this->option_name, $this->current_time, 'yes' );
	}

	/**
	 * Determines whether to send the tracking data.
	 *
	 * Returns false if tracking is disabled or the current page is one of the
	 * admin plugins pages. Returns true when there's no tracking data stored or
	 * the data was sent more than two weeks ago. The two weeks interval is set
	 * when instantiating the class.
	 *
	 * @param bool $ignore_time_treshhold Whether to send the tracking data ignoring
	 *                                    the two weeks time treshhold. Default false.
	 *
	 * @return bool True when tracking data should be sent.
	 */
	protected function should_send_tracking( $ignore_time_treshhold = false ) {
		global $pagenow;

		// Only send tracking on the main site of a multi-site instance. This returns true on non-multisite installs.
		if ( is_network_admin() || ! is_main_site() ) {
			return false;
		}

		// Because we don't want to possibly block plugin actions with our routines.
		if ( in_array( $pagenow, [ 'plugins.php', 'plugin-install.php', 'plugin-editor.php' ], true ) ) {
			return false;
		}

		$last_time = get_option( $this->option_name );

		// When tracking data haven't been sent yet or when sending data is forced.
		if ( ! $last_time || $ignore_time_treshhold ) {
			return true;
		}

		return $this->exceeds_treshhold( $this->current_time - $last_time );
	}

	/**
	 * Checks if the given amount of seconds exceeds the set threshold.
	 *
	 * @param int $seconds The amount of seconds to check.
	 *
	 * @return bool True when seconds is bigger than threshold.
	 */
	protected function exceeds_treshhold( $seconds ) {
		return ( $seconds > $this->threshold );
	}

	/**
	 * Returns the collector for collecting the data.
	 *
	 * @return WPSEO_Collector The instance of the collector.
	 */
	public function get_collector() {
		$collector = new WPSEO_Collector();
		$collector->add_collection( new WPSEO_Tracking_Default_Data() );
		$collector->add_collection( new WPSEO_Tracking_Server_Data() );
		$collector->add_collection( new WPSEO_Tracking_Theme_Data() );
		$collector->add_collection( new WPSEO_Tracking_Plugin_Data() );
		$collector->add_collection( new WPSEO_Tracking_Settings_Data() );
		$collector->add_collection( new WPSEO_Tracking_Addon_Data() );
		$collector->add_collection( YoastSEO()->classes->get( Missing_Indexables_Collector::class ) );
		$collector->add_collection( YoastSEO()->classes->get( To_Be_Cleaned_Indexables_Collector::class ) );

		return $collector;
	}

	/**
	 * See if we should run tracking at all.
	 *
	 * @return bool True when we can track, false when we can't.
	 */
	private function tracking_enabled() {
		// Check if we're allowing tracking.
		$tracking = WPSEO_Options::get( 'tracking' );

		if ( $tracking === false ) {
			return false;
		}

		// Save this state.
		if ( $tracking === null ) {
			/**
			 * Filter: 'wpseo_enable_tracking' - Enables the data tracking of Yoast SEO Premium and add-ons.
			 *
			 * @param string|false $is_enabled The enabled state. Default is false.
			 */
			$tracking = apply_filters( 'wpseo_enable_tracking', false );

			WPSEO_Options::set( 'tracking', $tracking );
		}

		if ( $tracking === false ) {
			return false;
		}

		if ( ! YoastSEO()->helpers->environment->is_production_mode() ) {
			return false;
		}

		return true;
	}
}
