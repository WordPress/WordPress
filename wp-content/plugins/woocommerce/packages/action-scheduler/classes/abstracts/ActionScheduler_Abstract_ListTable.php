<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Action Scheduler Abstract List Table class
 *
 * This abstract class enhances WP_List_Table making it ready to use.
 *
 * By extending this class we can focus on describing how our table looks like,
 * which columns needs to be shown, filter, ordered by and more and forget about the details.
 *
 * This class supports:
 *  - Bulk actions
 *  - Search
 *  - Sortable columns
 *  - Automatic translations of the columns
 *
 * @codeCoverageIgnore
 * @since  2.0.0
 */
abstract class ActionScheduler_Abstract_ListTable extends WP_List_Table {

	/**
	 * The table name
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * Package name, used to get options from WP_List_Table::get_items_per_page.
	 *
	 * @var string
	 */
	protected $package;

	/**
	 * How many items do we render per page?
	 *
	 * @var int
	 */
	protected $items_per_page = 10;

	/**
	 * Enables search in this table listing. If this array
	 * is empty it means the listing is not searchable.
	 *
	 * @var array
	 */
	protected $search_by = array();

	/**
	 * Columns to show in the table listing. It is a key => value pair. The
	 * key must much the table column name and the value is the label, which is
	 * automatically translated.
	 *
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Defines the row-actions. It expects an array where the key
	 * is the column name and the value is an array of actions.
	 *
	 * The array of actions are key => value, where key is the method name
	 * (with the prefix row_action_<key>) and the value is the label
	 * and title.
	 *
	 * @var array
	 */
	protected $row_actions = array();

	/**
	 * The Primary key of our table
	 *
	 * @var string
	 */
	protected $ID = 'ID';

	/**
	 * Enables sorting, it expects an array
	 * of columns (the column names are the values)
	 *
	 * @var array
	 */
	protected $sort_by = array();

	/**
	 * The default sort order
	 *
	 * @var string
	 */
	protected $filter_by = array();

	/**
	 * The status name => count combinations for this table's items. Used to display status filters.
	 *
	 * @var array
	 */
	protected $status_counts = array();

	/**
	 * Notices to display when loading the table. Array of arrays of form array( 'class' => {updated|error}, 'message' => 'This is the notice text display.' ).
	 *
	 * @var array
	 */
	protected $admin_notices = array();

	/**
	 * Localised string displayed in the <h1> element above the able.
	 *
	 * @var string
	 */
	protected $table_header;

	/**
	 * Enables bulk actions. It must be an array where the key is the action name
	 * and the value is the label (which is translated automatically). It is important
	 * to notice that it will check that the method exists (`bulk_$name`) and will throw
	 * an exception if it does not exists.
	 *
	 * This class will automatically check if the current request has a bulk action, will do the
	 * validations and afterwards will execute the bulk method, with two arguments. The first argument
	 * is the array with primary keys, the second argument is a string with a list of the primary keys,
	 * escaped and ready to use (with `IN`).
	 *
	 * @var array
	 */
	protected $bulk_actions = array();

	/**
	 * Makes translation easier, it basically just wraps
	 * `_x` with some default (the package name).
	 *
	 * @param string $text The new text to translate.
	 * @param string $context The context of the text.
	 * @return string|void The translated text.
	 *
	 * @deprecated 3.0.0 Use `_x()` instead.
	 */
	protected function translate( $text, $context = '' ) {
		return $text;
	}

	/**
	 * Reads `$this->bulk_actions` and returns an array that WP_List_Table understands. It
	 * also validates that the bulk method handler exists. It throws an exception because
	 * this is a library meant for developers and missing a bulk method is a development-time error.
	 *
	 * @return array
	 *
	 * @throws RuntimeException Throws RuntimeException when the bulk action does not have a callback method.
	 */
	protected function get_bulk_actions() {
		$actions = array();

		foreach ( $this->bulk_actions as $action => $label ) {
			if ( ! is_callable( array( $this, 'bulk_' . $action ) ) ) {
				throw new RuntimeException( "The bulk action $action does not have a callback method" );
			}

			$actions[ $action ] = $label;
		}

		return $actions;
	}

