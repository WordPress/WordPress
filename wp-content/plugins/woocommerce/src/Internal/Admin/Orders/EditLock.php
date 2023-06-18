<?php
namespace Automattic\WooCommerce\Internal\Admin\Orders;

/**
 * This class takes care of the edit lock logic when HPOS is enabled.
 * For better interoperability with WordPress, edit locks are stored in the same format as posts. That is, as a metadata
 * in the order object (key: '_edit_lock') in the format "timestamp:user_id".
 *
 * @since 7.8.0
 */
class EditLock {

	const META_KEY_NAME = '_edit_lock';

	/**
	 * Obtains lock information for a given order. If the lock has expired or it's assigned to an invalid user,
	 * the order is no longer considered locked.
	 *
	 * @param \WC_Order $order Order to check.
	 * @return bool|array
	 */
	public function get_lock( \WC_Order $order ) {
		$lock = $order->get_meta( self::META_KEY_NAME, true, 'edit' );
		if ( ! $lock ) {
			return false;
		}

		$lock = explode( ':', $lock );
		if ( 2 !== count( $lock ) ) {
			return false;
		}

		$time    = absint( $lock[0] );
		$user_id = isset( $lock[1] ) ? absint( $lock[1] ) : 0;

		if ( ! $time || ! get_user_by( 'id', $user_id ) ) {
			return false;
		}

		/** This filter is documented in WP's wp-admin/includes/ajax-actions.php */
		$time_window = apply_filters( 'wp_check_post_lock_window', 150 ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
		if ( time() >= ( $time + $time_window ) ) {
			return false;
		}

		return compact( 'time', 'user_id' );
	}

	/**
	 * Checks whether the order is being edited (i.e. locked) by another user.
	 *
	 * @param \WC_Order $order Order to check.
	 * @return bool TRUE if order is locked and currently being edited by another user. FALSE otherwise.
	 */
	public function is_locked_by_another_user( \WC_Order $order ) : bool {
		$lock = $this->get_lock( $order );
		return $lock && ( get_current_user_id() !== $lock['user_id'] );
	}

	/**
	 * Checks whether the order is being edited by any user.
	 *
	 * @param \WC_Order $order Order to check.
	 * @return boolean TRUE if order is locked and currently being edited by a user. FALSE otherwise.
	 */
	public function is_locked( \WC_Order $order ) : bool {
		return (bool) $this->get_lock( $order );
	}

	/**
	 * Assigns an order's edit lock to the current user.
	 *
	 * @param \WC_Order $order The order to apply the lock to.
	 * @return array|bool FALSE if no user is logged-in, an array in the same format as {@see get_lock()} otherwise.
	 */
	public function lock( \WC_Order $order ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$order->update_meta_data( self::META_KEY_NAME, time() . ':' . $user_id );
		$order->save_meta_data();

		return $order->get_meta( self::META_KEY_NAME, true, 'edit' );
	}

	/**
	 * Hooked to 'heartbeat_received' on the edit order page to refresh the lock on an order being edited by the current user.
	 *
	 * @param array $response The heartbeat response to be sent.
	 * @param array $data     Data sent through the heartbeat.
	 * @return array Response to be sent.
	 */
	public function refresh_lock_ajax( $response, $data ) {
		$order_id = absint( $data['wc-refresh-order-lock'] ?? 0 );
		if ( ! $order_id ) {
			return $response;
		}

		$order = wc_get_order( $order_id );
		if ( ! current_user_can( get_post_type_object( $order->get_type() )->cap->edit_post, $order->get_id() ) && ! current_user_can( 'manage_woocommerce' ) ) {
			return $response;
		}

		$response['wc-refresh-order-lock'] = array();

		if ( ! $this->is_locked_by_another_user( $order ) ) {
			$response['wc-refresh-order-lock']['lock'] = $this->lock( $order );
		} else {
			$current_lock = $this->get_lock( $order );
			$user         = get_user_by( 'id', $current_lock['user_id'] );

			$response['wc-refresh-order-lock']['error'] = array(
				// translators: %s is a user's name.
				'message'            => sprintf( __( '%s has taken over and is currently editing.', 'woocommerce' ), $user->display_name ),
				'user_name'          => $user->display_name,
				'user_avatar_src'    => get_option( 'show_avatars' ) ? get_avatar_url( $user->ID, array( 'size' => 64 ) ) : '',
				'user_avatar_src_2x' => get_option( 'show_avatars' ) ? get_avatar_url( $user->ID, array( 'size' => 128 ) ) : '',
			);
		}

		return $response;
	}

	/**
	 * Hooked to 'heartbeat_received' on the orders screen to refresh the locked status of orders in the list table.
	 *
	 * @param array $response The heartbeat response to be sent.
	 * @param array $data     Data sent through the heartbeat.
	 * @return array Response to be sent.
	 */
	public function check_locked_orders_ajax( $response, $data ) {
		if ( empty( $data['wc-check-locked-orders'] ) || ! is_array( $data['wc-check-locked-orders'] ) ) {
			return $response;
		}

		$response['wc-check-locked-orders'] = array();

		$order_ids = array_unique( array_map( 'absint', $data['wc-check-locked-orders'] ) );
		foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				continue;
			}

			if ( ! $this->is_locked_by_another_user( $order ) || ( ! current_user_can( get_post_type_object( $order->get_type() )->cap->edit_post, $order->get_id() ) && ! current_user_can( 'manage_woocommerce' ) ) ) {
				continue;
			}

			$response['wc-check-locked-orders'][ $order_id ] = true;
		}

