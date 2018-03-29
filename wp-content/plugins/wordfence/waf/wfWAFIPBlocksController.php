<?php
class wfWAFIPBlocksController
{
	const WFWAF_BLOCK_UAREFIPRANGE = 'UA/Referrer/IP Range not allowed';
	const WFWAF_BLOCK_COUNTRY = 'blocked access via country blocking';
	const WFWAF_BLOCK_COUNTRY_REDIR = 'blocked access via country blocking and redirected to URL';
	const WFWAF_BLOCK_COUNTRY_BYPASS_REDIR = 'redirected to bypass URL';
	const WFWAF_BLOCK_WFSN = 'Blocked by Wordfence Security Network';
	const WFWAF_BLOCK_BADPOST = 'POST received with blank user-agent and referer';
	const WFWAF_BLOCK_BANNEDURL = 'Accessed a banned URL.';
	const WFWAF_BLOCK_FAKEGOOGLE = 'Fake Google crawler automatically blocked';
	const WFWAF_BLOCK_LOGINSEC = 'Blocked by login security setting.';
	const WFWAF_BLOCK_LOGINSEC_FORGOTPASSWD = 'Exceeded the maximum number of tries to recover their password'; //substring search
	const WFWAF_BLOCK_LOGINSEC_FAILURES = 'Exceeded the maximum number of login failures'; //substring search
	const WFWAF_BLOCK_THROTTLEGLOBAL = 'Exceeded the maximum global requests per minute for crawlers or humans.';
	const WFWAF_BLOCK_THROTTLESCAN = 'Exceeded the maximum number of 404 requests per minute for a known security vulnerability.';
	const WFWAF_BLOCK_THROTTLECRAWLER = 'Exceeded the maximum number of requests per minute for crawlers.';
	const WFWAF_BLOCK_THROTTLECRAWLERNOTFOUND = 'Exceeded the maximum number of page not found errors per minute for a crawler.';
	const WFWAF_BLOCK_THROTTLEHUMAN = 'Exceeded the maximum number of page requests per minute for humans.';
	const WFWAF_BLOCK_THROTTLEHUMANNOTFOUND = 'Exceeded the maximum number of page not found errors per minute for humans.';
	
	protected static $_currentController = null;

	public static function currentController() {
		if (self::$_currentController === null) {
			self::$_currentController = new wfWAFIPBlocksController();
		}
		return self::$_currentController;
	}
	
	public static function setCurrentController($currentController) {
		self::$_currentController = $currentController;
	}
	
	/**
	 * Schedules a config sync to happen at the end of the current process's execution.
	 */
	public static function setNeedsSynchronizeConfigSettings() {
		static $willSynchronize = false;
		if (!$willSynchronize) {
			$willSynchronize = true;
			register_shutdown_function('wfWAFIPBlocksController::synchronizeConfigSettings');
		}
	}
	
