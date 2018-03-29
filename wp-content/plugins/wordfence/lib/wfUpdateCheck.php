<?php

class wfUpdateCheck {

	private $needs_core_update = false;
	private $core_update_version = 0;
	private $plugin_updates = array();
	private $all_plugins = array();
	private $plugin_slugs = array();
	private $theme_updates = array();
	private $api = null;

	public function __construct() {
		$this->api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
	}
	
	public function __sleep() {
		return array('needs_core_update', 'core_update_version', 'plugin_updates', 'all_plugins', 'plugin_slugs', 'theme_updates');
	}
	
	public function __wakeup() {
		$this->api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
	}
	
	/**
	 * @return bool
	 */
	public function needsAnyUpdates() {
		return $this->needsCoreUpdate() || count($this->getPluginUpdates()) > 0 || count($this->getThemeUpdates()) > 0;
	}

	/**
	 * Check for any core, plugin or theme updates.
	 *
	 * @return $this
	 */
	public function checkAllUpdates($useCachedValued = true) {
		return $this->checkCoreUpdates($useCachedValued)
			->checkPluginUpdates($useCachedValued)
			->checkThemeUpdates($useCachedValued);
	}

	/**
	 * Check if there is an update to the WordPress core.
	 *
	 * @return $this
	 */
	public function checkCoreUpdates($useCachedValued = true) {
		$this->needs_core_update = false;

		if (!function_exists('wp_version_check')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		if (!function_exists('get_preferred_from_update_core')) {
			require_once(ABSPATH . 'wp-admin/includes/update.php');
		}
		
		include( ABSPATH . WPINC . '/version.php' ); //defines $wp_version
		
		$update_core = get_preferred_from_update_core();
		if ($useCachedValued && isset($update_core->last_checked) && isset($update_core->version_checked) && 12 * HOUR_IN_SECONDS > (time() - $update_core->last_checked) && $update_core->version_checked == $wp_version) { //Duplicate of _maybe_update_core, which is a private call
			//Do nothing, use cached value
		}
		else {
			wp_version_check();
			$update_core = get_preferred_from_update_core();
		}

		if (isset($update_core->response) && $update_core->response == 'upgrade') {
			$this->needs_core_update = true;
			$this->core_update_version = $update_core->current;
		}

		return $this;
	}

	/**
	 * Check if any plugins need an update.
	 *
	 * @return $this
	 */
	public function checkPluginUpdates($useCachedValued = true) {
		$this->plugin_updates = array();

		if (!function_exists('wp_update_plugins')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}

		if (!function_exists('plugins_api')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin-install.php');
		}
		
		$update_plugins = get_site_transient('update_plugins');
		if ($useCachedValued && isset($update_plugins->last_checked) && 12 * HOUR_IN_SECONDS > (time() - $update_plugins->last_checked)) { //Duplicate of _maybe_update_plugins, which is a private call
			//Do nothing, use cached value
		}
		else {
			wp_update_plugins();
			$update_plugins = get_site_transient('update_plugins');
		}
		
		//Get the full plugin list
		if (!function_exists('get_plugins')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		}
		$installedPlugins = get_plugins();

		if ($update_plugins && !empty($update_plugins->response)) {
			foreach ($update_plugins->response as $plugin => $vals) {
				if (!function_exists('get_plugin_data')) {
					require_once ABSPATH . '/wp-admin/includes/plugin.php';
				}
				
				$pluginFile = wfUtils::getPluginBaseDir() . $plugin;
				if (!file_exists($pluginFile)) { //Plugin has been removed since the update status was pulled
					unset($installedPlugins[$plugin]);
					continue;
				}
				
				$valsArray = (array) $vals;
				
				$slug = (isset($valsArray['slug']) ? $valsArray['slug'] : null);
				if ($slug === null) { //Plugin may have been removed from the repo or was never in it so guess
					if (preg_match('/^([^\/]+)\//', $pluginFile, $matches)) {
						$slug = $matches[1];
					}
					else if (preg_match('/^([^\/.]+)\.php$/', $pluginFile, $matches)) {
						$slug = $matches[1];
					}
				}
				
				$data = get_plugin_data($pluginFile);
				$data['pluginFile'] = $pluginFile;
				$data['newVersion'] = (isset($valsArray['new_version']) ? $valsArray['new_version'] : 'Unknown');
				$data['slug'] = $slug;
				$data['wpURL'] = (isset($valsArray['url']) ? rtrim($valsArray['url'], '/') : null);

				//Check the vulnerability database
				if ($slug !== null && isset($data['Version'])) {
					$status = $this->isPluginVulnerable($slug, $data['Version']);
					$data['vulnerable'] = !!$status;
					if (is_string($status)) {
						$data['vulnerabilityLink'] = $status;
					}
				}
				else {
					$data['vulnerable'] = false;
				}
				
				if ($slug !== null) {
					$this->plugin_slugs[] = $slug;
					$this->all_plugins[$slug] = $data;
				}

				$this->plugin_updates[] = $data;
				unset($installedPlugins[$plugin]);
			}
		}
		
		//We have to grab the slugs from the update response because no built-in function exists to return the true slug from the local files
		if ($update_plugins && !empty($update_plugins->no_update)) {
			foreach ($update_plugins->no_update as $plugin => $vals) {
				if (!function_exists('get_plugin_data')) {
					require_once ABSPATH . '/wp-admin/includes/plugin.php';
				}
				
				$pluginFile = wfUtils::getPluginBaseDir() . $plugin;
				if (!file_exists($pluginFile)) { //Plugin has been removed since the update status was pulled
					unset($installedPlugins[$plugin]);
					continue;
				}
				
				$valsArray = (array) $vals;
				
				$data = get_plugin_data($pluginFile);
				$data['pluginFile'] = $pluginFile;
				$data['slug'] = (isset($valsArray['slug']) ? $valsArray['slug'] : null);
				$data['wpURL'] = (isset($valsArray['url']) ? rtrim($valsArray['url'], '/') : null);
				
				//Check the vulnerability database
				if (isset($valsArray['slug']) && isset($data['Version'])) {
					$status = $this->isPluginVulnerable($valsArray['slug'], $data['Version']);
					$data['vulnerable'] = !!$status;
					if (is_string($status)) {
						$data['vulnerabilityLink'] = $status;
					}
				}
				else {
					$data['vulnerable'] = false;
				}
				
				if (isset($valsArray['slug'])) {
					$this->plugin_slugs[] = $valsArray['slug'];
					$this->all_plugins[$valsArray['slug']] = $data;
				}
				
				unset($installedPlugins[$plugin]);
			}	
		}
		
		//Get the remaining plugins (not in the wordpress.org repo for whatever reason)
		foreach ($installedPlugins as $plugin => $data) {
			$pluginFile = wfUtils::getPluginBaseDir() . $plugin;
			if (!file_exists($pluginFile)) { //Plugin has been removed since the list was generated
				continue;
			}
			
			$data = get_plugin_data($pluginFile);
			
			$slug = null;
			if (preg_match('/^([^\/]+)\//', $plugin, $matches)) {
				$slug = $matches[1];
			}
			else if (preg_match('/^([^\/.]+)\.php$/', $plugin, $matches)) {
				$slug = $matches[1];
			}
			
			if ($slug !== null) {
				$this->plugin_slugs[] = $slug;
				$this->all_plugins[$slug] = $data;
			}
		}

		return $this;
	}

	/**
	 * Check if any themes need an update.
	 *
	 * @return $this
	 */
	public function checkThemeUpdates($useCachedValued = true) {
		$this->theme_updates = array();

		if (!function_exists('wp_update_themes')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		
		$update_themes = get_site_transient('update_themes');
		if ($useCachedValued && isset($update_themes->last_checked) && 12 * HOUR_IN_SECONDS > (time() - $update_themes->last_checked)) { //Duplicate of _maybe_update_themes, which is a private call
			//Do nothing, use cached value
		}
		else {
			wp_update_themes();
			$update_themes = get_site_transient('update_themes');
		}

		if ($update_themes && (!empty($update_themes->response))) {
			if (!function_exists('wp_get_themes')) {
				require_once ABSPATH . '/wp-includes/theme.php';
			}
			$themes = wp_get_themes();
			foreach ($update_themes->response as $theme => $vals) {
				foreach ($themes as $name => $themeData) {
					if (strtolower($name) == $theme) {
						$vulnerable = false;
						if (isset($themeData['Version'])) {
							$vulnerable = $this->isThemeVulnerable($theme, $themeData['Version']);
						}
						
						$this->theme_updates[] = array(
							'newVersion' => (isset($vals['new_version']) ? $vals['new_version'] : 'Unknown'),
							'package'    => (isset($vals['package']) ? $vals['package'] : null),
							'URL'        => (isset($vals['url']) ? $vals['url'] : null),
							'Name'       => $themeData['Name'],
							'name'       => $themeData['Name'],
							'version'    => $themeData['Version'],
							'vulnerable' => $vulnerable
						);
					}
				}
			}
		}
		return $this;
	}
	
	public function checkAllVulnerabilities() {
		$this->checkPluginVulnerabilities();
		$this->checkThemeVulnerabilities();
	}
	
	public function checkPluginVulnerabilities() {
		if (!function_exists('wp_update_plugins')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		
		if (!function_exists('plugins_api')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin-install.php');
		}
		
		$vulnerabilities = array();
		
		//Get the full plugin list
		if (!function_exists('get_plugins')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		}
		$installedPlugins = get_plugins();
		
		//Get the info for plugins on wordpress.org
		$this->checkPluginUpdates();
		$update_plugins = get_site_transient('update_plugins');
		if ($update_plugins) {
			if (!function_exists('get_plugin_data'))
			{
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			if (!empty($update_plugins->response)) {
				foreach ($update_plugins->response as $plugin => $vals) {
					$pluginFile = wfUtils::getPluginBaseDir() . $plugin;
					if (!file_exists($pluginFile)) { //Plugin has been removed since the update status was pulled
						unset($installedPlugins[$plugin]);
						continue;
					}
					
					$valsArray = (array) $vals;
					$data = get_plugin_data($pluginFile);
					
					$slug = (isset($valsArray['slug']) ? $valsArray['slug'] : null);
					if ($slug === null) { //Plugin may have been removed from the repo or was never in it so guess
						if (preg_match('/^([^\/]+)\//', $plugin, $matches)) {
							$slug = $matches[1];
						}
						else if (preg_match('/^([^\/.]+)\.php$/', $plugin, $matches)) {
							$slug = $matches[1];
						}
					}
					
					$record = array();
					$record['slug'] = $slug;
					$record['toVersion'] = (isset($valsArray['new_version']) ? $valsArray['new_version'] : 'Unknown');
					$record['fromVersion'] = (isset($data['Version']) ? $data['Version'] : 'Unknown');
					$record['vulnerable'] = false;
					$vulnerabilities[] = $record;
					
					unset($installedPlugins[$plugin]);
				}
			}
			
			if (!empty($update_plugins->no_update)) {
				foreach ($update_plugins->no_update as $plugin => $vals) {
					$pluginFile = wfUtils::getPluginBaseDir() . $plugin;
					if (!file_exists($pluginFile)) { //Plugin has been removed since the update status was pulled
						unset($installedPlugins[$plugin]);
						continue;
					}
					
					$valsArray = (array) $vals;
					$data = get_plugin_data($pluginFile);
					
					$slug = (isset($valsArray['slug']) ? $valsArray['slug'] : null);
					if ($slug === null) { //Plugin may have been removed from the repo or was never in it so guess
						if (preg_match('/^([^\/]+)\//', $plugin, $matches)) {
							$slug = $matches[1];
						}
						else if (preg_match('/^([^\/.]+)\.php$/', $plugin, $matches)) {
							$slug = $matches[1];
						}
					}
					
					$record = array();
					$record['slug'] = $slug;
					$record['fromVersion'] = (isset($data['Version']) ? $data['Version'] : 'Unknown');
					$record['vulnerable'] = false;
					$vulnerabilities[] = $record;
					
					unset($installedPlugins[$plugin]);
				}
			}
		}
		
		//Get the remaining plugins (not in the wordpress.org repo for whatever reason)
		foreach ($installedPlugins as $plugin => $data) {
			$pluginFile = wfUtils::getPluginBaseDir() . $plugin;
			if (!file_exists($pluginFile)) { //Plugin has been removed since the update status was pulled
				continue;
			}
			
			$data = get_plugin_data($pluginFile);
			
			$slug = null;
			if (preg_match('/^([^\/]+)\//', $plugin, $matches)) {
				$slug = $matches[1];
			}
			else if (preg_match('/^([^\/.]+)\.php$/', $plugin, $matches)) {
				$slug = $matches[1];
			}
			
			$record = array();
			$record['slug'] = $slug;
			$record['fromVersion'] = (isset($data['Version']) ? $data['Version'] : 'Unknown');
			$record['vulnerable'] = false;
			$vulnerabilities[] = $record;
		}
		
		if (count($vulnerabilities) > 0) {
			try {
				$result = $this->api->call('plugin_vulnerability_check', array(), array(
					'plugins' => json_encode($vulnerabilities),
				));
				
				foreach ($vulnerabilities as &$v) {
					$vulnerableList = $result['vulnerable'];
					foreach ($vulnerableList as $r) {
						if ($r['slug'] == $v['slug']) {
							$v['vulnerable'] = !!$r['vulnerable'];
							if (isset($r['link'])) {
								$v['link'] = $r['link'];
							}
							break;
						}
					}
				}
			}
			catch (Exception $e) {
				//Do nothing
			}
			
			wfConfig::set_ser('vulnerabilities_plugin', $vulnerabilities);
		}
	}
	
	public function checkThemeVulnerabilities() {
		if (!function_exists('wp_update_themes')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		
		if (!function_exists('plugins_api')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin-install.php');
		}
		
		$this->checkThemeUpdates();
		$update_themes = get_site_transient('update_themes');
		
		$vulnerabilities = array();
		if ($update_themes && !empty($update_themes->response)) {
			if (!function_exists('get_plugin_data'))
			{
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			foreach ($update_themes->response as $themeSlug => $vals) {
				
				$valsArray = (array) $vals;
				$theme = wp_get_theme($themeSlug);
				
				$record = array();
				$record['slug'] = $themeSlug;
				$record['toVersion'] = (isset($valsArray['new_version']) ? $valsArray['new_version'] : 'Unknown');
				$record['fromVersion'] = $theme->version;
				$record['vulnerable'] = false;
				$vulnerabilities[] = $record;
			}
			
			try {
				$result = $this->api->call('theme_vulnerability_check', array(), array(
					'themes' => json_encode($vulnerabilities),
				));
				
				foreach ($vulnerabilities as &$v) {
					$vulnerableList = $result['vulnerable'];
					foreach ($vulnerableList as $r) {
						if ($r['slug'] == $v['slug']) {
							$v['vulnerable'] = !!$r['vulnerable'];
							break;
						}
					}
				}
			}
			catch (Exception $e) {
				//Do nothing
			}
			
			wfConfig::set_ser('vulnerabilities_theme', $vulnerabilities);
		}
	}
	
	public function isPluginVulnerable($slug, $version) {
		return $this->_isSlugVulnerable('vulnerabilities_plugin', $slug, $version);
	}
	
	public function isThemeVulnerable($slug, $version) {
		return $this->_isSlugVulnerable('vulnerabilities_theme', $slug, $version);
	}
	
	private function _isSlugVulnerable($vulnerabilitiesKey, $slug, $version) {
		$vulnerabilities = wfConfig::get_ser($vulnerabilitiesKey, array());
		foreach ($vulnerabilities as $v) {
			if ($v['slug'] == $slug) {
				if ($v['fromVersion'] == 'Unknown' && $v['toVersion'] == 'Unknown') {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
				else if ((!isset($v['toVersion']) || $v['toVersion'] == 'Unknown') && version_compare($version, $v['fromVersion']) >= 0) {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
				else if ($v['fromVersion'] == 'Unknown' && isset($v['toVersion']) && version_compare($version, $v['toVersion']) < 0) {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
				else if (version_compare($version, $v['fromVersion']) >= 0 && isset($v['toVersion']) && version_compare($version, $v['toVersion']) < 0) {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
			}
		}
		return false;
	}

	/**
	 * @return boolean
	 */
	public function needsCoreUpdate() {
		return $this->needs_core_update;
	}

	/**
	 * @return int
	 */
	public function getCoreUpdateVersion() {
		return $this->core_update_version;
	}

	/**
	 * @return array
	 */
	public function getPluginUpdates() {
		return $this->plugin_updates;
	}
	
	/**
	 * @return array
	 */
	public function getAllPlugins() {
		return $this->all_plugins;
	}
	
	/**
	 * @return array
	 */
	public function getPluginSlugs() {
		return $this->plugin_slugs;
	}

	/**
	 * @return array
	 */
	public function getThemeUpdates() {
		return $this->theme_updates;
	}
}
