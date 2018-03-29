<?php

class wfActivityReport {
	const BLOCK_TYPE_COMPLEX = 'complex';
	const BLOCK_TYPE_BRUTE_FORCE = 'bruteforce';
	const BLOCK_TYPE_BLACKLIST = 'blacklist';

	/**
	 * @var int
	 */
	private $limit = 10;

	/**
	 * @var wpdb
	 */
	private $db;

	/**
	 * @param int $limit
	 */
	public function __construct($limit = 10) {
		global $wpdb;
		$this->db = $wpdb;
		$this->limit = $limit;
	}

	/**
	 * Schedule the activity report cron job.
	 */
	public static function scheduleCronJob() {
		self::clearCronJobs();

		if (!wfConfig::get('email_summary_enabled', 1)) {
			return;
		}

		if (is_main_site()) {
			list(, $end_time) = wfActivityReport::getReportDateRange();
			wp_schedule_single_event($end_time, 'wordfence_email_activity_report');
		}
	}

	/**
	 * Remove the activity report cron job.
	 */
	public static function disableCronJob() {
		self::clearCronJobs();
	}

	public static function clearCronJobs() {
		wp_clear_scheduled_hook('wordfence_email_activity_report');
	}

	/**
	 * Send out the report and reschedule the next report's cron job.
	 */
	public static function executeCronJob() {
		if (!wfConfig::get('email_summary_enabled', 1)) {
			return;
		}
		
		$report = new self();
		$report->sendReportViaEmail(wfConfig::getAlertEmails());
		self::scheduleCronJob();
	}

	/**
	 * Output a compact version of the email for the WP dashboard.
	 */
	public static function outputDashboardWidget() {
		$report = new self(5);
		echo $report->toWidgetView();
	}

	/**
	 * @return array
	 */
	public static function getReportDateRange() {
		$interval = wfConfig::get('email_summary_interval', 'weekly');
		$offset = get_option('gmt_offset');
		return self::_getReportDateRange($interval, $offset);
	}

	/**
	 * Testable code.
	 *
	 * @param string $interval
	 * @param int    $offset
	 * @param null   $time
	 * @return array
	 */
	public static function _getReportDateRange($interval = 'weekly', $offset = 0, $time = null) {
		if ($time === null) {
			$time = time();
		}

		$day = (int) gmdate('w', $time);
		$month = (int) gmdate("n", $time);
		$day_of_month = (int) gmdate("j", $time);
		$year = (int) gmdate("Y", $time);

		$start_time = 0;
		$end_time = 0;

		switch ($interval) {
			// Send a report 4pm every day
			case 'daily':
				$start_time = gmmktime(16, 0, 0, $month, $day_of_month, $year) + (-$offset * 60 * 60);
				$end_time = $start_time + 86400;
				break;
			
			// Send a report 4pm every Monday
			case 'weekly':
				$start_time = gmmktime(16, 0, 0, $month, $day_of_month - $day + 1, $year) + (-$offset * 60 * 60);
				$end_time = $start_time + (86400 * 7);
				break;

			// Send a report at 4pm the first of every month
			case 'monthly':
				$start_time = gmmktime(16, 0, 0, $month, 1, $year) + (-$offset * 60 * 60);
				$end_time = gmmktime(16, 0, 0, $month + 1, 1, $year) + (-$offset * 60 * 60);
				break;
		}

		return array($start_time, $end_time);
	}

	/**
	 * @return int
	 */
	public static function getReportDateFrom() {
		$interval = wfConfig::get('email_summary_interval', 'weekly');
		return self::_getReportDateFrom($interval);
	}

	/**
	 * @param string $interval
	 * @param null   $time
	 * @return int
	 */
	public static function _getReportDateFrom($interval = 'weekly', $time = null) {
		if ($time === null) {
			$time = time();
		}

		// weekly
		$from = $time - (86400 * 7);
		switch ($interval) {
			case 'daily':
				$from = $time - 86400;
				break;

			// Send a report at 4pm the first of every month
			case 'monthly':
				$from = strtotime('-1 month', $time);
				break;
		}

		return $from;
	}

