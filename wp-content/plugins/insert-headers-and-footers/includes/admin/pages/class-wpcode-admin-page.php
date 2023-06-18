<?php
/**
 * Admin pages abstract class.
 *
 * @package WPCode
 */

/**
 * Class Admin_Page
 */
abstract class WPCode_Admin_Page {

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	public $page_slug = '';

	/**
	 * The page title.
	 *
	 * @var string
	 */
	public $page_title = '';

	/**
	 * The menu title, defaults to the page title.
	 *
	 * @var string
	 */
	public $menu_title;

	/**
	 * If there's an error message, let's store it here.
	 *
	 * @var string
	 */
	public $message_error;

	/**
	 * If there's a success message, store it here.
	 *
	 * @var string
	 */
	public $message_success;
	/**
	 * The code type to be used by CodeMirror.
	 *
	 * @var string
	 */
	public $code_type = 'html';
	/**
	 * Whether the current user can edit the code on the current page.
	 *
	 * @var bool
	 */
	protected $can_edit = false;
	/**
	 * If true, the snippet library is shown, otherwise, we display
	 * the snippet editor.
	 *
	 * @var bool
	 */
	protected $show_library = false;

	/**
	 * The current view.
	 *
	 * @var string
	 */
	public $view = '';

	/**
	 * The available views for this page.
	 *
	 * @var array
	 */
	public $views = array();

