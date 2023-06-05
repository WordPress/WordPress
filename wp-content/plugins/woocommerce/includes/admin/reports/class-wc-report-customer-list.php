<?php
/**
 * Class WC_Report_Customer_List file.
 *
 * @package WooCommerce\Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WC_Report_Customer_List.
 *
 * @package     WooCommerce\Admin\Reports
 * @version     2.1.0
 */
class WC_Report_Customer_List extends WP_List_Table {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'customer',
				'plural'   => 'customers',
				'ajax'     => false,
			)
		);
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No customers found.', 'woocommerce' );
	}

	/**
	 * Output the report.
	 */
	public function output_report() {
		$this->prepare_items();

		echo '<div id="poststuff" class="woocommerce-reports-wide">';

		if ( ! empty( $_GET['link_orders'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'link_orders' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$linked = wc_update_new_customer_past_orders( absint( $_GET['link_orders'] ) );
			/* translators: single or plural number of orders */
			echo '<div class="updated"><p>' . sprintf( esc_html( _n( '%s previous order linked', '%s previous orders linked', $linked, 'woocommerce' ), $linked ) ) . '</p></div>';
		}

		if ( ! empty( $_GET['refresh'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'refresh' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$user_id = absint( $_GET['refresh'] );
			$user    = get_user_by( 'id', $user_id );

			delete_user_meta( $user_id, '_money_spent' );
			delete_user_meta( $user_id, '_order_count' );
			delete_user_meta( $user_id, '_last_order' );
			/* translators: User display name */
			echo '<div class="updated"><p>' . sprintf( esc_html__( 'Refreshed stats for %s', 'woocommerce' ), esc_html( $user->display_name ) ) . '</p></div>';
		}

		echo '<form method="post" id="woocommerce_customers">';

		$this->search_box( __( 'Search customers', 'woocommerce' ), 'customer_search' );
		$this->display();

		echo '</form>';
		echo '</div>';
	}

	/**
	 * Get column value.
	 *
	 * @param WP_User $user WP User object.
	 * @param string  $column_name Column name.
	 * @return string
	 */
	public function column_default( $user, $column_name ) {
		switch ( $column_name ) {

			case 'customer_name':
				if ( $user->last_name && $user->first_name ) {
					return $user->last_name . ', ' . $user->first_name;
				} else {
					return '-';
				}

			case 'username':
				return $user->user_login;

			case 'location':
				$state_code   = get_user_meta( $user->ID, 'billing_state', true );
				$country_code = get_user_meta( $user->ID, 'billing_country', true );

				$state   = isset( WC()->countries->states[ $country_code ][ $state_code ] ) ? WC()->countries->states[ $country_code ][ $state_code ] : $state_code;
				$country = isset( WC()->countries->countries[ $country_code ] ) ? WC()->countries->countries[ $country_code ] : $country_code;

				$value = '';

				if ( $state ) {
					$value .= $state . ', ';
				}

				$value .= $country;

				if ( $value ) {
					return $value;
				} else {
					return '-';
				}

			case 'email':
				return '<a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';

			case 'spent':
				return wc_price( wc_get_customer_total_spent( $user->ID ) );

			case 'orders':
				return wc_get_customer_order_count( $user->ID );

			case 'last_order':
				$orders = wc_get_orders(
					array(
						'limit'    => 1,
						'status'   => array_map( 'wc_get_order_status_name', wc_get_is_paid_statuses() ),
						'customer' => $user->ID,
					)
				);

				if ( ! empty( $orders ) ) {
					$order = $orders[0];
					return '<a href="' . admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) . '">' . _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() . '</a> &ndash; ' . wc_format_datetime( $order->get_date_created() );
				} else {
					return '-';
				}

				break;

			case 'wc_actions':
				ob_start();
				?><p>
					<?php
					do_action( 'woocommerce_admin_user_actions_start', $user );

					$actions = array();

					$actions['refresh'] = array(
						'url'    => wp_nonce_url( add_query_arg( 'refresh', $user->ID ), 'refresh' ),
						'name'   => __( 'Refresh stats', 'woocommerce' ),
						'action' => 'refresh',
					);

					$actions['edit'] = array(
						'url'    => admin_url( 'user-edit.php?user_id=' . $user->ID ),
						'name'   => __( 'Edit', 'woocommerce' ),
						'action' => 'edit',
					);

					$actions['view'] = array(
						'url'    => admin_url( 'edit.php?post_type=shop_order&_customer_user=' . $user->ID ),
						'name'   => __( 'View orders', 'woocommerce' ),
						'action' => 'view',
					);

					$orders = wc_get_orders(
						array(
							'limit'    => 1,
							'status'   => array_map( 'wc_get_order_status_name', wc_get_is_paid_statuses() ),
							'customer' => array( array( 0, $user->user_email ) ),
						)
					);

					if ( $orders ) {
						$actions['link'] = array(
							'url'    => wp_nonce_url( add_query_arg( 'link_orders', $user->ID ), 'link_orders' ),
							'name'   => __( 'Link previous orders', 'woocommerce' ),
							'action' => 'link',
						);
					}

					$actions = apply_filters( 'woocommerce_admin_user_actions', $actions, $user );

					foreach ( $actions as $action ) {
						printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
					}

					do_action( 'woocommerce_admin_user_actions_end', $user );
					?>
				</p>
				<?php
				$user_actions = ob_get_contents();
				ob_end_clean();

				return $user_actions;
		}

		return '';
	}

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'customer_name' => __( 'Name (Last, First)', 'woocommerce' ),
			'username'      => __( 'Username', 'woocommerce' ),
			'email'         => __( 'Email', 'woocommerce' ),
			'location'      => __( 'Location', 'woocommerce' ),
			'orders'        => __( 'Orders', 'woocommerce' ),
			'spent'         => __( 'Money spent', 'woocommerce' ),
			'last_order'    => __( 'Last order', 'woocommerce' ),
			'wc_actions'    => __( 'Actions', 'woocommerce' ),
		);

		return $columns;
	}

	/**
	 * Order users by name.
	 *
	 * @param WP_User_Query $query Query that gets passed through.
	 * @return WP_User_Query
	 */
	public function order_by_last_name( $query ) {
		global $wpdb;

		$s = ! empty( $_REQUEST['s'] ) ? wp_unslash( $_REQUEST['s'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$query->query_from   .= " LEFT JOIN {$wpdb->usermeta} as meta2 ON ({$wpdb->users}.ID = meta2.user_id) ";
		$query->query_where  .= " AND meta2.meta_key = 'last_name' ";
		$query->query_orderby = ' ORDER BY meta2.meta_value, user_login ASC ';

		if ( $s ) {
			$query->query_from   .= " LEFT JOIN {$wpdb->usermeta} as meta3 ON ({$wpdb->users}.ID = meta3.user_id)";
			$query->query_where  .= " AND ( user_login LIKE '%" . esc_sql( str_replace( '*', '', $s ) ) . "%' OR user_nicename LIKE '%" . esc_sql( str_replace( '*', '', $s ) ) . "%' OR meta3.meta_value LIKE '%" . esc_sql( str_replace( '*', '', $s ) ) . "%' ) ";
			$query->query_orderby = ' GROUP BY ID ' . $query->query_orderby;
		}

		return $query;
	}

	/**
	 * Prepare customer list items.
	 */
	public function prepare_items() {
		$current_page = absint( $this->get_pagenum() );
		$per_page     = 20;

		/**
		 * Init column headers.
		 */
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		add_action( 'pre_user_query', array( $this, 'order_by_last_name' ) );

		/**
		 * Get users.
		 */
		$admin_users = new WP_User_Query(
			array(
				'role'   => 'administrator',
				'fields' => 'ID',
			)
		);

		$manager_users = new WP_User_Query(
			array(
				'role'   => 'shop_manager',
				'fields' => 'ID',
			)
		);

		$query = new WP_User_Query(
			apply_filters(
				'woocommerce_admin_report_customer_list_user_query_args',
				array(
					'exclude' => array_merge( $admin_users->get_results(), $manager_users->get_results() ),
					'number'  => $per_page,
					'offset'  => ( $current_page - 1 ) * $per_page,
				)
			)
		);

		$this->items = $query->get_results();

		remove_action( 'pre_user_query', array( $this, 'order_by_last_name' ) );

		/**
		 * Pagination.
		 */
		$this->set_pagination_args(
			array(
				'total_items' => $query->total_users,
				'per_page'    => $per_page,
				'total_pages' => ceil( $query->total_users / $per_page ),
			)
		);
	}
}
