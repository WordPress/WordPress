<?php

class wfWAFStorageFile implements wfWAFStorageInterface {

	const LOG_FILE_HEADER = "<?php exit('Access denied'); __halt_compiler(); ?>\n";
	const LOG_INFO_HEADER = "******************************************************************\nThis file is used by the Wordfence Web Application Firewall. Read \nmore at https://docs.wordfence.com/en/Web_Application_Firewall_FAQ\n******************************************************************\n";
	const IP_BLOCK_RECORD_SIZE = 24;
	
	public static function allowFileWriting() {
		if (defined('WFWAF_ALWAYS_ALLOW_FILE_WRITING') && WFWAF_ALWAYS_ALLOW_FILE_WRITING) {
			return true;
		}
		
		$sapi = @php_sapi_name();
		if ($sapi == "cli") {
			return false;
		}
		return true;
	}

	public static function atomicFilePutContents($file, $content, $prefix = 'config') {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		$tmpFile = @tempnam(dirname($file), $prefix . '.tmp.');
		if (!$tmpFile) {
			$tmpFile = @tempnam(sys_get_temp_dir(), $prefix . '.tmp.');
		}
		if (!$tmpFile) {
			throw new wfWAFStorageFileException('Unable to save temporary file for atomic writing.');
		}
		$tmpHandle = @fopen($tmpFile, 'w');
		if (!$tmpHandle) {
			throw new wfWAFStorageFileException('Unable to save temporary file ' . $tmpFile . ' for atomic writing.');
		}

		self::lock($tmpHandle, LOCK_EX);
		fwrite($tmpHandle, $content);
		fflush($tmpHandle);
		self::lock($tmpHandle, LOCK_UN);
		fclose($tmpHandle);
		chmod($tmpFile, 0660); 

		// Attempt to verify file has finished writing (sometimes the disk will lie for better benchmarks)
		$tmpContents = file_get_contents($tmpFile);
		if ($tmpContents !== $content) {
			throw new wfWAFStorageFileException('Unable to verify temporary file contents for atomic writing.');
		}

		if (!@rename($tmpFile, $file)) {
			$backFile = @tempnam(dirname($file), $prefix . '.bak.');
			if (!$backFile) {
				$backFile = @tempnam(sys_get_temp_dir(), $prefix . '.bak.');
			}
			if (!$backFile) {
				throw new wfWAFStorageFileException('Unable to save temporary file for atomic writing.');
			}
			if (WFWAF_DEBUG) {
				rename($file, $backFile);
				rename($tmpFile, $file);
				unlink($backFile);
				unlink($tmpFile);
			} else {
				@rename($file, $backFile);
				@rename($tmpFile, $file);
				@unlink($backFile);
				@unlink($tmpFile);
			}
		}
	}

	public static function lock($handle, $lock, $wouldLock = 1) {
		$locked = flock($handle, $lock, $wouldLock);
		if (!$locked) {
			error_log('Lock not acquired ' . $locked);
		}
		return $locked;
	}

	/**
	 * @var resource
	 */
	private $ipCacheFileHandle;

	/**
	 * @var string|null
	 */
	private $attackDataFile;
	/**
	 * @var wfWAFAttackDataStorageFileEngine
	 */
	private $attackDataEngine;

	/**
	 * @var string|null
	 */
	private $ipCacheFile;
	private $configFile;
	private $rulesDSLCacheFile;
	private $dataChanged = false;
	private $data = false;
	/**
	 * @var resource
	 */
	private $configFileHandle;
	private $uninstalled;
	private $attackDataRows;
	private $attackDataNewerThan;


	/**
	 * @param string|null $attackDataFile
	 * @param string|null $ipCacheFile
	 * @param string|null $configFile
	 * @param null $rulesDSLCacheFile
	 */
	public function __construct($attackDataFile = null, $ipCacheFile = null, $configFile = null, $rulesDSLCacheFile = null) {
		$this->setAttackDataFile($attackDataFile);
		$this->setIPCacheFile($ipCacheFile);
		$this->setConfigFile($configFile);
		$this->setRulesDSLCacheFile($rulesDSLCacheFile);
	}

	/**
	 * @param float $olderThan
	 * @return bool
	 * @throws wfWAFStorageFileException
	 */
	public function hasPreviousAttackData($olderThan) {
		$this->open();
		$timestamp = $this->getAttackDataEngine()->getOldestTimestamp();
		return $timestamp && $timestamp < $olderThan;
	}

	/**
	 * @param float $newerThan
	 * @return bool
	 * @throws wfWAFStorageFileException
	 */
	public function hasNewerAttackData($newerThan) {
		$this->open();
		$timestamp = $this->getAttackDataEngine()->getNewestTimestamp();
		return $timestamp && $timestamp > $newerThan;
	}


	/**
	 * @return mixed|string|void
	 * @throws wfWAFStorageFileException
	 */
	public function getAttackData() {
		$this->open();
		$this->attackDataRows = array();
		$this->getAttackDataEngine()->scanRows(array($this, '_getAttackDataRowsSerialized'));
		return wfWAFUtils::json_encode($this->attackDataRows);
	}

	/**
	 * @return array
	 * @throws wfWAFStorageFileException
	 */
	public function getAttackDataArray() {
		$this->open();
		$this->attackDataRows = array();
		$this->getAttackDataEngine()->scanRows(array($this, '_getAttackDataRows'));
		return $this->attackDataRows;
	}

