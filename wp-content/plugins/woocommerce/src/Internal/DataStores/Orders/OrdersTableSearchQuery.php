<?php

namespace Automattic\WooCommerce\Internal\DataStores\Orders;

use Exception;

/**
 * Creates the join and where clauses needed to perform an order search using Custom Order Tables.
 *
 * @internal
 */
class OrdersTableSearchQuery {
	/**
	 * Holds the Orders Table Query object.
	 *
	 * @var OrdersTableQuery
	 */
	private $query;

	/**
	 * Holds the search term to be used in the WHERE clauses.
	 *
	 * @var string
	 */
	private $search_term;

	/**
	 * Creates the JOIN and WHERE clauses needed to execute a search of orders.
	 *
	 * @internal
	 *
	 * @param OrdersTableQuery $query The order query object.
	 */
	public function __construct( OrdersTableQuery $query ) {
		global $wpdb;
		$this->query         = $query;
		$this->search_term   = esc_sql( '%' . $wpdb->esc_like( urldecode( $query->get( 's' ) ) ) . '%' );
	}

	/**
	 * Supplies an array of clauses to be used in an order query.
	 *
	 * @internal
	 * @throws Exception If unable to generate either the JOIN or WHERE SQL fragments.
	 *
	 * @return array {
	 *     @type string $join  JOIN clause.
	 *     @type string $where WHERE clause.
	 * }
	 */
	public function get_sql_clauses(): array {
		return array(
			'join'  => array( $this->generate_join() ),
			'where' => array( $this->generate_where() ),
		);
	}

	/**
	 * Generates the necessary JOIN clauses for the order search to be performed.
	 *
	 * @throws Exception May be triggered if a table name cannot be determined.
	 *
	 * @return string
	 */
	private function generate_join(): string {
		$orders_table = $this->query->get_table_name( 'orders' );
		$items_table  = $this->query->get_table_name( 'items' );

		return "
			LEFT JOIN $items_table AS search_query_items ON search_query_items.order_id = $orders_table.id
		";
	}

	/**
	 * Generates the necessary WHERE clauses for the order search to be performed.
	 *
	 * @throws Exception May be triggered if a table name cannot be determined.
	 *
	 * @return string
	 */
	private function generate_where(): string {
		global $wpdb;
		$where             = '';
		$possible_order_id = (string) absint( $this->query->get( 's' ) );
		$order_table       = $this->query->get_table_name( 'orders' );

		// Support the passing of an order ID as the search term.
		if ( (string) $this->query->get( 's' ) === $possible_order_id ) {
			$where = "`$order_table`.id = $possible_order_id OR ";
		}

		$meta_sub_query = $this->generate_where_for_meta_table();

		$where .= $wpdb->prepare(
			"
			search_query_items.order_item_name LIKE %s
			OR `$order_table`.id IN ( $meta_sub_query )
			",
			$this->search_term
		);

		return " ( $where ) ";
	}

	/**
	 * Generates where clause for meta table.
	 *
	 * Note we generate the where clause as a subquery to be used by calling function inside the IN clause. This is against the general wisdom for performance, but in this particular case, a subquery is able to use the order_id-meta_key-meta_value index, which is not possible with a join.
	 *
	 * Since it can use the index, which otherwise would not be possible, it is much faster than both LEFT JOIN or SQL_CALC approach that could have been used.
	 *
	 * @return string The where clause for meta table.
	 */
	private function generate_where_for_meta_table(): string {
		global $wpdb;
		$meta_table  = $this->query->get_table_name( 'meta' );
		$meta_fields = $this->get_meta_fields_to_be_searched();
		return $wpdb->prepare(
			"
SELECT search_query_meta.order_id
FROM $meta_table as search_query_meta
WHERE search_query_meta.meta_key IN ( $meta_fields )
AND search_query_meta.meta_value LIKE %s
GROUP BY search_query_meta.order_id
",
			$this->search_term
		);
	}

	/**
	 * Returns the order meta field keys to be searched.
	 *
	 * These will be returned as a single string, where the meta keys have been escaped, quoted and are
	 * comma-separated (ie, "'abc', 'foo'" - ready for inclusion in a SQL IN() clause).
	 *
	 * @return string
	 */
	private function get_meta_fields_to_be_searched(): string {
		/**
		 * Controls the order meta keys to be included in search queries.
		 *
		 * This hook is used when Custom Order Tables are in use: the corresponding hook when CPT-orders are in use
		 * is 'woocommerce_shop_order_search_fields'.
		 *
		 * @since 7.0.0
		 *
		 * @param array
		 */
		$meta_keys = apply_filters(
			'woocommerce_order_table_search_query_meta_keys',
			array(
				'_billing_address_index',
				'_shipping_address_index',
			)
		);

		$meta_keys = (array) array_map(
			function ( string $meta_key ): string {
				return "'" . esc_sql( wc_clean( $meta_key ) ) . "'";
			},
			$meta_keys
		);

		return implode( ',', $meta_keys );
	}
}