		return $response;
	}

	/**
	 * Outputs HTML for the lock dialog based on the status of the lock on the order (if any).
	 * Depending on who owns the lock, this could be a message with the chance to take over or a message indicating that
	 * someone else has taken over the order.
	 *
	 * @param \WC_Order $order Order object.
	 * @return void
	 */
	public function render_dialog( $order ) {
		$locked = $this->is_locked_by_another_user( $order );
		$lock   = $this->get_lock( $order );
		$user   = get_user_by( 'id', $lock['user_id'] );

		$edit_url = wc_get_container()->get( \Automattic\WooCommerce\Internal\Admin\Orders\PageController::class )->get_edit_url( $order->get_id() );

		$sendback_url = wp_get_referer();
		if ( ! $sendback_url ) {
			$sendback_url = wc_get_container()->get( \Automattic\WooCommerce\Internal\Admin\Orders\PageController::class )->get_base_page_url( $order->get_type() );
		}

		$sendback_text = __( 'Go back', 'woocommerce' );
		?>
		<div id="post-lock-dialog" class="notification-dialog-wrap <?php echo $locked ? '' : 'hidden'; ?> order-lock-dialog">
			<div class="notification-dialog-background"></div>
			<div class="notification-dialog">
			<?php if ( $locked ) : ?>
			<div class="post-locked-message">
				<div class="post-locked-avatar"><?php echo get_avatar( $user->ID, 64 ); ?></div>
				<p class="currently-editing wp-tab-first" tabindex="0">
				<?php
				// translators: %s is a user's name.
				echo esc_html( sprintf( __( '%s is currently editing this order. Do you want to take over?', 'woocommerce' ), esc_html( $user->display_name ) ) );
				?>
				</p>
				<p>
					<a class="button" href="<?php echo esc_url( $sendback_url ); ?>"><?php echo esc_html( $sendback_text ); ?></a>
					<a class="button button-primary wp-tab-last" href="<?php echo esc_url( add_query_arg( 'claim-lock', '1', wp_nonce_url( $edit_url, 'claim-lock-' . $order->get_id() ) ) ); ?>"><?php esc_html_e( 'Take over', 'woocommerce' ); ?></a>
				</p>
			</div>
			<?php else : ?>
			<div class="post-taken-over">
				<div class="post-locked-avatar"></div>
				<p class="wp-tab-first" tabindex="0">
				<span class="currently-editing"></span><br />
				</p>
				<p><a class="button button-primary wp-tab-last" href="<?php echo esc_url( $sendback_url ); ?>"><?php echo esc_html( $sendback_text ); ?></a></p>
			</div>
			<?php endif; ?>
			</div>
		</div>
		<?php
	}

}
