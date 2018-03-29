<?php
require_once('wfUtils.php');
class wfIssues {
	//Possible responses from `addIssue`
	const ISSUE_ADDED = 'a';
	const ISSUE_UPDATED = 'u';
	const ISSUE_DUPLICATE = 'd';
	const ISSUE_IGNOREP = 'ip';
	const ISSUE_IGNOREC = 'ic';
	
	//Possible status message states
	const STATUS_NONE = 'n'; //Default state before running
	
	const STATUS_SKIPPED = 's'; //The scan job was skipped because it didn't need to run
	const STATUS_IGNORED = 'i'; //The scan job found an issue, but it matched an entry in the ignore list
	
	const STATUS_PROBLEM = 'p'; //The scan job found an issue
	const STATUS_SECURE = 'r'; //The scan job found no issues
	
	const STATUS_FAILED = 'f'; //The scan job failed
	const STATUS_SUCCESS = 'c'; //The scan job succeeded
	
	const STATUS_PAIDONLY = 'x';
	
	private $db = false;

	//Properties that are serialized on sleep:
	private $updateCalled = false;
	private $issuesTable = '';
	private $pendingIssuesTable = '';
	private $maxIssues = 0;
	private $newIssues = array();
	public $totalIssues = 0;
	public $totalCriticalIssues = 0;
	public $totalWarningIssues = 0;
	public $totalIgnoredIssues = 0;
	
	public static function statusPrep(){
		wfConfig::set_ser('wfStatusStartMsgs', array());
		wordfence::status(10, 'info', "SUM_PREP:Preparing a new scan.");
		wfIssues::updateScanStillRunning();
	}
	
	public static function statusStart($message) {
		$statusStartMsgs = wfConfig::get_ser('wfStatusStartMsgs', array());
		$statusStartMsgs[] = $message;
		wfConfig::set_ser('wfStatusStartMsgs', $statusStartMsgs);
		wordfence::status(10, 'info', 'SUM_START:' . $message);
		wfIssues::updateScanStillRunning();
		return count($statusStartMsgs) - 1;
	}
	
	public static function statusEnd($index, $state) {
		$statusStartMsgs = wfConfig::get_ser('wfStatusStartMsgs', array());
		if ($state == self::STATUS_SKIPPED) {
			wordfence::status(10, 'info', 'SUM_ENDSKIPPED:' . $statusStartMsgs[$index]);
		}
		else if ($state == self::STATUS_IGNORED) {
			wordfence::status(10, 'info', 'SUM_ENDIGNORED:' . $statusStartMsgs[$index]);
		}
		else if ($state == self::STATUS_PROBLEM) {
			wordfence::status(10, 'info', 'SUM_ENDBAD:' . $statusStartMsgs[$index]);
		}
		else if ($state == self::STATUS_SECURE) {
			wordfence::status(10, 'info', 'SUM_ENDOK:' . $statusStartMsgs[$index]);
		}
		else if ($state == self::STATUS_FAILED) {
			wordfence::status(10, 'info', 'SUM_ENDFAILED:' . $statusStartMsgs[$index]);
		}
		else if ($state == self::STATUS_SUCCESS) {
			wordfence::status(10, 'info', 'SUM_ENDSUCCESS:' . $statusStartMsgs[$index]);
		}
		wfIssues::updateScanStillRunning();
		$statusStartMsgs[$index] = '';
		wfConfig::set_ser('wfStatusStartMsgs', $statusStartMsgs);
	}
	
	public static function statusEndErr() {
		$statusStartMsgs = wfConfig::get_ser('wfStatusStartMsgs', array());
		for ($i = 0; $i < count($statusStartMsgs); $i++) {
			if (empty($statusStartMsgs[$i]) === false) {
				wordfence::status(10, 'info', 'SUM_ENDERR:' . $statusStartMsgs[$i]);
				$statusStartMsgs[$i] = '';
			}
		}
		wfIssues::updateScanStillRunning();
	}
	
	public static function statusPaidOnly($message) {
		wordfence::status(10, 'info', "SUM_PAIDONLY:" . $message);
		wfIssues::updateScanStillRunning();
	}
	
	public static function statusDisabled($message) {
		wordfence::status(10, 'info', "SUM_DISABLED:" . $message);
		wfIssues::updateScanStillRunning();
	}
	
	public static function updateScanStillRunning($running = true) {
		$timestamp = time();
		if (!$running) {
			$timestamp = 0;
		}
		wfConfig::set('wf_scanLastStatusTime', $timestamp);
	}
	
