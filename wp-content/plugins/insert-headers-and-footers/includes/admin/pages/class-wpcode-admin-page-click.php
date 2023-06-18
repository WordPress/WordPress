<?php
/**
 * Admin page for handling 1-click install of public snippets in the WPCode library.
 *
 * @package WPCode
 */

/**
 * Class for the 1-Click admin page.
 */
class WPCode_Admin_Page_Click extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-click';

	/**
	 * The action used for the nonce.
	 *
	 * @var string
	 */
	protected $action = 'wpcode-click';

	/**
	 * The nonce name field.
	 *
	 * @var string
	 */
	protected $nonce_name = 'wpcode-click_nonce';

	/**
	 * Hide this page in the menu.
	 *
	 * @var bool
	 */
	public $hide_menu = true;

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( '1-Click', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Register hook on admin init just for this page.
	 *
	 * @return void
	 */
	public function page_hooks() {
		add_action( 'admin_init', array( $this, 'submit_listener' ) );
	}

	/**
	 * Override to hide default header on this page.
	 *
	 * @return void
	 */
	public function output_header() {

	}

	/**
	 * Wrap this page in a form tag.
	 *
	 * @return void
	 */
	public function output() {
		?>
		<form action="<?php echo esc_url( $this->get_page_action_url() ); ?>" method="post">
			<?php parent::output(); ?>
		</form>
		<?php
	}

	/**
	 * The page output.
	 *
	 * @return void
	 */
	public function output_content() {
		?>
		<div class="wpcode-modal-area">
			<div class="wpcode-modal-header">
				<?php $this->logo_image(); ?>
			</div>
			<?php
			$snippet_hash = get_transient( 'wpcode_deploy_snippet_id' );
			// Let's see if we're in the middle of a fresh installation from the library.
			if ( isset( $_GET['message'] ) && 'wpcode-deploy' === $_GET['message'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ! wpcode()->library_auth->has_auth() ) {
					?>
					<h3><?php esc_html_e( 'Connect your site to the WPCode Library', 'insert-headers-and-footer' ); ?></h3>
					<p><?php esc_html_e( 'You\'re almost there! To finish installing the snippet, you need to connect your site to your account on the WPCode Library. This will allow you to install snippets directly to your site in the future.', 'insert-headers-and-footers' ); ?></p>
					<p><?php esc_html_e( 'You\'ll also get access to tens of free expert-curated snippets that can be installed with 1-click from inside the plugin.', 'insert-headers-and-footers' ); ?></p>
					<div class="wpcode-buttons-row">
						<button class="wpcode-button wpcode-button-large wpcode-start-auth">
							<?php esc_html_e( 'Connect to Library', 'insert-headers-and-footers' ); ?>
						</button>
					</div>
					<?php
				} else {
					// The site is connected to the library but it must mean it's connected to another account in which case they should connect to their own account.
					?>
					<h3><?php esc_html_e( 'Your site is already connected to the  WPCode Library using another account', 'insert-headers-and-footers' ); ?></h3>
					<p>
						<?php esc_html_e( 'In order to continue installing the snippet from the WPCode library you have to either login to the Library with the same account used to connect this site to the WPCode library initially or disconnect this site from the WPCode library and connect using your own account.', 'insert-headers-and-footers' ); ?>
					</p>
					<div class="wpcode-buttons-row">
						<button class="wpcode-button wpcode-delete-auth">
							<?php esc_html_e( 'Disconnect Site From Library', 'insert-headers-and-footers' ); ?>
						</button>
						<a class="wpcode-button" href="<?php echo esc_url( wpcode()->library_auth->library_url ); ?>/account">
							<?php esc_html_e( 'Login with another user on the WPCode Library', 'insert-headers-and-footers' ); ?>
						</a>
					</div>
					<?php
				}
			} elseif ( false !== $snippet_hash && wpcode()->library_auth->has_auth() && ! isset( $_GET['snippet'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				// We have a snippet hash so let's continue the snippet install process.
				$snippet_url = wpcode()->library_auth->library_url . '/snippet/' . $snippet_hash . '/?deploy=1';
				?>
				<h3><?php esc_html_e( 'Congratulations, your site is now connected!', 'insert-headers-and-footers' ); ?></h3>
				<p><?php esc_html_e( 'Your site is now connected to the WPCode Library and you can install snippets directly from the library. Please click the button below to resume installing the snippet you were viewing.', 'insert-headers-and-footers' ); ?></p>
				<div class="wpcode-buttons-row">
					<a href="<?php echo esc_url( $snippet_url ); ?>" class="wpcode-button wpcode-button-large">
						<?php esc_html_e( 'Resume Snippet Installation', 'insert-headers-and-footers' ); ?>
					</a>
				</div>
				<?php
				delete_transient( 'wpcode_deploy_snippet_id' );
			} elseif ( ! wpcode()->library_auth->has_auth() ) { // Let's make sure the site is authenticated with the library.
				?>
				<h3><?php esc_html_e( 'Your site is not connected to the WPCode library.', 'insert-headers-and-footers' ); ?></h3>
				<p><?php esc_html_e( 'Connect now to enable installing public snippets from the WPCode library with 1-click and also get access to tens of expert-curated snippets that you can install from inside the plugin.', 'insert-headers-and-footers' ); ?></p>
				<div class="wpcode-buttons-row">
					<button class="wpcode-button wpcode-button-large wpcode-start-auth">
						<?php esc_html_e( 'Connect to Library', 'insert-headers-and-footers' ); ?>
					</button>
				</div>
				<?php
				// Let's check that we have a snippet hash to load.
			} elseif ( empty( $_GET['snippet'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'No snippet provided.', 'insert-headers-and-footers' ); ?></p>
				</div>
				<?php
				// Let's check that the site is authenticated.
			} elseif ( empty( $_GET['auth'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'Missing authentication token, please click the link in the WPCode Library again.', 'insert-headers-and-footers' ); ?></p>
				</div>
				<?php
			} else {
				// Let's make sure they don't get redirect again if they reached this point.
				delete_transient( 'wpcode_deploy_snippet_id' );
				// Let's attempt to load the snippet data from the library.
				$auth            = sanitize_key( $_GET['auth'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$snippet_hash    = sanitize_key( $_GET['snippet'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$snippet_request = wpcode()->library->get_public_snippet( $snippet_hash, $auth );

				if ( ! isset( $snippet_request['status'] ) || 'success' !== $snippet_request['status'] ) {
					// We have an error. Let's default to a generic message.
					$error = __( 'We encountered an error loading your snippet, please try again in a few minutes', 'insert-headers-and-footers' );
					if ( isset( $snippet_request['message'] ) ) {
						// If there is a message, let's use that instead.
						$error = sprintf(
						/* translators: %s: The error message from the API. */
							__( 'We encountered the following error loading your snippet: %s', 'insert-headers-and-footers' ),
							$snippet_request['message']
						);
					}
					$go_back_url = wpcode()->library_auth->library_url . '/snippet/' . $snippet_hash;

					?>
					<div class="notice notice-error">
						<p><?php echo esc_html( $error ); ?></p>
					</div>
					<div class="wpcode-buttons-row">
						<a href="<?php echo esc_url( $go_back_url ); ?>" class="wpcode-button"><?php esc_html_e( 'Go back to the library', 'insert-headers-and-footers' ); ?></a>
					</div>
					<?php
				} else {
					$snippet       = $snippet_request['data'];
					$snippet_types = wpcode()->execute->get_options();
					// Let's show a preview of the snippet and ask the user to confirm.
					?>
					<h1><?php esc_html_e( 'Library Snippet Preview', 'insert-headers-and-footers' ); ?></h1>
					<p><?php esc_html_e( 'Please review the snippet below and confirm that you would like to install it.', 'insert-headers-and-footers' ); ?></p>
					<h3>
						<?php
						// Translators: %s: The snippet name.
						printf( esc_html__( 'Snippet title: %s', 'insert-headers-and-footers' ), esc_html( $snippet['title'] ) );
						?>
					</h3>
					<div class="wpcode-code-textarea">
						<div class="wpcode-flex">
							<div class="wpcode-column">
								<h3><?php esc_html_e( 'Code preview', 'insert-headers-and-footers' ); ?></h3>
							</div>
							<div class="wpcode-column">
								<div class="wpcode-input-select">
									<label for="wpcode_snippet_type"><?php esc_html_e( 'Code Type', 'insert-headers-and-footers' ); ?></label>
									<select disabled>
										<?php
										foreach ( $snippet_types as $key => $label ) {
											if ( $key !== $snippet['code_type'] ) {
												continue;
											}
											?>
											<option selected><?php echo esc_html( $label ); ?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
						</div>
						<textarea id="wpcode-code-preview"><?php echo esc_textarea( $snippet['code'] ); ?></textarea>
					</div>
					<div class="wpcode-buttons-row">
						<input type="hidden" name="snippet" value="<?php echo esc_attr( $snippet['cloud_id'] ); ?>">
						<input type="hidden" name="auth" value="<?php echo esc_attr( $auth ); ?>">
						<button class="wpcode-button wpcode-button-large"><?php esc_html_e( 'Confirm & Install Snippet', 'insert-headers-and-footers' ); ?></button>
					</div>
					<?php

					$editor = new WPCode_Code_Editor( esc_js( $snippet['code_type'] ) );
					$editor->set_setting( 'readOnly', 'nocursor' );
					$editor->register_editor( 'wpcode-code-preview' );
					$editor->init_editor();
				}

				wp_nonce_field( $this->action, $this->nonce_name );
			}
			?>
		</div>
		<?php
	}

	/**
	 * For this page we output a title and the save button.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		?>
		<div class="wpcode-column">
			<h1><?php esc_html_e( 'Confirm Snippet Installation', 'insert-headers-and-footers' ); ?></h1>
		</div>
		<?php
	}

	/**
	 * If the form is submitted attempt to save the values.
	 *
	 * @return void
	 */
	public function submit_listener() {
		if ( ! isset( $_REQUEST[ $this->nonce_name ] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST[ $this->nonce_name ] ), $this->action ) ) {
			// Nonce is missing, so we're not even going to try.
			return;
		}

		if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
			// Should not be able to load this page at all without that permission but let's check anyway.
			return;
		}

		$snippet_hash = isset( $_REQUEST['snippet'] ) ? sanitize_key( $_REQUEST['snippet'] ) : false;
		$auth         = isset( $_REQUEST['auth'] ) ? sanitize_key( $_REQUEST['auth'] ) : false;
		// Let's load the data reliably again - it's saved in a transient so it should be fast.
		$snippet_data = wpcode()->library->get_public_snippet( $snippet_hash, $auth );

		// You should not be able to submit this form if the hash is invalid but let's check anyway.
		if ( ! isset( $snippet_data['status'] ) || 'success' !== $snippet_data['status'] ) {
			// This should not happen if you did not manually change the hash, so we can just return.
			return;
		}

		// Let's create a new snippet.
		$snippet = new WPCode_Snippet( $snippet_data['data'] );
		// Let's save the snippet.
		if ( $snippet->save() ) {
			// If successfully saved let's redirect to the edit screen.
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'       => 'wpcode-snippet-manager',
						'snippet_id' => $snippet->get_id(),
					),
					admin_url( 'admin.php' )
				)
			);
			exit;
		}
	}
}