	/**
	 * @return array
	 */
	public function getFullReport() {
		$start_time = microtime(true);
		$remainder = 0;
		$recent_firewall_activity = $this->getRecentFirewallActivity($this->limit, $remainder);
		return array(
			'top_ips_blocked'          => $this->getTopIPsBlocked($this->limit),
			'top_countries_blocked'    => $this->getTopCountriesBlocked($this->limit),
			'top_failed_logins'        => $this->getTopFailedLogins($this->limit),
			'recent_firewall_activity' => $recent_firewall_activity,
			'omitted_firewall_activity'=> $remainder,
			'recently_modified_files'  => $this->getRecentFilesModified($this->limit),
			'updates_needed'           => $this->getUpdatesNeeded(),
			'microseconds'             => microtime(true) - $start_time,
		);
	}

	/**
	 * @return array
	 */
	public function getWidgetReport() {
		$start_time = microtime(true);
		return array(
			'top_ips_blocked'       => $this->getTopIPsBlocked($this->limit),
			'top_countries_blocked' => $this->getTopCountriesBlocked($this->limit),
			'top_failed_logins'     => $this->getTopFailedLogins($this->limit),
			'updates_needed'        => $this->getUpdatesNeeded(),
			'microseconds'          => microtime(true) - $start_time,
		);
	}
	
