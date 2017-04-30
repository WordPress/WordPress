<?php
require_once ADVMAN_LIB . '/Tools.php';
require_once OX_LIB . '/Dal.php';

class Advman_Dal extends OX_Dal
{
	var $data;
	
	function Advman_Dal()
	{
		$this->data = $this->_load_data();
	}
	
	function _load_data()
	{
		$save = false;
		$data = get_option('plugin_advman');
		if (!empty($data)) {
			if (version_compare($data['settings']['version'], ADVMAN_VERSION, '<')) {
				include_once(ADVMAN_LIB . '/Upgrade.php');
				Advman_Upgrade::upgrade_advman($data);
				$save = true;
			}
		} else {
			$data = get_option('plugin_adsensem');
			if (!empty($data)) {
				include_once(ADVMAN_LIB . '/Upgrade.php');
				Advman_Upgrade::upgrade_adsensem($data);
				$save = true;
			}
		}
		if (empty($data)) {
			$data['ads'] = array();
			$data['networks'] = array();
			$data['settings'] = array();
			$data['settings']['next_ad_id'] = 1;
			$data['settings']['default-ad'] = '';
			$data['settings']['version'] = ADVMAN_VERSION;
			$data['settings']['publisher-id'] = md5(uniqid('', true));
			$data['settings']['enable-php'] = false;
			$data['settings']['purge-stats-days'] = 30;
			$data['settings']['stats'] = true;
			$data['stats'] = array();
			$save = true;
		}
		if (!empty($data['stats'])) {
			$oldest = time() - ($data['settings']['purge-stats-days'] * 24 * 60 * 60);
			foreach ($data['stats'] as $day => $stat) {
				$ts = strtotime($day);
				if ($ts < $oldest) {
					unset($data['stats'][$day]);
					$save = true;
				}
			}
		}
		
		if ($save) {
			update_option('plugin_advman', $data);
		}
		
		$this->_map_objects($data);
		
		return $data;
	}
	
	function _map_arrays(&$data)
	{
		$aAds = array();
		foreach ($data['ads'] as $id => $oAd) {
			$aAds[$id] = $oAd->to_array();
			$aAds[$id]['class'] = get_class($oAd);
		}
		$data['ads'] = $aAds;
	}
	function _map_objects(&$data)
	{
		$oAds = array();
		foreach ($data['ads'] as $id => $aAd) {
			$ad = $this->factory($aAd['class'], $aAd, $data);
			if ($ad) {
				$oAds[$id] = $ad;
			}
		}
		$data['ads'] = $oAds;
	}
	function _update_data($data = null, $key = 'plugin_advman')
	{
		if (is_null($data)) {
			$data = $this->data;
		}
		
		$this->_map_arrays($data);
	
		update_option($key, $data);
	}
	
	function factory($class, $aAd = null, $data = null)
	{
		if (class_exists($class)) {
			$ad = new $class();
			if (is_null($data)) {
				$data = $this->data;
			}
			if (is_null($aAd)) {
				$ad->active = true;
				$ad->name = OX_Tools::generate_name($ad->network_name);
			} else {
				$ad->name = $aAd['name'];
				$ad->id = $aAd['id'];
				$ad->active = $aAd['active'];
				$aProperties = Advman_Tools::get_properties_from_array($aAd);
				$ad->p = $aProperties;
			}
			
			if (empty($data['networks'][$class])) {
				$ad->np = $ad->get_network_property_defaults();
			} else {
				$ad->np = $data['networks'][$class];
			}
			
			return $ad;
		} else {
			return false;
		}
	}
	
	function select_setting($key)
	{
		switch ($key) {
			case 'admin-email':
				return get_option('admin_email');
			case 'host-version':
				global $wp_version;
				return $wp_version;
			case 'product-name':
				global $wpmu_version;
				return !empty($wpmu_version) ? 'Wordpress MU' : 'Wordpress'; 
			case 'user-login':
				global $user_login;
				if (function_exists('get_currentuserinfo')) {
					get_currentuserinfo();
					return $user_login;
				}
				return '';
			case 'website-url':
				return get_option('siteurl');
			case 'yesterday-views':
				$yesterday = date('Y-m-d', time() - (60 * 60 * 24));
//				$yesterday = date('Y-m-d', time());  // for testing
				$stats = $this->data['stats'];
				return ( empty($stats[$yesterday]) ? 0 : array_sum($stats[$yesterday]) );
		}
		return $this->data['settings'][$key];
	}
	
	function update_setting($key, $value)
	{
		switch ($key) {
			case 'product-name':
			case 'host-version':
			case 'admin-email':
			case 'user-login':
			case 'website-url':
				return false; // all of these settings are read only
		}
		$this->data['settings'][$key] = $value;
		$this->_update_data();
		return true;
	}
	
	function select_stats()
	{
		return $this->data['stats'];
	}
	
	function update_stats($stats)
	{
		$this->data['stats'] = $stats;
		$this->_update_data();
		return true;
	}
	
	function insert_ad($ad)
	{
		$id = $this->data['settings']['next_ad_id'];
		$this->data['settings']['next_ad_id'] = $id+1;
		$ad->id = $id;
		$this->data['ads'][$id] = $ad;
		OX_Tools::sort($this->data['ads']);
		$this->_update_data();
		return $ad;
	}
	
	function delete_ad($id)
	{
		unset($this->data['ads'][$id]);
		$this->_update_data();
	}
	
	function select_ad($id)
	{
		return $this->data['ads'][$id];
	}
	
	function select_ads()
	{
		return $this->data['ads'];
	}
	
	function update_ad($ad)
	{
		$id = $ad->id;
		$this->data['ads'][$id] = $ad;
		$this->_update_data();
		return $id;
	}
	
	function update_ad_network($ad)
	{
		$this->data['networks'][strtolower(get_class($ad))] = $ad->np;
		$this->_update_data();
	}
}
?>