<?php
if ( defined('ABSPATH') )
	require_once( ABSPATH . 'wp-config.php');
else
    require_once('../wp-config.php');

if ( get_option('db_version') != $wp_db_version )
	die (sprintf(__("Your database is out-of-date.  Please <a href='%s'>upgrade</a>."), get_option('siteurl') . '/wp-admin/upgrade.php'));
    
require_once(ABSPATH . 'wp-admin/admin-functions.php');
require_once(ABSPATH . 'wp-admin/admin-db.php');
require_once(ABSPATH . WPINC . '/registration-functions.php');

auth_redirect();

nocache_headers();

update_category_cache();

wp_get_current_user();

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

wp_enqueue_script( 'fat' );

$editing = false;

require(ABSPATH . '/wp-admin/menu.php');

// Handle plugin admin pages.
if (isset($_GET['page'])) {
	$plugin_page = stripslashes($_GET['page']);
	$plugin_page = plugin_basename($plugin_page);
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
			die(sprintf(__('Cannot load %s.'), htmlentities($plugin_page)));

		if (! isset($_GET['noheader']))
			require_once(ABSPATH . '/wp-admin/admin-header.php');

		include(ABSPATH . "wp-content/plugins/$plugin_page");
	}

	include(ABSPATH . 'wp-admin/admin-footer.php');

	exit();
} else if (isset($_GET['import'])) {

	$importer = $_GET['import'];

	if ( validate_file($importer) ) {
		die(__('Invalid importer.'));
	}

	if (! file_exists(ABSPATH . "wp-admin/import/$importer.php"))
		die(__('Cannot load importer.'));

	include(ABSPATH . "wp-admin/import/$importer.php");

	$parent_file = 'import.php';
	$title = __('Import');

	if (! isset($_GET['noheader']))
		require_once(ABSPATH . 'wp-admin/admin-header.php');

	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

	define('WP_IMPORTING', true);
	kses_init_filters();  // Always filter imported data with kses.

	call_user_func($wp_importers[$importer][2]);

	include(ABSPATH . 'wp-admin/admin-footer.php');

	exit();
}

?>