	/**
	 * Checks if the current request has a bulk action. If that is the case it will validate and will
	 * execute the bulk method handler. Regardless if the action is valid or not it will redirect to
	 * the previous page removing the current arguments that makes this request a bulk action.
	 */
	protected function process_bulk_action() {
		global $wpdb;
		// Detect when a bulk action is being triggered.
		$action = $this->current_action();
		if ( ! $action ) {
			return;
		}

		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		$method = 'bulk_' . $action;
		if ( array_key_exists( $action, $this->bulk_actions ) && is_callable( array( $this, $method ) ) && ! empty( $_GET['ID'] ) && is_array( $_GET['ID'] ) ) {
			$ids_sql = '(' . implode( ',', array_fill( 0, count( $_GET['ID'] ), '%s' ) ) . ')';
			$id      = array_map( 'absint', $_GET['ID'] );
			$this->$method( $id, $wpdb->prepare( $ids_sql, $id ) ); //phpcs:ignore WordPress.DB.PreparedSQL
		}

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			wp_safe_redirect(
				remove_query_arg(
					array( '_wp_http_referer', '_wpnonce', 'ID', 'action', 'action2' ),
					esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
				)
			);
			exit;
		}
	}

	/**
	 * Default code for deleting entries.
	 * validated already by process_bulk_action()
	 *
	 * @param array  $ids ids of the items to delete.
	 * @param string $ids_sql the sql for the ids.
	 * @return void
	 */
	protected function bulk_delete( array $ids, $ids_sql ) {
		$store = ActionScheduler::store();
		foreach ( $ids as $action_id ) {
			$store->delete( $action_id );
		}
	}

	/**
	 * Prepares the _column_headers property which is used by WP_Table_List at rendering.
	 * It merges the columns and the sortable columns.
	 */
	protected function prepare_column_headers() {
		$this->_column_headers = array(
			$this->get_columns(),
			get_hidden_columns( $this->screen ),
			$this->get_sortable_columns(),
		);
	}

	/**
	 * Reads $this->sort_by and returns the columns name in a format that WP_Table_List
	 * expects
	 */
	public function get_sortable_columns() {
		$sort_by = array();
		foreach ( $this->sort_by as $column ) {
			$sort_by[ $column ] = array( $column, true );
		}
		return $sort_by;
	}

	/**
	 * Returns the columns names for rendering. It adds a checkbox for selecting everything
	 * as the first column
	 */
	public function get_columns() {
		$columns = array_merge(
			array( 'cb' => '<input type="checkbox" />' ),
			$this->columns
		);

		return $columns;
	}

	/**
	 * Get prepared LIMIT clause for items query
	 *
	 * @global wpdb $wpdb
	 *
	 * @return string Prepared LIMIT clause for items query.
	 */
	protected function get_items_query_limit() {
		global $wpdb;

		$per_page = $this->get_items_per_page( $this->get_per_page_option_name(), $this->items_per_page );
		return $wpdb->prepare( 'LIMIT %d', $per_page );
	}

	/**
	 * Returns the number of items to offset/skip for this current view.
	 *
	 * @return int
	 */
	protected function get_items_offset() {
		$per_page     = $this->get_items_per_page( $this->get_per_page_option_name(), $this->items_per_page );
		$current_page = $this->get_pagenum();
		if ( 1 < $current_page ) {
			$offset = $per_page * ( $current_page - 1 );
		} else {
			$offset = 0;
		}

		return $offset;
	}

	/**
	 * Get prepared OFFSET clause for items query
	 *
	 * @global wpdb $wpdb
	 *
	 * @return string Prepared OFFSET clause for items query.
	 */
	protected function get_items_query_offset() {
		global $wpdb;

		return $wpdb->prepare( 'OFFSET %d', $this->get_items_offset() );
	}

	/**
	 * Prepares the ORDER BY sql statement. It uses `$this->sort_by` to know which
	 * columns are sortable. This requests validates the orderby $_GET parameter is a valid
	 * column and sortable. It will also use order (ASC|DESC) using DESC by default.
	 */
	protected function get_items_query_order() {
		if ( empty( $this->sort_by ) ) {
			return '';
		}

		$orderby = esc_sql( $this->get_request_orderby() );
		$order   = esc_sql( $this->get_request_order() );

		return "ORDER BY {$orderby} {$order}";
	}

	/**
	 * Return the sortable column specified for this request to order the results by, if any.
	 *
	 * @return string
	 */
	protected function get_request_orderby() {

		$valid_sortable_columns = array_values( $this->sort_by );

		if ( ! empty( $_GET['orderby'] ) && in_array( $_GET['orderby'], $valid_sortable_columns, true ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} else {
			$orderby = $valid_sortable_columns[0];
		}

		return $orderby;
	}

	/**
	 * Return the sortable column order specified for this request.
	 *
	 * @return string
	 */
	protected function get_request_order() {

		if ( ! empty( $_GET['order'] ) && 'desc' === strtolower( sanitize_text_field( wp_unslash( $_GET['order'] ) ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

		return $order;
	}

	/**
	 * Return the status filter for this request, if any.
	 *
	 * @return string
	 */
	protected function get_request_status() {
		$status = ( ! empty( $_GET['status'] ) ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $status;
	}

	/**
	 * Return the search filter for this request, if any.
	 *
	 * @return string
	 */
	protected function get_request_search_query() {
		$search_query = ( ! empty( $_GET['s'] ) ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $search_query;
	}

	/**
	 * Process and return the columns name. This is meant for using with SQL, this means it
	 * always includes the primary key.
	 *
	 * @return array
	 */
	protected function get_table_columns() {
		$columns = array_keys( $this->columns );
		if ( ! in_array( $this->ID, $columns, true ) ) {
			$columns[] = $this->ID;
		}

		return $columns;
	}

	/**
	 * Check if the current request is doing a "full text" search. If that is the case
	 * prepares the SQL to search texts using LIKE.
	 *
	 * If the current request does not have any search or if this list table does not support
	 * that feature it will return an empty string.
	 *
	 * @return string
	 */
	protected function get_items_query_search() {
		global $wpdb;

		if ( empty( $_GET['s'] ) || empty( $this->search_by ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return '';
		}

		$search_string = sanitize_text_field( wp_unslash( $_GET['s'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$filter = array();
		foreach ( $this->search_by as $column ) {
			$wild     = '%';
			$sql_like = $wild . $wpdb->esc_like( $search_string ) . $wild;
			$filter[] = $wpdb->prepare( '`' . $column . '` LIKE %s', $sql_like ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.DB.PreparedSQL.NotPrepared
		}
		return implode( ' OR ', $filter );
	}

	/**
	 * Prepares the SQL to filter rows by the options defined at `$this->filter_by`. Before trusting
	 * any data sent by the user it validates that it is a valid option.
	 */
	protected function get_items_query_filters() {
		global $wpdb;

		if ( ! $this->filter_by || empty( $_GET['filter_by'] ) || ! is_array( $_GET['filter_by'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return '';
		}

		$filter = array();

		foreach ( $this->filter_by as $column => $options ) {
			if ( empty( $_GET['filter_by'][ $column ] ) || empty( $options[ $_GET['filter_by'][ $column ] ] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				continue;
			}

			$filter[] = $wpdb->prepare( "`$column` = %s", sanitize_text_field( wp_unslash( $_GET['filter_by'][ $column ] ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		return implode( ' AND ', $filter );

	}

	/**
	 * Prepares the data to feed WP_Table_List.
	 *
	 * This has the core for selecting, sorting and filting data. To keep the code simple
	 * its logic is split among many methods (get_items_query_*).
	 *
	 * Beside populating the items this function will also count all the records that matches
	 * the filtering criteria and will do fill the pagination variables.
	 */
	public function prepare_items() {
		global $wpdb;

		$this->process_bulk_action();

		$this->process_row_actions();

		if ( ! empty( $_REQUEST['_wp_http_referer'] && ! empty( $_SERVER['REQUEST_URI'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// _wp_http_referer is used only on bulk actions, we remove it to keep the $_GET shorter
			wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
			exit;
		}

		$this->prepare_column_headers();

		$limit   = $this->get_items_query_limit();
		$offset  = $this->get_items_query_offset();
		$order   = $this->get_items_query_order();
		$where   = array_filter(
			array(
				$this->get_items_query_search(),
				$this->get_items_query_filters(),
			)
		);
		$columns = '`' . implode( '`, `', $this->get_table_columns() ) . '`';

		if ( ! empty( $where ) ) {
			$where = 'WHERE (' . implode( ') AND (', $where ) . ')';
		} else {
			$where = '';
		}

		$sql = "SELECT $columns FROM {$this->table_name} {$where} {$order} {$limit} {$offset}";

		$this->set_items( $wpdb->get_results( $sql, ARRAY_A ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$query_count = "SELECT COUNT({$this->ID}) FROM {$this->table_name} {$where}";
		$total_items = $wpdb->get_var( $query_count ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$per_page    = $this->get_items_per_page( $this->get_per_page_option_name(), $this->items_per_page );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Display the table.
	 *
	 * @param string $which The name of the table.
	 */
	public function extra_tablenav( $which ) {
		if ( ! $this->filter_by || 'top' !== $which ) {
			return;
		}

		echo '<div class="alignleft actions">';

		foreach ( $this->filter_by as $id => $options ) {
			$default = ! empty( $_GET['filter_by'][ $id ] ) ? sanitize_text_field( wp_unslash( $_GET['filter_by'][ $id ] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( empty( $options[ $default ] ) ) {
				$default = '';
			}

			echo '<select name="filter_by[' . esc_attr( $id ) . ']" class="first" id="filter-by-' . esc_attr( $id ) . '">';

			foreach ( $options as $value => $label ) {
				echo '<option value="' . esc_attr( $value ) . '" ' . esc_html( $value === $default ? 'selected' : '' ) . '>'
					. esc_html( $label )
				. '</option>';
			}

			echo '</select>';
		}

		submit_button( esc_html__( 'Filter', 'woocommerce' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
		echo '</div>';
	}

	/**
	 * Set the data for displaying. It will attempt to unserialize (There is a chance that some columns
	 * are serialized). This can be override in child classes for futher data transformation.
	 *
	 * @param array $items Items array.
	 */
	protected function set_items( array $items ) {
		$this->items = array();
		foreach ( $items as $item ) {
			$this->items[ $item[ $this->ID ] ] = array_map( 'maybe_unserialize', $item );
		}
	}

	/**
	 * Renders the checkbox for each row, this is the first column and it is named ID regardless
	 * of how the primary key is named (to keep the code simpler). The bulk actions will do the proper
	 * name transformation though using `$this->ID`.
	 *
	 * @param array $row The row to render.
	 */
	public function column_cb( $row ) {
		return '<input name="ID[]" type="checkbox" value="' . esc_attr( $row[ $this->ID ] ) . '" />';
	}

	/**
	 * Renders the row-actions.
	 *
	 * This method renders the action menu, it reads the definition from the $row_actions property,
	 * and it checks that the row action method exists before rendering it.
	 *
	 * @param array  $row Row to be rendered.
	 * @param string $column_name Column name.
	 * @return string
	 */
	protected function maybe_render_actions( $row, $column_name ) {
		if ( empty( $this->row_actions[ $column_name ] ) ) {
			return;
		}

		$row_id = $row[ $this->ID ];

		$actions      = '<div class="row-actions">';
		$action_count = 0;
		foreach ( $this->row_actions[ $column_name ] as $action_key => $action ) {

			$action_count++;

			if ( ! method_exists( $this, 'row_action_' . $action_key ) ) {
				continue;
			}

			$action_link = ! empty( $action['link'] ) ? $action['link'] : add_query_arg(
				array(
					'row_action' => $action_key,
					'row_id'     => $row_id,
					'nonce'      => wp_create_nonce( $action_key . '::' . $row_id ),
				)
			);
			$span_class  = ! empty( $action['class'] ) ? $action['class'] : $action_key;
			$separator   = ( $action_count < count( $this->row_actions[ $column_name ] ) ) ? ' | ' : '';

			$actions .= sprintf( '<span class="%s">', esc_attr( $span_class ) );
			$actions .= sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', esc_url( $action_link ), esc_attr( $action['desc'] ), esc_html( $action['name'] ) );
			$actions .= sprintf( '%s</span>', $separator );
		}
		$actions .= '</div>';
		return $actions;
	}

	/**
	 * Process the bulk actions.
	 *
	 * @return void
	 */
	protected function process_row_actions() {
		$parameters = array( 'row_action', 'row_id', 'nonce' );
		foreach ( $parameters as $parameter ) {
			if ( empty( $_REQUEST[ $parameter ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}
		}

		$action = sanitize_text_field( wp_unslash( $_REQUEST['row_action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$row_id = sanitize_text_field( wp_unslash( $_REQUEST['row_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$nonce  = sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$method = 'row_action_' . $action; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( wp_verify_nonce( $nonce, $action . '::' . $row_id ) && method_exists( $this, $method ) ) {
			$this->$method( sanitize_text_field( wp_unslash( $row_id ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			wp_safe_redirect(
				remove_query_arg(
					array( 'row_id', 'row_action', 'nonce' ),
					esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
				)
			);
			exit;
		}
	}

	/**
	 * Default column formatting, it will escape everythig for security.
	 *
	 * @param array  $item The item array.
	 * @param string $column_name Column name to display.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		$column_html  = esc_html( $item[ $column_name ] );
		$column_html .= $this->maybe_render_actions( $item, $column_name );
		return $column_html;
	}

	/**
	 * Display the table heading and search query, if any
	 */
	protected function display_header() {
		echo '<h1 class="wp-heading-inline">' . esc_attr( $this->table_header ) . '</h1>';
		if ( $this->get_request_search_query() ) {
			/* translators: %s: search query */
			echo '<span class="subtitle">' . esc_attr( sprintf( __( 'Search results for "%s"', 'woocommerce' ), $this->get_request_search_query() ) ) . '</span>';
		}
		echo '<hr class="wp-header-end">';
	}

	/**
	 * Display the table heading and search query, if any
	 */
	protected function display_admin_notices() {
		foreach ( $this->admin_notices as $notice ) {
			echo '<div id="message" class="' . esc_attr( $notice['class'] ) . '">';
			echo '	<p>' . wp_kses_post( $notice['message'] ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * Prints the available statuses so the user can click to filter.
	 */
	protected function display_filter_by_status() {

		$status_list_items = array();
		$request_status    = $this->get_request_status();

		// Helper to set 'all' filter when not set on status counts passed in.
		if ( ! isset( $this->status_counts['all'] ) ) {
			$this->status_counts = array( 'all' => array_sum( $this->status_counts ) ) + $this->status_counts;
		}

		foreach ( $this->status_counts as $status_name => $count ) {

			if ( 0 === $count ) {
				continue;
			}

			if ( $status_name === $request_status || ( empty( $request_status ) && 'all' === $status_name ) ) {
				$status_list_item = '<li class="%1$s"><a href="%2$s" class="current">%3$s</a> (%4$d)</li>';
			} else {
				$status_list_item = '<li class="%1$s"><a href="%2$s">%3$s</a> (%4$d)</li>';
			}

			$status_filter_url   = ( 'all' === $status_name ) ? remove_query_arg( 'status' ) : add_query_arg( 'status', $status_name );
			$status_filter_url   = remove_query_arg( array( 'paged', 's' ), $status_filter_url );
			$status_list_items[] = sprintf( $status_list_item, esc_attr( $status_name ), esc_url( $status_filter_url ), esc_html( ucfirst( $status_name ) ), absint( $count ) );
		}

		if ( $status_list_items ) {
			echo '<ul class="subsubsub">';
			echo implode( " | \n", $status_list_items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</ul>';
		}
	}

	/**
	 * Renders the table list, we override the original class to render the table inside a form
	 * and to render any needed HTML (like the search box). By doing so the callee of a function can simple
	 * forget about any extra HTML.
	 */
	protected function display_table() {
		echo '<form id="' . esc_attr( $this->_args['plural'] ) . '-filter" method="get">';
		foreach ( $_GET as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( '_' === $key[0] || 'paged' === $key || 'ID' === $key ) {
				continue;
			}
			echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
		}
		if ( ! empty( $this->search_by ) ) {
			echo $this->search_box( $this->get_search_box_button_text(), 'plugin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		parent::display();
		echo '</form>';
	}

	/**
	 * Process any pending actions.
	 */
	public function process_actions() {
		$this->process_bulk_action();
		$this->process_row_actions();

		if ( ! empty( $_REQUEST['_wp_http_referer'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// _wp_http_referer is used only on bulk actions, we remove it to keep the $_GET shorter
			wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
			exit;
		}
	}

	/**
	 * Render the list table page, including header, notices, status filters and table.
	 */
	public function display_page() {
		$this->prepare_items();

		echo '<div class="wrap">';
		$this->display_header();
		$this->display_admin_notices();
		$this->display_filter_by_status();
		$this->display_table();
		echo '</div>';
	}

	/**
	 * Get the text to display in the search box on the list table.
	 */
	protected function get_search_box_placeholder() {
		return esc_html__( 'Search', 'woocommerce' );
	}

	/**
	 * Gets the screen per_page option name.
	 *
	 * @return string
	 */
	protected function get_per_page_option_name() {
		return $this->package . '_items_per_page';
	}
}
