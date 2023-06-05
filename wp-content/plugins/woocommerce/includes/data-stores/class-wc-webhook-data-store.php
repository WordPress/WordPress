<?php
/**
 * Webhook Data Store
 *
 * @version  3.3.0
 * @package  WooCommerce\Classes\Data_Store
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Webhook data store class.
 */
class WC_Webhook_Data_Store implements WC_Webhook_Data_Store_Interface {

	/**
	 * Create a new webhook in the database.
	 *
	 * @since 3.3.0
	 * @param WC_Webhook $webhook Webhook instance.
	 */
	public function create( &$webhook ) {
		global $wpdb;

		$changes = $webhook->get_changes();
		if ( isset( $changes['date_created'] ) ) {
			$date_created     = $webhook->get_date_created()->date( 'Y-m-d H:i:s' );
			$date_created_gmt = gmdate( 'Y-m-d H:i:s', $webhook->get_date_created()->getTimestamp() );
		} else {
			$date_created     = current_time( 'mysql' );
			$date_created_gmt = current_time( 'mysql', 1 );
			$webhook->set_date_created( $date_created );
		}

		// Pending delivery by default if not set while creating a new webhook.
		if ( ! isset( $changes['pending_delivery'] ) ) {
			$webhook->set_pending_delivery( true );
		}

		$data = array(
			'status'           => $webhook->get_status( 'edit' ),
			'name'             => $webhook->get_name( 'edit' ),
			'user_id'          => $webhook->get_user_id( 'edit' ),
			'delivery_url'     => $webhook->get_delivery_url( 'edit' ),
			'secret'           => $webhook->get_secret( 'edit' ),
			'topic'            => $webhook->get_topic( 'edit' ),
			'date_created'     => $date_created,
			'date_created_gmt' => $date_created_gmt,
			'api_version'      => $this->get_api_version_number( $webhook->get_api_version( 'edit' ) ),
			'failure_count'    => $webhook->get_failure_count( 'edit' ),
			'pending_delivery' => $webhook->get_pending_delivery( 'edit' ),
		);

		$wpdb->insert( $wpdb->prefix . 'wc_webhooks', $data ); // WPCS: DB call ok.

		$webhook_id = $wpdb->insert_id;
		$webhook->set_id( $webhook_id );
		$webhook->apply_changes();

		$this->delete_transients( $webhook->get_status( 'edit' ) );
		WC_Cache_Helper::invalidate_cache_group( 'webhooks' );
		do_action( 'woocommerce_new_webhook', $webhook_id, $webhook );
	}

	/**
	 * Read a webhook from the database.
	 *
	 * @since  3.3.0
	 * @param  WC_Webhook $webhook Webhook instance.
	 * @throws Exception When webhook is invalid.
	 */
	public function read( &$webhook ) {
		global $wpdb;

		$data = wp_cache_get( $webhook->get_id(), 'webhooks' );

		if ( false === $data ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT webhook_id, status, name, user_id, delivery_url, secret, topic, date_created, date_modified, api_version, failure_count, pending_delivery FROM {$wpdb->prefix}wc_webhooks WHERE webhook_id = %d LIMIT 1;", $webhook->get_id() ), ARRAY_A ); // WPCS: cache ok, DB call ok.

			wp_cache_add( $webhook->get_id(), $data, 'webhooks' );
		}

		if ( is_array( $data ) ) {
			$webhook->set_props(
				array(
					'id'               => $data['webhook_id'],
					'status'           => $data['status'],
					'name'             => $data['name'],
					'user_id'          => $data['user_id'],
					'delivery_url'     => $data['delivery_url'],
					'secret'           => $data['secret'],
					'topic'            => $data['topic'],
					'date_created'     => '0000-00-00 00:00:00' === $data['date_created'] ? null : $data['date_created'],
					'date_modified'    => '0000-00-00 00:00:00' === $data['date_modified'] ? null : $data['date_modified'],
					'api_version'      => $data['api_version'],
					'failure_count'    => $data['failure_count'],
					'pending_delivery' => $data['pending_delivery'],
				)
			);
			$webhook->set_object_read( true );

			do_action( 'woocommerce_webhook_loaded', $webhook );
		} else {
			throw new Exception( __( 'Invalid webhook.', 'woocommerce' ) );
		}
	}

