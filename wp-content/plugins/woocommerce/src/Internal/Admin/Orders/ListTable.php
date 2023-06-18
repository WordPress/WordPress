<?php

namespace Automattic\WooCommerce\Internal\Admin\Orders;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Utilities\OrderUtil;
use WC_Order;
use WP_List_Table;
use WP_Screen;

/**
 * Admin list table for orders as managed by the OrdersTableDataStore.
 */
class ListTable extends WP_List_Table {

	/**
	 * Order type.
	 *
	 * @var string
	 */
	private $order_type;

	/**
	 * Request vars.
	 *
	 * @var array
	 */
	private $request = array();

	/**
	 * Contains the arguments to be used in the order query.
	 *
	 * @var array
	 */
	private $order_query_args = array();

	/**
	 * Tracks if a filter (ie, date or customer filter) has been applied.
	 *
	 * @var bool
	 */
	private $has_filter = false;

	/**
	 * Page controller instance for this request.
	 *
	 * @var PageController
	 */
	private $page_controller;

	/**
	 * Tracks whether we're currently inside the trash.
	 *
	 * @var boolean
	 */
	private $is_trash = false;

	/**
	 * Caches order counts by status.
	 *
	 * @var array
	 */
	private $status_count_cache = null;

	/**
	 * Sets up the admin list table for orders (specifically, for orders managed by the OrdersTableDataStore).
	 *
	 * @see WC_Admin_List_Table_Orders for the corresponding class used in relation to the traditional WP Post store.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'order',
				'plural'   => 'orders',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Init method, invoked by DI container.
	 *
	 * @internal This method is not intended to be used directly (except for testing).
	 * @param PageController $page_controller Page controller instance for this request.
	 */
	final public function init( PageController $page_controller ) {
		$this->page_controller = $page_controller;
	}

	/**
	 * Performs setup work required before rendering the table.
	 *
	 * @param array $args Args to initialize this list table.
	 *
	 * @return void
	 */
	public function setup( $args = array() ): void {
		$this->order_type = $args['order_type'] ?? 'shop_order';

		add_action( 'admin_notices', array( $this, 'bulk_action_notices' ) );
		add_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );
		add_filter( 'set_screen_option_edit_' . $this->order_type . '_per_page', array( $this, 'set_items_per_page' ), 10, 3 );
		add_filter( 'default_hidden_columns', array( $this, 'default_hidden_columns' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'enqueue_scripts' ) );

		$this->items_per_page();
		set_screen_options();

