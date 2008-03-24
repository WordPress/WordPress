<?php
require_once('admin.php');

$title = __('Miscellaneous Settings');
$parent_file = 'options-general.php';

include('admin-header.php');

?>

<div class="wrap">
<h2><?php _e('Miscellaneous Settings') ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<h3><?php _e('Uploading'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Store uploads in this folder'); ?></th>
<td><input name="upload_path" type="text" id="upload_path" class="code" value="<?php echo attribute_escape(str_replace(ABSPATH, '', get_option('upload_path'))); ?>" size="40" />
<br />
<?php _e('Default is <code>wp-content/uploads</code>'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Full URL path to files (optional)'); ?></th>
<td><input name="upload_url_path" type="text" id="upload_url_path" class="code" value="<?php echo attribute_escape( get_option('upload_url_path')); ?>" size="40" />
</td>
</tr>

<tr>
<th scope="row" colspan="2" class="th-full">
<label for="uploads_use_yearmonth_folders">
<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1" <?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
<?php _e('Organize my uploads into month- and year-based folders'); ?>
</label>
</th>
</tr>
</table>

<h3><?php _e('Image sizes') ?></h3>
<p><?php _e('The sizes listed below determine the maximum dimensions to use when inserting an image into the body of a post.'); ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Thumbnail size') ?></th>
<td>
<label for="thumbnail_size_w"><?php _e('Width'); ?></label>
<input name="thumbnail_size_w" type="text" id="thumbnail_size_w" value="<?php form_option('thumbnail_size_w'); ?>" size="6" />
<label for="thumbnail_size_h"><?php _e('Height'); ?></label>
<input name="thumbnail_size_h" type="text" id="thumbnail_size_h" value="<?php form_option('thumbnail_size_h'); ?>" size="6" /><br />
<input name="thumbnail_crop" type="checkbox" id="thumbnail_crop" value="1" <?php checked('1', get_option('thumbnail_crop')); ?>/>
<label for="thumbnail_crop"><?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)'); ?></label>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Medium size') ?></th>
<td>
<label for="medium_size_w"><?php _e('Max Width'); ?></label>
<input name="medium_size_w" type="text" id="medium_size_w" value="<?php form_option('medium_size_w'); ?>" size="6" />
<label for="medium_size_h"><?php _e('Max Height'); ?></label>
<input name="medium_size_h" type="text" id="medium_size_h" value="<?php form_option('medium_size_h'); ?>" size="6" />
</td>
</tr>
</table>



<table class="form-table">

<tr>
<th scope="row" class="th-full">
<label for="use_linksupdate">
<input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_option('use_linksupdate')); ?> />
<?php _e('Track Links&#8217; Update Times') ?>
</label>
</th>
</tr>
<tr>

<th scope="row" class="th-full">
<label for="hack_file">
<input type="checkbox" id="hack_file" name="hack_file" value="1" <?php checked('1', get_option('hack_file')); ?> />
<?php _e('Use legacy <code>my-hacks.php</code> file support') ?>
</label>
</th>
</tr>

</table>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hack_file,use_linksupdate,uploads_use_yearmonth_folders,upload_path,upload_url_path,thumbnail_size_w,thumbnail_size_h,thumbnail_crop,medium_size_w,medium_size_h" />
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" class="button" />
</p>
</form>
</div>

<?php include('./admin-footer.php'); ?>