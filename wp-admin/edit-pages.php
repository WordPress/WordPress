<?php
require_once('admin.php');
$title = __('Pages');
$parent_file = 'edit.php';
$list_js = true;
require_once('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Page Management'); ?></h2>
<p><?php _e('Pages are like posts except they live outside of the normal blog chronology and can be hierarchical. You can use pages to organize and manage any amount of content.'); ?> <a href="page-new.php"><?php _e('Create a new page'); ?> &raquo;</a></p>

<form name="searchform" action="" method="get"> 
  <fieldset> 
  <legend><?php _e('Search Pages&hellip;') ?></legend>
  <input type="text" name="s" value="<?php if (isset($_GET['s'])) echo wp_specialchars($_GET['s'], 1); ?>" size="17" /> 
  <input type="submit" name="submit" value="<?php _e('Search') ?>"  /> 
  </fieldset>
</form>

<?php

$show_post_type = 'page';

if ( isset($_GET['s']) )
	wp();
else
	$posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static'");

if ($posts) {
?>
<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3"> 
  <tr> 
    <th scope="col"><?php _e('ID') ?></th> 
    <th scope="col"><?php _e('Title') ?></th> 
    <th scope="col"><?php _e('Owner') ?></th>
	<th scope="col"><?php _e('Updated') ?></th>
	<th scope="col"></th> 
    <th scope="col"></th> 
    <th scope="col"></th> 
  </tr> 
<?php
if ( isset($_GET['s']) ) {
foreach ( $posts as $post ) : 
	$class = ('alternate' != $class) ? 'alternate' : ''; ?>
  <tr id='page-<?php echo $id; ?>' class='<?php echo $class; ?>'> 
    <th scope="row"><?php echo $post->ID; ?></th> 
    <td>
      <?php echo $pad; ?><?php the_title() ?> 
    </td> 
    <td><?php the_author() ?></td>
    <td><?php echo mysql2date('Y-m-d g:i a', $post->post_modified); ?></td> 
	<td><a href="<?php the_permalink(); ?>" rel="permalink" class="edit"><?php _e('View'); ?></a></td>
    <td><?php if ( current_user_can('edit_pages') ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td> 
    <td><?php if ( current_user_can('edit_pages') ) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return deleteSomething( 'page', " . $id . ", '" . sprintf(__("You are about to delete the &quot;%s&quot; page.\\n&quot;OK&quot; to delete, &quot;Cancel&quot; to stop."), wp_specialchars(get_the_title('','',0), 1)) . "' );\">" . __('Delete') . "</a>"; } ?></td> 
  </tr>
<?php
endforeach;
} else {
	page_rows();
}
?>
</table> 

<div id="ajax-response"></div>

<?php
} else {
?>
<p><?php _e('No pages yet.') ?></p>
<?php
} // end if ($posts)
?> 

<h3><a href="page-new.php"><?php _e('Create New Page'); ?> &raquo;</a></h3>

</div>

<?php include('admin-footer.php'); ?> 
