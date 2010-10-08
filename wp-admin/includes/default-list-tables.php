<?php

/**
 * List table classes
 *
 * Each list-type admin screen has a class that handles the rendering of the list table.
 *
 * @package WordPress
 * @subpackage Administration
 */

class WP_Posts_Table extends WP_List_Table {

	/**
	 * Whether the items should be displayed hierarchically or linearly
	 *
	 * @since 3.1.0
	 * @var bool
	 * @access protected
	 */
	var $hierarchical_display;

	/**
	 * Holds the number of pending comments for each post
	 *
	 * @since 3.1.0
	 * @var bool
	 * @access protected
	 */
	var $comment_pending_count;

	/**
	 * Holds the number of posts for this user
	 *
	 * @since 3.1.0
	 * @var bool
	 * @access private
	 */
	var $user_posts_count;

	function WP_Posts_Table() {
		global $post_type_object, $post_type, $current_screen, $wpdb;

		if ( !isset( $_REQUEST['post_type'] ) )
			$post_type = 'post';
		elseif ( in_array( $_REQUEST['post_type'], get_post_types( array( 'show_ui' => true ) ) ) )
			$post_type = $_REQUEST['post_type'];
		else
			wp_die( __( 'Invalid post type' ) );
		$_REQUEST['post_type'] = $post_type;

		$post_type_object = get_post_type_object( $post_type );

		if ( !current_user_can( $post_type_object->cap->edit_others_posts ) ) {
			$this->user_posts_count = $wpdb->get_var( $wpdb->prepare( "
				SELECT COUNT( 1 ) FROM $wpdb->posts
				WHERE post_type = '%s' AND post_status NOT IN ( 'trash', 'auto-draft' )
				AND post_author = %d
			", $post_type, get_current_user_id() ) );

			if ( $this->user_posts_count && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['all_posts'] ) && empty( $_REQUEST['author'] ) )
				$_GET['author'] = get_current_user_id();
		}

		parent::WP_List_Table( array(
			'screen' => $current_screen,
			'plural' => 'posts',
		) );
	}

	function check_permissions() {
		global $post_type_object;

		if ( !current_user_can( $post_type_object->cap->edit_posts ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
	}

	function prepare_items() {
		global $post_type_object, $post_type, $avail_post_stati, $wp_query, $per_page, $mode;

		$avail_post_stati = wp_edit_posts_query();

		$this->hierarchical_display = ( $post_type_object->hierarchical && 'menu_order title' == $wp_query->query['orderby'] );

		$total_items = $this->hierarchical_display ? $wp_query->post_count : $wp_query->found_posts;

		$per_page = $this->get_items_per_page( 'edit_' . $post_type . '_per_page' );
 		$per_page = apply_filters( 'edit_posts_per_page', $per_page, $post_type ); 

		if ( $this->hierarchical_display )
			$total_pages = ceil( $total_items / $per_page );
		else
			$total_pages = $wp_query->max_num_pages;

		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

		$this->is_trash = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'trash';

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page
		) );
	}

	function has_items() {
		return have_posts();
	}

	function no_items() {
		global $post_type_object;

		if ( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] )
			echo $post_type_object->labels->not_found_in_trash;
		else
			echo $post_type_object->labels->not_found;
	}

	function get_views() {
		global $post_type, $post_type_object, $locked_post_status, $avail_post_stati;

		if ( !empty($locked_post_status) )
			return array();

		$status_links = array();
		$num_posts = wp_count_posts( $post_type, 'readable' );
		$class = '';
		$allposts = '';

		$current_user_id = get_current_user_id();

		if ( $this->user_posts_count ) {
			if ( isset( $_GET['author'] ) && ( $_GET['author'] == $current_user_id ) )
				$class = ' class="current"';
			$status_links['mine'] = "<li><a href='edit.php?post_type=$post_type&author=$current_user_id'$class>" . sprintf( _nx( 'Mine <span class="count">(%s)</span>', 'Mine <span class="count">(%s)</span>', $this->user_posts_count, 'posts' ), number_format_i18n( $this->user_posts_count ) ) . '</a>';
			$allposts = '&all_posts=1';
		}

		$total_posts = array_sum( (array) $num_posts );

		// Subtract post types that are not included in the admin all list.
		foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
			$total_posts -= $num_posts->$state;

		$class = empty($class) && empty($_REQUEST['post_status']) ? ' class="current"' : '';
		$status_links['all'] = "<li><a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( !in_array( $status_name, $avail_post_stati ) )
				continue;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<li><a href='edit.php?post_status=$status_name&amp;post_type=$post_type'$class>" . sprintf( _n( $status->label_count[0], $status->label_count[1], $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}
		
		return $status_links;
	}

	function get_bulk_actions() {
		$actions = array();

		if ( $this->is_trash )
			$actions['untrash'] = __( 'Restore' );
		else
			$actions['edit'] = __( 'Edit' );

		if ( $this->is_trash || !EMPTY_TRASH_DAYS )
			$actions['delete'] = __( 'Delete Permanently' );
		else
			$actions['trash'] = __( 'Move to Trash' );

		return $actions;
	}

	function extra_tablenav( $which ) {
		global $post_type, $post_type_object, $cat;

		if ( 'top' == $which && !is_singular() ) {
?>
		<div class="alignleft actions">
<?php
			$this->months_dropdown( $post_type );

			if ( is_object_in_taxonomy( $post_type, 'category' ) ) {
				$dropdown_options = array(
					'show_option_all' => __( 'View all categories' ),
					'hide_empty' => 0,
					'hierarchical' => 1,
					'show_count' => 0,
					'orderby' => 'name',
					'selected' => $cat
				);
				wp_dropdown_categories( $dropdown_options );
			}
			do_action( 'restrict_manage_posts' );
?>
			<input type="submit" id="post-query-submit" value="<?php esc_attr_e( 'Filter' ); ?>" class="button-secondary" />
		</div>
<?php
		}

		if ( $this->is_trash && current_user_can( $post_type_object->cap->edit_others_posts ) ) {
?>
		<input type="submit" name="delete_all" id="delete_all" value="<?php esc_attr_e( 'Empty Trash' ); ?>" class="button-secondary apply" />
<?php
		}
	}

