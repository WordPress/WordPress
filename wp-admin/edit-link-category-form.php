<?php
if ( ! empty($cat_ID) ) {
	$heading = __('Edit Category');
	$submit_text = __('Edit Category &raquo;');
	$form = '<form name="editcat" id="editcat" method="post" action="link-category.php">';
	$action = 'editedcat';
	$nonce_action = 'update-link-category_' . $cat_ID;
	do_action('edit_link_category_form_pre', $category);
} else {
	$heading = __('Add Category');
	$submit_text = __('Add Category &raquo;');
	$form = '<form name="addcat" id="addcat" method="post" action="link-category.php">';
	$action = 'addcat';
	$nonce_action = 'add-link-category';
	do_action('add_link_category_form_pre', $category);
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
			<th width="33%" scope="row" valign="top"><label for="name"><?php _e('Category name:') ?></label></th>
			<td width="67%"><input name="name" id="name" type="text" value="<?php echo $category->name; ?>" size="40" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="slug"><?php _e('Category slug:') ?></label></th>
			<td><input name="slug" id="slug" type="text" value="<?php echo $category->slug; ?>" size="40" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="description"><?php _e('Description: (optional)') ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="50" style="width: 97%;"><?php echo $category->description; ?></textarea></td>
		</tr>
	</table>
<p class="submit"><input type="submit" name="submit" value="<?php echo $submit_text ?>" /></p>
<?php do_action('edit_link_category_form', $category); ?>
</form>
</div>
