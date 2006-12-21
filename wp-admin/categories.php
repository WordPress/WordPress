<?php
require_once('admin.php');

$title = __('Categories');
$parent_file = 'edit.php';

wp_reset_vars(array('action', 'cat'));

switch($action) {

case 'addcat':

	check_admin_referer('add-category');

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	if( wp_insert_category($_POST ) ) {
		wp_redirect('categories.php?message=1#addcat');
	} else {
		wp_redirect('categories.php?message=4#addcat');
	}
	exit;
break;

case 'delete':
	$cat_ID = (int) $_GET['cat_ID'];
	check_admin_referer('delete-category_' .  $cat_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	$cat_name = get_catname($cat_ID);

	// Don't delete the default cats.
    if ( $cat_ID == get_option('default_category') )
		wp_die(sprintf(__("Can&8217;t delete the <strong>%s</strong> category: this is the default one"), $cat_name));

    if ( $cat_ID == get_option('default_link_category') )
		wp_die(sprintf(__("Can&8217;t delete the <strong>%s</strong> category: this is the default one for links"), $cat_name));

	wp_delete_category($cat_ID);

	wp_redirect('categories.php?message=2');
	exit;

break;

case 'edit':

	require_once ('admin-header.php');
	$cat_ID = (int) $_GET['cat_ID'];
	$category = get_category_to_edit($cat_ID);
	include('edit-category-form.php');

break;

case 'editedcat':
	$cat_ID = (int) $_POST['cat_ID'];
	check_admin_referer('update-category_' . $cat_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	if ( wp_update_category($_POST) )
		wp_redirect('categories.php?message=3'); 
	else
		wp_redirect('categories.php?message=5'); 

	exit;
break;

default:

wp_enqueue_script( 'admin-categories' );
require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');
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
<table class="widefat">
	<thead>
	<tr>
		<th scope="col" style="text-align: center"><?php _e('ID') ?></th>
        <th scope="col"><?php _e('Name') ?></th>
        <th scope="col"><?php _e('Description') ?></th>
        <th scope="col" width="90" style="text-align: center"><?php _e('Posts') ?></th>
        <th scope="col" width="90" style="text-align: center"><?php _e('Links') ?></th>
        <th colspan="2" style="text-align: center"><?php _e('Action') ?></th>
	</tr>
	</thead>
	<tbody id="the-list">
<?php
cat_rows();
?>
	</tbody>
</table>

</div>

<?php if ( current_user_can('manage_categories') ) : ?>
<div class="wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the posts and links in that category.  Instead, posts in the deleted category are set to the category <strong>%s</strong> and links are set to <strong>%s</strong>.'), get_catname(get_option('default_category')), get_catname(get_option('default_link_category'))) ?></p>
</div>

<?php include('edit-category-form.php'); ?>
<?php endif; ?>

<?php
break;
}

include('admin-footer.php');

?>
