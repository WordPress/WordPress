<?php

class wfGrant
{
	public $select = false;
	public $update = false;
	public $insert = false;
	public $delete = false;
	public $alter = false;
	public $create = false;
	public $drop = false;

	public static function get()
	{
		static $instance;
		if ($instance === null) {
			$instance = new self;
		}
		return $instance;
	}
	
	private function __construct()
	{
		global $wpdb;
		$rows = $wpdb->get_results("SHOW GRANTS FOR current_user()", ARRAY_N);
		
		foreach ($rows as $row) {
			preg_match("/GRANT (.+) ON (.+) TO/", $row[0], $matches);
			foreach (explode(",", $matches[1]) as $permission) {
				$permission = str_replace(" ", "_", trim(strtolower($permission)));
				if ($permission === 'all_privileges') {
					foreach ($this as $key => $value) {
						$this->$key = true;
					}
					break 2;
				}
				$this->$permission = true;
			}
		}
	}
}

class wfDiagnostic
{
	protected $minVersion = array(
		'PHP' => '5.2.4',
		'cURL' => '1.0',
	);

	protected $description = array(
		'Wordfence Status' => array(
			'description' => 'General information about the Wordfence installation.',
			'tests' => array(
				'wfVersion' => 'Wordfence Version',
			),
		),
		'Filesystem' => array(
			'description' => 'Ability to read/write various files.',
			'tests' => array(
				'isTmpReadable' => 'Checking if web server can read from <code>~/plugins/wordfence/tmp</code>',
				'isTmpWritable' => 'Checking if web server can write to <code>~/plugins/wordfence/tmp</code>',
				'isWAFReadable' => 'Checking if web server can read from <code>~/wp-content/wflogs</code>',
				'isWAFWritable' => 'Checking if web server can write to <code>~/wp-content/wflogs</code>',
			),
		),
		'Wordfence Config' => array(
			'description' => 'Ability to save Wordfence settings to the database.',
			'tests' => array(
				'configWritableSet' => 'Checking basic config reading/writing',
				'configWritableSetSer' => 'Checking serialized config reading/writing',
			),
		),
		'MySQL' => array(
			'description' => 'Database privileges.',
			'tests' => array(
				'userCanDelete' => 'Checking if MySQL user has <code>DELETE</code> privilege',
				'userCanInsert' => 'Checking if MySQL user has <code>INSERT</code> privilege',
				'userCanUpdate' => 'Checking if MySQL user has <code>UPDATE</code> privilege',
				'userCanSelect' => 'Checking if MySQL user has <code>SELECT</code> privilege',
				'userCanCreate' => 'Checking if MySQL user has <code>CREATE TABLE</code> privilege',
				'userCanAlter'  => 'Checking if MySQL user has <code>ALTER TABLE</code> privilege',
				'userCanDrop'   => 'Checking if MySQL user has <code>DROP</code> privilege',
				'userCanTruncate'   => 'Checking if MySQL user has <code>TRUNCATE</code> privilege',
			)
		),
		'PHP Environment' => array(
			'description' => 'PHP version, important PHP extensions.',
			'tests' => array(
				'phpVersion' => 'PHP version >= PHP 5.2.4<br><em> (<a href="https://wordpress.org/about/requirements/" target="_blank" rel="noopener noreferrer">Minimum version required by WordPress</a>)</em>',
				'processOwner' => 'Process Owner',
				'hasOpenSSL' => 'Checking for OpenSSL support',
				'hasCurl'    => 'Checking for cURL support',
			)
		),
		'Connectivity' => array(
			'description' => 'Ability to connect to the Wordfence servers and your own site.',
			'tests' => array(
				'connectToServer1' => 'Connecting to Wordfence servers (http)',
				'connectToServer2' => 'Connecting to Wordfence servers (https)',
				'connectToSelf' => 'Connecting back to this site',
			)
		),
//		'Configuration' => array(
//			'howGetIPs' => 'How does get IPs',
//		),
	);

	protected $results = array();

	public function __construct()
	{
		foreach ($this->description as $title => $tests) {
			$this->results[$title] = array(
				'description' => $tests['description'],
			);
			foreach ($tests['tests'] as $name => $description) {
				if (!method_exists($this, $name)) {
					continue;
				}
				
				$result = $this->$name();

				if (is_bool($result)) {
					$result = array(
						'test'    => $result,
						'message' => $result ? 'OK' : 'FAIL',
					);
				}

				$result['label'] = $description;
				$result['name'] = $name;

				$this->results[$title]['results'][] = $result;
			}
		}
	}

	public function getResults()
	{
		return $this->results;
	}
	
	public function wfVersion() {
		return array('test' => true, 'message' => WORDFENCE_VERSION . ' (' . WORDFENCE_BUILD_NUMBER . ')');
	}

