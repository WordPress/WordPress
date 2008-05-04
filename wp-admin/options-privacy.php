<?php
require_once('./admin.php');

$title = __('Privacy Settings');
$parent_file = 'options-general.php';

include('./admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Privacy Settings') ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Blog Visibility') ?> </th>
<td><fieldset><legend class="hidden"><?php _e('Blog Visibility') ?> </legend>
<p><input id="blog-public" type="radio" name="blog_public" value="1" <?php checked('1', get_option('blog_public')); ?> />
<label for="blog-public"><?php _e('I would like my blog to be visible to everyone, including search engines (like Google, Sphere, Technorati) and archivers');?></label></p>
<p><input id="blog-norobots" type="radio" name="blog_public" value="0" <?php checked('0', get_option('blog_public')); ?> />
<label for="blog-norobots"><?php _e('I would like to block search engines, but allow normal visitors'); ?></label></p>
<?php do_action('blog_privacy_selector'); ?>
</fieldset></td>
</tr>
</table>

<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="blog_public" />
</p>
</form>

</div>

<?php include('./admin-footer.php') ?>
