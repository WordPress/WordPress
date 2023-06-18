<?php
/**
 * WebhookUtil class file.
 */

namespace Automattic\WooCommerce\Internal\Utilities;

use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;

/**
 * Class with utility methods for dealing with webhooks.
 */
class WebhookUtil {

	use AccessiblePrivateMethods;

	/**
	 * Creates a new instance of the class.
	 */
	public function __construct() {
		self::add_action( 'deleted_user', array( $this, 'reassign_webhooks_to_new_user_id' ), 10, 2 );
		self::add_action( 'delete_user_form', array( $this, 'maybe_render_user_with_webhooks_warning' ), 10, 2 );
	}

	/**
	 * Whenever a user is deleted, re-assign their webhooks to the new user.
	 *
	 * If re-assignment isn't selected during deletion, assign the webhooks to user_id 0,
	 * so that an admin can edit and re-save them in order to get them to be assigned to a valid user.
	 *
	 * @param int      $old_user_id ID of the deleted user.
	 * @param int|null $new_user_id ID of the user to reassign existing data to, or null if no re-assignment is requested.
	 *
	 * @return void
	 * @since 7.8.0
	 */
	private function reassign_webhooks_to_new_user_id( int $old_user_id, ?int $new_user_id ): void {
		$webhook_ids = $this->get_webhook_ids_for_user( $old_user_id );

		foreach ( $webhook_ids as $webhook_id ) {
			$webhook = new \WC_Webhook( $webhook_id );
			$webhook->set_user_id( $new_user_id ?? 0 );
			$webhook->save();
		}
	}

	/**
	 * When users are about to be deleted show an informative text if they have webhooks assigned.
	 *
	 * @param \WP_User $current_user The current logged in user.
	 * @param array    $userids Array with the ids of the users that are about to be deleted.
	 * @return void
	 * @since 7.8.0
	 */
	private function maybe_render_user_with_webhooks_warning( \WP_User $current_user, array $userids ): void {
		global $wpdb;

		$at_least_one_user_with_webhooks = false;

		foreach ( $userids as $user_id ) {
			$webhook_ids = $this->get_webhook_ids_for_user( $user_id );
			if ( empty( $webhook_ids ) ) {
				continue;
			}

			$at_least_one_user_with_webhooks = true;

			$user_data      = get_userdata( $user_id );
			$user_login     = false === $user_data ? '' : $user_data->user_login;
			$webhooks_count = count( $webhook_ids );

			$text = sprintf(
				/* translators: 1 = user id, 2 = user login, 3 = webhooks count */
				_nx(
					'User #%1$s %2$s has created %3$d WooCommerce webhook.',
					'User #%1$s %2$s has created %3$d WooCommerce webhooks.',
					$webhooks_count,
					'user webhook count',
					'woocommerce'
				),
				$user_id,
				$user_login,
				$webhooks_count
			);

			echo '<p>' . esc_html( $text ) . '</p>';
		}

		if ( ! $at_least_one_user_with_webhooks ) {
			return;
		}

		$webhooks_settings_url = esc_url_raw( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=webhooks' ) );

		// This block of code is copied from WordPress' users.php.
		// phpcs:disable WooCommerce.Commenting.CommentHooks, WordPress.DB.PreparedSQL.NotPrepared
		$users_have_content = (bool) apply_filters( 'users_have_additional_content', false, $userids );
		if ( ! $users_have_content ) {
			if ( $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_author IN( " . implode( ',', $userids ) . ' ) LIMIT 1' ) ) {
				$users_have_content = true;
			} elseif ( $wpdb->get_var( "SELECT link_id FROM {$wpdb->links} WHERE link_owner IN( " . implode( ',', $userids ) . ' ) LIMIT 1' ) ) {
				$users_have_content = true;
			}
		}
		// phpcs:enable WooCommerce.Commenting.CommentHooks, WordPress.DB.PreparedSQL.NotPrepared

		if ( $users_have_content ) {
			$text = __( 'If the "Delete all content" option is selected, the affected WooCommerce webhooks will <b>not</b> be deleted and will be attributed to user id 0.<br/>', 'woocommerce' );
		} else {
			$text = __( 'The affected WooCommerce webhooks will <b>not</b> be deleted and will be attributed to user id 0.<br/>', 'woocommerce' );
		}

		$text .= sprintf(
			/* translators: 1 = url of the WooCommerce webhooks settings page */
			__( 'After that they can be reassigned to the logged-in user by going to the <a href="%1$s">WooCommerce webhooks settings page</a> and re-saving them.', 'woocommerce' ),
			$webhooks_settings_url
		);

		echo '<p>' . wp_kses_post( $text ) . '</p>';
	}

	/**
	 * Get the ids of the webhooks assigned to a given user.
	 *
	 * @param int $user_id User id.
	 * @return int[] Array of webhook ids.
	 */
	private function get_webhook_ids_for_user( int $user_id ): array {
		$data_store = \WC_Data_Store::load( 'webhook' );
		return $data_store->search_webhooks(
			array(
				'user_id' => $user_id,
			)
		);
	}
}