	/**
	 * @param resource $fileHandle
	 * @param int $offset
	 * @param int $length
	 */
	public function _getAttackDataRowsSerialized($fileHandle, $offset, $length) {
		fseek($fileHandle, $offset);
		self::lock($fileHandle, LOCK_SH);
		$binary = fread($fileHandle, $length);
		self::lock($fileHandle, LOCK_UN);
		$row = wfWAFAttackDataStorageFileEngineRow::unpack($binary);
		$data = wfWAFUtils::json_decode($row->getData(), true);
		if (is_array($data)) {
			array_unshift($data, $row->getTimestamp());
			$this->attackDataRows[] = $data;
		}
	}

	/**
	 * @param resource $fileHandle
	 * @param int $offset
	 * @param int $length
	 */
	public function _getAttackDataRows($fileHandle, $offset, $length) {
		fseek($fileHandle, $offset);
		self::lock($fileHandle, LOCK_SH);
		$binary = fread($fileHandle, $length);
		self::lock($fileHandle, LOCK_UN);
		$row = wfWAFAttackDataStorageFileEngineRow::unpack($binary);
		$data = $this->unserializeRow($row->getData());
		array_unshift($data, $row->getTimestamp());
		$this->attackDataRows[] = $data;
	}

	/**
	 * @param $newerThan
	 * @return array
	 * @throws wfWAFStorageFileException
	 */
	public function getNewestAttackDataArray($newerThan) {
		$this->open();
		$this->attackDataRows = array();
		$this->attackDataNewerThan = $newerThan;
		$this->getAttackDataEngine()->scanRowsReverse(array($this, '_getAttackDataRowsNewerThan'));
		return $this->attackDataRows;
	}

