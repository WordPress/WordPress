<?php
/**
 * Settings admin page.
 *
 * @package WPCode
 */

/**
 * Class for the Settings admin page.
 */
class WPCode_Admin_Page_Settings extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-settings';

	/**
	 * The action used for the nonce.
	 *
	 * @var string
	 */
	protected $action = 'wpcode-settings';

	/**
	 * The nonce name field.
	 *
	 * @var string
	 */
	protected $nonce_name = 'wpcode-settings_nonce';

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Settings', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Register hook on admin init just for this page.
	 *
	 * @return void
	 */
	public function page_hooks() {
		add_action( 'admin_init', array( $this, 'submit_listener' ) );
		add_filter( 'wpcode_admin_js_data', array( $this, 'add_connect_strings' ) );
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
	 * The Settings page output.
	 *
	 * @return void
	 */
	public function output_content() {
		$header_and_footers = wpcode()->settings->get_option( 'headers_footers_mode' );
		$usage_tracking     = wpcode()->settings->get_option( 'usage_tracking' );

		$description = __( 'This allows you to disable all Code Snippets functionality and have a single "Headers & Footers" item under the settings menu.', 'insert-headers-and-footers' );

		$description .= '<br />';
		$description .= sprintf(
		// Translators: Placeholders make the text bold.
			__( '%1$sNOTE:%2$s Please use this setting with caution. It will disable all custom snippets that you add using the new snippet management interface.', 'insert-headers-and-footers' ),
			'<strong>',
			'</strong>'
		);

		$this->metabox_row(
			__( 'License Key', 'insert-headers-and-footers' ),
			$this->get_license_key_input()
		);

		$this->metabox_row(
			__( 'Headers & Footers mode', 'insert-headers-and-footers' ),
			$this->get_checkbox_toggle(
				$header_and_footers,
				'headers_footers_mode',
				$description
			),
			'headers_footers_mode'
		);

		$this->common_settings();

		$this->metabox_row(
			__( 'Allow Usage Tracking', 'insert-headers-and-footers' ),
			$this->get_checkbox_toggle(
				$usage_tracking,
				'usage_tracking',
				esc_html__( 'By allowing us to track usage data, we can better help you, as we will know which WordPress configurations, themes, and plugins we should test.', 'insert-headers-and-footers' )
			),
			'usage_tracking'
		);

		wp_nonce_field( $this->action, $this->nonce_name );
	}

	/**
	 * Move common settings to a separate method so we can use it in other versions of this page.
	 *
	 * @return void
	 */
	public function common_settings() {
		$this->metabox_row(
			__( 'WPCode Library Connection', 'insert-headers-and-footers' ),
			$this->get_library_connection_input()
		);

		$this->metabox_row(
			__( 'Editor Height', 'insert-headers-and-footers' ),
			$this->get_editor_height_input(),
			'wpcode-editor-height'
		);

		$this->metabox_row(
			__( 'Error Logging', 'insert-headers-and-footers' ),
			$this->get_checkbox_toggle(
				wpcode()->settings->get_option( 'error_logging' ),
				'wpcode-error-logging',
				sprintf(
				// Translators: %1$s: opening anchor tag, %2$s: closing anchor tag.
					esc_html__( 'Log errors thrown by snippets? %1$sView Logs%2$s', 'insert-headers-and-footers' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=wpcode-tools&view=logs' ) ) . '">',
					'</a>'
				),
				1
			),
			'wpcode-error-logging'
		);
	}

	/**
	 * Get an input to connect or disconnect from the snippet library.
	 *
	 * @return string
	 */
	public function get_library_connection_input() {
		$button_classes = array(
			'wpcode-button',
		);
		$button_text    = __( 'Connect to the WPCode Library', 'insert-headers-and-footers' );
		if ( WPCode()->library_auth->has_auth() ) {
			$button_classes[] = 'wpcode-delete-auth';
			$button_text      = __( 'Disconnect from the WPCode Library', 'insert-headers-and-footers' );
		} else {
			$button_classes[] = 'wpcode-start-auth';
		}

		return sprintf(
			'<button type="button" class="%1$s">%2$s</button>',
			esc_attr( implode( ' ', $button_classes ) ),
			esc_html( $button_text )
		);
	}

	/**
	 * For this page we output a title and the save button.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		?>
		<div class="wpcode-column">
			<h1><?php esc_html_e( 'Settings', 'insert-headers-and-footers' ); ?></h1>
		</div>
		<div class="wpcode-column">
			<button class="wpcode-button" type="submit">
				<?php esc_html_e( 'Save Changes', 'insert-headers-and-footers' ); ?>
			</button>
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

		$settings = array(
			'headers_footers_mode' => isset( $_POST['headers_footers_mode'] ),
			'editor_height_auto'   => isset( $_POST['editor_height_auto'] ),
			'editor_height'        => isset( $_POST['editor_height'] ) ? absint( $_POST['editor_height'] ) : 300,
			'error_logging'        => isset( $_POST['wpcode-error-logging'] ),
			'usage_tracking'       => isset( $_POST['usage_tracking'] ),
		);

		wpcode()->settings->bulk_update_options( $settings );

		if ( true === $settings['headers_footers_mode'] ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => 'wpcode-headers-footers',
						'message' => 1,
					),
					admin_url( 'options-general.php' )
				)
			);
			exit;
		}

		$this->set_success_message( __( 'Settings Saved.', 'insert-headers-and-footers' ) );
	}

	/**
	 * Allow users to change the code editor height.
	 *
	 * @return string
	 */
	public function get_editor_height_input() {
		$editor_auto_height = boolval( wpcode()->settings->get_option( 'editor_height_auto' ) );
		$editor_height      = wpcode()->settings->get_option( 'editor_height', 300 );

		$html = sprintf(
			'<input type="number" min="100" value="%1$d" id="wpcode-editor-height" name="editor_height" %2$s />',
			absint( $editor_height ),
			disabled( $editor_auto_height, true, false )
		);

		$html .= $this->get_checkbox_toggle(
			$editor_auto_height,
			'editor_height_auto'
		);
		$html .= '<label for="editor_height_auto">' . __( 'Auto height', 'insert-headers-and-footers' ) . '</label>';

		$html .= '<p class="description">';
		$html .= esc_html__( 'Set the editor height in pixels or enable auto-grow so the code editor automatically grows in height with the code.', 'insert-headers-and-footers' );
		$html .= '</p>';

		return $html;
	}

	/**
	 * Get the license key input.
	 *
	 * @return string
	 */
	public function get_license_key_input() {
		ob_start();
		?>
		<div class="wpcode-metabox-form">
			<p><?php esc_html_e( 'You\'re using WPCode Lite - no license needed. Enjoy!', 'insert-headers-and-footers' ); ?>
				<img draggable="false" role="img" class="emoji" alt="🙂" src="https://s.w.org/images/core/emoji/14.0.0/svg/1f642.svg">
			</p>
			<p>
				<?php
				printf(
				// Translators: %1$s - Opening anchor tag, do not translate. %2$s - Closing anchor tag, do not translate.
					esc_html__( 'To unlock more features consider %1$supgrading to PRO%2$s.', 'insert-headers-and-footers' ),
					'<strong><a href="' . esc_url( wpcode_utm_url( 'https://wpcode.com/lite/', 'settings-license', 'upgrading-to-pro' ) ) . '" target="_blank" rel="noopener noreferrer">',
					'</a></strong>'
				)
				?>
			</p>
			<hr>
			<p><?php esc_html_e( 'Already purchased? Simply enter your license key below to enable WPCode PRO!', 'insert-headers-and-footers' ); ?></p>
			<p>
				<input type="password" class="wpcode-input-text" id="wpcode-settings-upgrade-license-key" placeholder="<?php esc_attr_e( 'Paste license key here', 'insert-headers-and-footers' ); ?>" value="">
				<button type="button" class="wpcode-button" id="wpcode-settings-connect-btn">
					<?php esc_html_e( 'Verify Key', 'insert-headers-and-footers' ); ?>
				</button>
			</p>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Add the strings for the connect page to the JS object.
	 *
	 * @param array $data The localized data we already have.
	 *
	 * @return array
	 */
	public function add_connect_strings( $data ) {
		$data['oops']                = esc_html__( 'Oops!', 'insert-headers-and-footers' );
		$data['ok']                  = esc_html__( 'OK', 'insert-headers-and-footers' );
		$data['almost_done']         = esc_html__( 'Almost Done', 'insert-headers-and-footers' );
		$data['plugin_activate_btn'] = esc_html__( 'Activate', 'insert-headers-and-footers' );
		$data['server_error']        = esc_html__( 'Unfortunately there was a server connection error.', 'insert-headers-and-footers' );

		return $data;
	}
}