	function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) )
			return 'delete_all';

		return parent::current_action();
	}

	function pagination( $which ) {
		global $post_type_object, $mode;

		parent::pagination( $which );

		if ( 'top' == $which && !$post_type_object->hierarchical )
			$this->view_switcher( $mode );
	}

	function get_table_classes() {
		global $post_type_object;

		return array( 'widefat', 'fixed', $post_type_object->hierarchical ? 'pages' : 'posts' );
	}

	function get_columns() {
		$screen = $this->_screen;

		if ( empty( $screen ) )
			$post_type = 'post';
		else
			$post_type = $screen->post_type;

		$posts_columns = array();
		$posts_columns['cb'] = '<input type="checkbox" />';
		/* translators: manage posts column name */
		$posts_columns['title'] = _x( 'Title', 'column name' );
		$posts_columns['author'] = __( 'Author' );
		if ( empty( $post_type ) || is_object_in_taxonomy( $post_type, 'category' ) )
			$posts_columns['categories'] = __( 'Categories' );
		if ( empty( $post_type ) || is_object_in_taxonomy( $post_type, 'post_tag' ) )
			$posts_columns['tags'] = __( 'Tags' );
		$post_status = !empty( $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : 'all';
		if ( !in_array( $post_status, array( 'pending', 'draft', 'future' ) ) && ( empty( $post_type ) || post_type_supports( $post_type, 'comments' ) ) )
			$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
		$posts_columns['date'] = __( 'Date' );

		if ( 'page' == $post_type )
			$posts_columns = apply_filters( 'manage_pages_columns', $posts_columns );
		else
			$posts_columns = apply_filters( 'manage_posts_columns', $posts_columns, $post_type );
		$posts_columns = apply_filters( "manage_{$post_type}_posts_columns", $posts_columns );

		return $posts_columns;
	}

	function get_sortable_columns() {
		return array(
			'title'    => 'title',
			'author'   => 'author',
			'parent'   => 'parent',
			'comments' => 'comment_count',
			'date'     => 'date',
		);
	}

	function display_rows( $posts = array() ) {
		global $wp_query, $post_type_object, $per_page;

		if ( empty( $posts ) )
			$posts = $wp_query->posts;

		if ( $this->hierarchical_display ) {
			$this->_display_rows_hierarchical( $posts, $this->get_pagenum(), $per_page );
		} else {
			$this->_display_rows( $posts );
		}
	}

	function _display_rows( $posts ) {
		global $post, $mode;

		add_filter( 'the_title','esc_html' );

		// Create array of post IDs.
		$post_ids = array();

		foreach ( $posts as $a_post )
			$post_ids[] = $a_post->ID;

		$this->comment_pending_count = get_pending_comments_num( $post_ids );

		foreach ( $posts as $post )
			$this->single_row( $post );
	}

	function _display_rows_hierarchical( $pages, $pagenum = 1, $per_page = 20 ) {
		global $wpdb;

		$level = 0;

		if ( ! $pages ) {
			$pages = get_pages( array( 'sort_column' => 'menu_order' ) );

			if ( ! $pages )
				return false;
		}

		/*
		 * arrange pages into two parts: top level pages and children_pages
		 * children_pages is two dimensional array, eg.
		 * children_pages[10][] contains all sub-pages whose parent is 10.
		 * It only takes O( N ) to arrange this and it takes O( 1 ) for subsequent lookup operations
		 * If searching, ignore hierarchy and treat everything as top level
		 */
		if ( empty( $_REQUEST['s'] ) ) {

			$top_level_pages = array();
			$children_pages = array();

			foreach ( $pages as $page ) {

				// catch and repair bad pages
				if ( $page->post_parent == $page->ID ) {
					$page->post_parent = 0;
					$wpdb->update( $wpdb->posts, array( 'post_parent' => 0 ), array( 'ID' => $page->ID ) );
					clean_page_cache( $page->ID );
				}

				if ( 0 == $page->post_parent )
					$top_level_pages[] = $page;
				else
					$children_pages[ $page->post_parent ][] = $page;
			}

			$pages = &$top_level_pages;
		}

		$count = 0;
		$start = ( $pagenum - 1 ) * $per_page;
		$end = $start + $per_page;

		foreach ( $pages as $page ) {
			if ( $count >= $end )
				break;

			if ( $count >= $start )
				echo "\t" . $this->single_row( $page, $level );

			$count++;

			if ( isset( $children_pages ) )
				$this->_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
		}

		// if it is the last pagenum and there are orphaned pages, display them with paging as well
		if ( isset( $children_pages ) && $count < $end ){
			foreach ( $children_pages as $orphans ){
				foreach ( $orphans as $op ) {
					if ( $count >= $end )
						break;
					if ( $count >= $start )
						echo "\t" . $this->single_row( $op, 0 );
					$count++;
				}
			}
		}
	}

	/**
	 * Given a top level page ID, display the nested hierarchy of sub-pages
	 * together with paging support
	 *
	 * @since unknown
	 *
	 * @param unknown_type $children_pages
	 * @param unknown_type $count
	 * @param unknown_type $parent
	 * @param unknown_type $level
	 * @param unknown_type $pagenum
	 * @param unknown_type $per_page
	 */
	function _page_rows( &$children_pages, &$count, $parent, $level, $pagenum, $per_page ) {

		if ( ! isset( $children_pages[$parent] ) )
			return;

		$start = ( $pagenum - 1 ) * $per_page;
		$end = $start + $per_page;

		foreach ( $children_pages[$parent] as $page ) {

			if ( $count >= $end )
				break;

			// If the page starts in a subtree, print the parents.
			if ( $count == $start && $page->post_parent > 0 ) {
				$my_parents = array();
				$my_parent = $page->post_parent;
				while ( $my_parent ) {
					$my_parent = get_post( $my_parent );
					$my_parents[] = $my_parent;
					if ( !$my_parent->post_parent )
						break;
					$my_parent = $my_parent->post_parent;
				}
				$num_parents = count( $my_parents );
				while ( $my_parent = array_pop( $my_parents ) ) {
					echo "\t" . $this->single_row( $my_parent, $level - $num_parents );
					$num_parents--;
				}
			}

			if ( $count >= $start )
				echo "\t" . $this->single_row( $page, $level );

			$count++;

			$this->_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
		}

		unset( $children_pages[$parent] ); //required in order to keep track of orphans
	}

	function single_row( $a_post, $level = 0 ) {
		global $post, $current_screen, $mode;
		static $rowclass;

		$global_post = $post;
		$post = $a_post;
		setup_postdata( $post );

		$rowclass = 'alternate' == $rowclass ? '' : 'alternate';
		$post_owner = ( get_current_user_id() == $post->post_author ? 'self' : 'other' );
		$edit_link = get_edit_post_link( $post->ID );
		$title = _draft_or_post_title();
		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post = current_user_can( 'edit_post', $post->ID );
	?>
		<tr id='post-<?php echo $post->ID; ?>' class='<?php echo trim( $rowclass . ' author-' . $post_owner . ' status-' . $post->post_status ); ?> iedit' valign="top">
	<?php

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			switch ( $column_name ) {

			case 'cb':
			?>
			<th scope="row" class="check-column"><?php if ( $can_edit_post ) { ?><input type="checkbox" name="post[]" value="<?php the_ID(); ?>" /><?php } ?></th>
			<?php
			break;

			case 'title':
				if ( $this->hierarchical_display ) {
					$attributes = 'class="post-title page-title column-title"' . $style;

					if ( 0 == $level && (int) $post->post_parent > 0 ) {
						//sent level 0 by accident, by default, or because we don't know the actual level
						$find_main_page = (int) $post->post_parent;
						while ( $find_main_page > 0 ) {
							$parent = get_page( $find_main_page );

							if ( is_null( $parent ) )
								break;

							$level++;
							$find_main_page = (int) $parent->post_parent;

							if ( !isset( $parent_name ) )
								$parent_name = $parent->post_title;
						}
					}

					$post->post_title = esc_html( $post->post_title );
					$pad = str_repeat( '&#8212; ', $level );
?>
			<td <?php echo $attributes ?>><strong><?php if ( $can_edit_post && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ); ?>"><?php echo $pad; echo $title ?></a><?php } else { echo $pad; echo $title; }; _post_states( $post ); echo isset( $parent_name ) ? ' | ' . $post_type_object->labels->parent_item_colon . ' ' . esc_html( $parent_name ) : ''; ?></strong>
<?php
				}
				else {
					$attributes = 'class="post-title page-title column-title"' . $style;
?>
			<td <?php echo $attributes ?>><strong><?php if ( $can_edit_post && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ); ?>"><?php echo $title ?></a><?php } else { echo $title; }; _post_states( $post ); ?></strong>
<?php
					if ( 'excerpt' == $mode ) {
						the_excerpt();
					}
				}

				$actions = array();
				if ( $can_edit_post && 'trash' != $post->post_status ) {
					$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
					$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline' ) ) . '">' . __( 'Quick&nbsp;Edit' ) . '</a>';
				}
				if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
					if ( 'trash' == $post->post_status )
						$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-' . $post->post_type . '_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
					elseif ( EMPTY_TRASH_DAYS )
						$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
					if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
						$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
				}
				if ( in_array( $post->post_status, array( 'pending', 'draft' ) ) ) {
					if ( $can_edit_post )
						$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
				} elseif ( 'trash' != $post->post_status ) {
					$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
				}

				$actions = apply_filters( $this->hierarchical_display ? 'page_row_actions' : 'post_row_actions', $actions, $post );
				echo $this->row_actions( $actions );

				get_inline_data( $post );
			break;

			case 'date':
				if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
					$t_time = $h_time = __( 'Unpublished' );
					$time_diff = 0;
				} else {
					$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
					$m_time = $post->post_date;
					$time = get_post_time( 'G', true, $post );

					$time_diff = time() - $time;

					if ( $time_diff > 0 && $time_diff < 24*60*60 )
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					else
						$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
				}

				echo '<td ' . $attributes . '>';
				if ( 'excerpt' == $mode )
					echo apply_filters( 'post_date_column_time', $t_time, $post, $column_name, $mode );
				else
					echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, $column_name, $mode ) . '</abbr>';
				echo '<br />';
				if ( 'publish' == $post->post_status ) {
					_e( 'Published' );
				} elseif ( 'future' == $post->post_status ) {
					if ( $time_diff > 0 )
						echo '<strong class="attention">' . __( 'Missed schedule' ) . '</strong>';
					else
						_e( 'Scheduled' );
				} else {
					_e( 'Last Modified' );
				}
				echo '</td>';
			break;

			case 'categories':
			?>
			<td <?php echo $attributes ?>><?php
				$categories = get_the_category();
				if ( !empty( $categories ) ) {
					$out = array();
					foreach ( $categories as $c ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							add_query_arg( array( 'post_type' => $post->post_type, 'category_name' => $c->slug ), 'edit.php' ),
							esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'category', 'display' ) )
						);
					}
					echo join( ', ', $out );
				} else {
					_e( 'Uncategorized' );
				}
			?></td>
			<?php
			break;

			case 'tags':
			?>
			<td <?php echo $attributes ?>><?php
				$tags = get_the_tags( $post->ID );
				if ( !empty( $tags ) ) {
					$out = array();
					foreach ( $tags as $c ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							add_query_arg( array( 'post_type' => $post->post_type, 'tag' => $c->slug ), 'edit.php' ),
							esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'tag', 'display' ) )
						);
					}
					echo join( ', ', $out );
				} else {
					_e( 'No Tags' );
				}
			?></td>
			<?php
			break;

			case 'comments':
			?>
			<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
			<?php
				$pending_comments = isset( $this->comment_pending_count[$post->ID] ) ? $this->comment_pending_count[$post->ID] : 0;

				$this->comments_bubble( $post->ID, $pending_comments );
			?>
			</div></td>
			<?php
			break;

			case 'author':
			?>
			<td <?php echo $attributes ?>><?php
				printf( '<a href="%s">%s</a>',
					add_query_arg( array( 'post_type' => $post->post_type, 'author' => get_the_author_meta( 'ID' ) ), 'edit.php' ),
					get_the_author()
				);
			?></td>
			<?php
			break;

			default:
			?>
			<td <?php echo $attributes ?>><?php do_action( 'manage_posts_custom_column', $column_name, $post->ID ); ?></td>
			<?php
			break;
		}
	}
	?>
		</tr>
	<?php
		$post = $global_post;
	}

	/**
	 * Outputs the hidden row displayed when inline editing
	 *
	 * @since 3.1
	 */
	function inline_edit() {
		global $mode;

		$screen = $this->_screen;

		$post = get_default_post_to_edit( $screen->post_type );
		$post_type_object = get_post_type_object( $screen->post_type );

		$taxonomy_names = get_object_taxonomies( $screen->post_type );
		$hierarchical_taxonomies = array();
		$flat_taxonomies = array();
		foreach ( $taxonomy_names as $taxonomy_name ) {
			$taxonomy = get_taxonomy( $taxonomy_name );

			if ( !$taxonomy->show_ui )
				continue;

			if ( $taxonomy->hierarchical )
				$hierarchical_taxonomies[] = $taxonomy;
			else
				$flat_taxonomies[] = $taxonomy;
		}

		list( $columns, $hidden ) = $this->get_column_info();

		$col_count = count( $columns ) - count( $hidden );
		$m = ( isset( $mode ) && 'excerpt' == $mode ) ? 'excerpt' : 'list';
		$can_publish = current_user_can( $post_type_object->cap->publish_posts );
		$core_columns = array( 'cb' => true, 'date' => true, 'title' => true, 'categories' => true, 'tags' => true, 'comments' => true, 'author' => true );

	?>

	<form method="get" action=""><table style="display: none"><tbody id="inlineedit">
		<?php
		$hclass = count( $hierarchical_taxonomies ) ? 'post' : 'page';
		$bulk = 0;
		while ( $bulk < 2 ) { ?>

		<tr id="<?php echo $bulk ? 'bulk-edit' : 'inline-edit'; ?>" class="inline-edit-row inline-edit-row-<?php echo "$hclass inline-edit-$screen->post_type ";
			echo $bulk ? "bulk-edit-row bulk-edit-row-$hclass bulk-edit-$screen->post_type" : "quick-edit-row quick-edit-row-$hclass inline-edit-$screen->post_type";
		?>" style="display: none"><td colspan="<?php echo $col_count; ?>">

		<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
			<h4><?php echo $bulk ? __( 'Bulk Edit' ) : __( 'Quick Edit' ); ?></h4>


	<?php

	if ( post_type_supports( $screen->post_type, 'title' ) ) :
		if ( $bulk ) : ?>
			<div id="bulk-title-div">
				<div id="bulk-titles"></div>
			</div>

	<?php else : // $bulk ?>

			<label>
				<span class="title"><?php _e( 'Title' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value="" /></span>
			</label>

			<label>
				<span class="title"><?php _e( 'Slug' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="post_name" value="" /></span>
			</label>

	<?php endif; // $bulk
	endif; // post_type_supports title ?>

	<?php if ( !$bulk ) : ?>
			<label><span class="title"><?php _e( 'Date' ); ?></span></label>
			<div class="inline-edit-date">
				<?php touch_time( 1, 1, 4, 1 ); ?>
			</div>
			<br class="clear" />

	<?php endif; // $bulk

		if ( post_type_supports( $screen->post_type, 'author' ) ) :
			$authors_dropdown = '';

			if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) :
				$users_opt = array( 
					'name' => 'post_author', 
					'class'=> 'authors', 
					'multi' => 1, 
					'echo' => 0 
				);
				if ( $bulk )
					$users_opt['show_option_none'] = __( '&mdash; No Change &mdash;' );
				$authors_dropdown  = '<label>';
				$authors_dropdown .= '<span class="title">' . __( 'Author' ) . '</span>';
				$authors_dropdown .= wp_dropdown_users( $users_opt );
				$authors_dropdown .= '</label>';
			endif; // authors
	?>

	<?php if ( !$bulk ) echo $authors_dropdown;
	endif; // post_type_supports author

	if ( !$bulk ) :
	?>

			<div class="inline-edit-group">
				<label class="alignleft">
					<span class="title"><?php _e( 'Password' ); ?></span>
					<span class="input-text-wrap"><input type="text" name="post_password" class="inline-edit-password-input" value="" /></span>
				</label>

				<em style="margin:5px 10px 0 0" class="alignleft">
					<?php
					/* translators: Between password field and private checkbox on post quick edit interface */
					echo __( '&ndash;OR&ndash;' );
					?>
				</em>
				<label class="alignleft inline-edit-private">
					<input type="checkbox" name="keep_private" value="private" />
					<span class="checkbox-title"><?php echo __( 'Private' ); ?></span>
				</label>
			</div>

	<?php endif; ?>

		</div></fieldset>

	<?php if ( count( $hierarchical_taxonomies ) && !$bulk ) : ?>

		<fieldset class="inline-edit-col-center inline-edit-categories"><div class="inline-edit-col">

	<?php foreach ( $hierarchical_taxonomies as $taxonomy ) : ?>

			<span class="title inline-edit-categories-label"><?php echo esc_html( $taxonomy->labels->name ) ?>
				<span class="catshow"><?php _e( '[more]' ); ?></span>
				<span class="cathide" style="display:none;"><?php _e( '[less]' ); ?></span>
			</span>
			<input type="hidden" name="<?php echo ( $taxonomy->name == 'category' ) ? 'post_category[]' : 'tax_input[' . esc_attr( $taxonomy->name ) . '][]'; ?>" value="0" />
			<ul class="cat-checklist <?php echo esc_attr( $taxonomy->name )?>-checklist">
				<?php wp_terms_checklist( null, array( 'taxonomy' => $taxonomy->name ) ) ?>
			</ul>

	<?php endforeach; //$hierarchical_taxonomies as $taxonomy ?>

		</div></fieldset>

	<?php endif; // count( $hierarchical_taxonomies ) && !$bulk ?>

		<fieldset class="inline-edit-col-right"><div class="inline-edit-col">

	<?php
		if ( post_type_supports( $screen->post_type, 'author' ) && $bulk )
			echo $authors_dropdown;
	?>

	<?php if ( $post_type_object->hierarchical ) : ?>

			<label>
				<span class="title"><?php _e( 'Parent' ); ?></span>
	<?php
		$dropdown_args = array( 'post_type' => $post_type_object->name, 'selected' => $post->post_parent, 'name' => 'post_parent', 'show_option_none' => __( 'Main Page ( no parent )' ), 'option_none_value' => 0, 'sort_column'=> 'menu_order, post_title' );
		if ( $bulk )
			$dropdown_args['show_option_no_change'] =  __( '&mdash; No Change &mdash;' );
		$dropdown_args = apply_filters( 'quick_edit_dropdown_pages_args', $dropdown_args );
		wp_dropdown_pages( $dropdown_args );
	?>
			</label>

	<?php if ( post_type_supports( $screen->post_type, 'page-attributes' ) ) :
			if ( !$bulk ) : ?>

			<label>
				<span class="title"><?php _e( 'Order' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="menu_order" class="inline-edit-menu-order-input" value="<?php echo $post->menu_order ?>" /></span>
			</label>

	<?php	endif; // !$bulk ?>

			<label>
				<span class="title"><?php _e( 'Template' ); ?></span>
				<select name="page_template">
	<?php	if ( $bulk ) : ?>
					<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
	<?php	endif; // $bulk ?>
					<option value="default"><?php _e( 'Default Template' ); ?></option>
					<?php page_template_dropdown() ?>
				</select>
			</label>

	<?php
		endif; // post_type_supports page-attributes
	endif; // $post_type_object->hierarchical ?>

	<?php if ( count( $flat_taxonomies ) && !$bulk ) : ?>

	<?php foreach ( $flat_taxonomies as $taxonomy ) : ?>

			<label class="inline-edit-tags">
				<span class="title"><?php echo esc_html( $taxonomy->labels->name ) ?></span>
				<textarea cols="22" rows="1" name="tax_input[<?php echo esc_attr( $taxonomy->name )?>]" class="tax_input_<?php echo esc_attr( $taxonomy->name )?>"></textarea>
			</label>

	<?php endforeach; //$flat_taxonomies as $taxonomy ?>

	<?php endif; // count( $flat_taxonomies ) && !$bulk  ?>

	<?php if ( post_type_supports( $screen->post_type, 'comments' ) || post_type_supports( $screen->post_type, 'trackbacks' ) ) :
		if ( $bulk ) : ?>

			<div class="inline-edit-group">
		<?php if ( post_type_supports( $screen->post_type, 'comments' ) ) : ?>
			<label class="alignleft">
				<span class="title"><?php _e( 'Comments' ); ?></span>
				<select name="comment_status">
					<option value=""><?php _e( '&mdash; No Change &mdash;' ); ?></option>
					<option value="open"><?php _e( 'Allow' ); ?></option>
					<option value="closed"><?php _e( 'Do not allow' ); ?></option>
				</select>
			</label>
		<?php endif; if ( post_type_supports( $screen->post_type, 'trackbacks' ) ) : ?>
			<label class="alignright">
				<span class="title"><?php _e( 'Pings' ); ?></span>
				<select name="ping_status">
					<option value=""><?php _e( '&mdash; No Change &mdash;' ); ?></option>
					<option value="open"><?php _e( 'Allow' ); ?></option>
					<option value="closed"><?php _e( 'Do not allow' ); ?></option>
				</select>
			</label>
		<?php endif; ?>
			</div>

	<?php else : // $bulk ?>

			<div class="inline-edit-group">
			<?php if ( post_type_supports( $screen->post_type, 'comments' ) ) : ?>
				<label class="alignleft">
					<input type="checkbox" name="comment_status" value="open" />
					<span class="checkbox-title"><?php _e( 'Allow Comments' ); ?></span>
				</label>
			<?php endif; if ( post_type_supports( $screen->post_type, 'trackbacks' ) ) : ?>
				<label class="alignleft">
					<input type="checkbox" name="ping_status" value="open" />
					<span class="checkbox-title"><?php _e( 'Allow Pings' ); ?></span>
				</label>
			<?php endif; ?>
			</div>

	<?php endif; // $bulk
	endif; // post_type_supports comments or pings ?>

			<div class="inline-edit-group">
				<label class="inline-edit-status alignleft">
					<span class="title"><?php _e( 'Status' ); ?></span>
					<select name="_status">
	<?php if ( $bulk ) : ?>
						<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
	<?php endif; // $bulk ?>
					<?php if ( $can_publish ) : // Contributors only get "Unpublished" and "Pending Review" ?>
						<option value="publish"><?php _e( 'Published' ); ?></option>
						<option value="future"><?php _e( 'Scheduled' ); ?></option>
	<?php if ( $bulk ) : ?>
						<option value="private"><?php _e( 'Private' ) ?></option>
	<?php endif; // $bulk ?>
					<?php endif; ?>
						<option value="pending"><?php _e( 'Pending Review' ); ?></option>
						<option value="draft"><?php _e( 'Draft' ); ?></option>
					</select>
				</label>

	<?php if ( post_type_supports( $screen->post_type, 'sticky' ) && $can_publish && current_user_can( $post_type_object->cap->edit_others_posts ) ) : ?>

	<?php	if ( $bulk ) : ?>

				<label class="alignright">
					<span class="title"><?php _e( 'Sticky' ); ?></span>
					<select name="sticky">
						<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
						<option value="sticky"><?php _e( 'Sticky' ); ?></option>
						<option value="unsticky"><?php _e( 'Not Sticky' ); ?></option>
					</select>
				</label>

	<?php	else : // $bulk ?>

				<label class="alignleft">
					<input type="checkbox" name="sticky" value="sticky" />
					<span class="checkbox-title"><?php _e( 'Make this sticky' ); ?></span>
				</label>

	<?php	endif; // $bulk ?>

	<?php endif; // post_type_supports(sticky) && $can_publish && current_user_can( 'edit_others_cap' ) ?>

			</div>

		</div></fieldset>

	<?php
		foreach ( $columns as $column_name => $column_display_name ) {
			if ( isset( $core_columns[$column_name] ) )
				continue;
			do_action( $bulk ? 'bulk_edit_custom_box' : 'quick_edit_custom_box', $column_name, $screen->post_type );
		}
	?>
		<p class="submit inline-edit-save">
			<a accesskey="c" href="#inline-edit" title="<?php _e( 'Cancel' ); ?>" class="button-secondary cancel alignleft"><?php _e( 'Cancel' ); ?></a>
			<?php if ( ! $bulk ) {
				wp_nonce_field( 'inlineeditnonce', '_inline_edit', false );
				$update_text = __( 'Update' );
				?>
				<a accesskey="s" href="#inline-edit" title="<?php _e( 'Update' ); ?>" class="button-primary save alignright"><?php echo esc_attr( $update_text ); ?></a>
				<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			<?php } else {
				$update_text = __( 'Update' );
			?>
				<input accesskey="s" class="button-primary alignright" type="submit" name="bulk_edit" value="<?php echo esc_attr( $update_text ); ?>" />
			<?php } ?>
			<input type="hidden" name="post_view" value="<?php echo esc_attr( $m ); ?>" />
			<input type="hidden" name="screen" value="<?php echo esc_attr( $screen->id ); ?>" />
			<br class="clear" />
		</p>
		</td></tr>
	<?php
		$bulk++;
		}
?>
		</tbody></table></form>
<?php
	}
}

class WP_Media_Table extends WP_List_Table {

	function WP_Media_Table() {
		global $detached;

		$detached = isset( $_REQUEST['detached'] ) || isset( $_REQUEST['find_detached'] );

		parent::WP_List_Table( array(
			'screen' => $detached ? 'upload-detached' : 'upload',
			'plural' => 'media'
		) );
	}

	function check_permissions() {
		if ( !current_user_can('upload_files') )
			wp_die( __( 'You do not have permission to upload files.' ) );
	}		

	function prepare_items() {
		global $lost, $wpdb, $wp_query, $post_mime_types, $avail_post_mime_types;

		$q = $_REQUEST;

		if ( !empty( $lost ) )
			$q['post__in'] = implode( ',', $lost );

		list( $post_mime_types, $avail_post_mime_types ) = wp_edit_attachments_query( $q );

 		$this->is_trash = isset( $_REQUEST['status'] ) && 'trash' == $_REQUEST['status'];

		$this->set_pagination_args( array(
			'total_items' => $wp_query->found_posts,
			'total_pages' => $wp_query->max_num_pages,
			'per_page' => $wp_query->query_vars['posts_per_page'],
		) );
	}

	function get_views() {
		global $wpdb, $post_mime_types, $detached, $avail_post_mime_types;

		$type_links = array();
		$_num_posts = (array) wp_count_attachments();
		$_total_posts = array_sum($_num_posts) - $_num_posts['trash'];
		if ( !isset( $total_orphans ) )
				$total_orphans = $wpdb->get_var( "SELECT COUNT( * ) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1" );
		$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
		foreach ( $matches as $type => $reals )
			foreach ( $reals as $real )
				$num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];

		$class = ( empty($_GET['post_mime_type']) && !$detached && !isset($_GET['status']) ) ? ' class="current"' : '';
		$type_links['all'] = "<li><a href='upload.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $_total_posts, 'uploaded files' ), number_format_i18n( $_total_posts ) ) . '</a>';
		foreach ( $post_mime_types as $mime_type => $label ) {
			$class = '';

			if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
				continue;

			if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
				$class = ' class="current"';
			if ( !empty( $num_posts[$mime_type] ) )
				$type_links[$mime_type] = "<li><a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( _n( $label[2][0], $label[2][1], $num_posts[$mime_type] ), number_format_i18n( $num_posts[$mime_type] )) . '</a>';
		}
		$type_links['detached'] = '<li><a href="upload.php?detached=1"' . ( $detached ? ' class="current"' : '' ) . '>' . sprintf( _nx( 'Unattached <span class="count">(%s)</span>', 'Unattached <span class="count">(%s)</span>', $total_orphans, 'detached files' ), number_format_i18n( $total_orphans ) ) . '</a>';

		if ( !empty($_num_posts['trash']) )
			$type_links['trash'] = '<li><a href="upload.php?status=trash"' . ( (isset($_GET['status']) && $_GET['status'] == 'trash' ) ? ' class="current"' : '') . '>' . sprintf( _nx( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', $_num_posts['trash'], 'uploaded files' ), number_format_i18n( $_num_posts['trash'] ) ) . '</a>';

		return $type_links;
	}

	function get_bulk_actions() {
		global $detached;

		$actions = array();
		$actions['delete'] = __( 'Delete Permanently' );
		if ( $detached )
			$actions['attach'] = __( 'Attach to a post' );

		return $actions;
	}

	function extra_tablenav( $which ) {
		global $post_type, $detached;
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which && !is_singular() && !$detached && !$this->is_trash ) {
			$this->months_dropdown( $post_type );

			do_action( 'restrict_manage_posts' );
?>
			<input type="submit" id="post-query-submit" value="<?php esc_attr_e( 'Filter' ); ?>" class="button-secondary" />
<?php
		}

		if ( $detached ) { ?>
			<input type="submit" id="find_detached" name="find_detached" value="<?php esc_attr_e( 'Scan for lost attachments' ); ?>" class="button-secondary" />
		<?php } elseif ( $this->is_trash && current_user_can( 'edit_others_posts' ) ) { ?>
			<input type="submit" id="delete_all" name="delete_all" value="<?php esc_attr_e( 'Empty Trash' ); ?>" class="button-secondary apply" />
		<?php } ?>
		</div>
<?php
	}

	function current_action() {
		if ( isset( $_REQUEST['find_detached'] ) )
			return 'find_detached';

		if ( isset( $_REQUEST['found_post_id'] ) && isset( $_REQUEST['media'] ) )
			return 'attach';

		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) )
			return 'delete_all';

		return parent::current_action();
	}

	function has_items() {
		return have_posts();
	}

	function no_items() {
		_e( 'No media attachments found.' );
	}

	function get_columns() {
		$posts_columns = array();
		$posts_columns['cb'] = '<input type="checkbox" />';
		$posts_columns['icon'] = '';
		/* translators: column name */
		$posts_columns['title'] = _x( 'File', 'column name' );
		$posts_columns['author'] = __( 'Author' );
		//$posts_columns['tags'] = _x( 'Tags', 'column name' );
		/* translators: column name */
		if ( 'upload' == $this->_screen->id ) {
			$posts_columns['parent'] = _x( 'Attached to', 'column name' );
			$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
		}
		/* translators: column name */
		$posts_columns['date'] = _x( 'Date', 'column name' );
		$posts_columns = apply_filters( 'manage_media_columns', $posts_columns, 'upload' != $this->_screen->id );

		return $posts_columns;
	}

	function get_sortable_columns() {
		return array(
			'title'    => 'title',
			'author'   => 'author',
			'parent'   => 'parent',
			'comments' => 'comment_count',
			'date'     => 'date',
		);
	}

	function display_rows() {
		global $detached, $post, $id;

		if ( $detached ) {
			$this->display_orphans();
			return;
		}

		add_filter( 'the_title','esc_html' );
		$alt = '';

		while ( have_posts() ) : the_post();

			if ( $this->is_trash && $post->post_status != 'trash'
			||  !$this->is_trash && $post->post_status == 'trash' )
				continue;

			$alt = ( 'alternate' == $alt ) ? '' : 'alternate';
			$post_owner = ( get_current_user_id() == $post->post_author ) ? 'self' : 'other';
			$att_title = _draft_or_post_title();
?>
	<tr id='post-<?php echo $id; ?>' class='<?php echo trim( $alt . ' author-' . $post_owner . ' status-' . $post->post_status ); ?>' valign="top">
<?php

list( $columns, $hidden ) = $this->get_column_info();
foreach ( $columns as $column_name => $column_display_name ) {
	$class = "class='$column_name column-$column_name'";

	$style = '';
	if ( in_array( $column_name, $hidden ) )
		$style = ' style="display:none;"';

	$attributes = $class . $style;

	switch ( $column_name ) {

	case 'cb':
?>
		<th scope="row" class="check-column"><?php if ( current_user_can( 'edit_post', $post->ID ) ) { ?><input type="checkbox" name="media[]" value="<?php the_ID(); ?>" /><?php } ?></th>
<?php
		break;

	case 'icon':
		$attributes = 'class="column-icon media-icon"' . $style;
?>
		<td <?php echo $attributes ?>><?php
			if ( $thumb = wp_get_attachment_image( $post->ID, array( 80, 60 ), true ) ) {
				if ( $this->is_trash ) {
					echo $thumb;
				} else {
?>
				<a href="<?php echo get_edit_post_link( $post->ID, true ); ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ); ?>">
					<?php echo $thumb; ?>
				</a>

<?php			}
			}
?>
		</td>
<?php
		break;

	case 'title':
?>
		<td <?php echo $attributes ?>><strong><?php if ( $this->is_trash ) echo $att_title; else { ?><a href="<?php echo get_edit_post_link( $post->ID, true ); ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ); ?>"><?php echo $att_title; ?></a><?php } ?></strong>
			<p>
<?php
			if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $post->ID ), $matches ) )
				echo esc_html( strtoupper( $matches[1] ) );
			else
				echo strtoupper( str_replace( 'image/', '', get_post_mime_type() ) );
