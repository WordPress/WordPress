<?php
/**
 * Class WPCode_Usage_Tracking - The abstract class for the usage tracking.
 *
 * @package WPCode
 */

/**
 * The abstract class for the usage tracking.
 */
abstract class WPCode_Usage_Tracking {
	/**
	 * Returns the current plugin version type ("lite" or "pro").
	 *
	 * @return string The version type.
	 * @since 2.0.10
	 */
	abstract public function get_type();

	/**
	 * Is the usage tracking enabled?
	 *
	 * @return bool
	 * @since 2.0.10
	 */
	abstract public function is_enabled();

	/**
	 * Usage Tracking endpoint.
	 *
	 * @since 2.0.10
	 *
	 * @var string
	 */
	private $url = 'https://wpcodeusage.com/v1/track';

	/**
	 * Option name to store the timestamp of the last run.
	 *
	 * @since 2.0.10
	 */
	const LAST_RUN = 'wpcode_send_usage_last_run';

	/**
	 * Class Constructor.
	 *
	 * @since 2.0.10
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 2 );
		add_action( 'wpcode_usage_tracking_cron', array( $this, 'process' ) );
		add_action( 'wpcode_library_api_auth_connected', array( $this, 'usage_tracking_auth' ) );
	}

	/**
	 * Initiate the usage tracking cron.
	 *
	 * @return void
	 * @since 2.0.10
	 */
	public function init() {
		if ( ! wp_next_scheduled( 'wpcode_usage_tracking_cron' ) ) {
			$tracking             = array();
			$tracking['day']      = wp_rand( 0, 6 );
			$tracking['hour']     = wp_rand( 0, 23 );
			$tracking['minute']   = wp_rand( 0, 59 );
			$tracking['second']   = wp_rand( 0, 59 );
			$tracking['offset']   = ( $tracking['day'] * DAY_IN_SECONDS ) + ( $tracking['hour'] * HOUR_IN_SECONDS ) + ( $tracking['minute'] * MINUTE_IN_SECONDS ) + $tracking['second'];
			$tracking['initsend'] = strtotime( 'next sunday' ) + $tracking['offset'];

			wp_schedule_event( $tracking['initsend'], 'weekly', 'wpcode_usage_tracking_cron' );
			update_option( 'wpcode_usage_tracking_config', $tracking, false );
		}
	}

	/**
	 * Processes the usage tracking.
	 *
	 * @return void
	 * @since 2.0.10
	 */
	public function process() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$last_run = get_option( self::LAST_RUN );

		// Make sure we do not run it more than once a day.
		if ( false !== $last_run && ( time() - $last_run ) < DAY_IN_SECONDS ) {
			return;
		}

		wp_remote_post(
			$this->get_url(),
			array(
				'timeout'    => 10,
				'headers'    => array(
					'Content-Type' => 'application/json; charset=utf-8',
				),
				'user-agent' => $this->get_user_agent(),
				'body'       => wp_json_encode( $this->get_data() ),
			)
		);

