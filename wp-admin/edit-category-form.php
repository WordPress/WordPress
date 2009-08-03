<?php
/**
 * Edit category form for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

if ( !current_user_can('manage_categories') )
	wp_die(__('You do not have sufficient permissions to edit categories for this blog.'));

/**
 * @var object
 */
if ( ! isset( $category ) )
	$category = (object) array();

/**
 * @ignore
 * @since 2.7
 * @internal Used to prevent errors in page when no category is being edited.
 *
 * @param object $category
 */
function _fill_empty_category(&$category) {
	if ( ! isset( $category->name ) )
		$category->name = '';

	if ( ! isset( $category->slug ) )
		$category->slug = '';

	if ( ! isset( $category->parent ) )
		$category->parent = '';

	if ( ! isset( $category->description ) )
		$category->description = '';
}

do_action('edit_category_form_pre', $category);

_fill_empty_category($category);
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Edit Category'); ?></h2>
<div id="ajax-response"></div>
<form name="editcat" id="editcat" method="post" action="categories.php" class="validate">
<input type="hidden" name="action" value="editedcat" />
<input type="hidden" name="cat_ID" value="<?php echo esc_attr($category->term_id) ?>" />
<?php wp_original_referer_field(true, 'previous'); wp_nonce_field('update-category_' . $cat_ID); ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="cat_name"><?php _e('Category Name') ?></label></th>
			<td><input name="cat_name" id="cat_name" type="text" value="<?php echo esc_attr($category->name); ?>" size="40" aria-required="true" /><br />
            <span class="description"><?php _e('The name is used to identify the category almost everywhere, for example under the post or in the category widget.'); ?></span></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="category_nicename"><?php _e('Category Slug') ?></label></th>
			<td><input name="category_nicename" id="category_nicename" type="text" value="<?php echo esc_attr(apply_filters('editable_slug', $category->slug)); ?>" size="40" /><br />
            <span class="description"><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></span></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="category_parent"><?php _e('Category Parent') ?></label></th>
			<td>
	  			<?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'category_parent', 'orderby' => 'name', 'selected' => $category->parent, 'hierarchical' => true, 'show_option_none' => __('None'))); ?><br />
                <span class="description"><?php _e('Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.'); ?></span>
	  		</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="category_description"><?php _e('Description') ?></label></th>
			<td><textarea name="category_description" id="category_description" rows="5" cols="50" style="width: 97%;"><?php echo esc_html($category->description); ?></textarea><br />
            <span class="description"><?php _e('The description is not prominent by default, however some themes may show it.'); ?></span></td>
		</tr>
		<?php do_action('edit_category_form_fields', $category); ?>
	</table>
<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php esc_attr_e('Update Category'); ?>" /></p>
<?php do_action('edit_category_form', $category); ?>
</form>
</div>
