<?php
/**
 * List Tables View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * List Tables View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_List_View extends TablePress_View {

	/**
	 * Object for the All Tables List Table.
	 *
	 * @since 1.0.0
	 * @var TablePress_All_Tables_List_Table
	 */
	protected $wp_list_table;

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

		add_thickbox();
		$this->admin_page->enqueue_script( 'list', array( 'jquery-core' ), array(
			'list' => array(
				'shortcode_popup'                  => __( 'To embed this table into a post or page, use this Shortcode:', 'tablepress' ),
				'donation-message-already-donated' => __( 'Thank you very much! Your donation is highly appreciated. You just contributed to the further development of TablePress!', 'tablepress' ),
				'donation-message-maybe-later'     => sprintf( __( 'No problem! I still hope you enjoy the benefits that TablePress adds to your site. If you should change your mind, you&#8217;ll always find the &#8220;Donate&#8221; button on the <a href="%s">TablePress website</a>.', 'tablepress' ), 'https://tablepress.org/' ),
			),
		) );

		if ( $data['messages']['first_visit'] ) {
			$this->add_header_message(
				'<strong><em>' . __( 'Welcome!', 'tablepress' ) . '</em></strong><br />'
				. __( 'Thank you for using TablePress for the first time!', 'tablepress' ) . ' '
				. sprintf( __( 'If you encounter any questions or problems, please visit the <a href="%1$s">FAQ</a>, the <a href="%2$s">documentation</a>, and the <a href="%3$s">Support</a> section on the <a href="%4$s">plugin website</a>.', 'tablepress' ), 'https://tablepress.org/faq/', 'https://tablepress.org/documentation/', 'https://tablepress.org/support/', 'https://tablepress.org/' ) . '<br /><br />'
				. $this->ajax_link( array( 'action' => 'hide_message', 'item' => 'first_visit', 'return' => 'list' ), __( 'Hide this message', 'tablepress' ) ),
				'notice-info not-dismissible'
			);
		}

		if ( $data['messages']['wp_table_reloaded_warning'] ) {
			$this->add_header_message(
				'<strong><em>' . __( 'Attention!', 'tablepress' ) . '</em></strong><br />'
				. __( 'You have activated the plugin WP-Table Reloaded, which can not be used together with TablePress.', 'tablepress' ) . '<br />'
				. __( 'It is strongly recommended that you switch from WP-Table Reloaded to TablePress, which not only fixes many problems, but also has more and better features than WP-Table Reloaded.', 'tablepress' ) . '<br />'
				. sprintf( __( 'Please follow the <a href="%s">migration guide</a> to move your tables and then deactivate WP-Table Reloaded!', 'tablepress' ), 'https://tablepress.org/migration-from-wp-table-reloaded/' ) . '<br />'
				. '<a href="' . TablePress::url( array( 'action' => 'import' ) ) . '" class="button button-primary button-large" style="color:#ffffff;margin-top:5px;">' . __( 'Import your tables from WP-Table Reloaded', 'tablepress' ) . '</a>',
				'notice-error not-dismissible'
			);
		}

		if ( $data['messages']['donation_message'] ) {
			$this->add_header_message(
				'<img alt="' . esc_attr__( 'Tobias Bäthge, developer of TablePress', 'tablepress' ) . '" src="https://secure.gravatar.com/avatar/50f1cff2e27a1f522b18ce229c057bc5?s=110" height="110" width="110" style="float:left;margin:1px 10px 40px 0;" />'
				. __( 'Hi, my name is Tobias, I&#8217;m the developer of the TablePress plugin.', 'tablepress' ) . '<br /><br />'
				. __( 'Thanks for using it! You&#8217;ve installed TablePress over a month ago.', 'tablepress' ) . ' '
				. sprintf( _n( 'If everything works and you are satisfied with the results of managing your %s table, isn&#8217;t that worth a coffee or two?', 'If everything works and you are satisfied with the results of managing your %s tables, isn&#8217;t that worth a coffee or two?', $data['table_count'], 'tablepress' ), $data['table_count'] ) . '<br />'
				. sprintf( __( '<a href="%s">Donations</a> help me to continue user support and development of this <em>free</em> software &mdash; things for which I spend countless hours of my free time! Thank you very much!', 'tablepress' ), 'https://tablepress.org/donate/' ) . '<br /><br />'
				. __( 'Sincerly, Tobias', 'tablepress' ) . '<br /><br />'
				. sprintf( '<a href="%s" target="_blank"><strong>%s</strong></a>', 'https://tablepress.org/donate/', __( 'Sure, I&#8217;ll buy you a coffee and support TablePress!', 'tablepress' ) ) . '&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;&nbsp;&nbsp;&nbsp;'
				. $this->ajax_link( array( 'action' => 'hide_message', 'item' => 'donation_nag', 'return' => 'list', 'target' => 'already-donated' ), __( 'I already donated.', 'tablepress' ) ) . '&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;&nbsp;&nbsp;&nbsp;'
				. $this->ajax_link( array( 'action' => 'hide_message', 'item' => 'donation_nag', 'return' => 'list', 'target' => 'maybe-later' ), __( 'No, thanks. Don&#8217;t ask again.', 'tablepress' ) ),
				'notice-success not-dismissible'
			);
		}

		if ( $data['messages']['plugin_update_message'] ) {
			$this->add_header_message(
				'<strong><em>' . sprintf( __( 'Thank you for updating to TablePress %s!', 'tablepress' ), TablePress::version ) . '</em></strong><br />'
				. sprintf( __( 'Please read the <a href="%s">release announcement</a> for more information.', 'tablepress' ), 'https://tablepress.org/news/' ) . ' '
				. sprintf( __( 'If you like the new features and enhancements, <a href="%s">giving a donation</a> towards the further support and development of TablePress is recommended. Thank you!', 'tablepress' ), 'https://tablepress.org/donate/' )
				. '<br /><br />'
				. $this->ajax_link( array( 'action' => 'hide_message', 'item' => 'plugin_update', 'return' => 'list' ), __( 'Hide this message', 'tablepress' ) ),
				'notice-info not-dismissible'
			);
		}

		$this->process_action_messages( array(
			'success_delete'                   => _n( 'The table was deleted successfully.', 'The tables were deleted successfully.', 1, 'tablepress' ),
			'success_delete_plural'            => _n( 'The table was deleted successfully.', 'The tables were deleted successfully.', 2, 'tablepress' ),
			'error_delete'                     => __( 'Error: The table could not be deleted.', 'tablepress' ),
			'error_save'                       => __( 'Error: The table could not be saved.', 'tablepress' ),
			'success_copy'                     => _n( 'The table was copied successfully.', 'The tables were copied successfully.', 1, 'tablepress' ) . ( ( false !== $data['table_id'] ) ? ' ' . sprintf( __( 'The copied table has the table ID &#8220;%s&#8221;.', 'tablepress' ), esc_html( $data['table_id'] ) ) : '' ),
			'success_copy_plural'              => _n( 'The table was copied successfully.', 'The tables were copied successfully.', 2, 'tablepress' ),
			'error_copy'                       => __( 'Error: The table could not be copied.', 'tablepress' ),
			'error_no_table'                   => __( 'Error: You did not specify a valid table ID.', 'tablepress' ),
			'error_load_table'                 => __( 'Error: This table could not be loaded!', 'tablepress' ),
			'error_bulk_action_invalid'        => __( 'Error: This bulk action is invalid!', 'tablepress' ),
			'error_no_selection'               => __( 'Error: You did not select any tables!', 'tablepress' ),
			'error_delete_not_all_tables'      => __( 'Notice: Not all selected tables could be deleted!', 'tablepress' ),
			'error_copy_not_all_tables'        => __( 'Notice: Not all selected tables could be copied!', 'tablepress' ),
			'success_import'                   => __( 'The tables were imported successfully.', 'tablepress' ),
			'success_import_wp_table_reloaded' => __( 'The tables were imported successfully from WP-Table Reloaded.', 'tablepress' ),
		) );

		$this->add_text_box( 'head', array( $this, 'textbox_head' ), 'normal' );
		$this->add_text_box( 'tables-list', array( $this, 'textbox_tables_list' ), 'normal' );

		add_screen_option( 'per_page', array( 'label' => __( 'Tables', 'tablepress' ), 'default' => 20 ) ); // Admin_Controller contains function to allow changes to this in the Screen Options to be saved
		$this->wp_list_table = new TablePress_All_Tables_List_Table();
		$this->wp_list_table->set_items( $this->data['table_ids'] );
		$this->wp_list_table->prepare_items();

		// Cleanup Request URI string, which WP_List_Table uses to generate the sort URLs.
		$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'message', 'table_id' ), $_SERVER['REQUEST_URI'] );
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

			// For this screen, this is done in textbox_tables_list(), to get the fields into the correct <form>:
			// $this->do_text_boxes( 'header' );
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
			<?php _e( 'This is a list of your tables.', 'tablepress' ); ?>
			<?php _e( 'Click the corresponding links within the list to edit, copy, delete, or preview a table.', 'tablepress' ); ?>
		</p>
		<p>
			<?php printf( __( 'To insert a table into a page, post, or text widget, copy its Shortcode %s and paste it at the desired place in the editor.', 'tablepress' ), '<input type="text" class="table-shortcode table-shortcode-inline" value="' . esc_attr( '[' . TablePress::$shortcode . ' id=<ID> /]' ) . '" readonly="readonly" />' ); ?>
			<?php _e( 'Each table has a unique ID that needs to be adjusted in that Shortcode.', 'tablepress' ); ?>
			<?php printf( __( 'You can also click the &#8220;%s&#8221; button in the editor toolbar to select and insert a table.', 'tablepress' ), __( 'Table', 'tablepress' ) ); ?>
		</p>
		<?php
	}

	/**
	 * Print the content of the "All Tables" text box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data for this screen.
	 * @param array $box  Information about the text box.
	 */
	public function textbox_tables_list( array $data, array $box ) {
		if ( ! empty( $_GET['s'] ) ) {
			printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;', 'tablepress' ) . '</span>', esc_html( wp_unslash( $_GET['s'] ) ) );
		}
	?>
<form method="get" action="">
	<?php
	if ( isset( $_GET['page'] ) ) {
		echo '<input type="hidden" name="page" value="' . esc_attr( $_GET['page'] ) . '" />' . "\n";
	}
	$this->wp_list_table->search_box( __( 'Search Tables', 'tablepress' ), 'tables_search' );
	?>
</form>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<?php
		// This prints the nonce and action fields for this screen (done here instead of render(), due to moved <form>).
		$this->do_text_boxes( 'header' );
		$this->wp_list_table->display();
	?>
</form>
	<?php
	}

	/**
	 * Create HTML code for an AJAXified link.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $params Parameters for the URL.
	 * @param string $text   Text for the link.
	 * @return string HTML code for the link.
	 */
	protected function ajax_link( array $params = array( 'action' => 'list', 'item' => '' ), $text ) {
		$url = TablePress::url( $params, true, 'admin-post.php' );
		$action = esc_attr( $params['action'] );
		$item = esc_attr( $params['item'] );
		$target = isset( $params['target'] ) ? esc_attr( $params['target'] ) : '';
		return "<a class=\"ajax-link\" href=\"{$url}\" data-action=\"{$action}\" data-item=\"{$item}\" data-target=\"{$target}\">{$text}</a>";
	}

} // class TablePress_List_View