	/**
	 * Update a webhook.
	 *
	 * @since 3.3.0
	 * @param WC_Webhook $webhook Webhook instance.
	 */
	public function update( &$webhook ) {
		global $wpdb;

		$changes = $webhook->get_changes();
		$trigger = isset( $changes['delivery_url'] );

		if ( isset( $changes['date_modified'] ) ) {
			$date_modified     = $webhook->get_date_modified()->date( 'Y-m-d H:i:s' );
			$date_modified_gmt = gmdate( 'Y-m-d H:i:s', $webhook->get_date_modified()->getTimestamp() );
		} else {
			$date_modified     = current_time( 'mysql' );
			$date_modified_gmt = current_time( 'mysql', 1 );
			$webhook->set_date_modified( $date_modified );
		}

		$data = array(
			'status'            => $webhook->get_status( 'edit' ),
			'name'              => $webhook->get_name( 'edit' ),
			'user_id'           => $webhook->get_user_id( 'edit' ),
			'delivery_url'      => $webhook->get_delivery_url( 'edit' ),
			'secret'            => $webhook->get_secret( 'edit' ),
			'topic'             => $webhook->get_topic( 'edit' ),
			'date_modified'     => $date_modified,
			'date_modified_gmt' => $date_modified_gmt,
			'api_version'       => $this->get_api_version_number( $webhook->get_api_version( 'edit' ) ),
			'failure_count'     => $webhook->get_failure_count( 'edit' ),
			'pending_delivery'  => $webhook->get_pending_delivery( 'edit' ),
		);

		$wpdb->update(
			$wpdb->prefix . 'wc_webhooks',
			$data,
			array(
				'webhook_id' => $webhook->get_id(),
			)
		); // WPCS: DB call ok.

		$webhook->apply_changes();

		if ( isset( $changes['status'] ) ) {
			// We need to delete all transients, because we can't be sure of the old status.
			$this->delete_transients( 'all' );
		}
		wp_cache_delete( $webhook->get_id(), 'webhooks' );
		WC_Cache_Helper::invalidate_cache_group( 'webhooks' );

		if ( 'active' === $webhook->get_status() && ( $trigger || $webhook->get_pending_delivery() ) ) {
			$webhook->deliver_ping();
		}

		do_action( 'woocommerce_webhook_updated', $webhook->get_id() );
	}

	/**
	 * Remove a webhook from the database.
	 *
	 * @since 3.3.0
	 * @param WC_Webhook $webhook      Webhook instance.
	 */
	public function delete( &$webhook ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'wc_webhooks',
			array(
				'webhook_id' => $webhook->get_id(),
			),
			array( '%d' )
		); // WPCS: cache ok, DB call ok.

