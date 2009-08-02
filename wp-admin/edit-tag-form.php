<?php
/**
 * Edit tag form for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( !current_user_can('manage_categories') )
	wp_die(__('You do not have sufficient permissions to edit tags for this blog.'));

if ( empty($tag_ID) ) { ?>
	<div id="message" class="updated fade"><p><strong><?php _e('A tag was not selected for editing.'); ?></strong></p></div>
<?php
	return;
}

do_action('edit_tag_form_pre', $tag); ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Edit Tag'); ?></h2>
<div id="ajax-response"></div>
<form name="edittag" id="edittag" method="post" action="edit-tags.php" class="validate">
<input type="hidden" name="action" value="editedtag" />
<input type="hidden" name="tag_ID" value="<?php echo esc_attr($tag->term_id) ?>" />
<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy) ?>" />
<?php wp_original_referer_field(true, 'previous'); wp_nonce_field('update-tag_' . $tag_ID); ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name"><?php _e('Tag name') ?></label></th>
			<td><input name="name" id="name" type="text" value="<?php if ( isset( $tag->name ) ) echo esc_attr($tag->name); ?>" size="40" aria-required="true" />
            <p class="description"><?php _e('The name is how the tag appears on your site.'); ?></p></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="slug"><?php _e('Tag slug') ?></label></th>
			<td><input name="slug" id="slug" type="text" value="<?php if ( isset( $tag->slug ) ) echo esc_attr(apply_filters('editable_slug', $tag->slug)); ?>" size="40" />
            <p class="description"><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></p></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php _e('Description') ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="50" style="width: 97%;"><?php echo esc_html($tag->description); ?></textarea><br />
            <span class="description"><?php _e('The description is not prominent by default, however some themes may show it.'); ?></span></td>
		</tr>
		<?php do_action('edit_tag_form_fields', $tag); ?>
	</table>
<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php esc_attr_e('Update Tag'); ?>" /></p>
<?php do_action('edit_tag_form', $tag); ?>
</form>
</div>
