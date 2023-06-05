<?php
/**
 * List tables: orders.
 *
 * @package WooCommerce\Admin
 * @version 3.3.0
 */

use Automattic\WooCommerce\Internal\Admin\Orders\ListTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Admin_List_Table_Orders', false ) ) {
	return;
}

if ( ! class_exists( 'WC_Admin_List_Table', false ) ) {
	include_once __DIR__ . '/abstract-class-wc-admin-list-table.php';
}

/**
 * WC_Admin_List_Table_Orders Class.
 */
class WC_Admin_List_Table_Orders extends WC_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = 'shop_order';

	/**
	 * The data store-agnostic list table implementation (introduced to support custom order tables),
	 * which we use here to render columns.
	 *
	 * @var ListTable $orders_list_table
	 */
	private $orders_list_table;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->orders_list_table = wc_get_container()->get( ListTable::class );
		add_action( 'admin_notices', array( $this, 'bulk_admin_notices' ) );
		add_action( 'admin_footer', array( $this, 'order_preview_template' ) );
		add_filter( 'get_search_query', array( $this, 'search_label' ) );
		add_filter( 'query_vars', array( $this, 'add_custom_query_var' ) );
		add_action( 'parse_query', array( $this, 'search_custom_fields' ) );
	}

	/**
	 * Render blank state.
	 */
	protected function render_blank_state() {
		$this->orders_list_table->render_blank_state();
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 */
	protected function get_primary_column() {
		return 'order_number';
	}

	/**
	 * Get row actions to show in the list table.
	 *
	 * @param array   $actions Array of actions.
	 * @param WP_Post $post Current post object.
	 * @return array
	 */
	protected function get_row_actions( $actions, $post ) {
		return array();
	}

	/**
	 * Define hidden columns.
	 *
	 * @return array
	 */
	protected function define_hidden_columns() {
		return array(
			'shipping_address',
			'billing_address',
			'wc_actions',
		);
	}

	/**
	 * Define which columns are sortable.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'order_number' => 'ID',
			'order_total'  => 'order_total',
			'order_date'   => 'date',
		);
		unset( $columns['comments'] );

		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_columns( $columns ) {
		$show_columns                     = array();
		$show_columns['cb']               = $columns['cb'];
		$show_columns['order_number']     = __( 'Order', 'woocommerce' );
		$show_columns['order_date']       = __( 'Date', 'woocommerce' );
		$show_columns['order_status']     = __( 'Status', 'woocommerce' );
		$show_columns['billing_address']  = __( 'Billing', 'woocommerce' );
		$show_columns['shipping_address'] = __( 'Ship to', 'woocommerce' );
		$show_columns['order_total']      = __( 'Total', 'woocommerce' );
		$show_columns['wc_actions']       = __( 'Actions', 'woocommerce' );

		wp_enqueue_script( 'wc-orders' );

		return $show_columns;
	}

	/**
	 * Define bulk actions.
	 *
	 * @param array $actions Existing actions.
	 * @return array
	 */
	public function define_bulk_actions( $actions ) {
		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['mark_processing'] = __( 'Change status to processing', 'woocommerce' );
		$actions['mark_on-hold']    = __( 'Change status to on-hold', 'woocommerce' );
		$actions['mark_completed']  = __( 'Change status to completed', 'woocommerce' );
		$actions['mark_cancelled']  = __( 'Change status to cancelled', 'woocommerce' );

		if ( wc_string_to_bool( get_option( 'woocommerce_allow_bulk_remove_personal_data', 'no' ) ) ) {
			$actions['remove_personal_data'] = __( 'Remove personal data', 'woocommerce' );
		}

		return $actions;
	}

	/**
	 * Pre-fetch any data for the row each column has access to it. the_order global is there for bw compat.
	 *
	 * @param int $post_id Post ID being shown.
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_order;

		if ( empty( $this->object ) || $this->object->get_id() !== $post_id ) {
			$this->object = wc_get_order( $post_id );
			$the_order    = $this->object;
		}
	}

	/**
	 * Render column: order_number.
	 */
	protected function render_order_number_column() {
		$this->orders_list_table->render_order_number_column( $this->object );
	}

	/**
	 * Render column: order_status.
	 */
	protected function render_order_status_column() {
		$this->orders_list_table->render_order_status_column( $this->object );
	}

	/**
	 * Render column: order_date.
	 */
	protected function render_order_date_column() {
		$this->orders_list_table->render_order_date_column( $this->object );
	}

	/**
	 * Render column: order_total.
	 */
	protected function render_order_total_column() {
		$this->orders_list_table->render_order_total_column( $this->object );
	}

	/**
	 * Render column: wc_actions.
	 */
	protected function render_wc_actions_column() {
		$this->orders_list_table->render_wc_actions_column( $this->object );
	}

	/**
	 * Render column: billing_address.
	 */
	protected function render_billing_address_column() {
		$this->orders_list_table->render_billing_address_column( $this->object );
	}

	/**
	 * Render column: shipping_address.
	 */
	protected function render_shipping_address_column() {
		$this->orders_list_table->render_shipping_address_column( $this->object );
	}

	/**
	 * Template for order preview.
	 *
	 * @since 3.3.0
	 */
	public function order_preview_template() {
		echo $this->orders_list_table->get_order_preview_template();
	}

	/**
	 * Get items to display in the preview as HTML.
	 *
	 * @param  WC_Order $order Order object.
	 * @return string
	 */
	public static function get_order_preview_item_html( $order ) {
		$hidden_order_itemmeta = apply_filters(
			'woocommerce_hidden_order_itemmeta',
			array(
				'_qty',
				'_tax_class',
				'_product_id',
				'_variation_id',
				'_line_subtotal',
				'_line_subtotal_tax',
				'_line_total',
				'_line_tax',
				'method_id',
				'cost',
				'_reduced_stock',
				'_restock_refunded_items',
			)
		);

		$line_items = apply_filters( 'woocommerce_admin_order_preview_line_items', $order->get_items(), $order );
		$columns    = apply_filters(
			'woocommerce_admin_order_preview_line_item_columns',
			array(
				'product'  => __( 'Product', 'woocommerce' ),
				'quantity' => __( 'Quantity', 'woocommerce' ),
				'tax'      => __( 'Tax', 'woocommerce' ),
				'total'    => __( 'Total', 'woocommerce' ),
			),
			$order
		);

		if ( ! wc_tax_enabled() ) {
			unset( $columns['tax'] );
		}

		$html = '
		<div class="wc-order-preview-table-wrapper">
			<table cellspacing="0" class="wc-order-preview-table">
				<thead>
					<tr>';

		foreach ( $columns as $column => $label ) {
			$html .= '<th class="wc-order-preview-table__column--' . esc_attr( $column ) . '">' . esc_html( $label ) . '</th>';
		}

		$html .= '
					</tr>
				</thead>
				<tbody>';

		foreach ( $line_items as $item_id => $item ) {

			$product_object = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : null;
			$row_class      = apply_filters( 'woocommerce_admin_html_order_preview_item_class', '', $item, $order );

			$html .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--' . esc_attr( $item_id ) . ( $row_class ? ' ' . esc_attr( $row_class ) : '' ) . '">';

			foreach ( $columns as $column => $label ) {
				$html .= '<td class="wc-order-preview-table__column--' . esc_attr( $column ) . '">';
				switch ( $column ) {
					case 'product':
						$html .= wp_kses_post( $item->get_name() );

						if ( $product_object ) {
							$html .= '<div class="wc-order-item-sku">' . esc_html( $product_object->get_sku() ) . '</div>';
						}

						$meta_data = $item->get_all_formatted_meta_data( '' );

						if ( $meta_data ) {
							$html .= '<table cellspacing="0" class="wc-order-item-meta">';

							foreach ( $meta_data as $meta_id => $meta ) {
								if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) {
									continue;
								}
								$html .= '<tr><th>' . wp_kses_post( $meta->display_key ) . ':</th><td>' . wp_kses_post( force_balance_tags( $meta->display_value ) ) . '</td></tr>';
							}
							$html .= '</table>';
						}
						break;
					case 'quantity':
						$html .= esc_html( $item->get_quantity() );
						break;
					case 'tax':
						$html .= wc_price( $item->get_total_tax(), array( 'currency' => $order->get_currency() ) );
						break;
					case 'total':
						$html .= wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
						break;
					default:
						$html .= apply_filters( 'woocommerce_admin_order_preview_line_item_column_' . sanitize_key( $column ), '', $item, $item_id, $order );
						break;
				}
				$html .= '</td>';
			}

			$html .= '</tr>';
		}

		$html .= '
				</tbody>
			</table>
		</div>';

		return $html;
	}

	/**
	 * Get actions to display in the preview as HTML.
	 *
	 * @param  WC_Order $order Order object.
	 * @return string
	 */
	public static function get_order_preview_actions_html( $order ) {
		$actions        = array();
		$status_actions = array();

		if ( $order->has_status( array( 'pending' ) ) ) {
			$status_actions['on-hold'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=on-hold&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'On-hold', 'woocommerce' ),
				'title'  => __( 'Change order status to on-hold', 'woocommerce' ),
				'action' => 'on-hold',
			);
		}

		if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
			$status_actions['processing'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'Processing', 'woocommerce' ),
				'title'  => __( 'Change order status to processing', 'woocommerce' ),
				'action' => 'processing',
			);
		}

		if ( $order->has_status( array( 'pending', 'on-hold', 'processing' ) ) ) {
			$status_actions['complete'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'Completed', 'woocommerce' ),
				'title'  => __( 'Change order status to completed', 'woocommerce' ),
				'action' => 'complete',
			);
		}

		if ( $status_actions ) {
			$actions['status'] = array(
				'group'   => __( 'Change status: ', 'woocommerce' ),
				'actions' => $status_actions,
			);
		}

		return wc_render_action_buttons( apply_filters( 'woocommerce_admin_order_preview_actions', $actions, $order ) );
	}

	/**
	 * Get order details to send to the ajax endpoint for previews.
	 *
	 * @param  WC_Order $order Order object.
	 * @return array
	 */
	public static function order_preview_get_order_details( $order ) {
		if ( ! $order ) {
			return array();
		}

		$payment_via      = $order->get_payment_method_title();
		$payment_method   = $order->get_payment_method();
		$payment_gateways = WC()->payment_gateways() ? WC()->payment_gateways->payment_gateways() : array();
		$transaction_id   = $order->get_transaction_id();

		if ( $transaction_id ) {

			$url = isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_transaction_url( $order ) : false;

			if ( $url ) {
				$payment_via .= ' (<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $transaction_id ) . '</a>)';
			} else {
				$payment_via .= ' (' . esc_html( $transaction_id ) . ')';
			}
		}

		$billing_address  = $order->get_formatted_billing_address();
		$shipping_address = $order->get_formatted_shipping_address();

		return apply_filters(
			'woocommerce_admin_order_preview_get_order_details',
			array(
				'data'                       => $order->get_data(),
				'order_number'               => $order->get_order_number(),
				'item_html'                  => self::get_order_preview_item_html( $order ),
				'actions_html'               => self::get_order_preview_actions_html( $order ),
				'ship_to_billing'            => wc_ship_to_billing_address_only(),
				'needs_shipping'             => $order->needs_shipping_address(),
				'formatted_billing_address'  => $billing_address ? $billing_address : __( 'N/A', 'woocommerce' ),
				'formatted_shipping_address' => $shipping_address ? $shipping_address : __( 'N/A', 'woocommerce' ),
				'shipping_address_map_url'   => $order->get_shipping_address_map_url(),
				'payment_via'                => $payment_via,
				'shipping_via'               => $order->get_shipping_method(),
				'status'                     => $order->get_status(),
				'status_name'                => wc_get_order_status_name( $order->get_status() ),
			),
			$order
		);
	}

	/**
	 * Handle bulk actions.
	 *
	 * @param  string $redirect_to URL to redirect to.
	 * @param  string $action      Action name.
	 * @param  array  $ids         List of ids.
	 * @return string
	 */
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {
		$ids     = apply_filters( 'woocommerce_bulk_action_ids', array_reverse( array_map( 'absint', $ids ) ), $action, 'order' );
		$changed = 0;

		if ( 'remove_personal_data' === $action ) {
			$report_action = 'removed_personal_data';

			foreach ( $ids as $id ) {
				$order = wc_get_order( $id );

				if ( $order ) {
					do_action( 'woocommerce_remove_order_personal_data', $order );
					$changed++;
				}
			}
		} elseif ( false !== strpos( $action, 'mark_' ) ) {
			$order_statuses = wc_get_order_statuses();
			$new_status     = substr( $action, 5 ); // Get the status name from action.
			$report_action  = 'marked_' . $new_status;

			// Sanity check: bail out if this is actually not a status, or is not a registered status.
			if ( isset( $order_statuses[ 'wc-' . $new_status ] ) ) {
				// Initialize payment gateways in case order has hooked status transition actions.
				WC()->payment_gateways();

				foreach ( $ids as $id ) {
					$order = wc_get_order( $id );
					$order->update_status( $new_status, __( 'Order status changed by bulk edit:', 'woocommerce' ), true );
					do_action( 'woocommerce_order_edit_status', $id, $new_status );
					$changed++;
				}
			}
		}

		if ( $changed ) {
			$redirect_to = add_query_arg(
				array(
					'post_type'   => $this->list_table_type,
					'bulk_action' => $report_action,
					'changed'     => $changed,
					'ids'         => join( ',', $ids ),
				),
				$redirect_to
			);
		}

		return esc_url_raw( $redirect_to );
	}

	/**
	 * Show confirmation message that order status changed for number of orders.
	 */
	public function bulk_admin_notices() {
		global $post_type, $pagenow;

		// Bail out if not on shop order list page.
		if ( 'edit.php' !== $pagenow || 'shop_order' !== $post_type || ! isset( $_REQUEST['bulk_action'] ) ) { // WPCS: input var ok, CSRF ok.
			return;
		}

		$order_statuses = wc_get_order_statuses();
		$number         = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0; // WPCS: input var ok, CSRF ok.
		$bulk_action    = wc_clean( wp_unslash( $_REQUEST['bulk_action'] ) ); // WPCS: input var ok, CSRF ok.

		// Check if any status changes happened.
		foreach ( $order_statuses as $slug => $name ) {
			if ( 'marked_' . str_replace( 'wc-', '', $slug ) === $bulk_action ) { // WPCS: input var ok, CSRF ok.
				/* translators: %d: orders count */
				$message = sprintf( _n( '%s order status changed.', '%s order statuses changed.', $number, 'woocommerce' ), number_format_i18n( $number ) );
				echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
				break;
			}
		}

		if ( 'removed_personal_data' === $bulk_action ) { // WPCS: input var ok, CSRF ok.
			/* translators: %d: orders count */
			$message = sprintf( _n( 'Removed personal data from %s order.', 'Removed personal data from %s orders.', $number, 'woocommerce' ), number_format_i18n( $number ) );
			echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		}
	}

	/**
	 * See if we should render search filters or not.
	 */
	public function restrict_manage_posts() {
		global $typenow;

		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
			$this->render_filters();
		}
	}

	/**
	 * Render any custom filters and search inputs for the list table.
	 */
	protected function render_filters() {
		$this->orders_list_table->customers_filter();
	}

	/**
	 * Handle any filters.
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function request_query( $query_vars ) {
		global $typenow;

		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
			return $this->query_filters( $query_vars );
		}

		return $query_vars;
	}

	/**
	 * Handle any custom filters.
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	protected function query_filters( $query_vars ) {
		global $wp_post_statuses;

		// Filter the orders by the posted customer.
		if ( ! empty( $_GET['_customer_user'] ) ) { // WPCS: input var ok.
			// @codingStandardsIgnoreStart.
			$query_vars['meta_query'] = array(
				array(
					'key'     => '_customer_user',
					'value'   => (int) $_GET['_customer_user'], // WPCS: input var ok, sanitization ok.
					'compare' => '=',
				),
			);
			// @codingStandardsIgnoreEnd
		}

		// Sorting.
		if ( isset( $query_vars['orderby'] ) ) {
			if ( 'order_total' === $query_vars['orderby'] ) {
				// @codingStandardsIgnoreStart
				$query_vars = array_merge( $query_vars, array(
					'meta_key'  => '_order_total',
					'orderby'   => 'meta_value_num',
				) );
				// @codingStandardsIgnoreEnd
			}
		}

		// Status.
		if ( empty( $query_vars['post_status'] ) ) {
			$post_statuses = wc_get_order_statuses();

			foreach ( $post_statuses as $status => $value ) {
				if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
					unset( $post_statuses[ $status ] );
				}
			}

			$query_vars['post_status'] = array_keys( $post_statuses );
		}
		return $query_vars;
	}

	/**
	 * Change the label when searching orders.
	 *
	 * @param mixed $query Current search query.
	 * @return string
	 */
	public function search_label( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' !== $pagenow || 'shop_order' !== $typenow || ! get_query_var( 'shop_order_search' ) || ! isset( $_GET['s'] ) ) { // phpcs:ignore  WordPress.Security.NonceVerification.Recommended
			return $query;
		}

		return wc_clean( wp_unslash( $_GET['s'] ) ); // WPCS: input var ok, sanitization ok.
	}

	/**
	 * Query vars for custom searches.
	 *
	 * @param mixed $public_query_vars Array of query vars.
	 * @return array
	 */
	public function add_custom_query_var( $public_query_vars ) {
		$public_query_vars[] = 'shop_order_search';
		return $public_query_vars;
	}

	/**
	 * Search custom fields as well as content.
	 *
	 * @param WP_Query $wp Query object.
	 */
	public function search_custom_fields( $wp ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow || empty( $wp->query_vars['s'] ) || 'shop_order' !== $wp->query_vars['post_type'] || ! isset( $_GET['s'] ) ) { // phpcs:ignore  WordPress.Security.NonceVerification.Recommended
			return;
		}

		$post_ids = wc_order_search( wc_clean( wp_unslash( $_GET['s'] ) ) ); // WPCS: input var ok, sanitization ok.

		if ( ! empty( $post_ids ) ) {
			// Remove "s" - we don't want to search order name.
			unset( $wp->query_vars['s'] );

			// so we know we're doing this.
			$wp->query_vars['shop_order_search'] = true;

			// Search by found posts.
			$wp->query_vars['post__in'] = array_merge( $post_ids, array( 0 ) );
		}
	}
}
