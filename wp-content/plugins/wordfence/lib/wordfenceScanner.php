<?php
require_once('wordfenceConstants.php');
require_once('wordfenceClass.php');
require_once('wordfenceURLHoover.php');
class wordfenceScanner {
	/*
	 * Mask to return all patterns in the exclusion list.
	 * @var int
	 */
	const EXCLUSION_PATTERNS_ALL = PHP_INT_MAX;
	/*
	 * Mask for patterns that the user has added.
	 */
	const EXCLUSION_PATTERNS_USER = 0x1;
	/*
	 * Mask for patterns that should be excluded from the known files scan.
	 */
	const EXCLUSION_PATTERNS_KNOWN_FILES = 0x2;
	/*
	 * Mask for patterns that should be excluded from the malware scan.
	 */
	const EXCLUSION_PATTERNS_MALWARE = 0x4;

	//serialized:
	protected $path = '';
	protected $results = array(); 
	public $errorMsg = false;
	protected $apiKey = false;
	protected $wordpressVersion = '';
	protected $totalFilesScanned = 0;
	protected $startTime = false;
	protected $lastStatusTime = false;
	protected $patterns = "";
	protected $api = false;
	protected static $excludePatterns = array();
	protected static $builtinExclusions = array(
											array('pattern' => 'wp\-includes\/version\.php', 'include' => self::EXCLUSION_PATTERNS_KNOWN_FILES), //Excluded from the known files scan because non-en_US installations will have extra content that fails the check, still in malware scan
											array('pattern' => '(?:wp\-includes|wp\-admin)\/(?:[^\/]+\/+)*(?:\.htaccess|\.htpasswd|php_errorlog|error_log|[^\/]+?\.log|\._|\.DS_Store|\.listing|dwsync\.xml)', 'include' => self::EXCLUSION_PATTERNS_KNOWN_FILES),
											);
	/** @var wfScanEngine */
	protected $scanEngine;

	public function __sleep(){
		return array('path', 'results', 'errorMsg', 'apiKey', 'wordpressVersion', 'urlHoover', 'totalFilesScanned',
			'startTime', 'lastStatusTime', 'patterns', 'scanEngine');
	}
	public function __wakeup(){
	}
	public function __construct($apiKey, $wordpressVersion, $path, $scanEngine) {
		$this->apiKey = $apiKey;
		$this->wordpressVersion = $wordpressVersion;
		$this->api = new wfAPI($this->apiKey, $this->wordpressVersion);
		if($path[strlen($path) - 1] != '/'){
			$path .= '/';
		}
		$this->path = $path;
		$this->scanEngine = $scanEngine;
		
		
		$this->results = array();
		$this->errorMsg = false;
		//First extract hosts or IPs and their URLs into $this->hostsFound and URL's into $this->urlsFound
		$options = $this->scanEngine->scanController()->scanOptions();
		if ($options['scansEnabled_fileContentsGSB']) {
			$this->urlHoover = new wordfenceURLHoover($this->apiKey, $this->wordpressVersion);
		}
		else {
			$this->urlHoover = false;
		}
		
		if ($options['scansEnabled_fileContents']) {
			$this->setupSigs();
		}
		else {
			$this->patterns = array();
		}
	}