?>
			</p>
<?php
		$actions = array();
		if ( current_user_can( 'edit_post', $post->ID ) && !$this->is_trash )
			$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '">' . __( 'Edit' ) . '</a>';
		if ( current_user_can( 'delete_post', $post->ID ) ) {
			if ( $this->is_trash )
				$actions['untrash'] = "<a class='submitdelete' href='" . wp_nonce_url( "post.php?action=untrash&amp;post=$post->ID", 'untrash-attachment_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
			elseif ( EMPTY_TRASH_DAYS && MEDIA_TRASH )
				$actions['trash'] = "<a class='submitdelete' href='" . wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-attachment_' . $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
			if ( $this->is_trash || !EMPTY_TRASH_DAYS || !MEDIA_TRASH ) {
				$delete_ays = ( !$this->is_trash && !MEDIA_TRASH ) ? " onclick='return showNotice.warn();'" : '';
				$actions['delete'] = "<a class='submitdelete'$delete_ays href='" . wp_nonce_url( "post.php?action=delete&amp;post=$post->ID", 'delete-attachment_' . $post->ID ) . "'>" . __( 'Delete Permanently' ) . "</a>";
			}
		}
		if ( !$this->is_trash ) {
			$title =_draft_or_post_title( $post->post_parent );
			$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
		}
		$actions = apply_filters( 'media_row_actions', $actions, $post );
		echo $this->row_actions( $actions );
?>
		</td>
<?php
		break;

	case 'author':
?>
		<td <?php echo $attributes ?>><?php the_author() ?></td>
<?php
		break;

	case 'tags':
?>
		<td <?php echo $attributes ?>><?php
		$tags = get_the_tags();
		if ( !empty( $tags ) ) {
			$out = array();
			foreach ( $tags as $c )
				$out[] = "<a href='edit.php?tag=$c->slug'> " . esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'post_tag', 'display' ) ) . "</a>";
			echo join( ', ', $out );
		} else {
			_e( 'No Tags' );
		}
?>
		</td>
<?php
		break;

	case 'desc':
?>
		<td <?php echo $attributes ?>><?php echo has_excerpt() ? $post->post_excerpt : ''; ?></td>
<?php
		break;

	case 'date':
		if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __( 'Unpublished' );
		} else {
			$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post, false );
			if ( ( abs( $t_diff = time() - $time ) ) < 86400 ) {
				if ( $t_diff < 0 )
					$h_time = sprintf( __( '%s from now' ), human_time_diff( $time ) );
				else
					$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			}
		}
?>
		<td <?php echo $attributes ?>><?php echo $h_time ?></td>
<?php
		break;

	case 'parent':
		if ( $post->post_parent > 0 ) {
			if ( get_post( $post->post_parent ) ) {
				$title =_draft_or_post_title( $post->post_parent );
			}
?>
			<td <?php echo $attributes ?>>
				<strong><a href="<?php echo get_edit_post_link( $post->post_parent ); ?>"><?php echo $title ?></a></strong>,
				<?php echo get_the_time( __( 'Y/m/d' ) ); ?>
			</td>
<?php
		} else {
?>
			<td <?php echo $attributes ?>><?php _e( '(Unattached)' ); ?><br />
			<a class="hide-if-no-js" onclick="findPosts.open( 'media[]','<?php echo $post->ID ?>' );return false;" href="#the-list"><?php _e( 'Attach' ); ?></a></td>
<?php
		}
		break;

	case 'comments':
		$attributes = 'class="comments column-comments num"' . $style;
?>
		<td <?php echo $attributes ?>>
			<div class="post-com-count-wrapper">
<?php
		$pending_comments = get_pending_comments_num( $post->ID );

		$this->comments_bubble( $post->ID, $pending_comments );
?>
			</div>
		</td>
<?php
		break;

	case 'actions':
?>
		<td <?php echo $attributes ?>>
			<a href="media.php?action=edit&amp;attachment_id=<?php the_ID(); ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ); ?>"><?php _e( 'Edit' ); ?></a> |
			<a href="<?php the_permalink(); ?>"><?php _e( 'Get permalink' ); ?></a>
		</td>
		<?php
		break;

	default:
?>
		<td <?php echo $attributes ?>>
			<?php do_action( 'manage_media_custom_column', $column_name, $id ); ?>
		</td>
<?php
		break;
	}
}
?>
	</tr>
<?php endwhile;
	}

	function display_orphans() {
		global $post;

		$class = '';

		while ( have_posts() ) : the_post();

			$class = ( 'alternate' == $class ) ? '' : 'alternate';
			$att_title = esc_html( _draft_or_post_title( $post->ID ) );

			$edit_link = '<a href="' . get_edit_post_link( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ) . '">%s</a>';
?>
	<tr id='post-<?php echo $post->ID; ?>' class='<?php echo $class; ?>' valign="top">
		<th scope="row" class="check-column">
		<?php if ( current_user_can( 'edit_post', $post->ID ) ) { ?>
			<input type="checkbox" name="media[]" value="<?php echo esc_attr( $post->ID ); ?>" />
		<?php } ?>
		</th>

		<td class="media-icon">
		<?php if ( $thumb = wp_get_attachment_image( $post->ID, array( 80, 60 ), true ) ) {
			printf( $edit_link, $thumb );
		} ?>
		</td>

		<td class="media column-media">
			<strong><?php printf( $edit_link, $att_title ); ?></strong><br />
<?php
			if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $post->ID ), $matches ) )
				echo esc_html( strtoupper( $matches[1] ) );
			else
				echo strtoupper( str_replace( 'image/', '', get_post_mime_type() ) );
?>
<?php
			$actions = array();
			if ( current_user_can( 'edit_post', $post->ID ) )
				$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '">' . __( 'Edit' ) . '</a>';
			if ( current_user_can( 'delete_post', $post->ID ) )
				if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
					$actions['trash'] = "<a class='submitdelete' href='" . wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-attachment_' . $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
				} else {
					$delete_ays = !MEDIA_TRASH ? " onclick='return showNotice.warn();'" : '';
					$actions['delete'] = "<a class='submitdelete'$delete_ays href='" . wp_nonce_url( "post.php?action=delete&amp;post=$post->ID", 'delete-attachment_' . $post->ID ) . "'>" . __( 'Delete Permanently' ) . "</a>";
				}
			$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $att_title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
			if ( current_user_can( 'edit_post', $post->ID ) )
				$actions['attach'] = '<a href="#the-list" onclick="findPosts.open( \'media[]\',\''.$post->ID.'\' );return false;" class="hide-if-no-js">'.__( 'Attach' ).'</a>';
			$actions = apply_filters( 'media_row_actions', $actions, $post );

			echo $this->row_actions( $actions );
?>
		</td>
		<td class="author column-author">
			<?php $author = get_userdata( $post->post_author ); echo $author->display_name; ?>
		</td>
<?php
		if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __( 'Unpublished' );
		} else {
			$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true );
			if ( ( abs( $t_diff = time() - $time ) ) < 86400 ) {
				if ( $t_diff < 0 )
					$h_time = sprintf( __( '%s from now' ), human_time_diff( $time ) );
				else
					$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			}
		}
?>
		<td class="date column-date"><?php echo $h_time ?></td>
	</tr>
<?php
		endwhile;
	}
}

class WP_Terms_Table extends WP_List_Table {

	var $callback_args;

