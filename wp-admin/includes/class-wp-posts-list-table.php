<?php
/**
 * Posts List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_Posts_List_Table extends WP_List_Table {

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
	 * @var int
	 * @access protected
	 */
	var $comment_pending_count;

	/**
	 * Holds the number of posts for this user
	 *
	 * @since 3.1.0
	 * @var int
	 * @access private
	 */
	var $user_posts_count;

	/**
	 * Holds the number of posts which are sticky.
	 *
	 * @since 3.1.0
	 * @var int
	 * @access private
	 */
	var $sticky_posts_count = 0;

	function __construct( $args = array() ) {
		global $post_type_object, $wpdb;

		parent::__construct( array(
			'plural' => 'posts',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );

		$post_type = $this->screen->post_type;
		$post_type_object = get_post_type_object( $post_type );

		if ( !current_user_can( $post_type_object->cap->edit_others_posts ) ) {
			$this->user_posts_count = $wpdb->get_var( $wpdb->prepare( "
				SELECT COUNT( 1 ) FROM $wpdb->posts
				WHERE post_type = %s AND post_status NOT IN ( 'trash', 'auto-draft' )
				AND post_author = %d
			", $post_type, get_current_user_id() ) );

			if ( $this->user_posts_count && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['all_posts'] ) && empty( $_REQUEST['author'] ) && empty( $_REQUEST['show_sticky'] ) )
				$_GET['author'] = get_current_user_id();
		}

		if ( 'post' == $post_type && $sticky_posts = get_option( 'sticky_posts' ) ) {
			$sticky_posts = implode( ', ', array_map( 'absint', (array) $sticky_posts ) );
			$this->sticky_posts_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( 1 ) FROM $wpdb->posts WHERE post_type = %s AND post_status != 'trash' AND ID IN ($sticky_posts)", $post_type ) );
		}
	}

	function ajax_user_can() {
		return current_user_can( get_post_type_object( $this->screen->post_type )->cap->edit_posts );
	}

	function prepare_items() {
		global $avail_post_stati, $wp_query, $per_page, $mode;

		$avail_post_stati = wp_edit_posts_query();

		$this->hierarchical_display = ( is_post_type_hierarchical( $this->screen->post_type ) && 'menu_order title' == $wp_query->query['orderby'] );

		$total_items = $this->hierarchical_display ? $wp_query->post_count : $wp_query->found_posts;

		$post_type = $this->screen->post_type;
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
		if ( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] )
			echo get_post_type_object( $this->screen->post_type )->labels->not_found_in_trash;
		else
			echo get_post_type_object( $this->screen->post_type )->labels->not_found;
	}

	function get_views() {
		global $locked_post_status, $avail_post_stati;

		$post_type = $this->screen->post_type;

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
			$status_links['mine'] = "<a href='edit.php?post_type=$post_type&author=$current_user_id'$class>" . sprintf( _nx( 'Mine <span class="count">(%s)</span>', 'Mine <span class="count">(%s)</span>', $this->user_posts_count, 'posts' ), number_format_i18n( $this->user_posts_count ) ) . '</a>';
			$allposts = '&all_posts=1';
		}

		$total_posts = array_sum( (array) $num_posts );

		// Subtract post types that are not included in the admin all list.
		foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
			$total_posts -= $num_posts->$state;

		$class = empty( $class ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';
		$status_links['all'] = "<a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( !in_array( $status_name, $avail_post_stati ) )
				continue;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='edit.php?post_status=$status_name&amp;post_type=$post_type'$class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}

		if ( ! empty( $this->sticky_posts_count ) ) {
			$class = ! empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';

			$sticky_link = array( 'sticky' => "<a href='edit.php?post_type=$post_type&amp;show_sticky=1'$class>" . sprintf( _nx( 'Sticky <span class="count">(%s)</span>', 'Sticky <span class="count">(%s)</span>', $this->sticky_posts_count, 'posts' ), number_format_i18n( $this->sticky_posts_count ) ) . '</a>' );

			// Sticky comes after Publish, or if not listed, after All.
			$split = 1 + array_search( ( isset( $status_links['publish'] ) ? 'publish' : 'all' ), array_keys( $status_links ) );
			$status_links = array_merge( array_slice( $status_links, 0, $split ), $sticky_link, array_slice( $status_links, $split ) );
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
		global $cat;
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which && !is_singular() ) {

			$this->months_dropdown( $this->screen->post_type );

			if ( is_object_in_taxonomy( $this->screen->post_type, 'category' ) ) {
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
			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
		}

		if ( $this->is_trash && current_user_can( get_post_type_object( $this->screen->post_type )->cap->edit_others_posts ) ) {
			submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
		}
?>
		</div>
<?php
	}

	function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) )
			return 'delete_all';

		return parent::current_action();
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which && ! is_post_type_hierarchical( $this->screen->post_type ) )
			$this->view_switcher( $mode );
	}

	function get_table_classes() {
		return array( 'widefat', 'fixed', is_post_type_hierarchical( $this->screen->post_type ) ? 'pages' : 'posts' );
	}

	function get_columns() {
		$post_type = $this->screen->post_type;

		$posts_columns = array();

		$posts_columns['cb'] = '<input type="checkbox" />';

		/* translators: manage posts column name */
		$posts_columns['title'] = _x( 'Title', 'column name' );

		if ( post_type_supports( $post_type, 'author' ) )
			$posts_columns['author'] = __( 'Author' );

		$taxonomies = array();

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name' );

		$taxonomies = apply_filters( "manage_taxonomies_for_{$post_type}_columns", $taxonomies, $post_type );
		$taxonomies = array_filter( $taxonomies, 'taxonomy_exists' );

		foreach ( $taxonomies as $taxonomy ) {
			if ( 'category' == $taxonomy )
				$column_key = 'categories';
			elseif ( 'post_tag' == $taxonomy )
				$column_key = 'tags';
			else
				$column_key = 'taxonomy-' . $taxonomy;

			$posts_columns[ $column_key ] = get_taxonomy( $taxonomy )->labels->name;
		}

		$post_status = !empty( $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : 'all';
		if ( post_type_supports( $post_type, 'comments' ) && !in_array( $post_status, array( 'pending', 'draft', 'future' ) ) )
			$posts_columns['comments'] = '<span class="vers"><div title="' . esc_attr__( 'Comments' ) . '" class="comment-grey-bubble"></div></span>';

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
			'parent'   => 'parent',
			'comments' => 'comment_count',
			'date'     => array( 'date', true )
		);
	}

	function display_rows( $posts = array(), $level = 0 ) {
		global $wp_query, $per_page;

		if ( empty( $posts ) )
			$posts = $wp_query->posts;

		add_filter( 'the_title', 'esc_html' );

		if ( $this->hierarchical_display ) {
			$this->_display_rows_hierarchical( $posts, $this->get_pagenum(), $per_page );
		} else {
			$this->_display_rows( $posts, $level );
		}
	}

	function _display_rows( $posts, $level = 0 ) {
		global $mode;

		// Create array of post IDs.
		$post_ids = array();

		foreach ( $posts as $a_post )
			$post_ids[] = $a_post->ID;

		$this->comment_pending_count = get_pending_comments_num( $post_ids );

		foreach ( $posts as $post )
			$this->single_row( $post, $level );
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
					clean_post_cache( $page );
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
	 * @since 3.1.0 (Standalone function exists since 2.6.0)
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

	function single_row( $post, $level = 0 ) {
		global $mode;
		static $alternate;

		$global_post = get_post();
		$GLOBALS['post'] = $post;
		setup_postdata( $post );

		$edit_link = get_edit_post_link( $post->ID );
		$title = _draft_or_post_title();
		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );

		$alternate = 'alternate' == $alternate ? '' : 'alternate';
		$classes = $alternate . ' iedit author-' . ( get_current_user_id() == $post->post_author ? 'self' : 'other' );
	?>
		<tr id="post-<?php echo $post->ID; ?>" class="<?php echo implode( ' ', get_post_class( $classes, $post->ID ) ); ?>" valign="top">
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
			<th scope="row" class="check-column">
				<?php if ( $can_edit_post ) { ?>
				<label class="screen-reader-text" for="cb-select-<?php the_ID(); ?>"><?php printf( __( 'Select %s' ), $title ); ?></label>
				<input id="cb-select-<?php the_ID(); ?>" type="checkbox" name="post[]" value="<?php the_ID(); ?>" />
				<?php } ?>
			</th>
			<?php
			break;

			case 'title':
				if ( $this->hierarchical_display ) {
					$attributes = 'class="post-title page-title column-title"' . $style;

					if ( 0 == $level && (int) $post->post_parent > 0 ) {
						//sent level 0 by accident, by default, or because we don't know the actual level
						$find_main_page = (int) $post->post_parent;
						while ( $find_main_page > 0 ) {
							$parent = get_post( $find_main_page );

							if ( is_null( $parent ) )
								break;

							$level++;
							$find_main_page = (int) $parent->post_parent;

							if ( !isset( $parent_name ) )
								$parent_name = apply_filters( 'the_title', $parent->post_title, $parent->ID );
						}
					}

					$pad = str_repeat( '&#8212; ', $level );
?>
			<td <?php echo $attributes ?>><strong><?php if ( $can_edit_post && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ); ?>"><?php echo $pad; echo $title ?></a><?php } else { echo $pad; echo $title; }; _post_states( $post ); echo isset( $parent_name ) ? ' | ' . $post_type_object->labels->parent_item_colon . ' ' . esc_html( $parent_name ) : ''; ?></strong>
<?php
				}
				else {
					$attributes = 'class="post-title page-title column-title"' . $style;

					$pad = str_repeat( '&#8212; ', $level );
?>
			<td <?php echo $attributes ?>><strong><?php if ( $can_edit_post && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ); ?>"><?php echo $pad; echo $title ?></a><?php } else { echo $pad; echo $title; }; _post_states( $post ); ?></strong>
<?php
					if ( 'excerpt' == $mode && current_user_can( 'read_post', $post->ID ) )
						the_excerpt();
				}

				$actions = array();
				if ( $can_edit_post && 'trash' != $post->post_status ) {
					$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
					$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline' ) ) . '">' . __( 'Quick&nbsp;Edit' ) . '</a>';
				}
				if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
					if ( 'trash' == $post->post_status )
						$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
					elseif ( EMPTY_TRASH_DAYS )
						$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
					if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
						$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
				}
				if ( $post_type_object->public ) {
					if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
						if ( $can_edit_post )
							$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
					} elseif ( 'trash' != $post->post_status ) {
						$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
					}
				}

				$actions = apply_filters( is_post_type_hierarchical( $post->post_type ) ? 'page_row_actions' : 'post_row_actions', $actions, $post );
				echo $this->row_actions( $actions );

				get_inline_data( $post );
				echo '</td>';
			break;

			case 'date':
				if ( '0000-00-00 00:00:00' == $post->post_date ) {
					$t_time = $h_time = __( 'Unpublished' );
					$time_diff = 0;
				} else {
					$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
					$m_time = $post->post_date;
					$time = get_post_time( 'G', true, $post );

					$time_diff = time() - $time;

					if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS )
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
					esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'author' => get_the_author_meta( 'ID' ) ), 'edit.php' )),
					get_the_author()
				);
			?></td>
			<?php
			break;

			default:
				if ( 'categories' == $column_name )
					$taxonomy = 'category';
				elseif ( 'tags' == $column_name )
					$taxonomy = 'post_tag';
				elseif ( 0 === strpos( $column_name, 'taxonomy-' ) )
					$taxonomy = substr( $column_name, 9 );
				else
					$taxonomy = false;

				if ( $taxonomy ) {
					$taxonomy_object = get_taxonomy( $taxonomy );
					echo '<td ' . $attributes . '>';
					if ( $terms = get_the_terms( $post->ID, $taxonomy ) ) {
						$out = array();
						foreach ( $terms as $t ) {
							$posts_in_term_qv = array();
							if ( 'post' != $post->post_type )
								$posts_in_term_qv['post_type'] = $post->post_type;
							if ( $taxonomy_object->query_var ) {
								$posts_in_term_qv[ $taxonomy_object->query_var ] = $t->slug;
							} else {
								$posts_in_term_qv['taxonomy'] = $taxonomy;
								$posts_in_term_qv['term'] = $t->slug;
							}

							$out[] = sprintf( '<a href="%s">%s</a>',
								esc_url( add_query_arg( $posts_in_term_qv, 'edit.php' ) ),
								esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) )
							);
						}
						/* translators: used between list items, there is a space after the comma */
						echo join( __( ', ' ), $out );
					} else {
						echo '&#8212;';
					}
					echo '</td>';
					break;
				}
			?>
			<td <?php echo $attributes ?>><?php
				if ( is_post_type_hierarchical( $post->post_type ) )
					do_action( 'manage_pages_custom_column', $column_name, $post->ID );
				else
					do_action( 'manage_posts_custom_column', $column_name, $post->ID );
				do_action( "manage_{$post->post_type}_posts_custom_column", $column_name, $post->ID );
			?></td>
			<?php
			break;
			}
		}
	?>
		</tr>
	<?php
		$GLOBALS['post'] = $global_post;
	}

	/**
	 * Outputs the hidden row displayed when inline editing
	 *
	 * @since 3.1.0
	 */
	function inline_edit() {
		global $mode;

		$screen = $this->screen;

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

		$m = ( isset( $mode ) && 'excerpt' == $mode ) ? 'excerpt' : 'list';
		$can_publish = current_user_can( $post_type_object->cap->publish_posts );
		$core_columns = array( 'cb' => true, 'date' => true, 'title' => true, 'categories' => true, 'tags' => true, 'comments' => true, 'author' => true );

	?>

	<form method="get" action=""><table style="display: none"><tbody id="inlineedit">
		<?php
		$hclass = count( $hierarchical_taxonomies ) ? 'post' : 'page';
		$bulk = 0;
		while ( $bulk < 2 ) { ?>

		<tr id="<?php echo $bulk ? 'bulk-edit' : 'inline-edit'; ?>" class="inline-edit-row inline-edit-row-<?php echo "$hclass inline-edit-" . $screen->post_type;
			echo $bulk ? " bulk-edit-row bulk-edit-row-$hclass bulk-edit-{$screen->post_type}" : " quick-edit-row quick-edit-row-$hclass inline-edit-{$screen->post_type}";
		?>" style="display: none"><td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">

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
				<?php touch_time( 1, 1, 0, 1 ); ?>
			</div>
			<br class="clear" />
	<?php endif; // $bulk

		if ( post_type_supports( $screen->post_type, 'author' ) ) :
			$authors_dropdown = '';

			if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) :
				$users_opt = array(
					'hide_if_only_one_author' => false,
					'who' => 'authors',
					'name' => 'post_author',
					'class'=> 'authors',
					'multi' => 1,
					'echo' => 0
				);
				if ( $bulk )
					$users_opt['show_option_none'] = __( '&mdash; No Change &mdash;' );

				if ( $authors = wp_dropdown_users( $users_opt ) ) :
					$authors_dropdown  = '<label class="inline-edit-author">';
					$authors_dropdown .= '<span class="title">' . __( 'Author' ) . '</span>';
					$authors_dropdown .= $authors;
					$authors_dropdown .= '</label>';
				endif;
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

		if ( post_type_supports( $screen->post_type, 'page-attributes' ) ) :

			if ( $post_type_object->hierarchical ) :
		?>
			<label>
				<span class="title"><?php _e( 'Parent' ); ?></span>
	<?php
		$dropdown_args = array(
			'post_type'         => $post_type_object->name,
			'selected'          => $post->post_parent,
			'name'              => 'post_parent',
			'show_option_none'  => __( 'Main Page (no parent)' ),
			'option_none_value' => 0,
			'sort_column'       => 'menu_order, post_title',
		);

		if ( $bulk )
			$dropdown_args['show_option_no_change'] =  __( '&mdash; No Change &mdash;' );
		$dropdown_args = apply_filters( 'quick_edit_dropdown_pages_args', $dropdown_args );
		wp_dropdown_pages( $dropdown_args );
	?>
			</label>

	<?php
			endif; // hierarchical

			if ( !$bulk ) : ?>

			<label>
				<span class="title"><?php _e( 'Order' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="menu_order" class="inline-edit-menu-order-input" value="<?php echo $post->menu_order ?>" /></span>
			</label>

	<?php	endif; // !$bulk

			if ( 'page' == $screen->post_type ) :
	?>

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
			endif; // page post_type
		endif; // page-attributes
	?>

	<?php if ( count( $flat_taxonomies ) && !$bulk ) : ?>

	<?php foreach ( $flat_taxonomies as $taxonomy ) : ?>
		<?php if ( current_user_can( $taxonomy->cap->assign_terms ) ) : ?>
			<label class="inline-edit-tags">
				<span class="title"><?php echo esc_html( $taxonomy->labels->name ) ?></span>
				<textarea cols="22" rows="1" name="tax_input[<?php echo esc_attr( $taxonomy->name )?>]" class="tax_input_<?php echo esc_attr( $taxonomy->name )?>"></textarea>
			</label>
		<?php endif; ?>

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

	<?php if ( 'post' == $screen->post_type && $can_publish && current_user_can( $post_type_object->cap->edit_others_posts ) ) : ?>

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
					<span class="checkbox-title"><?php _e( 'Make this post sticky' ); ?></span>
				</label>

	<?php	endif; // $bulk ?>

	<?php endif; // 'post' && $can_publish && current_user_can( 'edit_others_cap' ) ?>

			</div>

	<?php if ( post_type_supports( $screen->post_type, 'post-formats' ) && current_theme_supports( 'post-formats' ) ) :
		$post_formats = get_theme_support( 'post-formats' );
		if ( isset( $post_formats[0] ) && is_array( $post_formats[0] ) ) :
			$all_post_formats = get_post_format_strings();
			unset( $all_post_formats['standard'] ); ?>
			<div class="inline-edit-group">
				<label class="alignleft" for="post_format">
				<span class="title"><?php _ex( 'Format', 'post format' ); ?></span>
				<select name="post_format">
				<?php if ( $bulk ) : ?>
					<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
				<?php endif; ?>
					<option value="0"><?php _ex( 'Standard', 'Post format' ); ?></option>
				<?php foreach ( $all_post_formats as $slug => $format ) :
					$unsupported = ! in_array( $slug, $post_formats[0] );
					if ( $bulk && $unsupported )
						continue;
					?>
					<option value="<?php echo esc_attr( $slug ); ?>"<?php if ( $unsupported ) echo ' class="unsupported"'; ?>><?php echo esc_html( $format ); ?></option>
				<?php endforeach; ?>
				</select></label>
			</div>
		<?php endif; ?>
	<?php endif; // post-formats ?>

		</div></fieldset>

	<?php
		list( $columns ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			if ( isset( $core_columns[$column_name] ) )
				continue;
			do_action( $bulk ? 'bulk_edit_custom_box' : 'quick_edit_custom_box', $column_name, $screen->post_type );
		}
	?>
		<p class="submit inline-edit-save">
			<a accesskey="c" href="#inline-edit" title="<?php esc_attr_e( 'Cancel' ); ?>" class="button-secondary cancel alignleft"><?php _e( 'Cancel' ); ?></a>
			<?php if ( ! $bulk ) {
				wp_nonce_field( 'inlineeditnonce', '_inline_edit', false );
				$update_text = __( 'Update' );
				?>
				<a accesskey="s" href="#inline-edit" title="<?php esc_attr_e( 'Update' ); ?>" class="button-primary save alignright"><?php echo esc_attr( $update_text ); ?></a>
				<span class="spinner"></span>
			<?php } else {
				submit_button( __( 'Update' ), 'button-primary alignright', 'bulk_edit', false, array( 'accesskey' => 's' ) );
			} ?>
			<input type="hidden" name="post_view" value="<?php echo esc_attr( $m ); ?>" />
			<input type="hidden" name="screen" value="<?php echo esc_attr( $screen->id ); ?>" />
			<span class="error" style="display:none"></span>
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
