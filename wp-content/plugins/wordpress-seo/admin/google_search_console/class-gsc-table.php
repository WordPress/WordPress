<?php
/**
 * @package WPSEO\Admin|Google_Search_Console
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class WPSEO_GSC_Table
 */
class WPSEO_GSC_Table extends WP_List_Table {

	/**
	 * @var string
	 */
	private $search_string;

	/**
	 * @var array
	 */
	protected $_column_headers;

	/**
	 * The category that is displayed
	 *
	 * @var mixed|string
	 */
	private $current_view;

	/**
	 * @var integer
	 */
	private $per_page = 50;

	/**
	 * @var integer
	 */
	private $current_page = 1;

	/**
	 * @var array
	 */
	private $modal_heights = array(
		'create'         => 350,
		'no_premium'     => 125,
		'already_exists' => 150,
	);

	/**
	 * The constructor
	 *
	 * @param string $platform
	 * @param string $category
	 * @param array  $items
	 */
	public function __construct( $platform, $category, array $items ) {
		parent::__construct();

		// Adding the thickbox.
		add_thickbox();

		// Set search string.
		if ( ( $search_string = filter_input( INPUT_GET, 's' ) ) != '' ) {
			$this->search_string = $search_string;
		}

		$this->current_view = $category;

		// Set the crawl issue source.
		$this->show_fields( $platform );

		$this->items = $items;
	}

	/**
	 * Getting the screen id from this table
	 *
	 * @return string
	 */
	public function get_screen_id() {
		return $this->screen->id;
	}

	/**
	 * Setup the table variables, fetch the items from the database, search, sort and format the items.
	 */
	public function prepare_items() {
		// Get variables needed for pagination.
		$this->per_page     = $this->get_items_per_page( 'errors_per_page', $this->per_page );
		$this->current_page = intval( ( $paged = filter_input( INPUT_GET, 'paged' ) ) ? $paged : 1 );

		$this->setup_columns();
		$this->views();
		$this->parse_items();
	}

	/**
	 * Set the table columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'             => '<input type="checkbox" />',
			'url'            => __( 'URL', 'wordpress-seo' ),
			'last_crawled'   => __( 'Last crawled', 'wordpress-seo' ),
			'first_detected' => __( 'First detected', 'wordpress-seo' ),
			'response_code'  => __( 'Response code', 'wordpress-seo' ),
		);

		return $columns;
	}

	/**
	 * Return the columns that are sortable
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'url'            => array( 'url', false ),
			'last_crawled'   => array( 'last_crawled', false ),
			'first_detected' => array( 'first_detected', false ),
			'response_code'  => array( 'response_code', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Return available bulk actions
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'mark_as_fixed' => __( 'Mark as fixed', 'wordpress-seo' ),
		);
	}

	/**
	 * Default method to display a column
	 *
	 * @param array  $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	protected function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Checkbox column
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="wpseo_crawl_issues[]" value="%s" />', $item['url']
		);
	}

	/**
	 * Formatting the output of the column last crawled into a dateformat
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_last_crawled( $item ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $item['last_crawled'] ) );
	}

	/**
	 * Formatting the output of the column first detected into a dateformat
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_first_detected( $item ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $item['first_detected'] ) );
	}

	/**
	 * URL column
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_url( $item ) {
		$actions = array();

		if ( $this->can_create_redirect() ) {
			/**
			 * Modal box
			 */
			$modal_height = $this->modal_box( $item['url'] );

