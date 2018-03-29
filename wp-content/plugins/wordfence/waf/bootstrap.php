<?php

/*
	php_value auto_prepend_file ~/wp-content/plugins/wordfence/waf/bootstrap.php
*/

if (!defined('WFWAF_AUTO_PREPEND')) {
	define('WFWAF_AUTO_PREPEND', true);
}

require_once dirname(__FILE__) . '/wfWAFUserIPRange.php';
require_once dirname(__FILE__) . '/wfWAFIPBlocksController.php';
require_once dirname(__FILE__) . '/../vendor/wordfence/wf-waf/src/init.php';

class wfWAFWordPressRequest extends wfWAFRequest {
	
	/**
	 * @param wfWAFRequest|null $request
	 * @return wfWAFRequest
	 */
	public static function createFromGlobals($request = null) {
		if (version_compare(phpversion(), '5.3.0') >= 0) {
			$class = get_called_class();
			$request = new $class();
		} else {
			$request = new self();
		}
		return parent::createFromGlobals($request);
	}

	public function getIP() {
		static $theIP = null;
		if (isset($theIP)) {
			return $theIP;
		}
		$howGet = wfWAF::getInstance()->getStorageEngine()->getConfig('howGetIPs');
		if (is_string($howGet) && is_array($_SERVER) && array_key_exists($howGet, $_SERVER)) {
			$ips[] = array($_SERVER[$howGet], $howGet);
		}
		$ips[] = array((is_array($_SERVER) && array_key_exists('REMOTE_ADDR', $_SERVER)) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1', 'REMOTE_ADDR');
		$cleanedIP = $this->_getCleanIPAndServerVar($ips);
		if (is_array($cleanedIP)) {
			list($ip, $variable) = $cleanedIP;
			$theIP = $ip;
			return $ip;
		}
		$theIP = $cleanedIP;
		return $cleanedIP;
	}
	
	/**
	 * Expects an array of items. The items are either IPs or IPs separated by comma, space or tab. Or an array of IP's.
	 * We then examine all IP's looking for a public IP and storing private IP's in an array. If we find no public IPs we return the first private addr we found.
	 *
	 * @param array $arr
	 * @return bool|mixed
	 */
	private function _getCleanIPAndServerVar($arr) {
		$privates = array(); //Store private addrs until end as last resort.
		foreach ($arr as $entry) {
			list($item, $var) = $entry;
			if (is_array($item)) {
				foreach ($item as $j) {
					// try verifying the IP is valid before stripping the port off
					if (!$this->_isValidIP($j)) {
						$j = preg_replace('/:\d+$/', '', $j); //Strip off port
					}
					if ($this->_isValidIP($j)) {
						if ($this->_isIPv6MappedIPv4($j)) {
							$j = wfWAFUtils::inet_ntop(wfWAFUtils::inet_pton($j));
						}
						
						if ($this->_isPrivateIP($j)) {
							$privates[] = array($j, $var);
						}
						else {
							return array($j, $var);
						}
					}
				}
				continue; //This was an array so we can skip to the next item
			}
			$skipToNext = false;
			$trustedProxies = explode("\n", wfWAF::getInstance()->getStorageEngine()->getConfig('howGetIPs_trusted_proxies', ''));
			foreach (array(',', ' ', "\t") as $char) {
				if (strpos($item, $char) !== false) {
					$sp = explode($char, $item);
					$sp = array_reverse($sp);
					foreach ($sp as $index => $j) {
						$j = trim($j);
						if (!$this->_isValidIP($j)) {
							$j = preg_replace('/:\d+$/', '', $j); //Strip off port
						}
						if ($this->_isValidIP($j)) {
							if ($this->_isIPv6MappedIPv4($j)) {
								$j = wfWAFUtils::inet_ntop(wfWAFUtils::inet_pton($j));
							}
							
							foreach ($trustedProxies as $proxy) {
								if (!empty($proxy)) {
									if (wfWAFUtils::subnetContainsIP($proxy, $j) && $index < count($sp) - 1) {
										continue 2;
									}
								}
							}
							
							if ($this->_isPrivateIP($j)) {
								$privates[] = array($j, $var);
							}
							else {
								return array($j, $var);
							}
						}
					}
					$skipToNext = true;
					break;
				}
			}
			if ($skipToNext){ continue; } //Skip to next item because this one had a comma, space or tab so was delimited and we didn't find anything.
			
			if (!$this->_isValidIP($item)) {
				$item = preg_replace('/:\d+$/', '', $item); //Strip off port
			}
			if ($this->_isValidIP($item)) {
				if ($this->_isIPv6MappedIPv4($item)) {
					$item = wfWAFUtils::inet_ntop(wfWAFUtils::inet_pton($item));
				}
				
				if ($this->_isPrivateIP($item)) {
					$privates[] = array($item, $var);
				}
				else {
					return array($item, $var);
				}
			}
		}
		if (sizeof($privates) > 0) {
			return $privates[0]; //Return the first private we found so that we respect the order the IP's were passed to this function.
		}
		return false;
	}
	
	/**
	 * @param string $ip
	 * @return bool
	 */
	private function _isValidIP($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP) !== false;
	}
	
