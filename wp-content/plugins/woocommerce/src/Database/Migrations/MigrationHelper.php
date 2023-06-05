<?php
/**
 * Helper class with utility functions for migrations.
 */

namespace Automattic\WooCommerce\Database\Migrations;

use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;
use Automattic\WooCommerce\Utilities\ArrayUtil;
use Automattic\WooCommerce\Utilities\StringUtil;

/**
 * Helper class to assist with migration related operations.
 */
class MigrationHelper {

	/**
	 * Placeholders that we will use in building $wpdb queries.
	 *
	 * @var string[]
	 */
	private static $wpdb_placeholder_for_type = array(
		'int'        => '%d',
		'decimal'    => '%f',
		'string'     => '%s',
		'date'       => '%s',
		'date_epoch' => '%s',
		'bool'       => '%d',
	);

	/**
	 * Helper method to escape backtick in various schema fields.
	 *
	 * @param array $schema_config Schema config.
	 *
	 * @return array Schema config escaped for backtick.
	 */
	public static function escape_schema_for_backtick( array $schema_config ): array {
		array_walk( $schema_config['source']['entity'], array( self::class, 'escape_and_add_backtick' ) );
		array_walk( $schema_config['source']['meta'], array( self::class, 'escape_and_add_backtick' ) );
		array_walk( $schema_config['destination'], array( self::class, 'escape_and_add_backtick' ) );
		return $schema_config;
	}

	/**
	 * Helper method to escape backtick in column and table names.
	 * WP does not provide a method to escape table/columns names yet, but hopefully soon in @link https://core.trac.wordpress.org/ticket/52506
	 *
	 * @param string|array $identifier Column or table name.
	 *
	 * @return array|string|string[] Escaped identifier.
	 */
	public static function escape_and_add_backtick( $identifier ) {
		return '`' . str_replace( '`', '``', $identifier ) . '`';
	}

	/**
	 * Return $wpdb->prepare placeholder for data type.
	 *
	 * @param string $type Data type.
	 *
	 * @return string $wpdb placeholder.
	 */
	public static function get_wpdb_placeholder_for_type( string $type ): string {
		return self::$wpdb_placeholder_for_type[ $type ];
	}

	/**
	 * Generates ON DUPLICATE KEY UPDATE clause to be used in migration.
	 *
	 * @param array $columns List of column names.
	 *
	 * @return string SQL clause for INSERT...ON DUPLICATE KEY UPDATE
	 */
	public static function generate_on_duplicate_statement_clause( array $columns ): string {
		$db_util = wc_get_container()->get( DatabaseUtil::class );
		return $db_util->generate_on_duplicate_statement_clause( $columns );
	}

	/**
	 * Migrate state codes in all the required places in the database, needed after they change for a given country.
	 *
	 * @param string $country_code The country that has the states for which the migration is needed.
	 * @param array  $old_to_new_states_mapping An associative array where keys are the old state codes and values are the new state codes.
	 * @return bool True if there are more records that need to be migrated, false otherwise.
	 */
	public static function migrate_country_states( string $country_code, array $old_to_new_states_mapping ): bool {
		$more_remaining = self::migrate_country_states_for_orders( $country_code, $old_to_new_states_mapping );
		if ( ! $more_remaining ) {
			self::migrate_country_states_for_misc_data( $country_code, $old_to_new_states_mapping );
		}
		return $more_remaining;
	}

	/**
	 * Migrate state codes in all the required places in the database (except orders).
	 *
	 * @param string $country_code The country that has the states for which the migration is needed.
	 * @param array  $old_to_new_states_mapping An associative array where keys are the old state codes and values are the new state codes.
	 * @return void
	 */
	private static function migrate_country_states_for_misc_data( string $country_code, array $old_to_new_states_mapping ): void {
		self::migrate_country_states_for_shipping_locations( $country_code, $old_to_new_states_mapping );
		self::migrate_country_states_for_tax_rates( $country_code, $old_to_new_states_mapping );
		self::migrate_country_states_for_store_location( $country_code, $old_to_new_states_mapping );
	}

	/**
	 * Migrate state codes in the shipping locations table.
	 *
	 * @param string $country_code The country that has the states for which the migration is needed.
	 * @param array  $old_to_new_states_mapping An associative array where keys are the old state codes and values are the new state codes.
	 * @return void
	 */
	private static function migrate_country_states_for_shipping_locations( string $country_code, array $old_to_new_states_mapping ): void {
		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared

		$sql            = "SELECT location_id, location_code FROM {$wpdb->prefix}woocommerce_shipping_zone_locations WHERE location_code LIKE '{$country_code}:%'";
		$locations_data = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $locations_data as $location_data ) {
			$old_state_code = substr( $location_data['location_code'], 3 );
			if ( array_key_exists( $old_state_code, $old_to_new_states_mapping ) ) {
				$new_location_code = "{$country_code}:{$old_to_new_states_mapping[$old_state_code]}";
				$update_query      = $wpdb->prepare(
					"UPDATE {$wpdb->prefix}woocommerce_shipping_zone_locations SET location_code=%s WHERE location_id=%d",
					$new_location_code,
					$location_data['location_id']
				);
				$wpdb->query( $update_query );
			}
		}

		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Migrate the state code for the store location.
	 *
	 * @param string $country_code The country that has the states for which the migration is needed.
	 * @param array  $old_to_new_states_mapping An associative array where keys are the old state codes and values are the new state codes.
	 * @return void
	 */
	private static function migrate_country_states_for_store_location( string $country_code, array $old_to_new_states_mapping ): void {
		$store_location = get_option( 'woocommerce_default_country', '' );
		if ( StringUtil::starts_with( $store_location, "{$country_code}:" ) ) {
			$old_location_code = substr( $store_location, 3 );
			if ( array_key_exists( $old_location_code, $old_to_new_states_mapping ) ) {
				$new_location_code = "{$country_code}:{$old_to_new_states_mapping[$old_location_code]}";
				update_option( 'woocommerce_default_country', $new_location_code );
			}
		}
	}