	/**
	 * @param resource $fileHandle
	 * @param int $offset
	 * @param int $length
	 * @return bool
	 */
	public function _getAttackDataRowsNewerThan($fileHandle, $offset, $length) {
		fseek($fileHandle, $offset);
		self::lock($fileHandle, LOCK_SH);
		$binaryTimestamp = fread($fileHandle, 8);
		self::lock($fileHandle, LOCK_UN);
		$timestamp = wfWAFAttackDataStorageFileEngine::unpackMicrotime($binaryTimestamp);
		if ($timestamp > $this->attackDataNewerThan) {
			$binary = $binaryTimestamp . fread($fileHandle, $length - 8);
			$row = wfWAFAttackDataStorageFileEngineRow::unpack($binary);
			$data = $this->unserializeRow($row->getData());
			if (is_array($data)) {
				array_unshift($data, $row->getTimestamp());
				$this->attackDataRows[] = $data;
			}
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 * @throws wfWAFStorageFileException
	 */
	public function truncateAttackData() {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		$this->open();
		$this->getAttackDataEngine()->truncate();
		return $this->getAttackDataEngine()->getRowCount() === 0;
	}

	/**
	 * @return bool
	 * @throws wfWAFStorageFileException
	 */
	public function isAttackDataFull() {
		$this->open();
		return $this->getAttackDataEngine()->getRowCount() === wfWAFAttackDataStorageFileEngine::MAX_ROWS;
	}

	/**
	 * @param array $failedRules
	 * @param string $failedParamKey
	 * @param string $failedParamValue
	 * @param wfWAFRequestInterface $request
	 * @param mixed $_
	 * @return mixed
	 */
	public function logAttack($failedRules, $failedParamKey, $failedParamValue, $request, $_ = null) {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		$this->open();
		$row = array(
			$request->getTimestamp(),
			$request->getIP(),
			(int) $this->isInLearningMode(),
			$failedParamKey,
			$failedParamValue,
		);

		$failedRulesString = '';
		if (is_array($failedRules)) {
			/**
			 * @var int $index
			 * @var wfWAFRule|int $rule
			 */
			foreach ($failedRules as $index => $rule) {
				if ($rule instanceof wfWAFRule) {
					$failedRulesString .= $rule->getRuleID() . '|';
				} else {
					$failedRulesString .= $rule . '|';
				}
			}
			$failedRulesString = wfWAFUtils::substr($failedRulesString, 0, -1);
		}
		$row[] = $failedRulesString;
		$row[] = $request->getProtocol() === 'https' ? 1 : 0;
		$row[] = (string) $request;
		$args = func_get_args();
		$row = array_merge($row, array_slice($args, 4));

		if (($rowString = $this->serializeRow($row)) !== false) {
			$attackRow = new wfWAFAttackDataStorageFileEngineRow(microtime(false), $rowString);
			$this->getAttackDataEngine()->addRow($attackRow);
		}
	}

	/**
	 * @param int $timestamp
	 * @param string $ip
	 * @return mixed|void
	 * @throws wfWAFStorageFileException
	 */
	public function blockIP($timestamp, $ip, $type = wfWAFStorageInterface::IP_BLOCKS_SINGLE) {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		$this->open();
		if (!$this->isIPBlocked($ip)) {
			self::lock($this->ipCacheFileHandle, LOCK_EX);
			fseek($this->ipCacheFileHandle, 0, SEEK_END);
			fwrite($this->ipCacheFileHandle, wfWAFUtils::inet_pton($ip) . pack('V', $timestamp) . pack('V', $type));
			fflush($this->ipCacheFileHandle);
			self::lock($this->ipCacheFileHandle, LOCK_UN);
		}
	}

	/**
	 * @param string $ip
	 * @return bool
	 */
	public function isIPBlocked($ip) {
		$this->open();
		$ipBin = wfWAFUtils::inet_pton($ip);
		fseek($this->ipCacheFileHandle, wfWAFUtils::strlen(self::LOG_FILE_HEADER), SEEK_SET);
		self::lock($this->ipCacheFileHandle, LOCK_SH);
		while (!feof($this->ipCacheFileHandle)) {
			$ipStr = fread($this->ipCacheFileHandle, self::IP_BLOCK_RECORD_SIZE);
			if (wfWAFUtils::strlen($ipStr) < self::IP_BLOCK_RECORD_SIZE) { break; }
			$ip2 = wfWAFUtils::substr($ipStr, 0, 16);
			$unpacked = @unpack('V', wfWAFUtils::substr($ipStr, 16, 4));
			if (is_array($unpacked)) {
				$t = array_shift($unpacked);
				if ($ipBin === $ip2 && $t >= time()) {
					self::lock($this->ipCacheFileHandle, LOCK_UN);
					return true;
				}
			}
		}
		self::lock($this->ipCacheFileHandle, LOCK_UN);
		return false;
	}

	/**
	 * @return bool
	 */
	public function isOpened() {
		return is_resource($this->configFileHandle);
	}

	/**
	 * @throws wfWAFStorageFileException
	 */
	public function open() {
		if ($this->isOpened()) {
			return;
		}
		if ($this->uninstalled) {
			throw new wfWAFStorageFileException('Unable to open WAF file storage, WAF has been uninstalled.');
		}

		$files = array(
			array($this->getIPCacheFile(), 'ipCacheFileHandle', self::LOG_FILE_HEADER),
			array($this->getConfigFile(), 'configFileHandle', self::LOG_FILE_HEADER . self::LOG_INFO_HEADER . serialize($this->getDefaultConfiguration())),
		);
		foreach ($files as $file) {
			list($filePath, $fileHandle, $defaultContents) = $file;
			if (!file_exists($filePath)) {
				@file_put_contents($filePath, $defaultContents, LOCK_EX);
			}
			@chmod($filePath, 0660);
			$this->$fileHandle = @fopen($filePath, 'r+');
			if (!$this->$fileHandle) {
				throw new wfWAFStorageFileException('Unable to open ' . $filePath . ' for reading and writing.');
			}
		}

		$this->setAttackDataEngine(new wfWAFAttackDataStorageFileEngine($this->getAttackDataFile()));
		$this->getAttackDataEngine()->open();
	}

	/**
	 *
	 */
	public function close() {
		if (!$this->isOpened()) {
			return;
		}
		fclose($this->ipCacheFileHandle);
		fclose($this->configFileHandle);
		$this->ipCacheFileHandle = null;
		$this->configFileHandle = null;
		$this->getAttackDataEngine()->close();
	}

	/**
	 * Clean up old expired IP blocks.
	 */
	public function vacuum() {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		$this->open();
		$readPointer = wfWAFUtils::strlen(self::LOG_FILE_HEADER);
		$writePointer = wfWAFUtils::strlen(self::LOG_FILE_HEADER);
		fseek($this->ipCacheFileHandle, $readPointer, SEEK_SET);
		self::lock($this->ipCacheFileHandle, LOCK_EX);
		$ipCacheRow = fread($this->ipCacheFileHandle, self::IP_BLOCK_RECORD_SIZE);
		while (!feof($this->ipCacheFileHandle)) {
			$unpacked = @unpack('V', wfWAFUtils::substr($ipCacheRow, 16, 4));
			if (is_array($unpacked)) {
				$expires = array_shift($unpacked);
				if ($expires >= time()) {
					fseek($this->ipCacheFileHandle, $writePointer, SEEK_SET);
					fwrite($this->ipCacheFileHandle, $ipCacheRow);
					$writePointer += self::IP_BLOCK_RECORD_SIZE;
				}
			}
			$readPointer += self::IP_BLOCK_RECORD_SIZE;
			fseek($this->ipCacheFileHandle, $readPointer, SEEK_SET);
			$ipCacheRow = fread($this->ipCacheFileHandle, self::IP_BLOCK_RECORD_SIZE);
		}
		ftruncate($this->ipCacheFileHandle, $writePointer);
		fflush($this->ipCacheFileHandle);
		self::lock($this->ipCacheFileHandle, LOCK_UN);
	}
	
	/**
	 * Remove all existing IP blocks.
	 */
	public function purgeIPBlocks($types = wfWAFStorageInterface::IP_BLOCKS_ALL) {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		$this->open();
		$readPointer = wfWAFUtils::strlen(self::LOG_FILE_HEADER);
		$writePointer = wfWAFUtils::strlen(self::LOG_FILE_HEADER);
		fseek($this->ipCacheFileHandle, $readPointer, SEEK_SET);
		self::lock($this->ipCacheFileHandle, LOCK_EX);
		if ($types !== wfWAFStorageInterface::IP_BLOCKS_ALL) {
			$ipCacheRow = fread($this->ipCacheFileHandle, self::IP_BLOCK_RECORD_SIZE);
			while (!feof($this->ipCacheFileHandle)) {
				$unpacked = @unpack('Vexpires/Vtype', wfWAFUtils::substr($ipCacheRow, 16, 8));
				if (is_array($unpacked)) {
					$type = $unpacked['type'];
					if (($type & $types) == 0) {
						fseek($this->ipCacheFileHandle, $writePointer, SEEK_SET);
						fwrite($this->ipCacheFileHandle, $ipCacheRow);
						$writePointer += self::IP_BLOCK_RECORD_SIZE;
					}
				}
				$readPointer += self::IP_BLOCK_RECORD_SIZE;
				fseek($this->ipCacheFileHandle, $readPointer, SEEK_SET);
				$ipCacheRow = fread($this->ipCacheFileHandle, self::IP_BLOCK_RECORD_SIZE);
			}
		}
		ftruncate($this->ipCacheFileHandle, $writePointer);
		fflush($this->ipCacheFileHandle);
		self::lock($this->ipCacheFileHandle, LOCK_UN);
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getConfig($key, $default = null) {
		if (!$this->data)
		{
			$this->fetchConfigData();
		}
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function setConfig($key, $value) {
		if (!$this->data)
		{
			$this->fetchConfigData();
		}
		if (!$this->dataChanged && (
				(array_key_exists($key, $this->data) && $this->data[$key] !== $value) ||
				!array_key_exists($key, $this->data)
			)
		)
		{
			$this->dataChanged = array($key, true);
			register_shutdown_function(array($this, 'saveConfig'));
		}
		$this->data[$key] = $value;
	}

	/**
	 * @param string $key
	 */
	public function unsetConfig($key) {
		if (!$this->data)
		{
			$this->fetchConfigData();
		}
		if (!$this->dataChanged && array_key_exists($key, $this->data))
		{
			$this->dataChanged = array($key, true);
			register_shutdown_function(array($this, 'saveConfig'));
		}
		unset($this->data[$key]);
	}

	/**
	 * @throws wfWAFStorageFileException
	 */
	public function fetchConfigData() {
		$this->configFileHandle = null;
		$this->open();
		self::lock($this->configFileHandle, LOCK_SH);
		$i = 0;
		// Attempt to read contents of the config file. This could be in the middle of a write, so we account for it and
		// wait for the operation to complete.
		fseek($this->configFileHandle, wfWAFUtils::strlen(self::LOG_FILE_HEADER), SEEK_SET);
		$serializedData = '';
		while (!feof($this->configFileHandle)) {
			$serializedData .= fread($this->configFileHandle, 1024);
		}
		if (wfWAFUtils::substr($serializedData, 0, 1) == '*') {
			$serializedData = wfWAFUtils::substr($serializedData, wfWAFUtils::strlen(self::LOG_INFO_HEADER));
		}
		$this->data = @unserialize($serializedData);

		if ($this->data === false) {
			throw new wfWAFStorageFileConfigException('Error reading Wordfence Firewall config data, configuration file could be corrupted or inaccessible. Path: ' . $this->getConfigFile());
		}

		self::lock($this->configFileHandle, LOCK_UN);
	}

	/**
	 * @throws wfWAFStorageFileException
	 */
	public function saveConfig() {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		if (WFWAF_DEBUG) {
			error_log('Saving WAF config for change in key ' . $this->dataChanged[0] . ', value: ' .
				((is_object($this->data[$this->dataChanged[0]]) || $this->dataChanged[0] === 'cron') ?
					gettype($this->data[$this->dataChanged[0]]) :
					var_export($this->data[$this->dataChanged[0]], true)));
		}

		if ($this->uninstalled) {
			return;
		}

		if (WFWAF_IS_WINDOWS) {
			self::lock($this->configFileHandle, LOCK_UN);
			fclose($this->configFileHandle);
			file_put_contents($this->getConfigFile(), self::LOG_FILE_HEADER . self::LOG_INFO_HEADER . serialize($this->data), LOCK_EX);
		} else {
			wfWAFStorageFile::atomicFilePutContents($this->getConfigFile(), self::LOG_FILE_HEADER . self::LOG_INFO_HEADER . serialize($this->data));
		}

		if (WFWAF_IS_WINDOWS) {
			$this->configFileHandle = fopen($this->getConfigFile(), 'r+');
		}
	}

	/**
	 *
	 */
	public function uninstall() {
		$this->uninstalled = true;
		$this->close();
		@unlink($this->getConfigFile());
		@unlink($this->getAttackDataFile());
		@unlink($this->getIPCacheFile());
		@unlink($this->getRulesDSLCacheFile());
	}

	/**
	 * @return bool
	 */
	public function isInLearningMode() {
		if ($this->getConfig('wafStatus', '') == 'learning-mode') {
			if ($this->getConfig('learningModeGracePeriodEnabled', false)) {
				if ($this->getConfig('learningModeGracePeriod', 0) > time()) {
					return true;
				} else {
					// Reached the end of the grace period, activate the WAF.
					$this->setConfig('wafStatus', 'enabled');
					$this->setConfig('learningModeGracePeriodEnabled', 0);
					$this->unsetConfig('learningModeGracePeriod');
				}
			} else {
				return true;
			}
		}
		return false;
	}

	public function isDisabled() {
		return $this->getConfig('wafStatus', '') === 'disabled' || $this->getConfig('wafDisabled', 0);
	}

	/**
	 * @return array
	 */
	public function getDefaultConfiguration() {
		return array(
			'wafStatus'                      => 'learning-mode',
			'learningModeGracePeriodEnabled' => 1,
			'learningModeGracePeriod'        => time() + (86400 * 7),
			'authKey'                        => wfWAFUtils::getRandomString(64),
		);
	}

	/**
	 * @return mixed
	 */
	public function getConfigFile() {
		return $this->configFile;
	}

	/**
	 * @param mixed $configFile
	 */
	public function setConfigFile($configFile) {
		$this->configFile = $configFile;
	}

	/**
	 * @return string|null
	 */
	public function getAttackDataFile() {
		return $this->attackDataFile;
	}

	/**
	 * @param string|null $attackDataFile
	 */
	public function setAttackDataFile($attackDataFile) {
		$this->attackDataFile = $attackDataFile;
	}

	/**
	 * @return string|null
	 */
	public function getIPCacheFile() {
		return $this->ipCacheFile;
	}

	/**
	 * @param string|null $ipCacheFile
	 */
	public function setIPCacheFile($ipCacheFile) {
		$this->ipCacheFile = $ipCacheFile;
	}

	/**
	 * @return mixed
	 */
	public function getRulesDSLCacheFile() {
		return $this->rulesDSLCacheFile;
	}

	/**
	 * @param mixed $rulesDSLCacheFile
	 */
	public function setRulesDSLCacheFile($rulesDSLCacheFile) {
		$this->rulesDSLCacheFile = $rulesDSLCacheFile;
	}

	/**
	 * param key, param value, request string
	 *
	 * @var array
	 */
	private $rowsToB64 = array(3, 4, 7);

	/**
	 * @param $row
	 * @return bool|string
	 */
	private function serializeRow($row) {
		foreach ($this->rowsToB64 as $index) {
			if (array_key_exists($index, $row)) {
				$row[$index] = base64_encode($row[$index]);
			}
		}
		$row = wfWAFUtils::json_encode($row);
		if (is_string($row) && wfWAFUtils::strlen($row) > 0) {
			return $row;
		}
		return false;
	}

	/**
	 * @param $row
	 * @return array|bool|mixed|object
	 */
	private function unserializeRow($row) {
		if ($row) {
			$json = wfWAFUtils::json_decode($row, true);
			if (is_array($json)) {
				foreach ($this->rowsToB64 as $index) {
					if (array_key_exists($index, $json)) {
						$json[$index] = base64_decode($json[$index]);
					}
				}
				return $json;
			}
		}
		return false;
	}

	/**
	 * @return wfWAFAttackDataStorageFileEngine
	 */
	public function getAttackDataEngine() {
		return $this->attackDataEngine;
	}

	/**
	 * @param wfWAFAttackDataStorageFileEngine $attackDataEngine
	 */
	public function setAttackDataEngine($attackDataEngine) {
		$this->attackDataEngine = $attackDataEngine;
	}
}

class wfWAFAttackDataStorageFileEngine {

	const MAX_ROWS = 10000;
	const MAX_READ_LENGTH = 51200;
	const FILE_SIGNATURE = "wfWAF\x00\x00\x00";

	/**
	 * @param string|float|null $microtime
	 * @return string
	 */
	public static function packMicrotime($microtime = null) {
		if ($microtime === null) {
			$microtime = microtime();
		}
		if (is_string($microtime)) {
			list($msec, $sec) = explode(' ', $microtime, 2);
		} else if (is_float($microtime)) {
			list($sec, $msec) = explode('.', (string) $microtime, 2);
			$msec = '0.' . $msec;
		} else {
			throw new InvalidArgumentException(__METHOD__ . ' $microtime expected to be string or float, received '
				. gettype($microtime));
		}
		$msec = $msec * 1000000;
		return pack('V*', $sec, $msec);
	}

	/**
	 * @param string $binary
	 * @return string
	 */
	public static function unpackMicrotime($binary) {
		if (!is_string($binary) || wfWAFUtils::strlen($binary) !== 8) {
			throw new InvalidArgumentException(__METHOD__ . ' $binary expected to be string with length of 8, received '
				. gettype($binary) . (is_string($binary) ? ' of length ' . wfWAFUtils::strlen($binary) : ''));
		}
		list(, $attackLogSeconds, $attackLogMicroseconds) = @unpack('V*', $binary);
		return sprintf('%d.%s', $attackLogSeconds, str_pad($attackLogMicroseconds, 6, '0', STR_PAD_LEFT));
	}

	public static function getCompressionAlgos() {
		static $compressionFunctions;
		if ($compressionFunctions === null) {
			$compressionFunctions = array(
				new wfWAFStorageFileCompressionGZDeflate(),
				new wfWAFStorageFileCompressionGZCompress(),
				new wfWAFStorageFileCompressionGZEncode(),
			);
		}
		return $compressionFunctions;
	}

	/**
	 * @param string $decompressed
	 * @return mixed
	 */
	public static function compress($decompressed) {
		if (empty($decompressed))
			return $decompressed;

		$compressionAlgos = self::getCompressionAlgos();
		/** @var wfWAFStorageFileCompressionAlgo $algo */
		foreach ($compressionAlgos as $algo) {
			if ($algo->isUsable() && ($compressed = $algo->testCompression($decompressed)) !== false) {
				return $compressed;
			}
		}
		return $decompressed;
	}

	/**
	 * @param string $compressed
	 * @return mixed
	 */
	public static function decompress($compressed) {
		if (empty($compressed))
			return $compressed;

		$compressionAlgos = self::getCompressionAlgos();
		/** @var wfWAFStorageFileCompressionAlgo $algo */
		foreach ($compressionAlgos as $algo) {
			if ($algo->isUsable() && ($decompressed = $algo->decompress($compressed)) !== false) {
				return $decompressed;
			}
		}
		return $compressed;
	}


	private $file;
	private $fileHandle;

	private $header = array();
	private $offsetTable = array();

	/**
	 * wfWAFStorageFileEngine constructor.
	 * @param string $file
	 */
	public function __construct($file) {
		$this->file = $file;
	}

	/**
	 * @throws wfWAFStorageFileException
	 */
	public function open() {
		if (is_resource($this->fileHandle)) {
			return;
		}
		if (!file_exists($this->file)) {
			@file_put_contents($this->file, $this->getDefaultHeader(), LOCK_EX);
		}
		@chmod($this->file, 0660);
		$this->fileHandle = @fopen($this->file, 'r+');
		if (!$this->fileHandle) {
			throw new wfWAFStorageFileException('Unable to open ' . $this->file . ' for reading and writing.');
		}
	}

	/**
	 *
	 */
	public function close() {
		if (is_resource($this->fileHandle)) {
			fclose($this->fileHandle);
		}
		$this->fileHandle = null;
		$this->header = array();
		$this->offsetTable = array();
	}

	/**
	 * @param int $offset
	 * @return int
	 */
	private function seek($offset) {
		return fseek($this->fileHandle, $offset, SEEK_SET);
	}

	/**
	 * @return int
	 */
	private function seekToData() {
		return $this->seek(wfWAFUtils::strlen($this->getHeaderLength()));
	}

	/**
	 * @param int $length
	 * @return string
	 */
	private function read($length) {
		if ($length > self::MAX_READ_LENGTH) {
			$length = self::MAX_READ_LENGTH;
		}
		return fread($this->fileHandle, $length);
	}

	/**
	 * @param string $data
	 * @return int
	 */
	private function write($data) {
		return fwrite($this->fileHandle, $data);
	}

	/**
	 * @return bool
	 */
	private function lockRead() {
		return wfWAFStorageFile::lock($this->fileHandle, LOCK_SH);
	}

	/**
	 * @return bool
	 */
	private function lockWrite() {
		return wfWAFStorageFile::lock($this->fileHandle, LOCK_EX);
	}

	/**
	 * @return bool
	 */
	private function unlock() {
		return wfWAFStorageFile::lock($this->fileHandle, LOCK_UN);
	}

	/**
	 * @return int
	 */
	public function getHeaderLength() {
		return wfWAFUtils::strlen($this->getDefaultHeader());
	}

	/**
	 * @return string
	 */
	public function getDefaultHeader() {
		/**
		 * 51   PHP die() header
		 * 8    Signature
		 * 8    oldest 64bit timestamp
		 * 8    newest 64bit timestamp
		 * 4    row count
		 * 1600 offset table
		 * 1    last length
		 */
		$headerLength = wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE)
			+ 8 + 8 + 4 + (self::MAX_ROWS * 4);
		return wfWAFStorageFile::LOG_FILE_HEADER
		. self::FILE_SIGNATURE
		. str_repeat("\x00", 8 + 8 + 4)
		. pack('V', $headerLength)
		. str_repeat("\x00", self::MAX_ROWS * 4);
	}

	/**
	 * @throws wfWAFStorageFileException
	 */
	public function unpackHeader() {
		if ($this->header) {
			return $this->header;
		}

		$this->open();
		$this->header = array();
		$this->seek(0);
		$this->lockRead();
		$this->header['phpHeader'] = $this->read(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER));
		$this->header['signature'] = $this->read(wfWAFUtils::strlen(self::FILE_SIGNATURE));
		if ($this->header['phpHeader'] !== wfWAFStorageFile::LOG_FILE_HEADER || $this->header['signature'] !== self::FILE_SIGNATURE) {
			$this->unlock();
			$this->truncate();
			$this->lockRead();
			$this->seek(0);
			$this->lockRead();
			$this->header['phpHeader'] = $this->read(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER));
			$this->header['signature'] = $this->read(wfWAFUtils::strlen(self::FILE_SIGNATURE));
		}
		$this->header['oldestTimestamp'] = self::unpackMicrotime($this->read(8));
		$this->header['newestTimestamp'] = self::unpackMicrotime($this->read(8));
		list(, $this->header['rowCount']) = @unpack('V', $this->read(4));
		$this->header['offsetTable'] = $this->unpackOffsetTable();
		$this->unlock();
		return $this->header;
	}

	/**
	 * @return array
	 */
	private function unpackOffsetTable() {
		if ($this->offsetTable) {
			return $this->offsetTable;
		}
		$rowCount = min($this->header['rowCount'], self::MAX_ROWS);
		$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE) + 8 + 8 + 4);
		$offsetTableBinary = $this->read(($rowCount + 1) * 4);
		$this->offsetTable = array_values(@unpack('V*', $offsetTableBinary));
		return $this->offsetTable;
	}

	/**
	 * @param callable $callback
	 */
	public function scanRows($callback) {
		if (!is_callable($callback)) {
			throw new InvalidArgumentException(__METHOD__ . ' $callback expected to be callable, received ' . gettype($callback));
		}
		$this->open();
		$header = $this->unpackHeader();
		$this->seekToData();
		for ($index = 0; $index < $header['rowCount'] && $index < self::MAX_ROWS; $index++) {
			$offset = $header['offsetTable'][$index];
			$length = $header['offsetTable'][$index + 1] - $offset;
			if ($length > self::MAX_READ_LENGTH) {
				$length = self::MAX_READ_LENGTH;
			}
			$result = call_user_func($callback, $this->fileHandle, $offset, $length);
			if ($result === false) {
				break;
			}
		}
	}

	/**
	 * @param callable $callback
	 */
	public function scanRowsReverse($callback) {
		if (!is_callable($callback)) {
			throw new InvalidArgumentException(__METHOD__ . ' $callback expected to be callable, received ' . gettype($callback));
		}
		$this->open();
		$header = $this->unpackHeader();
		// $this->seekToData();
		for ($index = min($header['rowCount'], self::MAX_ROWS) - 1; $index >= 0; $index--) {
			$offset = $header['offsetTable'][$index];
			$length = $header['offsetTable'][$index + 1] - $offset;
			if ($length > self::MAX_READ_LENGTH) {
				$length = self::MAX_READ_LENGTH;
			}
			$result = call_user_func($callback, $this->fileHandle, $offset, $length);
			if ($result === false) {
				break;
			}
		}
	}

	/**
	 * @param $index
	 * @return wfWAFAttackDataStorageFileEngineRow
	 * @throws wfWAFStorageFileException
	 */
	public function getRow($index) {
		$this->open();
		$this->header = array();
		$this->offsetTable = array();
		$header = $this->unpackHeader();
		$this->seekToData();
		if ($index < $header['rowCount'] && $index >= 0) {
			$offset = $header['offsetTable'][$index];
			$length = $header['offsetTable'][$index + 1] - $offset;
		} else {
			return false;
		}
		$this->seek($offset);
		$this->lockRead();
		$binary = $this->read($length);
		$this->unlock();
		return wfWAFAttackDataStorageFileEngineRow::unpack($binary);
	}

	/**
	 * @return mixed
	 * @throws wfWAFStorageFileException
	 */
	public function getRowCount() {
		$this->open();
		$header = $this->unpackHeader();
		return $header['rowCount'];
	}

	/**
	 * @param wfWAFAttackDataStorageFileEngineRow $row
	 * @return bool
	 * @throws wfWAFStorageFileException
	 */
	public function addRow($row) {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		$this->open();

		$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE) + 8 + 8);
		$this->lockRead();
		list(, $rowCount) = @unpack('V', $this->read(4));
		$this->unlock();
		if ($rowCount >= self::MAX_ROWS) {
			return false;
		}
		
		$this->lockWrite();
		
		//Re-read the row count in case it changed between releasing the shared lock and getting the exclusive
		$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE) + 8 + 8);
		list(, $rowCount) = @unpack('V', $this->read(4));

		//Start the write
		$this->header = array();
		$this->offsetTable = array();

		$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE) + 8 + 8 + 4 + ($rowCount * 4));
		list(, $nextRowOffset) = @unpack('V', $this->read(4));

		$rowString = $row->pack();

		// Update offset table
		$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE) + 8 + 8 + 4 + (($rowCount + 1) * 4));
		$this->write(pack('V', $nextRowOffset + wfWAFUtils::strlen($rowString)));

		// Update rowCount
		$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE) + 8 + 8);
		$this->write(pack('V', $rowCount + 1));

		// Write data
		$this->seek($nextRowOffset);
		$packedTimestamp = wfWAFUtils::substr($rowString, 0, 8);
		$this->write($rowString);

		// Update oldest timestamp
		if ($rowCount === 0) {
			$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE));
			$this->write($packedTimestamp);
		}

		// Update newest timestamp
		$this->seek(wfWAFUtils::strlen(wfWAFStorageFile::LOG_FILE_HEADER) + wfWAFUtils::strlen(self::FILE_SIGNATURE) + 8);
		$this->write($packedTimestamp);

		$this->unlock();

		$this->header = array();
		$this->offsetTable = array();

		return true;
	}

	/**
	 *
	 */
	public function truncate() {
		if (!wfWAFStorageFile::allowFileWriting()) { return false; }
		
		$defaultHeader = $this->getDefaultHeader();
		$this->close();
		if (WFWAF_IS_WINDOWS) {
			file_put_contents($this->getFile(), $defaultHeader, LOCK_EX);
		} else {
			wfWAFStorageFile::atomicFilePutContents($this->getFile(), $defaultHeader, 'attack');
		}
		$this->header = array();
		$this->offsetTable = array();
		$this->open();
	}

	/**
	 * @return mixed
	 * @throws wfWAFStorageFileException
	 */
	public function getOldestTimestamp() {
		$this->open();
		if ($this->getRowCount() === 0) {
			return false;
		}
		$header = $this->unpackHeader();
		return $header['oldestTimestamp'];
	}

	/**
	 * @return mixed
	 * @throws wfWAFStorageFileException
	 */
	public function getNewestTimestamp() {
		$this->open();
		if ($this->getRowCount() === 0) {
			return false;
		}
		$header = $this->unpackHeader();
		return $header['newestTimestamp'];
	}

	/**
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @param string $file
	 */
	public function setFile($file) {
		$this->file = $file;
	}
}

