<?php

class wfScanner {
	const SCAN_TYPE_QUICK = 'quick';
	const SCAN_TYPE_LIMITED = 'limited';
	const SCAN_TYPE_STANDARD = 'standard';
	const SCAN_TYPE_HIGH_SENSITIVITY = 'highsensitivity';
	const SCAN_TYPE_CUSTOM = 'custom';
	
	const SCAN_SCHEDULING_MODE_AUTOMATIC = 'auto';
	const SCAN_SCHEDULING_MODE_MANUAL = 'manual';
	
	const MANUAL_SCHEDULING_ONCE_DAILY = 'onceDaily';
	const MANUAL_SCHEDULING_TWICE_DAILY = 'twiceDaily';
	const MANUAL_SCHEDULING_EVERY_OTHER_DAY = 'everyOtherDay';
	const MANUAL_SCHEDULING_WEEKDAYS = 'weekdays';
	const MANUAL_SCHEDULING_WEEKENDS = 'weekends';
	const MANUAL_SCHEDULING_ODD_DAYS_WEEKENDS = 'oddDaysWE';
	const MANUAL_SCHEDULING_CUSTOM = 'custom';
	
	const SIGNATURE_MODE_PREMIUM = 'premium';
	const SIGNATURE_MODE_COMMUNITY = 'community';
	
	const STATUS_PENDING = 'pending';
	const STATUS_RUNNING = 'running';
	const STATUS_RUNNING_WARNING = 'running-warning';
	const STATUS_COMPLETE_SUCCESS = 'complete-success';
	const STATUS_COMPLETE_WARNING = 'complete-warning';
	const STATUS_PREMIUM = 'premium';
	const STATUS_DISABLED = 'disabled';

	const STAGE_SPAMVERTISING_CHECKS = 'spamvertising';
	const STAGE_SPAM_CHECK = 'spam';
	const STAGE_BLACKLIST_CHECK = 'blacklist';
	const STAGE_SERVER_STATE = 'server';
	const STAGE_FILE_CHANGES = 'changes';
	const STAGE_PUBLIC_FILES = 'public';
	const STAGE_MALWARE_SCAN = 'malware';
	const STAGE_CONTENT_SAFETY = 'content';
	const STAGE_PASSWORD_STRENGTH = 'password';
	const STAGE_VULNERABILITY_SCAN = 'vulnerability';
	const STAGE_OPTIONS_AUDIT = 'options';
	
	const SUMMARY_TOTAL_USERS = 'totalUsers';
	const SUMMARY_TOTAL_PAGES = 'totalPages';
	const SUMMARY_TOTAL_POSTS = 'totalPosts';
	const SUMMARY_TOTAL_COMMENTS = 'totalComments';
	const SUMMARY_TOTAL_CATEGORIES = 'totalCategories';
	const SUMMARY_TOTAL_TABLES = 'totalTables';
	const SUMMARY_TOTAL_ROWS = 'totalRows';
	const SUMMARY_SCANNED_POSTS = 'scannedPosts';
	const SUMMARY_SCANNED_COMMENTS = 'scannedComments';
	const SUMMARY_SCANNED_FILES = 'scannedFiles';
	const SUMMARY_SCANNED_PLUGINS = 'scannedPlugins';
	const SUMMARY_SCANNED_THEMES = 'scannedThemes';
	const SUMMARY_SCANNED_USERS = 'scannedUsers';
	const SUMMARY_SCANNED_URLS = 'scannedURLs';
	
	private $_scanType = false;
	
	private $_summary = false;
	private $_destructRegistered = false;
	private $_dirty = false;
	
	/**
	 * Returns the singleton wfScanner with the user-configured scan type set.
	 * 
	 * @return wfScanner
	 */
	public static function shared() {
		static $_scanner = null;
		if ($_scanner === null) {
			$_scanner = new wfScanner();
		}
		return $_scanner;
	}
	
	/**
	 * Schedules a cron rescheduling to happen at the end of the current process's execution.
	 */
	public static function setNeedsRescheduling() {
		static $willReschedule = false;
		if (!$willReschedule) {
			$willReschedule = true;
			register_shutdown_function(array(self::shared(), 'scheduleScans'));
		}
	}
	
	/**
	 * Returns whether or not the scan type passed is valid.
	 * 
	 * @param $type
	 * @return bool
	 */
	public static function isValidScanType($type) {
		switch ($type) {
			case self::SCAN_TYPE_QUICK:
			case self::SCAN_TYPE_LIMITED:
			case self::SCAN_TYPE_HIGH_SENSITIVITY:
			case self::SCAN_TYPE_CUSTOM:
			case self::SCAN_TYPE_STANDARD:
				return true;
		}
		return false;
	}
	
	/**
	 * Returns the display string for the given type.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function displayScanType($type) {
		switch ($type) {
			case self::SCAN_TYPE_QUICK:
				return __('Quick', 'wordfence');
			case self::SCAN_TYPE_LIMITED:
				return __('Limited', 'wordfence');
			case self::SCAN_TYPE_HIGH_SENSITIVITY:
				return __('High Sensitivity', 'wordfence');
			case self::SCAN_TYPE_CUSTOM:
				return __('Custom', 'wordfence');
			case self::SCAN_TYPE_STANDARD:
			default:
				return __('Standard', 'wordfence');
		}
	}
	
	/**
	 * Returns the display detail string for the given type.
	 *
	 * @param string $type
	 * @return string
	 */
	public static function displayScanTypeDetail($type) {
		switch ($type) {
			case self::SCAN_TYPE_QUICK:
			case self::SCAN_TYPE_LIMITED:
				return __('Low resource utilization, limited detection capability', 'wordfence');
			case self::SCAN_TYPE_HIGH_SENSITIVITY:
				return __('Standard detection capability, chance of false positives', 'wordfence');
			case self::SCAN_TYPE_CUSTOM:
				return __('Custom scan options selected', 'wordfence');
			case self::SCAN_TYPE_STANDARD:
			default:
				return __('Standard detection capability', 'wordfence');
		}
	}
	