	public static function synchronizeConfigSettings() {
		if (!class_exists('wfConfig')) { // Ensure this is only called when WordPress and the plugin are fully loaded
			return;
		}
		
		static $isSynchronizing = false;
		if ($isSynchronizing) {
			return;
		}
		$isSynchronizing = true;
		
		// Pattern Blocks
		$blocks = wfBlock::patternBlocks(true);
		$patternBlocks = array();
		foreach ($blocks as $b) {
			$patternBlocks[] = array('id' => $b->id, 'ipRange' => $b->ipRange, 'hostnamePattern' => $b->hostname, 'uaPattern' => $b->userAgent, 'refPattern' => $b->referrer, 'expiration' => $b->expiration);
		}
		
		// Country Blocks
		$countryBlocks = array();
		$countryBlockEntries = wfBlock::countryBlocks(true);
		$countryBlocks['blocks'] = array();
		foreach ($countryBlockEntries as $b) {
			$reason = __('Access from your area has been temporarily limited for security reasons', 'wordfence');
			
			$countryBlocks['blocks'][] = array(
				'id' => $b->id,
				'countries' => $b->countries,
				'blockLogin' => $b->blockLogin,
				'blockSite' => $b->blockSite,
				'reason' => $reason,
				'expiration' => $b->expiration,
			);
		}
		$countryBlocks['action'] = wfConfig::get('cbl_action', false);
		$countryBlocks['loggedInBlocked'] = wfConfig::get('cbl_loggedInBlocked', false);
		$countryBlocks['bypassRedirURL'] = wfConfig::get('cbl_bypassRedirURL', '');
		$countryBlocks['bypassRedirDest'] = wfConfig::get('cbl_bypassRedirDest', '');
		$countryBlocks['bypassViewURL'] = wfConfig::get('cbl_bypassViewURL', '');
		$countryBlocks['redirURL'] = wfConfig::get('cbl_redirURL', '');
		$countryBlocks['cookieVal'] = wfBlock::countryBlockingBypassCookieValue();
		
		//Other Blocks
		$otherBlocks = array('blockedTime' => wfConfig::get('blockedTime', 0));
		$otherBlockEntries = wfBlock::ipBlocks(true);
		$otherBlocks['blocks'] = array();
		foreach ($otherBlockEntries as $b) {
			$reason = $b->reason;
			if ($b->type == wfBlock::TYPE_IP_MANUAL || $b->type == wfBlock::TYPE_IP_AUTOMATIC_PERMANENT) {
				$reason = __('Manual block by administrator', 'wordfence');
			}
			
			$otherBlocks['blocks'][] = array(
				'id' => $b->id,
				'IP' => base64_encode(wfUtils::inet_pton($b->ip)),
				'reason' => $reason,
				'expiration' => $b->expiration,
			);
		}
		
		//Lockouts
		$lockoutEntries = wfBlock::lockouts(true);
		$lockoutSecs = wfConfig::get('loginSec_lockoutMins') * 60;
		$lockouts = array('lockedOutTime' => $lockoutSecs, 'lockouts' => array());
		foreach ($lockoutEntries as $l) {
			$lockouts['lockouts'][] = array(
				'id' => $l->id,
				'IP' => base64_encode(wfUtils::inet_pton($l->ip)),
				'reason' => $l->reason,
				'expiration' => $l->expiration,
			);
		}
		
		// Save it
		try {
			$patternBlocksJSON = wfWAFUtils::json_encode($patternBlocks);
			wfWAF::getInstance()->getStorageEngine()->setConfig('patternBlocks', $patternBlocksJSON);
			$countryBlocksJSON = wfWAFUtils::json_encode($countryBlocks);
			wfWAF::getInstance()->getStorageEngine()->setConfig('countryBlocks', $countryBlocksJSON);
			$otherBlocksJSON = wfWAFUtils::json_encode($otherBlocks);
			wfWAF::getInstance()->getStorageEngine()->setConfig('otherBlocks', $otherBlocksJSON);
			$lockoutsJSON = wfWAFUtils::json_encode($lockouts);
			wfWAF::getInstance()->getStorageEngine()->setConfig('lockouts', $lockoutsJSON);
			
			wfWAF::getInstance()->getStorageEngine()->setConfig('advancedBlockingEnabled', wfConfig::get('firewallEnabled'));
			wfWAF::getInstance()->getStorageEngine()->setConfig('disableWAFIPBlocking', wfConfig::get('disableWAFIPBlocking'));
		}
		catch (Exception $e) {
			// Do nothing
		}
		$isSynchronizing = false;
	}
	
