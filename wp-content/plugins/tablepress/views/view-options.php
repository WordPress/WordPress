<?php
/**
 * Plugin Options View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Plugin Options View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Options_View extends TablePress_View {

	/**
	 * Set up the view with data and do things that are specific for this view.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view.
	 * @param array  $data   Data for this view.
	 */
	public function setup( $action, array $data ) {
		parent::setup( $action, $data );

		// Enqueue WordPress copy of CodeMirror, with CSS linting, etc.
		$codemirror_settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		if ( ! empty( $codemirror_settings ) ) {
			// Load CSS adjustments for CodeMirror and the added vertical resizing.
			$this->admin_page->enqueue_style( 'codemirror', array( 'code-editor' ) );
			$this->admin_page->enqueue_script( 'codemirror', array( 'jquery-core', 'jquery-ui-resizable' ), array(
				'codemirror_settings' => $codemirror_settings,
			) );
		}

		$this->admin_page->enqueue_script( 'options', array( 'jquery-core' ), array(
			'strings' => array(
				'uninstall_warning_1' => __( 'Do you really want to uninstall TablePress and delete ALL data?', 'tablepress' ),
				'uninstall_warning_2' => __( 'Are you really sure?', 'tablepress' ),
			)
		) );

		$this->process_action_messages( array(
			'success_save'                     => __( 'Options saved successfully.', 'tablepress' ),
			'success_save_error_custom_css'    => __( 'Options saved successfully, but &#8220;Custom CSS&#8221; was not saved to file.', 'tablepress' ),
			'error_save'                       => __( 'Error: Options could not be saved.', 'tablepress' ),
			'success_import_wp_table_reloaded' => __( 'The WP-Table Reloaded &#8220;Custom CSS&#8221; was imported successfully.', 'tablepress' ),
		) );

		$this->add_text_box( 'head', array( $this, 'textbox_head' ), 'normal' );
		if ( current_user_can( 'tablepress_edit_options' ) ) {
			$this->add_meta_box( 'frontend-options', __( 'Frontend Options', 'tablepress' ), array( $this, 'postbox_frontend_options' ), 'normal' );
		}
		$this->add_meta_box( 'user-options', __( 'User Options', 'tablepress' ), array( $this, 'postbox_user_options' ), 'normal' );
		$this->data['submit_button_caption'] = __( 'Save Changes', 'tablepress' );
		$this->add_text_box( 'submit', array( $this, 'textbox_submit_button' ), 'submit' );
		if ( current_user_can( 'deactivate_plugin', TABLEPRESS_BASENAME ) && current_user_can( 'tablepress_edit_options' ) && current_user_can( 'tablepress_delete_tables' ) && ! is_plugin_active_for_network( TABLEPRESS_BASENAME ) ) {
			$this->add_text_box( 'uninstall-tablepress', array( $this, 'textbox_uninstall_tablepress' ), 'submit' );
		}
	}

	/**
	 * Print the screen head text.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	public function textbox_head( array $data, array $box ) {
		?>
		<p>
			<?php _e( 'TablePress has several options which affect the plugin&#8217;s behavior in different areas.', 'tablepress' ); ?>
		</p>
		<p>
			<?php
				if ( current_user_can( 'tablepress_edit_options' ) ) {
					_e( 'Frontend Options influence the styling of tables in pages, posts, or text widgets, by defining which CSS code shall be loaded.', 'tablepress' );
					echo '<br />';
				}
				_e( 'In the User Options, every TablePress user can choose the position of the plugin in his WordPress admin menu.', 'tablepress' );
			?>
		</p>
		<?php
	}

	/**
	 * Print the content of the "Frontend Options" post meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the meta box.
	 */
	public function postbox_frontend_options( array $data, array $box ) {
?>
<table class="tablepress-postbox-table fixed">
<tbody>
	<tr>
		<th class="column-1" scope="row"><?php _e( 'Custom CSS', 'tablepress' ); ?>:</th>
		<td class="column-2"><label for="option-use-custom-css"><input type="checkbox" id="option-use-custom-css" name="options[use_custom_css]" value="true"<?php checked( $data['frontend_options']['use_custom_css'] ); ?> /> <?php _e( 'Load these &#8220;Custom CSS&#8221; commands to influence the table styling:', 'tablepress' ); ?></label>
		</td>
	</tr>
	<tr>
		<th class="column-1" scope="row"></th>
		<td class="column-2">
			<textarea name="options[custom_css]" id="option-custom-css" class="large-text" rows="8"><?php echo esc_textarea( $data['frontend_options']['custom_css'] ); ?></textarea>
			<p class="description">
			<?php
				printf( __( '&#8220;Custom CSS&#8221; (<a href="%s">Cascading Style Sheets</a>) can be used to change the styling or layout of a table.', 'tablepress' ), 'https://www.htmldog.com/guides/css/beginner/' );
				echo ' ';
				printf( __( 'You can get styling examples from the <a href="%s">FAQ</a>.', 'tablepress' ), 'https://tablepress.org/faq/' );
				echo ' ';
				printf( __( 'Information on available CSS selectors can be found in the <a href="%s">documentation</a>.', 'tablepress' ), 'https://tablepress.org/documentation/' );
				echo ' ';
				_e( 'Please note that invalid CSS code will be stripped, if it can not be corrected automatically.', 'tablepress' );
			?>
			</p>
		</td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 * Print the content of the "User Options" post meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the meta box.
	 */
	public function postbox_user_options( array $data, array $box ) {
		?>
<table class="tablepress-postbox-table fixed">
<tbody>
		<?php
		// Get list of current admin menu entries.
		$entries = array();
		foreach ( $GLOBALS['menu'] as $entry ) {
			if ( false !== strpos( $entry[2], '.php' ) ) {
				$entries[ $entry[2] ] = $entry[0];
			}
		}

		// Remove <span> elements with notification bubbles (e.g. update or comment count).
		if ( isset( $entries['plugins.php'] ) ) {
			$entries['plugins.php'] = preg_replace( '/ <span.*span>/', '', $entries['plugins.php'] );
		}
		if ( isset( $entries['edit-comments.php'] ) ) {
			$entries['edit-comments.php'] = preg_replace( '/ <span.*span>/', '', $entries['edit-comments.php'] );
		}

		// Add separator and generic positions.
		$entries['-'] = '---';
		$entries['top'] = __( 'Top-Level (top)', 'tablepress' );
		$entries['middle'] = __( 'Top-Level (middle)', 'tablepress' );
		$entries['bottom'] = __( 'Top-Level (bottom)', 'tablepress' );

		$select_box = '<select id="option-admin-menu-parent-page" name="options[admin_menu_parent_page]">' . "\n";
		foreach ( $entries as $page => $entry ) {
			$select_box .= '<option' . selected( $page, $data['user_options']['parent_page'], false ) . disabled( $page, '-', false ) . ' value="' . $page . '">' . $entry . "</option>\n";
		}
		$select_box .= "</select>\n";
		?>
	<tr>
		<th class="column-1" scope="row"><label for="option-admin-menu-parent-page"><?php _e( 'Admin menu entry', 'tablepress' ); ?>:</label></th>
		<td class="column-2"><?php printf( __( 'TablePress shall be shown in this section of my admin menu: %s', 'tablepress' ), $select_box ); ?></td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 * Print the content of the "Admin Options" post meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	public function textbox_uninstall_tablepress( array $data, array $box ) {
		?>
		<h1 style="margin-top:40px;"><?php _e( 'Uninstall TablePress', 'tablepress' ); ?></h1>
		<p>
		<?php
			echo __( 'Uninstalling <strong>will permanently delete</strong> all TablePress tables and options from the database.', 'tablepress' ) . '<br />'
				. __( 'It is recommended that you create a backup of the tables (by exporting the tables in the JSON format), in case you later change your mind.', 'tablepress' ) . '<br />'
				. __( 'You will manually need to remove the plugin&#8217;s files from the plugin folder afterwards.', 'tablepress' ) . '<br />'
				. __( 'Be very careful with this and only click the button if you know what you are doing!', 'tablepress' );
		?>
		</p>
		<p><a href="<?php echo TablePress::url( array( 'action' => 'uninstall_tablepress' ), true, 'admin-post.php' ); ?>" id="uninstall-tablepress" class="button"><?php _e( 'Uninstall TablePress', 'tablepress' ); ?></a></p>
		<?php
	}

} // class TablePress_Options_View
