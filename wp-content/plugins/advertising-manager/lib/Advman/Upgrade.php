<?php

include_once (ADVMAN_LIB . '/Admin.php');

class Advman_Upgrade
{
	// Upgrade the underlying data structure to the latest version
	function upgrade_advman(&$data)
	{
		$version = Advman_Upgrade::_get_version($data);
		Advman_Upgrade::_backup($data, $version);
		$versions = array('3.4', '3.4.2', '3.4.3', '3.4.7', '3.4.9', '3.4.12', '3.4.14', '3.4.15', '3.4.20');
		foreach ($versions as $v) {
			if (version_compare($version, $v, '<')) {
                $func = 'advman_' . str_replace('.','_',$v);
                Advman_Upgrade::$func($data);
			}
		}
		
		$data['settings']['version'] = ADVMAN_VERSION;
	}

    function advman_3_4_21(&$data)
    {
        // Remove OpenX Market - does not work
        unset($data['settings']['openx-market']);
        unset($data['settings']['openx-market-cpm']);

        // Set the category to be 'all' (by making it = '')
        foreach ($data['ads'] as $id => $ad) {
            if (!isset($data['ads'][$id]['openx-market'])) {
                unset($data['ads'][$id]['openx-market']);
            }
            if (!isset($data['ads'][$id]['openx-market-cpm'])) {
                unset($data['ads'][$id]['openx-market-cpm']);
            }
        }
        foreach ($data['networks'] as $id => $network) {
            if (!isset($data['networks'][$id]['openx-market'])) {
                unset($data['networks'][$id]['openx-market']);
            }
            if (!isset($data['networks'][$id]['openx-market-cpm'])) {
                unset($data['networks'][$id]['openx-market-cpm']);
            }
        }

    }
    function advman_3_4_20(&$data)
    {
        // Remove synchronization settings
        unset($data['settings']['last-sync']);
        unset($data['settings']['openx-sync']);
    }
	function advman_3_4_15(&$data)
	{
		// Set the category to be 'all' (by making it = '')
		foreach ($data['ads'] as $id => $ad) {
			if (!isset($data['ads'][$id]['show-tag'])) {
				$data['ads'][$id]['show-tag'] = '';
			}
		}
	}
	function advman_3_4_14(&$data)
	{
		// Add the 'enable php' setting
		if (!isset($data['settings']['enable-php'])) {
			$data['settings']['enable-php'] = false;
		}
		
		// Add the 'enable stats' setting
		if (!isset($data['settings']['stats'])) {
			$data['settings']['stats'] = true;
		}
		
		// Add the 'purge stats' setting
		if (!isset($data['settings']['enable-php'])) {
			$data['settings']['purge-stats-days'] = 30;
		}
		
		// Initialise the stats array
		$data['stats'] = array();
	}
	
	function advman_3_4_12(&$data)
	{
		// Set the category to be 'all' (by making it = '')
		foreach ($data['ads'] as $id => $ad) {
			if (!isset($data['ads'][$id]['show-category'])) {
				$data['ads'][$id]['show-category'] = '';
			}
		}
	}
	function advman_3_4_9(&$data)
	{
		// If all authors are selected, make the value '' (which means all), so that when new users are added, they will be included.
		$users = get_users_of_blog();
		foreach ($data['ads'] as $id => $ad) {
			if (is_array($ad['show-author'])) {
				$all = true;
				foreach ($users as $user) {
					if (!in_array($user->user_id, $ad['show-author'])) {
						$all = false;
						break;
					}
				}
				if ($all) {
					$data['ads'][$id]['show-author'] = '';
				}
			}
		}
	}
	function advman_3_4_7(&$data)
	{
		// Account ID for adsense did not get saved correctly.  See if we can grab it and save it correctly
		if (isset($data['networks']['ox_plugin_adsense']['account-id'])) {
			$accountId = $data['networks']['ox_plugin_adsense']['account-id'];
			if (is_numeric($accountId)) {
				$accountId = 'pub-' . $accountId;
				$data['networks']['ox_plugin_adsense']['account-id'] = $accountId;
			}
			foreach ($data['ads'] as $id => $ad) {
				if ($ad['class'] = 'ox_plugin_adsense' && empty($ad['account-id'])) {
					$data['ads'][$id]['account-id'] = $accountId;
				}
			}
		}
	}
	function advman_3_4_3(&$data)
	{
		// for some reason our meta boxes were hidden - remove this from database
		$us = get_users_of_blog();
		foreach ($us as $u) {
			delete_usermeta($u->user_id, 'meta-box-hidden_advman');
		}
	}
	function advman_3_4_2(&$data)
	{
		global $advman_engine;
		// Combine all show-* stuff into a single variable
		// Also remove the default values for the show-* stuff
		$types = array('page', 'post', 'home', 'search', 'archive');
		// Authors
		$users = array();
		$us = get_users_of_blog();
		foreach ($us as $u) {
			$users[] = $u->user_id;
		}
		
		foreach ($data['ads'] as $id => $ad) {
			
			$pageTypes = array();
			foreach ($types as $type) {
				if ($ad['show-' . $type] == 'yes') {
					$pageTypes[] = $type;
				} elseif (empty($ad['show-' . $type])) {
					$nw = $data['networks'][$ad['class']];
					if ($nw['show-' . $type] == 'yes') {
						$pageTypes[] = $type;
					}
				}
				
				unset($data['ads'][$id]['show-' . $type]);
			}
			$data['ads'][$id]['show-pagetype'] = $pageTypes;
			
			if (!empty($ad['show-author'])) {
				if (!is_array($ad['show-author'])) {
					$data['ads'][$id]['show-author'] = $data['ads'][$id]['author'] == 'all' ? $users : array($data['ads'][$id]['author']);
				}
			} else {
				$nw = $data['networks'][$ad['class']];
				if (!empty($nw['show-author'])) {
					if (is_array($nw['show-author'])) {
						$data['ads'][$id]['show-author'] = $nw['show-author'];
					} elseif ($nw['show-author'] == 'all') {
						$data['ads'][$id]['show-author'] = $users;
					} else {
						$data['ads'][$id]['show-author'] = array($nw['show-author']);
					}
				}
			}
		}
		
		foreach ($data['networks'] as $c => $nw) {
			foreach ($types as $type) {
				unset($data['networks'][$c]['show-' . $type]);
			}
			unset($data['networks'][$c]['show-author']);
		}
	}
	
