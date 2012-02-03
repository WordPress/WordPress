<?php
/**
 * Base class for displaying a list of items in an ajaxified HTML table.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Base class for displaying a list of items in an ajaxified HTML table.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
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
	 * @access protected
	 */
	var $screen;

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
	function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'plural' => '',
			'singular' => '',
			'ajax' => false
		) );

		$screen = get_current_screen();

		add_filter( "manage_{$screen->id}_columns", array( &$this, 'get_columns' ), 0 );

		if ( !$args['plural'] )
			$args['plural'] = $screen->base;

		$args['plural'] = sanitize_key( $args['plural'] );
		$args['singular'] = sanitize_key( $args['singular'] );

		$this->_args = $args;

		if ( $args['ajax'] ) {
			// wp_enqueue_script( 'list-table' );
			add_action( 'admin_footer', array( &$this, '_js_vars' ) );
		}
	}

	/**
	 * Checks the current user's permissions
	 * @uses wp_die()
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 */
	function ajax_user_can() {
		die( 'function WP_List_Table::ajax_user_can() must be over-ridden in a sub-class.' );
	}

	/**
	 * Prepares the list of items for displaying.
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
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
			'total_items' => 0,
			'total_pages' => 0,
			'per_page' => 0,
		) );

		if ( !$args['total_pages'] && $args['per_page'] > 0 )
			$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );

		// redirect if page number is invalid and headers are not already sent
		if ( ! headers_sent() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
			wp_redirect( add_query_arg( 'paged', $args['total_pages'] ) );
			exit;
		}

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

		if ( isset( $this->_pagination_args[$key] ) )
			return $this->_pagination_args[$key];
	}

	/**
	 * Whether the table has items to display or not
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
	 * Display the search box.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $text The search button text
	 * @param string $input_id The search input id
	 */
	function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
?>
<p class="search-box">
	<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
	<input type="text" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
	<?php submit_button( $text, 'button', false, false, array('id' => 'search-submit') ); ?>