	function WP_Terms_Table() {
		global $post_type, $taxonomy, $tax, $current_screen;

		wp_reset_vars( array( 'action', 'taxonomy', 'post_type' ) );

		if ( empty( $taxonomy ) )
			$taxonomy = 'post_tag';

		if ( !taxonomy_exists( $taxonomy ) )
			wp_die( __( 'Invalid taxonomy' ) );

		$tax = get_taxonomy( $taxonomy );

		if ( empty( $post_type ) || !in_array( $post_type, get_post_types( array( 'public' => true ) ) ) )
			$post_type = 'post';

		if ( !isset( $current_screen ) )
			set_current_screen( 'edit-' . $taxonomy );

		parent::WP_List_Table( array(
			'screen' => $current_screen,
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
		$edit_link = "edit-tags.php?action=edit&amp;taxonomy=$taxonomy&amp;post_type=$post_type&amp;tag_ID=$tag->term_id";

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
	 * @since 3.1
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

class WP_Users_Table extends WP_List_Table {

	function WP_Users_Table() {
		parent::WP_List_Table( array(
			'screen' => 'users',
			'plural' => 'users'
		) );
	}

	function check_permissions() {
		if ( !current_user_can('list_users') )
			wp_die(__('Cheatin&#8217; uh?'));
	}

	function prepare_items() {
		global $role, $usersearch;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

		$users_per_page = $this->get_items_per_page( 'users_per_page' );

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'role' => $role,
			'search' => $usersearch
		);

		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];

		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];

		// Query the user IDs for this page
		$wp_user_search = new WP_User_Query( $args );

		$this->items = $wp_user_search->get_results();
		
		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $users_per_page,		
		) );
	}

	function no_items() {
		_e( 'No matching users were found.' );
	}

	function get_views() {
		global $wp_roles, $role;

		$users_of_blog = count_users();
		$total_users = $users_of_blog['total_users'];
		$avail_roles =& $users_of_blog['avail_roles'];
		unset($users_of_blog);

		$current_role = false;
		$class = empty($role) ? ' class="current"' : '';
		$role_links = array();
		$role_links['all'] = "<li><a href='users.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users' ), number_format_i18n( $total_users ) ) . '</a>';
		foreach ( $wp_roles->get_names() as $this_role => $name ) {
			if ( !isset($avail_roles[$this_role]) )
				continue;

			$class = '';

			if ( $this_role == $role ) {
				$current_role = $role;
				$class = ' class="current"';
			}

			$name = translate_user_role( $name );
			/* translators: User role name with count */
			$name = sprintf( __('%1$s <span class="count">(%2$s)</span>'), $name, $avail_roles[$this_role] );
			$role_links[$this_role] = "<li><a href='users.php?role=$this_role'$class>$name</a>";
		}

		return $role_links;
	}

	function get_bulk_actions() {
		$actions = array();

		if ( !is_multisite() && current_user_can( 'delete_users' ) )
			$actions['delete'] = __( 'Delete' );
		else
			$actions['remove'] = __( 'Remove' );

		return $actions;
	}

	function extra_tablenav( $which ) {
		if ( 'top' != $which )
			return;
?>
	<div class="alignleft actions">
		<label class="screen-reader-text" for="new_role"><?php _e( 'Change role to&hellip;' ) ?></label>
		<select name="new_role" id="new_role">
			<option value=''><?php _e( 'Change role to&hellip;' ) ?></option>
			<?php wp_dropdown_roles(); ?>
		</select>
		<input type="submit" value="<?php esc_attr_e( 'Change' ); ?>" name="changeit" class="button-secondary" />
	</div>
<?php
	}
	
	function current_action() {
		if ( isset($_REQUEST['changeit']) && !empty($_REQUEST['new_role']) )
			return 'promote';

		return parent::current_action();
	}

	function get_columns() {
		return array(
			'cb'       => '<input type="checkbox" />',
			'username' => __( 'Login' ),
			'name'     => __( 'Name' ),
			'email'    => __( 'E-mail' ),
			'role'     => __( 'Role' ),
			'posts'    => __( 'Posts' )
		);
	}

	function get_sortable_columns() {
		return array(
			'username' => 'login',
			'name'     => 'name',
			'email'    => 'email',
			'posts'    => 'post_count',
		);
	}

	function display_rows() {
		// Query the post counts for this page
		$post_counts = count_many_users_posts( array_keys( $this->items ) );

		$style = '';
		foreach ( $this->items as $userid => $user_object ) {
			$role = reset( $user_object->roles );

			if ( is_multisite() && empty( $role ) )
				continue;

			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo "\n\t", $this->single_row( $user_object, $style, $role, $post_counts[ $userid ] );
		}
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 *
	 * @since 2.1.0
	 *
	 * @param object $user_object
	 * @param string $style Optional. Attributes added to the TR element.  Must be sanitized.
	 * @param string $role Key for the $wp_roles array.
	 * @param int $numposts Optional. Post count to display for this user.  Defaults to zero, as in, a new user has made zero posts.
	 * @return string
	 */
	function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		global $wp_roles;

		if ( !( is_object( $user_object ) && is_a( $user_object, 'WP_User' ) ) )
			$user_object = new WP_User( (int) $user_object );
		$user_object = sanitize_user_object( $user_object, 'display' );
		$email = $user_object->user_email;
		$url = $user_object->user_url;
		$short_url = str_replace( 'http://', '', $url );
		$short_url = str_replace( 'www.', '', $short_url );
		if ( '/' == substr( $short_url, -1 ) )
			$short_url = substr( $short_url, 0, -1 );
		if ( strlen( $short_url ) > 35 )
			$short_url = substr( $short_url, 0, 32 ).'...';
		$checkbox = '';
		// Check if the user for this row is editable
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link
			// TODO: make profile/user-edit determination a separate function
			if ( get_current_user_id() == $user_object->ID ) {
				$edit_link = 'profile.php';
			} else {
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), "user-edit.php?user_id=$user_object->ID" ) );
			}
			$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";

			// Set up the hover actions for this user
			$actions = array();

			if ( current_user_can( 'edit_user',  $user_object->ID ) ) {
				$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";
				$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
			} else {
				$edit = "<strong>$user_object->user_login</strong><br />";
			}

			if ( !is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'delete_user', $user_object->ID ) )
				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=delete&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Delete' ) . "</a>";
			if ( is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'remove_user', $user_object->ID ) )
				$actions['remove'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=remove&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Remove' ) . "</a>";
			$actions = apply_filters( 'user_row_actions', $actions, $user_object );
			$edit .= $this->row_actions( $actions );

			// Set up the checkbox ( because the user is editable, otherwise its empty )
			$checkbox = "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='$role' value='{$user_object->ID}' />";

		} else {
			$edit = '<strong>' . $user_object->user_login . '</strong>';
		}
		$role_name = isset( $wp_roles->role_names[$role] ) ? translate_user_role( $wp_roles->role_names[$role] ) : __( 'None' );
		$r = "<tr id='user-$user_object->ID'$style>";
		$avatar = get_avatar( $user_object->ID, 32 );

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			switch ( $column_name ) {
				case 'cb':
					$r .= "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'username':
					$r .= "<td $attributes>$avatar $edit</td>";
					break;
				case 'name':
					$r .= "<td $attributes>$user_object->first_name $user_object->last_name</td>";
					break;
				case 'email':
					$r .= "<td $attributes><a href='mailto:$email' title='" . sprintf( __( 'E-mail: %s' ), $email ) . "'>$email</a></td>";
					break;
				case 'role':
					$r .= "<td $attributes>$role_name</td>";
					break;
				case 'posts':
					$attributes = 'class="posts column-posts num"' . $style;
					$r .= "<td $attributes>";
					if ( $numposts > 0 ) {
						$r .= "<a href='edit.php?author=$user_object->ID' title='" . __( 'View posts by this author' ) . "' class='edit'>";
						$r .= $numposts;
						$r .= '</a>';
					} else {
						$r .= 0;
					}
					$r .= "</td>";
					break;
				default:
					$r .= "<td $attributes>";
					$r .= apply_filters( 'manage_users_custom_column', '', $column_name, $user_object->ID );
					$r .= "</td>";
			}
		}
		$r .= '</tr>';

		return $r;
	}
}

class WP_Comments_Table extends WP_List_Table {

	var $checkbox = true;
	var $from_ajax = false;

	var $pending_count = array();

	function WP_Comments_Table() {
		global $mode;

		$mode = ( empty( $_REQUEST['mode'] ) ) ? 'detail' : $_REQUEST['mode'];

		if ( get_option('show_avatars') && 'single' != $mode )
			add_filter( 'comment_author', 'floated_admin_avatar' );

		parent::WP_List_Table( array(
			'screen' => 'edit-comments',
			'plural' => 'comments'
		) );
	}

	function check_permissions() {
		if ( !current_user_can('edit_posts') )
			wp_die(__('Cheatin&#8217; uh?'));
	}

	function prepare_items() {
		global $post_id, $comment_status, $search;

		if ( isset( $_REQUEST['p'] ) )
			$post_id = absint( $_REQUEST['p'] );
		elseif ( isset( $_REQUEST['post'] ) )
			$post_id = absint( $_REQUEST['post'] );
		elseif ( isset( $_REQUEST['post_ID'] ) )
			$post_id = absint( $_REQUEST['post_ID'] );
		else
			$post_id = 0;

		$comment_status = isset( $_REQUEST['comment_status'] ) ? $_REQUEST['comment_status'] : 'all';
		if ( !in_array( $comment_status, array( 'all', 'moderated', 'approved', 'spam', 'trash' ) ) )
			$comment_status = 'all';

		$comment_type = !empty( $_REQUEST['comment_type'] ) ? $_REQUEST['comment_type'] : '';

		$search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : '';

		$comments_per_page = $this->get_items_per_page( 'edit_comments_per_page' );
		$comments_per_page = apply_filters( 'comments_per_page', $comments_per_page, $comment_status );

		if ( isset( $_POST['number'] ) )
			$number = (int) $_POST['number'];
		else
			$number = $comments_per_page + min( 8, $comments_per_page ); // Grab a few extra

		$page = $this->get_pagenum();

		$start = $offset = ( $page - 1 ) * $comments_per_page;

		$status_map = array(
			'moderated' => 'hold',
			'approved' => 'approve'
		);

		$args = array(
			'status' => isset( $status_map[$comment_status] ) ? $status_map[$comment_status] : $comment_status,
			'search' => $search,
			'offset' => $start,
			'number' => $number,
			'post_id' => $post_id,
			'type' => $comment_type,
			'orderby' => @$_REQUEST['orderby'],
			'order' => @$_REQUEST['order'],
		);

		$_comments = get_comments( $args );

		update_comment_cache( $_comments );

		$this->items = array_slice( $_comments, 0, $comments_per_page );
		$this->extra_items = array_slice( $_comments, $comments_per_page );

		$total_comments = get_comments( array_merge( $args, array('count' => true, 'offset' => 0, 'number' => 0) ) );

		$_comment_post_ids = array();
		foreach ( $_comments as $_c ) {
			$_comment_post_ids[] = $_c->comment_post_ID;
		}

		$this->pending_count = get_pending_comments_num( $_comment_post_ids );

		$this->set_pagination_args( array(
			'total_items' => $total_comments,
			'per_page' => $comments_per_page,
		) );
	}

	function get_views() {
		global $post_id, $comment_status;

		$status_links = array();
		$num_comments = ( $post_id ) ? wp_count_comments( $post_id ) : wp_count_comments();
		//, number_format_i18n($num_comments->moderated) ), "<span class='comment-count'>" . number_format_i18n($num_comments->moderated) . "</span>"),
		//, number_format_i18n($num_comments->spam) ), "<span class='spam-comment-count'>" . number_format_i18n($num_comments->spam) . "</span>")
		$stati = array(
				'all' => _nx_noop('All', 'All', 'comments'), // singular not used
				'moderated' => _n_noop('Pending <span class="count">(<span class="pending-count">%s</span>)</span>', 'Pending <span class="count">(<span class="pending-count">%s</span>)</span>'),
				'approved' => _n_noop('Approved', 'Approved'), // singular not used
				'spam' => _n_noop('Spam <span class="count">(<span class="spam-count">%s</span>)</span>', 'Spam <span class="count">(<span class="spam-count">%s</span>)</span>'),
				'trash' => _n_noop('Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>')
			);

		if ( !EMPTY_TRASH_DAYS )
			unset($stati['trash']);

		$link = 'edit-comments.php';
		if ( !empty($comment_type) && 'all' != $comment_type )
			$link = add_query_arg( 'comment_type', $comment_type, $link );

		foreach ( $stati as $status => $label ) {
			$class = ( $status == $comment_status ) ? ' class="current"' : '';

			if ( !isset( $num_comments->$status ) )
				$num_comments->$status = 10;
			$link = add_query_arg( 'comment_status', $status, $link );
			if ( $post_id )
				$link = add_query_arg( 'p', absint( $post_id ), $link );
			/*
			// I toyed with this, but decided against it. Leaving it in here in case anyone thinks it is a good idea. ~ Mark
			if ( !empty( $_REQUEST['s'] ) )
				$link = add_query_arg( 's', esc_attr( stripslashes( $_REQUEST['s'] ) ), $link );
			*/
			$status_links[$status] = "<li class='$status'><a href='$link'$class>" . sprintf(
				_n( $label[0], $label[1], $num_comments->$status ),
				number_format_i18n( $num_comments->$status )
			) . '</a>';
		}

		$status_links = apply_filters( 'comment_status_links', $status_links );
		return $status_links;
	}

	function get_bulk_actions() {
		global $comment_status;

		$actions = array();
		if ( in_array( $comment_status, array( 'all', 'approved' ) ) )
			$actions['unapprove'] = __( 'Unapprove' );
		if ( in_array( $comment_status, array( 'all', 'moderated', 'spam' ) ) )
			$actions['approve'] = __( 'Approve' );
		if ( in_array( $comment_status, array( 'all', 'moderated', 'approved' ) ) )
			$actions['spam'] = _x( 'Mark as Spam', 'comment' );

		if ( 'trash' == $comment_status )
			$actions['untrash'] = __( 'Restore' );
		elseif ( 'spam' == $comment_status )
			$actions['unspam'] = _x( 'Not Spam', 'comment' );

		if ( in_array( $comment_status, array( 'trash', 'spam' ) ) || !EMPTY_TRASH_DAYS )
			$actions['delete'] = __( 'Delete Permanently' );
		else
			$actions['trash'] = __( 'Move to Trash' );

		return $actions;
	}

	function extra_tablenav( $which ) {
		global $comment_status, $comment_type;
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which ) {
?>
			<select name="comment_type">
				<option value=""><?php _e( 'Show all comment types' ); ?></option>
<?php
				$comment_types = apply_filters( 'admin_comment_types_dropdown', array(
					'comment' => __( 'Comments' ),
					'pings' => __( 'Pings' ),
				) );

				foreach ( $comment_types as $type => $label )
					echo "\t<option value='" . esc_attr( $type ) . "'" . selected( $comment_type, $type, false ) . ">$label</option>\n";
			?>
			</select>
			<input type="submit" id="post-query-submit" value="<?php esc_attr_e( 'Filter' ); ?>" class="button-secondary" />
<?php
		}

		if ( ( 'spam' == $comment_status || 'trash' == $comment_status ) && current_user_can( 'moderate_comments' ) ) {
			wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );
			$title = ( 'spam' == $comment_status ) ? esc_attr__( 'Empty Spam' ) : esc_attr__( 'Empty Trash' );
?>
			<input type="submit" name="delete_all" id="delete_all" value="<?php echo $title ?>" class="button-secondary apply" />
<?php
		}
?>
<?php
		do_action( 'manage_comments_nav', $comment_status );
		echo '</div>';
	}

	function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) )
			return 'delete_all';

		return parent::current_action();
	}

	function get_columns() {
		global $mode;

		$columns = array();

		if ( $this->checkbox )
			$columns['cb'] = '<input type="checkbox" />';

		$columns['author'] = __( 'Author' );
		$columns['comment'] = _x( 'Comment', 'column name' );

		if ( 'single' !== $mode )
			$columns['response'] = _x( 'Comment', 'column name' );

		return $columns;
	}

	function get_sortable_columns() {
		return array(
			'author'   => 'comment_author',
			'comment'  => 'comment_content',
			'response' => 'comment_post_ID'
		);
	}

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

	<tbody id="the-comment-list" class="list:comment">
		<?php $this->display_rows(); ?>
	</tbody>

	<tbody id="the-extra-comment-list" class="list:comment" style="display: none;">
		<?php $this->items = $this->extra_items; $this->display_rows(); ?>
	</tbody>