	/**
	 * If the submenu for the page should be hidden, set this to true.
	 *
	 * @var bool
	 */
	public $hide_menu = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! isset( $this->menu_title ) ) {
			$this->menu_title = $this->page_title;
		}

		$this->hooks();
	}

	/**
	 * Add hooks to register the page and output content.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		// Only load if we are actually on the desired page.
		if ( $this->page_slug !== $page ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
		add_action( 'wpcode_admin_page', array( $this, 'output' ) );
		add_action( 'wpcode_admin_page', array( $this, 'output_footer' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'page_scripts' ) );
		add_filter( 'admin_body_class', array( $this, 'page_specific_body_class' ) );
		add_filter( 'wpcode_admin_js_data', array( $this, 'maybe_add_library_data' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_to_click' ) );

		$this->setup_views();
		$this->set_current_view();
		$this->page_hooks();
	}

	/**
	 * Override in child class to define page-specific hooks that will run only
	 * after checks have been passed.
	 *
	 * @return void
	 */
	public function page_hooks() {

	}

	/**
	 * Add the submenu page.
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			'wpcode',
			$this->page_title,
			$this->menu_title,
			'wpcode_edit_snippets',
			$this->page_slug,
			array(
				wpcode()->admin_page_loader,
				'admin_menu_page',
			)
		);
	}

	/**
	 * If the page has views, this is where you should assign them to $this->views.
	 *
	 * @return void
	 */
	protected function setup_views() {

	}

	/**
	 * Set the current view from the query param also checking it's a registered view for this page.
	 *
	 * @return void
	 */
	protected function set_current_view() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['view'] ) ) {
			return;
		}
		$view = sanitize_text_field( wp_unslash( $_GET['view'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( array_key_exists( $view, $this->views ) ) {
			$this->view = $view;
		}
	}

	/**
	 * Output the page content.
	 *
	 * @return void
	 */
	public function output() {
		$this->output_header();
		?>
		<div class="wpcode-content">
			<?php
			$this->output_content();
			do_action( "wpcode_admin_page_content_{$this->page_slug}", $this );
			?>
		</div>
		<?php
	}

	/**
	 * Output of the header markup for admin pages.
	 *
	 * @return void
	 */
	public function output_header() {
		?>
		<div class="wpcode-header">
			<div class="wpcode-header-top">
				<div class="wpcode-header-left">
					<?php $this->output_header_left(); ?>
				</div>
				<div class="wpcode-header-right">
					<?php $this->output_header_right(); ?>
				</div>
			</div>
			<div class="wpcode-header-bottom">
				<?php $this->output_header_bottom(); ?>
			</div>
		</div>
		<?php $this->maybe_output_message(); ?>
		<?php
	}

	/**
	 * Output footer markup, mostly used for overlays that are fixed.
	 *
	 * @return void
	 */
	public function output_footer() {
		?>
		<div class="wpcode-modal-overlay"></div>
		<div class="wpcode-notifications-overlay"></div>
		<div class="wpcode-docs-overlay" id="wpcode-docs-overlay">
			<?php $this->logo_image( 'wpcode-help-logo' ); ?>
			<button id="wpcode-help-close" class="wpcode-button-just-icon" type="button">
				<?php wpcode_icon( 'close', 19, 19 ); ?>
			</button>
			<div class="wpcode-docs-content">
				<div id="wpcode-help-search" class="wpcode-search-empty">
					<label>
						<span class="screen-reader-text"><?php esc_html_e( 'Search docs', 'insert-headers-and-footers' ); ?></span>
						<?php wpcode_icon( 'search' ); ?>
						<input type="text" class="wpcode-input-text"/>
					</label>
					<div id="wpcode-help-search-clear" title="<?php esc_attr_e( 'Clear', 'insert-headers-and-footers' ); ?>">
						<?php wpcode_icon( 'close', 14, 14 ); ?>
					</div>
				</div>
				<div id="wpcode-help-no-result" style="display: none;">
					<ul class="wpcode-help-docs">
						<li>
							<span><?php esc_html_e( 'No docs found', 'insert-headers-and-footers' ); ?></span>
						</li>
					</ul>
				</div>
				<div id="wpcode-help-result">
					<ul class="wpcode-help-docs"></ul>
				</div>
				<?php
				$docs = new WPCode_Docs();
				$docs->get_categories_accordion();
				$support_url = wpcode_utm_url( 'https://wpcode.com/contact/', 'help-overlay', 'support-url' );
				?>
				<div class="wpcode-help-footer">
					<div class="wpcode-help-footer-box">
						<?php wpcode_icon( 'file', 48, 48 ); ?>
						<h3><?php esc_html_e( 'View Documentation', 'insert-headers-and-footers' ); ?></h3>
						<p><?php esc_html_e( 'Browse documentation, reference material, and tutorials for WPCode.', 'insert-headers-and-footers' ); ?></p>
						<a class="wpcode-button wpcode-button-secondary" href="<?php echo esc_url( wpcode_utm_url( 'https://wpcode.com/docs/', 'help-overlay', 'docs', 'footer' ) ); ?>" target="_blank"><?php esc_html_e( 'View All Documentation', 'insert-headers-and-footers' ); ?></a>
					</div>
					<div class="wpcode-help-footer-box">
						<?php wpcode_icon( 'support', 48, 48 ); ?>
						<h3><?php esc_html_e( 'Get Support', 'insert-headers-and-footers' ); ?></h3>
						<p><?php esc_html_e( 'Submit a ticket and our world class support team will be in touch soon.', 'insert-headers-and-footers' ); ?></p>
						<a class="wpcode-button wpcode-button-secondary" href="<?php echo esc_url( $support_url ); ?>" target="_blank"><?php esc_html_e( 'Submit a Support Ticket', 'insert-headers-and-footers' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="wpcode-notifications-drawer" id="wpcode-notifications-drawer">
			<div class="wpcode-notifications-header">
				<h3 id="wpcode-active-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of active notifications.
							__( 'New Notifications (%s)', 'insert-headers-and-footers' )
						),
						'<span id="wpcode-notifications-count">' . absint( wpcode()->notifications->get_count() ) . '</span>'
					);
					?>
				</h3>
				<h3 id="wpcode-dismissed-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of dismissed notifications.
							__( 'Notifications (%s)', 'insert-headers-and-footers' )
						),
						'<span id="wpcode-notifications-dismissed-count">' . absint( wpcode()->notifications->get_dismissed_count() ) . '</span>'
					);
					?>
				</h3>
				<button type="button" class="wpcode-button-text" id="wpcode-notifications-show-dismissed">
					<?php esc_html_e( 'Dismissed Notifications', 'insert-headers-and-footers' ); ?>
				</button>
				<button type="button" class="wpcode-button-text" id="wpcode-notifications-show-active">
					<?php esc_html_e( 'Active Notifications', 'insert-headers-and-footers' ); ?>
				</button>
				<button type="button" class="wpcode-just-icon-button wpcode-notifications-close"><?php wpcode_icon( 'close', 12, 12, '0 0 16 16' ); ?></button>
			</div>
			<div class="wpcode-notifications-list">
				<ul class="wpcode-notifications-active">
					<?php
					$notifications = wpcode()->notifications->get_active_notifications();
					foreach ( $notifications as $notification ) {
						$this->get_notification_markup( $notification );
					}
					?>
				</ul>
				<ul class="wpcode-notifications-dismissed">
					<?php
					$notifications = wpcode()->notifications->get_dismissed_notifications();
					foreach ( $notifications as $notification ) {
						$this->get_notification_markup( $notification );
					}
					?>
				</ul>
			</div>
			<div class="wpcode-notifications-footer">
				<button type="button" class="wpcode-button-text wpcode-notification-dismiss" id="wpcode-dismiss-all" data-id="all"><?php esc_html_e( 'Dismiss all', 'insert-headers-and-footers' ); ?></button>
			</div>
		</div>
		<span class="wpcode-loading-spinner" id="wpcode-admin-spinner"></span>
		<?php
	}

	/**
	 * Get the notification HTML markup for displaying in a list.
	 *
	 * @param array $notification The notification array.
	 *
	 * @return void
	 */
	public function get_notification_markup( $notification ) {
		$type = ! empty( $notification['icon'] ) ? $notification['icon'] : 'info';
		?>
		<li>
			<div class="wpcode-notification-icon"><?php wpcode_icon( $type, 18, 18 ); ?></div>
			<div class="wpcode-notification-content">
				<h4><?php echo esc_html( $notification['title'] ); ?></h4>
				<p><?php echo wp_kses_post( $notification['content'] ); ?></p>
				<p class="wpcode-start"><?php echo esc_html( $notification['start'] ); ?></p>
				<div class="wpcode-notification-actions">
					<?php
					$main_button = ! empty( $notification['btns']['main'] ) ? $notification['btns']['main'] : false;
					$alt_button  = ! empty( $notification['btns']['alt'] ) ? $notification['btns']['alt'] : false;
					if ( $main_button ) {
						?>
						<a href="<?php echo esc_url( $main_button['url'] ); ?>" class="wpcode-button wpcode-button-small" target="_blank">
							<?php echo esc_html( $main_button['text'] ); ?>
						</a>
						<?php
					}
					if ( $alt_button ) {
						?>
						<a href="<?php echo esc_url( $alt_button['url'] ); ?>" class="wpcode-button wpcode-button-secondary wpcode-button-small" target="_blank">
							<?php echo esc_html( $alt_button['text'] ); ?>
						</a>
						<?php
					}
					?>
					<button type="button" class="wpcode-button-text wpcode-notification-dismiss" data-id="<?php echo esc_attr( $notification['id'] ); ?>"><?php esc_html_e( 'Dismiss', 'insert-headers-and-footers' ); ?></button>
				</div>
			</div>
		</li>
		<?php
	}

	/**
	 * Left side of the header, usually just the logo in this area.
	 *
	 * @return void
	 */
	public function output_header_left() {
		$this->logo_image();
	}

	/**
	 * Logo image.
	 *
	 * @param string $id Id of the image.
	 *
	 * @return void
	 */
	public function logo_image( $id = 'wpcode-header-logo' ) {
		$logo_src = WPCODE_PLUGIN_URL . 'admin/images/wpcode-logo.png';
		// Translators: This simply adds the plugin name before the logo text.
		$alt = sprintf( __( '%s logo', 'insert-headers-and-footers' ), 'WPCode' )
		?>
		<img src="<?php echo esc_url( $logo_src ); ?>" width="132" alt="<?php echo esc_attr( $alt ); ?>" id="<?php echo esc_attr( $id ); ?>"/>
		<?php
	}

	/**
	 * Top right area of the header, by default the notifications and help icons.
	 *
	 * @return void
	 */
	public function output_header_right() {
		$notifications_count = wpcode()->notifications->get_count();
		$dismissed_count     = wpcode()->notifications->get_dismissed_count();
		$data_count          = '';
		if ( $notifications_count > 0 ) {
			$data_count = sprintf(
				'data-count="%d"',
				absint( $notifications_count )
			);
		}
		?>
		<button
				type="button"
				id="wpcode-notifications-button"
				class="wpcode-button-just-icon wpcode-notifications-inbox wpcode-open-notifications"
				data-dismissed="<?php echo esc_attr( $dismissed_count ); ?>"
			<?php echo $data_count; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php wpcode_icon( 'inbox', 15, 16 ); ?>
		</button>
		<button class="wpcode-text-button-icon wpcode-show-help" type="button">
			<?php wpcode_icon( 'help', 21 ); ?>
			<?php esc_html_e( 'Help', 'insert-headers-and-footers' ); ?>
		</button>
		<?php
	}

	/**
	 * This is the menu area but on some pages it's just at title.
	 * Tabs could also be used here.
	 *
	 * @return void
	 */
	public function output_header_bottom() {

	}

	/**
	 * Checks if an error or success message is available and outputs using the specific format.
	 *
	 * @return void
	 */
	public function maybe_output_message() {
		$error_message   = $this->get_error_message();
		$success_message = $this->get_success_message();
		?>
		<div class="wrap" id="wpcode-notice-area">
			<?php
			if ( $error_message ) {
				?>
				<div class="error fade notice is-dismissible">
					<p><?php echo wp_kses_post( $error_message ); ?></p>
				</div>
				<?php
			}
			if ( $success_message ) {
				?>
				<div class="updated fade notice is-dismissible">
					<p><?php echo wp_kses_post( $success_message ); ?></p>
				</div>
				<?php
			}
			do_action( 'wpcode_admin_notices' );
			?>
		</div>
		<?php
	}

	/**
	 * If no message is set return false otherwise return the message string.
	 *
	 * @return false|string
	 */
	public function get_error_message() {
		return ! empty( $this->message_error ) ? $this->message_error : false;
	}

	/**
	 * If no message is set return false otherwise return the message string.
	 *
	 * @return false|string
	 */
	public function get_success_message() {
		return ! empty( $this->message_success ) ? $this->message_success : false;
	}

	/**
	 * This is the main page content and you can't get away without it.
	 *
	 * @return void
	 */
	abstract public function output_content();

	/**
	 * If you need to page-specific scripts override this function.
	 * Hooked to 'admin_enqueue_scripts'.
	 *
	 * @return void
	 */
	public function page_scripts() {
	}

	/**
	 * Set a success message to display it in the appropriate place.
	 * Let's use a function so if we decide to display multiple messages in the
	 * same instance it's easy to change the variable to an array.
	 *
	 * @param string $message The message to store as success message.
	 *
	 * @return void
	 */
	public function set_success_message( $message ) {
		$this->message_success = $message;
	}

	/**
	 * Set an error message to display it in the appropriate place.
	 * Let's use a function so if we decide to display multiple messages in the
	 * same instance it's easy to change the variable to an array.
	 *
	 * @param string $message The message to store as error message.
	 *
	 * @return void
	 */
	public function set_error_message( $message ) {
		$this->message_error = $message;
	}

	/**
	 * Add a page-specific body class using the page slug variable..
	 *
	 * @param string $body_class The body class to append.
	 *
	 * @return string
	 */
	public function page_specific_body_class( $body_class ) {

		$body_class .= ' ' . $this->page_slug;

		return $body_class;
	}

	/**
	 * Get the page url to be used in a form action.
	 *
	 * @return string
	 */
	public function get_page_action_url() {
		$args = array(
			'page' => $this->page_slug,
		);
		if ( ! empty( $this->view ) ) {
			$args['view'] = $this->view;
		}

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Metabox-style layout for admin pages.
	 *
	 * @param string $title The metabox title.
	 * @param string $content The metabox content.
	 * @param string $help The helper text (optional) - if set, a help icon will show up next to the title.
	 *
	 * @return void
	 */
	public function metabox( $title, $content, $help = '' ) {
		// translators: %s is the title of the metabox.
		$button_title = sprintf( __( 'Collapse Metabox %s', 'insert-headers-and-footers' ), $title )
		?>
		<div class="wpcode-metabox">
			<div class="wpcode-metabox-title">
				<div class="wpcode-metabox-title-text">
					<?php echo esc_html( $title ); ?>
					<?php $this->help_icon( $help ); ?>
				</div>
				<div class="wpcode-metabox-title-toggle">
					<button class="wpcode-metabox-button-toggle" type="button" title="<?php echo esc_attr( $button_title ); ?>">
						<svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.41 7.70508L6 3.12508L10.59 7.70508L12 6.29508L6 0.295079L-1.23266e-07 6.29508L1.41 7.70508Z" fill="#454545"/>
						</svg>
					</button>
				</div>
			</div>
			<div class="wpcode-metabox-content">
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Output a help icon with the text passed to it.
	 *
	 * @param string $text The tooltip text.
	 * @param bool   $echo Whether to echo or return the output.
	 *
	 * @return void|string
	 */
	public function help_icon( $text = '', $echo = true ) {
		if ( empty( $text ) ) {
			return;
		}
		if ( ! $echo ) {
			ob_start();
		}
		?>
		<span class="wpcode-help-tooltip">
			<?php wpcode_icon( 'help', 16, 16, '0 0 20 20' ); ?>
			<span class="wpcode-help-tooltip-text"><?php echo wp_kses_post( $text ); ?></span>
		</span>
		<?php
		if ( ! $echo ) {
			return ob_get_clean();
		}
	}

	/**
	 * Get a WPCode metabox row.
	 *
	 * @param string $label The label of the field.
	 * @param string $input The field input (html).
	 * @param string $id The id for the row.
	 * @param string $show_if_id Conditional logic id, automatically hide if the value of the field with this id doesn't match show if value.
	 * @param string $show_if_value Value(s) to match against, can be comma-separated string for multiple values.
	 * @param string $description Description to show under the input.
	 * @param bool   $is_pro Whether this is a pro feature and the pro indicator should be shown next to the label.
	 *
	 * @return void
	 */
	public function metabox_row( $label, $input, $id = '', $show_if_id = '', $show_if_value = '', $description = '', $is_pro = false ) {
		$show_if_rules = '';
		if ( ! empty( $show_if_id ) ) {
			$show_if_rules = sprintf( 'data-show-if-id="%1$s" data-show-if-value="%2$s"', $show_if_id, $show_if_value );
		}
		?>
		<div class="wpcode-metabox-form-row" <?php echo $show_if_rules; ?>>
			<div class="wpcode-metabox-form-row-label">
				<label for="<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $label ); ?>
					<?php
					if ( $is_pro ) {
						echo '<span class="wpcode-pro-pill">PRO</span>';
					}
					?>
				</label>
			</div>
			<div class="wpcode-metabox-form-row-input">
				<?php echo $input; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( ! empty( $description ) ) { ?>
					<p><?php echo wp_kses_post( $description ); ?></p>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get a checkbox wrapped with markup to be displayed as a toggle.
	 *
	 * @param bool       $checked Is it checked or not.
	 * @param string     $name The name for the input.
	 * @param string     $description Field description (optional).
	 * @param string|int $value Field value (optional).
	 * @param string     $label Field label (optional).
	 *
	 * @return string
	 */
	public function get_checkbox_toggle( $checked, $name, $description = '', $value = '', $label = '' ) {
		return wpcode_get_checkbox_toggle( $checked, $name, $description, $value, $label );
	}

	/**
	 * Build the markup for the snippet item. Also used as a template for the js.
	 *
	 * @param array  $snippet The snippet object.
	 * @param string $category The active category to display by default.
	 *
	 * @return void
	 */
	public function get_library_snippet_item( $snippet = array(), $category = '*' ) {
		$title                 = '';
		$url                   = '';
		$description           = '';
		$used_library_snippets = wpcode()->library->get_used_library_snippets();
		$button_text           = __( 'Use snippet', 'insert-headers-and-footers' );
		$pill_text             = '';
		$pill_class            = 'blue';
		if ( ! empty( $snippet ) ) {
			$url = add_query_arg(
				array(
					'page'   => 'wpcode-snippet-manager',
					'custom' => true,
				),
				admin_url( 'admin.php' )
			);
			if ( 0 !== $snippet['library_id'] ) {
				if ( ! empty( $used_library_snippets[ $snippet['library_id'] ] ) ) {
					$url         = wpcode()->library->get_edit_snippet_url( $used_library_snippets[ $snippet['library_id'] ] );
					$button_text = __( 'Edit snippet', 'insert-headers-and-footers' );
					$pill_text   = __( 'Used', 'insert-headers-and-footers' );
				} else {
					$url = wpcode()->library->get_install_snippet_url( $snippet['library_id'] );
				}
			}
			$title       = $snippet['title'];
			$description = $snippet['note'];
		}
		$id            = $snippet['library_id'];
		$button_2_text = '';
		if ( ! empty( $snippet['code'] ) ) {
			$button_2_text = __( 'Preview', 'insert-headers-and-footers' );
		}
		$categories = isset( $snippet['categories'] ) ? $snippet['categories'] : array();


		$button_2 = array(
			'text'  => $button_2_text,
			'class' => 'wpcode-button wpcode-button-secondary wpcode-library-preview-button',
		);

		if ( ! empty( $snippet['needs_auth'] ) ) {
			$button_1 = array(
				'tag'   => 'button',
				'text'  => get_wpcode_icon( 'lock', 17, 22, '0 0 17 22' ) . __( 'Connect to library to unlock (Free)', 'insert-headers-and-footers' ),
				'class' => 'wpcode-button wpcode-item-use-button wpcode-start-auth wpcode-button-icon',
			);
		} else {
			$button_1 = array(
				'tag'  => 'a',
				'url'  => $url,
				'text' => $button_text,
			);
		}

		$this->get_list_item( $id, $title, $description, $button_1, $button_2, $categories, $pill_text, $pill_class, $category );
	}

	/**
	 * Get a list item markup, used for library & generators.
	 *
	 * @param string $id The id used for the data-id param (used for filtering).
	 * @param string $title The title of the item.
	 * @param string $description The item description.
	 * @param array  $button_1 The first button config (@see get_list_item_button).
	 * @param array  $button_2 The second button config (@see get_list_item_button).
	 * @param array  $categories The categories of this object (for filtering).
	 * @param string $pill_text (optional) Display a "pill" with some text in the top right corner.
	 * @param string $pill_class (optional) Custom CSS class for the pill.
	 * @param string $selected_category (optional) Slug of the category selected by default.
	 *
	 * @return void
	 */
	public function get_list_item( $id, $title, $description, $button_1, $button_2 = array(), $categories = array(), $pill_text = '', $pill_class = 'blue', $selected_category = '*' ) {
		$item_class = array(
			'wpcode-list-item',
		);
		if ( ! empty( $pill_text ) ) {
			$item_class[] = 'wpcode-list-item-has-pill';
		}
		$style = '';
		if ( '*' !== $selected_category && ! in_array( $selected_category, $categories, true ) ) {
			$style = 'display:none;';
		}
		$button_1 = wp_parse_args(
			$button_1,
			array(
				'tag'   => 'a',
				'class' => 'wpcode-button wpcode-item-use-button',
			)
		);
		$button_2 = wp_parse_args(
			$button_2,
			array(
				'class' => 'wpcode-button wpcode-button-secondary',
			)
		);
		?>
		<li class="<?php echo esc_attr( implode( ' ', $item_class ) ); ?>" data-id="<?php echo esc_attr( $id ); ?>" data-categories='<?php echo wp_json_encode( $categories ); ?>' style="<?php echo esc_attr( $style ); ?>">
			<h3 title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></h3>
			<?php if ( ! empty( $pill_text ) ) { ?>
				<span class="wpcode-list-item-pill wpcode-list-item-pill-<?php echo esc_attr( $pill_class ); ?>"><?php echo esc_html( $pill_text ); ?></span>
			<?php } ?>
			<div class="wpcode-list-item-actions">
				<div class="wpcode-list-item-description">
					<p><?php echo esc_html( $description ); ?></p>
				</div>
				<div class="wpcode-list-item-buttons">
					<?php self::get_list_item_button( $button_1 ); ?>
					<?php self::get_list_item_button( $button_2 ); ?>
				</div>
			</div>
			<?php $this->get_list_item_top_actions( $id ); ?>
		</li>
		<?php
	}

	/**
	 * Allow child classes to display additional buttons.
	 *
	 * @param string|int $id The id of the element passed for generating action urls.
	 *
	 * @return void
	 */
	public function get_list_item_top_actions( $id ) {
	}

	/**
	 * Get a button for the list of items.
	 *
	 * @param array $args Arguments for the button.
	 * @param bool  $echo (optional) Whether to echo the button or return it.
	 *
	 * @return void|string
	 */
	public static function get_list_item_button( $args, $echo = true ) {
		$button_settings = wp_parse_args(
			$args,
			array(
				'tag'        => 'button',
				'url'        => '',
				'text'       => '',
				'class'      => 'wpcode-button',
				'attributes' => array(),
			)
		);

		if ( empty( $button_settings['text'] ) ) {
			return;
		}

		$button_settings['class'] = esc_attr( $button_settings['class'] );

		$parsed_attributes = "class='{$button_settings['class']}' ";
		if ( ! empty( $button_settings['url'] ) && 'a' === $button_settings['tag'] ) {
			$parsed_attributes .= 'href="' . esc_url( $button_settings['url'] ) . '" ';
		}
		if ( ! empty( $button_settings['attributes'] ) ) {
			foreach ( $button_settings['attributes'] as $key => $value ) {
				$parsed_attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}

		if ( $echo ) {
			printf(
				'<%1$s %2$s>%3$s</%1$s>',
				$button_settings['tag'], // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$parsed_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wp_kses( $button_settings['text'], wpcode_get_icon_allowed_tags() )
			);
		} else {
			return sprintf(
				'<%1$s %2$s>%3$s</%1$s>',
				$button_settings['tag'], // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$parsed_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wp_kses( $button_settings['text'], wpcode_get_icon_allowed_tags() )
			);
		}
	}

	/**
	 * Output the library markup from an array of categories and an array of snippets.
	 *
	 * @param array  $categories The snippet categories to show.
	 * @param array  $snippets The snippets to show.
	 * @param string $item_method The method in the current class for items output.
	 *
	 * @return void
	 */
	public function get_library_markup( $categories, $snippets, $item_method = 'get_library_snippet_item' ) {
		$selected_category = isset( $categories[0]['slug'] ) ? $categories[0]['slug'] : '*';
		$count             = 0;
		foreach ( $snippets as $snippet ) {
			if ( isset( $snippet['needs_auth'] ) ) {
				$count ++;
			}
		}
		$categories = $this->add_item_counts( $categories, $snippets );
		$categories = $this->add_available_category_label( $categories, $snippets, $count );
		$snippets   = $this->add_available_category_to_snippets( $snippets );
		?>
		<div class="wpcode-items-metabox wpcode-metabox">
			<?php $this->get_items_list_sidebar( $categories, __( 'All Snippets', 'insert-headers-and-footers' ), __( 'Search Snippets', 'insert-headers-and-footers' ), $selected_category, $count ); ?>
			<div class="wpcode-items-list">
				<?php
				if ( empty( $snippets ) ) {
					?>
					<div class="wpcode-alert wpcode-alert-warning">
						<?php printf( '<h4>%s</h4>', esc_html__( 'We encountered a problem loading the Snippet Library items, please try again later.', 'insert-headers-and-footers' ) ); ?>
					</div>
					<?php
				}
				?>
				<ul class="wpcode-items-list-category">
					<?php
					foreach ( $snippets as $snippet ) {
						call_user_func( array( $this, $item_method ), $snippet, $selected_category );
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Goes through snippets and adds the item count to the categories.
	 *
	 * @param array $categories The categories to add the item count to.
	 * @param array $snippets The snippets to count.
	 *
	 * @return array
	 */
	public function add_item_counts( $categories, $snippets ) {
		$category_counts = array();
		foreach ( $snippets as $snippet ) {
			if ( ! isset( $snippet['categories'] ) ) {
				continue;
			}
			if ( empty( $snippet['code'] ) && empty( $snippet['needs_auth'] ) ) {
				continue;
			}
			foreach ( $snippet['categories'] as $category ) {
				if ( ! isset( $category_counts[ $category ] ) ) {
					$category_counts[ $category ] = 0;
				}
				$category_counts[ $category ] ++;
			}
		}

		// Add counts to the categories array.
		foreach ( $categories as $category_id => $category ) {
			if ( ! isset( $category['slug'] ) ) {
				continue;
			}
			$categories[ $category_id ]['count'] = isset( $category_counts[ $category['slug'] ] ) ? $category_counts[ $category['slug'] ] : 0;
		}

		return $categories;
	}

	/**
	 * Create a dynamic category for the available snippets. This goes through all the snippets
	 * and counts how many of them need auth to be used, if there are any, it adds the category that shows
	 * how many are available.
	 *
	 * @param array $categories The categories to add the available category to.
	 * @param array $snippets The snippets to count.
	 *
	 * @return array
	 */
	public function add_available_category_label( $categories, $snippets, $total ) {
		if ( wpcode()->library_auth->has_auth() ) {
			return $categories;
		}
		$need_auth_count = 0;
		foreach ( $snippets as $snippet ) {
			if ( ! empty( $snippet['needs_auth'] ) ) {
				$need_auth_count ++;
			}
		}
		if ( $need_auth_count > 0 ) {
			$categories = array_merge( array(
				array(
					'name'  => __( 'Available Snippets', 'insert-headers-and-footers' ),
					'slug'  => 'available',
					'count' => $total - $need_auth_count,
				)
			), $categories );
		}

		return $categories;
	}

	/**
	 * For snippets that don't need auth, add an extra category "available" to allow easy filtering.
	 *
	 * @param array $snippets The snippets to add the category to.
	 *
	 * @return array
	 */
	public function add_available_category_to_snippets( $snippets ) {
		foreach ( $snippets as $key => $snippet ) {
			if ( empty( $snippet['needs_auth'] ) ) {
				$snippets[ $key ]['categories'][] = 'available';
			}
		}

		return $snippets;
	}

	/**
	 * Get the items list sidebar with optional search form.
	 *
	 * @param array  $categories The array of categories to display as filters - each item needs to have the "slug" and "name" keys.
	 * @param string $all_text Text to display on the all items button in the categories list.
	 * @param string $search_label The search label, if left empty the search form is hidden.
	 * @param string $selected_category Slug of the category selected by default.
	 * @param int    $all_count (optional) The number of items in the all category.
	 *
	 * @return void
	 */
	public function get_items_list_sidebar( $categories, $all_text = '', $search_label = '', $selected_category = '', $all_count = 0 ) {
		?>
		<div class="wpcode-items-sidebar">
			<?php if ( ! empty( $search_label ) ) { ?>
				<div class="wpcode-items-search">
					<label for="wpcode-items-search">
						<span class="screen-reader-text"><?php echo esc_html( $search_label ); ?></span>
						<?php wpcode_icon( 'search', 16, 16 ); ?>
					</label>
					<input type="search" id="wpcode-items-search" placeholder="<?php echo esc_html( $search_label ); ?>"/>
				</div>
			<?php } ?>
			<ul class="wpcode-items-categories-list wpcode-items-filters">
				<?php if ( ! empty( $all_text ) ) { ?>
					<li>
						<button type="button" data-category="*" class="<?php echo empty( $selected_category ) ? 'wpcode-active' : ''; ?>">
							<?php echo esc_html( $all_text ); ?>
							<?php if ( $all_count ) { ?>
								<span class="wpcode-items-count"><?php echo esc_html( $all_count ); ?></span>
							<?php } ?>
						</button>
					</li>
				<?php } ?>
				<?php
				foreach ( $categories as $category ) {
					// Mark the first category as active.
					$class = $category['slug'] === $selected_category ? 'wpcode-active' : '';
					?>
					<li>
						<button type="button" class="<?php echo esc_attr( $class ); ?>" data-category="<?php echo esc_attr( $category['slug'] ); ?>">
							<?php echo esc_html( $category['name'] ); ?>
							<?php if ( isset( $category['count'] ) ) { ?>
								<span class="wpcode-items-count"><?php echo esc_html( $category['count'] ); ?></span>
							<?php } ?>
						</button>
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Get the preview modal markup.
	 *
	 * @return void
	 */
	public function library_preview_modal_content() {
		?>
		<div class="wpcode-library-preview wpcode-modal" id="wpcode-library-preview">
			<div class="wpcode-library-preview-header">
				<button type="button" class="wpcode-just-icon-button wpcode-close-modal"><?php wpcode_icon( 'close', 15, 14 ); ?></button>
				<h2><?php esc_html_e( 'Preview Snippet', 'insert-headers-and-footers' ); ?></h2>
			</div>
			<div class="wpcode-library-preview-content">
				<h3>
					<label for="wpcode-code-preview" id="wpcode-preview-title"><?php esc_html_e( 'Code Preview', 'insert-headers-and-footers' ); ?></label>
				</h3>
				<textarea id="wpcode-code-preview"></textarea>
			</div>
			<div class="wpcode-library-preview-buttons">
				<a class="wpcode-button wpcode-button-wide" id="wpcode-preview-use-code"><?php esc_html_e( 'Use Snippet', 'insert-headers-and-footers' ); ?></a>
			</div>
		</div>
		<?php
		$editor = new WPCode_Code_Editor( 'text' );
		$editor->set_setting( 'readOnly', 'nocursor' );
		$editor->set_setting( 'gutters', array() );
		$editor->register_editor( 'wpcode-code-preview' );
		$editor->init_editor();
	}

	/**
	 * Load library data in JS.
	 *
	 * @param array $data The library data.
	 *
	 * @return array
	 */
	public function maybe_add_library_data( $data ) {
		if ( $this->show_library ) {
			$data['library'] = wpcode()->library->get_data();
		}

		return $data;
	}

	/**
	 * Get the full URL for a view of an admin page.
	 *
	 * @param string $view The view slug.
	 *
	 * @return string
	 */
	public function get_view_link( $view ) {
		return add_query_arg(
			array(
				'page' => $this->page_slug,
				'view' => $view,
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Get an upsell box markup.
	 *
	 * @param string $title The main upsell box title.
	 * @param string $text The text displayed under the title.
	 * @param string $button_1 The main CTA button.
	 * @param string $button_2 The text link below the main CTA.
	 * @param array  $features A list of features to display below the text.
	 *
	 * @return string
	 */
	public static function get_upsell_box( $title, $text = '', $button_1 = array(), $button_2 = array(), $features = array() ) {

		$container_class = array(
			'wpcode-upsell-box',
		);

		if ( ! empty( $features ) ) {
			$container_class[] = 'wpcode-upsell-box-with-features';
		}

		$html = sprintf(
			'<div class="%s">',
			esc_attr( implode( ' ', $container_class ) )
		);

		$html .= '<div class="wpcode-upsell-text-content">';

		$html .= sprintf(
			'<h2>%s</h2>',
			wp_kses_post( $title )
		);

		if ( ! empty( $text ) ) {
			$html .= sprintf(
				'<div class="wpcode-upsell-text">%s</div>',
				wp_kses_post( $text )
			);
		}

		if ( ! empty( $features ) ) {
			$html .= '<ul class="wpcode-upsell-features">';
			foreach ( $features as $feature ) {
				$html .= sprintf(
					'<li class="wpcode-upsell-feature">%s</li>',
					wp_kses_post( $feature )
				);
			}
			$html .= '</ul>';
		}
		$button_1 = wp_parse_args(
			$button_1,
			array(
				'tag'        => 'a',
				'text'       => '',
				'url'        => wpcode_utm_url( 'https://wpcode.com/lite/' ),
				'class'      => 'wpcode-button wpcode-button-orange wpcode-button-large',
				'attributes' => array(
					'target' => '_blank',
				),
			)
		);
		$button_2 = wp_parse_args(
			$button_2,
			array(
				'tag'        => 'a',
				'text'       => '',
				'url'        => wpcode_utm_url( 'https://wpcode.com/lite/' ),
				'class'      => 'wpcode-upsell-button-text',
				'attributes' => array(
					'target' => '_blank',
				),
			)
		);

		$html .= '</div>'; // .wpcode-upsell-text-content
		$html .= '<div class="wpcode-upsell-buttons">';

		if ( ! empty( $button_1['text'] ) ) {
			$html .= self::get_list_item_button( $button_1, false );
		}

		if ( ! empty( $button_2['text'] ) ) {
			$html .= '<br />';
			$html .= self::get_list_item_button( $button_2, false );
		}

		$html .= '</div>'; // .wpcode-upsell-buttons

		$html .= '</div>';

		return $html;

	}

	/**
	 * Banner to highlight that connecting to the library gives you access to more snippets.
	 *
	 * @return void
	 */
	public function library_connect_banner_template() {

		if ( wpcode()->library_auth->has_auth() ) {
			return;
		}

		$data  = wpcode()->library->get_data();
		$count = 0;
		if ( ! empty( $data['snippets'] ) ) {
			$count = count( $data['snippets'] );
		}
		?>
		<script type="text/html" id="tmpl-wpcode-library-connect-banner">
			<div id="wpcode-library-connect-banner">
				<div class="wpcode-template-content">
					<h3>
						<?php
						/* translators: %d - snippets count. */
						printf( esc_html__( 'Get Access to Our Library of %d FREE Snippets', 'insert-headers-and-footers' ), $count );
						?>
					</h3>

					<p>
						<?php esc_html_e( 'Connect your website with WPCode Library and get instant access to FREE code snippets written by our experts. Snippets can be installed with just 1-click from inside the plugin and come automatically-configured to save you time.', 'insert-headers-and-footers' ); ?>
					</p>
				</div>
				<div class="wpcode-template-upgrade-button">
					<button class="wpcode-button wpcode-start-auth"><?php esc_html_e( 'Connect to Library', 'insert-headers-and-footers' ); ?></button>
				</div>
			</div>
		</script>
		<?php
	}

	/**
	 * On any of the plugin pages, if the user installed the plugin from the
	 * deploy a snippet flow, redirect to the 1-click page to allow them to continue that process.
	 *
	 * @return void
	 */
	public function maybe_redirect_to_click() {
		if ( 'wpcode-click' === $this->page_slug ) {
			// Don't redirect this page to avoid an infinite loop.
			return;
		}
		if ( false !== get_transient( 'wpcode_deploy_snippet_id' ) ) {
			// Don't delete the transient here, it will be deleted in the 1-click page.
			wp_safe_redirect( admin_url( 'admin.php?page=wpcode-click' ) );
			exit;
		}
	}
}