		$this->delete_transients( 'all' );
		wp_cache_delete( $webhook->get_id(), 'webhooks' );
		WC_Cache_Helper::invalidate_cache_group( 'webhooks' );
		do_action( 'woocommerce_webhook_deleted', $webhook->get_id(), $webhook );
	}

	/**
	 * Get API version number.
	 *
	 * @since  3.3.0
	 * @param  string $api_version REST API version.
	 * @return int
	 */
	public function get_api_version_number( $api_version ) {
		return 'legacy_v3' === $api_version ? -1 : intval( substr( $api_version, -1 ) );
	}

	/**
	 * Get webhooks IDs from the database.
	 *
	 * @since  3.3.0
	 * @throws InvalidArgumentException If a $status value is passed in that is not in the known wc_get_webhook_statuses() keys.
	 * @param  string $status Optional - status to filter results by. Must be a key in return value of @see wc_get_webhook_statuses(). @since 3.6.0.
	 * @return int[]
	 */
	public function get_webhooks_ids( $status = '' ) {
		if ( ! empty( $status ) ) {
			$this->validate_status( $status );
		}

		$ids = get_transient( $this->get_transient_key( $status ) );

		if ( false === $ids ) {
			$ids = $this->search_webhooks(
				array(
					'limit'  => -1,
					'status' => $status,
				)
			);
			$ids = array_map( 'absint', $ids );
			set_transient( $this->get_transient_key( $status ), $ids );
		}

		return $ids;
	}

	/**
	 * Search webhooks.
	 *
	 * @param  array $args Search arguments.
	 * @return array|object
	 */
	public function search_webhooks( $args ) {
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'limit'    => 10,
				'offset'   => 0,
				'order'    => 'DESC',
				'orderby'  => 'id',
				'paginate' => false,
			)
		);

		// Map post statuses.
		$statuses = array(
			'publish' => 'active',
			'draft'   => 'paused',
			'pending' => 'disabled',
		);

		// Map orderby to support a few post keys.
		$orderby_mapping = array(
			'ID'            => 'webhook_id',
			'id'            => 'webhook_id',
			'name'          => 'name',
			'title'         => 'name',
			'post_title'    => 'name',
			'post_name'     => 'name',
			'date_created'  => 'date_created_gmt',
			'date'          => 'date_created_gmt',
			'post_date'     => 'date_created_gmt',
			'date_modified' => 'date_modified_gmt',
			'modified'      => 'date_modified_gmt',
			'post_modified' => 'date_modified_gmt',
		);
		$orderby         = isset( $orderby_mapping[ $args['orderby'] ] ) ? $orderby_mapping[ $args['orderby'] ] : 'webhook_id';
		$sort            = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
		$order           = "ORDER BY {$orderby} {$sort}";
		$limit           = -1 < $args['limit'] ? $wpdb->prepare( 'LIMIT %d', $args['limit'] ) : '';
		$offset          = 0 < $args['offset'] ? $wpdb->prepare( 'OFFSET %d', $args['offset'] ) : '';
		$status          = ! empty( $args['status'] ) ? $wpdb->prepare( 'AND `status` = %s', isset( $statuses[ $args['status'] ] ) ? $statuses[ $args['status'] ] : $args['status'] ) : '';
		$search          = ! empty( $args['search'] ) ? $wpdb->prepare( 'AND `name` LIKE %s', '%' . $wpdb->esc_like( sanitize_text_field( $args['search'] ) ) . '%' ) : '';
		$include         = '';
		$exclude         = '';
		$date_created    = '';
		$date_modified   = '';

		if ( ! empty( $args['include'] ) ) {
			$args['include'] = implode( ',', wp_parse_id_list( $args['include'] ) );
			$include         = 'AND webhook_id IN (' . $args['include'] . ')';
		}

		if ( ! empty( $args['exclude'] ) ) {
			$args['exclude'] = implode( ',', wp_parse_id_list( $args['exclude'] ) );
			$exclude         = 'AND webhook_id NOT IN (' . $args['exclude'] . ')';
		}

		if ( ! empty( $args['after'] ) || ! empty( $args['before'] ) ) {
			$args['after']  = empty( $args['after'] ) ? '0000-00-00' : $args['after'];
			$args['before'] = empty( $args['before'] ) ? current_time( 'mysql', 1 ) : $args['before'];

			$date_created = "AND `date_created_gmt` BETWEEN STR_TO_DATE('" . esc_sql( $args['after'] ) . "', '%Y-%m-%d %H:%i:%s') and STR_TO_DATE('" . esc_sql( $args['before'] ) . "', '%Y-%m-%d %H:%i:%s')";
		}

		if ( ! empty( $args['modified_after'] ) || ! empty( $args['modified_before'] ) ) {
			$args['modified_after']  = empty( $args['modified_after'] ) ? '0000-00-00' : $args['modified_after'];
			$args['modified_before'] = empty( $args['modified_before'] ) ? current_time( 'mysql', 1 ) : $args['modified_before'];

			$date_modified = "AND `date_modified_gmt` BETWEEN STR_TO_DATE('" . esc_sql( $args['modified_after'] ) . "', '%Y-%m-%d %H:%i:%s') and STR_TO_DATE('" . esc_sql( $args['modified_before'] ) . "', '%Y-%m-%d %H:%i:%s')";
		}

		// Check for cache.
		$cache_key   = WC_Cache_Helper::get_cache_prefix( 'webhooks' ) . 'search_webhooks' . md5( implode( ',', $args ) );
		$cache_value = wp_cache_get( $cache_key, 'webhook_search_results' );

		if ( $cache_value ) {
			return $cache_value;
		}

		if ( $args['paginate'] ) {
			$query = trim(
				"SELECT SQL_CALC_FOUND_ROWS webhook_id
				FROM {$wpdb->prefix}wc_webhooks
				WHERE 1=1
				{$status}
				{$search}
				{$include}
				{$exclude}
				{$date_created}
				{$date_modified}
				{$order}
				{$limit}
				{$offset}"
			);

			$webhook_ids  = wp_parse_id_list( $wpdb->get_col( $query ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$total        = (int) $wpdb->get_var( 'SELECT FOUND_ROWS();' );
			$return_value = (object) array(
				'webhooks'      => $webhook_ids,
				'total'         => $total,
				'max_num_pages' => $args['limit'] > 1 ? ceil( $total / $args['limit'] ) : 1,
			);
		} else {
			$query = trim(
				"SELECT webhook_id
				FROM {$wpdb->prefix}wc_webhooks
				WHERE 1=1
				{$status}
				{$search}
				{$include}
				{$exclude}
				{$date_created}
				{$date_modified}
				{$order}
				{$limit}
				{$offset}"
			);

			$webhook_ids  = wp_parse_id_list( $wpdb->get_col( $query ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$return_value = $webhook_ids;
		}

		wp_cache_set( $cache_key, $return_value, 'webhook_search_results' );

		return $return_value;
	}

	/**
	 * Count webhooks.
	 *
	 * @since 3.6.0
	 * @param string $status Status to count.
	 * @return int
	 */
	protected function get_webhook_count( $status = 'active' ) {
		global $wpdb;
		$cache_key = WC_Cache_Helper::get_cache_prefix( 'webhooks' ) . $status . '_count';
		$count     = wp_cache_get( $cache_key, 'webhooks' );

		if ( false === $count ) {
			$count = absint( $wpdb->get_var( $wpdb->prepare( "SELECT count( webhook_id ) FROM {$wpdb->prefix}wc_webhooks WHERE `status` = %s;", $status ) ) );

			wp_cache_add( $cache_key, $count, 'webhooks' );
		}

		return $count;
	}

	/**
	 * Get total webhook counts by status.
	 *
	 * @return array
	 */
	public function get_count_webhooks_by_status() {
		$statuses = array_keys( wc_get_webhook_statuses() );
		$counts   = array();

		foreach ( $statuses as $status ) {
			$counts[ $status ] = $this->get_webhook_count( $status );
		}

		return $counts;
	}

	/**
	 * Check if a given string is in known statuses, based on return value of @see wc_get_webhook_statuses().
	 *
	 * @since  3.6.0
	 * @throws InvalidArgumentException If $status is not empty and not in the known wc_get_webhook_statuses() keys.
	 * @param  string $status Status to check.
	 */
	private function validate_status( $status ) {
		if ( ! array_key_exists( $status, wc_get_webhook_statuses() ) ) {
			throw new InvalidArgumentException( sprintf( 'Invalid status given: %s. Status must be one of: %s.', $status, implode( ', ', array_keys( wc_get_webhook_statuses() ) ) ) );
		}
	}

	/**
	 * Get the transient key used to cache a set of webhook IDs, optionally filtered by status.
	 *
	 * @since  3.6.0
	 * @param  string $status Optional - status of cache key.
	 * @return string
	 */
	private function get_transient_key( $status = '' ) {
		return empty( $status ) ? 'woocommerce_webhook_ids' : sprintf( 'woocommerce_webhook_ids_status_%s', $status );
	}

	/**
	 * Delete the transients used to cache a set of webhook IDs, optionally filtered by status.
	 *
	 * @since 3.6.0
	 * @param string $status Optional - status of cache to delete, or 'all' to delete all caches.
	 */
	private function delete_transients( $status = '' ) {

		// Always delete the non-filtered cache.
		delete_transient( $this->get_transient_key( '' ) );

		if ( ! empty( $status ) ) {
			if ( 'all' === $status ) {
				foreach ( wc_get_webhook_statuses() as $status_key => $status_string ) {
					delete_transient( $this->get_transient_key( $status_key ) );
				}
			} else {
				delete_transient( $this->get_transient_key( $status ) );
			}
		}
	}
}
