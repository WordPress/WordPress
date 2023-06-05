<?php

namespace Automattic\WooCommerce\Internal\Admin\Orders;

use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;

/**
 * When Custom Order Tables are not the default order store (ie, posts are authoritative), we should take care of
 * redirecting requests for the order editor and order admin list table to the equivalent posts-table screens.
 *
 * If the redirect logic is problematic, it can be unhooked using code like the following example:
 *
 *     remove_action(
 *         'admin_page_access_denied',
 *         array( wc_get_container()->get( COTRedirectionController::class ), 'handle_hpos_admin_requests' )
 *     );
 */
class COTRedirectionController {
	use AccessiblePrivateMethods;

	/**
	 * Add hooks needed to perform our magic.
	 */
	public function setup(): void {
		// Only take action in cases where access to the admin screen would otherwise be denied.
		self::add_action( 'admin_page_access_denied', array( $this, 'handle_hpos_admin_requests' ) );
	}

	/**
	 * Listen for denied admin requests and, if they appear to relate to HPOS admin screens, potentially
	 * redirect the user to the equivalent CPT-driven screens.
	 *
	 * @param array|null $query_params The query parameters to use when determining the redirect. If not provided, the $_GET superglobal will be used.
	 */
	private function handle_hpos_admin_requests( $query_params = null ) {
		$query_params = is_array( $query_params ) ? $query_params : $_GET;

		if ( ! isset( $query_params['page'] ) || 'wc-orders' !== $query_params['page'] ) {
			return;
		}

		$params = wp_unslash( $query_params );
		$action = $params['action'] ?? '';
		unset( $params['page'] );

		if ( 'edit' === $action && isset( $params['id'] ) ) {
			$params['post'] = $params['id'];
			unset( $params['id'] );
			$new_url = add_query_arg( $params, get_admin_url( null, 'post.php' ) );
		} elseif ( 'new' === $action ) {
			unset( $params['action'] );
			$params['post_type'] = 'shop_order';
			$new_url             = add_query_arg( $params, get_admin_url( null, 'post-new.php' ) );
		} else {
			// If nonce parameters are present and valid, rebuild them for the CPT admin list table.
			if ( isset( $params['_wpnonce'] ) && check_admin_referer( 'bulk-orders' ) ) {
				$params['_wp_http_referer'] = get_admin_url( null, 'edit.php?post_type=shop_order' );
				$params['_wpnonce']         = wp_create_nonce( 'bulk-posts' );
			}

			// If an `order` array parameter is present, rename as `post`.
			if ( isset( $params['order'] ) && is_array( $params['order'] ) ) {
				$params['post'] = $params['order'];
				unset( $params['order'] );
			}

			$params['post_type'] = 'shop_order';
			$new_url             = add_query_arg( $params, get_admin_url( null, 'edit.php' ) );
		}

		if ( ! empty( $new_url ) && wp_safe_redirect( $new_url, 301 ) ) {
			exit;
		}
	}
}
