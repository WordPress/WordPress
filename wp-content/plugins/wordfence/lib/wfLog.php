<?php
require_once('wfDB.php');
require_once('wfUtils.php');
require_once('wfBrowscap.php');
class wfLog {
	public $canLogHit = true;
	private $hitsTable = '';
	private $apiKey = '';
	private $wp_version = '';
	private $db = false;
	private $googlePattern = '/\.(?:googlebot\.com|google\.[a-z]{2,3}|google\.[a-z]{2}\.[a-z]{2}|1e100\.net)$/i';
	private static $gbSafeCache = array();

	/**
	 * @var wfRequestModel
	 */
	private $currentRequest;
	
	public static function shared() {
		static $_shared = null;
		if ($_shared === null) {
			$_shared = new wfLog(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		}
		return $_shared;
	}

	public function __construct($apiKey, $wp_version){
		$this->apiKey = $apiKey;
		$this->wp_version = $wp_version;
		$this->hitsTable = wfDB::networkTable('wfHits');
		$this->loginsTable = wfDB::networkTable('wfLogins');
		$this->blocksTable = wfBlock::blocksTable();
		$this->lockOutTable = wfDB::networkTable('wfLockedOut');
		$this->leechTable = wfDB::networkTable('wfLeechers');
		$this->badLeechersTable = wfDB::networkTable('wfBadLeechers');
		$this->scanTable = wfDB::networkTable('wfScanners');
		$this->throttleTable = wfDB::networkTable('wfThrottleLog');
		$this->statusTable = wfDB::networkTable('wfStatus');
		$this->ipRangesTable = wfDB::networkTable('wfBlocksAdv');
		$this->perfTable = wfDB::networkTable('wfPerfLog');
	}

	public function initLogRequest() {
		if ($this->currentRequest === null) {
			$this->currentRequest = new wfRequestModel();

			$this->currentRequest->ctime = sprintf('%.6f', microtime(true));
			$this->currentRequest->statusCode = 200;
			$this->currentRequest->isGoogle = (wfCrawl::isGoogleCrawler() ? 1 : 0);
			$this->currentRequest->IP = wfUtils::inet_pton(wfUtils::getIP());
			$this->currentRequest->userID = $this->getCurrentUserID();
			$this->currentRequest->newVisit = (wordfence::$newVisit ? 1 : 0);
			$this->currentRequest->URL = wfUtils::getRequestedURL();
			$this->currentRequest->referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
			$this->currentRequest->UA = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$this->currentRequest->jsRun = 0;
			
			if (!function_exists('wp_verify_nonce')) {
				add_action('plugins_loaded', array($this, 'actionSetRequestJSEnabled'));
			} else {
				$this->actionSetRequestJSEnabled();
			}

			add_action('init', array($this, 'actionSetRequestOnInit'), 9999);

			if (function_exists('register_shutdown_function')) {
				register_shutdown_function(array($this, 'logHit'));
			}
		}
	}

	public function actionSetRequestJSEnabled() {
		$UA = $this->currentRequest->UA;
		$IP = wfUtils::getIP();
		$jsRun = (int) (isset($_COOKIE['wordfence_verifiedHuman']) &&
			$this->validateVerifiedHumanCookie($_COOKIE['wordfence_verifiedHuman'], $UA, $IP));
		$this->currentRequest->jsRun = $jsRun;
	}

	/**
	 * CloudFlare's plugin changes $_SERVER['REMOTE_ADDR'] on init.
	 */
	public function actionSetRequestOnInit() {
		$this->currentRequest->IP = wfUtils::inet_pton(wfUtils::getIP());
		$this->currentRequest->userID = $this->getCurrentUserID();
	}

	/**
	 * @param string $cookieVal
	 * @param string $ua
	 * @param string $ip
	 * @return string
	 */
	public function validateVerifiedHumanCookie($cookieVal, $ua = null, $ip = null) {
		if ($ua === null) {
			$ua = !empty($this->currentRequest) ? $this->currentRequest->UA : '';
		}
		if ($ip === null) {
			$ip = wfUtils::getIP();
		}
		if (!function_exists('hash_equals')) {
			require_once ABSPATH . WPINC . '/compat.php';
		}
		return hash_equals($cookieVal, $this->getVerifiedHumanCookieValue($ua, $ip));
	}

	/**
	 * @param string $ua
	 * @param string $ip
	 * @return string
	 */
	public function getVerifiedHumanCookieValue($ua = null, $ip = null) {
		if ($ua === null) {
			$ua = !empty($this->currentRequest) ? $this->currentRequest->UA : '';
		}
		if ($ip === null) {
			$ip = wfUtils::getIP();
		}
		if (!function_exists('wp_hash')) {
			require_once ABSPATH . WPINC . '/pluggable.php';
		}
		return wp_hash('wordfence_verifiedHuman' . $ua . $ip, 'nonce');
	}

	/**
	 * @return wfRequestModel
	 */
	public function getCurrentRequest() {
		return $this->currentRequest;
	}

	public function logPerf($IP, $UA, $URL, $data){
		$IP = wfUtils::inet_pton($IP);
		$this->getDB()->queryWrite("insert into " . $this->perfTable . " (IP, userID, UA, URL, ctime, fetchStart, domainLookupStart, domainLookupEnd, connectStart, connectEnd, requestStart, responseStart, responseEnd, domReady, loaded) values (%s, %d, '%s', '%s', unix_timestamp(), %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)", 
			$IP, 
			$this->getCurrentUserID(), 
			$UA, 
			$URL,
			$data['fetchStart'],
			$data['domainLookupStart'],
			$data['domainLookupEnd'],
			$data['connectStart'],
			$data['connectEnd'],
			$data['requestStart'],
			$data['responseStart'],
			$data['responseEnd'],
			$data['domReady'],
			$data['loaded']
			);
	}
	public function logLogin($action, $fail, $username){
		if(! $username){
			return;
		}
		$user = get_user_by('login', $username);
		$userID = 0;
		if($user){
			$userID = $user->ID;
			if(! $userID){
				return;
			}
		}
		else {
			$user = get_user_by('email', $username);
			if ($user) {
				$userID = $user->ID;
				if (!$userID) {
					return;
				}
			}
		}
		// change the action flag here if the user does not exist.
		if ($action == 'loginFailValidUsername' && $userID == 0) {
			$action = 'loginFailInvalidUsername';
		}

		$hitID = 0;
		if ($this->currentRequest !== null) {
			$this->currentRequest->userID = $userID;
			$this->currentRequest->action = $action;
			$this->currentRequest->save();
			$hitID = $this->currentRequest->getPrimaryKey();
		}

		//Else userID stays 0 but we do log this even though the user doesn't exist.
		$this->getDB()->queryWrite("insert into " . $this->loginsTable . " (hitID, ctime, fail, action, username, userID, IP, UA) values (%d, %f, %d, '%s', '%s', %s, %s, '%s')",
			$hitID,
			sprintf('%.6f', microtime(true)),
			$fail,
			$action,
			$username,
			$userID,
			wfUtils::inet_pton(wfUtils::getIP()),
			(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '')
			);
	}
	private function getCurrentUserID(){
		if (!function_exists('get_current_user_id') || !defined('AUTH_COOKIE')) { //If pluggable.php is loaded early by some other plugin on a multisite installation, it leads to an error because AUTH_COOKIE is undefined and WP doesn't check for it first
			return 0;
		}
		$id = get_current_user_id();
		return $id ? $id : 0;
	}
	public function logLeechAndBlock($type){ //404 or hit
		if(wfConfig::get('firewallEnabled')){
			//Moved the following block into the "is fw enabled section" for optimization. 
			$IP = wfUtils::getIP();
			$IPnum = wfUtils::inet_pton($IP);
			if (wfBlock::isWhitelisted($IP)) {
				return;
			}
			if (wfConfig::get('neverBlockBG') == 'neverBlockUA' && wfCrawl::isGoogleCrawler()) {
				return;
			}
			if (wfConfig::get('neverBlockBG') == 'neverBlockVerified' && wfCrawl::isVerifiedGoogleCrawler()) {
				return;
			}

			if ($type == '404') {
				$allowed404s = wfConfig::get('allowed404s');
				if (is_string($allowed404s)) {
					$allowed404s = array_filter(preg_split("/[\r\n]+/", $allowed404s));
					$allowed404sPattern = '';
					foreach ($allowed404s as $allowed404) {
						$allowed404sPattern .= preg_replace('/\\\\\*/', '.*?', preg_quote($allowed404, '/')) . '|';
					}
					$uri = $_SERVER['REQUEST_URI'];
					if (($index = strpos($uri, '?')) !== false) {
						$uri = substr($uri, 0, $index);
					}
					if ($allowed404sPattern && preg_match('/^' . substr($allowed404sPattern, 0, -1) . '$/i', $uri)) {
						return;
					}
				}
			}


			if($type == '404'){
				$table = $this->scanTable;
			} else if($type == 'hit'){
				$table = $this->leechTable;
			} else {
				wordfence::status(1, 'error', "Invalid type to logLeechAndBlock(): $type");
				return;
			}
			$this->getDB()->queryWrite("insert into $table (eMin, IP, hits) values (floor(unix_timestamp() / 60), %s, 1) ON DUPLICATE KEY update hits = IF(@wfcurrenthits := hits + 1, hits + 1, hits + 1)", wfUtils::inet_pton($IP));
			$hitsPerMinute = $this->getDB()->querySingle("select @wfcurrenthits");
			//end block moved into "is fw enabled" section

			//Range blocking was here. Moved to wordfenceClass::veryFirstAction

			if(wfConfig::get('maxGlobalRequests') != 'DISABLED' && $hitsPerMinute > wfConfig::getInt('maxGlobalRequests')){ //Applies to 404 or pageview
				$this->takeBlockingAction('maxGlobalRequests', "Exceeded the maximum global requests per minute for crawlers or humans.");
			}
			if($type == '404'){
				if(wfConfig::get('other_WFNet')){
					$table_wfNet404s = wfDB::networkTable('wfNet404s');
					$this->getDB()->queryWrite("insert IGNORE into {$table_wfNet404s} (sig, ctime, URI) values (UNHEX(MD5('%s')), unix_timestamp(), '%s')", $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_URI']);
				}
				$pat = wfConfig::get('vulnRegex');
				if($pat){
					$URL = wfUtils::getRequestedURL();
					if(preg_match($pat, $URL)){
						$table_wfVulnScanners = wfDB::networkTable('wfVulnScanners');
						$this->getDB()->queryWrite("insert IGNORE into {$table_wfVulnScanners} (IP, ctime, hits) values (%s, unix_timestamp(), 1) ON DUPLICATE KEY UPDATE ctime = unix_timestamp(), hits = hits + 1", wfUtils::inet_pton($IP));
						if(wfConfig::get('maxScanHits') != 'DISABLED'){
							if( empty($_SERVER['HTTP_REFERER'] )){
								$this->getDB()->queryWrite("insert into " . $this->badLeechersTable . " (eMin, IP, hits) values (floor(unix_timestamp() / 60), %s, 1) ON DUPLICATE KEY update hits = IF(@wfblcurrenthits := hits + 1, hits + 1, hits + 1)", $IPnum); 
								$BL_hitsPerMinute = $this->getDB()->querySingle("select @wfblcurrenthits");
								if($BL_hitsPerMinute > wfConfig::getInt('maxScanHits')){
									$this->takeBlockingAction('maxScanHits', "Exceeded the maximum number of 404 requests per minute for a known security vulnerability.");
								}
							}
						}
					}
				}
			}
			if(isset($_SERVER['HTTP_USER_AGENT']) && wfCrawl::isCrawler($_SERVER['HTTP_USER_AGENT'])){
				if($type == 'hit' && wfConfig::get('maxRequestsCrawlers') != 'DISABLED' && $hitsPerMinute > wfConfig::getInt('maxRequestsCrawlers')){
					$this->takeBlockingAction('maxRequestsCrawlers', "Exceeded the maximum number of requests per minute for crawlers."); //may not exit
				} else if($type == '404' && wfConfig::get('max404Crawlers') != 'DISABLED' && $hitsPerMinute > wfConfig::getInt('max404Crawlers')){
					$this->takeBlockingAction('max404Crawlers', "Exceeded the maximum number of page not found errors per minute for a crawler.");
				}
			} else {
				if($type == 'hit' && wfConfig::get('maxRequestsHumans') != 'DISABLED' && $hitsPerMinute > wfConfig::getInt('maxRequestsHumans')){
					$this->takeBlockingAction('maxRequestsHumans', "Exceeded the maximum number of page requests per minute for humans.");
				} else if($type == '404' && wfConfig::get('max404Humans') != 'DISABLED' && $hitsPerMinute > wfConfig::getInt('max404Humans')){
					$this->takeBlockingAction('max404Humans', "Exceeded the maximum number of page not found errors per minute for humans.");
				}
			}
		}
	}
	
	public function tagRequestForBlock($reason, $wfsn = false) {
		if ($this->currentRequest !== null) {
			$this->currentRequest->statusCode = 403;
			$this->currentRequest->action = 'blocked:' . ($wfsn ? 'wfsn' : 'wordfence');
			$this->currentRequest->actionDescription = $reason;
		}
	}
	
	public function tagRequestForLockout($reason) {
		if ($this->currentRequest !== null) {
			$this->currentRequest->statusCode = 503;
			$this->currentRequest->action = 'lockedOut';
			$this->currentRequest->actionDescription = $reason;
		}
	}

	/**
	 * @return bool|int
	 */
	public function logHit() {
		$liveTrafficEnabled = wfConfig::liveTrafficEnabled();
		$action = $this->currentRequest->action;
		$logHitOK = $this->logHitOK();
		if (!$logHitOK) {
			return false;
		}
		if (!$liveTrafficEnabled && !$action) {
			return false;
		}
		if ($this->currentRequest !== null) {
			if ($this->currentRequest->save()) {
				return $this->currentRequest->getPrimaryKey();
			}
		}
		return false;
	}

	public function getPerfStats($afterTime, $limit = 50){
		$serverTime = $this->getDB()->querySingle("select unix_timestamp()");
		$results = $this->getDB()->querySelect("select * from " . $this->perfTable . " where ctime > %f order by ctime desc limit %d", $afterTime, $limit);
		$this->resolveIPs($results);
		$browscap = new wfBrowscap();
		foreach($results as &$res){
			$res['timeAgo'] = wfUtils::makeTimeAgo($serverTime - $res['ctime']);
			$res['IP'] = wfUtils::inet_ntop($res['IP']);
			$res['browser'] = false;
			if($res['UA']){
				$b = $browscap->getBrowser($res['UA']);
				if ($b && $b['Parent'] != 'DefaultProperties') {
					$res['browser'] = array(
						'browser' => $b['Browser'],
						'version' => $b['Version'],
						'platform' => $b['Platform'],
						'isMobile' => $b['isMobileDevice'],
						'isCrawler' => $b['Crawler']
						);
				}
				else {
					$IP = wfUtils::getIP();
					$res['browser'] = array(
						'isCrawler' => !(isset($_COOKIE['wordfence_verifiedHuman']) && $this->validateVerifiedHumanCookie($_COOKIE['wordfence_verifiedHuman'], $res['UA'], $IP))
					);
				}
			}
			if($res['userID']){
				$ud = get_userdata($res['userID']);
				if($ud){
					$res['user'] = array(
						'editLink' => wfUtils::editUserLink($res['userID']),
						'display_name' => $ud->display_name,
						'ID' => $res['userID']
						);
					$res['user']['avatar'] = get_avatar($res['userID'], 16);
				}
			} else {
				$res['user'] = false;
			}
		}
		return $results;
	}
	public function getHits($hitType /* 'hits' or 'logins' */, $type, $afterTime, $limit = 50, $IP = false){
		global $wpdb;
		$IPSQL = "";
		if($IP){
			$IPSQL = " and IP=%s ";
			$sqlArgs = array($afterTime, wfUtils::inet_pton($IP), $limit);
		} else {
			$sqlArgs = array($afterTime, $limit);
		}
		if($hitType == 'hits'){
			if($type == 'hit'){
				$typeSQL = " ";
			} else if($type == 'crawler'){
				$now = time();
				$typeSQL = " and jsRun = 0 and $now - ctime > 30 ";
			} else if($type == 'gCrawler'){
				$typeSQL = " and isGoogle = 1 ";
			} else if($type == '404'){
				$typeSQL = " and statusCode = 404 ";
			} else if($type == 'human'){
				$typeSQL = " and jsRun = 1 ";
			} else if($type == 'ruser'){
				$typeSQL = " and userID > 0 ";
			} else {
				wordfence::status(1, 'error', "Invalid log type to wfLog: $type");
				return false;
			}
			array_unshift($sqlArgs, "select h.*, u.display_name from {$this->hitsTable} h
				LEFT JOIN {$wpdb->users} u on h.userID = u.ID
				where ctime > %f $IPSQL $typeSQL order by ctime desc limit %d");
			$results = call_user_func_array(array($this->getDB(), 'querySelect'), $sqlArgs);

		} else if($hitType == 'logins'){
			array_unshift($sqlArgs, "select l.*, u.display_name from {$this->loginsTable} l
				LEFT JOIN {$wpdb->users} u on l.userID = u.ID
				where ctime > %f $IPSQL order by ctime desc limit %d");
			$results = call_user_func_array(array($this->getDB(), 'querySelect'), $sqlArgs ); 

		} else {
			wordfence::status(1, 'error', "getHits got invalid hitType: $hitType");
			return false;
		}
		$this->processGetHitsResults($type, $results);
		return $results;
	}

	/**
	 * @param string $type
	 * @param array $results
	 * @throws Exception
	 */
	public function processGetHitsResults($type, &$results) {
		$serverTime = $this->getDB()->querySingle("select unix_timestamp()");

		$this->resolveIPs($results);
		$ourURL = parse_url(site_url());
		$ourHost = strtolower($ourURL['host']);
		$ourHost = preg_replace('/^www\./i', '', $ourHost);
		$browscap = new wfBrowscap();

		$patternBlocks = wfBlock::patternBlocks(true);

		foreach($results as &$res){
			$res['type'] = $type;
			$res['IP'] = wfUtils::inet_ntop($res['IP']);
			$res['timeAgo'] = wfUtils::makeTimeAgo($serverTime - $res['ctime']);
			$res['blocked'] = false;
			$res['rangeBlocked'] = false;
			$res['ipRangeID'] = -1;
			
			$ipBlock = wfBlock::findIPBlock($res['IP']);
			if ($ipBlock !== false) {
				$res['blocked'] = true;
				$res['blockID'] = $ipBlock->id;
			}
			
			foreach ($patternBlocks as $b) {
				if (empty($b->ipRange)) { continue; }
				$range = new wfUserIPRange($b->ipRange);
				if ($range->isIPInRange($res['IP'])) {
					$res['rangeBlocked'] = true;
					$res['ipRangeID'] = $b->id;
					break;
				}
			}
			
			$res['extReferer'] = false;
			if(isset( $res['referer'] ) && $res['referer']){
				if(wfUtils::hasXSS($res['referer'] )){ //filtering out XSS
					$res['referer'] = '';
				}
			}
			if( isset( $res['referer'] ) && $res['referer']){
				$refURL = parse_url($res['referer']);
				if(is_array($refURL) && isset($refURL['host']) && $refURL['host']){
					$refHost = strtolower(preg_replace('/^www\./i', '', $refURL['host']));
					if($refHost != $ourHost){
						$res['extReferer'] = true;
						//now extract search terms
						$q = false;
						if(preg_match('/(?:google|bing|alltheweb|aol|ask)\./i', $refURL['host'])){
							$q = 'q';
						} else if(stristr($refURL['host'], 'yahoo.')){
							$q = 'p';
						} else if(stristr($refURL['host'], 'baidu.')){
							$q = 'wd';
						}
						if($q){
							$queryVars = array();
							if( isset( $refURL['query'] ) ) {
								parse_str($refURL['query'], $queryVars);
								if(isset($queryVars[$q])){
									$res['searchTerms'] = urlencode($queryVars[$q]);
								}
							}
						}
					}
				}
				if($res['extReferer']){
					if ( isset( $referringPage ) && stristr( $referringPage['host'], 'google.' ) )
					{
						parse_str( $referringPage['query'], $queryVars );
						// echo $queryVars['q']; // This is the search term used
					}
				}
			}
			$res['browser'] = false;
			if($res['UA']){
				$b = $browscap->getBrowser($res['UA']);
				if($b && $b['Parent'] != 'DefaultProperties'){
					$res['browser'] = array(
						'browser'   => !empty($b['Browser']) ? $b['Browser'] : "",
						'version'   => !empty($b['Version']) ? $b['Version'] : "",
						'platform'  => !empty($b['Platform']) ? $b['Platform'] : "",
						'isMobile'  => !empty($b['isMobileDevice']) ? $b['isMobileDevice'] : "",
						'isCrawler' => !empty($b['Crawler']) ? $b['Crawler'] : "",
					);
				}
				else {
					$IP = wfUtils::getIP();
					$res['browser'] = array(
						'isCrawler' => !(isset($_COOKIE['wordfence_verifiedHuman']) && $this->validateVerifiedHumanCookie($_COOKIE['wordfence_verifiedHuman'], $res['UA'], $IP)) ? 'true' : ''
					);
				}
			}


			if($res['userID']){
				$ud = get_userdata($res['userID']);
				if($ud){
					$res['user'] = array(
						'editLink' => wfUtils::editUserLink($res['userID']),
						'display_name' => $res['display_name'],
						'ID' => $res['userID']
					);
					$res['user']['avatar'] = get_avatar($res['userID'], 16);
				}
			} else {
				$res['user'] = false;
			}
		}
	}

	public function resolveIPs(&$results){
		if(sizeof($results) < 1){ return; }
		$IPs = array();
		foreach($results as &$res){
			if($res['IP']){ //Can also be zero in case of non IP events
				$IPs[] = $res['IP'];
			}
		}
		$IPLocs = wfUtils::getIPsGeo($IPs); //Creates an array with IP as key and data as value

		foreach($results as &$res){
			$ip_printable = wfUtils::inet_ntop($res['IP']);
			if(isset($IPLocs[$ip_printable])){
				$res['loc'] = $IPLocs[$ip_printable];
			} else {
				$res['loc'] = false;
			}
		}
	}
	public function logHitOK(){
		if (!$this->canLogHit) {
			return false;
		}
		if(is_admin()){ return false; } //Don't log admin pageviews
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			if(preg_match('/WordPress\/' . $this->wp_version . '/i', $_SERVER['HTTP_USER_AGENT'])){ return false; } //Ignore requests generated by WP UA.
		}
		if($userID = get_current_user_id()){
			if(wfConfig::get('liveTraf_ignorePublishers') && (current_user_can('publish_posts') || current_user_can('publish_pages')) ){ return false; } //User is logged in and can publish, so we don't log them. 
			$user = get_userdata($userID);
			if($user){
				if(wfConfig::get('liveTraf_ignoreUsers')){
					foreach(explode(',', wfConfig::get('liveTraf_ignoreUsers')) as $ignoreLogin){
						if($user->user_login == $ignoreLogin){
							return false;
						}
					}
				}
			}
		}
		if(wfConfig::get('liveTraf_ignoreIPs')){
			$IPs = explode(',', wfConfig::get('liveTraf_ignoreIPs'));
			$IP = wfUtils::getIP();
			foreach($IPs as $ignoreIP){
				if($ignoreIP == $IP){
					return false;
				}
			}
		}
		if( isset($_SERVER['HTTP_USER_AGENT']) && wfConfig::get('liveTraf_ignoreUA') ){
			if($_SERVER['HTTP_USER_AGENT'] == wfConfig::get('liveTraf_ignoreUA')){
				return false;
			}
		}

		return true;
	}
	private function getDB(){
		if(! $this->db){
			$this->db = new wfDB();
		}
		return $this->db;
	}
	public function firewallBadIPs() {
		$IP = wfUtils::getIP();
		if (wfBlock::isWhitelisted($IP)) {
			return;
		}

		//Range and UA pattern blocking
		$patternBlocks = wfBlock::patternBlocks(true);
		$userAgent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		foreach ($patternBlocks as $b) {
			if ($b->matchRequest($IP, $userAgent, $referrer) !== wfBlock::MATCH_NONE) {
				$b->recordBlock();
				wfActivityReport::logBlockedIP($IP, null, 'advanced');
				$this->currentRequest->actionDescription = 'UA/Referrer/IP Range not allowed';
				$this->do503(3600, "Advanced blocking in effect."); //exits
			}
		}

		// Country blocking
		$countryBlocks = wfBlock::countryBlocks(true);
		foreach ($countryBlocks as $b) {
			$match = $b->matchRequest($IP, false, false);
			if ($match === wfBlock::MATCH_COUNTRY_REDIR_BYPASS) {
				$bypassRedirDest = wfConfig::get('cbl_bypassRedirDest', '');
				wfUtils::doNotCache();
				wp_redirect($bypassRedirDest, 302);
				exit();
			}
			else if ($match === wfBlock::MATCH_COUNTRY_REDIR) {
				$b->recordBlock();
				wfConfig::inc('totalCountryBlocked');
				
				$this->initLogRequest();
				$this->getCurrentRequest()->actionDescription = sprintf(__('blocked access via country blocking and redirected to URL (%s)', 'wordfence'), wfConfig::get('cbl_redirURL'));
				$this->getCurrentRequest()->statusCode = 503;
				if (!$this->getCurrentRequest()->action) {
					$this->currentRequest->action = 'blocked:wordfence';
				}
				$this->logHit();
				
				wfActivityReport::logBlockedIP($IP, null, 'country');
				
				wfUtils::doNotCache();
				wp_redirect(wfConfig::get('cbl_redirURL'), 302);
				exit();
			}
			else if ($match !== wfBlock::MATCH_NONE) {
				$b->recordBlock();
				$this->currentRequest->actionDescription = __('blocked access via country blocking', 'wordfence');
				wfConfig::inc('totalCountryBlocked');
				wfActivityReport::logBlockedIP($IP, null, 'country');
				$this->do503(3600, __('Access from your area has been temporarily limited for security reasons', 'wordfence'));
			}
		}

		//Specific IP blocks
		$ipBlock = wfBlock::findIPBlock($IP);
		if ($ipBlock !== false) {
			$ipBlock->recordBlock();
			$secsToGo = max(0, $ipBlock->expiration - time());
			if (wfConfig::get('other_WFNet') && self::isAuthRequest()) { //It's an auth request and this IP has been blocked
				$this->getCurrentRequest()->action = 'blocked:wfsnrepeat';
				wordfence::wfsnReportBlockedAttempt($IP, 'login');
			}
			$reason = $ipBlock->reason;
			if ($ipBlock->type == wfBlock::TYPE_IP_MANUAL || $ipBlock->type == wfBlock::TYPE_IP_AUTOMATIC_PERMANENT) {
				$reason = __('Manual block by administrator', 'wordfence');
			}
			$this->do503($secsToGo, $reason); //exits
		}
	}

	private function takeBlockingAction($configVar, $reason) {
		if ($this->googleSafetyCheckOK()) {
			$action = wfConfig::get($configVar . '_action');
			if (!$action) {
				return;
			}
			
			$IP = wfUtils::getIP();
			$secsToGo = 0;
			if ($action == 'block') { //Rate limited - block temporarily
				$secsToGo = wfBlock::blockDuration();
				wfBlock::createRateBlock($reason, $IP, $secsToGo);
				wfActivityReport::logBlockedIP($IP, null, 'throttle');
				$this->tagRequestForBlock($reason);
				
				if (wfConfig::get('alertOn_block')) {
					wordfence::alert("Blocking IP {$IP}", "Wordfence has blocked IP address {$IP}.\nThe reason is: \"{$reason}\".", $IP);
				}
				wordfence::status(2, 'info', "Blocking IP {$IP}. {$reason}");
			}
			else if ($action == 'throttle') { //Rate limited - throttle
				$secsToGo = wfBlock::rateLimitThrottleDuration();
				wfBlock::createRateThrottle($reason, $IP, $secsToGo);
				wfActivityReport::logBlockedIP($IP, null, 'throttle');
				
				wordfence::status(2, 'info', "Throttling IP {$IP}. {$reason}");
				wfConfig::inc('totalIPsThrottled');
			}
			$this->do503($secsToGo, $reason);
		}
		
		return;
	}
	
	/**
	 * Test if the current request is for wp-login.php or xmlrpc.php
	 *
	 * @return boolean
	 */
	private static function isAuthRequest() {
		if ((strpos($_SERVER['REQUEST_URI'], '/wp-login.php') !== false)) {
			return true;
		}
		return false;
	}
	
	public function do503($secsToGo, $reason){
		$this->initLogRequest();
		$this->currentRequest->statusCode = 503;
		if (!$this->currentRequest->action) {
			$this->currentRequest->action = 'blocked:wordfence';
		}
		if (!$this->currentRequest->actionDescription) {
			$this->currentRequest->actionDescription = "blocked: " . $reason;
		}
		
		$this->logHit();

		wfConfig::inc('total503s');
		wfUtils::doNotCache();
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		if($secsToGo){
			header('Retry-After: ' . $secsToGo);
		}
		require_once('wf503.php');
		exit();
	}
	private function redirect($URL){
		wfUtils::doNotCache();
		wp_redirect($URL, 302);
		exit();
	}
	private function googleSafetyCheckOK(){ //returns true if OK to block. Returns false if we must not block.
		$cacheKey = md5( (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . ' ' . wfUtils::getIP());
		//Cache so we can call this multiple times in one request
		if(! isset(self::$gbSafeCache[$cacheKey])){
			$nb = wfConfig::get('neverBlockBG');
			if($nb == 'treatAsOtherCrawlers'){
				self::$gbSafeCache[$cacheKey] = true; //OK to block because we're treating google like everyone else
			} else if($nb == 'neverBlockUA' || $nb == 'neverBlockVerified'){
				if(wfCrawl::isGoogleCrawler()){ //Check the UA using regex
					if($nb == 'neverBlockVerified'){
						if(wfCrawl::isVerifiedGoogleCrawler(wfUtils::getIP())){ //UA check passed, now verify using PTR if configured to
							self::$gbSafeCache[$cacheKey] = false; //This is a verified Google crawler, so no we can't block it
						} else {
							self::$gbSafeCache[$cacheKey] = true; //This is a crawler claiming to be Google but it did not verify
						}
					} else { //neverBlockUA
						self::$gbSafeCache[$cacheKey] = false; //User configured us to only do a UA check and this claims to be google so don't block
					}
				} else {
					self::$gbSafeCache[$cacheKey] = true; //This isn't a Google UA, so it's OK to block
				}
			} else {
				//error_log("Wordfence error: neverBlockBG option is not set.");
				self::$gbSafeCache[$cacheKey] = false; //Oops the config option is not set. This should never happen because it's set on install. So we return false to indicate it's not OK to block just for safety.
			}
		}
		if(! isset(self::$gbSafeCache[$cacheKey])){
			//error_log("Wordfence assertion fail in googleSafetyCheckOK: cached value is not set.");
			return false; //for safety
		}
		return self::$gbSafeCache[$cacheKey]; //return cached value
	}
	public function addStatus($level, $type, $msg){
		//$msg = '[' . sprintf('%.2f', memory_get_usage(true) / (1024 * 1024)) . '] ' . $msg;
		$this->getDB()->queryWrite("insert into " . $this->statusTable . " (ctime, level, type, msg) values (%s, %d, '%s', '%s')", sprintf('%.6f', microtime(true)), $level, $type, $msg);
	}
	public function getStatusEvents($lastCtime){
		if($lastCtime < 1){
			$lastCtime = $this->getDB()->querySingle("select ctime from " . $this->statusTable . " order by ctime desc limit 1000,1");
			if(! $lastCtime){
				$lastCtime = 0;
			}
		}
		$results = $this->getDB()->querySelect("select ctime, level, type, msg from " . $this->statusTable . " where ctime > %f order by ctime asc", $lastCtime);
		$timeOffset = 3600 * get_option('gmt_offset');
		foreach($results as &$rec){
			//$rec['timeAgo'] = wfUtils::makeTimeAgo(time() - $rec['ctime']);
			$rec['date'] = date('M d H:i:s', $rec['ctime'] + $timeOffset);
			$rec['msg'] = wp_kses_data( (string) $rec['msg']);
		}
		return $results;
	}
	public function getSummaryEvents(){
		$results = $this->getDB()->querySelect("select ctime, level, type, msg from " . $this->statusTable . " where level = 10 order by ctime desc limit 100");
		$timeOffset = 3600 * get_option('gmt_offset');
		foreach($results as &$rec){
			$rec['date'] = date('M d H:i:s', $rec['ctime'] + $timeOffset);
			if(strpos($rec['msg'], 'SUM_PREP:') === 0){
				break;
			}
		}
		return array_reverse($results);
	}

	/**
	 * @return string
	 */
	public function getGooglePattern() {
		return $this->googlePattern;
	}

}

/**
 *
 */
class wfUserIPRange {

	/**
	 * @var string|null
	 */
	private $ip_string;

	/**
	 * @param string|null $ip_string
	 */
	public function __construct($ip_string = null) {
		$this->setIPString($ip_string);
	}

	/**
	 * Check if the supplied IP address is within the user supplied range.
	 *
	 * @param string $ip
	 * @return bool
	 */
	public function isIPInRange($ip) {
		$ip_string = $this->getIPString();
		
		if (strpos($ip_string, '/') !== false) { //CIDR range -- 127.0.0.1/24
			return wfUtils::subnetContainsIP($ip_string, $ip);
		}
		else if (strpos($ip_string, '[') !== false) //Bracketed range -- 127.0.0.[1-100]
		{
			// IPv4 range
			if (strpos($ip_string, '.') !== false && strpos($ip, '.') !== false) {
				// IPv4-mapped-IPv6
				if (preg_match('/:ffff:([^:]+)$/i', $ip_string, $matches)) {
					$ip_string = $matches[1];
				}
				if (preg_match('/:ffff:([^:]+)$/i', $ip, $matches)) {
					$ip = $matches[1];
				}
				
				// Range check
				if (preg_match('/\[\d+\-\d+\]/', $ip_string)) {
					$IPparts = explode('.', $ip);
					$whiteParts = explode('.', $ip_string);
					$mismatch = false;
					if (count($whiteParts) != 4 || count($IPparts) != 4) {
						return false;
					}
					
					for ($i = 0; $i <= 3; $i++) {
						if (preg_match('/^\[(\d+)\-(\d+)\]$/', $whiteParts[$i], $m)) {
							if ($IPparts[$i] < $m[1] || $IPparts[$i] > $m[2]) {
								$mismatch = true;
							}
						}
						else if ($whiteParts[$i] != $IPparts[$i]) {
							$mismatch = true;
						}
					}
					if ($mismatch === false) {
						return true; // Is whitelisted because we did not get a mismatch
					}
				}
				else if ($ip_string == $ip) {
					return true;
				}
				
				// IPv6 range
			}
			else if (strpos($ip_string, ':') !== false && strpos($ip, ':') !== false) {
				$ip = strtolower(wfUtils::expandIPv6Address($ip));
				$ip_string = strtolower(self::expandIPv6Range($ip_string));
				if (preg_match('/\[[a-f0-9]+\-[a-f0-9]+\]/i', $ip_string)) {
					$IPparts = explode(':', $ip);
					$whiteParts = explode(':', $ip_string);
					$mismatch = false;
					if (count($whiteParts) != 8 || count($IPparts) != 8) {
						return false;
					}
					
					for ($i = 0; $i <= 7; $i++) {
						if (preg_match('/^\[([a-f0-9]+)\-([a-f0-9]+)\]$/i', $whiteParts[$i], $m)) {
							$ip_group = hexdec($IPparts[$i]);
							$range_group_from = hexdec($m[1]);
							$range_group_to = hexdec($m[2]);
							if ($ip_group < $range_group_from || $ip_group > $range_group_to) {
								$mismatch = true;
								break;
							}
						}
						else if ($whiteParts[$i] != $IPparts[$i]) {
							$mismatch = true;
							break;
						}
					}
					if ($mismatch === false) {
						return true; // Is whitelisted because we did not get a mismatch
					}
				}
				else if ($ip_string == $ip) {
					return true;
				}
			}
		}
		else if (strpos($ip_string, '-') !== false) { //Linear range -- 127.0.0.1 - 127.0.1.100
			list($ip1, $ip2) = explode('-', $ip_string);
			$ip1N = wfUtils::inet_pton($ip1);
			$ip2N = wfUtils::inet_pton($ip2);
			$ipN = wfUtils::inet_pton($ip);
			return (strcmp($ip1N, $ipN) <= 0 && strcmp($ip2N, $ipN) >= 0);
		}
		else { //Treat as a literal IP
			$ip1 = @wfUtils::inet_pton($ip_string);
			$ip2 = @wfUtils::inet_pton($ip);
			if ($ip1 !== false && $ip1 == $ip2) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Expand a compressed printable range representation of an IPv6 address.
	 *
	 * @todo Hook up exceptions for better error handling.
	 * @todo Allow IPv4 mapped IPv6 addresses (::ffff:192.168.1.1).
	 * @param string $ip_range
	 * @return string
	 */
	public static function expandIPv6Range($ip_range) {
		$colon_count = substr_count($ip_range, ':');
		$dbl_colon_count = substr_count($ip_range, '::');
		if ($dbl_colon_count > 1) {
			return false;
		}
		$dbl_colon_pos = strpos($ip_range, '::');
		if ($dbl_colon_pos !== false) {
			$ip_range = str_replace('::', str_repeat(':0000',
					(($dbl_colon_pos === 0 || $dbl_colon_pos === strlen($ip_range) - 2) ? 9 : 8) - $colon_count) . ':', $ip_range);
			$ip_range = trim($ip_range, ':');
		}
		$colon_count = substr_count($ip_range, ':');
		if ($colon_count != 7) {
			return false;
		}

		$groups = explode(':', $ip_range);
		$expanded = '';
		foreach ($groups as $group) {
			if (preg_match('/\[([a-f0-9]{1,4})\-([a-f0-9]{1,4})\]/i', $group, $matches)) {
				$expanded .= sprintf('[%s-%s]', str_pad(strtolower($matches[1]), 4, '0', STR_PAD_LEFT), str_pad(strtolower($matches[2]), 4, '0', STR_PAD_LEFT)) . ':';
			} else if (preg_match('/[a-f0-9]{1,4}/i', $group)) {
				$expanded .= str_pad(strtolower($group), 4, '0', STR_PAD_LEFT) . ':';
			} else {
				return false;
			}
		}
		return trim($expanded, ':');
	}

	/**
	 * @return bool
	 */
	public function isValidRange() {
		return $this->isValidCIDRRange() || $this->isValidBracketedRange() || $this->isValidLinearRange() || wfUtils::isValidIP($this->getIPString());
	}
	
	public function isValidCIDRRange() { //e.g., 192.0.2.1/24
		$ip_string = $this->getIPString();
		if (preg_match('/[^0-9a-f:\/\.]/i', $ip_string)) { return false; }
		return wfUtils::isValidCIDRRange($ip_string);
	}
	
	public function isValidBracketedRange() { //e.g., 192.0.2.[1-10]
		$ip_string = $this->getIPString();
		if (preg_match('/[^0-9a-f:\.\[\]\-]/i', $ip_string)) { return false; }
		if (strpos($ip_string, '.') !== false) { //IPv4
			if (preg_match_all('/(\d+)/', $ip_string, $matches) > 0) {
				foreach ($matches[1] as $match) {
					$group = (int) $match;
					if ($group > 255 || $group < 0) {
						return false;
					}
				}
			}
			
			$group_regex = '([0-9]{1,3}|\[[0-9]{1,3}\-[0-9]{1,3}\])';
			return preg_match('/^' . str_repeat("{$group_regex}\\.", 3) . $group_regex . '$/i', $ip_string) > 0;
		}
		
		//IPv6
		if (strpos($ip_string, '::') !== false) {
			$ip_string = self::expandIPv6Range($ip_string);
		}
		if (!$ip_string) {
			return false;
		}
		$group_regex = '([a-f0-9]{1,4}|\[[a-f0-9]{1,4}\-[a-f0-9]{1,4}\])';
		return preg_match('/^' . str_repeat("$group_regex:", 7) . $group_regex . '$/i', $ip_string) > 0;
	}
	
	public function isValidLinearRange() { //e.g., 192.0.2.1-192.0.2.100
		$ip_string = $this->getIPString();
		if (preg_match('/[^0-9a-f:\.\-]/i', $ip_string)) { return false; }
		list($ip1, $ip2) = explode("-", $ip_string);
		$ip1N = @wfUtils::inet_pton($ip1);
		$ip2N = @wfUtils::inet_pton($ip2);
		
		if ($ip1N === false || !wfUtils::isValidIP($ip1) || $ip2N === false || !wfUtils::isValidIP($ip2)) {
			return false;
		}
		
		return strcmp($ip1N, $ip2N) <= 0;
	}
	
	public function isMixedRange() { //e.g., 192.0.2.1-2001:db8::ffff
		$ip_string = $this->getIPString();
		if (preg_match('/[^0-9a-f:\.\-]/i', $ip_string)) { return false; }
		list($ip1, $ip2) = explode("-", $ip_string);
		
		$ipv4Count = 0;
		$ipv4Count += filter_var($ip1, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false ? 1 : 0;
		$ipv4Count += filter_var($ip2, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false ? 1 : 0;
		
		$ipv6Count = 0;
		$ipv6Count += filter_var($ip1, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false ? 1 : 0;
		$ipv6Count += filter_var($ip2, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false ? 1 : 0;
		
		if ($ipv4Count != 2 && $ipv6Count != 2) { 
			return true;
		}
		
		return false;
	}

	protected function _sanitizeIPRange($ip_string) {
		$ip_string = preg_replace('/\s/', '', $ip_string); //Strip whitespace
		$ip_string = preg_replace('/[\\x{2013}-\\x{2015}]/u', '-', $ip_string); //Non-hyphen dashes to hyphen
		$ip_string = strtolower($ip_string);
		
		if (preg_match('/^\d+-\d+$/', $ip_string)) { //v5 32 bit int style format
			list($start, $end) = explode('-', $ip_string);
			$start = long2ip($start);
			$end = long2ip($end);
			$ip_string = "{$start}-{$end}";
		}
		
		return $ip_string;
	}

	/**
	 * @return string|null
	 */
	public function getIPString() {
		return $this->ip_string;
	}

	/**
	 * @param string|null $ip_string
	 */
	public function setIPString($ip_string) {
		$this->ip_string = $this->_sanitizeIPRange($ip_string);
	}
}

/**
 * The function of this class is to detect admin users created via direct access to the database (in other words, not
 * through WordPress).
 */
class wfAdminUserMonitor {

	public function isEnabled() {
		$options = wfScanner::shared()->scanOptions();
		$enabled = $options['scansEnabled_suspiciousAdminUsers'];
		if ($enabled && is_multisite()) {
			if (!function_exists('wp_is_large_network')) {
				require_once ABSPATH . WPINC . '/ms-functions.php';
			}
			$enabled = !wp_is_large_network('sites') && !wp_is_large_network('users');
		}
		return $enabled;
	}

	/**
	 *
	 */
	public function createInitialList() {
		$admins = $this->getCurrentAdmins();
		wfConfig::set_ser('adminUserList', $admins);
	}

	/**
	 * @param int $userID
	 */
	public function grantSuperAdmin($userID = null) {
		if ($userID) {
			$this->addAdmin($userID);
		}
	}

	/**
	 * @param int $userID
	 */
	public function revokeSuperAdmin($userID = null) {
		if ($userID) {
			$this->removeAdmin($userID);
		}
	}

	/**
	 * @param int $ID
	 * @param mixed $role
	 * @param mixed $old_roles
	 */
	public function updateToUserRole($ID = null, $role = null, $old_roles = null) {
		$admins = $this->getLoggedAdmins();
		if ($role !== 'administrator' && array_key_exists($ID, $admins)) {
			$this->removeAdmin($ID);
		} else if ($role === 'administrator') {
			$this->addAdmin($ID);
		}
	}

	/**
	 * @return array|bool
	 */
	public function checkNewAdmins() {
		$loggedAdmins = $this->getLoggedAdmins();
		$admins = $this->getCurrentAdmins();
		$suspiciousAdmins = array();
		foreach ($admins as $adminID => $v) {
			if (!array_key_exists($adminID, $loggedAdmins)) {
				$suspiciousAdmins[] = $adminID;
			}
		}
		return $suspiciousAdmins ? $suspiciousAdmins : false;
	}

	/**
	 * Checks if the supplied user ID is suspicious.
	 *
	 * @param int $userID
	 * @return bool
	 */
	public function isAdminUserLogged($userID) {
		$loggedAdmins = $this->getLoggedAdmins();
		return array_key_exists($userID, $loggedAdmins);
	}

	/**
	 * @return array
	 */
	public function getCurrentAdmins() {
		require_once ABSPATH . WPINC . '/user.php';
		if (is_multisite()) {
			if (function_exists("get_sites")) {
				$sites = get_sites(array(
					'network_id' => null,
				));
			}
			else {
				$sites = wp_get_sites(array(
					'network_id' => null,
				));
			}
		} else {
			$sites = array(array(
				'blog_id' => get_current_blog_id(),
			));
		}

		// not very efficient, but the WordPress API doesn't provide a good way to do this.
		$admins = array();
		foreach ($sites as $siteRow) {
			$siteRowArray = (array) $siteRow;
			$user_query = new WP_User_Query(array(
				'blog_id' => $siteRowArray['blog_id'],
				'role'    => 'administrator',
			));
			$users = $user_query->get_results();
			if (is_array($users)) {
				/** @var WP_User $user */
				foreach ($users as $user) {
					$admins[$user->ID] = 1;
				}
			}
		}

		// Add any super admins that aren't also admins on a network
		$superAdmins = get_super_admins();
		foreach ($superAdmins as $userLogin) {
			$user = get_user_by('login', $userLogin);
			if ($user) {
				$admins[$user->ID] = 1;
			}
		}
		return $admins;
	}

	public function getLoggedAdmins() {
		$loggedAdmins = wfConfig::get_ser('adminUserList', false);
		if (!is_array($loggedAdmins)) {
			$this->createInitialList();
			$loggedAdmins = wfConfig::get_ser('adminUserList', false);
		}
		if (!is_array($loggedAdmins)) {
			$loggedAdmins = array();
		}
		return $loggedAdmins;
	}

	/**
	 * @param int $userID
	 */
	public function addAdmin($userID) {
		$loggedAdmins = $this->getLoggedAdmins();
		if (!array_key_exists($userID, $loggedAdmins)) {
			$loggedAdmins[$userID] = 1;
			wfConfig::set_ser('adminUserList', $loggedAdmins);
		}
	}

	/**
	 * @param int $userID
	 */
	public function removeAdmin($userID) {
		$loggedAdmins = $this->getLoggedAdmins();
		if (array_key_exists($userID, $loggedAdmins) && !array_key_exists($userID, $this->getCurrentAdmins())) {
			unset($loggedAdmins[$userID]);
			wfConfig::set_ser('adminUserList', $loggedAdmins);
		}
	}
}

/**
 *
 */
class wfRequestModel extends wfModel {

	private static $actionDataEncodedParams = array(
		'paramKey',
		'paramValue',
		'path',
	);

	/**
	 * @param $actionData
	 * @return mixed|string|void
	 */
	public static function serializeActionData($actionData) {
		if (is_array($actionData)) {
			foreach (self::$actionDataEncodedParams as $key) {
				if (array_key_exists($key, $actionData)) {
					$actionData[$key] = base64_encode($actionData[$key]);
				}
			}
		}
		return json_encode($actionData);
	}

	/**
	 * @param $actionDataJSON
	 * @return mixed|string|void
	 */
	public static function unserializeActionData($actionDataJSON) {
		$actionData = json_decode($actionDataJSON, true);
		if (is_array($actionData)) {
			foreach (self::$actionDataEncodedParams as $key) {
				if (array_key_exists($key, $actionData)) {
					$actionData[$key] = base64_decode($actionData[$key]);
				}
			}
		}
		return $actionData;
	}

	private $columns = array(
		'id',
		'attackLogTime',
		'ctime',
		'IP',
		'jsRun',
		'statusCode',
		'isGoogle',
		'userID',
		'newVisit',
		'URL',
		'referer',
		'UA',
		'action',
		'actionDescription',
		'actionData',
	);

	public function getIDColumn() {
		return 'id';
	}

	public function getTable() {
		return wfDB::networkTable('wfHits');
	}

	public function hasColumn($column) {
		return in_array($column, $this->columns);
	}
	
	public function save() {
		$sapi = @php_sapi_name();
		if ($sapi == "cli") {
			return false;
		}
		
		return parent::save();
	}
}


class wfLiveTrafficQuery {

	protected $validParams = array(
		'id' => 'h.id',
		'ctime' => 'h.ctime',
		'ip' => 'h.ip',
		'jsrun' => 'h.jsrun',
		'statuscode' => 'h.statuscode',
		'isgoogle' => 'h.isgoogle',
		'userid' => 'h.userid',
		'newvisit' => 'h.newvisit',
		'url' => 'h.url',
		'referer' => 'h.referer',
		'ua' => 'h.ua',
		'action' => 'h.action',
		'actiondescription' => 'h.actiondescription',
		'actiondata' => 'h.actiondata',

		// wfLogins
		'user_login' => 'u.user_login',
		'username' => 'l.username',
	);

	/** @var wfLiveTrafficQueryFilterCollection */
	private $filters = array();

	/** @var wfLiveTrafficQueryGroupBy */
	private $groupBy;
	/**
	 * @var float|null
	 */
	private $startDate;
	/**
	 * @var float|null
	 */
	private $endDate;
	/**
	 * @var int
	 */
	private $limit;
	/**
	 * @var int
	 */
	private $offset;

	private $tableName;

	/** @var wfLog */
	private $wfLog;

	/**
	 * wfLiveTrafficQuery constructor.
	 *
	 * @param wfLog $wfLog
	 * @param wfLiveTrafficQueryFilterCollection $filters
	 * @param wfLiveTrafficQueryGroupBy $groupBy
	 * @param float $startDate
	 * @param float $endDate
	 * @param int $limit
	 * @param int $offset
	 */
	public function __construct($wfLog, $filters = null, $groupBy = null, $startDate = null, $endDate = null, $limit = 20, $offset = 0) {
		$this->wfLog = $wfLog;
		$this->filters = $filters;
		$this->groupBy = $groupBy;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->limit = $limit;
		$this->offset = $offset;
	}

	/**
	 * @return array|null|object
	 */
	public function execute() {
		global $wpdb;
		$sql = $this->buildQuery();
		$results = $wpdb->get_results($sql, ARRAY_A);
		$this->getWFLog()->processGetHitsResults('', $results);
		
		$verifyCrawlers = false;
		if ($this->filters !== null && count($this->filters->getFilters()) > 0) {
			$filters = $this->filters->getFilters();
			foreach ($filters as $f) {
				if (strtolower($f->getParam()) == "isgoogle") {
					$verifyCrawlers = true;
					break;
				}
			}
		}
		
		foreach ($results as $key => &$row) {
			if ($row['isGoogle'] && $verifyCrawlers) {
				if (!wfCrawl::isVerifiedGoogleCrawler($row['IP'], $row['UA'])) {
					unset($results[$key]); //foreach copies $results and iterates on the copy, so it is safe to mutate $results within the loop
					continue;
				}
			}
			
			$row['actionData'] = (array) json_decode($row['actionData'], true);
		}
		return array_values($results);
	}

	/**
	 * @return string
	 * @throws wfLiveTrafficQueryException
	 */
	public function buildQuery() {
		global $wpdb;
		$filters = $this->getFilters();
		$groupBy = $this->getGroupBy();
		$startDate = $this->getStartDate();
		$endDate = $this->getEndDate();
		$limit = absint($this->getLimit());
		$offset = absint($this->getOffset());

		$wheres = array("h.action != 'logged:waf'", "h.action != 'scan:detectproxy'");
		if ($startDate) {
			$wheres[] = $wpdb->prepare('h.ctime > %f', $startDate);
		}
		if ($endDate) {
			$wheres[] = $wpdb->prepare('h.ctime < %f', $endDate);
		}

		if ($filters instanceof wfLiveTrafficQueryFilterCollection) {
			$filtersSQL = $filters->toSQL();
			if ($filtersSQL) {
				$wheres[] = $filtersSQL;
			}
		}

		$orderBy = 'ORDER BY h.ctime DESC';
		$select = ', l.username';
		$groupBySQL = '';
		if ($groupBy && $groupBy->validate()) {
			$groupBySQL = "GROUP BY {$groupBy->getParam()}";
			$orderBy = 'ORDER BY hitCount DESC';
			$select .= ', COUNT(h.id) as hitCount, MAX(h.ctime) AS lastHit, u.user_login AS username';
			
			if ($groupBy->getParam() == 'user_login') {
				$wheres[] = 'user_login IS NOT NULL';
			}
			else if ($groupBy->getParam() == 'action') {
				$wheres[] = '(statusCode = 403 OR statusCode = 503)';
			}
		}
		
		$where = join(' AND ', $wheres);
		if ($where) {
			$where = 'WHERE ' . $where;
		}
		if (!$limit || $limit > 1000) {
			$limit = 20;
		}
		$limitSQL = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		
		$table_wfLogins = wfDB::networkTable('wfLogins');
		$sql = <<<SQL
SELECT h.*, u.display_name{$select} FROM {$this->getTableName()} h
LEFT JOIN {$wpdb->users} u on h.userID = u.ID
LEFT JOIN {$table_wfLogins} l on h.id = l.hitID
$where
$groupBySQL
$orderBy
$limitSQL
SQL;

		return $sql;
	}

	/**
	 * @param $param
	 * @return bool
	 */
	public function isValidParam($param) {
		return array_key_exists(strtolower($param), $this->validParams);
	}

	/**
	 * @param $getParam
	 * @return bool|string
	 */
	public function getColumnFromParam($getParam) {
		$getParam = strtolower($getParam);
		if (array_key_exists($getParam, $this->validParams)) {
			return $this->validParams[$getParam];
		}
		return false;
	}

	/**
	 * @return wfLiveTrafficQueryFilterCollection
	 */
	public function getFilters() {
		return $this->filters;
	}

	/**
	 * @param wfLiveTrafficQueryFilterCollection $filters
	 */
	public function setFilters($filters) {
		$this->filters = $filters;
	}

	/**
	 * @return float|null
	 */
	public function getStartDate() {
		return $this->startDate;
	}

	/**
	 * @param float|null $startDate
	 */
	public function setStartDate($startDate) {
		$this->startDate = $startDate;
	}

	/**
	 * @return float|null
	 */
	public function getEndDate() {
		return $this->endDate;
	}

	/**
	 * @param float|null $endDate
	 */
	public function setEndDate($endDate) {
		$this->endDate = $endDate;
	}

	/**
	 * @return wfLiveTrafficQueryGroupBy
	 */
	public function getGroupBy() {
		return $this->groupBy;
	}

	/**
	 * @param wfLiveTrafficQueryGroupBy $groupBy
	 */
	public function setGroupBy($groupBy) {
		$this->groupBy = $groupBy;
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

	/**
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset) {
		$this->offset = $offset;
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		if ($this->tableName === null) {
			$this->tableName = wfDB::networkTable('wfHits');
		}
		return $this->tableName;
	}

	/**
	 * @param string $tableName
	 */
	public function setTableName($tableName) {
		$this->tableName = $tableName;
	}

	/**
	 * @return wfLog
	 */
	public function getWFLog() {
		return $this->wfLog;
	}

	/**
	 * @param wfLog $wfLog
	 */
	public function setWFLog($wfLog) {
		$this->wfLog = $wfLog;
	}
}

class wfLiveTrafficQueryFilterCollection {

	private $filters = array();

	/**
	 * wfLiveTrafficQueryFilterCollection constructor.
	 *
	 * @param array $filters
	 */
	public function __construct($filters = array()) {
		$this->filters = $filters;
	}

	public function toSQL() {
		$params = array();
		$sql = '';
		$filters = $this->getFilters();
		if ($filters) {
			/** @var wfLiveTrafficQueryFilter $filter */
			foreach ($filters as $filter) {
				$params[$filter->getParam()][] = $filter;
			}
		}

		foreach ($params as $param => $filters) {
			// $sql .= '(';
			$filtersSQL = '';
			foreach ($filters as $filter) {
				$filterSQL = $filter->toSQL();
				if ($filterSQL) {
					$filtersSQL .= $filterSQL . ' OR ';
				}
			}
			if ($filtersSQL) {
				$sql .= '(' . substr($filtersSQL, 0, -4) . ') AND ';
			}
		}
		if ($sql) {
			$sql = substr($sql, 0, -5);
		}
		return $sql;
	}

	public function addFilter($filter) {
		$this->filters[] = $filter;
	}

	/**
	 * @return array
	 */
	public function getFilters() {
		return $this->filters;
	}

	/**
	 * @param array $filters
	 */
	public function setFilters($filters) {
		$this->filters = $filters;
	}
}

class wfLiveTrafficQueryFilter {

	private $param;
	private $operator;
	private $value;

	protected $validOperators = array(
		'=',
		'!=',
		'contains',
		'match',
		'hregexp',
		'hnotregexp',
	);

	/**
	 * @var wfLiveTrafficQuery
	 */
	private $query;

	/**
	 * wfLiveTrafficQueryFilter constructor.
	 *
	 * @param wfLiveTrafficQuery $query
	 * @param string $param
	 * @param string $operator
	 * @param string $value
	 */
	public function __construct($query, $param, $operator, $value) {
		$this->query = $query;
		$this->param = $param;
		$this->operator = $operator;
		$this->value = $value;
	}

	/**
	 * @return string|void
	 */
	public function toSQL() {
		$sql = '';
		if ($this->validate()) {
			/** @var wpdb $wpdb */
			global $wpdb;
			$operator = $this->getOperator();
			$param = $this->getQuery()->getColumnFromParam($this->getParam());
			if (!$param) {
				return $sql;
			}
			$value = $this->getValue();
			switch ($operator) {
				case 'contains':
					$like = addcslashes($value, '_%\\');
					$sql = $wpdb->prepare("$param LIKE %s", "%$like%");
					break;

				case 'match':
					$sql = $wpdb->prepare("$param LIKE %s", $value);
					break;
				
				case 'hregexp':
					$sql = $wpdb->prepare("HEX($param) REGEXP %s", $value);
					break;
				
				case 'hnotregexp':
					$sql = $wpdb->prepare("HEX($param) NOT REGEXP %s", $value);
					break;

				default:
					$sql = $wpdb->prepare("$param $operator %s", $value);
					break;
			}
		}
		return $sql;
	}

	/**
	 * @return bool
	 */
	public function validate() {
		$valid = $this->isValidParam($this->getParam()) && $this->isValidOperator($this->getOperator());
		if (defined('WP_DEBUG') && WP_DEBUG) {
			if (!$valid) {
				throw new wfLiveTrafficQueryException("Invalid param/operator [{$this->getParam()}]/[{$this->getOperator()}] passed to " . get_class($this));
			}
			return true;
		}
		return $valid;
	}

	/**
	 * @param string $param
	 * @return bool
	 */
	public function isValidParam($param) {
		return $this->getQuery() && $this->getQuery()->isValidParam($param);
	}

	/**
	 * @param string $operator
	 * @return bool
	 */
	public function isValidOperator($operator) {
		return in_array($operator, $this->validOperators);
	}

	/**
	 * @return mixed
	 */
	public function getParam() {
		return $this->param;
	}

	/**
	 * @param mixed $param
	 */
	public function setParam($param) {
		$this->param = $param;
	}

	/**
	 * @return mixed
	 */
	public function getOperator() {
		return $this->operator;
	}

	/**
	 * @param mixed $operator
	 */
	public function setOperator($operator) {
		$this->operator = $operator;
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	/**
	 * @return wfLiveTrafficQuery
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * @param wfLiveTrafficQuery $query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}
}

class wfLiveTrafficQueryGroupBy {

	private $param;

	/**
	 * @var wfLiveTrafficQuery
	 */
	private $query;

	/**
	 * wfLiveTrafficQueryGroupBy constructor.
	 *
	 * @param wfLiveTrafficQuery $query
	 * @param string $param
	 */
	public function __construct($query, $param) {
		$this->query = $query;
		$this->param = $param;
	}

	/**
	 * @return bool
	 * @throws wfLiveTrafficQueryException
	 */
	public function validate() {
		$valid = $this->isValidParam($this->getParam());
		if (defined('WP_DEBUG') && WP_DEBUG) {
			if (!$valid) {
				throw new wfLiveTrafficQueryException("Invalid param [{$this->getParam()}] passed to " . get_class($this));
			}
			return true;
		}
		return $valid;
	}

	/**
	 * @param string $param
	 * @return bool
	 */
	public function isValidParam($param) {
		return $this->getQuery() && $this->getQuery()->isValidParam($param);
	}

	/**
	 * @return wfLiveTrafficQuery
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * @param wfLiveTrafficQuery $query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}

	/**
	 * @return mixed
	 */
	public function getParam() {
		return $this->param;
	}

	/**
	 * @param mixed $param
	 */
	public function setParam($param) {
		$this->param = $param;
	}

}


class wfLiveTrafficQueryException extends Exception {

}

class wfErrorLogHandler {
	public static function getErrorLogs($deepSearch = false) {
		static $errorLogs = null;
		
		if ($errorLogs === null) {
			$searchPaths = array(ABSPATH, ABSPATH . 'wp-admin', ABSPATH . 'wp-content');
			
			$homePath = get_home_path();
			if (!in_array($homePath, $searchPaths)) {
				$searchPaths[] = $homePath;
			}
			
			$errorLogPath = ini_get('error_log');
			if (!empty($errorLogPath) && !in_array($errorLogPath, $searchPaths)) {
				$searchPaths[] = $errorLogPath;
			}
			
			$errorLogs = array();
			foreach ($searchPaths as $s) {
				$errorLogs = array_merge($errorLogs, self::_scanForLogs($s, $deepSearch));
			}
		}
		return $errorLogs;
	}
	
	private static function _scanForLogs($path, $deepSearch = false) {
		static $processedFolders = array(); //Protection for endless loops caused by symlinks
		if (is_file($path)) {
			$file = basename($path);
			if (preg_match('#(?:error_log(\-\d+)?$|\.log$)#i', $file)) {
				return array($path => is_readable($path));
			}
			return array();
		}
		
		$path = untrailingslashit($path);
		$contents = @scandir($path);
		if (!is_array($contents)) {
			return array();
		}
		
		$processedFolders[$path] = true;
		$errorLogs = array();
		foreach ($contents as $name) {
			if ($name == '.' || $name == '..') { continue; }
			$testPath = $path . DIRECTORY_SEPARATOR . $name;
			if (!array_key_exists($testPath, $processedFolders)) {
				if ((is_dir($testPath) && $deepSearch) || !is_dir($testPath)) {
					$errorLogs = array_merge($errorLogs, self::_scanForLogs($testPath, $deepSearch));
				}
			}
		}
		return $errorLogs;
	}
	
	public static function outputErrorLog($path) {
		$errorLogs = self::getErrorLogs();
		if (!isset($errorLogs[$path])) { //Only allow error logs we've identified
			status_header(404);
			nocache_headers();
			
			$template = get_404_template();
			if ($template && file_exists($template)) {
				include($template);
			}
			exit;
		}
		
		$fh = @fopen($path, 'r');
		if (!$fh) {
			status_header(503);
			nocache_headers();
			echo "503 Service Unavailable";
			exit;
		}
		
		$headersOutputted = false;
		while (!feof($fh)) {
			$data = fread($fh, 1 * 1024 * 1024); //read 1 megs max per chunk
			if ($data === false) { //Handle the error where the file was reported readable but we can't actually read it
				status_header(503);
				nocache_headers();
				echo "503 Service Unavailable";
				exit;
			}
		
			if (!$headersOutputted) {
				header('Content-Type: text/plain');
				header('Content-Disposition: attachment; filename="' . basename($path));
				$headersOutputted = true;
			}
			echo $data;
		}
		exit;
	}
}