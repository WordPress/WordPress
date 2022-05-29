<?php
/**
 * Edit Comments Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';
if ( ! current_user_can( 'edit_posts' ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to edit comments.' ) . '</p>',
		403
	);
}

$wp_list_table = _get_list_table( 'WP_Comments_List_Table' );
$pagenum       = $wp_list_table->get_pagenum();

$doaction = $wp_list_table->current_action();

if ( $doaction ) {
	check_admin_referer( 'bulk-comments' );

	if ( 'delete_all' === $doaction && ! empty( $_REQUEST['pagegen_timestamp'] ) ) {
		/**
		 * @global wpdb $wpdb WordPress database abstraction object.
		 */
		global $wpdb;

		$comment_status = wp_unslash( $_REQUEST['comment_status'] );
		$delete_time    = wp_unslash( $_REQUEST['pagegen_timestamp'] );
		$comment_ids    = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = %s AND %s > comment_date_gmt", $comment_status, $delete_time ) );
		$doaction       = 'delete';
	} elseif ( isset( $_REQUEST['delete_comments'] ) ) {
		$comment_ids = $_REQUEST['delete_comments'];
		$doaction    = $_REQUEST['action'];
	} elseif ( isset( $_REQUEST['ids'] ) ) {
		$comment_ids = array_map( 'absint', explode( ',', $_REQUEST['ids'] ) );
	} elseif ( wp_get_referer() ) {
		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	$approved   = 0;
	$unapproved = 0;
	$spammed    = 0;
	$unspammed  = 0;
	$trashed    = 0;
	$untrashed  = 0;
	$deleted    = 0;

	$redirect_to = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'spammed', 'unspammed', 'approved', 'unapproved', 'ids' ), wp_get_referer() );
	$redirect_to = add_query_arg( 'paged', $pagenum, $redirect_to );

	wp_defer_comment_counting( true );

	foreach ( $comment_ids as $comment_id ) { // Check the permissions on each.
		if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
			continue;
		}

		switch ( $doaction ) {
			case 'approve':
				wp_set_comment_status( $comment_id, 'approve' );
				$approved++;
				break;
			case 'unapprove':
				wp_set_comment_status( $comment_id, 'hold' );
				$unapproved++;
				break;
			case 'spam':
				wp_spam_comment( $comment_id );
				$spammed++;
				break;
			case 'unspam':
				wp_unspam_comment( $comment_id );
				$unspammed++;
				break;
			case 'trash':
				wp_trash_comment( $comment_id );
				$trashed++;
				break;
			case 'untrash':
				wp_untrash_comment( $comment_id );
				$untrashed++;
				break;
			case 'delete':
				wp_delete_comment( $comment_id );
				$deleted++;
				break;
		}
	}

	if ( ! in_array( $doaction, array( 'approve', 'unapprove', 'spam', 'unspam', 'trash', 'delete' ), true ) ) {
		$screen = get_current_screen()->id;

		/** This action is documented in wp-admin/edit.php */
		$redirect_to = apply_filters( "handle_bulk_actions-{$screen}", $redirect_to, $doaction, $comment_ids ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	}

	wp_defer_comment_counting( false );

	if ( $approved ) {
		$redirect_to = add_query_arg( 'approved', $approved, $redirect_to );
	}
	if ( $unapproved ) {
		$redirect_to = add_query_arg( 'unapproved', $unapproved, $redirect_to );
	}
	if ( $spammed ) {
		$redirect_to = add_query_arg( 'spammed', $spammed, $redirect_to );
	}
	if ( $unspammed ) {
		$redirect_to = add_query_arg( 'unspammed', $unspammed, $redirect_to );
	}
	if ( $trashed ) {
		$redirect_to = add_query_arg( 'trashed', $trashed, $redirect_to );
	}
	if ( $untrashed ) {
		$redirect_to = add_query_arg( 'untrashed', $untrashed, $redirect_to );
	}
	if ( $deleted ) {
		$redirect_to = add_query_arg( 'deleted', $deleted, $redirect_to );
	}
	if ( $trashed || $spammed ) {
		$redirect_to = add_query_arg( 'ids', implode( ',', $comment_ids ), $redirect_to );
	}

	wp_safe_redirect( $redirect_to );
	exit;
} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
	wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
	exit;
}

$wp_list_table->prepare_items();

wp_enqueue_script( 'admin-comments' );
enqueue_comment_hotkeys_js();

/**
 * @global int $post_id
 */
global $post_id;

