<?php

/**
 * Class ActionScheduler_AdminView_Deprecated
 *
 * Store deprecated public functions previously found in the ActionScheduler_AdminView class.
 * Keeps them out of the way of the main class.
 *
 * @codeCoverageIgnore
 */
class ActionScheduler_AdminView_Deprecated {

	public function action_scheduler_post_type_args( $args ) {
		_deprecated_function( __METHOD__, '2.0.0' );
		return $args;
	}

	/**
	 * Customise the post status related views displayed on the Scheduled Actions administration screen.
	 *
	 * @param array $views An associative array of views and view labels which can be used to filter the 'scheduled-action' posts displayed on the Scheduled Actions administration screen.
	 * @return array $views An associative array of views and view labels which can be used to filter the 'scheduled-action' posts displayed on the Scheduled Actions administration screen.
	 */
	public function list_table_views( $views ) {
		_deprecated_function( __METHOD__, '2.0.0' );
		return $views;
	}

	/**
	 * Do not include the "Edit" action for the Scheduled Actions administration screen.
	 *
	 * Hooked to the 'bulk_actions-edit-action-scheduler' filter.
	 *
	 * @param array $actions An associative array of actions which can be performed on the 'scheduled-action' post type.
	 * @return array $actions An associative array of actions which can be performed on the 'scheduled-action' post type.
	 */
	public function bulk_actions( $actions ) {
		_deprecated_function( __METHOD__, '2.0.0' );
		return $actions;
	}

	/**
	 * Completely customer the columns displayed on the Scheduled Actions administration screen.
	 *
	 * Because we can't filter the content of the default title and date columns, we need to recreate our own
	 * custom columns for displaying those post fields. For the column content, @see self::list_table_column_content().
	 *
	 * @param array $columns An associative array of columns that are use for the table on the Scheduled Actions administration screen.
	 * @return array $columns An associative array of columns that are use for the table on the Scheduled Actions administration screen.
	 */
	public function list_table_columns( $columns ) {
		_deprecated_function( __METHOD__, '2.0.0' );
		return $columns;
	}

	/**
	 * Make our custom title & date columns use defaulting title & date sorting.
	 *
	 * @param array $columns An associative array of columns that can be used to sort the table on the Scheduled Actions administration screen.
	 * @return array $columns An associative array of columns that can be used to sort the table on the Scheduled Actions administration screen.
	 */
	public static function list_table_sortable_columns( $columns ) {
		_deprecated_function( __METHOD__, '2.0.0' );
		return $columns;
	}

	/**
	 * Print the content for our custom columns.
	 *
	 * @param string $column_name The key for the column for which we should output our content.
	 * @param int $post_id The ID of the 'scheduled-action' post for which this row relates.
	 */
	public static function list_table_column_content( $column_name, $post_id ) {
		_deprecated_function( __METHOD__, '2.0.0' );
	}

	/**
	 * Hide the inline "Edit" action for all 'scheduled-action' posts.
	 *
	 * Hooked to the 'post_row_actions' filter.
	 *
	 * @param array $actions An associative array of actions which can be performed on the 'scheduled-action' post type.
	 * @return array $actions An associative array of actions which can be performed on the 'scheduled-action' post type.
	 */
	public static function row_actions( $actions, $post ) {
		_deprecated_function( __METHOD__, '2.0.0' );
		return $actions;
	}

	/**
	 * Run an action when triggered from the Action Scheduler administration screen.
	 *
	 * @codeCoverageIgnore
	 */
	public static function maybe_execute_action() {
		_deprecated_function( __METHOD__, '2.0.0' );
	}

	/**
	 * Convert an interval of seconds into a two part human friendly string.
	 *
	 * The WordPress human_time_diff() function only calculates the time difference to one degree, meaning
	 * even if an action is 1 day and 11 hours away, it will display "1 day". This funciton goes one step
	 * further to display two degrees of accuracy.
	 *
	 * Based on Crontrol::interval() function by Edward Dale: https://wordpress.org/plugins/wp-crontrol/
	 *
	 * @param int $interval A interval in seconds.
	 * @return string A human friendly string representation of the interval.
	 */
	public static function admin_notices() {
		_deprecated_function( __METHOD__, '2.0.0' );
	}

	/**
	 * Filter search queries to allow searching by Claim ID (i.e. post_password).
	 *
	 * @param string $orderby MySQL orderby string.
	 * @param WP_Query $query Instance of a WP_Query object
	 * @return string MySQL orderby string.
	 */
	public function custom_orderby( $orderby, $query ){
		_deprecated_function( __METHOD__, '2.0.0' );
	}

	/**
	 * Filter search queries to allow searching by Claim ID (i.e. post_password).
	 *
	 * @param string $search MySQL search string.
	 * @param WP_Query $query Instance of a WP_Query object
	 * @return string MySQL search string.
	 */
	public function search_post_password( $search, $query ) {
		_deprecated_function( __METHOD__, '2.0.0' );
	}

	/**
	 * Change messages when a scheduled action is updated.
	 *
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		_deprecated_function( __METHOD__, '2.0.0' );
		return $messages;
	}
}