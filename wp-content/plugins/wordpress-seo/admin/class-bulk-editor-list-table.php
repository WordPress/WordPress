<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Bulk Editor
 * @since   1.5.0
 */

/**
 * Implements table for bulk editing.
 */
class WPSEO_Bulk_List_Table extends WP_List_Table {

	/**
	 * The nonce that was passed with the request.
	 *
	 * @var string
	 */
	private $nonce;

	/**
	 * Array of post types for which the current user has `edit_others_posts` capabilities.
	 *
	 * @var array
	 */
	private $all_posts;

	/**
	 * Array of post types for which the current user has `edit_posts` capabilities, but not `edit_others_posts`.
	 *
	 * @var array
	 */
	private $own_posts;

	/**
	 * Saves all the metadata into this array.
	 *
	 * @var array
	 */
	protected $meta_data = [];

	/**
	 * The current requested page_url.
	 *
	 * @var string
	 */
	private $request_url = '';

	/**
	 * The current page (depending on $_GET['paged']) if current tab is for current page_type, else it will be 1.
	 *
	 * @var int
	 */
	private $current_page;

	/**
	 * The current post filter, if is used (depending on $_GET['post_type_filter']).
	 *
	 * @var string
	 */
	private $current_filter;

	/**
	 * The current post status, if is used (depending on $_GET['post_status']).
	 *
	 * @var string
	 */
	private $current_status;

	/**
	 * The current sorting, if used (depending on $_GET['order'] and $_GET['orderby']).
	 *
	 * @var string
	 */
	private $current_order;

	/**
	 * The page_type for current class instance (for example: title / description).
	 *
	 * @var string
	 */
	protected $page_type;

	/**
	 * Based on the page_type ($this->page_type) there will be constructed an url part, for subpages and
	 * navigation.
	 *
	 * @var string
	 */
	protected $page_url;

	/**
	 * The settings which will be used in the __construct.
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Holds the pagination config.
	 *
	 * @var array
	 */
	protected $pagination = [];

	/**
	 * Holds the sanitized data from the user input.
	 *
	 * @var array
	 */
	protected $input_fields = [];

	/**
	 * The field in the database where meta field is saved.
	 *
	 * Should be set in the child class.
	 *
	 * @var string
	 */
	protected $target_db_field = '';

	/**
	 * Class constructor.
	 *
	 * @param array $args The arguments.
	 */
	public function __construct( $args = [] ) {
		parent::__construct( $this->settings );

		$args = wp_parse_args(
			$args,
			[
				'nonce'        => '',
				'input_fields' => [],
			]
		);

		$this->input_fields = $args['input_fields'];
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$this->request_url = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		$this->current_page   = ( ! empty( $this->input_fields['paged'] ) ) ? $this->input_fields['paged'] : 1;
		$this->current_filter = ( ! empty( $this->input_fields['post_type_filter'] ) ) ? $this->input_fields['post_type_filter'] : 1;
		$this->current_status = ( ! empty( $this->input_fields['post_status'] ) ) ? $this->input_fields['post_status'] : 1;
		$this->current_order  = [
			'order'   => ( ! empty( $this->input_fields['order'] ) ) ? $this->input_fields['order'] : 'asc',
			'orderby' => ( ! empty( $this->input_fields['orderby'] ) ) ? $this->input_fields['orderby'] : 'post_title',
		];

		$this->nonce    = $args['nonce'];
		$this->page_url = "&nonce={$this->nonce}&type={$this->page_type}#top#{$this->page_type}";

		$this->populate_editable_post_types();
	}

	/**
	 * Prepares the data and renders the page.
	 *
	 * @return void
	 */
	public function show_page() {
		$this->prepare_page_navigation();
		$this->prepare_items();

		$this->views();
		$this->display();
	}

	/**
	 * Used in the constructor to build a reference list of post types the current user can edit.
	 *
	 * @return void
	 */
	protected function populate_editable_post_types() {
		$post_types = get_post_types(
			[
				'public'              => true,
				'exclude_from_search' => false,
			],
			'object'
		);

		$this->all_posts = [];
		$this->own_posts = [];

		if ( is_array( $post_types ) && $post_types !== [] ) {
			foreach ( $post_types as $post_type ) {
				if ( ! current_user_can( $post_type->cap->edit_posts ) ) {
					continue;
				}

				if ( current_user_can( $post_type->cap->edit_others_posts ) ) {
					$this->all_posts[] = esc_sql( $post_type->name );
				}
				else {
					$this->own_posts[] = esc_sql( $post_type->name );
				}
			}
		}
	}