	/**
	 * @param string $ip
	 * @return bool
	 */
	private function _isIPv6MappedIPv4($ip) {
		return preg_match('/^(?:\:(?:\:0{1,4}){0,4}\:|(?:0{1,4}\:){5})ffff\:\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/i', $ip) > 0;
	}
	
	/**
	 * @param string $addr Should be in dot or colon notation (127.0.0.1 or ::1)
	 * @return bool
	 */
	private function _isPrivateIP($ip) {
		// Run this through the preset list for IPv4 addresses.
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
			$wordfenceLib = realpath(dirname(__FILE__) . '/../lib');
			include($wordfenceLib . '/wfIPWhitelist.php'); // defines $wfIPWhitelist
			$private = $wfIPWhitelist['private'];
			
			foreach ($private as $a) {
				if (wfWAFUtils::subnetContainsIP($a, $ip)) {
					return true;
				}
			}
		}
		
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false
		&& filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
	}
}

class wfWAFWordPressObserver extends wfWAFBaseObserver {

	public function beforeRunRules() {
		// Whitelisted URLs (in WAF config)
		$whitelistedURLs = wfWAF::getInstance()->getStorageEngine()->getConfig('whitelistedURLs');
		if ($whitelistedURLs) {
			$whitelistPattern = "";
			foreach ($whitelistedURLs as $whitelistedURL) {
				$whitelistPattern .= preg_replace('/\\\\\*/', '.*?', preg_quote($whitelistedURL, '/')) . '|';
			}
			$whitelistPattern = '/^(?:' . wfWAFUtils::substr($whitelistPattern, 0, -1) . ')$/i';

			wfWAFRule::create(wfWAF::getInstance(), 0x8000000, 'rule', 'whitelist', 0, 'User Supplied Whitelisted URL', 'allow',
				new wfWAFRuleComparisonGroup(
					new wfWAFRuleComparison(wfWAF::getInstance(), 'match', $whitelistPattern, array(
						'request.uri',
					))
				)
			)->evaluate();
		}

		// Whitelisted IPs (Wordfence config)
		$whitelistedIPs = wfWAF::getInstance()->getStorageEngine()->getConfig('whitelistedIPs');
		if ($whitelistedIPs) {
			if (!is_array($whitelistedIPs)) {
				$whitelistedIPs = explode(',', $whitelistedIPs);
			}
			foreach ($whitelistedIPs as $whitelistedIP) {
				$ipRange = new wfWAFUserIPRange($whitelistedIP);
				if ($ipRange->isIPInRange(wfWAF::getInstance()->getRequest()->getIP())) {
					throw new wfWAFAllowException('Wordfence whitelisted IP.');
				}
			}
		}
		
		// Check plugin blocking
		if ($result = wfWAF::getInstance()->willPerformFinalAction(wfWAF::getInstance()->getRequest())) {
			if ($result === true) { $result = 'Not available'; } // Should not happen but can if the reason in the blocks table is empty
			wfWAF::getInstance()->getRequest()->setMetadata(array_merge(wfWAF::getInstance()->getRequest()->getMetadata(), array('finalAction' => $result)));
		}
	}
	