	/**
	 * Get scan regexes from noc1 and add any user defined regexes, including descriptions, ID's and time added.
	 * @todo add caching to this.
	 * @throws Exception
	 */
	protected function setupSigs() {
		$sigData = $this->api->call('get_patterns', array(), array());
		if(! (is_array($sigData) && isset($sigData['rules'])) ){
			throw new Exception(__('Wordfence could not get the attack signature patterns from the scanning server.', 'wordfence'));
		}
		
		if (wfWAF::getInstance() && method_exists(wfWAF::getInstance(), 'setMalwareSignatures')) {
			try { wfWAF::getInstance()->setMalwareSignatures(array()); } catch (Exception $e) { /* Ignore */ }
			if (method_exists(wfWAF::getInstance(), 'setMalwareSignatureCommonStrings')) {
				try {
					wfWAF::getInstance()->setMalwareSignatureCommonStrings(array(), array());
				}
				catch (Exception $e) { /* Ignore */ }
			}
		}

		if (is_array($sigData['rules'])) {
			$wafPatterns = array();
			$wafCommonStringIndexes = array();
			foreach ($sigData['rules'] as $key => $signatureRow) {
				list(, , $pattern) = $signatureRow;
				$logOnly = (isset($signatureRow[5]) && !empty($signatureRow[5])) ? $signatureRow[5] : false;
				$commonStringIndexes = (isset($signatureRow[8]) && is_array($signatureRow[8])) ? $signatureRow[8] : array(); 
				if (@preg_match('/' . $pattern . '/iS', null) === false) {
					wordfence::status(1, 'error', __('A regex Wordfence received from its servers is invalid. The pattern is: ', 'wordfence') . esc_html($pattern));
					unset($sigData['rules'][$key]);
				}
				else if (!$logOnly) {
					$wafPatterns[] = $pattern;
					$wafCommonStringIndexes[] = $commonStringIndexes;
				}
			}
			
			if (wfWAF::getInstance() && method_exists(wfWAF::getInstance(), 'setMalwareSignatures')) {
				try { wfWAF::getInstance()->setMalwareSignatures($wafPatterns); } catch (Exception $e) { /* Ignore */ }
				if (method_exists(wfWAF::getInstance(), 'setMalwareSignatureCommonStrings') && isset($sigData['commonStrings']) && is_array($sigData['commonStrings'])) {
					try {
						wfWAF::getInstance()->setMalwareSignatureCommonStrings($sigData['commonStrings'], $wafCommonStringIndexes);
					}
					catch (Exception $e) { /* Ignore */ }
				}
			}
		}

		$userSignatures = wfScanner::shared()->userScanSignatures();
		foreach ($userSignatures as $s) {
			$sigData['rules'][] = $s;
		}

		$this->patterns = $sigData;
		if (isset($this->patterns['signatureUpdateTime'])) {
			wfConfig::set('signatureUpdateTime', $this->patterns['signatureUpdateTime']);
		}
	}

	/**
	 * Return regular expression to exclude files or false if
	 * there is no pattern
	 *
	 * @param $whichPatterns int Bitmask indicating which patterns to include.
	 * @return string|boolean
	 */
	public static function getExcludeFilePattern($whichPatterns = self::EXCLUSION_PATTERNS_USER) {
		if (isset(self::$excludePatterns[$whichPatterns])) {
			return self::$excludePatterns[$whichPatterns];
		}
		
		$exParts = array();
		if (($whichPatterns & self::EXCLUSION_PATTERNS_USER) > 0)
		{
			$exParts = wfScanner::shared()->userExclusions();
		}
		
		$exParts = array_filter($exParts);
		foreach ($exParts as &$exPart) {
			$exPart = preg_quote(trim($exPart), '/');
			$exPart = preg_replace('/\\\\\*/', '.*', $exPart);
		}

		foreach (self::$builtinExclusions as $pattern) {
			if (($pattern['include'] & $whichPatterns) > 0) {
				$exParts[] = $pattern['pattern'];
			}
		}

		if (!empty($exParts)) {
			self::$excludePatterns[$whichPatterns] = '/(?:' . implode('|', array_filter($exParts)) . ')$/i';
		}
		else {
			self::$excludePatterns[$whichPatterns] = false;
		}

		return self::$excludePatterns[$whichPatterns];
	}

