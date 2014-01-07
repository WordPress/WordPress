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

	function __construct( $args = array() ) {
		parent::__construct( array(
			'plural' => 'bookmarks',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
	}

	function ajax_user_can() {
		return current_user_can( 'manage_links' );
	}

	function prepare_items() {
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

	function no_items() {
		_e( 'No links found.' );
	}

	function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete' );

		return $actions;
	}

	function extra_tablenav( $which ) {
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
				'show_option_all' => __( 'View all categories' ),
				'hide_empty' => true,
				'hierarchical' => 1,
				'show_count' => 0,
				'orderby' => 'name',
			);
			wp_dropdown_categories( $dropdown_options );
			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
?>
		</div>
<?php
	}

	function get_columns() {
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

	function get_sortable_columns() {
		return array(
			'name'    => 'name',
			'url'     => 'url',
			'visible' => 'visible',
			'rating'  => 'rating'
		);
	}

	function display_rows() {
		global $cat_id;

		$alt = 0;

		foreach ( $this->items as $link ) {
			$link = sanitize_bookmark( $link );
			$link->link_name = esc_attr( $link->link_name );
			$link->link_category = wp_get_link_cats( $link->link_id );

			$short_url = url_shorten( $link->link_url );

			$visible = ( $link->link_visible == 'Y' ) ? __( 'Yes' ) : __( 'No' );
			$rating  = $link->link_rating;
			$style = ( $alt++ % 2 ) ? '' : ' class="alternate"';

			$edit_link = get_edit_bookmark_link( $link );
?>
		<tr id="link-<?php echo $link->link_id; ?>" valign="middle" <?php echo $style; ?>>
<?php

			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) {
				$class = "class='column-$column_name'";

				$style = '';
				if ( in_array( $column_name, $hidden ) )
					$style = ' style="display:none;"';

				$attributes = $class . $style;

				switch ( $column_name ) {
					case 'cb': ?>
						<th scope="row" class="check-column">
							<label class="screen-reader-text" for="cb-select-<?php echo $link->link_id; ?>"><?php echo sprintf( __( 'Select %s' ), $link->link_name ); ?></label>
							<input type="checkbox" name="linkcheck[]" id="cb-select-<?php echo $link->link_id; ?>" value="<?php echo esc_attr( $link->link_id ); ?>" />
						</th>
						<?php
						break;

					case 'name':
						echo "<td $attributes><strong><a class='row-title' href='$edit_link' title='" . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $link->link_name ) ) . "'>$link->link_name</a></strong><br />";

						$actions = array();
						$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
						$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id ) . "' onclick=\"if ( confirm( '" . esc_js( sprintf( __( "You are about to delete this link '%s'\n  'Cancel' to stop, 'OK' to delete." ), $link->link_name ) ) . "' ) ) { return true;}return false;\">" . __( 'Delete' ) . "</a>";
						echo $this->row_actions( $actions );

						echo '</td>';
						break;
					case 'url':
						echo "<td $attributes><a href='$link->link_url' title='". esc_attr( sprintf( __( 'Visit %s' ), $link->link_name ) )."'>$short_url</a></td>";
						break;
					case 'categories':
						?><td <?php echo $attributes ?>><?php
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
						?></td><?php
						break;
					case 'rel':
						?><td <?php echo $attributes ?>><?php echo empty( $link->link_rel ) ? '<br />' : $link->link_rel; ?></td><?php
						break;
					case 'visible':
						?><td <?php echo $attributes ?>><?php echo $visible; ?></td><?php
						break;
					case 'rating':
	 					?><td <?php echo $attributes ?>><?php echo $rating; ?></td><?php
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
						?>
						<td <?php echo $attributes ?>><?php do_action( 'manage_link_custom_column', $column_name, $link->link_id ); ?></td>
						<?php
						break;
				}
			}
?>
		</tr>
<?php
		}
	}
}
