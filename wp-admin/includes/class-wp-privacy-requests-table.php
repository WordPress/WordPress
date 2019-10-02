<?php
/**
 * List Table API: WP_Privacy_Requests_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.9.6
 */

abstract class WP_Privacy_Requests_Table extends WP_List_Table {

	/**
	 * Action name for the requests this table will work with. Classes
	 * which inherit from WP_Privacy_Requests_Table should define this.
	 *
	 * Example: 'export_personal_data'.
	 *
	 * @since 4.9.6
	 *
	 * @var string $request_type Name of action.
	 */
	protected $request_type = 'INVALID';

	/**
	 * Post type to be used.
	 *
	 * @since 4.9.6
	 *
	 * @var string $post_type The post type.
	 */
	protected $post_type = 'INVALID';

	/**
	 * Get columns to show in the list table.
	 *
	 * @since 4.9.6
	 *
	 * @return array Array of columns.
	 */
	public function get_columns() {
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'email'             => __( 'Requester' ),
			'status'            => __( 'Status' ),
			'created_timestamp' => __( 'Requested' ),
			'next_steps'        => __( 'Next Steps' ),
		);
		return $columns;
	}

	/**
	 * Normalize the admin URL to the current page (by request_type).
	 *
	 * @since 5.3.0
	 *
	 * @return string URL to the current admin page.
	 */
	protected function get_admin_url() {
		$pagenow = str_replace( '_', '-', $this->request_type );

		if ( 'remove-personal-data' === $pagenow ) {
			$pagenow = 'erase-personal-data';
		}

		return admin_url( $pagenow . '.php' );
	}

	/**
	 * Get a list of sortable columns.
	 *
	 * @since 4.9.6
	 *
	 * @return array Default sortable columns.
	 */
	protected function get_sortable_columns() {
		// The initial sorting is by 'Requested' (post_date) and descending.
		// With initial sorting, the first click on 'Requested' should be ascending.
		// With 'Requester' sorting active, the next click on 'Requested' should be descending.
		$desc_first = isset( $_GET['orderby'] );

		return array(
			'email'             => 'requester',
			'created_timestamp' => array( 'requested', $desc_first ),
		);
	}

	/**
	 * Default primary column.
	 *
	 * @since 4.9.6
	 *
	 * @return string Default primary column name.
	 */
	protected function get_default_primary_column_name() {
		return 'email';
	}

	/**
	 * Count number of requests for each status.
	 *
	 * @since 4.9.6
	 *
	 * @return object Number of posts for each status.
	 */
	protected function get_request_counts() {
		global $wpdb;

		$cache_key = $this->post_type . '-' . $this->request_type;
		$counts    = wp_cache_get( $cache_key, 'counts' );

		if ( false !== $counts ) {
			return $counts;
		}

		$query = "
			SELECT post_status, COUNT( * ) AS num_posts
			FROM {$wpdb->posts}
			WHERE post_type = %s
			AND post_name = %s
			GROUP BY post_status";

		$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $this->post_type, $this->request_type ), ARRAY_A );
		$counts  = array_fill_keys( get_post_stati(), 0 );

		foreach ( $results as $row ) {
			$counts[ $row['post_status'] ] = $row['num_posts'];
		}

		$counts = (object) $counts;
		wp_cache_set( $cache_key, $counts, 'counts' );

		return $counts;
	}

	/**
	 * Get an associative array ( id => link ) with the list of views available on this table.
	 *
	 * @since 4.9.6
	 *
	 * @return array Associative array of views in the format of $view_name => $view_markup.
	 */
	protected function get_views() {
		$current_status = isset( $_REQUEST['filter-status'] ) ? sanitize_text_field( $_REQUEST['filter-status'] ) : '';
		$statuses       = _wp_privacy_statuses();
		$views          = array();
		$counts         = $this->get_request_counts();
		$total_requests = absint( array_sum( (array) $counts ) );

		// Normalized admin URL
		$admin_url = $this->get_admin_url();

		$current_link_attributes = empty( $current_status ) ? ' class="current" aria-current="page"' : '';
		$status_label            = sprintf(
			/* translators: %s: Number of requests. */
			_nx(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				$total_requests,
				'requests'
			),
			number_format_i18n( $total_requests )
		);

		$views['all'] = sprintf(
			'<a href="%s"%s>%s</a>',
			esc_url( $admin_url ),
			$current_link_attributes,
			$status_label
		);

		foreach ( $statuses as $status => $label ) {
			$post_status = get_post_status_object( $status );
			if ( ! $post_status ) {
				continue;
			}

			$current_link_attributes = $status === $current_status ? ' class="current" aria-current="page"' : '';
			$total_status_requests   = absint( $counts->{$status} );

			$status_label = sprintf(
				translate_nooped_plural( $post_status->label_count, $total_status_requests ),
				number_format_i18n( $total_status_requests )
			);

			$status_link = add_query_arg( 'filter-status', $status, $admin_url );

			$views[ $status ] = sprintf(
				'<a href="%s"%s>%s</a>',
				esc_url( $status_link ),
				$current_link_attributes,
				$status_label
			);
		}

		return $views;
	}

	/**
	 * Get bulk actions.
	 *
	 * @since 4.9.6
	 *
	 * @return array List of bulk actions.
	 */
	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Remove' ),
			'resend' => __( 'Resend email' ),
		);
	}

	/**
	 * Process bulk actions.
	 *
	 * @since 4.9.6
	 */
	public function process_bulk_action() {
		$action      = $this->current_action();
		$request_ids = isset( $_REQUEST['request_id'] ) ? wp_parse_id_list( wp_unslash( $_REQUEST['request_id'] ) ) : array();

		$count = 0;

		if ( $request_ids ) {
			check_admin_referer( 'bulk-privacy_requests' );
		}

		switch ( $action ) {
			case 'delete':
				foreach ( $request_ids as $request_id ) {
					if ( wp_delete_post( $request_id, true ) ) {
						$count ++;
					}
				}

				add_settings_error(
					'bulk_action',
					'bulk_action',
					/* translators: %d: Number of requests. */
					sprintf( _n( 'Deleted %d request', 'Deleted %d requests', $count ), $count ),
					'success'
				);
				break;
			case 'resend':
				foreach ( $request_ids as $request_id ) {
					$resend = _wp_privacy_resend_request( $request_id );

					if ( $resend && ! is_wp_error( $resend ) ) {
						$count++;
					}
				}

				add_settings_error(
					'bulk_action',
					'bulk_action',
					/* translators: %d: Number of requests. */
					sprintf( _n( 'Re-sent %d request', 'Re-sent %d requests', $count ), $count ),
					'success'
				);
				break;
		}
	}

	/**
	 * Prepare items to output.
	 *
	 * @since 4.9.6
	 * @since 5.1.0 Added support for column sorting.
	 */
	public function prepare_items() {
		$this->items    = array();
		$posts_per_page = $this->get_items_per_page( $this->request_type . '_requests_per_page' );
		$args           = array(
			'post_type'      => $this->post_type,
			'post_name__in'  => array( $this->request_type ),
			'posts_per_page' => $posts_per_page,
			'offset'         => isset( $_REQUEST['paged'] ) ? max( 0, absint( $_REQUEST['paged'] ) - 1 ) * $posts_per_page : 0,
			'post_status'    => 'any',
			's'              => isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '',
		);

		$orderby_mapping = array(
			'requester' => 'post_title',
			'requested' => 'post_date',
		);

		if ( isset( $_REQUEST['orderby'] ) && isset( $orderby_mapping[ $_REQUEST['orderby'] ] ) ) {
			$args['orderby'] = $orderby_mapping[ $_REQUEST['orderby'] ];
		}

		if ( isset( $_REQUEST['order'] ) && in_array( strtoupper( $_REQUEST['order'] ), array( 'ASC', 'DESC' ), true ) ) {
			$args['order'] = strtoupper( $_REQUEST['order'] );
		}

		if ( ! empty( $_REQUEST['filter-status'] ) ) {
			$filter_status       = isset( $_REQUEST['filter-status'] ) ? sanitize_text_field( $_REQUEST['filter-status'] ) : '';
			$args['post_status'] = $filter_status;
		}

		$requests_query = new WP_Query( $args );
		$requests       = $requests_query->posts;

		foreach ( $requests as $request ) {
			$this->items[] = wp_get_user_request_data( $request->ID );
		}

		$this->items = array_filter( $this->items );

		$this->set_pagination_args(
			array(
				'total_items' => $requests_query->found_posts,
				'per_page'    => $posts_per_page,
			)
		);
	}

	/**
	 * Checkbox column.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item Item being shown.
	 * @return string Checkbox column markup.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="request_id[]" value="%1$s" /><span class="spinner"></span>', esc_attr( $item->ID ) );
	}

	/**
	 * Status column.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item Item being shown.
	 * @return string Status column markup.
	 */
	public function column_status( $item ) {
		$status        = get_post_status( $item->ID );
		$status_object = get_post_status_object( $status );

		if ( ! $status_object || empty( $status_object->label ) ) {
			return '-';
		}

		$timestamp = false;

		switch ( $status ) {
			case 'request-confirmed':
				$timestamp = $item->confirmed_timestamp;
				break;
			case 'request-completed':
				$timestamp = $item->completed_timestamp;
				break;
		}

		echo '<span class="status-label status-' . esc_attr( $status ) . '">';
		echo esc_html( $status_object->label );

		if ( $timestamp ) {
			echo ' (' . $this->get_timestamp_as_date( $timestamp ) . ')';
		}

		echo '</span>';
	}

	/**
	 * Convert timestamp for display.
	 *
	 * @since 4.9.6
	 *
	 * @param int $timestamp Event timestamp.
	 * @return string Human readable date.
	 */
	protected function get_timestamp_as_date( $timestamp ) {
		if ( empty( $timestamp ) ) {
			return '';
		}

		$time_diff = time() - $timestamp;

		if ( $time_diff >= 0 && $time_diff < DAY_IN_SECONDS ) {
			/* translators: %s: Human-readable time difference. */
			return sprintf( __( '%s ago' ), human_time_diff( $timestamp ) );
		}

		return date_i18n( get_option( 'date_format' ), $timestamp );
	}

	/**
	 * Default column handler.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item        Item being shown.
	 * @param string          $column_name Name of column being shown.
	 * @return string Default column output.
	 */
	public function column_default( $item, $column_name ) {
		$cell_value = $item->$column_name;

		if ( in_array( $column_name, array( 'created_timestamp' ), true ) ) {
			return $this->get_timestamp_as_date( $cell_value );
		}

		return $cell_value;
	}

	/**
	 * Actions column. Overridden by children.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item Item being shown.
	 * @return string Email column markup.
	 */
	public function column_email( $item ) {
		return sprintf( '<a href="%1$s">%2$s</a> %3$s', esc_url( 'mailto:' . $item->email ), $item->email, $this->row_actions( array() ) );
	}

	/**
	 * Next steps column. Overridden by children.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item Item being shown.
	 */
	public function column_next_steps( $item ) {}

	/**
	 * Generates content for a single row of the table,
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item The current item.
	 */
	public function single_row( $item ) {
		$status = $item->status;

		echo '<tr id="request-' . esc_attr( $item->ID ) . '" class="status-' . esc_attr( $status ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Embed scripts used to perform actions. Overridden by children.
	 *
	 * @since 4.9.6
	 */
	public function embed_scripts() {}
}
