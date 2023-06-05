<?php
namespace Automattic\WooCommerce\Internal\Admin\Orders;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;

/**
 * Controls the different pages/screens associated to the "Orders" menu page.
 */
class PageController {

	use AccessiblePrivateMethods;

	/**
	 * The order type.
	 *
	 * @var string
	 */
	private $order_type = '';

	/**
	 * Instance of the posts redirection controller.
	 *
	 * @var PostsRedirectionController
	 */
	private $redirection_controller;

	/**
	 * Instance of the orders list table.
	 *
	 * @var ListTable
	 */
	private $orders_table;

	/**
	 * Instance of orders edit form.
	 *
	 * @var Edit
	 */
	private $order_edit_form;

	/**
	 * Current action.
	 *
	 * @var string
	 */
	private $current_action = '';

	/**
	 * Order object to be used in edit/new form.
	 *
	 * @var \WC_Order
	 */
	private $order;

	/**
	 * Verify that user has permission to edit orders.
	 *
	 * @return void
	 */
	private function verify_edit_permission() {
		if ( 'edit_order' === $this->current_action && ( ! isset( $this->order ) || ! $this->order ) ) {
			wp_die( esc_html__( 'You attempted to edit an order that does not exist. Perhaps it was deleted?', 'woocommerce' ) );
		}

		if ( $this->order->get_type() !== $this->order_type ) {
			wp_die( esc_html__( 'Order type mismatch.', 'woocommerce' ) );
		}

		if ( ! current_user_can( get_post_type_object( $this->order_type )->cap->edit_post, $this->order->get_id() ) && ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to edit this order', 'woocommerce' ) );
		}

		if ( 'trash' === $this->order->get_status() ) {
			wp_die( esc_html__( 'You cannot edit this item because it is in the Trash. Please restore it and try again.', 'woocommerce' ) );
		}
	}