	public function afterRunRules()
	{
		//Blacklist
		if (!wfWAF::getInstance()->getStorageEngine()->getConfig('disableWAFBlacklistBlocking')) {
			$blockedPrefixes = wfWAF::getInstance()->getStorageEngine()->getConfig('blockedPrefixes');
			if ($blockedPrefixes && wfWAF::getInstance()->getStorageEngine()->getConfig('isPaid')) {
				$blockedPrefixes = base64_decode($blockedPrefixes);
				if ($this->_prefixListContainsIP($blockedPrefixes, wfWAF::getInstance()->getRequest()->getIP()) !== false) {
					$allowedCacheJSON = wfWAF::getInstance()->getStorageEngine()->getConfig('blacklistAllowedCache', '');
					$allowedCache = @json_decode($allowedCacheJSON, true);
					if (!is_array($allowedCache)) {
						$allowedCache = array();
					}
					
					$cacheTest = base64_encode(wfWAFUtils::inet_pton(wfWAF::getInstance()->getRequest()->getIP()));
					if (!in_array($cacheTest, $allowedCache)) {
						$guessSiteURL = sprintf('%s://%s/', wfWAF::getInstance()->getRequest()->getProtocol(), wfWAF::getInstance()->getRequest()->getHost());
						try {
							$request = new wfWAFHTTP();
							$response = wfWAFHTTP::get(WFWAF_API_URL_SEC . "?" . http_build_query(array(
									'action' => 'is_ip_blacklisted',
									'ip'	 => wfWAF::getInstance()->getRequest()->getIP(),
									'k'      => wfWAF::getInstance()->getStorageEngine()->getConfig('apiKey'),
									's'      => wfWAF::getInstance()->getStorageEngine()->getConfig('siteURL') ? wfWAF::getInstance()->getStorageEngine()->getConfig('siteURL') : $guessSiteURL,
									'h'      => wfWAF::getInstance()->getStorageEngine()->getConfig('homeURL') ? wfWAF::getInstance()->getStorageEngine()->getConfig('homeURL') : $guessSiteURL,
									't'		 => microtime(true),
								), null, '&'), $request);
							
							if ($response instanceof wfWAFHTTPResponse && $response->getBody()) {
								$jsonData = wfWAFUtils::json_decode($response->getBody(), true);
								if (array_key_exists('data', $jsonData)) {
									if (preg_match('/^block:(\d+)$/i', $jsonData['data'], $matches)) {
										wfWAF::getInstance()->getStorageEngine()->blockIP((int)$matches[1] + time(), wfWAF::getInstance()->getRequest()->getIP(), wfWAFStorageInterface::IP_BLOCKS_BLACKLIST);
										$e = new wfWAFBlockException();
										$e->setFailedRules(array('blocked'));
										$e->setRequest(wfWAF::getInstance()->getRequest());
										throw $e;
									}
									else { //Allowed, cache until the next prefix list refresh
										$allowedCache[] = $cacheTest;
										wfWAF::getInstance()->getStorageEngine()->setConfig('blacklistAllowedCache', json_encode($allowedCache));
									}
								}
							}
						} catch (wfWAFHTTPTransportException $e) {
							error_log($e->getMessage());
						}
					}
				}
			}
		}
		
		//wfWAFLogException
		$watchedIPs = wfWAF::getInstance()->getStorageEngine()->getConfig('watchedIPs');
		if ($watchedIPs) {
			if (!is_array($watchedIPs)) {
				$watchedIPs = explode(',', $watchedIPs);
			}
			foreach ($watchedIPs as $watchedIP) {
				$ipRange = new wfWAFUserIPRange($watchedIP);
				if ($ipRange->isIPInRange(wfWAF::getInstance()->getRequest()->getIP())) {
					throw new wfWAFLogException('Wordfence watched IP.');
				}
			}
		}
		
		if ($reason = wfWAF::getInstance()->getRequest()->getMetadata('finalAction')) {
			$e = new wfWAFBlockException($reason['action']);
			$e->setRequest(wfWAF::getInstance()->getRequest());
			throw $e;
		}
	}
	
