<?php
require_once('admin.php');

$title = __('Categories');
$parent_file = 'edit.php';
$list_js = true;

$wpvarstoreset = array('action','cat');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
    $wpvar = $wpvarstoreset[$i];
    if (!isset($$wpvar)) {
        if (empty($_POST["$wpvar"])) {
            if (empty($_GET["$wpvar"])) {
                $$wpvar = '';
            } else {
                $$wpvar = $_GET["$wpvar"];
            }
        } else {
            $$wpvar = $_POST["$wpvar"];
        }
    }
}

switch($action) {

case 'addcat':

	check_admin_referer('add-category');

	if ( !current_user_can('manage_categories') )
		die (__('Cheatin&#8217; uh?'));
	
	wp_insert_category($_POST);

	wp_redirect('categories.php?message=1#addcat');
	exit;
break;

case 'delete':
	$cat_ID = (int) $_GET['cat_ID'];
	check_admin_referer('delete-category_' .  $cat_ID);

	if ( !current_user_can('manage_categories') )
		die (__('Cheatin&#8217; uh?'));

	$cat_name = get_catname($cat_ID);

	// Don't delete the default cats.
	if ( $cat_ID == get_option('default_category') )
		die(sprintf(__("Can't delete the <strong>%s</strong> category: this is the default one"), $cat_name));

	wp_delete_category($cat_ID);

	wp_redirect('categories.php?message=2');
	exit;
break;

case 'edit':

    require_once ('admin-header.php');
    $cat_ID = (int) $_GET['cat_ID'];
    $category = get_category_to_edit($cat_ID);
    ?>

<div class="wrap">
 <h2><?php _e('Edit Category') ?></h2>
 <form name="editcat" action="categories.php" method="post">
	  <?php wp_nonce_field('update-category_' .  $category->cat_ID); ?>
	  <table class="editform" width="100%" cellspacing="2" cellpadding="5">
		<tr>
		  <th width="33%" scope="row"><?php _e('Category name:') ?></th>
		  <td width="67%"><input name="cat_name" type="text" value="<?php echo attribute_escape($category->cat_name); ?>" size="40" /> <input type="hidden" name="action" value="editedcat" />
<input type="hidden" name="cat_ID" value="<?php echo $category->cat_ID ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Category slug:') ?></th>
			<td><input name="category_nicename" type="text" value="<?php echo attribute_escape($category->category_nicename); ?>" size="40" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Category parent:') ?></th>
			<td>        
			<select name='category_parent'>
	  <option value='0' <?php if (!$category->category_parent) echo " selected='selected'"; ?>><?php _e('None') ?></option>
	  <?php wp_dropdown_cats($category->cat_ID, $category->category_parent); ?>
	  </select></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Description:') ?></th>
			<td><textarea name="category_description" rows="5" cols="50" style="width: 97%;"><?php echo wp_specialchars($category->category_description); ?></textarea></td>
		</tr>
		</table>
	  <p class="submit"><input type="submit" name="submit" value="<?php _e('Edit category') ?> &raquo;" /></p>
 </form>
 <p><a href="categories.php"><?php _e('&laquo; Return to category list'); ?></a></p>
</div>
    <?php

break;

case 'editedcat':
	$cat_ID = (int) $_POST['cat_ID'];
	check_admin_referer('update-category_' . $cat_ID);

	if ( !current_user_can('manage_categories') )
		die (__('Cheatin&#8217; uh?'));
	
	wp_update_category($_POST);

	wp_redirect('categories.php?message=3');
	exit;
break;

default:

require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
?>

<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<div class="wrap">
<?php if ( current_user_can('manage_categories') ) : ?>
	<h2><?php printf(__('Categories (<a href="%s">add new</a>)'), '#addcat') ?> </h2>
<?php else : ?>
	<h2><?php _e('Categories') ?> </h2>
<?php endif; ?>
<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th scope="col"><?php _e('ID') ?></th>
        <th scope="col"><?php _e('Name') ?></th>
        <th scope="col"><?php _e('Description') ?></th>
        <th scope="col"><?php _e('# Posts') ?></th>
        <th colspan="2"><?php _e('Action') ?></th>
	</tr>
<?php
cat_rows();
?>
</table>

<div id="ajax-response"></div>

</div>

<?php if ( current_user_can('manage_categories') ) : ?>
<div class="wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete posts from that category, it will just set them back to the default category <strong>%s</strong>.'), get_catname(get_option('default_category'))) ?></p>
</div>

<div class="wrap">
    <h2><?php _e('Add New Category') ?></h2>
    <form name="addcat" id="addcat" action="categories.php" method="post">
    <?php wp_nonce_field('add-category'); ?>
        <p><?php _e('Name:') ?><br />
        <input type="text" name="cat_name" value="" /></p>
        <p><?php _e('Category parent:') ?><br />
        <select name='category_parent' class='postform'>
        <option value='0'><?php _e('None') ?></option>
        <?php wp_dropdown_cats(0); ?>
        </select></p>
        <p><?php _e('Description: (optional)') ?> <br />
        <textarea name="category_description" rows="5" cols="50" style="width: 97%;"></textarea></p>
        <p class="submit"><input type="hidden" name="action" value="addcat" /><input type="submit" name="submit" value="<?php _e('Add Category &raquo;') ?>" /></p>
    </form>
</div>
<?php endif; ?>

<?php
break;
}

include('admin-footer.php');
?>