	/**
	 * Returns an array of the scan options (as keys) and the corresponding value for the quick scan type. All omitted
	 * scan stages are considered disabled.
	 *
	 * @return array
	 */
	public static function quickScanTypeOptions() {
		$oldVersions = true;
		if (wfConfig::get('scanType') == self::SCAN_TYPE_CUSTOM) { //Obey the setting in custom if that's the true scan type
			$oldVersions = wfConfig::get('scansEnabled_oldVersions');
		}
		
		return array_merge(self::_inactiveScanOptions(), array(
			'scansEnabled_oldVersions' => $oldVersions,
		));
	}
	
	/**
	 * Returns an array of the scan options (as keys) and the corresponding value for the limited scan type.
	 * 
	 * @return array
	 */
	public static function limitedScanTypeOptions() {
		return array_merge(self::_inactiveScanOptions(), array(
			'scansEnabled_checkHowGetIPs' => true,
			'scansEnabled_malware' => true,
			'scansEnabled_fileContents' => true,
			'scansEnabled_fileContentsGSB' => true,
			'scansEnabled_suspiciousOptions' => true,
			'scansEnabled_oldVersions' => true,
			'lowResourceScansEnabled' => true,
			'scan_exclude' => wfConfig::get('scan_exclude', ''),
			'scan_include_extra' => wfConfig::get('scan_include_extra', ''),
			'scansEnabled_geoipSupport' => true,
		));
	}
	
	/**
	 * Returns an array of the scan options (as keys) and the corresponding value for the standard scan type.
	 *
	 * @return array
	 */
	public static function standardScanTypeOptions() {
		return array_merge(self::_inactiveScanOptions(), array(
			'spamvertizeCheck' => true,
			'checkSpamIP' => true,
			'scansEnabled_checkGSB' => true,
			'scansEnabled_checkHowGetIPs' => true,
			'scansEnabled_checkReadableConfig' => true,
			'scansEnabled_suspectedFiles' => true,
			'scansEnabled_core' => true,
			'scansEnabled_coreUnknown' => true,
			'scansEnabled_malware' => true,
			'scansEnabled_fileContents' => true,
			'scansEnabled_fileContentsGSB' => true,
			'scansEnabled_posts' => true,
			'scansEnabled_comments' => true,
			'scansEnabled_suspiciousOptions' => true,
			'scansEnabled_oldVersions' => true,
			'scansEnabled_suspiciousAdminUsers' => true,
			'scansEnabled_passwds' => true,
			'scansEnabled_diskSpace' => true,
			'scansEnabled_dns' => true,
			'scan_exclude' => wfConfig::get('scan_exclude', ''),
			'scan_include_extra' => wfConfig::get('scan_include_extra', ''),
			'scansEnabled_geoipSupport' => true,
		));
	}
	
	/**
	 * Returns an array of the scan options (as keys) and the corresponding value for the high sensitivity scan type.
	 *
	 * @return array
	 */
	public static function highSensitivityScanTypeOptions() {
		return array_merge(self::_inactiveScanOptions(), array(
			'spamvertizeCheck' => true,
			'checkSpamIP' => true,
			'scansEnabled_checkGSB' => true,
			'scansEnabled_checkHowGetIPs' => true,
			'scansEnabled_checkReadableConfig' => true,
			'scansEnabled_suspectedFiles' => true,
			'scansEnabled_core' => true,
			'scansEnabled_themes' => true,
			'scansEnabled_plugins' => true,
			'scansEnabled_coreUnknown' => true,
			'scansEnabled_malware' => true,
			'scansEnabled_fileContents' => true,
			'scansEnabled_fileContentsGSB' => true,
			'scansEnabled_posts' => true,
			'scansEnabled_comments' => true,
			'scansEnabled_suspiciousOptions' => true,
			'scansEnabled_oldVersions' => true,
			'scansEnabled_suspiciousAdminUsers' => true,
			'scansEnabled_passwds' => true,
			'scansEnabled_diskSpace' => true,
			'scansEnabled_dns' => true,
			'other_scanOutside' => true,
			'scansEnabled_scanImages' => true,
			'scansEnabled_highSense' => true,
			'scan_exclude' => wfConfig::get('scan_exclude', ''),
			'scan_include_extra' => wfConfig::get('scan_include_extra', ''),
			'scansEnabled_geoipSupport' => true,
		));
	}
	
	/**
	 * Returns an array of the scan options (as keys) and the corresponding value for the custom scan type.
	 *
	 * @return array
	 */
	public static function customScanTypeOptions() {
		$allOptions = self::_inactiveScanOptions();
		foreach ($allOptions as $key => &$value) {
			$value = wfConfig::get($key);
		}
		
		$allOptions['scansEnabled_geoipSupport'] = true;
		
		return $allOptions;
	}
	