	function advman_3_4(&$data)
	{
		// Move the where last-sync is stored
		if (isset($data['last-sync'])) {
			$data['settings']['last-sync'] = $data['last-sync'];
			unset($data['last-sync']);
		}
		// Move the 'slot' and 'ad' adtypes to 'all'
		foreach ($data['ads'] as $id => $ad) {
			if (isset($ad['adtype'])) {
				$v = $ad['adtype'];
				if ($v == 'slot' || $v == 'ad') {
					$data['ads'][$id]['adtype'] = 'all';
				}
			}
		}
		// Make sure the class name key is lower case (php4 is case insensitive)
		$nw = array();
		foreach ($data['networks'] as $k => $v) {
			$nw[strtolower($k)] = $v;
		}
		$data['networks'] = $nw;
	}
	
	function upgrade_adsensem(&$data)
	{
		$version = Advman_Upgrade::_get_version($data);
		Advman_Upgrade::adsensem_upgrade_ad_classes($data);
		Advman_Upgrade::adsensem_upgrade_ad_ids($data);
		Advman_Upgrade::adsensem_upgrade_network_classes($data);
		Advman_Upgrade::adsensem_upgrade_ad_settings($data);
		Advman_Upgrade::adsensem_upgrade_network_settings($data);
		Advman_Upgrade::adsensem_upgrade_settings($data);
		$notice = __('<strong>Advertising Manager</strong> has been upgraded from your <strong>Adsense Manager</strong> settings.', 'advman');
//		$question = __('Enable <a>auto optimisation</a>? (RECOMMENDED)', 'advman');
//		$question = str_replace('<a>', '<a href="http://code.openx.org/wiki/advertising-manager/Auto_Optimization" target="_new">', $question);
//		Advman_Admin::add_notice('optimise', $notice, 'ok');

		// Set the new version
		$data['settings']['version'] = '3.3.19';

		return Advman_Upgrade::upgrade_advman($data);
	}
	
	function _get_version(&$data)
	{
		$version = $data['settings']['version'];
		if (empty($version)) {
			$version = $data['version'];
			if ($version == 'ADVMAN_VERSION') {
				$version = '3.3.4';
			}
			unset ($data['version']);
			$data['settings']['version'] = $version;
		}
		return $version;
	}
	
	function _backup($data, $version)
	{
		$backup = get_option('plugin_advman_backup');
		if (empty($backup)) {
			$backup = get_option('plugin_adsensem_backup');
			delete_option('plugin_adsensem_backup');
		}
		
		$backup[$version] = $data;
		update_option('plugin_advman_backup', $backup);
	}
		
