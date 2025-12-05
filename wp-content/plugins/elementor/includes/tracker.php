<?php
namespace Elementor;

use Elementor\Core\Common\Modules\EventTracker\DB as Events_DB_Manager;
use Elementor\Core\Experiments\Experiments_Reporter;
use Elementor\Modules\System_Info\Module as System_Info_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor tracker.
 *
 * Elementor tracker handler class is responsible for sending non-sensitive plugin
 * data to Elementor servers for users that actively allowed data tracking.
 *
 * @since 1.0.0
 */
class Tracker {

	/**
	 * API URL.
	 *
	 * Holds the URL of the Tracker API.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string API URL.
	 */
	private static $api_url = 'https://my.elementor.com/api/v1/tracker/';

	private static $notice_shown = false;

	const LAST_TERMS_UPDATED = '2025-07-07';

	/**
	 * Init.
	 *
	 * Initialize Elementor tracker.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_action( 'elementor/tracker/send_event', [ __CLASS__, 'send_tracking_data' ] );
		add_action( 'admin_init', [ __CLASS__, 'handle_tracker_actions' ] );

		add_action( 'update_option_elementor_allow_tracking', [ __CLASS__, 'set_last_update_time' ] );
	}

	/**
	 * Check for settings opt-in.
	 *
	 * Checks whether the site admin has opted-in for data tracking, or not.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $new_value Allowed tracking value.
	 *
	 * @return string Return `yes` if tracking allowed, `no` otherwise.
	 */
	public static function check_for_settings_optin( $new_value ) {
		$old_value = get_option( 'elementor_allow_tracking', 'no' );
		if ( $old_value !== $new_value && 'yes' === $new_value ) {
			Plugin::$instance->custom_tasks->add_tasks_requested_to_run( [
				'opt_in_recalculate_usage',
				'opt_in_send_tracking_data',
			] );
		}

		self::set_last_update_time();

		if ( empty( $new_value ) ) {
			$new_value = 'no';
		}

		return $new_value;
	}

	/**
	 * Send tracking data.
	 *
	 * Decide whether to send tracking data, or not.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param bool $override
	 */
	public static function send_tracking_data( $override = false ) {
		// Don't trigger this on AJAX Requests.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! self::is_allow_track() ) {
			return;
		}

		$last_send = self::get_last_send_time();

		/**
		 * Tracker override send.
		 *
		 * Filters whether to override sending tracking data or not.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $override Whether to override default setting or not.
		 */
		$override = apply_filters( 'elementor/tracker/send_override', $override );

		if ( ! $override ) {
			$last_send_interval = strtotime( '-1 week' );

			/**
			 * Tracker last send interval.
			 *
			 * Filters the interval of between two tracking requests.
			 *
			 * @since 1.0.0
			 *
			 * @param int $last_send_interval A date/time string. Default is `strtotime( '-1 week' )`.
			 */
			$last_send_interval = apply_filters( 'elementor/tracker/last_send_interval', $last_send_interval );

			// Send a maximum of once per week by default.
			if ( $last_send && $last_send > $last_send_interval ) {
				return;
			}
		} elseif ( $last_send && $last_send > strtotime( '-1 hours' ) ) {
			return;
		}

		// Update time first before sending to ensure it is set.
		update_option( 'elementor_tracker_last_send', time() );

		$params = self::get_tracking_data( empty( $last_send ) );

		// Tracking data is used for System Info reports, and events should not be included in System Info reports,
		// so it is added here.
		$params['analytics_events'] = self::get_events();

		add_filter( 'https_ssl_verify', '__return_false' );

		wp_safe_remote_post(
			self::$api_url,
			[
				'timeout' => 25,
				'blocking' => false,
				'body' => [
					'data' => wp_json_encode( $params ),
				],
			]
		);