	private function _prefixListContainsIP($prefixList, $ip) {
		$size = ord(wfWAFUtils::substr($prefixList, 0, 1));
		
		$sha256 = hash('sha256', wfWAFUtils::inet_pton($ip), true);
		$p = wfWAFUtils::substr($sha256, 0, $size);
		
		$count = ceil((wfWAFUtils::strlen($prefixList) - 1) / $size);
		$low = 0;
		$high = $count - 1;
		
		while ($low <= $high) {
			$mid = (int) (($high + $low) / 2);
			$val = wfWAFUtils::substr($prefixList, 1 + $mid * $size, $size);
			$cmp = strcmp($val, $p);
			if ($cmp < 0) {
				$low = $mid + 1;
			}
			else if ($cmp > 0) {
				$high = $mid - 1;
			}
			else {
				return $mid;
			}
		}
		
		return false;
	}
}

/**
 *
 */
class wfWAFWordPress extends wfWAF {

	/** @var wfWAFRunException */
	private $learningModeAttackException;

	/**
	 * @param wfWAFBlockException $e
	 * @param int $httpCode
	 */
	public function blockAction($e, $httpCode = 403, $redirect = false, $template = null) {
		$failedRules = $e->getFailedRules();
		if (!is_array($failedRules)) {
			$failedRules = array();
		}
		
		if ($this->isInLearningMode() && !$e->getRequest()->getMetadata('finalAction') && !in_array('blocked', $failedRules)) {
			register_shutdown_function(array(
				$this, 'whitelistFailedRulesIfNot404',
			));
			$this->getStorageEngine()->logAttack($e->getFailedRules(), $e->getParamKey(), $e->getParamValue(), $e->getRequest());
			$this->setLearningModeAttackException($e);
		} else {
			if (empty($failedRules)) {
				$finalAction = $e->getRequest()->getMetadata('finalAction');
				if (is_array($finalAction)) {
					$isLockedOut = isset($finalAction['lockout']) && $finalAction['lockout'];
					$finalAction = $finalAction['action'];
					if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_COUNTRY_REDIR) {
						$redirect = wfWAFIPBlocksController::currentController()->countryRedirURL();
					}
					else if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_COUNTRY_BYPASS_REDIR) {
						$redirect = wfWAFIPBlocksController::currentController()->countryBypassRedirURL();
					}
					else if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_UAREFIPRANGE) {
						wfWAF::getInstance()->getRequest()->setMetadata(array_merge(wfWAF::getInstance()->getRequest()->getMetadata(), array('503Reason' => 'Advanced blocking in effect.', '503Time' => 3600)));
						$httpCode = 503;
					}
					else if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_COUNTRY) {
						wfWAF::getInstance()->getRequest()->setMetadata(array_merge(wfWAF::getInstance()->getRequest()->getMetadata(), array('503Reason' => 'Access from your area has been temporarily limited for security reasons.', '503Time' => 3600)));
						$httpCode = 503;
					}
					else if (is_string($finalAction) && strlen($finalAction) > 0) {
						wfWAF::getInstance()->getRequest()->setMetadata(array_merge(wfWAF::getInstance()->getRequest()->getMetadata(), array('503Reason' => $finalAction, '503Time' => 3600)));
						$httpCode = 503;
						
						if ($isLockedOut) {
							parent::blockAction($e, $httpCode, $redirect, '503-lockout'); //exits
						}
					}
				}
			}
			else if (array_search('blocked', $failedRules) !== false) {
				parent::blockAction($e, $httpCode, $redirect, '403-blacklist'); //exits
			}
			
			parent::blockAction($e, $httpCode, $redirect, $template);
		}
	}

	/**
	 * @param wfWAFBlockXSSException $e
	 * @param int $httpCode
	 */
	public function blockXSSAction($e, $httpCode = 403, $redirect = false) {
		if ($this->isInLearningMode() && !$e->getRequest()->getMetadata('finalAction')) {
			register_shutdown_function(array(
				$this, 'whitelistFailedRulesIfNot404',
			));
			$this->getStorageEngine()->logAttack($e->getFailedRules(), $e->getParamKey(), $e->getParamValue(), $e->getRequest());
			$this->setLearningModeAttackException($e);
		} else {
			$failedRules = $e->getFailedRules();
			if (empty($failedRules)) {
				$finalAction = $e->getRequest()->getMetadata('finalAction');
				if (is_array($finalAction)) {
					$finalAction = $finalAction['action'];
					if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_COUNTRY_REDIR) {
						$redirect = wfWAFIPBlocksController::currentController()->countryRedirURL();
					}
					else if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_COUNTRY_BYPASS_REDIR) {
						$redirect = wfWAFIPBlocksController::currentController()->countryBypassRedirURL();
					}
					else if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_UAREFIPRANGE) {
						wfWAF::getInstance()->getRequest()->setMetadata(array_merge(wfWAF::getInstance()->getRequest()->getMetadata(), array('503Reason' => 'Advanced blocking in effect.', '503Time' => 3600)));
						$httpCode = 503;
					}
					else if ($finalAction == wfWAFIPBlocksController::WFWAF_BLOCK_COUNTRY) {
						wfWAF::getInstance()->getRequest()->setMetadata(array_merge(wfWAF::getInstance()->getRequest()->getMetadata(), array('503Reason' => 'Access from your area has been temporarily limited for security reasons.', '503Time' => 3600)));
						$httpCode = 503;
					}
					else if (is_string($finalAction) && strlen($finalAction) > 0) {
						wfWAF::getInstance()->getRequest()->setMetadata(array_merge(wfWAF::getInstance()->getRequest()->getMetadata(), array('503Reason' => $finalAction, '503Time' => 3600)));
						$httpCode = 503;
					}
				}
			}
			
			parent::blockXSSAction($e, $httpCode, $redirect);
		}
	}

	/**
	 *
	 */
	public function runCron() {
		/**
		 * Removed sending attack data. Attack data is sent in @see wordfence::veryFirstAction
		 */
		$cron = $this->getStorageEngine()->getConfig('cron');
		if (is_array($cron)) {
			/** @var wfWAFCronEvent $event */
			$cronDeduplication = array();
			foreach ($cron as $index => $event) {
				$event->setWaf($this);
				if ($event->isInPast()) {
					$event->fire();
					$newEvent = $event->reschedule();
					$className = get_class($newEvent);
					if ($newEvent instanceof wfWAFCronEvent && $newEvent !== $event && !in_array($className, $cronDeduplication)) {
						$cron[$index] = $newEvent;
						$cronDeduplication[] = $className;
					} else {
						unset($cron[$index]);
					}
				}
				else {
					$className = get_class($event);
					if (in_array($className, $cronDeduplication)) {
						unset($cron[$index]);
					}
					else {
						$cronDeduplication[] = $className;
					}
				}
			}
		}
		$this->getStorageEngine()->setConfig('cron', $cron);
	}

	/**
	 *
	 */
	public function whitelistFailedRulesIfNot404() {
		/** @var WP_Query $wp_query */
		global $wp_query;
		if (defined('ABSPATH') &&
			isset($wp_query) && class_exists('WP_Query') && $wp_query instanceof WP_Query &&
			method_exists($wp_query, 'is_404') && $wp_query->is_404() &&
			function_exists('is_admin') && !is_admin()) {
			return;
		}
		$this->whitelistFailedRules();
	}

	/**
	 * @param $ip
	 * @return mixed
	 */
	public function isIPBlocked($ip) {
		return parent::isIPBlocked($ip);
	}
	
	/**
	 * @param wfWAFRequest $request
	 * @return bool|string false if it should not be blocked, otherwise true or a reason for blocking 
	 */
	public function willPerformFinalAction($request) {
		try {
			$disableWAFIPBlocking = $this->getStorageEngine()->getConfig('disableWAFIPBlocking');
			$advancedBlockingEnabled = $this->getStorageEngine()->getConfig('advancedBlockingEnabled');
		}
		catch (Exception $e) {
			return false;
		}
		
		if ($disableWAFIPBlocking || !$advancedBlockingEnabled) {
			return false;
		}
		
		return wfWAFIPBlocksController::currentController()->shouldBlockRequest($request);
	}
	
	public function uninstall() {
		parent::uninstall();
		@unlink(rtrim(WFWAF_LOG_PATH . '/') . '/.htaccess');
		@rmdir(WFWAF_LOG_PATH);
	}

	/**
	 * @return wfWAFRunException
	 */
	public function getLearningModeAttackException() {
		return $this->learningModeAttackException;
	}

	/**
	 * @param wfWAFRunException $learningModeAttackException
	 */
	public function setLearningModeAttackException($learningModeAttackException) {
		$this->learningModeAttackException = $learningModeAttackException;
	}
}

