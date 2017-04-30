<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * WC_Report_Customer_List
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Reports
 * @version     2.1.0
 */
class WC_Report_Customer_List extends WP_List_Table {

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	function __construct(){
		global $status, $page;

		parent::__construct( array(
			'singular'  => __( 'Customer', 'woocommerce' ),
			'plural'    => __( 'Customers', 'woocommerce' ),
			'ajax'      => false
		) );
	}

	/**
	 * No items found text
	 */
	public function no_items() {
		_e( 'No customers found.', 'woocommerce' );
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		$this->prepare_items();

		echo '<div id="poststuff" class="woocommerce-reports-wide">';

		if ( ! empty( $_GET['link_orders'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'link_orders' ) ) {
			$linked = wc_update_new_customer_past_orders( absint( $_GET['link_orders'] ) );

			echo '<div class="updated"><p>' . sprintf( _n( '%s previous order linked', '%s previous orders linked', $linked, 'woocommerce' ), $linked ) . '</p></div>';
		}

		echo '<form method="post" id="woocommerce_customers">';

		$this->search_box( __( 'Search customers', 'woocommerce' ), 'customer_search' );
		$this->display();

		echo '</form>';
		echo '</div>';
	}

	/**
	 * column_default function.
	 * @access public
	 * @param mixed  $user
	 * @param string $column_name
	 * @return int|string
	 * @todo Inconsistent return types, and void return at the end. Needs a rewrite.
	 */
	function column_default( $user, $column_name ) {
		global $wpdb;

		switch( $column_name ) {
			case 'customer_name' :
				if ( $user->last_name && $user->first_name ) {
					return $user->last_name . ', ' . $user->first_name;
				} else {
					return '-';
				}
			case 'username' :
				return $user->user_login;
			break;
			case 'location' :

				$state_code   = get_user_meta( $user->ID, 'billing_state', true );
				$country_code = get_user_meta( $user->ID, 'billing_country', true );

				$state = isset( WC()->countries->states[ $country_code ][ $state_code ] ) ? WC()->countries->states[ $country_code ][ $state_code ] : $state_code;
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
			break;
			case 'email' :
				return '<a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';
			case 'spent' :
				if ( ! $spent = get_user_meta( $user->ID, '_money_spent', true ) ) {

					$spent = $wpdb->get_var( "SELECT SUM(meta2.meta_value)
						FROM $wpdb->posts as posts

						LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
						LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id
						LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
						LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
						LEFT JOIN {$wpdb->terms} AS term USING( term_id )

						WHERE 	meta.meta_key 		= '_customer_user'
						AND 	meta.meta_value 	= $user->ID
						AND 	posts.post_type 	= 'shop_order'
						AND 	posts.post_status 	= 'publish'
						AND 	tax.taxonomy		= 'shop_order_status'
						AND		term.slug			IN ( 'completed' )
						AND     meta2.meta_key 		= '_order_total'
					" );

					update_user_meta( $user->ID, '_money_spent', $spent );
				}

				return wc_price( $spent );
			break;
			case 'orders' :
				if ( ! $count = get_user_meta( $user->ID, '_order_count', true ) ) {

					$count = $wpdb->get_var( "SELECT COUNT(*)
						FROM $wpdb->posts as posts

						LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
						LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
						LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
						LEFT JOIN {$wpdb->terms} AS term USING( term_id )

						WHERE 	meta.meta_key 		= '_customer_user'
						AND 	posts.post_type 	= 'shop_order'
						AND 	posts.post_status 	= 'publish'
						AND 	tax.taxonomy		= 'shop_order_status'
						AND		term.slug			IN ( 'completed' )
						AND 	meta_value 			= $user->ID
					" );

					update_user_meta( $user->ID, '_order_count', $count );
				}

				return absint( $count );
			break;
			case 'last_order' :

				$order_ids = get_posts( array(
					'posts_per_page' => 1,
					'post_type'      => 'shop_order',
					'orderby'        => 'date',
					'order'          => 'desc',
					'meta_query' => array(
						array(
							'key'     => '_customer_user',
							'value'   => $user->ID
						)
					),
					'fields' => 'ids'
				) );

				if ( $order_ids ) {
					$order = new WC_Order( $order_ids[0] );

					echo '<a href="' . admin_url( 'post.php?post=' . $order->id . '&action=edit' ) . '">' . $order->get_order_number() . '</a> &ndash; ' . date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) );
				} else echo '-';

			break;
			case 'user_actions' :
				?><p>
					<?php
						do_action( 'woocommerce_admin_user_actions_start', $user );

						$actions = array();

						$actions['edit'] = array(
							'url' 		=> admin_url( 'user-edit.php?user_id=' . $user->ID ),
							'name' 		=> __( 'Edit', 'woocommerce' ),
							'action' 	=> "edit"
						);

						$actions['view'] = array(
							'url' 		=> admin_url( 'edit.php?post_type=shop_order&_customer_user=' . $user->ID ),
							'name' 		=> __( 'View orders', 'woocommerce' ),
							'action' 	=> "view"
						);

						$order_ids = get_posts( array(
							'posts_per_page' => 1,
							'post_type'      => 'shop_order',
							'meta_query' => array(
								array(
									'key'     => '_customer_user',
									'value'   => array( 0, '' ),
									'compare' => 'IN'
								),
								array(
									'key'     => '_billing_email',
									'value'   => $user->user_email
								)
							),
							'fields' => 'ids'
						) );

						if ( $order_ids ) {
							$actions['link'] = array(
								'url' 		=> wp_nonce_url( add_query_arg( 'link_orders', $user->ID ), 'link_orders' ),
								'name' 		=> __( 'Link previous orders', 'woocommerce' ),
								'action' 	=> "link"
							);
						}

						$actions = apply_filters( 'woocommerce_admin_user_actions', $actions, $user );

						foreach ( $actions as $action ) {
							printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
						}

						do_action( 'woocommerce_admin_user_actions_end', $user );
					?>
				</p><?php
			break;
		}
	}

	/**
	 * get_columns function.
	 *
	 * @access public
	 */
	function get_columns(){
		$columns = array(
			'customer_name'   => __( 'Name (Last, First)', 'woocommerce' ),
			'username'        => __( 'Username', 'woocommerce' ),
			'email'           => __( 'Email', 'woocommerce' ),
			'location'        => __( 'Location', 'woocommerce' ),
			'orders'          => __( 'Orders', 'woocommerce' ),
			'spent'           => __( 'Spent', 'woocommerce' ),
			'last_order'      => __( 'Last order', 'woocommerce' ),
			'user_actions'    => __( 'Actions', 'woocommerce' )
		);

		return $columns;
	}

	/**
	 * Order users by name
	 */
	public function order_by_last_name( $query ) {
		global $wpdb;

		$s = ! empty( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : '';

		$query->query_from    .= " LEFT JOIN {$wpdb->usermeta} as meta2 ON ({$wpdb->users}.ID = meta2.user_id) ";
		$query->query_where   .= " AND meta2.meta_key = 'last_name' ";
		$query->query_orderby  = " ORDER BY meta2.meta_value, user_login ASC ";

		if ( $s ) {
			$query->query_from    .= " LEFT JOIN {$wpdb->usermeta} as meta3 ON ({$wpdb->users}.ID = meta3.user_id)";
			$query->query_where   .= " AND ( user_login LIKE '%" . esc_sql( str_replace( '*', '', $s ) ) . "%' OR user_nicename LIKE '%" . esc_sql( str_replace( '*', '', $s ) ) . "%' OR meta3.meta_value LIKE '%" . esc_sql( str_replace( '*', '', $s ) ) . "%' ) ";
			$query->query_orderby  = " GROUP BY ID " . $query->query_orderby;
		}

		return $query;
	}

	/**
	 * prepare_items function.
	 *
	 * @access public
	 */
	public function prepare_items() {
		global $wpdb;

		$current_page = absint( $this->get_pagenum() );
		$per_page     = 20;

		/**
		 * Init column headers
		 */
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		add_action( 'pre_user_query', array( $this, 'order_by_last_name' ) );

		/**
		 * Get users
		 */
		$admin_users = new WP_User_Query(
			array(
				'role'   => 'administrator',
				'fields' => 'ID'
			)
		);

		$manager_users = new WP_User_Query(
			array(
				'role'   => 'shop_manager',
				'fields' => 'ID'
			)
		);

		$query = new WP_User_Query( array(
			'exclude' => array_merge( $admin_users->get_results(), $manager_users->get_results() ),
			'number'  => $per_page,
			'offset'  => ( $current_page - 1 ) * $per_page
		) );

		$this->items = $query->get_results();

		remove_action( 'pre_user_query', array( $this, 'order_by_last_name' ) );

		/**
		 * Pagination
		 */
		$this->set_pagination_args( array(
			'total_items' => $query->total_users,
			'per_page'    => $per_page,
			'total_pages' => ceil( $query->total_users / $per_page )
		) );
	}
}