interface wfWAFAttackDataStorageFileEngineScanRowCallback {

	public function scanRow($handle, $offset, $length);
}

class wfWAFAttackDataStorageFileEngineResultSet implements wfWAFAttackDataStorageFileEngineScanRowCallback {

	private $rows = array();

	public function scanRow($handle, $offset, $length) {
		fseek($handle, $offset);
		$binary = fread($handle, $length);
		$this->rows = wfWAFAttackDataStorageFileEngineRow::unpack($binary);
	}

	/**
	 * @return array
	 */
	public function getRows() {
		return $this->rows;
	}
}

class wfWAFAttackDataStorageFileEngineScanRowAttackDataNewer implements wfWAFAttackDataStorageFileEngineScanRowCallback {

	/**
	 * @var int
	 */
	private $newerThan;

	/**
	 * wfWAFStorageFileEngineScanRowAttackDataNewer constructor.
	 * @param int $newerThan
	 */
	public function __construct($newerThan) {
		$this->newerThan = $newerThan;
	}

	/**
	 * @param resource $handle
	 * @param int $offset
	 * @param int $length
	 * @return bool
	 */
	public function scanRow($handle, $offset, $length) {
		$attackLogTimeBin = fread($handle, 8);
		list(, $attackLogSeconds, $attackLogMicroseconds) = @unpack('VV', $attackLogTimeBin);
		$attackLogTime = $attackLogSeconds . '.' . $attackLogMicroseconds;
		return $this->newerThan < $attackLogTime;
	}
}