	/**
	 * @param wfWAFRequest $request
	 * @return bool|string If not blocked, returns false. Otherwise a string of the reason it was blocked or true. 
	 */
	public function shouldBlockRequest($request) {
		// Checking the user whitelist is done before reaching this call
		
		$ip = $request->getIP();
		
		//Check the system whitelist
		if ($this->checkForWhitelisted($ip)) {
			return false;
		}
		
		//Let the plugin handle these
		$wfFunc = $request->getQueryString('_wfsf');
		if ($wfFunc == 'unlockEmail' || $wfFunc == 'unlockAccess') { // Can't check validity here, let it pass through to plugin level where it can
			return false;
		}
		
		$logHuman = $request->getQueryString('wordfence_lh');
		if ($logHuman !== null) {
			return false;
		}
		
		//Start block checks
		$ipNum = wfWAFUtils::inet_pton($ip);
		$hostname = null;
		$ua = $request->getHeaders('User-Agent'); if ($ua === null) { $ua = ''; }
		$referer = $request->getHeaders('Referer'); if ($referer === null) { $referer = ''; }
		
		$isPaid = false;
		try {
			$isPaid = wfWAF::getInstance()->getStorageEngine()->getConfig('isPaid');
			$pluginABSPATH = wfWAF::getInstance()->getStorageEngine()->getConfig('pluginABSPATH');
			
			$patternBlocksJSON = wfWAF::getInstance()->getStorageEngine()->getConfig('patternBlocks');
			$countryBlocksJSON = wfWAF::getInstance()->getStorageEngine()->getConfig('countryBlocks');
			$otherBlocksJSON = wfWAF::getInstance()->getStorageEngine()->getConfig('otherBlocks');
			$lockoutsJSON = wfWAF::getInstance()->getStorageEngine()->getConfig('lockouts');
		}
		catch (Exception $e) {
			// Do nothing
		}
		
		if (isset($_SERVER['SCRIPT_FILENAME']) && (strpos($_SERVER['SCRIPT_FILENAME'], $pluginABSPATH . "wp-admin/") === 0 || strpos($_SERVER['SCRIPT_FILENAME'], $pluginABSPATH . "wp-content/") === 0 || strpos($_SERVER['SCRIPT_FILENAME'], $pluginABSPATH . "wp-includes/") === 0)) {
			return false; //Rely on WordPress's own access control and blocking at the plugin level
		}
		
		// Pattern Blocks from the Advanced Blocking page (IP Range, UA, Referer)
		$patternBlocks = @wfWAFUtils::json_decode($patternBlocksJSON, true);
		if (is_array($patternBlocks)) {
			// Instead of a long block of if/else statements, using bitshifting to generate an expected value and a found value
			$ipRangeOffset = 1;
			$uaPatternOffset = 2;
			$refPatternOffset = 3;
			
			foreach ($patternBlocks as $b) {
				$expectedBits = 0;
				$foundBits = 0;
				
				if (isset($b['expiration']) && $b['expiration'] < time() && $b['expiration'] != 0) {
					continue;
				}
				
				if (!empty($b['ipRange'])) {
					$expectedBits |= (1 << $ipRangeOffset);
					
					$range = new wfWAFUserIPRange($b['ipRange']); 
					if ($range->isIPInRange($ip)) {
						$foundBits |= (1 << $ipRangeOffset);
					}
				}
				
				if (!empty($b['hostnamePattern'])) {
					$expectedBits |= (1 << $ipRangeOffset);
					if ($hostname === null) {
						$hostname = wfWAFUtils::reverseLookup($ip);
					}
					if (preg_match(wfWAFUtils::patternToRegex($b['hostnamePattern']), $hostname)) {
						$foundBits |= (1 << $ipRangeOffset);
					}
				}
				
				if (!empty($b['uaPattern'])) {
					$expectedBits |= (1 << $uaPatternOffset);
					if (wfWAFUtils::isUABlocked($b['uaPattern'], $ua)) {
						$foundBits |= (1 << $uaPatternOffset);
					}
				}
				
				if (!empty($b['refPattern'])) {
					$expectedBits |= (1 << $refPatternOffset);
					if (wfWAFUtils::isRefererBlocked($b['refPattern'], $referer)) {
						$foundBits |= (1 << $refPatternOffset);
					}
				}
				
				if ($foundBits === $expectedBits && $expectedBits > 0) {
					return array('action' => self::WFWAF_BLOCK_UAREFIPRANGE, 'id' => $b['id']);
				}
			}
		}
		// End Pattern Blocks
		
		// Country Blocking
		if ($isPaid) {
			$countryBlocks = @wfWAFUtils::json_decode($countryBlocksJSON, true);
			if (is_array($countryBlocks) && isset($countryBlocks['blocks'])) {
				$blocks = $countryBlocks['blocks'];
				foreach ($blocks as $b) {
					$blockedCountries = $b['countries'];
					$bareRequestURI = wfWAFUtils::extractBareURI($request->getURI());
					$bareBypassRedirURI = wfWAFUtils::extractBareURI($countryBlocks['bypassRedirURL']);
					$skipCountryBlocking = false;
					
					if ($bareBypassRedirURI && $bareRequestURI == $bareBypassRedirURI) { // Run this before country blocking because even if the user isn't blocked we need to set the bypass cookie so they can bypass future blocks.
						if ($countryBlocks['bypassRedirDest']) {
							setcookie('wfCBLBypass', $countryBlocks['cookieVal'], time() + (86400 * 365), '/', null, $this->isFullSSL(), true);
							return array('action' => self::WFWAF_BLOCK_COUNTRY_BYPASS_REDIR, 'id' => $b['id']);
						}
					}
					
					$bareBypassViewURI = wfWAFUtils::extractBareURI($countryBlocks['bypassViewURL']);
					if ($bareBypassViewURI && $bareBypassViewURI == $bareRequestURI) {
						setcookie('wfCBLBypass', $countryBlocks['cookieVal'], time() + (86400 * 365), '/', null, $this->isFullSSL(), true);
						$skipCountryBlocking = true;
					}
					
					$bypassCookieSet = false;
					$bypassCookie = $request->getCookies('wfCBLBypass');
					if (isset($bypassCookie) && $bypassCookie == $countryBlocks['cookieVal']) {
						$bypassCookieSet = true;
					}
					
					if (!$skipCountryBlocking && $blockedCountries && !$bypassCookieSet) {
						$isAuthRequest = (strpos($bareRequestURI, '/wp-login.php') !== false);
						$isXMLRPC = (strpos($bareRequestURI, '/xmlrpc.php') !== false);
						$isUserLoggedIn = wfWAF::getInstance()->parseAuthCookie() !== false;
						
						// If everything is checked, make sure this always runs.
						if ($countryBlocks['loggedInBlocked'] && $b['blockLogin'] && $b['blockSite']) {
							if ($blocked = $this->checkForBlockedCountry($countryBlocks, $ip, $bareRequestURI)) { $blocked['id'] = $b['id']; return $blocked; }
						}
						// Block logged in users.
						if ($countryBlocks['loggedInBlocked'] && $isUserLoggedIn) {
							if ($blocked = $this->checkForBlockedCountry($countryBlocks, $ip, $bareRequestURI)) { $blocked['id'] = $b['id']; return $blocked; }
						}
						// Block the login form itself and any attempt to authenticate.
						if ($b['blockLogin'] && $isAuthRequest) {
							if ($blocked = $this->checkForBlockedCountry($countryBlocks, $ip, $bareRequestURI)) { $blocked['id'] = $b['id']; return $blocked; }
						}
						// Block requests that aren't to the login page, xmlrpc.php, or a user already logged in.
						if ($b['blockSite'] && !$isAuthRequest && !$isXMLRPC && !$isUserLoggedIn) {
							if ($blocked = $this->checkForBlockedCountry($countryBlocks, $ip, $bareRequestURI)) { $blocked['id'] = $b['id']; return $blocked; }
						}
						// XMLRPC is inaccesible when public portion of the site and auth is disabled.
						if ($b['blockLogin'] && $b['blockSite'] && $isXMLRPC) {
							if ($blocked = $this->checkForBlockedCountry($countryBlocks, $ip, $bareRequestURI)) { $blocked['id'] = $b['id']; return $blocked; }
						}
						
						// Any bypasses and other block possibilities will be checked at the plugin level once WordPress loads
					}
				}
			}
		}
		// End Country Blocking
		
		// Other Blocks
		$otherBlocks = @wfWAFUtils::json_decode($otherBlocksJSON, true);
		if (is_array($otherBlocks)) {
			$blocks = $otherBlocks['blocks'];
			$bareRequestURI = wfWAFUtils::extractBareURI($request->getURI());
			$isAuthRequest = (stripos($bareRequestURI, '/wp-login.php') !== false);
			foreach ($blocks as $b) {
				if (isset($b['expiration']) && $b['expiration'] < time() && $b['expiration'] != 0) {
					continue;
				}
				
				if (base64_decode($b['IP']) != $ipNum) {
					continue;
				}
				
				if ($isAuthRequest && isset($b['wfsn']) && $b['wfsn']) {
					return array('action' => self::WFWAF_BLOCK_WFSN, 'id' => $b['id']);
				}
				
				return array('action' => (empty($b['reason']) ? '' : $b['reason']), 'id' => $b['id'], 'block' => true);
			}
		}
		// End Other Blocks
		
		// Lockouts
		$lockouts = @wfWAFUtils::json_decode($lockoutsJSON, true);
		if (is_array($lockouts)) {
			$lockouts = $lockouts['lockouts'];
			$isAuthRequest = (stripos($bareRequestURI, '/wp-login.php') !== false) || (stripos($bareRequestURI, '/xmlrpc.php') !== false);
			if ($isAuthRequest) {
				foreach ($lockouts as $l) {
					if (isset($l['expiration']) && $l['expiration'] < time()) {
						continue;
					}
					
					if (base64_decode($l['IP']) != $ipNum) {
						continue;
					}
					
					return array('action' => (empty($l['reason']) ? '' : $l['reason']), 'id' => $l['id'], 'lockout' => true);
				}
			}
		}
		// End Lockouts
		
		return false;
	}
	
