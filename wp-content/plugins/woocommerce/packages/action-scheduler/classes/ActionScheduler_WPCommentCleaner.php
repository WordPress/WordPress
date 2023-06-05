<?php

/**
 * Class ActionScheduler_WPCommentCleaner
 *
 * @since 3.0.0
 */
class ActionScheduler_WPCommentCleaner {

	/**
	 * Post migration hook used to cleanup the WP comment table.
	 *
	 * @var string
	 */
	protected static $cleanup_hook = 'action_scheduler/cleanup_wp_comment_logs';

	/**
	 * An instance of the ActionScheduler_wpCommentLogger class to interact with the comments table.
	 *
	 * This instance should only be used as an interface. It should not be initialized.
	 *
	 * @var ActionScheduler_wpCommentLogger
	 */
	protected static $wp_comment_logger = null;

	/**
	 * The key used to store the cached value of whether there are logs in the WP comment table.
	 *
	 * @var string
	 */
	protected static $has_logs_option_key = 'as_has_wp_comment_logs';

	/**
	 * Initialize the class and attach callbacks.
	 */
	public static function init() {
		if ( empty( self::$wp_comment_logger ) ) {
			self::$wp_comment_logger = new ActionScheduler_wpCommentLogger();
		}

		add_action( self::$cleanup_hook, array( __CLASS__, 'delete_all_action_comments' ) );

		// While there are orphaned logs left in the comments table, we need to attach the callbacks which filter comment counts.
		add_action( 'pre_get_comments', array( self::$wp_comment_logger, 'filter_comment_queries' ), 10, 1 );
		add_action( 'wp_count_comments', array( self::$wp_comment_logger, 'filter_comment_count' ), 20, 2 ); // run after WC_Comments::wp_count_comments() to make sure we exclude order notes and action logs
		add_action( 'comment_feed_where', array( self::$wp_comment_logger, 'filter_comment_feed' ), 10, 2 );

		// Action Scheduler may be displayed as a Tools screen or WooCommerce > Status administration screen
		add_action( 'load-tools_page_action-scheduler', array( __CLASS__, 'register_admin_notice' ) );
		add_action( 'load-woocommerce_page_wc-status', array( __CLASS__, 'register_admin_notice' ) );
	}

	/**
	 * Determines if there are log entries in the wp comments table.
	 *
	 * Uses the flag set on migration completion set by @see self::maybe_schedule_cleanup().
	 *
	 * @return boolean Whether there are scheduled action comments in the comments table.
	 */
	public static function has_logs() {
		return 'yes' === get_option( self::$has_logs_option_key );
	}

	/**
	 * Schedules the WP Post comment table cleanup to run in 6 months if it's not already scheduled.
	 * Attached to the migration complete hook 'action_scheduler/migration_complete'.
	 */
	public static function maybe_schedule_cleanup() {
		if ( (bool) get_comments( array( 'type' => ActionScheduler_wpCommentLogger::TYPE, 'number' => 1, 'fields' => 'ids' ) ) ) {
			update_option( self::$has_logs_option_key, 'yes' );

			if ( ! as_next_scheduled_action( self::$cleanup_hook ) ) {
				as_schedule_single_action( gmdate( 'U' ) + ( 6 * MONTH_IN_SECONDS ), self::$cleanup_hook );
			}
		}
	}

	/**
	 * Delete all action comments from the WP Comments table.
	 */
	public static function delete_all_action_comments() {
		global $wpdb;
		$wpdb->delete( $wpdb->comments, array( 'comment_type' => ActionScheduler_wpCommentLogger::TYPE, 'comment_agent' => ActionScheduler_wpCommentLogger::AGENT ) );
		delete_option( self::$has_logs_option_key );
	}

	/**
	 * Registers admin notices about the orphaned action logs.
	 */
	public static function register_admin_notice() {
		add_action( 'admin_notices', array( __CLASS__, 'print_admin_notice' ) );
	}
	
	/**
	 * Prints details about the orphaned action logs and includes information on where to learn more.
	 */
	public static function print_admin_notice() {
		$next_cleanup_message        = '';
		$next_scheduled_cleanup_hook = as_next_scheduled_action( self::$cleanup_hook );

		if ( $next_scheduled_cleanup_hook ) {
			/* translators: %s: date interval */
			$next_cleanup_message = sprintf( __( 'This data will be deleted in %s.', 'woocommerce' ), human_time_diff( gmdate( 'U' ), $next_scheduled_cleanup_hook ) );
		}

		$notice = sprintf(
			/* translators: 1: next cleanup message 2: github issue URL */
			__( 'Action Scheduler has migrated data to custom tables; however, orphaned log entries exist in the WordPress Comments table. %1$s <a href="%2$s">Learn more &raquo;</a>', 'woocommerce' ),
			$next_cleanup_message,
			'https://github.com/woocommerce/action-scheduler/issues/368'
		);

		echo '<div class="notice notice-warning"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}
}