	function adsensem_get_classmap()
	{
		return array(
			'ad_adbrite' => 'OX_Plugin_Adbrite',
			'ad_adgridwork' => 'OX_Plugin_Adgridwork',
			'ad_adpinion' => 'OX_Plugin_Adpinion',
			'ad_adroll' => 'OX_Plugin_Adroll',
			'ad_adsense' => 'OX_Plugin_Adsense',
			'ad_adsense_ad' => 'OX_Plugin_Adsense',
			'ad_adsense_classic' => 'OX_Plugin_Adsense',
			'ad_adsense_link' => 'OX_Plugin_Adsense',
			'ad_adsense_referral' => 'OX_Plugin_Adsense',
			'ad_cj' => 'OX_Plugin_Cj',
			'ad_code' => 'OX_Ad_Html',
			'ad_crispads' => 'OX_Plugin_Crispads',
			'ad_openx_adserver' => 'OX_Plugin_Openx',
			'ad_shoppingads' => 'OX_Plugin_Shoppingads',
			'ad_widgetbucks' => 'OX_Plugin_Widgetbucks',
			'ad_ypn' => 'OX_Plugin_Ypn',
			'ox_adnet_adbrite' => 'OX_Plugin_Adbrite',
			'ox_adnet_adgridwork' => 'OX_Plugin_Adgridwork',
			'ox_adnet_adify' => 'OX_Plugin_Adify',
			'ox_adnet_adpinion' => 'OX_Plugin_Adpinion',
			'ox_adnet_adroll' => 'OX_Plugin_Adroll',
			'ox_adnet_adsense' => 'OX_Plugin_Adsense',
			'ox_adnet_chitika' => 'OX_Plugin_Chitika',
			'ox_adnet_cj' => 'OX_Plugin_Cj',
			'ox_adnet_crispads' => 'OX_Plugin_Crispads',
			'ox_adnet_html' => 'OX_Ad_Html',
			'ox_adnet_openx' => 'OX_Plugin_Openx',
			'ox_adnet_shoppingads' => 'OX_Plugin_Shoppingads',
			'ox_adnet_widgetbucks' => 'OX_Plugin_Widgetbucks',
			'ox_adnet_ypn' => 'OX_Plugin_Ypn',
		);
	}
	
	function adsensem_upgrade_ad_classes(&$data)
	{
		$adnets = Advman_Upgrade::adsensem_get_classmap();
		$aAds = array();
		
		foreach ($data['ads'] as $n => $ad) {
			$aAd = array();
			if (get_class($ad) != '__PHP_Incomplete_Class') {
				$aAd['class'] = $adnets[strtolower(get_class($ad))];
			}
			foreach ($ad as $key => $value) {
				if ($key == '__PHP_Incomplete_Class_Name') {
					$aAd['class'] = $adnets[strtolower($value)];
				} elseif ($key == 'p') {
					$aAd += $value;
				} elseif ($key) {
					$aAd[$key] = $value;
				}
			}
			$aAds[$n] = $aAd;
		}
		
		$data['ads'] = $aAds;
	}
	
	function adsensem_upgrade_network_classes(&$data)
	{
		$adnets = Advman_Upgrade::adsensem_get_classmap();
		$aNws = array();
		foreach ($data['defaults'] as $c => $network) {
			$newclass = in_array(strtolower($c), $adnets) ? $c : $adnets[strtolower($c)];
			$aNws[$newclass] = $network;
		}
		$data['networks'] = $aNws;
		unset($data['defaults']);
		
		if (isset($data['account-ids'])) {
			foreach ($data['account-ids'] as $c => $accountId) {
				$newclass = in_array(strtolower($c), $adnets) ? $c : $adnets[strtolower($c)];
				// Fix account ID for adsense
				if (strtolower($newclass) == 'ox_plugin_adsense' && is_numeric($accountId)) {
					$accountId = 'pub-' . $accountId;
				}
				$data['networks'][$newclass]['account-id'] = $accountId;
				foreach ($data['ads'] as $id => $ad) {
					if ((strtolower($ad['class']) == strtolower($newclass)) && empty($ad['account-id'])) {
						$data['ads'][$id]['account-id'] = $accountId;
					}
				}
			}
			unset($data['account-ids']);
		}
		
		if (isset($data['adsense-account'])) {
			$accountId = $data['adsense-account'];
			foreach ($data['ads'] as $id => $ad) {
				if ((strtolower($ad['class']) == 'ox_plugin_adsense') && empty($ad['account-id'])) {
					$data['ads'][$id]['account-id'] = $accountId;
				}
			}
			unset($data['adsense-account']);
		}
	}
	