	/**
	 * Returns false if the scan has not been detected as failing. If it has, it returns the timestamp of the last status update.
	 * 
	 * @return bool|int
	 */
	public static function hasScanFailed() {
		if (wfConfig::get('wf_scanLastStatusTime', 0) === 0) {
			return false;
		}
		
		$threshold = WORDFENCE_SCAN_FAILURE_THRESHOLD;
		return (time() > wfConfig::get('wf_scanLastStatusTime', 0) + $threshold) ? wfConfig::get('wf_scanLastStatusTime', 0) : false;
	}
	
	/**
	 * Returns the singleton wfIssues.
	 *
	 * @return wfIssues
	 */
	public static function shared() {
		static $_issues = null;
		if ($_issues === null) {
			$_issues = new wfIssues();
		}
		return $_issues;
	}
	
	public function __sleep(){ //Same order here as vars above
		return array('updateCalled', 'issuesTable', 'pendingIssuesTable', 'maxIssues', 'newIssues', 'totalIssues', 'totalCriticalIssues', 'totalWarningIssues', 'totalIgnoredIssues');
	}
	public function __construct(){
		$this->issuesTable = wfDB::networkTable('wfIssues');
		$this->pendingIssuesTable = wfDB::networkTable('wfPendingIssues');
		$this->maxIssues = wfConfig::get('scan_maxIssues', 0);
	}
	public function __wakeup(){
		$this->db = new wfDB();
	}
	
	public function addIssue($type, $severity,  $ignoreP, $ignoreC, $shortMsg, $longMsg, $templateData, $alreadyHashed = false) {
		return $this->_addIssue('issue', $type, $severity, $ignoreP, $ignoreC, $shortMsg, $longMsg, $templateData, $alreadyHashed);
	}
	public function addPendingIssue($type, $severity,  $ignoreP, $ignoreC, $shortMsg, $longMsg, $templateData) {
		return $this->_addIssue('pending', $type, $severity, $ignoreP, $ignoreC, $shortMsg, $longMsg, $templateData);
	}
	
