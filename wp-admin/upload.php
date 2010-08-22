<?php
/**
 * Media Library administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

require_once( './includes/default-list-tables.php' );

$wp_list_table = new WP_Media_Table;
$wp_list_table->check_permissions();

// Handle bulk actions
if ( isset($_REQUEST['find_detached']) ) {
	check_admin_referer('bulk-media');

	if ( !current_user_can('edit_posts') )
		wp_die( __('You are not allowed to scan for lost attachments.') );

	$lost = $wpdb->get_col( "
		SELECT ID FROM $wpdb->posts
		WHERE post_type = 'attachment' AND post_parent > '0'
		AND post_parent NOT IN (
			SELECT ID FROM $wpdb->posts
			WHERE post_type NOT IN ( 'attachment', '" . join( "', '", get_post_types( array( 'public' => false ) ) ) . "' )
		)
	" );

	$_REQUEST['detached'] = 1;

} elseif ( isset( $_REQUEST['found_post_id'] ) && isset( $_REQUEST['media'] ) ) {
	check_admin_referer( 'bulk-media' );

	$parent_id = (int) $_REQUEST['found_post_id'];
	if ( !$parent_id )
		return;

	$parent = &get_post( $parent_id );
	if ( !current_user_can( 'edit_post', $parent_id ) )
		wp_die( __( 'You are not allowed to edit this post.' ) );

	$attach = array();
	foreach ( (array) $_REQUEST['media'] as $att_id ) {
		$att_id = (int) $att_id;

		if ( !current_user_can( 'edit_post', $att_id ) )
			continue;

		$attach[] = $att_id;
		clean_attachment_cache( $att_id );
	}

	if ( ! empty( $attach ) ) {
		$attach = implode( ',', $attach );
		$attached = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID IN ( $attach )", $parent_id ) );
	}

	if ( isset( $attached ) ) {
		$location = 'upload.php';
		if ( $referer = wp_get_referer() ) {
			if ( false !== strpos( $referer, 'upload.php' ) )
				$location = $referer;
		}

		$location = add_query_arg( array( 'attached' => $attached ) , $location );
		wp_redirect( $location );
		exit;
	}

} elseif ( isset( $_REQUEST['doaction'] ) || isset( $_REQUEST['doaction2'] ) || isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
	check_admin_referer( 'bulk-media' );

	if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
		$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type='attachment' AND post_status = 'trash'" );
		$doaction = 'delete';
	} elseif ( ( $_REQUEST['action'] != -1 || $_REQUEST['action2'] != -1 ) && ( isset( $_REQUEST['media'] ) || isset( $_REQUEST['ids'] ) ) ) {
		$post_ids = isset( $_REQUEST['media'] ) ? $_REQUEST['media'] : explode( ',', $_REQUEST['ids'] );
		$doaction = ( $_REQUEST['action'] != -1 ) ? $_REQUEST['action'] : $_REQUEST['action2'];
	} else {
		wp_redirect( $_SERVER['HTTP_REFERER'] );
	}

	$location = 'upload.php';
	if ( $referer = wp_get_referer() ) {
		if ( false !== strpos( $referer, 'upload.php' ) )
			$location = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'message', 'ids', 'posted' ), $referer );
	}

	switch ( $doaction ) {
		case 'trash':
			foreach ( (array) $post_ids as $post_id ) {
				if ( !current_user_can( 'delete_post', $post_id ) )
					wp_die( __( 'You are not allowed to move this post to the trash.' ) );

				if ( !wp_trash_post( $post_id ) )
					wp_die( __( 'Error in moving to trash...' ) );
			}
			$location = add_query_arg( array( 'trashed' => count( $post_ids ), 'ids' => join( ',', $post_ids ) ), $location );
			break;
		case 'untrash':
			foreach ( (array) $post_ids as $post_id ) {
				if ( !current_user_can( 'delete_post', $post_id ) )
					wp_die( __( 'You are not allowed to move this post out of the trash.' ) );

				if ( !wp_untrash_post( $post_id ) )
					wp_die( __( 'Error in restoring from trash...' ) );
			}
			$location = add_query_arg( 'untrashed', count( $post_ids ), $location );
			break;
		case 'delete':
			foreach ( (array) $post_ids as $post_id_del ) {
				if ( !current_user_can( 'delete_post', $post_id_del ) )
					wp_die( __( 'You are not allowed to delete this post.' ) );

				if ( !wp_delete_attachment( $post_id_del ) )
					wp_die( __( 'Error in deleting...' ) );
			}
			$location = add_query_arg( 'deleted', count( $post_ids ), $location );
			break;
	}

	wp_redirect( $location );
	exit;
} elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
	 wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
	 exit;
}

$wp_list_table->prepare_items();

$title = __('Media Library');
$parent_file = 'upload.php';

wp_enqueue_script( 'wp-ajax-response' );
wp_enqueue_script( 'jquery-ui-draggable' );
wp_enqueue_script( 'media' );

add_contextual_help( $current_screen,
	'<p>' . __('All the files you&#8217;ve uploaded are listed in the Media Library, with the most recent uploads listed first. You can use the <em>Screen Options</em> tab to customize the display of this screen.') . '</p>' .
	'<p>' . __('You can narrow the list by file type/status using the text link filters at the top of the screen. You also can refine the list by date using the dropdown menu above the media table.') . '</p>' .
	'<p>' . __('Hovering over a row reveals action links: <em>Edit</em>, <em>Delete Permanently</em>, and <em>View</em>. Clicking <em>Edit</em> or on the media file&#8217;s name displays a simple screen to edit that individual file&#8217;s metadata. Clicking <em>Delete Permanently</em> will delete the file from the media library (as well as from any posts to which it is currently attached). <em>View</em> will take you to the display page for that file.') . '</p>' .
	'<p>' . __('If a media file has not been attached to any post, you will see that in the <em>Attached To</em> column, and can click on <em>Attach File</em> to launch a small popup that will allow you to search for a post and attach the file.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Media_Library_SubPanel" target="_blank">Media Library Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

require_once('./admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?> <a href="media-new.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'file'); ?></a> <?php
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', get_search_query() ); ?>
</h2>

<?php
$message = '';
if ( isset($_GET['posted']) && (int) $_GET['posted'] ) {
	$message = __('Media attachment updated.');
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['attached']) && (int) $_GET['attached'] ) {
	$attached = (int) $_GET['attached'];
	$message = sprintf( _n('Reattached %d attachment.', 'Reattached %d attachments.', $attached), $attached );
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('attached'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['deleted']) && (int) $_GET['deleted'] ) {
	$message = sprintf( _n( 'Media attachment permanently deleted.', '%d media attachments permanently deleted.', $_GET['deleted'] ), number_format_i18n( $_GET['deleted'] ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('deleted'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['trashed']) && (int) $_GET['trashed'] ) {
	$message = sprintf( _n( 'Media attachment moved to the trash.', '%d media attachments moved to the trash.', $_GET['trashed'] ), number_format_i18n( $_GET['trashed'] ) );
	$message .= ' <a href="' . esc_url( wp_nonce_url( 'upload.php?doaction=undo&action=untrash&ids='.(isset($_GET['ids']) ? $_GET['ids'] : ''), "bulk-media" ) ) . '">' . __('Undo') . '</a>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('trashed'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['untrashed']) && (int) $_GET['untrashed'] ) {
	$message = sprintf( _n( 'Media attachment restored from the trash.', '%d media attachments restored from the trash.', $_GET['untrashed'] ), number_format_i18n( $_GET['untrashed'] ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('untrashed'), $_SERVER['REQUEST_URI']);
}

$messages[1] = __('Media attachment updated.');
$messages[2] = __('Media permanently deleted.');
$messages[3] = __('Error saving media attachment.');
$messages[4] = __('Media moved to the trash.') . ' <a href="' . esc_url( wp_nonce_url( 'upload.php?doaction=undo&action=untrash&ids='.(isset($_GET['ids']) ? $_GET['ids'] : ''), "bulk-media" ) ) . '">' . __('Undo') . '</a>';
$messages[5] = __('Media restored from the trash.');

if ( isset($_GET['message']) && (int) $_GET['message'] ) {
	$message = $messages[$_GET['message']];
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}

if ( !empty($message) ) { ?>
<div id="message" class="updated"><p><?php echo $message; ?></p></div>
<?php } ?>

<ul class="subsubsub">
<?php
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
$type_links[] = "<li><a href='upload.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $_total_posts, 'uploaded files' ), number_format_i18n( $_total_posts ) ) . '</a>';
foreach ( $post_mime_types as $mime_type => $label ) {
	$class = '';

	if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
		continue;

	if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
		$class = ' class="current"';
	if ( !empty( $num_posts[$mime_type] ) )
		$type_links[] = "<li><a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( _n( $label[2][0], $label[2][1], $num_posts[$mime_type] ), number_format_i18n( $num_posts[$mime_type] )) . '</a>';
}
$type_links[] = '<li><a href="upload.php?detached=1"' . ( $detached ? ' class="current"' : '' ) . '>' . sprintf( _nx( 'Unattached <span class="count">(%s)</span>', 'Unattached <span class="count">(%s)</span>', $total_orphans, 'detached files' ), number_format_i18n( $total_orphans ) ) . '</a>';

if ( !empty($_num_posts['trash']) )
	$type_links[] = '<li><a href="upload.php?status=trash"' . ( (isset($_GET['status']) && $_GET['status'] == 'trash' ) ? ' class="current"' : '') . '>' . sprintf( _nx( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', $_num_posts['trash'], 'uploaded files' ), number_format_i18n( $_num_posts['trash'] ) ) . '</a>';

echo implode( " |</li>\n", $type_links) . '</li>';
unset($type_links);
?>
</ul>

<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="screen-reader-text" for="media-search-input"><?php _e( 'Search Media' ); ?>:</label>
	<input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Media' ); ?>" class="button" />
</p>
</form>

<form id="posts-filter" action="" method="post">
<?php $wp_list_table->display(); ?>
<div id="ajax-response"></div>
<?php find_posts_div(); ?>
<br class="clear" />
</div>
</form>
<br class="clear" />

</div>

<?php
include('./admin-footer.php');