if ( $post_id ) {
	$comments_count      = wp_count_comments( $post_id );
	$draft_or_post_title = wp_html_excerpt( _draft_or_post_title( $post_id ), 50, '&hellip;' );

	if ( $comments_count->moderated > 0 ) {
		// Used in the HTML title tag.
		$title = sprintf(
			/* translators: 1: Comments count, 2: Post title. */
			__( 'Comments (%1$s) on &#8220;%2$s&#8221;' ),
			number_format_i18n( $comments_count->moderated ),
			$draft_or_post_title
		);
	} else {
		// Used in the HTML title tag.
		$title = sprintf(
			/* translators: %s: Post title. */
			__( 'Comments on &#8220;%s&#8221;' ),
			$draft_or_post_title
		);
	}
} else {
	$comments_count = wp_count_comments();

	if ( $comments_count->moderated > 0 ) {
		// Used in the HTML title tag.
		$title = sprintf(
			/* translators: %s: Comments count. */
			__( 'Comments (%s)' ),
			number_format_i18n( $comments_count->moderated )
		);
	} else {
		// Used in the HTML title tag.
		$title = __( 'Comments' );
	}
}

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
				'<p>' . __( 'You can manage comments made on your site similar to the way you manage posts and other content. This screen is customizable in the same ways as other management screens, and you can act on comments using the on-hover action links or the bulk actions.' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'moderating-comments',
		'title'   => __( 'Moderating Comments' ),
		'content' =>
					'<p>' . __( 'A red bar on the left means the comment is waiting for you to moderate it.' ) . '</p>' .
					'<p>' . __( 'In the <strong>Author</strong> column, in addition to the author&#8217;s name, email address, and blog URL, the commenter&#8217;s IP address is shown. Clicking on this link will show you all the comments made from this IP address.' ) . '</p>' .
					'<p>' . __( 'In the <strong>Comment</strong> column, hovering over any comment gives you options to approve, reply (and approve), quick edit, edit, spam mark, or trash that comment.' ) . '</p>' .
					'<p>' . __( 'In the <strong>In response to</strong> column, there are three elements. The text is the name of the post that inspired the comment, and links to the post editor for that entry. The View Post link leads to that post on your live site. The small bubble with the number in it shows the number of approved comments that post has received. If there are pending comments, a red notification circle with the number of pending comments is displayed. Clicking the notification circle will filter the comments screen to show only pending comments on that post.' ) . '</p>' .
					'<p>' . __( 'In the <strong>Submitted on</strong> column, the date and time the comment was left on your site appears. Clicking on the date/time link will take you to that comment on your live site.' ) . '</p>' .
					'<p>' . __( 'Many people take advantage of keyboard shortcuts to moderate their comments more quickly. Use the link to the side to learn more.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/comments-screen/">Documentation on Comments</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/comment-spam/">Documentation on Comment Spam</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/keyboard-shortcuts/">Documentation on Keyboard Shortcuts</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( 'Filter comments list' ),
		'heading_pagination' => __( 'Comments list navigation' ),
		'heading_list'       => __( 'Comments list' ),
	)
);

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wrap">
<h1 class="wp-heading-inline">
<?php
if ( $post_id ) {
	printf(
		/* translators: %s: Link to post. */
		__( 'Comments on &#8220;%s&#8221;' ),
		sprintf(
			'<a href="%1$s">%2$s</a>',
			get_edit_post_link( $post_id ),
			wp_html_excerpt( _draft_or_post_title( $post_id ), 50, '&hellip;' )
		)
	);
} else {
	_e( 'Comments' );
}
?>
</h1>

<?php
if ( $post_id ) {
	$post_type_object = get_post_type_object( get_post_type( $post_id ) );

	if ( $post_type_object ) {
		printf(
			'<a href="%1$s" class="comments-view-item-link">%2$s</a>',
			get_permalink( $post_id ),
			$post_type_object->labels->view_item
		);
	}
}

if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( 'Search results for: %s' ),
		'<strong>' . esc_html( wp_unslash( $_REQUEST['s'] ) ) . '</strong>'
	);
	echo '</span>';
}
?>

<hr class="wp-header-end">

<?php
if ( isset( $_REQUEST['error'] ) ) {
	$error     = (int) $_REQUEST['error'];
	$error_msg = '';
	switch ( $error ) {
		case 1:
			$error_msg = __( 'Invalid comment ID.' );
			break;
		case 2:
			$error_msg = __( 'Sorry, you are not allowed to edit comments on this post.' );
			break;
	}
	if ( $error_msg ) {
		echo '<div id="moderated" class="error"><p>' . $error_msg . '</p></div>';
	}
}

