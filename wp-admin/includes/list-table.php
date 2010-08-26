<?php

/**
 * Base class for displaying a list of items as an ajaxified html table
 *
 * @package WordPress
 * @since 3.1.0
 */
class WP_List_Table {

	/**
	 * The current list of items
	 *
	 * @since 3.1.0
	 * @var array
	 * @access protected
	 */
	var $items;

	/**
	 * Various information about the current table
	 *
	 * @since 3.1.0
	 * @var array
	 * @access private
	 */
	var $_args;

	/**
	 * Various information needed for displaying the pagination
	 *
	 * @since 3.1.0
	 * @var array
	 * @access private
	 */
	var $_pagination_args = array();

	/**
	 * The current screen
	 *
	 * @since 3.1.0
	 * @var object
	 * @access private
	 */
	var $_screen;

	/**
	 * Cached bulk actions
	 *
	 * @since 3.1.0
	 * @var array
	 * @access private
	 */
	var $_actions;

	/**
	 * Cached pagination output
	 *
	 * @since 3.1.0
	 * @var string
	 * @access private
	 */
	var $_pagination;

	/**
	 * Constructor. The child class should call this constructor from it's own constructor
	 *
	 * @param array $args An associative array with information about the current table
	 * @access protected
	 */
	function WP_List_Table( $args ) {

		$args = wp_parse_args( $args, array(
			'screen' => '',
			'plural' => '',
			'singular' => '',
			'ajax' => true
		) );

		$this->_screen = $args['screen'];

		if ( is_string( $this->_screen ) )
			$this->_screen = convert_to_screen( $this->_screen );

		if ( !$args['plural'] )
			$args['plural'] = $this->_screen->base;

		$this->_args = $args;

		if ( $args['ajax'] ) {
			wp_enqueue_script( 'list-table' );
			add_action( 'admin_footer', array( $this, '_js_vars' ) );
		}
	}

	/**
	 * Checks the current user's permissions
	 * @uses wp_die()
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function check_permissions() {
		die( 'function WP_List_Table::check_permissions() must be over-ridden in a sub-class.' );
	}

	/**
	 * Prepares the list of items for displaying.
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function prepare_items() {
		die( 'function WP_List_Table::prepare_items() must be over-ridden in a sub-class.' );
	}

	/**
	 * An internal method that sets all the necessary pagination arguments
	 *
	 * @param array $args An associative array with information about the pagination
	 * @access protected
	 */
	function set_pagination_args( $args ) {
		$args = wp_parse_args( $args, array(
			'query_var' => 'paged',
			'total_items' => 0,
			'total_pages' => 0,
			'per_page' => 0,
		) );

		if ( !$args['total_pages'] && $args['per_page'] > 0 )
			$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );

