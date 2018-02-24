<?php
/**
 * Import Table View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Import Table View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Import_View extends TablePress_View {

	/**
	 * List of WP feature pointers for this view.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $wp_pointers = array( 'tp100_wp_table_reloaded_import' );

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

		$this->admin_page->enqueue_script( 'import', array( 'jquery-core' ), array(
			'import' => array(
				'error_wp_table_reloaded_nothing_selected' => __( 'Error: You did not select what to import from WP-Table Reloaded!', 'tablepress' ),
			),
		) );

		$this->process_action_messages( array(
			'error_import'                             => __( 'Error: The import failed.', 'tablepress' ),
			'error_no_zip_import'                      => __( 'Error: Import of ZIP files is not available on this server.', 'tablepress' ),
			'error_import_zip_open'                    => __( 'Error: The ZIP file could not be opened.', 'tablepress' ),
			'error_import_zip_content'                 => __( 'Error: The data in the ZIP file is invalid.', 'tablepress' ),
			'error_import_no_existing_id'              => __( 'Error: You selected to replace or append to an existing table, but did not select a table.', 'tablepress' ),
			'error_import_source_invalid'              => __( 'Error: The source for the import is invalid or could not be accessed.', 'tablepress' ),
			'error_import_data'                        => __( 'Error: The data for the import is invalid.', 'tablepress' ),
			'error_wp_table_reloaded_nothing_selected' => __( 'Error: You did not select what to import from WP-Table Reloaded!', 'tablepress' ),
			'error_wp_table_reloaded_not_installed'    => __( 'Error: Existing WP-Table Reloaded tables were not found in the database.', 'tablepress' ),
			'error_import_wp_table_reloaded'           => __( 'Error: The tables from WP-Table Reloaded could not be imported.', 'tablepress' ),
			'error_wp_table_reloaded_dump_file'        => __( 'Error: The WP-Table Reloaded Dump File could not be imported!', 'tablepress' ),
		) );

		$this->add_text_box( 'head', array( $this, 'textbox_head' ), 'normal' );
		$this->add_meta_box( 'import-form', __( 'Import Tables', 'tablepress' ), array( $this, 'postbox_import_form' ), 'normal' );
		if ( current_user_can( 'tablepress_import_tables_wptr' ) ) {
			$this->add_meta_box( 'import-wp-table-reloaded', __( 'Import from WP-Table Reloaded', 'tablepress' ), array( $this, 'postbox_wp_table_reloaded_import' ), 'additional' );
		}

		add_filter( 'default_hidden_meta_boxes', array( $this, 'hide_import_wptr_postbox' ), 10, 2 );
	}

	/**
	 * Hide the "Import from WP-Table Reloaded postbox" by default, if WP-Table Reloaded is not installed. It can still be opened manually from the "Screen Options".
	 *
	 * @since 1.0.0
	 */
	public function hide_import_wptr_postbox( $hidden, $screen ) {
		if ( 'tablepress_import' !== $screen->id ) {
			return $hidden;
		}
		if ( ! $this->data['wp_table_reloaded_installed'] ) {
			$hidden[] = 'tablepress_import-import-wp-table-reloaded';
		}
		return $hidden;
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
			<?php _e( 'TablePress can import tables from existing data, like from a CSV, XLS, or XLSX file from a spreadsheet application (e.g. Excel), an HTML file resembling a webpage, or its own JSON format.', 'tablepress' ); ?>
			<?php _e( 'You can also import existing tables from the WP-Table Reloaded plugin below.', 'tablepress' ); ?>
		</p>
		<p>
			<?php
				_e( 'To import a table, select and enter the import source in the following form.', 'tablepress' );
			if ( 0 < $data['tables_count'] ) {
				echo ' ';
				_e( 'You can also choose to import it as a new table, to replace an existing table, or to append the rows to an existing table.', 'tablepress' );
			}

			?>
		</p>
		<?php
	}

	/**
	 * Print the content of the "Import Tables" post meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the meta box.
	 */
	public function postbox_import_form( array $data, array $box ) {
?>
<table class="tablepress-postbox-table fixed">
<tbody>
	<tr id="row-import-source">
		<th class="column-1" scope="row"><?php _e( 'Import Source', 'tablepress' ); ?>:</th>
		<td class="column-2">
			<label for="tables-import-source-file-upload"><input name="import[source]" id="tables-import-source-file-upload" type="radio" value="file-upload"<?php checked( $data['import_source'], 'file-upload', true ); ?> /> <?php _e( 'File Upload', 'tablepress' ); ?></label>
			<label for="tables-import-source-url"><input name="import[source]" id="tables-import-source-url" type="radio" value="url"<?php checked( $data['import_source'], 'url', true ); ?> /> <?php _e( 'URL', 'tablepress' ); ?></label>
			<?php if ( ( ! is_multisite() && current_user_can( 'manage_options' ) ) || is_super_admin() ) { ?>
			<label for="tables-import-source-server"><input name="import[source]" id="tables-import-source-server" type="radio" value="server"<?php checked( $data['import_source'], 'server', true ); ?> /> <?php _e( 'File on server', 'tablepress' ); ?></label>
			<?php } ?>
			<label for="tables-import-source-form-field"><input name="import[source]" id="tables-import-source-form-field" type="radio" value="form-field"<?php checked( $data['import_source'], 'form-field', true ); ?> /> <?php _e( 'Manual Input', 'tablepress' ); ?></label>
		</td>
	</tr>
	<tr id="row-import-source-file-upload" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-file-upload"><?php _e( 'Select file', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input name="import_file_upload" id="tables-import-file-upload" type="file" class="large-text" style="box-sizing: border-box;" />
			<?php
			if ( $data['zip_support_available'] ) {
				echo '<br /><span class="description">' . __( 'You can import multiple tables by placing them in a ZIP file.', 'tablepress' ) . '</span>';
			}
			?>
		</td>
	</tr>
	<tr id="row-import-source-url" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-url"><?php _e( 'File URL', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input type="text" name="import[url]" id="tables-import-url" class="large-text" value="<?php echo esc_attr( $data['import_url'] ); ?>" />
			<?php
			if ( $data['zip_support_available'] ) {
				echo '<br /><span class="description">' . __( 'You can import multiple tables by placing them in a ZIP file.', 'tablepress' ) . '</span>';
			}
			?>
		</td>
	</tr>
	<?php if ( ( ! is_multisite() && current_user_can( 'manage_options' ) ) || is_super_admin() ) { ?>
	<tr id="row-import-source-server" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-server"><?php _e( 'Server Path to file', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input type="text" name="import[server]" id="tables-import-server" class="large-text" value="<?php echo esc_attr( $data['import_server'] ); ?>" />
			<?php
			if ( $data['zip_support_available'] ) {
				echo '<br /><span class="description">' . __( 'You can import multiple tables by placing them in a ZIP file.', 'tablepress' ) . '</span>';
			}
			?>
		</td>
	</tr>
	<?php } ?>
	<tr id="row-import-source-form-field" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-form-field"><?php _e( 'Import data', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<textarea name="import[form_field]" id="tables-import-form-field" rows="15" cols="40" class="large-text"><?php echo esc_textarea( $data['import_form_field'] ); ?></textarea>
		</td>
	</tr>
	<tr class="top-border bottom-border">
		<th class="column-1" scope="row"><label for="tables-import-format"><?php _e( 'Import Format', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<select id="tables-import-format" name="import[format]">
			<?php
			foreach ( $data['import_formats'] as $format => $name ) {
				$selected = selected( $format, $data['import_format'], false );
				echo "<option{$selected} value=\"{$format}\">{$name}</option>";
			}
			?>
			</select>
			<?php
			if ( ! $data['html_import_support_available'] ) {
				echo '<br /><span class="description">' . __( 'Import of HTML files is not available on your server.', 'tablepress' ) . '</span>';
			}
			?>
		</td>
	</tr>
	<tr id="row-import-type" class="top-border">
		<th class="column-1" scope="row"><?php _e( 'Add, Replace, or Append?', 'tablepress' ); ?>:</th>
		<td class="column-2">
			<label for="tables-import-type-add"><input name="import[type]" id="tables-import-type-add" type="radio" value="add"<?php checked( $data['import_type'], 'add', true ); ?> /> <?php _e( 'Add as new table', 'tablepress' ); ?></label>
			<label for="tables-import-type-replace"><input name="import[type]" id="tables-import-type-replace" type="radio" value="replace"<?php checked( $data['import_type'], 'replace', true ); ?><?php disabled( $data['tables_count'] > 0, false, true ); ?> /> <?php _e( 'Replace existing table', 'tablepress' ); ?></label>
			<label for="tables-import-type-append"><input name="import[type]" id="tables-import-type-append" type="radio" value="append"<?php checked( $data['import_type'], 'append', true ); ?><?php disabled( $data['tables_count'] > 0, false, true ); ?> /> <?php _e( 'Append rows to existing table', 'tablepress' ); ?></label>
		</td>
	</tr>
	<tr id="row-import-existing-table" class="bottom-border">
		<th class="column-1" scope="row"><label for="tables-import-existing-table"><?php _e( 'Table to replace or append to', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<select id="tables-import-existing-table" name="import[existing_table]"<?php disabled( $data['tables_count'] > 0, false, true ); ?>>
				<option value=""><?php _e( '&mdash; Select &mdash;', 'tablepress' ); ?></option>
			<?php
			foreach ( $data['table_ids'] as $table_id ) {
				$table = TablePress::$model_table->load( $table_id, false, false ); // Load table, without table data, options, and visibility settings
				if ( ! current_user_can( 'tablepress_edit_table', $table['id'] ) ) {
					continue;
				}
				if ( '' === trim( $table['name'] ) ) {
					$table['name'] = __( '(no name)', 'tablepress' );
				}
				$text = esc_html( sprintf( __( 'ID %1$s: %2$s', 'tablepress' ), $table['id'], $table['name'] ) );
				$selected = selected( $table['id'], $data['import_existing_table'], false );
				echo "<option{$selected} value=\"{$table['id']}\">{$text}</option>";
			}
			?>
			</select>
		</td>
	</tr>
	<tr class="top-border">
		<th class="column-1" scope="row"></th>
		<td class="column-2"><input type="submit" value="<?php echo esc_attr_x( 'Import', 'button', 'tablepress' ); ?>" class="button button-primary button-large" name="submit" /></td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 * Print the content of the "Import from WP-Table Reloaded" post meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the meta box.
	 */
	public function postbox_wp_table_reloaded_import( array $data, array $box ) {
		?>
<p>
	<?php _e( 'To import all tables from a WP-Table Reloaded installation, choose the relevant import source below.', 'tablepress' ); ?>
<br />
	<?php _e( 'If WP-Table Reloaded is installed on this site, the &#8220;WordPress database&#8221; option is recommended.', 'tablepress' ); ?>
	<?php _e( 'If you want to import tables from another site, create a &#8220;WP-Table Reloaded Dump File&#8221; there and upload it below, after choosing &#8220;WP-Table Reloaded Dump File&#8221;.', 'tablepress' ); ?>
<br />
	<?php printf( __( 'Before doing this, it is highly recommended to read the <a href="%s">migration guide</a> on the TablePress website.', 'tablepress' ), 'https://tablepress.org/migration-from-wp-table-reloaded/' ); ?>
</p>
<table class="tablepress-postbox-table fixed">
<tbody>
	<tr id="row-import-wp-table-reloaded-source">
		<th class="column-1" scope="row"><?php _e( 'Import Source', 'tablepress' ); ?>:</th>
		<td class="column-2">
			<label for="import-wp-table-reloaded-source-db"><input name="import[wp_table_reloaded][source]" id="import-wp-table-reloaded-source-db" type="radio" value="db" <?php
				checked( $data['import_wp_table_reloaded_source'], 'db', true );
				disabled( $data['wp_table_reloaded_installed'], false, true );
			?> /> <?php _e( 'WordPress database', 'tablepress' ); ?></label>
			<label for="import-wp-table-reloaded-source-dump-file"><input name="import[wp_table_reloaded][source]" id="import-wp-table-reloaded-source-dump-file" type="radio" value="dump-file"<?php checked( $data['import_wp_table_reloaded_source'], 'dump-file', true ); ?> /> <?php _e( 'WP-Table Reloaded Dump File', 'tablepress' ); ?></label>
		</td>
	</tr>
	<tr id="row-import-wp-table-reloaded-source-dump-file" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-wp-table-reloaded-dump-file"><?php _e( 'Select file', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input name="import_wp_table_reloaded_file_upload" id="tables-import-wp-table-reloaded-dump-file" type="file" class="large-text" style="box-sizing: border-box;" />
		</td>
	</tr>
	<tr id="row-import-wp-table-reloaded-source-db" class="bottom-border">
		<th class="column-1 top-align" scope="row" style="padding:2px;"></th>
		<td class="column-2" style="padding:2px;"></td>
	</tr>
	<tr class="top-border">
		<th class="column-1" scope="row"><?php _e( 'Import tables', 'tablepress' ); ?>:</th>
		<td class="column-2"><label for="import-wp-table-reloaded-tables"> <input type="checkbox" id="import-wp-table-reloaded-tables" name="import[wp_table_reloaded][tables]" value="true" checked="checked" /> <?php _e( 'Import all tables and their settings from WP-Table Reloaded.', 'tablepress' ); ?> <span class="description"><?php _e( '(recommended)', 'tablepress' ); ?></span></label></td>
	</tr>
	<tr class="bottom-border">
		<th class="column-1" scope="row"><?php _e( 'Import styling', 'tablepress' ); ?>:</th>
		<td class="column-2"><label for="import-wp-table-reloaded-css"> <input type="checkbox" id="import-wp-table-reloaded-css" name="import[wp_table_reloaded][css]" value="true" checked="checked" /> <?php _e( 'Try to automatically convert the &#8220;Custom CSS&#8221; code from the &#8220;Plugin Options&#8221; screen of WP-Table Reloaded.', 'tablepress' ); ?></label></td>
	</tr>
	<tr class="top-border">
		<th class="column-1" scope="row"></th>
		<td class="column-2"><input type="submit" value="<?php echo esc_attr_x( 'Import from WP-Table Reloaded', 'button', 'tablepress' ); ?>" class="button button-large" id="submit_wp_table_reloaded_import" name="submit_wp_table_reloaded_import" /></td>
	</tr>
</tbody>
</table>
		<?php
	}

	/**
	 * Set the content for the WP feature pointer about the WP-Table Reloaded import feature.
	 *
	 * @since 1.0.0
	 */
	public function wp_pointer_tp100_wp_table_reloaded_import() {
		if ( ! $this->data['wp_table_reloaded_installed'] ) {
			return;
		}

		$content  = '<h3>' . __( 'TablePress Feature: Import from WP-Table Reloaded', 'tablepress' ) . '</h3>';
		$content .= '<p>' . __( 'You can import your existing tables and &#8220;Custom CSS&#8221; from WP-Table Reloaded into TablePress.', 'tablepress' ) . '</p>';

		$this->admin_page->print_wp_pointer_js( 'tp100_wp_table_reloaded_import', '#tablepress_import-import-wp-table-reloaded', array(
			'content'  => $content,
			'position' => array( 'edge' => 'bottom', 'align' => 'left', 'offset' => '16 -16' ),
		) );
	}

} // class TablePress_Import_View