	/**
	 * Returns an array of scan options and their inactive values for convenience in merging with the various scan type
	 * option arrays.
	 * 
	 * @return array
	 */
	protected static function _inactiveScanOptions() {
		return array(
			'spamvertizeCheck' => false,
			'checkSpamIP' => false,
			'scansEnabled_checkGSB' => false,
			'scansEnabled_checkHowGetIPs' => false,
			'scansEnabled_checkReadableConfig' => false,
			'scansEnabled_suspectedFiles' => false,
			'scansEnabled_core' => false,
			'scansEnabled_themes' => false,
			'scansEnabled_plugins' => false,
			'scansEnabled_coreUnknown' => false,
			'scansEnabled_malware' => false,
			'scansEnabled_fileContents' => false,
			'scan_include_extra' => '',
			'scansEnabled_fileContentsGSB' => false,
			'scansEnabled_posts' => false,
			'scansEnabled_comments' => false,
			'scansEnabled_suspiciousOptions' => false,
			'scansEnabled_oldVersions' => false,
			'scansEnabled_suspiciousAdminUsers' => false,
			'scansEnabled_passwds' => false,
			'scansEnabled_diskSpace' => false,
			'scansEnabled_dns' => false,
			'other_scanOutside' => false,
			'scansEnabled_scanImages' => false,
			'scansEnabled_highSense' => false,
			'lowResourceScansEnabled' => false,
			'scan_exclude' => '',
			'scansEnabled_geoipSupport' => false,
		);
	}
	
	/**
	 * Returns the scan options only available to premium users.
	 *
	 * @return array
	 */
	protected static function _premiumScanOptions() {
		return array('spamvertizeCheck', 'checkSpamIP', 'scansEnabled_checkGSB');
	}
	
	/**
	 * Returns an array of weights for calculating the scan option status score.
	 * 
	 * @return array
	 */
	protected static function _scanOptionWeights() {
		return array(
			'spamvertizeCheck' => 0.05,
			'checkSpamIP' => 0.05,
			'scansEnabled_checkGSB' => 0.05,
			'scansEnabled_checkHowGetIPs' => 0.05,
			'scansEnabled_checkReadableConfig' => 0.05,
			'scansEnabled_suspectedFiles' => 0.05,
			'scansEnabled_core' => 0.05,
			'scansEnabled_themes' => 0,
			'scansEnabled_plugins' => 0,
			'scansEnabled_coreUnknown' => 0.05,
			'scansEnabled_malware' => 0.05,
			'scansEnabled_fileContents' => 0.05,
			'scan_include_extra' => 0,
			'scansEnabled_fileContentsGSB' => 0.05,
			'scansEnabled_posts' => 0.05,
			'scansEnabled_comments' => 0.05,
			'scansEnabled_suspiciousOptions' => 0.05,
			'scansEnabled_oldVersions' => 0.1,
			'scansEnabled_suspiciousAdminUsers' => 0.05,
			'scansEnabled_passwds' => 0.05,
			'scansEnabled_diskSpace' => 0.05,
			'scansEnabled_dns' => 0.05,
			'other_scanOutside' => 0,
			'scansEnabled_scanImages' => 0,
			'scansEnabled_highSense' => 0,
			'lowResourceScansEnabled' => 0,
			'scan_exclude' => 0,
			'scansEnabled_geoipSupport' => 0,
		);
	}
	
	/**
	 * wfScanner constructor.
	 * @param int|bool $scanType If false, defaults to the config option `scanType`.
	 */
	public function __construct($scanType = false) {
		if ($scanType === false || !self::isValidScanType($scanType)) {
			$this->_scanType = wfConfig::get('scanType');
		}
		else {
			$this->_scanType = $scanType;
		}
	}
	
	/**
	 * Returns whether or not the scanner will run as premium.
	 * 
	 * @return bool
	 */
	public function isPremiumScan() {
		return !!wfConfig::get('isPaid');
	}
	
	/**
	 * Returns whether or not automatic scans will run.
	 * 
	 * @return bool
	 */
	public function isEnabled() {
		return !!wfConfig::get('scheduledScansEnabled');
	}
	
	/**
	 * Returns whether or not a scan is running. A scan is considered running if the timestamp
	 * under wf_scanRunning is within WORDFENCE_MAX_SCAN_LOCK_TIME seconds of now.
	 * 
	 * @return bool
	 */
	public function isRunning() {
		$scanRunning = wfConfig::get('wf_scanRunning');
		return ($scanRunning && time() - $scanRunning < WORDFENCE_MAX_SCAN_LOCK_TIME);
	}
	
	/**
	 * Returns the current scan scheduling mode.
	 * 
	 * @return string One of the SCAN_SCHEDULING_MODE_ constants
	 */
	public function schedulingMode() {
		if (wfConfig::get('isPaid') && wfConfig::get('schedMode') == 'manual') {
			return self::SCAN_SCHEDULING_MODE_MANUAL;
		}
		return self::SCAN_SCHEDULING_MODE_AUTOMATIC;
	}
	
	/**
	 * Returns the manual scheduling type. This is only applicable when the scheduling mode is
	 * SCAN_SCHEDULING_MODE_MANUAL.
	 * 
	 * @return string One of the MANUAL_SCHEDULING_ constants.
	 */
	public function manualSchedulingType() {
		return wfConfig::get('manualScanType', self::MANUAL_SCHEDULING_ONCE_DAILY);
	}
	
	/**
	 * Returns the start hour used for non-custom manual schedules. This is initially random but may be modified
	 * by the user later.
	 *
	 * @return int An hour number.
	 */
	public function manualSchedulingStartHour() {
		return wfConfig::get('schedStartHour');
	}
	