	/**
	 * @param wfScanEngine $forkObj
	 * @return array
	 */
	public function scan($forkObj){
		$this->scanEngine = $forkObj;
		$loader = $this->scanEngine->getKnownFilesLoader();
		if(! $this->startTime){
			$this->startTime = microtime(true);
		}
		if(! $this->lastStatusTime){
			$this->lastStatusTime = microtime(true);
		}
		
		//The site's own URL is checked in an earlier scan stage so we exclude it here.
		$options = $this->scanEngine->scanController()->scanOptions();
		$hooverExclusions = array();
		if ($options['scansEnabled_fileContentsGSB']) {
			$hooverExclusions = wordfenceURLHoover::standardExcludedHosts();
		}
		
		$lastCount = 'whatever';
		$excludePattern = self::getExcludeFilePattern(self::EXCLUSION_PATTERNS_USER | self::EXCLUSION_PATTERNS_MALWARE); 
		while (true) {
			$thisCount = wordfenceMalwareScanFile::countRemaining();
			if ($thisCount == $lastCount) {
				//count should always be decreasing. If not, we're in an infinite loop so lets catch it early
				wordfence::status(4, 'info', __('Detected loop in malware scan, aborting.', 'wordfence'));
				break;
			}
			$lastCount = $thisCount;
			
			$files = wordfenceMalwareScanFile::files();
			if (count($files) < 1) {
				wordfence::status(4, 'info', __('No files remaining for malware scan.', 'wordfence'));
				break;
			}
			
			foreach ($files as $record) {
				$file = $record->filename;
				if ($excludePattern && preg_match($excludePattern, $file)) {
					$record->markComplete();
					continue;
				}
				if (!file_exists($this->path . $file)) {
					$record->markComplete();
					continue;
				}
				$fileSum = $record->newMD5;
				
				$fileExt = '';
				if(preg_match('/\.([a-zA-Z\d\-]{1,7})$/', $file, $matches)){
					$fileExt = strtolower($matches[1]);
				}
				$isPHP = false;
				if(preg_match('/\.(?:php(?:\d+)?|phtml)(\.|$)/i', $file)) {
					$isPHP = true;
				}
				$isHTML = false;
				if(preg_match('/\.(?:html?)(\.|$)/i', $file)) {
					$isHTML = true;
				}
				$isJS = false;
				if(preg_match('/\.(?:js)(\.|$)/i', $file)) {
					$isJS = true;
				}
				$dontScanForURLs = false;
				if (!$options['scansEnabled_highSense'] && (preg_match('/^(?:\.htaccess|wp\-config\.php)$/', $file) || $file === ini_get('user_ini.filename'))) {
					$dontScanForURLs = true;
				}
				
				$isScanImagesFile = false;
				if (!$isPHP && preg_match('/^(?:jpg|jpeg|mp3|avi|m4v|mov|mp4|gif|png|tiff?|svg|sql|js|tbz2?|bz2?|xz|zip|tgz|gz|tar|log|err\d+)$/', $fileExt)) {
					if ($options['scansEnabled_scanImages']) {
						$isScanImagesFile = true;
					}
					else if (!$isJS) {
						$record->markComplete();
						continue;
					}
				}
				$isHighSensitivityFile = false;
				if (strtolower($fileExt) == 'sql') {
					if ($options['scansEnabled_highSense']) {
						$isHighSensitivityFile = true;
					}
					else {
						$record->markComplete();
						continue;
					}
				}
				if(wfUtils::fileTooBig($this->path . $file)){ //We can't use filesize on 32 bit systems for files > 2 gigs
					//We should not need this check because files > 2 gigs are not hashed and therefore won't be received back as unknowns from the API server
					//But we do it anyway to be safe.
					wordfence::status(2, 'error', sprintf(__('Encountered file that is too large: %s - Skipping.', 'wordfence'), $file));
					$record->markComplete();
					continue;
				}
				wfUtils::beginProcessingFile($file);

				$fsize = @filesize($this->path . $file); //Checked if too big above
				$fsize = wfUtils::formatBytes($fsize);
				if (function_exists('memory_get_usage')) {
					wordfence::status(4, 'info', sprintf(__('Scanning contents: %s (Size: %s Mem: %s)', 'wordfence'), $file, $fsize, wfUtils::formatBytes(memory_get_usage(true))));
				} else {
					wordfence::status(4, 'info', sprintf(__('Scanning contents: %s (Size: %s)', 'wordfence'), $file, $fsize));
				}

				$stime = microtime(true);
				$fh = @fopen($this->path . $file, 'r');
				if (!$fh) {
					$record->markComplete();
					continue;
				}
				$totalRead = $record->stoppedOnPosition;
				if ($totalRead > 0) {
					if (@fseek($fh, $totalRead, SEEK_SET) !== 0) {
						$totalRead = 0;
					}
				}

				$dataForFile = $this->dataForFile($file);
				
				while (!feof($fh)) {
					$data = fread($fh, 1 * 1024 * 1024); //read 1 megs max per chunk
					$readSize = wfUtils::strlen($data);
					$currentPosition = $totalRead;
					$totalRead += $readSize;
					if ($readSize < 1) {
						break;
					}
					
					$extraMsg = '';
					if ($isScanImagesFile) {
						$extraMsg = ' ' . __('This file was detected because you have enabled "Scan images, binary, and other files as if they were executable", which treats non-PHP files as if they were PHP code. This option is more aggressive than the usual scans, and may cause false positives.', 'wordfence');
					}
					else if ($isHighSensitivityFile) {
						$extraMsg = ' ' . __('This file was detected because you have enabled HIGH SENSITIVITY scanning. This option is more aggressive than the usual scans, and may cause false positives.', 'wordfence');
					}
					
					$treatAsBinary = ($isPHP || $isHTML || $options['scansEnabled_scanImages']);
					if ($options['scansEnabled_fileContents']) {
						if ($treatAsBinary && wfUtils::strpos($data, '$allowed'.'Sites') !== false && wfUtils::strpos($data, "define ('VER"."SION', '1.") !== false && wfUtils::strpos($data, "TimThum"."b script created by") !== false) {
							$this->addResult(array(
								'type' => 'file',
								'severity' => 1,
								'ignoreP' => $this->path . $file,
								'ignoreC' => $fileSum,
								'shortMsg' => __('File is an old version of TimThumb which is vulnerable.', 'wordfence'),
								'longMsg' => __('This file appears to be an old version of the TimThumb script which makes your system vulnerable to attackers. Please upgrade the theme or plugin that uses this or remove it.', 'wordfence') . $extraMsg,
								'data' => array_merge(array(
									'file' => $file,
									'shac' => $record->SHAC,
									'highSense' => $options['scansEnabled_highSense'],
									'betaSigs' => wfConfig::get('betaThreatDefenseFeed'),
								), $dataForFile),
							));
							break;
						}
						else {
							$allCommonStrings = $this->patterns['commonStrings'];
							$commonStringsFound = array_fill(0, count($allCommonStrings), null); //Lazily looked up below
							
							$regexMatched = false;
							foreach ($this->patterns['rules'] as $rule) {
								$stoppedOnSignature = $record->stoppedOnSignature;
								if (!empty($stoppedOnSignature)) { //Advance until we find the rule we stopped on last time
									//wordfence::status(4, 'info', "Searching for malware scan resume point (". $stoppedOnSignature . ") at rule " . $rule[0]);
									if ($stoppedOnSignature == $rule[0]) {
										$record->updateStoppedOn('', $currentPosition);
										wordfence::status(4, 'info', sprintf(__('Resuming malware scan at rule %s.', 'wordfence'), $rule[0]));
									}
									continue;
								}
								
								$type = (isset($rule[4]) && !empty($rule[4])) ? $rule[4] : 'server';
								$logOnly = (isset($rule[5]) && !empty($rule[5])) ? $rule[5] : false;
								$commonStringIndexes = (isset($rule[8]) && is_array($rule[8])) ? $rule[8] : array(); 
								if ($type == 'server' && !$treatAsBinary) { continue; }
								else if (($type == 'both' || $type == 'browser') && $fileExt == 'js') { $extraMsg = ''; }
								else if (($type == 'both' || $type == 'browser') && !$treatAsBinary) { continue; }
								
								foreach ($commonStringIndexes as $i) {
									if ($commonStringsFound[$i] === null) {
										$s = $allCommonStrings[$i];
										$commonStringsFound[$i] = (preg_match('/' . $s . '/i', $data) == 1);
									}
									
									if (!$commonStringsFound[$i]) {
										//wordfence::status(4, 'info', "Skipping malware signature ({$rule[0]}) due to short circuit.");
										continue 2;
									}
								}
								
								/*if (count($commonStringIndexes) > 0) {
									wordfence::status(4, 'info', "Processing malware signature ({$rule[0]}) because short circuit matched.");
								}*/
								
								if (preg_match('/(' . $rule[2] . ')/iS', $data, $matches, PREG_OFFSET_CAPTURE)) {
									$matchString = $matches[1][0];
									$matchOffset = $matches[1][1];
									$beforeString = wfWAFUtils::substr($data, max(0, $matchOffset - 100), $matchOffset - max(0, $matchOffset - 100));
									$afterString = wfWAFUtils::substr($data, $matchOffset + strlen($matchString), 100);
									if (!$logOnly) {
										$this->addResult(array(
											'type' => 'file',
											'severity' => 1,
											'ignoreP' => $this->path . $file,
											'ignoreC' => $fileSum,
											'shortMsg' => __('File appears to be malicious: ', 'wordfence') . esc_html($file),
											'longMsg' => sprintf(__('This file appears to be installed or modified by a hacker to perform malicious activity. If you know about this file you can choose to ignore it to exclude it from future scans. The text we found in this file that matches a known malicious file is: <strong style="color: #F00;" class="wf-split-word">%s</strong>.', 'wordfence'), wfUtils::potentialBinaryStringToHTML((wfUtils::strlen($matchString) > 200 ? wfUtils::substr($matchString, 0, 200) . '...' : $matchString))) . ' ' . sprintf(__('The infection type is: <strong>%s</strong>.', 'wordfence'), esc_html($rule[3])) . $extraMsg,
											'data' => array_merge(array(
												'file' => $file,
												'shac' => $record->SHAC,
												'highSense' => $options['scansEnabled_highSense'],
												'betaSigs' => wfConfig::get('betaThreatDefenseFeed'),
											), $dataForFile),
										));
									}
									$regexMatched = true;
									$this->scanEngine->recordMetric('malwareSignature', $rule[0], array('file' => $file, 'match' => $matchString, 'before' => $beforeString, 'after' => $afterString), false);
									break;
								}
								
								if ($forkObj->shouldFork()) {
									$record->updateStoppedOn($rule[0], $currentPosition);
									fclose($fh);
									
									wordfence::status(4, 'info', sprintf(__('Forking during malware scan (%s) to ensure continuity.', 'wordfence'), $rule[0]));
									$forkObj->fork(); //exits
								}
							}
							if ($regexMatched) { break; }
						}
						if ($treatAsBinary && $options['scansEnabled_highSense']) {
							$badStringFound = false;
							if (strpos($data, $this->patterns['badstrings'][0]) !== false) {
								for ($i = 1; $i < sizeof($this->patterns['badstrings']); $i++) {
									if (wfUtils::strpos($data, $this->patterns['badstrings'][$i]) !== false) {
										$badStringFound = $this->patterns['badstrings'][$i];
										break;
									}
								}
							}
							if ($badStringFound) {
								$this->addResult(array(
									'type' => 'file',
									'severity' => 1,
									'ignoreP' => $this->path . $file,
									'ignoreC' => $fileSum,
									'shortMsg' => __('This file may contain malicious executable code: ', 'wordfence') . esc_html($file),
									'longMsg' => sprintf(__('This file is a PHP executable file and contains the word "eval" (without quotes) and the word "<span class="wf-split-word">%s</span>" (without quotes). The eval() function along with an encoding function like the one mentioned are commonly used by hackers to hide their code. If you know about this file you can choose to ignore it to exclude it from future scans. This file was detected because you have enabled HIGH SENSITIVITY scanning. This option is more aggressive than the usual scans, and may cause false positives.', 'wordfence'), esc_html($badStringFound)),
									'data' => array_merge(array(
										'file' => $file,
										'shac' => $record->SHAC,
										'highSense' => $options['scansEnabled_highSense'],
										'betaSigs' => wfConfig::get('betaThreatDefenseFeed'),
									), $dataForFile),
								));
								break;
							}
						}
					}
					
					if (!$dontScanForURLs && $options['scansEnabled_fileContentsGSB']) {
						$found = $this->urlHoover->hoover($file, $data, $hooverExclusions);
						$this->scanEngine->scanController()->incrementSummaryItem(wfScanner::SUMMARY_SCANNED_URLS, $found);
					}
					
					if ($totalRead > 2 * 1024 * 1024) {
						break;
					}
				}
				fclose($fh);
				$this->totalFilesScanned++;
				if(microtime(true) - $this->lastStatusTime > 1){
					$this->lastStatusTime = microtime(true);
					$this->writeScanningStatus();
				}
				
				$record->markComplete();
				$forkObj->forkIfNeeded();
			}
		}
		$this->writeScanningStatus();
		if ($options['scansEnabled_fileContentsGSB']) {
			wordfence::status(2, 'info', __('Asking Wordfence to check URLs against malware list.', 'wordfence'));
			$hooverResults = $this->urlHoover->getBaddies();
			if($this->urlHoover->errorMsg){
				$this->errorMsg = $this->urlHoover->errorMsg;
				return false;
			}
			$this->urlHoover->cleanup();
			
			foreach($hooverResults as $file => $hresults){
				$record = wordfenceMalwareScanFile::fileForPath($file);
				$dataForFile = $this->dataForFile($file, $this->path . $file);
	
				foreach($hresults as $result){
					if(preg_match('/wfBrowscapCache\.php$/', $file)){
						continue;
					}
					
					if (empty($result['URL'])) {
						continue; 
					}
					
					if ($result['badList'] == 'goog-malware-shavar') {
						$this->addResult(array(
							'type' => 'file',
							'severity' => 1,
							'ignoreP' => $this->path . $file,
							'ignoreC' => md5_file($this->path . $file),
							'shortMsg' => __('File contains suspected malware URL: ', 'wordfence') . esc_html($file),
							'longMsg' => sprintf(__('This file contains a suspected malware URL listed on Google\'s list of malware sites. Wordfence decodes %s when scanning files so the URL may not be visible if you view this file. The URL is: %s - More info available at <a href="http://safebrowsing.clients.google.com/safebrowsing/diagnostic?site=%s&client=googlechrome&hl=en-US" target="_blank" rel="noopener noreferrer">Google Safe Browsing diagnostic page</a>.', 'wordfence'), esc_html($this->patterns['word3']), esc_html($result['URL']), urlencode($result['URL'])),
							'data' => array_merge(array(
								'file' => $file,
								'shac' => $record->SHAC,
								'badURL' => $result['URL'],
								'gsb' => 'goog-malware-shavar',
								'highSense' => $options['scansEnabled_highSense'],
								'betaSigs' => wfConfig::get('betaThreatDefenseFeed'),
							), $dataForFile),
						));
					}
					else if ($result['badList'] == 'googpub-phish-shavar') {
						$this->addResult(array(
							'type' => 'file',
							'severity' => 1,
							'ignoreP' => $this->path . $file,
							'ignoreC' => md5_file($this->path . $file),
							'shortMsg' => __('File contains suspected phishing URL: ', 'wordfence') . esc_html($file),
							'longMsg' => __('This file contains a URL that is a suspected phishing site that is currently listed on Google\'s list of known phishing sites. The URL is: ', 'wordfence') . esc_html($result['URL']),
							'data' => array_merge(array(
								'file' => $file,
								'shac' => $record->SHAC,
								'badURL' => $result['URL'],
								'gsb' => 'googpub-phish-shavar',
								'highSense' => $options['scansEnabled_highSense'],
								'betaSigs' => wfConfig::get('betaThreatDefenseFeed'),
							), $dataForFile),
						));
					}
					else if ($result['badList'] == 'wordfence-dbl') {
						$this->addResult(array(
							'type' => 'file',
							'severity' => 1,
							'ignoreP' => $this->path . $file,
							'ignoreC' => md5_file($this->path . $file),
							'shortMsg' => __('File contains suspected malware URL: ', 'wordfence') . esc_html($file),
							'longMsg' => __('This file contains a URL that is currently listed on Wordfence\'s domain blacklist. The URL is: ', 'wordfence') . esc_html($result['URL']),
							'data' => array_merge(array(
								'file' => $file,
								'shac' => $record->SHAC,
								'badURL' => $result['URL'],
								'gsb' => 'wordfence-dbl',
								'highSense' => $options['scansEnabled_highSense'],
								'betaSigs' => wfConfig::get('betaThreatDefenseFeed'),
							), $dataForFile),
						));
					}
				}
			}
		}
		wfUtils::endProcessingFile();
		
		wordfence::status(4, 'info', __('Finalizing malware scan results', 'wordfence'));
		$hashesToCheck = array();
		foreach ($this->results as $r) {
			$hashesToCheck[] = $r['data']['shac'];
		}
		
		if (count($hashesToCheck) > 0) {
			$safeFiles = $this->isSafeFile($hashesToCheck);
			foreach ($this->results as $index => $value) {
				if (in_array($value['data']['shac'], $safeFiles)) {
					unset($this->results[$index]);
				}
			}
		}

		return $this->results;
	}