	public function getBlockedCount($maxAgeDays = null, $grouping = null) {
		$maxAgeDays = (int) $maxAgeDays;
		if ($maxAgeDays <= 0) {
			$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 7 day)) / 86400)';
			switch (wfConfig::get('email_summary_interval', 'weekly')) {
				case 'daily':
					$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 day)) / 86400)';
					break;
				case 'monthly':
					$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month)) / 86400)';
					break;
			}
		}
		else {
			$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval ' . $maxAgeDays . ' day)) / 86400)';
		}
		
		//Possible values for blockType: throttle, manual, brute, fakegoogle, badpost, country, advanced, blacklist, waf
		$groupingWHERE = '';
		switch ($grouping) {
			case self::BLOCK_TYPE_COMPLEX:
				$groupingWHERE = ' AND blockType IN ("fakegoogle", "badpost", "country", "advanced", "waf")';
				break;
			case self::BLOCK_TYPE_BRUTE_FORCE:
				$groupingWHERE = ' AND blockType IN ("throttle", "brute")';
				break;
			case self::BLOCK_TYPE_BLACKLIST:
				$groupingWHERE = ' AND blockType IN ("blacklist", "manual")';
				break;
		}
		
		$table_wfBlockedIPLog = wfDB::networkTable('wfBlockedIPLog');
		$count = $this->db->get_var(<<<SQL
SELECT SUM(blockCount) as blockCount
FROM {$table_wfBlockedIPLog}
WHERE unixday >= {$interval}{$groupingWHERE}
SQL
			);
		return $count;
	}

	/**
	 * @param int $limit
	 * @return mixed
	 */
	public function getTopIPsBlocked($limit = 10, $maxAgeDays = null) {
		$maxAgeDays = (int) $maxAgeDays;
		if ($maxAgeDays <= 0) {
			$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 7 day)) / 86400)';
			switch (wfConfig::get('email_summary_interval', 'weekly')) {
				case 'daily':
					$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 day)) / 86400)';
					break;
				case 'monthly':
					$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month)) / 86400)';
					break;
			}
		}
		else {
			$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval ' . $maxAgeDays . ' day)) / 86400)';
		}
		
		$table_wfBlockedIPLog = wfDB::networkTable('wfBlockedIPLog');
		$results = $this->db->get_results($this->db->prepare(<<<SQL
SELECT *,
SUM(blockCount) as blockCount
FROM {$table_wfBlockedIPLog}
WHERE unixday >= {$interval}
GROUP BY IP
ORDER BY blockCount DESC
LIMIT %d
SQL
			, $limit));
		if ($results) {
			foreach ($results as &$row) {
				$row->countryName = $this->getCountryNameByCode($row->countryCode);
			}
		}
		return $results;
	}

	/**
	 * @param int $limit
	 * @return array
	 */
	public function getTopCountriesBlocked($limit = 10, $maxAgeDays = null) {
		$maxAgeDays = (int) $maxAgeDays;
		if ($maxAgeDays <= 0) {
			$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 7 day)) / 86400)';
			switch (wfConfig::get('email_summary_interval', 'weekly')) {
				case 'daily':
					$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 day)) / 86400)';
					break;
				case 'monthly':
					$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month)) / 86400)';
					break;
			}
		}
		else {
			$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval ' . $maxAgeDays . ' day)) / 86400)';
		}
	  	
		$table_wfBlockedIPLog = wfDB::networkTable('wfBlockedIPLog');
		$results = $this->db->get_results($this->db->prepare(<<<SQL
SELECT *, COUNT(IP) as totalIPs, SUM(blockCount) as totalBlockCount
FROM {$table_wfBlockedIPLog}
WHERE unixday >= {$interval}
GROUP BY countryCode
ORDER BY totalBlockCount DESC
LIMIT %d
SQL
			, $limit));
		if ($results) {
			foreach ($results as &$row) {
				$row->countryName = $this->getCountryNameByCode($row->countryCode);
			}
		}
		return $results;
	}

	/**
	 * @param int $limit
	 * @return mixed
	 */
	public function getTopFailedLogins($limit = 10) {
		$interval = 'UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 7 day))';
		switch (wfConfig::get('email_summary_interval', 'weekly')) {
			case 'daily':
				$interval = 'UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 day))';
				break;
			case 'monthly':
				$interval = 'UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month))';
				break;
		}
	  
		$table_wfLogins = wfDB::networkTable('wfLogins');
		$failedLogins = $this->db->get_results($this->db->prepare(<<<SQL
SELECT wfl.*,
sum(wfl.fail) as fail_count
FROM {$table_wfLogins} wfl
WHERE wfl.fail = 1
AND wfl.ctime > $interval
GROUP BY wfl.username
ORDER BY fail_count DESC
LIMIT %d
SQL
			, $limit));
		
		foreach ($failedLogins as &$login) {
			$exists = $this->db->get_var($this->db->prepare(<<<SQL
SELECT !ISNULL(ID) FROM {$this->db->users} WHERE user_login = '%s' OR user_email = '%s'
SQL
			, $login->username, $login->username));
			$login->is_valid_user = $exists;
		}
		
		return $failedLogins;
	}

	/**
	 * Returns any updates needs or false if everything is up to date.
	 *
	 * @return array|bool
	 */
	public function getUpdatesNeeded($useCachedValued = true) {
		$update_check = new wfUpdateCheck();
		$needs_update = $update_check->checkAllUpdates($useCachedValued)
			->needsAnyUpdates();
		if ($needs_update) {
			return array(
				'core'    => $update_check->getCoreUpdateVersion(),
				'plugins' => $update_check->getPluginUpdates(),
				'themes'  => $update_check->getThemeUpdates(),
			);
		}
		return false;
	}
	
	/**
	 * Returns list of firewall activity up to $limit number of entries.
	 * 
	 * @param int $limit Max events to return in results
	 * @return array
	 */
	public function getRecentFirewallActivity($limit = 300, &$remainder) {
		$dateRange = wfActivityReport::getReportDateRange();
		$recent_firewall_activity = new wfRecentFirewallActivity(null, max(604800, $dateRange[1] - $dateRange[0]));
		$recent_firewall_activity->run();
		return $recent_firewall_activity->mostRecentActivity($limit, $remainder);
	}

	/**
	 * Returns list of files modified within given timeframe.
	 *
	 * @todo Add option to configure the regex used to filter files allowed in this list.
	 * @todo Add option to exclude directories (such as cache directories).
	 *
	 * @param string $directory Search for files within this directory
	 * @param int    $time_range One week
	 * @param int    $limit Max files to return in results
	 * @param int    $directory_limit Hard limit for number of files to search within a directory.
	 * @return array
	 */
	public function getRecentFilesModified($limit = 300, $directory = ABSPATH, $time_range = 604800, $directory_limit = 20000) {
		$recently_modified = new wfRecentlyModifiedFiles($directory);
		$recently_modified->run();
		return $recently_modified->mostRecentFiles($limit);
	}

	/**
	 * Remove entries older than a month in the IP log.
	 */
	public function rotateIPLog() {
		$table_wfBlockedIPLog = wfDB::networkTable('wfBlockedIPLog');
		$this->db->query(<<<SQL
DELETE FROM {$table_wfBlockedIPLog}
WHERE unixday < FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month)) / 86400)
SQL
		);
		
		$table_wfBlockedCommentLog = wfDB::networkTable('wfBlockedCommentLog');
		$this->db->query(<<<SQL
DELETE FROM {$table_wfBlockedCommentLog}
WHERE unixday < FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month)) / 86400)
SQL
		);
	}

	/**
	 * @param mixed $ip_address
	 * @param int|null $unixday
	 */
	public static function logBlockedIP($ip_address, $unixday = null, $type = null) {
		/** @var wpdb $wpdb */
		global $wpdb;
		
		//Possible values for $type: throttle, manual, brute, fakegoogle, badpost, country, advanced, blacklist, waf

		if (wfUtils::isValidIP($ip_address)) {
			$ip_bin = wfUtils::inet_pton($ip_address);
		} else {
			$ip_bin = $ip_address;
			$ip_address = wfUtils::inet_ntop($ip_bin);
		}
		
		$blocked_table = wfDB::networkTable('wfBlockedIPLog');

		$unixday_insert = 'FLOOR(UNIX_TIMESTAMP() / 86400)';
		if (is_int($unixday)) {
			$unixday_insert = absint($unixday);
		}
		
		if ($type === null) {
			$type = 'generic';
		}

		$country = wfUtils::IP2Country($ip_address);

		$wpdb->query($wpdb->prepare(<<<SQL
INSERT INTO $blocked_table (IP, countryCode, blockCount, unixday, blockType)
VALUES (%s, %s, 1, $unixday_insert, %s)
ON DUPLICATE KEY UPDATE blockCount = blockCount + 1
SQL
			, $ip_bin, $country, $type));
	}
	
	/**
	 * @param mixed $ip_address
	 * @param int|null $unixday
	 */
	public static function logBlockedComment($ip_address, $type, $unixday = null) {
		/** @var wpdb $wpdb */
		global $wpdb;
		
		//Possible values for $type: anon, gsb, reputation
		
		if (wfUtils::isValidIP($ip_address)) {
			$ip_bin = wfUtils::inet_pton($ip_address);
		}
		else {
			$ip_bin = $ip_address;
			$ip_address = wfUtils::inet_ntop($ip_bin);
		}
	  
	 	$blocked_table = wfDB::networkTable('wfBlockedCommentLog');
		
		$unixday_insert = 'FLOOR(UNIX_TIMESTAMP() / 86400)';
		if (is_int($unixday)) {
			$unixday_insert = absint($unixday);
		}
		
		$country = wfUtils::IP2Country($ip_address);
		
		$wpdb->query($wpdb->prepare(<<<SQL
INSERT INTO {$blocked_table} (IP, countryCode, blockCount, unixday, blockType)
VALUES (%s, %s, 1, {$unixday_insert}, %s)
ON DUPLICATE KEY UPDATE blockCount = blockCount + 1
SQL
			, $ip_bin, $country, $type));
	}

	/**
	 * @param $code
	 * @return string
	 */
	public function getCountryNameByCode($code) {
		static $wfBulkCountries;
		if (!isset($wfBulkCountries)) {
			include 'wfBulkCountries.php';
		}
		return array_key_exists($code, $wfBulkCountries) ? $wfBulkCountries[$code] : "";
	}

	/**
	 * @return wfActivityReportView
	 */
	public function toView() {
		return new wfActivityReportView('reports/activity-report', $this->getFullReport() + array(
				'limit' => $this->getLimit(),
			));
	}

	/**
	 * @return wfActivityReportView
	 */
	public function toWidgetView() {
		return new wfActivityReportView('reports/activity-report', $this->getWidgetReport() + array(
				'limit' => $this->getLimit(),
			));
	}

	/**
	 * @return wfActivityReportView
	 */
	public function toEmailView() {
		return new wfActivityReportView('reports/activity-report-email-inline', $this->getFullReport());
	}

	/**
	 * @param $email_addresses string|array
	 * @return bool
	 */
	public function sendReportViaEmail($email_addresses) {
		$shortSiteURL = preg_replace('/^https?:\/\//i', '', site_url());
		return wp_mail($email_addresses, 'Wordfence activity for ' . date_i18n(get_option('date_format')) . ' on ' . $shortSiteURL, $this->toEmailView()->__toString(), 'Content-Type: text/html');
	}

	/**
	 * @return string
	 * @throws wfViewNotFoundException
	 */
	public function render() {
		return $this->toView()
			->render();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->toView()
			->__toString();
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @param int $limit
	 */
	public function setLimit($limit) {
		$this->limit = $limit;
	}
}

