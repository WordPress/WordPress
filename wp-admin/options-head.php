<?php

if ($user_level <= 6) {
	die( __('You have do not have sufficient permissions to edit the options for this blog.') );
}

//we need to iterate through the available option groups.
$groups = '';
$option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $wpdb->optiongroups ORDER BY group_id");
foreach ($option_groups as $option_group) {
	if ($option_group->group_id == $option_group_id) {
		$current_desc = $option_group->group_desc;
		$current_long_desc = $option_group->group_longdesc;
		$groups .= "<li><a class='current' title='{$option_group->group_desc}'>{$option_group->group_name}</a></li>\n";
	} else {
		$groups .= "<li><a href='options.php?option_group_id={$option_group->group_id}' title='{$option_group->group_desc}'>{$option_group->group_name}</a></li>\n";
	}
}
?>

<br clear="all" />

<?php if (isset($updated)) : ?>
<div class="updated"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>