</table>
<?php

		$this->display_tablenav( 'bottom' );
	}

	function single_row( $a_comment ) {
		global $post, $comment, $the_comment_status;

		$comment = $a_comment;
		$the_comment_status = wp_get_comment_status( $comment->comment_ID );

		$post = get_post( $comment->comment_post_ID );

		$this->user_can = current_user_can( 'edit_comment', $comment->comment_ID );

		echo "<tr id='comment-$comment->comment_ID' class='$the_comment_status'>";
		echo $this->single_row_columns( $comment );
		echo "</tr>\n";
	}

	function column_cb( $comment ) {
		if ( $this->user_can )
			echo "<input type='checkbox' name='delete_comments[]' value='$comment->comment_ID' />";
	}

	function column_comment( $comment ) {
		global $post, $comment_status, $the_comment_status;

		$user_can = $this->user_can;

		$comment_url = esc_url( get_comment_link( $comment->comment_ID ) );

		$ptime = date( 'G', strtotime( $comment->comment_date ) );
		if ( ( abs( time() - $ptime ) ) < 86400 )
			$ptime = sprintf( __( '%s ago' ), human_time_diff( $ptime ) );
		else
			$ptime = mysql2date( __( 'Y/m/d \a\t g:i A' ), $comment->comment_date );

		if ( $user_can ) {
			$del_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_$comment->comment_ID" ) );
			$approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_$comment->comment_ID" ) );

			$url = "comment.php?c=$comment->comment_ID";

			$approve_url = esc_url( $url . "&action=approvecomment&$approve_nonce" );
			$unapprove_url = esc_url( $url . "&action=unapprovecomment&$approve_nonce" );
			$spam_url = esc_url( $url . "&action=spamcomment&$del_nonce" );
			$unspam_url = esc_url( $url . "&action=unspamcomment&$del_nonce" );
			$trash_url = esc_url( $url . "&action=trashcomment&$del_nonce" );
			$untrash_url = esc_url( $url . "&action=untrashcomment&$del_nonce" );
			$delete_url = esc_url( $url . "&action=deletecomment&$del_nonce" );
		}

		echo '<div id="submitted-on">';
		/* translators: 2: comment date, 3: comment time */
		printf( __( '<a href="%1$s">%2$s at %3$s</a>' ), $comment_url,
			/* translators: comment date format. See http://php.net/date */ get_comment_date( __( 'Y/m/d' ) ),
			/* translators: comment time format. See http://php.net/date */ get_comment_date( get_option( 'time_format' ) ) );

		if ( $comment->comment_parent ) {
			$parent = get_comment( $comment->comment_parent );
			$parent_link = esc_url( get_comment_link( $comment->comment_parent ) );
			$name = get_comment_author( $parent->comment_ID );
			printf( ' | '.__( 'In reply to <a href="%1$s">%2$s</a>.' ), $parent_link, $name );
		}

		echo '</div>';
		comment_text();
		if ( $user_can ) { ?>
		<div id="inline-<?php echo $comment->comment_ID; ?>" class="hidden">
		<textarea class="comment" rows="1" cols="1"><?php echo esc_html( apply_filters( 'comment_edit_pre', $comment->comment_content ) ); ?></textarea>
		<div class="author-email"><?php echo esc_attr( $comment->comment_author_email ); ?></div>
		<div class="author"><?php echo esc_attr( $comment->comment_author ); ?></div>
		<div class="author-url"><?php echo esc_attr( $comment->comment_author_url ); ?></div>
		<div class="comment_status"><?php echo $comment->comment_approved; ?></div>
		</div>
		<?php
		}

		if ( $user_can ) {
			// preorder it: Approve | Reply | Quick Edit | Edit | Spam | Trash
			$actions = array(
				'approve' => '', 'unapprove' => '',
				'reply' => '',
				'quickedit' => '',
				'edit' => '',
				'spam' => '', 'unspam' => '',
				'trash' => '', 'untrash' => '', 'delete' => ''
			);

			if ( $comment_status && 'all' != $comment_status ) { // not looking at all comments
				if ( 'approved' == $the_comment_status )
					$actions['unapprove'] = "<a href='$unapprove_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment&amp;new=unapproved vim-u vim-destructive' title='" . esc_attr__( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
				else if ( 'unapproved' == $the_comment_status )
					$actions['approve'] = "<a href='$approve_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment&amp;new=approved vim-a vim-destructive' title='" . esc_attr__( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
			} else {
				$actions['approve'] = "<a href='$approve_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=approved vim-a' title='" . esc_attr__( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
				$actions['unapprove'] = "<a href='$unapprove_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u' title='" . esc_attr__( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
			}

			if ( 'spam' != $the_comment_status && 'trash' != $the_comment_status ) {
				$actions['spam'] = "<a href='$spam_url' class='delete:the-comment-list:comment-$comment->comment_ID::spam=1 vim-s vim-destructive' title='" . esc_attr__( 'Mark this comment as spam' ) . "'>" . /* translators: mark as spam link */ _x( 'Spam', 'verb' ) . '</a>';
			} elseif ( 'spam' == $the_comment_status ) {
				$actions['unspam'] = "<a href='$unspam_url' class='delete:the-comment-list:comment-$comment->comment_ID:66cc66:unspam=1 vim-z vim-destructive'>" . _x( 'Not Spam', 'comment' ) . '</a>';
			} elseif ( 'trash' == $the_comment_status ) {
				$actions['untrash'] = "<a href='$untrash_url' class='delete:the-comment-list:comment-$comment->comment_ID:66cc66:untrash=1 vim-z vim-destructive'>" . __( 'Restore' ) . '</a>';
			}

			if ( 'spam' == $the_comment_status || 'trash' == $the_comment_status || !EMPTY_TRASH_DAYS ) {
				$actions['delete'] = "<a href='$delete_url' class='delete:the-comment-list:comment-$comment->comment_ID::delete=1 delete vim-d vim-destructive'>" . __( 'Delete Permanently' ) . '</a>';
			} else {
				$actions['trash'] = "<a href='$trash_url' class='delete:the-comment-list:comment-$comment->comment_ID::trash=1 delete vim-d vim-destructive' title='" . esc_attr__( 'Move this comment to the trash' ) . "'>" . _x( 'Trash', 'verb' ) . '</a>';
			}

			if ( 'trash' != $the_comment_status ) {
				$actions['edit'] = "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . esc_attr__( 'Edit comment' ) . "'>". __( 'Edit' ) . '</a>';
				$actions['quickedit'] = '<a onclick="commentReply.open( \''.$comment->comment_ID.'\',\''.$post->ID.'\',\'edit\' );return false;" class="vim-q" title="'.esc_attr__( 'Quick Edit' ).'" href="#">' . __( 'Quick&nbsp;Edit' ) . '</a>';
				if ( 'spam' != $the_comment_status )
					$actions['reply'] = '<a onclick="commentReply.open( \''.$comment->comment_ID.'\',\''.$post->ID.'\' );return false;" class="vim-r" title="'.esc_attr__( 'Reply to this comment' ).'" href="#">' . __( 'Reply' ) . '</a>';
			}

			$actions = apply_filters( 'comment_row_actions', array_filter( $actions ), $comment );

			$i = 0;
			echo '<div class="row-actions">';
			foreach ( $actions as $action => $link ) {
				++$i;
				( ( ( 'approve' == $action || 'unapprove' == $action ) && 2 === $i ) || 1 === $i ) ? $sep = '' : $sep = ' | ';

				// Reply and quickedit need a hide-if-no-js span when not added with ajax
				if ( ( 'reply' == $action || 'quickedit' == $action ) && ! $this->from_ajax )
					$action .= ' hide-if-no-js';
				elseif ( ( $action == 'untrash' && $the_comment_status == 'trash' ) || ( $action == 'unspam' && $the_comment_status == 'spam' ) ) {
					if ( '1' == get_comment_meta( $comment->comment_ID, '_wp_trash_meta_status', true ) )
						$action .= ' approve';
					else
						$action .= ' unapprove';
				}

				echo "<span class='$action'>$sep$link</span>";
			}
			echo '</div>';
		}
	}

	function column_author( $comment ) {
		global $comment_status;

		$author_url = get_comment_author_url();
		if ( 'http://' == $author_url )
			$author_url = '';
		$author_url_display = preg_replace( '|http://(www\.)?|i', '', $author_url );
		if ( strlen( $author_url_display ) > 50 )
			$author_url_display = substr( $author_url_display, 0, 49 ) . '...';

		echo "<strong>"; comment_author(); echo '</strong><br />';
		if ( !empty( $author_url ) )
			echo "<a title='$author_url' href='$author_url'>$author_url_display</a><br />";

		if ( $this->user_can ) {
			if ( !empty( $comment->comment_author_email ) ) {
				comment_author_email_link();
				echo '<br />';
			}
			echo '<a href="edit-comments.php?s=';
			comment_author_IP();
			echo '&amp;mode=detail';
			if ( 'spam' == $comment_status )
				echo '&amp;comment_status=spam';
			echo '">';
			comment_author_IP();
			echo '</a>';
		}
	}

	function column_date( $comment ) {
		return get_comment_date( __( 'Y/m/d \a\t g:ia' ) );
	}

	function column_response( $comment ) {
		global $post;

		if ( isset( $this->pending_count[$post->ID] ) ) {
			$pending_comments = $this->pending_count[$post->ID];
		} else {
			$_pending_count_temp = get_pending_comments_num( array( $post->ID ) );
			$pending_comments = $this->pending_count[$post->ID] = $_pending_count_temp[$post->ID];
		}

		if ( current_user_can( 'edit_post', $post->ID ) ) {
			$post_link = "<a href='" . get_edit_post_link( $post->ID ) . "'>";
			$post_link .= get_the_title( $post->ID ) . '</a>';
		} else {
			$post_link = get_the_title( $post->ID );
		}

		echo '<div class="response-links"><span class="post-com-count-wrapper">';
		echo $post_link . '<br />';
		$this->comments_bubble( $post->ID, $pending_comments );
		echo '</span> ';
		echo "<a href='" . get_permalink( $post->ID ) . "'>#</a>";
		echo '</div>';
		if ( 'attachment' == $post->post_type && ( $thumb = wp_get_attachment_image( $post->ID, array( 80, 60 ), true ) ) )
			echo $thumb;
	}

	function column_default( $comment, $column_name ) {
		do_action( 'manage_comments_custom_column', $column_name, $comment->comment_ID );
	}
}

class WP_Post_Comments_Table extends WP_Comments_Table {

	function get_columns() {
		return array(
			'author'   => __( 'Author' ),
			'comment'  => _x( 'Comment', 'column name' ),
		);
	}

	function get_sortable_columns() {
		return array();
	}
}

class WP_Links_Table extends WP_List_Table {

	function WP_Links_Table() {
		parent::WP_List_Table( array(
			'screen' => 'link-manager',
			'plural' => 'bookmarks',
		) );
	}

	function check_permissions() {
		if ( ! current_user_can( 'manage_links' ) )
			wp_die( __( 'You do not have sufficient permissions to edit the links for this site.' ) );
	}

	function prepare_items() {
		global $cat_id, $s, $orderby, $order;

		wp_reset_vars( array( 'action', 'cat_id', 'linkurl', 'name', 'image', 'description', 'visible', 'target', 'category', 'link_id', 'submit', 'orderby', 'order', 'links_show_cat_id', 'rating', 'rel', 'notes', 'linkcheck[]', 's' ) );

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
?>
			<input type="submit" id="post-query-submit" value="<?php esc_attr_e( 'Filter' ); ?>" class="button-secondary" />
		</div>
<?php
	}

	function get_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'name'       => __( 'Name' ),
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

			$short_url = str_replace( 'http://', '', $link->link_url );
			$short_url = preg_replace( '/^www\./i', '', $short_url );
			if ( '/' == substr( $short_url, -1 ) )
				$short_url = substr( $short_url, 0, -1 );
			if ( strlen( $short_url ) > 35 )
				$short_url = substr( $short_url, 0, 32 ).'...';

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
					case 'cb':
						echo '<th scope="row" class="check-column"><input type="checkbox" name="linkcheck[]" value="'. esc_attr( $link->link_id ) .'" /></th>';
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
						echo "<td $attributes><a href='$link->link_url' title='".sprintf( __( 'Visit %s' ), $link->link_name )."'>$short_url</a></td>";
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

class WP_Sites_Table extends WP_List_Table {

	function WP_Sites_Table() {
		parent::WP_List_Table( array(
			'screen' => 'sites-network',
			'plural' => 'sites',
		) );
	}

	function check_permissions() {		
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );
	}

	function prepare_items() {
		global $s, $mode, $wpdb;

		$mode = ( empty( $_REQUEST['mode'] ) ) ? 'list' : $_REQUEST['mode'];

		$per_page = $this->get_items_per_page( 'sites_network_per_page' );

		$pagenum = $this->get_pagenum();

		$s = isset( $_REQUEST['s'] ) ? stripslashes( trim( $_REQUEST[ 's' ] ) ) : '';
		$like_s = esc_sql( like_escape( $s ) );

		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";

		if ( isset( $_REQUEST['searchaction'] ) ) {
			if ( 'name' == $_REQUEST['searchaction'] ) {
				$query .= " AND ( {$wpdb->blogs}.domain LIKE '%{$like_s}%' OR {$wpdb->blogs}.path LIKE '%{$like_s}%' ) ";
			} elseif ( 'id' == $_REQUEST['searchaction'] ) {
				$query .= " AND {$wpdb->blogs}.blog_id = '{$like_s}' ";
			} elseif ( 'ip' == $_REQUEST['searchaction'] ) {
				$query = "SELECT *
					FROM {$wpdb->blogs}, {$wpdb->registration_log}
					WHERE site_id = '{$wpdb->siteid}'
					AND {$wpdb->blogs}.blog_id = {$wpdb->registration_log}.blog_id
					AND {$wpdb->registration_log}.IP LIKE ( '%{$like_s}%' )";
			}
		}

		$order_by = isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'id';
		if ( $order_by == 'registered' ) {
			$query .= ' ORDER BY registered ';
		} elseif ( $order_by == 'lastupdated' ) {
			$query .= ' ORDER BY last_updated ';
		} elseif ( $order_by == 'blogname' ) {
			$query .= ' ORDER BY domain ';
		} else {
			$order_by = 'id';
			$query .= " ORDER BY {$wpdb->blogs}.blog_id ";
		}

		$order = ( isset( $_REQUEST['order'] ) && 'DESC' == strtoupper( $_REQUEST['order'] ) ) ? "DESC" : "ASC";
		$query .= $order;

		$total = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT( blog_id )', $query ) );

		$query .= " LIMIT " . intval( ( $pagenum - 1 ) * $per_page ) . ", " . intval( $per_page );
		$this->items = $wpdb->get_results( $query, ARRAY_A );

		$this->set_pagination_args( array(
			'total_items' => $total,
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		_e( 'No sites found.' );
	}

	function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete' );
		$actions['spam'] = _x( 'Mark as Spam', 'site' );
		$actions['notspam'] = _x( 'Not Spam', 'site' );

		return $actions;
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which )
			$this->view_switcher( $mode );
	}

	function get_columns() {
		$blogname_columns = ( is_subdomain_install() ) ? __( 'Domain' ) : __( 'Path' );
		$sites_columns = array(
			'cb'          => '<input type="checkbox" />',
			'blogname'    => $blogname_columns,
			'lastupdated' => __( 'Last Updated' ),
			'registered'  => _x( 'Registered', 'site' ),
			'users'       => __( 'Users' )
		);

		if ( has_filter( 'wpmublogsaction' ) )
			$sites_columns['plugins'] = __( 'Actions' );

		$sites_columns = apply_filters( 'wpmu_blogs_columns', $sites_columns );

		return $sites_columns;
	}

	function get_sortable_columns() {
		return array(
			'id'          => 'id',
			'blogname'    => 'blogname',
			'lastupdated' => 'lastupdated',
			'registered'  => 'registered',
		);
	}

	function display_rows() {
		global $current_site, $mode;

		$status_list = array(
			'archived' => array( 'site-archived', __( 'Archived' ) ),
			'spam'     => array( 'site-spammed', _x( 'Spam', 'site' ) ),
			'deleted'  => array( 'site-deleted', __( 'Deleted' ) ),
			'mature'   => array( 'site-mature', __( 'Mature' ) )
		);

		$class = '';
		foreach ( $this->items as $blog ) {
			$class = ( 'alternate' == $class ) ? '' : 'alternate';
			reset( $status_list );

			$blog_states = array();
			foreach ( $status_list as $status => $col ) {
				if ( get_blog_status( $blog['blog_id'], $status ) == 1 ) {
					$class = $col[0];
					$blog_states[] = $col[1];
				}
			}
			$blog_state = '';
			if ( ! empty( $blog_states ) ) {
				$state_count = count( $blog_states );
				$i = 0;
				$blog_state .= ' - ';
				foreach ( $blog_states as $state ) {
					++$i;
					( $i == $state_count ) ? $sep = '' : $sep = ', ';
					$blog_state .= "<span class='post-state'>$state$sep</span>";
				}
			}
			echo "<tr class='$class'>";

			$blogname = ( is_subdomain_install() ) ? str_replace( '.'.$current_site->domain, '', $blog['domain'] ) : $blog['path'];

			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) {
				switch ( $column_name ) {
					case 'cb': ?>
						<th scope="row" class="check-column">
							<input type="checkbox" id="blog_<?php echo $blog['blog_id'] ?>" name="allblogs[]" value="<?php echo esc_attr( $blog['blog_id'] ) ?>" />
						</th>
					<?php
					break;

					case 'id': ?>
						<th valign="top" scope="row">
							<?php echo $blog['blog_id'] ?>
						</th>
					<?php
					break;

					case 'blogname': ?>
						<td class="column-title">
							<a href="<?php echo esc_url( network_admin_url( 'sites.php?action=editblog&amp;id=' . $blog['blog_id'] ) ); ?>" class="edit"><?php echo $blogname . $blog_state; ?></a>
							<?php
							if ( 'list' != $mode )
								echo '<p>' . sprintf( _x( '%1$s &#8211; <em>%2$s</em>', '%1$s: site name. %2$s: site tagline.' ), get_blog_option( $blog['blog_id'], 'blogname' ), get_blog_option( $blog['blog_id'], 'blogdescription ' ) ) . '</p>';

							// Preordered.
							$actions = array(
								'edit' => '', 'backend' => '',
								'activate' => '', 'deactivate' => '',
								'archive' => '', 'unarchive' => '',
								'spam' => '', 'unspam' => '',
								'delete' => '',
								'visit' => '',
							);

							$actions['edit']	= '<span class="edit"><a href="' . esc_url( network_admin_url( 'sites.php?action=editblog&amp;id=' . $blog['blog_id'] ) ) . '">' . __( 'Edit' ) . '</a></span>';
							$actions['backend']	= "<span class='backend'><a href='" . esc_url( get_admin_url( $blog['blog_id'] ) ) . "' class='edit'>" . __( 'Backend' ) . '</a></span>';
							if ( $current_site->blog_id != $blog['blog_id'] ) {
								if ( get_blog_status( $blog['blog_id'], 'deleted' ) == '1' )
									$actions['activate']	= '<span class="activate"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=activateblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to activate the site %s' ), $blogname ) ) ) ) . '">' . __( 'Activate' ) . '</a></span>';
								else
									$actions['deactivate']	= '<span class="activate"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=deactivateblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to deactivate the site %s' ), $blogname ) ) ) ) . '">' . __( 'Deactivate' ) . '</a></span>';

								if ( get_blog_status( $blog['blog_id'], 'archived' ) == '1' )
									$actions['unarchive']	= '<span class="archive"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=unarchiveblog&amp;id=' .  $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to unarchive the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Unarchive' ) . '</a></span>';
								else
									$actions['archive']	= '<span class="archive"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=archiveblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to archive the site %s.' ), $blogname ) ) ) ) . '">' . _x( 'Archive', 'verb; site' ) . '</a></span>';

								if ( get_blog_status( $blog['blog_id'], 'spam' ) == '1' )
									$actions['unspam']	= '<span class="spam"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=unspamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to unspam the site %s.' ), $blogname ) ) ) ) . '">' . _x( 'Not Spam', 'site' ) . '</a></span>';
								else
									$actions['spam']	= '<span class="spam"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=spamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to mark the site %s as spam.' ), $blogname ) ) ) ) . '">' . _x( 'Spam', 'site' ) . '</a></span>';

								$actions['delete']	= '<span class="delete"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to delete the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Delete' ) . '</a></span>';
							}

							$actions['visit']	= "<span class='view'><a href='" . esc_url( get_home_url( $blog['blog_id'] ) ) . "' rel='permalink'>" . __( 'Visit' ) . '</a></span>';
							$actions = array_filter( $actions );
							echo $this->row_actions( $actions );
					?>
						</td>
					<?php
					break;

					case 'lastupdated': ?>
						<td valign="top">
							<?php
							if ( 'list' == $mode )
								$date = 'Y/m/d';
							else
								$date = 'Y/m/d \<\b\r \/\> g:i:s a';
							echo ( $blog['last_updated'] == '0000-00-00 00:00:00' ) ? __( 'Never' ) : mysql2date( $date, $blog['last_updated'] ); ?>
						</td>
					<?php
					break;
				case 'registered': ?>
						<td valign="top">
						<?php
						if ( $blog['registered'] == '0000-00-00 00:00:00' )
							echo '&#x2014;';
						else
							echo mysql2date( $date, $blog['registered'] );
						?>
						</td>
				<?php
				break;
					case 'users': ?>
						<td valign="top">
							<?php
							$blogusers = get_users_of_blog( $blog['blog_id'] );
							if ( is_array( $blogusers ) ) {
								$blogusers_warning = '';
								if ( count( $blogusers ) > 5 ) {
									$blogusers = array_slice( $blogusers, 0, 5 );
									$blogusers_warning = __( 'Only showing first 5 users.' ) . ' <a href="' . esc_url( get_admin_url( $blog['blog_id'], 'users.php' ) ) . '">' . __( 'More' ) . '</a>';
								}
								foreach ( $blogusers as $user_object ) {
									echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $user_object->ID ) ) . '">' . esc_html( $user_object->user_login ) . '</a> ';
									if ( 'list' != $mode )
										echo '( ' . $user_object->user_email . ' )';
									echo '<br />';
								}
								if ( $blogusers_warning != '' )
									echo '<strong>' . $blogusers_warning . '</strong><br />';
							}
							?>
						</td>
					<?php
					break;

					case 'plugins': ?>
						<?php if ( has_filter( 'wpmublogsaction' ) ) { ?>
						<td valign="top">
							<?php do_action( 'wpmublogsaction', $blog['blog_id'] ); ?>
						</td>
						<?php } ?>
					<?php break;

					default: ?>
						<?php if ( has_filter( 'manage_blogs_custom_column' ) ) { ?>
						<td valign="top">
							<?php do_action( 'manage_blogs_custom_column', $column_name, $blog['blog_id'] ); ?>
						</td>
						<?php } ?>
					<?php break;
				}
			}
			?>
			</tr>
			<?php
		}
	}
}

