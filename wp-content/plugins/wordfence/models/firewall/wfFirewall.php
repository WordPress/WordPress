<?php

class wfFirewall
{
	const FIREWALL_MODE_DISABLED = 'disabled';
	const FIREWALL_MODE_LEARNING = 'learning-mode';
	const FIREWALL_MODE_ENABLED = 'enabled';
	
	const PROTECTION_MODE_EXTENDED = 'extended';
	const PROTECTION_MODE_BASIC = 'basic';
	
	const RULE_MODE_COMMUNITY = 'community';
	const RULE_MODE_PREMIUM = 'premium';
	
	const BLACKLIST_MODE_DISABLED = 'disabled';
	const BLACKLIST_MODE_ENABLED = 'enabled';
	
	/**
	 * Tests the WAF configuration and returns true if successful.
	 * 
	 * @return bool
	 */
	public function testConfig() {
		try {
			wfWAF::getInstance()->getStorageEngine()->isDisabled();
		}
		catch (Exception $e) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns a normalized percentage (i.e., in the range [0, 1]) to the corresponding display percentage
	 * based on license type.
	 * 
	 * @param float $percentage
	 * @param bool $adjust Whether or not to adjust the range to [0, 0.7]
	 * @return float
	 */
	protected function _normalizedPercentageToDisplay($percentage, $adjust = true) {
		if (wfConfig::get('isPaid') || !$adjust) {
			return round($percentage, 2);
		}
		
		return round($percentage * 0.70, 2);
	}
	
	/**
	 * Returns the percentage calculation of the overall firewall status, which is displayed under "Firewall" 
	 * on the Dashboard page.
	 *
	 * @return float
	 */
	public function overallStatus() {
		try {
			$wafStatus = $this->wafStatus();
			$bruteForceStatus = $this->bruteForceStatus();
			
			$percentage = 0.0;
			$percentage += $wafStatus * 0.80;
			$percentage += $bruteForceStatus * 0.20;
			return $this->_normalizedPercentageToDisplay($percentage, false);
		}
		catch (Exception $e) {
			//Ignore, return 0%
		}
		
		return 0.0;
	}
	
	public function statusList($section = null) {
		$statusList = array();
		$wafStatusList = $this->wafStatusList($section);
		$bruteForceStatusList = $this->bruteForceStatusList();
		
		foreach ($wafStatusList as $entry) {
			$entry['percentage'] *= 0.8;
			$statusList[] = $entry;
		}
		
		foreach ($bruteForceStatusList as $entry) {
			$entry['percentage'] *= 0.2;
			$statusList[] = $entry;
		}
			
		return array_filter($statusList);
	}
	
	/**
	 * Returns the percentage calculation of the WAF status, which is displayed under "Web Application
	 * Firewall" on the Firewall page.
	 * 
	 * @return float
	 */
	public function wafStatus() {
		try {
			$ruleStatus = $this->ruleStatus(true);
			$blacklistStatus = $this->blacklistStatus();
			$wafEnabled = !(!WFWAF_ENABLED || wfWAF::getInstance()->getStorageEngine()->isDisabled());
			$extendedProtection = $wafEnabled && WFWAF_AUTO_PREPEND && !WFWAF_SUBDIRECTORY_INSTALL;
			$rateLimitingAdvancedBlockingEnabled = wfConfig::get('firewallEnabled', 1);

			if (!$wafEnabled) {
				return 0.0;
			}
			
			$percentage = 0.0;
			$percentage += $this->_normalizedPercentageToDisplay($ruleStatus * 0.35, true);
			$percentage += $blacklistStatus * 0.35;
			$percentage += ($extendedProtection ? 0.20 : 0.0);
			$percentage += ($rateLimitingAdvancedBlockingEnabled ? 0.10 : 0.0);
			return $this->_normalizedPercentageToDisplay($percentage, false);
		}
		catch (Exception $e) {
			//Ignore, return 0%
		}
		
		return 0.0;
	}

	public function wafStatusList($section = null) {
		$statusList = array();
		try {
			$wafEnabled = !(!WFWAF_ENABLED || wfWAF::getInstance()->getStorageEngine()->isDisabled());
			if (!$wafEnabled) {
				return array(
					array(
						'percentage' => 1.0,
						'title'      => __('Enable firewall.', 'wordfence'),
					),
				);
			}

			// Get percent of rules enabled.
			$ruleStatus = $this->ruleStatusDescription(true);
			$premiumStatus = array();
			if (!wfConfig::get('isPaid')) {
				$premiumStatus = array(
					'percentage' => 0.30,
					'title'      => __('Enable Premium Rules.', 'wordfence'),
				);
			}

			if ($section === 'rules') {
				if ($ruleStatus) {
					$ruleStatus['percentage'] = $this->_normalizedPercentageToDisplay($ruleStatus['percentage']);
				}
				return array_filter(array($ruleStatus, $premiumStatus));
			}
			if ($premiumStatus) {
				$premiumStatus['percentage'] *= 0.35;
				$premiumStatus['percentage'] = $this->_normalizedPercentageToDisplay($premiumStatus['percentage'], false);
			}
			if ($ruleStatus) {
				$ruleStatus['percentage'] *= 0.35;
				$ruleStatus['percentage'] = $this->_normalizedPercentageToDisplay($ruleStatus['percentage']);
			}
			$statusList = array_merge($statusList, array($ruleStatus), array($premiumStatus));

			$blacklistStatus = $this->blacklistStatusDescription();
			if ($section === 'blacklist') {
				return array_filter(array($blacklistStatus));
			}
			if ($blacklistStatus) {
				$blacklistStatus['percentage'] *= 0.35;
				$blacklistStatus['percentage'] = $this->_normalizedPercentageToDisplay($blacklistStatus['percentage'], false);
			}
			$statusList = array_merge($statusList, array($blacklistStatus));

			$extendedProtection = $wafEnabled && WFWAF_AUTO_PREPEND && !WFWAF_SUBDIRECTORY_INSTALL;
			if (!$extendedProtection) {
				$statusList[] = array(
					'percentage' => $this->_normalizedPercentageToDisplay(0.20, false),
					'title' => __('Optimize the Wordfence Firewall.', 'wordfence'),
				);
			}

			$rateLimitingAdvancedBlockingEnabled = wfConfig::get('firewallEnabled', 1);
			if (!$rateLimitingAdvancedBlockingEnabled) {
				$statusList[] = array(
					'percentage' => $this->_normalizedPercentageToDisplay(0.10, false),
					'title' => __('Enable Rate Limiting and Advanced Blocking.', 'wordfence'),
				);
			}

			return array_filter($statusList);
		}
		catch (Exception $e) {
			//Ignore, return 0%
		}

		return array();
	}
	
	/**
	 * Returns the status of the WAF.
	 * 
	 * @return string
	 */
	public function firewallMode() {
		try {
			return (!WFWAF_ENABLED ? 'disabled' : wfWAF::getInstance()->getStorageEngine()->getConfig('wafStatus'));
		}
		catch (Exception $e) {
			//Ignore
		}
		
		return self::FIREWALL_MODE_DISABLED;
	}
	
	/**
	 * Returns the current protection mode configured for the WAF.
	 * 
	 * @return string
	 */
	public function protectionMode() {
		if (defined('WFWAF_AUTO_PREPEND') && WFWAF_AUTO_PREPEND) {
			return self::PROTECTION_MODE_EXTENDED;
		}
		return self::PROTECTION_MODE_BASIC;
	}
	
	/**
	 * Returns whether or not this installation is in a subdirectory of another WordPress site with the WAF already optimized.
	 * 
	 * @return bool
	 */
	public function isSubDirectoryInstallation() {
		if (defined('WFWAF_SUBDIRECTORY_INSTALL') && WFWAF_SUBDIRECTORY_INSTALL) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the percentage calculation of the firewall rule status, which is displayed under "Firewall Rules" on the
	 * Firewall page.
	 * 
	 * The calculation is the number of rules enabled divided by the total number of rules. If the WAF is in learning 
	 * mode, no rules are enforced, so it's clamped to 0%.
	 * 
	 * @param bool $round Round the percentage (in the range [0, 1]) to be only whole percentages.
	 * @return float
	 */
	public function ruleStatus($round = false) {
		try {
			$wafEnabled = !(!WFWAF_ENABLED || wfWAF::getInstance()->getStorageEngine()->isDisabled());
			if (!$wafEnabled) {
				return 0.0;
			}
			
			/*$learningMode = !!wfWAF::getInstance()->isInLearningMode();
			if ($learningMode) {
				return 0.0;
			}*/
			
			$rules = wfWAF::getInstance()->getRules();
			$disabledRules = (array) wfWAF::getInstance()->getStorageEngine()->getConfig('disabledRules');
			/** @var wfWAFRule $rule */
			$enabledCount = 0;
			foreach ($rules as $ruleID => $rule) {
				if (isset($disabledRules[$ruleID]) && $disabledRules[$ruleID]) {
					continue;
				}
				
				$enabledCount++;
			}
			
			$percentEnabled = (count($rules) == 0 ? 0 : $enabledCount / count($rules));
			if ($round) {
				return round($percentEnabled, 2);
			}
			
			return $this->_normalizedPercentageToDisplay($percentEnabled);
		}
		catch (Exception $e) {
			//Ignore, return 0%
		}
		
		return 0.0;
	}

	/**
	 * @param bool $round
	 * @return array
	 */
	public function ruleStatusDescription($round = false) {
		try {
			$wafEnabled = !(!WFWAF_ENABLED || wfWAF::getInstance()->getStorageEngine()->isDisabled());
			if (!$wafEnabled) {
				return array(
					'percentage' => 1.0,
					'title'      => __('Enable firewall.', 'wordfence'),
				);
			}
			
			/*$learningMode = !!wfWAF::getInstance()->isInLearningMode();
			if ($learningMode) {
				return 0.0;
			}*/

			$rules = wfWAF::getInstance()->getRules();
			$disabledRules = (array) wfWAF::getInstance()->getStorageEngine()->getConfig('disabledRules');
			/** @var wfWAFRule $rule */
			$enabledCount = 0;
			foreach ($rules as $ruleID => $rule) {
				if (isset($disabledRules[$ruleID]) && $disabledRules[$ruleID]) {
					continue;
				}

				$enabledCount++;
			}
			
			$percentEnabled = 1.0 - ((float) (count($rules) == 0 ? 0 : $enabledCount / count($rules)));
			if ($percentEnabled === 0.0) {
				return array();
			}
			$reenbleCount = count($rules) - $enabledCount;
			return array(
				'percentage' => ($round ? round($percentEnabled, 2) : $percentEnabled),
				'title'      => sprintf(_nx('Re-enable %d firewall rule.', 'Re-enable %d firewall rules.', $reenbleCount, 'wordfence'), number_format_i18n($reenbleCount)),
			);
		}
		catch (Exception $e) {
			//Ignore, return 0%
		}

		return array(
			'percentage' => 1.0,
			'title'      => __('Enable firewall.', 'wordfence'),
		);
	}
	
	/**
	 * Returns the rule feed that is in use.
	 * 
	 * @return string
	 */
	public function ruleMode() {
		if (wfConfig::get('isPaid')) {
			return self::RULE_MODE_PREMIUM;
		}
		return self::RULE_MODE_COMMUNITY;
	}
	
	/**
	 * Returns 100% if the blacklist is enabled, 0% if not.
	 * 
	 * @return float
	 */
	public function blacklistStatus() {
		try {
			$wafEnabled = !(!WFWAF_ENABLED || wfWAF::getInstance()->getStorageEngine()->isDisabled());
			if (!$wafEnabled) {
				return 0.0;
			}
			
			return $this->blacklistMode() == self::BLACKLIST_MODE_ENABLED ? 1.0 : 0.0;
		}
		catch (Exception $e) {
			//Ignore, return 0%
		}
		
		return 0.0;
	}
	
	/**
	 * Returns 100% if the blacklist is enabled, 0% if not.
	 *
	 * @return array
	 */
	public function blacklistStatusDescription() {
		try {
			$wafEnabled = !(!WFWAF_ENABLED || wfWAF::getInstance()->getStorageEngine()->isDisabled());
			if (!$wafEnabled) {
				return array(
					'percentage' => 1.0,
					'title'      => __('Enable Firewall.', 'wordfence'),
				);
			}
			
			if ($this->blacklistMode() == self::BLACKLIST_MODE_ENABLED) {
				return array();
			}
			return array(
				'percentage' => 1.0,
				'title'      => __('Enable Real-Time IP Blacklist.', 'wordfence'),
			);
		}
		catch (Exception $e) {
			//Ignore, return 0%
		}
			
		return array(
			'percentage' => 1.0,
			'title'      => __('Enable Real-Time IP Blacklist.', 'wordfence'),
		);
	}

	/**
	 * Returns the blacklist mode.
	 *
	 * @return string
	 */
	public function blacklistMode() {
		$blacklistEnabled = false;
		try {
			$wafEnabled = !(!WFWAF_ENABLED || wfWAF::getInstance()->getStorageEngine()->isDisabled());
			$blacklistEnabled = $wafEnabled && !wfWAF::getInstance()->getStorageEngine()->getConfig('disableWAFBlacklistBlocking');
		}
		catch (Exception $e) {
			//Do nothing
		}
		
		if (wfConfig::get('isPaid') && $blacklistEnabled) {
			return self::BLACKLIST_MODE_ENABLED;
		}
		return self::BLACKLIST_MODE_DISABLED;
	}
	
	/**
	 * Returns a percentage rating for the brute force protection status. This includes both the WFSN enabled status
	 * and the status of individual login security options. These options are available to all, so they are always
	 * in the range [0,1].
	 * 
	 * @return float
	 */
	public function bruteForceStatus() {
		$networkBruteForceEnabled = !!wfConfig::get('other_WFNet');
		$localBruteForceEnabled = !!wfConfig::get('loginSecurityEnabled');
		
		$percentage = 0.0;

		if ($localBruteForceEnabled) {
			$percentage += 0.1;

			if ($networkBruteForceEnabled) {
				$percentage += 0.5;
			}
			if (wfConfig::get('loginSec_strongPasswds_enabled') && (wfConfig::get('loginSec_strongPasswds') == 'pubs' || wfConfig::get('loginSec_strongPasswds') == 'all')) {
				$percentage += 0.1;
			}
			if (wfConfig::get('loginSec_maskLoginErrors')) {
				$percentage += 0.1;
			}
			if (wfConfig::get('loginSec_blockAdminReg')) {
				$percentage += 0.1;
			}
			if (wfConfig::get('loginSec_disableAuthorScan')) {
				$percentage += 0.1;
			}
		}
		
		return round($percentage, 2);
	}
	
	/**
	 * Returns the status of the WAF's learning mode.
	 * 
	 * @return bool|int Returns true if enabled without an automatic switchover, a timestamp if enabled with one, and false if not in learning mode.
	 */
	public function learningModeStatus() {
		if ($this->firewallMode() != self::FIREWALL_MODE_LEARNING) {
			return false;
		}
		
		try {
			$config = wfWAF::getInstance()->getStorageEngine();
			if ($config->getConfig('learningModeGracePeriodEnabled')) {
				return (int) $config->getConfig('learningModeGracePeriod');
			}
			
			return true;
		}
		catch (Exception $e) {
			//Ignore, return false
		}
		
		return false;
	}

	/**
	 * @return array
	 */
	public function bruteForceStatusList() {
		$networkBruteForceEnabled = !!wfConfig::get('other_WFNet');
		$localBruteForceEnabled = !!wfConfig::get('loginSecurityEnabled');

		$status = array();

		if ($localBruteForceEnabled) {
			if (!$networkBruteForceEnabled) {
				$status[] = array(
					'percentage' => 0.5,
					'title' => __('Enable Real-Time WordPress Security Network.', 'wordfence'),
				);
			}
			if (!wfConfig::get('loginSec_strongPasswds_enabled')) {
				$status[] = array(
					'percentage' => 0.1,
					'title' => __('Enforce Strong Passwords.', 'wordfence'),
				);
			}
			if (!wfConfig::get('loginSec_maskLoginErrors')) {
				$status[] = array(
					'percentage' => 0.1,
					'title' => __('Enable Mask Login Errors.', 'wordfence'),
				);
			}
			if (!wfConfig::get('loginSec_blockAdminReg')) {
				$status[] = array(
					'percentage' => 0.1,
					'title' => __('Enable Block Admin Registration.', 'wordfence'),
				);
			}
			if (!wfConfig::get('loginSec_disableAuthorScan')) {
				$status[] = array(
					'percentage' => 0.1,
					'title' => __('Disable Author Scanning.', 'wordfence'),
				);
			}
		} else {
			$status[] = array(
				'percentage' => 1.0,
				'title' => __('Enable Brute Force Protection.', 'wordfence'),
			);
		}

		return array_filter($status);
	}
}
