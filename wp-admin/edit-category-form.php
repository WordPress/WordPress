<?php
if ( ! empty($cat_ID) ) {
	$heading = __('Edit Category');
	$submit_text = __('Edit Category');
	$form = '<form name="editcat" id="editcat" method="post" action="categories.php" class="validate">';
	$action = 'editedcat';
	$nonce_action = 'update-category_' . $cat_ID;
	do_action('edit_category_form_pre', $category);
} else {
	$heading = __('Add Category');
	$submit_text = __('Add Category');
	$form = '<form name="addcat" id="addcat" method="post" action="categories.php" class="add:the-list: validate">';
	$action = 'addcat';
	$nonce_action = 'add-category';
	do_action('add_category_form_pre', $category);
}
?>

<div class="wrap">
<h2><?php echo $heading ?></h2>
<div id="ajax-response"></div>
<?php echo $form ?>
<input type="hidden" name="action" value="<?php echo $action ?>" />
<input type="hidden" name="cat_ID" value="<?php echo $category->term_id ?>" />
<?php wp_nonce_field($nonce_action); ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="cat_name"><?php _e('Category Name') ?></label></th>
			<td><input name="cat_name" id="cat_name" type="text" value="<?php echo attribute_escape($category->name); ?>" size="40" /><br />
            <?php _e('The name is used to identify the category almost everywhere, for example under the post or in the category widget.'); ?></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="category_nicename"><?php _e('Category Slug') ?></label></th>
			<td><input name="category_nicename" id="category_nicename" type="text" value="<?php echo attribute_escape($category->slug); ?>" size="40" /><br />
            <?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="category_parent"><?php _e('Category Parent') ?></label></th>
			<td>
	  			<?php wp_dropdown_categories('hide_empty=0&name=category_parent&orderby=name&selected=' . $category->parent . '&hierarchical=1&show_option_none=' . __('None')); ?><br />
                <?php _e('Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.'); ?>
	  		</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="category_description"><?php _e('Description') ?></label></th>
			<td><textarea name="category_description" id="category_description" rows="5" cols="50" style="width: 97%;"><?php echo wp_specialchars($category->description); ?></textarea><br />
            <?php _e('The description is not prominent by default, however some themes may show it.'); ?></td>
		</tr>
	</table>
<p class="submit"><input type="submit" class="button" name="submit" value="<?php echo $submit_text ?>" /></p>
<?php do_action('edit_category_form', $category); ?>
</form>
</div>
