<?php
/**
 * Code snippets admin main list page.
 *
 * @package WPCode
 */

/**
 * Class for the code snippets page.
 */
class WPCode_Admin_Page_Code_Snippets extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode';

	/**
	 * Instance of the code snippets table.
	 *
	 * @see WP_List_Table
	 * @var WPCode_Code_Snippets_Table
	 */
	private $snippets_table;

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Code Snippets', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Page-specific hooks, init the custom WP_List_Table.
	 *
	 * @return void
	 */
	public function page_hooks() {
		$this->process_message();
		add_action( 'current_screen', array( $this, 'init_table' ) );
		add_action( 'admin_init', array( $this, 'maybe_capture_filter' ) );
		add_action( 'load-toplevel_page_wpcode', array( $this, 'maybe_process_bulk_action' ) );
		add_filter( 'screen_options_show_screen', '__return_false' );
		add_action( 'wpcode_admin_notices', array( $this, 'maybe_show_deactivated_notice' ) );
	}

	/**
	 * If the referer is set, remove and redirect.
	 *
	 * @return void
	 */
	public function maybe_capture_filter() {
		if ( ! empty( $_REQUEST['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) && isset( $_REQUEST['filter_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect(
				remove_query_arg(
					array(
						'_wp_http_referer',
						'_wpnonce',
					),
					wp_unslash( $_SERVER['REQUEST_URI'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				)
			);
			exit;
		}
		if ( ! empty( $_REQUEST['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) && isset( $_REQUEST['filter_clear'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect(
				add_query_arg(
					'page',
					'wpcode',
					admin_url( 'admin.php' )
				)
			);

			exit;
		}
	}

	/**
	 * Listener for bulk actions.
	 *
	 * @return void
	 */
	public function maybe_process_bulk_action() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$ids    = isset( $_GET['snippet_id'] ) ? array_map( 'absint', (array) $_GET['snippet_id'] ) : array();
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : false;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( empty( $ids ) || empty( $action ) ) {
			return;
		}
		if ( empty( $_GET['_wpnonce'] ) ) {
			return;
		}

		if (
			! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'bulk-wpcode-snippets' ) &&
			! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'wpcode_' . $action . '_nonce' )
		) {
			return;
		}

		$update_status_actions = array( 'trash', 'untrash' );

		if ( in_array( $action, $update_status_actions, true ) ) {
			$newstatus = 'trash' === $action ? 'trash' : 'draft';
			foreach ( $ids as $id ) {
				wp_update_post(
					array(
						'ID'          => $id,
						'post_status' => $newstatus,
					)
				);
			}
		}
		if ( 'delete' === $action ) {
			foreach ( $ids as $id ) {
				wp_delete_post( $id );
			}
		}
		$failed = 0;
		if ( 'enable' === $action ) {
			foreach ( $ids as $key => $id ) {
				$snippet = new WPCode_Snippet( $id );
				$snippet->activate();
				if ( ! $snippet->active ) {
					// If failed to activate don't count it.
					unset( $ids[ $key ] );
					$failed ++;
				}
			}
		}
		if ( 'disable' === $action ) {
			foreach ( $ids as $id ) {
				$snippet = new WPCode_Snippet( $id );
				$snippet->deactivate();
			}
		}
		$message = array(
			rtrim( $action, 'e' ) . 'ed' => count( $ids ),
		);
		if ( $failed ) {
			$message['error'] = $failed;
		}

		wpcode()->cache->cache_all_loaded_snippets();

		// Clear used library snippets.
		delete_transient( 'wpcode_used_library_snippets' );

		wp_safe_redirect(
			add_query_arg(
				$message,
				remove_query_arg(
					array(
						'action',
						'action2',
						'_wpnonce',
						'snippet_id',
						'paged',
						'_wp_http_referer',
					)
				)
			)
		);
		exit;

	}

	/**
	 * Init the custom table for the snippets list.
	 *
	 * @return void
	 */
	public function init_table() {
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-code-snippets-table.php';

		$this->snippets_table = new WPCode_Code_Snippets_Table();
	}

	/**
	 * Output the custom table and page content.
	 *
	 * @return void
	 */
	public function output_content() {
		$this->snippets_table->prepare_items();

		?>
		<form id="wpcode-code-snippets-table" method="get" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcode' ) ); ?>">
			<input type="hidden" name="page" value="wpcode"/>
			<?php
			$this->snippets_table->search_box( esc_html__( 'Search Snippets', 'insert-headers-and-footers' ), 'wpcode_snippet_search' );
			$this->snippets_table->views();
			$this->snippets_table->display();
			?>

		</form>
		<?php
	}

	/**
	 * Content of the bottom row of the header.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		$add_new_url = admin_url( 'admin.php?page=wpcode-snippet-manager' );
		?>
		<div class="wpcode-column wpcode-title-button">
			<h1><?php esc_html_e( 'All Snippets', 'insert-headers-and-footers' ); ?></h1>
			<a class="wpcode-button" href="<?php echo esc_url( $add_new_url ); ?>">
				<?php esc_html_e( 'Add New', 'insert-headers-and-footers' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Capture screen-specific messages and add notices.
	 *
	 * @return void
	 */
	public function process_message() {

		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! empty( $_GET['trashed'] ) ) {
			$count  = absint( $_GET['trashed'] );
			$notice = sprintf( /* Translators: %d - Trashed snippets count. */
				_n( '%d snippet was successfully moved to Trash.', '%d snippets were successfully moved to Trash.', $count, 'insert-headers-and-footers' ),
				$count
			);
		}

		if ( ! empty( $_GET['untrashed'] ) ) {
			$count  = absint( $_GET['untrashed'] );
			$notice = sprintf( /* translators: %d - Restored from trash snippets count. */
				_n( '%d snippet was successfully restored.', '%d snippet were successfully restored.', $count, 'insert-headers-and-footers' ),
				$count
			);
		}

		if ( ! empty( $_GET['deleted'] ) ) {
			$count  = absint( $_GET['deleted'] );
			$notice = sprintf( /* translators: %d - Deleted snippets count. */
				_n( '%d snippet was successfully permanently deleted.', '%d snippets were successfully permanently deleted.', $count, 'insert-headers-and-footers' ),
				$count
			);
		}

		if ( isset( $_GET['enabled'] ) ) {
			$count  = absint( $_GET['enabled'] );
			$notice = '';
			if ( $count > 0 ) {
				$notice = sprintf( /* translators: %d - Activated snippets count. */
					_n( '%d snippet was successfully activated.', '%d snippets were successfully activated.', $count, 'insert-headers-and-footers' ),
					$count
				);
			}
			if ( isset( $_GET['error'] ) ) {
				$error_count = absint( $_GET['error'] );

				$notice .= ' ';
				$notice .= sprintf( /* translators: %d - Failed to activate snippets count. */
					_n( '%d snippet was not activated due to an error.', '%d snippets were not activated due to errors.', $error_count, 'insert-headers-and-footers' ),
					$error_count
				);
			}
		}

		if ( ! empty( $_GET['disabled'] ) ) {
			$count  = absint( $_GET['disabled'] );
			$notice = sprintf( /* translators: %d - Deactivated snippets count. */
				_n( '%d snippet was successfully deactivated.', '%d snippets were successfully deactivated.', $count, 'insert-headers-and-footers' ),
				$count
			);
		}
		// phpcs:enable WordPress.Security.NonceVerification

		if ( isset( $error_count ) && isset( $notice ) ) {
			$this->set_error_message( $notice );
		} elseif ( isset( $notice ) ) {
			$this->set_success_message( $notice );
		}
	}

	/**
	 * On the deactivated snippets view, show a notice explaining that this view shows the snippets that have been
	 * automatically disabled due to throwing an error and highlight the error logging option, if disabled.
	 *
	 * @return void
	 */
	public function maybe_show_deactivated_notice() {
		if ( ! isset( $_GET['view'] ) || 'deactivated' !== $_GET['view'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		// Let's see if error logging is enabled.
		$logging_enabled = wpcode()->settings->get_option( 'error_logging' );
		$button_text = esc_html__( 'Enable Error Logging', 'insert-headers-and-footers' );
		$button_url  = add_query_arg(
			array(
				'page' => 'wpcode-settings',
			),
			admin_url( 'admin.php' )
		);

		?>
		<div class="info fade notice">
			<p>
				<?php esc_html_e( 'This view lists your snippets that have been automatically disabled due to throwing an error.', 'insert-headers-and-footers' ); ?>
				<a href="<?php echo esc_url( wpcode_utm_url( 'https://wpcode.com/docs/php-error-handling-safe-mode/', 'snippet-deactivated-notice', 'deactivated-list' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Learn More', 'insert-headers-and-footers' ); ?>
				</a>
			</p>
			<?php
			if ( ! $logging_enabled ) {
				?>
				<p>
					<?php esc_html_e( 'In order to get more info about the errors that caused the snippets to be disabled please consider enabling error logging.', 'insert-headers-and-footers' ); ?>
				</p>
				<p>
					<a href="<?php echo esc_url( $button_url ); ?>" class="button button-primary">
						<?php echo esc_html( $button_text ); ?>
					</a>
				</p>
			<?php } ?>
		</div>
		<?php
	}
}
