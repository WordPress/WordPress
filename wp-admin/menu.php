<h1 id="wphead"><a href="http://wordpress.org" rel="external">WordPress</a></h1>

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
		if ((substr($self, -20) == substr($item[1], -20)) || ($parent_file && ($item[1] == $parent_file))) $class = ' class="current"';
		if ($user_level >= $item[0]) echo "\n\t<li><a href='{$item[1]}'$class>{$item[2]}</a></li>";
	}
}

?>

	<li><a href="javascript:profile(<?php echo $user_ID ?>)">My Profile</a></li>
	<li><a href="<?php echo "$siteurl/$blogfilename"; ?>">View site</a></li>
	<li class="last"><a href="<?php echo $siteurl ?>/wp-login.php?action=logout">Logout (<?php echo stripslashes($user_nickname) ?>)</a></li>
</ul>

<h2><?php echo $title; ?></h2>