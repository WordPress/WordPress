<?php
require_once('admin.php');

$title = __('Categories');
$parent_file = 'edit.php';

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
	if ($user_level < 3)
		die (__('Cheatin&#8217; uh?'));
	
	$cat_name= wp_specialchars($_POST['cat_name']);
	$id_result = $wpdb->get_row("SHOW TABLE STATUS LIKE '$wpdb->categories'");
	$cat_ID = $id_result->Auto_increment;
	$category_nicename = sanitize_title($cat_name, $cat_ID);
	$category_description = $_POST['category_description'];
	$cat = intval($_POST['cat']);
	
	$wpdb->query("INSERT INTO $wpdb->categories (cat_ID, cat_name, category_nicename, category_description, category_parent) VALUES ('0', '$cat_name', '$category_nicename', '$category_description', '$cat')");
	
	header('Location: categories.php?message=1#addcat');
break;

case 'Delete':

    check_admin_referer();

    $cat_ID = intval($_GET["cat_ID"]);
    $cat_name = get_catname($cat_ID);
    $category = $wpdb->get_row("SELECT * FROM $wpdb->categories WHERE cat_ID = '$cat_ID'");
    $cat_parent = $category->category_parent;

    if (1 == $cat_ID)
        die(sprintf(__("Can't delete the <strong>%s</strong> category: this is the default one"), $cat_name));

    if ($user_level < 3)
        die (__('Cheatin&#8217; uh?'));

    $wpdb->query("DELETE FROM $wpdb->categories WHERE cat_ID = '$cat_ID'");
    $wpdb->query("UPDATE $wpdb->categories SET category_parent = '$cat_parent' WHERE category_parent = '$cat_ID'");
    $wpdb->query("UPDATE $wpdb->post2cat SET category_id='1' WHERE category_id='$cat_ID'");

    header('Location: categories.php?message=2');

break;

case 'edit':

    require_once ('admin-header.php');
    $cat_ID = (int) $_GET['cat_ID'];
    $category = $wpdb->get_row("SELECT * FROM $wpdb->categories WHERE cat_ID = '$cat_ID'");
    $cat_name = $category->cat_name;
    ?>

<div class="wrap">
 <h2><?php _e('Edit Category') ?></h2>
 <form name="editcat" action="categories.php" method="post">
	  <table class="editform" width="100%" cellspacing="2" cellpadding="5">
		<tr>
		  <th width="33%" scope="row"><?php _e('Category name:') ?></th>
		  <td width="67%"><input name="cat_name" type="text" value="<?php echo wp_specialchars($cat_name); ?>" size="40" /> <input type="hidden" name="action" value="editedcat" />
<input type="hidden" name="cat_ID" value="<?php echo $cat_ID ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Category slug:') ?></th>
			<td><input name="category_nicename" type="text" value="<?php echo wp_specialchars($category->category_nicename); ?>" size="40" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Category parent:') ?></th>
			<td>        
			<select name='cat'>
	  <option value='0' <?php if (!$category->category_parent) echo " selected='selected'"; ?>><?php _e('None') ?></option>
	  <?php wp_dropdown_cats($category->cat_ID, $category->category_parent); ?>
	  </select></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Description:') ?></th>
			<td><textarea name="category_description" rows="5" cols="50" style="width: 97%;"><?php echo wp_specialchars($category->category_description, 1); ?></textarea></td>
		</tr>
		</table>
	  <p class="submit"><input type="submit" name="submit" value="<?php _e('Edit category') ?> &raquo;" /></p>
 </form>
 <p><a href="categories.php"><?php _e('&laquo; Return to category list'); ?></a></p>
</div>
    <?php

break;

case 'editedcat':
	if ($user_level < 3)
		die (__('Cheatin&#8217; uh?'));
	
	$cat_name = wp_specialchars($_POST['cat_name']);
	$cat_ID = (int) $_POST['cat_ID'];
	$category_nicename = sanitize_title($_POST['category_nicename'], $cat_ID);
	$category_description = $_POST['category_description'];
	
	$wpdb->query("UPDATE $wpdb->categories SET cat_name = '$cat_name', category_nicename = '$category_nicename', category_description = '$category_description', category_parent = '$cat' WHERE cat_ID = '$cat_ID'");

	header('Location: categories.php?message=3');
break;

default:

require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
?>

<?php if (isset($_GET['message'])) : ?>
<div class="updated"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<div class="wrap">
<?php if ( $user_level > 3 ) : ?>
	<h2><?php printf(__('Categories (<a href="%s">add new</a>)'), '#addcat') ?> </h2>
<?php else : ?>
	<h2><?php _e('Categories') ?> </h2>
<?php endif; ?>
<table width="100%" cellpadding="3" cellspacing="3">
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

</div>

<?php if ( $user_level > 3 ) : ?>
<div class="wrap">
    <p><?php printf(__('<strong>Note:</strong><br />
Deleting a category does not delete posts from that category, it will just
set them back to the default category <strong>%s</strong>.'), get_catname(1)) ?>
  </p>
</div>

<div class="wrap">
    <h2><?php _e('Add New Category') ?></h2>
    <form name="addcat" id="addcat" action="categories.php" method="post">
        
        <p><?php _e('Name:') ?><br />
        <input type="text" name="cat_name" value="" /></p>
        <p><?php _e('Category parent:') ?><br />
        <select name='cat' class='postform'>
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