	public function countryRedirURL($countryBlocks = null) {
		if (!isset($countryBlocks)) {
			try {
				$countryBlocksJSON = wfWAF::getInstance()->getStorageEngine()->getConfig('countryBlocks');
			}
			catch (Exception $e) {
				return false;
			}
		}
		
		$countryBlocks = @wfWAFUtils::json_decode($countryBlocksJSON, true);
		if (is_array($countryBlocks)) {
			if ($countryBlocks['action'] == 'redir') {
				return $countryBlocks['redirURL'];
			}
		}
		return false;
	}
	
	public function countryBypassRedirURL($countryBlocks = null) {
		if (!isset($countryBlocks)) {
			try {
				$countryBlocksJSON = wfWAF::getInstance()->getStorageEngine()->getConfig('countryBlocks');
			}
			catch (Exception $e) {
				return false;
			}
		}
		
		$countryBlocks = @wfWAFUtils::json_decode($countryBlocksJSON, true);
		if (is_array($countryBlocks)) {
			return $countryBlocks['bypassRedirDest'];
		}
		return false;
	}
	
	protected function checkForBlockedCountry($countryBlock, $ip, $bareRequestURI) {
		try {
			$homeURL = wfWAF::getInstance()->getStorageEngine()->getConfig('homeURL');
		}
		catch (Exception $e) {
			//Do nothing
		}
		
		$bareRequestURI = rtrim($bareRequestURI, '/\\');
		if ($country = $this->ip2Country($ip)) {
			$blocks = $countryBlock['blocks'];
			foreach ($blocks as $b) {
				foreach ($b['countries'] as $blocked) {
					if (strtoupper($blocked) == strtoupper($country)) {
						if ($countryBlock['action'] == 'redir') {
							$redirURL = $countryBlock['redirURL'];
							$eRedirHost = wfWAFUtils::extractHostname($redirURL);
							$isExternalRedir = false;
							if ($eRedirHost && $homeURL && $eRedirHost != wfWAFUtils::extractHostname($homeURL)) {
								$isExternalRedir = true;
							}
							
							if ((!$isExternalRedir) && rtrim(wfWAFUtils::extractBareURI($redirURL), '/\\') == $bareRequestURI){ //Is this the URI we want to redirect to, then don't block it
								//Do nothing
							}
							else {
								return array('action' => self::WFWAF_BLOCK_COUNTRY_REDIR);
							}
						}
						else {
							return array('action' => self::WFWAF_BLOCK_COUNTRY);
						}
					}
				}
			}
		}
		
		return false;
	}
	
