<?php
/**
 * Add Table View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Add Table View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Add_View extends TablePress_View {

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

		$this->admin_page->enqueue_script( 'add', array( 'jquery-core' ) );

		$this->process_action_messages( array(
			'error_add' => __( 'Error: The table could not be added.', 'tablepress' ),
		) );

		$this->add_text_box( 'head', array( $this, 'textbox_head' ), 'normal' );
		$this->add_meta_box( 'add-table', __( 'Add New Table', 'tablepress' ), array( $this, 'postbox_add_table' ), 'normal' );
		$this->data['submit_button_caption'] = __( 'Add Table', 'tablepress' );
		$this->add_text_box( 'submit', array( $this, 'textbox_submit_button' ), 'submit' );
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
			<?php _e( 'To add a new table, enter its name, a description (optional), and the number of rows and columns into the form below.', 'tablepress' ); ?>
		</p>
		<p>
			<?php _e( 'You can always change the name, description, and size of your table later.', 'tablepress' ); ?>
		</p>
		<?php
	}

	/**
	 * Print the content of the "Add New Table" post meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the meta box.
	 */
	public function postbox_add_table( array $data, array $box ) {
		?>
		<div class="form-wrap">
			<div class="form-field">
				<label for="table-name"><?php _e( 'Table Name', 'tablepress' ); ?>:</label>
				<input type="text" name="table[name]" id="table-name" class="placeholder placeholder-active" value="<?php esc_attr_e( 'Enter Table Name here', 'tablepress' ); ?>" />
				<p><?php _e( 'The name or title of your table.', 'tablepress' ); ?></p>
			</div>
			<div class="form-field">
				<label for="table-description"><?php _e( 'Description', 'tablepress' ); ?> <?php _e( '(optional)', 'tablepress' ); ?>:</label>
				<textarea name="table[description]" id="table-description" class="placeholder placeholder-active" rows="4"><?php echo esc_textarea( __( 'Enter Description here', 'tablepress' ) ); ?></textarea>
				<p><?php _e( 'A description of the contents of your table.', 'tablepress' ); ?></p>
			</div>
			<div class="form-field form-required form-field-numbers-only form-field-small">
				<label for="table-rows"><?php _e( 'Number of Rows', 'tablepress' ); ?>:</label>
				<input type="number" name="table[rows]" id="table-rows" title="<?php esc_attr_e( 'This field must contain a positive number.', 'tablepress' ); ?>" value="5" min="1" max="99999" maxlength="5" required />
				<p><?php _e( 'The number of rows in your table.', 'tablepress' ); ?></p>
			</div>
			<div class="form-field form-required form-field-numbers-only form-field-small">
				<label for="table-columns"><?php _e( 'Number of Columns', 'tablepress' ); ?>:</label>
				<input type="number" name="table[columns]" id="table-columns" title="<?php esc_attr_e( 'This field must contain a positive number.', 'tablepress' ); ?>" value="5" min="1" max="99999" maxlength="5" required />
				<p><?php _e( 'The number of columns in your table.', 'tablepress' ); ?></p>
			</div>
			<div class="clear"></div>
		</div>
		<?php
	}

} // class TablePress_Add_View