	/**
	 * Migrate state codes for orders in the orders table and in the posts table.
	 * It will migrate only N*2*(number of states) records, being N equal to 100 by default
	 * but this number can be modified via the woocommerce_migrate_country_states_for_orders_batch_size filter.
	 *
	 * @param string $country_code The country that has the states for which the migration is needed.
	 * @param array  $old_to_new_states_mapping An associative array where keys are the old state codes and values are the new state codes.
	 * @return bool True if there are more records that need to be migrated, false otherwise.
	 */
	private static function migrate_country_states_for_orders( string $country_code, array $old_to_new_states_mapping ): bool {
		global $wpdb;

		/**
		 * Filters the value of N, where the maximum count of database records that will be updated in one single run of migrate_country_states_for_orders
		 * is N*2*count($old_to_new_states_mapping) if the woocommerce_orders table exists, or N*count($old_to_new_states_mapping) otherwise.
		 *
		 * @param int $batch_size Default value for the count of records to update.
		 * @param string $country_code Country code for the update.
		 * @param array  $old_to_new_states_mapping Associative array of old to new state codes.
		 *
		 * @since 7.2.0
		 */
		$limit      = apply_filters( 'woocommerce_migrate_country_states_for_orders_batch_size', 100, $country_code, $old_to_new_states_mapping );
		$cot_exists = wc_get_container()->get( DataSynchronizer::class )->check_orders_table_exists();

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		foreach ( $old_to_new_states_mapping as $old_state => $new_state ) {
			if ( $cot_exists ) {
				$update_query = $wpdb->prepare(
					"UPDATE {$wpdb->prefix}wc_order_addresses SET state=%s WHERE country=%s AND state=%s LIMIT %d",
					$new_state,
					$country_code,
					$old_state,
					$limit
				);

				$wpdb->query( $update_query );
			}

			// We need to split the update query for the postmeta table in two, select + update,
			// because MySQL doesn't support the LIMIT keyword in multi-table UPDATE statements.

			$select_meta_ids_query = $wpdb->prepare(
				"SELECT meta_id FROM {$wpdb->prefix}postmeta,
					(SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta
					WHERE (meta_key = '_billing_country' OR meta_key='_shipping_country') AND meta_value=%s)
					AS states_in_country
				WHERE (meta_key='_billing_state' OR meta_key='_shipping_state')
				AND meta_value=%s
				AND {$wpdb->postmeta}.post_id = states_in_country.post_id
				LIMIT %d",
				$country_code,
				$old_state,
				$limit
			);

			$meta_ids = $wpdb->get_results( $select_meta_ids_query, ARRAY_A );
			if ( ! empty( $meta_ids ) ) {
				$meta_ids                    = ArrayUtil::select( $meta_ids, 'meta_id' );
				$meta_ids_as_comma_separated = '(' . join( ',', $meta_ids ) . ')';

				$update_query = $wpdb->prepare(
					"UPDATE {$wpdb->prefix}postmeta
					SET meta_value=%s
					WHERE meta_id IN {$meta_ids_as_comma_separated}",
					$new_state
				);

				$wpdb->query( $update_query );
			}
		}

		$states_as_comma_separated = "('" . join( "','", array_keys( $old_to_new_states_mapping ) ) . "')";

		$posts_exist_query = $wpdb->prepare(
			"
			SELECT 1 FROM {$wpdb->prefix}postmeta
			WHERE (meta_key='_billing_state' OR meta_key='_shipping_state')
			AND meta_value IN {$states_as_comma_separated}
			AND post_id IN (
				SELECT post_id FROM {$wpdb->prefix}postmeta WHERE
				(meta_key = '_billing_country' OR meta_key='_shipping_country')
				AND meta_value=%s
			)",
			$country_code
		);

		if ( $cot_exists ) {
			$more_exist_query = $wpdb->prepare(
				"
			SELECT EXISTS(
				SELECT 1 FROM {$wpdb->prefix}wc_order_addresses
				WHERE country=%s AND state IN {$states_as_comma_separated}
			)
			OR EXISTS (
			  {$posts_exist_query}
			)",
				$country_code
			);
		} else {
			$more_exist_query = "SELECT EXISTS ({$posts_exist_query})";
		}

		return (int) ( $wpdb->get_var( $more_exist_query ) ) !== 0;

		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Migrate state codes in the tax rates table.
	 *
	 * @param string $country_code The country that has the states for which the migration is needed.
	 * @param array  $old_to_new_states_mapping An associative array where keys are the old state codes and values are the new state codes.
	 * @return void
	 */
	private static function migrate_country_states_for_tax_rates( string $country_code, array $old_to_new_states_mapping ): void {
		global $wpdb;

		foreach ( $old_to_new_states_mapping as $old_state_code => $new_state_code ) {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}woocommerce_tax_rates SET tax_rate_state=%s WHERE tax_rate_country=%s AND tax_rate_state=%s",
					$new_state_code,
					$country_code,
					$old_state_code
				)
			);
		}
	}
}
