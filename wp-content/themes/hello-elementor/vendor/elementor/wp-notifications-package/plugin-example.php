<?php
/**
 * Plugin Name: WP Notifications Package
 * Description: ...
 * Plugin URI: https://elementor.com/
 * Author: Elementor.com
 * Version: 1.0.0
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Text Domain: wp-notifications-package
 */

use Elementor\WPNotificationsPackage\V120\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Plugin_Example {

	public Notifications $notifications;

	public function __construct() {
		$this->init();
	}

	private function init() {
		require __DIR__ . '/vendor/autoload.php';

		$this->notifications = new Notifications( [
			'app_name' => 'wp-notifications-package',
			'app_version' => '1.2.0',
			'short_app_name' => 'wppe',
			'app_data' => [
				'plugin_basename' => plugin_basename( __FILE__ ),
			],
		] );

		add_action( 'admin_notices', [ $this, 'display_notifications' ] );
		add_action( 'admin_footer', [ $this, 'display_dialog' ] );
	}

	public function display_notifications() {
		$notifications = $this->notifications->get_notifications_by_conditions();

		if ( empty( $notifications ) ) {
			return;
		}

		?>
		<div class="notice notice-info is-dismissible">
			<h3><?php esc_html_e( 'What\'s new:', 'wp-notifications-package' ); ?></h3>
			<ul>
				<?php foreach ( $notifications as $item ) : ?>
					<li><a href="<?php echo esc_url( $item['link'] ?? '#' ); ?>" target="_blank"><?php echo esc_html( $item['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php

		// Example with HTML Dialog modal
		?>
		<div class="notice notice-info is-dismissible">
			<button class="plugin-example-notifications-dialog-open">Open Notification</button>
		</div>
		<?php
	}

	public function display_dialog() {
		$notifications = $this->notifications->get_notifications_by_conditions();

		if ( empty( $notifications ) ) {
			return;
		}

		?>
		<style>
			#plugin-example-notifications-dialog {
				padding: 20px;
				border: 1px solid #ccc;
				box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
			}
			#plugin-example-notifications-dialog::backdrop {
				background: rgba(0, 0, 0, 0.5);
			}
		</style>
		<dialog id="plugin-example-notifications-dialog">
			<h3><?php esc_html_e( 'What\'s new:', 'wp-notifications-package' ); ?></h3>
			<ul>
				<?php foreach ( $notifications as $item ) : ?>
					<li><a href="<?php echo esc_url( $item['link'] ?? '#' ); ?>" target="_blank"><?php echo esc_html( $item['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<button class="close">Close</button>
		</dialog>

		<script>
			document.addEventListener( 'DOMContentLoaded', function() {
				const openDialogBtn = document.querySelector( '.plugin-example-notifications-dialog-open' );
				const closeDialogBtn = document.querySelector( '#plugin-example-notifications-dialog button.close' );
				const dialog = document.getElementById( 'plugin-example-notifications-dialog' );

				openDialogBtn.addEventListener( 'click', function() {
					dialog.showModal();
				} );

				closeDialogBtn.addEventListener( 'click', function() {
					dialog.close();
				} );
			} );
		</script>
		<?php
	}
}

new Plugin_Example();

