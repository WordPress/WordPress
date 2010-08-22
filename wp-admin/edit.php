<?php
/**
 * Edit Posts Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

require_once( './includes/default-list-tables.php' );

$wp_list_table = new WP_Posts_Table;
$wp_list_table->check_permissions();

// Back-compat for viewing comments of an entry
if ( $_redirect = intval( max( @$_REQUEST['p'], @$_REQUEST['attachment_id'], @$_REQUEST['page_id'] ) ) ) {
	wp_redirect( admin_url('edit-comments.php?p=' . $_redirect ) );
	exit;
} else {
	unset( $_redirect );
}

// Handle bulk actions
if ( isset($_REQUEST['doaction']) || isset($_REQUEST['doaction2']) || isset($_REQUEST['delete_all']) || isset($_REQUEST['delete_all2']) || isset($_REQUEST['bulk_edit']) ) {
	check_admin_referer('bulk-posts');
	$sendback = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() );

	if ( strpos($sendback, 'post.php') !== false )
		$sendback = admin_url($post_new_file);

	if ( isset($_REQUEST['delete_all']) || isset($_REQUEST['delete_all2']) ) {
		$post_status = preg_replace('/[^a-z0-9_-]+/i', '', $_REQUEST['post_status']);
		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type=%s AND post_status = %s", $post_type, $post_status ) );
		$doaction = 'delete';
	} elseif ( ( $_REQUEST['action'] != -1 || $_REQUEST['action2'] != -1 ) && ( isset($_REQUEST['post']) || isset($_REQUEST['ids']) ) ) {
		$post_ids = isset($_REQUEST['post']) ? array_map( 'intval', (array) $_REQUEST['post'] ) : explode(',', $_REQUEST['ids']);
		$doaction = ($_REQUEST['action'] != -1) ? $_REQUEST['action'] : $_REQUEST['action2'];
	} else {
		wp_redirect( admin_url("edit.php?post_type=$post_type") );
	}

	switch ( $doaction ) {
		case 'trash':
			$trashed = 0;
			foreach( (array) $post_ids as $post_id ) {
				if ( !current_user_can($post_type_object->cap->delete_post, $post_id) )
					wp_die( __('You are not allowed to move this item to the Trash.') );

				if ( !wp_trash_post($post_id) )
					wp_die( __('Error in moving to Trash.') );

				$trashed++;
			}
			$sendback = add_query_arg( array('trashed' => $trashed, 'ids' => join(',', $post_ids)), $sendback );
			break;
		case 'untrash':
			$untrashed = 0;
			foreach( (array) $post_ids as $post_id ) {
				if ( !current_user_can($post_type_object->cap->delete_post, $post_id) )
					wp_die( __('You are not allowed to restore this item from the Trash.') );

				if ( !wp_untrash_post($post_id) )
					wp_die( __('Error in restoring from Trash.') );

				$untrashed++;
			}
			$sendback = add_query_arg('untrashed', $untrashed, $sendback);
			break;
		case 'delete':
			$deleted = 0;
			foreach( (array) $post_ids as $post_id ) {
				$post_del = & get_post($post_id);

				if ( !current_user_can($post_type_object->cap->delete_post, $post_id) )
					wp_die( __('You are not allowed to delete this item.') );

				if ( $post_del->post_type == 'attachment' ) {
					if ( ! wp_delete_attachment($post_id) )
						wp_die( __('Error in deleting...') );
				} else {
					if ( !wp_delete_post($post_id) )
						wp_die( __('Error in deleting...') );
				}
				$deleted++;
			}
			$sendback = add_query_arg('deleted', $deleted, $sendback);
			break;
		case 'edit':
			$done = bulk_edit_posts($_REQUEST);

			if ( is_array($done) ) {
				$done['updated'] = count( $done['updated'] );
				$done['skipped'] = count( $done['skipped'] );
				$done['locked'] = count( $done['locked'] );
				$sendback = add_query_arg( $done, $sendback );
			}
			break;
	}

	if ( isset($_REQUEST['action']) )
		$sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );

	wp_redirect($sendback);
	exit();
} elseif ( ! empty($_REQUEST['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

if ( 'post' != $post_type ) {
	$parent_file = "edit.php?post_type=$post_type";
	$submenu_file = "edit.php?post_type=$post_type";
	$post_new_file = "post-new.php?post_type=$post_type";
} else {
	$parent_file = 'edit.php';
	$submenu_file = 'edit.php';
	$post_new_file = 'post-new.php';
}

$wp_list_table->prepare_items();

wp_enqueue_script('inline-edit-post');

$title = $post_type_object->labels->name;

if ( 'post' == $post_type ) {
	add_contextual_help($current_screen,
	'<p>' . __('You can customize the display of this screen in a number of ways:') . '</p>' .
	'<ul>' .
	'<li>' . __('You can hide/display columns based on your needs and decide how many posts to list per screen using the Screen Options tab.') . '</li>' .
	'<li>' . __('You can filter the list of posts by post status using the text links in the upper left to show All, Published, Draft, or Trashed posts. The default view is to show all posts.') . '</li>' .
	'<li>' . __('You can view posts in a simple title list or with an excerpt. Choose the view you prefer by clicking on the icons at the top of the list on the right.') . '</li>' .
	'<li>' . __('You can refine the list to show only posts in a specific category or from a specific month by using the dropdown menus above the posts list. Click the Filter button after making your selection. You also can refine the list by clicking on the post author, category or tag in the posts list.') . '</li>' .
	'</ul>' .
	'<p>' . __('Hovering over a row in the posts list will display action links that allow you to manage your post. You can perform the following actions:') . '</p>' .
	'<ul>' .
	'<li>' . __('Edit takes you to the editing screen for that post. You can also reach that screen by clicking on the post title.') . '</li>' .
	'<li>' . __('Quick Edit provides inline access to the metadata of your post, allowing you to update post details without leaving this screen.') . '</li>' .
	'<li>' . __('Trash removes your post from this list and places it in the trash, from which you can permanently delete it.') . '</li>' .
	'<li>' . __('Preview will show you what your draft post will look like if you publish it. View will take you to your live site to view the post. Which link is available depends on your post&#8217;s status.') . '</li>' .
	'</ul>' .
	'<p>' . __('You can also edit multiple posts at once. Select the posts you want to edit using the checkboxes, select Edit from the Bulk Actions menu and click Apply. You will be able to change the metadata (categories, author, etc.) for all selected posts at once. To remove a post from the grouping, just click the x next to its name in the Bulk Edit area that appears.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
	);
} elseif ( 'page' == $post_type ) {
	add_contextual_help($current_screen,
	'<p>' . __('Pages are similar to to Posts in that they have a title, body text, and associated metadata, but they are different in that they are not part of the chronological blog stream, kind of like permanent posts. Pages are not categorized or tagged, but can have a hierarchy. You can nest Pages under other Pages by making one the &#8220;Parent&#8221; of the other, creating a group of Pages.') . '</p>' .
	'<p>' . __('Managing Pages is very similar to managing Posts, and the screens can be customized in the same way.') . '</p>' .
	'<p>' . __('You can also perform the same types of actions, including narrowing the list by using the filters, acting on a Page using the action links that appear when you hover over a row, or using the Bulk Actions menu to edit the metadata for multiple Pages at once.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Pages_Edit_SubPanel" target="_blank">Page Management Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
	);
}

require_once('./admin-header.php');
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $post_type_object->labels->name ); ?> <a href="<?php echo $post_new_file ?>" class="button add-new-h2"><?php echo esc_html($post_type_object->labels->add_new); ?></a> <?php
if ( isset($_REQUEST['s']) && $_REQUEST['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', get_search_query() ); ?>
</h2>

<?php
if ( isset($_REQUEST['posted']) && $_REQUEST['posted'] ) : $_REQUEST['posted'] = (int) $_REQUEST['posted']; ?>
<div id="message" class="updated"><p><strong><?php _e('This has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_REQUEST['posted'] ); ?>"><?php _e('View Post'); ?></a> | <a href="<?php echo get_edit_post_link( $_REQUEST['posted'] ); ?>"><?php _e('Edit Post'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif; ?>

<?php if ( isset($_REQUEST['locked']) || isset($_REQUEST['skipped']) || isset($_REQUEST['updated']) || isset($_REQUEST['deleted']) || isset($_REQUEST['trashed']) || isset($_REQUEST['untrashed']) ) { ?>
<div id="message" class="updated"><p>
<?php if ( isset($_REQUEST['updated']) && (int) $_REQUEST['updated'] ) {
	printf( _n( '%s post updated.', '%s posts updated.', $_REQUEST['updated'] ), number_format_i18n( $_REQUEST['updated'] ) );
	unset($_REQUEST['updated']);
}

if ( isset($_REQUEST['skipped']) && (int) $_REQUEST['skipped'] )
	unset($_REQUEST['skipped']);

if ( isset($_REQUEST['locked']) && (int) $_REQUEST['locked'] ) {
	printf( _n( '%s item not updated, somebody is editing it.', '%s items not updated, somebody is editing them.', $_REQUEST['locked'] ), number_format_i18n( $_REQUEST['locked'] ) );
	unset($_REQUEST['locked']);
}

if ( isset($_REQUEST['deleted']) && (int) $_REQUEST['deleted'] ) {
	printf( _n( 'Item permanently deleted.', '%s items permanently deleted.', $_REQUEST['deleted'] ), number_format_i18n( $_REQUEST['deleted'] ) );
	unset($_REQUEST['deleted']);
}

if ( isset($_REQUEST['trashed']) && (int) $_REQUEST['trashed'] ) {
	printf( _n( 'Item moved to the Trash.', '%s items moved to the Trash.', $_REQUEST['trashed'] ), number_format_i18n( $_REQUEST['trashed'] ) );
	$ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : 0;
	echo ' <a href="' . esc_url( wp_nonce_url( "edit.php?post_type=$post_type&doaction=undo&action=untrash&ids=$ids", "bulk-posts" ) ) . '">' . __('Undo') . '</a><br />';
	unset($_REQUEST['trashed']);
}

if ( isset($_REQUEST['untrashed']) && (int) $_REQUEST['untrashed'] ) {
	printf( _n( 'Item restored from the Trash.', '%s items restored from the Trash.', $_REQUEST['untrashed'] ), number_format_i18n( $_REQUEST['untrashed'] ) );
	unset($_REQUEST['undeleted']);
}

$_SERVER['REQUEST_URI'] = remove_query_arg( array('locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed'), $_SERVER['REQUEST_URI'] );
?>
</p></div>
<?php } ?>

<form id="posts-filter" action="" method="get">

<ul class="subsubsub">
<?php
if ( empty($locked_post_status) ) :
$status_links = array();
$num_posts = wp_count_posts( $post_type, 'readable' );
$class = '';
$allposts = '';

$user_posts = false;
if ( !current_user_can( $post_type_object->cap->edit_others_posts ) ) {
	$user_posts = true;

	$user_posts_count = $wpdb->get_var( $wpdb->prepare( "
		SELECT COUNT( 1 ) FROM $wpdb->posts
		WHERE post_type = '%s' AND post_status NOT IN ( 'trash', 'auto-draft' )
		AND post_author = %d
	", $post_type, get_current_user_id() ) );

	if ( $user_posts_count && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['all_posts'] ) && empty( $_REQUEST['author'] ) )
		$_REQUEST['author'] = get_current_user_id();
}

if ( $user_posts ) {
	if ( isset( $_REQUEST['author'] ) && ( $_REQUEST['author'] == $current_user->ID ) )
		$class = ' class="current"';
	$status_links[] = "<li><a href='edit.php?post_type=$post_type&author=$current_user->ID'$class>" . sprintf( _nx( 'Mine <span class="count">(%s)</span>', 'Mine <span class="count">(%s)</span>', $user_posts_count, 'posts' ), number_format_i18n( $user_posts_count ) ) . '</a>';
	$allposts = '&all_posts=1';
}

$total_posts = array_sum( (array) $num_posts );

// Subtract post types that are not included in the admin all list.
foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
	$total_posts -= $num_posts->$state;

$class = empty($class) && empty($_REQUEST['post_status']) ? ' class="current"' : '';
$status_links[] = "<li><a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
	$class = '';

	$status_name = $status->name;

	if ( !in_array( $status_name, $avail_post_stati ) )
		continue;

	if ( empty( $num_posts->$status_name ) )
		continue;

	if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
		$class = ' class="current"';

	$status_links[] = "<li><a href='edit.php?post_status=$status_name&amp;post_type=$post_type'$class>" . sprintf( _n( $status->label_count[0], $status->label_count[1], $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
}
echo implode( " |</li>\n", $status_links ) . '</li>';
unset( $status_links );
endif;
?>
</ul>

<p class="search-box">
	<label class="screen-reader-text" for="post-search-input"><?php echo $post_type_object->labels->search_items; ?>:</label>
	<input type="text" id="post-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php echo esc_attr( $post_type_object->labels->search_items  ); ?>" class="button" />
</p>

<input type="hidden" name="post_status" class="post_status_page" value="<?php echo !empty($_REQUEST['post_status']) ? esc_attr($_REQUEST['post_status']) : 'all'; ?>" />
<input type="hidden" name="post_type" class="post_type_page" value="<?php echo $post_type; ?>" />

<?php $wp_list_table->display(); ?>

</form>

<?php $wp_list_table->inline_edit(); ?>

<div id="ajax-response"></div>
<br class="clear" />
</div>

<?php
include('./admin-footer.php');