	/**
	 * Will show the navigation for the table like page navigation and page filter.
	 *
	 * @param string $which Table nav location (such as top).
	 *
	 * @return void
	 */
	public function display_tablenav( $which ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		$post_status      = isset( $_GET['post_status'] ) && is_string( $_GET['post_status'] ) ? sanitize_text_field( wp_unslash( $_GET['post_status'] ) ) : '';
		$order_by         = isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
		$order            = isset( $_GET['order'] ) && is_string( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		$post_type_filter = isset( $_GET['post_type_filter'] ) && is_string( $_GET['post_type_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type_filter'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended;
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( $which === 'top' ) { ?>
			<form id="posts-filter" action="" method="get">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( $this->nonce ); ?>" />
				<input type="hidden" name="page" value="wpseo_tools" />
				<input type="hidden" name="tool" value="bulk-editor" />
				<input type="hidden" name="type" value="<?php echo esc_attr( $this->page_type ); ?>" />
				<input type="hidden" name="orderby"
					value="<?php echo esc_attr( $order_by ); ?>" />
				<input type="hidden" name="order"
					value="<?php echo esc_attr( $order ); ?>" />
				<input type="hidden" name="post_type_filter"
					value="<?php echo esc_attr( $post_type_filter ); ?>" />
				<?php if ( ! empty( $post_status ) ) { ?>
					<input type="hidden" name="post_status" value="<?php echo esc_attr( $post_status ); ?>" />
				<?php } ?>
				<?php } ?>

				<?php
				$this->extra_tablenav( $which );
				$this->pagination( $which );
				?>

				<br class="clear"/>
				<?php if ( $which === 'top' ) { ?>
			</form>
		<?php } ?>
		</div>

		<?php
	}

	/**
	 * This function builds the base sql subquery used in this class.
	 *
	 * This function takes into account the post types in which the current user can
	 * edit all posts, and the ones the current user can only edit his/her own.
	 *
	 * @return string The subquery, which should always be used in $wpdb->prepare(),
	 *                passing the current user_id in as the first parameter.
	 */
	public function get_base_subquery() {
		global $wpdb;

		$all_posts_string = "'" . implode( "', '", $this->all_posts ) . "'";
		$own_posts_string = "'" . implode( "', '", $this->own_posts ) . "'";

		$post_author = esc_sql( (int) get_current_user_id() );

		$subquery = "(
				SELECT *
				FROM {$wpdb->posts}
				WHERE post_type IN ({$all_posts_string})
				UNION ALL
				SELECT *
				FROM {$wpdb->posts}
				WHERE post_type IN ({$own_posts_string}) AND post_author = {$post_author}
			) sub_base";

		return $subquery;
	}

	/**
	 * Gets the views.
	 *
	 * @return array The views.
	 */
	public function get_views() {
		global $wpdb;

		$status_links = [];

		$states   = get_post_stati( [ 'show_in_admin_all_list' => true ] );
		$subquery = $this->get_base_subquery();

		$total_posts = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(ID) FROM {$subquery}
					WHERE post_status IN ("
						. implode( ', ', array_fill( 0, count( $states ), '%s' ) )
					. ')',
				$states
			)
		);

		$post_status             = isset( $_GET['post_status'] ) && is_string( $_GET['post_status'] ) ? sanitize_text_field( wp_unslash( $_GET['post_status'] ) ) : '';
		$current_link_attributes = empty( $post_status ) ? ' class="current" aria-current="page"' : '';
		$localized_text          = sprintf(
			/* translators: %s expands to the number of posts in localized format. */
			_nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts', 'wordpress-seo' ),
			number_format_i18n( $total_posts )
		);

		$status_links['all'] = '<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_tools&tool=bulk-editor' . $this->page_url ) ) . '"' . $current_link_attributes . '>' . $localized_text . '</a>';

