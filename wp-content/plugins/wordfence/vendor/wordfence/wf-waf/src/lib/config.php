<?php

/**
 * Class wfWAFConfig provides a convenience interface for accessing the WAF's configuration
 * that does not throw exceptions. All exceptions are caught and, if WFWAF_DEBUG is true, logged 
 * to the server error log.
 */
class wfWAFConfig {
	public static function set($key, $val, $waf = null) {
		if (!($waf instanceof wfWAF)) {
			$waf = wfWAF::getInstance();
		}
		
		try {
			$waf->getStorageEngine()->setConfig($key, $val);
		}
		catch (Exception $e) {
			if (WFWAF_DEBUG) {
				error_log("Exception in " . __CLASS__ . "->" . __FUNCTION__ . ": " . $e->getMessage());
			}
		}
	}
	
	public static function get($key, $default = null, $waf = null) {
		if (!($waf instanceof wfWAF)) {
			$waf = wfWAF::getInstance();
		}
		
		try {
			return $waf->getStorageEngine()->getConfig($key, $default);
		}
		catch (Exception $e) {
			if (WFWAF_DEBUG) {
				error_log("Exception in " . __CLASS__ . "->" . __FUNCTION__ . ": " . $e->getMessage());
			}
		}
		return $default;
	}
	
	public static function unsetKey($key, $waf = null) {
		if (!($waf instanceof wfWAF)) {
			$waf = wfWAF::getInstance();
		}
		
		try {
			$waf->getStorageEngine()->unsetConfig($key);
		}
		catch (Exception $e) {
			if (WFWAF_DEBUG) {
				error_log("Exception in " . __CLASS__ . "->" . __FUNCTION__ . ": " . $e->getMessage());
			}
		}
	}
	
	public static function isInLearningMode($waf = null) {
		if (!($waf instanceof wfWAF)) {
			$waf = wfWAF::getInstance();
		}
		
		try {
			return $waf->getStorageEngine()->isInLearningMode();
		}
		catch (Exception $e) {
			if (WFWAF_DEBUG) {
				error_log("Exception in " . __CLASS__ . "->" . __FUNCTION__ . ": " . $e->getMessage());
			}
		}
		return false;
	}
	
	public static function isDisabled($waf = null) {
		if (!($waf instanceof wfWAF)) {
			$waf = wfWAF::getInstance();
		}
		
		try {
			return $waf->getStorageEngine()->isDisabled();
		}
		catch (Exception $e) {
			if (WFWAF_DEBUG) {
				error_log("Exception in " . __CLASS__ . "->" . __FUNCTION__ . ": " . $e->getMessage());
			}
		}
		return true;
	}
}
