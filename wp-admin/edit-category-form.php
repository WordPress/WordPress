<?php
if ( ! empty($cat_ID) ) {
	$heading = __('Edit Category');
	$submit_text = __('Edit Category &raquo;');
	$form = '<form name="editcat" id="editcat" method="post" action="categories.php">';
	$action = 'editedcat';
	$nonce_action = 'update-category_' . $cat_ID;
	do_action('edit_category_form_pre', $category);
} else {
	$heading = __('Add Category');
	$submit_text = __('Add Category &raquo;');
	$form = '<form name="addcat" id="addcat" method="post" action="categories.php">';
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
	<table class="editform" width="100%" cellspacing="2" cellpadding="5">
		<tr>
			<th width="33%" scope="row" valign="top"><label for="cat_name"><?php _e('Category name:') ?></label></th>
			<td width="67%"><input name="cat_name" id="cat_name" type="text" value="<?php echo attribute_escape($category->name); ?>" size="40" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="category_nicename"><?php _e('Category slug:') ?></label></th>
			<td><input name="category_nicename" id="category_nicename" type="text" value="<?php echo attribute_escape($category->slug); ?>" size="40" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="category_parent"><?php _e('Category parent:') ?></label></th>
			<td>
	  			<?php wp_dropdown_categories('hide_empty=0&name=category_parent&orderby=name&selected=' . $category->parent . '&hierarchical=1&show_option_none=' . __('None')); ?>
	  		</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="category_description"><?php _e('Description: (optional)') ?></label></th>
			<td><textarea name="category_description" id="category_description" rows="5" cols="50" style="width: 97%;"><?php echo wp_specialchars($category->description); ?></textarea></td>
		</tr>
	</table>
<p class="submit"><input type="submit" name="submit" value="<?php echo $submit_text ?>" /></p>
<?php do_action('edit_category_form', $category); ?>
</form>
</div>
