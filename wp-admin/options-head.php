<?php

if ($user_level <= 6) {
	die( __('You have do not have sufficient permissions to edit the options for this blog.') );
}

//we need to iterate through the available option groups.
$groups = '';
$option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $tableoptiongroups ORDER BY group_id");
foreach ($option_groups as $option_group) {
	if ($option_group->group_id == $option_group_id) {
		$current_desc = $option_group->group_desc;
		$current_long_desc = $option_group->group_longdesc;
		$groups .= "<li><a class='current' title='{$option_group->group_desc}'>{$option_group->group_name}</a></li>\n";
	} else {
		$groups .= "<li><a href='options.php?option_group_id={$option_group->group_id}' title='{$option_group->group_desc}'>{$option_group->group_name}</a></li>\n";
	}
}

$submenu = '
 <ul id="adminmenu2"> 
 	<li><a href="options-general.php">' . __('General') . '</a></li>
	<li><a href="options-writing.php">' . __('Writing') . '</a></li>
	<li><a href="options-reading.php">' . __('Reading') . '</a></li>
	<li><a href="options-discussion.php">' . __('Discussion') . '</a></li>
	<li><a href="options-misc.php">' . __('Miscellaneous') . '</a></li>
	<li><a href="options-permalink.php">' . __('Permalinks') . '</a></li>';

$sublines = split("\n", $submenu);
$_SERVER['REQUEST_URI'] = str_replace('?updated=true', '', $_SERVER['REQUEST_URI']);
foreach ($sublines as $subline) {
	if (preg_match('/href="([^"]+)"/', $subline, $url)) {
		if (substr($_SERVER['REQUEST_URI'], -8) == substr($url[1], -8)) {
			$subline = str_replace('a hr', 'a class="current" hr', $subline);
			if (str_replace('/wp-admin/', '', $_SERVER["REQUEST_URI"]) == $url[1]) {
				$subline = preg_replace('|href=".*?"|', '', $subline);
			}
		}
	}
	echo $subline."\n";
}
echo  $groups .
    '</ul>';
?>
  

<br clear="all" />

<?php if (isset($updated)) : ?>
<div class="updated"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>