	/**
	 * Create a new issue
	 *
	 * @param string	$group The issue type (e.g., issue or pending
	 * @param string	$type
	 * @param int		$severity
	 * @param string	$ignoreP	string to compare against for permanent ignores
	 * @param string	$ignoreC	string to compare against for ignoring until something changes
	 * @param string	$shortMsg
	 * @param string	$longMsg
	 * @param array		$templateData
	 * @param bool		$alreadyHashed If true, don't re-hash $ignoreP and $ignoreC
	 * @return string	One of the ISSUE_ constants
	 */
	private function _addIssue($group, $type, $severity, $ignoreP, $ignoreC, $shortMsg, $longMsg, $templateData, $alreadyHashed = false) {
		if ($group == 'pending') {
			$table = $this->pendingIssuesTable;
		}
		else {
			$table = $this->issuesTable;
		}
		
		if (!$alreadyHashed) {
			$ignoreP = md5($ignoreP);
			$ignoreC = md5($ignoreC);
		}
		
		$results = $this->getDB()->querySelect("SELECT id, status, ignoreP, ignoreC FROM {$table} WHERE (ignoreP = '%s' OR ignoreC = '%s')", $ignoreP, $ignoreC);
		foreach ($results as $row) {
			if ($row['status'] == 'new' && ($row['ignoreC'] == $ignoreC || $row['ignoreP'] == $ignoreP)) {
				if ($type != 'file' && $type != 'database') { //Filter out duplicate new issues except for infected files because we want to see all infections even if file contents are identical
					return self::ISSUE_DUPLICATE;
				}
			}
			
			if ($row['status'] == 'ignoreP' && $row['ignoreP'] == $ignoreP) { $this->totalIgnoredIssues++; return self::ISSUE_IGNOREP; } //Always ignore
			else if ($row['status'] == 'ignoreC' && $row['ignoreC'] == $ignoreC) { $this->totalIgnoredIssues++; return self::ISSUE_IGNOREC; } //Unchanged, ignore
			else if ($row['status'] == 'ignoreC') {
				$updateID = $row['id']; //Re-use the existing issue row
				break;
			}
		}
		
		if ($group != 'pending') {
			if ($severity == 1) {
				$this->totalCriticalIssues++;
			}
			else if ($severity == 2) {
				$this->totalWarningIssues++;
			}
			$this->totalIssues++;
			if (empty($this->maxIssues) || $this->totalIssues <= $this->maxIssues)
			{
				$this->newIssues[] = array(
					'type' => $type,
					'severity' => $severity,
					'ignoreP' => $ignoreP,
					'ignoreC' => $ignoreC,
					'shortMsg' => $shortMsg,
					'longMsg' => $longMsg,
					'tmplData' => $templateData
					);
			}
		}
		
		if (isset($updateID)) {
			$this->getDB()->queryWrite(
				"UPDATE {$table} SET lastUpdated = UNIX_TIMESTAMP(), status = '%s', type = '%s', severity = %d, ignoreP = '%s', ignoreC = '%s', shortMsg = '%s', longMsg = '%s', data = '%s' WHERE id = %d",
				'new',
				$type,
				$severity,
				$ignoreP,
				$ignoreC,
				$shortMsg,
				$longMsg,
				serialize($templateData),
				$updateID);
			return self::ISSUE_UPDATED;
		}
		
		$this->getDB()->queryWrite("INSERT INTO {$table} (time, lastUpdated, status, type, severity, ignoreP, ignoreC, shortMsg, longMsg, data) VALUES (UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s')",
			'new',
			$type,
			$severity,
			$ignoreP,
			$ignoreC,
			$shortMsg,
			$longMsg,
			serialize($templateData));

		return self::ISSUE_ADDED;
	}
	public function deleteIgnored(){
		$this->getDB()->queryWrite("delete from " . $this->issuesTable . " where status='ignoreP' or status='ignoreC'");
	}
	public function deleteNew($types = null) {
		if (!is_array($types)) {
			$this->getDB()->queryWrite("DELETE FROM {$this->issuesTable} WHERE status = 'new'");
		}
		else {
			$query = "DELETE FROM {$this->issuesTable} WHERE status = 'new' AND type IN (" . implode(',', array_fill(0, count($types), "'%s'")) . ")";
			array_unshift($types, $query);
			call_user_func_array(array($this->getDB(), 'queryWrite'), $types);
		}
	}
	public function ignoreAllNew(){
		$this->getDB()->queryWrite("update " . $this->issuesTable . " set status='ignoreC' where status='new'");
	}
	public function emailNewIssues($timeLimitReached = false){
		$level = wfConfig::getAlertLevel();
		$emails = wfConfig::getAlertEmails();
		$shortSiteURL = preg_replace('/^https?:\/\//i', '', site_url());
		$subject = "[Wordfence Alert] Problems found on $shortSiteURL";

		if(sizeof($emails) < 1){ return; }
		if($level < 1){ return; }
		if($level == 2 && $this->totalCriticalIssues < 1 && $this->totalWarningIssues < 1){ return; }
		if($level == 1 && $this->totalCriticalIssues < 1){ return; }
		$emailedIssues = wfConfig::get_ser('emailedIssuesList', array());
		if(! is_array($emailedIssues)){
			$emailedIssues = array();
		}
		$overflowCount = $this->totalIssues - count($this->newIssues);
		$finalIssues = array();
		foreach($this->newIssues as $newIssue){
			$alreadyEmailed = false;
			foreach($emailedIssues as $emailedIssue){
				if($newIssue['ignoreP'] == $emailedIssue['ignoreP'] || $newIssue['ignoreC'] == $emailedIssue['ignoreC']){
					$alreadyEmailed = true;
					break;
				}
			}
			if(! $alreadyEmailed){
				$finalIssues[] = $newIssue;
			}
			else {
				$overflowCount--;
			}
		}
		if(sizeof($finalIssues) < 1){ return; }
		
		$this->newIssues = array();
		$this->totalIssues = 0;

		$totalWarningIssues = 0;
		$totalCriticalIssues = 0;
		foreach($finalIssues as $i){
			$emailedIssues[] = array( 'ignoreC' => $i['ignoreC'], 'ignoreP' => $i['ignoreP'] );
			if($i['severity'] == 1){
				$totalCriticalIssues++;
			} else if($i['severity'] == 2){
				$totalWarningIssues++;
			}
		}
		wfConfig::set_ser('emailedIssuesList', $emailedIssues);
		if($level == 2 && $totalCriticalIssues < 1 && $totalWarningIssues < 1){ return; }
		if($level == 1 && $totalCriticalIssues < 1){ return; }
		$content = wfUtils::tmpl('email_newIssues.php', array(
			'isPaid' => wfConfig::get('isPaid'),
			'issues' => $finalIssues,
			'totalCriticalIssues' => $totalCriticalIssues,
			'totalWarningIssues' => $totalWarningIssues,
			'level' => $level,
			'issuesNotShown' => $overflowCount,
			'adminURL' => get_admin_url(),
			'timeLimitReached' => $timeLimitReached,
			));
		
		wp_mail(implode(',', $emails), $subject, $content, 'Content-type: text/html');
	}
	public function deleteIssue($id){ 
		$this->getDB()->queryWrite("delete from " . $this->issuesTable . " where id=%d", $id);
	}
	public function updateIssue($id, $status){ //ignoreC, ignoreP, delete or new
		if($status == 'delete'){
			$this->getDB()->queryWrite("delete from " . $this->issuesTable . " where id=%d", $id);
		} else if($status == 'ignoreC' || $status == 'ignoreP' || $status == 'new'){
			$this->getDB()->queryWrite("update " . $this->issuesTable . " set status='%s' where id=%d", $status, $id);
		}
	}
	public function getIssueByID($id){
		$rec = $this->getDB()->querySingleRec("select * from " . $this->issuesTable . " where id=%d", $id);
		$rec['data'] = unserialize($rec['data']);
		return $rec;
	}
	public function getIssueCounts() {
		global $wpdb;
		$counts = $wpdb->get_results('SELECT COUNT(*) AS c, status FROM ' . $this->issuesTable . ' WHERE status = "new" OR status = "ignoreP" OR status = "ignoreC" GROUP BY status', ARRAY_A);
		$result = array();
		foreach ($counts as $row) {
			$result[$row['status']] = $row['c']; 
		}
		return $result;
	}
	public function getIssues($offset = 0, $limit = 100, $ignoredOffset = 0, $ignoredLimit = 100) {
		/** @var wpdb $wpdb */
		global $wpdb;
		
		$siteCleaningTypes = array('file', 'checkGSB', 'checkSpamIP', 'commentBadURL', 'dnsChange', 'knownfile', 'optionBadURL', 'postBadTitle', 'postBadURL', 'spamvertizeCheck', 'suspiciousAdminUsers');
		$sortTagging = 'CASE';
		foreach ($siteCleaningTypes as $index => $t) {
			$sortTagging .= ' WHEN type = \'' . esc_sql($t) . '\' THEN ' . ((int) $index);
		}
		$sortTagging .= ' ELSE 999 END';
		
		$ret = array(
			'new' => array(),
			'ignored' => array()
			);
		$userIni = ini_get('user_ini.filename');
		$q1 = $this->getDB()->querySelect("SELECT *, {$sortTagging} AS sortTag FROM " . $this->issuesTable . " WHERE status = 'new' ORDER BY severity ASC, sortTag ASC, type ASC, time DESC LIMIT %d,%d", $offset, $limit);
		$q2 = $this->getDB()->querySelect("SELECT *, {$sortTagging} AS sortTag FROM " . $this->issuesTable . " WHERE status = 'ignoreP' OR status = 'ignoreC' ORDER BY severity ASC, sortTag ASC, type ASC, time DESC LIMIT %d,%d", $ignoredOffset, $ignoredLimit);
		$q = array_merge($q1, $q2);
		foreach($q as $i){
			$i['data'] = unserialize($i['data']);
			$i['timeAgo'] = wfUtils::makeTimeAgo(time() - $i['time']);
			$i['displayTime'] = wfUtils::formatLocalTime(get_option('date_format') . ' ' . get_option('time_format'), $i['time']);
			$i['longMsg'] = wp_kses($i['longMsg'], 'post');
			if($i['status'] == 'new'){
				$ret['new'][] = $i;
			} else if($i['status'] == 'ignoreP' || $i['status'] == 'ignoreC'){
				$ret['ignored'][] = $i;
			} else {
				error_log("Issue has bad status: " . $i['status']);
				continue;
			}
		}
		foreach($ret as $status => &$issueList){
			for($i = 0; $i < sizeof($issueList); $i++){
				if ($issueList[$i]['type'] == 'file' || $issueList[$i]['type'] == 'knownfile') {
					$localFile = $issueList[$i]['data']['file'];
					if ($localFile != '.htaccess' && $localFile != $userIni) {
						$localFile = ABSPATH . '/' . preg_replace('/^[\.\/]+/', '', $localFile);
					}
					else {
						$localFile = ABSPATH . '/' . $localFile;
					}
					
					if(file_exists($localFile)){
						$issueList[$i]['data']['fileExists'] = true;
					} else {
						$issueList[$i]['data']['fileExists'] = '';
					}
				}
				if ($issueList[$i]['type'] == 'database') {
					$issueList[$i]['data']['optionExists'] = false;
					if (!empty($issueList[$i]['data']['site_id'])) {
						$table_options = wfDB::blogTable('options', $issueList[$i]['data']['site_id']);
						$issueList[$i]['data']['optionExists'] = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM {$table_options} WHERE option_name = %s", $issueList[$i]['data']['option_name'])) > 0;
					}
				}
				$issueList[$i]['issueIDX'] = $i;
			}
		}
		return $ret; //array of lists of issues by status
	}
	public function getPendingIssues($offset = 0, $limit = 100){
		/** @var wpdb $wpdb */
		global $wpdb;
		$issues = $this->getDB()->querySelect("SELECT * FROM {$this->pendingIssuesTable} ORDER BY id ASC LIMIT %d,%d", $offset, $limit);
		foreach($issues as &$i){
			$i['data'] = unserialize($i['data']);
		}
		return $issues;
	}
	public function getFixableIssueCount() {
		global $wpdb;
		$issues = $this->getDB()->querySelect("SELECT * FROM {$this->issuesTable} WHERE data LIKE '%s:6:\"canFix\";b:1;%'");
		$count = 0;
		foreach ($issues as $i) {
			$i['data'] = unserialize($i['data']);
			if (isset($i['data']['canFix']) && $i['data']['canFix']) {
				$count++;
			}
		}
		return $count;
	}
	public function getDeleteableIssueCount() {
		global $wpdb;
		$issues = $this->getDB()->querySelect("SELECT * FROM {$this->issuesTable} WHERE data LIKE '%s:9:\"canDelete\";b:1;%'");
		$count = 0;
		foreach ($issues as $i) {
			$i['data'] = unserialize($i['data']);
			if (isset($i['data']['canDelete']) && $i['data']['canDelete']) {
				$count++;
			}
		}
		return $count;
	}
	public function getIssueCount() {
		return (int) $this->getDB()->querySingle("select COUNT(*) from " . $this->issuesTable . " WHERE status = 'new'");
	}
	public function getPendingIssueCount() {
		return (int) $this->getDB()->querySingle("select COUNT(*) from " . $this->pendingIssuesTable . " WHERE status = 'new'");
	}
	public function getLastIssueUpdateTimestamp() {
		return (int) $this->getDB()->querySingle("select MAX(lastUpdated) from " . $this->issuesTable);
	}
	public function reconcileUpgradeIssues($report = null, $useCachedValued = false) {
		if ($report === null) {
			$report = new wfActivityReport();
		}
		
		$updatesNeeded = $report->getUpdatesNeeded($useCachedValued);
		if ($updatesNeeded) {
			if (!$updatesNeeded['core']) {
				$this->getDB()->queryWrite("DELETE FROM {$this->issuesTable} WHERE status = 'new' AND type = 'wfUpgrade'");
			}
			
			if ($updatesNeeded['plugins']) {
				$upgradeNames = array();
				foreach ($updatesNeeded['plugins'] as $p) {
					$name = $p['Name'];
					$upgradeNames[$name] = 1;
				}
				$upgradeIssues = $this->getDB()->querySelect("SELECT * FROM {$this->issuesTable} WHERE status = 'new' AND type = 'wfPluginUpgrade'");
				foreach ($upgradeIssues as $issue) {
					$data = unserialize($issue['data']);
					$name = $data['Name'];
					if (!isset($upgradeNames[$name])) { //Some plugins don't have a slug associated with them, so we anchor on the name
						$this->deleteIssue($issue['id']);
					}
				}
			}
			else {
				$this->getDB()->queryWrite("DELETE FROM {$this->issuesTable} WHERE status = 'new' AND type = 'wfPluginUpgrade'");
			}
			
			if ($updatesNeeded['themes']) {
				$upgradeNames = array();
				foreach ($updatesNeeded['themes'] as $t) {
					$name = $t['Name'];
					$upgradeNames[$name] = 1;
				}
				$upgradeIssues = $this->getDB()->querySelect("SELECT * FROM {$this->issuesTable} WHERE status = 'new' AND type = 'wfThemeUpgrade'");
				foreach ($upgradeIssues as $issue) {
					$data = unserialize($issue['data']);
					$name = $data['Name'];
					if (!isset($upgradeNames[$name])) { //Some themes don't have a slug associated with them, so we anchor on the name
						$this->deleteIssue($issue['id']);
					}
				}
			}
			else {
				$this->getDB()->queryWrite("DELETE FROM {$this->issuesTable} WHERE status = 'new' AND type = 'wfThemeUpgrade'");
			}
		}
		else {
			$this->getDB()->queryWrite("DELETE FROM {$this->issuesTable} WHERE status = 'new' AND (type = 'wfUpgrade' OR type = 'wfPluginUpgrade' OR type = 'wfThemeUpgrade')");
		}
		
		wfScanEngine::refreshScanNotification($this);
	}
	private function getDB(){
		if(! $this->db){
			$this->db = new wfDB();
		}
		return $this->db;
	}
}
