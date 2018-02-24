<?php
/**
 * Editor Button Thickbox List View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Editor Button Thickbox List View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Editor_Button_Thickbox_View extends TablePress_View {

	/**
	 * Object for the Editor Button Thickbox List Table.
	 *
	 * @since 1.0.0
	 * @var TablePress_Editor_Button_Thickbox_List_Table
	 */
	protected $wp_list_table;

	/**
	 * Initialize the View class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Intentionally left empty, to void code from parent::__construct().
	}

	/**
	 * Set up the view with data and do things that are specific for this view.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view.
	 * @param array  $data   Data for this view.
	 */
	public function setup( $action, array $data ) {
		$this->action = $action;
		$this->data = $data;

		$this->wp_list_table = new TablePress_Editor_Button_Thickbox_List_Table();
		$this->wp_list_table->set_items( $this->data['table_ids'] );
		$this->wp_list_table->prepare_items();
	}

	/**
	 * Render the current view.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		_wp_admin_html_begin();

		wp_print_styles( 'colors' );
		wp_print_scripts( 'jquery-core' );
?>
<title><?php printf( __( '%1$s &lsaquo; %2$s', 'tablepress' ), __( 'List of Tables', 'tablepress' ), 'TablePress' ); ?></title>
<style type="text/css">
/* Account for .wp-toolbar */
html {
	padding-top: 0 !important;
}
body {
	margin: 0 0 15px 15px;
}

/* Fix search field positioning */
#tablepress-page .search-box {
	position: relative;
	height: auto;
	width: auto;
	float: right;
	clear: none;
	margin: 0;
}
#tablepress-page .subtitle {
	float: left;
	padding: 10px 0 0;
}
#tablepress-page .search-box input[name="s"] {
	float: left;
	width: auto;
}

/* Fix pagination layout */
#tablepress-page .tablenav-pages {
	text-align: left;
}
#tablepress-page .tablenav .tablenav-pages a {
	padding: 5px 12px;
	font-size: 16px;
}
#tablepress-page .tablenav-pages .pagination-links .paging-input {
	font-size: 16px;
}

#tablepress-page .tablenav-pages .pagination-links .current-page {
	padding: 4px;
	font-size: 16px;
}

/* Width and font weight for the columns */
.tablepress-editor-button-list thead .column-table_id {
	width: 50px;
}
.tablepress-editor-button-list tbody .column-table_id,
.tablepress-editor-button-list tbody .column-table_name {
	font-weight: bold;
}
.tablepress-editor-button-list thead .column-table_action {
	width: 150px;
}
/* Responsiveness on the All Tables screen */
@media screen and (max-width: 782px) {
	.tablepress-editor-button-list .column-table_id {
		display: none !important;
		padding: 3px 8px 3px 35%;
	}
}

/* Shortcode input field */
#tablepress-page .table-shortcode-inline {
	background: transparent;
	border: none;
	color: #333333;
	width: 110px;
	margin: 0;
	padding: 0;
	font-weight: bold;
	font-size: 14px;
	-webkit-box-shadow: none;
	box-shadow: none;
	text-align: center;
	vertical-align: top;
}
#tablepress-page .table-shortcode {
	cursor: text;
}
<?php if ( is_rtl() ) : ?>
/* RTL CSS */
body.rtl {
	margin: 0 15px 15px 0;
}
.rtl #tablepress-page .search-box {
	float: left;
}
.rtl #tablepress-page .subtitle {
	float: right;
}
.rtl #tablepress-page .search-box input[name="s"] {
	float: right;
}
.rtl #tablepress-page .table-shortcode-inline {
	width: 125px;
	font-size: 13px;
	vertical-align: baseline;
}
<?php endif; ?>
</style>
</head>
<body class="wp-admin wp-core-ui js iframe<?php echo is_rtl() ? ' rtl' : ''; ?>">
<div id="tablepress-page" class="wrap">
<h1><?php printf( __( '%1$s &lsaquo; %2$s', 'tablepress' ), __( 'List of Tables', 'tablepress' ), 'TablePress' ); ?></h1>
<div id="poststuff">
<p>
<?php _e( 'This is a list of all available tables.', 'tablepress' ); ?> <?php _e( 'You may insert a table into a post or page here.', 'tablepress' ); ?>
</p><p>
<?php printf( __( 'Click the &#8220;%1$s&#8221; button for the desired table to automatically insert the<br />corresponding Shortcode (%2$s) into the editor.', 'tablepress' ), __( 'Insert Shortcode', 'tablepress' ), '<input type="text" class="table-shortcode table-shortcode-inline ltr" value="' . esc_attr( '[' . TablePress::$shortcode . ' id=<ID> /]' ) . '" readonly="readonly" />' ); ?>
</p>
<?php
	if ( ! empty( $_GET['s'] ) ) {
		printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;', 'tablepress' ) . '</span>', esc_html( wp_unslash( $_GET['s'] ) ) );
	}