if ( isset( $_REQUEST['approved'] ) || isset( $_REQUEST['deleted'] ) || isset( $_REQUEST['trashed'] ) || isset( $_REQUEST['untrashed'] ) || isset( $_REQUEST['spammed'] ) || isset( $_REQUEST['unspammed'] ) || isset( $_REQUEST['same'] ) ) {
	$approved  = isset( $_REQUEST['approved'] ) ? (int) $_REQUEST['approved'] : 0;
	$deleted   = isset( $_REQUEST['deleted'] ) ? (int) $_REQUEST['deleted'] : 0;
	$trashed   = isset( $_REQUEST['trashed'] ) ? (int) $_REQUEST['trashed'] : 0;
	$untrashed = isset( $_REQUEST['untrashed'] ) ? (int) $_REQUEST['untrashed'] : 0;
	$spammed   = isset( $_REQUEST['spammed'] ) ? (int) $_REQUEST['spammed'] : 0;
	$unspammed = isset( $_REQUEST['unspammed'] ) ? (int) $_REQUEST['unspammed'] : 0;
	$same      = isset( $_REQUEST['same'] ) ? (int) $_REQUEST['same'] : 0;

	if ( $approved > 0 || $deleted > 0 || $trashed > 0 || $untrashed > 0 || $spammed > 0 || $unspammed > 0 || $same > 0 ) {
		if ( $approved > 0 ) {
			/* translators: %s: Number of comments. */
			$messages[] = sprintf( _n( '%s comment approved.', '%s comments approved.', $approved ), $approved );
		}

		if ( $spammed > 0 ) {
			$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
			/* translators: %s: Number of comments. */
			$messages[] = sprintf( _n( '%s comment marked as spam.', '%s comments marked as spam.', $spammed ), $spammed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=unspam&ids=$ids", 'bulk-comments' ) ) . '">' . __( 'Undo' ) . '</a><br />';
		}

		if ( $unspammed > 0 ) {
			/* translators: %s: Number of comments. */
			$messages[] = sprintf( _n( '%s comment restored from the spam.', '%s comments restored from the spam.', $unspammed ), $unspammed );
		}

		if ( $trashed > 0 ) {
			$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
			/* translators: %s: Number of comments. */
			$messages[] = sprintf( _n( '%s comment moved to the Trash.', '%s comments moved to the Trash.', $trashed ), $trashed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=untrash&ids=$ids", 'bulk-comments' ) ) . '">' . __( 'Undo' ) . '</a><br />';
		}

		if ( $untrashed > 0 ) {
			/* translators: %s: Number of comments. */
			$messages[] = sprintf( _n( '%s comment restored from the Trash.', '%s comments restored from the Trash.', $untrashed ), $untrashed );
		}

		if ( $deleted > 0 ) {
			/* translators: %s: Number of comments. */
			$messages[] = sprintf( _n( '%s comment permanently deleted.', '%s comments permanently deleted.', $deleted ), $deleted );
		}

		if ( $same > 0 ) {
			$comment = get_comment( $same );
			if ( $comment ) {
				switch ( $comment->comment_approved ) {
					case '1':
						$messages[] = __( 'This comment is already approved.' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( 'Edit comment' ) . '</a>';
						break;
					case 'trash':
						$messages[] = __( 'This comment is already in the Trash.' ) . ' <a href="' . esc_url( admin_url( 'edit-comments.php?comment_status=trash' ) ) . '"> ' . __( 'View Trash' ) . '</a>';
						break;
					case 'spam':
						$messages[] = __( 'This comment is already marked as spam.' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( 'Edit comment' ) . '</a>';
						break;
				}
			}
		}

		echo '<div id="moderated" class="updated notice is-dismissible"><p>' . implode( "<br/>\n", $messages ) . '</p></div>';
	}
}
?>

<?php $wp_list_table->views(); ?>

<form id="comments-form" method="get">

<?php $wp_list_table->search_box( __( 'Search Comments' ), 'comment' ); ?>

<?php if ( $post_id ) : ?>
<input type="hidden" name="p" value="<?php echo esc_attr( (int) $post_id ); ?>" />
<?php endif; ?>
<input type="hidden" name="comment_status" value="<?php echo esc_attr( $comment_status ); ?>" />
<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr( current_time( 'mysql', 1 ) ); ?>" />

<input type="hidden" name="_total" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg( 'total_items' ) ); ?>" />
<input type="hidden" name="_per_page" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg( 'per_page' ) ); ?>" />
<input type="hidden" name="_page" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg( 'page' ) ); ?>" />

<?php if ( isset( $_REQUEST['paged'] ) ) { ?>
	<input type="hidden" name="paged" value="<?php echo esc_attr( absint( $_REQUEST['paged'] ) ); ?>" />
<?php } ?>

<?php $wp_list_table->display(); ?>
</form>
</div>

<div id="ajax-response"></div>

<?php
wp_comment_reply( '-1', true, 'detail' );
wp_comment_trashnotice();
require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
