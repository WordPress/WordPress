<?php
/**
 * Class for WPPost To order table migrator.
 */

namespace Automattic\WooCommerce\Database\Migrations\CustomOrderTable;

use Automattic\WooCommerce\Database\Migrations\MetaToCustomTableMigrator;

/**
 * Helper class to migrate records from the WordPress post table
 * to the custom order table (and only that table - PostsToOrdersMigrationController
 * is used for fully migrating orders).
 */
class PostToOrderTableMigrator extends MetaToCustomTableMigrator {

	/**
	 * Get schema config for wp_posts and wc_order table.
	 *
	 * @return array Config.
	 */
	protected function get_schema_config(): array {
		global $wpdb;

		$this->table_names = array(
			'orders'    => $wpdb->prefix . 'wc_orders',
			'addresses' => $wpdb->prefix . 'wc_order_addresses',
			'op_data'   => $wpdb->prefix . 'wc_order_operational_data',
			'meta'      => $wpdb->prefix . 'wc_orders_meta',
		);

		return array(
			'source'      => array(
				'entity' => array(
					'table_name'             => $wpdb->posts,
					'meta_rel_column'        => 'ID',
					'destination_rel_column' => 'ID',
					'primary_key'            => 'ID',
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
				'table_name'        => $this->table_names['orders'],
				'source_rel_column' => 'id',
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
		return array(
			'ID'                => array(
				'type'        => 'int',
				'destination' => 'id',
			),
			'post_status'       => array(
				'type'        => 'string',
				'destination' => 'status',
			),
			'post_date_gmt'     => array(
				'type'        => 'date',
				'destination' => 'date_created_gmt',
			),
			'post_modified_gmt' => array(
				'type'        => 'date',
				'destination' => 'date_updated_gmt',
			),
			'post_parent'       => array(
				'type'        => 'int',
				'destination' => 'parent_order_id',
			),
			'post_type'         => array(
				'type'        => 'string',
				'destination' => 'type',
			),
			'post_excerpt'      => array(
				'type'        => 'string',
				'destination' => 'customer_note',
			),
		);
	}

	/**
	 * Get meta data config.
	 *
	 * @return \string[][] Config.
	 */
	public function get_meta_column_config(): array {
		return array(
			'_order_currency'       => array(
				'type'        => 'string',
				'destination' => 'currency',
			),
			'_order_tax'            => array(
				'type'        => 'decimal',
				'destination' => 'tax_amount',
			),
			'_order_total'          => array(
				'type'        => 'decimal',
				'destination' => 'total_amount',
			),
			'_customer_user'        => array(
				'type'        => 'int',
				'destination' => 'customer_id',
			),
			'_billing_email'        => array(
				'type'        => 'string',
				'destination' => 'billing_email',
			),
			'_payment_method'       => array(
				'type'        => 'string',
				'destination' => 'payment_method',
			),
			'_payment_method_title' => array(
				'type'        => 'string',
				'destination' => 'payment_method_title',
			),
			'_customer_ip_address'  => array(
				'type'        => 'string',
				'destination' => 'ip_address',
			),
			'_customer_user_agent'  => array(
				'type'        => 'string',
				'destination' => 'user_agent',
			),
			'_transaction_id'       => array(
				'type'        => 'string',
				'destination' => 'transaction_id',
			),
		);
	}
}