	function adsensem_upgrade_ad_ids(&$data)
	{
		$ads = array();
		$nextId = 1;
		foreach ($data['ads'] as $n => $ad) {
			if (is_numeric($n) && $nextId <= $n) {
				$nextId = $n + 1;
			}
		}
		foreach ($data['ads'] as $n => $ad) {
			if (is_numeric($n)) {
				$ads[$n] = $ad;
			} else {
				$ad['name'] = $n;
				$ads[$nextId++] = $ad;
			}
		}
		
		$data['ads'] = $ads;
		$data['settings']['next_ad_id'] = $nextId;
		unset($data['next_ad_id']);  // old way of storing next ad id
	}
	
	function adsensem_upgrade_ad_settings(&$data)
	{
		$ads = array();
		foreach ($data['ads'] as $id => $ad) {
			$ad['id'] = $id;
			if (!isset($ad['name'])) {
				$base = 'ad';
				if (!empty($ad['class'])) {
					$class = $ad['class'];
					$tmp = new $class;
					$base = $tmp->network_name;
				}
				$ad['name'] = OX_Tools::generate_name($base);
			}
			// add active
			if (!isset($ad['active'])) {
				$ad['active'] = true;
			}
			// remove title
			if (isset($ad['title'])) {
				unset($ad['title']);
			}
			// Make sure that any settings under 'color-url' are now under 'color-link'
			if (!empty($ad['color-url']) && empty($ad['color-link'])) {
				$ad['color-link'] = $ad['color-url'];
			}
			unset($ad['color-url']);
			// Set the OpenX Market
			if (!isset($ad['openx-market'])) {
				$ad['openx-market'] = false;
			}
			// Set the OpenX Market CPM
			if (!isset($ad['openx-market-cpm'])) {
				$ad['openx-market-cpm'] = '0.20';
			}
			// Set the Weight
			if (!isset($ad['weight'])) {
				$ad['weight'] = '1';
			}
			// Changed the 'hide link url' field to 'status' (for cj ads)
			if (isset($ad['hide-link-url'])) {
				$ad['status'] = $ad['hide-link-url'];
				unset($ad['hide-link-url']);
			}
			
			// Make sure width and height are correct
			if (empty($ad['width']) || empty($ad['height'])) {
				$format = $ad['adformat'];
				if ( !empty($format) && ($format != 'custom')) {
					list($width, $height, $null) = split('[x]', $format);
					$ad['width'] = $width;
					$ad['height'] = $height;
				}
			}
			
			// Make sure that there is ad code
			if (empty($ad['code'])) {
				Advman_Upgrade::_get_code($ad);
			}
			
			// remove some variables...
			$aVars = array('codemethod', 'networkName', 'shortName', 'url');
			foreach ($aVars as $var) {
				if (isset($ad[$var])) {
					unset($ad[$var]);
				}
			}
			
			$ads[$id] = $ad;
		}
		
		$data['ads'] = $ads;
	}
	