	/**
	 * Returns the currently defined custom schedule. This is only applicable when the scheduling mode is 
	 * SCAN_SCHEDULING_MODE_MANUAL and the manual type is set to MANUAL_SCHEDULING_CUSTOM.
	 * 
	 * @return array The array will be of the format array(0 => array(0 => 0, 1 => 0 ... 23 => 0), ... 6 => array(...))
	 */
	public function customSchedule() {
		$normalizedSchedule = array_fill(0, 7, array_fill(0, 24, 0));
		$storedSchedule = wfConfig::get_ser('scanSched', array());
		if (is_array($storedSchedule) && !empty($storedSchedule) && is_array($storedSchedule[0])) {
			foreach ($storedSchedule as $dayNumber => $day) {
				foreach ($day as $hourNumber => $enabled) {
					$normalizedSchedule[$dayNumber][$hourNumber] = wfUtils::truthyToInt($enabled);
				}
			}
		}
		return $normalizedSchedule;
	}
	
	/**
	 * Returns an associative array containing the current state each scan stage and its corresponding status.
	 * 
	 * @return array
	 */
	public function stageStatus() {
		$status = $this->_defaultStageStatuses();
		$runningStatus = wfConfig::get_ser('scanStageStatuses', array(), false);
		$status = array_merge($status, $runningStatus);
		
		foreach ($status as $stage => &$value) { //Convert value array into status only
			$value = $value['status'];
			if (!$this->isRunning() && $value == self::STATUS_RUNNING) {
				$value = self::STATUS_PENDING;
			}
		}
		
		return $status;
	}
	
	/**
	 * Returns an array of all scan options for the given stage that are enabled.
	 * 
	 * @param string $stage One of the STAGE_ constants
	 * @return array
	 */
	private function _scanJobsForStage($stage) {
		$options = array();
		switch ($stage) {
			case self::STAGE_SPAMVERTISING_CHECKS:
				$options = array(
					'spamvertizeCheck',
				);
				break;
			case self::STAGE_SPAM_CHECK:
				$options = array(
					'checkSpamIP',
				);
				break;
			case self::STAGE_BLACKLIST_CHECK:
				$options = array(
					'scansEnabled_checkGSB',
				);
				break;
			case self::STAGE_SERVER_STATE:
				$options = array(
					'scansEnabled_checkHowGetIPs',
					'scansEnabled_diskSpace',
					'scansEnabled_dns',
					'scansEnabled_geoipSupport',
				);
				break;
			case self::STAGE_FILE_CHANGES:
				$options = array(
					'scansEnabled_core',
					'scansEnabled_themes',
					'scansEnabled_plugins',
					'scansEnabled_coreUnknown',
				);
				break;
			case self::STAGE_PUBLIC_FILES:
				$options = array(
					'scansEnabled_checkReadableConfig',
					'scansEnabled_suspectedFiles',
				);
				break;
			case self::STAGE_MALWARE_SCAN:
				$options = array(
					'scansEnabled_malware',
					'scansEnabled_fileContents',
				);
				break;
			case self::STAGE_CONTENT_SAFETY:
				$options = array(
					'scansEnabled_posts',
					'scansEnabled_comments',
					'scansEnabled_fileContentsGSB', 
				);
				break;
			case self::STAGE_PASSWORD_STRENGTH:
				$options = array(
					'scansEnabled_passwds',
				);
				break;
			case self::STAGE_VULNERABILITY_SCAN:
				$options = array(
					'scansEnabled_oldVersions',
				);
				break;
			case self::STAGE_OPTIONS_AUDIT:
				$options = array(
					'scansEnabled_suspiciousOptions',
					'scansEnabled_suspiciousAdminUsers',
				);
				break;
		}
		
		$enabledOptions = $this->scanOptions();
		$filteredOptions = array();
		foreach ($options as $o) {
			if (isset($enabledOptions[$o]) && $enabledOptions[$o]) {
				$filteredOptions[] = $o;
			}
		}
		
		return $filteredOptions;
	}
	