if (!defined('WFWAF_LOG_PATH')) {
	define('WFWAF_LOG_PATH', WP_CONTENT_DIR . '/wflogs/');
}
if (!is_dir(WFWAF_LOG_PATH)) {
	@mkdir(WFWAF_LOG_PATH, 0775);
	@chmod(WFWAF_LOG_PATH, 0775);
	@file_put_contents(rtrim(WFWAF_LOG_PATH . '/') . '/.htaccess', <<<APACHE
<IfModule mod_authz_core.c>
	Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
	Order deny,allow
	Deny from all
</IfModule>
APACHE
	);
	@chmod(rtrim(WFWAF_LOG_PATH . '/') . '/.htaccess', 0664);
}


wfWAF::setInstance(new wfWAFWordPress(
	wfWAFWordPressRequest::createFromGlobals(),
	new wfWAFStorageFile(WFWAF_LOG_PATH . 'attack-data.php', WFWAF_LOG_PATH . 'ips.php', WFWAF_LOG_PATH . 'config.php', WFWAF_LOG_PATH . 'wafRules.rules')
));
wfWAF::getInstance()->getEventBus()->attach(new wfWAFWordPressObserver);

try {
	$rulesFiles = array(
		WFWAF_LOG_PATH . 'rules.php',
		// WFWAF_PATH . 'rules.php',
	);
	foreach ($rulesFiles as $rulesFile) {
		if (!file_exists($rulesFile) && !wfWAF::getInstance()->isReadOnly()) {
			@touch($rulesFile);
		}
		@chmod($rulesFile, 0664);
		if (is_writable($rulesFile)) {
			wfWAF::getInstance()->setCompiledRulesFile($rulesFile);
			break;
		}
	}
		
	if (!wfWAF::getInstance()->isReadOnly()) {
		if (!file_exists(wfWAF::getInstance()->getCompiledRulesFile()) || !filesize(wfWAF::getInstance()->getCompiledRulesFile())) {
			try {
				if (is_writable(wfWAF::getInstance()->getCompiledRulesFile()) &&
					wfWAF::getInstance()->getStorageEngine()->getConfig('apiKey') !== null &&
					wfWAF::getInstance()->getStorageEngine()->getConfig('createInitialRulesDelay') < time()
				) {
					$event = new wfWAFCronFetchRulesEvent(time() - 60);
					$event->setWaf(wfWAF::getInstance());
					$event->fire();
					wfWAF::getInstance()->getStorageEngine()->setConfig('createInitialRulesDelay', time() + (5 * 60));
				}
			} catch (wfWAFBuildRulesException $e) {
				// Log this somewhere
				error_log($e->getMessage());
			} catch (Exception $e) {
				// Suppress this
				error_log($e->getMessage());
			}
		}
	}

	if (WFWAF_DEBUG && file_exists(wfWAF::getInstance()->getStorageEngine()->getRulesDSLCacheFile())) {
		try {
			wfWAF::getInstance()->updateRuleSet(file_get_contents(wfWAF::getInstance()->getStorageEngine()->getRulesDSLCacheFile()), false);
		} catch (wfWAFBuildRulesException $e) {
			$GLOBALS['wfWAFDebugBuildException'] = $e;
		} catch (Exception $e) {
			$GLOBALS['wfWAFDebugBuildException'] = $e;
		}
	}

	try {
		wfWAF::getInstance()->run();
	} catch (wfWAFBuildRulesException $e) {
		// Log this
		error_log($e->getMessage());
	} catch (Exception $e) {
		// Suppress this
		error_log($e->getMessage());
	}

} catch (wfWAFStorageFileConfigException $e) {
	// Let this request through for now
	error_log($e->getMessage());

} catch (wfWAFStorageFileException $e) {
	// We need to choose another storage engine here.
}
