<?php
require_once('admin.php');

// Handle bulk deletes
if ( isset($_GET['deleteit']) && isset($_GET['delete']) ) {
	check_admin_referer('bulk-pages');
	foreach( (array) $_GET['delete'] as $post_id_del ) {
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

	$sendback = wp_get_referer();
	if (strpos($sendback, 'page.php') !== false) $sendback = admin_url('page-new.php');
	elseif (strpos($sendback, 'attachments.php') !== false) $sendback = admin_url('attachments.php');
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);

	wp_redirect($sendback);
	exit();
} elseif ( !empty($_GET['_wp_http_referer']) ) {
	 wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	 exit;
}

$title = __('Pages');
$parent_file = 'edit.php';
wp_enqueue_script('admin-forms');

$post_stati  = array(	//	array( adj, noun )
		'publish' => array(__('Published'), __('Published pages'), __ngettext_noop('Published (%s)', 'Published (%s)')),
		'future' => array(__('Scheduled'), __('Scheduled pages'), __ngettext_noop('Scheduled (%s)', 'Scheduled (%s)')),
		'pending' => array(__('Pending Review'), __('Pending pages'), __ngettext_noop('Pending Review (%s)', 'Pending Review (%s)')),
		'draft' => array(__('Draft'), _c('Drafts|manage posts header'), __ngettext_noop('Draft (%s)', 'Drafts (%s)')),
		'private' => array(__('Private'), __('Private pages'), __ngettext_noop('Private (%s)', 'Private (%s)'))
	);

$post_status_label = __('Manage Pages');
$post_status_q = '';
if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$post_status_q = '&post_status=' . $_GET['post_status'];
	$post_status_q .= '&perm=readable';
}

$query_str = "post_type=page&orderby=menu_order title&what_to_show=posts$post_status_q&posts_per_page=-1&posts_per_archive_page=-1&order=asc";

$query_str = apply_filters('manage_pages_query', $query_str);
wp($query_str);

if ( is_singular() )
	wp_enqueue_script( 'admin-comments' );
require_once('admin-header.php');

?>
<div class="wrap">
<form id="posts-filter" action="" method="get">
<h2><?php
// Use $_GET instead of is_ since they can override each other
$h2_search = isset($_GET['s']) && $_GET['s'] ? ' ' . sprintf(__('matching &#8220;%s&#8221;'), wp_specialchars( stripslashes( $_GET['s'] ) ) ) : '';
$h2_author = '';
if ( isset($_GET['author']) && $_GET['author'] ) {
	$author_user = get_userdata( (int) $_GET['author'] );
	$h2_author = ' ' . sprintf(__('by %s'), wp_specialchars( $author_user->display_name ));
}
printf( _c( '%1$s%2$s%3$s|You can reorder these: 1: Pages, 2: by {s}, 3: matching {s}' ), $post_status_label, $h2_author, $h2_search );
?></h2>

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

	if ( $status == $_GET['post_status'] )
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
<?php
endif;
if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your page has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View page'); ?></a> | <a href="page.php?action=edit&amp;post=<?php echo $_GET['posted']; ?>"><?php _e('Edit page'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif;
?>

<p id="post-search">
	<label class="hidden" for="post-search-input"><?php _e( 'Search Pages' ); ?>:</label>
	<input type="text" id="post-search-input" name="s" value="<?php echo attribute_escape(stripslashes($_GET['s'])); ?>" />
	<input type="submit" value="<?php _e( 'Search Pages' ); ?>" class="button" />
</p>

<div class="tablenav">

<?php
$pagenum = absint( $_GET['pagenum'] );
if ( empty($pagenum) )
	$pagenum = 1;
if( !$per_page || $per_page < 0 )
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
<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary delete" />
<?php wp_nonce_field('bulk-pages'); ?>
</div>

<br class="clear" />
</div>

<br class="clear" />

<?php

$all = !( $h2_search || $post_status_q );

if ($posts) {
?>
<table class="widefat">
  <thead>
  <tr>
<?php $posts_columns = wp_manage_pages_columns(); ?>
<?php foreach($posts_columns as $post_column_key => $column_display_name) {
	if ( 'cb' === $post_column_key )
		$class = ' class="check-column"';
	elseif ( 'comments' === $post_column_key )
		$class = ' class="num"';
	else
		$class = '';
?>
	<th scope="col"<?php echo $class; ?>><?php echo $column_display_name; ?></th>
<?php } ?>
  </tr>
  </thead>
  <tbody>
  <?php page_rows($posts, $pagenum, $per_page); ?>
  </tbody>
</table>

</form>

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

endif; // comments
endif; // posts;

?>

</div>

<?php include('admin-footer.php'); ?>
