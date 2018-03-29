<?php

class wfDashboard {
	const SCAN_SUCCESS = 1;
	const SCAN_FAILED = 0;
	const SCAN_NEVER_RAN = -1;
	const SCAN_WARNINGS = 2;
	
	const FEATURE_ENABLED = 1;
	const FEATURE_DISABLED = 0;
	const FEATURE_PREMIUM = -1;
	
	public $scanLastCompletion;
	public $scanLastStatusMessage;
	public $scanLastStatus;
	
	public $notifications = array();
	
	public $features = array();
	
	public $lastGenerated;
	
	public $tdfCommunity;
	public $tdfPremium;
	
	public $ips24h;
	public $ips7d;
	public $ips30d;
	
	public $loginsSuccess;
	public $loginsFail;
	
	public $localBlocks;
	
	public $networkBlock24h;
	public $networkBlock7d;
	public $networkBlock30d;
	
	public $countriesLocal;
	public $countriesNetwork;
	
	public static function processDashboardResponse($data) {
		if (isset($data['notifications'])) {
			foreach ($data['notifications'] as $n) {
				if (!isset($n['id']) || !isset($n['priority']) || !isset($n['html'])) {
					continue;
				}
				
				new wfNotification($n['id'], $n['priority'], $n['html'], (isset($n['category']) ? $n['category'] : null));
			}
			
			unset($data['notifications']);
		}
		
		if (isset($data['revoked'])) {
			foreach ($data['revoked'] as $r) {
				if (!isset($r['id'])) {
					continue;
				}
				
				$notification = wfNotification::getNotificationForID($r['id']);
				if ($notification !== null) {
					$notification->markAsRead();
				}
			}
			
			unset($data['revoked']);
		}
		wfConfig::set_ser('dashboardData', $data);
	}
	
