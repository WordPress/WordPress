<?php
/**
 * Edit link category form for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * @var object
 */
if ( ! isset( $category ) )
	$category = (object) array();

if ( ! empty($cat_ID) ) {
	/**
	 * @var string
	 */
	$heading = '<h2>' . __('Edit Link Category') . '</h2>';
	$submit_text = __('Update Category');
	$form = '<form name="editcat" id="editcat" method="post" action="link-category.php" class="validate">';
	$action = 'editedcat';
	$nonce_action = 'update-link-category_' . $cat_ID;
	do_action('edit_link_category_form_pre', $category);
} else {
	$heading = '<h2>' . __('Add Link Category') . '</h2>';
	$submit_text = __('Add Category');
	$form = '<form name="addcat" id="addcat" class="add:the-list: validate" method="post" action="link-category.php">';
	$action = 'addcat';
	$nonce_action = 'add-link-category';
	do_action('add_link_category_form_pre', $category);
}

/**
 * @ignore
 * @since 2.7
 * @internal Used to prevent errors in page when no category is being edited.
 *
 * @param object $category
 */
function _fill_empty_link_category(&$category) {
	if ( ! isset( $category->name ) )
		$category->name = '';

	if ( ! isset( $category->slug ) )
		$category->slug = '';

	if ( ! isset( $category->description ) )
		$category->description = '';
}

_fill_empty_link_category($category);
?>

<div class="wrap">
<?php screen_icon(); ?>
<?php echo $heading ?>
<div id="ajax-response"></div>
<?php echo $form ?>
<input type="hidden" name="action" value="<?php echo esc_attr($action) ?>" />
<input type="hidden" name="cat_ID" value="<?php echo esc_attr($category->term_id) ?>" />
<?php wp_original_referer_field(true, 'previous'); wp_nonce_field($nonce_action); ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name"><?php _e('Link Category name') ?></label></th>
			<td><input name="name" id="name" type="text" value="<?php echo esc_attr($category->name); ?>" size="40" aria-required="true" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="slug"><?php _e('Link Category slug') ?></label></th>
			<td><input name="slug" id="slug" type="text" value="<?php echo esc_attr(apply_filters('editable_slug', $category->slug)); ?>" size="40" /><br />
            <?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php _e('Description (optional)') ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="50" style="width: 97%;"><?php echo $category->description; ?></textarea></td>
		</tr>
	</table>
<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php echo esc_attr($submit_text) ?>" /></p>
<?php do_action('edit_link_category_form', $category); ?>
</form>
</div>
