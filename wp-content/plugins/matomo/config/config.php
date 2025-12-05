<?php

if (!defined( 'ABSPATH')) {
	exit; // if accessed directly
}

use Piwik\Cache;
use Piwik\Container\StaticContainer;
use Piwik\Option;
use Piwik\Plugin\API;
use WpMatomo\Capabilities;
use WpMatomo\Paths;
use WpMatomo\Settings;

return array(
	'path.tmp' => function () {
		$paths = new \WpMatomo\Paths();
		return $paths->get_tmp_dir();
	},
	'path.misc.user' => function () {
		$paths = new \WpMatomo\Paths();
		return $paths->get_relative_dir_to_matomo($paths->get_upload_base_dir()) . '/';
	},
	'log.handlers' => array(), // required in the console
	'EnableDbVersionCheck' => false,
	'path.geoip2' => function () {
		$paths = new \WpMatomo\Paths();
		return $paths->get_gloal_upload_dir_if_possible('DBIP-City.mmdb') . '/';
	},
	// we want to avoid the regular monolog logger as it could interfere with other plugins maybe. for now lets use a
	// custom logger
	'Piwik\Log\LoggerInterface' => \Piwik\DI::get('\Piwik\Plugins\WordPress\Logger'),
	// following two entries used by CoreAdminHome.runCronArchiving
	'log.short.format' => '%level% %tag%[%datetime%] %message%',
	'Piwik\Plugins\Monolog\Formatter\LineMessageFormatter' => \Piwik\DI::create('Piwik\Plugins\Monolog\Formatter\LineMessageFormatter')
		->constructor(\Piwik\DI::get('log.short.format')),
	'TagManagerContainerStorageDir' => function () {
		if (defined('MATOMO_TAG_MANAGER_STORAGE_DIR')) {
			return MATOMO_TAG_MANAGER_STORAGE_DIR;
		}

		// the location where we store the generated javascript or json container files
		$paths = new \WpMatomo\Paths();
		return rtrim('/'. $paths->get_relative_dir_to_matomo($paths->get_upload_base_dir().'/', @realpath(MATOMO_ANALYTICS_FILE)), '/');
	},
	'TagManagerContainerWebDir' => function () {
		if (defined('MATOMO_TAG_MANAGER_WEB_DIR')) {
			return MATOMO_TAG_MANAGER_WEB_DIR;
		}

		// the location where we store the generated javascript or json container files
		$paths = new \WpMatomo\Paths();
		return rtrim('/'. matomo_rel_path($paths->get_upload_base_dir() . '/', WP_PLUGIN_DIR . '/matomo/app'), '/');
	},
	'Piwik\Plugins\Login\PasswordVerifier' => \Piwik\DI::autowire('Piwik\Plugins\WordPress\WpPasswordVerifier'),
	'Piwik\Session\SessionAuth' => \Piwik\DI::autowire('Piwik\Plugins\WordPress\SessionAuth'),
	'Piwik\Auth' => \Piwik\DI::autowire('Piwik\Plugins\WordPress\Auth'),
	\Piwik\Config::class => \Piwik\DI::decorate(function ($previous) {

		\Piwik\Plugins\TagManager\TagManager::$enableAutoContainerCreation = false;

		if (defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN) {
			$general = $previous->General;
			$general['force_ssl'] = 1;
			$general['assume_secure_protocol'] = 1;
			$previous->General = $general;
		}

		$database = $previous->database;
		$previous->database = \WpMatomo\Installer::get_db_infos($database);

		$paths = new Paths();
		if ( file_exists( $paths->get_config_ini_path() ) ) {
			$general = $previous->General;

			if (defined('MATOMO_TRIGGER_BROWSER_ARCHIVING')) {
				$general['enable_browser_archiving_triggering'] = (int) MATOMO_TRIGGER_BROWSER_ARCHIVING;
			}

			$matomo_salt_key = Settings::OPTION_PREFIX . 'matomo_salt';
			$matomo_salt = get_option($matomo_salt_key); // needs to be per site!
			if (!$matomo_salt) {
				$matomo_salt = \Piwik\Common::getRandomString(32);
				update_option($matomo_salt_key, $matomo_salt, true);
			}

			$general['salt'] = $matomo_salt;

			if (empty($general['trusted_hosts'])) {
				$general['trusted_hosts'] = array();
			}
			$site_url = site_url();
			if (!in_array($site_url, $general['trusted_hosts'])) {
				$general['trusted_hosts'][] = $site_url;
			}
			$previous->General = $general;

			if (empty($GLOBALS['MATOMO_SWITCH_BLOG_SET_UP'])) {
			    // only execute it once since we might init this several times...
                $GLOBALS['MATOMO_SWITCH_BLOG_SET_UP'] = true;

                add_action('switch_blog', function ($new_blog, $prev_blog) {
                    if ($new_blog == $prev_blog) {
                        return;
                    }
                    // ensure correct path to config is set, ensure to update tables_prefix etc.
                    \WpMatomo\Bootstrap::destroy_bootstrapped_environment();
                }, 10, 2);
            }
		}

		\WpMatomo\AjaxTracker::add_ip_forward_proxy_header_to_config( $previous );

		return $previous;
	}),
	'Piwik\Mail\Transport' => \Piwik\DI::autowire('WpMatomo\Email'),
	'Piwik\Plugins\CustomJsTracker\TrackerUpdater' => \Piwik\DI::decorate(function ($previous) {
		/** @var \Piwik\Plugins\CustomJsTracker\TrackerUpdater $previous */

		$paths = new Paths();
		$dir = $paths->get_matomo_js_upload_path();

		$previous->setToFile($dir);

		return $previous;
	}),
	'diagnostics.optional' => \Piwik\DI::decorate(function ($checks) {
		foreach ($checks as $index => $check) {
			if ($check && is_object($check)) {
				$class_name = get_class($check);
				if ($class_name === 'Piwik\Plugins\Diagnostics\Diagnostic\ForceSSLCheck'
					|| $class_name === 'Piwik\Plugins\Diagnostics\Diagnostic\LoadDataInfileCheck'
					|| $class_name === 'Piwik\Plugins\CustomJsTracker\Diagnostic\TrackerJsCheck'
					|| $class_name === 'Piwik\Plugins\Diagnostics\Diagnostic\RequiredPrivateDirectories' // it doesn't resolve config path correctly as it is outside matomo dir etc
					|| $class_name === 'Piwik\Plugins\Diagnostics\Diagnostic\RecommendedPrivateDirectories' // tmp check doesn't work so far see matomo-org/matomo#18684
					|| $class_name === 'Piwik\Plugins\Diagnostics\Diagnostic\CronArchivingCheck'
					|| $class_name === 'Piwik\Plugins\Diagnostics\Diagnostic\FileIntegrityCheck') {
					$checks[$index] = null;
				}
			}
		}
		return array_values(array_filter($checks));
	}),
	'diagnostics.disabled'  => \Piwik\DI::add([
		\Piwik\DI::get(\Piwik\Plugins\Diagnostics\Diagnostic\PageSpeedCheck::class),
		\Piwik\DI::get(\Piwik\Plugins\Diagnostics\Diagnostic\RecommendedPrivateDirectories::class),
	]),
	'observers.global' => \Piwik\DI::add(array(
		array('FrontController.modifyErrorPage', \Piwik\DI::value(function (&$result, $ex) {
			if (!empty($ex) && is_object($ex) && $ex instanceof \Piwik\Exception\NoWebsiteFoundException) {
				// try to repair itself in case for some reason the site was not yet synced... on next reload it would
				// then work
				$sync = new \WpMatomo\Site\Sync(new Settings());
				$sync->sync_current_site();
			}
			if (!empty($ex)
			    && is_object($ex)
			    && $ex instanceof \Piwik\Exception\NoPrivilegesException
			    && is_user_logged_in()) {
				if (current_user_can(Capabilities::KEY_VIEW)) {
					// some error... it looks like user should by synced but isn't yet
					// could happen eg when in network activated mode the super admin changes permission and another
					// user from a blog wants to access the UI while not all users are synced just yet
					// try to repair itself in case for some reason the user was not yet synced... on next reload it would
					// then work
					$sync = new \WpMatomo\User\Sync();
					$sync->sync_current_users();
				}
			}
		})),
		array('Db.getDatabaseConfig', \Piwik\DI::value(function (&$config) {
			// we don't want to save these and instead detect them on demand.
			// for security reasons etc we don't want to duplicate these values
			include_once plugin_dir_path(MATOMO_ANALYTICS_FILE ) . 'classes/WpMatomo/Db/WordPress.php';
		})),
		array('Tracker.getDatabaseConfig', \Piwik\DI::value(function (&$configDb) {
			// we don't want to save these and instead detect them on demand.
			// for security reasons etc we don't want to duplicate these values
			include_once plugin_dir_path(MATOMO_ANALYTICS_FILE ) . 'classes/WpMatomo/Db/WordPress.php';
		})),
		array('Config.beforeSave', \Piwik\DI::value(function (&$values) {
			// we don't want to save these and instead detect them on demand.
			// for security reasons etc we don't want to duplicate these values
			unset($values['database']['host']);
			unset($values['database']['username']);
			unset($values['database']['password']);
			unset($values['database']['dbname']);
			unset($values['database']['tables_prefix']);
			unset($values['database']['charset']);
			unset($values['Plugins']);
			unset($values['General']['enable_users_admin']);
			unset($values['General']['enable_sites_admin']);
			unset($values['General']['salt']);
		})),
	)),

);
