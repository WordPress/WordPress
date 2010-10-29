<?php
/**
 * Media Library List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
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
				$type_links[$mime_type] = "<li><a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), number_format_i18n( $num_posts[$mime_type] )) . '</a>';
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
		$post_type_obj = get_post_type_object( $post_type );
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which && !is_singular() && !$detached && !$this->is_trash ) {
			$this->months_dropdown( $post_type );

			do_action( 'restrict_manage_posts' );
			submit_button( __( 'Filter' ), 'secondary', 'post-query-submit', false );
		}

		if ( $detached ) {
			submit_button( __( 'Scan for lost attachments' ), 'secondary', 'find_detached', false );
		} elseif ( $this->is_trash && current_user_can( 'edit_others_posts' ) ) { 
			submit_button( __( 'Empty Trash' ), 'button-secondary apply', 'delete_all', false );
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

?>