/**
 * TablePress All Tables List Table Class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @link https://codex.wordpress.org/Class_Reference/WP_List_Table
 * @since 1.0.0
 */
class TablePress_All_Tables_List_Table extends WP_List_Table {

	/**
	 * Number of items of the initial data set (before sort, search, and pagination).
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $items_count = 0;

	/**
	 * Initialize the List Table.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$screen = get_current_screen();

		// Hide "Last Modified By" column by default.
		if ( false === get_user_option( 'manage' . $screen->id . 'columnshidden' ) ) {
			update_user_option( get_current_user_id(), 'manage' . $screen->id . 'columnshidden', array( 'table_last_modified_by' ), true );
		}

		parent::__construct( array(
			'singular' => 'tablepress-table',      // Singular name of the listed records.
			'plural'   => 'tablepress-all-tables', // Plural name of the listed records.
			'ajax'     => false,                   // Does this list table support AJAX?
			'screen'   => $screen,                 // WP_Screen object.
		) );
	}

	/**
	 * Set the data items (here: tables) that are to be displayed by the List Tables, and their original count.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items Tables to be displayed in the List Table.
	 */
	public function set_items( array $items ) {
		$this->items = $items;
		$this->items_count = count( $items );
	}

	/**
	 * Check whether the user has permissions for certain AJAX actions.
	 * (not used, but must be implemented in this child class)
	 *
	 * @since 1.0.0
	 *
	 * @return bool true (Default value).
	 */
	public function ajax_user_can() {
		return true;
	}

