<ul id="adminmenu">
<?php
$self = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
$self = preg_replace('|^.*/plugins/|i', '', $self);

get_admin_page_parent();

foreach ($menu as $item) {
	$class = '';

	// 0 = name, 1 = user_level, 2 = file
	if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';
    
	if ($user_level >= $item[1]) {
		if ( file_exists(ABSPATH . "wp-content/plugins/{$item[2]}") )
			echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";			
		else
			echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/{$item[2]}'$class>{$item[0]}</a></li>";
	}
}

?>
	<li class="last"><a href="<?php echo get_settings('siteurl')
	 ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php printf(__('Logout (%s)'), $user_nickname) ?></a></li>
</ul>

<?php
// Sub-menu
if ( isset($submenu["$parent_file"]) ) :
?>
<ul id="adminmenu2">
<?php 
foreach ($submenu["$parent_file"] as $item) : 
	 if ($user_level < $item[1]) {
		 continue;
	 }

if ( (isset($plugin_page) && $plugin_page == $item[2]) || (!isset($plugin_page) && substr($self, -10) == substr($item[2], -10)) ) $class = ' class="current"';
else if (isset($submenu_file) && $submenu_file == substr($item[2], -10)) $class = ' class="current"';	 
else $class = '';

if (file_exists(ABSPATH . "wp-content/plugins/{$item[2]}")) {
	$page_hook = get_plugin_page_hook($item[2], $parent_file);
	if ( $page_hook )
		echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/{$parent_file}?page={$item[2]}'$class>{$item[0]}</a></li>";		
	else
		echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/admin.php?page={$item[2]}'$class>{$item[0]}</a></li>";
 } else {
	echo "\n\t<li><a href='" . get_settings('siteurl') . "/wp-admin/{$item[2]}'$class>{$item[0]}</a></li>";
 }
endforeach;
?>

</ul>
<?php endif; ?>