		$this->_pagination_args = $args;
	}

	/**
	 * Access the pagination args
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $key
	 * @return array
	 */
	function get_pagination_arg( $key ) {
		if ( 'page' == $key )
			return $this->get_pagenum();

		return @$this->_pagination_args[ $key ];
	}

	/**
	 * Wether the table has items to display or not
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return bool
	 */
	function has_items() {
		return !empty( $this->items );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function no_items() {
		_e( 'No items found.' );
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		return array();
	}

	/**
	 * Display the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function bulk_actions() {

		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();
			$this->_actions = apply_filters( 'bulk_actions-' . $this->_screen->base, $this->_actions );
			$two = '';
		}
		else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo "<select name='action$two'>\n";
		echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions' ) . "</option>\n";
		foreach ( $this->_actions as $name => $title )
			echo "\t<option value='$name'>$title</option>\n";
		echo "</select>\n";

		echo "<input type='submit' value='" . esc_attr__( 'Apply' ) . "' name='doaction$two' id='doaction$two' class='button-secondary action' />\n";
	}

	/**
	 * Display a monthly dropdown for filtering items
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function months_dropdown( $post_type ) {
		global $wpdb, $wp_locale;

		$months = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
			FROM $wpdb->posts
			WHERE post_type = %s
			ORDER BY post_date DESC
		", $post_type ) );

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
?>
		<select name='m'>
			<option<?php selected( $m, 0 ); ?> value='0'><?php _e( 'Show all dates' ); ?></option>
<?php
		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option %s value='%s'>%s</option>\n",
				selected( $m, $year . $month, false ),
				esc_attr( $arc_row->year . $month ),
				$wp_locale->get_month( $month ) . " $year"
			);
		}
?>
		</select>
<?php
	}

	/**
	 * Display a view switcher
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function view_switcher( $current_mode ) {
		$modes = array(
			'list'    => __( 'List View' ),
			'excerpt' => __( 'Excerpt View' )
		);

?>
		<input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
		<div class="view-switch">
<?php
			foreach ( $modes as $mode => $title ) {
				$class = ( $current_mode == $mode ) ? 'class="current"' : '';
				echo "<a href='" . esc_url( add_query_arg( 'mode', $mode, $_SERVER['REQUEST_URI'] ) ) . "' $class><img id='view-switch-$mode' src='" . esc_url( includes_url( 'images/blank.gif' ) ) . "' width='20' height='20' title='$title' alt='$title' /></a>\n";
			}
		?>
		</div>
<?php
	}

	/**
	 * Display a comment count bubble
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param int $post_id
	 * @param int $pending_comments
	 */
	function comments_bubble( $post_id, $pending_comments ) {
		$pending_phrase = sprintf( __( '%s pending' ), number_format( $pending_comments ) );

		if ( $pending_comments )
			echo '<strong>';

		$link = "<a href='" . add_query_arg( 'p', $post_id, admin_url('edit-comments.php') ) . "' title='$pending_phrase' class='post-com-count'><span class='comment-count'>%s</span></a>";

		comments_number(
			sprintf( $link, /* translators: comment count link */ _x( '0', 'comment count' ) ),
			sprintf( $link, /* translators: comment count link */ _x( '1', 'comment count' ) ),
			sprintf( $link, /* translators: comment count link: % will be substituted by comment count */ _x( '%', 'comment count' ) )
		);

		if ( $pending_comments )
			echo '</strong>';
	}

	/**
	 * Get the current page number
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return int
	 */
	function get_pagenum( $query_var = 'paged' ) {
		$pagenum = isset( $_REQUEST[$query_var] ) ? absint( $_REQUEST[$query_var] ) : 0;
	
		return max( 1, $pagenum );
	}

	/**
	 * Display the pagination.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function pagination() {
		if ( $this->_pagination ) {
			echo $this->_pagination;
			return;
		}

		if ( empty( $this->_pagination_args ) )
			return;

		extract( $this->_pagination_args );

		if ( $total_pages < 2 )
			return;

		$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum( $query_var );

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$page_links = array();

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page',
			esc_attr__( 'Go to the first page' ),
			esc_url( remove_query_arg( $query_var, $current_url ) ),
			'&laquo;&laquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page',
			esc_attr__( 'Go to the previous page' ),
			esc_url( add_query_arg( $query_var, max( 1, $current-1 ), $current_url ) ),
			'&laquo;'
		);

		$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='%s' value='%s' size='%d' />",
			esc_attr__( 'Current page' ),
			esc_attr( $query_var ),
			number_format_i18n( $current ),
			strlen( $total_pages )
		);
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = sprintf( _x( '%s of %s', 'paging' ), $html_current_page, $html_total_pages );

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page',
			esc_attr__( 'Go to the next page' ),
			esc_url( add_query_arg( $query_var, min( $total_pages, $current+1 ), $current_url ) ),
			'&raquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page',
			esc_attr__( 'Go to the last page' ),
			esc_url( add_query_arg( $query_var, $total_pages, $current_url ) ),
			'&raquo;&raquo;'
		);

		$output .= join( "\n", $page_links );

		$this->_pagination = "<div class='tablenav-pages'>$output</div>";

		echo $this->_pagination;
	}

	/**
	 * Get a list of columns. The format is internal_name => title
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	function get_columns() {
		die( 'function WP_List_Table::get_columns() must be over-ridden in a sub-class.' );
	}

	/**
	 * Get a list of sortable columns. The format is internal_name => orderby
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	function get_sortable_columns() {
		return array();
	}

	/**
	 * Get a list of hidden columns.
	 *
	 * @since 3.1.0
	 * @access private
	 *
	 * @return array
	 */
	function get_hidden_columns() {
		return (array) get_user_option( 'manage' . $this->_screen->id. 'columnshidden' );
	}

	/**
	 * Get a list of all, hidden and sortable columns, with filter applied
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	function get_column_headers() {
		if ( !isset( $this->_column_headers ) ) {
			$columns = apply_filters( 'manage_' . $this->_screen->id . '_columns', $this->get_columns() );
			$sortable = apply_filters( 'manage_' . $this->_screen->id . '_sortable_columns', $this->get_sortable_columns() );
			$hidden = $this->get_hidden_columns();

			$this->_column_headers = array( $columns, $hidden, $sortable );
		}

		return $this->_column_headers;
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param bool $with_id Wether to set the id attribute or not
	 */
	function print_column_headers( $with_id = true ) {
		$screen = $this->_screen;

		list( $columns, $hidden, $sortable ) = $this->get_column_headers();

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if ( isset( $_GET['orderby'] ) )
			$current_orderby = $_GET['orderby'];
		else
			$current_orderby = '';

		if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] )
			$current_order = 'desc';
		else
			$current_order = 'asc';

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			$style = '';
			if ( in_array( $column_key, $hidden ) )
				$style = 'display:none;';

			$style = ' style="' . $style . '"';

			if ( 'cb' == $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( isset( $sortable[$column_key] ) ) {
				$orderby = $sortable[$column_key];
				if ( $current_orderby == $orderby ) {
					$order = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = "sorted-$current_order";
				} else {
					$order = 'asc';
					$class[] = 'sortable';
				}
				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '">' . $column_display_name . '</a>';
				$column_display_name .= '<div class="sorting-indicator"></div>';
			}

			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<th scope='col' $id $class $style>$column_display_name</th>";
		}
	}

	/**
	 * Display the table or a message if there are no items
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function display() {
		if ( $this->has_items() ) {
			$this->display_table();
		} else {
			echo '<br class="clear">';
			$this->extra_tablenav( 'top' );
			echo '<br class="clear">';
			echo '<p>';
			$this->no_items();
			echo '</p>';
		}
	}

	/**
	 * Get a list of CSS classes for the <table> tag
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */	
	function get_table_classes() {
		extract( $this->_args );

		return array( 'widefat', 'fixed', $plural );
	}

	/**
	 * Display the full table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function display_table() {
		extract( $this->_args );

		$this->display_tablenav( 'top' );

?>
<table class="<?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
	<thead>
	<tr>
		<?php $this->print_column_headers(); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<?php $this->print_column_headers( false ); ?>
	</tr>
	</tfoot>

	<tbody id="the-list"<?php if ( $singular ) echo " class='list:$singular'"; ?>>
		<?php $this->display_rows(); ?>
	</tbody>
</table>
<?php

		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function display_tablenav( $which ) {
		if ( 'top' == $which )
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
?>
	<div class="tablenav">

		<div class="alignleft actions">
			<?php $this->bulk_actions( $which ); ?>
		</div>

	<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
	?>

		<br class="clear">
	</div>

	<br class="clear">
<?php
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function extra_tablenav( $which ) {}

	/**
	 * Generate the <tbody> part of the table
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function display_rows() {
		die( 'function WP_List_Table::display_rows() must be over-ridden in a sub-class.' );
	}

	/**
	 * Handle an incoming ajax request (called from admin-ajax.php)
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function ajax_response() {
		$this->check_permissions();
		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args );

		ob_start();
		$this->display_rows();
		$rows = ob_get_clean();

		die( json_encode( array(
			'rows' => $rows,
			'total_items' => sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ),
			'total_pages' => $total_pages
		) ) );
	}

	/**
	 * Send required variables to JavaScript land
	 *
	 * @access private
	 */
	function _js_vars() {
		extract( $this->_args );

		$class = get_class( $this );

		printf( "<script type='text/javascript'>list_args = %s;</script>\n",
			json_encode( compact( 'screen', 'class' ) )
		);
	}
}

/**
 * Fetch an instance of a WP_List_Table class
 *
 * @since 3.1.0
 *
 * @param string $type The type of the list table
 * @return object
 */
function get_list_table( $type ) {
	require_once( ABSPATH . '/wp-admin/includes/default-list-tables.php' );

	$class = 'WP_' . strtr( ucwords( strtr( $type, '-', ' ') ), ' ', '_' ) . '_Table';
	$class = apply_filters( "get_list_table_$type", $class );

	return new $class;
}