class wfRecentFirewallActivity {
	private $activity = array();
	
	private $max_fetch = 2000;
	private $time_range = 604800;
	
	public function __construct($max_fetch = null, $time_range = null) {
		if ($max_fetch !== null) {
			$this->max_fetch = $max_fetch;
		}
		
		if ($time_range !== null) {
			$this->time_range = $time_range;
		}
	}
	
	public function run() {
		global $wpdb;
		
		$table_wfHits = wfDB::networkTable('wfHits');
		$results = $wpdb->get_results($wpdb->prepare(<<<SQL
SELECT attackLogTime, IP, URL, UA, actionDescription, actionData
FROM {$table_wfHits}
WHERE action = 'blocked:waf' AND attackLogTime > (UNIX_TIMESTAMP() - %d)
ORDER BY attackLogTime DESC
LIMIT %d
SQL
			, $this->time_range, $this->max_fetch));
		if ($results) {
			foreach ($results as &$row) {
				$actionData = json_decode($row->actionData, true);
				if (!is_array($actionData) || !isset($actionData['paramKey']) || !isset($actionData['paramValue'])) {
					continue;
				}
				
				if (isset($actionData['failedRules']) && $actionData['failedRules'] == 'blocked') {
					$row->longDescription = "Blocked because the IP is blacklisted";
				}
				else {
					$row->longDescription = "Blocked for " . $row->actionDescription;
				}
				
				$paramKey = base64_decode($actionData['paramKey']);
				$paramValue = base64_decode($actionData['paramValue']);
				if (strlen($paramValue) > 100) {
					$paramValue = substr($paramValue, 0, 100) . chr(2026);
				}
				
				if (preg_match('/([a-z0-9_]+\.[a-z0-9_]+)(?:\[(.+?)\](.*))?/i', $paramKey, $matches)) {
					switch ($matches[1]) {
						case 'request.queryString':
							$row->longDescription = "Blocked for " . $row->actionDescription . ' in query string: ' . $matches[2] . '=' . $paramValue;
							break;
						case 'request.body':
							$row->longDescription = "Blocked for " . $row->actionDescription . ' in POST body: ' . $matches[2] . '=' . $paramValue;
							break;
						case 'request.cookie':
							$row->longDescription = "Blocked for " . $row->actionDescription . ' in cookie: ' . $matches[2] . '=' . $paramValue;
							break;
						case 'request.fileNames':
							$row->longDescription = "Blocked for a " . $row->actionDescription . ' in file: ' . $matches[2] . '=' . $paramValue;
							break;
					}
				}
			}
		}
		
		$this->activity = $results;
	}
	
