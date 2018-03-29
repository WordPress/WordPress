<?php

/**
 * Represents an individual block definition.
 * 
 * @property int $id
 * @property int $type One of the TYPE_* constants.
 * @property string $ip The human-readable version of the IP if applicable for the block type.
 * @property int $blockedTime The timestamp the block was created.
 * @property string $reason Description of the block.
 * @property int $lastAttempt Timestamp of the last request blocked. If never, this will be 0.
 * @property int $blockedHits Count of the number of hits blocked.
 * @property int $expiration Timestamp when the block will expire. If never, this will be 0.
 * @property mixed $parameters Variable parameters defining the block (e.g., the matchers for a pattern block).
 * 
 * @property bool $blockLogin For wfBlock::TYPE_COUNTRY only, this is whether or not to block hits to the login page.
 * @property bool $blockSite For wfBlock::TYPE_COUNTRY only, this is whether or not to block hits to the rest of the site.
 * @property array $countries For wfBlock::TYPE_COUNTRY only, this is the list of countries to block.
 * 
 * @property mixed $ipRange For wfBlock::TYPE_PATTERN only, this is the matching IP range if set.
 * @property mixed $hostname For wfBlock::TYPE_PATTERN only, this is the hostname pattern if set.
 * @property mixed $userAgent For wfBlock::TYPE_PATTERN only, this is the user agent pattern if set.
 * @property mixed $referrer For wfBlock::TYPE_PATTERN only, this is the HTTP referrer pattern if set.
 */
class wfBlock {
	//Constants for block record types
	const TYPE_IP_MANUAL = 1; //Same behavior as TYPE_IP_AUTOMATIC_PERMANENT - the reason will be overridden for public display
	const TYPE_WFSN_TEMPORARY = 2;
	const TYPE_COUNTRY = 3;
	const TYPE_PATTERN = 4;
	const TYPE_RATE_BLOCK = 5;
	const TYPE_RATE_THROTTLE = 6;
	const TYPE_LOCKOUT = 7; //Blocks login-related actions only
	const TYPE_IP_AUTOMATIC_TEMPORARY = 8; //Automatic block, still temporary
	const TYPE_IP_AUTOMATIC_PERMANENT = 9; //Automatic block, started as temporary but now permanent as a result of admin action
	
	//Constants to identify the match type of a block record
	const MATCH_NONE = 0;
	const MATCH_IP = 1;
	const MATCH_COUNTRY_BLOCK = 2;
	const MATCH_COUNTRY_REDIR = 3;
	const MATCH_COUNTRY_REDIR_BYPASS = 4;
	const MATCH_PATTERN = 5;
	
	//Duration constants
	const DURATION_FOREVER = 0;
	
	//Constants defining the placeholder IPs for non-IP block records
	const MARKER_COUNTRY = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff\xc0\x00\x02\x01";// 192.0.2.1 TEST-NET-1
	const MARKER_PATTERN = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff\xc0\x00\x02\x02";// 192.0.2.2 TEST-NET-1
	
	private $_id;
	private $_type = false;
	private $_ip = false;
	private $_blockedTime = false;
	private $_reason = false;
	private $_lastAttempt = false;
	private $_blockedHits = false;
	private $_expiration = false;
	private $_parameters = false;
	
	/**
	 * Returns the name of the storage table for the blocks.
	 * 
	 * @return string
	 */
	public static function blocksTable() {
		return wfDB::networkPrefix() . 'wfBlocks7';
	}
	
	/**
	 * Returns a user-displayable name for the corresponding type constant.
	 * 
	 * @param int $type
	 * @return string
	 */
	public static function nameForType($type) {
		switch ($type) {
			case self::TYPE_IP_MANUAL:
			case self::TYPE_IP_AUTOMATIC_TEMPORARY:
			case self::TYPE_IP_AUTOMATIC_PERMANENT:
			case self::TYPE_WFSN_TEMPORARY:
			case self::TYPE_RATE_BLOCK:
				return __('IP Block', 'wordfence');
			case self::TYPE_RATE_THROTTLE:
				return __('IP Throttled', 'wordfence');
			case self::TYPE_LOCKOUT:
				return __('Lockout', 'wordfence');
			case self::TYPE_COUNTRY:
				return __('Country Block', 'wordfence');
			case self::TYPE_PATTERN:
				return __('Advanced Block', 'wordfence');
		}
		
		return __('Unknown', 'wordfence');
	}
	
	/**
	 * Returns the number of seconds for a temporary block to last by default.
	 * 
	 * @return int
	 */
	public static function blockDuration() {
		return (int) wfConfig::get('blockedTime');
	}
	
	/**
	 * Returns the number of seconds for a rate limit throttle to last by default.
	 *
	 * @return int
	 */
	public static function rateLimitThrottleDuration() {
		return 60;
	}
	
	/**
	 * Returns the number of seconds for a lockout to last by default.
	 *
	 * @return int
	 */
	public static function lockoutDuration() {
		return (int) wfConfig::get('loginSec_lockoutMins') * 60;
	}
	
