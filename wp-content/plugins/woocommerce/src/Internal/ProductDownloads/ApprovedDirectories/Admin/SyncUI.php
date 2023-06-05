<?php

namespace Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Admin;

use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Synchronize;
use Automattic\WooCommerce\Internal\Utilities\Users;

/**
 * Adds tools to the Status > Tools page that can be used to (re-)initiate or stop a synchronization process
 * for Approved Download Directories.
 */
class SyncUI {
	/**
	 * The active register of approved directories.
	 *
	 * @var Register
	 */
	private $register;

	/**
	 * Sets up UI controls for product download URLs.
	 *
	 * @internal
	 *
	 * @param Register $register Register of approved directories.
	 */
	final public function init( Register $register ) {
		$this->register = $register;
	}

	/**
	 * Performs any work needed to add hooks and otherwise integrate with the wider system,
	 * except in the case where the current user is not a site administrator, no hooks will
	 * be initialized.
	 */
	final public function init_hooks() {
		if ( ! Users::is_site_administrator() ) {
			return;
		}

		add_filter( 'woocommerce_debug_tools', array( $this, 'add_tools' ) );
	}

	/**
	 * Adds Approved Directory list-related entries to the tools page.
	 *
	 * @param array $tools Admin tool definitions.
	 *
	 * @return array
	 */
	public function add_tools( array $tools ): array {
		$sync = wc_get_container()->get( Synchronize::class );

		if ( ! $sync->in_progress() ) {
			// Provide tools to trigger a fresh scan (migration) and to clear the Approved Directories list.
			$tools['approved_directories_sync'] = array(
				'name'             => __( 'Synchronize approved download directories', 'woocommerce' ),
				'desc'             => __( 'Updates the list of Approved Product Download Directories. Note that triggering this tool does not impact whether the Approved Download Directories list is enabled or not.', 'woocommerce' ),
				'button'           => __( 'Update', 'woocommerce' ),
				'callback'         => array( $this, 'trigger_sync' ),
				'requires_refresh' => true,
			);

			$tools['approved_directories_clear'] = array(
				'name'             => __( 'Empty the approved download directories list', 'woocommerce' ),
				'desc'             => __( 'Removes all existing entries from the Approved Product Download Directories list.', 'woocommerce' ),
				'button'           => __( 'Clear', 'woocommerce' ),
				'callback'         => array( $this, 'clear_existing_entries' ),
				'requires_refresh' => true,
			);
		} else {
			// Or if a scan (migration) is already in progress, offer a means of cancelling it.
			$tools['cancel_directories_scan'] = array(
				'name'     => __( 'Cancel synchronization of approved directories', 'woocommerce' ),
				'desc'     => sprintf(
				/* translators: %d is an integer between 0-100 representing the percentage complete of the current scan. */
					__( 'The Approved Product Download Directories list is currently being synchronized with the product catalog (%d%% complete). If you need to, you can cancel it.', 'woocommerce' ),
					$sync->get_progress()
				),
				'button'   => __( 'Cancel', 'woocommerce' ),
				'callback' => array( $this, 'cancel_sync' ),
			);
		}

		return $tools;
	}

	/**
	 * Triggers a new migration.
	 */
	public function trigger_sync() {
		$this->security_check();
		wc_get_container()->get( Synchronize::class )->start();
	}

	/**
	 * Clears all existing rules from the Approved Directories list.
	 */
	public function clear_existing_entries() {
		$this->security_check();
		$this->register->delete_all();
	}

	/**
	 * If a migration is in progress, this will attempt to cancel it.
	 */
	public function cancel_sync() {
		$this->security_check();
		wc_get_logger()->log( 'info', __( 'Approved Download Directories sync: scan has been cancelled.', 'woocommerce' ) );
		wc_get_container()->get( Synchronize::class )->stop();
	}

	/**
	 * Makes sure the user has appropriate permissions and that we have a valid nonce.
	 */
	private function security_check() {
		if ( ! Users::is_site_administrator() ) {
			wp_die( esc_html__( 'You do not have permission to modify the list of approved directories for product downloads.', 'woocommerce' ) );
		}
	}
}
