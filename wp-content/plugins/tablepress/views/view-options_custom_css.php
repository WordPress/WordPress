<?php
/**
 * Plugin Options/Save Custom CSS Credentials Form View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Plugin Options/Save Custom CSS Credentials Form View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Options_Custom_CSS_View extends TablePress_View {

	/**
	 * Set up the view with data and do things that are specific for this view.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view.
	 * @param array  $data   Data for this view.
	 */
	public function setup( $action, array $data ) {
		// Set action manually here, to get correct page title and nav bar entries.
		$this->action = 'options';
		$this->data = $data;

		// Set page title.
		$GLOBALS['title'] = sprintf( __( '%1$s &lsaquo; %2$s', 'tablepress' ), $this->data['view_actions'][ $this->action ]['page_title'], 'TablePress' );

		$this->add_header_message( '<strong>' . __( 'Attention: Further action is required to save the changes to your &#8220;Custom CSS&#8221;!', 'tablepress' ) . '</strong>', 'notice-success' );

		// Admin page helpers, like script/style loading, could be moved to view.
		$this->admin_page = TablePress::load_class( 'TablePress_Admin_Page', 'class-admin-page-helper.php', 'classes' );
		$this->admin_page->enqueue_style( 'common' );

		$this->admin_page->add_admin_footer_text();

		$this->add_text_box( 'explanation-text', array( $this, 'textbox_explanation_text' ), 'normal' );
		$this->add_text_box( 'credentials-form', array( $this, 'textbox_credentials_form' ), 'normal' );
		$this->add_text_box( 'proceed-no-file-saving', array( $this, 'textbox_proceed_no_file_saving' ), 'submit' );
	}

	/**
	 * Render the current view (in this view: without form tag).
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
		</div>
		<?php
	}

	/**
	 * Print the content of the "Explanation" text box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	public function textbox_explanation_text( array $data, array $box ) {
		?>
		<p>
			<?php _e( 'Due to the configuration of your server, TablePress was not able to automatically save your &#8220;Custom CSS&#8221; to a file.', 'tablepress' ); ?>
			<?php printf( __( 'To try again with the same method that you use for updating plugins or themes, please fill out the &#8220;%s&#8221; form below.', 'tablepress' ), __( 'Connection Information', 'default' ) ); ?>
		</p>
		<?php
	}

	/**
	 * Print the content of the "Credentials" text box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	public function textbox_credentials_form( array $data, array $box ) {
		echo $data['credentials_form'];
	}

	/**
	 * Print the content of the "Cancel Saving" text box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	public function textbox_proceed_no_file_saving( array $data, array $box ) {
		?>
		<h2><?php _e( 'Proceed without saving a file', 'tablepress' ); ?></h2>
		<p>
			<?php _e( 'To proceed without trying to save the &#8220;Custom CSS&#8221; to a file, click the button below.', 'tablepress' ); ?>
			<?php _e( 'Your &#8220;Custom CSS&#8221; will then be loaded inline.', 'tablepress' ); ?>
		</p><p>
			<a href="<?php echo TablePress::url( array( 'action' => 'options', 'message' => 'success_save_error_custom_css' ) ); ?>" class="button button-large"><?php _e( 'Proceed without saving &#8220;Custom CSS&#8221; to a file', 'tablepress' ); ?></a>
		</p>
		<?php
	}

} // class TablePress_Options_Custom_CSS_View