?>
<form method="get" action="">
	<input type="hidden" name="action" value="tablepress_<?php echo $this->action; ?>" />
<?php
	wp_nonce_field( TablePress::nonce( $this->action ), '_wpnonce', false );
	$this->wp_list_table->search_box( __( 'Search Tables', 'tablepress' ), 'tables_search' );
?>
</form>
	<?php $this->wp_list_table->display(); ?>
</div>
</div>
<script type="text/javascript">
jQuery( document ).ready( function( $ ) {
	// Toggle list table rows on small screens
	$( '.tablepress-editor-button-list' )
	.on( 'click', '.toggle-row', function() {
		$( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
	})
	.on( 'click', '.insert-shortcode', function() {
		var win = window.dialogArguments || opener || parent || top;
		win.send_to_editor( $(this).attr( 'title' ) );
	} );
} );
</script>
</body>
</html>
<?php
	}

} // class TablePress_Editor_Button_View

/**
 * TablePress Editor Button Thickbox List Table Class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @link https://codex.wordpress.org/Class_Reference/WP_List_Table
 * @since 1.0.0
 */
class TablePress_Editor_Button_Thickbox_List_Table extends WP_List_Table {

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
		parent::__construct( array(
			'singular' => 'tablepress-table',              // Singular name of the listed records.
			'plural'   => 'tablepress-editor-button-list', // Plural name of the listed records.
			'ajax'     => false,                           // Does this list table support AJAX?
			'screen'   => get_current_screen(),            // WP_Screen object.
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
			// "name" is special in WP, which is why we prefix every entry here, to be safe!
			'table_id'          => __( 'ID', 'tablepress' ),
			'table_name'        => __( 'Table Name', 'tablepress' ),
			'table_description' => __( 'Description', 'tablepress' ),
			'table_action'      => __( 'Action', 'tablepress' ),
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
			'table_id'          => array( 'id', true ), // true means its already sorted
			'table_name'        => array( 'name', false ),
			'table_description' => array( 'description', false ),
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
		if ( '' === trim( $item['name'] ) ) {
			$item['name'] = __( '(no name)', 'tablepress' );
		}
		return esc_html( $item['name'] );
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
	 * Render a cell in the "table_action" column, i.e. the "Insert" link.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row.
	 * @return string HTML content of the cell.
	 */
	protected function column_table_action( array $item ) {
		return '<input type="button" class="insert-shortcode button" title="' . esc_attr( '[' . TablePress::$shortcode . " id={$item['id']} /]" ) . '" value="' . esc_attr__( 'Insert Shortcode', 'tablepress' ) . '" />';
	}

	/**
	 * Holds the message to be displayed when there are no items in the table.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		_e( 'No tables found.', 'tablepress' );
		if ( 0 === $this->items_count ) {
			echo ' ' . __( 'You should add or import a table on the TablePress screens to get started!', 'tablepress' );
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

		// Load table again, with table data, but without options and visibility settings.
		$item = TablePress::$model_table->load( $item, true, false );

		if ( isset( $item['is_corrupted'] ) && $item['is_corrupted'] ) {
			return false; // Don't search corrupted tables
		}

		// Search from easy to hard, so that "expensive" code maybe doesn't have to run.
		if ( false !== stripos( $item['id'], $term )
		|| false !== stripos( $item['name'], $term )
		|| false !== stripos( $item['description'], $term )
		|| false !== stripos( TablePress::get_user_display_name( $item['author'] ), $term )
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

		if ( $item_a[ $orderby ] === $item_b[ $orderby ] ) {
			return 0;
		}

		// Fields in this list table are all strings.
		$result = strnatcasecmp( $item_a[ $orderby ], $item_b[ $orderby ] );

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
			// Don't load data nor table options.
			$item = TablePress::$model_table->load( $item, false, false );
		}
		// Break reference in foreach iterator.
		unset( $item );

		// Maybe sort the items.
		$_sortable_columns = $this->get_sortable_columns();
		if ( $orderby && ! empty( $this->items ) && isset( $_sortable_columns[ "table_{$orderby}" ] ) ) {
			usort( $this->items, array( $this, '_order_callback' ) );
		}

		// Number of records to show per page.
		$per_page = 15; // hard-coded, as there's no possibility to change this in the Thickbox
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

} // class TablePress_Editor_Button_Thickbox_List_Table
