<?php

require_once './admin.php';

$title = __( 'Moderate Comments' );
$parent_file = 'edit-comments.php';

wp_enqueue_script( 'admin-comments' );

wp_reset_vars( array( 'action', 'item_ignored', 'item_deleted', 'item_approved', 'item_spam', 'feelinglucky' ) );

$comment = array();

if ( isset( $_POST['comment'] ) && is_array( $_POST['comment'] ) ) {
	foreach ( $_POST['comment'] as $k => $v ) {
		$comment[intval( $k )] = $v;
	}
}

if ( $action == 'update' ) {
	check_admin_referer( 'moderate-comments' );

	if ( !current_user_can( 'moderate_comments' ) ) {
		wp_die( __( 'Your level is not high enough to moderate comments.' ) );
	}

	$item_ignored = 0;
	$item_deleted = 0;
	$item_approved = 0;
	$item_spam = 0;

	foreach ( $comment as $k => $v ) {
		if ( $feelinglucky && $v == 'later' ) {
			$v = 'delete';
		}

		switch ( $v ) {
			case 'later' :
				$item_ignored++;
			break;

			case 'delete' :
				wp_set_comment_status( $k, 'delete' );
				$item_deleted++;
			break;

			case 'spam' :
				wp_set_comment_status( $k, 'spam' );
				$item_spam++;
			break;

			case 'approve' :
				wp_set_comment_status( $k, 'approve' );

				if ( get_option( 'comments_notify' ) == true ) {
					wp_notify_postauthor( $k );
				}

				$item_approved++;
			break;
		}
	}

	wp_redirect( basename( __FILE__ ) . '?ignored=' . $item_ignored . '&deleted=' . $item_deleted . '&approved=' . $item_approved . '&spam=' . $item_spam );
	exit;
}

require_once './admin-header.php';

if ( !current_user_can( 'moderate_comments' ) ) {
	echo '<div class="wrap"><p>' . __( 'Your level is not high enough to moderate comments.' ) . '</p></div>';
	include_once './admin-footer.php';
	exit;
}

if ( isset( $_GET['approved'] ) || isset( $_GET['deleted'] ) || isset( $_GET['spam'] ) ) {
	$approved = isset( $_GET['approved'] ) ? (int) $_GET['approved'] : 0;
	$deleted = isset( $_GET['deleted'] ) ? (int) $_GET['deleted'] : 0;
	$spam = isset( $_GET['ignored'] ) ? (int) $_GET['spam'] : 0;

	if ( $approved > 0 || $deleted > 0 || $spam > 0 ) {
		echo '<div id="moderated" class="updated fade"><p>';

		if ( $approved > 0 ) {
			printf( __ngettext( '%s comment approved.', '%s comments approved.', $approved ), $approved );
			echo '<br />';
		}

		if ( $deleted > 0 ) {
			printf( __ngettext( '%s comment deleted', '%s comments deleted.', $deleted ), $deleted );
			echo '<br />';
		}

		if ( $spam > 0 ) {
			printf( __ngettext( '%s comment marked as spam', '%s comments marked as spam', $spam ), $spam );
			echo '<br />';
		}

		echo '</p></div>';
	}
}

?>
<div class="wrap">
<?php

$comments = $wpdb->get_results( "SELECT * FROM $wpdb->comments WHERE comment_approved = '0'" );

if ( !$comments ) {
	echo '<p>' . __( 'Currently there are no comments for you to moderate.' ) . '</p></div>';
	include_once './admin-footer.php';
	exit;
}

$total = count( $comments );
$per = 100;

if ( isset( $_GET['paged'] ) ) {
	$page = (int) $_GET['paged'];
} else {
	$page = 1;
}

$start = ( $page * $per ) - $per;
$stop = $start + $per;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'total' => ceil( $total / $per ),
	'current' => $page,
	'prev_text' => '&laquo;',
	'next_text' => '&raquo;'
) );

$comments = array_slice( $comments, $start, $stop );

