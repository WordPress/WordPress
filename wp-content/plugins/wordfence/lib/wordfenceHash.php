<?php
require_once('wordfenceClass.php');
class wordfenceHash {
	private $engine = false;
	private $db = false;
	private $startTime = false;

	//Begin serialized vars
	public $striplen = 0;
	public $totalFiles = 0;
	public $totalDirs = 0;
	public $totalData = 0; //To do a sanity check, don't use 'du' because it gets sparse files wrong and reports blocks used on disk. Use : find . -type f -ls | awk '{total += $7} END {print total}'
	public $stoppedOnFile = false;
	private $coreEnabled = false;
	private $pluginsEnabled = false;
	private $themesEnabled = false;
	private $malwareEnabled = false;
	private $coreUnknownEnabled = false;
	private $knownFiles = false;
	private $malwareData = "";
	private $haveIssues = array();
	private $status = array();
	private $possibleMalware = array();
	private $path = false;
	private $only = false;
	private $totalForks = 0;
	private $alertedOnUnknownWordPressVersion = false;
	private $foldersEntered = array();
	private $foldersProcessed = array();
	private $suspectedFiles = array();
	private $indexed = false;
	private $indexSize = 0;
	private $currentIndex = 0;

	/**
	 * @param string $striplen
	 * @param string $path
	 * @param array $only
	 * @param array $themes
	 * @param array $plugins
	 * @param wfScanEngine $engine
	 * @throws Exception
	 */
	public function __construct($striplen, $path, $only, $themes, $plugins, $engine, $malwarePrefixesHash){
		$this->striplen = $striplen;
		$this->path = $path;
		$this->only = $only;
		$this->engine = $engine;

		$this->startTime = microtime(true);

		$options = $this->engine->scanController()->scanOptions();
		if ($options['scansEnabled_core']) { $this->coreEnabled = true; }
		if ($options['scansEnabled_plugins']) { $this->pluginsEnabled = true; }
		if ($options['scansEnabled_themes']) { $this->themesEnabled = true; }
		if ($options['scansEnabled_malware']) { $this->malwareEnabled = true; }
		if ($options['scansEnabled_coreUnknown']) { $this->coreUnknownEnabled = true; }

		$this->db = new wfDB();

		//Doing a delete for now. Later we can optimize this to only scan modified files.
		//$this->db->queryWrite("update " . wfDB::networkTable('wfFileMods') . " set oldMD5 = newMD5");
		$this->db->truncate(wfDB::networkTable('wfFileMods'));
		$this->db->truncate(wfDB::networkTable('wfKnownFileList'));
		$this->db->truncate(wfDB::networkTable('wfPendingIssues'));
		$fetchCoreHashesStatus = wfIssues::statusStart("Fetching core, theme and plugin file signatures from Wordfence");
		try {
			$this->knownFiles = $this->engine->getKnownFilesLoader()->getKnownFiles();
		} catch (wfScanKnownFilesException $e) {
			wfIssues::statusEndErr();
			throw $e;
		}
		wfIssues::statusEnd($fetchCoreHashesStatus, wfIssues::STATUS_SUCCESS);
		if ($this->malwareEnabled) {
			$malwarePrefixStatus = wfIssues::statusStart("Fetching list of known malware files from Wordfence");
			
			$stored = wfConfig::get_ser('malwarePrefixes', array(), false);
			if (is_array($stored) && isset($stored['hash']) && $stored['hash'] == $malwarePrefixesHash && isset($stored['prefixes']) && wfWAFUtils::strlen($stored['prefixes']) % 4 == 0) {
				wordfence::status(4, 'info', "Using cached malware prefixes");
			}
			else {
				wordfence::status(4, 'info', "Fetching fresh malware prefixes");
				
				$malwareData = $engine->api->getStaticURL('/malwarePrefixes.bin');
				if (!$malwareData) {
					wfIssues::statusEndErr();
					throw new Exception("Could not fetch malware signatures from Wordfence servers.");
				}
				
				if (wfWAFUtils::strlen($malwareData) % 4 != 0) {
					wfIssues::statusEndErr();
					throw new Exception("Malware data received from Wordfence servers was not valid.");
				}
				
				$stored = array('hash' => $malwarePrefixesHash, 'prefixes' => $malwareData);
				wfConfig::set_ser('malwarePrefixes', $stored, true, wfConfig::DONT_AUTOLOAD);
			}
			
			$this->malwareData = $stored['prefixes'];
			wfIssues::statusEnd($malwarePrefixStatus, wfIssues::STATUS_SUCCESS);
		}

		if($this->path[strlen($this->path) - 1] != '/'){
			$this->path .= '/';
		}
		if(! is_readable($path)){
			throw new Exception("Could not read directory " . $this->path . " to do scan.");
		}
		$this->haveIssues = array(
			'core' => wfIssues::STATUS_SECURE,
			'coreUnknown' => wfIssues::STATUS_SECURE,
			'themes' => wfIssues::STATUS_SECURE,
			'plugins' => wfIssues::STATUS_SECURE,
			'malware' => wfIssues::STATUS_SECURE,
			);
		if($this->coreEnabled){ $this->status['core'] = wfIssues::statusStart("Comparing core WordPress files against originals in repository"); $this->engine->scanController()->startStage(wfScanner::STAGE_FILE_CHANGES); } else { wfIssues::statusDisabled("Skipping core scan"); }
		if($this->themesEnabled){ $this->status['themes'] = wfIssues::statusStart("Comparing open source themes against WordPress.org originals"); $this->engine->scanController()->startStage(wfScanner::STAGE_FILE_CHANGES); } else { wfIssues::statusDisabled("Skipping theme scan"); }
		if($this->pluginsEnabled){ $this->status['plugins'] = wfIssues::statusStart("Comparing plugins against WordPress.org originals"); $this->engine->scanController()->startStage(wfScanner::STAGE_FILE_CHANGES); } else { wfIssues::statusDisabled("Skipping plugin scan"); }
		if($this->malwareEnabled){ $this->status['malware'] = wfIssues::statusStart("Scanning for known malware files"); $this->engine->scanController()->startStage(wfScanner::STAGE_MALWARE_SCAN); } else { wfIssues::statusDisabled("Skipping malware scan"); }
		if($this->coreUnknownEnabled){ $this->status['coreUnknown'] = wfIssues::statusStart("Scanning for unknown files in wp-admin and wp-includes"); $this->engine->scanController()->startStage(wfScanner::STAGE_FILE_CHANGES); } else { wfIssues::statusDisabled("Skipping unknown core file scan"); }
		
		if ($options['scansEnabled_fileContents']) { $this->engine->scanController()->startStage(wfScanner::STAGE_MALWARE_SCAN); }
		if ($options['scansEnabled_fileContentsGSB']) { $this->engine->scanController()->startStage(wfScanner::STAGE_CONTENT_SAFETY); }
		
		if ($this->coreUnknownEnabled && !$this->alertedOnUnknownWordPressVersion && empty($this->knownFiles['core'])) {
			require(ABSPATH . 'wp-includes/version.php'); //defines $wp_version
			$this->alertedOnUnknownWordPressVersion = true;
			$added = $this->engine->addIssue(
				'coreUnknown',
				2,
				'coreUnknown' . $wp_version,
				'coreUnknown' . $wp_version,
				'Unknown WordPress core version: ' . $wp_version,
				"The core files scan will not be run because this version of WordPress is not currently indexed by Wordfence. This may be due to using a prerelease version or because the servers are still indexing a new release. If you are using an official WordPress release, this issue will automatically dismiss once the version is indexed and another scan is run.",
				array()
			);
			
			if ($added == wfIssues::ISSUE_ADDED || $added == wfIssues::ISSUE_UPDATED) { $this->haveIssues['coreUnknown'] = wfIssues::STATUS_PROBLEM; }
			else if ($this->haveIssues['coreUnknown'] != wfIssues::STATUS_PROBLEM && ($added == wfIssues::ISSUE_IGNOREP || $added == wfIssues::ISSUE_IGNOREC)) { $this->haveIssues['coreUnknown'] = wfIssues::STATUS_IGNORED; }
		}
	}
	public function __sleep(){
		return array('striplen', 'totalFiles', 'totalDirs', 'totalData', 'stoppedOnFile', 'coreEnabled', 'pluginsEnabled', 'themesEnabled', 'malwareEnabled', 'coreUnknownEnabled', 'knownFiles', 'haveIssues', 'status', 'possibleMalware', 'path', 'only', 'totalForks', 'alertedOnUnknownWordPressVersion', 'foldersProcessed', 'suspectedFiles', 'indexed', 'indexSize', 'currentIndex', 'foldersEntered');
	}
	public function __wakeup(){
		$this->db = new wfDB();
		$this->startTime = microtime(true);
		$this->totalForks++;
		
		$stored = wfConfig::get_ser('malwarePrefixes', array(), false);
		if (!isset($stored['prefixes'])) {
			$stored['prefixes'] = '';
		}
		$this->malwareData = $stored['prefixes'];
	}
	public function getSuspectedFiles() {
		return array_keys($this->suspectedFiles);
	}
	public function run($engine){ //base path and 'only' is a list of files and dirs in the bast that are the only ones that should be processed. Everything else in base is ignored. If only is empty then everything is processed.
		if($this->totalForks > 1000){
			throw new Exception("Wordfence file scanner detected a possible infinite loop. Exiting on file: " . $this->stoppedOnFile);
		}
		$this->engine = $engine;
		wordfence::status(4, 'info', "Indexing files for scanning");
		if (!$this->indexed) {
			$start = microtime(true);
			$indexedFiles = array();
			
			if (count($this->only) > 0) {
				$files = $this->only;
			}
			else {
				$files = scandir($this->path);
			}
			
			foreach ($files as $file) {
				if ($file == '.' || $file == '..') { continue; }
				$file = $this->path . $file;
				$this->_dirIndex($file, $indexedFiles);
			}
			$this->_serviceIndexQueue($indexedFiles, true);
			$this->indexed = true;
			unset($this->foldersEntered); $this->foldersEntered = array();
			unset($this->foldersProcessed); $this->foldersProcessed = array();
			$end = microtime(true);
			wordfence::status(4, 'info', "Index time: " . ($end - $start));
		}
		
		$this->_checkForTimeout('');
		
		wordfence::status(4, 'info', "Beginning file hashing");
		while ($file = $this->_nextFile()) {
			$this->processFile($file);
			$this->_checkForTimeout($file);
		}
		
		wordfence::status(4, 'info', "Processing pending issues");
		$this->_processPendingIssues();
		
		wordfence::status(2, 'info', "Analyzed " . $this->totalFiles . " files containing " . wfUtils::formatBytes($this->totalData) . " of data.");
		if($this->coreEnabled){ wfIssues::statusEnd($this->status['core'], $this->haveIssues['core']); $this->engine->scanController()->completeStage(wfScanner::STAGE_FILE_CHANGES, $this->haveIssues['core']); }
		if($this->themesEnabled){ wfIssues::statusEnd($this->status['themes'], $this->haveIssues['themes']); $this->engine->scanController()->completeStage(wfScanner::STAGE_FILE_CHANGES, $this->haveIssues['themes']); }
		if($this->pluginsEnabled){ wfIssues::statusEnd($this->status['plugins'], $this->haveIssues['plugins']); $this->engine->scanController()->completeStage(wfScanner::STAGE_FILE_CHANGES, $this->haveIssues['plugins']); }
		if($this->coreUnknownEnabled){ wfIssues::statusEnd($this->status['coreUnknown'], $this->haveIssues['coreUnknown']); $this->engine->scanController()->completeStage(wfScanner::STAGE_FILE_CHANGES, $this->haveIssues['coreUnknown']); }
		if(sizeof($this->possibleMalware) > 0){
			$malwareResp = $engine->api->binCall('check_possible_malware', json_encode($this->possibleMalware));
			if($malwareResp['code'] != 200){
				wfIssues::statusEndErr();
				throw new Exception("Invalid response from Wordfence API during check_possible_malware");
			}
			$malwareList = json_decode($malwareResp['data'], true);
			if(is_array($malwareList) && sizeof($malwareList) > 0){
				for($i = 0; $i < sizeof($malwareList); $i++){ 
					$file = $malwareList[$i][0];
					$md5 = $malwareList[$i][1];
					$name = $malwareList[$i][2];
					$added = $this->engine->addIssue(
						'file', 
						1, 
						$this->path . $file, 
						$md5,
						'This file is suspected malware: ' . $file,
						"This file's signature matches a known malware file. The title of the malware is '" . $name . "'. Immediately inspect this file using the 'View' option below and consider deleting it from your server.",
						array(
							'file' => $file,
							'cType' => 'unknown',
							'canDiff' => false,
							'canFix' => false,
							'canDelete' => true
							)
						);
					
					if ($added == wfIssues::ISSUE_ADDED || $added == wfIssues::ISSUE_UPDATED) { $this->haveIssues['malware'] = wfIssues::STATUS_PROBLEM; }
					else if ($this->haveIssues['malware'] != wfIssues::STATUS_PROBLEM && ($added == wfIssues::ISSUE_IGNOREP || $added == wfIssues::ISSUE_IGNOREC)) { $this->haveIssues['malware'] = wfIssues::STATUS_IGNORED; }
				}
			}
		}
		if($this->malwareEnabled){ wfIssues::statusEnd($this->status['malware'], $this->haveIssues['malware']); $this->engine->scanController()->completeStage(wfScanner::STAGE_MALWARE_SCAN, $this->haveIssues['malware']); }
		unset($this->knownFiles); $this->knownFiles = false;
	}
	private function _dirIndex($path, &$indexedFiles) {
		if (substr($path, -3, 3) == '/..' || substr($path, -2, 2) == '/.') {
			return;
		}
		if (!is_readable($path)) { return; } //Applies to files and dirs
		if (!$this->_shouldProcessPath($path)) { return; }
		if (is_dir($path)) {
			$realPath = realpath($path);
			if (!$this->stoppedOnFile && isset($this->foldersEntered[$realPath])) { //Not resuming and already entered this path
				return;
			}
			
			$this->foldersEntered[$realPath] = 1;
			
			$this->totalDirs++;
			if ($path[strlen($path) - 1] != '/') {
				$path .= '/';
			}
			$cont = scandir($path);
			for ($i = 0; $i < sizeof($cont); $i++) {
				if ($cont[$i] == '.' || $cont[$i] == '..') { continue; }
				$file = $path . $cont[$i];
				if (is_file($file)) {
					$relativeFile = substr($file, $this->striplen);
					if ($this->stoppedOnFile && $relativeFile != $this->stoppedOnFile) {
						continue;
					}
					
					if (preg_match('/\.suspected$/i', $relativeFile)) { //Already iterating over all files in the search areas so generate this list here
						wordfence::status(4, 'info', "Found .suspected file: {$relativeFile}");
						$this->suspectedFiles[$relativeFile] = 1;
					}
					
					$this->_checkForTimeout($file, $indexedFiles);
					if ($this->_shouldHashFile($file)) {
						$indexedFiles[] = $relativeFile;
					}
					else {
						wordfence::status(4, 'info', "Skipping unneeded hash: {$file}");
					}
					$this->_serviceIndexQueue($indexedFiles);
				} else if (is_dir($file)) {
					$this->_dirIndex($file, $indexedFiles);
				}
			}
			
			$this->foldersProcessed[$realPath] = 1;
			unset($this->foldersEntered[$realPath]);
		}
		else {
			if (is_file($path)) {
				$relativeFile = substr($path, $this->striplen);
				if ($this->stoppedOnFile && $relativeFile != $this->stoppedOnFile) {
					return;
				}
				
				if (preg_match('/\.suspected$/i', $relativeFile)) { //Already iterating over all files in the search areas so generate this list here
					wordfence::status(4, 'info', "Found .suspected file: {$relativeFile}");
					$this->suspectedFiles[$relativeFile] = 1;
				}
				
				$this->_checkForTimeout($path, $indexedFiles);
				if ($this->_shouldHashFile($path)) {
					$indexedFiles[] = substr($path, $this->striplen);
				}
				else {
					wordfence::status(4, 'info', "Skipping unneeded hash: {$path}");
				}
				$this->_serviceIndexQueue($indexedFiles);
			}
		}
	}
	private function _serviceIndexQueue(&$indexedFiles, $final = false) {
		$payload = array();
		if (count($indexedFiles) > 500) {
			$payload = array_splice($indexedFiles, 0, 500);
		}
		else if ($final) {
			$payload = $indexedFiles;
			$indexedFiles = array();
		}
		
		if (count($payload) > 0) {
			global $wpdb;
			$table_wfKnownFileList = wfDB::networkTable('wfKnownFileList');
			$query = substr("INSERT INTO {$table_wfKnownFileList} (path) VALUES " . str_repeat("('%s'), ", count($payload)), 0, -2);
			$wpdb->query($wpdb->prepare($query, $payload));
			$this->indexSize += count($payload);
			wordfence::status(2, 'info', "{$this->indexSize} files indexed");
		}
	}
	private function _nextFile($advanceCursor = true) {
		static $files = array();
		if (count($files) == 0) {
			global $wpdb;
			$table_wfKnownFileList = wfDB::networkTable('wfKnownFileList');
			$files = $wpdb->get_col($wpdb->prepare("SELECT path FROM {$table_wfKnownFileList} WHERE id > %d ORDER BY id ASC LIMIT 500", $this->currentIndex));
		}
		
		$file = null;
		if ($advanceCursor) {
			$file = array_shift($files);
			$this->currentIndex++;
		}
		else if (count($files) > 0) {
			$file = $files[0];
		}
		
		if ($file === null) {
			return null;
		}
		return ABSPATH . $file;
	}
	private function _checkForTimeout($path, $indexQueue = false) {
		$file = substr($path, $this->striplen);
		if ((!$this->stoppedOnFile) && $this->engine->shouldFork()) { //max X seconds but don't allow fork if we're looking for the file we stopped on. Search mode is VERY fast.
			if ($indexQueue !== false) {
				$this->_serviceIndexQueue($indexQueue, true);
				$this->stoppedOnFile = $file;
				wordfence::status(4, 'info', "Forking during indexing: " . $path); 
			}
			else {
				wordfence::status(4, 'info', "Calling fork() from wordfenceHash with maxExecTime: " . $this->engine->maxExecTime);
			}
			$this->engine->fork();
			//exits
		}
		
		if ($this->stoppedOnFile && $file != $this->stoppedOnFile && $indexQueue !== false) {
			return;
		}
		else if ($this->stoppedOnFile && $file == $this->stoppedOnFile) {
			$this->stoppedOnFile = false; //Continue indexing
		}
	}
	private function _shouldProcessPath($path) {
		$file = substr($path, $this->striplen);
		$exclude = wordfenceScanner::getExcludeFilePattern(wordfenceScanner::EXCLUSION_PATTERNS_USER);
		if ($exclude && preg_match($exclude, $file)) {
			return false;
		}
		
		$realPath = realpath($path);
		if (isset($this->foldersProcessed[$realPath])) {
			return false;
		}
		
		return true;
	}
	private function processFile($realFile){
		$file = substr($realFile, $this->striplen);
		
		if(wfUtils::fileTooBig($realFile)){
			wordfence::status(4, 'info', "Skipping file larger than max size: $realFile");
			return;
		}
		
		if (function_exists('memory_get_usage')) {
			wordfence::status(4, 'info', "Scanning: $realFile (Mem:" . sprintf('%.1f', memory_get_usage(true) / (1024 * 1024)) . "M)");
		} else {
			wordfence::status(4, 'info', "Scanning: $realFile");
		}
		
		wfUtils::beginProcessingFile($file);
		$wfHash = self::wfHash($realFile);
		$this->engine->scanController()->incrementSummaryItem(wfScanner::SUMMARY_SCANNED_FILES);
		if($wfHash){
			$md5 = strtoupper($wfHash[0]);
			$shac = strtoupper($wfHash[1]);
			$knownFile = 0;
			if($this->malwareEnabled && $this->isMalwarePrefix($md5)){
				$this->possibleMalware[] = array($file, $md5);
			}

			$knownFileExclude = wordfenceScanner::getExcludeFilePattern(wordfenceScanner::EXCLUSION_PATTERNS_KNOWN_FILES);
			$allowKnownFileScan = true;
			if ($knownFileExclude) {
				$allowKnownFileScan = !preg_match($knownFileExclude, $realFile);
			}

			if ($allowKnownFileScan)
			{
				if (isset($this->knownFiles['core'][$file]))
				{
					if (strtoupper($this->knownFiles['core'][$file]) == $shac)
					{
						$knownFile = 1;
					} else
					{
						if ($this->coreEnabled)
						{
							$localFile = ABSPATH . '/' . preg_replace('/^[\.\/]+/', '', $file);
							$fileContents = @file_get_contents($localFile);
							if ($fileContents && (!preg_match('/<\?' . 'php[\r\n\s\t]*\/\/[\r\n\s\t]*Silence is golden\.[\r\n\s\t]*(?:\?>)?[\r\n\s\t]*$/s', $fileContents))) {
								$added = $this->engine->addPendingIssue(
									'knownfile',
									1,
									'coreModified' . $file,
									'coreModified' . $file . $md5,
									'WordPress core file modified: ' . $file,
									"This WordPress core file has been modified and differs from the original file distributed with this version of WordPress.",
									array(
										'file' => $file,
										'cType' => 'core',
										'canDiff' => true,
										'canFix' => true,
										'canDelete' => false,
										'haveIssues' => 'core'
									)
								);
							}
						}
					}
				} else if (isset($this->knownFiles['plugins'][$file]))
				{
					if (in_array($shac, $this->knownFiles['plugins'][$file]))
					{
						$knownFile = 1;
					} else
					{
						if ($this->pluginsEnabled)
						{
							$options = $this->engine->scanController()->scanOptions();
							$shouldGenerateIssue = true;
							if (!$options['scansEnabled_highSense'] && preg_match('~/readme\.(?:txt|md)$~i', $file)) { //Don't generate issues for changed readme files unless high sensitivity is on
								$shouldGenerateIssue = false;
							}
							
							if ($shouldGenerateIssue)
							{
								$itemName = $this->knownFiles['plugins'][$file][0];
								$itemVersion = $this->knownFiles['plugins'][$file][1];
								$cKey = $this->knownFiles['plugins'][$file][2];
								$this->engine->addPendingIssue(
									'knownfile',
									2,
									'modifiedplugin' . $file,
									'modifiedplugin' . $file . $md5,
									'Modified plugin file: ' . $file,
									"This file belongs to plugin \"$itemName\" version \"$itemVersion\" and has been modified from the file that is distributed by WordPress.org for this version. Please use the link to see how the file has changed. If you have modified this file yourself, you can safely ignore this warning. If you see a lot of changed files in a plugin that have been made by the author, then try uninstalling and reinstalling the plugin to force an upgrade. Doing this is a workaround for plugin authors who don't manage their code correctly. [See our FAQ on www.wordfence.com for more info]",
									array(
										'file' => $file,
										'cType' => 'plugin',
										'canDiff' => true,
										'canFix' => true,
										'canDelete' => false,
										'cName' => $itemName,
										'cVersion' => $itemVersion,
										'cKey' => $cKey,
										'haveIssues' => 'plugins'
									)
								);
							}
						}

					}
				} else if (isset($this->knownFiles['themes'][$file]))
				{
					if (in_array($shac, $this->knownFiles['themes'][$file]))
					{
						$knownFile = 1;
					} else
					{
						if ($this->themesEnabled)
						{
							$options = $this->engine->scanController()->scanOptions();
							$shouldGenerateIssue = true;
							if (!$options['scansEnabled_highSense'] && preg_match('~/readme\.(?:txt|md)$~i', $file)) { //Don't generate issues for changed readme files unless high sensitivity is on
								$shouldGenerateIssue = false;
							}
							
							if ($shouldGenerateIssue)
							{
								$itemName = $this->knownFiles['themes'][$file][0];
								$itemVersion = $this->knownFiles['themes'][$file][1];
								$cKey = $this->knownFiles['themes'][$file][2];
								$this->engine->addPendingIssue(
									'knownfile',
									2,
									'modifiedtheme' . $file,
									'modifiedtheme' . $file . $md5,
									'Modified theme file: ' . $file,
									"This file belongs to theme \"$itemName\" version \"$itemVersion\" and has been modified from the original distribution. It is common for site owners to modify their theme files, so if you have modified this file yourself you can safely ignore this warning.",
									array(
										'file' => $file,
										'cType' => 'theme',
										'canDiff' => true,
										'canFix' => true,
										'canDelete' => false,
										'cName' => $itemName,
										'cVersion' => $itemVersion,
										'cKey' => $cKey,
										'haveIssues' => 'themes'
									)
								);
							}
						}

					}
				}
				else if ($this->coreUnknownEnabled && !$this->alertedOnUnknownWordPressVersion) { //Check for unknown files in system directories
					$restrictedWordPressFolders = array(ABSPATH . 'wp-admin/', ABSPATH . WPINC . '/');
					foreach ($restrictedWordPressFolders as $path) {
						if (strpos($realFile, $path) === 0) {
							$added = $this->engine->addIssue(
								'knownfile',
								2,
								'coreUnknown' . $file,
								'coreUnknown' . $file . $md5,
								'Unknown file in WordPress core: ' . $file,
								"This file is in a WordPress core location but is not distributed with this version of WordPress. This is usually due to it being left over from a previous WordPress update, but it may also have been added by another plugin or a malicious file added by an attacker.",
								array(
									'file' => $file,
									'cType' => 'core',
									'canDiff' => false,
									'canFix' => false,
									'canDelete' => true,
								)
							);
							
							if ($added == wfIssues::ISSUE_ADDED || $added == wfIssues::ISSUE_UPDATED) { $this->haveIssues['coreUnknown'] = wfIssues::STATUS_PROBLEM; }
							else if ($this->haveIssues['coreUnknown'] != wfIssues::STATUS_PROBLEM && ($added == wfIssues::ISSUE_IGNOREP || $added == wfIssues::ISSUE_IGNOREC)) { $this->haveIssues['coreUnknown'] = wfIssues::STATUS_IGNORED; }
						}
					}
				}
			}
			// knownFile means that the file is both part of core or a known plugin or theme AND that we recognize the file's hash. 
			// we could split this into files whose path we recognize and file's whose path we recognize AND who have a valid sig.
			// But because we want to scan files whose sig we don't recognize, regardless of known path or not, we only need one "knownFile" field.
			$fileModsTable = wfDB::networkTable('wfFileMods');
			$this->db->queryWrite("INSERT INTO {$fileModsTable} (filename, filenameMD5, knownFile, oldMD5, newMD5, SHAC) VALUES ('%s', UNHEX(MD5('%s')), %d, '', UNHEX('%s'), UNHEX('%s')) ON DUPLICATE KEY UPDATE newMD5 = UNHEX('%s'), SHAC = UNHEX('%s'), knownFile = %d", $file, $file, $knownFile, $md5, $shac, $md5, $shac, $knownFile);

			$this->totalFiles++;
			$this->totalData += @filesize($realFile); //We already checked if file overflows int in the fileTooBig routine above
			if($this->totalFiles % 100 === 0){
				wordfence::status(2, 'info', "Analyzed " . $this->totalFiles . " files containing " . wfUtils::formatBytes($this->totalData) . " of data so far");
			}
		} else {
			//wordfence::status(2, 'error', "Could not gen hash for file (probably because we don't have permission to access the file): $realFile");
		}
		wfUtils::endProcessingFile();
	}
	private function _processPendingIssues() {
		$fileModsTable = wfDB::networkTable('wfFileMods');
		
		$count = $this->engine->getPendingIssueCount();
		$offset = 0;
		while ($offset < $count) {
			$issues = $this->engine->getPendingIssues($offset);
			if (count($issues) == 0) {
				break;
			}
			
			//Do a bulk check of is_safe_file
			$hashesToCheck = array();
			foreach ($issues as &$i) {
				$shac = $this->db->querySingle("SELECT HEX(SHAC) FROM {$fileModsTable} WHERE filename = '%s' AND isSafeFile = '?'", $i['data']['file']);
				$shac = strtoupper($shac);
				$i['shac'] = null;
				if ($shac !== null) {
					$shac = strtoupper($shac);
					$i['shac'] = $shac;
					$hashesToCheck[] = $shac;
				}
			}
			
			$safeFiles = array();
			if (count($hashesToCheck) > 0) {
				$safeFiles = $this->isSafeFile($hashesToCheck);
			}
			
			//Migrate non-safe file issues to official issues
			foreach ($issues as &$i) {
				if (!in_array($i['shac'], $safeFiles)) {
					$haveIssuesType = $i['data']['haveIssues'];
					$added = $this->engine->addIssue(
						$i['type'],
						$i['severity'],
						$i['ignoreP'],
						$i['ignoreC'],
						$i['shortMsg'],
						$i['longMsg'],
						$i['data'],
						true //Prevent ignoreP and ignoreC from being hashed again
					);
					if ($added == wfIssues::ISSUE_ADDED || $added == wfIssues::ISSUE_UPDATED) { $this->haveIssues[$haveIssuesType] = wfIssues::STATUS_PROBLEM; }
					else if ($this->haveIssues[$haveIssuesType] != wfIssues::STATUS_PROBLEM && ($added == wfIssues::ISSUE_IGNOREP || $added == wfIssues::ISSUE_IGNOREC)) { $this->haveIssues[$haveIssuesType] = wfIssues::STATUS_IGNORED; }
					$this->db->queryWrite("UPDATE {$fileModsTable} SET isSafeFile = '0' WHERE SHAC = UNHEX('%s')", $i['shac']);
				}
				else {
					$this->db->queryWrite("UPDATE {$fileModsTable} SET isSafeFile = '1' WHERE SHAC = UNHEX('%s')", $i['shac']);
				}
			}
			
			$offset += count($issues);
			$this->engine->checkForKill();
		}
	}
	public static function wfHash($file){
		$fp = @fopen($file, "rb");
		if (!$fp) {
			return false;
		}
		$md5Context = hash_init('md5');
		$sha256Context = hash_init('sha256');
		while (!feof($fp)) {
			$data = fread($fp, 65536);
			if ($data === false) {
				return false;
			}
			hash_update($md5Context, $data);
			hash_update($sha256Context, str_replace(array("\n","\r","\t"," "),"", $data));
		}
		$md5 = hash_final($md5Context, false);
		$shac = hash_final($sha256Context, false);
		return array($md5, $shac);
	}
	private function _shouldHashFile($fullPath) {
		$file = substr($fullPath, $this->striplen);

		//Core File, return true
		if ((isset($this->knownFiles['core']) && isset($this->knownFiles['core'][$file])) ||
			(isset($this->knownFiles['plugins']) && isset($this->knownFiles['plugins'][$file])) ||
			(isset($this->knownFiles['themes']) && isset($this->knownFiles['themes'][$file]))) {
			return true;
		}
		
		//Excluded file, return false
		$excludePattern = wordfenceScanner::getExcludeFilePattern(wordfenceScanner::EXCLUSION_PATTERNS_USER | wordfenceScanner::EXCLUSION_PATTERNS_MALWARE); 
		if ($excludePattern && preg_match($excludePattern, $file)) {
			return false;
		}
		
		//Unknown file in a core location
		if ($this->coreUnknownEnabled && !$this->alertedOnUnknownWordPressVersion) {
			$restrictedWordPressFolders = array(ABSPATH . 'wp-admin/', ABSPATH . WPINC . '/');
			foreach ($restrictedWordPressFolders as $path) {
				if (strpos($fullPath, $path) === 0) {
					return true;
				}
			}
		}
		
		//Determine treatment
		$fileExt = '';
		if (preg_match('/\.([a-zA-Z\d\-]{1,7})$/', $file, $matches)) {
			$fileExt = strtolower($matches[1]);
		}
		$isPHP = false;
		if (preg_match('/\.(?:php(?:\d+)?|phtml)(\.|$)/i', $file)) {
			$isPHP = true;
		}
		$isHTML = false;
		if (preg_match('/\.(?:html?)(\.|$)/i', $file)) {
			$isHTML = true;
		}
		$isJS = false;
		if (preg_match('/\.(?:js)(\.|$)/i', $file)) {
			$isJS = true;
		}
		
		$options = $this->engine->scanController()->scanOptions();
		
		//If scan images is disabled, only allow .js through
		if (!$isPHP && preg_match('/^(?:jpg|jpeg|mp3|avi|m4v|mov|mp4|gif|png|tiff?|svg|sql|js|tbz2?|bz2?|xz|zip|tgz|gz|tar|log|err\d+)$/', $fileExt)) {
			if (!$options['scansEnabled_scanImages'] && !$isJS) {
				return false;
			}
		}
		
		//If high sensitivity is disabled, don't allow .sql
		if (strtolower($fileExt) == 'sql') {
			if (!$options['scansEnabled_highSense']) {
				return false;
			}
		}
		
		//Treating as binary, return true
		$treatAsBinary = ($isPHP || $isHTML || $options['scansEnabled_scanImages']);
		if ($treatAsBinary) {
			return true;
		}
		
		//Will be malware scanned, return true
		if ($fileExt == 'js') {
			return true;
		}
		
		return false;
	}
	private function isMalwarePrefix($hexMD5){
		$hasPrefix = $this->_prefixListContainsMD5($this->malwareData, wfUtils::hex2bin($hexMD5));
		return $hasPrefix !== false;
	}
	