	protected function writeScanningStatus() {
		wordfence::status(2, 'info', sprintf(__('Scanned contents of %d additional files at %.2f per second', 'wordfence'), $this->totalFilesScanned, ($this->totalFilesScanned / (microtime(true) - $this->startTime))));
	}

	protected function addResult($result) {
		for ($i = 0; $i < sizeof($this->results); $i++) {
			if ($this->results[$i]['type'] == 'file' && $this->results[$i]['data']['file'] == $result['data']['file']) {
				if ($this->results[$i]['severity'] > $result['severity']) {
					$this->results[$i] = $result; //Overwrite with more severe results
				}
				return;
			}
		}
		//We don't have a results for this file so append
		$this->results[] = $result;
	}
	
	/**
	 * Queries the is_safe_file endpoint. If provided an array, it does a bulk check and returns an array containing the
	 * hashes that were marked as safe. If provided a string, it returns a boolean to indicate the safeness of the file.
	 *
	 * @param string|array $shac
	 * @return array|bool
	 */
	private function isSafeFile($shac) {
		if(! $this->api){
			$this->api = new wfAPI($this->apiKey, $this->wordpressVersion);
		}
		
		if (is_array($shac)) {
			$result = $this->api->call('is_safe_file', array(), array('multipleSHAC' => json_encode($shac)));
			if (isset($result['isSafe'])) {
				return $result['isSafe'];
			}
			return array();
		}
		
		$result = $this->api->call('is_safe_file', array(), array('shac' => strtoupper($shac)));
		if(isset($result['isSafe']) && $result['isSafe'] == 1){
			return true;
		}
		return false;
	}

