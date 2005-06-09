<?php
if ( defined('ABSPATH') )
	require_once( ABSPATH . 'wp-config.php');
else
    require_once('../wp-config.php');
    
require_once(ABSPATH . 'wp-admin/admin-functions.php');
auth_redirect();

nocache_headers();

update_category_cache();

get_currentuserinfo();

$posts_per_page = get_settings('posts_per_page');
$what_to_show = get_settings('what_to_show');
$date_format = get_settings('date_format');
$time_format = get_settings('time_format');

$wpvarstoreset = array('profile','redirect','redirect_url','a','popuptitle','popupurl','text', 'trackback', 'pingback');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
    $wpvar = $wpvarstoreset[$i];
    if (!isset($$wpvar)) {
        if (empty($_POST["$wpvar"])) {
            if (empty($_GET["$wpvar"])) {
                $$wpvar = '';
            } else {
                $$wpvar = $_GET["$wpvar"];
            }
        } else {
            $$wpvar = $_POST["$wpvar"];
        }
    }
}

require(ABSPATH . '/wp-admin/menu.php');

// Handle plugin admin pages.
if (isset($_GET['page'])) {
	$plugin_page = plugin_basename($_GET['page']);
	$page_hook = get_plugin_page_hook($plugin_page, $pagenow);

	if ( $page_hook ) {
		if (! isset($_GET['noheader']))
			require_once(ABSPATH . '/wp-admin/admin-header.php');
		
		do_action($page_hook);
	} else {
		if ( validate_file($plugin_page) ) {
			die(__('Invalid plugin page'));
		}
		
		if (! file_exists(ABSPATH . "wp-content/plugins/$plugin_page"))
			die(sprintf(__('Cannot load %s.'), $plugin_page));

		if (! isset($_GET['noheader']))
			require_once(ABSPATH . '/wp-admin/admin-header.php');
		
		include(ABSPATH . "wp-content/plugins/$plugin_page");
	}
	
	include(ABSPATH . 'wp-admin/admin-footer.php');

	exit();
}

?>