class wfWAFAttackDataStorageFileEngineScanRowAttackDataOlder implements wfWAFAttackDataStorageFileEngineScanRowCallback {

	/**
	 * @var int
	 */
	private $olderThan;

	/**
	 * wfWAFStorageFileEngineScanRowAttackDataNewer constructor.
	 * @param int $olderThan
	 */
	public function __construct($olderThan) {
		$this->olderThan = $olderThan;
	}

	/**
	 * @param resource $handle
	 * @param int $offset
	 * @param int $length
	 * @return bool
	 */
	public function scanRow($handle, $offset, $length) {
		$attackLogTimeBin = fread($handle, 8);
		list(, $attackLogSeconds, $attackLogMicroseconds) = @unpack('VV', $attackLogTimeBin);
		$attackLogTime = $attackLogSeconds . '.' . $attackLogMicroseconds;
		return $this->olderThan > $attackLogTime;
	}
}

class wfWAFAttackDataStorageFileEngineRow {

	/**
	 * @param string $binary
	 * @return wfWAFAttackDataStorageFileEngineRow
	 */
	public static function unpack($binary) {
		$attackLogTime = wfWAFAttackDataStorageFileEngine::unpackMicrotime(wfWAFUtils::substr($binary, 0, 8));
		$data = wfWAFAttackDataStorageFileEngine::decompress(wfWAFUtils::substr($binary, 8));
		return new self($attackLogTime, $data);
	}

