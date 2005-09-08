<?php
require_once('admin.php');

$title = __('Personal Options');
$parent_file = 'options-personal.php';

include('admin-header.php');
?>

<div class="wrap"> 
<h2><?php _e('Personal Options') ?></h2> 
<form id="personal-options" method="post" action="options-personal-update.php"> 
<fieldset>
<p><?php _e('Personal options are just for you, they don&#8217;t affect other users on blog.'); ?><input type="hidden" name="action" value="update" /> 
<input type="hidden" name="page_options" value="'rich_editing'<?php do_action('personal_option_list'); ?>" /></p>
<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
<tr valign="top"> 
<th width="33%" scope="row"><?php _e('Editing:') ?></th> 
<td><label for="rich_editing">
<input name="rich_editing" type="checkbox" id="rich_editing" value="true" <?php checked('true', get_user_option('rich_editing')); ?> />
<?php _e('Use the visual rich editor when writing') ?></label></td> 
</tr> 
<tr valign="top"> 
<th scope="row"><?php _e('More:') ?></th> 
<td>We should really figure out what else to put here.</td> 
</tr> 
<?php do_action('personal_options_table'); ?>
</table>

</fieldset> 
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Personal Options') ?> &raquo;" />
</p>
</form> 
</div> 
<?php include('admin-footer.php'); ?>