			$actions['create_redirect'] = '<a title="' . __( 'Create a redirect', 'wordpress-seo' ) . '" href="#TB_inline?width=600&height=' . $this->modal_heights[ $modal_height ] . '&inlineId=redirect-' . md5( $item['url'] ) . '" class="thickbox">' . __( 'Create redirect', 'wordpress-seo' ) . '</a>';
		}

		$actions['view']        = '<a href="' . $item['url'] . '" target="_blank">' . __( 'View', 'wordpress-seo' ) . '</a>';
		$actions['markasfixed'] = '<a href="javascript:wpseo_mark_as_fixed(\'' . urlencode( $item['url'] ) . '\');">' . __( 'Mark as fixed', 'wordpress-seo' ) . '</a>';

		return sprintf(
			'<span class="value">%1$s</span> %2$s',
			$item['url'],
			$this->row_actions( $actions )
		);
	}

	/**
	 * Running the setup of the columns
	 */
	private function setup_columns() {
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
	}

	/**
	 * Check if the current category allow creating redirects
	 * @return bool
	 */
	private function can_create_redirect() {
		return in_array( $this->current_view, array( 'soft_404', 'not_found', 'access_denied' ) );
	}

	/**
	 * Setting the table navigation
	 *
	 * @param int $total_items
	 * @param int $posts_per_page
	 */
	private function set_pagination( $total_items, $posts_per_page ) {
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => ceil( ( $total_items / $posts_per_page ) ),
			'per_page'    => $posts_per_page,
		) );
	}

	/**
	 * Setting the items
	 */
	private function parse_items() {
		if ( is_array( $this->items ) && count( $this->items ) > 0 ) {
			if ( ! empty( $this->search_string ) ) {
				$this->do_search();
			}

			$this->set_pagination( count( $this->items ), $this->per_page );

			$this->sort_items();
			$this->paginate_items();
		}
	}

	/**
	 * Search through the items
	 */
	private function do_search() {
		$results = array();

		foreach ( $this->items as $item ) {
			foreach ( $item as $value ) {
				if ( stristr( $value, $this->search_string ) !== false ) {
					$results[] = $item;
					continue;
				}
			}
		}

		$this->items = $results;
	}

	/**
	 * Running the pagination
	 */
	private function paginate_items() {
		// Setting the starting point. If starting point is below 1, overwrite it with value 0, otherwise it will be sliced of at the back.
		$slice_start = ( $this->current_page - 1 );
		if ( $slice_start < 0 ) {
			$slice_start = 0;
		}

		// Apply 'pagination'.
		$this->items = array_slice( $this->items, ( $slice_start * $this->per_page ), $this->per_page );
	}

	/**
	 * Sort the items by callback
	 */
	private function sort_items() {
		// Sort the results.
		usort( $this->items, array( $this, 'do_reorder' ) );
	}

	/**
	 * Doing the sorting of the issues
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	private function do_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ( $orderby = filter_input( INPUT_GET, 'orderby' ) ) ? $orderby : 'url';

		// If no order, default to asc.
		$order = ( $order = filter_input( INPUT_GET, 'order' ) ) ? $order : 'asc';

		// When there is a raw field of it, sort by this field.
		if ( array_key_exists( $orderby . '_raw', $a ) && array_key_exists( $orderby . '_raw', $b ) ) {
			$orderby = $orderby . '_raw';
		}

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		// Send final sort direction to usort.
		return ( $order === 'asc' ) ? $result : ( - $result );
	}

	/**
	 * Modal box
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	private function modal_box( $url ) {
		$current_redirect = false;
		$view_type        = $this->modal_box_type( $url, $current_redirect );

		require WPSEO_PATH . '/admin/google_search_console/views/gsc-create-redirect.php';

		return $view_type;
	}

	/**
	 * Determine which model box type should be rendered
	 *
	 * @param string $url
	 * @param string $current_redirect
	 *
	 * @return string
	 */
	private function modal_box_type( $url, &$current_redirect ) {
		if ( defined( 'WPSEO_PREMIUM_FILE' ) && class_exists( 'WPSEO_URL_Redirect_Manager' ) ) {
			static $redirect_manager;

			if ( ! $redirect_manager ) {
				$redirect_manager = new WPSEO_URL_Redirect_Manager();
			}

			if ( $current_redirect = $redirect_manager->search_url( $url ) ) {
				return 'already_exists';
			}

			return 'create';
		}

		return 'no_premium';
	}


	/**
	 * Showing the hidden fields used by the AJAX requests
	 *
	 * @param string $platform
	 */
	private function show_fields( $platform ) {
		echo "<input type='hidden' name='wpseo_gsc_nonce' value='" . wp_create_nonce( 'wpseo_gsc_nonce' ) . "' />";
		echo "<input id='field_platform' type='hidden' name='platform' value='{$platform}' />";
		echo "<input id='field_category' type='hidden' name='category' value='{$this->current_view}' />";
	}

}
