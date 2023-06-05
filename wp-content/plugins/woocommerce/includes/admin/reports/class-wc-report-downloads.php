<?php
/**
 * Download report.
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce\Admin\Reports
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WC_Report_Downloads.
 */
class WC_Report_Downloads extends WP_List_Table {

	/**
	 * Max items.
	 *
	 * @var int
	 */
	protected $max_items;

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'download',
				'plural'   => 'downloads',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Don't need this.
	 *
	 * @param string $position Top or bottom.
	 */
	public function display_tablenav( $position ) {
		if ( 'top' !== $position ) {
			parent::display_tablenav( $position );
		}
	}

	/**
	 * Output the report.
	 */
	public function output_report() {

		$this->prepare_items();

		// Subtitle for permission if set.
		if ( ! empty( $_GET['permission_id'] ) ) { // WPCS: input var ok.
			$permission_id = absint( $_GET['permission_id'] ); // WPCS: input var ok.

			// Load the permission, order, etc. so we can render more information.
			$permission = null;
			$product    = null;

			try {
				$permission = new WC_Customer_Download( $permission_id );
				$product    = wc_get_product( $permission->product_id );
			} catch ( Exception $e ) {
				wp_die( sprintf( esc_html__( 'Permission #%d not found.', 'woocommerce' ), esc_html( $permission_id ) ) );
			}
		}

		echo '<h1>' . esc_html__( 'Customer downloads', 'woocommerce' );

		$filters      = $this->get_filter_vars();
		$filter_list  = array();
		$filter_names = array(
			'product_id'      => __( 'Product', 'woocommerce' ),
			'download_id'     => __( 'File ID', 'woocommerce' ),
			'permission_id'   => __( 'Permission ID', 'woocommerce' ),
			'order_id'        => __( 'Order', 'woocommerce' ),
			'user_id'         => __( 'User', 'woocommerce' ),
			'user_ip_address' => __( 'IP address', 'woocommerce' ),
		);

		foreach ( $filters as $key => $value ) {
			if ( is_null( $value ) ) {
				continue;
			}
			switch ( $key ) {
				case 'order_id':
					$order = wc_get_order( $value );
					if ( $order ) {
						$display_value = _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number();
					} else {
						break 2;
					}
					break;
				case 'product_id':
					$product = wc_get_product( $value );
					if ( $product ) {
						$display_value = $product->get_formatted_name();
					} else {
						break 2;
					}
					break;
				default:
					$display_value = $value;
					break;
			}
			$filter_list[] = $filter_names[ $key ] . ' ' . $display_value . ' <a href="' . esc_url( remove_query_arg( $key ) ) . '" class="woocommerce-reports-remove-filter">&times;</a>';
		}

		echo '</h1>';

		echo '<div id="active-filters" class="woocommerce-reports-wide"><h2>';
		echo esc_html__( 'Active filters', 'woocommerce' ) . ': ';
		echo $filter_list ? wp_kses_post( implode( ', ', $filter_list ) ) : '';
		echo '</h2></div>';

		echo '<div id="poststuff" class="woocommerce-reports-wide">';
		$this->display();
		echo '</div>';
	}

