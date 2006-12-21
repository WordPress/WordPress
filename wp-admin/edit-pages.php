<?php
require_once('admin.php');
$title = __('Pages');
$parent_file = 'edit.php';
wp_enqueue_script( 'listman' );
require_once('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Page Management'); ?></h2>
<p><?php _e('Pages are like posts except they live outside of the normal blog chronology and can be hierarchical. You can use pages to organize and manage any amount of content.'); ?> <a href="page-new.php"><?php _e('Create a new page &raquo;'); ?></a></p>

<form name="searchform" action="" method="get">
	<fieldset>
	<legend><?php _e('Search Pages&hellip;') ?></legend>
	<input type="text" name="s" value="<?php if (isset($_GET['s'])) echo attribute_escape($_GET['s']); ?>" size="17" />
	<input type="submit" name="submit" value="<?php _e('Search') ?>"  />
	</fieldset>
</form>

<?php
wp('post_type=page&orderby=menu_order&what_to_show=posts&posts_per_page=-1&posts_per_archive_page=-1');

if ( $_GET['s'] )
	$all = false;
else
	$all = true;

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
<?php
page_rows(0, 0, $posts, $all);
?>
  </tbody>
</table>

<div id="ajax-response"></div>

<?php
} else {
?>
<p><?php _e('No pages yet.') ?></p>
<?php
} // end if ($posts)
?>

<h3><a href="page-new.php"><?php _e('Create New Page &raquo;'); ?></a></h3>

</div>

<?php include('admin-footer.php'); ?>
