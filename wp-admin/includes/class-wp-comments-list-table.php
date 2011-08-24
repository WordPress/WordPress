<?php
/**
 * Comments and Post Comments List Table classes.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Comments List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_Comments_List_Table extends WP_List_Table {

	var $checkbox = true;

	var $pending_count = array();

	function __construct() {
		global $post_id;

		$post_id = isset( $_REQUEST['p'] ) ? absint( $_REQUEST['p'] ) : 0;

		if ( get_option('show_avatars') )
			add_filter( 'comment_author', 'floated_admin_avatar' );

		parent::__construct( array(
			'plural' => 'comments',
			'singular' => 'comment',
			'ajax' => true,
		) );
	}

	function ajax_user_can() {
		return current_user_can('edit_posts');
	}

	function prepare_items() {
		global $post_id, $comment_status, $search, $comment_type;

		$comment_status = isset( $_REQUEST['comment_status'] ) ? $_REQUEST['comment_status'] : 'all';
		if ( !in_array( $comment_status, array( 'all', 'moderated', 'approved', 'spam', 'trash' ) ) )
			$comment_status = 'all';

		$comment_type = !empty( $_REQUEST['comment_type'] ) ? $_REQUEST['comment_type'] : '';

		$search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : '';

		$user_id = ( isset( $_REQUEST['user_id'] ) ) ? $_REQUEST['user_id'] : '';

		$orderby = ( isset( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : '';
		$order = ( isset( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : '';

		$comments_per_page = $this->get_per_page( $comment_status );

		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( isset( $_REQUEST['number'] ) ) {
			$number = (int) $_REQUEST['number'];
		}
		else {
			$number = $comments_per_page + min( 8, $comments_per_page ); // Grab a few extra
		}

		$page = $this->get_pagenum();

		if ( isset( $_REQUEST['start'] ) ) {
			$start = $_REQUEST['start'];
		} else {
			$start = ( $page - 1 ) * $comments_per_page;
		}

		if ( $doing_ajax && isset( $_REQUEST['offset'] ) ) {
			$start += $_REQUEST['offset'];
		}

		$status_map = array(
			'moderated' => 'hold',
			'approved' => 'approve'
		);

		$args = array(
			'status' => isset( $status_map[$comment_status] ) ? $status_map[$comment_status] : $comment_status,
			'search' => $search,
			'user_id' => $user_id,
			'offset' => $start,
			'number' => $number,
			'post_id' => $post_id,
			'type' => $comment_type,
			'orderby' => $orderby,
			'order' => $order,
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

		$_comment_post_ids = array_unique( $_comment_post_ids );

		$this->pending_count = get_pending_comments_num( $_comment_post_ids );

		$this->set_pagination_args( array(
			'total_items' => $total_comments,
			'per_page' => $comments_per_page,
		) );
	}

	function get_per_page( $comment_status = 'all' ) {
		$comments_per_page = $this->get_items_per_page( 'edit_comments_per_page' );
		$comments_per_page = apply_filters( 'comments_per_page', $comments_per_page, $comment_status );
		return $comments_per_page;
	}

	function no_items() {
		global $comment_status;

		if ( 'moderated' == $comment_status )
			_e( 'No comments awaiting moderation&hellip; yet.' );
		else
			_e( 'No comments found.' );
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
			$status_links[$status] = "<a href='$link'$class>" . sprintf(
				translate_nooped_plural( $label, $num_comments->$status ),
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
<?php
			submit_button( __( 'Filter' ), 'secondary', false, false, array( 'id' => 'post-query-submit' ) );
		}

		if ( ( 'spam' == $comment_status || 'trash' == $comment_status ) && current_user_can( 'moderate_comments' ) ) {
			wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );
			$title = ( 'spam' == $comment_status ) ? esc_attr__( 'Empty Spam' ) : esc_attr__( 'Empty Trash' );
			submit_button( $title, 'button-secondary apply', 'delete_all', false );
		}
		do_action( 'manage_comments_nav', $comment_status );
		echo '</div>';
	}

	function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) )
			return 'delete_all';

		return parent::current_action();
	}

	function get_columns() {
		global $post_id;

		$columns = array();

		if ( $this->checkbox )
			$columns['cb'] = '<input type="checkbox" />';

		$columns['author'] = __( 'Author' );
		$columns['comment'] = _x( 'Comment', 'column name' );

		if ( !$post_id )
			$columns['response'] = _x( 'In Response To', 'column name' );

		return $columns;
	}

	function get_sortable_columns() {
		return array(
			'author'   => 'comment_author',
			'response' => 'comment_post_ID'
		);
	}

	function display() {
		extract( $this->_args );

		wp_nonce_field( "fetch-list-" . get_class( $this ), '_ajax_fetch_list_nonce' );

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
		<?php $this->display_rows_or_placeholder(); ?>
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
		$the_comment_class = join( ' ', get_comment_class( wp_get_comment_status( $comment->comment_ID ) ) );

		$post = get_post( $comment->comment_post_ID );

		$this->user_can = current_user_can( 'edit_comment', $comment->comment_ID );

		echo "<tr id='comment-$comment->comment_ID' class='$the_comment_class'>";
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

		echo '<div class="submitted-on">';
		/* translators: 2: comment date, 3: comment time */
		printf( __( 'Submitted on <a href="%1$s">%2$s at %3$s</a>' ), $comment_url,
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
		<textarea class="comment" rows="1" cols="1"><?php echo esc_textarea( apply_filters( 'comment_edit_pre', $comment->comment_content ) ); ?></textarea>
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
				if ( ( 'reply' == $action || 'quickedit' == $action ) && ! defined('DOING_AJAX') )
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

/**
 * Post Comments List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 *
 * @see WP_Comments_Table
 */
class WP_Post_Comments_List_Table extends WP_Comments_List_Table {

	function get_column_info() {
		$this->_column_headers = array(
			array(
			'author'   => __( 'Author' ),
			'comment'  => _x( 'Comment', 'column name' ),
			),
			array(),
			array(),
		);

		return $this->_column_headers;
	}

	function get_table_classes() {
		$classes = parent::get_table_classes();
		$classes[] = 'comments-box';
		return $classes;
	}

	function display( $output_empty = false ) {
		extract( $this->_args );

		wp_nonce_field( "fetch-list-" . get_class( $this ), '_ajax_fetch_list_nonce' );
?>
<table class="<?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0" style="display:none;">
	<tbody id="the-comment-list"<?php if ( $singular ) echo " class='list:$singular'"; ?>>
		<?php if ( ! $output_empty ) $this->display_rows_or_placeholder(); ?>
	</tbody>
</table>
<?php
	}

	function get_per_page( $comment_status = false ) {
		return 10;
	}
}

?>
