<?php

namespace Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Admin;

use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\StoredUrl;
use WP_List_Table;
use WP_Screen;

/**
 * Admin list table used to render our current list of approved directories.
 */
class Table extends WP_List_Table {
	/**
	 * Initialize the webhook table list.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'url',
				'plural'   => 'urls',
				'ajax'     => false,
			)
		);

		add_filter( 'manage_woocommerce_page_wc-settings_columns', array( $this, 'get_columns' ) );
		$this->items_per_page();
		set_screen_options();
	}

	/**
	 * Sets up an items-per-page control.
	 */
	private function items_per_page() {
		add_screen_option(
			'per_page',
			array(
				'default' => 20,
				'option'  => 'edit_approved_directories_per_page',
			)
		);

		add_filter( 'set_screen_option_edit_approved_directories_per_page', array( $this, 'set_items_per_page' ), 10, 3 );
	}

	/**
	 * Saves the items-per-page setting.
	 *
	 * @param mixed  $default The default value.
	 * @param string $option  The option being configured.
	 * @param int    $value   The submitted option value.
	 *
	 * @return mixed
	 */
	public function set_items_per_page( $default, string $option, int $value ) {
		return 'edit_approved_directories_per_page' === $option ? absint( $value ) : $default;
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No approved directory URLs found.', 'woocommerce' );
	}

	/**
	 * Displays the list of views available on this table.
	 */
	public function render_views() {
		$register = wc_get_container()->get( Register::class );

		$enabled_count  = $register->count( true );
		$disabled_count = $register->count( false );
		$all_count      = $enabled_count + $disabled_count;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$selected_view = isset( $_REQUEST['view'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['view'] ) ) : 'all';

		$all_url   = esc_url( add_query_arg( 'view', 'all', $this->get_base_url() ) );
		$all_class = 'all' === $selected_view ? 'class="current"' : '';
		$all_text  = sprintf(
			/* translators: %s is the count of approved directory list entries. */
			_nx(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				$all_count,
				'Approved product download directory views',
				'woocommerce'
			),
			$all_count
		);

		$enabled_url   = esc_url( add_query_arg( 'view', 'enabled', $this->get_base_url() ) );
		$enabled_class = 'enabled' === $selected_view ? 'class="current"' : '';
		$enabled_text  = sprintf(
			/* translators: %s is the count of enabled approved directory list entries. */
			_nx(
				'Enabled <span class="count">(%s)</span>',
				'Enabled <span class="count">(%s)</span>',
				$enabled_count,
				'Approved product download directory views',
				'woocommerce'
			),
			$enabled_count
		);

		$disabled_url   = esc_url( add_query_arg( 'view', 'disabled', $this->get_base_url() ) );
		$disabled_class = 'disabled' === $selected_view ? 'class="current"' : '';
		$disabled_text  = sprintf(
			/* translators: %s is the count of disabled directory list entries. */
			_nx(
				'Disabled <span class="count">(%s)</span>',
				'Disabled <span class="count">(%s)</span>',
				$disabled_count,
				'Approved product download directory views',
				'woocommerce'
			),
			$disabled_count
		);

		$views = array(
			'all'      => "<a href='{$all_url}' {$all_class}>{$all_text}</a>",
			'enabled'  => "<a href='{$enabled_url}' {$enabled_class}>{$enabled_text}</a>",
			'disabled' => "<a href='{$disabled_url}' {$disabled_class}>{$disabled_text}</a>",
		);

		$this->screen->render_screen_reader_content( 'heading_views' );

		echo '<ul class="subsubsub list-table-filters">';
		foreach ( $views as $slug => $view ) {
			$views[ $slug ] = "<li class='{$slug}'>{$view}";
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo implode( ' | </li>', $views ) . "</li>\n";
		echo '</ul>';
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'    => '<input type="checkbox" />',
			'title' => _x( 'URL', 'Approved product download directories', 'woocommerce' ),
			'enabled' => _x( 'Enabled', 'Approved product download directories', 'woocommerce' ),
		);
	}