	public function mostRecentActivity($limit, &$remainder = null) {
		if ($remainder !== null) {
			$remainder = count($this->activity) - $limit;
		}
		return array_slice($this->activity, 0, $limit);
	}
}

class wfRecentlyModifiedFiles extends wfDirectoryIterator {

	/**
	 * @var int
	 */
	private $time_range = 604800;

	/**
	 * @var array
	 */
	private $files = array();
	private $excluded_directories;

	/**
	 * @param string $directory
	 * @param int    $max_files_per_directory
	 * @param int    $max_iterations
	 * @param int    $time_range
	 */
	public function __construct($directory = ABSPATH, $max_files_per_directory = 20000, $max_iterations = 250000, $time_range = 604800) {
		parent::__construct($directory, $max_files_per_directory, $max_iterations);
		$this->time_range = $time_range;
		$excluded_directories = explode("\n", wfUtils::cleanupOneEntryPerLine(wfConfig::get('email_summary_excluded_directories', '')));
		$this->excluded_directories = array();
		foreach ($excluded_directories  as $index => $path) {
			if (($dir = realpath(ABSPATH . $path)) !== false) {
				$this->excluded_directories[$dir] = 1;
			}
		}
	}

	/**
	 * @param $dir
	 * @return bool
	 */
	protected function scan($dir) {
		if (!array_key_exists(realpath($dir), $this->excluded_directories)) {
			return parent::scan($dir);
		}
		return true;
	}


