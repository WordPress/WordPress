<?php
/**
 * Edit Pages Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

// Handle bulk actions
if ( isset($_GET['action']) && $_GET['action'] != -1 ) {
	switch ( $_GET['action'] ) {
		case 'delete':
			if ( isset($_GET['post']) && isset($_GET['doaction']) ) {
				check_admin_referer('bulk-pages');
				foreach( (array) $_GET['post'] as $post_id_del ) {
					$post_del = & get_post($post_id_del);
		
					if ( !current_user_can('delete_page', $post_id_del) )
						wp_die( __('You are not allowed to delete this page.') );
		
					if ( $post_del->post_type == 'attachment' ) {
						if ( ! wp_delete_attachment($post_id_del) )
							wp_die( __('Error in deleting...') );
					} else {
						if ( !wp_delete_post($post_id_del) )
							wp_die( __('Error in deleting...') );
					}
				}
			}
			break;
		case 'edit':
			if ( isset($_GET['post']) ) {
				check_admin_referer('bulk-pages');
				$_GET['post_status'] = $_GET['_status'];
	
				if ( -1 == $_GET['post_author'] )
					unset($_GET['post_author']);
	
				$done = bulk_edit_posts($_GET);
			}
			break;
	}
	$sendback = wp_get_referer();
	if (strpos($sendback, 'page.php') !== false) $sendback = admin_url('page-new.php');
	elseif (strpos($sendback, 'attachments.php') !== false) $sendback = admin_url('attachments.php');
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	if ( isset($done) ) {
		$done['upd'] = count( $done['upd'] );
		$done['skip'] = count( $done['skip'] );
		$sendback = add_query_arg( $done, $sendback );
		unset($done);
	}
	wp_redirect($sendback);
	exit();
} elseif ( !empty($_GET['_wp_http_referer']) ) {
	 wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	 exit;
}

$title = __('Pages');
$parent_file = 'edit.php';
wp_enqueue_script('admin-forms');
wp_enqueue_script('inline-edit');
wp_enqueue_script('pages');

$post_stati  = array(	//	array( adj, noun )
		'publish' => array(__('Published'), __('Published pages'), __ngettext_noop('Published (%s)', 'Published (%s)')),
		'future' => array(__('Scheduled'), __('Scheduled pages'), __ngettext_noop('Scheduled (%s)', 'Scheduled (%s)')),
		'pending' => array(__('Pending Review'), __('Pending pages'), __ngettext_noop('Pending Review (%s)', 'Pending Review (%s)')),
		'draft' => array(__('Draft'), _c('Drafts|manage posts header'), __ngettext_noop('Draft (%s)', 'Drafts (%s)')),
		'private' => array(__('Private'), __('Private pages'), __ngettext_noop('Private (%s)', 'Private (%s)'))
	);

$post_status_label = __('Pages');
$post_status_q = '';
if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$post_status_q = '&post_status=' . $_GET['post_status'];
	$post_status_q .= '&perm=readable';
}

$query_str = "post_type=page&orderby=menu_order title&what_to_show=posts$post_status_q&posts_per_page=-1&posts_per_archive_page=-1&order=asc";

$query_str = apply_filters('manage_pages_query', $query_str);
wp($query_str);

if ( is_singular() ) {
	wp_enqueue_script( 'admin-comments' );
	wp_enqueue_script( 'jquery-table-hotkeys' );
}

require_once('admin-header.php');

?>

<form class="search-form" action="" method="get">
	<p id="page-search" class="search-box">
		<label class="hidden" for="page-search-input"><?php _e( 'Search Pages' ); ?></label>
		<input type="text" id="page-search-input" name="s" value="<?php the_search_query(); ?>" />
		<input type="submit" value="<?php _e( 'Search Pages' ); ?>" class="button" />
	</p>
</form>

<?php if ( isset($_GET['upd']) && (int) $_GET['upd'] ) { ?>
<div id="message" class="updated fade"><p>
<?php printf( __ngettext( '%d page updated.', '%d pages updated.', $_GET['upd'] ), number_format_i18n( $_GET['upd'] ) );
unset($_GET['upd']);
	
	if ( isset($_GET['skip']) && (int) $_GET['skip'] ) {
		printf( __ngettext( ' %d page not updated. Somebody is editing it.', ' %d pages not updated. Somebody is editing them.', $_GET['skip'] ), number_format_i18n( $_GET['skip'] ) );
		unset($_GET['skip']);
	} ?>
</p></div>
<?php } ?>

<div class="wrap">
<form id="adv-settings" action="" method="get">
<div id="show-settings"><a href="#edit_settings" id="show-settings-link" class="hide-if-no-js"><?php _e('Advanced Options') ?></a>
<a href="#edit_settings" id="hide-settings-link" class="hide-if-js hide-if-no-js"><?php _e('Hide Options') ?></a></div>

<div id="edit-settings" class="hide-if-js hide-if-no-js">
<div id="edit-settings-wrap">
<h5><?php _e('Show on screen') ?></h5>
<div class="metabox-prefs">
<?php manage_columns_prefs('page') ?>
<br class="clear" />
</div></div>
<?php wp_nonce_field( 'hiddencolumns', 'hiddencolumnsnonce', false ); ?>
</div></form>

<h2><?php
// Use $_GET instead of is_ since they can override each other
$h2_search = isset($_GET['s']) && $_GET['s'] ? ' ' . sprintf(__('matching &#8220;%s&#8221;'), wp_specialchars( stripslashes( $_GET['s'] ) ) ) : '';
$h2_author = '';
if ( isset($_GET['author']) && $_GET['author'] ) {
	$author_user = get_userdata( (int) $_GET['author'] );
	$h2_author = ' ' . sprintf(__('by %s'), wp_specialchars( $author_user->display_name ));
}
printf( _c( '%1$s%2$s%3$s (<a href="%4$s">Add New</a>)|You can reorder these: 1: Pages, 2: by {s}, 3: matching {s}' ), $post_status_label, $h2_author, $h2_search, 'page-new.php' );
?></h2>

<form id="posts-filter" action="" method="get">
<ul class="subsubsub">
<?php

$avail_post_stati = get_available_post_statuses('page');

$status_links = array();
$num_posts = wp_count_posts('page', 'readable');
$class = empty($_GET['post_status']) ? ' class="current"' : '';
$status_links[] = "<li><a href=\"edit-pages.php\"$class>".__('All Pages')."</a>";
foreach ( $post_stati as $status => $label ) {
	$class = '';

	if ( !in_array($status, $avail_post_stati) )
		continue;

	if ( isset( $_GET['post_status'] ) && $status == $_GET['post_status'] )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"edit-pages.php?post_status=$status\"$class>" .
	sprintf(__ngettext($label[2][0], $label[2][1], $num_posts->$status), number_format_i18n( $num_posts->$status ) ) . '</a>';
}
echo implode(' |</li>', $status_links) . '</li>';
unset($status_links);
?>
</ul>

<?php if ( isset($_GET['post_status'] ) ) : ?>
<input type="hidden" name="post_status" value="<?php echo attribute_escape($_GET['post_status']) ?>" />
<?php endif;

if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your page has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View page'); ?></a> | <a href="<?php echo get_edit_post_link( $_GET['posted'] ); ?>"><?php _e('Edit page'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif;
?>

<div class="tablenav">

<?php
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
if ( empty($pagenum) )
	$pagenum = 1;
if( ! isset( $per_page ) || $per_page < 0 )
	$per_page = 20;

$num_pages = ceil(count($posts) / $per_page);
$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'total' => $num_pages,
	'current' => $pagenum
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action">
<option value="-1" selected><?php _e('Actions'); ?></option>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-pages'); ?>
</div>

<br class="clear" />
</div>

<br class="clear" />

<?php

$all = !( $h2_search || $post_status_q );

if ($posts) {
?>
<table class="widefat page">
  <thead>
  <tr>
<?php print_column_headers('page'); ?>
  </tr>
  </thead>
  <tbody>
  <?php page_rows($posts, $pagenum, $per_page); ?>
  </tbody>
</table>
</form>

<?php inline_edit_row( 'page' ) ?>

<div id="ajax-response"></div>

<?php
} else {
?>
</form>
<p><?php _e('No pages found.') ?></p>
<?php
} // end if ($posts)
?>

<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>
<br class="clear" />
</div>

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

<table class="widefat" style="margin-top: .5em">
<thead>
  <tr>
    <th scope="col"><?php _e('Comment') ?></th>
    <th scope="col"><?php _e('Date') ?></th>
    <th scope="col"><?php _e('Actions') ?></th>
  </tr>
</thead>
<tbody id="the-comment-list" class="list:comment">
<?php
	foreach ($comments as $comment)
		_wp_comment_row( $comment->comment_ID, 'detail', false, false );
?>
</tbody>
</table>

<?php
wp_comment_reply();
endif; // comments
endif; // posts;

?>

</div>

<?php include('admin-footer.php'); ?>