	function adsensem_upgrade_network_settings(&$data)
	{
		foreach ($data['networks'] as $c => $network) {
			if (!isset($network['counter'])) {
				$data['networks'][$c]['counter'] = ($c == 'OX_Plugin_Adsense') ? '3' : '';
			}
			if (!isset($network['openx-market'])) {
				$data['networks'][$c]['openx-market'] = 'no';
			}
			// Set OpenX Market eCPM
			if (!isset($network['openx-market-cpm'])) {
				$data['networks'][$c]['openx-market-cpm'] = '0.20';
			}
			// Set Weight
			if (!isset($network['weight'])) {
				$data['networks'][$c]['weight'] = '1';
			}
			// Show only to an Author
			if (!isset($network['show-author'])) {
				$data['networks'][$c]['show-author'] = 'all';
			}
			if (!isset($network['color-border']) && isset($network['colors']['border'])) {
				$data['networks'][$c]['color-border'] = $network['colors']['border'];
			}
			if (!isset($network['color-title']) && isset($network['colors']['title'])) {
				$data['networks'][$c]['color-title'] = $network['colors']['title'];
			}
			if (!isset($network['color-bg']) && isset($network['colors']['bg'])) {
				$data['networks'][$c]['color-bg'] = $network['colors']['bg'];
			}
			if (!isset($network['color-text']) && isset($network['colors']['text'])) {
				$data['networks'][$c]['color-text'] = $network['colors']['text'];
			}
			if (!isset($network['color-link']) && isset($network['colors']['url'])) {
				$data['networks'][$c]['color-link'] = $network['colors']['url'];
			}
			
			if (!isset($network['show-page']) && isset($network['show-post'])) {
				$data['networks'][$c]['show-page'] = $network['show-post'];
			}
			if (!isset($network['adformat']) && isset($network['linkformat'])) {
				$data['networks'][$c]['adformat'] = $network['linkformat'];
			}
			if (!isset($network['adformat']) && isset($network['referralformat'])) {
				$data['networks'][$c]['adformat'] = $network['referralformat'];
			}
			// Set height and width for an ad format
			if (!empty($network['adformat']) && ($network['adformat'] != 'custom')) {
				list($width, $height) = split('[x]', $network['adformat']);
				if (is_numeric($width)) {
					$data['networks'][$c]['width'] = $width;
				}
				if (is_numeric($height)) {
					$data['networks'][$c]['height'] = $height;
				}
			}
			
			
			unset($data['networks'][$c]['colors']);
			unset($data['networks'][$c]['product']);
		}
	}
	function adsensem_upgrade_settings(&$data)
	{
		// Get rid of the 'default_ad' field (should be 'default-ad')
		if (isset($data['default_ad'])) {
			unset($data['default_ad']);
		}
		
		if (!isset($data['settings']['openx-sync'])) {
			$data['settings']['openx-sync'] = isset($data['openx-sync']) ? $data['openx-sync'] : true;
		}
		unset($data['openx-sync']);
		
		if (isset($data['uuid'])) {
			$data['settings']['publisher-id'] = $data['uuid'];
			unset($data['uuid']);
		} else {
			if (!isset($data['settings']['publisher-id'])) {
				$data['settings']['publisher-id'] = md5(uniqid('', true));
			}
		}
		
		if (isset($data['be-nice'])) {
			unset($data['be-nice']);
		}
		if (isset($data['benice'])) {
			unset($data['benice']);
		}
		// Reset ad ids just in case
		$nextId = 1;
		foreach ($data['ads'] as $id => $ad) {
			if ($id > $nextId) {
				$nextId = $id;
			}
		}
		$data['settings']['next_ad_id'] = $nextId + 1;
		
		if (!isset($data['settings']['default-ad'])) {
			if (isset($data['default-ad'])) {
				$data['settings']['default-ad'] = $data['default-ad'];
			} elseif (isset($data['defaults']['ad'])) {
				$data['settings']['default-ad'] = $data['defaults']['ad'];
			}
		}
		unset($data['default-ad']);
		unset($data['defaults']['ad']);
	}
	
	function _get_code(&$ad)
	{
		$code = '';
		
		switch (strtolower($ad['class'])) {
			case 'ox_plugin_adbrite' : $code = Advman_Upgrade::_get_code_adbrite($ad); break;
			case 'ox_plugin_adgridwork' : $code = Advman_Upgrade::_get_code_adgridwork($ad); break;
			case 'ox_plugin_adpinion' : $code = Advman_Upgrade::_get_code_adpinion($ad); break;
			case 'ox_plugin_adroll' : $code = Advman_Upgrade::_get_code_adroll($ad); break;
			case 'ox_plugin_adsense' : $code = Advman_Upgrade::_get_code_adsense($ad); break;
			case 'ox_plugin_cj' : $code = Advman_Upgrade::_get_code_cj($ad); break;
			case 'ox_plugin_crispads' : $code = Advman_Upgrade::_get_code_crispads($ad); break;
			case 'ox_plugin_openx' : $code = Advman_Upgrade::_get_code_openx($ad); break;
			case 'ox_plugin_shoppingads' : $code = Advman_Upgrade::_get_code_shoppingads($ad); break;
			case 'ox_plugin_widgetbucks' : $code = Advman_Upgrade::_get_code_widgetbucks($ad); break;
			case 'ox_plugin_ypn' : $code = Advman_Upgrade::_get_code_ypn($ad); break;
		}
		
		if (!empty($code)) {
			$oAd = new $ad['class'];
			$oAd->import_settings($code);
			foreach ($oAd->p as $property => $value) {
				$ad[$property] = $value;
			}
		}
	}
	
	function _get_code_adsense($ad)
	{
		$code = '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $ad['account-id'] . '";' . "\n";
		$code.= 'google_ad_slot = "' . str_pad($ad['slot'],10,'0',STR_PAD_LEFT) . '"' . ";\n"; //String padding to max 10 char slot ID
		
		if($ad['adtype']=='ref_text'){
			$code.= 'google_ad_output = "textlink"' . ";\n";
			$code.= 'google_ad_format = "ref_text"' . ";\n";
			$code.= 'google_cpa_choice = ""' . ";\n";
		} else if($ad['adtype']=='ref_image'){
			$code.= 'google_ad_width = ' . $ad['width'] . ";\n";
			$code.= 'google_ad_height = ' . $ad['height'] . ";\n";
			$code.= 'google_cpa_choice = ""' . ";\n";
		} else {
			$code.= 'google_ad_width = ' . $ad['width'] . ";\n";
			$code.= 'google_ad_height = ' . $ad['height'] . ";\n";
		}
		
		$code.= '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";
		
		return $code;
	}
	