	/**
	 * @param $prefixList The prefix list to search, sorted as a binary string.
	 * @param $md5 The binary MD5 hash to search for.
	 * @return bool|int false if not found, otherwise the index in the list
	 */
	private function _prefixListContainsMD5($prefixList, $md5) {
		$size = 4; //bytes
		$p = substr($md5, 0, $size);
		
		$count = ceil(wfWAFUtils::strlen($prefixList) / $size);
		$low = 0;
		$high = $count - 1;
		
		while ($low <= $high) {
			$mid = (int) (($high + $low) / 2);
			$val = wfWAFUtils::substr($prefixList, $mid * $size, $size);
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
	
	/**
	 * Queries the is_safe_file endpoint. If provided an array, it does a bulk check and returns an array containing the
	 * hashes that were marked as safe. If provided a string, it returns a boolean to indicate the safeness of the file.
	 * 
	 * @param string|array $shac
	 * @return array|bool
	 */
	private function isSafeFile($shac) {
		if (is_array($shac)) {
			$result = $this->engine->api->call('is_safe_file', array(), array('multipleSHAC' => json_encode($shac)));
			if (isset($result['isSafe'])) {
				return $result['isSafe'];
			}
			return array();
		}
		
		$result = $this->engine->api->call('is_safe_file', array(), array('shac' => strtoupper($shac)));
		if(isset($result['isSafe']) && $result['isSafe'] == 1){
			return true;
		}
		return false;
	}
}
