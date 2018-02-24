<?php
/**
 * TablePress Base View with members and methods for all views
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress Base View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
abstract class TablePress_View {

	/**
	 * Data for the view.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array();

	/**
	 * Number of screen columns for post boxes.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $screen_columns = 0;

	/**
	 * User action for this screen.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = '';

	/**
	 * Instance of the Admin Page Helper Class, with necessary functions.
	 *
	 * @since 1.0.0
	 * @var TablePress_Admin_Page
	 */
	protected $admin_page;

	/**
	 * List of text boxes (similar to post boxes, but just with text and without extra functionality).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $textboxes = array();

	/**
	 * List of messages that are to be displayed as boxes below the page title.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $header_messages = array();

	/**
	 * Whether there are post boxes registered for this screen,
	 * is automatically set to true, when a meta box is added.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_meta_boxes = false;

	/**
	 * List of WP feature pointers for this view.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $wp_pointers = array();

	/**
	 * Initialize the View class, by setting the correct screen columns and adding help texts.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$screen = get_current_screen();
		if ( 0 !== $this->screen_columns ) {
			$screen->add_option( 'layout_columns', array( 'max' => $this->screen_columns ) );
		}
		// Enable two column layout.
		add_filter( "get_user_option_screen_layout_{$screen->id}", array( $this, 'set_current_screen_layout_columns' ) );

		$screen->add_help_tab( array(
			'id'      => 'tablepress-help', // This should be unique for the screen.
			'title'   => __( 'TablePress Help', 'tablepress' ),
			'content' => '<p>' . $this->help_tab_content() . '</p>'
						. '<p>' . sprintf( __( 'More information about TablePress can be found on the <a href="%1$s">plugin&#8217;s website</a> or on its page in the <a href="%2$s">WordPress Plugin Directory</a>.', 'tablepress' ), 'https://tablepress.org/', 'https://wordpress.org/plugins/tablepress/' ) . ' '
						. sprintf( __( 'For technical information, please see the <a href="%s">documentation</a>.', 'tablepress' ), 'https://tablepress.org/documentation/' ) . ' '
						. sprintf( __( '<a href="%1$s">Support</a> is provided through the <a href="%2$s">WordPress Support Forums</a>.', 'tablepress' ), 'https://tablepress.org/support/', 'https://wordpress.org/tags/tablepress' ) . ' '
						. sprintf( __( 'Before asking for support, please carefully read the <a href="%s">Frequently Asked Questions</a>, where you will find answers to the most common questions, and search through the forums.', 'tablepress' ), 'https://tablepress.org/faq/' ) . '<br />'
						. sprintf( __( 'If you like the plugin, <a href="%1$s"><strong>a donation</strong></a> is recommended.', 'tablepress' ), 'https://tablepress.org/donate/' ) . '</p>',
		) );
		// "sidebar" in the help tab.
		$screen->set_help_sidebar( '<p><strong>' . __( 'For more information:', 'tablepress' ) . '</strong></p><p><a href="https://tablepress.org/" target="_blank">TablePress Website</a></p><p><a href="https://tablepress.org/faq/" target="_blank">TablePress FAQ</a></p><p><a href="https://tablepress.org/documentation/" target="_blank">TablePress Documentation</a></p><p><a href="https://tablepress.org/support/" target="_blank">TablePress Support</a></p>' );
	}

	/**
	 * Change the value of the user option "screen_layout_{$screen->id}" through a filter.
	 *
	 * @since 1.0.0
	 *
	 * @param int|bool Current value of the user option.
	 * @return int New value for the user option.
	 */
	public function set_current_screen_layout_columns( $result ) {
		if ( false === $result ) {
			// The user option does not yet exist.
			$result = $this->screen_columns;
		} elseif ( $result > $this->screen_columns ) {
			// The value of the user option is bigger than what is possible on this screen (e.g. because the number of columns was reduced in an update).
			$result = $this->screen_columns;
		}
		return $result;
	}

	/**
	 * Set up the view with data and do things that are necessary for all views.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view.
	 * @param array  $data   Data for this view.
	 */
	public function setup( $action, array $data ) {
		$this->action = $action;
		$this->data = $data;

		// Set page title.
		$GLOBALS['title'] = sprintf( __( '%1$s &lsaquo; %2$s', 'tablepress' ), $this->data['view_actions'][ $this->action ]['page_title'], 'TablePress' );

		// Admin page helpers, like script/style loading, could be moved to view.
		$this->admin_page = TablePress::load_class( 'TablePress_Admin_Page', 'class-admin-page-helper.php', 'classes' );
		$this->admin_page->enqueue_style( 'common' );
		// RTL styles for the admin interface.
		if ( is_rtl() ) {
			$this->admin_page->enqueue_style( 'common-rtl', array( 'tablepress-common' ) );
		}
		$this->admin_page->enqueue_script( 'common', array( 'jquery-core', 'postbox' ), array(
			'common' => array(
				'ays_delete_single_table'    => _n( 'Do you really want to delete this table?', 'Do you really want to delete these tables?', 1, 'tablepress' ),
				'ays_delete_multiple_tables' => _n( 'Do you really want to delete this table?', 'Do you really want to delete these tables?', 2, 'tablepress' ),
			)
		) );

		$this->admin_page->add_admin_footer_text();

		// Initialize WP feature pointers for TablePress.
		$this->_init_wp_pointers();

		// Necessary fields for all views.
		$this->add_text_box( 'default_nonce_fields', array( $this, 'default_nonce_fields' ), 'header', false );
		$this->add_text_box( 'action_nonce_field', array( $this, 'action_nonce_field' ), 'header', false );
		$this->add_text_box( 'action_field', array( $this, 'action_field' ), 'header', false );
	}

	/**
	 * Register a header message for the view.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text  Text for the header message.
	 * @param string $class Optional. Additional CSS class for the header message.
	 */
	protected function add_header_message( $text, $class = 'notice-success' ) {
		if ( ! stripos( $class, 'not-dismissible' ) ) {
			$class .= ' is-dismissible';
		}
		$this->header_messages[] = "<div class=\"notice {$class}\"><p>{$text}</p></div>\n";
	}

	/**
	 * Process header action messages, i.e. check if a message should be added to the page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $action_messages Action messages for the screen.
	 */
	protected function process_action_messages( array $action_messages ) {
		if ( $this->data['message'] && isset( $action_messages[ $this->data['message'] ] ) ) {
			$class = ( 'error' === substr( $this->data['message'], 0, 5 ) ) ? 'notice-error' : 'notice-success';
			$this->add_header_message( "<strong>{$action_messages[ $this->data['message'] ]}</strong>", $class );
		}
	}

	/**
	 * Register a text box for the view.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $id       Unique HTML ID for the text box container (only visible with $wrap = true).
	 * @param callback $callback Callback that prints the contents of the text box.
	 * @param string   $context  Optional. Context/position of the text box (normal, side, additional, header, submit).
	 * @param bool $   wrap      Whether the content of the text box shall be wrapped in a <div> container.
	 */
	protected function add_text_box( $id, $callback, $context = 'normal', $wrap = false ) {
		if ( ! isset( $this->textboxes[ $context ] ) ) {
			$this->textboxes[ $context ] = array();
		}

		$long_id = "tablepress_{$this->action}-{$id}";
		$this->textboxes[ $context ][ $id ] = array(
			'id'       => $long_id,
			'callback' => $callback,
			'context'  => $context,
			'wrap'     => $wrap,
		);
	}

	/**
	 * Register a post meta box for the view, that is drag/droppable with WordPress functionality.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $id            Unique ID for the meta box.
	 * @param string   $title         Title for the meta box.
	 * @param callback $callback      Callback that prints the contents of the post meta box.
	 * @param string   $context       Optional. Context/position of the post meta box (normal, side, additional).
	 * @param string   $priority      Optional. Order of the post meta box for the $context position (high, default, low).
	 * @param bool     $callback_args Optional. Additional data for the callback function (e.g. useful when in different class).
	 */
	protected function add_meta_box( $id, $title, $callback, $context = 'normal', $priority = 'default', $callback_args = null ) {
		$this->has_meta_boxes = true;
		add_meta_box( "tablepress_{$this->action}-{$id}", $title, $callback, null, $context, $priority, $callback_args );
	}

	/**
	 * Render all text boxes for the given context.
	 *
	 * @since 1.0.0
	 *
	 * @param string $context Context (normal, side, additional, header, submit) for which registered text boxes shall be rendered.
	 */
	protected function do_text_boxes( $context ) {
		if ( empty( $this->textboxes[ $context ] ) ) {
			return;
		}

		foreach ( $this->textboxes[ $context ] as $box ) {
			if ( $box['wrap'] ) {
				echo "<div id=\"{$box['id']}\" class=\"textbox\">\n";
			}
			call_user_func( $box['callback'], $this->data, $box );
			if ( $box['wrap'] ) {
				echo "</div>\n";
			}
		}
	}

	/**
	 * Render all post meta boxes for the given context, if there are post meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $context Context (normal, side, additional) for which registered post meta boxes shall be rendered.
	 */
	protected function do_meta_boxes( $context ) {
		if ( ! $this->has_meta_boxes ) {
			return;
		}
		do_meta_boxes( null, $context, $this->data );
	}

	/**
	 * Print hidden fields with nonces for post meta box AJAX handling, if there are post meta boxes on the screen.
	 *
	 * The check is possible as this function is executed after post meta boxes have to be registered.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	protected function default_nonce_fields( array $data, array $box ) {
		if ( ! $this->has_meta_boxes ) {
			return;
		}
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		echo "\n";
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		echo "\n";
	}

	/**
	 * Print hidden field with a nonce for the screen's action, to be transmitted in HTTP requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	protected function action_nonce_field( array $data, array $box ) {
		wp_nonce_field( TablePress::nonce( $this->action ) );
		echo "\n";
	}

	/**
	 * Print hidden field with the screen action.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	protected function action_field( array $data, array $box ) {
		echo "<input type=\"hidden\" name=\"action\" value=\"tablepress_{$this->action}\" />\n";
	}

	/**
	 * Render the current view.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>
		<div id="tablepress-page" class="wrap">
		<?php
			$this->print_nav_tab_menu();
			// Print all header messages.
			foreach ( $this->header_messages as $message ) {
				echo $message;
			}
			// "Import" screen has file upload.
			$enctype = ( 'import' === $this->action ) ? ' enctype="multipart/form-data"' : '';
		?>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"<?php echo $enctype; ?>>
			<?php
			$this->do_text_boxes( 'header' );
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-<?php echo ( isset( $GLOBALS['screen_layout_columns'] ) && ( 2 === $GLOBALS['screen_layout_columns'] ) ) ? '2' : '1'; ?>">
					<div id="postbox-container-2" class="postbox-container">
						<?php
						$this->do_text_boxes( 'normal' );
						$this->do_meta_boxes( 'normal' );

						$this->do_text_boxes( 'additional' );
						$this->do_meta_boxes( 'additional' );

						// Print all submit buttons.
						$this->do_text_boxes( 'submit' );
						?>
					</div>
					<div id="postbox-container-1" class="postbox-container">
					<?php
						// Print all boxes in the sidebar.
						$this->do_text_boxes( 'side' );
						$this->do_meta_boxes( 'side' );
					?>
					</div>
				</div>
				<br class="clear" />
			</div>
		</form>
		</div>
		<?php
	}

	/**
	 * Render the navigation menu with links to the possible actions, highlighting the current one.
	 *
	 * @since 1.0.0
	 */
	protected function print_nav_tab_menu() {
		?>
		<div id="tablepress-nav" class="nav-tab-wrapper">
			<h1 class="wp-heading-inline"><?php _e( 'TablePress', 'tablepress' ); ?></h1>
			<?php
			foreach ( $this->data['view_actions'] as $action => $entry ) {
				if ( '' === $entry['nav_tab_title'] ) {
					continue;
				}
				if ( ! current_user_can( $entry['required_cap'] ) ) {
					continue;
				}

				$url = esc_url( TablePress::url( array( 'action' => $action ) ) );
				$active = ( $action === $this->action ) ? ' nav-tab-active' : '';
				$separator = ( 'options' === $action ) ? ' nav-tab-separator' : ''; // Make the "Plugin Options" entry a separator, for some spacing.
				echo "<a class=\"nav-tab{$active}{$separator}\" href=\"{$url}\">{$entry['nav_tab_title']}</a>";
			}
			?>
		</div><hr class="wp-header-end" />
		<?php
	}

	/**
	 * Print a submit button (only done when function is used as a callback for a text box).
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	protected function textbox_submit_button( array $data, array $box ) {
		$caption = isset( $data['submit_button_caption'] ) ? $data['submit_button_caption'] : __( 'Save Changes', 'tablepress' );
		?>
		<p class="submit"><input type="submit" value="<?php echo esc_attr( $caption ); ?>" class="button button-primary button-large" name="submit" /></p>
		<?php
	}

	/**
	 * Return the content for the help tab for this screen.
	 *
	 * Has to be implemented for every view that is visible in the WP Dashboard!
	 *
	 * @since 1.0.0
	 */
	protected function help_tab_content() {
		// Has to be implemented for every view that is visible in the WP Dashboard!
		return '';
	}

	/**
	 * Initialize the WP feature pointers for TablePress.
	 *
	 * @since 1.0.0
	 */
	protected function _init_wp_pointers() {
		// Check if there are WP pointers for this view.
		if ( empty( $this->wp_pointers ) ) {
			return;
		}

		// Get dismissed pointers.
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		$got_pointers = false;
		foreach ( array_diff( $this->wp_pointers, $dismissed ) as $pointer ) {
			// Bind pointer print function.
			add_action( "admin_footer-{$GLOBALS['hook_suffix']}", array( $this, 'wp_pointer_' . $pointer ) );
			$got_pointers = true;
		}

		if ( ! $got_pointers ) {
			return;
		}

		// Add pointers script and style to queue.
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

} // class TablePress_View
