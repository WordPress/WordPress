<?php
if ( ! empty($cat_ID) ) {
	$heading = __('Edit Category');
	$submit_text = __('Edit Category');
	$form = '<form name="editcat" id="editcat" method="post" action="link-category.php" class="validate">';
	$action = 'editedcat';
	$nonce_action = 'update-link-category_' . $cat_ID;
	do_action('edit_link_category_form_pre', $category);
} else {
	$heading = __('Add Category');
	$submit_text = __('Add Category');
	$form = '<form name="addcat" id="addcat" class="add:the-list: validate" method="post" action="link-category.php">';
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
<?php wp_original_referer_field(true, 'previous'); wp_nonce_field($nonce_action); ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name"><?php _e('Category name') ?></label></th>
			<td><input name="name" id="name" type="text" value="<?php echo $category->name; ?>" size="40" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="slug"><?php _e('Category slug') ?></label></th>
			<td><input name="slug" id="slug" type="text" value="<?php echo $category->slug; ?>" size="40" />
            <?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php _e('Description (optional)') ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="50" style="width: 97%;"><?php echo $category->description; ?></textarea></td>
		</tr>
	</table>
<p class="submit"><input type="submit" class="button" name="submit" value="<?php echo $submit_text ?>" /></p>
<?php do_action('edit_link_category_form', $category); ?>
</form>
</div>