	/**
	 * @param string $file
	 */
	public function file($file) {
		$mtime = filemtime($file);
		if (time() - $mtime < $this->time_range) {
			$this->files[] = array($file, $mtime);
		}
	}

	/**
	 * @param int $limit
	 * @return array
	 */
	public function mostRecentFiles($limit = 300) {
		usort($this->files, array(
			$this,
			'_sortMostRecentFiles',
		));
		return array_slice($this->files, 0, $limit);
	}

	/**
	 * Sort in descending order.
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	private function _sortMostRecentFiles($a, $b) {
		if ($a[1] > $b[1]) {
			return -1;
		}
		if ($a[1] < $b[1]) {
			return 1;
		}
		return 0;
	}

	/**
	 * @return mixed
	 */
	public function getFiles() {
		return $this->files;
	}
}


class wfActivityReportView extends wfView {

	/**
	 * @param $file
	 * @return string
	 */
	public function displayFile($file) {
		if (stripos($file, ABSPATH) === 0) {
			return substr($file, strlen(ABSPATH));
		}
		return $file;
	}

	/**
	 * @param null $unix_time
	 * @return string
	 */
	public function modTime($unix_time = null) {
		if ($unix_time === null) {
			$unix_time = time();
		}
		return wfUtils::formatLocalTime('F j, Y g:ia', $unix_time);
	}
	
	public function attackTime($unix_time = null) {
		if ($unix_time === null) {
			$unix_time = time();
		}
		return wfUtils::formatLocalTime('F j, Y', $unix_time) . "<br>" . wfUtils::formatLocalTime('g:ia', $unix_time);
	}
	
	public function displayIP($binaryIP) {
		$readableIP = wfUtils::inet_ntop($binaryIP);
		$country = wfUtils::countryCode2Name(wfUtils::IP2Country($readableIP));
		return "{$readableIP} (" . ($country ? $country : 'Unknown') . ")"; 
	}
}
