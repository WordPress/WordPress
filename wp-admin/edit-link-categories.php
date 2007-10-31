<?php
require_once('admin.php');

$title = __('Categories');
$parent_file = 'link-manager.php';

wp_enqueue_script( 'admin-categories' );
require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');

if (isset($_GET['message'])) : ?>

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
        <th scope="col" width="90" style="text-align: center"><?php _e('Links') ?></th>
        <th colspan="2" style="text-align: center"><?php _e('Action') ?></th>
	</tr>
	</thead>
	<tbody id="the-list" class="list:link-cat">
<?php
$categories = get_terms( 'link_category', 'hide_empty=0' );
if ( $categories ) {
	$output = '';
	foreach ( $categories as $category ) {
		$category = sanitize_term($category, 'link_category', 'display');
		$output .= link_cat_row($category);
	}
	$output = apply_filters('cat_rows', $output);
	echo $output;
	unset($category);
}

?>
	</tbody>
</table>

</div>

<?php if ( current_user_can('manage_categories') ) : ?>
<div class="wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the links in that category. Instead, links that were only assigned to the deleted category are set to the category <strong>%s</strong>.'), get_term_field('name', get_option('default_link_category'), 'link_category')) ?></p>
</div>

<?php include('edit-link-category-form.php'); ?>

<?php endif; ?>

<?php include('admin-footer.php'); ?>
