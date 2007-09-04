<?php
require_once('admin.php');
$title = __('Pages');
$parent_file = 'edit.php';
wp_enqueue_script( 'listman' );
require_once('admin-header.php');

$post_stati  = array(	//	array( adj, noun )
			'publish' => array(__('Published'), __('Published pages')),
			'draft'   => array(__('Draft'), __('Draft pages')),
			'private' => array(__('Private'), __('Private pages'))
		);


$post_status_label = __('Pages');
$post_status_q = '';
if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$post_status_q = '&post_status=' . $_GET['post_status'];
}

?>

<div class="wrap">

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

<p><?php _e('Pages are like posts except they live outside of the normal blog chronology and can be hierarchical. You can use pages to organize and manage any amount of content.'); ?> <a href="page-new.php"><?php _e('Create a new page &raquo;'); ?></a></p>

<form name="searchform" id="searchform" action="" method="get">
	<fieldset><legend><?php _e('Search Terms&hellip;') ?></legend>
		<input type="text" name="s" id="s" value="<?php echo attribute_escape( stripslashes( $_GET['s'] ) ); ?>" size="17" />
	</fieldset>


	<fieldset><legend><?php _e('Page Type&hellip;'); ?></legend>
		<select name='post_status'>
			<option<?php selected( @$_GET['post_status'], 0 ); ?> value='0'><?php _e('Any'); ?></option>
<?php	foreach ( $post_stati as $status => $label ) : ?>
			<option<?php selected( @$_GET['post_status'], $status ); ?> value='<?php echo $status; ?>'><?php echo $label[0]; ?></option>
<?php	endforeach; ?>
		</select>
	</fieldset>

<?php $editable_ids = get_editable_user_ids( $user_ID ); if ( $editable_ids && count( $editable_ids ) > 1 ) : ?>

	<fieldset><legend><?php _e('Author&hellip;'); ?></legend>
		<?php wp_dropdown_users( array('include' => $editable_ids, 'show_option_all' => __('Any'), 'name' => 'author', 'selected' => isset($_GET['author']) ? $_GET['author'] : 0) ); ?>
	</fieldset>

<?php endif; ?>

	<input type="submit" id="post-query-submit" value="<?php _e('Filter &#187;'); ?>" class="button" />
</form>

<br style="clear:both;" />

<?php
wp("post_type=page&orderby=menu_order&what_to_show=posts$post_status_q&posts_per_page=-1&posts_per_archive_page=-1&order=asc");

$all = !( $h2_search || $post_status_q );

if ($posts) {
?>
<table class="widefat">
  <thead>
  <tr>
    <th scope="col" style="text-align: center"><?php _e('ID') ?></th>
    <th scope="col"><?php _e('Title') ?></th>
    <th scope="col"><?php _e('Owner') ?></th>
	<th scope="col"><?php _e('Updated') ?></th>
	<th scope="col" colspan="3" style="text-align: center"><?php _e('Action'); ?></th>
  </tr>
  </thead>
  <tbody id="the-list">
<?php page_rows(0, 0, $posts, $all); ?>
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

<h3><a href="page-new.php"><?php _e('Create New Page &raquo;'); ?></a></h3>

</div>

<?php include('admin-footer.php'); ?>
