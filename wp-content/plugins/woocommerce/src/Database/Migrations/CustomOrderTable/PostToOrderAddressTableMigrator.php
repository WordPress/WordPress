<?php
/**
 * Class for WPPost to wc_order_address table migrator.
 */

namespace Automattic\WooCommerce\Database\Migrations\CustomOrderTable;

use Automattic\WooCommerce\Database\Migrations\MetaToCustomTableMigrator;

/**
 * Helper class to migrate records from the WordPress post table
 * to the custom order addresses table.
 *
 * @package Automattic\WooCommerce\Database\Migrations\CustomOrderTable
 */
class PostToOrderAddressTableMigrator extends MetaToCustomTableMigrator {
	/**
	 * Type of addresses being migrated, could be billing|shipping.
	 *
	 * @var $type
	 */
	protected $type;

	/**
	 * PostToOrderAddressTableMigrator constructor.
	 *
	 * @param string $type Type of addresses being migrated, could be billing|shipping.
	 */
	public function __construct( $type ) {
		$this->type = $type;
		parent::__construct();
	}

	/**
	 * Get schema config for wp_posts and wc_order_address table.
	 *
	 * @return array Config.
	 */
	protected function get_schema_config(): array {
		global $wpdb;
		// TODO: Remove hardcoding.
		$this->table_names = array(
			'orders'    => $wpdb->prefix . 'wc_orders',
			'addresses' => $wpdb->prefix . 'wc_order_addresses',
			'op_data'   => $wpdb->prefix . 'wc_order_operational_data',
			'meta'      => $wpdb->prefix . 'wc_orders_meta',
		);

		return array(
			'source'      => array(
				'entity' => array(
					'table_name'             => $this->table_names['orders'],
					'meta_rel_column'        => 'id',
					'destination_rel_column' => 'id',
					'primary_key'            => 'id',
				),
				'meta'   => array(
					'table_name'        => $wpdb->postmeta,
					'meta_id_column'    => 'meta_id',
					'meta_key_column'   => 'meta_key',
					'meta_value_column' => 'meta_value',
					'entity_id_column'  => 'post_id',
				),
			),
			'destination' => array(
				'table_name'        => $this->table_names['addresses'],
				'source_rel_column' => 'order_id',
				'primary_key'       => 'id',
				'primary_key_type'  => 'int',
			),
		);
	}

	/**
	 * Get columns config.
	 *
	 * @return \string[][] Config.
	 */
	protected function get_core_column_mapping(): array {
		$type = $this->type;

		return array(
			'id'   => array(
				'type'        => 'int',
				'destination' => 'order_id',
			),
			'type' => array(
				'type'          => 'string',
				'destination'   => 'address_type',
				'select_clause' => "'$type'",
			),
		);
	}

	/**
	 * Get meta data config.
	 *
	 * @return \string[][] Config.
	 */
	public function get_meta_column_config(): array {
		$type = $this->type;

		return array(
			"_{$type}_first_name" => array(
				'type'        => 'string',
				'destination' => 'first_name',
			),
			"_{$type}_last_name"  => array(
				'type'        => 'string',
				'destination' => 'last_name',
			),
			"_{$type}_company"    => array(
				'type'        => 'string',
				'destination' => 'company',
			),
			"_{$type}_address_1"  => array(
				'type'        => 'string',
				'destination' => 'address_1',
			),
			"_{$type}_address_2"  => array(
				'type'        => 'string',
				'destination' => 'address_2',
			),
			"_{$type}_city"       => array(
				'type'        => 'string',
				'destination' => 'city',
			),
			"_{$type}_state"      => array(
				'type'        => 'string',
				'destination' => 'state',
			),
			"_{$type}_postcode"   => array(
				'type'        => 'string',
				'destination' => 'postcode',
			),
			"_{$type}_country"    => array(
				'type'        => 'string',
				'destination' => 'country',
			),
			"_{$type}_email"      => array(
				'type'        => 'string',
				'destination' => 'email',
			),
			"_{$type}_phone"      => array(
				'type'        => 'string',
				'destination' => 'phone',
			),
		);
	}

	/**
	 * Additional WHERE clause to only fetch the addresses of the current type.
	 *
	 * @param array $entity_ids The ids of the entities being inserted or updated.
	 * @return string The additional string for the WHERE clause.
	 */
	protected function get_additional_where_clause_for_get_data_to_insert_or_update( array $entity_ids ): string {
		return "AND destination.`address_type` = '{$this->type}'";
	}

	/**
	 * Helper function to generate where clause for fetching data for verification.
	 *
	 * @param array $source_ids Array of IDs from source table.
	 *
	 * @return string WHERE clause.
	 */
	protected function get_where_clause_for_verification( $source_ids ) {
		global $wpdb;
		$query = parent::get_where_clause_for_verification( $source_ids );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $query should already be prepared, $schema_config is hardcoded.
		return $wpdb->prepare( "$query AND {$this->schema_config['destination']['table_name']}.address_type = %s", $this->type );
	}
}