		$post_stati = get_post_stati( [ 'show_in_admin_all_list' => true ], 'objects' );
		if ( is_array( $post_stati ) && $post_stati !== [] ) {
			foreach ( $post_stati as $status ) {

				$status_name = esc_sql( $status->name );

				$total = (int) $wpdb->get_var(
					$wpdb->prepare(
						"
								SELECT COUNT(ID) FROM {$subquery}
								WHERE post_status = %s
							",
						$status_name
					)
				);

				if ( $total === 0 ) {
					continue;
				}

				$current_link_attributes = '';
				if ( $status_name === $post_status ) {
					$current_link_attributes = ' class="current" aria-current="page"';
				}

				$status_links[ $status_name ] = '<a href="' . esc_url( add_query_arg( [ 'post_status' => $status_name ], admin_url( 'admin.php?page=wpseo_tools&tool=bulk-editor' . $this->page_url ) ) ) . '"' . $current_link_attributes . '>' . sprintf( translate_nooped_plural( $status->label_count, $total ), number_format_i18n( $total ) ) . '</a>';
			}
		}
		unset( $post_stati, $status, $status_name, $total, $current_link_attributes );

		$trashed_posts = $wpdb->get_var(
			"SELECT COUNT(ID) FROM {$subquery}
				WHERE post_status IN ('trash')
			"
		);

		$current_link_attributes = '';
		if ( $post_status === 'trash' ) {
			$current_link_attributes = 'class="current" aria-current="page"';
		}

