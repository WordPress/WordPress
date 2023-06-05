<?php
/**
 * List tables.
 *
 * @package  WooCommerce\Admin
 * @version  3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Admin_List_Table', false ) ) {
	return;
}

/**
 * WC_Admin_List_Table Class.
 */
abstract class WC_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = '';

	/**
	 * Object being shown on the row.
	 *
	 * @var object|null
	 */
	protected $object = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( $this->list_table_type ) {
			add_action( 'manage_posts_extra_tablenav', array( $this, 'maybe_render_blank_state' ) );
			add_filter( 'view_mode_post_types', array( $this, 'disable_view_mode' ) );
			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
			add_filter( 'request', array( $this, 'request_query' ) );
			add_filter( 'post_row_actions', array( $this, 'row_actions' ), 100, 2 );
			add_filter( 'default_hidden_columns', array( $this, 'default_hidden_columns' ), 10, 2 );
			add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
			add_filter( 'manage_edit-' . $this->list_table_type . '_sortable_columns', array( $this, 'define_sortable_columns' ) );
			add_filter( 'manage_' . $this->list_table_type . '_posts_columns', array( $this, 'define_columns' ) );
			add_filter( 'bulk_actions-edit-' . $this->list_table_type, array( $this, 'define_bulk_actions' ) );
			add_action( 'manage_' . $this->list_table_type . '_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );
			add_filter( 'handle_bulk_actions-edit-' . $this->list_table_type, array( $this, 'handle_bulk_actions' ), 10, 3 );
		}
	}

	/**
	 * Show blank slate.
	 *
	 * @param string $which String which tablenav is being shown.
	 */
	public function maybe_render_blank_state( $which ) {
		global $post_type;

		if ( $post_type === $this->list_table_type && 'bottom' === $which ) {
			$counts = (array) wp_count_posts( $post_type );
			unset( $counts['auto-draft'] );
			$count = array_sum( $counts );

			if ( 0 < $count ) {
				return;
			}

			$this->render_blank_state();

			echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom { height: auto; } </style>';
		}
	}

	/**
	 * Render blank state. Extend to add content.
	 */
	protected function render_blank_state() {}

	/**
	 * Removes this type from list of post types that support "View Mode" switching.
	 * View mode is seen on posts where you can switch between list or excerpt. Our post types don't support
	 * it, so we want to hide the useless UI from the screen options tab.
	 *
	 * @param  array $post_types Array of post types supporting view mode.
	 * @return array             Array of post types supporting view mode, without this type.
	 */
	public function disable_view_mode( $post_types ) {
		unset( $post_types[ $this->list_table_type ] );
		return $post_types;
	}

	/**
	 * See if we should render search filters or not.
	 */
	public function restrict_manage_posts() {
		global $typenow;

		if ( $this->list_table_type === $typenow ) {
			$this->render_filters();
		}
	}

	/**
	 * Handle any filters.
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function request_query( $query_vars ) {
		global $typenow;

		if ( $this->list_table_type === $typenow ) {
			return $this->query_filters( $query_vars );
		}

		return $query_vars;
	}

	/**
	 * Render any custom filters and search inputs for the list table.
	 */
	protected function render_filters() {}

	/**
	 * Handle any custom filters.
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	protected function query_filters( $query_vars ) {
		return $query_vars;
	}

	/**
	 * Set row actions.
	 *
	 * @param array   $actions Array of actions.
	 * @param WP_Post $post Current post object.
	 * @return array
	 */
	public function row_actions( $actions, $post ) {
		if ( $this->list_table_type === $post->post_type ) {
			return $this->get_row_actions( $actions, $post );
		}
		return $actions;
	}

	/**
	 * Get row actions to show in the list table.
	 *
	 * @param array   $actions Array of actions.
	 * @param WP_Post $post Current post object.
	 * @return array
	 */
	protected function get_row_actions( $actions, $post ) {
		return $actions;
	}

	/**
	 * Adjust which columns are displayed by default.
	 *
	 * @param array  $hidden Current hidden columns.
	 * @param object $screen Current screen.
	 * @return array
	 */
	public function default_hidden_columns( $hidden, $screen ) {
		if ( isset( $screen->id ) && 'edit-' . $this->list_table_type === $screen->id ) {
			$hidden = array_merge( $hidden, $this->define_hidden_columns() );
		}
		return $hidden;
	}

	/**
	 * Set list table primary column.
	 *
	 * @param  string $default Default value.
	 * @param  string $screen_id Current screen ID.
	 * @return string
	 */
	public function list_table_primary_column( $default, $screen_id ) {
		if ( 'edit-' . $this->list_table_type === $screen_id && $this->get_primary_column() ) {
			return $this->get_primary_column();
		}
		return $default;
	}

	/**
	 * Define primary column.
	 *
	 * @return array
	 */
	protected function get_primary_column() {
		return '';
	}

	/**
	 * Define hidden columns.
	 *
	 * @return array
	 */
	protected function define_hidden_columns() {
		return array();
	}

	/**
	 * Define which columns are sortable.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_sortable_columns( $columns ) {
		return $columns;
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_columns( $columns ) {
		return $columns;
	}

	/**
	 * Define bulk actions.
	 *
	 * @param array $actions Existing actions.
	 * @return array
	 */
	public function define_bulk_actions( $actions ) {
		return $actions;
	}

	/**
	 * Pre-fetch any data for the row each column has access to it.
	 *
	 * @param int $post_id Post ID being shown.
	 */
	protected function prepare_row_data( $post_id ) {}

	/**
	 * Render individual columns.
	 *
	 * @param string $column Column ID to render.
	 * @param int    $post_id Post ID being shown.
	 */
	public function render_columns( $column, $post_id ) {
		$this->prepare_row_data( $post_id );

		if ( ! $this->object ) {
			return;
		}

		if ( is_callable( array( $this, 'render_' . $column . '_column' ) ) ) {
			$this->{"render_{$column}_column"}();
		}
	}

	/**
	 * Handle bulk actions.
	 *
	 * @param  string $redirect_to URL to redirect to.
	 * @param  string $action      Action name.
	 * @param  array  $ids         List of ids.
	 * @return string
	 */
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {
		return esc_url_raw( $redirect_to );
	}
}
