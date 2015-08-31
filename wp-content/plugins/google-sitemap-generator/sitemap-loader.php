<?php

/**
 * Loader class for the Google Sitemap Generator
 *
 * This class takes care of the sitemap plugin and tries to load the different parts as late as possible.
 * On normal requests, only this small class is loaded. When the sitemap needs to be rebuild, the generator itself is loaded.
 * The last stage is the user interface which is loaded when the administration page is requested.
 *
 * @author Arne Brachhold
 * @package sitemap
 */
class GoogleSitemapGeneratorLoader {

	/**
	 * @var string Version of the generator in SVN
	 */
	private static $svnVersion = '$Id: sitemap-loader.php 937300 2014-06-23 18:04:11Z arnee $';


	/**
	 * Enabled the sitemap plugin with registering all required hooks
	 *
	 * @uses add_action Adds actions for admin menu, executing pings and handling robots.txt
	 * @uses add_filter Adds filtes for admin menu icon and contexual help
	 * @uses GoogleSitemapGeneratorLoader::CallShowPingResult() Shows the ping result on request
	 */
	public static function Enable() {

		//Register the sitemap creator to wordpress...
		add_action('admin_menu', array(__CLASS__, 'RegisterAdminPage'));

		//Nice icon for Admin Menu (requires Ozh Admin Drop Down Plugin)
		add_filter('ozh_adminmenu_icon', array(__CLASS__, 'RegisterAdminIcon'));

		//Additional links on the plugin page
		add_filter('plugin_row_meta', array(__CLASS__, 'RegisterPluginLinks'), 10, 2);

		//Listen to ping request
		add_action('sm_ping', array(__CLASS__, 'CallSendPing'), 10, 1);

		//Listen to daily ping
		add_action('sm_ping_daily', array(__CLASS__, 'CallSendPingDaily'), 10, 1);

		//Post is somehow changed (also publish to publish (=edit) is fired)
		add_action('transition_post_status', array(__CLASS__, 'SchedulePingOnStatusChange'), 9999, 3);

		//Robots.txt request
		add_action('do_robots', array(__CLASS__, 'CallDoRobots'), 100, 0);

		//Help topics for context sensitive help
		//add_filter('contextual_help_list', array(__CLASS__, 'CallHtmlShowHelpList'), 9999, 2);

		//Check if the result of a ping request should be shown
		if(!empty($_GET["sm_ping_service"])) {
			self::CallShowPingResult();
		}

		//Fix rewrite rules if not already done on activation hook. This happens on network activation for example.
		if (get_option("sm_rewrite_done", null) != self::$svnVersion) {
			add_action('wp_loaded', array(__CLASS__, 'ActivateRewrite'), 9999, 1);
		}

		//Schedule daily ping
		if (!wp_get_schedule('sm_ping_daily')) {
			wp_schedule_event(time() + (60 * 60), 'daily', 'sm_ping_daily');
		}
	}

	/**
	 * Sets up the query vars and template redirect hooks
	 * @uses GoogleSitemapGeneratorLoader::RegisterQueryVars
	 * @uses GoogleSitemapGeneratorLoader::DoTemplateRedirect
	 * @since 4.0
	 */
	public static function SetupQueryVars() {

		add_filter('query_vars', array(__CLASS__, 'RegisterQueryVars'), 1, 1);

		add_filter('template_redirect', array(__CLASS__, 'DoTemplateRedirect'), 1, 0);

	}

	/**
	 * Register the plugin specific "xml_sitemap" query var
	 *
	 * @since 4.0
	 * @param $vars Array Array of existing query_vars
	 * @return Array An aarray containing the new query vars
	 */
	public static function RegisterQueryVars($vars) {
		array_push($vars, 'xml_sitemap');
		return $vars;
	}

	/**
	 * Registers the plugin specific rewrite rules
	 *
	 * Combined: sitemap(-+([a-zA-Z0-9_-]+))?\.(xml|html)(.gz)?$
	 *
	 * @since 4.0
	 * @param $wpRules Array of existing rewrite rules
	 * @return Array An array containing the new rewrite rules
	 */
	public static function AddRewriteRules($wpRules) {
		$smRules = array(
			'sitemap(-+([a-zA-Z0-9_-]+))?\.xml$' => 'index.php?xml_sitemap=params=$matches[2]',
			'sitemap(-+([a-zA-Z0-9_-]+))?\.xml\.gz$' => 'index.php?xml_sitemap=params=$matches[2];zip=true',
			'sitemap(-+([a-zA-Z0-9_-]+))?\.html$' => 'index.php?xml_sitemap=params=$matches[2];html=true',
			'sitemap(-+([a-zA-Z0-9_-]+))?\.html.gz$' => 'index.php?xml_sitemap=params=$matches[2];html=true;zip=true'
		);
		return array_merge($smRules,$wpRules);
	}