		$localized_text = sprintf(
			/* translators: %s expands to the number of trashed posts in localized format. */
			_nx( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', $trashed_posts, 'posts', 'wordpress-seo' ),
			number_format_i18n( $trashed_posts )
		);

		$status_links['trash'] = '<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_tools&tool=bulk-editor&post_status=trash' . $this->page_url ) ) . '"' . $current_link_attributes . '>' . $localized_text . '</a>';

		return $status_links;
	}

	/**
	 * Outputs extra table navigation.
	 *
	 * @param string $which Table nav location (such as top).
	 *
	 * @return void
	 */
	public function extra_tablenav( $which ) {

		if ( $which === 'top' ) {
			$post_types = get_post_types(
				[
					'public'              => true,
					'exclude_from_search' => false,
				]
			);

			$instance_type = esc_attr( $this->page_type );

			if ( is_array( $post_types ) && $post_types !== [] ) {
				global $wpdb;

				echo '<div class="alignleft actions">';

				$post_types = esc_sql( $post_types );
				$post_types = "'" . implode( "', '", $post_types ) . "'";

				$states          = get_post_stati( [ 'show_in_admin_all_list' => true ] );
				$states['trash'] = 'trash';

				$subquery = $this->get_base_subquery();

				$post_types = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT DISTINCT post_type FROM {$subquery}
							WHERE post_status IN ("
								. implode( ', ', array_fill( 0, count( $states ), '%s' ) )
							. ') ORDER BY post_type ASC',
						$states
					)
				);

				$post_type_filter = isset( $_GET['post_type_filter'] ) && is_string( $_GET['post_type_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type_filter'] ) ) : '';
				$selected         = ( ! empty( $post_type_filter ) ) ? $post_type_filter : '-1';

				$options = '<option value="-1">' . esc_html__( 'Show All Content Types', 'wordpress-seo' ) . '</option>';

				if ( is_array( $post_types ) && $post_types !== [] ) {
					foreach ( $post_types as $post_type ) {
						$obj      = get_post_type_object( $post_type->post_type );
						$options .= sprintf(
							'<option value="%2$s" %3$s>%1$s</option>',
							esc_html( $obj->labels->name ),
							esc_attr( $post_type->post_type ),
							selected( $selected, $post_type->post_type, false )
						);
					}
				}

				printf(
					'<label for="%1$s" class="screen-reader-text">%2$s</label>',
					esc_attr( 'post-type-filter-' . $instance_type ),
					/* translators: Hidden accessibility text. */
					esc_html__( 'Filter by content type', 'wordpress-seo' )
				);
				printf(
					'<select name="post_type_filter" id="%2$s">%1$s</select>',
					// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $options is properly escaped above.
					$options,
					esc_attr( 'post-type-filter-' . $instance_type )
				);

				submit_button( esc_html__( 'Filter', 'wordpress-seo' ), 'button', false, false, [ 'id' => 'post-query-submit' ] );
				echo '</div>';
			}
		}
	}

	/**
	 * Gets a list of sortable columns.
	 *
	 * The format is: 'internal-name' => array( 'orderby', bool ).
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return [
			'col_page_title' => [ 'post_title', true ],
			'col_post_type'  => [ 'post_type', false ],
			'col_post_date'  => [ 'post_date', false ],
		];
	}

	/**
	 * Sets the correct pagenumber and pageurl for the navigation.
	 *
	 * @return void
	 */
	public function prepare_page_navigation() {

		$request_url = $this->request_url . $this->page_url;

		$current_page   = $this->current_page;
		$current_filter = $this->current_filter;
		$current_status = $this->current_status;
		$current_order  = $this->current_order;

		/*
		 * If current type doesn't compare with objects page_type, then we have to unset
		 * some vars in the requested url (which will be used for internal table urls).
		 */
		if ( isset( $this->input_fields['type'] ) && $this->input_fields['type'] !== $this->page_type ) {
			$request_url = remove_query_arg( 'paged', $request_url ); // Page will be set with value 1 below.
			$request_url = remove_query_arg( 'post_type_filter', $request_url );
			$request_url = remove_query_arg( 'post_status', $request_url );
			$request_url = remove_query_arg( 'orderby', $request_url );
			$request_url = remove_query_arg( 'order', $request_url );
			$request_url = add_query_arg( 'pages', 1, $request_url );

			$current_page   = 1;
			$current_filter = '-1';
			$current_status = '';
			$current_order  = [
				'orderby' => 'post_title',
				'order'   => 'asc',
			];
		}

		$_SERVER['REQUEST_URI'] = $request_url;

		$_GET['paged']                = $current_page;
		$_REQUEST['paged']            = $current_page;
		$_REQUEST['post_type_filter'] = $current_filter;
		$_GET['post_type_filter']     = $current_filter;
		$_GET['post_status']          = $current_status;
		$_GET['orderby']              = $current_order['orderby'];
		$_GET['order']                = $current_order['order'];
	}

	/**
	 * Preparing the requested pagerows and setting the needed variables.
	 *
	 * @return void
	 */
	public function prepare_items() {

		$post_type_clause = $this->get_post_type_clause();
		$all_states       = $this->get_all_states();
		$subquery         = $this->get_base_subquery();

		// Setting the column headers.
		$this->set_column_headers();

		// Count the total number of needed items and setting pagination given $total_items.
		$total_items = $this->count_items( $subquery, $all_states, $post_type_clause );
		$this->set_pagination( $total_items );

		// Getting items given $query.
		$query = $this->parse_item_query( $subquery, $all_states, $post_type_clause );
		$this->get_items( $query );

		// Get the metadata for the current items ($this->items).
		$this->get_meta_data();
	}

	/**
	 * Getting the columns for first row.
	 *
	 * @return array
	 */
	public function get_columns() {
		return $this->merge_columns();
	}

	/**
	 * Setting the column headers.
	 *
	 * @return void
	 */
	protected function set_column_headers() {
		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = [ $columns, $hidden, $sortable ];
	}

	/**
	 * Counting total items.
	 *
	 * @param string $subquery         SQL FROM part.
	 * @param string $all_states       SQL IN part.
	 * @param string $post_type_clause SQL post type part.
	 *
	 * @return mixed
	 */
	protected function count_items( $subquery, $all_states, $post_type_clause ) {
		global $wpdb;

		return (int) $wpdb->get_var(
			"SELECT COUNT(ID) FROM {$subquery}
				WHERE post_status IN ({$all_states})
					{$post_type_clause}
			"
		);
	}

	/**
	 * Getting the post_type_clause filter.
	 *
	 * @return string
	 */
	protected function get_post_type_clause() {
		// Filter Block.
		$post_type_clause = '';
		$post_type_filter = isset( $_GET['post_type_filter'] ) && is_string( $_GET['post_type_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type_filter'] ) ) : '';

		if ( ! empty( $post_type_filter ) && get_post_type_object( $post_type_filter ) ) {
			$post_types       = esc_sql( $post_type_filter );
			$post_type_clause = "AND post_type IN ('{$post_types}')";
		}

		return $post_type_clause;
	}

	/**
	 * Setting the pagination.
	 *
	 * Total items is the number of all visible items.
	 *
	 * @param int $total_items Total items counts.
	 *
	 * @return void
	 */
	protected function set_pagination( $total_items ) {
		// Calculate items per page.
		$per_page = $this->get_items_per_page( 'wpseo_posts_per_page', 10 );
		$paged    = isset( $_GET['paged'] ) && is_string( $_GET['paged'] ) ? esc_sql( sanitize_text_field( wp_unslash( $_GET['paged'] ) ) ) : '';

		if ( empty( $paged ) || ! is_numeric( $paged ) ) {
			$paged = 1;
		}
		else {
			$paged = (int) $paged;
		}

		if ( $paged <= 0 ) {
			$paged = 1;
		}

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'total_pages' => ceil( $total_items / $per_page ),
				'per_page'    => $per_page,
			]
		);

		$this->pagination = [
			'per_page' => $per_page,
			'offset'   => ( ( $paged - 1 ) * $per_page ),
		];
	}

	/**
	 * Parse the query to get items from database.
	 *
	 * Based on given parameters there will be parse a query which will get all the pages/posts and other post_types
	 * from the database.
	 *
	 * @param string $subquery         SQL FROM part.
	 * @param string $all_states       SQL IN part.
	 * @param string $post_type_clause SQL post type part.
	 *
	 * @return string
	 */
	protected function parse_item_query( $subquery, $all_states, $post_type_clause ) {
		// Order By block.
		$orderby = isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';

		$orderby = ! empty( $orderby ) ? esc_sql( $orderby ) : 'post_title';
		$orderby = $this->sanitize_orderby( $orderby );

		// Order clause.
		$order = isset( $_GET['order'] ) && is_string( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		$order = ! empty( $order ) ? esc_sql( strtoupper( $order ) ) : 'ASC';
		$order = $this->sanitize_order( $order );

		// Get all needed results.
		$query = "
			SELECT ID, post_title, post_type, post_status, post_modified, post_date
				FROM {$subquery}
				WHERE post_status IN ({$all_states}) $post_type_clause
				ORDER BY {$orderby} {$order}
				LIMIT %d,%d
			";

		return $query;
	}

	/**
	 * Heavily restricts the possible columns by which a user can order the table
	 * in the bulk editor, thereby preventing a possible CSRF vulnerability.
	 *
	 * @param string $orderby The column by which we want to order.
	 *
	 * @return string
	 */
	protected function sanitize_orderby( $orderby ) {
		$valid_column_names = [
			'post_title',
			'post_type',
			'post_date',
		];

		if ( in_array( $orderby, $valid_column_names, true ) ) {
			return $orderby;
		}

		return 'post_title';
	}

	/**
	 * Makes sure the order clause is always ASC or DESC for the bulk editor table,
	 * thereby preventing a possible CSRF vulnerability.
	 *
	 * @param string $order Whether we want to sort ascending or descending.
	 *
	 * @return string SQL order string (ASC, DESC).
	 */
	protected function sanitize_order( $order ) {
		if ( in_array( strtoupper( $order ), [ 'ASC', 'DESC' ], true ) ) {
			return $order;
		}

		return 'ASC';
	}

	/**
	 * Getting all the items.
	 *
	 * @param string $query SQL query to use.
	 *
	 * @return void
	 */
	protected function get_items( $query ) {
		global $wpdb;

		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				$query,
				$this->pagination['offset'],
				$this->pagination['per_page']
			)
		);
	}

	/**
	 * Getting all the states.
	 *
	 * @return string
	 */
	protected function get_all_states() {
		global $wpdb;

		$states          = get_post_stati( [ 'show_in_admin_all_list' => true ] );
		$states['trash'] = 'trash';

		if ( ! empty( $this->input_fields['post_status'] ) ) {
			$requested_state = $this->input_fields['post_status'];
			if ( in_array( $requested_state, $states, true ) ) {
				$states = [ $requested_state ];
			}

			if ( $requested_state !== 'trash' ) {
				unset( $states['trash'] );
			}
		}

		return $wpdb->prepare(
			implode( ', ', array_fill( 0, count( $states ), '%s' ) ),
			$states
		);
	}

	/**
	 * Based on $this->items and the defined columns, the table rows will be displayed.
	 *
	 * @return void
	 */
	public function display_rows() {

		$records = $this->items;

		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		if ( ( is_array( $records ) && $records !== [] ) && ( is_array( $columns ) && $columns !== [] ) ) {

			foreach ( $records as $record ) {

				echo '<tr id="', esc_attr( 'record_' . $record->ID ), '">';

				foreach ( $columns as $column_name => $column_display_name ) {

					$classes = '';
					if ( $primary === $column_name ) {
						$classes .= ' has-row-actions column-primary';
					}

					$attributes = $this->column_attributes( $column_name, $hidden, $classes, $column_display_name );

					$column_value = $this->parse_column( $column_name, $record );

					if ( method_exists( $this, 'parse_page_specific_column' ) && empty( $column_value ) ) {
						$column_value = $this->parse_page_specific_column( $column_name, $record, $attributes );
					}

					if ( ! empty( $column_value ) ) {
						printf( '<td %2$s>%1$s</td>', $column_value, $attributes );
					}
				}

				echo '</tr>';
			}
		}
	}

	/**
	 * Getting the attributes for each table cell.
	 *
	 * @param string $column_name         Column name string.
	 * @param array  $hidden              Set of hidden columns.
	 * @param string $classes             Additional CSS classes.
	 * @param string $column_display_name Column display name string.
	 *
	 * @return string
	 */
	protected function column_attributes( $column_name, $hidden, $classes, $column_display_name ) {

		$attributes = '';
		$class      = [ $column_name, "column-$column_name$classes" ];

		if ( in_array( $column_name, $hidden, true ) ) {
			$class[] = 'hidden';
		}

		if ( ! empty( $class ) ) {
			$attributes = 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
		}

		$attributes .= ' data-colname="' . esc_attr( $column_display_name ) . '"';

		return $attributes;
	}

	/**
	 * Parsing the title.
	 *
	 * @param WP_Post $rec Post object.
	 *
	 * @return string
	 */
	protected function parse_page_title_column( $rec ) {

		$title = empty( $rec->post_title ) ? __( '(no title)', 'wordpress-seo' ) : $rec->post_title;

		$return = sprintf( '<strong>%1$s</strong>', stripslashes( wp_strip_all_tags( $title ) ) );

		$post_type_object = get_post_type_object( $rec->post_type );
		$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $rec->ID );

		$actions = [];

		if ( $can_edit_post && $rec->post_status !== 'trash' ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( get_edit_post_link( $rec->ID, true ) ),
				/* translators: Hidden accessibility text; %s: post title. */
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'wordpress-seo' ), $title ) ),
				__( 'Edit', 'wordpress-seo' )
			);
		}

		if ( $post_type_object->public ) {
			if ( in_array( $rec->post_status, [ 'pending', 'draft', 'future' ], true ) ) {
				if ( $can_edit_post ) {
					$actions['view'] = sprintf(
						'<a href="%s" aria-label="%s">%s</a>',
						esc_url( add_query_arg( 'preview', 'true', get_permalink( $rec->ID ) ) ),
						/* translators: Hidden accessibility text; %s: post title. */
						esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'wordpress-seo' ), $title ) ),
						__( 'Preview', 'wordpress-seo' )
					);
				}
			}
			elseif ( $rec->post_status !== 'trash' ) {
				$actions['view'] = sprintf(
					'<a href="%s" aria-label="%s" rel="bookmark">%s</a>',
					esc_url( get_permalink( $rec->ID ) ),
					/* translators: Hidden accessibility text; %s: post title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'wordpress-seo' ), $title ) ),
					__( 'View', 'wordpress-seo' )
				);
			}
		}

		$return .= $this->row_actions( $actions );

		return $return;
	}

	/**
	 * Parsing the column based on the $column_name.
	 *
	 * @param string  $column_name Column name.
	 * @param WP_Post $rec         Post object.
	 *
	 * @return string
	 */
	protected function parse_column( $column_name, $rec ) {

		static $date_format;

		if ( ! isset( $date_format ) ) {
			$date_format = get_option( 'date_format' );
		}

		switch ( $column_name ) {
			case 'col_page_title':
				$column_value = $this->parse_page_title_column( $rec );
				break;

			case 'col_page_slug':
				$permalink    = get_permalink( $rec->ID );
				$display_slug = str_replace( get_bloginfo( 'url' ), '', $permalink );
				$column_value = sprintf( '<a href="%2$s" target="_blank">%1$s</a>', stripslashes( rawurldecode( $display_slug ) ), esc_url( $permalink ) );
				break;

			case 'col_post_type':
				$post_type    = get_post_type_object( $rec->post_type );
				$column_value = $post_type->labels->singular_name;
				break;

			case 'col_post_status':
				$post_status  = get_post_status_object( $rec->post_status );
				$column_value = $post_status->label;
				break;

			case 'col_post_date':
				$column_value = date_i18n( $date_format, strtotime( $rec->post_date ) );
				break;

			case 'col_row_action':
				$column_value = sprintf(
					'<a href="#" role="button" class="wpseo-save" data-id="%1$s">%2$s</a> <span aria-hidden="true">|</span> <a href="#" role="button" class="wpseo-save-all">%3$s</a>',
					$rec->ID,
					esc_html__( 'Save', 'wordpress-seo' ),
					esc_html__( 'Save all', 'wordpress-seo' )
				);
				break;
		}

		if ( ! empty( $column_value ) ) {
			return $column_value;
		}
	}

	/**
	 * Parse the field where the existing meta-data value is displayed.
	 *
	 * @param int        $record_id  Record ID.
	 * @param string     $attributes HTML attributes.
	 * @param bool|array $values     Optional values data array.
	 *
	 * @return string
	 */
	protected function parse_meta_data_field( $record_id, $attributes, $values = false ) {

		// Fill meta data if exists in $this->meta_data.
		$meta_data  = ( ! empty( $this->meta_data[ $record_id ] ) ) ? $this->meta_data[ $record_id ] : [];
		$meta_key   = WPSEO_Meta::$meta_prefix . $this->target_db_field;
		$meta_value = ( ! empty( $meta_data[ $meta_key ] ) ) ? $meta_data[ $meta_key ] : '';

		if ( ! empty( $values ) ) {
			$meta_value = $values[ $meta_value ];
		}

		$id = "wpseo-existing-$this->target_db_field-$record_id";

		// $attributes correctly escaped, verified by Alexander. See WPSEO_Bulk_Description_List_Table::parse_page_specific_column.
		return sprintf( '<td %2$s id="%3$s">%1$s</td>', esc_html( $meta_value ), $attributes, esc_attr( $id ) );
	}

	/**
	 * Method for setting the meta data, which belongs to the records that will be shown on the current page.
	 *
	 * This method will loop through the current items ($this->items) for getting the post_id. With this data
	 * ($needed_ids) the method will query the meta-data table for getting the title.
	 *
	 * @return void
	 */
	protected function get_meta_data() {

		$post_ids  = $this->get_post_ids();
		$meta_data = $this->get_meta_data_result( $post_ids );

		$this->parse_meta_data( $meta_data );

		// Little housekeeping.
		unset( $post_ids, $meta_data );
	}

	/**
	 * Getting all post_ids from to $this->items.
	 *
	 * @return array
	 */
	protected function get_post_ids() {
		$post_ids = [];
		foreach ( $this->items as $item ) {
			$post_ids[] = $item->ID;
		}

		return $post_ids;
	}

	/**
	 * Getting the meta_data from database.
	 *
	 * @param array $post_ids Post IDs for SQL IN part.
	 *
	 * @return mixed
	 */
	protected function get_meta_data_result( array $post_ids ) {
		global $wpdb;

		$where = $wpdb->prepare(
			'post_id IN (' . implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) ) . ')',
			$post_ids
		);

		$where .= $wpdb->prepare( ' AND meta_key = %s', WPSEO_Meta::$meta_prefix . $this->target_db_field );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- They are prepared on the lines above.
		return $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE {$where}" );
	}

	/**
	 * Setting $this->meta_data.
	 *
	 * @param array $meta_data Meta data set.
	 *
	 * @return void
	 */
	protected function parse_meta_data( $meta_data ) {

		foreach ( $meta_data as $row ) {
			$this->meta_data[ $row->post_id ][ $row->meta_key ] = $row->meta_value;
		}
	}

	/**
	 * This method will merge general array with given parameter $columns.
	 *
	 * @param array $columns Optional columns set.
	 *
	 * @return array
	 */
	protected function merge_columns( $columns = [] ) {
		$columns = array_merge(
			[
				'col_page_title'  => __( 'WP Page Title', 'wordpress-seo' ),
				'col_post_type'   => __( 'Content Type', 'wordpress-seo' ),
				'col_post_status' => __( 'Post Status', 'wordpress-seo' ),
				'col_post_date'   => __( 'Publication date', 'wordpress-seo' ),
				'col_page_slug'   => __( 'Page URL/Slug', 'wordpress-seo' ),
			],
			$columns
		);

		$columns['col_row_action'] = __( 'Action', 'wordpress-seo' );

		return $columns;
	}
}
