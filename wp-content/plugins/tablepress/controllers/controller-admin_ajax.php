<?php
/**
 * Admin AJAX Controller for TablePress with functionality for the AJAX backend
 *
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Admin AJAX Controller class, extends Base Controller Class
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Admin_AJAX_Controller extends TablePress_Controller {

	/**
	 * Initiate Admin AJAX functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Buffer all outputs, to prevent errors/warnings being printed that make the JSON invalid.
		ob_start();

		parent::__construct();

		$ajax_actions = array( 'hide_message', 'save_table', 'preview_table' );
		foreach ( $ajax_actions as $action ) {
			add_action( "wp_ajax_tablepress_{$action}", array( $this, "ajax_action_{$action}" ) );
		}
	}

	/**
	 * Hide a header message on an admin screen.
	 *
	 * @since 1.0.0
	 */
	public function ajax_action_hide_message() {
		if ( empty( $_GET['item'] ) ) {
			wp_die( '0' );
		} else {
			$message_item = $_GET['item'];
		}

		TablePress::check_nonce( 'hide_message', $message_item, '_wpnonce', true );

		if ( ! current_user_can( 'tablepress_list_tables' ) ) {
			wp_die( '-1' );
		}

		TablePress::$model_options->update( "message_{$message_item}", false );

		wp_die( '1' );
	}

	/**
	 * Save the table after the "Save Changes" button on the "Edit" screen has been clicked.
	 *
	 * @since 1.0.0
	 */
	public function ajax_action_save_table() {
		if ( empty( $_POST['tablepress'] ) || empty( $_POST['tablepress']['id'] ) ) {
			wp_die( '-1' );
		} else {
			$edit_table = wp_unslash( $_POST['tablepress'] );
		}

		// Check if the submitted nonce matches the generated nonce we created earlier, dies -1 on failure.
		TablePress::check_nonce( 'edit', $edit_table['id'], '_ajax_nonce', true );

		// Ignore the request if the current user doesn't have sufficient permissions.
		if ( ! current_user_can( 'tablepress_edit_table', $edit_table['id'] ) ) {
			wp_die( '-1' );
		}

		// Default response data.
		$success = false;
		$message = 'error_save';
		$error_details = '';
		do { // to be able to "break;" (allows for better readable code)
			// Load table, without table data, but with options and visibility settings.
			$existing_table = TablePress::$model_table->load( $edit_table['id'], false, true );
			if ( is_wp_error( $existing_table ) ) { // maybe somehow load a new table here? (TablePress::$model_table->get_table_template())?
				// Add an error code to the existing WP_Error.
				$existing_table->add( 'ajax_save_table_load', '', $edit_table['id'] );
				$error_details = $this->get_wp_error_string( $existing_table );
				break;
			}

			// Check and convert data that was transmitted as JSON.
			if ( empty( $edit_table['data'] )
			|| empty( $edit_table['options'] )
			|| empty( $edit_table['visibility'] ) ) {
				// Create a new WP_Error.
				$empty_data_error = new WP_Error( 'ajax_save_table_data_empty', '', $edit_table['id'] );
				$error_details = $this->get_wp_error_string( $empty_data_error );
				break;
			}
			$edit_table['data'] = (array) json_decode( $edit_table['data'], true );
			$edit_table['options'] = (array) json_decode( $edit_table['options'], true );
			$edit_table['visibility'] = (array) json_decode( $edit_table['visibility'], true );

			// Check consistency of new table, and then merge with existing table.
			$table = TablePress::$model_table->prepare_table( $existing_table, $edit_table, true, true );
			if ( is_wp_error( $table ) ) {
				// Add an error code to the existing WP_Error.
				$table->add( 'ajax_save_table_prepare', '', $edit_table['id'] );
				$error_details = $this->get_wp_error_string( $table );
				break;
			}

			// DataTables Custom Commands can only be edit by trusted users.
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				$table['options']['datatables_custom_commands'] = $existing_table['options']['datatables_custom_commands'];
			}

			// Save updated table.
			$saved = TablePress::$model_table->save( $table );
			if ( is_wp_error( $saved ) ) {
				// Add an error code to the existing WP_Error.
				$saved->add( 'ajax_save_table_save', '', $table['id'] );
				$error_details = $this->get_wp_error_string( $saved );
				break;
			}

			// At this point, the table was saved successfully, possible ID change remains.
			$success = true;
			$message = 'success_save';

			// Check if ID change is desired.
			if ( $table['id'] === $table['new_id'] ) {
				// If not, we are done.
				break;
			}

			// Change table ID.
			if ( current_user_can( 'tablepress_edit_table_id', $table['id'] ) ) {
				$id_changed = TablePress::$model_table->change_table_id( $table['id'], $table['new_id'] );
				if ( ! is_wp_error( $id_changed ) ) {
					$message = 'success_save_success_id_change';
					$table['id'] = $table['new_id'];
				} else {
					$message = 'success_save_error_id_change';
					// Add an error code to the existing WP_Error.
					$id_changed->add( 'ajax_save_table_id_change', '', $table['new_id'] );
					$error_details = $this->get_wp_error_string( $id_changed );
				}
			} else {
				$message = 'success_save_error_id_change';
				$error_details = 'table_id_could_not_be_changed: capability_check_failed';
			}
		} while ( false ); // Do-while-loop through this exactly once, to be able to "break;" early.

		// Generate the response.

		// Common data for all responses.
		$response = array(
			'success' => $success,
			'message' => $message,
		);
		if ( $success ) {
			$response['table_id'] = $table['id'];
			$response['new_edit_nonce'] = wp_create_nonce( TablePress::nonce( 'edit', $table['id'] ) );
			$response['new_preview_nonce'] = wp_create_nonce( TablePress::nonce( 'preview_table', $table['id'] ) );
			$response['last_modified'] = TablePress::format_datetime( $table['last_modified'] );
			$response['last_editor'] = TablePress::get_user_display_name( $table['options']['last_editor'] );
		}
		if ( ! empty( $error_details ) ) {
			$response['error_details'] = esc_html( $error_details );
		}
		// Buffer all outputs, to prevent errors/warnings being printed that make the JSON invalid.
		$output_buffer = ob_get_clean();
		if ( ! empty( $output_buffer ) ) {
			$response['output_buffer'] = $output_buffer;
		}

		// Send the response.
		wp_send_json( $response );
	}

	/**
	 * Return the live preview data of table that has non-saved changes.
	 *
	 * @since 1.0.0
	 */
	public function ajax_action_preview_table() {
		if ( empty( $_POST['tablepress'] ) || empty( $_POST['tablepress']['id'] ) ) {
			wp_die( '-1' );
		} else {
			$preview_table = wp_unslash( $_POST['tablepress'] );
		}

		// Check if the submitted nonce matches the generated nonce we created earlier, dies -1 on failure.
		TablePress::check_nonce( 'preview_table', $preview_table['id'], '_ajax_nonce', true );

		// Ignore the request if the current user doesn't have sufficient permissions.
		if ( ! current_user_can( 'tablepress_preview_table', $preview_table['id'] ) ) {
			wp_die( '-1' );
		}

		// Default response data.
		$success = false;
		do { // to be able to "break;" (allows for better readable code)
			// Load table, without table data, but with options and visibility settings.
			$existing_table = TablePress::$model_table->load( $preview_table['id'], false, true );
			if ( is_wp_error( $existing_table ) ) { // maybe somehow load a new table here? (TablePress::$model_table->get_table_template())?
				break;
			}

			// Check and convert data that was transmitted as JSON.
			if ( empty( $preview_table['data'] )
			|| empty( $preview_table['options'] )
			|| empty( $preview_table['visibility'] ) ) {
				break;
			}
			$preview_table['data'] = (array) json_decode( $preview_table['data'], true );
			$preview_table['options'] = (array) json_decode( $preview_table['options'], true );
			$preview_table['visibility'] = (array) json_decode( $preview_table['visibility'], true );

			// Check consistency of new table, and then merge with existing table.
			$table = TablePress::$model_table->prepare_table( $existing_table, $preview_table, true, true );
			if ( is_wp_error( $table ) ) {
				break;
			}

			// DataTables Custom Commands can only be edit by trusted users.
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				$table['options']['datatables_custom_commands'] = $existing_table['options']['datatables_custom_commands'];
			}

			// If the ID has changed, and the new ID is valid, render with the new ID (important e.g. for CSS classes/HTML ID).
			if ( $table['id'] !== $table['new_id'] && 0 === preg_match( '/[^a-zA-Z0-9_-]/', $table['new_id'] ) ) {
				$table['id'] = $table['new_id'];
			}

			// Sanitize all table data to remove unsafe HTML from the preview output.
			$table = TablePress::$model_table->sanitize( $table );

			// At this point, the table data is valid and sanitized and can be rendered.
			$success = true;
		} while ( false ); // Do-while-loop through this exactly once, to be able to "break;" early.

		// Initialize i18n support, load plugin's textdomain, to retrieve correct translations for the description of the preview.
		load_plugin_textdomain( 'tablepress', false, dirname( TABLEPRESS_BASENAME ) . '/i18n' );

		if ( $success ) {
			// Create a render class instance.
			$_render = TablePress::load_class( 'TablePress_Render', 'class-render.php', 'classes' );
			// Merge desired options with default render options (see TablePress_Controller_Frontend::shortcode_table()).
			$default_render_options = $_render->get_default_render_options();
			/** This filter is documented in controllers/controller-frontend.php */
			$default_render_options = apply_filters( 'tablepress_shortcode_table_default_shortcode_atts', $default_render_options );
			$render_options = shortcode_atts( $default_render_options, $table['options'] );
			/** This filter is documented in controllers/controller-frontend.php */
			$render_options = apply_filters( 'tablepress_shortcode_table_shortcode_atts', $render_options );
			$_render->set_input( $table, $render_options );
			$head_html = $_render->get_preview_css();
			$custom_css = TablePress::$model_options->get( 'custom_css' );
			if ( ! empty( $custom_css ) ) {
				$head_html .= "<style type=\"text/css\">\n{$custom_css}\n</style>\n";
			}

			$body_html = '<div id="tablepress-page"><p>'
				. __( 'This is a preview of your table.', 'tablepress' ) . ' '
				. __( 'Because of CSS styling in your theme, the table might look different on your page!', 'tablepress' ) . ' '
				. __( 'The features of the DataTables JavaScript library are also not available or visible in this preview!', 'tablepress' ) . '<br />'
				. sprintf( __( 'To insert the table into a page, post, or text widget, copy the Shortcode %s and paste it into the editor.', 'tablepress' ), '<input type="text" class="table-shortcode table-shortcode-inline" value="' . esc_attr( '[' . TablePress::$shortcode . " id={$table['id']} /]" ) . '" readonly="readonly" />' )
				. '</p>' . $_render->get_output() . '</div>';
		} else {
			$head_html = '';
			$body_html = __( 'The preview could not be loaded.', 'tablepress' );
		}

		// Generate the response.
		$response = array(
			'success'   => $success,
			'head_html' => $head_html,
			'body_html' => $body_html,
		);
		// Buffer all outputs, to prevent errors/warnings being printed that make the JSON invalid.
		$output_buffer = ob_get_clean();
		if ( ! empty( $output_buffer ) ) {
			$response['output_buffer'] = $output_buffer;
		}

		// Send the response.
		wp_send_json( $response );
	}

	/**
	 * Retrieve all information of a WP_Error object as a string.
	 *
	 * @since 1.4.0
	 *
	 * @param WP_Error $wp_error A WP_Error object.
	 * @return string All error codes, messages, and data of the WP_Error.
	 */
	protected function get_wp_error_string( $wp_error ) {
		$error_strings = array();
		$error_codes = $wp_error->get_error_codes();
		// Reverse order to get latest errors first.
		$error_codes = array_reverse( $error_codes );
		foreach ( $error_codes as $error_code ) {
			$error_strings[ $error_code ] = $error_code;
			$error_messages = $wp_error->get_error_messages( $error_code );
			$error_messages = implode( ', ', $error_messages );
			if ( ! empty( $error_messages ) ) {
				$error_strings[ $error_code ] .= " ({$error_messages})";
			}
			$error_data = $wp_error->get_error_data( $error_code );
			if ( ! is_null( $error_data ) ) {
				$error_strings[ $error_code ] .= " [{$error_data}]";
			}
		}
		return implode( ";\n", $error_strings );
	}

} // class TablePress_Admin_AJAX_Controller
