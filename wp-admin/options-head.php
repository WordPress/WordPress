<?php

if ($user_level <= 6) {
	die( __('You have do not have sufficient permissions to edit the options for this blog.') );
}
?>

<br clear="all" />

<?php if (isset($updated)) : ?>
<div class="updated"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>