	/**
	 * @var float|string
	 */
	private $timestamp;
	/**
	 * @var string
	 */
	private $data;

	/**
	 * @param float $timestamp
	 * @param string $data
	 */
	public function __construct($timestamp, $data) {
		$this->timestamp = $timestamp;
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function pack() {
		return wfWAFAttackDataStorageFileEngine::packMicrotime($this->getTimestamp()) . wfWAFAttackDataStorageFileEngine::compress($this->getData());
	}

	/**
	 * @return float|string
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}

	/**
	 * @param float|string $timestamp
	 */
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	/**
	 * @return string
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param string $data
	 */
	public function setData($data) {
		$this->data = $data;
	}
}

abstract class wfWAFStorageFileCompressionAlgo {

	abstract public function isUsable();

	abstract public function compress($string);

	abstract public function decompress($binary);

	/**
	 * @param string $string
	 * @return bool
	 */
	public function testCompression($string) {
		$compressed = $this->compress($string);
		if ($string === $this->decompress($compressed)) {
			return $compressed;
		}
		return false;
	}
}

class wfWAFStorageFileCompressionGZDeflate extends wfWAFStorageFileCompressionAlgo {

	public function isUsable() {
		return function_exists('gzinflate') && function_exists('gzdeflate');
	}

	public function compress($string) {
		return @gzdeflate($string);
	}

	public function decompress($binary) {
		return @gzinflate($binary);
	}
}

class wfWAFStorageFileCompressionGZCompress extends wfWAFStorageFileCompressionAlgo {

	public function isUsable() {
		return function_exists('gzuncompress') && function_exists('gzcompress');
	}

	public function compress($string) {
		return @gzcompress($string);
	}

	public function decompress($binary) {
		return @gzuncompress($binary);
	}
}

class wfWAFStorageFileCompressionGZEncode extends wfWAFStorageFileCompressionAlgo {

	public function isUsable() {
		return function_exists('gzencode') && function_exists('gzdecode');
	}

	public function compress($string) {
		return @gzencode($string);
	}

	public function decompress($binary) {
		return @gzdecode($binary);
	}
}

class wfWAFStorageFileException extends wfWAFException {

}

class wfWAFStorageFileConfigException extends wfWAFStorageFileException {

}