		add_action( 'manage_' . wc_get_page_screen_id( $this->order_type ) . '_custom_column', array( $this, 'render_column' ), 10, 2 );
	}

	/**
	 * Generates content for a single row of the table.
	 *
	 * @since 7.8.0
	 *
	 * @param \WC_Order $order The current order.
	 */
	public function single_row( $order ) {
		/**
		 * Filters the list of CSS class names for a given order row in the orders list table.
		 *
		 * @since 7.8.0
		 *
		 * @param string[]  $classes An array of CSS class names.
		 * @param \WC_Order $order   The order object.
		 */
		$css_classes = apply_filters(
			'woocommerce_' . $this->order_type . '_list_table_order_css_classes',
			array(
				'order-' . $order->get_id(),
				'type-' . $order->get_type(),
				'status-' . $order->get_status(),
			),
			$order
		);
		$css_classes = array_unique( array_map( 'trim', $css_classes ) );

		// Is locked?
		$edit_lock = wc_get_container()->get( EditLock::class );
		if ( $edit_lock->is_locked_by_another_user( $order ) ) {
			$css_classes[] = 'wp-locked';
		}

		echo '<tr id="order-' . esc_attr( $order->get_id() ) . '" class="' . esc_attr( implode( ' ', $css_classes ) ) . '">';
		$this->single_row_columns( $order );
		echo '</tr>';
	}

	/**
	 * Render individual column.
	 *
	 * @param string   $column_id Column ID to render.
	 * @param WC_Order $order Order object.
	 */
	public function render_column( $column_id, $order ) {
		if ( ! $order ) {
			return;
		}

		if ( is_callable( array( $this, 'render_' . $column_id . '_column' ) ) ) {
			call_user_func( array( $this, 'render_' . $column_id . '_column' ), $order );
		}
	}

	/**
	 * Handles output for the default column.
	 *
	 * @param \WC_Order $order       Current WooCommerce order object.
	 * @param string    $column_name Identifier for the custom column.
	 */
	public function column_default( $order, $column_name ) {
		/**
		 * Fires for each custom column for a specific order type. This hook takes precedence over the generic
		 * action `manage_{$this->screen->id}_custom_column`.
		 *
		 * @param string    $column_name Identifier for the custom column.
		 * @param \WC_Order $order       Current WooCommerce order object.
		 *
		 * @since 7.3.0
		 */
		do_action( 'woocommerce_' . $this->order_type . '_list_table_custom_column', $column_name, $order );

		/**
		 * Fires for each custom column in the Custom Order Table in the administrative screen.
		 *
		 * @param string    $column_name Identifier for the custom column.
		 * @param \WC_Order $order       Current WooCommerce order object.
		 *
		 * @since 7.0.0
		 */
		do_action( "manage_{$this->screen->id}_custom_column", $column_name, $order );
	}

	/**
	 * Sets up an items-per-page control.
	 */
	private function items_per_page(): void {
		add_screen_option(
			'per_page',
			array(
				'default' => 20,
				'option'  => 'edit_' . $this->order_type . '_per_page',
			)
		);
	}

	/**
	 * Saves the items-per-page setting.
	 *
	 * @param mixed  $default The default value.
	 * @param string $option  The option being configured.
	 * @param int    $value   The submitted option value.
	 *
	 * @return mixed
	 */
	public function set_items_per_page( $default, string $option, int $value ) {
		return 'edit_' . $this->order_type . '_per_page' === $option ? absint( $value ) : $default;
	}

	/**
	 * Render the table.
	 *
	 * @return void
	 */
	public function display() {
		$post_type = get_post_type_object( $this->order_type );

		$title         = esc_html( $post_type->labels->name );
		$add_new       = esc_html( $post_type->labels->add_new );
		$new_page_link = $this->page_controller->get_new_page_url( $this->order_type );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post(
			"
			<div class='wrap'>
				<h1 class='wp-heading-inline'>{$title}</h1>
				<a href='" . esc_url( $new_page_link ) . "' class='page-title-action'>{$add_new}</a>
				<hr class='wp-header-end'>"
		);

		if ( $this->should_render_blank_state() ) {
			$this->render_blank_state();
			return;
		}

		$this->views();

		echo '<form id="wc-orders-filter" method="get" action="' . esc_url( get_admin_url( null, 'admin.php' ) ) . '">';
		$this->print_hidden_form_fields();
		$this->search_box( esc_html__( 'Search orders', 'woocommerce' ), 'orders-search-input' );

		parent::display();
		echo '</form> </div>';
	}

	/**
	 * Renders advice in the event that no orders exist yet.
	 *
	 * @return void
	 */
	public function render_blank_state(): void {
		?>
			<div class="woocommerce-BlankState">

				<h2 class="woocommerce-BlankState-message">
					<?php esc_html_e( 'When you receive a new order, it will appear here.', 'woocommerce' ); ?>
				</h2>

				<div class="woocommerce-BlankState-buttons">
					<a class="woocommerce-BlankState-cta button-primary button" target="_blank" href="https://docs.woocommerce.com/document/managing-orders/?utm_source=blankslate&utm_medium=product&utm_content=ordersdoc&utm_campaign=woocommerceplugin"><?php esc_html_e( 'Learn more about orders', 'woocommerce' ); ?></a>
				</div>

			<?php
			/**
			 * Renders after the 'blank state' message for the order list table has rendered.
			 *
			 * @since 6.6.1
			 */
			do_action( 'wc_marketplace_suggestions_orders_empty_state' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
			?>

			</div>
		<?php
	}

	/**
	 * Retrieves the list of bulk actions available for this table.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		$selected_status = $this->order_query_args['status'] ?? false;

		if ( array( 'trash' ) === $selected_status ) {
			$actions = array(
				'untrash' => __( 'Restore', 'woocommerce' ),
				'delete'  => __( 'Delete permanently', 'woocommerce' ),
			);
		} else {
			$actions = array(
				'mark_processing' => __( 'Change status to processing', 'woocommerce' ),
				'mark_on-hold'    => __( 'Change status to on-hold', 'woocommerce' ),
				'mark_completed'  => __( 'Change status to completed', 'woocommerce' ),
				'mark_cancelled'  => __( 'Change status to cancelled', 'woocommerce' ),
				'trash'           => __( 'Move to Trash', 'woocommerce' ),
			);
		}

		if ( wc_string_to_bool( get_option( 'woocommerce_allow_bulk_remove_personal_data', 'no' ) ) ) {
			$actions['remove_personal_data'] = __( 'Remove personal data', 'woocommerce' );
		}

		return $actions;
	}

	/**
	 * Gets a list of CSS classes for the WP_List_Table table tag.
	 *
	 * @since 7.8.0
	 *
	 * @return string[] Array of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		/**
		 * Filters the list of CSS class names for the orders list table.
		 *
		 * @since 7.8.0
		 *
		 * @param string[] $classes    An array of CSS class names.
		 * @param string   $order_type The order type.
		 */
		$css_classes = apply_filters(
			'woocommerce_' . $this->order_type . '_list_table_css_classes',
			array_merge(
				parent::get_table_classes(),
				array(
					'wc-orders-list-table',
					'wc-orders-list-table-' . $this->order_type,
				)
			),
			$this->order_type
		);

		return array_unique( array_map( 'trim', $css_classes ) );
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {
		$limit = $this->get_items_per_page( 'edit_' . $this->order_type . '_per_page' );

		$this->order_query_args = array(
			'limit'    => $limit,
			'page'     => $this->get_pagenum(),
			'paginate' => true,
			'type'     => $this->order_type,
		);

		foreach ( array( 'status', 's', 'm', '_customer_user' ) as $query_var ) {
			$this->request[ $query_var ] = sanitize_text_field( wp_unslash( $_REQUEST[ $query_var ] ?? '' ) );
		}

		/**
		 * Allows 3rd parties to filter the initial request vars before defaults and other logic is applied.
		 *
		 * @param array $request Request to be passed to `wc_get_orders()`.
		 *
		 * @since 7.3.0
		 */
		$this->request = apply_filters( 'woocommerce_' . $this->order_type . '_list_table_request', $this->request );

		$this->set_status_args();
		$this->set_order_args();
		$this->set_date_args();
		$this->set_customer_args();
		$this->set_search_args();

		/**
		 * Provides an opportunity to modify the query arguments used in the (Custom Order Table-powered) order list
		 * table.
		 *
		 * @since 6.9.0
		 *
		 * @param array $query_args Arguments to be passed to `wc_get_orders()`.
		 */
		$order_query_args = (array) apply_filters( 'woocommerce_order_list_table_prepare_items_query_args', $this->order_query_args );

		/**
		 * Same as `woocommerce_order_list_table_prepare_items_query_args` but for a specific order type.
		 *
		 * @param array $query_args Arguments to be passed to `wc_get_orders()`.
		 *
		 * @since 7.3.0
		 */
		$order_query_args = apply_filters( 'woocommerce_' . $this->order_type . '_list_table_prepare_items_query_args', $order_query_args );

		// We must ensure the 'paginate' argument is set.
		$order_query_args['paginate'] = true;

		$orders      = wc_get_orders( $order_query_args );
		$this->items = $orders->orders;

		$max_num_pages = $orders->max_num_pages;

		// Check in case the user has attempted to page beyond the available range of orders.
		if ( 0 === $max_num_pages && $this->order_query_args['page'] > 1 ) {
			$count_query_args          = $order_query_args;
			$count_query_args['page']  = 1;
			$count_query_args['limit'] = 1;
			$order_count               = wc_get_orders( $count_query_args );
			$max_num_pages             = (int) ceil( $order_count->total / $order_query_args['limit'] );
		}

		$this->set_pagination_args(
			array(
				'total_items' => $orders->total ?? 0,
				'per_page'    => $limit,
				'total_pages' => $max_num_pages,
			)
		);

		// Are we inside the trash?
		$this->is_trash = 'trash' === $this->request['status'];
	}

	/**
	 * Updates the WC Order Query arguments as needed to support orderable columns.
	 */
	private function set_order_args() {
		$sortable  = $this->get_sortable_columns();
		$field     = sanitize_text_field( wp_unslash( $_GET['orderby'] ?? '' ) );
		$direction = strtoupper( sanitize_text_field( wp_unslash( $_GET['order'] ?? '' ) ) );

		if ( ! in_array( $field, $sortable, true ) ) {
			$this->order_query_args['orderby'] = 'date';
			$this->order_query_args['order']   = 'DESC';
			return;
		}

		$this->order_query_args['orderby'] = $field;
		$this->order_query_args['order']   = in_array( $direction, array( 'ASC', 'DESC' ), true ) ? $direction : 'ASC';
	}

	/**
	 * Implements date (month-based) filtering.
	 */
	private function set_date_args() {
		$year_month = sanitize_text_field( wp_unslash( $_GET['m'] ?? '' ) );

		if ( empty( $year_month ) || ! preg_match( '/^[0-9]{6}$/', $year_month ) ) {
			return;
		}

		$year  = (int) substr( $year_month, 0, 4 );
		$month = (int) substr( $year_month, 4, 2 );

		if ( $month < 0 || $month > 12 ) {
			return;
		}

		$last_day_of_month                      = date_create( "$year-$month" )->format( 'Y-m-t' );
		$this->order_query_args['date_created'] = "$year-$month-01..." . $last_day_of_month;
		$this->has_filter                       = true;
	}

	/**
	 * Implements filtering of orders by customer.
	 */
	private function set_customer_args() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$customer = (int) wp_unslash( $_GET['_customer_user'] ?? '' );

		if ( $customer < 1 ) {
			return;
		}

		$this->order_query_args['customer'] = $customer;
		$this->has_filter                   = true;
	}

	/**
	 * Implements filtering of orders by status.
	 */
	private function set_status_args() {
		$status = array_filter( array_map( 'trim', (array) $this->request['status'] ) );

		if ( empty( $status ) || in_array( 'all', $status, true ) ) {
			/**
			 * Allows 3rd parties to set the default list of statuses for a given order type.
			 *
			 * @param string[] $statuses Statuses.
			 *
			 * @since 7.3.0
			 */
			$status = apply_filters(
				'woocommerce_' . $this->order_type . '_list_table_default_statuses',
				array_intersect(
					array_keys( wc_get_order_statuses() ),
					get_post_stati( array( 'show_in_admin_all_list' => true ), 'names' )
				)
			);
		} else {
			$this->has_filter = true;
		}

		$this->order_query_args['status'] = $status;
	}

	/**
	 * Implements order search.
	 */
	private function set_search_args(): void {
		$search_term = trim( sanitize_text_field( $this->request['s'] ) );

		if ( ! empty( $search_term ) ) {
			$this->order_query_args['s'] = $search_term;
			$this->has_filter            = true;
		}
	}

	/**
	 * Get the list of views for this table (all orders, completed orders, etc, each with a count of the number of
	 * corresponding orders).
	 *
	 * @return array
	 */
	public function get_views() {
		$view_counts = array();
		$view_links  = array();
		$statuses    = $this->get_visible_statuses();
		$current     = ! empty( $this->request['status'] ) ? sanitize_text_field( $this->request['status'] ) : 'all';
		$all_count   = 0;

		foreach ( array_keys( $statuses ) as $slug ) {
			$total_in_status = $this->count_orders_by_status( $slug );

			if ( $total_in_status > 0 ) {
				$view_counts[ $slug ] = $total_in_status;
			}

			if ( ( get_post_status_object( $slug ) )->show_in_admin_all_list && 'auto-draft' !== $slug ) {
				$all_count += $total_in_status;
			}
		}

		$view_links['all'] = $this->get_view_link( 'all', __( 'All', 'woocommerce' ), $all_count, '' === $current || 'all' === $current );

		foreach ( $view_counts as $slug => $count ) {
			$view_links[ $slug ] = $this->get_view_link( $slug, $statuses[ $slug ], $count, $slug === $current );
		}

		return $view_links;
	}

	/**
	 * Count orders by status.
	 *
	 * @param string|string[] $status The order status we are interested in.
	 *
	 * @return int
	 */
	private function count_orders_by_status( $status ): int {
		global $wpdb;

		// Compute all counts and cache if necessary.
		if ( is_null( $this->status_count_cache ) ) {
			$orders_table = OrdersTableDataStore::get_orders_table_name();

			$res = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT status, COUNT(*) AS cnt FROM {$orders_table} WHERE type = %s GROUP BY status", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$this->order_type
				),
				ARRAY_A
			);

			$this->status_count_cache =
				$res
				? array_combine( array_column( $res, 'status' ), array_map( 'absint', array_column( $res, 'cnt' ) ) )
				: array();
		}

		$status = (array) $status;
		$count  = array_sum( array_intersect_key( $this->status_count_cache, array_flip( $status ) ) );

		/**
		 * Allows 3rd parties to modify the count of orders by status.
		 *
		 * @param int      $count  Number of orders for the given status.
		 * @param string[] $status List of order statuses in the count.
		 * @since 7.3.0
		 */
		return apply_filters(
			'woocommerce_' . $this->order_type . '_list_table_order_count',
			$count,
			$status
		);
	}

	/**
	 * Checks whether the blank state should be rendered or not. This depends on whether there are others with a visible
	 * status.
	 *
	 * @return boolean TRUE when the blank state should be rendered, FALSE otherwise.
	 */
	private function should_render_blank_state(): bool {
		return ( ! $this->has_filter ) && 0 === $this->count_orders_by_status( array_keys( $this->get_visible_statuses() ) );
	}

	/**
	 * Returns a list of slug and labels for order statuses that should be visible in the status list.
	 *
	 * @return array slug => label array of order statuses.
	 */
	private function get_visible_statuses(): array {
		return array_intersect_key(
			array_merge(
				wc_get_order_statuses(),
				array(
					'trash'      => ( get_post_status_object( 'trash' ) )->label,
					'draft'      => ( get_post_status_object( 'draft' ) )->label,
					'auto-draft' => ( get_post_status_object( 'auto-draft' ) )->label,
				)
			),
			array_flip( get_post_stati( array( 'show_in_admin_status_list' => true ) ) )
		);
	}

	/**
	 * Form a link to use in the list of table views.
	 *
	 * @param string $slug    Slug used to identify the view (usually the order status slug).
	 * @param string $name    Human-readable name of the view (usually the order status label).
	 * @param int    $count   Number of items in this view.
	 * @param bool   $current If this is the current view.
	 *
	 * @return string
	 */
	private function get_view_link( string $slug, string $name, int $count, bool $current ): string {
		$base_url = get_admin_url( null, 'admin.php?page=wc-orders' . ( 'shop_order' === $this->order_type ? '' : '--' . $this->order_type ) );
		$url      = esc_url( add_query_arg( 'status', $slug, $base_url ) );
		$name     = esc_html( $name );
		$count    = absint( $count );
		$class    = $current ? 'class="current"' : '';

		return "<a href='$url' $class>$name <span class='count'>($count)</span></a>";
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @param string $which Either 'top' or 'bottom'.
	 */
	protected function extra_tablenav( $which ) {
		echo '<div class="alignleft actions">';

		if ( 'top' === $which ) {
			ob_start();

			$this->months_filter();
			$this->customers_filter();

			/**
			 * Fires before the "Filter" button on the list table for orders and other order types.
			 *
			 * @since 7.3.0
			 *
			 * @param string $order_type  The order type.
			 * @param string $which       The location of the extra table nav: 'top' or 'bottom'.
			 */
			do_action( 'woocommerce_order_list_table_restrict_manage_orders', $this->order_type, $which );

			$output = ob_get_clean();

			if ( ! empty( $output ) ) {
				echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				submit_button( __( 'Filter', 'woocommerce' ), '', 'filter_action', false, array( 'id' => 'order-query-submit' ) );
			}
		}

		if ( $this->is_trash && $this->has_items() && current_user_can( 'edit_others_shop_orders' ) ) {
			submit_button( __( 'Empty Trash', 'woocommerce' ), 'apply', 'delete_all', false );
		}

		/**
		 * Fires immediately following the closing "actions" div in the tablenav for the order
		 * list table.
		 *
		 * @since 7.3.0
		 *
		 * @param string $order_type  The order type.
		 * @param string $which       The location of the extra table nav: 'top' or 'bottom'.
		 */
		do_action( 'woocommerce_order_list_table_extra_tablenav', $this->order_type, $which );

		echo '</div>';
	}

	/**
	 * Render the months filter dropdown.
	 *
	 * @return void
	 */
	private function months_filter() {
		// XXX: [review] we may prefer to move this logic outside of the ListTable class.

		global $wp_locale;
		global $wpdb;

		$orders_table = esc_sql( OrdersTableDataStore::get_orders_table_name() );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$order_dates = $wpdb->get_results(
			"
				SELECT DISTINCT YEAR( date_created_gmt ) AS year,
								MONTH( date_created_gmt ) AS month

				FROM $orders_table

				WHERE status NOT IN (
					'trash'
				)

				ORDER BY year DESC, month DESC;
			"
		);

		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
		echo '<select name="m" id="filter-by-date">';
		echo '<option ' . selected( $m, 0, false ) . ' value="0">' . esc_html__( 'All dates', 'woocommerce' ) . '</option>';

		foreach ( $order_dates as $date ) {
			$month           = zeroise( $date->month, 2 );
			$month_year_text = sprintf(
				/* translators: 1: Month name, 2: 4-digit year. */
				esc_html_x( '%1$s %2$d', 'order dates dropdown', 'woocommerce' ),
				$wp_locale->get_month( $month ),
				$date->year
			);

			printf(
				'<option %1$s value="%2$s">%3$s</option>\n',
				selected( $m, $date->year . $month, false ),
				esc_attr( $date->year . $month ),
				esc_html( $month_year_text )
			);
		}

		echo '</select>';
	}

	/**
	 * Render the customer filter dropdown.
	 *
	 * @return void
	 */
	public function customers_filter() {
		$user_string = '';
		$user_id     = '';

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['_customer_user'] ) ) {
			$user_id = absint( $_GET['_customer_user'] );
			$user    = get_user_by( 'id', $user_id );

			$user_string = sprintf(
				/* translators: 1: user display name 2: user ID 3: user email */
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
				$user->display_name,
				absint( $user->ID ),
				$user->user_email
			);
		}

		// Note: use of htmlspecialchars (below) is to prevent XSS when rendered by selectWoo.
		?>
		<select class="wc-customer-search" name="_customer_user" data-placeholder="<?php esc_attr_e( 'Filter by registered customer', 'woocommerce' ); ?>" data-allow_clear="true">
			<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo htmlspecialchars( wp_kses_post( $user_string ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
		</select>
		<?php
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		/**
		 * Filters the list of columns.
		 *
		 * @param array $columns List of sortable columns.
		 *
		 * @since 7.3.0
		 */
		return apply_filters(
			'woocommerce_' . $this->order_type . '_list_table_columns',
			array(
				'cb'               => '<input type="checkbox" />',
				'order_number'     => esc_html__( 'Order', 'woocommerce' ),
				'order_date'       => esc_html__( 'Date', 'woocommerce' ),
				'order_status'     => esc_html__( 'Status', 'woocommerce' ),
				'billing_address'  => esc_html__( 'Billing', 'woocommerce' ),
				'shipping_address' => esc_html__( 'Ship to', 'woocommerce' ),
				'order_total'      => esc_html__( 'Total', 'woocommerce' ),
				'wc_actions'       => esc_html__( 'Actions', 'woocommerce' ),
			)
		);
	}

	/**
	 * Defines the default sortable columns.
	 *
	 * @return string[]
	 */
	public function get_sortable_columns() {
		/**
		 * Filters the list of sortable columns.
		 *
		 * @param array $sortable_columns List of sortable columns.
		 *
		 * @since 7.3.0
		 */
		return apply_filters(
			'woocommerce_' . $this->order_type . '_list_table_sortable_columns',
			array(
				'order_number' => 'ID',
				'order_date'   => 'date',
				'order_total'  => 'order_total',
			)
		);
	}

	/**
	 * Specify the columns we wish to hide by default.
	 *
	 * @param array     $hidden Columns set to be hidden.
	 * @param WP_Screen $screen Screen object.
	 *
	 * @return array
	 */
	public function default_hidden_columns( array $hidden, WP_Screen $screen ) {
		if ( isset( $screen->id ) && wc_get_page_screen_id( 'shop-order' ) === $screen->id ) {
			$hidden = array_merge(
				$hidden,
				array(
					'billing_address',
					'shipping_address',
					'wc_actions',
				)
			);
		}

		return $hidden;
	}

	/**
	 * Checklist column, used for selecting items for processing by a bulk action.
	 *
	 * @param WC_Order $item The order object for the current row.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		ob_start();
		?>
		<input id="cb-select-<?php echo esc_attr( $item->get_id() ); ?>" type="checkbox" name="<?php echo esc_attr( $this->_args['singular'] ); ?>" value="<?php echo esc_attr( $item->get_id() ); ?>" />

		<div class="locked-indicator">
			<span class="locked-indicator-icon" aria-hidden="true"></span>
			<span class="screen-reader-text">
				<?php
				// translators: %s is an order ID.
				echo esc_html( sprintf( __( 'Order %s is locked.', 'woocommerce' ), $item->get_id() ) );
				?>
			</span>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders the order number, customer name and provides a preview link.
	 *
	 * @param WC_Order $order The order object for the current row.
	 *
	 * @return void
	 */
	public function render_order_number_column( WC_Order $order ): void {
		$buyer = '';

		if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
			/* translators: 1: first name 2: last name */
			$buyer = trim( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
		} elseif ( $order->get_billing_company() ) {
			$buyer = trim( $order->get_billing_company() );
		} elseif ( $order->get_customer_id() ) {
			$user  = get_user_by( 'id', $order->get_customer_id() );
			$buyer = ucwords( $user->display_name );
		}

		/**
		 * Filter buyer name in list table orders.
		 *
		 * @since 3.7.0
		 *
		 * @param string   $buyer Buyer name.
		 * @param WC_Order $order Order data.
		 */
		$buyer = apply_filters( 'woocommerce_admin_order_buyer_name', $buyer, $order );

		if ( $order->get_status() === 'trash' ) {
			echo '<strong>#' . esc_attr( $order->get_order_number() ) . ' ' . esc_html( $buyer ) . '</strong>';
		} else {
			echo '<a href="#" class="order-preview" data-order-id="' . absint( $order->get_id() ) . '" title="' . esc_attr( __( 'Preview', 'woocommerce' ) ) . '">' . esc_html( __( 'Preview', 'woocommerce' ) ) . '</a>';
			echo '<a href="' . esc_url( $this->get_order_edit_link( $order ) ) . '" class="order-view"><strong>#' . esc_attr( $order->get_order_number() ) . ' ' . esc_html( $buyer ) . '</strong></a>';
		}
	}

	/**
	 * Get the edit link for an order.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return string Edit link for the order.
	 */
	private function get_order_edit_link( WC_Order $order ) : string {
		return $this->page_controller->get_edit_url( $order->get_id() );
	}

	/**
	 * Renders the order date.
	 *
	 * @param WC_Order $order The order object for the current row.
	 *
	 * @return void
	 */
	public function render_order_date_column( WC_Order $order ): void {
		$order_timestamp = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : '';

		if ( ! $order_timestamp ) {
			echo '&ndash;';
			return;
		}

		// Check if the order was created within the last 24 hours, and not in the future.
		if ( $order_timestamp > strtotime( '-1 day', time() ) && $order_timestamp <= time() ) {
			$show_date = sprintf(
			/* translators: %s: human-readable time difference */
				_x( '%s ago', '%s = human-readable time difference', 'woocommerce' ),
				human_time_diff( $order->get_date_created()->getTimestamp(), time() )
			);
		} else {
			$show_date = $order->get_date_created()->date_i18n( apply_filters( 'woocommerce_admin_order_date_format', __( 'M j, Y', 'woocommerce' ) ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		}
		printf(
			'<time datetime="%1$s" title="%2$s">%3$s</time>',
			esc_attr( $order->get_date_created()->date( 'c' ) ),
			esc_html( $order->get_date_created()->date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ),
			esc_html( $show_date )
		);
	}

	/**
	 * Renders the order status.
	 *
	 * @param WC_Order $order The order object for the current row.
	 *
	 * @return void
	 */
	public function render_order_status_column( WC_Order $order ): void {
		$tooltip = '';
		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
		$comment_count = get_comment_count( $order->get_id() );
		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
		$approved_comments_count = absint( $comment_count['approved'] );

		if ( $approved_comments_count ) {
			$latest_notes = wc_get_order_notes(
				array(
					'order_id' => $order->get_id(),
					'limit'    => 1,
					'orderby'  => 'date_created_gmt',
				)
			);

			$latest_note = current( $latest_notes );

			if ( isset( $latest_note->content ) && 1 === $approved_comments_count ) {
				$tooltip = wc_sanitize_tooltip( $latest_note->content );
			} elseif ( isset( $latest_note->content ) ) {
				/* translators: %d: notes count */
				$tooltip = wc_sanitize_tooltip( $latest_note->content . '<br/><small style="display:block">' . sprintf( _n( 'Plus %d other note', 'Plus %d other notes', ( $approved_comments_count - 1 ), 'woocommerce' ), $approved_comments_count - 1 ) . '</small>' );
			} else {
				/* translators: %d: notes count */
				$tooltip = wc_sanitize_tooltip( sprintf( _n( '%d note', '%d notes', $approved_comments_count, 'woocommerce' ), $approved_comments_count ) );
			}
		}

		// Gracefully handle legacy statuses.
		if ( in_array( $order->get_status(), array( 'trash', 'draft', 'auto-draft' ), true ) ) {
			$status_name = ( get_post_status_object( $order->get_status() ) )->label;
		} else {
			$status_name = wc_get_order_status_name( $order->get_status() );
		}

		if ( $tooltip ) {
			printf( '<mark class="order-status %s tips" data-tip="%s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ), wp_kses_post( $tooltip ), esc_html( $status_name ) );
		} else {
			printf( '<mark class="order-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ), esc_html( $status_name ) );
		}
	}

	/**
	 * Renders order billing information.
	 *
	 * @param WC_Order $order The order object for the current row.
	 *
	 * @return void
	 */
	public function render_billing_address_column( WC_Order $order ): void {
		$address = $order->get_formatted_billing_address();

		if ( $address ) {
			echo esc_html( preg_replace( '#<br\s*/?>#i', ', ', $address ) );

			if ( $order->get_payment_method() ) {
				/* translators: %s: payment method */
				echo '<span class="description">' . sprintf( esc_html__( 'via %s', 'woocommerce' ), esc_html( $order->get_payment_method_title() ) ) . '</span>';
			}
		} else {
			echo '&ndash;';
		}
	}

	/**
	 * Renders order shipping information.
	 *
	 * @param WC_Order $order The order object for the current row.
	 *
	 * @return void
	 */
	public function render_shipping_address_column( WC_Order $order ): void {
		$address = $order->get_formatted_shipping_address();

		if ( $address ) {
			echo '<a target="_blank" href="' . esc_url( $order->get_shipping_address_map_url() ) . '">' . esc_html( preg_replace( '#<br\s*/?>#i', ', ', $address ) ) . '</a>';
			if ( $order->get_shipping_method() ) {
				/* translators: %s: shipping method */
				echo '<span class="description">' . sprintf( esc_html__( 'via %s', 'woocommerce' ), esc_html( $order->get_shipping_method() ) ) . '</span>';
			}
		} else {
			echo '&ndash;';
		}
	}

	/**
	 * Renders the order total.
	 *
	 * @param WC_Order $order The order object for the current row.
	 *
	 * @return void
	 */
	public function render_order_total_column( WC_Order $order ): void {
		if ( $order->get_payment_method_title() ) {
			/* translators: %s: method */
			echo '<span class="tips" data-tip="' . esc_attr( sprintf( __( 'via %s', 'woocommerce' ), $order->get_payment_method_title() ) ) . '">' . wp_kses_post( $order->get_formatted_order_total() ) . '</span>';
		} else {
			echo wp_kses_post( $order->get_formatted_order_total() );
		}
	}

	/**
	 * Renders order actions.
	 *
	 * @param WC_Order $order The order object for the current row.
	 *
	 * @return void
	 */
	public function render_wc_actions_column( WC_Order $order ): void {
		echo '<p>';

		/**
		 * Fires before the order action buttons (within the actions column for the order list table)
		 * are registered.
		 *
		 * @param WC_Order $order Current order object.
		 * @since 6.7.0
		 */
		do_action( 'woocommerce_admin_order_actions_start', $order );

		$actions = array();

		if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
			$actions['processing'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'Processing', 'woocommerce' ),
				'action' => 'processing',
			);
		}

		if ( $order->has_status( array( 'pending', 'on-hold', 'processing' ) ) ) {
			$actions['complete'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'Complete', 'woocommerce' ),
				'action' => 'complete',
			);
		}

		/**
		 * Provides an opportunity to modify the action buttons within the order list table.
		 *
		 * @param array    $action Order actions.
		 * @param WC_Order $order  Current order object.
		 * @since 6.7.0
		 */
		$actions = apply_filters( 'woocommerce_admin_order_actions', $actions, $order );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wc_render_action_buttons( $actions );

		/**
		 * Fires after the order action buttons (within the actions column for the order list table)
		 * are rendered.
		 *
		 * @param WC_Order $order Current order object.
		 * @since 6.7.0
		 */
		do_action( 'woocommerce_admin_order_actions_end', $order );

		echo '</p>';
	}

	/**
	 * Outputs hidden fields used to retain state when filtering.
	 *
	 * @return void
	 */
	private function print_hidden_form_fields(): void {
		echo '<input type="hidden" name="page" value="wc-orders' . ( 'shop_order' === $this->order_type ? '' : '--' . $this->order_type ) . '" >'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$state_params = array(
			'paged',
			'status',
		);

		foreach ( $state_params as $param ) {
			if ( ! isset( $_GET[ $param ] ) ) {
				continue;
			}

			echo '<input type="hidden" name="' . esc_attr( $param ) . '" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) ) . '" >';
		}
	}

	/**
	 * Gets the current action selected from the bulk actions dropdown.
	 *
	 * @return string|false The action name. False if no action was selected.
	 */
	public function current_action() {
		if ( ! empty( $_REQUEST['delete_all'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * Handle bulk actions.
	 */
	public function handle_bulk_actions() {
		$action = $this->current_action();

		if ( ! $action ) {
			return;
		}

		check_admin_referer( 'bulk-orders' );

		$redirect_to = remove_query_arg( array( 'deleted', 'ids' ), wp_get_referer() );
		$redirect_to = add_query_arg( 'paged', $this->get_pagenum(), $redirect_to );

		if ( 'delete_all' === $action ) {
			// Get all trashed orders.
			$ids = wc_get_orders(
				array(
					'type'   => $this->order_type,
					'status' => 'trash',
					'limit'  => -1,
					'return' => 'ids',
				)
			);

			$action = 'delete';
		} else {
			$ids = isset( $_REQUEST['order'] ) ? array_reverse( array_map( 'absint', (array) $_REQUEST['order'] ) ) : array();
		}

		/**
		 * Allows 3rd parties to modify order IDs about to be affected by a bulk action.
		 *
		 * @param array Array of order IDs.
		 */
		$ids = apply_filters( // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
			'woocommerce_bulk_action_ids',
			$ids,
			$action,
			'order'
		);

		if ( ! $ids ) {
			wp_safe_redirect( $redirect_to );
			exit;
		}

		$report_action = '';
		$changed       = 0;

		if ( 'remove_personal_data' === $action ) {
			$report_action = 'removed_personal_data';
			$changed       = $this->do_bulk_action_remove_personal_data( $ids );
		} elseif ( 'trash' === $action ) {
			$changed       = $this->do_delete( $ids );
			$report_action = 'trashed';
		} elseif ( 'delete' === $action ) {
			$changed       = $this->do_delete( $ids, true );
			$report_action = 'deleted';
		} elseif ( 'untrash' === $action ) {
			$changed       = $this->do_untrash( $ids );
			$report_action = 'untrashed';
		} elseif ( false !== strpos( $action, 'mark_' ) ) {
			$order_statuses = wc_get_order_statuses();
			$new_status     = substr( $action, 5 );
			$report_action  = 'marked_' . $new_status;

			if ( isset( $order_statuses[ 'wc-' . $new_status ] ) ) {
				$changed = $this->do_bulk_action_mark_orders( $ids, $new_status );
			}
		} else {
			$screen = get_current_screen()->id;

			/**
			 * This action is documented in /wp-admin/edit.php (it is a core WordPress hook).
			 *
			 * @since 7.2.0
			 *
			 * @param string $redirect_to The URL to redirect to after processing the bulk actions.
			 * @param string $action      The current bulk action.
			 * @param int[]  $ids         IDs for the orders to be processed.
			 */
			$custom_sendback = apply_filters( "handle_bulk_actions-{$screen}", $redirect_to, $action, $ids ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		}

		if ( ! empty( $custom_sendback ) ) {
			$redirect_to = $custom_sendback;
		} elseif ( $changed ) {
			$redirect_to = add_query_arg(
				array(
					'bulk_action' => $report_action,
					'changed'     => $changed,
					'ids'         => implode( ',', $ids ),
				),
				$redirect_to
			);
		}

		wp_safe_redirect( $redirect_to );
		exit;
	}

	/**
	 * Implements the "remove personal data" bulk action.
	 *
	 * @param array $order_ids The Order IDs.
	 * @return int Number of orders modified.
	 */
	private function do_bulk_action_remove_personal_data( $order_ids ): int {
		$changed = 0;

		foreach ( $order_ids as $id ) {
			$order = wc_get_order( $id );

			if ( ! $order ) {
				continue;
			}

			do_action( 'woocommerce_remove_order_personal_data', $order ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
			$changed++;
		}

		return $changed;
	}

	/**
	 * Implements the "mark <status>" bulk action.
	 *
	 * @param array  $order_ids  The order IDs to change.
	 * @param string $new_status The new order status.
	 * @return int Number of orders modified.
	 */
	private function do_bulk_action_mark_orders( $order_ids, $new_status ): int {
		$changed = 0;

		// Initialize payment gateways in case order has hooked status transition actions.
		WC()->payment_gateways();

		foreach ( $order_ids as $id ) {
			$order = wc_get_order( $id );

			if ( ! $order ) {
				continue;
			}

			$order->update_status( $new_status, __( 'Order status changed by bulk edit.', 'woocommerce' ), true );
			do_action( 'woocommerce_order_edit_status', $id, $new_status ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
			$changed++;
		}

		return $changed;
	}

	/**
	 * Handles bulk trashing of orders.
	 *
	 * @param int[] $ids Order IDs to be trashed.
	 * @param bool  $force_delete When set, the order will be completed deleted. Otherwise, it will be trashed.
	 *
	 * @return int Number of orders that were trashed.
	 */
	private function do_delete( array $ids, bool $force_delete = false ): int {
		$orders_store = wc_get_container()->get( OrdersTableDataStore::class );
		$delete_args  = $force_delete ? array( 'force_delete' => true ) : array();
		$changed      = 0;

		foreach ( $ids as $id ) {
			$order = wc_get_order( $id );
			$orders_store->delete( $order, $delete_args );
			$updated_order = wc_get_order( $id );

			if ( ( $force_delete && false === $updated_order ) || ( ! $force_delete && $updated_order->get_status() === 'trash' ) ) {
				$changed++;
			}
		}

		return $changed;
	}

	/**
	 * Handles bulk restoration of trashed orders.
	 *
	 * @param array $ids Order IDs to be restored to their previous status.
	 *
	 * @return int Number of orders that were restored from the trash.
	 */
	private function do_untrash( array $ids ): int {
		$orders_store = wc_get_container()->get( OrdersTableDataStore::class );
		$changed      = 0;

		foreach ( $ids as $id ) {
			if ( $orders_store->untrash_order( wc_get_order( $id ) ) ) {
				$changed++;
			}
		}

		return $changed;
	}

	/**
	 * Show confirmation message that order status changed for number of orders.
	 */
	public function bulk_action_notices() {
		if ( empty( $_REQUEST['bulk_action'] ) ) {
			return;
		}

		$order_statuses = wc_get_order_statuses();
		$number         = absint( $_REQUEST['changed'] ?? 0 );
		$bulk_action    = wc_clean( wp_unslash( $_REQUEST['bulk_action'] ) );
		$message        = '';

		// Check if any status changes happened.
		foreach ( $order_statuses as $slug => $name ) {
			if ( 'marked_' . str_replace( 'wc-', '', $slug ) === $bulk_action ) { // WPCS: input var ok, CSRF ok.
				/* translators: %s: orders count */
				$message = sprintf( _n( '%s order status changed.', '%s order statuses changed.', $number, 'woocommerce' ), number_format_i18n( $number ) );
				break;
			}
		}

		switch ( $bulk_action ) {
			case 'removed_personal_data':
				/* translators: %s: orders count */
				$message = sprintf( _n( 'Removed personal data from %s order.', 'Removed personal data from %s orders.', $number, 'woocommerce' ), number_format_i18n( $number ) );
				echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
				break;

			case 'trashed':
				/* translators: %s: orders count */
				$message = sprintf( _n( '%s order moved to the Trash.', '%s orders moved to the Trash.', $number, 'woocommerce' ), number_format_i18n( $number ) );
				break;

			case 'untrashed':
				/* translators: %s: orders count */
				$message = sprintf( _n( '%s order restored from the Trash.', '%s orders restored from the Trash.', $number, 'woocommerce' ), number_format_i18n( $number ) );
				break;

			case 'deleted':
				/* translators: %s: orders count */
				$message = sprintf( _n( '%s order permanently deleted.', '%s orders permanently deleted.', $number, 'woocommerce' ), number_format_i18n( $number ) );
				break;
		}

		if ( ! empty( $message ) ) {
			echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		}
	}

	/**
	 * Enqueue list table scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		echo $this->get_order_preview_template(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_enqueue_script( 'wc-orders' );
	}

	/**
	 * Returns the HTML for the order preview template.
	 *
	 * @return string HTML template.
	 */
	public function get_order_preview_template(): string {
		$order_edit_url_placeholder =
			wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
			? esc_url( admin_url( 'admin.php?page=wc-orders&action=edit' ) ) . '&id={{ data.data.id }}'
			: esc_url( admin_url( 'post.php?action=edit' ) ) . '&post={{ data.data.id }}';

		ob_start();
		?>
		<script type="text/template" id="tmpl-wc-modal-view-order">
			<div class="wc-backbone-modal wc-order-preview">
				<div class="wc-backbone-modal-content">
					<section class="wc-backbone-modal-main" role="main">
						<header class="wc-backbone-modal-header">
							<mark class="order-status status-{{ data.status }}"><span>{{ data.status_name }}</span></mark>
							<?php /* translators: %s: order ID */ ?>
							<h1><?php echo esc_html( sprintf( __( 'Order #%s', 'woocommerce' ), '{{ data.order_number }}' ) ); ?></h1>
							<button class="modal-close modal-close-link dashicons dashicons-no-alt">
								<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
							</button>
						</header>
						<article>
							<?php do_action( 'woocommerce_admin_order_preview_start' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>

							<div class="wc-order-preview-addresses">
								<div class="wc-order-preview-address">
									<h2><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h2>
									{{{ data.formatted_billing_address }}}

									<# if ( data.data.billing.email ) { #>
										<strong><?php esc_html_e( 'Email', 'woocommerce' ); ?></strong>
										<a href="mailto:{{ data.data.billing.email }}">{{ data.data.billing.email }}</a>
									<# } #>

									<# if ( data.data.billing.phone ) { #>
										<strong><?php esc_html_e( 'Phone', 'woocommerce' ); ?></strong>
										<a href="tel:{{ data.data.billing.phone }}">{{ data.data.billing.phone }}</a>
									<# } #>

									<# if ( data.payment_via ) { #>
										<strong><?php esc_html_e( 'Payment via', 'woocommerce' ); ?></strong>
										{{{ data.payment_via }}}
									<# } #>
								</div>
								<# if ( data.needs_shipping ) { #>
									<div class="wc-order-preview-address">
										<h2><?php esc_html_e( 'Shipping details', 'woocommerce' ); ?></h2>
										<# if ( data.ship_to_billing ) { #>
											{{{ data.formatted_billing_address }}}
										<# } else { #>
											<a href="{{ data.shipping_address_map_url }}" target="_blank">{{{ data.formatted_shipping_address }}}</a>
										<# } #>

										<# if ( data.shipping_via ) { #>
											<strong><?php esc_html_e( 'Shipping method', 'woocommerce' ); ?></strong>
											{{ data.shipping_via }}
										<# } #>
									</div>
								<# } #>

								<# if ( data.data.customer_note ) { #>
									<div class="wc-order-preview-note">
										<strong><?php esc_html_e( 'Note', 'woocommerce' ); ?></strong>
										{{ data.data.customer_note }}
									</div>
								<# } #>
							</div>

							{{{ data.item_html }}}

							<?php do_action( 'woocommerce_admin_order_preview_end' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>
						</article>
						<footer>
							<div class="inner">
								{{{ data.actions_html }}}

								<a class="button button-primary button-large" aria-label="<?php esc_attr_e( 'Edit this order', 'woocommerce' ); ?>" href="<?php echo $order_edit_url_placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="wc-backbone-modal-backdrop modal-close"></div>
		</script>
		<?php

		$html = ob_get_clean();

		return $html;
	}

}
