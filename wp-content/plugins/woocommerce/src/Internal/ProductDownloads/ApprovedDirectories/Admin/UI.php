<?php

namespace Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Admin;

use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register;
use Automattic\WooCommerce\Internal\Utilities\Users;
use Exception;
use WC_Admin_Settings;

/**
 * Manages user interactions for product download URL safety.
 */
class UI {
	/**
	 * The active register of approved directories.
	 *
	 * @var Register
	 */
	private $register;

	/**
	 * The WP_List_Table instance used to display approved directories.
	 *
	 * @var Table
	 */
	private $table;

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

		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
		add_action( 'load-woocommerce_page_wc-settings', array( $this, 'setup' ) );
		add_action( 'woocommerce_settings_products', array( $this, 'render' ) );
	}

	/**
	 * Injects our new settings section (when approved directory rules are disabled, it will not show).
	 *
	 * @param array $sections Other admin settings sections.
	 *
	 * @return array
	 */
	public function add_section( array $sections ): array {
		$sections['download_urls'] = __( 'Approved download directories', 'woocommerce' );
		return $sections;
	}

	/**
	 * Sets up the table, renders any notices and processes actions as needed.
	 */
	public function setup() {
		if ( ! $this->is_download_urls_screen() ) {
			return;
		}

		$this->table = new Table();
		$this->admin_notices();
		$this->handle_search();
		$this->process_actions();
	}

	/**
	 * Renders the UI.
	 */
	public function render() {
		if ( null === $this->table || ! $this->is_download_urls_screen() ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] && isset( $_REQUEST['url'] ) ) {
			$this->edit_screen( (int) $_REQUEST['url'] );
			return;
		}
		// phpcs:enable

		// Show list table.
		$this->table->prepare_items();
		wp_nonce_field( 'modify_approved_directories', 'check' );
		$this->display_title();
		$this->table->render_views();
		$this->table->search_box( _x( 'Search', 'Approved Directory URLs', 'woocommerce' ), 'download_url_search' );
		$this->table->display();
	}

	/**
	 * Indicates if we are currently on the download URLs admin screen.
	 *
	 * @return bool
	 */
	private function is_download_urls_screen(): bool {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['tab'] )
			&& 'products' === $_GET['tab']
			&& isset( $_GET['section'] )
			&& 'download_urls' === $_GET['section'];
		// phpcs:enable
	}

	/**
	 * Process bulk and single-row actions.
	 */
	private function process_actions() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$ids = isset( $_REQUEST['url'] ) ? array_map( 'absint', (array) $_REQUEST['url'] ) : array();

		if ( empty( $ids ) || empty( $_REQUEST['action'] ) ) {
			return;
		}

		$this->security_check();

		$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );

		switch ( $action ) {
			case 'edit':
				$this->process_edits( current( $ids ) );
				break;

			case 'delete':
			case 'enable':
			case 'disable':
				$this->process_bulk_actions( $ids, $action );
				break;

			case 'enable-all':
			case 'disable-all':
				$this->process_all_actions( $action );
				break;

			case 'turn-on':
			case 'turn-off':
				$this->process_on_off( $action );
				break;
		}
		// phpcs:enable
	}

	/**
	 * Support pagination across search results.
	 *
	 * In the context of the WC settings screen, form data is submitted by the post method: that poses
	 * a problem for the default WP_List_Table pagination logic which expects the search value to live
	 * as part of the URL query. This method is a simple shim to bridge the resulting gap.
	 */
	private function handle_search() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended

		// If a search value has not been POSTed, or if it was POSTed but is already equal to the
		// same value in the URL query, we need take no further action.
		if ( empty( $_POST['s'] ) || sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ) === $_POST['s'] ) {
			return;
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'paged' => absint( $_GET['paged'] ?? 1 ),
					's'     => sanitize_text_field( wp_unslash( $_POST['s'] ) ),
				),
				$this->table->get_base_url()
			)
		);
		// phpcs:enable

		exit;
	}

	/**
	 * Handles updating or adding a new URL to the list of approved directories.
	 *
	 * @param int $url_id The ID of the rule to be edited/created. Zero if we are creating a new entry.
	 */
	private function process_edits( int $url_id ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$url     = esc_url_raw( wp_unslash( $_POST['approved_directory_url'] ?? '' ) );
		$enabled = (bool) sanitize_text_field( wp_unslash( $_POST['approved_directory_enabled'] ?? '' ) );

		if ( empty( $url ) ) {
			return;
		}

		$redirect_url = add_query_arg( 'id', $url_id, $this->table->get_action_url( 'edit', $url_id ) );

		try {
			$upserted = 0 === $url_id
				? $this->register->add_approved_directory( $url, $enabled )
				: $this->register->update_approved_directory( $url_id, $url, $enabled );

			if ( is_integer( $upserted ) ) {
				$redirect_url = add_query_arg( 'url', $upserted, $redirect_url );
			}

			$redirect_url = add_query_arg( 'edit-status', 0 === $url_id ? 'added' : 'updated', $redirect_url );
		} catch ( Exception $e ) {
			$redirect_url = add_query_arg(
				array(
					'edit-status'   => 'failure',
					'submitted-url' => $url,
				),
				$redirect_url
			);
		}

		wp_safe_redirect( $redirect_url );
		exit;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Processes actions that can be applied in bulk (requests to delete, enable
	 * or disable).
	 *
	 * @param int[]  $ids    The ID(s) to be updates.
	 * @param string $action The action to be applied.
	 */
	private function process_bulk_actions( array $ids, string $action ) {
		$deletes  = 0;
		$enabled  = 0;
		$disabled = 0;
		$register = wc_get_container()->get( Register::class );

		foreach ( $ids as $id ) {
			if ( 'delete' === $action && $register->delete_by_id( $id ) ) {
				$deletes++;
			} elseif ( 'enable' === $action && $register->enable_by_id( $id ) ) {
				$enabled++;
			} elseif ( 'disable' === $action && $register->disable_by_id( $id ) ) {
				$disabled ++;
			}
		}

		$fails    = count( $ids ) - $deletes - $enabled - $disabled;
		$redirect = $this->table->get_base_url();

		if ( $deletes ) {
			$redirect = add_query_arg( 'deleted-ids', $deletes, $redirect );
		} elseif ( $enabled ) {
			$redirect = add_query_arg( 'enabled-ids', $enabled, $redirect );
		} elseif ( $disabled ) {
			$redirect = add_query_arg( 'disabled-ids', $disabled, $redirect );
		}

		if ( $fails ) {
			$redirect = add_query_arg( 'bulk-fails', $fails, $redirect );
		}

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Handles the enable/disable-all actions.
	 *
	 * @param string $action The action to be applied.
	 */
	private function process_all_actions( string $action ) {
		$register = wc_get_container()->get( Register::class );
		$redirect = $this->table->get_base_url();

		switch ( $action ) {
			case 'enable-all':
				$redirect = add_query_arg( 'enabled-all', (int) $register->enable_all(), $redirect );
				break;

			case 'disable-all':
				$redirect = add_query_arg( 'disabled-all', (int) $register->disable_all(), $redirect );
				break;
		}

		wp_safe_redirect( $redirect );
			exit;
}

	/**
	 * Handles turning on/off the entire approved download directory system (vs enabling
	 * and disabling of individual rules).
	 *
	 * @param string $action Whether the feature should be turned on or off.
	 */
	private function process_on_off( string $action ) {
		switch ( $action ) {
				case 'turn-on':
					$this->register->set_mode( Register::MODE_ENABLED );
					break;

			case 'turn-off':
				$this->register->set_mode( Register::MODE_DISABLED );
				break;
		}
	}

	/**
	 * Displays the screen title, etc.
	 */
	private function display_title() {
		$turn_on_off = $this->register->get_mode() === Register::MODE_ENABLED
			? '<a href="' . esc_url( $this->table->get_action_url( 'turn-off', 0 ) ) . '" class="page-title-action">' . esc_html_x( 'Stop Enforcing Rules', 'Approved product download directories', 'woocommerce' ) . '</a>'
			: '<a href="' . esc_url( $this->table->get_action_url( 'turn-on', 0 ) ) . '" class="page-title-action">' . esc_html_x( 'Start Enforcing Rules', 'Approved product download directories', 'woocommerce' ) . '</a>';

		?>
			<h2 class='wc-table-list-header'>
				<?php esc_html_e( 'Approved Download Directories', 'woocommerce' ); ?>
				<a href='<?php echo esc_url( $this->table->get_action_url( 'edit', 0 ) ); ?>' class='page-title-action'><?php esc_html_e( 'Add New', 'woocommerce' ); ?></a>
				<?php echo $turn_on_off; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</h2>
		<?php
	}

	/**
	 * Renders the editor screen for approved directory URLs.
	 *
	 * @param int $url_id The ID of the rule to be edited (may be zero for new rules).
	 */
	private function edit_screen( int $url_id ) {
		$this->security_check();
		$existing = $this->register->get_by_id( $url_id );

		if ( 0 !== $url_id && ! $existing ) {
			WC_Admin_Settings::add_error( _x( 'The provided ID was invalid.', 'Approved product download directories', 'woocommerce' ) );
			WC_Admin_Settings::show_messages();
			return;
		}

		$title = $existing
			? __( 'Edit Approved Directory', 'woocommerce' )
			: __( 'Add New Approved Directory', 'woocommerce' );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$submitted    = sanitize_text_field( wp_unslash( $_GET['submitted-url'] ?? '' ) );
		$existing_url = $existing ? $existing->get_url() : '';
		$enabled      = $existing ? $existing->is_enabled() : true;
		// phpcs:enable

		?>
			<h2 class='wc-table-list-header'>
				<?php echo esc_html( $title ); ?>
				<?php if ( $existing ) : ?>
					<a href="<?php echo esc_url( $this->table->get_action_url( 'edit', 0 ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'woocommerce' ); ?></a>
				<?php endif; ?>
				<a href="<?php echo esc_url( $this->table->get_base_url() ); ?> " class="page-title-action"><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></a>
			</h2>
			<table class='form-table'>
				<tbody>
					<tr valign='top'>
						<th scope='row' class='titledesc'>
							<label for='approved_directory_url'> <?php echo esc_html_x( 'Directory URL', 'Approved product download directories', 'woocommerce' ); ?> </label>
						</th>
						<td class='forminp'>
							<input name='approved_directory_url' id='approved_directory_url' type='text' class='input-text regular-input' value='<?php echo esc_attr( empty( $submitted ) ? $existing_url : $submitted ); ?>'>
						</td>
					</tr>
					<tr valign='top'>
						<th scope='row' class='titledesc'>
							<label for='approved_directory_enabled'> <?php echo esc_html_x( 'Enabled', 'Approved product download directories', 'woocommerce' ); ?> </label>
						</th>
						<td class='forminp'>
							<input name='approved_directory_enabled' id='approved_directory_enabled' type='checkbox' value='1' <?php checked( true, $enabled ); ?>'>
						</td>
					</tr>
				</tbody>
			</table>
			<input name='id' id='approved_directory_id' type='hidden' value='{$url_id}'>
		<?php
	}

	/**
	 * Displays any admin notices that might be needed.
	 */
	private function admin_notices() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$successfully_deleted  = isset( $_GET['deleted-ids'] ) ? (int) $_GET['deleted-ids'] : 0;
		$successfully_enabled  = isset( $_GET['enabled-ids'] ) ? (int) $_GET['enabled-ids'] : 0;
		$successfully_disabled = isset( $_GET['disabled-ids'] ) ? (int) $_GET['disabled-ids'] : 0;
		$failed_updates        = isset( $_GET['bulk-fails'] ) ? (int) $_GET['bulk-fails'] : 0;
		$edit_status           = sanitize_text_field( wp_unslash( $_GET['edit-status'] ?? '' ) );
		$edit_url              = esc_attr( sanitize_text_field( wp_unslash( $_GET['submitted-url'] ?? '' ) ) );
		// phpcs:enable

		if ( $successfully_deleted ) {
			WC_Admin_Settings::add_message(
				sprintf(
					/* translators: %d: count */
					_n( '%d approved directory URL deleted.', '%d approved directory URLs deleted.', $successfully_deleted, 'woocommerce' ),
					$successfully_deleted
				)
			);
		} elseif ( $successfully_enabled ) {
			WC_Admin_Settings::add_message(
				sprintf(
				/* translators: %d: count */
					_n( '%d approved directory URL enabled.', '%d approved directory URLs enabled.', $successfully_enabled, 'woocommerce' ),
					$successfully_enabled
				)
			);
		} elseif ( $successfully_disabled ) {
			WC_Admin_Settings::add_message(
				sprintf(
				/* translators: %d: count */
					_n( '%d approved directory URL disabled.', '%d approved directory URLs disabled.', $successfully_disabled, 'woocommerce' ),
					$successfully_disabled
				)
			);
		}

		if ( $failed_updates ) {
			WC_Admin_Settings::add_error(
				sprintf(
					/* translators: %d: count */
					_n( '%d URL could not be updated.', '%d URLs could not be updated.', $failed_updates, 'woocommerce' ),
					$failed_updates
				)
			);
		}

		if ( 'added' === $edit_status ) {
			WC_Admin_Settings::add_message( __( 'URL was successfully added.', 'woocommerce' ) );
		}

		if ( 'updated' === $edit_status ) {
			WC_Admin_Settings::add_message( __( 'URL was successfully updated.', 'woocommerce' ) );
		}

		if ( 'failure' === $edit_status && ! empty( $edit_url ) ) {
			WC_Admin_Settings::add_error(
				sprintf(
					/* translators: %s is the submitted URL. */
					__( '"%s" could not be saved. Please review, ensure it is a valid URL and try again.', 'woocommerce' ),
					$edit_url
				)
			);
		}
	}

	/**
	 * Makes sure the user has appropriate permissions and that we have a valid nonce.
	 */
	private function security_check() {
		if ( ! Users::is_site_administrator() || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['check'] ?? '' ) ), 'modify_approved_directories' ) ) {
			wp_die( esc_html__( 'You do not have permission to modify the list of approved directories for product downloads.', 'woocommerce' ) );
		}
	}
}
