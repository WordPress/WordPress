<?php
/**
 * Customer syncing related functions and actions.
 */

namespace Automattic\WooCommerce\Internal\Admin\Schedulers;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;
use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore as CustomersDataStore;

/**
 * CustomersScheduler Class.
 */
class CustomersScheduler extends ImportScheduler {
	/**
	 * Slug to identify the scheduler.
	 *
	 * @var string
	 */
	public static $name = 'customers';

	/**
	 * Attach customer lookup update hooks.
	 *
	 * @internal
	 */
	public static function init() {
		CustomersDataStore::init();
		parent::init();
	}

	/**
	 * Add customer dependencies.
	 *
	 * @internal
	 * @return array
	 */
	public static function get_dependencies() {
		return array(
			'delete_batch_init' => OrdersScheduler::get_action( 'delete_batch_init' ),
		);
	}

	/**
	 * Get the customer IDs and total count that need to be synced.
	 *
	 * @internal
	 * @param int      $limit Number of records to retrieve.
	 * @param int      $page  Page number.
	 * @param int|bool $days Number of days prior to current date to limit search results.
	 * @param bool     $skip_existing Skip already imported customers.
	 */
	public static function get_items( $limit = 10, $page = 1, $days = false, $skip_existing = false ) {
		$customer_roles = apply_filters( 'woocommerce_analytics_import_customer_roles', array( 'customer' ) );
		$query_args     = array(
			'fields'   => 'ID',
			'orderby'  => 'ID',
			'order'    => 'ASC',
			'number'   => $limit,
			'paged'    => $page,
			'role__in' => $customer_roles,
		);

		if ( is_int( $days ) ) {
			$query_args['date_query'] = array(
				'after' => gmdate( 'Y-m-d 00:00:00', time() - ( DAY_IN_SECONDS * $days ) ),
			);
		}

		if ( $skip_existing ) {
			add_action( 'pre_user_query', array( __CLASS__, 'exclude_existing_customers_from_query' ) );
		}

		$customer_query = new \WP_User_Query( $query_args );

		remove_action( 'pre_user_query', array( __CLASS__, 'exclude_existing_customers_from_query' ) );

		return (object) array(
			'total' => $customer_query->get_total(),
			'ids'   => $customer_query->get_results(),
		);
	}

	/**
	 * Exclude users that already exist in our customer lookup table.
	 *
	 * Meant to be hooked into 'pre_user_query' action.
	 *
	 * @internal
	 * @param WP_User_Query $wp_user_query WP_User_Query to modify.
	 */
	public static function exclude_existing_customers_from_query( $wp_user_query ) {
		global $wpdb;

		$wp_user_query->query_where .= " AND NOT EXISTS (
			SELECT ID FROM {$wpdb->prefix}wc_customer_lookup
			WHERE {$wpdb->prefix}wc_customer_lookup.user_id = {$wpdb->users}.ID
		)";
	}

	/**
	 * Get total number of rows imported.
	 *
	 * @internal
	 * @return int
	 */
	public static function get_total_imported() {
		global $wpdb;
		return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_customer_lookup" );
	}

	/**
	 * Imports a single customer.
	 *
	 * @internal
	 * @param int $user_id User ID.
	 * @return void
	 */
	public static function import( $user_id ) {
		CustomersDataStore::update_registered_customer( $user_id );
	}

	/**
	 * Delete a batch of customers.
	 *
	 * @internal
	 * @param int $batch_size Number of items to delete.
	 * @return void
	 */
	public static function delete( $batch_size ) {
		global $wpdb;

		$customer_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT customer_id FROM {$wpdb->prefix}wc_customer_lookup ORDER BY customer_id ASC LIMIT %d",
				$batch_size
			)
		);

		foreach ( $customer_ids as $customer_id ) {
			CustomersDataStore::delete_customer( $customer_id );
		}
	}
}
