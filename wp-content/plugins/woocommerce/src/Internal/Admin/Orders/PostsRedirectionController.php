<?php
namespace Automattic\WooCommerce\Internal\Admin\Orders;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

/**
 * When {@see OrdersTableDataStore} is in use, this class takes care of redirecting admins from CPT-based URLs
 * to the new ones.
 */
class PostsRedirectionController {

	/**
	 * Instance of the PageController class.
	 *
	 * @var PageController
	 */
	private $page_controller;

	/**
	 * Constructor.
	 *
	 * @param PageController $page_controller Page controller instance. Used to generate links/URLs.
	 */
	public function __construct( PageController $page_controller ) {
		$this->page_controller = $page_controller;

		if ( ! wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ) {
			return;
		}

		add_action(
			'load-edit.php',
			function() {
				$this->maybe_redirect_to_orders_page();
			}
		);

		add_action(
			'load-post-new.php',
			function() {
				$this->maybe_redirect_to_new_order_page();
			}
		);

		add_action(
			'load-post.php',
			function() {
				$this->maybe_redirect_to_edit_order_page();
			}
		);
	}

	/**
	 * If needed, performs a redirection to the main orders page.
	 *
	 * @return void
	 */
	private function maybe_redirect_to_orders_page(): void {
		$post_type = $_GET['post_type'] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! $post_type || ! in_array( $post_type, wc_get_order_types( 'admin-menu' ), true ) ) {
			return;
		}

		// Respect query args, except for 'post_type'.
		$query_args = wp_unslash( $_GET );
		$action     = $query_args['action'] ?? '';
		$posts      = $query_args['post'] ?? array();
		unset( $query_args['post_type'], $query_args['post'], $query_args['_wpnonce'], $query_args['_wp_http_referer'], $query_args['action'] );

		// Remap 'post_status' arg.
		if ( isset( $query_args['post_status'] ) ) {
			$query_args['status'] = $query_args['post_status'];
			unset( $query_args['post_status'] );
		}

		$new_url = $this->page_controller->get_base_page_url( $post_type );
		$new_url = add_query_arg( $query_args, $new_url );

		// Handle bulk actions.
		if ( $action && in_array( $action, array( 'trash', 'untrash', 'delete', 'mark_processing', 'mark_on-hold', 'mark_completed', 'mark_cancelled' ), true ) ) {
			check_admin_referer( 'bulk-posts' );

			$new_url = add_query_arg(
				array(
					'action'           => $action,
					'order'            => $posts,
					'_wp_http_referer' => $this->page_controller->get_orders_url(),
					'_wpnonce'         => wp_create_nonce( 'bulk-orders' ),
				),
				$new_url
			);
		}

		wp_safe_redirect( $new_url, 301 );
		exit;
	}

	/**
	 * If needed, performs a redirection to the new order page.
	 *
	 * @return void
	 */
	private function maybe_redirect_to_new_order_page(): void {
		$post_type = $_GET['post_type'] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! $post_type || ! in_array( $post_type, wc_get_order_types( 'admin-menu' ), true ) ) {
			return;
		}

		// Respect query args, except for 'post_type'.
		$query_args = wp_unslash( $_GET );
		unset( $query_args['post_type'] );

		$new_url = $this->page_controller->get_new_page_url( $post_type );
		$new_url = add_query_arg( $query_args, $new_url );

		wp_safe_redirect( $new_url, 301 );
		exit;
	}

	/**
	 * If needed, performs a redirection to the edit order page.
	 *
	 * @return void
	 */
	private function maybe_redirect_to_edit_order_page(): void {
		$post_id = absint( $_GET['post'] ?? 0 );

		$redirect_from_types   = wc_get_order_types( 'admin-menu' );
		$redirect_from_types[] = 'shop_order_placehold';

		if ( ! $post_id || ! in_array( get_post_type( $post_id ), $redirect_from_types, true ) || ! isset( $_GET['action'] ) ) {
			return;
		}

		// Respect query args, except for 'post'.
		$query_args = wp_unslash( $_GET );
		$action     = $query_args['action'];
		unset( $query_args['post'], $query_args['_wpnonce'], $query_args['_wp_http_referer'], $query_args['action'] );

		$new_url = '';

		switch ( $action ) {
			case 'edit':
				$new_url = $this->page_controller->get_edit_url( $post_id );
				break;

			case 'trash':
			case 'untrash':
			case 'delete':
				// Re-generate nonce if validation passes.
				check_admin_referer( $action . '-post_' . $post_id );

				$new_url = add_query_arg(
					array(
						'action'           => $action,
						'order'            => array( $post_id ),
						'_wp_http_referer' => $this->page_controller->get_orders_url(),
						'_wpnonce'         => wp_create_nonce( 'bulk-orders' ),
					),
					$this->page_controller->get_orders_url()
				);

				break;

			default:
				break;
		}

		if ( ! $new_url ) {
			return;
		}

		$new_url = add_query_arg( $query_args, $new_url );

		wp_safe_redirect( $new_url, 301 );
		exit;
	}

}