	/**
	 * Returns the rules required for Nginx permalinks
	 *
	 * @return string[]
	 */
	public static function GetNginXRules() {
		return array(
			'rewrite ^/sitemap(-+([a-zA-Z0-9_-]+))?\.xml$ "/index.php?xml_sitemap=params=$2" last;',
			'rewrite ^/sitemap(-+([a-zA-Z0-9_-]+))?\.xml\.gz$ "/index.php?xml_sitemap=params=$2;zip=true" last;',
			'rewrite ^/sitemap(-+([a-zA-Z0-9_-]+))?\.html$ "/index.php?xml_sitemap=params=$2;html=true" last;',
			'rewrite ^/sitemap(-+([a-zA-Z0-9_-]+))?\.html.gz$ "/index.php?xml_sitemap=params=$2;html=true;zip=true" last;'
		);

	}

	/**
	 * Adds the filters for wp rewrite rule adding
	 *
	 * @since 4.0
	 * @uses add_filter()
	 */
	public static function SetupRewriteHooks() {
		add_filter('rewrite_rules_array', array(__CLASS__, 'AddRewriteRules'), 1, 1);
	}

	/**
	 * Flushes the rewrite rules
	 *
	 * @since 4.0
	 * @global $wp_rewrite WP_Rewrite
	 * @uses WP_Rewrite::flush_rules()
	 */
	public static function ActivateRewrite() {
		/** @var $wp_rewrite WP_Rewrite */
		global $wp_rewrite;
		$wp_rewrite->flush_rules(false);
		update_option("sm_rewrite_done", self::$svnVersion);
	}

	/**
	 * Handled the plugin activation on installation
	 *
	 * @uses GoogleSitemapGeneratorLoader::ActivateRewrite
	 * @since 4.0
	 */
	public static function ActivatePlugin() {
		self::SetupRewriteHooks();
		self::ActivateRewrite();

		if(self::LoadPlugin()) {
			$gsg = GoogleSitemapGenerator::GetInstance();
			if($gsg->OldFileExists()) {
				$gsg->DeleteOldFiles();
			}
		}

	}

	/**
	 * Handled the plugin deactivation
	 *
	 * @uses GoogleSitemapGeneratorLoader::ActivateRewrite
	 * @since 4.0
	 */
	public static function DeactivatePlugin() {
		delete_option("sm_rewrite_done");
		wp_clear_scheduled_hook('sm_ping_daily');
	}


	/**
	 * Handles the plugin output on template redirection if the xml_sitemap query var is present.
	 *
	 * @since 4.0
	 */
	public static function DoTemplateRedirect() {
		/** @var $wp_query WP_Query */
		global $wp_query;
		if(!empty($wp_query->query_vars["xml_sitemap"])) {
			$wp_query->is_404 = false;
			$wp_query->is_feed = true;
			self::CallShowSitemap($wp_query->query_vars["xml_sitemap"]);
		}
	}

	/**
	 * Registers the plugin in the admin menu system
	 *
	 * @uses add_options_page()
	 */
	public static function RegisterAdminPage() {
		add_options_page(__('XML-Sitemap Generator', 'sitemap'), __('XML-Sitemap', 'sitemap'), 'administrator', self::GetBaseName(), array(__CLASS__, 'CallHtmlShowOptionsPage'));
	}

	/**
	 * Returns a nice icon for the Ozh Admin Menu if the {@param $hook} equals to the sitemap plugin
	 *
	 * @param string $hook The hook to compare
	 * @return string The path to the icon
	 */
	public static function RegisterAdminIcon($hook) {
		if($hook == self::GetBaseName() && function_exists('plugins_url')) {
			return plugins_url('img/icon-arne.gif', self::GetBaseName());
		}
		return $hook;
	}

