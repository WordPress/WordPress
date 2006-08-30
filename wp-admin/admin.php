<?php
if ( defined('ABSPATH') )
	require_once( ABSPATH . 'wp-config.php');
else
    require_once('../wp-config.php');

if ( get_option('db_version') != $wp_db_version )
	wp_die(sprintf(__("Your database is out-of-date.  Please <a href='%s'>upgrade</a>."), get_option('siteurl') . '/wp-admin/upgrade.php'));
    
require_once(ABSPATH . 'wp-admin/admin-functions.php');
require_once(ABSPATH . 'wp-admin/admin-db.php');
require_once(ABSPATH . WPINC . '/registration.php');

auth_redirect();

nocache_headers();

update_category_cache();

wp_get_current_user();

$posts_per_page = get_option('posts_per_page');
$what_to_show = get_option('what_to_show');
$date_format = get_option('date_format');
$time_format = get_option('time_format');

wp_reset_vars(array('profile', 'redirect', 'redirect_url', 'a', 'popuptitle', 'popupurl', 'text', 'trackback', 'pingback'));

wp_enqueue_script( 'fat' );

$editing = false;

if (isset($_GET['page'])) {
	$plugin_page = stripslashes($_GET['page']);
	$plugin_page = plugin_basename($plugin_page);
}

require(ABSPATH . '/wp-admin/menu.php');

// Handle plugin admin pages.
if (isset($plugin_page)) {
	$page_hook = get_plugin_page_hook($plugin_page, $pagenow);

	if ( $page_hook ) {
		do_action('load-' . $page_hook);
		if (! isset($_GET['noheader']))
			require_once(ABSPATH . '/wp-admin/admin-header.php');

		do_action($page_hook);
	} else {
		if ( validate_file($plugin_page) ) {
			wp_die(__('Invalid plugin page'));
		}

		if (! file_exists(ABSPATH . "wp-content/plugins/$plugin_page"))
			wp_die(sprintf(__('Cannot load %s.'), htmlentities($plugin_page)));

		do_action('load-' . $plugin_page);

		if (! isset($_GET['noheader']))
			require_once(ABSPATH . '/wp-admin/admin-header.php');

		include(ABSPATH . "wp-content/plugins/$plugin_page");
	}

	include(ABSPATH . 'wp-admin/admin-footer.php');

	exit();
} else if (isset($_GET['import'])) {

	$importer = $_GET['import'];

	if ( ! current_user_can('import') )
		wp_die(__('You are not allowed to import.'));

	if ( validate_file($importer) ) {
		wp_die(__('Invalid importer.'));
	}

	if (! file_exists(ABSPATH . "wp-admin/import/$importer.php"))
		wp_die(__('Cannot load importer.'));

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