	/**
	 * Verify that user has permission to create order.
	 *
	 * @return void
	 */
	private function verify_create_permission() {
		if ( ! current_user_can( get_post_type_object( $this->order_type )->cap->publish_posts ) && ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You don\'t have permission to create a new order', 'woocommerce' ) );
		}

		if ( isset( $this->order ) ) {
			$this->verify_edit_permission();
		}
	}

	/**
	 * Sets up the page controller, including registering the menu item.
	 *
	 * @return void
	 */
	public function setup(): void {
		$this->redirection_controller = new PostsRedirectionController( $this );

		// Register menu.
		if ( 'admin_menu' === current_action() ) {
			$this->register_menu();
		} else {
			add_action( 'admin_menu', 'register_menu', 9 );
		}

		$this->set_order_type();
		$this->set_action();

		$page_suffix = ( 'shop_order' === $this->order_type ? '' : '--' . $this->order_type );

		self::add_action( 'load-woocommerce_page_wc-orders' . $page_suffix, array( $this, 'handle_load_page_action' ) );
	}

	/**
	 * Perform initialization for the current action.
	 */
	private function handle_load_page_action() {
		if ( method_exists( $this, 'setup_action_' . $this->current_action ) ) {
			$this->{"setup_action_{$this->current_action}"}();
		}
	}

	/**
	 * Determines the order type for the current screen.
	 *
	 * @return void
	 */
	private function set_order_type() {
		global $plugin_page, $pagenow;

		if ( 'admin.php' !== $pagenow || 0 !== strpos( $plugin_page, 'wc-orders' ) ) {
			return;
		}

		$this->order_type = str_replace( array( 'wc-orders--', 'wc-orders' ), '', $plugin_page );
		$this->order_type = empty( $this->order_type ) ? 'shop_order' : $this->order_type;

		$wc_order_type = wc_get_order_type( $this->order_type );
		$wp_order_type = get_post_type_object( $this->order_type );

		if ( ! $wc_order_type || ! $wp_order_type || ! $wp_order_type->show_ui || ! current_user_can( $wp_order_type->cap->edit_posts ) ) {
			wp_die();
		}
	}

	/**
	 * Sets the current action based on querystring arguments. Defaults to 'list_orders'.
	 *
	 * @return void
	 */
	private function set_action(): void {
		switch ( isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '' ) {
			case 'edit':
				$this->current_action = 'edit_order';
				break;
			case 'new':
				$this->current_action = 'new_order';
				break;
			default:
				$this->current_action = 'list_orders';
				break;
		}
	}

	/**
	 * Registers the "Orders" menu.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		$order_types = wc_get_order_types( 'admin-menu' );

		foreach ( $order_types as $order_type ) {
			$post_type = get_post_type_object( $order_type );

			add_submenu_page(
				'woocommerce',
				$post_type->labels->name,
				$post_type->labels->menu_name,
				$post_type->cap->edit_posts,
				'wc-orders' . ( 'shop_order' === $order_type ? '' : '--' . $order_type ),
				array( $this, 'output' )
			);
		}

		// In some cases (such as if the authoritative order store was changed earlier in the current request) we
		// need an extra step to remove the menu entry for the menu post type.
		add_action(
			'admin_init',
			function() use ( $order_types ) {
				foreach ( $order_types as $order_type ) {
					remove_submenu_page( 'woocommerce', 'edit.php?post_type=' . $order_type );
				}
			}
		);
	}

	/**
	 * Outputs content for the current orders screen.
	 *
	 * @return void
	 */
	public function output(): void {
		switch ( $this->current_action ) {
			case 'edit_order':
			case 'new_order':
				if ( ! isset( $this->order_edit_form ) ) {
					$this->order_edit_form = new Edit();
					$this->order_edit_form->setup( $this->order );
				}
				$this->order_edit_form->set_current_action( $this->current_action );
				$this->order_edit_form->display();
				break;
			case 'list_orders':
			default:
				$this->orders_table->prepare_items();
				$this->orders_table->display();
				break;
		}
	}

	/**
	 * Handles initialization of the orders list table.
	 *
	 * @return void
	 */
	private function setup_action_list_orders(): void {
		$this->orders_table = wc_get_container()->get( ListTable::class );
		$this->orders_table->setup(
			array(
				'order_type' => $this->order_type,
			)
		);

		if ( $this->orders_table->current_action() ) {
			$this->orders_table->handle_bulk_actions();
		}

		$this->strip_http_referer();
	}

	/**
	 * Perform a redirect to remove the `_wp_http_referer` and `_wpnonce` strings if present in the URL (see also
	 * wp-admin/edit.php where a similar process takes place), otherwise the size of this field builds to an
	 * unmanageable length over time.
	 */
	private function strip_http_referer(): void {
		$current_url  = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
		$stripped_url = remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), $current_url );

		if ( $stripped_url !== $current_url ) {
			wp_safe_redirect( $stripped_url );
			exit;
		}
	}

	/**
	 * Handles initialization of the orders edit form.
	 *
	 * @return void
	 */
	private function setup_action_edit_order(): void {
		global $theorder;
		$this->order = wc_get_order( absint( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
		$this->verify_edit_permission();
		$theorder = $this->order;
	}

	/**
	 * Handles initialization of the orders edit form with a new order.
	 *
	 * @return void
	 */
	private function setup_action_new_order(): void {
		global $theorder;

		$this->verify_create_permission();

		$order_class_name = wc_get_order_type( $this->order_type )['class_name'];
		if ( ! $order_class_name || ! class_exists( $order_class_name ) ) {
			wp_die();
		}

		$this->order = new $order_class_name();
		$this->order->set_object_read( false );
		$this->order->set_status( 'pending' );
		$this->order->save();

		$theorder = $this->order;
	}

	/**
	 * Returns the current order type.
	 *
	 * @return string
	 */
	public function get_order_type() {
		return $this->order_type;
	}

	/**
	 * Helper method to generate a link to the main orders screen.
	 *
	 * @return string Orders screen URL.
	 */
	public function get_orders_url(): string {
		return wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ?
			admin_url( 'admin.php?page=wc-orders' ) :
			admin_url( 'edit.php?post_type=shop_order' );
	}

	/**
	 * Helper method to generate edit link for an order.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return string Edit link.
	 */
	public function get_edit_url( int $order_id ) : string {
		if ( ! wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ) {
			return admin_url( 'post.php?post=' . absint( $order_id ) ) . '&action=edit';
		}

		$order = wc_get_order( $order_id );

		// Confirm we could obtain the order object (since it's possible it will not exist, due to a sync issue, or may
		// have been deleted in a separate concurrent request).
		if ( false === $order ) {
			wc_get_logger()->debug(
				sprintf(
					/* translators: %d order ID. */
					__( 'Attempted to determine the edit URL for order %d, however the order does not exist.', 'woocommerce' ),
					$order_id
				)
			);
			$order_type = 'shop_order';
		} else {
			$order_type = $order->get_type();
		}

		return add_query_arg(
			array(
				'action' => 'edit',
				'id'     => absint( $order_id ),
			),
			$this->get_base_page_url( $order_type )
		);
	}

	/**
	 * Helper method to generate a link for creating order.
	 *
	 * @param string $order_type The order type. Defaults to 'shop_order'.
	 * @return string
	 */
	public function get_new_page_url( $order_type = 'shop_order' ) : string {
		$url = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ?
			add_query_arg( 'action', 'new', $this->get_base_page_url( $order_type ) ) :
			admin_url( 'post-new.php?post_type=' . $order_type );

		return $url;
	}

	/**
	 * Helper method to generate a link to the main screen for a custom order type.
	 *
	 * @param string $order_type The order type.
	 *
	 * @return string
	 *
	 * @throws \Exception When an invalid order type is passed.
	 */
	public function get_base_page_url( $order_type ): string {
		$order_types_with_ui = wc_get_order_types( 'admin-menu' );

		if ( ! in_array( $order_type, $order_types_with_ui, true ) ) {
			// translators: %s is a custom order type.
			throw new \Exception( sprintf( __( 'Invalid order type: %s.', 'woocommerce' ), esc_html( $order_type ) ) );
		}

		return admin_url( 'admin.php?page=wc-orders' . ( 'shop_order' === $order_type ? '' : '--' . $order_type ) );
	}

}
