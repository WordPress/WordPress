<?php

if ($user_level <= 6) {
	die("You have do not have sufficient permissions to edit the options for this blog.");
}

//we need to iterate through the available option groups.
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

$submenu = <<<END
 <ul id="adminmenu2"> 
 	<li><a href="options-general.php">General</a></li>
	<li><a href="options-writing.php">Writing</a></li>
	<li><a href="options-reading.php">Reading</a></li>
	<li><a href="options-discussion.php">Discussion</a></li>
	<li><a href="options-misc.php">Miscellaneous</a></li>
	<li><a href="options-permalink.php">Permalinks</a></li> 
	$groups
</ul>
END;

$sublines = split("\n", $submenu);
foreach ($sublines as $subline) {
	preg_match('/href="([^"]+)"/', $subline, $url);
	if (substr($_SERVER['REQUEST_URI'], -8) == substr($url[1], -8)) {
		$subline = str_replace('a hr', 'a class="current" hr', $subline);
		if (str_replace('/wp-admin/', '', $_SERVER["REQUEST_URI"]) == $url[1]) {
			$subline = preg_replace('|href=".*?"|', '', $subline);
		}
	}
	echo $subline."\n";
}
?>
  

<br clear="all" />

<?php if ($updated) : ?>
<div class="updated"><p><strong>Options saved.</strong></p></div>
<?php endif; ?>