	/**
	 * Get a list of columns in this List Table.
	 *
	 * Format: 'internal-name' => 'Column Title'.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of columns in this List Table.
	 */
	public function get_columns() {
		$columns = array(
			'cb'                     => $this->has_items() ? '<input type="checkbox" />' : '', // Checkbox for "Select all", but only if there are items in the table.
			// "name" is special in WP, which is why we prefix every entry here, to be safe!
			'table_id'               => __( 'ID', 'tablepress' ),
			'table_name'             => __( 'Table Name', 'tablepress' ),
			'table_description'      => __( 'Description', 'tablepress' ),
			'table_author'           => __( 'Author', 'tablepress' ),
			'table_last_modified_by' => __( 'Last Modified By', 'tablepress' ),
			'table_last_modified'    => __( 'Last Modified', 'tablepress' ),
		);
		return $columns;
	}

	/**
	 * Get a list of columns that are sortable.
	 *
	 * Format: 'internal-name' => array( $field for $item[ $field ], true for already sorted ).
	 *
	 * @since 1.0.0
	 *
	 * @return array List of sortable columns in this List Table.
	 */
	protected function get_sortable_columns() {
		// No sorting on the Empty List placeholder.
		if ( ! $this->has_items() ) {
			return array();
		}

		$sortable_columns = array(
			'table_id'               => array( 'id', true ), // true means its already sorted
			'table_name'             => array( 'name', false ),
			'table_description'      => array( 'description', false ),
			'table_author'           => array( 'author', false ),
			'table_last_modified_by' => array( 'last_modified_by', false ),
			'table_last_modified'    => array( 'last_modified', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 1.7.0
	 *
	 * @return string Name of the default primary column, in this case, the table name.
	 */
	protected function get_default_primary_column_name() {
		return 'table_name';
	}

	/**
	 * Render a cell in the "cb" column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_cb( /* array */ $item ) { // No `array` type hint to prevent a Strict Standards notice, as the method is inherited.
		$user_can_copy_table = current_user_can( 'tablepress_copy_table', $item['id'] );
		$user_can_delete_table = current_user_can( 'tablepress_delete_table', $item['id'] );
		$user_can_export_table = current_user_can( 'tablepress_export_table', $item['id'] );

		if ( $user_can_copy_table || $user_can_delete_table || $user_can_export_table ) {
			return '<input type="checkbox" name="table[]" value="' . esc_attr( $item['id'] ) . '" />';
		} else {
			return '';
		}
	}

	/**
	 * Render a cell in the "table_id" column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_table_id( array $item ) {
		return esc_html( $item['id'] );
	}

	/**
	 * Render a cell in the "table_name" column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_table_name( array $item ) {
		$user_can_edit_table = current_user_can( 'tablepress_edit_table', $item['id'] );
		$user_can_copy_table = current_user_can( 'tablepress_copy_table', $item['id'] );
		$user_can_export_table = current_user_can( 'tablepress_export_table', $item['id'] );
		$user_can_delete_table = current_user_can( 'tablepress_delete_table', $item['id'] );
		$user_can_preview_table = current_user_can( 'tablepress_preview_table', $item['id'] );

		$edit_url = TablePress::url( array( 'action' => 'edit', 'table_id' => $item['id'] ) );
		$copy_url = TablePress::url( array( 'action' => 'copy_table', 'item' => $item['id'], 'return' => 'list', 'return_item' => $item['id'] ), true, 'admin-post.php' );
		$export_url = TablePress::url( array( 'action' => 'export', 'table_id' => $item['id'] ) );
		$delete_url = TablePress::url( array( 'action' => 'delete_table', 'item' => $item['id'], 'return' => 'list', 'return_item' => $item['id'] ), true, 'admin-post.php' );
		$preview_url = TablePress::url( array( 'action' => 'preview_table', 'item' => $item['id'], 'return' => 'list', 'return_item' => $item['id'] ), true, 'admin-post.php' );

		if ( '' === trim( $item['name'] ) ) {
			$item['name'] = __( '(no name)', 'tablepress' );
		}

		if ( $user_can_edit_table ) {
			$row_text = '<strong><a title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'tablepress' ), esc_attr( $item['name'] ) ) ) . '" class="row-title" href="' . $edit_url . '">' . esc_html( $item['name'] ) . '</a></strong>';
		} else {
			$row_text = '<strong>' . esc_html( $item['name'] ) . '</strong>';
		}

		$row_actions = array();
		if ( $user_can_edit_table ) {
			$row_actions['edit'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $edit_url, esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'tablepress' ), $item['name'] ) ), __( 'Edit', 'tablepress' ) );
		}
		$row_actions['shortcode hide-if-no-js'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', '#', esc_attr( '[' . TablePress::$shortcode . " id={$item['id']} /]" ), __( 'Show Shortcode', 'tablepress' ) );
		if ( $user_can_copy_table ) {
			$row_actions['copy'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $copy_url, esc_attr( sprintf( __( 'Copy &#8220;%s&#8221;', 'tablepress' ), $item['name'] ) ), __( 'Copy', 'tablepress' ) );
		}
		if ( $user_can_export_table ) {
			$row_actions['export'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $export_url, esc_attr( sprintf( __( 'Export &#8220;%s&#8221;', 'tablepress' ), $item['name'] ) ), _x( 'Export', 'row action', 'tablepress' ) );
		}
		if ( $user_can_delete_table ) {
			$row_actions['delete'] = sprintf( '<a href="%1$s" title="%2$s" class="delete-link">%3$s</a>', $delete_url, esc_attr( sprintf( __( 'Delete &#8220;%s&#8221;', 'tablepress' ), $item['name'] ) ), __( 'Delete', 'tablepress' ) );
		}
		if ( $user_can_preview_table ) {
			$row_actions['table-preview'] = sprintf( '<a href="%1$s" title="%2$s" target="_blank">%3$s</a>', $preview_url, esc_attr( sprintf( __( 'Show a preview of &#8220;%s&#8221;', 'tablepress' ), $item['name'] ) ), __( 'Preview', 'tablepress' ) );
		}

		return $row_text . $this->row_actions( $row_actions );
	}

	/**
	 * Render a cell in the "table_description" column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_table_description( array $item ) {
		if ( '' === trim( $item['description'] ) ) {
			$item['description'] = __( '(no description)', 'tablepress' );
		}
		return esc_html( $item['description'] );
	}

	/**
	 * Render a cell in the "table_author" column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_table_author( array $item ) {
		return TablePress::get_user_display_name( $item['author'] );
	}

	/**
	 * Render a cell in the "last_modified_by" column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_table_last_modified_by( array $item ) {
		return TablePress::get_user_display_name( $item['options']['last_editor'] );
	}

	/**
	 * Render a cell in the "table_last_modified" column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_table_last_modified( array $item ) {
		$modified_timestamp = strtotime( $item['last_modified'] );
		$current_timestamp = current_time( 'timestamp' );
		$time_diff = $current_timestamp - $modified_timestamp;
		// Time difference is only shown up to one day.
		if ( $time_diff >= 0 && $time_diff < DAY_IN_SECONDS ) {
			$time_diff = sprintf( __( '%s ago', 'tablepress' ), human_time_diff( $modified_timestamp, $current_timestamp ) );
		} else {
			$time_diff = TablePress::format_datetime( $item['last_modified'], 'mysql', '<br />' );
		}

		$readable_time = TablePress::format_datetime( $item['last_modified'], 'mysql', ' ' );
		return '<abbr title="' . esc_attr( $readable_time ) . '">' . $time_diff . '</abbr>';
	}

	/**
	 * Handles output for the default column.
	 *
	 * @since 1.8.0
	 *
	 * @param array  $item        Data item for the current row.
	 * @param string $column_name Current column name.
	 */
	protected function column_default( /* array */ $item, $column_name ) { // No `array` type hint to prevent a Strict Standards notice, as the method is inherited.
		/**
		 * Fires inside each custom column of the TablePress list table.
		 *
		 * @since 1.8.0
		 *
		 * @param array  $column_name Current column name.
		 * @param string $item        Data item for the current row.
		 */
		do_action( 'manage_tablepress_list_custom_column', $column_name, $item );
	}

	/**
	 * Get a list (name => title) bulk actions that are available.
	 *
	 * @since 1.0.0
	 *
	 * @return array Bulk actions for this table.
	 */
	protected function get_bulk_actions() {
		$bulk_actions = array();

		if ( current_user_can( 'tablepress_copy_tables' ) ) {
			$bulk_actions['copy'] = _x( 'Copy', 'bulk action', 'tablepress' );
		}
		if ( current_user_can( 'tablepress_export_tables' ) ) {
			$bulk_actions['export'] = _x( 'Export', 'bulk action', 'tablepress' );
		}
		if ( current_user_can( 'tablepress_delete_tables' ) ) {
			$bulk_actions['delete'] = _x( 'Delete', 'bulk action', 'tablepress' );
		}

		return $bulk_actions;
	}

	/**
	 * Render the bulk actions dropdown.
	 *
	 * In comparison with parent class, this has modified HTML (especially no field named "action" as that's being used already)!
	 *
	 * @since 1.0.0
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 *                      This is designated as optional for backwards-compatibility.
	 */
	protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$no_new_actions = $this->_actions = $this->get_bulk_actions();
			/** This filter is documented in the WordPress function WP_List_Table::bulk_actions() in wp-admin/includes/class-wp-list-table.php */
			$this->_actions = apply_filters( 'bulk_actions-' . $this->screen->id, $this->_actions );
			$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) ) {
			return;
		}