		// After sending the event tracking data, we reset the events table.
		Events_DB_Manager::reset_table();
	}

	/**
	 * Is allow track.
	 *
	 * Checks whether the site admin has opted-in for data tracking, or not.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function is_allow_track() {
		return 'yes' === get_option( 'elementor_allow_tracking', 'no' );
	}

	public static function get_last_update_time() {
		return get_option( 'elementor_allow_tracking_last_update', false );
	}

	public static function set_last_update_time(): void {
		update_option( 'elementor_allow_tracking_last_update', gmdate( 'U' ) );
	}

	public static function has_terms_changed( $terms_updated = self::LAST_TERMS_UPDATED ): bool {
		if ( ! self::is_allow_track() ) {
			return false;
		}

		$last_update_time = self::get_last_update_time();
		if ( $last_update_time ) {
			$terms_updated_timestamp = strtotime( $terms_updated . ' UTC' );

			return $last_update_time < $terms_updated_timestamp;
		}

		return true;
	}

	/**
	 * Handle tracker actions.
	 *
	 * Check if the user opted-in or opted-out and update the database.
	 *
	 * Fired by `admin_init` action.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function handle_tracker_actions() {
		if ( ! isset( $_GET['elementor_tracker'] ) ) {
			return;
		}

		if ( 'opt_into' === $_GET['elementor_tracker'] ) {
			check_admin_referer( 'opt_into' );

			self::set_opt_in( true );
		}

		if ( 'opt_out' === $_GET['elementor_tracker'] ) {
			check_admin_referer( 'opt_out' );

			self::set_opt_in( false );
		}

		wp_safe_redirect( remove_query_arg( 'elementor_tracker' ) );
		exit;
	}

	/**
	 * @since 2.2.0
	 * @access public
	 * @static
	 */
	public static function is_notice_shown() {
		return self::$notice_shown;
	}

	public static function set_opt_in( $value ) {
		if ( $value ) {
			update_option( 'elementor_allow_tracking', 'yes' );
			self::set_last_update_time();

			self::send_tracking_data( true );
		} else {
			update_option( 'elementor_allow_tracking', 'no' );
			update_option( 'elementor_tracker_notice', '1' );
		}
	}

	/**
	 * Get system reports data.
	 *
	 * Retrieve the data from system reports.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @return array The data from system reports.
	 */
	private static function get_system_reports_data() {
		$reports = Plugin::$instance->system_info->load_reports( System_Info_Module::get_allowed_reports() );

		// The log report should not be sent with the usage data - it is not used and causes bloat.
		if ( isset( $reports['log'] ) ) {
			unset( $reports['log'] );
		}

		$system_reports = [];
		foreach ( $reports as $report_key => $report_details ) {
			$system_reports[ $report_key ] = [];
			foreach ( $report_details['report']->get_report() as $sub_report_key => $sub_report_details ) {
				$system_reports[ $report_key ][ $sub_report_key ] = $sub_report_details['value'];
			}
		}
		return $system_reports;
	}

	/**
	 * Get last send time.
	 *
	 * Retrieve the last time tracking data was sent.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @return int|false The last time tracking data was sent, or false if
	 *                   tracking data never sent.
	 */
	private static function get_last_send_time() {
		$last_send_time = get_option( 'elementor_tracker_last_send', false );

		/**
		 * Tracker last send time.
		 *
		 * Filters the last time tracking data was sent.
		 *
		 * @since 1.0.0
		 *
		 * @param int|false $last_send_time The last time tracking data was sent,
		 *                                  or false if tracking data never sent.
		 */
		$last_send_time = apply_filters( 'elementor/tracker/last_send_time', $last_send_time );

		return $last_send_time;
	}

	/**
	 * Get non elementor post usages.
	 *
	 * Retrieve the number of posts that not using elementor.

	 * @return array The number of posts using not used by Elementor grouped by post types
	 *               and post status.
	 */
	public static function get_non_elementor_posts_usage() {
		global $wpdb;

		$usage = [];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$results = $wpdb->get_results(
			"SELECT `post_type`, `post_status`, COUNT(`ID`) `hits`
				FROM {$wpdb->posts} `p`
				LEFT JOIN {$wpdb->postmeta} `pm` ON(`p`.`ID` = `pm`.`post_id` AND  `meta_key` = '_elementor_edit_mode' )
				WHERE `post_type` != 'elementor_library' AND `meta_value` IS NULL
				GROUP BY `post_type`, `post_status`;"
		);

		if ( $results ) {
			foreach ( $results as $result ) {
				$usage[ $result->post_type ][ $result->post_status ] = $result->hits;
			}
		}

		return $usage;
	}

	/**
	 * Get posts usage.
	 *
	 * Retrieve the number of posts using Elementor.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return array The number of posts using Elementor grouped by post types
	 *               and post status.
	 */
	public static function get_posts_usage() {
		global $wpdb;

		$usage = [];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$results = $wpdb->get_results(
			"SELECT `post_type`, `post_status`, COUNT(`ID`) `hits`
				FROM {$wpdb->posts} `p`
				LEFT JOIN {$wpdb->postmeta} `pm` ON(`p`.`ID` = `pm`.`post_id`)
				WHERE `post_type` != 'elementor_library'
					AND `meta_key` = '_elementor_edit_mode' AND `meta_value` = 'builder'
				GROUP BY `post_type`, `post_status`;"
		);

		if ( $results ) {
			foreach ( $results as $result ) {
				$usage[ $result->post_type ][ $result->post_status ] = (int) $result->hits;
			}
		}

		return $usage;
	}

	/**
	 * Get library usage.
	 *
	 * Retrieve the number of Elementor library items saved.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return array The number of Elementor library items grouped by post types
	 *               and meta value.
	 */
	public static function get_library_usage() {
		global $wpdb;

		$usage = [];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$results = $wpdb->get_results(
			"SELECT `meta_value`, COUNT(`ID`) `hits`
				FROM {$wpdb->posts} `p`
				LEFT JOIN {$wpdb->postmeta} `pm` ON(`p`.`ID` = `pm`.`post_id`)
				WHERE `post_type` = 'elementor_library'
					AND `meta_key` = '_elementor_template_type'
				GROUP BY `post_type`, `meta_value`;"
		);

		if ( $results ) {
			foreach ( $results as $result ) {
				$usage[ $result->meta_value ] = $result->hits;
			}
		}

		return $usage;
	}

	/**
	 * Get usage of general settings.
	 * 'Elementor->Settings->General'.
	 *
	 * @return array
	 */
	public static function get_settings_general_usage() {
		return self::get_tracking_data_from_settings( 'general' );
	}

	/**
	 * Get usage of advanced settings.
	 * 'Elementor->Settings->Advanced'.
	 *
	 * @return array
	 */
	public static function get_settings_advanced_usage() {
		return self::get_tracking_data_from_settings( 'advanced' );
	}

	/**
	 * Get usage of performance settings.
	 * 'Elementor->Settings->Performance'.
	 *
	 * @return array
	 */
	public static function get_settings_performance_usage() {
		return self::get_tracking_data_from_settings( 'performance' );
	}

	/**
	 * Get usage of experiments settings.
	 *
	 * 'Elementor->Settings->Experiments'.
	 *
	 * @return array
	 */
	public static function get_settings_experiments_usage() {
		$system_info = Plugin::$instance->system_info;

		/**
		 * @var $experiments_report Experiments_Reporter
		 */
		$experiments_report = $system_info->create_reporter( [
			'class_name' => Experiments_Reporter::class,
		] );

		return $experiments_report->get_experiments()['value'];
	}

	/**
	 * Get usage of general tools.
	 * 'Elementor->Tools->General'.
	 *
	 * @return array
	 */
	public static function get_tools_general_usage() {
		return self::get_tracking_data_from_tools( 'general' );
	}

	/**
	 * Get usage of 'version control' tools.
	 * 'Elementor->Tools->Version Control'.
	 *
	 * @return array
	 */
	public static function get_tools_version_control_usage() {
		return self::get_tracking_data_from_tools( 'versions' );
	}

	/**
	 * Get usage of 'maintenance' tools.
	 * 'Elementor->Tools->Maintenance'.
	 *
	 * @return array
	 */
	public static function get_tools_maintenance_usage() {
		return self::get_tracking_data_from_tools( 'maintenance_mode' );
	}

	/**
	 * Get library usage extend.
	 *
	 * Retrieve the number of Elementor library items saved.
	 *
	 * @return array The number of Elementor library items grouped by post types, post status
	 *               and meta value.
	 */
	public static function get_library_usage_extend() {
		global $wpdb;

		$usage = [];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$results = $wpdb->get_results(
			"SELECT `meta_value`, COUNT(`ID`) `hits`, `post_status`
				FROM {$wpdb->posts} `p`
				LEFT JOIN {$wpdb->postmeta} `pm` ON(`p`.`ID` = `pm`.`post_id`)
				WHERE `post_type` = 'elementor_library'
					AND `meta_key` = '_elementor_template_type'
				GROUP BY `post_type`, `meta_value`, `post_status`;"
		);

		if ( $results ) {
			foreach ( $results as $result ) {
				if ( empty( $usage[ $result->meta_value ] ) ) {
					$usage[ $result->meta_value ] = [];
				}

				if ( empty( $usage[ $result->meta_value ][ $result->post_status ] ) ) {
					$usage[ $result->meta_value ][ $result->post_status ] = 0;
				}

				$usage[ $result->meta_value ][ $result->post_status ] += $result->hits;
			}
		}

		return $usage;
	}

	public static function get_events() {
		global $wpdb;
		$table_name = $wpdb->prefix . Events_DB_Manager::TABLE_NAME;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$results = $wpdb->get_results( "SELECT event_data FROM {$table_name}" );

		$events_data = [];

		foreach ( $results as $event ) {
			// Results are stored in the database as a JSON string. Since all tracking data is encoded right before
			// being sent, it is now decoded.
			$events_data[] = json_decode( $event->event_data, true );
		}

		return $events_data;
	}

	/**
	 * Get the tracking data
	 *
	 * Retrieve tracking data and apply filter
	 *
	 * @access public
	 * @static
	 *
	 * @param bool $is_first_time
	 *
	 * @return array
	 */
	public static function get_tracking_data( $is_first_time = false ) {
		$params = [
			'system' => self::get_system_reports_data(),
			'site_lang' => get_bloginfo( 'language' ),
			'email' => get_option( 'admin_email' ),
			'usages' => [
				'posts' => self::get_posts_usage(),
				'non-elementor-posts' => self::get_non_elementor_posts_usage(),
				'library' => self::get_library_usage(),
				'settings' => [
					'general' => self::get_settings_general_usage(),
					'advanced' => self::get_settings_advanced_usage(),
					'experiments' => self::get_settings_experiments_usage(),
				],
				'tools' => [
					'general' => self::get_tools_general_usage(),
					'version' => self::get_tools_version_control_usage(),
					'maintenance' => self::get_tools_maintenance_usage(),
				],
				'library-details' => self::get_library_usage_extend(),
			],
			'is_first_time' => $is_first_time,
			'install_time' => Plugin::instance()->get_install_time(),
		];

		$site_key = Api::get_site_key();
		if ( ! empty( $site_key ) ) {
			$params['site_key'] = $site_key;
		}

		$allowed_usage_time = self::get_last_update_time();
		if ( ! empty( $allowed_usage_time ) ) {
			$params['allowed_usage_time'] = $allowed_usage_time;
		}

		/**
		 * Tracker send tracking data params.
		 *
		 * Filters the data parameters when sending tracking request.
		 *
		 * @param array $params Variable to encode as JSON.
		 *
		 * @since 1.0.0
		 */
		$params = apply_filters( 'elementor/tracker/send_tracking_data_params', $params );

		return $params;
	}

	/**
	 * @param string $tab_name
	 * @return array
	 */
	private static function get_tracking_data_from_settings( $tab_name ) {
		return self::get_tracking_data_from_settings_page(
			Plugin::$instance->settings->get_tabs(),
			$tab_name
		);
	}

	/**
	 * @param string $tab_name
	 * @return array
	 */
	private static function get_tracking_data_from_tools( $tab_name ) {
		return self::get_tracking_data_from_settings_page(
			Plugin::$instance->tools->get_tabs(),
			$tab_name
		);
	}

	private static function get_tracking_data_from_settings_page( $tabs, $tab_name ) {
		$result = [];

		if ( empty( $tabs[ $tab_name ] ) ) {
			return $result;
		}

		$tab = $tabs[ $tab_name ];

		foreach ( $tab['sections'] as $section_name => $section ) {
			foreach ( $section['fields'] as $field_name => $field ) {
				// Skips fields with '_' prefix.
				if ( '_' === $field_name[0] ) {
					continue;
				}

				$default_value = null;
				$args = $field['field_args'];
				switch ( $args['type'] ) {
					case 'checkbox':
						$default_value = $args['value'];
						break;

					case 'select':
					case 'checkbox_list_cpt':
						$default_value = $args['std'];
						break;

					case 'checkbox_list_roles':
						$default_value = null;
						break;

					// 'raw_html' is used as action and not as data.
					case 'raw_html':
						continue 2; // Skip fields loop.

					default:
						trigger_error( 'Invalid type: \'' . $args['type'] . '\'' ); // phpcs:ignore
				}

				$result[ $field_name ] = get_option( 'elementor_' . $field_name, $default_value );
			}
		}

		return $result;
	}
}
