<?php
/**
 * Media Library List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_Media_List_Table extends WP_List_Table {

	function __construct( $args = array() ) {
		$this->detached = isset( $_REQUEST['detached'] ) || isset( $_REQUEST['find_detached'] );

		parent::__construct( array(
			'plural' => 'media',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
	}

	function ajax_user_can() {
		return current_user_can('upload_files');
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
		global $wpdb, $post_mime_types, $avail_post_mime_types;

		$type_links = array();
		$_num_posts = (array) wp_count_attachments();
		$_total_posts = array_sum($_num_posts) - $_num_posts['trash'];
		$total_orphans = $wpdb->get_var( "SELECT COUNT( * ) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1" );
		$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
		foreach ( $matches as $type => $reals )
			foreach ( $reals as $real )
				$num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];

		$class = ( empty($_GET['post_mime_type']) && !$this->detached && !isset($_GET['status']) ) ? ' class="current"' : '';
		$type_links['all'] = "<a href='upload.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $_total_posts, 'uploaded files' ), number_format_i18n( $_total_posts ) ) . '</a>';
		foreach ( $post_mime_types as $mime_type => $label ) {
			$class = '';

			if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
				continue;

			if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
				$class = ' class="current"';
			if ( !empty( $num_posts[$mime_type] ) )
				$type_links[$mime_type] = "<a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), number_format_i18n( $num_posts[$mime_type] )) . '</a>';
		}
		$type_links['detached'] = '<a href="upload.php?detached=1"' . ( $this->detached ? ' class="current"' : '' ) . '>' . sprintf( _nx( 'Unattached <span class="count">(%s)</span>', 'Unattached <span class="count">(%s)</span>', $total_orphans, 'detached files' ), number_format_i18n( $total_orphans ) ) . '</a>';

		if ( !empty($_num_posts['trash']) )
			$type_links['trash'] = '<a href="upload.php?status=trash"' . ( (isset($_GET['status']) && $_GET['status'] == 'trash' ) ? ' class="current"' : '') . '>' . sprintf( _nx( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', $_num_posts['trash'], 'uploaded files' ), number_format_i18n( $_num_posts['trash'] ) ) . '</a>';

		return $type_links;
	}

	function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete Permanently' );
		if ( $this->detached )
			$actions['attach'] = __( 'Attach to a post' );

		return $actions;
	}

	function extra_tablenav( $which ) {
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which && !is_singular() && !$this->detached && !$this->is_trash ) {
			$this->months_dropdown( 'attachment' );

			do_action( 'restrict_manage_posts' );
			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
		}

		if ( $this->detached ) {
			submit_button( __( 'Scan for lost attachments' ), 'secondary', 'find_detached', false );
		} elseif ( $this->is_trash && current_user_can( 'edit_others_posts' ) ) {
			submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
		} ?>
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

		$taxonomies = array();

		$taxonomies = get_taxonomies_for_attachments( 'objects' );
		$taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name' );

		$taxonomies = apply_filters( 'manage_taxonomies_for_attachment_columns', $taxonomies, 'attachment' );
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

		/* translators: column name */
		if ( !$this->detached ) {
			$posts_columns['parent'] = _x( 'Uploaded to', 'column name' );
			if ( post_type_supports( 'attachment', 'comments' ) )
				$posts_columns['comments'] = '<span class="vers"><div title="' . esc_attr__( 'Comments' ) . '" class="comment-grey-bubble"></div></span>';
		}
		/* translators: column name */
		$posts_columns['date'] = _x( 'Date', 'column name' );
		$posts_columns = apply_filters( 'manage_media_columns', $posts_columns, $this->detached );

		return $posts_columns;
	}

	function get_sortable_columns() {
		return array(
			'title'    => 'title',
			'author'   => 'author',
			'parent'   => 'parent',
			'comments' => 'comment_count',
			'date'     => array( 'date', true ),
		);
	}

	function display_rows() {
		global $post;

		add_filter( 'the_title','esc_html' );
		$alt = '';

		while ( have_posts() ) : the_post();
			$user_can_edit = current_user_can( 'edit_post', $post->ID );

			if ( $this->is_trash && $post->post_status != 'trash'
			||  !$this->is_trash && $post->post_status == 'trash' )
				continue;

			$alt = ( 'alternate' == $alt ) ? '' : 'alternate';
			$post_owner = ( get_current_user_id() == $post->post_author ) ? 'self' : 'other';
			$att_title = _draft_or_post_title();
?>
	<tr id='post-<?php echo $post->ID; ?>' class='<?php echo trim( $alt . ' author-' . $post_owner . ' status-' . $post->post_status ); ?>' valign="top">
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
		<th scope="row" class="check-column">
			<?php if ( $user_can_edit ) { ?>
				<label class="screen-reader-text" for="cb-select-<?php the_ID(); ?>"><?php echo sprintf( __( 'Select %s' ), $att_title );?></label>
				<input type="checkbox" name="media[]" id="cb-select-<?php the_ID(); ?>" value="<?php the_ID(); ?>" />
			<?php } ?>
		</th>
<?php
		break;

	case 'icon':
		$attributes = 'class="column-icon media-icon"' . $style;
?>
		<td <?php echo $attributes ?>><?php
			if ( $thumb = wp_get_attachment_image( $post->ID, array( 80, 60 ), true ) ) {
				if ( $this->is_trash || ! $user_can_edit ) {
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
		<td <?php echo $attributes ?>><strong>
			<?php if ( $this->is_trash || ! $user_can_edit ) {
				echo $att_title;
			} else { ?>
			<a href="<?php echo get_edit_post_link( $post->ID, true ); ?>"
				title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ); ?>">
				<?php echo $att_title; ?></a>
			<?php };
			_media_states( $post ); ?></strong>
			<p>
<?php
			if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $post->ID ), $matches ) )
				echo esc_html( strtoupper( $matches[1] ) );
			else
				echo strtoupper( str_replace( 'image/', '', get_post_mime_type() ) );
