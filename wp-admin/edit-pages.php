<?php
require_once('admin.php');
$title = __('Pages');
$parent_file = 'edit.php';
wp_enqueue_script( 'wp-lists' );
require_once('admin-header.php');

$post_stati  = array(	//	array( adj, noun )
		'publish' => array(__('Published'), __('Published pages'), __('Published (%s)')),
		'future' => array(__('Scheduled'), __('Scheduled pages'), __('Scheduled (%s)')),
		'pending' => array(__('Pending Review'), __('Pending pages'), __('Pending Review (%s)')),
		'draft' => array(__('Draft'), _c('Drafts|manage posts header'), _c('Draft (%s)|manage posts header')),
		'private' => array(__('Private'), __('Private pages'), __('Private (%s)'))
	);

$post_status_label = __('Manage Pages');
$post_status_q = '';
if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$post_status_q = '&post_status=' . $_GET['post_status'];
}

?>
<script>
/* <![CDATA[ */
jQuery(function($){$('#the-list').wpList();});
/* ]]> */
</script>
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
$num_posts = wp_count_posts('page');
foreach ( $post_stati as $status => $label ) {
	$class = '';

	if ( !in_array($status, $avail_post_stati) )
		continue;
	
	if ( $status == $_GET['post_status'] )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"edit-pages.php?post_status=$status\"$class>" .
	sprintf($label[2], $num_posts->$status) . '</a>';
}
$class = empty($_GET['post_status']) ? ' class="current"' : '';
$status_links[] = "<li><a href=\"edit-pages.php\"$class>All Pages</a>";
echo implode(' |</li>', $status_links) . '</li>';
unset($status_links);
?>
</ul>

<p id="post-search">
	<input type="text" id="post-search-input" name="s" value="<?php echo attribute_escape(stripslashes($_GET['s'])); ?>" />
	<input type="submit" value="<?php _e( 'Search Pages' ); ?>" />
</p>

<br style="clear:both;" />

<div class="tablenav">

<div style="float: left">
<input type="button" value="<?php _e('Delete'); ?>" name="deleteit" />
</div>

<br style="clear:both;" />
</div>
</form>

<br style="clear:both;" />

<?php
$query_str = "post_type=page&orderby=menu_order title&what_to_show=posts$post_status_q&posts_per_page=-1&posts_per_archive_page=-1&order=asc";
$query_str = apply_filters('manage_pages_query', $query_str);
wp($query_str);

$all = !( $h2_search || $post_status_q );

if ($posts) {
?>
<table class="widefat">
  <thead>
  <tr>
<?php $posts_columns = wp_manage_pages_columns(); ?>
<?php foreach($posts_columns as $column_display_name) { ?>
	<th scope="col"><?php echo $column_display_name; ?></th>
<?php } ?>
  </tr>
  </thead>
  <tbody id="the-list" class="list:page">
  <?php page_rows($posts); ?>
  </tbody>
</table>

<div id="ajax-response"></div>

<?php
} else {
?>
<p><?php _e('No pages found.') ?></p>
<?php
} // end if ($posts)
?>

<div class="tablenav">
<br style="clear:both;" />
</div>

</div>

<?php include('admin-footer.php'); ?>