	/**
	 * @param string $file
	 * @return array
	 */
	private function dataForFile($file, $fullPath = null) {
		$loader = $this->scanEngine->getKnownFilesLoader();
		$data = array();
		if ($isKnownFile = $loader->isKnownFile($file)) {
			if ($loader->isKnownCoreFile($file)) {
				$data['cType'] = 'core';

			} else if ($loader->isKnownPluginFile($file)) {
				$data['cType'] = 'plugin';
				list($itemName, $itemVersion, $cKey) = $loader->getKnownPluginData($file);
				$data = array_merge($data, array(
					'cName'    => $itemName,
					'cVersion' => $itemVersion,
					'cKey'     => $cKey
				));

			} else if ($loader->isKnownThemeFile($file)) {
				$data['cType'] = 'theme';
				list($itemName, $itemVersion, $cKey) = $loader->getKnownThemeData($file);
				$data = array_merge($data, array(
					'cName'    => $itemName,
					'cVersion' => $itemVersion,
					'cKey'     => $cKey
				));
			}
		}
		
		$suppressDelete = false;
		$canRegenerate = false;
		if ($fullPath !== null) {
			$bootstrapPath = wordfence::getWAFBootstrapPath();
			$htaccessPath = get_home_path() . '.htaccess';
			$userIni = ini_get('user_ini.filename');
			$userIniPath = false;
			if ($userIni) {
				$userIniPath = get_home_path() . $userIni;
			}
			
			if ($fullPath == $htaccessPath) {
				$suppressDelete = true;	
			}
			else if ($userIniPath !== false && $fullPath == $userIniPath) {
				$suppressDelete = true;
			}
			else if ($fullPath == $bootstrapPath) {
				$suppressDelete = true;
				$canRegenerate = true;
			}
		}
		
		$localFile = realpath($this->path . $file);
		$isWPConfig = $localFile === ABSPATH . 'wp-config.php';

		$data['canDiff'] = $isKnownFile;
		$data['canFix'] = $isKnownFile && !$isWPConfig;
		$data['canDelete'] = !$isKnownFile && !$canRegenerate && !$suppressDelete && !$isWPConfig;
		$data['canRegenerate'] = $canRegenerate && !$isWPConfig;
		$data['wpconfig'] = $isWPConfig;

		return $data;
	}
}