	public function isTmpReadable() {
		return is_readable(WORDFENCE_PATH . 'tmp');
	}

	public function isTmpWritable() {
		return is_writable(WORDFENCE_PATH . 'tmp');
	}
	
	public function isWAFReadable() {
		if (!is_readable(WFWAF_LOG_PATH)) {
			return array('test' => false, 'message' => 'No files readable');
		}
		
		$files = array(
			WFWAF_LOG_PATH . 'attack-data.php', 
			WFWAF_LOG_PATH . 'ips.php', 
			WFWAF_LOG_PATH . 'config.php',
			WFWAF_LOG_PATH . 'rules.php',
			WFWAF_LOG_PATH . 'wafRules.rules',
		);
		$unreadable = array();
		foreach ($files as $f) {
			if (!file_exists($f)) {
				$unreadable[] = 'File "' . basename($f) . '" does not exist';
			}
			else if (!is_readable($f)) {
				$unreadable[] = 'File "' . basename($f) . '" is unreadable';
			}
		}
		
		if (count($unreadable) > 0) {
			return array('test' => false, 'message' => implode(', ', $unreadable));
		}
		
		return true;
	}
	
	public function isWAFWritable() {
		if (!is_writable(WFWAF_LOG_PATH)) {
			return array('test' => false, 'message' => 'No files writable');
		}
		
		$files = array(
			WFWAF_LOG_PATH . 'attack-data.php',
			WFWAF_LOG_PATH . 'ips.php',
			WFWAF_LOG_PATH . 'config.php',
			WFWAF_LOG_PATH . 'rules.php',
			WFWAF_LOG_PATH . 'wafRules.rules',
		);
		$unwritable = array();
		foreach ($files as $f) {
			if (!file_exists($f)) {
				$unwritable[] = 'File "' . basename($f) . '" does not exist';
			}
			else if (!is_writable($f)) {
				$unwritable[] = 'File "' . basename($f) . '" is unwritable';
			}
		}
		
		if (count($unwritable) > 0) {
			return array('test' => false, 'message' => implode(', ', $unwritable));
		}
		
		return true;
	}

	public function userCanInsert() {
		return wfGrant::get()->insert;
	}
	
	public function userCanUpdate() {
		return wfGrant::get()->update;
	}

	public function userCanDelete() {
		return wfGrant::get()->delete;
	}

	public function userCanSelect() {
		return wfGrant::get()->select;
	}

	public function userCanCreate() {
		return wfGrant::get()->create;
	}

	public function userCanDrop() {
		return wfGrant::get()->drop;
	}

	public function userCanTruncate() {
		return wfGrant::get()->drop && wfGrant::get()->delete;
	}

	public function userCanAlter() {
		return wfGrant::get()->alter;
	}

	public function phpVersion()
	{
		return array(
			'test' => version_compare(phpversion(), $this->minVersion['PHP'], '>='),
			'message'  => phpversion(),
		);
	}
	
	public function configWritableSet() {
		global $wpdb;
		$show = $wpdb->hide_errors();
		$val = md5(time());
		wfConfig::set('configWritingTest', $val, wfConfig::DONT_AUTOLOAD);
		$testVal = wfConfig::get('configWritingTest');
		$wpdb->show_errors($show);
		return array(
			'test' => ($val === $testVal),
			'message' => 'Basic config writing'
		);
	}
	public function configWritableSetSer() {
		global $wpdb;
		$show = $wpdb->hide_errors();
		$val = md5(time());
		wfConfig::set_ser('configWritingTest_ser', array($val), false, wfConfig::DONT_AUTOLOAD);
		$testVal = @array_shift(wfConfig::get_ser('configWritingTest_ser', array(), false));
		$wpdb->show_errors($show);
		return array(
			'test' => ($val === $testVal),
			'message' => 'Serialized config writing'
		);
	}

	public function processOwner() {
		$disabledFunctions = explode(',', ini_get('disable_functions'));

		if (is_callable('posix_geteuid')) {
			if (!is_callable('posix_getpwuid') || in_array('posix_getpwuid', $disabledFunctions)) {
				return array(
					'test' => false,
					'message' => 'Unavailable',
				);
			}

			$processOwner = posix_getpwuid(posix_geteuid());
			if ($processOwner !== null)
			{
				return array(
					'test' => true,
					'message' => $processOwner['name'],
				);
			}
		}

		$usernameOrUserEnv = getenv('USERNAME') ? getenv('USERNAME') : getenv('USER');
		if (!empty($usernameOrUserEnv)) { //Check some environmental variable possibilities
			return array(
				'test' => true,
				'message' => $usernameOrUserEnv,
			);
		}

		$currentUser = get_current_user();
		if (!empty($currentUser)) { //php.net comments indicate on Windows this returns the process owner rather than the file owner
			return array(
				'test' => true,
				'message' => $currentUser,
			);
		}

		if (!empty($_SERVER['LOGON_USER'])) { //Last resort for IIS since POSIX functions are unavailable, Source: https://msdn.microsoft.com/en-us/library/ms524602(v=vs.90).aspx
			return array(
				'test' => true,
				'message' => $_SERVER['LOGON_USER'],
			);
		}

		return array(
			'test' => false,
			'message' => 'Unknown',
		);
	}

