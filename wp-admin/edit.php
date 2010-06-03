<?php
/**
 * Edit Posts Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( !isset($_GET['post_type']) )
	$post_type = 'post';
elseif ( in_array( $_GET['post_type'], get_post_types( array('show_ui' => true ) ) ) )
	$post_type = $_GET['post_type'];
else
	wp_die( __('Invalid post type') );
$_GET['post_type'] = $post_type;

$post_type_object = get_post_type_object($post_type);

if ( !current_user_can($post_type_object->cap->edit_posts) )
	wp_die(__('Cheatin&#8217; uh?'));

// Back-compat for viewing comments of an entry
if ( $_redirect = intval( max( @$_GET['p'], @$_GET['attachment_id'], @$_GET['page_id'] ) ) ) {
	wp_redirect( admin_url('edit-comments.php?p=' . $_redirect ) );
	exit;
} else {
	unset( $_redirect );
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

$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
if ( empty($pagenum) )
	$pagenum = 1;
$per_page = 'edit_' . $post_type . '_per_page';
$per_page = (int) get_user_option( $per_page );
if ( empty( $per_page ) || $per_page < 1 )
	$per_page = 20;
// @todo filter based on type
$per_page = apply_filters( 'edit_posts_per_page', $per_page );

// Handle bulk actions
if ( isset($_GET['doaction']) || isset($_GET['doaction2']) || isset($_GET['delete_all']) || isset($_GET['delete_all2']) || isset($_GET['bulk_edit']) ) {
	check_admin_referer('bulk-posts');
	$sendback = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() );

	if ( strpos($sendback, 'post.php') !== false )
		$sendback = admin_url($post_new_file);

	if ( isset($_GET['delete_all']) || isset($_GET['delete_all2']) ) {
		$post_status = preg_replace('/[^a-z0-9_-]+/i', '', $_GET['post_status']);
		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type=%s AND post_status = %s", $post_type, $post_status ) );
		$doaction = 'delete';
	} elseif ( ( $_GET['action'] != -1 || $_GET['action2'] != -1 ) && ( isset($_GET['post']) || isset($_GET['ids']) ) ) {
		$post_ids = isset($_GET['post']) ? array_map( 'intval', (array) $_GET['post'] ) : explode(',', $_GET['ids']);
		$doaction = ($_GET['action'] != -1) ? $_GET['action'] : $_GET['action2'];
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
			$done = bulk_edit_posts($_GET);

			if ( is_array($done) ) {
				$done['updated'] = count( $done['updated'] );
				$done['skipped'] = count( $done['skipped'] );
				$done['locked'] = count( $done['locked'] );
				$sendback = add_query_arg( $done, $sendback );
			}
			break;
	}

	if ( isset($_GET['action']) )
		$sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );

	wp_redirect($sendback);
	exit();
} elseif ( ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

wp_enqueue_script('inline-edit-post');

$user_posts = false;
if ( !current_user_can($post_type_object->cap->edit_others_posts) ) {
	$user_posts_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(1) FROM $wpdb->posts WHERE post_type = '%s' AND post_status NOT IN ('trash', 'auto-draft') AND post_author = %d", $post_type, $current_user->ID) );
	$user_posts = true;
	if ( $user_posts_count && empty($_GET['post_status']) && empty($_GET['all_posts']) && empty($_GET['author']) )
		$_GET['author'] = $current_user->ID;
}

$avail_post_stati = wp_edit_posts_query();

if ( $post_type_object->hierarchical )
	$num_pages = ceil($wp_query->post_count / $per_page);
else
	$num_pages = $wp_query->max_num_pages;

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

if ( empty($_GET['mode']) )
	$mode = 'list';
else
	$mode = esc_attr($_GET['mode']); ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $post_type_object->labels->name ); ?> <a href="<?php echo $post_new_file ?>" class="button add-new-h2"><?php echo esc_html($post_type_object->labels->add_new); ?></a> <?php
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', get_search_query() ); ?>
</h2>

<?php
if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated"><p><strong><?php _e('This has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View Post'); ?></a> | <a href="<?php echo get_edit_post_link( $_GET['posted'] ); ?>"><?php _e('Edit Post'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif; ?>

<?php if ( isset($_GET['locked']) || isset($_GET['skipped']) || isset($_GET['updated']) || isset($_GET['deleted']) || isset($_GET['trashed']) || isset($_GET['untrashed']) ) { ?>
<div id="message" class="updated"><p>
<?php if ( isset($_GET['updated']) && (int) $_GET['updated'] ) {
	printf( _n( '%s post updated.', '%s posts updated.', $_GET['updated'] ), number_format_i18n( $_GET['updated'] ) );
	unset($_GET['updated']);
}

if ( isset($_GET['skipped']) && (int) $_GET['skipped'] )
	unset($_GET['skipped']);

if ( isset($_GET['locked']) && (int) $_GET['locked'] ) {
	printf( _n( '%s item not updated, somebody is editing it.', '%s items not updated, somebody is editing them.', $_GET['locked'] ), number_format_i18n( $_GET['locked'] ) );
	unset($_GET['locked']);
}

if ( isset($_GET['deleted']) && (int) $_GET['deleted'] ) {
	printf( _n( 'Item permanently deleted.', '%s items permanently deleted.', $_GET['deleted'] ), number_format_i18n( $_GET['deleted'] ) );
	unset($_GET['deleted']);
}

if ( isset($_GET['trashed']) && (int) $_GET['trashed'] ) {
	printf( _n( 'Item moved to the trash.', '%s items moved to the trash.', $_GET['trashed'] ), number_format_i18n( $_GET['trashed'] ) );
	$ids = isset($_GET['ids']) ? $_GET['ids'] : 0;
	echo ' <a href="' . esc_url( wp_nonce_url( "edit.php?post_type=$post_type&doaction=undo&action=untrash&ids=$ids", "bulk-posts" ) ) . '">' . __('Undo') . '</a><br />';
	unset($_GET['trashed']);
}

if ( isset($_GET['untrashed']) && (int) $_GET['untrashed'] ) {
	printf( _n( 'Item restored from the Trash.', '%s items restored from the Trash.', $_GET['untrashed'] ), number_format_i18n( $_GET['untrashed'] ) );
	unset($_GET['undeleted']);
}

$_SERVER['REQUEST_URI'] = remove_query_arg( array('locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed'), $_SERVER['REQUEST_URI'] );
?>
</p></div>
<?php } ?>

<form id="posts-filter" action="<?php echo admin_url('edit.php'); ?>" method="get">

<ul class="subsubsub">
<?php
if ( empty($locked_post_status) ) :
$status_links = array();
$num_posts = wp_count_posts( $post_type, 'readable' );
$class = '';
$allposts = '';

if ( $user_posts ) {
	if ( isset( $_GET['author'] ) && ( $_GET['author'] == $current_user->ID ) )
		$class = ' class="current"';
	$status_links[] = "<li><a href='edit.php?post_type=$post_type&author=$current_user->ID'$class>" . sprintf( _nx( 'Mine <span class="count">(%s)</span>', 'Mine <span class="count">(%s)</span>', $user_posts_count, 'posts' ), number_format_i18n( $user_posts_count ) ) . '</a>';
	$allposts = '&all_posts=1';
}

$total_posts = array_sum( (array) $num_posts );

// Subtract post types that are not included in the admin all list.
foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
	$total_posts -= $num_posts->$state;

$class = empty($class) && empty($_GET['post_status']) ? ' class="current"' : '';
$status_links[] = "<li><a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
	$class = '';

	$status_name = $status->name;

	if ( !in_array( $status_name, $avail_post_stati ) )
		continue;

	if ( empty( $num_posts->$status_name ) )
		continue;

	if ( isset($_GET['post_status']) && $status_name == $_GET['post_status'] )
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

<input type="hidden" name="post_status" class="post_status_page" value="<?php echo !empty($_GET['post_status']) ? esc_attr($_GET['post_status']) : 'all'; ?>" />
<input type="hidden" name="post_type" class="post_type_page" value="<?php echo $post_type; ?>" />
<input type="hidden" name="mode" value="<?php echo esc_attr($mode); ?>" />

<?php if ( have_posts() ) { ?>

<div class="tablenav">
<?php
$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $num_pages,
	'current' => $pagenum
));

$is_trash = isset($_GET['post_status']) && $_GET['post_status'] == 'trash';

?>

<div class="alignleft actions">
<select name="action">
<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
<?php if ( $is_trash ) { ?>
<option value="untrash"><?php _e('Restore'); ?></option>
<?php } else { ?>
<option value="edit"><?php _e('Edit'); ?></option>
<?php } if ( $is_trash || !EMPTY_TRASH_DAYS ) { ?>
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php } else { ?>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php } ?>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-posts'); ?>

<?php // view filters
if ( !is_singular() ) {
$arc_query = $wpdb->prepare("SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = %s ORDER BY post_date DESC", $post_type);

$arc_result = $wpdb->get_results( $arc_query );

$month_count = count($arc_result);

if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) {
$m = isset($_GET['m']) ? (int)$_GET['m'] : 0;
?>
<select name='m'>
<option<?php selected( $m, 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( $arc_row->yyear . $arc_row->mmonth == $m )
		$default = ' selected="selected"';
	else
		$default = '';

	echo "<option$default value='" . esc_attr("$arc_row->yyear$arc_row->mmonth") . "'>";
	echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
	echo "</option>\n";
}
?>
</select>
<?php } ?>

<?php
if ( is_object_in_taxonomy($post_type, 'category') ) {
	$dropdown_options = array('show_option_all' => __('View all categories'), 'hide_empty' => 0, 'hierarchical' => 1,
		'show_count' => 0, 'orderby' => 'name', 'selected' => $cat);
	wp_dropdown_categories($dropdown_options);
}
do_action('restrict_manage_posts');
?>
<input type="submit" id="post-query-submit" value="<?php esc_attr_e('Filter'); ?>" class="button-secondary" />
<?php }

if ( $is_trash && current_user_can($post_type_object->cap->edit_others_posts) ) { ?>
<input type="submit" name="delete_all" id="delete_all" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php } ?>
</div>

<?php if ( $page_links ) { ?>
<div class="tablenav-pages"><?php
	$count_posts = $post_type_object->hierarchical ? $wp_query->post_count : $wp_query->found_posts;
	$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
						number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
						number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
						number_format_i18n( $count_posts ),
						$page_links
						);
	echo $page_links_text;
	?></div>
<?php
}

if ( !$post_type_object->hierarchical ) {
?>

<div class="view-switch">
	<a href="<?php echo esc_url(add_query_arg('mode', 'list', $_SERVER['REQUEST_URI'])) ?>"><img <?php if ( 'list' == $mode ) echo 'class="current"'; ?> id="view-switch-list" src="<?php echo esc_url( includes_url( 'images/blank.gif' ) ); ?>" width="20" height="20" title="<?php _e('List View') ?>" alt="<?php _e('List View') ?>" /></a>
	<a href="<?php echo esc_url(add_query_arg('mode', 'excerpt', $_SERVER['REQUEST_URI'])) ?>"><img <?php if ( 'excerpt' == $mode ) echo 'class="current"'; ?> id="view-switch-excerpt" src="<?php echo esc_url( includes_url( 'images/blank.gif' ) ); ?>" width="20" height="20" title="<?php _e('Excerpt View') ?>" alt="<?php _e('Excerpt View') ?>" /></a>
</div>

<?php } ?>
<div class="clear"></div>
</div>

<div class="clear"></div>

<?php include( './edit-post-rows.php' ); ?>

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links_text</div>";
?>

<div class="alignleft actions">
<select name="action2">
<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
<?php if ( $is_trash ) { ?>
<option value="untrash"><?php _e('Restore'); ?></option>
<?php } else { ?>
<option value="edit"><?php _e('Edit'); ?></option>
<?php } if ( $is_trash || !EMPTY_TRASH_DAYS ) { ?>
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php } else { ?>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php } ?>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
<?php if ( $is_trash && current_user_can($post_type_object->cap->edit_others_posts) ) { ?>
<input type="submit" name="delete_all2" id="delete_all2" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php } ?>
<br class="clear" />
</div>
<br class="clear" />
</div>

<?php } else { // have_posts() ?>
<div class="clear"></div>
<p><?php
if ( isset($_GET['post_status']) && 'trash' == $_GET['post_status'] )
	echo $post_type_object->labels->not_found_in_trash;
else
	echo $post_type_object->labels->not_found;
?></p>
<?php } ?>

</form>

<?php inline_edit_row( $current_screen ); ?>

<div id="ajax-response"></div>
<br class="clear" />
</div>

<?php
include('./admin-footer.php');
