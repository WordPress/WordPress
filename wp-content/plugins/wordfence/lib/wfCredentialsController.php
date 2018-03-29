<?php

class wfCredentialsController {
	const UNCACHED = 'uncached';
	const NOT_LEAKED = 'not-leaked';
	const LEAKED = 'leaked';
	
	/**
	 * Queries the API and returns whether or not the password exists in the breach database.
	 * 
	 * @param string $login
	 * @param string $password
	 * @return bool
	 */
	public static function isLeakedPassword($login, $password) {
		$sha1 = strtoupper(hash('sha1', $password));
		$prefix = substr($sha1, 0, 5);
		
		$ssl_verify = (bool) wfConfig::get('ssl_verify');
		$args = array(
			'timeout'    => 5,
			'user-agent' => "Wordfence.com UA " . (defined('WORDFENCE_VERSION') ? WORDFENCE_VERSION : '[Unknown version]'),
			'sslverify'  => $ssl_verify,
			'headers'	 => array('Referer' => false),
		);
		
		if (!$ssl_verify) { // Some versions of cURL will complain that SSL verification is disabled but the CA bundle was supplied.
			$args['sslcertificates'] = false;
		}
		
		$response = wp_remote_get(sprintf(WORDFENCE_BREACH_URL_BASE_SEC . "%s.txt", $prefix), $args);
		
		if (!is_wp_error($response)) {
			$data = wp_remote_retrieve_body($response);
			$lines = explode("\n", $data);
			foreach ($lines as $l) {
				$components = explode(":", $l);
				$teshSHA1 = $prefix . strtoupper($components[0]);
				if (hash_equals($sha1, $teshSHA1)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the transient key for the given user.
	 * 
	 * @param WP_User $user
	 * @return string
	 */
	protected static function _cachedCredentialStatusKey($user) {
		$key = 'wfcredentialstatus_' . $user->ID;
		return $key;
	}
	
	/**
	 * Returns the cached credential status for the given user: self::UNCACHED, self::NOT_LEAKED, or self::LEAKED.
	 * 
	 * @param WP_User $user
	 * @return string
	 */
	public static function cachedCredentialStatus($user) {
		$key = self::_cachedCredentialStatusKey($user);
		$value = get_transient($key);
		if ($value === false) {
			return self::UNCACHED;
		}
		else if ($value) {
			return self::LEAKED;
		}
		return self::NOT_LEAKED;
	}
	
	/**
	 * Stores a cached leak value for the given user.
	 * 
	 * @param WP_User $user
	 * @param bool $isLeaked
	 */
	public static function setCachedCredentialStatus($user, $isLeaked) {
		$key = self::_cachedCredentialStatusKey($user);
		set_transient($key, $isLeaked ? 1 : 0, 3600);
	}
	
	/**
	 * Clears the cache for the given user.
	 * 
	 * @param WP_User $user
	 */
	public static function clearCachedCredentialStatus($user) {
		$key = self::_cachedCredentialStatusKey($user);
		delete_transient($key);
	}
	
	public static function hasPreviousLoginFromIP($user, $ip) {
		global $wpdb;
		$table_wfLogins = wfDB::networkTable('wfLogins');
		
		$id = property_exists($user, 'ID') ? $user->ID : 0;
		if ($id == 0) {
			return false;
		}
		
		$result = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$table_wfLogins} WHERE action = 'loginOK' AND userID = %d AND IP = %s", $id, wfUtils::inet_pton($ip)), ARRAY_A);
		if (is_array($result)) {
			return true;
		}
		
		$lastAdminLogin = wfConfig::get_ser('lastAdminLogin');
		if (is_array($lastAdminLogin) && isset($lastAdminLogin['userID']) && $lastAdminLogin['userID'] == $id && isset($lastAdminLogin['IP']) && wfUtils::inet_pton($lastAdminLogin['IP']) == wfUtils::inet_pton($ip)) {
			return true;
		}
		
		return false;
	}
}