?>
	<h2><?php _e( 'Moderation Queue' ); ?></h2>

	<?php
		if ( $page_links ) {
			echo '<p class="pagenav">' . $page_links . '</p>';
		}
	?>

	<form name="approval" id="approval" action="<?php echo basename( __FILE__ ); ?>" method="post">
		<?php wp_nonce_field( 'moderate-comments' ); ?>
		<input type="hidden" name="action" value="update" />
		<ol id="the-comments-list" class="commentlist">
	<?php
		$i = 0;

		foreach ( $comments as $comment ) {
			$class = 'js-unapproved';

			if ( $i++ % 2 ) {
				$class .= ' alternate';
			}
		?>
			<li id="comment-<?php comment_ID(); ?>" class="<?php echo $class; ?>">
				<p>
					<strong><?php comment_author(); ?></strong>
					<?php if ( !empty( $comment->comment_author_email ) ) { ?>| <?php comment_author_email_link(); ?> <?php } ?>
					<?php if ( !empty( $comment->comment_author_url ) && $comment->comment_author_url != 'http://' ) { ?>| <?php comment_author_url_link(); ?> <?php } ?>
					| <?php _e( 'IP:' ); ?> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP(); ?>"><?php comment_author_IP(); ?></a>
				</p>

				<p>
					<?php comment_text(); ?>
				</p>

				<p><small>
					<?php comment_date( __( 'M j, g:i A' ) ); ?> &#8212;
					[ <a href="comment.php?action=editcomment&amp;c=<?php comment_ID(); ?>" title="<?php _e( 'Edit this comment' ); ?>"><?php _e( 'Edit' ); ?></a> |
					<a href="post.php?action=deletecomment&amp;p=<?php echo $comment->comment_post_ID; ?>" title="<?php _e( 'Delete this comment' ); ?>" onclick="return deleteSomething( 'comment', <?php comment_ID(); ?>, '<?php echo js_escape( sprintf( __( "You are about to delete this comment by '%s'.\n'OK' to delete, 'Cancel' to stop." ), get_comment_author() ) ); ?>', theCommentList );"><?php _e( 'Delete' ); ?></a> ] &#8212;
					<a href="<?php echo get_permalink( $comment->comment_post_ID ); ?>" title="<?php _e( 'View the post' ); ?>"><?php printf( __( 'View post &#8220;%s&#8221;' ), get_the_title( $comment->comment_post_ID ) ); ?></a>
				</small></p>

				<p><small>
					<?php _e( 'Bulk action:' ); ?>
					<label for="comment-<?php comment_ID(); ?>-approve"><input type="radio" name="comment[<?php comment_ID(); ?>]" id="comment-<?php comment_ID(); ?>-approve" value="approve" /> <?php _e( 'Approve' ); ?></label> &nbsp;
					<label for="comment-<?php comment_ID(); ?>-spam"><input type="radio" name="comment[<?php comment_ID(); ?>]" id="comment-<?php comment_ID(); ?>-spam" value="spam" /> <?php _e( 'Spam' ); ?></label> &nbsp;
					<label for="comment-<?php comment_ID(); ?>-delete"><input type="radio" name="comment[<?php comment_ID(); ?>]" id="comment-<?php comment_ID(); ?>-delete" value="delete" /> <?php _e( 'Delete' ); ?></label> &nbsp;
					<label for="comment-<?php comment_ID(); ?>-nothing"><input type="radio" name="comment[<?php comment_ID(); ?>]" id="comment-<?php comment_ID(); ?>-nothing" value="later" checked="checked" /> <?php _e( 'No action' ); ?></label>
				</small></p>
			</li>
		<?php
		}
	?>
		</ol>

		<?php
			if ( $page_links ) {
				echo '<p class="pagenav">' . $page_links . '</p>';
			}
		?>

		<div id="ajax-response"></div>

		<noscript>
			<p class="submit">
				<label for="feelinglucky"><input name="feelinglucky" id="feelinglucky" type="checkbox" value="true" /> <?php _e( 'Delete every comment marked &#8220;defer.&#8221; <strong>Warning: This can&#8217;t be undone.</strong>' ); ?></label>
			</p>
		</noscript>

		<p class="submit">
			<input type="submit" id="submit" name="submit" value="<?php _e( 'Bulk Moderate Comments &raquo;' ); ?>" />
		</p>

		<script type="text/javascript">
		// <![CDATA[
			function mark_all_as( what ) {
				for ( var i = 0; i < document.approval.length; i++ ) {
					if ( document.approval[i].value == what ) {
						document.approval[i].checked = true;
					}
				}
			}

			document.write( '<p><strong><?php _e( 'Mark all:' ); ?></strong> <a href="javascript:mark_all_as(\'approve\')"><?php _e( 'Approved' ); ?></a> &ndash; <a href="javascript:mark_all_as(\'spam\')"><?php _e( 'Spam' ); ?></a> &ndash; <a href="javascript:mark_all_as(\'delete\')"><?php _e( 'Deleted' ); ?></a> &ndash; <a href="javascript:mark_all_as(\'later\')"><?php _e( 'Later' ); ?></a></p>' );
		// ]]>
		</script>
	</form>
</div>
<?php include_once './admin-footer.php'; ?>