	/**
	 * @param string $IP Should be in dot or colon notation (127.0.0.1 or ::1)
	 * @param bool $forcedWhitelistEntry If provided, returns whether or not the IP is on a forced whitelist (i.e., it's not one the user can delete).
	 * @return bool
	 */
	public static function isWhitelisted($IP, &$forcedWhitelistEntry = null) {
		if ($forcedWhitelistEntry !== null) {
			$forcedWhitelistEntry = false;
		}
		
		foreach (wfUtils::getIPWhitelist() as $subnet) {
			if ($subnet instanceof wfUserIPRange) {
				if ($subnet->isIPInRange($IP)) {
					return true;
				}
			} elseif (wfUtils::subnetContainsIP($subnet, $IP)) {
				if ($forcedWhitelistEntry !== null) {
					$forcedWhitelistEntry = true;
				}
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Validates the payload for block creation. Returns true if valid, otherwise it'll return the first error found.
	 * 
	 * @param $payload
	 * @return bool|string
	 */
	public static function validate($payload) {
		if (!isset($payload['type']) || array_search($payload['type'], array('ip-address', 'country', 'custom-pattern')) === false) { return __('Invalid block type.', 'wordfence'); }
		if (!isset($payload['duration']) || intval($payload['duration']) < 0) { return __('Invalid block duration.', 'wordfence'); }
		if (!isset($payload['reason']) || empty($payload['reason'])) { return __('A block reason must be provided.', 'wordfence'); }
		
		if ($payload['type'] == 'ip-address') {
			if (!isset($payload['ip']) || !filter_var(trim($payload['ip']), FILTER_VALIDATE_IP) || @wfUtils::inet_pton(trim($payload['ip'])) === false) { return __('Invalid IP address.', 'wordfence'); }
			if (self::isWhitelisted(trim($payload['ip']))) { return __('This IP address is in a range of addresses that Wordfence does not block. The IP range may be internal or belong to a service that is safe to allow.', 'wordfence'); }
		}
		else if ($payload['type'] == 'country') {
			if (!isset($payload['blockLogin']) || !isset($payload['blockSite'])) { return __('Nothing selected to block.', 'wordfence'); }
			if (!$payload['blockLogin'] && !$payload['blockSite']) { return __('Nothing selected to block.', 'wordfence'); }
			if (!isset($payload['countries']) || empty($payload['countries']) || !is_array($payload['countries'])) { return __('No countries selected.', 'wordfence'); }
			
			require(WORDFENCE_PATH . 'lib/wfBulkCountries.php'); /** @var array $wfBulkCountries */
			foreach ($payload['countries'] as $code) {
				if (!isset($wfBulkCountries[$code])) {
					return __('An invalid country was selected.', 'wordfence');
				}
			}
		}
		else if ($payload['type'] == 'custom-pattern') {
			$hasOne = false;
			if (isset($payload['ipRange']) && !empty($payload['ipRange'])) {
				$ipRange = new wfUserIPRange($payload['ipRange']);
				if ($ipRange->isValidRange()) {
					if ($ipRange->isMixedRange()) {
						return __('Ranges mixing IPv4 and IPv6 addresses are not supported.', 'wordfence');
					}
					
					$hasOne = true;
				}
				else {
					return __('Invalid IP range.', 'wordfence');
				}
			}
			if (isset($payload['hostname']) && !empty($payload['hostname'])) {
				if (preg_match('/^[a-z0-9\.\*\-]+$/i', $payload['hostname'])) {
					$hasOne = true;
				}
				else {
					return __('Invalid hostname.', 'wordfence');
				}
			}
			if (isset($payload['userAgent']) && !empty($payload['userAgent'])) { $hasOne = true; }
			if (isset($payload['referrer']) && !empty($payload['referrer'])) { $hasOne = true; }
			if (!$hasOne) { return __('No block parameters provided.', 'wordfence'); }
		}
		
		return true;
	}
	
	/**
	 * Creates the block. The $payload value is expected to have been validated prior to calling this.
	 * 
	 * @param $payload
	 */
	public static function create($payload) {
		$type = $payload['type'];
		$duration = max((int) $payload['duration'], 0);
		$reason = $payload['reason'];
		
		if ($type == 'ip-address') {
			$ip = trim($payload['ip']);
			wfBlock::createIP($reason, $ip, $duration);
		}
		else if ($type == 'country') {
			$blockLogin = !!$payload['blockLogin'];
			$blockSite = !!$payload['blockSite'];
			$countries = array_unique($payload['countries']);
			wfBlock::createCountry($reason, $blockLogin, $blockSite, $countries, $duration);
		}
		else if ($type == 'custom-pattern') {
			$ipRange = '';
			if (isset($payload['ipRange']) && !empty($payload['ipRange'])) {
				$ipRange = new wfUserIPRange($payload['ipRange']);
				$ipRange = $ipRange->getIPString();
			}
			$hostname = (isset($payload['hostname']) && !empty($payload['hostname'])) ? $payload['hostname'] : '';
			$userAgent = (isset($payload['userAgent']) && !empty($payload['userAgent'])) ? $payload['userAgent'] : '';
			$referrer = (isset($payload['referrer']) && !empty($payload['referrer'])) ? $payload['referrer'] : '';
			wfBlock::createPattern($reason, $ipRange, $hostname, $userAgent, $referrer, $duration);
		}
	}
	
	/**
	 * Creates an IP block if one doesn't already exist for the given IP. The parameters are expected to have been validated and sanitized prior to calling this.
	 * 
	 * @param string $reason
	 * @param string $ip
	 * @param int $duration Optional. Defaults to forever. This is the number of seconds for the block to last.
	 * @param bool|int $blockedTime Optional. Defaults to the current timestamp.
	 * @param bool|int $lastAttempt Optional. Defaults to 0, which means never.
	 * @param bool|int $blockedHits Optional. Defaults to 0.
	 */
	public static function createIP($reason, $ip, $duration = self::DURATION_FOREVER, $blockedTime = false, $lastAttempt = false, $blockedHits = false, $type = self::TYPE_IP_MANUAL) {
		global $wpdb;
		
		if (self::isWhitelisted($ip)) { return; }
		
		if ($blockedTime === false) {
			$blockedTime = time();
		}
		
		$blocksTable = wfBlock::blocksTable();
		$hasExisting = $wpdb->query($wpdb->prepare("UPDATE `{$blocksTable}` SET `reason` = %s, `expiration` = %d WHERE `expiration` > UNIX_TIMESTAMP() AND `type` = %d AND `IP` = %s", $reason, ($duration ? $blockedTime + $duration : $duration), $type, wfUtils::inet_pton($ip)));
		if (!$hasExisting) {
			$wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, NULL)", $type, wfUtils::inet_pton($ip), $blockedTime, $reason, (int) $lastAttempt, (int) $blockedHits, ($duration ? $blockedTime + $duration : $duration)));
			
			wfConfig::inc('totalIPsBlocked');
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Creates an IP block for a WFSN response if one doesn't already exist for the given IP. The parameters are expected to have been validated and sanitized prior to calling this.
	 *
	 * @param string $reason
	 * @param string $ip
	 * @param int $duration This is the number of seconds for the block to last.
	 * @param bool|int $blockedTime Optional. Defaults to the current timestamp.
	 * @param bool|int $lastAttempt Optional. Defaults to 0, which means never.
	 * @param bool|int $blockedHits Optional. Defaults to 0.
	 */
	public static function createWFSN($reason, $ip, $duration, $blockedTime = false, $lastAttempt = false, $blockedHits = false) {
		global $wpdb;
		
		if (self::isWhitelisted($ip)) { return; }
		
		if ($blockedTime === false) {
			$blockedTime = time();
		}
		
		$blocksTable = wfBlock::blocksTable();
		$hasExisting = $wpdb->query($wpdb->prepare("UPDATE `{$blocksTable}` SET `reason` = %s, `expiration` = %d WHERE `expiration` > UNIX_TIMESTAMP() AND `type` = %d AND `IP` = %s", $reason, ($duration ? $blockedTime + $duration : $duration), self::TYPE_WFSN_TEMPORARY, wfUtils::inet_pton($ip)));
		if (!$hasExisting) {
			$wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, NULL)", self::TYPE_WFSN_TEMPORARY, wfUtils::inet_pton($ip), $blockedTime, $reason, (int) $lastAttempt, (int) $blockedHits, ($duration ? $blockedTime + $duration : $duration)));
			
			wfConfig::inc('totalIPsBlocked');
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Creates an IP block for a rate limit if one doesn't already exist for the given IP. The parameters are expected to have been validated and sanitized prior to calling this.
	 *
	 * @param string $reason
	 * @param string $ip
	 * @param int $duration This is the number of seconds for the block to last.
	 * @param bool|int $blockedTime Optional. Defaults to the current timestamp.
	 * @param bool|int $lastAttempt Optional. Defaults to 0, which means never.
	 * @param bool|int $blockedHits Optional. Defaults to 0.
	 */
	public static function createRateBlock($reason, $ip, $duration, $blockedTime = false, $lastAttempt = false, $blockedHits = false) {
		global $wpdb;
		
		if (self::isWhitelisted($ip)) { return; }
		
		if ($blockedTime === false) {
			$blockedTime = time();
		}
		
		$blocksTable = wfBlock::blocksTable();
		$hasExisting = $wpdb->query($wpdb->prepare("UPDATE `{$blocksTable}` SET `reason` = %s, `expiration` = %d WHERE `expiration` > UNIX_TIMESTAMP() AND `type` = %d AND `IP` = %s", $reason, ($duration ? $blockedTime + $duration : $duration), self::TYPE_RATE_BLOCK, wfUtils::inet_pton($ip)));
		if (!$hasExisting) {
			$wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, NULL)", self::TYPE_RATE_BLOCK, wfUtils::inet_pton($ip), $blockedTime, $reason, (int) $lastAttempt, (int) $blockedHits, ($duration ? $blockedTime + $duration : $duration)));
			
			wfConfig::inc('totalIPsBlocked');
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Creates an IP throttle for a rate limit if one doesn't already exist for the given IP. The parameters are expected to have been validated and sanitized prior to calling this.
	 *
	 * @param string $reason
	 * @param string $ip
	 * @param int $duration This is the number of seconds for the block to last.
	 * @param bool|int $blockedTime Optional. Defaults to the current timestamp.
	 * @param bool|int $lastAttempt Optional. Defaults to 0, which means never.
	 * @param bool|int $blockedHits Optional. Defaults to 0.
	 */
	public static function createRateThrottle($reason, $ip, $duration, $blockedTime = false, $lastAttempt = false, $blockedHits = false) {
		global $wpdb;
		
		if (self::isWhitelisted($ip)) { return; }
		
		if ($blockedTime === false) {
			$blockedTime = time();
		}
		
		$blocksTable = wfBlock::blocksTable();
		$hasExisting = $wpdb->query($wpdb->prepare("UPDATE `{$blocksTable}` SET `reason` = %s, `expiration` = %d WHERE `expiration` > UNIX_TIMESTAMP() AND `type` = %d AND `IP` = %s", $reason, ($duration ? $blockedTime + $duration : $duration), self::TYPE_RATE_THROTTLE, wfUtils::inet_pton($ip)));
		if (!$hasExisting) {
			$wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, NULL)", self::TYPE_RATE_THROTTLE, wfUtils::inet_pton($ip), $blockedTime, $reason, (int) $lastAttempt, (int) $blockedHits, ($duration ? $blockedTime + $duration : $duration)));
			
			wfConfig::inc('totalIPsBlocked');
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Creates a lockout if one doesn't already exist for the given IP. The parameters are expected to have been validated and sanitized prior to calling this.
	 *
	 * @param string $reason
	 * @param string $ip
	 * @param int $duration This is the number of seconds for the block to last.
	 * @param bool|int $blockedTime Optional. Defaults to the current timestamp.
	 * @param bool|int $lastAttempt Optional. Defaults to 0, which means never.
	 * @param bool|int $blockedHits Optional. Defaults to 0.
	 */
	public static function createLockout($reason, $ip, $duration, $blockedTime = false, $lastAttempt = false, $blockedHits = false) {
		global $wpdb;
		
		if (self::isWhitelisted($ip)) { return; }
		
		if ($blockedTime === false) {
			$blockedTime = time();
		}
		
		$blocksTable = wfBlock::blocksTable();
		$hasExisting = $wpdb->query($wpdb->prepare("UPDATE `{$blocksTable}` SET `reason` = %s, `expiration` = %d WHERE `expiration` > UNIX_TIMESTAMP() AND `type` = %d AND `IP` = %s", $reason, ($duration ? $blockedTime + $duration : $duration), self::TYPE_LOCKOUT, wfUtils::inet_pton($ip)));
		if (!$hasExisting) {
			$wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, NULL)", self::TYPE_LOCKOUT, wfUtils::inet_pton($ip), $blockedTime, $reason, (int) $lastAttempt, (int) $blockedHits, ($duration ? $blockedTime + $duration : $duration)));
			
			wfConfig::inc('totalIPsLocked');
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Creates a country block. The parameters are expected to have been validated and sanitized prior to calling this.
	 *
	 * @param string $reason
	 * @param string $blockLogin
	 * @param string $blockSite
	 * @param string $countries
	 * @param int $duration Optional. Defaults to forever. This is the number of seconds for the block to last.
	 * @param bool|int $blockedTime Optional. Defaults to the current timestamp.
	 * @param bool|int $lastAttempt Optional. Defaults to 0, which means never.
	 * @param bool|int $blockedHits Optional. Defaults to 0.
	 */
	public static function createCountry($reason, $blockLogin, $blockSite, $countries, $duration = self::DURATION_FOREVER, $blockedTime = false, $lastAttempt = false, $blockedHits = false) {
		global $wpdb;
		
		if ($blockedTime === false) {
			$blockedTime = time();
		}
		
		$parameters = array(
			'blockLogin' => $blockLogin ? 1 : 0,
			'blockSite' => $blockSite ? 1 : 0,
			'countries' => $countries,
		);
		
		$blocksTable = wfBlock::blocksTable();
		$existing = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `{$blocksTable}` WHERE `type` = %d LIMIT 1", self::TYPE_COUNTRY));
		if ($existing) {
			$wpdb->query($wpdb->prepare("UPDATE `{$blocksTable}` SET `reason` = %s, `parameters` = %s WHERE `id` = %d", $reason, json_encode($parameters), $existing));
		}
		else {
			$wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, %s)", self::TYPE_COUNTRY, self::MARKER_COUNTRY, $blockedTime, $reason, (int) $lastAttempt, (int) $blockedHits, ($duration ? $blockedTime + $duration : $duration), json_encode($parameters)));
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Creates a pattern block. The parameters are expected to have been validated and sanitized prior to calling this.
	 * 
	 * @param string $reason
	 * @param string $ipRange
	 * @param string $hostname
	 * @param string $userAgent
	 * @param string $referrer
	 * @param int $duration Optional. Defaults to forever. This is the number of seconds for the block to last.
	 * @param bool|int $blockedTime Optional. Defaults to the current timestamp.
	 * @param bool|int $lastAttempt Optional. Defaults to 0, which means never.
	 * @param bool|int $blockedHits Optional. Defaults to 0.
	 */
	public static function createPattern($reason, $ipRange, $hostname, $userAgent, $referrer, $duration = self::DURATION_FOREVER, $blockedTime = false, $lastAttempt = false, $blockedHits = false) {
		global $wpdb;
		
		if ($blockedTime === false) {
			$blockedTime = time();
		}
		
		$parameters = array(
			'ipRange' => $ipRange,
			'hostname' => $hostname,
			'userAgent' => $userAgent,
			'referrer' => $referrer,
		);
		
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, %s)", self::TYPE_PATTERN, self::MARKER_PATTERN, $blockedTime, $reason, (int) $lastAttempt, (int) $blockedHits, ($duration ? $blockedTime + $duration : $duration), json_encode($parameters)));
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Removes all expired blocks.
	 */
	public static function vacuum() {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query("DELETE FROM `{$blocksTable}` WHERE `expiration` <= UNIX_TIMESTAMP() AND `expiration` != " . self::DURATION_FOREVER);
	}
	
	/**
	 * Imports all valid blocks in $blocks. If $replaceExisting is true, this will remove all permanent blocks prior to the import.
	 * 
	 * @param array $blocks
	 * @param bool $replaceExisting
	 */
	public static function importBlocks($blocks, $replaceExisting = true) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		
		if ($replaceExisting) {
			$wpdb->query("DELETE FROM `{$blocksTable}` WHERE `expiration` = " . self::DURATION_FOREVER);
		}
		
		foreach ($blocks as $b) {
			self::_importBlock($b);
		}
		
		if (!WFWAF_SUBDIRECTORY_INSTALL && class_exists('wfWAFIPBlocksController')) {
			wfWAFIPBlocksController::setNeedsSynchronizeConfigSettings();
		}
	}
	
	/**
	 * Validates the block import record and inserts it if valid. This validation is identical to what is applied to adding one through the UI.
	 * 
	 * @param array $b
	 * @return bool
	 */
	private static function _importBlock($b) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		
		if (!isset($b['type']) || !isset($b['IP']) || !isset($b['blockedTime']) || !isset($b['reason']) || !isset($b['lastAttempt']) || !isset($b['blockedHits'])) { return false; }
		if (empty($b['IP']) || empty($b['reason'])) { return false; }
		
		$ip = @wfUtils::inet_ntop(wfUtils::hex2bin($b['IP']));
		if (!wfUtils::isValidIP($ip)) { return false; }
		
		switch ($b['type']) {
			case self::TYPE_IP_MANUAL:
			case self::TYPE_IP_AUTOMATIC_TEMPORARY:
			case self::TYPE_IP_AUTOMATIC_PERMANENT:
			case self::TYPE_WFSN_TEMPORARY:
			case self::TYPE_RATE_BLOCK:
			case self::TYPE_RATE_THROTTLE:
			case self::TYPE_LOCKOUT:
				if (self::isWhitelisted($ip)) { return false; }
				
				return $wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, NULL)", (int) $b['type'], wfUtils::inet_pton($ip), (int) $b['blockedTime'], $b['reason'], (int) $b['lastAttempt'], (int) $b['blockedHits'], self::DURATION_FOREVER)) !== false;
			case self::TYPE_COUNTRY:
				if (!isset($b['parameters'])) { continue; }
				if (wfUtils::inet_pton($ip) != self::MARKER_COUNTRY) { continue; }
				$parameters = @json_decode($b['parameters'], true);
				if (!isset($parameters['blockLogin']) || !isset($parameters['blockSite']) || !isset($parameters['countries'])) { continue; }
				$parameters['blockLogin'] = wfUtils::truthyToInt($parameters['blockLogin']);
				$parameters['blockSite'] = wfUtils::truthyToInt($parameters['blockSite']);
				
				require(WORDFENCE_PATH . 'lib/wfBulkCountries.php'); /** @var array $wfBulkCountries */
				foreach ($parameters['countries'] as $code) {
					if (!isset($wfBulkCountries[$code])) {
						return false;
					}
				}
				
				$parameters = array('blockLogin' => $parameters['blockLogin'], 'blockSite' => $parameters['blockSite'], 'countries' => $parameters['countries']);
				
				return $wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, %s)", self::TYPE_COUNTRY, self::MARKER_COUNTRY, (int) $b['blockedTime'], $b['reason'], (int) $b['lastAttempt'], (int) $b['blockedHits'], self::DURATION_FOREVER, json_encode($parameters))) !== false;
			case self::TYPE_PATTERN:
				if (!isset($b['parameters'])) { continue; }
				if (wfUtils::inet_pton($ip) != self::MARKER_PATTERN) { return false; }
				$parameters = @json_decode($b['parameters'], true);
				if (!isset($parameters['ipRange']) || !isset($parameters['hostname']) || !isset($parameters['userAgent']) || !isset($parameters['referrer'])) { continue; }
				
				$hasOne = false;
				if (!empty($parameters['ipRange'])) {
					$ipRange = new wfUserIPRange($parameters['ipRange']);
					if ($ipRange->isValidRange()) {
						if ($ipRange->isMixedRange()) {
							return false;
						}
						
						$hasOne = true;
					}
					else {
						return false;
					}
				}
				if (!empty($parameters['hostname'])) {
					if (preg_match('/^[a-z0-9\.\*\-]+$/i', $parameters['hostname'])) {
						$hasOne = true;
					}
					else {
						return false;
					}
				}
				if (!empty($parameters['userAgent'])) { $hasOne = true; }
				if (!empty($parameters['referrer'])) { $hasOne = true; }
				if (!$hasOne) { return false; }
				
				$ipRange = '';
				if (!empty($parameters['ipRange'])) {
					$ipRange = new wfUserIPRange($parameters['ipRange']);
					$ipRange = $ipRange->getIPString();
				}
				$parameters = array(
					'ipRange' => $ipRange,
					'hostname' => $parameters['hostname'],
					'userAgent' => $parameters['userAgent'],
					'referrer' => $parameters['referrer'],
				);
				
				return $wpdb->query($wpdb->prepare("INSERT INTO `{$blocksTable}` (`type`, `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `expiration`, `parameters`) VALUES (%d, %s, %d, %s, %d, %d, %d, %s)", self::TYPE_PATTERN, self::MARKER_PATTERN, (int) $b['blockedTime'], $b['reason'], (int) $b['lastAttempt'], (int) $b['blockedHits'], self::DURATION_FOREVER, json_encode($parameters))) !== false;
		}
		
		return false;
	}
	
	/**
	 * Returns an array suitable for JSON output of all permanent blocks.
	 * 
	 * @return array
	 */
	public static function exportBlocks() {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$query = "SELECT `type`, HEX(`IP`) AS `IP`, `blockedTime`, `reason`, `lastAttempt`, `blockedHits`, `parameters` FROM `{$blocksTable}` WHERE `expiration` = " . self::DURATION_FOREVER;
		$rows = $wpdb->get_results($query, ARRAY_A);
		return $rows;
	}
	
	/**
	 * Returns all unexpired blocks (including lockouts by default), optionally only of the specified types. These are sorted descending by the time created.
	 * 
	 * @param bool $prefetch If true, the full data for the block is fetched rather than using lazy loading.
	 * @param array $ofTypes An optional array of block types to restrict the returned array of blocks to.
	 * @param int $offset The offset to start the result fetch at.
	 * @param int $limit The maximum number of results to return. -1 for all.
	 * @param string $sortColumn The column to sort by.
	 * @param string $sortDirection The direction to sort.
	 * @param string $filter An optional value to filter by.
	 * @return wfBlock[]
	 */
	public static function allBlocks($prefetch = false, $ofTypes = array(), $offset = 0, $limit = -1, $sortColumn = 'type', $sortDirection = 'ascending', $filter = '') {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$columns = '`id`';
		if ($prefetch) {
			$columns = '*';
		}
		
		$filter = trim($filter);
		$filterClause = '';
		if (!empty($filter)) {
			if (wfUtils::isValidIP($filter)) { //e.g., 4.5.6.7, ffe0::, ::0
				$filterClause = '(`IP` = \'' . esc_sql(wfUtils::inet_pton($filter)) . '\' OR `parameters` LIKE \'%' . esc_sql(wfUtils::inet_ntop(wfUtils::inet_pton($filter))) . '%\') AND ';
			}
			else if (strpos($filter, '*') !== false && preg_match('/^(?:\d+\.)(?:\d+\.|\*\.){1,2}(?:\d+|\*)?$/', $filter)) { //e.g., 4.5.*
				$components = explode('.', $filter);
				$regex = '^00000000000000000000FFFF';
				for ($i = 0; $i < 4; $i++) {
					if (isset($components[$i]) && $components[$i] != '*') {
						$regex .= strtoupper(str_pad(dechex($components[$i]), 2, '0', STR_PAD_LEFT));
					}
					else {
						$regex .= '..';
					}
				}
				$regex .= '$';
				$filterClause = 'HEX(`IP`) REGEXP \'' . $regex . '\' AND ';
			}
			else if (strpos($filter, '*') !== false && preg_match('/^(?:[0-9a-f]+:)(?:[0-9a-f]+:|\*:){1,2}(?:[0-9a-f]+|\*)?$/i', $filter)) { //e.g., ffe0:*
				$components = explode(':', $filter);
				$regex = '^';
				for ($i = 0; $i < 4; $i++) {
					if (isset($components[$i])) {
						$regex .= strtoupper(str_pad(dechex($components[$i]), 4, '0', STR_PAD_LEFT));
					}
					else {
						$regex .= '....';
					}
				}
				$regex .= '$';
				$filterClause = 'HEX(`IP`) REGEXP \'' . $regex . '\' AND ';
			}
			else {
				$escapedFilter = esc_sql($filter);
				$filterClause = '(`reason` LIKE \'%' . $escapedFilter . '%\' OR `parameters` LIKE \'%' . $escapedFilter . '%\') AND ';
			}
		}
		
		$sort = 'typeSort';
		switch ($sortColumn) { //Match the display table column to the corresponding schema column
			case 'type':
				//Use default;
				break;
			case 'detail':
				$sort = 'detailSort';
				break;
			case 'ruleAdded':
				$sort = 'blockedTime';
				break;
			case 'reason':
				$sort = 'reason';
				break;
			case 'expiration':
				$sort = 'expiration';
				break;
			case 'blockCount':
				$sort = 'blockedHits';
				break;
			case 'lastAttempt':
				$sort = 'lastAttempt';
				break;
		}
		
		$order = 'ASC';
		if ($sortDirection == 'descending') {
			$order = 'DESC';
		}
		
		$query = "SELECT {$columns}, CASE 
WHEN `type` = " . self::TYPE_COUNTRY . " THEN 0
WHEN `type` = " . self::TYPE_PATTERN . " THEN 1
WHEN `type` = " . self::TYPE_IP_MANUAL . " THEN 2
WHEN `type` = " . self::TYPE_IP_AUTOMATIC_PERMANENT . " THEN 3
WHEN `type` = " . self::TYPE_RATE_BLOCK . " THEN 4
WHEN `type` = " . self::TYPE_RATE_THROTTLE . " THEN 5
WHEN `type` = " . self::TYPE_LOCKOUT . " THEN 6
WHEN `type` = " . self::TYPE_WFSN_TEMPORARY . " THEN 7
WHEN `type` = " . self::TYPE_IP_AUTOMATIC_TEMPORARY . " THEN 8
ELSE 9999
END AS `typeSort`, CASE 
WHEN `type` = " . self::TYPE_COUNTRY . " THEN `parameters`
WHEN `type` = " . self::TYPE_PATTERN . " THEN `parameters`
WHEN `type` = " . self::TYPE_IP_MANUAL . " THEN `IP`
WHEN `type` = " . self::TYPE_IP_AUTOMATIC_PERMANENT . " THEN `IP`
WHEN `type` = " . self::TYPE_RATE_BLOCK . " THEN `IP`
WHEN `type` = " . self::TYPE_RATE_THROTTLE . " THEN `IP`
WHEN `type` = " . self::TYPE_LOCKOUT . " THEN `IP`
WHEN `type` = " . self::TYPE_WFSN_TEMPORARY . " THEN `IP`
WHEN `type` = " . self::TYPE_IP_AUTOMATIC_TEMPORARY . " THEN `IP`
ELSE 9999
END AS `detailSort`
 FROM `{$blocksTable}` WHERE {$filterClause}";
		if (!empty($ofTypes)) {
			$sanitizedTypes = array_map('intval', $ofTypes);
			$query .= "`type` IN (" . implode(', ', $sanitizedTypes) . ') AND ';
		}
		$query .= '(`expiration` = ' . self::DURATION_FOREVER . " OR `expiration` > UNIX_TIMESTAMP()) ORDER BY `{$sort}` {$order}, `id` DESC";
		
		if ($limit > -1) {
			$offset = (int) $offset;
			$limit = (int) $limit;
			$query .= " LIMIT {$offset},{$limit}";
		}
		
		$rows = $wpdb->get_results($query, ARRAY_A);
		$result = array();
		foreach ($rows as $r) {
			if ($prefetch) {
				if ($r['type'] == self::TYPE_COUNTRY || $r['type'] == self::TYPE_PATTERN) {
					$ip = null;
				}
				else {
					$ip = wfUtils::inet_ntop($r['IP']);
				}
				
				$parameters = null;
				if ($r['type'] == self::TYPE_PATTERN || $r['type'] == self::TYPE_COUNTRY) {
					$parameters = @json_decode($r['parameters'], true);
				}
				
				$result[] = new wfBlock($r['id'], $r['type'], $ip, $r['blockedTime'], $r['reason'], $r['lastAttempt'], $r['blockedHits'], $r['expiration'], $parameters);
			}
			else {
				$result[] = new wfBlock($r['id']);
			}
		}
		
		return $result;
	}
	
	/**
	 * Returns all unexpired blocks of types wfBlock::TYPE_IP_MANUAL, wfBlock::TYPE_IP_AUTOMATIC_TEMPORARY, wfBlock::TYPE_IP_AUTOMATIC_PERMANENT, wfBlock::TYPE_WFSN_TEMPORARY, wfBlock::TYPE_RATE_BLOCK, and wfBlock::TYPE_RATE_THROTTLE.
	 *
	 * @param bool $prefetch If true, the full data for the block is fetched rather than using lazy loading.
	 * @return wfBlock[]
	 */
	public static function ipBlocks($prefetch = false) {
		return self::allBlocks($prefetch, array(self::TYPE_IP_MANUAL, self::TYPE_IP_AUTOMATIC_TEMPORARY, self::TYPE_IP_AUTOMATIC_PERMANENT, self::TYPE_WFSN_TEMPORARY, self::TYPE_RATE_BLOCK, self::TYPE_RATE_THROTTLE));
	}
	
	/**
	 * Finds an IP block matching the given IP, returning it if found. Returns false if none are found.
	 * 
	 * @param string $ip
	 * @return bool|wfBlock
	 */
	public static function findIPBlock($ip) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		
		$query = "SELECT * FROM `{$blocksTable}` WHERE ";
		
		$ofTypes = array(self::TYPE_IP_MANUAL, self::TYPE_IP_AUTOMATIC_TEMPORARY, self::TYPE_IP_AUTOMATIC_PERMANENT, self::TYPE_WFSN_TEMPORARY, self::TYPE_RATE_BLOCK, self::TYPE_RATE_THROTTLE);
		$query .= "`type` IN (" . implode(', ', $ofTypes) . ') AND ';
		$query .= "`IP` = %s AND ";
		$query .= '(`expiration` = ' . self::DURATION_FOREVER . ' OR `expiration` > UNIX_TIMESTAMP()) ORDER BY `blockedTime` DESC LIMIT 1';
		
		$r = $wpdb->get_row($wpdb->prepare($query, wfUtils::inet_pton($ip)), ARRAY_A);
		if (is_array($r)) {
			$ip = wfUtils::inet_ntop($r['IP']);
			return new wfBlock($r['id'], $r['type'], $ip, $r['blockedTime'], $r['reason'], $r['lastAttempt'], $r['blockedHits'], $r['expiration'], null);
		}
		return false;
	}
	
	/**
	 * Returns all unexpired blocks of type wfBlock::TYPE_COUNTRY.
	 *
	 * @param bool $prefetch If true, the full data for the block is fetched rather than using lazy loading.
	 * @return wfBlock[]
	 */
	public static function countryBlocks($prefetch = false) {
		return self::allBlocks($prefetch, array(self::TYPE_COUNTRY));
	}
	
	/**
	 * Returns whether or not there is a country block rule.
	 * 
	 * @return bool
	 */
	public static function hasCountryBlock() {
		$countryBlocks = self::countryBlocks();
		return !empty($countryBlocks);
	}
	
	/**
	 * Returns the value for the country blocking bypass cookie.
	 *
	 * @return string
	 */
	public static function countryBlockingBypassCookieValue() {
		$val = wfConfig::get('cbl_cookieVal', false);
		if (!$val) {
			$val = uniqid();
			wfConfig::set('cbl_cookieVal', $val);
		}
		return $val;
	}
	
	/**
	 * Returns all unexpired blocks of type wfBlock::TYPE_PATTERN.
	 * 
	 * @param bool $prefetch If true, the full data for the block is fetched rather than using lazy loading.
	 * @return wfBlock[]
	 */
	public static function patternBlocks($prefetch = false) {
		return self::allBlocks($prefetch, array(self::TYPE_PATTERN));
	}
	
	/**
	 * Returns all unexpired lockouts (type wfBlock::TYPE_LOCKOUT).
	 *
	 * @param bool $prefetch If true, the full data for the block is fetched rather than using lazy loading.
	 * @return wfBlock[]
	 */
	public static function lockouts($prefetch = false) {
		return self::allBlocks($prefetch, array(self::TYPE_LOCKOUT));
	}
	
	/**
	 * Returns the lockout record for the given IP if it exists.
	 * 
	 * @param string $ip
	 * @return bool|wfBlock
	 */
	public static function lockoutForIP($ip) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$blocksTable}` WHERE `IP` = %s AND `type` = %d AND (`expiration` = %d OR `expiration` > UNIX_TIMESTAMP())", wfUtils::inet_pton($ip), self::TYPE_LOCKOUT, self::DURATION_FOREVER), ARRAY_A);
		if ($row) {
			return new wfBlock($row['id'], $row['type'], wfUtils::inet_ntop($row['IP']), $row['blockedTime'], $row['reason'], $row['lastAttempt'], $row['blockedHits'], $row['expiration'], null);
		}
		
		return false;
	}
	
	/**
	 * Removes all blocks whose ID is in the given array.
	 * 
	 * @param array $blockIDs
	 */
	public static function removeBlockIDs($blockIDs) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		
		$blockIDs = array_map('intval', $blockIDs);
		$query = "DELETE FROM `{$blocksTable}` WHERE `id` IN (" . implode(', ', $blockIDs) . ")";
		$wpdb->query($query);
	}
	
	/**
	 * Removes all IP blocks (i.e., manual, wfsn, or rate limited)
	 */
	public static function removeAllIPBlocks() {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query("DELETE FROM `{$blocksTable}` WHERE `type` IN (" . implode(', ', array(self::TYPE_IP_MANUAL, self::TYPE_IP_AUTOMATIC_TEMPORARY, self::TYPE_IP_AUTOMATIC_PERMANENT, self::TYPE_WFSN_TEMPORARY, self::TYPE_RATE_BLOCK, self::TYPE_RATE_THROTTLE, self::TYPE_LOCKOUT)) . ")");
	}
	
	/**
	 * Removes all country blocks
	 */
	public static function removeAllCountryBlocks() {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query("DELETE FROM `{$blocksTable}` WHERE `type` IN (" . implode(', ', array(self::TYPE_COUNTRY)) . ")");
	}
	
	/**
	 * Removes all blocks that were created by WFSN responses.
	 */
	public static function removeTemporaryWFSNBlocks() {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query($wpdb->prepare("DELETE FROM `{$blocksTable}` WHERE `type` = %d", self::TYPE_WFSN_TEMPORARY));
	}
	
	/**
	 * Converts all blocks to non-expiring whose ID is in the given array.
	 * 
	 * @param array $blockIDs
	 */
	public static function makePermanentBlockIDs($blockIDs) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		
		//TODO: revise this if we support user-customizable durations
		$supportedTypes = array(
			self::TYPE_WFSN_TEMPORARY,
			self::TYPE_RATE_BLOCK,
			self::TYPE_RATE_THROTTLE,
			self::TYPE_LOCKOUT,
			self::TYPE_IP_AUTOMATIC_TEMPORARY,
		);
		
		$blockIDs = array_map('intval', $blockIDs);
		$query = $wpdb->prepare("UPDATE `{$blocksTable}` SET `expiration` = %d, `type` = %d WHERE `id` IN (" . implode(', ', $blockIDs) . ") AND `type` IN (" . implode(', ', $supportedTypes) . ") AND (`expiration` > UNIX_TIMESTAMP())", self::DURATION_FOREVER, self::TYPE_IP_AUTOMATIC_PERMANENT);
		$wpdb->query($query);
		
		$supportedTypes = array(
			self::TYPE_IP_MANUAL,
		);
		
		$blockIDs = array_map('intval', $blockIDs);
		$query = $wpdb->prepare("UPDATE `{$blocksTable}` SET `expiration` = %d, `type` = %d WHERE `id` IN (" . implode(', ', $blockIDs) . ") AND `type` IN (" . implode(', ', $supportedTypes) . ") AND (`expiration` > UNIX_TIMESTAMP())", self::DURATION_FOREVER, self::TYPE_IP_MANUAL);
		$wpdb->query($query);
	}
	
	/**
	 * Removes all specific IP blocks and lockouts that can result in the given IP being blocked.
	 * 
	 * @param string $ip
	 */
	public static function unblockIP($ip) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query($wpdb->prepare("DELETE FROM `{$blocksTable}` WHERE `IP` = %s", wfUtils::inet_pton($ip)));
	}
	
	/**
	 * Removes all lockouts that can result in the given IP being blocked.
	 *
	 * @param string $ip
	 */
	public static function unlockOutIP($ip) {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query($wpdb->prepare("DELETE FROM `{$blocksTable}` WHERE `IP` = %s AND `type` = %d", wfUtils::inet_pton($ip), self::TYPE_LOCKOUT));
	}
	
	/**
	 * Constructs a wfBlock instance. This _does not_ create a new record in the table, only fetches or updates an existing one.
	 * 
	 * @param $id
	 * @param bool $type
	 * @param bool $ip
	 * @param bool $blockedTime
	 * @param bool $reason
	 * @param bool $lastAttempt
	 * @param bool $blockedHits
	 * @param bool $expiration
	 * @param bool $parameters
	 */
	public function __construct($id, $type = false, $ip = false, $blockedTime = false, $reason = false, $lastAttempt = false, $blockedHits = false, $expiration = false, $parameters = false) {
		$this->_id = $id;
		$this->_type = $type;
		$this->_ip = $ip;
		$this->_blockedTime = $blockedTime;
		$this->_reason = $reason;
		$this->_lastAttempt = $lastAttempt;
		$this->_blockedHits = $blockedHits;
		$this->_expiration = $expiration;
		$this->_parameters = $parameters;
	}
	
	public function __get($key) {
		switch ($key) {
			case 'id':
				return $this->_id;
			case 'type':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_type;
			case 'ip':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_ip;
			case 'blockedTime':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_blockedTime;
			case 'reason':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_reason;
			case 'lastAttempt':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_lastAttempt;
			case 'blockedHits':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_blockedHits;
			case 'expiration':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_expiration;
			case 'parameters':
				if ($this->_type === false) { $this->_fetch(); }
				return $this->_parameters;
				
			//Country
			case 'blockLogin':
				if ($this->type != self::TYPE_COUNTRY) { throw new OutOfBoundsException("{$key} is not a valid property for this block type"); }
				return $this->parameters['blockLogin'];
			case 'blockSite':
				if ($this->type != self::TYPE_COUNTRY) { throw new OutOfBoundsException("{$key} is not a valid property for this block type"); }
				return $this->parameters['blockSite'];
			case 'countries':
				if ($this->type != self::TYPE_COUNTRY) { throw new OutOfBoundsException("{$key} is not a valid property for this block type"); }
				return $this->parameters['countries'];
				
			//Pattern
			case 'ipRange':
				if ($this->type != self::TYPE_PATTERN) { throw new OutOfBoundsException("{$key} is not a valid property for this block type"); }
				return $this->parameters['ipRange'];
			case 'hostname':
				if ($this->type != self::TYPE_PATTERN) { throw new OutOfBoundsException("{$key} is not a valid property for this block type"); }
				return $this->parameters['hostname'];
			case 'userAgent':
				if ($this->type != self::TYPE_PATTERN) { throw new OutOfBoundsException("{$key} is not a valid property for this block type"); }
				return $this->parameters['userAgent'];
			case 'referrer':
				if ($this->type != self::TYPE_PATTERN) { throw new OutOfBoundsException("{$key} is not a valid property for this block type"); }
				return $this->parameters['referrer'];
		}
		
		throw new OutOfBoundsException("{$key} is not a valid property");
	}
	
	public function __isset($key) {
		switch ($key) {
			case 'id':
			case 'type':
			case 'ip':
			case 'blockedTime':
			case 'reason':
			case 'lastAttempt':
			case 'blockedHits':
			case 'expiration':
				return true;
			case 'parameters':
				if ($this->_type === false) { $this->_fetch(); }
				return !empty($this->_parameters);
			
			//Country
			case 'blockLogin':
				if ($this->type != self::TYPE_COUNTRY) { return false; }
				return !empty($this->parameters['blockLogin']);
			case 'blockSite':
				if ($this->type != self::TYPE_COUNTRY) { return false; }
				return !empty($this->parameters['blockSite']);
			case 'countries':
				if ($this->type != self::TYPE_COUNTRY) { return false; }
				return !empty($this->parameters['countries']);
			
			//Pattern
			case 'ipRange':
				if ($this->type != self::TYPE_PATTERN) { return false; }
				return !empty($this->parameters['ipRange']);
			case 'hostname':
				if ($this->type != self::TYPE_PATTERN) { return false; }
				return !empty($this->parameters['hostname']);
			case 'userAgent':
				if ($this->type != self::TYPE_PATTERN) { return false; }
				return !empty($this->parameters['userAgent']);
			case 'referrer':
				if ($this->type != self::TYPE_PATTERN) { return false; }
				return !empty($this->parameters['referrer']);
		}
		
		return false;
	}
	
	/**
	 * Fetches the record for the block from the database and populates the instance variables.
	 */
	private function _fetch() {
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$blocksTable}` WHERE `id` = %d", $this->id), ARRAY_A);
		if ($row !== null) {
			$this->_type = $row['type'];
			
			$ip = $row['IP'];
			if ($ip == self::MARKER_COUNTRY || $ip == self::MARKER_PATTERN) {
				$this->_ip = null;
			}
			else {
				$this->_ip = wfUtils::inet_ntop($ip);
			}
			
			$this->_blockedTime = $row['blockedTime'];
			$this->_reason = $row['reason'];
			$this->_lastAttempt = $row['lastAttempt'];
			$this->_blockedHits = $row['blockedHits'];
			$this->_expiration = $row['expiration'];
			
			$parameters = $row['parameters'];
			if ($parameters === null) {
				$this->_parameters = null;
			}
			else {
				$this->_parameters = @json_decode($parameters, true);
			}
		}
	}
	
	/**
	 * Tests the block parameters against the given request. If matched, this will return the corresponding wfBlock::MATCH_
	 * constant. If not, it will return wfBlock::MATCH_NONE.
	 * 
	 * @param $ip
	 * @param $userAgent
	 * @param $referrer
	 * @return int
	 */
	public function matchRequest($ip, $userAgent, $referrer) {
		switch ($this->type) {
			case self::TYPE_IP_MANUAL:
			case self::TYPE_IP_AUTOMATIC_TEMPORARY:
			case self::TYPE_IP_AUTOMATIC_PERMANENT:
			case self::TYPE_WFSN_TEMPORARY:
			case self::TYPE_RATE_BLOCK:
			case self::TYPE_RATE_THROTTLE:
				if (wfUtils::inet_pton($ip) == wfUtils::inet_pton($this->ip))
				{
					return self::MATCH_IP;
				}
				break;
			case self::TYPE_PATTERN:
				$match = (!empty($this->ipRange) || !empty($this->hostname) || !empty($this->userAgent) || !empty($this->referrer));
				if (!empty($this->ipRange)) {
					$range = new wfUserIPRange($this->ipRange);
					$match = $match && $range->isIPInRange($ip);
				}
				if (!empty($this->hostname)) {
					$hostname = wfUtils::reverseLookup($ip);
					$match = $match && preg_match(wfUtils::patternToRegex($this->hostname), $hostname);
				}
				if (!empty($this->userAgent)) {
					$match = $match && fnmatch($this->userAgent, $userAgent, FNM_CASEFOLD);
				}
				if (!empty($this->referrer)) {
					$match = $match && fnmatch($this->referrer, $referrer, FNM_CASEFOLD);
				}
				
				if ($match) {
					return self::MATCH_PATTERN;
				}
				
				break;
			case self::TYPE_COUNTRY:
				if (!wfConfig::get('isPaid')) {
					return self::MATCH_NONE;
				}
				
				//Bypass Redirect URL Hit
				$bareRequestURI = wfUtils::extractBareURI($_SERVER['REQUEST_URI']);
				$bareBypassRedirURI = wfUtils::extractBareURI(wfConfig::get('cbl_bypassRedirURL', ''));
				if ($bareBypassRedirURI && $bareRequestURI == $bareBypassRedirURI) {
					$bypassRedirDest = wfConfig::get('cbl_bypassRedirDest', '');
					if ($bypassRedirDest) {
						wfUtils::setcookie('wfCBLBypass', wfBlock::countryBlockingBypassCookieValue(), time() + (86400 * 365), '/', null, wfUtils::isFullSSL(), true);
						return self::MATCH_COUNTRY_REDIR_BYPASS;
					}
				}
				
				//Bypass View URL Hit
				$bareBypassViewURI = wfUtils::extractBareURI(wfConfig::get('cbl_bypassViewURL', ''));
				if ($bareBypassViewURI && $bareBypassViewURI == $bareRequestURI) {
					wfUtils::setcookie('wfCBLBypass', wfBlock::countryBlockingBypassCookieValue(), time() + (86400 * 365), '/', null, wfUtils::isFullSSL(), true);
					return self::MATCH_NONE;
				}
				
				//Early exit checks
				if ($this->_shouldBypassCountryBlocking()) { //Has valid bypass cookie
					return self::MATCH_NONE;
				}
				else if (!$this->blockLogin && $this->_isAuthRequest()) { //Not blocking login and this is a login request
					return self::MATCH_NONE;
				}
				else if (!$this->blockSite && !$this->_isAuthRequest()) { //Not blocking site and this is a site request
					return self::MATCH_NONE;
				}
				else if (is_user_logged_in() && !wfConfig::get('cbl_loggedInBlocked', false)) { //Not blocking logged in users and a user is
					return self::MATCH_NONE;
				}
				
				//Block everything
				if ($this->blockSite && $this->blockLogin) {
					return $this->_checkForBlockedCountry();
				}
				
				//Block the login form itself and any attempt to authenticate
				if ($this->blockLogin) {
					add_filter('authenticate', array($this, '_checkForBlockedCountryFilter'), 1, 1);
					if ($this->_isAuthRequest()) {
						return $this->_checkForBlockedCountry();
					}
				}
				
				//Block requests that aren't to the login page, xmlrpc.php, or a user already logged in
				if ($this->blockSite && !$this->_isAuthRequest() && !defined('XMLRPC_REQUEST')) {
					return $this->_checkForBlockedCountry();
				}
				
				//XMLRPC is inaccesible when public portion of the site and auth is disabled
				if ($this->blockLogin && $this->blockSite && defined('XMLRPC_REQUEST')) {
					return $this->_checkForBlockedCountry();
				}
				
				break;
		}
		
		return self::MATCH_NONE;
	}
	
	/**
	 * Returns whether or not the current request should be treated as an auth request.
	 * 
	 * @return bool
	 */
	private function _isAuthRequest() {
		if ((strpos($_SERVER['REQUEST_URI'], '/wp-login.php') !== false)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Tests whether or not the country blocking bypass cookie is set and valid.
	 * 
	 * @return bool
	 */
	private function _shouldBypassCountryBlocking() {
		if (isset($_COOKIE['wfCBLBypass']) && $_COOKIE['wfCBLBypass'] == wfBlock::countryBlockingBypassCookieValue()) {
			return true;
		}
		return false;
	}
	
	/**
	 * Checks the country block against the requesting IP, returning the action to take.
	 * 
	 * @return int
	 */
	private function _checkForBlockedCountry() {
		$blockedCountries = $this->countries;
		$bareRequestURI = untrailingslashit(wfUtils::extractBareURI($_SERVER['REQUEST_URI']));
		$IP = wfUtils::getIP();
		if ($country = wfUtils::IP2Country($IP)) {
			foreach ($blockedCountries as $blocked) {
				if (strtoupper($blocked) == strtoupper($country)) { //At this point we know the user has been blocked
					if (wfConfig::get('cbl_action') == 'redir') {
						$redirURL = wfConfig::get('cbl_redirURL');
						$eRedirHost = wfUtils::extractHostname($redirURL);
						$isExternalRedir = false;
						if ($eRedirHost && $eRedirHost != wfUtils::extractHostname(home_url())) { //It's an external redirect...
							$isExternalRedir = true;
						}
						
						if ((!$isExternalRedir) && untrailingslashit(wfUtils::extractBareURI($redirURL)) == $bareRequestURI) { //Is this the URI we want to redirect to, then don't block it
							return self::MATCH_NONE;
						}
						else {
							return self::MATCH_COUNTRY_REDIR;
						}
					}
					else {
						return self::MATCH_COUNTRY_BLOCK;
					}
				}
			}
		}
		
		return self::MATCH_NONE;
	}
	
	/**
	 * Filter hook for the country blocking check. Does nothing if not blocked, otherwise presents the block page and exits.
	 * 
	 * Note: Must remain `public` for callback to work.
	 */
	public function _checkForBlockedCountryFilter($user) {
		$block = $this->_checkForBlockedCountry();
		if ($block == self::MATCH_NONE) { 
			return $user;
		}
		
		$log = wfLog::shared();
		$log->getCurrentRequest()->actionDescription = __('blocked access via country blocking', 'wordfence');
		wfConfig::inc('totalCountryBlocked');
		wfActivityReport::logBlockedIP(wfUtils::getIP(), null, 'country');
		$log->do503(3600, __('Access from your area has been temporarily limited for security reasons', 'wordfence')); //exits
	}
	
	/**
	 * Adds $quantity to the blocked count and sets the timestamp for lastAttempt.
	 * 
	 * @param int $quantity
	 * @param bool|int $timestamp
	 */
	public function recordBlock($quantity = 1, $timestamp = false) {
		if ($timestamp === false) {
			$timestamp = time();
		}
		
		global $wpdb;
		$blocksTable = wfBlock::blocksTable();
		$wpdb->query($wpdb->prepare("UPDATE `{$blocksTable}` SET `blockedHits` = `blockedHits` + %d, `lastAttempt` = GREATEST(`lastAttempt`, %d) WHERE `id` = %d", $quantity, $timestamp, $this->id));
		$this->_type = false; //Trigger a re-fetch next access 
	}
	
	/**
	 * Returns an array suitable for JSON of the values needed to edit the block.
	 * 
	 * @return array
	 */
	public function editValues() {
		switch ($this->type) {
			case self::TYPE_COUNTRY:
				return array(
					'blockLogin' => wfUtils::truthyToInt($this->blockLogin),
					'blockSite' => wfUtils::truthyToInt($this->blockSite),
					'countries' => $this->countries,
					'reason' => $this->reason,
					'expiration' => $this->expiration,
				);
		}
		
		return array();
	}
}
