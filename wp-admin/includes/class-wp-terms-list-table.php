<?php
/**
 * Terms List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
class WP_Terms_List_Table extends WP_List_Table {

	var $callback_args;

	function WP_Terms_List_Table() {
		global $post_type, $taxonomy, $tax;

		wp_reset_vars( array( 'action', 'taxonomy', 'post_type' ) );

		if ( empty( $taxonomy ) )
			$taxonomy = 'post_tag';

		if ( !taxonomy_exists( $taxonomy ) )
			wp_die( __( 'Invalid taxonomy' ) );

		$tax = get_taxonomy( $taxonomy );

		if ( empty( $post_type ) || !in_array( $post_type, get_post_types( array( 'public' => true ) ) ) )
			$post_type = 'post';

		parent::WP_List_Table( array(
			'plural' => 'tags',
			'singular' => 'tag',
		) );
	}

	function check_permissions( $type = 'manage' ) {
		global $tax;

		$cap = 'manage' == $type ? $tax->cap->manage_terms : $tax->cap->edit_terms;

		if ( !current_user_can( $tax->cap->manage_terms ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
	}

	function prepare_items() {
		global $taxonomy;

		$tags_per_page = $this->get_items_per_page( 'edit_' .  $taxonomy . '_per_page' );

		if ( 'post_tag' == $taxonomy ) {
			$tags_per_page = apply_filters( 'edit_tags_per_page', $tags_per_page );
			$tags_per_page = apply_filters( 'tagsperpage', $tags_per_page ); // Old filter
		} elseif ( 'category' == $taxonomy ) {
			$tags_per_page = apply_filters( 'edit_categories_per_page', $tags_per_page ); // Old filter
		}

		$search = !empty( $_REQUEST['s'] ) ? trim( stripslashes( $_REQUEST['s'] ) ) : '';

		$args = array(
			'search' => $search,
			'page' => $this->get_pagenum(),
			'number' => $tags_per_page,
		);

		if ( !empty( $_REQUEST['orderby'] ) )
			$args['orderby'] = trim( stripslashes( $_REQUEST['orderby'] ) );

		if ( !empty( $_REQUEST['order'] ) )
			$args['order'] = trim( stripslashes( $_REQUEST['order'] ) );

		$this->callback_args = $args;

		$this->set_pagination_args( array(
			'total_items' => wp_count_terms( $taxonomy, compact( 'search' ) ),
			'per_page' => $tags_per_page,
		) );
	}

	function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete' );

		return $actions;
	}

	function current_action() {
		if ( isset( $_REQUEST['action'] ) && isset( $_REQUEST['delete_tags'] ) && ( 'delete' == $_REQUEST['action'] || 'delete' == $_REQUEST['action2'] ) )
			return 'bulk-delete';

		return parent::current_action();
	}

	function get_columns() {
		global $taxonomy;

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name' ),
			'description' => __( 'Description' ),
			'slug'        => __( 'Slug' ),
		);

		if ( 'link_category' == $taxonomy )
			$columns['links'] = __( 'Links' );
		else
			$columns['posts'] = __( 'Posts' );

		return $columns;
	}

	function get_sortable_columns() {
		return array(
			'name'        => 'name',
			'description' => 'description',
			'slug'        => 'slug',
			'posts'       => 'count',
			'links'       => 'count'
		);
	}

	function display_rows() {
		global $taxonomy;

		$args = wp_parse_args( $this->callback_args, array(
			'page' => 1,
			'number' => 20,
			'search' => '',
			'hide_empty' => 0
		) );

		extract( $args, EXTR_SKIP );

		$args['offset'] = $offset = ( $page - 1 ) * $number;

		// convert it to table rows
		$out = '';
		$count = 0;
		if ( is_taxonomy_hierarchical( $taxonomy ) && !isset( $orderby ) ) {
			// We'll need the full set of terms then.
			$args['number'] = $args['offset'] = 0;

			$terms = get_terms( $taxonomy, $args );
			if ( !empty( $search ) ) // Ignore children on searches.
				$children = array();
			else
				$children = _get_term_hierarchy( $taxonomy );

			// Some funky recursion to get the job done( Paging & parents mainly ) is contained within, Skip it for non-hierarchical taxonomies for performance sake
			$out .= $this->_rows( $taxonomy, $terms, $children, $offset, $number, $count );
		} else {
			$terms = get_terms( $taxonomy, $args );
			foreach ( $terms as $term )
				$out .= $this->single_row( $term, 0, $taxonomy );
			$count = $number; // Only displaying a single page.
		}

		echo $out;
	}

	function _rows( $taxonomy, $terms, &$children, $start = 0, $per_page = 20, &$count, $parent = 0, $level = 0 ) {

		$end = $start + $per_page;

		$output = '';
		foreach ( $terms as $key => $term ) {

			if ( $count >= $end )
				break;

			if ( $term->parent != $parent && empty( $_REQUEST['s'] ) )
				continue;

			// If the page starts in a subtree, print the parents.
			if ( $count == $start && $term->parent > 0 && empty( $_REQUEST['s'] ) ) {
				$my_parents = $parent_ids = array();
				$p = $term->parent;
				while ( $p ) {
					$my_parent = get_term( $p, $taxonomy );
					$my_parents[] = $my_parent;
					$p = $my_parent->parent;
					if ( in_array( $p, $parent_ids ) ) // Prevent parent loops.
						break;
					$parent_ids[] = $p;
				}
				unset( $parent_ids );

				$num_parents = count( $my_parents );
				while ( $my_parent = array_pop( $my_parents ) ) {
					$output .=  "\t" . $this->single_row( $my_parent, $level - $num_parents, $taxonomy );
					$num_parents--;
				}
			}

			if ( $count >= $start )
				$output .= "\t" . $this->single_row( $term, $level, $taxonomy );

			++$count;

			unset( $terms[$key] );

			if ( isset( $children[$term->term_id] ) && empty( $_REQUEST['s'] ) )
				$output .= $this->_rows( $taxonomy, $terms, $children, $start, $per_page, $count, $term->term_id, $level + 1 );
		}

		return $output;
	}

	function single_row( $tag, $level = 0 ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		$this->level = $level;

		echo '<tr id="tag-' . $tag->term_id . '"' . $row_class . '>';
		echo $this->single_row_columns( $tag );
		echo '</tr>';
	}

	function column_cb( $tag ) {
		global $taxonomy, $tax;

		$default_term = get_option( 'default_' . $taxonomy );

		if ( current_user_can( $tax->cap->delete_terms ) && $tag->term_id != $default_term )
			return '<input type="checkbox" name="delete_tags[]" value="' . $tag->term_id . '" />';
		else
			return '&nbsp;';
	}

	function column_name( $tag ) {
		global $taxonomy, $tax, $post_type;

		$default_term = get_option( 'default_' . $taxonomy );

		$pad = str_repeat( '&#8212; ', max( 0, $this->level ) );
		$name = apply_filters( 'term_name', $pad . ' ' . $tag->name, $tag );
		$qe_data = get_term( $tag->term_id, $taxonomy, OBJECT, 'edit' );
		$edit_link = get_edit_term_link( $tag->term_id, $taxonomy, $post_type );

		$out = '<strong><a class="row-title" href="' . $edit_link . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $name ) ) . '">' . $name . '</a></strong><br />';

		$actions = array();
		if ( current_user_can( $tax->cap->edit_terms ) ) {
			$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline">' . __( 'Quick&nbsp;Edit' ) . '</a>';
		}
		if ( current_user_can( $tax->cap->delete_terms ) && $tag->term_id != $default_term )
			$actions['delete'] = "<a class='delete-tag' href='" . wp_nonce_url( "edit-tags.php?action=delete&amp;taxonomy=$taxonomy&amp;tag_ID=$tag->term_id", 'delete-tag_' . $tag->term_id ) . "'>" . __( 'Delete' ) . "</a>";

		$actions = apply_filters( 'tag_row_actions', $actions, $tag );
		$actions = apply_filters( "${taxonomy}_row_actions", $actions, $tag );

		$out .= $this->row_actions( $actions );
		$out .= '<div class="hidden" id="inline_' . $qe_data->term_id . '">';
		$out .= '<div class="name">' . $qe_data->name . '</div>';
		$out .= '<div class="slug">' . apply_filters( 'editable_slug', $qe_data->slug ) . '</div>';
		$out .= '<div class="parent">' . $qe_data->parent . '</div></div></td>';

		return $out;
	}

	function column_description( $tag ) {
		return $tag->description;
	}

	function column_slug( $tag ) {
		return apply_filters( 'editable_slug', $tag->slug );
	}

	function column_posts( $tag ) {
		global $taxonomy, $post_type;

		$count = number_format_i18n( $tag->count );

		if ( 'post_tag' == $taxonomy ) {
			$tagsel = 'tag';
		} elseif ( 'category' == $taxonomy ) {
			$tagsel = 'category_name';
		} elseif ( ! empty( $tax->query_var ) ) {
			$tagsel = $tax->query_var;
		} else {
			$tagsel = $taxonomy;
		}

		return "<a href='edit.php?$tagsel=$tag->slug&amp;post_type=$post_type'>$count</a>";
	}

	function column_links( $tag ) {
		$count = number_format_i18n( $tag->count );
		return $count;
	}

	function column_default( $tag, $column_name ) {
		global $taxonomy;

		return apply_filters( "manage_${taxonomy}_custom_column", '', $column_name, $tag->term_id );
		$out .= "</td>";
	}

	/**
	 * Outputs the hidden row displayed when inline editing
	 *
	 * @since 3.1.0
	 */
	function inline_edit() {
		global $tax;

		if ( ! current_user_can( $tax->cap->edit_terms ) )
			return;

		list( $columns, $hidden ) = $this->get_column_info();

		$col_count = count( $columns ) - count( $hidden );
		?>

	<form method="get" action=""><table style="display: none"><tbody id="inlineedit">
		<tr id="inline-edit" class="inline-edit-row" style="display: none"><td colspan="<?php echo $col_count; ?>">

			<fieldset><div class="inline-edit-col">
				<h4><?php _e( 'Quick Edit' ); ?></h4>

				<label>
					<span class="title"><?php _e( 'Name' ); ?></span>
					<span class="input-text-wrap"><input type="text" name="name" class="ptitle" value="" /></span>
				</label>
	<?php if ( !global_terms_enabled() ) { ?>
				<label>
					<span class="title"><?php _e( 'Slug' ); ?></span>
					<span class="input-text-wrap"><input type="text" name="slug" class="ptitle" value="" /></span>
				</label>
	<?php } ?>

			</div></fieldset>
	<?php

		$core_columns = array( 'cb' => true, 'description' => true, 'name' => true, 'slug' => true, 'posts' => true );

		foreach ( $columns as $column_name => $column_display_name ) {
			if ( isset( $core_columns[$column_name] ) )
				continue;
			do_action( 'quick_edit_custom_box', $column_name, $type, $tax->taxonomy );
		}

	?>

		<p class="inline-edit-save submit">
			<a accesskey="c" href="#inline-edit" title="<?php _e( 'Cancel' ); ?>" class="cancel button-secondary alignleft"><?php _e( 'Cancel' ); ?></a>
			<?php $update_text = $tax->labels->update_item; ?>
			<a accesskey="s" href="#inline-edit" title="<?php echo esc_attr( $update_text ); ?>" class="save button-primary alignright"><?php echo $update_text; ?></a>
			<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			<span class="error" style="display:none;"></span>
			<?php wp_nonce_field( 'taxinlineeditnonce', '_inline_edit', false ); ?>
			<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $tax->name ); ?>" />
			<br class="clear" />
		</p>
		</td></tr>
		</tbody></table></form>
	<?php
	}
}

?>