	/**
	 * Get column value.
	 *
	 * @param mixed  $item Item being displayed.
	 * @param string $column_name Column name.
	 */
	public function column_default( $item, $column_name ) {
		$permission = null;
		$product    = null;
		try {
			$permission = new WC_Customer_Download( $item->permission_id );
			$product    = wc_get_product( $permission->product_id );
		} catch ( Exception $e ) {
			// Ok to continue rendering other information even if permission and/or product is not found.
			return;
		}

		switch ( $column_name ) {
			case 'timestamp':
				echo esc_html( $item->timestamp );
				break;
			case 'product':
				if ( ! empty( $product ) ) {
					edit_post_link( esc_html( $product->get_formatted_name() ), '', '', $product->get_id(), 'view-link' );

					echo '<div class="row-actions">';
					echo '<a href="' . esc_url( add_query_arg( 'product_id', $product->get_id() ) ) . '">' . esc_html__( 'Filter by product', 'woocommerce' ) . '</a>';
					echo '</div>';
				}
				break;
			case 'file':
				if ( ! empty( $permission ) && ! empty( $product ) ) {
					// File information.
					$file = $product->get_file( $permission->get_download_id() );

					if ( false === $file ) {
						echo esc_html__( 'File does not exist', 'woocommerce' );
					} else {
						echo esc_html( $file->get_name() . ' - ' . basename( $file->get_file() ) );

						echo '<div class="row-actions">';
						echo '<a href="' . esc_url( add_query_arg( 'download_id', $permission->get_download_id() ) ) . '">' . esc_html__( 'Filter by file', 'woocommerce' ) . '</a>';
						echo '</div>';
					}
				}
				break;
			case 'order':
				if ( ! empty( $permission ) && ( $order = wc_get_order( $permission->order_id ) ) ) {
					edit_post_link( esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ), '', '', $permission->order_id, 'view-link' );

					echo '<div class="row-actions">';
					echo '<a href="' . esc_url( add_query_arg( 'order_id', $order->get_id() ) ) . '">' . esc_html__( 'Filter by order', 'woocommerce' ) . '</a>';
					echo '</div>';
				}
				break;
			case 'user':
				if ( $item->user_id > 0 ) {
					$user = get_user_by( 'id', $item->user_id );

					if ( ! empty( $user ) ) {
						echo '<a href="' . esc_url( get_edit_user_link( $item->user_id ) ) . '">' . esc_html( $user->display_name ) . '</a>';
						echo '<div class="row-actions">';
						echo '<a href="' . esc_url( add_query_arg( 'user_id', $item->user_id ) ) . '">' . esc_html__( 'Filter by user', 'woocommerce' ) . '</a>';
						echo '</div>';
					}
				} else {
					esc_html_e( 'Guest', 'woocommerce' );
				}
				break;
			case 'user_ip_address':
				echo esc_html( $item->user_ip_address );

				echo '<div class="row-actions">';
				echo '<a href="' . esc_url( add_query_arg( 'user_ip_address', $item->user_ip_address ) ) . '">' . esc_html__( 'Filter by IP address', 'woocommerce' ) . '</a>';
				echo '</div>';
				break;
		}
	}

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'timestamp'       => __( 'Timestamp', 'woocommerce' ),
			'product'         => __( 'Product', 'woocommerce' ),
			'file'            => __( 'File', 'woocommerce' ),
			'order'           => __( 'Order', 'woocommerce' ),
			'user'            => __( 'User', 'woocommerce' ),
			'user_ip_address' => __( 'IP address', 'woocommerce' ),
		);

		return $columns;
	}

	/**
	 * Prepare download list items.
	 */
	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$current_page          = absint( $this->get_pagenum() );
		// Allow filtering per_page value, but ensure it's at least 1.
		$per_page = max( 1, apply_filters( 'woocommerce_admin_downloads_report_downloads_per_page', 20 ) );

		$this->get_items( $current_page, $per_page );

		/**
		 * Pagination.
		 */
		$this->set_pagination_args(
			array(
				'total_items' => $this->max_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $this->max_items / $per_page ),
			)
		);
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No customer downloads found.', 'woocommerce' );
	}

	/**
	 * Get filters from querystring.
	 *
	 * @return object
	 */
	protected function get_filter_vars() {
		$product_id      = ! empty( $_GET['product_id'] ) ? absint( wp_unslash( $_GET['product_id'] ) ) : null; // WPCS: input var ok.
		$download_id     = ! empty( $_GET['download_id'] ) ? wc_clean( wp_unslash( $_GET['download_id'] ) ) : null; // WPCS: input var ok.
		$permission_id   = ! empty( $_GET['permission_id'] ) ? absint( wp_unslash( $_GET['permission_id'] ) ) : null; // WPCS: input var ok.
		$order_id        = ! empty( $_GET['order_id'] ) ? absint( wp_unslash( $_GET['order_id'] ) ) : null; // WPCS: input var ok.
		$user_id         = ! empty( $_GET['user_id'] ) ? absint( wp_unslash( $_GET['user_id'] ) ) : null; // WPCS: input var ok.
		$user_ip_address = ! empty( $_GET['user_ip_address'] ) ? wc_clean( wp_unslash( $_GET['user_ip_address'] ) ) : null; // WPCS: input var ok.

		return (object) array(
			'product_id'      => $product_id,
			'download_id'     => $download_id,
			'permission_id'   => $permission_id,
			'order_id'        => $order_id,
			'user_id'         => $user_id,
			'user_ip_address' => $user_ip_address,
		);
	}

	/**
	 * Get downloads matching criteria.
	 *
	 * @param int $current_page Current viewed page.
	 * @param int $per_page How many results to show per page.
	 */
	public function get_items( $current_page, $per_page ) {
		global $wpdb;

		$this->max_items = 0;
		$this->items     = array();
		$filters         = $this->get_filter_vars();

		// Get downloads from database.
		$table      = $wpdb->prefix . WC_Customer_Download_Log_Data_Store::get_table_name();
		$query_from = " FROM {$table} as downloads ";

		if ( ! is_null( $filters->product_id ) || ! is_null( $filters->download_id ) || ! is_null( $filters->order_id ) ) {
			$query_from .= " LEFT JOIN {$wpdb->prefix}woocommerce_downloadable_product_permissions as permissions on downloads.permission_id = permissions.permission_id ";
		}

		$query_from .= ' WHERE 1=1 ';

		if ( ! is_null( $filters->product_id ) ) {
			$query_from .= $wpdb->prepare( ' AND product_id = %d ', $filters->product_id );
		}

		if ( ! is_null( $filters->download_id ) ) {
			$query_from .= $wpdb->prepare( ' AND download_id = %s ', $filters->download_id );
		}

		if ( ! is_null( $filters->order_id ) ) {
			$query_from .= $wpdb->prepare( ' AND order_id = %d ', $filters->order_id );
		}

		if ( ! is_null( $filters->permission_id ) ) {
			$query_from .= $wpdb->prepare( ' AND downloads.permission_id = %d ', $filters->permission_id );
		}

		if ( ! is_null( $filters->user_id ) ) {
			$query_from .= $wpdb->prepare( ' AND downloads.user_id = %d ', $filters->user_id );
		}

		if ( ! is_null( $filters->user_ip_address ) ) {
			$query_from .= $wpdb->prepare( ' AND user_ip_address = %s ', $filters->user_ip_address );
		}

		$query_from  = apply_filters( 'woocommerce_report_downloads_query_from', $query_from );
		$query_order = $wpdb->prepare( 'ORDER BY timestamp DESC LIMIT %d, %d;', ( $current_page - 1 ) * $per_page, $per_page );

		$this->items     = $wpdb->get_results( "SELECT * {$query_from} {$query_order}" ); // WPCS: cache ok, db call ok, unprepared SQL ok.
		$this->max_items = $wpdb->get_var( "SELECT COUNT( DISTINCT download_log_id ) {$query_from};" ); // WPCS: cache ok, db call ok, unprepared SQL ok.
	}
}
