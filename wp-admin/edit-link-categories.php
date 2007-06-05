<?php
require_once('admin.php');

$title = __('Categories');
$parent_file = 'link-manager.php';

//wp_enqueue_script( 'admin-categories' );  TODO: Fix AJAX
require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');

function link_cat_row($category) {
	global $class;

	if ( current_user_can( 'manage_categories' ) ) {
		$edit = "<a href='link-category.php?action=edit&amp;cat_ID=$category->term_id' class='edit'>".__( 'Edit' )."</a></td>";
		$default_cat_id = (int) get_option( 'default_link_category' );

		if ( $category->term_id != $default_cat_id )
			$edit .= "<td><a href='" . wp_nonce_url( "link-category.php?action=delete&amp;cat_ID=$category->term_id", 'delete-link-category_' . $category->term_id ) . "' onclick=\"return deleteSomething( 'cat', $category->term_id, '" . js_escape(sprintf( __("You are about to delete the category '%s'.\nAll links that were only assigned to this category will be assigned to the '%s' category.\n'OK' to delete, 'Cancel' to stop." ), $category->name, get_term_field( 'name', $default_cat_id,  'link_category' ))) . "' );\" class='delete'>".__( 'Delete' )."</a>";
		else
			$edit .= "<td style='text-align:center'>".__( "Default" );
	} else {
		$edit = '';
	}

	$class = ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || " class='alternate'" == $class ) ? '' : " class='alternate'";

	$category->count = number_format_i18n( $category->count );
	$count = ( $category->count > 0 ) ? "<a href='link-manager.php?cat_id=$category->term_id'>$category->count</a>" : $category->count;
	return "<tr id='cat-$category->term_id'$class>
		<th scope='row' style='text-align: center'>$category->term_id</th>
		<td>" . ( $name_override ? $name_override : $pad . ' ' . $category->name ) . "</td>
		<td>$category->description</td>
		<td align='center'>$count</td>
		<td>$edit</td>\n\t</tr>\n";
}
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
        <th scope="col" width="90" style="text-align: center"><?php _e('Links') ?></th>
        <th colspan="2" style="text-align: center"><?php _e('Action') ?></th>
	</tr>
	</thead>
	<tbody id="the-list">
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
