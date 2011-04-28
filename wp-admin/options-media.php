<?php
/**
 * Media settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('Media Settings');
$parent_file = 'options-general.php';

add_contextual_help($current_screen,
	'<p>' . __('You can set maximum sizes for images inserted into your written content; you can also insert an image as Full Size.') . '</p>' .
	'<p>' . __('The Embed option allows you embed a video, image, or other media content into your content automatically by typing the URL (of the web page where the file lives) on its own line when you create your content.') . '</p>' .
	( is_multisite() ? '' : '<p>' . __('Uploading Options gives you folder and path choices for storing your files in your installation&#8217;s directory.') . '</p>' ) .
	'<p>' . __('You must click the Save Changes button at the bottom of the screen for new settings to take effect.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Settings_Media_Screen" target="_blank">Documentation on Media Settings</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include('./admin-header.php');

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
<td><fieldset><legend class="screen-reader-text"><span><?php _e('When possible, embed the media content from a URL directly onto the page. For example: links to Flickr and YouTube.'); ?></span></legend>
<label for="embed_autourls"><input name="embed_autourls" type="checkbox" id="embed_autourls" value="1" <?php checked( '1', get_option('embed_autourls') ); ?>/> <?php _e('When possible, embed the media content from a URL directly onto the page. For example: links to Flickr and YouTube.'); ?></label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Maximum embed size') ?></th>
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

<?php if ( !is_multisite() ) : ?>
<h3><?php _e('Uploading Files'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="upload_path"><?php _e('Store uploads in this folder'); ?></label></th>
<td><input name="upload_path" type="text" id="upload_path" value="<?php echo esc_attr(get_option('upload_path')); ?>" class="regular-text code" />
<span class="description"><?php _e('Default is <code>wp-content/uploads</code>'); ?></span>
</td>
</tr>

<tr valign="top">
<th scope="row"><label for="upload_url_path"><?php _e('Full URL path to files'); ?></label></th>
<td><input name="upload_url_path" type="text" id="upload_url_path" value="<?php echo esc_attr( get_option('upload_url_path')); ?>" class="regular-text code" />
<span class="description"><?php _e('Configuring this is optional. By default, it should be blank.'); ?></span>
</td>
</tr>

<tr>
<th scope="row" colspan="2" class="th-full">
<label for="uploads_use_yearmonth_folders">
<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1"<?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
<?php _e('Organize my uploads into month- and year-based folders'); ?>
</label>
</th>
</tr>

<?php do_settings_fields('media', 'uploads'); ?>
</table>
<?php endif; ?>

<?php do_settings_sections('media'); ?>

<?php submit_button(); ?>

</form>

</div>

<?php include('./admin-footer.php'); ?>