class WP_MS_Users_Table extends WP_List_Table {

	function WP_MS_Users_Table() {
		parent::WP_List_Table( array(
			'screen' => 'users-network',
		) );
	}

	function check_permissions() {
		if ( !is_multisite() )
			wp_die( __( 'Multisite support is not enabled.' ) );
	
		if ( ! current_user_can( 'manage_network_users' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );
	}

	function prepare_items() {
		global $usersearch;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$users_per_page = $this->get_items_per_page( 'users_network_per_page' );

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'search' => $usersearch,
			'blog_id' => 0
		);

		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];

		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];

		// Query the user IDs for this page
		$wp_user_search = new WP_User_Query( $args );

		$this->items = $wp_user_search->get_results();
		
		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $users_per_page,		
		) );
	}

	function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete' );
		$actions['spam'] = _x( 'Mark as Spam', 'user' );
		$actions['notspam'] = _x( 'Not Spam', 'user' );

		return $actions;
	}

	function no_items() {
		_e( 'No users found.' );
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination ( $which );

		if ( 'top' == $which )
			$this->view_switcher( $mode );
	}

	function get_columns() {
		$users_columns = array(
			'cb'         => '<input type="checkbox" />',
			'login'      => __( 'Login' ),
			'name'       => __( 'Name' ),
			'email'      => __( 'E-mail' ),
			'registered' => _x( 'Registered', 'user' ),
			'blogs'      => __( 'Sites' )
		);
		$users_columns = apply_filters( 'wpmu_users_columns', $users_columns );

		return $users_columns;
	}

	function get_sortable_columns() {
		return array(
			'id'         => 'id',
			'login'      => 'login',
			'name'       => 'name',
			'email'      => 'email',
			'registered' => 'registered',
		);
	}

	function display_rows() {
		global $current_site, $mode;

		$class = '';
		$super_admins = get_super_admins();
		foreach ( $this->items as $user ) {
			$class = ( 'alternate' == $class ) ? '' : 'alternate';

			$status_list = array( 'spam' => 'site-spammed', 'deleted' => 'site-deleted' );

			foreach ( $status_list as $status => $col ) {
				if ( $user->$status )
					$class = $col;
			}

			?>
			<tr class="<?php echo $class; ?>">
			<?php

			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) :
				switch ( $column_name ) {
					case 'cb': ?>
						<th scope="row" class="check-column">
							<input type="checkbox" id="blog_<?php echo $user->ID ?>" name="allusers[]" value="<?php echo esc_attr( $user->ID ) ?>" />
						</th>
					<?php
					break;

					case 'id': ?>
						<th valign="top" scope="row">
							<?php echo $user->ID ?>
						</th>
					<?php
					break;

					case 'login':
						$avatar	= get_avatar( $user->user_email, 32 );
						$edit_link = ( get_current_user_id() == $user->ID ) ? 'profile.php' : 'user-edit.php?user_id=' . $user->ID;
						?>
						<td class="username column-username">
							<?php echo $avatar; ?><strong><a href="<?php echo esc_url( self_admin_url( $edit_link ) ); ?>" class="edit"><?php echo stripslashes( $user->user_login ); ?></a><?php
							if ( in_array( $user->user_login, $super_admins ) )
								echo ' - ' . __( 'Super admin' );
							?></strong>
							<br/>
							<?php
								$actions = array();
								$actions['edit'] = '<a href="' . esc_url( self_admin_url( $edit_link ) ) . '">' . __( 'Edit' ) . '</a>';

								if ( ! in_array( $user->user_login, $super_admins ) ) {
									$actions['delete'] = '<a href="' . $delete = esc_url( network_admin_url( add_query_arg( '_wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), wp_nonce_url( 'edit.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user->ID ) ) ) . '" class="delete">' . __( 'Delete' ) . '</a>';
								}

								echo $this->row_actions( $actions );
							?>
							</div>
						</td>
					<?php
					break;

					case 'name': ?>
						<td class="name column-name"><?php echo $user->display_name ?></td>
					<?php
					break;

					case 'email': ?>
						<td class="email column-email"><a href="mailto:<?php echo $user->user_email ?>"><?php echo $user->user_email ?></a></td>
					<?php
					break;

					case 'registered':
						if ( 'list' == $mode )
							$date = 'Y/m/d';
						else
							$date = 'Y/m/d \<\b\r \/\> g:i:s a';
					?>
						<td><?php echo mysql2date( $date, $user->user_registered ); ?></td>
					<?php
					break;

					case 'blogs':
						$blogs = get_blogs_of_user( $user->ID, true );
						?>
						<td>
							<?php
							if ( is_array( $blogs ) ) {
								foreach ( (array) $blogs as $key => $val ) {
									$path	= ( $val->path == '/' ) ? '' : $val->path;
									echo '<a href="'. esc_url( network_admin_url( 'sites.php?action=editblog&amp;id=' . $val->userblog_id ) ) .'">' . str_replace( '.' . $current_site->domain, '', $val->domain . $path ) . '</a>';
									echo ' <small class="row-actions">';

									// Edit
									echo '<a href="'. esc_url( network_admin_url( 'sites.php?action=editblog&amp;id=' . $val->userblog_id ) ) .'">' . __( 'Edit' ) . '</a> | ';

									// View
									echo '<a ';
									if ( get_blog_status( $val->userblog_id, 'spam' ) == 1 )
										echo 'style="background-color: #faa" ';
									echo 'href="' .  esc_url( get_home_url( $val->userblog_id ) )  . '">' . __( 'View' ) . '</a>';

									echo '</small><br />';
								}
							}
							?>
						</td>
					<?php
					break;

					default: ?>
						<td><?php do_action( 'manage_users_custom_column', $column_name, $user->ID ); ?></td>
					<?php
					break;
				}
			endforeach
			?>
			</tr>
			<?php
		}
	}
}

class WP_Plugins_Table extends WP_List_Table {

	function WP_Plugins_Table() {
		global $status, $page;

		$default_status = get_user_option( 'plugins_last_view' );
		if ( empty( $default_status ) )
			$default_status = 'all';
		$status = isset( $_REQUEST['plugin_status'] ) ? $_REQUEST['plugin_status'] : $default_status;
		if ( !in_array( $status, array( 'all', 'active', 'inactive', 'recently_activated', 'upgrade', 'network', 'mustuse', 'dropins', 'search' ) ) )
			$status = 'all';
		if ( $status != $default_status && 'search' != $status )
			update_user_meta( get_current_user_id(), 'plugins_last_view', $status );

		$page = $this->get_pagenum();

		parent::WP_List_Table( array(
			'screen' => 'plugins',
			'plural' => 'plugins',
		) );
	}

	function check_permissions() {
		if ( is_multisite() ) {
			$menu_perms = get_site_option( 'menu_items', array() );

			if ( empty( $menu_perms['plugins'] ) ) {
				if ( !is_super_admin() )
					wp_die( __( 'Cheatin&#8217; uh?' ) );
			}
		}

		if ( !current_user_can('activate_plugins') )
			wp_die( __( 'You do not have sufficient permissions to manage plugins for this site.' ) );
	}

