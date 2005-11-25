<?php
require_once('admin.php');

$title = __('Miscellaneous Options');
$parent_file = 'options-general.php';

include('admin-header.php');

?>
 
<div class="wrap"> 
<h2><?php _e('Miscellaneous Options') ?></h2> 
<form method="post" action="options.php"> 
<p><input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_settings('use_linksupdate')); ?> />
<label for="use_linksupdate"><?php _e('Track Links&#8217; Update Times') ?></label></p>
<p>
<label><input type="checkbox" name="hack_file" value="1" <?php checked('1', get_settings('hack_file')); ?> /> <?php _e('Use legacy <code>my-hacks.php</code> file support') ?></label>
</p>
<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hack_file,use_linksupdate" /> 
	<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
</p>
</form> 
</div>

<?php include('./admin-footer.php'); ?>