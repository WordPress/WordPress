<?php
require_once('admin.php');
$title = __('Pages');
$parent_file = 'edit.php';
require_once('admin-header.php');

get_currentuserinfo();
?>

<div class="wrap">
<h2><?php _e('Page Management'); ?></h2>

<?php
if (isset($user_ID) && ('' != intval($user_ID))) {
    $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static' AND post_author = $user_ID");
} else {
    $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static'");
}

if ($posts) {
?>
<table width="100%" cellpadding="3" cellspacing="3"> 
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
$bgcolor = '';
foreach ($posts as $post) : start_wp();
$class = ('alternate' == $class) ? '' : 'alternate';
?> 
  <tr class='<?php echo $class; ?>'> 
    <th scope="row"><?php echo $id ?></th> 
    <td>
      <?php the_title() ?> 
    </td> 
    <td><?php the_author() ?></td>
    <td><?php the_time('Y-m-d g:i a'); ?></td> 
	<td><a href="<?php the_permalink(); ?>" rel="permalink" class="edit"><?php _e('View'); ?></a></td>
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td> 
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return confirm('" . sprintf(__("You are about to delete this post \'%s\'\\n  \'OK\' to delete, \'Cancel\' to stop."), the_title('','',0)) . "')\">" . __('Delete') . "</a>"; } ?></td> 
  </tr> 
<?php endforeach; ?>
</table> 
<?php
} else {
?>
<p><?php _e('No pages yet.') ?></p>
<?php
} // end if ($posts)
?> 
<p><?php _e('Pages are like posts except they live outside of the normal blog chronology. You can use pages to organize and manage any amount of content.'); ?></p>
<h3><a href="page-new.php"><?php _e('Create New Page'); ?> &raquo;</a></h3>
</div> 


<?php include('admin-footer.php'); ?> 