	protected function checkForWhitelisted($ip) {
		$wordfenceLib = realpath(dirname(__FILE__) . '/../lib');
		include($wordfenceLib . '/wfIPWhitelist.php'); /** @var $wfIPWhitelist */
		foreach ($wfIPWhitelist as $group) {
			foreach ($group as $subnet) {
				if ($subnet instanceof wfWAFUserIPRange) { //Not currently reached
					if ($subnet->isIPInRange($ip)) {
						return true;
					}
				} elseif (wfWAFUtils::subnetContainsIP($subnet, $ip)) {
					return true;
				}
			}
		}
		return false;
	}
	
	protected function ip2Country($ip){
		$wordfenceLib = realpath(dirname(__FILE__) . '/../lib');
		require_once(dirname(__FILE__) . '/wfWAFGeoIP.php');
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			$gi = geoip_open($wordfenceLib . "/GeoIPv6.dat", WF_GEOIP_STANDARD);
			$country = geoip_country_code_by_addr_v6($gi, $ip);
		} else {
			$gi = geoip_open($wordfenceLib . "/GeoIP.dat", WF_GEOIP_STANDARD);
			$country = geoip_country_code_by_addr($gi, $ip);
		}
		geoip_close($gi);
		return $country ? $country : '';
	}
	
	/**
	 * Returns whether or not the site should be treated as if it's full-time SSL.
	 *
	 * @return bool
	 */
	protected function isFullSSL() {
		try {
			$is_ssl = false; //This is the same code from WP modified so we can use it here
			if ( isset( $_SERVER['HTTPS'] ) ) {
				if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) {
					$is_ssl = true;
				}
				
				if ( '1' == $_SERVER['HTTPS'] ) {
					$is_ssl = true;
				}
			} elseif ( isset($_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
				$is_ssl = true;
			}
			
			$homeURL = wfWAF::getInstance()->getStorageEngine()->getConfig('homeURL');
			return $is_ssl && parse_url($homeURL, PHP_URL_SCHEME) === 'https';
		}
		catch (Exception $e) {
			//Do nothing
		}
		
		return false;
	}
}