	function _get_code_adbrite($ad)
	{
		$code ='<!-- Begin: AdBrite -->';
		$code .= '<script type="text/javascript">' . "\n";
		$code .= "var AdBrite_Title_Color = '" . $ad['color-title'] . "'\n";
		$code .= "var AdBrite_Text_Color = '" . $ad['color-text'] . "'\n";
		$code .= "var AdBrite_Background_Color = '" . $ad['color-bg'] . "'\n";
		$code .= "var AdBrite_Border_Color = '" . $ad['color-border'] . "'\n";
		$code .= '</script>' . "\n";
	   	$code .= '<script src="http://ads.adbrite.com/mb/text_group.php?sid=' . $ad['slot'] . '&zs=' . $ad['account-id'] . '" type="text/javascript"></script>';
		$code .= '<div><a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=' . $ad['slot'] . '&afsid=1" style="font-weight:bold;font-family:Arial;font-size:13px;">Your Ad Here</a></div>';
		$code .= '<!-- End: AdBrite -->';
		
		return $code;
	}
	
	function _get_code_adgridwork($ad)
	{
		$code ='<a href="http://www.adgridwork.com/?r=' . $ad['account-id'] . '" style="color: #' . $ad['color-link'] .  '; font-size: 14px" target="_blank">Free Advertising</a>';
		$code.='<script type="text/javascript">' . "\n";
		$code.="var sid = '"  . $ad['slot'] . "';\n";
		$code.="var title_color = '" . $ad['color-title'] . "';\n";
		$code.="var description_color = '" . $ad['color-text'] . "';\n";
		$code.="var link_color = '" . $ad['color-link'] . "';\n";
		$code.="var background_color = '" . $ad['color-bg'] . "';\n";
		$code.="var border_color = '" . $ad['color-border'] . "';\n";
		$code.='</script><script type="text/javascript" src="http://www.mediagridwork.com/mx.js"></script>';
		
		return $code;
	}
	
	function _get_code_adpinion($ad)
	{
		if($ad['width']>$ad['height']){$xwidth=18;$xheight=17;} else {$xwidth=0;$xheight=35;}
		$code ='';
	 	$code .= '<iframe src="http://www.adpinion.com/app/adpinion_frame?website=' . $ad['account-id'] . '&amp;width=' . $ad['width'] . '&amp;height=' . $ad['height'] . '" ';
		$code .= 'id="adframe" style="width:' . ($ad['width']+$xwidth) . 'px;height:' . ($ad['height']+$xheight) . 'px;" scrolling="no" frameborder="0">.</iframe>';
	
		return $code;
	}
	
	function _get_code_adroll($ad)
	{
		$code ='';
		$code .= '<!-- Start: Adroll Ads -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $ad['account-id'] . '/' . $ad['slot'] . '/">';
		$code .= '</script>';
		$code .= '<!-- Start: Adroll Profile Link -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $ad['account-id'] . '/' . $ad['slot'] . '/link">';
		$code .= '</script>';
	
		return $code;
	}
	
