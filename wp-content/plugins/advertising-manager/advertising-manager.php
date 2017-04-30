<?php
/*
Plugin Name: Advertising Manager
Plugin URI: http://github.com/switzer/advertising-manager/wiki
Description: Control and arrange your Advertising and Referral blocks on your Wordpress blog. With Widget and inline post support, integration with all major ad networks.
Author: Scott Switzer
Author URI: http://github.com/switzer
Version: 3.4.22
Text Domain: advman
Domain Path: /languages
*/

// Show notices (DEBUGGING ONLY)
//error_reporting(E_ALL);

// Load all of the definitions that are needed for Advertising Manager
advman_init();
// Run init after the plugins are loaded
add_action('plugins_loaded', 'advman_run', 1);

function advman_init()
{
	global $wp_version;
	global $advman_engine;

	define('ADVMAN_VERSION', '3.4.22');
	define('ADVMAN_PATH', dirname(__FILE__));
	define('ADVMAN_LIB', ADVMAN_PATH . '/lib/Advman');
	define('OX_LIB', ADVMAN_PATH . '/lib/OX');
	define('ADVMAN_URL', get_bloginfo('wpurl') . '/wp-content/plugins/advertising-manager');

	// Get the template path
	$version = (version_compare($wp_version,"2.7-alpha", "<")) ? 'WP2.6' : 'WP2.7';
	define('ADVMAN_TEMPLATE_PATH', ADVMAN_PATH . "/lib/Advman/Template/{$version}");

	// Load the language file
	load_plugin_textdomain('advman', false, 'advertising-manager/languages');
	
	// Load all require files that are needed for Advertising Manager
	require_once(OX_LIB . '/Tools.php');
	require_once(OX_LIB . '/Swifty.php');
	require_once(ADVMAN_LIB . '/Dal.php');

	// Define PHP_INT_MAX for versions of PHP < 4.4.0
	if (!defined('PHP_INT_MAX')) {
	    define ('PHP_INT_MAX', OX_Tools::get_int_max());
	}
	
	// Get an instance of the ad engine
	$advman_engine = new OX_Swifty('Advman_Dal');

	// Next, load admin if needed
	if (is_admin()) {
		require_once(ADVMAN_LIB . '/Admin.php');
	}
	
	// Add widgets
	if (version_compare($wp_version,"2.8-alpha", "<")) {
		include_once(ADVMAN_LIB . '/Widget_Old.php');
		add_action('widgets_init',  array('Advman_Widget', 'init'), 1);
	} else {
		include_once(ADVMAN_LIB . '/Widget.php');
		add_action('widgets_init', create_function('', 'return register_widget("Advman_Widget");'));
	}
}

function advman_run()
{
	global $advman_engine;
	
	// An ad is being requested by its name
	if (!empty($_REQUEST['advman-ad-name'])) {
		$name = OX_Tools::sanitize($_REQUEST['advman-ad-name'], 'key');
		$ad = $advman_engine->selectAd($name);
		if (!empty($ad)) {
			echo $ad->display();
			$advman_engine->incrementStats($ad);
		}
		die(0);
	}
	
	// An ad is being requested by its id
	if (!empty($_REQUEST['advman-ad-id'])) {
		$id = OX_Tools::sanitize($_REQUEST['advman-ad-id'], 'number');
		$ad = $advman_engine->getAd($id);
		if (!empty($ad)) {
			echo $ad->display();
			$advman_engine->incrementStats($ad);
		}
		die(0);
	}

	// Add a filter for displaying an ad in the content
	add_filter('the_content', 'advman_filter_content');
	// Add an action when the wordpress footer displays
	add_action('wp_footer', 'advman_footer');
	// If admin, initialise the Admin functionality	
	if (is_admin()) {
		add_action('admin_menu', array('Advman_Admin','init'));
	}
}



/* This filter parses post content and replaces markup with the correct ad,
<!--adsense#name--> for named ad or <!--adsense--> for default */
function advman_filter_content($content)
{
	$patterns = array(
		'/<!--adsense-->/',
		'/<!--adsense#(.*?)-->/',
		'/<!--am-->/',
		'/<!--am#(.*?)-->/',
		'/\[ad\]/',
		'/\[ad#(.*?)\]/',
	);
	
	return preg_replace_callback($patterns, 'advman_filter_content_callback', $content);
}
	
function advman_filter_content_callback($matches)
{
	global $advman_engine;
	
	$ad = $advman_engine->selectAd($matches[1]);
	if (!empty($ad)) {
		$adHtml = $ad->display();
		$advman_engine->incrementStats($ad);
		return $adHtml;
	}
	return '';
}
	
// Backwards compatibility with adsense-manager
if (!function_exists('adsensem_ad')) {
	function adsensem_ad($name = false)
	{
		return advman_ad($name);
	}
}

function advman_ad($name = false)
{
	global $advman_engine;
	
	$ad = $advman_engine->selectAd($name);
	if (!empty($ad)) {
		echo $ad->display();
		$advman_engine->incrementStats($ad);
	}
}


/**
 * Called when the Wordpress footer displays, and adds a comment in the HTML for debugging purposes
 */
function advman_footer()
{
?>		<!-- Advertising Manager v<?php echo ADVMAN_VERSION;?> (<?php timer_stop(1); ?> seconds.) -->
<?php
}
?>