	function prepare_items() {
		global $status, $plugins, $totals, $page, $orderby, $order, $s;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		$plugins = array(
			'all' => apply_filters( 'all_plugins', get_plugins() ),
			'search' => array(),
			'active' => array(),
			'inactive' => array(),
			'recently_activated' => array(),
			'upgrade' => array(),
			'mustuse' => array(),
			'dropins' => array()
		);

		if ( ! is_multisite() || ( is_network_admin() && current_user_can('manage_network_plugins') ) ) {
			if ( apply_filters( 'show_advanced_plugins', true, 'mustuse' ) )
				$plugins['mustuse'] = get_mu_plugins();
			if ( apply_filters( 'show_advanced_plugins', true, 'dropins' ) )
				$plugins['dropins'] = get_dropins();
		}

		set_transient( 'plugin_slugs', array_keys( $plugins['all'] ), 86400 );

		$recently_activated = get_option( 'recently_activated', array() );

		$one_week = 7*24*60*60;
		foreach ( $recently_activated as $key => $time )
			if ( $time + $one_week < time() )
				unset( $recently_activated[$key] );
		update_option( 'recently_activated', $recently_activated );

		$current = get_site_transient( 'update_plugins' );

		foreach ( array( 'all', 'mustuse', 'dropins' ) as $type ) {
			foreach ( (array) $plugins[$type] as $plugin_file => $plugin_data ) {
				// Translate, Apply Markup, Sanitize HTML
				$plugins[$type][$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
			}
		}

		foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
			// Filter into individual sections
			if ( is_plugin_active_for_network($plugin_file) && !is_network_admin() ) {
				unset( $plugins['all'][ $plugin_file ] );
				continue;
			} elseif ( is_multisite() && is_network_only_plugin( $plugin_file ) && !current_user_can( 'manage_network_plugins' ) ) {
				$plugins['network'][ $plugin_file ] = $plugin_data;
			} elseif ( is_plugin_active( $plugin_file ) ) {
				$plugins['active'][ $plugin_file ] = $plugin_data;
			} else {
				if ( !is_network_admin() && isset( $recently_activated[ $plugin_file ] ) ) // Was the plugin recently activated?
					$plugins['recently_activated'][ $plugin_file ] = $plugin_data;
				$plugins['inactive'][ $plugin_file ] = $plugin_data;
			}

			if ( isset( $current->response[ $plugin_file ] ) )
				$plugins['upgrade'][ $plugin_file ] = $plugin_data;
		}

		if ( !current_user_can( 'update_plugins' ) )
			$plugins['upgrade'] = array();

		if ( $s ) {
			function _search_plugins_filter_callback( $plugin ) {
				static $term;
				if ( is_null( $term ) )
					$term = stripslashes( $_REQUEST['s'] );

				foreach ( $plugin as $value )
					if ( stripos( $value, $term ) !== false )
						return true;

				return false;
			}
			$status = 'search';
			$plugins['search'] = array_filter( $plugins['all'], '_search_plugins_filter_callback' );
		}

		$totals = array();
		foreach ( $plugins as $type => $list )
			$totals[ $type ] = count( $list );

		if ( empty( $plugins[ $status ] ) && !in_array( $status, array( 'all', 'search' ) ) )
			$status = 'all';

		$this->items = $plugins[ $status ];
		$total_this_page = $totals[ $status ];

		if ( $orderby ) {
			$orderby = ucfirst( $orderby );
			$order = strtoupper( $order );

			function _order_plugins_callback( $plugin_a, $plugin_b ) {
				global $orderby, $order;

				$a = $plugin_a[$orderby];
				$b = $plugin_b[$orderby];

				if ( $a == $b )
					return 0;

				if ( 'DESC' == $order )
					return ( $a < $b ) ? 1 : -1;
				else
					return ( $a < $b ) ? -1 : 1;
			}
			uasort( $this->items, '_order_plugins_callback' );
		}
		
		$plugins_per_page = $this->get_items_per_page( 'plugins_per_page', 999 );

		$start = ( $page - 1 ) * $plugins_per_page;

		if ( $total_this_page > $plugins_per_page )
			$this->items = array_slice( $this->items, $start, $plugins_per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $plugins_per_page,
		) );
	}

	function no_items() {
		global $plugins;

		if ( !empty( $plugins['all'] ) )
			_e( 'No plugins found.' );
		else
			_e( 'You do not appear to have any plugins available at this time.' );
	}

	function get_columns() {
		global $status;

		return array(
			'cb'          => !in_array( $status, array( 'mustuse', 'dropins' ) ) ? '<input type="checkbox" />' : '',
			'name'        => __( 'Plugin' ),
			'description' => __( 'Description' ),
		);
	}

	function get_sortable_columns() {
		return array(
			'name'         => 'name',
			'description'  => 'description',
		);
	}

	function display_tablenav( $which ) {
		global $status;

		if ( !in_array( $status, array( 'mustuse', 'dropins' ) ) )
			parent::display_tablenav( $which );
	}

	function get_views() {
		global $totals, $status;
	
		$status_links = array();
		foreach ( $totals as $type => $count ) {
			if ( !$count )
				continue;

			switch ( $type ) {
				case 'all':
					$text = _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'plugins' );
					break;
				case 'active':
					$text = _n( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', $count );
					break;
				case 'recently_activated':
					$text = _n( 'Recently Active <span class="count">(%s)</span>', 'Recently Active <span class="count">(%s)</span>', $count );
					break;
				case 'inactive':
					$text = _n( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', $count );
					break;
				case 'network':
					$text = _n( 'Network <span class="count">(%s)</span>', 'Network <span class="count">(%s)</span>', $count );
					break;
				case 'mustuse':
					$text = _n( 'Must-Use <span class="count">(%s)</span>', 'Must-Use <span class="count">(%s)</span>', $count );
					break;
				case 'dropins':
					$text = _n( 'Drop-ins <span class="count">(%s)</span>', 'Drop-ins <span class="count">(%s)</span>', $count );
					break;
				case 'upgrade':
					$text = _n( 'Upgrade Available <span class="count">(%s)</span>', 'Upgrade Available <span class="count">(%s)</span>', $count );
					break;
				case 'search':
					$text = _n( 'Search Results <span class="count">(%s)</span>', 'Search Results <span class="count">(%s)</span>', $count );
					break;
			}

			$status_links[$type] = sprintf( "<li><a href='%s' %s>%s</a>", 
				add_query_arg('plugin_status', $type, 'plugins.php'),
				( $type == $status ) ? ' class="current"' : '',
				sprintf( $text, number_format_i18n( $count ) )
			);
		}

		return $status_links;
	}

	function get_bulk_actions() {
		global $status;

		$actions = array();
		if ( 'active' != $status )
			$actions['activate-selected'] = __( 'Activate' );
		if ( is_multisite() && 'network' != $status )
			$actions['network-activate-selected'] = __( 'Network Activate' );
		if ( 'inactive' != $status && 'recent' != $status )
			$actions['deactivate-selected'] = __( 'Deactivate' );
		if ( current_user_can( 'update_plugins' ) )
			$actions['update-selected'] = __( 'Update' );
		if ( current_user_can( 'delete_plugins' ) && ( 'active' != $status ) )
			$actions['delete-selected'] = __( 'Delete' );

		return $actions;
	}

	function bulk_actions( $which ) {
		global $status;

		if ( in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		parent::bulk_actions( $which );
	}

	function extra_tablenav( $which ) {
		global $status;

		if ( 'recently_activated' == $status ) { ?>
			<div class="alignleft actions">
				<input type="submit" name="clear-recent-list" value="<?php esc_attr_e( 'Clear List' ) ?>" class="button-secondary" />
			</div>
		<?php }
	}

	function current_action() {
		if ( isset($_POST['clear-recent-list']) )
			return 'clear-recent-list';

		return parent::current_action();
	}

	function display_rows() {
		global $status, $page, $s;

		$context = $status;

		foreach ( $this->items as $plugin_file => $plugin_data ) {
			// preorder
			$actions = array(
				'network_deactivate' => '', 'deactivate' => '',
				'network_only' => '', 'activate' => '',
				'network_activate' => '',
				'edit' => '',
				'delete' => '',
			);

			if ( 'mustuse' == $context ) {
				if ( is_multisite() && !is_network_admin() )
					continue;
				$is_active = true;
			} elseif ( 'dropins' == $context ) {
				if ( is_multisite() && !is_network_admin() )
					continue;
				$dropins = _get_dropins();
				$plugin_name = $plugin_file;
				if ( $plugin_file != $plugin_data['Name'] )
					$plugin_name .= '<br/>' . $plugin_data['Name'];
				if ( true === ( $dropins[ $plugin_file ][1] ) ) { // Doesn't require a constant
					$is_active = true;
					$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
				} elseif ( constant( $dropins[ $plugin_file ][1] ) ) { // Constant is true
					$is_active = true;
					$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
				} else {
					$is_active = false;
					$description = '<p><strong>' . $dropins[ $plugin_file ][0] . ' <span class="attention">' . __('Inactive:') . '</span></strong> ' . sprintf( __( 'Requires <code>%s</code> in <code>wp-config.php</code>.' ), "define('" . $dropins[ $plugin_file ][1] . "', true);" ) . '</p>';
				}
				if ( $plugin_data['Description'] )
					$description .= '<p>' . $plugin_data['Description'] . '</p>';
			} else {
				$is_active_for_network = is_plugin_active_for_network($plugin_file);
				if ( is_network_admin() )
					$is_active = $is_active_for_network;
				else
					$is_active = is_plugin_active( $plugin_file );

				if ( $is_active_for_network && !is_super_admin() && !is_network_admin() )
					continue;

				if ( is_network_admin() ) {
					if ( $is_active_for_network ) {
						if ( current_user_can( 'manage_network_plugins' ) )
							$actions['network_deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;networkwide=1&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin') . '">' . __('Network Deactivate') . '</a>';
					} else {
						if ( current_user_can( 'manage_network_plugins' ) )
							$actions['network_activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin for all sites in this network') . '" class="edit">' . __('Network Activate') . '</a>';
						if ( current_user_can('delete_plugins') )
							$actions['delete'] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins') . '" title="' . __('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';
					}
				} else {
					if ( $is_active ) {
						$actions['deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin') . '">' . __('Deactivate') . '</a>';
					} else {
						if ( is_network_only_plugin( $plugin_file ) && !is_network_admin() )
							continue;

						$actions['activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin') . '" class="edit">' . __('Activate') . '</a>';

						if ( current_user_can('delete_plugins') )
							$actions['delete'] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins') . '" title="' . __('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';
					} // end if $is_active
				 } // end if is_network_admin()

				if ( current_user_can('edit_plugins') && is_writable(WP_PLUGIN_DIR . '/' . $plugin_file) )
					$actions['edit'] = '<a href="plugin-editor.php?file=' . $plugin_file . '" title="' . __('Open this file in the Plugin Editor') . '" class="edit">' . __('Edit') . '</a>';
			} // end if $context

			$actions = apply_filters( 'plugin_action_links', array_filter( $actions ), $plugin_file, $plugin_data, $context );
			$actions = apply_filters( "plugin_action_links_$plugin_file", $actions, $plugin_file, $plugin_data, $context );

			$class = $is_active ? 'active' : 'inactive';
			$checkbox = in_array( $status, array( 'mustuse', 'dropins' ) ) ? '' : "<input type='checkbox' name='checked[]' value='" . esc_attr( $plugin_file ) . "' />";
			if ( 'dropins' != $status ) {
				$description = '<p>' . $plugin_data['Description'] . '</p>';
				$plugin_name = $plugin_data['Name'];
			}
			echo "
		<tr class='$class'>
			<th scope='row' class='check-column'>$checkbox</th>
			<td class='plugin-title'><strong>$plugin_name</strong></td>
			<td class='desc'>$description</td>
		</tr>
		<tr class='$class second'>
			<td></td>
			<td class='plugin-title'>";
			echo '<div class="row-actions-visible">';
			foreach ( $actions as $action => $link ) {
				$sep = end( $actions ) == $link ? '' : ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
			echo "</div></td>
			<td class='desc'>";
			$plugin_meta = array();
			if ( !empty( $plugin_data['Version'] ) )
				$plugin_meta[] = sprintf( __( 'Version %s' ), $plugin_data['Version'] );
			if ( !empty( $plugin_data['Author'] ) ) {
				$author = $plugin_data['Author'];
				if ( !empty( $plugin_data['AuthorURI'] ) )
					$author = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';
				$plugin_meta[] = sprintf( __( 'By %s' ), $author );
			}
			if ( ! empty( $plugin_data['PluginURI'] ) )
				$plugin_meta[] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin site' ) . '">' . __( 'Visit plugin site' ) . '</a>';

			$plugin_meta = apply_filters( 'plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $status );
			echo implode( ' | ', $plugin_meta );
			echo "</td>
		</tr>\n";

			do_action( 'after_plugin_row', $plugin_file, $plugin_data, $status );
			do_action( "after_plugin_row_$plugin_file", $plugin_file, $plugin_data, $status );
		}
	}
}

class WP_Plugin_Install_Table extends WP_List_Table {

	function WP_Plugin_Install_Table() {
		parent::WP_List_Table( array(
			'screen' => 'plugin-install',
		) );
	}

	function check_permissions() {
		if ( ! current_user_can('install_plugins') )
			wp_die(__('You do not have sufficient permissions to install plugins on this site.'));		
	}

	function prepare_items() {
		include( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		global $tabs, $tab, $paged, $type, $term;

		wp_reset_vars( array( 'tab' ) );

		$paged = $this->get_pagenum();

		$per_page = 30;

		// These are the tabs which are shown on the page
		$tabs = array();
		$tabs['dashboard'] = __( 'Search' );
		if ( 'search' == $tab )
			$tabs['search']	= __( 'Search Results' );
		$tabs['upload'] = __( 'Upload' );
		$tabs['featured'] = _x( 'Featured','Plugin Installer' );
		$tabs['popular']  = _x( 'Popular','Plugin Installer' );
		$tabs['new']      = _x( 'Newest','Plugin Installer' );
		$tabs['updated']  = _x( 'Recently Updated','Plugin Installer' );

		$nonmenu_tabs = array( 'plugin-information' ); //Valid actions to perform which do not have a Menu item.

		$tabs = apply_filters( 'install_plugins_tabs', $tabs );
		$nonmenu_tabs = apply_filters( 'install_plugins_nonmenu_tabs', $nonmenu_tabs );

		// If a non-valid menu tab has been selected, And its not a non-menu action.
		if ( empty( $tab ) || ( !isset( $tabs[ $tab ] ) && !in_array( $tab, (array) $nonmenu_tabs ) ) )
			$tab = key( $tabs );

		$args = array( 'page' => $paged, 'per_page' => $per_page );

		switch ( $tab ) {
			case 'search':
				$type = isset( $_REQUEST['type'] ) ? stripslashes( $_REQUEST['type'] ) : '';
				$term = isset( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : '';

				switch ( $type ) {
					case 'tag':
						$args['tag'] = sanitize_title_with_dashes( $term );
						break;
					case 'term':
						$args['search'] = $term;
						break;
					case 'author':
						$args['author'] = $term;
						break;
				}

				add_action( 'install_plugins_table_header', 'install_search_form' );
				break;

			case 'featured':
			case 'popular':
			case 'new':
			case 'updated':
				$args['browse'] = $tab;
				break;

			default:
				$args = false;
		}

		if ( !$args )
			return;

		$api = plugins_api( 'query_plugins', $args );

		if ( is_wp_error( $api ) )
			wp_die( $api->get_error_message() . '</p> <p class="hide-if-no-js"><a href="#" onclick="document.location.reload(); return false;">' . __( 'Try again' ) . '</a>' );

		$this->items = $api->plugins;

		$this->set_pagination_args( array(
			'total_items' => $api->info['results'],
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		_e( 'No plugins match your request.' );
	}

	function get_views() {
		global $tabs, $tab;

		$display_tabs = array();
		foreach ( (array) $tabs as $action => $text ) {
			$class = ( $action == $tab ) ? ' class="current"' : '';
			$href = admin_url('plugin-install.php?tab=' . $action);
			$display_tabs[$action] = "<a href='$href'$class>$text</a>";
		}

		return $display_tabs;
	}

	function display_tablenav( $which ) {
		if ( 'top' ==  $which ) { ?>
			<div class="tablenav">
				<div class="alignleft actions">
					<?php do_action( 'install_plugins_table_header' ); ?>
				</div>
				<?php $this->pagination( $which ); ?>
				<br class="clear" />
			</div>
		<?php } else { ?>
			<div class="tablenav">
				<?php $this->pagination( $which ); ?>
				<br class="clear" />
			</div>
		<?php
		}
	}

	function get_table_classes() {
		extract( $this->_args );

		return array( 'widefat', $plural );
	}

	function get_columns() {
		return array(
			'name'        => __( 'Name' ),
			'version'     => __( 'Version' ),
			'rating'      => __( 'Rating' ),
			'description' => __( 'Description' ),
		);
	}

	function display_rows() {
		$plugins_allowedtags = array(
			'a' => array( 'href' => array(),'title' => array(), 'target' => array() ),
			'abbr' => array( 'title' => array() ),'acronym' => array( 'title' => array() ),
			'code' => array(), 'pre' => array(), 'em' => array(),'strong' => array(),
			'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'br' => array()
		);

		foreach ( (array) $this->items as $plugin ) {
			if ( is_object( $plugin ) )
				$plugin = (array) $plugin;

			$title = wp_kses( $plugin['name'], $plugins_allowedtags );
			//Limit description to 400char, and remove any HTML.
			$description = strip_tags( $plugin['description'] );
			if ( strlen( $description ) > 400 )
				$description = mb_substr( $description, 0, 400 ) . '&#8230;';
			//remove any trailing entities
			$description = preg_replace( '/&[^;\s]{0,6}$/', '', $description );
			//strip leading/trailing & multiple consecutive lines
			$description = trim( $description );
			$description = preg_replace( "|(\r?\n)+|", "\n", $description );
			//\n => <br>
			$description = nl2br( $description );
			$version = wp_kses( $plugin['version'], $plugins_allowedtags );

			$name = strip_tags( $title . ' ' . $version );

			$author = $plugin['author'];
			if ( ! empty( $plugin['author'] ) )
				$author = ' <cite>' . sprintf( __( 'By %s' ), $author ) . '.</cite>';

			$author = wp_kses( $author, $plugins_allowedtags );

			$action_links = array();
			$action_links[] = '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
								'&amp;TB_iframe=true&amp;width=600&amp;height=550' ) . '" class="thickbox" title="' .
								esc_attr( sprintf( __( 'More information about %s' ), $name ) ) . '">' . __( 'Details' ) . '</a>';

			if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
				$status = install_plugin_install_status( $plugin );

				switch ( $status['status'] ) {
					case 'install':
						if ( $status['url'] )
							$action_links[] = '<a class="install-now" href="' . $status['url'] . '" title="' . esc_attr( sprintf( __( 'Install %s' ), $name ) ) . '">' . __( 'Install Now' ) . '</a>';
						break;
					case 'update_available':
						if ( $status['url'] )
							$action_links[] = '<a href="' . $status['url'] . '" title="' . esc_attr( sprintf( __( 'Update to version %s' ), $status['version'] ) ) . '">' . sprintf( __( 'Update Now' ), $status['version'] ) . '</a>';
						break;
					case 'latest_installed':
					case 'newer_installed':
						$action_links[] = '<span title="' . esc_attr__( 'This plugin is already installed and is up to date' ) . ' ">' . __( 'Installed' ) . '</span>';
						break;
				}
			}

			$action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );
		?>
		<tr>
			<td class="name"><strong><?php echo $title; ?></strong>
				<div class="action-links"><?php if ( !empty( $action_links ) ) echo implode( ' | ', $action_links ); ?></div>
			</td>
			<td class="vers"><?php echo $version; ?></td>
			<td class="vers">
				<div class="star-holder" title="<?php printf( _n( '( based on %s rating )', '( based on %s ratings )', $plugin['num_ratings'] ), number_format_i18n( $plugin['num_ratings'] ) ) ?>">
					<div class="star star-rating" style="width: <?php echo esc_attr( $plugin['rating'] ) ?>px"></div>
					<div class="star star5"><img src="<?php echo admin_url( 'images/star.gif' ); ?>" alt="<?php _e( '5 stars' ) ?>" /></div>
					<div class="star star4"><img src="<?php echo admin_url( 'images/star.gif' ); ?>" alt="<?php _e( '4 stars' ) ?>" /></div>
					<div class="star star3"><img src="<?php echo admin_url( 'images/star.gif' ); ?>" alt="<?php _e( '3 stars' ) ?>" /></div>
					<div class="star star2"><img src="<?php echo admin_url( 'images/star.gif' ); ?>" alt="<?php _e( '2 stars' ) ?>" /></div>
					<div class="star star1"><img src="<?php echo admin_url( 'images/star.gif' ); ?>" alt="<?php _e( '1 star' ) ?>" /></div>
				</div>
			</td>
			<td class="desc"><?php echo $description, $author; ?></td>
		</tr>
		<?php
		}
	}
}

class WP_Themes_Table extends WP_List_Table {

	var $search = array();
	var $features = array();

	function WP_Themes_Table() {
		parent::__construct( array(
			'screen' => 'themes',
		) );
	}

	function check_permissions() {
		if ( !current_user_can('switch_themes') && !current_user_can('edit_theme_options') )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
	}
	
	function prepare_items() {
		global $ct;

		$ct = current_theme_info();

		$themes = get_allowed_themes();

		$search = !empty( $_REQUEST['s'] ) ? trim( stripslashes( $_REQUEST['s'] ) ) : '';

		if ( '' !== $this->search ) {
			$this->search = array_merge( $this->search, array_filter( array_map( 'trim', explode( ',', $search ) ) ) );
			$this->search = array_unique( $this->search );
			foreach ( $themes as $key => $theme ) {
				if ( !$this->search_theme( $theme ) )
					unset( $themes[ $key ] );
			}
		}

		unset( $themes[$ct->name] );
		uksort( $themes, "strnatcasecmp" );

		$per_page = 15;
		$page = $this->get_pagenum();

		$start = ( $page - 1 ) * $per_page;

		$this->items = array_slice( $themes, $start, $per_page );

		$this->set_pagination_args( array(
			'query_var' => 'pagenum',
			'total_items' => count( $themes ),
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		if ( current_user_can( 'install_themes' ) )
			printf( __( 'You only have one theme installed right now. Live a little! You can choose from over 1,000 free themes in the WordPress.org Theme Directory at any time: just click on the <em><a href="%s">Install Themes</a></em> tab above.' ), 'theme-install.php' );
		else
			printf( __( 'Only the current theme is available to you. Contact the %s administrator for information about accessing additional themes.' ), get_site_option( 'site_name' ) );
	}

	function display_table() {
?>
		<div class="tablenav">
			<?php $this->pagination( 'top' ); ?>
			<br class="clear" />
		</div>

		<table id="availablethemes" cellspacing="0" cellpadding="0">
			<tbody id="the-list" class="list:themes">
				<?php $this->display_rows(); ?>
			</tbody>
		</table>

		<div class="tablenav">
			<?php $this->pagination( 'bottom' ); ?>
			<br class="clear" />
		</div>
<?php
	}

	function get_columns() {
		return array();
	}

	function display_rows() {
		$themes = $this->items;
		$theme_names = array_keys( $themes );
		natcasesort( $theme_names );

		$table = array();
		$rows = ceil( count( $theme_names ) / 3 );
		for ( $row = 1; $row <= $rows; $row++ )
			for ( $col = 1; $col <= 3; $col++ )
				$table[$row][$col] = array_shift( $theme_names );

		foreach ( $table as $row => $cols ) {
?>
<tr>
<?php
foreach ( $cols as $col => $theme_name ) {
	$class = array( 'available-theme' );
	if ( $row == 1 ) $class[] = 'top';
	if ( $col == 1 ) $class[] = 'left';
	if ( $row == $rows ) $class[] = 'bottom';
	if ( $col == 3 ) $class[] = 'right';
?>
	<td class="<?php echo join( ' ', $class ); ?>">
<?php if ( !empty( $theme_name ) ) :
	$template = $themes[$theme_name]['Template'];
	$stylesheet = $themes[$theme_name]['Stylesheet'];
	$title = $themes[$theme_name]['Title'];
	$version = $themes[$theme_name]['Version'];
	$description = $themes[$theme_name]['Description'];
	$author = $themes[$theme_name]['Author'];
	$screenshot = $themes[$theme_name]['Screenshot'];
	$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
	$template_dir = $themes[$theme_name]['Template Dir'];
	$parent_theme = $themes[$theme_name]['Parent Theme'];
	$theme_root = $themes[$theme_name]['Theme Root'];
	$theme_root_uri = $themes[$theme_name]['Theme Root URI'];
	$preview_link = esc_url( get_option( 'home' ) . '/' );
	if ( is_ssl() )
		$preview_link = str_replace( 'http://', 'https://', $preview_link );
	$preview_link = htmlspecialchars( add_query_arg( array( 'preview' => 1, 'template' => $template, 'stylesheet' => $stylesheet, 'TB_iframe' => 'true' ), $preview_link ) );
	$preview_text = esc_attr( sprintf( __( 'Preview of &#8220;%s&#8221;' ), $title ) );
	$tags = $themes[$theme_name]['Tags'];
	$thickbox_class = 'thickbox thickbox-preview';
	$activate_link = wp_nonce_url( "themes.php?action=activate&amp;template=".urlencode( $template )."&amp;stylesheet=".urlencode( $stylesheet ), 'switch-theme_' . $template );
	$activate_text = esc_attr( sprintf( __( 'Activate &#8220;%s&#8221;' ), $title ) );
	$actions = array();
	$actions[] = '<a href="' . $activate_link .  '" class="activatelink" title="' . $activate_text . '">' . __( 'Activate' ) . '</a>';
	$actions[] = '<a href="' . $preview_link . '" class="thickbox thickbox-preview" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $theme_name ) ) . '">' . __( 'Preview' ) . '</a>';
	if ( current_user_can( 'delete_themes' ) )
		$actions[] = '<a class="submitdelete deletion" href="' . wp_nonce_url( "themes.php?action=delete&amp;template=$stylesheet", 'delete-theme_' . $stylesheet ) . '" onclick="' . "return confirm( '" . esc_js( sprintf( __( "You are about to delete this theme '%s'\n  'Cancel' to stop, 'OK' to delete." ), $theme_name ) ) . "' );" . '">' . __( 'Delete' ) . '</a>';
	$actions = apply_filters( 'theme_action_links', $actions, $themes[$theme_name] );

	$actions = implode ( ' | ', $actions );
?>
		<a href="<?php echo $preview_link; ?>" class="<?php echo $thickbox_class; ?> screenshot">
<?php if ( $screenshot ) : ?>
			<img src="<?php echo $theme_root_uri . '/' . $stylesheet . '/' . $screenshot; ?>" alt="" />
<?php endif; ?>
		</a>
<h3><?php
	/* translators: 1: theme title, 2: theme version, 3: theme author */
	printf( __( '%1$s %2$s by %3$s' ), $title, $version, $author ) ; ?></h3>
<p class="description"><?php echo $description; ?></p>
<span class='action-links'><?php echo $actions ?></span>
	<?php if ( current_user_can( 'edit_themes' ) && $parent_theme ) {
	/* translators: 1: theme title, 2:  template dir, 3: stylesheet_dir, 4: theme title, 5: parent_theme */ ?>
	<p><?php printf( __( 'The template files are located in <code>%2$s</code>. The stylesheet files are located in <code>%3$s</code>. <strong>%4$s</strong> uses templates from <strong>%5$s</strong>. Changes made to the templates will affect both themes.' ), $title, str_replace( WP_CONTENT_DIR, '', $template_dir ), str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ), $title, $parent_theme ); ?></p>
<?php } else { ?>
	<p><?php printf( __( 'All of this theme&#8217;s files are located in <code>%2$s</code>.' ), $title, str_replace( WP_CONTENT_DIR, '', $template_dir ), str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ) ); ?></p>
<?php } ?>
<?php if ( $tags ) : ?>
<p><?php _e( 'Tags:' ); ?> <?php echo join( ', ', $tags ); ?></p>
<?php endif; ?>
		<?php theme_update_available( $themes[$theme_name] ); ?>
<?php endif; // end if not empty theme_name ?>
	</td>
<?php } // end foreach $cols ?>
</tr>
<?php } // end foreach $table
	}

	function search_theme( $theme ) {
		$matched = 0;

		// Match all phrases
		if ( count( $this->search ) > 0 ) {
			foreach ( $this->search as $word ) {
				$matched = 0;

				// In a tag?
				if ( in_array( $word, array_map( 'sanitize_title_with_dashes', $theme['Tags'] ) ) )
					$matched = 1;

				// In one of the fields?
				foreach ( array( 'Name', 'Title', 'Description', 'Author', 'Template', 'Stylesheet' ) AS $field ) {
					if ( stripos( $theme[$field], $word ) !== false )
						$matched++;
				}

				if ( $matched == 0 )
					return false;
			}
		}

		// Now search the features
		if ( count( $this->features ) > 0 ) {
			foreach ( $this->features as $word ) {
				// In a tag?
				if ( !in_array( $word, array_map( 'sanitize_title_with_dashes', $theme['Tags'] ) ) )
					return false;
			}
		}

		// Only get here if each word exists in the tags or one of the fields
		return true;
	}
}

class WP_Theme_Install_Table extends WP_List_Table {

	function WP_Theme_Install_Table() {
		parent::WP_List_Table( array(
			'screen' => 'theme-install',
		) );
	}

	function check_permissions() {
		if ( ! current_user_can('install_themes') )
			wp_die( __( 'You do not have sufficient permissions to install themes on this site.' ) );
	}

	function prepare_items() {
		include( ABSPATH . 'wp-admin/includes/theme-install.php' );

		global $tabs, $tab, $paged, $type, $term, $theme_field_defaults;		
		
		wp_reset_vars( array( 'tab' ) );

		$paged = $this->get_pagenum();

		$per_page = 30;

		// These are the tabs which are shown on the page,
		$tabs = array();
		$tabs['dashboard'] = __( 'Search' );
		if ( 'search' == $tab )
			$tabs['search']	= __( 'Search Results' );
		$tabs['upload'] = __( 'Upload' );
		$tabs['featured'] = _x( 'Featured','Theme Installer' );
		//$tabs['popular']  = _x( 'Popular','Theme Installer' );
		$tabs['new']      = _x( 'Newest','Theme Installer' );
		$tabs['updated']  = _x( 'Recently Updated','Theme Installer' );

		$nonmenu_tabs = array( 'theme-information' ); // Valid actions to perform which do not have a Menu item.

		$tabs = apply_filters( 'install_themes_tabs', $tabs );
		$nonmenu_tabs = apply_filters( 'install_themes_nonmenu_tabs', $nonmenu_tabs );

		// If a non-valid menu tab has been selected, And its not a non-menu action.
		if ( empty( $tab ) || ( ! isset( $tabs[ $tab ] ) && ! in_array( $tab, (array) $nonmenu_tabs ) ) )
			$tab = key( $tabs );

		$args = array( 'page' => $paged, 'per_page' => $per_page, 'fields' => $theme_field_defaults );

		switch ( $tab ) {
			case 'search':
				$type = isset( $_REQUEST['type'] ) ? stripslashes( $_REQUEST['type'] ) : '';
				$term = isset( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : '';

				switch ( $type ) {
					case 'tag':
						$terms = explode( ',', $term );
						$terms = array_map( 'trim', $terms );
						$terms = array_map( 'sanitize_title_with_dashes', $terms );
						$args['tag'] = $terms;
						break;
					case 'term':
						$args['search'] = $term;
						break;
					case 'author':
						$args['author'] = $term;
						break;
				}

				if ( !empty( $_POST['features'] ) ) {
					$terms = $_POST['features'];
					$terms = array_map( 'trim', $terms );
					$terms = array_map( 'sanitize_title_with_dashes', $terms );
					$args['tag'] = $terms;
					$_REQUEST['s'] = implode( ',', $terms );
					$_REQUEST['type'] = 'tag';
				}

				add_action( 'install_themes_table_header', 'install_theme_search_form' );
				break;

			case 'featured':
			//case 'popular':
			case 'new':
			case 'updated':
				$args['browse'] = $tab;
				break;

			default:
				$args = false;
		}

		if ( !$args )
			return;

		$api = themes_api( 'query_themes', $args );

		if ( is_wp_error( $api ) )
			wp_die( $api->get_error_message() . '</p> <p class="hide-if-no-js"><a href="#" onclick="document.location.reload(); return false;">' . __( 'Try again' ) . '</a>' );

		$this->items = $api->themes;

		$this->set_pagination_args( array(
			'total_items' => $api->info['results'],
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		_e( 'No themes match your request.' );
	}

	function get_views() {
		global $tabs, $tab;

		$display_tabs = array();
		foreach ( (array) $tabs as $action => $text ) {
			$class = ( $action == $tab ) ? ' class="current"' : '';
			$href = admin_url('theme-install.php?tab=' . $action);
			$display_tabs[$action] = "<a href='$href'$class>$text</a>";
		}

		return $display_tabs;
	}

	function get_columns() {
		return array();
	}

	function display_table() {
?>
		<div class="tablenav">
			<div class="alignleft actions">
				<?php do_action( 'install_themes_table_header' ); ?>
			</div>
			<?php $this->pagination( 'top' ); ?>
			<br class="clear" />
		</div>

		<table id="availablethemes" cellspacing="0" cellpadding="0">
			<tbody id="the-list" class="list:themes">
				<?php $this->display_rows(); ?>
			</tbody>
		</table>

		<div class="tablenav">
			<?php $this->pagination( 'bottom' ); ?>
			<br class="clear" />
		</div>
<?php
	}

	function display_rows() {
		$themes = $this->items;

		$rows = ceil( count( $themes ) / 3 );
		$table = array();
		$theme_keys = array_keys( $themes );
		for ( $row = 1; $row <= $rows; $row++ )
			for ( $col = 1; $col <= 3; $col++ )
				$table[$row][$col] = array_shift( $theme_keys );

		foreach ( $table as $row => $cols ) {
			echo "\t<tr>\n";
			foreach ( $cols as $col => $theme_index ) {
				$class = array( 'available-theme' );
				if ( $row == 1 ) $class[] = 'top';
				if ( $col == 1 ) $class[] = 'left';
				if ( $row == $rows ) $class[] = 'bottom';
				if ( $col == 3 ) $class[] = 'right';
				?>
				<td class="<?php echo join( ' ', $class ); ?>"><?php
					if ( isset( $themes[$theme_index] ) )
						display_theme( $themes[$theme_index] );
				?></td>
			<?php } // end foreach $cols
			echo "\t</tr>\n";
		} // end foreach $table
	}
}

