<?php
/**
 * Links Manager List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_Links_List_Table extends WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array(
			'plural' => 'bookmarks',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
	}

	public function ajax_user_can() {
		return current_user_can( 'manage_links' );
	}

	/**
	 *
	 * @global int    $cat_id
	 * @global string $s
	 * @global string $orderby
	 * @global string $order
	 */
	public function prepare_items() {
		global $cat_id, $s, $orderby, $order;

		wp_reset_vars( array( 'action', 'cat_id', 'link_id', 'orderby', 'order', 's' ) );

		$args = array( 'hide_invisible' => 0, 'hide_empty' => 0 );

		if ( 'all' != $cat_id )
			$args['category'] = $cat_id;
		if ( !empty( $s ) )
			$args['search'] = $s;
		if ( !empty( $orderby ) )
			$args['orderby'] = $orderby;
		if ( !empty( $order ) )
			$args['order'] = $order;

		$this->items = get_bookmarks( $args );
	}

	public function no_items() {
		_e( 'No links found.' );
	}

	protected function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete' );

		return $actions;
	}

	/**
	 *
	 * @global int $cat_id
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $cat_id;

		if ( 'top' != $which )
			return;
?>
		<div class="alignleft actions">
<?php
			$dropdown_options = array(
				'selected' => $cat_id,
				'name' => 'cat_id',
				'taxonomy' => 'link_category',
				'show_option_all' => __( 'All categories' ),
				'hide_empty' => true,
				'hierarchical' => 1,
				'show_count' => 0,
				'orderby' => 'name',
			);

			echo '<label class="screen-reader-text" for="cat_id">' . __( 'Filter by category' ) . '</label>';
			wp_dropdown_categories( $dropdown_options );
			submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
?>
		</div>
<?php
	}

	public function get_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'name'       => _x( 'Name', 'link name' ),
			'url'        => __( 'URL' ),
			'categories' => __( 'Categories' ),
			'rel'        => __( 'Relationship' ),
			'visible'    => __( 'Visible' ),
			'rating'     => __( 'Rating' )
		);
	}

	protected function get_sortable_columns() {
		return array(
			'name'    => 'name',
			'url'     => 'url',
			'visible' => 'visible',
			'rating'  => 'rating'
		);
	}

	/**
	 * Get name of default primary column
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_default_primary_column_name() {
		return 'name';
	}

	/**
	 *
	 * @global int $cat_id
	 */
	public function display_rows() {
		global $cat_id;

		foreach ( $this->items as $link ) {
			$link = sanitize_bookmark( $link );
			$link->link_name = esc_attr( $link->link_name );
			$link->link_category = wp_get_link_cats( $link->link_id );

			$short_url = url_shorten( $link->link_url );

			$visible = ( $link->link_visible == 'Y' ) ? __( 'Yes' ) : __( 'No' );
			$rating  = $link->link_rating;

			$edit_link = get_edit_bookmark_link( $link );
?>
		<tr id="link-<?php echo $link->link_id; ?>">
<?php

			list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) {
				$classes = "$column_name column-$column_name";
				if ( $primary === $column_name ) {
					$classes .= ' has-row-actions column-primary';
				}

				$style = '';
				if ( in_array( $column_name, $hidden ) ) {
					$style = ' style="display:none;"';
				}

				$attributes = "class='$classes'$style";

				if ( 'cb' === $column_name ) {
					?>
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-<?php echo $link->link_id; ?>"><?php echo sprintf( __( 'Select %s' ), $link->link_name ); ?></label>
						<input type="checkbox" name="linkcheck[]" id="cb-select-<?php echo $link->link_id; ?>" value="<?php echo esc_attr( $link->link_id ); ?>" />
					</th>
					<?php
				} else {
					echo "<td $attributes>";

					switch ( $column_name ) {
						case 'name':
							echo "<strong><a class='row-title' href='$edit_link' title='" . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $link->link_name ) ) . "'>$link->link_name</a></strong><br />";
							break;
						case 'url':
							echo "<a href='$link->link_url' title='". esc_attr( sprintf( __( 'Visit %s' ), $link->link_name ) )."'>$short_url</a>";
							break;
						case 'categories':
							$cat_names = array();
							foreach ( $link->link_category as $category ) {
								$cat = get_term( $category, 'link_category', OBJECT, 'display' );
								if ( is_wp_error( $cat ) )
									echo $cat->get_error_message();
								$cat_name = $cat->name;
								if ( $cat_id != $category )
									$cat_name = "<a href='link-manager.php?cat_id=$category'>$cat_name</a>";
								$cat_names[] = $cat_name;
							}
							echo implode( ', ', $cat_names );
							break;
						case 'rel':
							echo empty( $link->link_rel ) ? '<br />' : $link->link_rel;
							break;
						case 'visible':
							echo $visible;
							break;
						case 'rating':
							echo $rating;
							break;
						default:
							/**
							 * Fires for each registered custom link column.
							 *
							 * @since 2.1.0
							 *
							 * @param string $column_name Name of the custom column.
							 * @param int    $link_id     Link ID.
							 */
							do_action( 'manage_link_custom_column', $column_name, $link->link_id );
							break;
					}

					echo $this->handle_row_actions( $link, $column_name, $primary );
					echo '</td>';
				}
			}
?>
		</tr>
<?php
		}
	}

	/**
	 * Generate and display row actions links
	 *
	 * @since 4.3
	 * @access protected
	 *
	 * @param object $link Link being acted upon
	 * @param string $column_name Current column name
	 * @param string $primary Primary column name
	 *
	 * @return string
	 */
	protected function handle_row_actions( $link, $column_name, $primary ) {
		if( $primary === $column_name ) {
			$edit_link = get_edit_bookmark_link( $link );

			$actions = array();
			$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
			$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id) . "' onclick=\"if ( confirm( '" . esc_js(sprintf(__("You are about to delete this link '%s'\n  'Cancel' to stop, 'OK' to delete."), $link->link_name)) . "' ) ) { return true;}return false;\">" . __('Delete') . "</a>";
			return $this->row_actions($actions);
		}
	}
}
