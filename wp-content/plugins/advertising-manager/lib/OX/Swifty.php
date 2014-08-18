<?php
require_once(OX_LIB . '/Ad.php');
require_once(OX_LIB . '/Dal.php');
require_once(OX_LIB . '/Html.php');

class OX_Swifty
{
	var $dal;
	var $ad_networks;
	var $counter;
	var $actions;
	
	function OX_Swifty($dalClass = null)
	{
		// Functions here are initialisation only - plugins have not been loaded (so we cannot initialise data)
		$this->counter = array();
		$this->ad_networks = array();
		$this->actions = array();
		
		// Load all Swifty plugins
		OX_Tools::load_plugins(OX_LIB . '/Plugins', $this);
		
		// Load the data access layer
		$this->dal = is_null($dalClass) ? new OX_Dal() : new $dalClass();
	}
	
	function addAction($key, $value)
	{
		$actions = !empty($this->actions[$key]) ? $this->actions[$key] : array();
		$actions[] = $value;
		
		$this->actions[$key] = $actions;
	}
	
	function getAction($key)
	{
		return $this->actions[$key];
	}
	
	function factory($class)
	{
		return $this->dal->factory($class);
	}
	
	function getSetting($key)
	{
		return $this->dal->select_setting($key);
	}
	
	function setSetting($key, $value)
	{
		return $this->dal->update_setting($key, $value);
	}
	
	function setStats($stats)
	{
		return $this->dal->update_stats($stats);
	}
	
	function getStats()
	{
		return $this->dal->select_stats();
	}
	
	function incrementStats($ad)
	{
		$date = date("Y-m-d");
		$adId = $ad->id;
		if ($this->getSetting('stats')) {
			$stats = $this->getStats();
			if (empty($stats[$date][$adId])) {
				$stats[$date][$adId] = 0;
			}
			$stats[$date][$adId]++;
			$this->setStats($stats);
		}
	}
	
	function insertAd(&$ad)
	{
		$ad->add_revision();
		return $this->dal->insert_ad($ad);
	}
	
	function deleteAd($id)
	{
		return $this->dal->delete_ad($id);
	}
	
	function getAds()
	{
		return $this->dal->select_ads();
	}
	
	function getAd($id)
	{
		return $this->dal->select_ad($id);
	}
	
	function setAd(&$ad)
	{
		$ad->add_revision();
		return $this->dal->update_ad($ad);
	}
	
	function copyAd($id)
	{
		$ad = $this->dal->select_ad($id);
		if ($ad) {
            // Not sure why, but we will manually clone an object here
            $new = unserialize(serialize($ad));
            $new->add_revision();
			return $this->dal->insert_ad($new);
		}
		
		return false;
	}
	
	function setAdNetwork(&$ad)
	{
		$ad->add_revision(true);
		return $this->dal->update_ad_network($ad);
	}
	
	function importAdTag($tag)
	{
		global $advman_engine;
		
		$imported = false;
		if (!empty($tag)) {
			$networks = $this->getAction('ad_network');
			foreach ($networks as $network) {
				if (call_user_func(array($network, 'import_detect_network'), $tag)) {
					$ad = $advman_engine->factory($network);
					if ($ad) {
						$ad->import_settings($tag);
						$imported = true;
						break; //leave the foreach loop
					}
				}
			}
		}
		
		// Not a pre-defined network - we will make it HTML code...
		if (!$imported) {
			$ad = $advman_engine->factory('OX_Ad_Html');
			$ad->import_settings($tag);
		}
		
		$ad = $this->insertAd($ad);
		// Add the ad network defaults if they are not set yet
		if (empty($ad->np)) {
			$this->setAdNetwork($ad);
		}
		
		return $ad;
	}
	
	function setAdActive($id, $active)
	{
		$ad = $this->dal->select_ad($id);
		if ($active != $ad->active) {
			$ad->active = $active;
			return $this->setAd($ad);
		}
		
		return false;
	}
		
	function selectAd($name = null)
	{
		global $advman_engine;
		
		if (empty($name)) {
			$name = $this->getSetting('default-ad');
		}
		if (!empty($name)) {
			// Find the ads which match the name
			$ads = $advman_engine->getAds();
			$totalWeight = 0;
			$validAds = array();
			foreach ($ads as $id => $ad) {
				if ( ($ad->name == $name) && ($ad->is_available()) ) {
					$weight = $ad->get('weight');
					if ($weight > 0) {
						$validAds[] = $ad;
						$totalWeight += $weight;
					}
				}
			}
			// Pick the ad
			// Generate a number between 0 and 1
			$rnd = (mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX);
			// Loop through ads until the selected one is chosen
			$wt = 0;
			foreach ($validAds as $ad) {
				$wt += $ad->get('weight');
				if ( ($wt / $totalWeight) > $rnd) {
					// Update the counters for this ad
					$this->update_counters($ad);
					// Display the ad
					return $ad;
				}
			}
		}
	}
	
	function update_counters($ad)
	{
		if (!empty($ad)) {
			if (empty($this->counter['id'][$ad->id])) {
				$this->counter['id'][$ad->id] = 1;
			} else {
				$this->counter['id'][$ad->id]++;
			}
			
			if (empty($this->counter['network'][strtolower(get_class($ad))])) {
				$this->counter['network'][strtolower(get_class($ad))] = 1;
			} else {
				$this->counter['network'][strtolower(get_class($ad))]++;
			}
		}
	}
}
?>