?>
			</p>
<?php
		echo $this->row_actions( $this->_get_row_actions( $post, $att_title ) );
?>
		</td>
<?php
		break;

	case 'author':
?>
		<td <?php echo $attributes ?>><?php
			printf( '<a href="%s">%s</a>',
				esc_url( add_query_arg( array( 'author' => get_the_author_meta('ID') ), 'upload.php' ) ),
				get_the_author()
			);
		?></td>
<?php
		break;

	case 'desc':
?>
		<td <?php echo $attributes ?>><?php echo has_excerpt() ? $post->post_excerpt : ''; ?></td>
<?php
		break;

	case 'date':
		if ( '0000-00-00 00:00:00' == $post->post_date ) {
			$h_time = __( 'Unpublished' );
		} else {
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post, false );
			if ( ( abs( $t_diff = time() - $time ) ) < DAY_IN_SECONDS ) {
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
		if ( $post->post_parent > 0 && get_post( $post->post_parent ) ) {
			$title = _draft_or_post_title( $post->post_parent );
?>
			<td <?php echo $attributes ?>><strong>
				<?php if ( current_user_can( 'edit_post', $post->post_parent ) ) { ?>
					<a href="<?php echo get_edit_post_link( $post->post_parent ); ?>">
						<?php echo $title ?></a><?php
				} else {
					echo $title;
				} ?></strong>,
				<?php echo get_the_time( __( 'Y/m/d' ) ); ?>
			</td>
<?php
		} else {
?>
			<td <?php echo $attributes ?>><?php _e( '(Unattached)' ); ?><br />
			<?php if ( $user_can_edit ) { ?>
				<a class="hide-if-no-js"
					onclick="findPosts.open( 'media[]','<?php echo $post->ID ?>' ); return false;"
					href="#the-list">
					<?php _e( 'Attach' ); ?></a>
			<?php } ?></td>
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
					$posts_in_term_qv['taxonomy'] = $taxonomy;
					$posts_in_term_qv['term'] = $t->slug;

					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( $posts_in_term_qv, 'upload.php' ) ),
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
		<td <?php echo $attributes ?>>
			<?php do_action( 'manage_media_custom_column', $column_name, $post->ID ); ?>
		</td>
<?php
		break;
	}
}
?>
	</tr>
<?php endwhile;
	}

	function _get_row_actions( $post, $att_title ) {
		$actions = array();

		if ( $this->detached ) {
			if ( current_user_can( 'edit_post', $post->ID ) )
				$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '">' . __( 'Edit' ) . '</a>';
			if ( current_user_can( 'delete_post', $post->ID ) )
				if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
					$actions['trash'] = "<a class='submitdelete' href='" . wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-post_' . $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
				} else {
					$delete_ays = !MEDIA_TRASH ? " onclick='return showNotice.warn();'" : '';
					$actions['delete'] = "<a class='submitdelete'$delete_ays href='" . wp_nonce_url( "post.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID ) . "'>" . __( 'Delete Permanently' ) . "</a>";
				}
			$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $att_title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
			if ( current_user_can( 'edit_post', $post->ID ) )
				$actions['attach'] = '<a href="#the-list" onclick="findPosts.open( \'media[]\',\''.$post->ID.'\' );return false;" class="hide-if-no-js">'.__( 'Attach' ).'</a>';
		}
		else {
			if ( current_user_can( 'edit_post', $post->ID ) && !$this->is_trash )
				$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '">' . __( 'Edit' ) . '</a>';
			if ( current_user_can( 'delete_post', $post->ID ) ) {
				if ( $this->is_trash )
					$actions['untrash'] = "<a class='submitdelete' href='" . wp_nonce_url( "post.php?action=untrash&amp;post=$post->ID", 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
				elseif ( EMPTY_TRASH_DAYS && MEDIA_TRASH )
					$actions['trash'] = "<a class='submitdelete' href='" . wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-post_' . $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
				if ( $this->is_trash || !EMPTY_TRASH_DAYS || !MEDIA_TRASH ) {
					$delete_ays = ( !$this->is_trash && !MEDIA_TRASH ) ? " onclick='return showNotice.warn();'" : '';
					$actions['delete'] = "<a class='submitdelete'$delete_ays href='" . wp_nonce_url( "post.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID ) . "'>" . __( 'Delete Permanently' ) . "</a>";
				}
			}
			if ( !$this->is_trash ) {
				$title =_draft_or_post_title( $post->post_parent );
				$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
			}
		}

		$actions = apply_filters( 'media_row_actions', $actions, $post, $this->detached );

		return $actions;
	}
}