/**
 * Convenience class for interfacing with the wfFileMods table.
 * 
 * @property string $filename
 * @property string $filenameMD5
 * @property string $newMD5
 * @property string $SHAC
 * @property string $stoppedOnSignature
 * @property string $stoppedOnPosition
 * @property string $isSafeFile
 */
class wordfenceMalwareScanFile {
	protected $_filename;
	protected $_filenameMD5;
	protected $_newMD5;
	protected $_shac;
	protected $_stoppedOnSignature;
	protected $_stoppedOnPosition;
	protected $_isSafeFile;
	
	protected static function getDB() {
		static $db = null;
		if ($db === null) {
			$db = new wfDB();
		}
		return $db;
	}
	
	public static function countRemaining() {
		$db = self::getDB();
		return $db->querySingle("SELECT COUNT(*) FROM " . wfDB::networkPrefix() . "wfFileMods WHERE oldMD5 != newMD5 AND knownFile = 0");
	}
	
	public static function files($limit = 500) {
		$db = self::getDB();
		$result = $db->querySelect("SELECT filename, filenameMD5, HEX(newMD5) AS newMD5, HEX(SHAC) AS SHAC, stoppedOnSignature, stoppedOnPosition, isSafeFile FROM " . wfDB::networkPrefix() . "wfFileMods WHERE oldMD5 != newMD5 AND knownFile = 0 limit %d", $limit);
		$files = array();
		foreach ($result as $row) {
			$files[] = new wordfenceMalwareScanFile($row['filename'], $row['filenameMD5'], $row['newMD5'], $row['SHAC'], $row['stoppedOnSignature'], $row['stoppedOnPosition'], $row['isSafeFile']);
		}
		return $files;
	}
	
