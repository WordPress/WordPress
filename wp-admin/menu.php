
<ul id="adminmenu">
<?php
// This array constructs the admin menu bar.
//
// Menu item name
// The minimum level the user needs to access the item: between 0 and 10
// The URL of the item's file
$menu[0] = array(__('Dashboard'), 0, 'index.php');
$menu[5] = array(__('Write'), 1, 'post.php');
$menu[10] = array(__('Manage'), 1, 'edit.php');
$menu[20] = array(__('Links'), 5, 'link-manager.php');
$menu[25] = array(__('Users'), 3, 'users.php');
$menu[30] = array(__('Options'), 6, 'options-general.php');
$menu[35] = array(__('Plugins'), 8, 'plugins.php');
$menu[40] = array(__('Templates'), 4, 'templates.php');
$menu[45] = array(__('Upload'), get_settings('fileupload_minlevel'), 'upload.php');
ksort($menu); // So other files can plugin

$submenu['edit.php'][5] = array(__('Posts'), 1, 'edit.php');
$submenu['edit.php'][10] = array(__('Pages'), 5, 'edit-pages.php');
$submenu['edit.php'][15] = array(__('Categories'), 1, 'categories.php');
$submenu['edit.php'][20] = array(__('Comments'), 1, 'edit-comments.php');
$awaiting_mod = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");
$submenu['edit.php'][25] = array(sprintf(__("Awaiting Moderation (%s)"), $awaiting_mod), 1, 'moderation.php');

$submenu['link-manager.php'][5] = array(__('Manage Links'), 5, 'link-manager.php');
$submenu['link-manager.php'][10] = array(__('Add Link'), 5, 'link-add.php');
$submenu['link-manager.php'][15] = array(__('Link Categories'), 5, 'link-categories.php');
$submenu['link-manager.php'][20] = array(__('Import Links'), 5, 'link-import.php');

$submenu['users.php'][5] = array(__('Authors &amp; Users'), 5, 'users.php');
$submenu['users.php'][10] = array(__('Your Profile'), 5, 'profile.php');

$submenu['options-general.php'][5] = array(__('General'), 5, 'options-general.php');
$submenu['options-general.php'][10] = array(__('Writing'), 5, 'options-writing.php');
$submenu['options-general.php'][15] = array(__('Reading'), 5, 'options-reading.php');
$submenu['options-general.php'][20] = array(__('Discussion'), 5, 'options-discussion.php');
$submenu['options-general.php'][25] = array(__('Miscellaneous'), 5, 'options-misc.php');
$submenu['options-general.php'][30] = array(__('Permalinks'), 5, 'options-permalink.php');
$submenu['options-general.php'][35] = array(__('Link Manager'), 5, 'options.php?option_group_id=8');

$self = preg_replace('|.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
if (!isset($parent_file)) $parent_file = '';
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
	 ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php printf(__('Logout (%s)'), $user_nickname) ?></a></li>
</ul>

<?php
// Sub-menu
if ( isset($submenu["$parent_file"]) ) :
?>
<ul id="adminmenu2">
<?php 
foreach ($submenu["$parent_file"] as $item) : 
	if ( substr($self, -10) == substr($item[2], -10) ) $class = ' class="current"';
	else $class = '';
	echo "\n\t<li><a href='{$item[2]}'$class>{$item[0]}</a></li>";
endforeach;
?>

</ul>
<?php endif; ?>