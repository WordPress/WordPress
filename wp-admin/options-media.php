<?php
/**
 * Media settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Media Settings');
$parent_file = 'options-general.php';

include('admin-header.php');

?>

<div class="wrap">
<h2><?php echo wp_specialchars( $title ); ?></h2> 

<form action="options.php" method="post">
<input type="hidden" name="action" value="update" />
<?php wp_nonce_field( 'media-options' ); ?>
<input type='hidden' name='option_page' value='media' />
<input type="hidden" name="page_options" value="thumbnail_size_w,thumbnail_size_h,thumbnail_crop,medium_size_w,medium_size_h,image_default_size,image_default_align,image_default_link_type,large_size_w,large_size_h" /> <!-- is this needed anymore TODO -->

<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>

<p><?php _e('The setting below determines where images, documents, and other media files will be linked to when inserted into the body of a post.'); ?></p>

<table class="form-table">
<tr valign="top"> 
<th scope="row"><?php _e('Default media links') ?></th> 
<td><fieldset><legend class="hidden"><?php _e('Default image links') ?></legend> 
<?php 
    $link_types = array('none' => 'None', 'post' => 'Post URL', 'file' => 'File'); 

    $default_link_type = get_option('image_default_link_type');
        if ( empty($default_link_type) )
            $default_link_type = 'file';

    foreach ($link_types as $type => $name) { ?>
        <input type="radio" name="image_default_link_type" id="image_default_link_type_<?php echo $type; ?>" value="<?php echo $type; ?>"<?php echo ($default_link_type == $type ? ' checked="checked"' : ''); ?> />
        <label for="image_default_link_type_<?php echo $type; ?>"><?php _e($name); ?></label>
    <?php 
    } 
?> 
</fieldset></td> 
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
<td><fieldset><legend class="hidden"><?php _e('Medium size') ?></legend>
<label for="medium_size_w"><?php _e('Max Width'); ?></label>
<input name="medium_size_w" type="text" id="medium_size_w" value="<?php form_option('medium_size_w'); ?>" size="6" />
<label for="medium_size_h"><?php _e('Max Height'); ?></label>
<input name="medium_size_h" type="text" id="medium_size_h" value="<?php form_option('medium_size_h'); ?>" size="6" />
</fieldset></td>
</tr>

<tr valign="top"> 
<th scope="row"><?php _e('Large size') ?></th> 
<td><fieldset><legend class="hidden"><?php _e('Large size') ?></legend> 
<label for="large_size_w"><?php _e('Max Width'); ?></label> 
<input name="large_size_w" type="text" id="large_size_w" value="<?php form_option('large_size_w'); ?>" size="6" /> 
<label for="large_size_h"><?php _e('Max Height'); ?></label> 
<input name="large_size_h" type="text" id="large_size_h" value="<?php form_option('large_size_h'); ?>" size="6" /> 
</fieldset></td> 
</tr> 
 
<tr valign="top"> 
<th scope="row"><?php _e('Default image size') ?></th> 
<td><fieldset><legend class="hidden"><?php _e('Default image size') ?></legend> 
<?php 
    $size_names = array('' => 'Auto', 'thumbnail' => 'Thumbnail', 'medium' => 'Medium', 'large' => 'Large', 'full' => 'Full size'); 
    foreach ($size_names as $size => $name) { ?> 
        <input type="radio" name="image_default_size" id="image_default_size_<?php echo $size; ?>" value="<?php echo $size; ?>"<?php echo (get_option('image_default_size') == $size ? ' checked="checked"' : ''); ?> />             
        <label for="image_default_size_<?php echo $size; ?>"><?php _e($name); ?></label> 
    <?php 
	} 
?> 
</fieldset></td> 
</tr> 

<tr valign="top"> 
<th scope="row"><?php _e('Default image alignment') ?></th> 
<td><fieldset><legend class="hidden"><?php _e('Default image alignment') ?></legend> 
<?php 
    $alignments = array('none' => 'None', 'left' => 'Left', 'center' => 'Center', 'right' => 'Right'); 

    $default_align = get_option('image_default_align'); 
	if ( empty($default_align) )
		$default_align = 'none'; 

    foreach ($alignments as $align => $name) { ?> 
        <input type="radio" name="image_default_align" id="image_default_align_<?php echo $align; ?>" value="<?php echo $align; ?>"<?php echo ($default_align == $align ? ' checked="checked"' : ''); ?> />           
        <label for="image_default_align_<?php echo $align; ?>"><?php _e($name); ?></label> 
    <?php 
    } 
?> 
</fieldset></td> 
</tr>
<?php do_settings_fields('media', 'default'); ?>
</table>

<?php do_settings_sections('media'); ?>

<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>

</div>

<?php include('./admin-footer.php'); ?>
