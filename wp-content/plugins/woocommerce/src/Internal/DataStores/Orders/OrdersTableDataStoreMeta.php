<?php
/**
 * OrdersTableDataStoreMeta class file.
 */

namespace Automattic\WooCommerce\Internal\DataStores\Orders;

use Automattic\WooCommerce\Internal\DataStores\CustomMetaDataStore;

/**
 * Mimics a WP metadata (i.e. add_metadata(), get_metadata() and friends) implementation using a custom table.
 */
class OrdersTableDataStoreMeta extends CustomMetaDataStore {

	/**
	 * Returns the name of the table used for storage.
	 *
	 * @return string
	 */
	protected function get_table_name() {
		return OrdersTableDataStore::get_meta_table_name();
	}

	/**
	 * Returns the name of the field/column used for associating meta with objects.
	 *
	 * @return string
	 */
	protected function get_object_id_field() {
		return 'order_id';
	}

}