	/**
	 * Returns an associative array containing each scan stage's default state. The keys are the stage identifiers and the value
	 * is an array in the format 
	 * array(
	 * 		'started' => the number of tasks for this stage that have started (initially 0),
	 * 		'finished' => the number of tasks that have started and finished (initially 0),
	 * 		'expected' => the expected number of tasks to run for this stage (based on the scan type and options enabled)
	 * )
	 * 
	 * @return array
	 */
	private function _defaultStageStatuses() {
		$status = array(
			self::STAGE_SPAMVERTISING_CHECKS => array('status' => ($this->isPremiumScan() ? self::STATUS_PENDING : self::STATUS_PREMIUM), 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_SPAM_CHECK => array('status' => ($this->isPremiumScan() ? self::STATUS_PENDING : self::STATUS_PREMIUM), 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_BLACKLIST_CHECK => array('status' => ($this->isPremiumScan() ? self::STATUS_PENDING : self::STATUS_PREMIUM), 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_SERVER_STATE => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_FILE_CHANGES => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_PUBLIC_FILES => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_MALWARE_SCAN => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_CONTENT_SAFETY => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_PASSWORD_STRENGTH => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_VULNERABILITY_SCAN => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
			self::STAGE_OPTIONS_AUDIT => array('status' => self::STATUS_PENDING, 'started' => 0, 'finished' => 0, 'expected' => 0),
		);
		
		foreach ($status as $stage => &$parameters) {
			if ($parameters['status'] == self::STATUS_PREMIUM) {
				continue;
			}
			
			$options = $this->_scanJobsForStage($stage);
			if (count($options)) {
				$parameters['expected'] = count($options);
			}
			else {
				$parameters['status'] = self::STATUS_DISABLED;
			}
		}
		
		return $status;
	}
	
	/**
	 * Resets the state of the scan stage status record.
	 */
	public function resetStages() {
		if ($this->scanType() == self::SCAN_TYPE_QUICK) { //Suppress for quick scans
			return;
		}
		wfConfig::set_ser('scanStageStatuses', $this->_defaultStageStatuses(), false, wfConfig::DONT_AUTOLOAD);
	}
	
	/**
	 * Increments the stage started counter and marks it as running if not already in that state.
	 * 
	 * @param string $stageID One of the STAGE_ constants
	 */
	public function startStage($stageID) {
		if ($this->scanType() == self::SCAN_TYPE_QUICK) { //Suppress for quick scans
			return;
		}
		
		$runningStatus = wfConfig::get_ser('scanStageStatuses', array(), false);
		if ($runningStatus[$stageID]['status'] != self::STATUS_RUNNING_WARNING) {
			$runningStatus[$stageID]['status'] = self::STATUS_RUNNING;
		}
		
		$runningStatus[$stageID]['started'] += 1;
		wfConfig::set_ser('scanStageStatuses', $runningStatus, false, wfConfig::DONT_AUTOLOAD);
	}
	
	/**
	 * Increments the stage finished counter and updates the stage status according to whether it's fully finished or encountered a negative status.
	 * 
	 * @param string $stageID One of the STAGE_ constants.
	 * @param string $status One of the wfIssues::STATUS_ constants
	 */
	public function completeStage($stageID, $status) {
		if ($this->scanType() == self::SCAN_TYPE_QUICK) { //Suppress for quick scans
			return;
		}
		
		$runningStatus = wfConfig::get_ser('scanStageStatuses', array(), false);
		
		if ($runningStatus[$stageID]['status'] == self::STATUS_RUNNING && ($status == wfIssues::STATUS_PROBLEM || $status == wfIssues::STATUS_FAILED)) {
			$runningStatus[$stageID]['status'] = self::STATUS_RUNNING_WARNING;
		}
		
		$runningStatus[$stageID]['finished'] += 1;
		if ($runningStatus[$stageID]['finished'] >= $runningStatus[$stageID]['expected']) {
			if ($runningStatus[$stageID]['status'] == self::STATUS_RUNNING) {
				$runningStatus[$stageID]['status'] = self::STATUS_COMPLETE_SUCCESS;
			}
			else {
				$runningStatus[$stageID]['status'] = self::STATUS_COMPLETE_WARNING;
			}
		}
		
		wfConfig::set_ser('scanStageStatuses', $runningStatus, false, wfConfig::DONT_AUTOLOAD);
	}
	
	/**
	 * Returns the selected type of the scan.
	 * 
	 * @return string
	 */
	public function scanType() {
		switch ($this->_scanType) {
			case self::SCAN_TYPE_QUICK://SCAN_TYPE_QUICK is not user-selectable
			case self::SCAN_TYPE_LIMITED:
			case self::SCAN_TYPE_STANDARD:
			case self::SCAN_TYPE_HIGH_SENSITIVITY:
			case self::SCAN_TYPE_CUSTOM:
				return $this->_scanType;
		}
		return self::SCAN_TYPE_STANDARD;
	}
	
	/**
	 * Returns a normalized percentage (i.e., in the range [0, 1]) to the corresponding display percentage
	 * based on license type.
	 *
	 * @param float $percentage
	 * @return float
	 */
	protected function _normalizedPercentageToDisplay($percentage) {
		if ($this->isPremiumScan()) {
			return round($percentage, 2);
		}
		
		return round($percentage * 0.70, 2);
	}
	
	/**
	 * Returns a normalized percentage (i.e., in the range [0, 1]) for the scan type status indicator.
	 * 
	 * @return float
	 */
	public function scanTypeStatus() {
		$isFree = !wfConfig::get('isPaid');
		$weights = self::_scanOptionWeights();
		$options = $this->scanOptions();
		$score = 0.0;
		$premiumOptions = self::_premiumScanOptions();
		foreach ($options as $key => $value) {
			if ($isFree && array_search($key, $premiumOptions) !== false) {
				continue;
			}
			
			if ($value) {
				$score += $weights[$key];
			}
		}
		return $this->_normalizedPercentageToDisplay($score);
	}

	public function scanTypeStatusList() {
		$isFree = !wfConfig::get('isPaid');
		$weights = self::_scanOptionWeights();
		$options = $this->scanOptions();
		$disabledOptionCount = 0;
		$premiumDisabledOptionCount = 0;
		$percentage = 0.0;
		$premiumPercentage = 0.0;
		$premiumOptions = self::_premiumScanOptions();
		$statusList = array();
		foreach ($options as $key => $value) {
			if ($isFree && array_search($key, $premiumOptions) !== false) {
				$premiumPercentage += $weights[$key];
				$premiumDisabledOptionCount++;
				continue;
			}
			
			if (!$value && $weights[$key] > 0) {
				$percentage += $weights[$key];
				$disabledOptionCount++;
			}
		}
		
		$remainingPercentage = 1 - $this->scanTypeStatus();
		if ($isFree) {
			$remainingPercentage -= 0.30;
			$statusList[] = array(
				'percentage' => 0.30,
				'title'      => __('Enable Premium Scan Signatures.', 'wordfence'),
			);
		}
		
		if ($premiumPercentage > 0) {
			$subtraction = min($this->_normalizedPercentageToDisplay($premiumPercentage), $remainingPercentage);
			$remainingPercentage -= $subtraction;
			$statusList[] = array(
				'percentage' => $subtraction,
				'title'      => __('Enable Premium Reputation Checks.', 'wordfence'),
			);
		}

		if ($percentage > 0) {
			$subtraction = min($this->_normalizedPercentageToDisplay($percentage), $remainingPercentage);
			$statusList[] = array(
				'percentage' => $subtraction,
				'title' => sprintf(_nx('Enable %d scan option.', 'Enable %d scan options.', $disabledOptionCount,'wordfence'), number_format_i18n($disabledOptionCount)),
			);
		}

		return $statusList;
	}
	/**
	 * Returns the malware signature feed that is in use.
	 *
	 * @return string
	 */
	public function signatureMode() {
		if ($this->isPremiumScan()) {
			return self::SIGNATURE_MODE_PREMIUM;
		}
		return self::SIGNATURE_MODE_COMMUNITY;
	}
	
	/**
	 * Returns a normalized percentage (i.e., in the range [0, 1]) for the reputation status indicator.
	 * 
	 * @return float
	 */
	public function reputationStatus() {
		$score = 0.0;
		if ($this->isPremiumScan()) {
			$options = $this->scanOptions();
			if ($options['spamvertizeCheck']) { $score += 0.333; }
			if ($options['checkSpamIP']) { $score += 0.333; }
			if ($options['scansEnabled_checkGSB']) { $score += 0.333; }
		}
		return round($score, 2);
	}

	/**
	 * @return array
	 */
	public function reputationStatusList() {
		$statusList = array();
		$options = $this->scanOptions();

		$reputationChecks = array(
			'spamvertizeCheck' => __('Enable scan option to check if this website is being "Spamvertised".', 'wordfence'),
			'checkSpamIP' => __('Enable scan option to check if your website IP is generating spam.', 'wordfence'),
			'scansEnabled_checkGSB' => __('Enable scan option to check if your website is on a domain blacklist.', 'wordfence'),
		);

		foreach ($reputationChecks as $option => $optionLabel) {
			if (!$this->isPremiumScan() || !$options[$option]) {
				$statusList[] = array(
					'percentage' => round(1 / count($reputationChecks), 2),
					'title'      => $optionLabel,
				);
			}
		}
		return $statusList;
	}

	/**
	 * Returns the options for the configured scan type.
	 * 
	 * @return array
	 */
	public function scanOptions() {
		switch ($this->scanType()) {
			case self::SCAN_TYPE_QUICK:
				return self::quickScanTypeOptions();
			case self::SCAN_TYPE_LIMITED:
				return self::limitedScanTypeOptions();
			case self::SCAN_TYPE_STANDARD:
				return self::standardScanTypeOptions();
			case self::SCAN_TYPE_HIGH_SENSITIVITY:
				return self::highSensitivityScanTypeOptions();
			case self::SCAN_TYPE_CUSTOM:
				return self::customScanTypeOptions();
		}
	}
	
	/**
	 * Returns the array of jobs for the scan type.
	 * 
	 * @return array
	 */
	public function jobs() {
		$options = $this->scanOptions();
		$preferredOrder = array(
			'checkSpamvertized' => array('spamvertizeCheck'), 
			'checkSpamIP' => array('checkSpamIP'), 
			'checkGSB' => array('scansEnabled_checkGSB'),
			'checkHowGetIPs' => array('scansEnabled_checkHowGetIPs'),
			'dns' => array('scansEnabled_dns'),
			'diskSpace' => array('scansEnabled_diskSpace'),
			'geoipSupport' => array('scansEnabled_geoipSupport'),
			'knownFiles' => ($this->scanType() != self::SCAN_TYPE_QUICK), //Always runs except for quick, options are scansEnabled_core, scansEnabled_themes, scansEnabled_plugins, scansEnabled_coreUnknown, scansEnabled_malware
			'checkReadableConfig' => array('scansEnabled_checkReadableConfig'),
			'fileContents' => ($this->scanType() != self::SCAN_TYPE_QUICK), //Always runs except for quick, options are scansEnabled_fileContents and scansEnabled_fileContentsGSB
			'suspectedFiles' => array('scansEnabled_suspectedFiles'),
			'posts' => array('scansEnabled_posts'),
			'comments' => array('scansEnabled_comments'),
			'passwds' => array('scansEnabled_passwds'),
			'oldVersions' => array('scansEnabled_oldVersions'),
			'suspiciousAdminUsers' => array('scansEnabled_suspiciousAdminUsers'),
			'suspiciousOptions' => array('scansEnabled_suspiciousOptions'),
		);
		
		$jobs = array();
		foreach ($preferredOrder as $job => $enabler) {
			if ($enabler === true) {
				$jobs[] = $job;
			}
			else if (is_array($enabler)) {
				foreach ($enabler as $o) {
					if ($options[$o]) {
						$jobs[] = $job;
						break;
					}
				}
			}
		}
		return $jobs;
	}
	
	/**
	 * Returns whether or not the scanner should use its low resource mode.
	 * 
	 * @return bool
	 */
	public function useLowResourceScanning() {
		$options = $this->scanOptions();
		return $options['lowResourceScansEnabled'];
	}
	
	/**
	 * Returns the array of user-defined malware signatures for use by the scanner.
	 * 
	 * @return array
	 */
	public function userScanSignatures() {
		$options = $this->scanOptions();
		$value = $options['scan_include_extra'];
		$signatures = array();
		if (!empty($value)) {
			$regexs = explode("\n", $value);
			$id = 1000001;
			foreach ($regexs as $r) {
				$r = rtrim($r, "\r");
				if (preg_match('/' . $r . '/i', "") !== false) {
					$signatures[] = array($id++, time(), $r, __('User defined scan pattern', 'wordfence'));
				}
			}
		}
		return $signatures;
	}
	
	/**
	 * Returns whether or not the scanner should check files outside of the WordPress installation.
	 * 
	 * @return bool
	 */
	public function scanOutsideWordPress() {
		$options = $this->scanOptions();
		return $options['other_scanOutside'];
	}
	
	/**
	 * Returns the cleaned up array of user-excluded scan paths and patterns.
	 * 
	 * @return array
	 */
	public function userExclusions() {
		$options = $this->scanOptions();
		$value = $options['scan_exclude'];
		return explode("\n", wfUtils::cleanupOneEntryPerLine($value));
	}
	
	/**
	 * Fetches the scan summary items into the internal cache.
	 */
	private function _fetchSummaryItems() {
		if ($this->_summary !== false) { 
			return;
		}
		
		$this->_summary = wfConfig::get_ser('wf_summaryItems', array());
	}
	
	/**
	 * Writes the scan summary cache to permanent storage.
	 */
	private function _saveSummaryItems() {
		if ($this->_summary !== false && $this->_dirty) {
			$this->_summary['lastUpdate'] = time();
			wfConfig::set_ser('wf_summaryItems', $this->_summary);
		}
		$this->_dirty = false;
	}
	
	/**
	 * Saves the scan summary cache if it has been more than two seconds since the last update.
	 * 
	 * @return bool Whether or not it saved.
	 */
	private function _maybeSaveSummaryItems() {
		if ($this->_summary !== false && $this->_summary['lastUpdate'] < (time() - 2)) {
			$this->_saveSummaryItems();
			return true;
		}
		return false;
	}
	
	/**
	 * Populates the scan summary with the default counters.
	 */
	public function resetSummaryItems() {
		global $wpdb;
		
		$this->_summary = array();
		$this->_summary[self::SUMMARY_SCANNED_POSTS] = 0;
		$this->_summary[self::SUMMARY_SCANNED_COMMENTS] = 0;
		$this->_summary[self::SUMMARY_SCANNED_FILES] = 0;
		$this->_summary[self::SUMMARY_SCANNED_PLUGINS] = 0;
		$this->_summary[self::SUMMARY_SCANNED_THEMES] = 0;
		$this->_summary[self::SUMMARY_SCANNED_USERS] = 0;
		$this->_summary[self::SUMMARY_SCANNED_URLS] = 0;
		
		$this->_dirty = true;
		$this->_saveSummaryItems();
	}
	
	/**
	 * Forces a save of the scan summary cache.
	 */
	public function flushSummaryItems() {
		$this->_saveSummaryItems();
	}
	
	/**
	 * Returns the corresponding summary value for $key or $default if not found.
	 * 
	 * @param $key
	 * @param mixed $default The value returned if there is no value for $key.
	 * @return mixed
	 */
	public function getSummaryItem($key, $default = false) {
		$this->_fetchSummaryItems();
		if (isset($this->_summary[$key])) {
			return $this->_summary[$key];
		}
		return $default;
	}
	
	/**
	 * Sets the summary item $key as $value.
	 * 
	 * @param $key
	 * @param $value
	 */
	public function setSummaryItem($key, $value) {
		$this->_fetchSummaryItems();
		$this->_summary[$key] = $value;
		$this->_dirty = true;
		
		if (!$this->_maybeSaveSummaryItems() && !$this->_destructRegistered) {
			register_shutdown_function(array($this, 'flushSummaryItems'));
			$this->_destructRegistered = true;
		}
	}
	
	/**
	 * Atomically increments the summary item under $key by $value.
	 * 
	 * @param $key
	 * @param int $value
	 */
	public function incrementSummaryItem($key, $value = 1) {
		if ($value == 0) { return; }
		$this->_fetchSummaryItems();
		if (isset($this->_summary[$key])) {
			$this->_summary[$key] += $value;
			$this->_dirty = true;
			
			if (!$this->_maybeSaveSummaryItems() && !$this->_destructRegistered) {
				register_shutdown_function(array($this, 'flushSummaryItems'));
				$this->_destructRegistered = true;
			}
		}
	}
	
	/**
	 * Schedules a single scan for the given time. If $originalTime is provided, it will be associated with the cron.
	 * 
	 * @param $futureTime
	 * @param bool|int $originalTime
	 */
	public function scheduleSingleScan($futureTime, $originalTime = false) {
		if (is_main_site()) { // Removed ability to activate on network site in v5.3.12
			if ($originalTime === false) {
				$originalTime = $futureTime;
			}
			wp_schedule_single_event($futureTime, 'wordfence_start_scheduled_scan', array((int) $originalTime));
			
			//Saving our own copy of the schedule because the wp-cron functions all require the args list to act
			$allScansScheduled = wfConfig::get_ser('allScansScheduled', array());
			$allScansScheduled[] = array('timestamp' => $futureTime, 'args' => array((int) $originalTime));
			wfConfig::set_ser('allScansScheduled', $allScansScheduled);
		}
	}
	
	/**
	 * Clears all scheduled scan cron jobs and re-creates them.
	 */
	public function scheduleScans() {
		$this->unscheduleAllScans();
		if (!$this->isEnabled()) {
			return;
		}
		
		if ($this->schedulingMode() == wfScanner::SCAN_SCHEDULING_MODE_MANUAL) {
			//Generate a two-week schedule
			$manualType = $this->manualSchedulingType();
			$preferredHour = $this->manualSchedulingStartHour();
			switch ($manualType) {
				case self::MANUAL_SCHEDULING_ONCE_DAILY:
					$schedule = array_fill(0, 14, array_fill(0, 24, 0));
					foreach ($schedule as $dayNumber => &$day) {
						$day[$preferredHour] = 1;
					}
					break;
				case self::MANUAL_SCHEDULING_TWICE_DAILY:
					$schedule = array_fill(0, 14, array_fill(0, 24, 0));
					foreach ($schedule as $dayNumber => &$day) {
						$day[$preferredHour] = 1;
						$day[($preferredHour + 12) % 24] = 1;
					}
					break;
				case self::MANUAL_SCHEDULING_EVERY_OTHER_DAY:
					$baseDay = floor(time() / 86400);
					$schedule = array_fill(0, 14, array_fill(0, 24, 0));
					foreach ($schedule as $dayNumber => &$day) {
						if (($baseDay + $dayNumber) % 2) {
							$day[$preferredHour] = 1;
						}
					}
					break;
				case self::MANUAL_SCHEDULING_WEEKDAYS:
					$schedule = array_fill(0, 14, array_fill(0, 24, 0));
					foreach ($schedule as $dayNumber => &$day) {
						if ($dayNumber > 0 && $dayNumber < 6) {
							$day[$preferredHour] = 1;
						}
					}
					break;
				case self::MANUAL_SCHEDULING_WEEKENDS:
					$schedule = array_fill(0, 14, array_fill(0, 24, 0));
					foreach ($schedule as $dayNumber => &$day) {
						if ($dayNumber == 0 || $dayNumber == 6) {
							$day[$preferredHour] = 1;
						}
					}
					break;
				case self::MANUAL_SCHEDULING_ODD_DAYS_WEEKENDS:
					$schedule = array_fill(0, 14, array_fill(0, 24, 0));
					foreach ($schedule as $dayNumber => &$day) {
						if ($dayNumber == 0 || $dayNumber == 6 || ($dayNumber % 2)) {
							$day[$preferredHour] = 1;
						}
					}
					break;
				case self::MANUAL_SCHEDULING_CUSTOM:
					$oneWeekSchedule = $this->customSchedule();
					$schedule = array();
					foreach ($oneWeekSchedule as $day) { $schedule[] = $day; }
					foreach ($oneWeekSchedule as $day) { $schedule[] = $day; }
					break;
			}
			
			$now = time();
			$tzOffset = wfUtils::formatLocalTime('Z', $now);
			
			//Apply the time zone shift so the start times align to the server's time zone
			$shiftedSchedule = array_fill(0, 14, array());
			foreach ($schedule as $dayNumber => $day) {
				foreach ($day as $hourNumber => $enabled) {
					if ($enabled) {
						$effectiveHour = round(($hourNumber * 3600 - $tzOffset) / 3600, 2); //round() rather than floor() to account for fractional time zones
						$wrappedHour = ($effectiveHour + 24) % 24;
						if ($effectiveHour < 0) {
							if ($dayNumber > 0) {
								$shiftedSchedule[$dayNumber - 1][$wrappedHour] = 1;
							}
						}
						else if ($effectiveHour > 23) {
							if ($dayNumber < count($schedule) - 1) {
								$shiftedSchedule[$dayNumber + 1][$wrappedHour] = 1;
							}
						}
						else {
							$shiftedSchedule[$dayNumber][$effectiveHour] = 1;
						}
					}
				}
			}
			$schedule = $shiftedSchedule;
			
			//Trim out all but an 8-day period
			$currentDayOfWeekUTC = date('w', $now);
			$currentHourUTC = date('G', $now);
			$periodStart = floor($now / 86400) * 86400 - $currentDayOfWeekUTC * 86400;
			$schedule = array_slice($schedule, $currentDayOfWeekUTC, null, true);
			$schedule = array_slice($schedule, 0, 8, true);
			
			//Schedule them
			foreach ($schedule as $dayNumber => $day) {
				foreach ($day as $hourNumber => $enabled) {
					if ($enabled) {
						if ($dayNumber == $currentDayOfWeekUTC && $currentHourUTC > $hourNumber) { //It's today and we've already passed its hour, skip it
							continue;
						}
						else if ($dayNumber > 6 && ($dayNumber % 7) == $currentDayOfWeekUTC && $currentHourUTC <= $hourNumber) { //It's one week from today but beyond the current hour, skip it this cycle
							continue;
						}
						
						$scanTime = $periodStart + $dayNumber * 86400 + $hourNumber * 3600 + wfWAFUtils::random_int(0, 3600);
						wordfence::status(4, 'info', "Scheduled time for day {$dayNumber} hour {$hourNumber} is: " . wfUtils::formatLocalTime('l jS \of F Y h:i:s A P', $scanTime));
						$this->scheduleSingleScan($scanTime);
					}
				}
			}
		}
		else {
			$noc1ScanSchedule = wfConfig::get_ser('noc1ScanSchedule', array());
			foreach ($noc1ScanSchedule as $timestamp) {
				$timestamp = wfUtils::denormalizedTime($timestamp);
				if ($timestamp > time()) {
					$this->scheduleSingleScan($timestamp);
				}
			}
		}
	}
	
	public function unscheduleAllScans() {
		$allScansScheduled = wfConfig::get_ser('allScansScheduled', array());
		foreach ($allScansScheduled as $entry) {
			wp_unschedule_event($entry['timestamp'], 'wordfence_start_scheduled_scan', $entry['args']);
		}
		wp_clear_scheduled_hook('wordfence_start_scheduled_scan');
		wfConfig::set_ser('allScansScheduled', array());
	}
	
	public function lastScanTime() {
		return wfConfig::get('scanTime');
	}
	
	public function recordLastScanTime() {
		wfConfig::set('scanTime', microtime(true));
	}
}