</p>
<?php
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	function get_views() {
		return array();
	}

	/**
	 * Display the list of views available on this table.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function views() {
		$screen = get_current_screen();

		$views = $this->get_views();
		$views = apply_filters( 'views_' . $screen->id, $views );

		if ( empty( $views ) )
			return;

		echo "<ul class='subsubsub'>\n";
		foreach ( $views as $class => $view ) {
			$views[ $class ] = "\t<li class='$class'>$view";
		}
		echo implode( " |</li>\n", $views ) . "</li>\n";
		echo "</ul>";
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
		$screen = get_current_screen();

		if ( is_null( $this->_actions ) ) {
			$no_new_actions = $this->_actions = $this->get_bulk_actions();
			// This filter can currently only be used to remove actions.
			$this->_actions = apply_filters( 'bulk_actions-' . $screen->id, $this->_actions );
			$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo "<select name='action$two'>\n";
		echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' == $name ? ' class="hide-if-no-js"' : '';

			echo "\t<option value='$name'$class>$title</option>\n";
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'button-secondary action', false, false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return string|bool The action name or False if no action was selected
	 */
	function current_action() {
		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
			return $_REQUEST['action'];

		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
			return $_REQUEST['action2'];

		return false;
	}

	/**
	 * Generate row actions div
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array $actions The list of actions
	 * @param bool $always_visible Whether the actions should be always visible
	 * @return string
	 */
	function row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count )
			return '';

		$out = '<div class="' . ( $always_visible ? 'row-actions-visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		return $out;
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
				/* translators: 1: month name, 2: 4-digit year */
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
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

		echo "<a href='" . esc_url( add_query_arg( 'p', $post_id, admin_url( 'edit-comments.php' ) ) ) . "' title='" . esc_attr( $pending_phrase ) . "' class='post-com-count'><span class='comment-count'>" . number_format_i18n( get_comments_number() ) . "</span></a>";

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
	function get_pagenum() {
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;

		if( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
			$pagenum = $this->_pagination_args['total_pages'];

		return max( 1, $pagenum );
	}

	/**
	 * Get number of items to display on a single page
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return int
	 */
	function get_items_per_page( $option, $default = 20 ) {
		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = $default;

		return (int) apply_filters( $option, $per_page );
	}

	/**
	 * Display the pagination.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function pagination( $which ) {
		if ( empty( $this->_pagination_args ) )
			return;

		extract( $this->_pagination_args );

		$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		$disable_first = $disable_last = '';
		if ( $current == 1 )
			$disable_first = ' disabled';
		if ( $current == $total_pages )
			$disable_last = ' disabled';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page' ),
			esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page' ),
			esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which )
			$html_current_page = $current;
		else
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='%s' value='%s' size='%d' />",
				esc_attr__( 'Current page' ),
				esc_attr( 'paged' ),
				$current,
				strlen( $total_pages )
			);

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page' ),
			esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
			'&rsaquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page' ),
			esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
			'&raquo;'
		);

		$output .= "\n<span class='pagination-links'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages )
			$page_class = $total_pages < 2 ? ' one-page' : '';
		else
			$page_class = ' no-pages';

		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 3.1.0
	 * @access protected
	 * @abstract
	 *
	 * @return array
	 */
	function get_columns() {
		die( 'function WP_List_Table::get_columns() must be over-ridden in a sub-class.' );
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
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
	 * Get a list of all, hidden and sortable columns, with filter applied
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	function get_column_info() {
		if ( isset( $this->_column_headers ) )
			return $this->_column_headers;

		$screen = get_current_screen();

		$columns = get_column_headers( $screen );
		$hidden = get_hidden_columns( $screen );

		$_sortable = apply_filters( "manage_{$screen->id}_sortable_columns", $this->get_sortable_columns() );

		$sortable = array();
		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) )
				continue;

			$data = (array) $data;
			if ( !isset( $data[1] ) )
				$data[1] = false;

			$sortable[$id] = $data;
		}

		$this->_column_headers = array( $columns, $hidden, $sortable );

		return $this->_column_headers;
	}

	/**
	 * Return number of visible columns
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return int
	 */
	function get_column_count() {
		list ( $columns, $hidden ) = $this->get_column_info();
		$hidden = array_intersect( array_keys( $columns ), array_filter( $hidden ) );
		return count( $columns ) - count( $hidden );
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	function print_column_headers( $with_id = true ) {
		$screen = get_current_screen();

		list( $columns, $hidden, $sortable ) = $this->get_column_info();

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = remove_query_arg( 'paged', $current_url );

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
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby == $orderby ) {
					$order = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<th scope='col' $id $class $style>$column_display_name</th>";
		}
	}

	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function display() {
		extract( $this->_args );

		$this->display_tablenav( 'top' );

?>
<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
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
		<?php $this->display_rows_or_placeholder(); ?>
	</tbody>
</table>
<?php
		$this->display_tablenav( 'bottom' );
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
		return array( 'widefat', 'fixed', $this->_args['plural'] );
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
	function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			list( $columns, $hidden ) = $this->get_column_info();
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			$this->no_items();
			echo '</td></tr>';
		}
	}

	/**
	 * Generate the table rows
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function display_rows() {
		foreach ( $this->items as $item )
			$this->single_row( $item );
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param object $item The current item
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr' . $row_class . '>';
		echo $this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Generates the columns for a single row of the table
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param object $item The current item
	 */
	function single_row_columns( $item ) {
		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class='$column_name column-$column_name'";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			if ( 'cb' == $column_name ) {
				echo '<th scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			}
			elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( &$this, 'column_' . $column_name ), $item );
				echo "</td>";
			}
			else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				echo "</td>";
			}
		}
	}

	/**
	 * Handle an incoming ajax request (called from admin-ajax.php)
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function ajax_response() {
		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args );

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) )
			$this->display_rows();
		else
			$this->display_rows_or_placeholder();

		$rows = ob_get_clean();

		$response = array( 'rows' => $rows );

		if ( isset( $total_items ) )
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		die( json_encode( $response ) );
	}

	/**
	 * Send required variables to JavaScript land
	 *
	 * @access private
	 */
	function _js_vars() {
		$current_screen = get_current_screen();

		$args = array(
			'class'  => get_class( $this ),
			'screen' => array(
				'id'   => $current_screen->id,
				'base' => $current_screen->base,
			)
		);

		printf( "<script type='text/javascript'>list_args = %s;</script>\n", json_encode( $args ) );
	}
}
