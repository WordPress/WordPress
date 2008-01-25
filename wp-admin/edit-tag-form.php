<?php
if ( ! empty($tag_ID) ) {
	$heading = __('Edit Tag');
	$submit_text = __('Edit Tag &raquo;');
	$form = '<form name="edittag" id="edittag" method="post" action="edit-tags.php">';
	$action = 'editedtag';
	$nonce_action = 'update-tag_' . $tag_ID;
	do_action('edit_tag_form_pre', $tag);
} else {
	$heading = __('Add Tag');
	$submit_text = __('Add Tag &raquo;');
	$form = '<form name="addtag" id="addtag" method="post" action="edit-tags.php" class="add:the-list:">';
	$action = 'addtag';
	$nonce_action = 'add-tag';
	do_action('add_tag_form_pre', $tag);
}
?>

<div class="wrap">
<h2><?php echo $heading ?></h2>
<div id="ajax-response"></div>
<?php echo $form ?>
<input type="hidden" name="action" value="<?php echo $action ?>" />
<input type="hidden" name="tag_ID" value="<?php echo $tag->term_id ?>" />
<?php wp_nonce_field($nonce_action); ?>
	<table class="editform" width="100%" cellspacing="2" cellpadding="5">
		<tr class="form-field form-required">
			<th width="33%" scope="row" valign="top"><label for="name"><?php _e('Tag name:') ?></label></th>
			<td width="67%"><input name="name" id="name" type="text" value="<?php echo attribute_escape($tag->name); ?>" size="40" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="slug"><?php _e('Tag slug:') ?></label></th>
			<td><input name="slug" id="slug" type="text" value="<?php echo attribute_escape($tag->slug); ?>" size="40" /></td>
		</tr>
	</table>
<p class="submit"><input type="submit" name="submit" value="<?php echo $submit_text ?>" /></p>
<?php do_action('edit_tag_form', $tag); ?>
</form>
</div>
