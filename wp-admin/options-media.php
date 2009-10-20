<?php
/**
 * Media settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('manage_options') )
	wp_die(__('You do not have sufficient permissions to manage options for this blog.'));

$title = __('Media Settings');
$parent_file = 'options-general.php';

include('admin-header.php');

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form action="options.php" method="post">
<?php settings_fields('media'); ?>

<h3><?php _e('Image sizes') ?></h3>
<p><?php _e('The sizes listed below determine the maximum dimensions in pixels to use when inserting an image into the body of a post.'); ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Thumbnail size') ?></th>
<td>
<label for="thumbnail_size_w"><?php _e('Width'); ?></label>
<input name="thumbnail_size_w" type="text" id="thumbnail_size_w" value="<?php form_option('thumbnail_size_w'); ?>" class="small-text" />
<label for="thumbnail_size_h"><?php _e('Height'); ?></label>
<input name="thumbnail_size_h" type="text" id="thumbnail_size_h" value="<?php form_option('thumbnail_size_h'); ?>" class="small-text" /><br />
<input name="thumbnail_crop" type="checkbox" id="thumbnail_crop" value="1" <?php checked('1', get_option('thumbnail_crop')); ?>/>
<label for="thumbnail_crop"><?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)'); ?></label>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Medium size') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Medium size'); ?></span></legend>
<label for="medium_size_w"><?php _e('Max Width'); ?></label>
<input name="medium_size_w" type="text" id="medium_size_w" value="<?php form_option('medium_size_w'); ?>" class="small-text" />
<label for="medium_size_h"><?php _e('Max Height'); ?></label>
<input name="medium_size_h" type="text" id="medium_size_h" value="<?php form_option('medium_size_h'); ?>" class="small-text" />
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Large size') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Large size'); ?></span></legend>
<label for="large_size_w"><?php _e('Max Width'); ?></label>
<input name="large_size_w" type="text" id="large_size_w" value="<?php form_option('large_size_w'); ?>" class="small-text" />
<label for="large_size_h"><?php _e('Max Height'); ?></label>
<input name="large_size_h" type="text" id="large_size_h" value="<?php form_option('large_size_h'); ?>" class="small-text" />
</fieldset></td>
</tr>

<?php do_settings_fields('media', 'default'); ?>
</table>

<h3><?php _e('Embeds') ?></h3>

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Auto-embeds'); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Attempt to automatically embed all plain text URLs'); ?></span></legend>
<label for="embed_autourls"><input name="embed_autourls" type="checkbox" id="embed_autourls" value="1" <?php checked( '1', get_option('embed_autourls') ); ?>/> <?php _e('Attempt to automatically embed all plain text URLs'); ?></label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('oEmbed'); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php printf( __('Use <a href="%s">oEmbed</a> to assist in rich content embedding'), 'http://codex.wordpress.org/oEmbed' ); ?></span></legend>
<label for="embed_useoembed"><input name="embed_useoembed" type="checkbox" id="embed_useoembed" value="1" <?php checked( '1', get_option('embed_useoembed') ); ?>/> <?php printf( __('Use <a href="%s">oEmbed</a> to allow embedding content from additional websites'), 'http://codex.wordpress.org/oEmbed' ); ?></label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Embed size') ?></th>
<td>
<label for="embed_size_w"><?php _e('Width'); ?></label>
<input name="embed_size_w" type="text" id="embed_size_w" value="<?php form_option('embed_size_w'); ?>" class="small-text" />
<label for="embed_size_h"><?php _e('Height'); ?></label>
<input name="embed_size_h" type="text" id="embed_size_h" value="<?php form_option('embed_size_h'); ?>" class="small-text" />
<?php if ( !empty($content_width) ) echo '<br />' . __("If the width value is left blank, embeds will default to the max width of your theme."); ?>
</td>
</tr>

<?php do_settings_fields('media', 'embeds'); ?>
</table>

<?php do_settings_sections('media'); ?>

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>

</div>

<?php include('./admin-footer.php'); ?>