	function _get_code_adsense_ad($ad)
	{
		$code='';
		
		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $ad['account-id'] . '";' . "\n";
				
		if($ad['channel']!==''){ $code.= 'google_ad_channel = "' . $ad['channel'] . '";' . "\n"; }
		if($ad['uistyle']!==''){ $code.= 'google_ui_features = "rc:' . $ad['uistyle'] . '";' . "\n"; }
				
		$code.= 'google_ad_width = ' . $ad['width'] . ";\n";
		$code.= 'google_ad_height = ' . $ad['height'] . ";\n";
				
		$code.= 'google_ad_format = "' . $ad['adformat'] . '_as"' . ";\n";
		$code.= 'google_ad_type = "' . $ad['adtype'] . '"' . ";\n";

		switch ($ad['alternate-ad']) {
			case 'url'		: $code.= 'google_alternate_ad_url = "' . $ad['alternate-url'] . '";' . "\n"; break;
			case 'color'	: $code.= 'google_alternate_ad_color = "' . $ad['alternate-color'] . '";' . "\n"; break;
			case ''				: break;
			default				:
				$alternateAd = $ad['alternate-ad'];
				if (!empty($alternateAd)) {
					$code.= 'google_alternate_ad_url = "' . get_bloginfo('wpurl') . '/?advman-ad-name=' . $alternateAd . '";'  . "\n";
				}
		}
		
		$code.= 'google_color_border = "' . $ad['color-border'] . '"' . ";\n";
		$code.= 'google_color_bg = "' . $ad['color-bg'] . '"' . ";\n";
		$code.= 'google_color_link = "' . $ad['color-title'] . '"' . ";\n";
		$code.= 'google_color_text = "' . $ad['color-text'] . '"' . ";\n";
		$code.= 'google_color_url = "' . $ad['color-link'] . '"' . ";\n";
		
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _get_code_adsense_link($ad)
	{
		$code='';

		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $ad['account-id'] . '";' . "\n";
					
		if($ad['channel']!==''){ $code.= 'google_ad_channel = "' . $ad['channel'] . '";' . "\n"; }
		if($ad['uistyle']!==''){ $code.= 'google_ui_features = "rc:' . $ad['uistyle'] . '";' . "\n"; }
					
		$code.= 'google_ad_width = ' . $ad['width'] . ";\n";
		$code.= 'google_ad_height = ' . $ad['height'] . ";\n";
					
		$code.= 'google_ad_format = "' . $ad['adformat'] . $ad['adtype'] . '"' . ";\n"; 

		//$code.=$ad->_render_alternate_ad_code();
		$code.= 'google_color_border = "' . $ad['color-border'] . '"' . ";\n";
		$code.= 'google_color_bg = "' . $ad['color-bg'] . '"' . ";\n";
		$code.= 'google_color_link = "' . $ad['color-title'] . '"' . ";\n";
		$code.= 'google_color_text = "' . $ad['color-text'] . '"' . ";\n";
		$code.= 'google_color_url = "' . $ad['color-link'] . '"' . ";\n";
			
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _get_code_adsense_referral($ad)
	{
		//if($ad===false){$ad=$_advman['ads'][$_advman['default_ad']];}
		//$ad=advman::merge_defaults($ad); //Apply defaults
		if($ad['product']=='referral-image') {
			$format = $ad['adformat'] . '_as_rimg';
		} else if($ad['product']=='referral-text') {
			$format = 'ref_text';
		}				
		$code='';

	
		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $ad['account-id'] . '";' . "\n";
		
		if($ad['channel']!==''){ $code.= 'google_ad_channel = "' . $ad['channel'] . '";' . "\n"; }
		
		if($ad['product']=='referral-image'){
			$code.= 'google_ad_width = ' . $ad['width'] . ";\n";
			$code.= 'google_ad_height = ' . $ad['height'] . ";\n";
		}
		
		if($ad['product']=='referral-text'){$code.='google_ad_output = "textlink"' . ";\n";}
		$code.='google_cpa_choice = "' . $ad['referral'] . '"' . ";\n";
		
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _get_code_cj($ad)
	{
		$cjservers=array(
			'www.kqzyfj.com',
			'www.tkqlhce.com',
			'www.jdoqocy.com',
			'www.dpbolvw.net',
			'www.lduhtrp.net');
		
		$code = '';
		$code .= '<!-- Start: CJ Ads -->';
	 	$code .= '<a href="http://' . $cjservers[array_rand($cjservers)] . '/click-' . $ad['account-id'] . '-' . $ad['slot'] . '"';
		if($ad['new-window']=='yes'){$code.=' target="_blank" ';}
		
		if($ad['hide-link']=='yes'){
			$code.='onmouseover="window.status=\'';
			$code.=$ad['hide-link-url'];
			$code.='\';return true;" onmouseout="window.status=\' \';return true;"';
		}
		
		$code .= '>';
		
		$code .= '<img src="http://' . $cjservers[array_rand($cjservers)] . '/image-' . $ad['account-id'] . '-' . $ad['slot'] . '"';
		$code .= ' width="' . $ad['width'] . '" ';
		$code .= ' height="' . $ad['height'] . '" ';
		$code .= ' alt="' . $ad['alt-text'] . '" ';
		$code .= '>';
		$code .= '</a>';
	
		return $code;
	}
	
	function _get_code_crispads($ad)
	{
		global $_advman;

		if ($ad['codemethod']=='javascript'){
			$code='<script type="text/javascript"><!--//<![CDATA[' . "\n";
			$code.="var m3_u = (location.protocol=='https:'?'https://www.crispads.com/spinner/www/delivery/ajs.php':'http://www.crispads.com/spinner/www/delivery/ajs.php');\n";
			$code.="var m3_r = Math.floor(Math.random()*99999999999);\n";
			$code.="if (!document.MAX_used) document.MAX_used = ',';\n";
			$code.="document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);\n";
			$code.='document.write ("?zoneid=' . $ad['slot'] . '");' . "\n";
			$code.="document.write ('&amp;cb=' + m3_r);\n";
			$code.="if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);\n";
	   		$code.='document.write ("&amp;loc=" + escape(window.location));' . "\n";
			$code.='if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));' . "\n";
			$code.='if (document.context) document.write ("&context=" + escape(document.context));' . "\n";
			$code.='if (document.mmm_fo) document.write ("&amp;mmm_fo=1");' . "\n";
			$code.='document.write ("\'><\/scr"+"ipt>");' . "\n";
			$code.='//]]>--></script><noscript><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $ad['identifier'] . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $ad['slot'] . '&amp;n=' . $ad['identifier'] . '" border="0" alt="" /></a></noscript>';
		} else { //Iframe
			$code='<iframe id="' . $ad['identifier'] . '" name="' . $ad['identifier'] . '" src="http://www.crispads.com/spinner/www/delivery/afr.php?n=' . $ad['identifier'] . '&amp;zoneid=' . $ad['slot'] . '" framespacing="0" frameborder="no" scrolling="no" width="' . $ad['width'] . '" height="' . $ad['height'] . '"><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $ad['identifier'] . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $ad['slot'] . '&amp;n=' . $ad['identifier'] . '" border="0" alt="" /></a></iframe>';
			$code.='<script type="text/javascript" src="http://www.crispads.com/spinner/www/delivery/ag.php"></script>';
		}
		
		return $code;
	}
	function _get_code_shoppingads($ad)
	{
		$code = '<script type="text/javascript"><!--' . "\n";
		$code.= 'shoppingads_ad_client = "' . $ad['account-id'] . '";' . "\n";
		$code.= 'shoppingads_ad_campaign = "' . $ad['campaign'] . '";' . "\n";

		list($width,$height)=split('[x]',$ad['adformat']);
		$code.= 'shoppingads_ad_width = "' . $width . '";' . "\n";
		$code.= 'shoppingads_ad_height = "' . $height . '";' . "\n";

		$code.= 'shoppingads_ad_kw = "' . $ad['keywords'] . '";' . "\n";

		$code.= 'shoppingads_color_border = "' . $ad['color-border'] . '";' . "\n";
		$code.= 'shoppingads_color_bg = "' . $ad['color-bg'] . '";' . "\n";
		$code.= 'shoppingads_color_heading = "' . $ad['color-title'] . '";' . "\n";
		$code.= 'shoppingads_color_text = "' . $ad['color-text'] . '";' . "\n";
		$code.= 'shoppingads_color_link = "' . $ad['color-link'] . '";' . "\n";

		$code.= 'shoppingads_attitude = "' . $ad['attitude'] . '";' . "\n";
		if($ad['new-window']=='yes'){$code.= 'shoppingads_options = "n";' . "\n";}

		$code.= '--></script>
		<script type="text/javascript" src="http://ads.shoppingads.com/pagead/show_sa_ads.js">
		</script>' . "\n";
		
		return $code;
	}
	
	function _get_code_widgetbucks($ad)
	{
		global $_advman;

		$code ='';
		$code .= '<!-- START CUSTOM WIDGETBUCKS CODE --><div>';
		$code .= '<script src="http://api.widgetbucks.com/script/ads.js?uid=' . $ad['slot'] . '"></script>'; 
		$code .= '</div><!-- END CUSTOM WIDGETBUCKS CODE -->';
		return $code;
	}
	
	function _get_code_ypn($ad)
	{
		$code = '<script language="JavaScript">';
		$code .= '<!--';
		$code .= 'ctxt_ad_partner = "' . $ad['account-id'] . '";' . "\n";
		$code .= 'ctxt_ad_section = "' . $ad['channel'] . '";' . "\n";
		$code .= 'ctxt_ad_bg = "";' . "\n";
		$code .= 'ctxt_ad_width = "' . $ad['width'] . '";' . "\n";
		$code .= 'ctxt_ad_height = "' . $ad['height'] . '";' . "\n";
		
		$code .= 'ctxt_ad_bc = "' . $ad['color-bg'] . '";' . "\n";
		$code .= 'ctxt_ad_cc = "' . $ad['color-border'] . '";' . "\n";
		$code .= 'ctxt_ad_lc = "' . $ad['color-title'] . '";' . "\n";
		$code .= 'ctxt_ad_tc = "' . $ad['color-text'] . '";' . "\n";
		$code .= 'ctxt_ad_uc = "' . $ad['color-link'] . '";' . "\n";
		
		$code .= '// -->';
		$code .= '</script>';
		$code .= '<script language="JavaScript" src="http://ypn-js.overture.com/partner/js/ypn.js">';
		$code .= '</script>';
		
		return $code;
	}
}
?>