		// If we have completed successfully, recheck in 1 week.
		update_option( self::LAST_RUN, time() );
	}

	/**
	 * Gets the URL for the notifications api.
	 *
	 * @return string The URL to use for the api requests.
	 * @since 2.0.10
	 */
	private function get_url() {
		if ( defined( 'WPCODE_USAGE_TRACKING_URL' ) ) {
			return WPCODE_USAGE_TRACKING_URL;
		}

		return $this->url;
	}

	/**
	 * Rtrieve the data to send to the usage tracking api.
	 *
	 * @return array
	 * @since 2.0.10
	 */
	public function get_data() {
		global $wpdb;

		$theme_data     = wp_get_theme();
		$activated      = get_option( 'ihaf_activated', array() );
		$installed_date = isset( $activated['wpcode'] ) ? $activated['wpcode'] : null;

		$data = array(
			// Generic data (environment).
			'url'                        => home_url(),
			'php_version'                => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
			'wp_version'                 => get_bloginfo( 'version' ),
			'mysql_version'              => $wpdb->db_version(),
			'server_version'             => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '',
			'is_ssl'                     => is_ssl(),
			'is_multisite'               => is_multisite(),
			'sites_count'                => function_exists( 'get_blog_count' ) ? (int) get_blog_count() : 1,
			'active_plugins'             => $this->get_active_plugins(),
			'theme_name'                 => $theme_data->name,
			'theme_version'              => $theme_data->version,
			'user_count'                 => function_exists( 'get_user_count' ) ? get_user_count() : null,
			'locale'                     => get_locale(),
			'timezone_offset'            => wpcode_wp_timezone_string(),
			// WPCode specific data.
			'wpcode_version'             => WPCODE_VERSION,
			'wpcode_license_key'         => null,
			'wpcode_license_type'        => null,
			'wpcode_is_pro'              => false,
			'wpcode_lite_installed_date' => $installed_date,
			'wpcode_settings'            => $this->get_settings(),
		);

		// Add snippets data.
		$data = array_merge( $data, $this->get_snippets_data() );

		return $data;
	}

	/**
	 * Return a list of active plugins.
	 *
	 * @return array An array of active plugin data.
	 * @since 2.0.10
	 */
	private function get_active_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}
		$active  = get_option( 'active_plugins', array() );
		$plugins = array_intersect_key( get_plugins(), array_flip( $active ) );

		return array_map(
			static function ( $plugin ) {
				if ( isset( $plugin['Version'] ) ) {
					return $plugin['Version'];
				}

				return 'Not Set';
			},
			$plugins
		);
	}

	/**
	 * Get the User Agent string that will be sent to the API.
	 *
	 * @return string
	 * @since 2.0.10
	 */
	public function get_user_agent() {
		return 'WPCode/' . WPCODE_VERSION . '; ' . get_bloginfo( 'url' );
	}

	/**
	 * Get the WPCode settings but anonymize some data.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = wpcode()->settings->get_options();

		// By default, don't send the API tokens.
		$settings_to_ignore = apply_filters(
			'wpcode_usage_tracking_excluded_settings',
			array(
				'facebook_pixel_api_token',
				'pinterest_conversion_token',
				'tiktok_access_token',
			)
		);

		foreach ( $settings_to_ignore as $setting ) {
			if ( isset( $settings[ $setting ] ) ) {
				$settings[ $setting ] = '***';
			}
		}

		return $settings;
	}

	/**
	 * After the user has successfully authenticated with the API, we can start sending data.
	 *
	 * @return void
	 */
	public function usage_tracking_auth() {
		// If already enabled, don't do anything.
		if ( $this->is_enabled() ) {
			return;
		}
		wpcode()->settings->update_option( 'usage_tracking', true );
	}

	/**
	 * Track snippet-specific data.
	 *
	 * @return array
	 */
	public function get_snippets_data() {
		$counts        = wp_count_posts( 'wpcode' );
		$snippets_data = array(
			'wpcode_total_snippets'     => array_sum( array( $counts->publish, $counts->draft ) ),
			'wpcode_active_snippets'    => $counts->publish,
			'wpcode_trashed_snippets'   => $counts->trash,
			'wpcode_generated_snippets' => 0,
			'wpcode_generators'         => array(),
		);

		if ( empty( $snippets_data['wpcode_total_snippets'] ) ) {
			// If there are no snippets to look at then we can return early.
			return $snippets_data;
		}
		// Let's see how many snippets were generated with a WPCode Generator.
		$generated_snippets = get_posts(
			array(
				'post_type' => 'wpcode',
				'meta_key'  => '_wpcode_generator', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'fields'    => 'ids',
			)
		);

		if ( ! empty( $generated_snippets ) ) {
			$snippets_data['wpcode_generated_snippets'] = count( $generated_snippets );
			foreach ( $generated_snippets as $snippet_id ) {
				$snippets_data['wpcode_generators'][] = get_post_meta( $snippet_id, '_wpcode_generator', true );
			}
		}

		return $snippets_data;
	}
}