	/**
	 * Registers additional links for the sitemap plugin on the WP plugin configuration page
	 *
	 * Registers the links if the $file param equals to the sitemap plugin
	 * @param $links Array An array with the existing links
	 * @param $file string The file to compare to
	 * @return string[]
	 */
	public static function RegisterPluginLinks($links, $file) {
		$base = self::GetBaseName();
		if($file == $base) {
			$links[] = '<a href="options-general.php?page=' . self::GetBaseName() . '">' . __('Settings', 'sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-faq/">' . __('FAQ', 'sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-support/">' . __('Support', 'sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-donate/">' . __('Donate', 'sitemap') . '</a>';
		}
		return $links;
	}

	/**
	 * @param $new_status string The new post status
	 * @param $old_status string The old post status
	 * @param $post WP_Post The post object
	 */
	public static function SchedulePingOnStatusChange($new_status, $old_status, $post ) {
		if($new_status == 'publish') {
			set_transient('sm_ping_post_id', $post->ID, 120);
			wp_schedule_single_event(time() + 5, 'sm_ping');
		}
	}

	/**
	 * Invokes the HtmlShowOptionsPage method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::HtmlShowOptionsPage()
	 */
	public static function CallHtmlShowOptionsPage() {
		if(self::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->HtmlShowOptionsPage();
		}
	}

	/**
	 * Invokes the ShowPingResult method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::ShowPingResult()
	 */
	public static function CallShowPingResult() {
		if(self::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->ShowPingResult();
		}
	}

	/**
	 * Invokes the SendPing method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::SendPing()
	 */
	public static function CallSendPing() {
		if(self::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->SendPing();
		}
	}

	/**
	 * Invokes the SendPingDaily method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::SendPingDaily()
	 */
	public static function CallSendPingDaily()
	{
		if (self::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->SendPingDaily();
		}
	}

	/**
	 * Invokes the ShowSitemap method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::ShowSitemap()
	 */
	public static function CallShowSitemap($options) {
		if(self::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->ShowSitemap($options);
		}
	}

	/**
	 * Invokes the DoRobots method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::DoRobots()
	 */
	public static function CallDoRobots() {
		if(self::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->DoRobots();
		}
	}

	/**
	 * Displays the help links in the upper Help Section of WordPress
	 *
	 * @return Array The new links
	 */
	public static function CallHtmlShowHelpList() {

		$screen = get_current_screen();
		$id = get_plugin_page_hookname(self::GetBaseName(), 'options-general.php');

		if(is_object($screen) &&  $screen->id == $id) {

			/*
			load_plugin_textdomain('sitemap',false,dirname( plugin_basename( __FILE__ ) ) .  '/lang');

			$links = array(
				__('Plugin Homepage', 'sitemap') => 'http://www.arnebrachhold.de/redir/sitemap-help-home/',
				__('My Sitemaps FAQ', 'sitemap') => 'http://www.arnebrachhold.de/redir/sitemap-help-faq/'
			);

			$filterVal[$id] = '';

			$i = 0;
			foreach($links AS $text => $url) {
				$filterVal[$id] .= '<a href="' . $url . '">' . $text . '</a>' . ($i < (count($links) - 1) ? ' | ' : '');
				$i++;
			}

			$screen->add_help_tab( array(
			    'id'      => 'sitemap-links',
			    'title'   => __('My Sitemaps FAQ', 'sitemap'),
			    'content' => '<p>' . __('dsf dsf sd f', 'sitemap') . '</p>',

			));
			*/

		}
		//return $filterVal;
	}


	/**
	 * Loads the actual generator class and tries to raise the memory and time limits if not already done by WP
	 *
	 * @uses GoogleSitemapGenerator::Enable()
	 * @return boolean true if run successfully
	 */
	public static function LoadPlugin() {

		if(!class_exists("GoogleSitemapGenerator")) {

			$mem = abs(intval(@ini_get('memory_limit')));
			if($mem && $mem < 128) {
				@ini_set('memory_limit', '128M');
			}

			$time = abs(intval(@ini_get("max_execution_time")));
			if($time != 0 && $time < 120) {
				@set_time_limit(120);
			}

			$path = trailingslashit(dirname(__FILE__));

			if(!file_exists($path . 'sitemap-core.php')) return false;
			require_once($path . 'sitemap-core.php');
		}

		GoogleSitemapGenerator::Enable();
		return true;
	}

	/**
	 * Returns the plugin basename of the plugin (using __FILE__)
	 *
	 * @return string The plugin basename, "sitemap" for example
	 */
	public static function GetBaseName() {
		return plugin_basename(sm_GetInitFile());
	}

	/**
	 * Returns the name of this loader script, using sm_GetInitFile
	 *
	 * @return string The sm_GetInitFile value
	 */
	public static function GetPluginFile() {
		return sm_GetInitFile();
	}

	/**
	 * Returns the plugin version
	 *
	 * Uses the WP API to get the meta data from the top of this file (comment)
	 *
	 * @return string The version like 3.1.1
	 */
	public static function GetVersion() {
		if(!isset($GLOBALS["sm_version"])) {
			if(!function_exists('get_plugin_data')) {
				if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) {
					require_once(ABSPATH . 'wp-admin/includes/plugin.php');
				}
				else return "0.ERROR";
			}
			$data = get_plugin_data(self::GetPluginFile(), false, false);
			$GLOBALS["sm_version"] = $data['Version'];
		}
		return $GLOBALS["sm_version"];
	}

	public static function GetSvnVersion() {
		return self::$svnVersion;
	}
}

//Enable the plugin for the init hook, but only if WP is loaded. Calling this php file directly will do nothing.
if(defined('ABSPATH') && defined('WPINC')) {
	add_action("init", array("GoogleSitemapGeneratorLoader", "Enable"), 15, 0);
	register_activation_hook(sm_GetInitFile(), array('GoogleSitemapGeneratorLoader', 'ActivatePlugin'));
	register_deactivation_hook(sm_GetInitFile(), array('GoogleSitemapGeneratorLoader', 'DeactivatePlugin'));

	//Set up hooks for adding permalinks, query vars.
	//Don't wait until init with this, since other plugins might flush the rewrite rules in init already...
	GoogleSitemapGeneratorLoader::SetupQueryVars();
	GoogleSitemapGeneratorLoader::SetupRewriteHooks();
}

