
<ul id="adminmenu">
<?php
// This array constructs the admin menu bar.
//
// Menu item name
// The minimum level the user needs to access the item: between 0 and 10
// The URL of the item's file
$menu = array(
              array(__('Write'), 1, 'post.php'),
              array(__('Edit'), 1, 'edit.php'),
              array(__('Categories'), 3, 'categories.php'),
              array(__('Links'), 5, 'link-manager.php'),
              array(__('Users'), 3, 'users.php'),
              array(__('Options'), 6, 'options-general.php'),
              array(__('Plugins'), 8, 'plugins.php'),
              array(__('Templates'), 4, 'templates.php'),
              array(__('Upload'), 5, 'upload.php'),
              array(__('Profile'), 0, 'profile.php')
);

$self = preg_replace('|.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
foreach ($menu as $item) {
	$class = '';

    // 0 = name, 1 = user_level, 2 = file
    if ((substr($self, -10) == substr($item[2], -10) && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file))) $class = ' class="current"';
    
    if ($user_level >= $item[1]) {
        if (
('upload.php' == $item[2] && 
get_settings('use_fileupload') && 
($user_level >= get_settings('fileupload_minlevel'))
             ) || 'upload.php' != $item[2])
            echo "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a></li>";
    }
}

?>
    <li><a href="<?php echo get_settings('home') . '/' . get_settings('blogfilename'); ?>" title="<?php _e('View your site') ?>"><?php _e('View site') ?> &raquo;</a></li>
	<li class="last"><a href="<?php echo get_settings('siteurl')
	 ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php printf(__('Logout (%s)'), stripslashes($user_nickname)) ?></a></li>
</ul>
