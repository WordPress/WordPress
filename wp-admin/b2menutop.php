<h1 id="wphead"><a href="http://wordpress.org" rel="external">WordPress</a></h1>

<ul id="adminmenu">
<?php
$menu = file('./b2menutop.txt');
$continue = true;
foreach ($menu as $item) {
	$class = '';
	$item = trim($item);
	if ('***' == $item) $continue = false;
	if ($continue) {
		$item = explode("\t", $item);
		// 0 = user level, 1 = file, 2 = name
		if (substr($PHP_SELF, -6) == substr($item[1], -6)) $class = ' id="current"';
		if ($user_level >= $item[0]) echo "\n\t<li><a href='{$item[1]}'$class>{$item[2]}</a></li>";
	}
}

?>

	<li><a href="javascript:profile(<?php echo $user_ID ?>)">My&nbsp;Profile</a></li>
	<li><a href="<?php echo "$siteurl/$blogfilename"; ?>">View&nbsp;site</a></li>
	<li class="last"><a href="<?php echo $siteurl ?>/b2login.php?action=logout">Logout&nbsp;(<?php echo stripslashes($user_nickname) ?>)</a></li>
</ul>

<h2 id="top"><?php echo $title; ?></h2>