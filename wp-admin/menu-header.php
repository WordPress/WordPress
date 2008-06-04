<?php
$self = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
$self = preg_replace('|^.*/plugins/|i', '', $self);

global $menu, $submenu, $parent_file; //For when admin-header is included from within a function.

get_admin_page_parent();

// We're going to do this loop three times
?>

<ul id="dashmenu">
<?php
foreach ( $menu as $key => $item ) {
	if ( 3 < $key ) // get each menu item before 3
		continue;
	$class = '';
	// 0 = name, 1 = capability, 2 = file
	if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';

	if ( !empty($submenu[$item[2]]) ) {
		$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
		$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
		if ( file_exists(WP_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}") || !empty($menu_hook))
			echo "\n\t<li><a href='admin.php?page={$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
	} else if ( current_user_can($item[1]) ) {
		$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
		if ( file_exists(WP_PLUGIN_DIR . "/{$item[2]}") || !empty($menu_hook) )
			echo "\n\t<li><a href='admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a></li>";
	}
}
do_action( 'dashmenu' );
?>
</ul>

<ul id="adminmenu">
<?php
foreach ( $menu as $key => $item ) {
	if ( 5 > $key || $key > 25 ) // get each menu item before 3
		continue;

	$class = '';

	// 0 = name, 1 = capability, 2 = file
	if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';

	if ( !empty($submenu[$item[2]]) ) {
		$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
		$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
		if ( file_exists(WP_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}") || !empty($menu_hook))
			echo "\n\t<li><a href='admin.php?page={$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
	} else if ( current_user_can($item[1]) ) {
		$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
		if ( file_exists(WP_PLUGIN_DIR . "/{$item[2]}") || !empty($menu_hook) )
			echo "\n\t<li><a href='admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a></li>";
	}
}

foreach ( $menu as $key => $item ) {
	if ( $key < 41 ) // there is a more efficient way to do this!
		continue;

	$class = '';

	// 0 = name, 1 = capability, 2 = file
	if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';

	if ( !empty($submenu[$item[2]]) ) {
		$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
		$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
		if ( file_exists(WP_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}") || !empty($menu_hook))
			echo "\n\t<li><a href='admin.php?page={$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$submenu[$item[2]][0][2]}'$class>{$item[0]}</a></li>";
	} else if ( current_user_can($item[1]) ) {
		$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
		if ( file_exists(WP_PLUGIN_DIR . "/{$item[2]}") || !empty($menu_hook) )
			echo "\n\t<li><a href='admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
		else
			echo "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a></li>";
	}
}

do_action( 'adminmenu' );
?>
</ul>

<ul id="sidemenu">
<?php
$side_items = array();
foreach ( $menu as $key => $item ) {
	if ( 26 > $key || $key > 40 )
		continue;

	$class = '';

	// 0 = name, 1 = capability, 2 = file
	if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';

	if ( !empty($submenu[$item[2]]) ) {
		$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
		$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
		if ( file_exists(WP_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}") || !empty($menu_hook))
			$side_items[] = "\n\t<li><a href='admin.php?page={$submenu[$item[2]][0][2]}'$class>{$item[0]}</a>";
		else
			$side_items[] = "\n\t<li><a href='{$submenu[$item[2]][0][2]}'$class>{$item[0]}</a>";
	} else if ( current_user_can($item[1]) ) {
		$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
		if ( file_exists(WP_PLUGIN_DIR . "/{$item[2]}") || !empty($menu_hook) )
			$side_items[] = "\n\t<li><a href='admin.php?page={$item[2]}'$class>{$item[0]}</a>";
		else
			$side_items[] = "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a>";
	}
}
echo implode(' </li>', $side_items) . '</li>';
unset($side_items);
do_action( 'sidemenu' );
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

if (file_exists(WP_PLUGIN_DIR . "/{$item[2]}") || ! empty($menu_hook)) {
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