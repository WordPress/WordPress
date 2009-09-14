<?php
/**
 * Edit Pages Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( !current_user_can('edit_pages') )
	wp_die(__('Cheatin&#8217; uh?'));

// Handle bulk actions
if ( isset($_GET['doaction']) || isset($_GET['doaction2']) || isset($_GET['delete_all']) || isset($_GET['delete_all2']) || isset($_GET['bulk_edit']) ) {
	check_admin_referer('bulk-pages');
	$sendback = wp_get_referer();

	if ( strpos($sendback, 'page.php') !== false )
		$sendback = admin_url('page-new.php');

	if ( isset($_GET['delete_all']) || isset($_GET['delete_all2']) ) {
		$post_status = preg_replace('/[^a-z0-9_-]+/i', '', $_GET['post_status']);
		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = %s", $post_status ) );
		$doaction = 'delete';
	} elseif ( ($_GET['action'] != -1 || $_GET['action2'] != -1) && isset($_GET['post']) ) {
		$post_ids = array_map( 'intval', (array) $_GET['post'] );
		$doaction = ($_GET['action'] != -1) ? $_GET['action'] : $_GET['action2'];
	} else {
		wp_redirect( admin_url('edit-pages.php') );
	}

	switch ( $doaction ) {
		case 'trash':
			$trashed = 0;
			foreach( (array) $post_ids as $post_id ) {
				if ( !current_user_can('delete_page', $post_id) )
					wp_die( __('You are not allowed to move this page to the trash.') );

				if ( !wp_trash_post($post_id) )
					wp_die( __('Error in moving to trash...') );

				$trashed++;
			}
			$sendback = add_query_arg('trashed', $trashed, $sendback);
			break;
		case 'untrash':
			$untrashed = 0;
			foreach( (array) $post_ids as $post_id ) {
				if ( !current_user_can('delete_page', $post_id) )
					wp_die( __('You are not allowed to restore this page from the trash.') );

				if ( !wp_untrash_post($post_id) )
					wp_die( __('Error in restoring from trash...') );

				$untrashed++;
			}
			$sendback = add_query_arg('untrashed', $untrashed, $sendback);
			break;
		case 'delete':
			$deleted = 0;
			foreach( (array) $post_ids as $post_id ) {
				$post_del = & get_post($post_id);

				if ( !current_user_can('delete_page', $post_id) )
					wp_die( __('You are not allowed to delete this page.') );

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
			$_GET['post_type'] = 'page';
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
		$sendback = remove_query_arg( array('action', 'action2', 'post_parent', 'page_template', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view', 'post_type'), $sendback );

	wp_redirect($sendback);
	exit();
} elseif ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

if ( empty($title) )
	$title = __('Edit Pages');
$parent_file = 'edit-pages.php';
wp_enqueue_script('inline-edit-post');

$post_stati  = array(	//	array( adj, noun )
		'publish' => array(_x('Published', 'page'), __('Published pages'), _nx_noop('Published <span class="count">(%s)</span>', 'Published <span class="count">(%s)</span>', 'page')),
		'future' => array(_x('Scheduled', 'page'), __('Scheduled pages'), _nx_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>', 'page')),
		'pending' => array(_x('Pending Review', 'page'), __('Pending pages'), _nx_noop('Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>', 'page')),
		'draft' => array(_x('Draft', 'page'), _x('Drafts', 'manage posts header'), _nx_noop('Draft <span class="count">(%s)</span>', 'Drafts <span class="count">(%s)</span>', 'page')),
		'private' => array(_x('Private', 'page'), __('Private pages'), _nx_noop('Private <span class="count">(%s)</span>', 'Private <span class="count">(%s)</span>', 'page')),
		'trash' => array(_x('Trash', 'page'), __('Trash pages'), _nx_noop('Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', 'page'))
	);

$post_stati = apply_filters('page_stati', $post_stati);

$query = array('post_type' => 'page', 'orderby' => 'menu_order title',
	'posts_per_page' => -1, 'posts_per_archive_page' => -1, 'order' => 'asc');

$post_status_label = __('Pages');
if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$query['post_status'] = $_GET['post_status'];
	$query['perm'] = 'readable';
}

$query = apply_filters('manage_pages_query', $query);
wp($query);

if ( is_singular() ) {
	wp_enqueue_script( 'admin-comments' );
	enqueue_comment_hotkeys_js();
}

require_once('admin-header.php'); ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?> <a href="page-new.php" class="button add-new-h2"><?php esc_html_e('Add New'); ?></a> <?php
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( get_search_query() ) ); ?>
</h2>

<?php if ( isset($_GET['locked']) || isset($_GET['skipped']) || isset($_GET['updated']) || isset($_GET['deleted']) || isset($_GET['trashed']) || isset($_GET['untrashed']) ) { ?>
<div id="message" class="updated fade"><p>
<?php if ( isset($_GET['updated']) && (int) $_GET['updated'] ) {
	printf( _n( '%s page updated.', '%s pages updated.', $_GET['updated'] ), number_format_i18n( $_GET['updated'] ) );
	unset($_GET['updated']);
}
if ( isset($_GET['skipped']) && (int) $_GET['skipped'] ) {
	printf( _n( '%s page not updated, invalid parent page specified.', '%s pages not updated, invalid parent page specified.', $_GET['skipped'] ), number_format_i18n( $_GET['skipped'] ) );
	unset($_GET['skipped']);
}
if ( isset($_GET['locked']) && (int) $_GET['locked'] ) {
	printf( _n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $_GET['locked'] ), number_format_i18n( $_GET['skipped'] ) );
	unset($_GET['locked']);
}
if ( isset($_GET['deleted']) && (int) $_GET['deleted'] ) {
	printf( _n( 'Page permanently deleted.', '%s pages permanently deleted.', $_GET['deleted'] ), number_format_i18n( $_GET['deleted'] ) );
	unset($_GET['deleted']);
}
if ( isset($_GET['trashed']) && (int) $_GET['trashed'] ) {
	printf( _n( 'Page moved to the trash.', '%s pages moved to the trash.', $_GET['trashed'] ), number_format_i18n( $_GET['trashed'] ) );
	unset($_GET['trashed']);
}
if ( isset($_GET['untrashed']) && (int) $_GET['untrashed'] ) {
	printf( _n( 'Page restored from the trash.', '%s pages restored from the trash.', $_GET['untrashed'] ), number_format_i18n( $_GET['untrashed'] ) );
	unset($_GET['untrashed']);
}
$_SERVER['REQUEST_URI'] = remove_query_arg( array('locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed'), $_SERVER['REQUEST_URI'] );
?>
</p></div>
<?php } ?>

<?php if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your page has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View page'); ?></a> | <a href="<?php echo get_edit_post_link( $_GET['posted'] ); ?>"><?php _e('Edit page'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif; ?>

<form id="posts-filter" action="<?php echo admin_url('edit-pages.php'); ?>" method="get">
<ul class="subsubsub">
<?php

$avail_post_stati = get_available_post_statuses('page');
if ( empty($locked_post_status) ) :
$status_links = array();
$num_posts = wp_count_posts('page', 'readable');
$total_posts = array_sum( (array) $num_posts ) - $num_posts->trash;
$class = empty($_GET['post_status']) ? ' class="current"' : '';
$status_links[] = "<li><a href='edit-pages.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'pages' ), number_format_i18n( $total_posts ) ) . '</a>';
foreach ( $post_stati as $status => $label ) {
	$class = '';

	if ( !in_array($status, $avail_post_stati) )
		continue;

	if ( isset( $_GET['post_status'] ) && $status == $_GET['post_status'] )
		$class = ' class="current"';

	$status_links[] = "<li><a href='edit-pages.php?post_status=$status'$class>" . sprintf( _nx( $label[2][0], $label[2][1], $num_posts->$status, $label[2][2] ), number_format_i18n( $num_posts->$status ) ) . '</a>';
}
echo implode( " |</li>\n", $status_links ) . '</li>';
unset($status_links);
endif;
?>
</ul>

<p class="search-box">
	<label class="screen-reader-text" for="page-search-input"><?php _e( 'Search Pages' ); ?>:</label>
	<input type="text" id="page-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Pages' ); ?>" class="button" />
</p>

<input type="hidden" name="post_status" class="post_status_page" value="<?php echo !empty($_GET['post_status']) ? esc_attr($_GET['post_status']) : 'all'; ?>" />

<?php if ($posts) { ?>

<div class="tablenav">

<?php
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
if ( empty($pagenum) )
	$pagenum = 1;
$per_page = get_user_option('edit_pages_per_page');
if ( empty( $per_page ) || $per_page < 0 )
	$per_page = 20;

$num_pages = ceil($wp_query->post_count / $per_page);
$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $num_pages,
	'current' => $pagenum
));

$is_trash = isset($_GET['post_status']) && $_GET['post_status'] == 'trash';

if ( $page_links ) : ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
	number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
	number_format_i18n( min( $pagenum * $per_page, $wp_query->post_count ) ),
	number_format_i18n( $wp_query->post_count ),
	$page_links
); echo $page_links_text; ?></div>
<?php endif; ?>

<div class="alignleft actions">
<select name="action">
<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
<?php if ( $is_trash ) { ?>
<option value="untrash"><?php _e('Restore'); ?></option>
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php } else { ?>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php } ?>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-pages'); ?>
<?php if ( $is_trash ) { ?>
<input type="submit" name="delete_all" id="delete_all" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php } ?>
</div>

<br class="clear" />
</div>

<div class="clear"></div>

<table class="widefat page fixed" cellspacing="0">
  <thead>
  <tr>
<?php print_column_headers('edit-pages'); ?>
  </tr>
  </thead>

  <tfoot>
  <tr>
<?php print_column_headers('edit-pages', false); ?>
  </tr>
  </tfoot>

  <tbody>
  <?php page_rows($posts, $pagenum, $per_page); ?>
  </tbody>
</table>

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
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php } else { ?>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php } ?>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
<?php if ( $is_trash ) { ?>
<input type="submit" name="delete_all2" id="delete_all2" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php } ?>
</div>

<br class="clear" />
</div>

<?php } else { ?>
<div class="clear"></div>
<p><?php _e('No pages found.') ?></p>
<?php
} // end if ($posts)
?>

</form>

<?php inline_edit_row( 'page' ) ?>

<div id="ajax-response"></div>


<?php

if ( 1 == count($posts) && is_singular() ) :

	$comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved != 'spam' ORDER BY comment_date", $id) );
	if ( $comments ) :
		// Make sure comments, post, and post_author are cached
		update_comment_cache($comments);
		$post = get_post($id);
		$authordata = get_userdata($post->post_author);
	?>

<br class="clear" />

<table class="widefat" cellspacing="0">
<thead>
  <tr>
    <th scope="col" class="column-comment">
		<?php  /* translators: column name */ echo _x('Comment', 'column name') ?>
	</th>
    <th scope="col" class="column-author"><?php _e('Author') ?></th>
    <th scope="col" class="column-date"><?php _e('Submitted') ?></th>
  </tr>
</thead>
<tbody id="the-comment-list" class="list:comment">
<?php
	foreach ($comments as $comment)
		_wp_comment_row( $comment->comment_ID, 'single', false, false );
?>
</tbody>
</table>

<?php
wp_comment_reply();
endif; // comments
endif; // posts;

?>

</div>

<?php
include('admin-footer.php');