	public static function fileForPath($file) {
		$db = self::getDB();
		$row = $db->querySingleRec("SELECT filename, filenameMD5, HEX(newMD5) AS newMD5, HEX(SHAC) AS SHAC, stoppedOnSignature, stoppedOnPosition, isSafeFile FROM " . wfDB::networkPrefix() . "wfFileMods WHERE filename = '%s'", $file);
		return new wordfenceMalwareScanFile($row['filename'], $row['filenameMD5'], $row['newMD5'], $row['SHAC'], $row['stoppedOnSignature'], $row['stoppedOnPosition'], $row['isSafeFile']);
	}
	
	public function __construct($filename, $filenameMD5, $newMD5, $shac, $stoppedOnSignature, $stoppedOnPosition, $isSafeFile) {
		$this->_filename = $filename;
		$this->_filenameMD5 = $filenameMD5;
		$this->_newMD5 = $newMD5;
		$this->_shac = strtoupper($shac);
		$this->_stoppedOnSignature = $stoppedOnSignature;
		$this->_stoppedOnPosition = $stoppedOnPosition;
		$this->_isSafeFile = $isSafeFile;
	}
	
	public function __get($key) {
		switch ($key) {
			case 'filename':
				return $this->_filename;
			case 'filenameMD5':
				return $this->_filenameMD5;
			case 'newMD5':
				return $this->_newMD5;
			case 'SHAC':
				return $this->_shac;
			case 'stoppedOnSignature':
				return $this->_stoppedOnSignature;
			case 'stoppedOnPosition':
				return $this->_stoppedOnPosition;
			case 'isSafeFile':
				return $this->_isSafeFile;
		}
	}
	