	public function hasOpenSSL() {
		return is_callable('openssl_open');
	}

	public function hasCurl() {
		if (!is_callable('curl_version')) {
			return false;
		}
		$version = curl_version();
		return array(
			'test' => version_compare($version['version'], $this->minVersion['cURL'], '>='),
			'message'  => $version['version'],
		);
	}

	public function connectToServer1() {
		return $this->_connectToServer('http');
	}

	public function connectToServer2() {
		return $this->_connectToServer('https');
	}

	public function _connectToServer($protocol) {
		$cronURL = admin_url('admin-ajax.php');
		$cronURL = preg_replace('/^(https?:\/\/)/i', '://noc1.wordfence.com/scanptest/', $cronURL);
		$cronURL .= '?action=wordfence_doScan&isFork=0&cronKey=47e9d1fa6a675b5999999333';
		$cronURL = $protocol . $cronURL;
		$result = wp_remote_post($cronURL, array(
			'timeout' => 10, //Must be less than max execution time or more than 2 HTTP children will be occupied by scan
			'blocking' => true, //Non-blocking seems to block anyway, so we use blocking
			// This causes cURL to throw errors in some versions since WordPress uses its own certificate bundle ('CA certificate set, but certificate verification is disabled')
			// 'sslverify' => false,
			'headers' => array()
			));
		if( (! is_wp_error($result)) && $result['response']['code'] == 200 && strpos($result['body'], "scanptestok") !== false){
			return true;
		}

		ob_start();
		if(is_wp_error($result)){
			echo "wp_remote_post() test to noc1.wordfence.com failed! Response was: " . $result->get_error_message() . "<br />\n";
		} else {
			echo "wp_remote_post() test to noc1.wordfence.com failed! Response was: " . $result['response']['code'] . " " . $result['response']['message'] . "<br />\n";
			echo "This likely means that your hosting provider is blocking requests to noc1.wordfence.com or has set up a proxy that is not behaving itself.<br />\n";
			echo "This additional info may help you diagnose the issue. The response headers we received were:<br />\n";
			foreach($result['headers'] as $key => $value){
				echo "$key => $value<br />\n";
			}
		}

		return array(
			'test' => false,
			'message' => ob_get_clean()
		);
	}
	
	public function connectToSelf() {
		$adminAJAX = admin_url('admin-ajax.php?action=wordfence_testAjax');
		$result = wp_remote_post($adminAJAX, array(
			'timeout' => 10, //Must be less than max execution time or more than 2 HTTP children will be occupied by scan
			'blocking' => true, //Non-blocking seems to block anyway, so we use blocking
			'headers' => array()
		));
		
		if ((!is_wp_error($result)) && $result['response']['code'] == 200 && strpos($result['body'], "WFSCANTESTOK") !== false) {
			$host = parse_url($adminAJAX, PHP_URL_HOST);
			if ($host !== null) {
				$ips = wfUtils::resolveDomainName($host);
				$ips = implode(', ', $ips);
				return array('test' => true, 'message' => 'OK - ' . $ips);
			}
			return true;
		}
		
		ob_start();
		if (is_wp_error($result)) {
			echo "wp_remote_post() test back to this server failed! Response was: " . $result->get_error_message() . "<br />\n";
		}
		else {
			echo "wp_remote_post() test back to this server failed! Response was: " . $result['response']['code'] . " " . $result['response']['message'] . "<br />\n";
			echo "This additional info may help you diagnose the issue. The response headers we received were:<br />\n";
			foreach($result['headers'] as $key => $value){
				echo "$key => $value<br />\n";
			}
		}
		
		return array(
			'test' => false,
			'message' => ob_get_clean()
		);
	}

	public function howGetIPs()
	{
		$howGet = wfConfig::get('howGetIPs', false);
		if ($howGet) {
			if (empty($_SERVER[$howGet])) {
				return array(
					'test' => false,
					'message' => 'We cannot read $_SERVER[' . $howGet . ']',
				);
			}
			return array(
				'test' => true,
				'message' => $howGet,
			);
		}
		foreach (array('HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR') as $test) {
			if (!empty($_SERVER[$test])) {
				return array(
					'test' => false,
					'message' => 'Should be: ' . $test
				);
			}
		}
		return array(
			'test' => true,
			'message' => 'REMOTE_ADDR',
		);
	}
}