	/**
	 * Checklist column, used for selecting items for processing by a bulk action.
	 *
	 * @param StoredUrl $item The approved directory information for the current row.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', esc_attr( $this->_args['singular'] ), esc_attr( $item->get_id() ) );
	}

	/**
	 * URL column.
	 *
	 * @param StoredUrl $item The approved directory information for the current row.
	 *
	 * @return string
	 */
	public function column_title( $item ) {
		$id      = (int) $item->get_id();
		$url     = esc_html( $item->get_url() );
		$enabled = $item->is_enabled();

		$edit_url            = esc_url( $this->get_action_url( 'edit', $id ) );
		$enable_disable_url  = esc_url( $enabled ? $this->get_action_url( 'disable', $id ) : $this->get_action_url( 'enable', $id ) );
		$enable_disable_text = esc_html( $enabled ? __( 'Disable', 'woocommerce' ) : __( 'Enable', 'woocommerce' ) );
		$delete_url          = esc_url( $this->get_action_url( 'delete', $id ) );
		$edit_link           = "<a href='{$edit_url}'>" . esc_html_x( 'Edit', 'Product downloads list', 'woocommerce' ) . '</a>';
		$enable_disable_link = "<a href='{$enable_disable_url}'>{$enable_disable_text}</a>";
		$delete_link         = "<a href='{$delete_url}' class='submitdelete wc-confirm-delete'>" . esc_html_x( 'Delete permanently', 'Product downloads list', 'woocommerce' ) . '</a>';
		$url_link            = "<a href='{$edit_url}'>{$url}</a>";

		return "
			<strong>{$url_link}</strong>
			<div class='row-actions'>
				<span class='id'>ID: {$id}</span> |
				<span class='edit'>{$edit_link}</span> |
				<span class='enable-disable'>{$enable_disable_link}</span> |
				<span class='delete'><a class='submitdelete'>{$delete_link}</a></span>
			</div>
		";
	}

	/**
	 * Rule-is-enabled column.
	 *
	 * @param StoredUrl $item The approved directory information for the current row.
	 *
	 * @return string
	 */
	public function column_enabled( StoredUrl $item ): string {
		return $item->is_enabled()
			? '<mark class="yes" title="' . esc_html__( 'Enabled', 'woocommerce' ) . '"><span class="dashicons dashicons-yes"></span></mark>'
			: '<mark class="no" title="' . esc_html__( 'Disabled', 'woocommerce' ) . '">&ndash;</mark>';
	}

	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'enable'  => __( 'Enable rule', 'woocommerce' ),
			'disable' => __( 'Disable rule', 'woocommerce' ),
			'delete'  => __( 'Delete permanently', 'woocommerce' ),
		);
	}

	/**
	 * Builds an action URL (ie, to edit or delete a row).
	 *
	 * @param string $action       The action to be created.
	 * @param int    $id           The ID that is the subject of the action.
	 * @param string $nonce_action Action used to add a nonce to the URL.
	 *
	 * @return string
	 */
	public function get_action_url( string $action, int $id, string $nonce_action = 'modify_approved_directories' ): string {
		return add_query_arg(
			array(
				'check'  => wp_create_nonce( $nonce_action ),
				'action' => $action,
				'url'    => $id,
			),
			$this->get_base_url()
		);
	}

	/**
	 * Supplies the 'base' admin URL for this admin table.
	 *
	 * @return string
	 */
	public function get_base_url(): string {
		return add_query_arg(
			array(
				'page'    => 'wc-settings',
				'tab'     => 'products',
				'section' => 'download_urls',
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Generate the table navigation above or below the table.
	 * Included to remove extra nonce input.
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		$directories = wc_get_container()->get( Register::class );
		echo '<div class="tablenav ' . esc_attr( $which ) . '">';

		if ( $this->has_items() ) {
			echo '<div class="alignleft actions bulkactions">';
			$this->bulk_actions( $which );

			if ( $directories->count( false ) > 0 ) {
				echo '<a href="' . esc_url( $this->get_action_url( 'enable-all', 0 ) ) . '" class="wp-core-ui button">' . esc_html_x( 'Enable All', 'Approved product download directories', 'woocommerce' ) . '</a> ';
			}

			if ( $directories->count( true ) > 0 ) {
				echo '<a href="' . esc_url( $this->get_action_url( 'disable-all', 0 ) ) . '" class="wp-core-ui button">' . esc_html_x( 'Disable All', 'Approved product download directories', 'woocommerce' ) . '</a>';
			}

			echo '</div>';
		}

		$this->pagination( $which );
		echo '<br class="clear" />';
		echo '</div>';
	}

	/**
	 * Prepare table list items.
	 */
	public function prepare_items() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$current_page = $this->get_pagenum();
		$per_page     = $this->get_items_per_page( 'edit_approved_directories_per_page' );
		$search       = sanitize_text_field( wp_unslash( $_REQUEST['s'] ?? '' ) );

		switch ( $_REQUEST['view'] ?? '' ) {
			case 'enabled':
				$enabled = true;
				break;

			case 'disabled':
				$enabled = false;
				break;

			default:
				$enabled = null;
				break;
		}
		// phpcs:enable

		$approved_directories = wc_get_container()->get( Register::class )->list(
			array(
				'page'     => $current_page,
				'per_page' => $per_page,
				'search'   => $search,
				'enabled'  => $enabled,
			)
		);

		$this->items = $approved_directories['approved_directories'];

		// Set the pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $approved_directories['total_urls'],
				'total_pages' => $approved_directories['total_pages'],
				'per_page'    => $per_page,
			)
		);
	}
}
