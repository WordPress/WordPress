
<ul id="adminmenu">
<?php
$menu = file('./menu.txt');
$continue = true;
foreach ($menu as $item) {
	$class = '';
	$item = trim($item);
	if ('***' == $item) $continue = false;
	if ($continue) {
		$item = explode("\t", $item);
		// 0 = user level, 1 = file, 2 = name
		$self = str_replace('/wp-admin/', '', $PHP_SELF);
		if ((substr($self, -20) == substr($item[1], -20) && empty($parent_file)) || ($parent_file && ($item[1] == $parent_file))) $class = ' class="current"';
		if ($user_level >= $item[0]) {
			if (('upload.php' == $item[1] && get_settings('use_fileupload') && ($user_level >= get_settings('fileupload_minlevel'))
         && (in_array($user_login, explode(' ', $allowed_users)) || (trim(get_settings('fileupload_allowedusers'))==''))) || 'upload.php' != $item[1])
				echo "\n\t<li><a href='{$item[1]}'$class>{$item[2]}</a></li>";
		}
	}
}

?>
	<li><a href="<?php echo get_settings('siteurl') . '/' . get_settings('blogfilename'); ?>" title="View your site">View site &raquo;</a></li>
	<li class="last"><a href="<?php echo get_settings('siteurl')
	 ?>/wp-login.php?action=logout" title="Log out of this account">Logout (<?php echo stripslashes($user_nickname) ?>)</a></li>
</ul>