	public function __construct() {
		// Scan values
		$lastScanCompleted = wfConfig::get('lastScanCompleted');
		if ($lastScanCompleted === false || empty($lastScanCompleted)) {
			$this->scanLastStatus = self::SCAN_NEVER_RAN;
		}
		else if ($lastScanCompleted == 'ok') {
			$this->scanLastStatus = self::SCAN_SUCCESS;
			
			$i = new wfIssues();
			$this->scanLastCompletion = (int) wfScanner::shared()->lastScanTime();
			$issueCount = $i->getIssueCount();
			if ($issueCount) {
				$this->scanLastStatus = self::SCAN_WARNINGS;
				$this->scanLastStatusMessage = "{$issueCount} issue" . ($issueCount == 1 ? ' found' : 's found');
			}
		} 
		else {
			$this->scanLastStatus = self::SCAN_FAILED;
			$n = wfNotification::getNotificationForCategory('wfplugin_scan', false);
			if ($n !== null) {
				$this->scanLastStatusMessage = $n->html;
			}
			else {
				$this->scanLastStatusMessage = esc_html(substr($lastScanCompleted, 0, 100) . (strlen($lastScanCompleted) > 100 ? '...' : ''));
			}
		}
		
		// Notifications
		$this->notifications = wfNotification::notifications();
		
		// Features
		$countryBlocking = self::FEATURE_PREMIUM;
		if (wfConfig::get('isPaid')) {
			$countryBlocking = self::FEATURE_DISABLED;
			$countryList = wfConfig::get('cbl_countries');
			if (!empty($countryList) && (wfConfig::get('cbl_loggedInBlocked', false) || wfConfig::get('cbl_loginFormBlocked', false) || wfConfig::get('cbl_restOfSiteBlocked', false))) {
				$countryBlocking = self::FEATURE_ENABLED;
			}
		}
		
		$this->features = array(); //Deprecated
		
		$data = wfConfig::get_ser('dashboardData');
		$lastChecked = wfConfig::get('lastDashboardCheck', 0);
		if ((!is_array($data) || (isset($data['generated']) && $data['generated'] + 3600 < time())) && $lastChecked + 3600 < time()) {
			$wp_version = wfUtils::getWPVersion();
			$apiKey = wfConfig::get('apiKey');
			$api = new wfAPI($apiKey, $wp_version);
			wfConfig::set('lastDashboardCheck', time());
			try {
				$json = $api->getStaticURL('/stats.json');
				$data = @json_decode($json, true);
				if ($json && is_array($data)) {
					self::processDashboardResponse($data);
				}
			}
			catch (Exception $e) {
				//Do nothing
			}
		}
		
		// Last Generated
		if (is_array($data) && isset($data['generated'])) {
			$this->lastGenerated = $data['generated'];
		}
		
		// TDF
		if (is_array($data) && isset($data['tdf']) && isset($data['tdf']['community'])) {
			$this->tdfCommunity = (int) $data['tdf']['community'];
			$this->tdfPremium = (int) $data['tdf']['premium'];
		}
		
		// Top IPs Blocked
		$activityReport = new wfActivityReport();
		$this->ips24h = (array) $activityReport->getTopIPsBlocked(100, 1);
		foreach ($this->ips24h as &$r24h) {
			$r24h = (array) $r24h;
			if (empty($r24h['countryName'])) { $r24h['countryName'] = 'Unknown'; }
		}
		$this->ips7d = (array) $activityReport->getTopIPsBlocked(100, 7);
		foreach ($this->ips7d as &$r7d) {
			$r7d = (array) $r7d;
			if (empty($r7d['countryName'])) { $r7d['countryName'] = 'Unknown'; }
		}
		$this->ips30d = (array) $activityReport->getTopIPsBlocked(100, 30);
		foreach ($this->ips30d as &$r30d) {
			$r30d = (array) $r30d;
			if (empty($r30d['countryName'])) { $r30d['countryName'] = 'Unknown'; }
		}
		
		// Recent Logins
		$logins = wordfence::getLog()->getHits('logins', 'loginLogout', 0, 200);
		$this->loginsSuccess = array();
		$this->loginsFail = array();
		foreach ($logins as $l) {
			if ($l['fail']) {
				$this->loginsFail[] = array('t' => $l['ctime'], 'name' => $l['username'], 'ip' => $l['IP']);
			}
			else if ($l['action'] != 'logout') {
				$this->loginsSuccess[] = array('t' => $l['ctime'], 'name' => $l['username'], 'ip' => $l['IP']);
			}
		}
		
		// Local Attack Data
		$this->localBlocks = array();
		$this->localBlocks[] = array('title' => __('Complex', 'wordfence'), 'type' => wfActivityReport::BLOCK_TYPE_COMPLEX,
			'24h' => (int) $activityReport->getBlockedCount(1, wfActivityReport::BLOCK_TYPE_COMPLEX),
			'7d' => (int) $activityReport->getBlockedCount(7, wfActivityReport::BLOCK_TYPE_COMPLEX),
			'30d' => (int) $activityReport->getBlockedCount(30, wfActivityReport::BLOCK_TYPE_COMPLEX),
		);
		$this->localBlocks[] = array('title' => __('Brute Force', 'wordfence'), 'type' => wfActivityReport::BLOCK_TYPE_BRUTE_FORCE,
			'24h' => (int) $activityReport->getBlockedCount(1, wfActivityReport::BLOCK_TYPE_BRUTE_FORCE),
			'7d' => (int) $activityReport->getBlockedCount(7, wfActivityReport::BLOCK_TYPE_BRUTE_FORCE),
			'30d' => (int) $activityReport->getBlockedCount(30, wfActivityReport::BLOCK_TYPE_BRUTE_FORCE),
		);
		$this->localBlocks[] = array('title' => __('Blacklist', 'wordfence'), 'type' => wfActivityReport::BLOCK_TYPE_BLACKLIST,
			'24h' => (int) $activityReport->getBlockedCount(1, wfActivityReport::BLOCK_TYPE_BLACKLIST),
			'7d' => (int) $activityReport->getBlockedCount(7, wfActivityReport::BLOCK_TYPE_BLACKLIST),
			'30d' => (int) $activityReport->getBlockedCount(30, wfActivityReport::BLOCK_TYPE_BLACKLIST),
		);
		
		// Network Attack Data
		if (is_array($data) && isset($data['attackdata']) && isset($data['attackdata']['24h'])) {
			$this->networkBlock24h = $data['attackdata']['24h'];
			$this->networkBlock7d = $data['attackdata']['7d'];
			$this->networkBlock30d = $data['attackdata']['30d'];
		}
		
		// Blocked Countries
		$this->countriesLocal = (array) $activityReport->getTopCountriesBlocked(10, 7);
		foreach ($this->countriesLocal as &$rLocal) {
			$rLocal = (array) $rLocal;
			if (empty($rLocal['countryName'])) { $rLocal['countryName'] = 'Unknown'; }
		}
		
		if (is_array($data) && isset($data['countries']) && isset($data['countries']['7d'])) {
			$networkCountries = array();
			foreach ($data['countries']['7d'] as $rNetwork) {
				$countryCode = $rNetwork['cd'];
				$countryName = $activityReport->getCountryNameByCode($countryCode);
				if (empty($countryName)) { $countryName = 'Unknown'; }
				$totalBlockCount = $rNetwork['ct'];
				$networkCountries[] = array('countryCode' => $countryCode, 'countryName' => $countryName, 'totalBlockCount' => $totalBlockCount);
			}
			$this->countriesNetwork = $networkCountries;
		}
	}
}
