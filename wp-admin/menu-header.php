<ul id="adminmenu">
<?php
$self = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
$self = preg_replace('|^.*/plugins/|i', '', $self);

get_admin_page_parent();

foreach ($menu as $item) {
	$class = '';

	// 0 = name, 1 = capability, 2 = file
	if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';

	if ( !empty($submenu[$item[2]]) ) {
		$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
		$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
		if ( file_exists(ABSPATH . PLUGINDIR . "/{$submenu[$item[2]][0][2]}") || !empty($menu_hook))
			echo "\n\t<li><a href='admin.php?page={$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
	} else if ( current_user_can($item[1]) ) {
		if ( file_exists(ABSPATH . PLUGINDIR . "/{$item[2]}") )
			echo "\n\t<li><a href='admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a></li>";
	}
}

?>
</ul>

<?php
// Sub-menu
if ( isset($submenu["$parent_file"]) ) :
?>
<ul id="submenu">
<?php
foreach ($submenu["$parent_file"] as $item) :
	 if ( !current_user_can($item[1]) )
		 continue;

if ( isset($submenu_file) ) {
	if ( $submenu_file == $item[2] ) $class = ' class="current"';
	else $class = '';
} else if ( (isset($plugin_page) && $plugin_page == $item[2]) || (!isset($plugin_page) && $self == $item[2]) ) $class = ' class="current"';
else $class = '';

$menu_hook = get_plugin_page_hook($item[2], $parent_file);

if (file_exists(ABSPATH . PLUGINDIR . "/{$item[2]}") || ! empty($menu_hook)) {
 	if ( 'admin.php' == $pagenow )
		echo "\n\t<li><a href='admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
	else
		echo "\n\t<li><a href='{$parent_file}?page={$item[2]}'$class>{$item[0]}</a></li>";
 } else {
	echo "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a></li>";
 }
endforeach;
?>

</ul>
<?php
else :
?>
<div id="minisub"></div>
<?php

endif;

do_action('admin_notices');

?>