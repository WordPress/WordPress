<?php
require_once('wfAPI.php');
require_once('wfArray.php');
class wordfenceURLHoover {
	private $debug = false;
	public $errorMsg = false;
	private $hostsToAdd = false;
	private $table = '';
	private $apiKey = false;
	private $wordpressVersion = false;
	private $useDB = true;
	private $hostKeys = array();
	private $hostList = array();
	public $currentHooverID = false;
	private $_foundSome = false;
	private $_excludedHosts = array();
	private $api = false;
	private $db = false;
	
	public static function standardExcludedHosts() {
		static $standardExcludedHosts = null;
		if ($standardExcludedHosts !== null) {
			return $standardExcludedHosts;
		}
		
		global $wpdb;
		$excludedHosts = array();
		if (is_multisite()) {
			$blogIDs = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}"); //Can't use wp_get_sites or get_sites because they return empty at 10k sites
			foreach ($blogIDs as $id) {
				$homeURL = get_home_url($id);
				$host = parse_url($homeURL, PHP_URL_HOST);
				if ($host) {
					$excludedHosts[$host] = 1;
				}
				$siteURL = get_site_url($id);
				$host = parse_url($siteURL, PHP_URL_HOST);
				if ($host) {
					$excludedHosts[$host] = 1;
				}
			}
		}
		else {
			$homeURL = wfUtils::wpHomeURL();
			$host = parse_url($homeURL, PHP_URL_HOST);
			if ($host) {
				$excludedHosts[$host] = 1;
			}
			$siteURL = wfUtils::wpSiteURL();
			$host = parse_url($siteURL, PHP_URL_HOST);
			if ($host) {
				$excludedHosts[$host] = 1;
			}
		}
		
