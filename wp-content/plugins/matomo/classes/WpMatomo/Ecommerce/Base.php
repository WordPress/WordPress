<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Ecommerce;

use Exception;
use WpMatomo;
use WpMatomo\Admin\TrackingSettings;
use WpMatomo\AjaxTracker;
use WpMatomo\Logger;
use WpMatomo\Settings;
use WpMatomo\Site\Sync\SyncConfig;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Base {
	const DELAYED_SERVER_SIDE_TRACKING_HOOK        = 'matomo_delayed_tracking';
	const DELAYED_SERVER_SIDE_TRACKING_SESSION_KEY = 'matomo_delayed_tracking_data';

	protected $key_order_tracked = 'order-tracked';

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var AjaxTracker
	 */
	protected $tracker;

	/**
	 * We can't echo cart updates directly as we wouldn't know where in the template rendering stage we are and whether
	 * we're supposed to print or not etc. Also there might be multiple cart updates triggered during one page load so
	 * we want to make sure to print only the most recent tracking code
	 *
	 * @var string
	 */
	protected $cart_update_queue = '';

	/**
	 * @var Settings
	 */
	protected $settings;

	private $ajax_tracker_calls = [];

	/**
	 * @var SyncConfig
	 */
	private $config;

	public function __construct( AjaxTracker $tracker, Settings $settings, SyncConfig $config ) {
		$this->logger   = new Logger();
		$this->tracker  = $tracker;
		$this->settings = $settings;
		$this->config   = $config;

		// by using prefix we make sure it will be removed on unistall and make sure it's clear it belongs to us
		$this->key_order_tracked = Settings::OPTION_PREFIX . $this->key_order_tracked;
	}

	public function register_hooks() {
		if ( ! is_admin() ) {
			add_action( 'wp_footer', [ $this, 'on_print_queues' ], 99999, 0 );
			add_action( 'wp_footer', [ $this, 'maybe_do_delayed_tracking_early' ], 99999, 0 );
		}

		add_action( self::DELAYED_SERVER_SIDE_TRACKING_HOOK, [ $this, 'do_delayed_tracking' ], 10, 1 );
	}

	public function on_print_queues() {
		// we need to queue in case there are multiple cart updates within one page load
		if ( ! empty( $this->cart_update_queue ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->cart_update_queue;
		}
	}

	protected function has_order_been_tracked_already( $order_id ) {
		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
		return get_post_meta( $order_id, $this->key_order_tracked, true ) == 1;
	}

	protected function set_order_been_tracked( $order_id ) {
		update_post_meta( $order_id, $this->key_order_tracked, 1 );
	}

	protected function should_track_background() {
		return ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			   || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			   || ( defined( 'MATOMO_TRACK_ECOMMERCE_SERVER_SIDE' ) && MATOMO_TRACK_ECOMMERCE_SERVER_SIDE )
			   || ( did_action( 'wp_footer' ) && ! doing_action( 'wp_footer' ) )
			   || $this->settings->get_global_option( 'track_mode' ) === TrackingSettings::TRACK_MODE_TAGMANAGER;
	}

	protected function make_matomo_js_tracker_call( $params ) {
		$this->ajax_tracker_calls[] = $params;

		$code = 'window._paq = window._paq || [];';
		if ( $this->settings->get_global_option( 'disable_cookies' ) ) {
			$code .= ' ' . WpMatomo\TrackingCode\TrackingCodeGenerator::get_disable_cookies_partial();
		}
		$code .= sprintf( ' window._paq.push(%s);', wp_json_encode( $params ) );

		return $code;
	}

	protected function wrap_script( $script ) {
		if ( $this->should_track_background() ) {
			if ( $this->should_delay_server_side_tracking() ) {
				$this->delay_background_tracking();
				return false;
			}

			$this->track_in_background( $this->ajax_tracker_calls );

			$this->ajax_tracker_calls = [];

			return '';
		}

		if ( empty( $script ) ) {
			return '';
		}

		if ( function_exists( 'wp_get_inline_script_tag' ) ) {
			$script = wp_get_inline_script_tag( $script );
		} else {
			// line feed is required to match the wp_get_inline_script_tag output
			$script = '<script >' . PHP_EOL . $script . PHP_EOL . '</script>' . PHP_EOL;
		}

		// NOTE: we expect the script to be echo'd after wrap_script is called
		$this->set_order_been_tracked_if_ajax_calls_has_order_track();

		return $script;
	}

	private function set_order_been_tracked_if_ajax_calls_has_order_track() {
		foreach ( $this->ajax_tracker_calls as $call ) {
			$tracker_method = array_shift( $call );
			$this->set_order_been_tracked_if_call_is_track_order( $tracker_method, $call );
		}
	}

	private function track_in_background( $ajax_tracker_calls, $visitor_id = null, $tracking_time = null, $ip = null ) {
		$original_visitor_id = $this->tracker->forcedVisitorId;
		if ( ! empty( $visitor_id ) ) {
			$this->tracker->set_visitor_id_safe( $visitor_id );
		}

		if ( ! empty( $tracking_time ) ) {
			$this->tracker->setForceVisitDateTime( $tracking_time );
		}

		if ( ! empty( $ip ) ) {
			$this->tracker->setIp( $ip );
		}

		try {
			foreach ( $ajax_tracker_calls as $call ) {
				$methods = [
					'addEcommerceItem'         => 'addEcommerceItem',
					'trackEcommerceOrder'      => 'doTrackEcommerceOrder',
					'trackEcommerceCartUpdate' => 'doTrackEcommerceCartUpdate',
				];
				if ( ! empty( $call[0] ) && ! empty( $methods[ $call[0] ] ) ) {
					try {
						$tracker_method = $methods[ $call[0] ];
						array_shift( $call );
						$response = call_user_func_array( [ $this->tracker, $tracker_method ], $call );

						if ( $this->tracker->is_success_response( $response ) ) {
							$this->set_order_been_tracked_if_call_is_track_order( $tracker_method, $call );
						}
					} catch ( Exception $e ) {
						$this->logger->log_exception( $call[0], $e );
					}
				}
			}
		} finally {
			$this->tracker->forcedVisitorId = $original_visitor_id;
			$this->tracker->forcedDatetime  = false;
			$this->tracker->ip              = false;
		}
	}

	private function set_order_been_tracked_if_call_is_track_order( $tracker_method, $params ) {
		if (
			'doTrackEcommerceOrder' === $tracker_method
			|| 'trackEcommerceOrder' === $tracker_method
		) {
			$order_id = reset( $params );
			$this->set_order_been_tracked( $order_id );
		}
	}

	protected function delay_background_tracking() {
		$delay_time   = $this->get_seconds_to_delay_tracking();
		$delayed_time = time() + $delay_time;

		$client_headers = $this->config->get_config_value( 'General', 'proxy_client_headers' );
		if ( ! empty( $client_headers ) ) {
			foreach ( $client_headers as $header ) {
				if ( isset( $_SERVER[ $header ] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$ip = wp_unslash( $_SERVER[ $header ] );
				}
			}
		}
		if ( empty( $ip ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : null;
		}

		$tracking_data = [
			'calls'         => $this->ajax_tracker_calls,
			'delayed_time'  => $delayed_time,
			'visitor_id'    => $this->tracker->forcedVisitorId,
			'tracking_time' => time(),
			'ip'            => $ip,
		];

		$this->add_tracking_calls_to_session( $tracking_data );

		wp_schedule_single_event( $delayed_time, self::DELAYED_SERVER_SIDE_TRACKING_HOOK, [ $tracking_data ] );

		$this->ajax_tracker_calls = [];
	}

	protected function should_delay_server_side_tracking() {
		return $this->supports_delayed_tracking();
	}

	protected function get_seconds_to_delay_tracking() {
		$delay = $this->settings->get_option( Settings::SERVER_SIDE_TRACKING_DELAY_SECS );
		if ( $delay <= 0 ) {
			$delay = 180;
		}
		return $delay;
	}

	public function maybe_do_delayed_tracking_early() {
		// do not output tracking code if we do not support delayed tracking
		// or if the current request requires background tracking (in which case
		// outputting JS code would not work)
		if (
			! $this->supports_delayed_tracking()
			|| $this->should_track_background()
		) {
			return;
		}

		$all_queued_tracking = $this->get_tracking_calls_in_session();
		if ( ! empty( $all_queued_tracking ) && is_array( $all_queued_tracking ) ) {
			foreach ( $all_queued_tracking as $tracking_data ) {
				if ( ! wp_get_scheduled_event( self::DELAYED_SERVER_SIDE_TRACKING_HOOK, [ $tracking_data ], $tracking_data['delayed_time'] ) ) {
					continue; // delayed tracking event already ran
				}

				$script = '';
				foreach ( $tracking_data['calls'] as $call ) {
					$script .= $this->make_matomo_js_tracker_call( $call );

					$tracker_method = array_shift( $call );
					$this->set_order_been_tracked_if_call_is_track_order( $tracker_method, $call );
				}

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->wrap_script( $script );

				wp_unschedule_event( $tracking_data['delayed_time'], self::DELAYED_SERVER_SIDE_TRACKING_HOOK, [ $tracking_data ] );
			}

			$this->remove_tracking_calls_in_session();
		}
	}

	public function do_delayed_tracking( $tracking_info ) {
		// WP cron jobs can be executed during normal requests. in such a case, make sure we don't use
		// the visitor ID of the current request/session
		$this->tracker->setNewVisitorId();

		$calls         = isset( $tracking_info['calls'] ) ? $tracking_info['calls'] : [];
		$visitor_id    = isset( $tracking_info['visitor_id'] ) ? $tracking_info['visitor_id'] : null;
		$tracking_time = isset( $tracking_info['tracking_time'] ) ? $tracking_info['tracking_time'] : null;
		$ip            = isset( $tracking_info['ip'] ) ? $tracking_info['ip'] : null;

		$this->track_in_background( $calls, $visitor_id, $tracking_time, $ip );
	}

	/**
	 * Returns true if this ecommerce tracking implementation supports delayed
	 * server side tracking.
	 *
	 * In order for an implementation to support this, it must be able to save
	 * tracking information as session data.
	 *
	 * @return false
	 */
	protected function supports_delayed_tracking() {
		return false;
	}

	/**
	 * Removes all tracking calls currently stored in the session.
	 *
	 * This method must be overridden by ecommerce tracking implementations.
	 *
	 * @return void
	 */
	protected function remove_tracking_calls_in_session() {
		// empty
	}

	/**
	 * Adds provided queued tracking information to the session.
	 *
	 * This method must be overridden by ecommerce tracking implementations.
	 *
	 * @param array $calls
	 * @return void
	 */
	protected function add_tracking_calls_to_session( $calls ) {
		// empty
	}

	/**
	 * Returns tracking calls stored in the session, if any.
	 *
	 * This method must be overridden by ecommerce tracking implementations.
	 *
	 * @return array
	 */
	protected function get_tracking_calls_in_session() {
		return [];
	}
}