	public function __toString() {
		return "Record [filename: {$this->filename}, filenameMD5: {$this->filenameMD5}, newMD5: {$this->newMD5}, stoppedOnSignature: {$this->stoppedOnSignature}, stoppedOnPosition: {$this->stoppedOnPosition}]";
	}
	
	public function markComplete() {
		$db = self::getDB();
		$db->queryWrite("UPDATE " . wfDB::networkPrefix() . "wfFileMods SET oldMD5 = newMD5 WHERE filenameMD5 = '%s'", $this->filenameMD5); //A way to mark as scanned so that if we come back from a sleep we don't rescan this one.
	}
	
	public function updateStoppedOn($signature, $position) {
		$this->_stoppedOnSignature = $signature;
		$this->_stoppedOnPosition = $position;
		$db = self::getDB();
		$db->queryWrite("UPDATE " . wfDB::networkPrefix() . "wfFileMods SET stoppedOnSignature = '%s', stoppedOnPosition = %d WHERE filenameMD5 = '%s'", $this->stoppedOnSignature, $this->stoppedOnPosition, $this->filenameMD5);
	}
	
	public function markSafe() {
		$db = self::getDB();
		$db->queryWrite("UPDATE " . wfDB::networkPrefix() . "wfFileMods SET isSafeFile = '1' WHERE filenameMD5 = '%s'", $this->filenameMD5);
		$this->isSafeFile = '1';
	}
	
	public function markUnsafe() {
		$db = self::getDB();
		$db->queryWrite("UPDATE " . wfDB::networkPrefix() . "wfFileMods SET isSafeFile = '0' WHERE filenameMD5 = '%s'", $this->filenameMD5);
		$this->isSafeFile = '0';
	}
}