		$standardExcludedHosts = array_keys($excludedHosts);
		return $standardExcludedHosts;
	}
	
	public function __sleep() {
		$this->writeHosts();	
		return array('debug', 'errorMsg', 'table', 'apiKey', 'wordpressVersion');
	}
	
	public function __wakeup() {
		$this->hostsToAdd = new wfArray(array('owner', 'host', 'path', 'hostKey'));
		$this->api = new wfAPI($this->apiKey, $this->wordpressVersion);
		$this->db = new wfDB();
	}
	
	public function __construct($apiKey, $wordpressVersion, $db = false, $continuation = false) {
		$this->hostsToAdd = new wfArray(array('owner', 'host', 'path', 'hostKey'));
		$this->apiKey = $apiKey;
		$this->wordpressVersion = $wordpressVersion;
		$this->api = new wfAPI($apiKey, $wordpressVersion);
		if($db){
			$this->db = $db;
		} else {
			$this->db = new wfDB();
		}
		global $wpdb;
		if(isset($wpdb)){
			$this->table = wfDB::networkTable('wfHoover');
		} else {
			$this->table = 'wp_wfHoover';
		}
		
		if (!$continuation) {
			$this->cleanup();
		}
	}
	
	public function cleanup() {
		$this->db->truncate($this->table);
	}
	
	public function hoover($id, $data, $excludedHosts = array()) {
		$this->currentHooverID = $id;
		$this->_foundSome = 0;
		$this->_excludedHosts = $excludedHosts;
		@preg_replace_callback('/\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/i', array($this, 'captureURL'), $data);
		$this->writeHosts();
		return $this->_foundSome;
	}
	
	private function dbg($msg) { 
		if ($this->debug) { wordfence::status(4, 'info', $msg); } 
	}
	
	public function captureURL($matches) {
		$id = $this->currentHooverID;
		$url = $matches[0];
		$components = parse_url($url);
		if (!isset($components['scheme']) || !preg_match('/^https?$/i', $components['scheme'])) {
			return;
		}
		foreach ($this->_excludedHosts as $h) {
			if (strcasecmp($h, $components['host']) === 0) {
				return;
			}
		}
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			return;
		}
		
		$this->_foundSome++;
		
		$host = (isset($components['host']) ? $components['host'] : '');
		$path = (isset($components['path']) && !empty($components['path']) ? $components['path'] : '/');
		$hashes = $this->_generateHashes($url);
		foreach ($hashes as $h) {
			$this->hostsToAdd->push(array('owner' => $id, 'host' => $host, 'path' => $path, 'hostKey' => wfUtils::substr($h, 0, 4)));
		}
		
		if($this->hostsToAdd->size() > 1000){ $this->writeHosts(); }
	}
	
	private function writeHosts() {
		if ($this->hostsToAdd->size() < 1) { return; }
		if ($this->useDB) {
			$sql = "INSERT INTO " . $this->table . " (owner, host, path, hostKey) VALUES ";
			while ($elem = $this->hostsToAdd->shift()) {
				//This may be an issue for hyperDB or other abstraction layers, but leaving it for now.
				$sql .= sprintf("('%s', '%s', '%s', '%s'),", 
						$this->db->realEscape($elem['owner']),
						$this->db->realEscape($elem['host']),
						$this->db->realEscape($elem['path']),
						$this->db->realEscape($elem['hostKey'])
								);
			}
			$sql = rtrim($sql, ',');
			$this->db->queryWrite($sql);
			$this->hostsToAdd->collectGarbage();
		}
		else {
			while ($elem = $this->hostsToAdd->shift()) {
				$keys = str_split($elem['hostKey'], 4);
				foreach ($keys as $k) {
					$this->hostKeys[] = $k;
				}
				$this->hostList[] = array(
					'owner' => $elem['owner'],
					'host' => $elem['host'],
					'path' => $elem['path'],
					'hostKey' => $elem['hostKey']
					);
			}
			$this->hostsToAdd->collectGarbage();
		}
	}
	public function getBaddies() {
		wordfence::status(4, 'info', "Gathering host keys.");
		$allHostKeys = '';
		if ($this->useDB) {
			global $wpdb;
			$dbh = $wpdb->dbh;
			$useMySQLi = (is_object($dbh) && $wpdb->use_mysqli);
			if ($useMySQLi) { //If direct-access MySQLi is available, we use it to minimize the memory footprint instead of letting it fetch everything into an array first
				wordfence::status(4, 'info', "Using MySQLi directly.");
				$result = $dbh->query("SELECT DISTINCT hostKey FROM {$this->table} ORDER BY hostKey ASC LIMIT 100000"); /* We limit to 100,000 prefixes since more than that cannot be reliably checked within the default max_execution_time */
				if (!is_object($result)) {
					$this->errorMsg = "Unable to query database";
					$this->dbg($this->errorMsg);
					return false;
				}
				while ($row = $result->fetch_assoc()) {
					$allHostKeys .= $row['hostKey'];
				}
			}
			else {
				$q1 = $this->db->querySelect("SELECT DISTINCT hostKey FROM {$this->table} ORDER BY hostKey ASC LIMIT 100000"); /* We limit to 100,000 prefixes since more than that cannot be reliably checked within the default max_execution_time */
				foreach ($q1 as $hRec) {
					$allHostKeys .= $hRec['hostKey'];
				}
			}
		}
		else {
			$allHostKeys = implode('', array_values(array_unique($this->hostKeys)));
		}
		
		/**
		 * Check hash prefixes first. Each one is a 4-byte binary prefix of a SHA-256 hash of the URL. The response will
		 * be a binary list of 4-byte indices; The full URL for each index should be sent in the secondary query to
		 * find the true good/bad status.
		 */
		
		$allCount = wfUtils::strlen($allHostKeys) / 4;
		if ($allCount > 0) {
			if ($this->debug) {
				$this->dbg("Checking {$allCount} hostkeys");
				for ($i = 0; $i < $allCount; $i++) {
					$key = wfUtils::substr($allHostKeys, $i * 4, 4);
					$this->dbg("Checking hostkey: " . bin2hex($key));
				}
			}
			
			wordfence::status(2, 'info', "Checking {$allCount} host keys against Wordfence scanning servers.");
			$resp = $this->api->binCall('check_host_keys', $allHostKeys);
			wordfence::status(2, 'info', "Done host key check.");
			$this->dbg("Done host key check");

			$badHostKeys = '';
			if ($resp['code'] >= 200 && $resp['code'] <= 299) {
				$this->dbg("Host key response: " . bin2hex($resp['data']));
				$dataLen = wfUtils::strlen($resp['data']);
				if ($dataLen > 0 && $dataLen % 2 == 0) {
					$this->dbg("Checking response indexes");
					for ($i = 0; $i < $dataLen; $i += 2) {
						$idx = wfUtils::array_first(unpack('n', wfUtils::substr($resp['data'], $i, 2)));
						$this->dbg("Checking index {$idx}");
						if ($idx < $allCount) {
							$prefix = wfUtils::substr($allHostKeys, $idx * 4, 4);
							$badHostKeys .= $prefix;
							$this->dbg("Got bad hostkey for record: " . bin2hex($prefix));
						}
						else {
							$this->dbg("Bad allHostKeys index: {$idx}");
							$this->errorMsg = "Bad allHostKeys index: {$idx}";
							return false;
						}
					}
				}
				else if ($dataLen > 0) {
					$this->errorMsg = "Invalid data length received from Wordfence server: " . $dataLen;
					$this->dbg($this->errorMsg);
					return false;
				}
			}
			else {
				$this->errorMsg = "Wordfence server responded with an error. HTTP code " . $resp['code'] . " and data: " . $resp['data'];
				return false;
			}
			
			$badCount = wfUtils::strlen($badHostKeys) / 4;
			if ($badCount > 0) {
				$urlsToCheck = array();
				$totalURLs = 0;
				
				//Reconcile flagged prefixes with their corresponding URLs
				for ($i = 0; $i < $badCount; $i++) {
					$prefix = wfUtils::substr($badHostKeys, $i * 4, 4);
					
					if ($this->useDB) {
						/**
						 * Putting a 10000 limit in here for sites that have a huge number of items with the same URL 
						 * that repeats. This is an edge case. But if the URLs are malicious then presumably the admin 
						 * will fix the malicious URLs and on subsequent scans the items (owners) that are above the 
						 * 10000 limit will appear.
						 */
						$q1 = $this->db->querySelect("SELECT DISTINCT owner, host, path FROM {$this->table} WHERE hostKey = %s LIMIT 10000", $prefix);
						foreach ($q1 as $rec) {
							$url = 'http://' . $rec['host'] . $rec['path'];
							if (!isset($urlsToCheck[$rec['owner']])) {
								$urlsToCheck[$rec['owner']] = array();
							}
							if (!in_array($url, $urlsToCheck[$rec['owner']])) {
								$urlsToCheck[$rec['owner']][] = $url;
								$totalURLs++;
							}
						}
					}
					else {
						foreach ($this->hostList as $rec) {
							$pos = wfUtils::strpos($rec['hostKey'], $prefix);
							if ($pos !== false && $pos % 4 == 0) {
								$url = 'http://' . $rec['host'] . $rec['path'];
								if (!isset($urlsToCheck[$rec['owner']])) {
									$urlsToCheck[$rec['owner']] = array();
								}
								if (!in_array($url, $urlsToCheck[$rec['owner']])) {
									$urlsToCheck[$rec['owner']][] = $url;
									$totalURLs++;
								}
							}
						}
					}
					if ($totalURLs > 10000) { break; }
				}

				if (count($urlsToCheck) > 0) {
					wordfence::status(2, 'info', "Checking " . $totalURLs . " URLs from " . sizeof($urlsToCheck) . " sources.");
					$badURLs = $this->api->call('check_bad_urls', array(), array('toCheck' => json_encode($urlsToCheck)));
					wordfence::status(2, 'info', "Done URL check.");
					$this->dbg("Done URL check");
					if (is_array($badURLs) && count($badURLs) > 0) {
						$finalResults = array();
						foreach ($badURLs as $file => $badSiteList) {
							if (!isset($finalResults[$file])) {
								$finalResults[$file] = array();
							}
							foreach ($badSiteList as $badSite) {
								$finalResults[$file][] = array(
									'URL' => $badSite[0],
									'badList' => $badSite[1]
									);
							}
						}
						$this->dbg("Confirmed " . count($badURLs) . " bad URLs");
						return $finalResults;
					}
				}
			}
		}
		
		return array();
	}
	
	protected function _generateHashes($url) {
		//The GSB specification requires generating and sending hash prefixes for a number of additional similar URLs. See: https://developers.google.com/safe-browsing/v4/urls-hashing#suffixprefix-expressions
		
		$canonicalURL = $this->_canonicalizeURL($url);
		
		//Extract the scheme
		$scheme = 'http';
		if (preg_match('~^([a-z]+[a-z0-9+\.\-]*)://(.*)$~i', $canonicalURL, $matches)) {
			$scheme = strtolower($matches[1]);
			$canonicalURL = $matches[2];
		}
		
		//Separate URL and query string
		$query = '';
		if (preg_match('/^([^?]+)(\??.*)/', $canonicalURL, $matches)) {
			$canonicalURL = $matches[1];
			$query = $matches[2];
		}
		
		//Separate host and path
		$path = '';
		preg_match('~^(.*?)(?:(/.*)|$)~', $canonicalURL, $matches);
		$host = $matches[1];
		if (isset($matches[2])) {
			$path = $matches[2];
		}
		
		//Clean host
		$host = $this->_normalizeHost($host);
		
		//Generate hosts list
		$hosts = array();
		if (filter_var(trim($host, '[]'), FILTER_VALIDATE_IP)) {
			$hosts[] = $host;
		}
		else {
			$hostComponents = explode('.', $host);
			
			$numComponents = count($hostComponents) - 7;
			if ($numComponents < 1) {
				$numComponents = 1;
			}
			
			$hosts[] = $host;
			for ($i = $numComponents; $i < count($hostComponents) - 1; $i++) {
				$hosts[] = implode('.', array_slice($hostComponents, $i));
			}
		}
		
		//Generate paths list
		$paths = array('/');
		$pathComponents = array_filter(explode('/', $path));
		
		$numComponents = min(count($pathComponents), 4);
		for ($i = 1; $i < $numComponents; $i++) {
			$paths[] = '/' . implode('/', array_slice($pathComponents, 0, $i)) . '/';
		}
		if ($path != '/') {
			$paths[] = $path;
		}
		if (strlen($query) > 0) {
			$paths[] = $path . '?' . $query;
		}
		$paths = array_reverse($paths); //So we start at the most specific and move to most generic
		
		//Generate hashes
		$hashes = array();
		foreach ($hosts as $h) {
			$hashes[$h] = hash('sha256', $h, true); //WFSB compatibility -- it uses hashes without the path
			foreach ($paths as $p) {
				$key = $h . $p;
				$hashes[$key] = hash('sha256', $key, true);
			}
		}
		
		return $hashes;
	}
	
	protected function _canonicalizeURL($url) { //Based on https://developers.google.com/safe-browsing/v4/urls-hashing#canonicalization and Google's reference implementation https://github.com/google/safebrowsing/blob/master/urls.go
		//Strip fragment
		$url = $this->_array_first(explode('#', $url));
		
		//Trim space
		$url = trim($url);
		
		//Remove tabs, CR, LF
		$url = preg_replace('/[\t\n\r]/', '', $url);
		
		//Normalize escapes
		$url = $this->_normalizeEscape($url);
		if ($url === false) { return false; }
		
		//Extract the scheme
		$scheme = 'http';
		if (preg_match('~^([a-z]+[a-z0-9+\.\-]*)://(.*)$~i', $url, $matches)) {
			$scheme = strtolower($matches[1]);
			$url = $matches[2];
		}
		
		//Separate URL and query string
		$query = '';
		if (preg_match('/^([^?]+)(\??.*)/', $url, $matches)) {
			$url = $matches[1];
			$query = $matches[2];
		}
		$endsWithSlash = substr($url, -1) == '/';
		
		//Separate host and path
		$path = '';
		preg_match('~^(.*?)(?:(/.*)|$)~', $url, $matches);
		$host = $matches[1];
		if (isset($matches[2])) {
			$path = $matches[2];
		}
		
		//Clean host
		$host = $this->_normalizeHost($host);
		if ($host === false) { return false; }
		
		//Clean path
		$path = preg_replace('~//+~', '/', $path); //Multiple slashes -> single slash
		$path = preg_replace('~(?:^|/)\.(?:$|/)~', '/', $path); //. path components removed
		while (preg_match('~/(?!\.\./)[^/]+/\.\.(?:$|/)~', $path)) { //Resolve ..
			$path = preg_replace('~/(?!\.\./)[^/]+/\.\.(?:$|/)~', '/', $path, 1);
		}
		$path = preg_replace('~(?:^|/)\.\.(?:$|/)~', '/', $path); //Eliminate .. at the beginning
		$path = trim($path, '.');
		$path = preg_replace('/\.\.+/', '.', $path);
		
		if ($path == '.' || $path == '') {
			$path = '/';
		}
		else if ($endsWithSlash && substr($path, -1) != '/') {
			$path .= '/';
		}
		
		return $scheme . '://' . $host . $path . $query;
	}
	
	protected function _normalizeEscape($url) {
		$maxDepth = 1024;
		$i = 0;
		while (preg_match('/%([0-9a-f]{2})/i', $url)) {
			$url = preg_replace_callback('/%([0-9a-f]{2})/i', array($this, '_hex2binCallback'), $url);
			$i++;
			
			if ($i > $maxDepth) {
				return false;
			}
		}
		
		return preg_replace_callback('/[\x00-\x20\x7f-\xff#%]/', array($this, '_bin2hexCallback'), $url);
	}
	
	protected function _hex2binCallback($matches) {
		return wfUtils::hex2bin($matches[1]);
	}

	protected function _bin2hexCallback($matches) {
		return '%' . bin2hex($matches[0]);	
	}
	
	protected function _normalizeHost($host) {
		//Strip username:password
		$host = $this->_array_last(explode('@', $host));
		
		//IPv6 literal
		if (substr($host, 0, 1) == '[') {
			if (strpos($host, ']') === false) { //No closing bracket
				return false;
			}
		}
		
		//Strip port
		$host = preg_replace('/:\d+$/', '', $host);
		
		//Unicode to IDNA
		$u = rawurldecode($host);
		if (preg_match('/[\x81-\xff]/', $u)) { //0x80 is technically Unicode, but the GSB canonicalization doesn't consider it one
			if (function_exists('idn_to_ascii')) { //Some PHP versions don't have this and we don't have a polyfill
				$host = idn_to_ascii($u);
			}
		}
		
		//Remove extra dots
		$host = trim($host, '.');
		$host = preg_replace('/\.\.+/', '.', $host);
		
		//Canonicalize IP addresses
		if ($iphost = $this->_parseIP($host)) {
			return $iphost;
		}
		
		return strtolower($host);
	}
	
	protected function _parseIP($host) {
		// The Windows resolver allows a 4-part dotted decimal IP address to have a
		// space followed by any old rubbish, so long as the total length of the
		// string doesn't get above 15 characters. So, "10.192.95.89 xy" is
		// resolved to 10.192.95.89. If the string length is greater than 15
		// characters, e.g. "10.192.95.89 xy.wildcard.example.com", it will be
		// resolved through DNS.
		if (strlen($host) <= 15) {
			$host = $this->_array_first(explode(' ', $host));
		}
		
		if (!preg_match('/^((?:0x[0-9a-f]+|[0-9\.])+)$/i', $host)) {
			return false;
		}
		
		$parts = explode('.', $host);
		if (count($parts) > 4) {
			return false;
		}
		
		$strings = array();
		foreach ($parts as $i => $p) {
			if ($i == count($parts) - 1) {
				$strings[] = $this->_canonicalNum($p, 5 - count($parts));
			}
			else {
				$strings[] = $this->_canonicalNum($p, 1);
			}
			
			if ($strings[$i] == '') {
				return '';
			}
		}
		
		return implode('.', $strings);
	}
	
	protected function _canonicalNum($part, $n) {
		if ($n <= 0 || $n > 4) {
			return '';
		}
		
		if (preg_match('/^0x(\d+)$/i', $part, $matches)) { //hex
			$part = hexdec($matches[1]);
		}
		else if (preg_match('/^0(\d+)$/i', $part, $matches)) { //octal
			$part = octdec($matches[1]);
		}
		else {
			$part = (int) $part;
		}
		
		$strings = array_fill(0, $n, '');
		for ($i = $n - 1; $i >= 0; $i--) {
			$strings[$i] = (string) ($part & 0xff);
			$part = $part >> 8;
		}
		return implode('.', $strings);
	}
	
	protected function _array_first($array) {
		if (empty($array)) {
			return null;
		}
		
		return $array[0];
	}
	
	protected function _array_last($array) {
		if (empty($array)) {
			return null;
		}
		
		return $array[count($array) - 1];
	}
}
