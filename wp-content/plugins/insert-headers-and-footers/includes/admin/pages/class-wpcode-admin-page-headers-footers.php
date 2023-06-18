<?php
/**
 * Headers & Footers admin page.
 *
 * @package WPCode
 */

/**
 * Class for the headers & footers admin page.
 */
class WPCode_Admin_Page_Headers_Footers extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-headers-footers';

	/**
	 * The action used for the nonce.
	 *
	 * @var string
	 */
	private $action = 'insert-headers-and-footers';

	/**
	 * If the page should be a submenu of Settings instead of wpcode.
	 *
	 * @var bool
	 */
	private $settings_submenu = false;

	/**
	 * The nonce name field.
	 *
	 * @var string
	 */
	private $nonce_name = 'insert-headers-and-footers_nonce';

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
			$this->settings_submenu = true;
		}
		$this->page_title = __( 'Header & Footer', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Add the submenu page.
	 *
	 * @return void
	 */
	public function add_page() {
		if ( $this->settings_submenu ) {
			add_options_page(
				$this->menu_title,
				$this->page_title,
				'wpcode_edit_snippets',
				$this->page_slug,
				array(
					wpcode()->admin_page_loader,
					'admin_menu_page',
				)
			);

			return;
		}
		parent::add_page();
	}

	/**
	 * Register hook on admin init just for this page.
	 *
	 * @return void
	 */
	public function page_hooks() {
		$this->can_edit = current_user_can( 'unfiltered_html' );
		add_action( 'admin_init', array( $this, 'submit_listener' ) );
		$this->process_message();
	}

	/**
	 * Process messages specific to this page.
	 *
	 * @return void
	 */
	public function process_message() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! isset( $_GET['message'] ) ) {
			return;
		}

		$messages = array(
			1 => __( 'Headers & Footers mode activated. Use the toggle next to the Save Changes button to disable it at any time.', 'insert-headers-and-footers' ),
			2 => __( 'Headers & Footers mode deactivated, if you wish to switch back please use the option on the settings page.', 'insert-headers-and-footers' ),
		);
		$message  = absint( $_GET['message'] );
		// phpcs:enable WordPress.Security.NonceVerification

		if ( ! isset( $messages[ $message ] ) ) {
			return;
		}
		$this->set_success_message( $messages[ $message ] );
	}

	/**
	 * Wrap this page in a form tag.
	 *
	 * @return void
	 */
	public function output() {
		if ( ! $this->can_edit ) {
			$this->set_error_message( __( 'Sorry, you only have read-only access to this page. Ask your administrator for assistance editing.', 'insert-headers-and-footers' ) );
			$headers_footers_mode = wpcode()->settings->get_option( 'headers_footers_mode' );
			// If in headers & footers mode allow them to update to disable the simple mode.
			if ( ! $headers_footers_mode ) {
				// If the user can't edit the values just don't load form at all.
				parent::output();

				return;
			}
		}
		?>
		<form action="<?php echo esc_url( $this->get_page_action_url() ); ?>" method="post">
			<?php parent::output(); ?>
		</form>
		<?php
	}

	/**
	 * The headers & footers page output.
	 *
	 * @return void
	 */
	public function output_content() {

		$header_desc = sprintf(
		/* translators: %s: The `<head>` tag */
			esc_html__( 'These scripts will be printed in the %s section.', 'insert-headers-and-footers' ),
			'<code>&lt;head&gt;</code>'
		);
		$body_desc = sprintf(
		/* translators: %s: The `<head>` tag */
			esc_html__( 'These scripts will be printed just below the opening %s tag.', 'insert-headers-and-footers' ),
			'<code>&lt;body&gt;</code>'
		);
		$footer_desc = sprintf(
		/* translators: %s: The `</body>` tag */
			esc_html__( 'These scripts will be printed above the closing %s tag.', 'insert-headers-and-footers' ),
			'<code>&lt;/body&gt;</code>'
		);
		$this->textarea_field( 'ihaf_insert_header', __( 'Header', 'insert-headers-and-footers' ), $header_desc );
		if ( $this->body_supported() ) {
			$this->textarea_field( 'ihaf_insert_body', __( 'Body', 'insert-headers-and-footers' ), $body_desc );
		}
		$this->textarea_field( 'ihaf_insert_footer', __( 'Footer', 'insert-headers-and-footers' ), $footer_desc );
		wp_nonce_field( $this->action, $this->nonce_name );
	}

	/**
	 * Check if the website supports wp_body_open.
	 *
	 * @return bool
	 */
	public function body_supported() {
		return function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2', '>=' );
	}

	/**
	 * Standard output for a code input field.
	 *
	 * @param string $option The option name as stored in the DB.
	 * @param string $title The title of the input (also used as label).
	 * @param string $desc The description that shows up under the field.
	 *
	 * @return void
	 */
	public function textarea_field( $option, $title, $desc ) {
		$value = esc_html( wp_unslash( get_option( $option ) ) );
		?>
		<div class="wpcode-code-textarea">
			<h2><label for="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $title ); ?></label></h2>
			<textarea name="<?php echo esc_attr( $option ); ?>" id="<?php echo esc_attr( $option ); ?>" class="widefat" rows="8" <?php disabled( ! current_user_can( 'unfiltered_html' ) ); ?>><?php echo $value; ?></textarea>
			<p>
				<?php echo wp_kses( $desc, array( 'code' => array() ) ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * For this page we output a title and the save button.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		$headers_footers_mode = wpcode()->settings->get_option( 'headers_footers_mode' );
		$button_disabled      = ! $this->can_edit && ! $headers_footers_mode ? 'disabled' : '';
		?>
		<div class="wpcode-column">
			<h1><?php esc_html_e( 'Global Header and Footer', 'insert-headers-and-footers' ); ?></h1>
		</div>
		<div class="wpcode-column">
			<?php $this->get_submenu_toggle(); ?>
			<button class="wpcode-button" type="submit" <?php echo esc_attr( $button_disabled ); ?>>
				<?php esc_html_e( 'Save Changes', 'insert-headers-and-footers' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Get the toggle to disable submenu mode.
	 *
	 * @return void
	 */
	public function get_submenu_toggle() {
		if ( ! $this->settings_submenu ) {
			return;
		}

		?>
		<div>
			<label for="headers_footers_mode" class="wpcode-status-text"><?php esc_html_e( 'Simple mode', 'insert-headers-and-footers' ); ?></label>
			<?php echo $this->get_checkbox_toggle( true, 'headers_footers_mode' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
	}

	/**
	 * Page specific scripts. Hooked to 'admin_enqueue_scripts'.
	 *
	 * @return void
	 */
	public function page_scripts() {
		$editor = new WPCode_Code_Editor();

		$editor->register_editor( 'ihaf_insert_header' );
		$editor->register_editor( 'ihaf_insert_footer' );
		if ( $this->body_supported() ) {
			$editor->register_editor( 'ihaf_insert_body' );
		}
		$editor->init_editor();
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

		if ( $this->can_edit && isset( $_REQUEST['ihaf_insert_header'] ) && isset( $_REQUEST['ihaf_insert_footer'] ) ) {
			// If they are not allowed to edit the page these should not be processed but we still allow them to save to disable the simple mode.
			update_option( 'ihaf_insert_header', $_REQUEST['ihaf_insert_header'] );
			update_option( 'ihaf_insert_footer', $_REQUEST['ihaf_insert_footer'] );
			update_option( 'ihaf_insert_body', isset( $_REQUEST['ihaf_insert_body'] ) ? $_REQUEST['ihaf_insert_body'] : '' );
		}

		if ( wpcode()->settings->get_option( 'headers_footers_mode' ) && ! isset( $_REQUEST['headers_footers_mode'] ) ) {
			wpcode()->settings->update_option( 'headers_footers_mode', false );
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => $this->page_slug,
						'message' => 2,
					),
					admin_url( 'admin.php' )
				)
			);
			exit;
		}

		$this->set_success_message( __( 'Settings Saved.', 'insert-headers-and-footers' ) );
	}

	/**
	 * Use a different base url when the headers_footers_mode is enabled.
	 *
	 * @return string
	 */
	public function get_page_action_url() {
		$url = parent::get_page_action_url();

		if ( ! wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
			return $url;
		}

		return str_replace( 'admin.php', 'options-general.php', $url );
	}
}