		$name_id = "bulk-action-{$which}";
		echo "<label for='{$name_id}' class='screen-reader-text'>" . __( 'Select Bulk Action', 'tablepress' ) . "</label>\n";
		echo "<select name='{$name_id}' id='{$name_id}'>\n";
		echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions', 'tablepress' ) . "</option>\n";
		foreach ( $this->_actions as $name => $title ) {
			echo "\t<option value='{$name}'>{$title}</option>\n";
		}
		echo "</select>\n";
		submit_button( __( 'Apply', 'tablepress' ), 'action', '', false, array( 'id' => "doaction{$two}" ) );
		echo "\n";
	}

	/**
	 * Holds the message to be displayed when there are no items in the table.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		_e( 'No tables found.', 'tablepress' );
		if ( 0 === $this->items_count ) {
			$user_can_add_tables = current_user_can( 'tablepress_add_tables' );
			$user_can_import_tables = current_user_can( 'tablepress_import_tables' );

			$add_url = TablePress::url( array( 'action' => 'add' ) );
			$import_url = TablePress::url( array( 'action' => 'import' ) );

			if ( $user_can_add_tables && $user_can_import_tables ) {
				echo ' ' . sprintf( __( 'You should <a href="%1$s">add</a> or <a href="%2$s">import</a> a table to get started!', 'tablepress' ), $add_url, $import_url );
			} elseif ( $user_can_add_tables ) {
				echo ' ' . sprintf( __( 'You should <a href="%s">add</a> a table to get started!', 'tablepress' ), $add_url );
			} elseif ( $user_can_import_tables ) {
				echo ' ' . sprintf( __( 'You should <a href="%s">import</a> a table to get started!', 'tablepress' ), $import_url );
			}
		}
	}

	/**
	 * Generate the elements above or below the table (like bulk actions and pagination).
	 *
	 * In comparison with parent class, this has modified HTML (no nonce field), and a check whether there are items.
	 *
	 * @since 1.0.0
	 *
	 * @param string $which Location ("top" or "bottom").
	 */
	protected function display_tablenav( $which ) {
		if ( ! $this->has_items() ) {
			return;
		}
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<div class="alignleft actions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
		<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
		?>
			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Callback to determine whether the given $item contains the search term.
	 *
	 * @since 1.0.0
	 *
	 * @param string $item Table ID that shall be searched.
	 * @return bool Whether the search term was found or not.
	 */
	protected function _search_callback( $item ) {
		static $term, $json_encoded_term;
		if ( is_null( $term ) || is_null( $json_encoded_term ) ) {
			$term = wp_unslash( $_GET['s'] );
			$json_encoded_term = substr( wp_json_encode( $term ), 1, -1 );
		}

		static $debug;
		if ( is_null( $debug ) ) {
			// Set debug variable to allow searching in corrupted tables.
			$debug = isset( $_GET['debug'] ) ? ( 'true' === $_GET['debug'] ) : WP_DEBUG;
		}

		// load table again, with data and options (for last_editor).
		$item = TablePress::$model_table->load( $item, true, true );

		// Don't search corrupted tables, except when debug mode is enabled via $_GET parameter or WP_DEBUG constant.
		if ( ! $debug && isset( $item['is_corrupted'] ) && $item['is_corrupted'] ) {
			return false;
		}

		// Search from easy to hard, so that "expensive" code maybe doesn't have to run.
		if ( false !== stripos( $item['id'], $term )
		|| false !== stripos( $item['name'], $term )
		|| false !== stripos( $item['description'], $term )
		|| false !== stripos( TablePress::get_user_display_name( $item['author'] ), $term )
		|| false !== stripos( TablePress::get_user_display_name( $item['options']['last_editor'] ), $term )
		|| false !== stripos( TablePress::format_datetime( $item['last_modified'], 'mysql', ' ' ), $term )
		|| false !== stripos( wp_json_encode( $item['data'] ), $json_encoded_term ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Callback to for the array sort function.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item_a First item that shall be compared to.
	 * @param array $item_b The second item for the comparison.
	 * @return int (-1, 0, 1) depending on which item sorts "higher".
	 */
	protected function _order_callback( array $item_a, array $item_b ) {
		global $orderby, $order;

		if ( 'last_modified_by' !== $orderby ) {
			if ( $item_a[ $orderby ] === $item_b[ $orderby ] ) {
				return 0;
			}
		} else {
			if ( $item_a['options']['last_editor'] === $item_b['options']['last_editor'] ) {
				return 0;
			}
		}

		// Certain fields require some extra work before being sortable.
		switch ( $orderby ) {
			case 'last_modified':
				// Compare UNIX timestamps for "last modified", which actually is a mySQL datetime string.
				$result = ( strtotime( $item_a['last_modified'] ) > strtotime( $item_b['last_modified'] ) ) ? 1 : -1;
				break;
			case 'author':
				// Get the actual author name, plain value is just the user ID.
				$result = strnatcasecmp( TablePress::get_user_display_name( $item_a['author'] ), TablePress::get_user_display_name( $item_b['author'] ) );
				break;
			case 'last_modified_by':
				// Get the actual last editor name, plain value is just the user ID.
				$result = strnatcasecmp( TablePress::get_user_display_name( $item_a['options']['last_editor'] ), TablePress::get_user_display_name( $item_b['options']['last_editor'] ) );
				break;
			default:
				// Other fields (ID, name, description) are sorted as strings.
				$result = strnatcasecmp( $item_a[ $orderby ], $item_b[ $orderby ] );
		}

		return ( 'asc' === $order ) ? $result : - $result;
	}

	/**
	 * Prepares the list of items for displaying, by maybe searching and sorting, and by doing pagination.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		global $orderby, $order, $s;
		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		// Maybe search in the items.
		if ( $s ) {
			$this->items = array_filter( $this->items, array( $this, '_search_callback' ) );
		}

		// Load actual tables after search for less memory consumption.
		foreach ( $this->items as &$item ) {
			// Don't load data, but load table options for access to last_editor.
			$item = TablePress::$model_table->load( $item, false, true );
		}
		// Break reference in foreach iterator.
		unset( $item );

		// Maybe sort the items.
		$_sortable_columns = $this->get_sortable_columns();
		if ( $orderby && ! empty( $this->items ) && isset( $_sortable_columns[ "table_{$orderby}" ] ) ) {
			usort( $this->items, array( $this, '_order_callback' ) );
		}

		// Number of records to show per page.
		$per_page = $this->get_items_per_page( 'tablepress_list_per_page', 20 ); // hard-coded, as in filter in Admin_Controller
		// Page number the user is currently viewing.
		$current_page = $this->get_pagenum();
		// Number of records in the array.
		$total_items = count( $this->items );

		// Slice items array to hold only items for the current page.
		$this->items = array_slice( $this->items, ( ( $current_page - 1 ) * $per_page ), $per_page );

		// Register pagination options and calculation results.
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // Total number of records/items
			'per_page'    => $per_page,                           // Number of items per page
			'total_pages' => ceil( $total_items / $per_page ), // Total number of pages
		) );
	}

} // class TablePress_All_Tables_List_Table
