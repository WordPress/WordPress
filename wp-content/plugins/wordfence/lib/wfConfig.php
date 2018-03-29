<?php
class wfConfig {
	const AUTOLOAD = 'yes';
	const DONT_AUTOLOAD = 'no';
	
	const TYPE_BOOL = 'boolean';
	const TYPE_INT = 'integer';
	const TYPE_FLOAT = 'double';
	const TYPE_DOUBLE = 'double';
	const TYPE_STRING = 'string';
	const TYPE_ARRAY = 'array';
	
	const OPTIONS_TYPE_GLOBAL = 'global';
	const OPTIONS_TYPE_FIREWALL = 'firewall';
	const OPTIONS_TYPE_BLOCKING = 'blocking';
	const OPTIONS_TYPE_SCANNER = 'scanner';
	const OPTIONS_TYPE_TWO_FACTOR = 'twofactor';
	const OPTIONS_TYPE_LIVE_TRAFFIC = 'livetraffic';
	const OPTIONS_TYPE_COMMENT_SPAM = 'commentspam';
	const OPTIONS_TYPE_DIAGNOSTICS = 'diagnostics';
	const OPTIONS_TYPE_ALL = 'all';
	
	public static $diskCache = array();
	private static $diskCacheDisabled = false; //enables if we detect a write fail so we don't keep calling stat()
	private static $cacheDisableCheckDone = false;
	private static $table = false;
	private static $tableExists = true;
	private static $cache = array();
	private static $DB = false;
	private static $tmpFileHeader = "<?php\n/* Wordfence temporary file security header */\necho \"Nothing to see here!\\n\"; exit(0);\n?>";
	private static $tmpDirCache = false;
	public static $defaultConfig = array(
		//All exportable boolean options
		"checkboxes" => array(
			"alertOn_critical" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"alertOn_update" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"alertOn_warnings" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"alertOn_throttle" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"alertOn_block" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"alertOn_loginLockout" => array('value' => true, 'autoload' => self::AUTOLOAD),
			'alertOn_breachLogin' => array('value' => true, 'autoload' => self::AUTOLOAD),
			"alertOn_lostPasswdForm" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"alertOn_adminLogin" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"alertOn_firstAdminLoginOnly" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"alertOn_nonAdminLogin" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"alertOn_firstNonAdminLoginOnly" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"alertOn_wordfenceDeactivated" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"liveTrafficEnabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"advancedCommentScanning" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"checkSpamIP" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"spamvertizeCheck" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"liveTraf_ignorePublishers" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"liveTraf_displayExpandedRecords" => array('value' => false, 'autoload' => self::DONT_AUTOLOAD),
			//"perfLoggingEnabled" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"scheduledScansEnabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"lowResourceScansEnabled" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"scansEnabled_checkGSB" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_checkHowGetIPs" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_core" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_themes" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"scansEnabled_plugins" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"scansEnabled_coreUnknown" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_malware" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_fileContents" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_fileContentsGSB" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_checkReadableConfig" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_suspectedFiles" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_posts" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_comments" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_suspiciousOptions" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_passwds" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_diskSpace" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_options" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_wpscan_fullPathDisclosure" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_wpscan_directoryListingEnabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_dns" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_scanImages" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"scansEnabled_highSense" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"scansEnabled_oldVersions" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"scansEnabled_suspiciousAdminUsers" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"liveActivityPauseEnabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"firewallEnabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"blockFakeBots" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"autoBlockScanners" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"loginSecurityEnabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"loginSec_strongPasswds_enabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"loginSec_breachPasswds_enabled" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"loginSec_lockInvalidUsers" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"loginSec_maskLoginErrors" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"loginSec_blockAdminReg" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"loginSec_disableAuthorScan" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"loginSec_disableOEmbedAuthor" => array('value' => false, 'autoload' => self::AUTOLOAD),
			'loginSec_requireAdminTwoFactor' => array('value' => false, 'autoload' => self::AUTOLOAD),
			"notification_updatesNeeded" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"notification_securityAlerts" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"notification_promotions" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"notification_blogHighlights" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"notification_productUpdates" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"notification_scanStatus" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"other_hideWPVersion" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"other_noAnonMemberComments" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"other_blockBadPOST" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"other_scanComments" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"other_pwStrengthOnUpdate" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"other_WFNet" => array('value' => true, 'autoload' => self::AUTOLOAD),
			"other_scanOutside" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"other_bypassLitespeedNoabort" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"deleteTablesOnDeact" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"autoUpdate" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"disableCookies" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"startScansRemotely" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"disableConfigCaching" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"addCacheComment" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"disableCodeExecutionUploads" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"allowHTTPSCaching" => array('value' => false, 'autoload' => self::AUTOLOAD),
			"debugOn" => array('value' => false, 'autoload' => self::AUTOLOAD),
			'email_summary_enabled' => array('value' => true, 'autoload' => self::AUTOLOAD),
			'email_summary_dashboard_widget_enabled' => array('value' => true, 'autoload' => self::AUTOLOAD),
			'ssl_verify' => array('value' => true, 'autoload' => self::AUTOLOAD),
			'ajaxWatcherDisabled_front' => array('value' => false, 'autoload' => self::AUTOLOAD),
			'ajaxWatcherDisabled_admin' => array('value' => false, 'autoload' => self::AUTOLOAD),
			'wafAlertOnAttacks' => array('value' => true, 'autoload' => self::AUTOLOAD),
			'disableWAFIPBlocking' => array('value' => false, 'autoload' => self::AUTOLOAD),
			'showAdminBarMenu' => array('value' => true, 'autoload' => self::AUTOLOAD),
			'displayTopLevelOptions' => array('value' => true, 'autoload' => self::AUTOLOAD),
			'displayTopLevelBlocking' => array('value' => false, 'autoload' => self::AUTOLOAD),
			'displayTopLevelLiveTraffic' => array('value' => false, 'autoload' => self::AUTOLOAD),
			'displayAutomaticBlocks' => array('value' => true, 'autoload' => self::AUTOLOAD),
		),
		//All exportable variable type options
		"otherParams" => array(
			"scan_include_extra" => array('value' => "", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			"alertEmails" => array('value' => "", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			"liveTraf_ignoreUsers" => array('value' => "", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			"liveTraf_ignoreIPs" => array('value' => "", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			"liveTraf_ignoreUA" => array('value' => "", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),   
			"maxMem" => array('value' => 256, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)), 
			'scan_exclude' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			'scan_maxIssues' => array('value' => 1000, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)), 
			'scan_maxDuration' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			'whitelisted' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			'bannedURLs' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			'maxExecutionTime' => array('value' => 0, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)), 
			'howGetIPs' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)), 
			'actUpdateInterval' => array('value' => 2, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)), 
			'alert_maxHourly' => array('value' => 0, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)), 
			'loginSec_userBlacklist' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'liveTraf_maxRows' => array('value' => 2000, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			"neverBlockBG" => array('value' => "neverBlockVerified", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			"loginSec_countFailMins" => array('value' => 240, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			"loginSec_lockoutMins" => array('value' => 240, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'loginSec_strongPasswds' => array('value' => 'pubs', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'loginSec_breachPasswds' => array('value' => 'admins', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'loginSec_maxFailures' => array('value' => 20, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'loginSec_maxForgotPasswd' => array('value' => 20, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'maxGlobalRequests' => array('value' => 'DISABLED', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'maxGlobalRequests_action' => array('value' => "throttle", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'maxRequestsCrawlers' => array('value' => 'DISABLED', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'maxRequestsCrawlers_action' => array('value' => "throttle", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'maxRequestsHumans' => array('value' => 'DISABLED', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'maxRequestsHumans_action' => array('value' => "throttle", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'max404Crawlers' => array('value' => 'DISABLED', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'max404Crawlers_action' => array('value' => "throttle", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'max404Humans' => array('value' => 'DISABLED', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'max404Humans_action' => array('value' => "throttle", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'maxScanHits' => array('value' => 'DISABLED', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'maxScanHits_action' => array('value' => "throttle", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'blockedTime' => array('value' => 300, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'email_summary_interval' => array('value' => 'weekly', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'email_summary_excluded_directories' => array('value' => 'wp-content/cache,wp-content/wflogs', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'allowed404s' => array('value' => "/favicon.ico\n/apple-touch-icon*.png\n/*@2x.png\n/browserconfig.xml", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'wafAlertWhitelist' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'wafAlertInterval' => array('value' => 600, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'wafAlertThreshold' => array('value' => 100, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'howGetIPs_trusted_proxies' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'scanType' => array('value' => wfScanner::SCAN_TYPE_STANDARD, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'manualScanType' => array('value' => wfScanner::MANUAL_SCHEDULING_ONCE_DAILY, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'schedStartHour' => array('value' => -1, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'schedMode' => array('value' => wfScanner::SCAN_SCHEDULING_MODE_AUTOMATIC, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'cbl_loggedInBlocked' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'cbl_action' => array('value' => 'block', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'cbl_redirURL' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'cbl_bypassRedirURL' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'cbl_bypassRedirDest' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'cbl_bypassViewURL' => array('value' => '', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'loginSec_enableSeparateTwoFactor' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
		),
		//Set as default only, not included automatically in the settings import/export or options page saving
		'defaultsOnly' => array(
			"apiKey" => array('value' => "", 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'keyType' => array('value' => wfAPI::KEY_TYPE_FREE, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'isPaid' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'hasKeyConflict' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'betaThreatDefenseFeed' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'timeoffset_wf_updated' => array('value' => 0, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_INT)),
			'cacheType' => array('value' => 'disabled', 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'detectProxyRecommendation' => array('value' => '', 'autoload' => self::DONT_AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'dismissAutoPrependNotice' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'onboardingAttempt1' => array('value' => '', 'autoload' => self::DONT_AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'onboardingAttempt2' => array('value' => '', 'autoload' => self::DONT_AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'onboardingAttempt3' => array('value' => '', 'autoload' => self::DONT_AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'onboardingAttempt3Initial' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsNewTour_dashboard' => array('value' => true, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsNewTour_firewall' => array('value' => true, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsNewTour_scan' => array('value' => true, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsNewTour_blocking' => array('value' => true, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsNewTour_livetraffic' => array('value' => true, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsUpgradeTour_dashboard' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsUpgradeTour_firewall' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsUpgradeTour_scan' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsUpgradeTour_blocking' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'needsUpgradeTour_livetraffic' => array('value' => false, 'autoload' => self::AUTOLOAD, 'validation' => array('type' => self::TYPE_BOOL)),
			'supportContent' => array('value' => '{}', 'autoload' => self::DONT_AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
			'supportHash' => array('value' => '', 'autoload' => self::DONT_AUTOLOAD, 'validation' => array('type' => self::TYPE_STRING)),
		),
	);
	public static $serializedOptions = array('lastAdminLogin', 'scanSched', 'emailedIssuesList', 'wf_summaryItems', 'adminUserList', 'twoFactorUsers', 'alertFreqTrack', 'wfStatusStartMsgs', 'vulnerabilities_plugin', 'vulnerabilities_theme', 'dashboardData', 'malwarePrefixes', 'noc1ScanSchedule', 'allScansScheduled', 'disclosureStates', 'scanStageStatuses', 'adminNoticeQueue');
	public static function setDefaults() {
		foreach (self::$defaultConfig['checkboxes'] as $key => $config) {
			$val = $config['value'];
			$autoload = $config['autoload'];
			if (self::get($key) === false) {
				self::set($key, $val ? '1' : '0', $autoload);
			}
		}
		foreach (self::$defaultConfig['otherParams'] as $key => $config) {
			$val = $config['value'];
			$autoload = $config['autoload'];
			if (self::get($key) === false) {
				self::set($key, $val, $autoload);
			}
		}
		foreach (self::$defaultConfig['defaultsOnly'] as $key => $config) {
			$val = $config['value'];
			$autoload = $config['autoload'];
			if (self::get($key) === false) {
				if ($val === false) {
					self::set($key, '0', $autoload);
				}
				else if ($val === true) {
					self::set($key, '1', $autoload);
				}
				else {
					self::set($key, $val, $autoload);
				}
			}
		}
		self::set('encKey', substr(wfUtils::bigRandomHex(), 0, 16));
		if (self::get('maxMem', false) === false) {
			self::set('maxMem', '256');
		}
		if (self::get('other_scanOutside', false) === false) {
			self::set('other_scanOutside', 0);
		}

		if (self::get('email_summary_enabled')) {
			wfActivityReport::scheduleCronJob();
		} else {
			wfActivityReport::disableCronJob();
		}
	}
	public static function loadAllOptions() {
		global $wpdb;
		
		$options = wp_cache_get('alloptions', 'wordfence');
		if (!$options) {
			$table = self::table();
			self::updateTableExists();
			$suppress = $wpdb->suppress_errors();
			if (!($rawOptions = $wpdb->get_results("SELECT name, val FROM {$table} WHERE autoload = 'yes'"))) {
				$rawOptions = $wpdb->get_results("SELECT name, val FROM {$table}");
			}
			$wpdb->suppress_errors($suppress);
			$options = array();
			foreach ((array) $rawOptions as $o) {
				if (in_array($o->name, self::$serializedOptions)) {
					$val = maybe_unserialize($o->val);
					if ($val) {
						$options[$o->name] = $val;
					}
				}
				else {
					$options[$o->name] = $o->val;
				}
			}
			
			wp_cache_add_non_persistent_groups('wordfence');
			wp_cache_add('alloptions', $options, 'wordfence');
		}
		
		return $options;
	}
	public static function updateTableExists() {
		global $wpdb;
		self::$tableExists = $wpdb->get_col($wpdb->prepare(<<<SQL
SELECT TABLE_NAME FROM information_schema.TABLES
WHERE TABLE_SCHEMA=DATABASE()
AND TABLE_NAME=%s
SQL
			, self::table()));
	}
	private static function updateCachedOption($name, $val) {
		$options = self::loadAllOptions();
		$options[$name] = $val;
		wp_cache_set('alloptions', $options, 'wordfence');
	}
	private static function removeCachedOption($name) {
		$options = self::loadAllOptions();
		if (isset($options[$name])) {
			unset($options[$name]);
			wp_cache_set('alloptions', $options, 'wordfence');
		}
	}
	private static function getCachedOption($name) {
		$options = self::loadAllOptions();
		if (isset($options[$name])) {
			return $options[$name];
		}
		
		$table = self::table();
		$val = self::getDB()->querySingle("SELECT val FROM {$table} WHERE name='%s'", $name);
		if ($val !== null) {
			$options[$name] = $val;
			wp_cache_set('alloptions', $options, 'wordfence');
		}
		return $val;
	}
	public static function hasCachedOption($name) {
		$options = self::loadAllOptions();
		return isset($options[$name]);
	}
	
	/**
	 * Returns an array of all option keys that are eligible for export with the exception of serialized options.
	 * 
	 * @return array
	 */
	public static function getExportableOptionsKeys() {
		$ret = array();
		foreach (self::$defaultConfig['checkboxes'] as $key => $val) {
			$ret[] = $key;
		}
		foreach (self::$defaultConfig['otherParams'] as $key => $val) {
			$ret[] = $key;
		}
		return $ret;
	}
	public static function parseOptions($excludeOmitted = false) {
		$ret = array();
		foreach (self::$defaultConfig['checkboxes'] as $key => $val) { //value is not used. We just need the keys for validation
			if ($excludeOmitted && isset($_POST[$key])) {
				$ret[$key] = (int) $_POST[$key];
			}
			else if (!$excludeOmitted || isset($_POST[$key])) {
				$ret[$key] = isset($_POST[$key]) ? '1' : '0';
			}
		}
		foreach (self::$defaultConfig['otherParams'] as $key => $val) {
			if (!$excludeOmitted || isset($_POST[$key])) {
				if (isset($_POST[$key])) {
					$ret[$key] = stripslashes($_POST[$key]);
				}
				else {
					error_log("Missing options param \"$key\" when parsing parameters.");
				}
			}
		}
		/* for debugging only:
		foreach($_POST as $key => $val){
			if($key != 'action' && $key != 'nonce' && (! array_key_exists($key, self::$checkboxes)) && (! array_key_exists($key, self::$otherParams)) ){
				error_log("Unrecognized option: $key");
			}
		}
		*/
		return $ret;
	}
	public static function setArray($arr){
		foreach($arr as $key => $val){
			self::set($key, $val);
		}
	}
	public static function getHTML($key){
		return esc_html(self::get($key));
	}
	public static function inc($key){
		$val = self::get($key, false);
		if(! $val){
			$val = 0;
		}
		self::set($key, $val + 1);
		return $val + 1;
	}
	public static function atomicInc($key) {
		if (!self::$tableExists) {
			return false;
		}
		
		global $wpdb;
		$old_suppress_errors = $wpdb->suppress_errors(true);
		$table = self::table();
		$rowExists = false;
		do {
			if (!$rowExists && $wpdb->query($wpdb->prepare("INSERT INTO {$table} (name, val, autoload) values (%s, %s, %s)", $key, 1, self::DONT_AUTOLOAD))) {
				$val = 1;
				$successful = true;
			}
			else {
				$rowExists = true;
				$val = self::get($key, 1);
				if ($wpdb->query($wpdb->prepare("UPDATE {$table} SET val = %s WHERE name = %s AND val = %s", $val + 1, $key, $val))) {
					$val++;
					$successful = true;
				}
			}
		} while (!$successful);
		$wpdb->suppress_errors($old_suppress_errors);
		return $val;
	}
	public static function remove($key) {
		global $wpdb;
		
		if (!self::$tableExists) {
			return;
		}
		
		$table = self::table();
		$wpdb->query($wpdb->prepare("DELETE FROM {$table} WHERE name = %s", $key));
		self::removeCachedOption($key);
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController') && (substr($key, 0, 4) == 'cbl_' || $key == 'blockedTime' || $key == 'disableWAFIPBlocking')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	public static function set($key, $val, $autoload = self::AUTOLOAD) {
		global $wpdb;
		
		if (is_array($val)) {
			$msg = "wfConfig::set() got an array as second param with key: $key and value: " . var_export($val, true);
			wordfence::status(1, 'error', $msg);
			return;
		}

		if (($key == 'apiKey' || $key == 'isPaid' || $key == 'other_WFNet') && wfWAF::getInstance() && !WFWAF_SUBDIRECTORY_INSTALL) {
			if ($key == 'isPaid' || $key == 'other_WFNet') {
				$val = !!$val;
			}
			
			try {
				wfWAF::getInstance()->getStorageEngine()->setConfig($key, $val);
			} catch (wfWAFStorageFileException $e) {
				error_log($e->getMessage());
			}
		}
		
		if (!self::$tableExists) {
			return;
		
		}
		$table = self::table();
		if ($wpdb->query($wpdb->prepare("INSERT INTO {$table} (name, val, autoload) values (%s, %s, %s) ON DUPLICATE KEY UPDATE val = %s, autoload = %s", $key, $val, $autoload, $val, $autoload)) !== false && $autoload != self::DONT_AUTOLOAD) {
			self::updateCachedOption($key, $val);
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController') && (substr($key, 0, 4) == 'cbl_' || $key == 'blockedTime' || $key == 'disableWAFIPBlocking')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		} 
	}
	public static function get($key, $default = false, $allowCached = true) {
		global $wpdb;
		
		if ($allowCached && self::hasCachedOption($key)) {
			return self::getCachedOption($key);
		}
		
		if (!self::$tableExists) {
			return $default;
		}
		
		$table = self::table();
		if (!($option = $wpdb->get_row($wpdb->prepare("SELECT name, val, autoload FROM {$table} WHERE name = %s", $key)))) {
			return $default;
		}
		
		if ($option->autoload != self::DONT_AUTOLOAD) {
			self::updateCachedOption($key, $option->val);
		}
		return $option->val;
	}
	
	public static function getInt($key, $default = 0, $allowCached = true) {
		return (int) self::get($key, $default, $allowCached);
	}
	
	private static function canCompressValue() {
		if (!function_exists('gzencode') || !function_exists('gzdecode')) {
			return false;
		}
		$disabled = explode(',', ini_get('disable_functions'));
		if (in_array('gzencode', $disabled) || in_array('gzdecode', $disabled)) {
			return false;
		}
		return true;
	}
	
	private static function isCompressedValue($data) {
		//Based on http://www.ietf.org/rfc/rfc1952.txt
		if (strlen($data) < 2) {
			return false;
		}
		
		$magicBytes = substr($data, 0, 2);
		if ($magicBytes !== (chr(0x1f) . chr(0x8b))) {
			return false;
		}
		
		//Small chance of false positives here -- can check the header CRC if it turns out it's needed
		return true;
	}
	
	private static function ser_chunked_key($key) {
		return 'wordfence_chunked_' . $key . '_';
	}
	
	public static function get_ser($key, $default = false, $cache = true) {
		if (self::hasCachedOption($key)) {
			return self::getCachedOption($key);
		}
		
		if (!self::$tableExists) {
			return $default;
		}
		
		//Check for a chunked value first
		$chunkedValueKey = self::ser_chunked_key($key);
		$header = self::getDB()->querySingle("select val from " . self::table() . " where name=%s", $chunkedValueKey . 'header');
		if ($header) {
			$header = unserialize($header);
			$count = $header['count'];
			$path = tempnam(sys_get_temp_dir(), $key); //Writing to a file like this saves some of PHP's in-memory copying when just appending each chunk to a string
			$fh = fopen($path, 'r+');
			$length = 0;
			for ($i = 0; $i < $count; $i++) {
				$chunk = self::getDB()->querySingle("select val from " . self::table() . " where name=%s", $chunkedValueKey . $i);
				self::getDB()->flush(); //clear cache
				if (!$chunk) {
					wordfence::status(2, 'error', "Error reassembling value for {$key}");
					return $default;
				}
				fwrite($fh, $chunk);
				$length += strlen($chunk);
				unset($chunk);
			}
			
			fseek($fh, 0);
			$serialized = fread($fh, $length);
			fclose($fh);
			unlink($path);
			
			if (self::canCompressValue() && self::isCompressedValue($serialized)) {
				$inflated = @gzdecode($serialized);
				if ($inflated !== false) {
					unset($serialized);
					if ($cache) {
						self::updateCachedOption($key, unserialize($inflated));
						return self::getCachedOption($key);
					}
					return unserialize($inflated);
				}
			}
			if ($cache) {
				self::updateCachedOption($key, unserialize($serialized));
				return self::getCachedOption($key);
			}
			return unserialize($serialized);
		}
		else {
			$serialized = self::getDB()->querySingle("select val from " . self::table() . " where name=%s", $key);
			self::getDB()->flush(); //clear cache
			if ($serialized) {
				if (self::canCompressValue() && self::isCompressedValue($serialized)) {
					$inflated = @gzdecode($serialized);
					if ($inflated !== false) {
						unset($serialized);
						return unserialize($inflated);
					}
				}
				if ($cache) {
					self::updateCachedOption($key, unserialize($serialized));
					return self::getCachedOption($key);
				}
				return unserialize($serialized);
			}
		}
		
		return $default;
	}
	
	public static function set_ser($key, $val, $allowCompression = false, $autoload = self::AUTOLOAD) {
		/*
		 * Because of the small default value for `max_allowed_packet` and `max_long_data_size`, we're stuck splitting
		 * large values into multiple chunks. To minimize memory use, the MySQLi driver is used directly when possible.
		 */
		
		global $wpdb;
		$dbh = $wpdb->dbh;
		$useMySQLi = (is_object($dbh) && $wpdb->use_mysqli);
		
		if (!self::$tableExists) {
			return;
		}
		
		self::delete_ser_chunked($key); //Ensure any old values for a chunked value are deleted first
		
		if (self::canCompressValue() && $allowCompression) {
			$data = gzencode(serialize($val));
		}
		else {
			$data = serialize($val);
		}
		
		if (!$useMySQLi) {
			$data = bin2hex($data);
		}
		
		$dataLength = strlen($data);
		$maxAllowedPacketBytes = self::getDB()->getMaxAllowedPacketBytes();
		$chunkSize = intval((($maxAllowedPacketBytes < 1024 /* MySQL minimum, probably failure to fetch it */ ? 1024 * 1024 /* MySQL default */ : $maxAllowedPacketBytes) - 50) / 1.2); //Based on max_allowed_packet + 20% for escaping and SQL
		$chunkSize = $chunkSize - ($chunkSize % 2); //Ensure it's even
		$chunkedValueKey = self::ser_chunked_key($key);
		if ($dataLength > $chunkSize) {
			$chunks = 0;
			while (($chunks * $chunkSize) < $dataLength) {
				$dataChunk = substr($data, $chunks * $chunkSize, $chunkSize);
				if ($useMySQLi) {
					$chunkKey = $chunkedValueKey . $chunks;
					$stmt = $dbh->prepare("INSERT IGNORE INTO " . self::table() . " (name, val, autoload) VALUES (?, ?, 'no')");
					if ($stmt === false) {
						wordfence::status(2, 'error', "Error writing value chunk for {$key} (MySQLi error: [{$dbh->errno}] {$dbh->error})");
						return false;
					}
					$null = NULL;
					$stmt->bind_param("sb", $chunkKey, $null);
					
					if (!$stmt->send_long_data(1, $dataChunk)) {
						wordfence::status(2, 'error', "Error writing value chunk for {$key} (MySQLi error: [{$dbh->errno}] {$dbh->error})");
						return false;
					}
					
					if (!$stmt->execute()) {
						wordfence::status(2, 'error', "Error finishing writing value for {$key} (MySQLi error: [{$dbh->errno}] {$dbh->error})");
						return false;
					}
				}
				else {
					if (!self::getDB()->queryWrite(sprintf("insert ignore into " . self::table() . " (name, val, autoload) values (%%s, X'%s', 'no')", $dataChunk), $chunkedValueKey . $chunks)) {
						$errno = mysql_errno($wpdb->dbh);
						wordfence::status(2, 'error', "Error writing value chunk for {$key} (MySQL error: [$errno] {$wpdb->last_error})");
						return false;
					}
				}
				$chunks++;
			}
			
			if (!self::getDB()->queryWrite(sprintf("insert ignore into " . self::table() . " (name, val, autoload) values (%%s, X'%s', 'no')", bin2hex(serialize(array('count' => $chunks)))), $chunkedValueKey . 'header')) {
				wordfence::status(2, 'error', "Error writing value header for {$key}");
				return false;
			}
		}
		else {
			$exists = self::getDB()->querySingle("select name from " . self::table() . " where name='%s'", $key);
			
			if ($useMySQLi) {
				if ($exists) {
					$stmt = $dbh->prepare("UPDATE " . self::table() . " SET val=? WHERE name=?");
					if ($stmt === false) {
						wordfence::status(2, 'error', "Error writing value for {$key} (MySQLi error: [{$dbh->errno}] {$dbh->error})");
						return false;
					}
					$null = NULL;
					$stmt->bind_param("bs", $null, $key);
				}
				else {
					$stmt = $dbh->prepare("INSERT IGNORE INTO " . self::table() . " (val, name, autoload) VALUES (?, ?, ?)");
					if ($stmt === false) {
						wordfence::status(2, 'error', "Error writing value for {$key} (MySQLi error: [{$dbh->errno}] {$dbh->error})");
						return false;
					}
					$null = NULL;
					$stmt->bind_param("bss", $null, $key, $autoload);
				}
				
				if (!$stmt->send_long_data(0, $data)) {
					wordfence::status(2, 'error', "Error writing value for {$key} (MySQLi error: [{$dbh->errno}] {$dbh->error})");
					return false;
				}
				
				if (!$stmt->execute()) {
					wordfence::status(2, 'error', "Error finishing writing value for {$key} (MySQLi error: [{$dbh->errno}] {$dbh->error})");
					return false;
				}
			}
			else {
				if ($exists) {
					self::getDB()->queryWrite(sprintf("update " . self::table() . " set val=X'%s' where name=%%s", $data), $key);
				}
				else {
					self::getDB()->queryWrite(sprintf("insert ignore into " . self::table() . " (name, val, autoload) values (%%s, X'%s', %%s)", $data), $key, $autoload);
				}
			}
		}
		self::getDB()->flush();
		
		if ($autoload != self::DONT_AUTOLOAD) {
			self::updateCachedOption($key, $val);
		}
		return true;
	}
	
	private static function delete_ser_chunked($key) {
		if (!self::$tableExists) {
			return;
		}
		
		self::removeCachedOption($key);
		
		$chunkedValueKey = self::ser_chunked_key($key);
		$header = self::getDB()->querySingle("select val from " . self::table() . " where name=%s", $chunkedValueKey . 'header');
		if (!$header) {
			return;
		}
		
		$header = unserialize($header);
		$count = $header['count'];
		for ($i = 0; $i < $count; $i++) {
			self::getDB()->queryWrite("delete from " . self::table() . " where name='%s'", $chunkedValueKey . $i);
		}
		self::getDB()->queryWrite("delete from " . self::table() . " where name='%s'", $chunkedValueKey . 'header');
	}
	public static function f($key){
		echo esc_attr(self::get($key));
	}
	public static function p() {
		return self::get('isPaid');
	}
	public static function cbp($key){
		if(self::get('isPaid') && self::get($key)){
			echo ' checked ';
		}
	}
	public static function cb($key){
		if(self::get($key)){
			echo ' checked ';
		}
	}
	public static function sel($key, $val, $isDefault = false){
		if((! self::get($key)) && $isDefault){ echo ' selected '; }
		if(self::get($key) == $val){ echo ' selected '; }
	}
	private static function getDB(){
		if(! self::$DB){ 
			self::$DB = new wfDB();
		}
		return self::$DB;
	}
	private static function table(){
		if(! self::$table){
			self::$table = wfDB::networkTable('wfConfig');
		}
		return self::$table;
	}
	public static function haveAlertEmails(){
		$emails = self::getAlertEmails();
		return sizeof($emails) > 0 ? true : false;
	}
	public static function getAlertEmails(){
		$dat = explode(',', self::get('alertEmails'));
		$emails = array();
		foreach($dat as $email){
			if(preg_match('/\@/', $email)){
				$emails[] = trim($email);
			}
		}
		return $emails;
	}
	public static function getAlertLevel(){
		if(self::get('alertOn_warnings')){
			return 2;
		} else if(self::get('alertOn_critical')){
			return 1;
		} else {
			return 0;
		}
	}
	public static function liveTrafficEnabled(&$overriden = null){
		$enabled = self::get('liveTrafficEnabled');
		if (WORDFENCE_DISABLE_LIVE_TRAFFIC || function_exists('wpe_site')) {
			$enabled = false;
			if ($overriden !== null) {
				$overriden = true;
			}
		}
		return $enabled;
	}
	public static function enableAutoUpdate(){
		wfConfig::set('autoUpdate', '1');
		wp_clear_scheduled_hook('wordfence_daily_autoUpdate');
		if (is_main_site()) {
			wp_schedule_event(time(), 'daily', 'wordfence_daily_autoUpdate');
		}
	}
	public static function disableAutoUpdate(){
		wfConfig::set('autoUpdate', '0');	
		wp_clear_scheduled_hook('wordfence_daily_autoUpdate');
	}
	public static function createLock($name, $timeout = null) { //Polyfill since WP's built-in version wasn't added until 4.5
		global $wpdb;
		$oldBlogID = $wpdb->set_blog_id(0);
		
		if (function_exists('WP_Upgrader::create_lock')) {
			$result = WP_Upgrader::create_lock($name, $timeout);
			$wpdb->set_blog_id($oldBlogID);
			return $result;
		}
		
		if (!$timeout) {
			$timeout = 3600;
		}
		
		$lock_option = $name . '.lock';
		$lock_result = $wpdb->query($wpdb->prepare("INSERT IGNORE INTO `{$wpdb->options}` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, 'no') /* LOCK */", $lock_option, time()));
		
		if (!$lock_result) {
			$lock_result = get_option($lock_option);
			if (!$lock_result) {
				$wpdb->set_blog_id($oldBlogID);
				return false;
			}
			
			if ($lock_result > (time() - $timeout)) {
				$wpdb->set_blog_id($oldBlogID);
				return false;
			}
			
			self::releaseLock($name);
			$wpdb->set_blog_id($oldBlogID);
			return self::createLock($name, $timeout);
		}
		
		update_option($lock_option, time());
		$wpdb->set_blog_id($oldBlogID);
		return true;
	}
	public static function releaseLock($name) {
		global $wpdb;
		$oldBlogID = $wpdb->set_blog_id(0);
		if (function_exists('WP_Upgrader::release_lock')) {
			$result = WP_Upgrader::release_lock($name);
		}
		else {
			$result = delete_option($name . '.lock');
		}
		
		$wpdb->set_blog_id($oldBlogID);
		return $result;
	}
	public static function autoUpdate(){
		try {
			if (!wfConfig::get('other_bypassLitespeedNoabort', false) && getenv('noabort') != '1' && stristr($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false) {
				$lastEmail = self::get('lastLiteSpdEmail', false);
				if( (! $lastEmail) || (time() - (int)$lastEmail > (86400 * 30))){
					self::set('lastLiteSpdEmail', time());
					 wordfence::alert("Wordfence Upgrade not run. Please modify your .htaccess", "To preserve the integrity of your website we are not running Wordfence auto-update.\n" .
						"You are running the LiteSpeed web server which has been known to cause a problem with Wordfence auto-update.\n" .
						"Please go to your website now and make a minor change to your .htaccess to fix this.\n" .
						"You can find out how to make this change at:\n" .
						 wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_LITESPEED_WARNING) . "\n" .
						"\nAlternatively you can disable auto-update on your website to stop receiving this message and upgrade Wordfence manually.\n",
						'127.0.0.1'
						);
				}
				return;
			}
			require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
			require_once(ABSPATH . 'wp-admin/includes/misc.php');
			/* We were creating show_message here so that WP did not write to STDOUT. This had the strange effect of throwing an error about redeclaring show_message function, but only when a crawler hit the site and triggered the cron job. Not a human. So we're now just require'ing misc.php which does generate output, but that's OK because it is a loopback cron request.  
			if(! function_exists('show_message')){ 
				function show_message($msg = 'null'){}
			}
			*/
			if(! defined('FS_METHOD')){ 
				define('FS_METHOD', 'direct'); //May be defined already and might not be 'direct' so this could cause problems. But we were getting reports of a warning that this is already defined, so this check added. 
			}
			require_once(ABSPATH . 'wp-includes/update.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			
			if (!self::createLock('wfAutoUpdate')) {
				return;
			}
			
			wp_update_plugins();
			ob_start();
			$upgrader = new Plugin_Upgrader();
			$upret = $upgrader->upgrade(WORDFENCE_BASENAME);
			if($upret){
				$cont = file_get_contents(WORDFENCE_FCPATH);
				if(wfConfig::get('alertOn_update') == '1' && preg_match('/Version: (\d+\.\d+\.\d+)/', $cont, $matches) ){
					wordfence::alert("Wordfence Upgraded to version " . $matches[1], "Your Wordfence installation has been upgraded to version " . $matches[1], '127.0.0.1');
				}
			}
			$output = @ob_get_contents();
			@ob_end_clean();
		} catch(Exception $e){}
		
		self::releaseLock('wfAutoUpdate');
	}
	
	/**
	 * .htaccess file contents to disable all script execution in a given directory.
	 */
	private static $_disable_scripts_htaccess = '# BEGIN Wordfence code execution protection
<IfModule mod_php5.c>
php_flag engine 0
</IfModule>
<IfModule mod_php7.c>
php_flag engine 0
</IfModule>

AddHandler cgi-script .php .phtml .php3 .pl .py .jsp .asp .htm .shtml .sh .cgi
Options -ExecCGI
# END Wordfence code execution protection
';
	private static $_disable_scripts_regex = '/# BEGIN Wordfence code execution protection.+?# END Wordfence code execution protection/s';
	
	private static function _uploadsHtaccessFilePath() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/.htaccess';
	}

	/**
	 * Add/Merge .htaccess file in the uploads directory to prevent code execution.
	 *
	 * @return bool
	 * @throws wfConfigException
	 */
	public static function disableCodeExecutionForUploads() {
		$uploads_htaccess_file_path = self::_uploadsHtaccessFilePath();
		$uploads_htaccess_has_content = false;
		if (file_exists($uploads_htaccess_file_path)) {
			$htaccess_contents = file_get_contents($uploads_htaccess_file_path);
			
			// htaccess exists and contains our htaccess code to disable script execution, nothing more to do
			if (strpos($htaccess_contents, self::$_disable_scripts_htaccess) !== false) {
				return true;
			}
			$uploads_htaccess_has_content = strlen(trim($htaccess_contents)) > 0;
		}
		if (@file_put_contents($uploads_htaccess_file_path, ($uploads_htaccess_has_content ? "\n\n" : "") . self::$_disable_scripts_htaccess, FILE_APPEND | LOCK_EX) === false) {
			throw new wfConfigException("Unable to save the .htaccess file needed to disable script execution in the uploads directory.  Please check your permissions on that directory.");
		}
		self::set('disableCodeExecutionUploadsPHP7Migrated', true);
		return true;
	}
	
	public static function migrateCodeExecutionForUploadsPHP7() {
		if (self::get('disableCodeExecutionUploads')) {
			if (!self::get('disableCodeExecutionUploadsPHP7Migrated')) {
				$uploads_htaccess_file_path = self::_uploadsHtaccessFilePath();
				if (file_exists($uploads_htaccess_file_path)) {
					$htaccess_contents = file_get_contents($uploads_htaccess_file_path);
					if (preg_match(self::$_disable_scripts_regex, $htaccess_contents)) {
						$htaccess_contents = preg_replace(self::$_disable_scripts_regex, self::$_disable_scripts_htaccess, $htaccess_contents); 
						@file_put_contents($uploads_htaccess_file_path, $htaccess_contents);
						self::set('disableCodeExecutionUploadsPHP7Migrated', true);
					}
				}
			}
		}
	}

	/**
	 * Remove script execution protections for our the .htaccess file in the uploads directory.
	 *
	 * @return bool
	 * @throws wfConfigException
	 */
	public static function removeCodeExecutionProtectionForUploads() {
		$uploads_htaccess_file_path = self::_uploadsHtaccessFilePath();
		if (file_exists($uploads_htaccess_file_path)) {
			$htaccess_contents = file_get_contents($uploads_htaccess_file_path);

			// Check that it is in the file
			if (preg_match(self::$_disable_scripts_regex, $htaccess_contents)) {
				$htaccess_contents = preg_replace(self::$_disable_scripts_regex, '', $htaccess_contents);

				$error_message = "Unable to remove code execution protections applied to the .htaccess file in the uploads directory.  Please check your permissions on that file.";
				if (strlen(trim($htaccess_contents)) === 0) {
					// empty file, remove it
					if (!@unlink($uploads_htaccess_file_path)) {
						throw new wfConfigException($error_message);
					}

				} elseif (@file_put_contents($uploads_htaccess_file_path, $htaccess_contents, LOCK_EX) === false) {
					throw new wfConfigException($error_message);
				}
			}
		}
		return true;
	}
	
	/**
	 * Validates the array of configuration changes without applying any. All bounds checks must be performed here.
	 *
	 * @param array $changes
	 * @return bool|array Returns true if valid, otherwise a displayable error message per error encountered.
	 * @throws wfWAFStorageFileException
	 */
	public static function validate($changes) {
		$errors = array();
		$waf = wfWAF::getInstance();
		$wafConfig = $waf->getStorageEngine();
		
		foreach ($changes as $key => $value) {
			$checked = false;
			switch ($key) {
				//============ WAF
				case 'learningModeGracePeriod':
				{
					//If currently in or will be in learning mode, restrict the grace period to be in the future
					$wafStatus = (isset($changes['wafStatus']) ? $changes['wafStatus'] : $wafConfig->getConfig('wafStatus'));
					$gracePeriodEnd = strtotime($value);
					if ($wafStatus == wfFirewall::FIREWALL_MODE_LEARNING && $gracePeriodEnd <= time()) {
						$errors[] = array('option' => $key, 'error' => __('The grace period end time must be in the future.', 'wordfence'));
					}
					
					$checked = true;
					break;
				}
				case 'wafStatus':
				{
					if ($value != wfFirewall::FIREWALL_MODE_ENABLED && $value != wfFirewall::FIREWALL_MODE_LEARNING && $value != wfFirewall::FIREWALL_MODE_DISABLED) {
						$errors[] = array('option' => $key, 'error' => __('Unknown firewall mode.', 'wordfence'));
					}
					
					$checked = true;
					break;
				}
				
				//============ Plugin
				case 'alertEmails':
				{
					$dirtyEmails = explode(',', preg_replace('/[\r\n\s\t]+/', '', $value));
					$dirtyEmails = array_filter($dirtyEmails);
					$badEmails = array();
					foreach ($dirtyEmails as $email) {
						if (!wfUtils::isValidEmail($email)) {
							$badEmails[] = $email;
						}
					}
					if (count($badEmails) > 0) {
						$errors[] = array('option' => $key, 'error' => __('The following emails are invalid: ', 'wordfence') . esc_html(implode(', ', $badEmails), array()));
					}
					
					$checked = true;
					break;
				}
				case 'scan_include_extra':
				{
					$dirtyRegexes = explode("\n", $value);
					foreach ($dirtyRegexes as $regex) {
						if (@preg_match("/$regex/", "") === false) {
							$errors[] = array('option' => $key, 'error' => sprintf(__('"%s" is not a valid regular expression.', 'wordfence'), esc_html($regex)));
						}
					}
					$checked = true;
					break;
				}
				case 'whitelisted':
				{
					$dirtyWhitelisted = explode(',', preg_replace('/[\r\n\s\t]+/', ',', $value));
					$dirtyWhitelisted = array_filter($dirtyWhitelisted);
					$badWhiteIPs = array();
					$range = new wfUserIPRange();
					foreach ($dirtyWhitelisted as $whiteIP) {
						$range->setIPString($whiteIP);
						if (!$range->isValidRange()) {
							$badWhiteIPs[] = $whiteIP;
						}
					}
					if (count($badWhiteIPs) > 0) {
						$errors[] = array('option' => $key, 'error' => __('Please make sure you separate your IP addresses with commas. The following whitelisted IP addresses are invalid: ', 'wordfence') . esc_html(implode(', ', $badWhiteIPs), array()));
					}
					
					$checked = true;
					break;
				}
				case 'liveTraf_ignoreUsers':
				{
					$dirtyUsers = explode(',', $value);
					$invalidUsers = array();
					foreach ($dirtyUsers as $val) {
						$val = trim($val);
						if (strlen($val) > 0) {
							if (!get_user_by('login', $val)) {
								$invalidUsers[] = $val;
							}
						}
					}
					if (count($invalidUsers) > 0) {
						$errors[] = array('option' => $key, 'error' => __('The following users you selected to ignore in live traffic reports are not valid on this system: ', 'wordfence') . esc_html(implode(', ', $invalidUsers), array()));
					}
					
					$checked = true;
					break;
				}
				case 'liveTraf_ignoreIPs':
				{
					$dirtyIPs = explode(',', preg_replace('/[\r\n\s\t]+/', '', $value));
					$dirtyIPs = array_filter($dirtyIPs);
					$invalidIPs = array();
					foreach ($dirtyIPs as $val) {
						if (!wfUtils::isValidIP($val)) {
							$invalidIPs[] = $val;
						}
					}
					if (count($invalidIPs) > 0) {
						$errors[] = array('option' => $key, 'error' => __('The following IPs you selected to ignore in live traffic reports are not valid: ', 'wordfence') . esc_html(implode(', ', $invalidIPs), array()));
					}
					
					$checked = true;
					break;
				}
				case 'howGetIPs_trusted_proxies':
				{
					$dirtyIPs = preg_split('/[\r\n,]+/', $value);
					$dirtyIPs = array_filter($dirtyIPs);
					$invalidIPs = array();
					foreach ($dirtyIPs as $val) {
						if (!(wfUtils::isValidIP($val) || wfUtils::isValidCIDRRange($val))) {
							$invalidIPs[] = $val;
						}
					}
					if (count($invalidIPs) > 0) {
						$errors[] = array('option' => $key, 'error' => __('The following IPs/ranges you selected to trust as proxies are not valid: ', 'wordfence') . esc_html(implode(', ', $invalidIPs), array()));
					}
					
					$checked = true;
					break;
				}
				case 'apiKey':
				{
					$value = trim($value);
					if (empty($value)) {
						$errors[] = array('option' => $key, 'error' => __('An empty license key was entered.', 'wordfence'));
					}
					else if ($value && !preg_match('/^[a-fA-F0-9]+$/', $value)) {
						$errors[] = array('option' => $key, 'error' => __('The license key entered is not in a valid format. It must contain only numbers and the letters A-F.', 'wordfence'));
					}
					
					$checked = true;
					break;
				}
			}
		}
		
		if (empty($errors)) {
			return true;
		}
		return $errors;
	}
	
	/**
	 * Saves the array of configuration changes in the correct place. This may currently be the wfConfig table, the WAF's config file, or both. The
	 * validation function will handle all bounds checks and this will be limited to normalizing the values as needed.
	 * 
	 * @param array $changes
	 * @throws wfConfigException
	 * @throws wfWAFStorageFileException
	 */
	public static function save($changes) {
		$waf = wfWAF::getInstance();
		$wafConfig = $waf->getStorageEngine();
		
		$apiKey = false;
		if (isset($changes['apiKey'])) { //Defer to end
			$apiKey = $changes['apiKey'];
			unset($changes['apiKey']);
		}
		
		foreach ($changes as $key => $value) {
			$saved = false;
			switch ($key) {
				//============ WAF
				case 'learningModeGracePeriod':
				{
					$wafStatus = (isset($changes['wafStatus']) ? $changes['wafStatus'] : $wafConfig->getConfig('wafStatus'));
					if ($wafStatus == wfFirewall::FIREWALL_MODE_LEARNING) {
						$dt = wfUtils::parseLocalTime($value);
						$gracePeriodEnd = $dt->format('U');
						$wafConfig->setConfig($key, $gracePeriodEnd);
					}
					
					$saved = true;
					break;
				}
				case 'learningModeGracePeriodEnabled':
				{
					$wafStatus = (isset($changes['wafStatus']) ? $changes['wafStatus'] : $wafConfig->getConfig('wafStatus'));
					if ($wafStatus == wfFirewall::FIREWALL_MODE_LEARNING) {
						$wafConfig->setConfig($key, wfUtils::truthyToInt($value));
					}
					
					$saved = true;
					break;
				}
				case 'wafStatus':
				{
					$wafConfig->setConfig($key, $value);
					if ($value != wfFirewall::FIREWALL_MODE_LEARNING) {
						$wafConfig->setConfig('learningModeGracePeriodEnabled', 0);
						$wafConfig->unsetConfig('learningModeGracePeriod');
					}
					
					$saved = true;
					break;
				}
				case 'wafRules':
				{
					$disabledRules = (array) $wafConfig->getConfig('disabledRules');
					foreach ($value as $ruleID => $ruleEnabled) {
						$ruleID = (int) $ruleID;
						if ($ruleEnabled) {
							unset($disabledRules[$ruleID]);
						} else {
							$disabledRules[$ruleID] = true;
						}
					}
					$wafConfig->setConfig('disabledRules', $disabledRules);
					
					$saved = true;
					break;
				}
				case 'whitelistedURLParams':
				{
					$whitelistedURLParams = (array) $wafConfig->getConfig('whitelistedURLParams');
					if (isset($value['delete'])) {
						foreach ($value['delete'] as $whitelistKey => $unused) {
							unset($whitelistedURLParams[$whitelistKey]);
						}
					}
					if (isset($value['enabled'])) {
						foreach ($value['enabled'] as $whitelistKey => $enabled) {
							if (array_key_exists($whitelistKey, $whitelistedURLParams) && is_array($whitelistedURLParams[$whitelistKey])) {
								foreach ($whitelistedURLParams[$whitelistKey] as $ruleID => $data) {
									$whitelistedURLParams[$whitelistKey][$ruleID]['disabled'] = !$enabled;
								}
							}
						}
					}
					$wafConfig->setConfig('whitelistedURLParams', $whitelistedURLParams);
					
					if (isset($value['add'])) {
						foreach ($value['add'] as $entry) {
							$path = @base64_decode($entry['path']);
							$paramKey = @base64_decode($entry['paramKey']);
							if (!$path || !$paramKey) {
								continue;
							}
							$data = array(
								'timestamp'   => (int) $entry['data']['timestamp'],
								'description' => $entry['data']['description'],
								'ip'          => wfUtils::getIP(),
								'disabled'    => !!$entry['data']['disabled'],
							);
							if (function_exists('get_current_user_id')) {
								$data['userID'] = get_current_user_id();
							}
							$waf->whitelistRuleForParam($path, $paramKey, 'all', $data);
						}
					}
					
					$saved = true;
					break;
				}
				case 'disableWAFIPBlocking':
				{
					wfConfig::set($key, wfUtils::truthyToInt($value));
					$wafConfig->setConfig($key, wfUtils::truthyToInt($value));
					$saved = true;
					break;
				}
				case 'disableWAFBlacklistBlocking':
				{
					$wafConfig->setConfig($key, wfUtils::truthyToInt($value));
					$saved = true;
					break;
				}
				
				//============ Plugin (specialty treatment)
				case 'alertEmails':
				{
					$emails = explode(',', preg_replace('/[\r\n\s\t]+/', '', $value));
					$emails = array_filter($emails); //Already validated above
					if (count($emails) > 0) {
						wfConfig::set($key, implode(',', $emails));
					}
					else {
						wfConfig::set($key, '');
					}
					
					$saved = true;
					break;
				}
				case 'loginSec_userBlacklist':
				case 'scan_exclude':
				case 'email_summary_excluded_directories':
				{
					if (is_array($value)) {
						$value = implode("\n", $value);
					}
					
					wfConfig::set($key, wfUtils::cleanupOneEntryPerLine($value));
					$saved = true;
					break;
				}
				case 'whitelisted':
				{
					$whiteIPs = explode(',', preg_replace('/[\r\n\s\t]+/', ',', $value));
					$whiteIPs = array_filter($whiteIPs); //Already validated above
					if (count($whiteIPs) > 0) {
						wfConfig::set($key, implode(',', $whiteIPs));
					}
					else {
						wfConfig::set($key, '');
					}
					
					$saved = true;
					break;
				}
				case 'liveTraf_ignoreUsers':
				{
					$dirtyUsers = explode(',', $value);
					$validUsers = array();
					foreach ($dirtyUsers as $val) {
						$val = trim($val);
						if (strlen($val) > 0) {
							$validUsers[] = $val; //Already validated above
						}
					}
					if (count($validUsers) > 0) {
						wfConfig::set($key, implode(',', $validUsers));
					}
					else {
						wfConfig::set($key, '');
					}
					
					$saved = true;
					break;
				}
				case 'liveTraf_ignoreIPs':
				{
					$validIPs = explode(',', preg_replace('/[\r\n\s\t]+/', '', $value));
					$validIPs = array_filter($validIPs); //Already validated above
					if (count($validIPs) > 0) {
						wfConfig::set($key, implode(',', $validIPs));
					}
					else {
						wfConfig::set($key, '');
					}
					
					$saved = true;
					break;
				}
				case 'liveTraf_ignoreUA':
				{
					if (preg_match('/[a-zA-Z0-9\d]+/', $value)) {
						wfConfig::set($key, trim($value));
					}
					else {
						wfConfig::set($key, '');
					}
					$saved = true;
					break;
				}
				case 'howGetIPs_trusted_proxies':
				{
					$validIPs = preg_split('/[\r\n,]+/', $value);
					$validIPs = array_filter($validIPs); //Already validated above
					if (count($validIPs) > 0) {
						wfConfig::set($key, implode("\n", $validIPs));
					}
					else {
						wfConfig::set($key, '');
					}
					
					$saved = true;
					break;
				}
				case 'other_WFNet':
				{
					$value = wfUtils::truthyToBoolean($value);
					wfConfig::set($key, $value);
					if (!$value) {
						wfBlock::removeTemporaryWFSNBlocks();
					}
					$saved = true;
					break;
				}
				case 'howGetIPs':
				{
					wfConfig::set($key, $value);
					wfConfig::set('detectProxyNextCheck', false, wfConfig::DONT_AUTOLOAD);
					$saved = true;
					break;
				}
				case 'bannedURLs':
				{
					wfConfig::set($key, preg_replace('/[\n\r]+/', ',', $value));
					$saved = true;
					break;
				}
				case 'autoUpdate':
				{
					if (wfUtils::truthyToBoolean($value)) {
						wfConfig::enableAutoUpdate(); //Also sets the option
					}
					else {
						wfConfig::disableAutoUpdate();
					}
					$saved = true;
					break;
				}
				case 'disableCodeExecutionUploads':
				{
					$value = wfUtils::truthyToBoolean($value);
					wfConfig::set($key, $value);
					if ($value) {
						wfConfig::disableCodeExecutionForUploads(); //Can throw wfConfigException
					}
					else {
						wfConfig::removeCodeExecutionProtectionForUploads();
					}
					$saved = true;
					break;
				}
				case 'email_summary_enabled':
				{
					$value = wfUtils::truthyToBoolean($value);
					wfConfig::set($key, $value);
					if ($value) {
						wfActivityReport::scheduleCronJob();
					}
					else {
						wfActivityReport::disableCronJob();
					}
					$saved = true;
					break;
				}
				case 'other_hideWPVersion':
				{
					$value = wfUtils::truthyToBoolean($value);
					wfConfig::set($key, $value);
					if ($value) {
						wfUtils::hideReadme();
					}
					else {
						wfUtils::showReadme();
					}
					$saved = true;
					break;
				}
				case 'betaThreatDefenseFeed':
				{
					$value = wfUtils::truthyToBoolean($value);
					wfConfig::set($key, $value);
					if (class_exists('wfWAFConfig')) {
						wfWAFConfig::set('betaThreatDefenseFeed', $value);
					}
					$saved = true;
					break;
				}
				
				//Scan scheduling
				case 'scanSched':
				case 'schedStartHour':
				case 'manualScanType':
				case 'schedMode':
				case 'scheduledScansEnabled':
				{
					wfScanner::setNeedsRescheduling();
					//Letting these fall through to the default save handler
					break;
				}
			}
			
			//============ Plugin (default treatment)
			if (!$saved) {
				if (isset(self::$defaultConfig['checkboxes'][$key]) ||
					(isset(self::$defaultConfig['otherParams'][$key]) && self::$defaultConfig['otherParams'][$key]['validation']['type'] == self::TYPE_BOOL) ||
					(isset(self::$defaultConfig['defaultsOnly'][$key]) && self::$defaultConfig['defaultsOnly'][$key]['validation']['type'] == self::TYPE_BOOL)) { //Boolean
					wfConfig::set($key, wfUtils::truthyToInt($value));
				}
				else if ((isset(self::$defaultConfig['otherParams'][$key]) && self::$defaultConfig['otherParams'][$key]['validation']['type'] == self::TYPE_INT) ||
						 (isset(self::$defaultConfig['defaultsOnly'][$key]) && self::$defaultConfig['defaultsOnly'][$key]['validation']['type'] == self::TYPE_INT)) {
					wfConfig::set($key, (int) $value);
				}
				else if ((isset(self::$defaultConfig['otherParams'][$key]) && (self::$defaultConfig['otherParams'][$key]['validation']['type'] == self::TYPE_FLOAT || self::$defaultConfig['otherParams'][$key]['validation']['type'] == self::TYPE_DOUBLE)) ||
						 (isset(self::$defaultConfig['defaultsOnly'][$key]) && (self::$defaultConfig['defaultsOnly'][$key]['validation']['type'] == self::TYPE_FLOAT || self::$defaultConfig['defaultsOnly'][$key]['validation']['type'] == self::TYPE_DOUBLE))) {
					wfConfig::set($key, (double) $value);
				}
				else if ((isset(self::$defaultConfig['otherParams'][$key]) && self::$defaultConfig['otherParams'][$key]['validation']['type'] == self::TYPE_STRING) ||
						 (isset(self::$defaultConfig['defaultsOnly'][$key]) && self::$defaultConfig['defaultsOnly'][$key]['validation']['type'] == self::TYPE_STRING)) {
					wfConfig::set($key, (string) $value);
				}
				else if (in_array($key, self::$serializedOptions)) {
					wfConfig::set_ser($key, $value);
				}
				else {
					//TODO: remove me when done with QA
					error_log("*** DEBUG: Config option '{$key}' missing save handler.");
				}
			}
		}
	
		if ($apiKey !== false) {
			$existingAPIKey = wfConfig::get('apiKey', '');
			$apiKey = strtolower(trim($apiKey)); //Already validated above
			if (empty($apiKey)) { //Empty, try getting a free key
				$api = new wfAPI('', wfUtils::getWPVersion());
				try {
					$keyData = $api->call('get_anon_api_key');
					if ($keyData['ok'] && $keyData['apiKey']) {
						wfConfig::set('apiKey', $keyData['apiKey']);
						wfConfig::set('isPaid', false);
						wfConfig::set('keyType', wfAPI::KEY_TYPE_FREE);
						wordfence::licenseStatusChanged();
					}
					else {
						throw new Exception("The Wordfence server's response did not contain the expected elements.");
					}
				}
				catch (Exception $e) {
					throw new wfConfigException(__('Your options have been saved, but you left your API key blank, so we tried to get you a free API key from the Wordfence servers. There was a problem fetching the free key: ', 'wordfence') . wp_kses($e->getMessage(), array()));
				}
			}
			else if ($existingAPIKey != $apiKey) { //Key changed, try activating
				$api = new wfAPI($apiKey, wfUtils::getWPVersion());
				try {
					$res = $api->call('check_api_key', array(), array());
					if ($res['ok'] && isset($res['isPaid'])) {
						$isPaid = wfUtils::truthyToBoolean($res['isPaid']);
						wfConfig::set('apiKey', $apiKey);
						wfConfig::set('isPaid', $isPaid); //res['isPaid'] is boolean coming back as JSON and turned back into PHP struct. Assuming JSON to PHP handles bools.
						wordfence::licenseStatusChanged();
						if (!$isPaid) {
							wfConfig::set('keyType', wfAPI::KEY_TYPE_FREE);
						}
					}
					else {
						throw new Exception("The Wordfence server's response did not contain the expected elements.");
					}
				}
				catch (Exception $e) {
					throw new wfConfigException(__('Your options have been saved. However we noticed you changed your API key, and we tried to verify it with the Wordfence servers but received an error: ', 'wordfence') . wp_kses($e->getMessage(), array()));
				}
			}
			else { //Key unchanged, just ping it
				$api = new wfAPI($apiKey, wfUtils::getWPVersion());
				try {
					$keyType = wfAPI::KEY_TYPE_FREE;
					$keyData = $api->call('ping_api_key', array(), array('supportHash' => wfConfig::get('supportHash', '')));
					if (isset($keyData['_isPaidKey'])) {
						$keyType = wfConfig::get('keyType');
					}
					if (isset($keyData['dashboard'])) {
						wfConfig::set('lastDashboardCheck', time());
						wfDashboard::processDashboardResponse($keyData['dashboard']);
					}
					if (isset($keyData['support']) && isset($keyData['supportHash'])) {
						wfConfig::set('supportContent', $keyData['support']);
						wfConfig::set('supportHash', $keyData['supportHash']);
					}
					if (isset($keyData['scanSchedule']) && is_array($keyData['scanSchedule'])) {
						wfConfig::set_ser('noc1ScanSchedule', $keyData['scanSchedule']);
						if (wfScanner::shared()->schedulingMode() == wfScanner::SCAN_SCHEDULING_MODE_AUTOMATIC) {
							wfScanner::shared()->scheduleScans();
						}
					}
					
					wfConfig::set('keyType', $keyType);
				}
				catch (Exception $e){
					throw new wfConfigException(__('Your options have been saved. However we tried to verify your API key with the Wordfence servers and received an error: ', 'wordfence') . wp_kses($e->getMessage(), array()));
				}
			}
		}
		
		wfNotification::reconcileNotificationsWithOptions();
	}
	
	public static function restoreDefaults($section) {
		switch ($section) {
			case self::OPTIONS_TYPE_GLOBAL:
				$options = array(
					'alertOn_critical',
					'alertOn_update',
					'alertOn_warnings',
					'alertOn_throttle',
					'alertOn_block',
					'alertOn_loginLockout',
					'alertOn_breachLogin',
					'alertOn_lostPasswdForm',
					'alertOn_adminLogin',
					'alertOn_firstAdminLoginOnly',
					'alertOn_nonAdminLogin',
					'alertOn_firstNonAdminLoginOnly',
					'alertOn_wordfenceDeactivated',
					'liveActivityPauseEnabled',
					'notification_updatesNeeded',
					'notification_securityAlerts',
					'notification_promotions',
					'notification_blogHighlights',
					'notification_productUpdates',
					'notification_scanStatus',
					'other_hideWPVersion',
					'other_bypassLitespeedNoabort',
					'deleteTablesOnDeact',
					'autoUpdate',
					'disableCookies',
					'disableCodeExecutionUploads',
					'email_summary_enabled',
					'email_summary_dashboard_widget_enabled',
					'howGetIPs',
					'actUpdateInterval',
					'alert_maxHourly',
					'email_summary_interval',
					'email_summary_excluded_directories',
					'howGetIPs_trusted_proxies',
					'displayTopLevelOptions',
				);
				break;
			case self::OPTIONS_TYPE_FIREWALL:
				$options = array(
					'firewallEnabled',
					'blockFakeBots',
					'autoBlockScanners',
					'loginSecurityEnabled',
					'loginSec_strongPasswds_enabled',
					'loginSec_breachPasswds_enabled',
					'loginSec_lockInvalidUsers',
					'loginSec_maskLoginErrors',
					'loginSec_blockAdminReg',
					'loginSec_disableAuthorScan',
					'loginSec_disableOEmbedAuthor',
					'other_blockBadPOST',
					'other_pwStrengthOnUpdate',
					'other_WFNet',
					'ajaxWatcherDisabled_front',
					'ajaxWatcherDisabled_admin',
					'wafAlertOnAttacks',
					'disableWAFIPBlocking',
					'whitelisted',
					'bannedURLs',
					'loginSec_userBlacklist',
					'neverBlockBG',
					'loginSec_countFailMins',
					'loginSec_lockoutMins',
					'loginSec_strongPasswds',
					'loginSec_breachPasswds',
					'loginSec_maxFailures',
					'loginSec_maxForgotPasswd',
					'maxGlobalRequests',
					'maxGlobalRequests_action',
					'maxRequestsCrawlers',
					'maxRequestsCrawlers_action',
					'maxRequestsHumans',
					'maxRequestsHumans_action',
					'max404Crawlers',
					'max404Crawlers_action',
					'max404Humans',
					'max404Humans_action',
					'maxScanHits',
					'maxScanHits_action',
					'blockedTime',
					'allowed404s',
					'wafAlertWhitelist',
					'wafAlertInterval',
					'wafAlertThreshold',
					'dismissAutoPrependNotice',
				);
				break;
			case self::OPTIONS_TYPE_BLOCKING:
				$options = array(
					'displayTopLevelBlocking',
					'cbl_loggedInBlocked',
					'cbl_action',
					'cbl_redirURL',
					'cbl_bypassRedirURL',
					'cbl_bypassRedirDest',
					'cbl_bypassViewURL',
				);
				break;
			case self::OPTIONS_TYPE_SCANNER:
				$options = array(
					'checkSpamIP',
					'spamvertizeCheck',
					'scheduledScansEnabled',
					'lowResourceScansEnabled',
					'scansEnabled_checkGSB',
					'scansEnabled_checkHowGetIPs',
					'scansEnabled_core',
					'scansEnabled_themes',
					'scansEnabled_plugins',
					'scansEnabled_coreUnknown',
					'scansEnabled_malware',
					'scansEnabled_fileContents',
					'scansEnabled_fileContentsGSB',
					'scansEnabled_checkReadableConfig',
					'scansEnabled_suspectedFiles',
					'scansEnabled_posts',
					'scansEnabled_comments',
					'scansEnabled_suspiciousOptions',
					'scansEnabled_passwds',
					'scansEnabled_diskSpace',
					'scansEnabled_options',
					'scansEnabled_wpscan_fullPathDisclosure',
					'scansEnabled_wpscan_directoryListingEnabled',
					'scansEnabled_dns',
					'scansEnabled_scanImages',
					'scansEnabled_highSense',
					'scansEnabled_oldVersions',
					'scansEnabled_suspiciousAdminUsers',
					'scan_include_extra',
					'maxMem',
					'scan_exclude',
					'scan_maxIssues',
					'scan_maxDuration',
					'maxExecutionTime',
					'scanType',
					'manualScanType',
					'schedMode',
				);
				break;
			case self::OPTIONS_TYPE_TWO_FACTOR:
				$options = array(
					'loginSec_requireAdminTwoFactor',
					'loginSec_enableSeparateTwoFactor',
				);
				break;
			case self::OPTIONS_TYPE_LIVE_TRAFFIC:
				$options = array(
					'liveTrafficEnabled',
					'liveTraf_ignorePublishers',
					'liveTraf_displayExpandedRecords',
					'liveTraf_ignoreUsers',
					'liveTraf_ignoreIPs',
					'liveTraf_ignoreUA',
					'liveTraf_maxRows',
					'displayTopLevelLiveTraffic',
				);
				break;
			case self::OPTIONS_TYPE_COMMENT_SPAM:
				$options = array(
					'other_noAnonMemberComments',
					'other_scanComments',
					'advancedCommentScanning',
				);
				break;
			case self::OPTIONS_TYPE_DIAGNOSTICS:
				$options = array(
					'debugOn',
					'startScansRemotely',
					'ssl_verify',
					'betaThreatDefenseFeed',
				);
				break;
			case self::OPTIONS_TYPE_ALL:
				$options = array(
					'alertOn_critical',
					'alertOn_update',
					'alertOn_warnings',
					'alertOn_throttle',
					'alertOn_block',
					'alertOn_loginLockout',
					'alertOn_breachLogin',
					'alertOn_lostPasswdForm',
					'alertOn_adminLogin',
					'alertOn_firstAdminLoginOnly',
					'alertOn_nonAdminLogin',
					'alertOn_firstNonAdminLoginOnly',
					'alertOn_wordfenceDeactivated',
					'liveActivityPauseEnabled',
					'notification_updatesNeeded',
					'notification_securityAlerts',
					'notification_promotions',
					'notification_blogHighlights',
					'notification_productUpdates',
					'notification_scanStatus',
					'other_hideWPVersion',
					'other_bypassLitespeedNoabort',
					'deleteTablesOnDeact',
					'autoUpdate',
					'disableCookies',
					'disableCodeExecutionUploads',
					'email_summary_enabled',
					'email_summary_dashboard_widget_enabled',
					'howGetIPs',
					'actUpdateInterval',
					'alert_maxHourly',
					'email_summary_interval',
					'email_summary_excluded_directories',
					'howGetIPs_trusted_proxies',
					'firewallEnabled',
					'blockFakeBots',
					'autoBlockScanners',
					'loginSecurityEnabled',
					'loginSec_strongPasswds_enabled',
					'loginSec_breachPasswds_enabled',
					'loginSec_lockInvalidUsers',
					'loginSec_maskLoginErrors',
					'loginSec_blockAdminReg',
					'loginSec_disableAuthorScan',
					'loginSec_disableOEmbedAuthor',
					'other_blockBadPOST',
					'other_pwStrengthOnUpdate',
					'other_WFNet',
					'ajaxWatcherDisabled_front',
					'ajaxWatcherDisabled_admin',
					'wafAlertOnAttacks',
					'disableWAFIPBlocking',
					'whitelisted',
					'bannedURLs',
					'loginSec_userBlacklist',
					'neverBlockBG',
					'loginSec_countFailMins',
					'loginSec_lockoutMins',
					'loginSec_strongPasswds',
					'loginSec_breachPasswds',
					'loginSec_maxFailures',
					'loginSec_maxForgotPasswd',
					'maxGlobalRequests',
					'maxGlobalRequests_action',
					'maxRequestsCrawlers',
					'maxRequestsCrawlers_action',
					'maxRequestsHumans',
					'maxRequestsHumans_action',
					'max404Crawlers',
					'max404Crawlers_action',
					'max404Humans',
					'max404Humans_action',
					'maxScanHits',
					'maxScanHits_action',
					'blockedTime',
					'allowed404s',
					'wafAlertWhitelist',
					'wafAlertInterval',
					'wafAlertThreshold',
					'dismissAutoPrependNotice',
					'displayTopLevelBlocking',
					'cbl_loggedInBlocked',
					'cbl_action',
					'cbl_redirURL',
					'cbl_bypassRedirURL',
					'cbl_bypassRedirDest',
					'cbl_bypassViewURL',
					'checkSpamIP',
					'spamvertizeCheck',
					'scheduledScansEnabled',
					'lowResourceScansEnabled',
					'scansEnabled_checkGSB',
					'scansEnabled_checkHowGetIPs',
					'scansEnabled_core',
					'scansEnabled_themes',
					'scansEnabled_plugins',
					'scansEnabled_coreUnknown',
					'scansEnabled_malware',
					'scansEnabled_fileContents',
					'scansEnabled_fileContentsGSB',
					'scansEnabled_checkReadableConfig',
					'scansEnabled_suspectedFiles',
					'scansEnabled_posts',
					'scansEnabled_comments',
					'scansEnabled_suspiciousOptions',
					'scansEnabled_passwds',
					'scansEnabled_diskSpace',
					'scansEnabled_options',
					'scansEnabled_wpscan_fullPathDisclosure',
					'scansEnabled_wpscan_directoryListingEnabled',
					'scansEnabled_dns',
					'scansEnabled_scanImages',
					'scansEnabled_highSense',
					'scansEnabled_oldVersions',
					'scansEnabled_suspiciousAdminUsers',
					'scan_include_extra',
					'maxMem',
					'scan_exclude',
					'scan_maxIssues',
					'scan_maxDuration',
					'maxExecutionTime',
					'scanType',
					'manualScanType',
					'schedMode',
					'loginSec_requireAdminTwoFactor',
					'loginSec_enableSeparateTwoFactor',
					'liveTrafficEnabled',
					'liveTraf_ignorePublishers',
					'liveTraf_displayExpandedRecords',
					'liveTraf_ignoreUsers',
					'liveTraf_ignoreIPs',
					'liveTraf_ignoreUA',
					'liveTraf_maxRows',
					'displayTopLevelLiveTraffic',
					'other_noAnonMemberComments',
					'other_scanComments',
					'advancedCommentScanning',
				);
				break;
		}
		
		if (isset($options)) {
			$changes = array();
			foreach ($options as $key) {
				if (isset(self::$defaultConfig['checkboxes'][$key])) {
					$changes[$key] = self::$defaultConfig['checkboxes'][$key]['value'];
				}
				else if (isset(self::$defaultConfig['otherParams'][$key])) {
					$changes[$key] = self::$defaultConfig['otherParams'][$key]['value'];
				}
				else if (isset(self::$defaultConfig['defaultsOnly'][$key])) {
					$changes[$key] = self::$defaultConfig['defaultsOnly'][$key]['value'];
				}
			}
			
			try {
				self::save($changes);
				return true;
			}
			catch (Exception $e) {
				//Do nothing
			}
		}
		
		return false;
	}
}

class wfConfigException extends Exception {}
