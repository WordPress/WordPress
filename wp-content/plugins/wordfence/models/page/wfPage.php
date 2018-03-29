<?php

class wfPage {
	const PAGE_DASHBOARD = 'dashboard';
	const PAGE_DASHBOARD_OPTIONS = 'dashboard-options';
	const PAGE_FIREWALL = 'firewall';
	const PAGE_FIREWALL_OPTIONS = 'firewall-options';
	const PAGE_BLOCKING = 'blocking';
	const PAGE_BLOCKING_OPTIONS = 'blocking-options';
	const PAGE_SCAN = 'scan';
	const PAGE_SCAN_OPTIONS = 'scan-options';
	const PAGE_TOOLS_2FA = 'tools-2fa';
	const PAGE_TOOLS_LIVE_TRAFFIC = 'tools-2fa';
	const PAGE_TOOLS_COMMENT_SPAM = 'tools-comment-spam';
	const PAGE_TOOLS_WHOIS = 'tools-whois';
	const PAGE_TOOLS_DIAGNOSTICS = 'tools-diagnostics';
	const PAGE_SUPPORT = 'support';
	
	/** @var string */
	private $_identifier;
	
	/**
	 * Provides validation for a user-provided page identifier.
	 * 
	 * @param string $identifier
	 * @return bool
	 */
	public static function isValidPage($identifier) {
		switch ($identifier) {
			case self::PAGE_DASHBOARD:
			case self::PAGE_DASHBOARD_OPTIONS:
			case self::PAGE_FIREWALL:
			case self::PAGE_FIREWALL_OPTIONS:
			case self::PAGE_BLOCKING:
			case self::PAGE_BLOCKING_OPTIONS:
			case self::PAGE_SCAN:
			case self::PAGE_SCAN_OPTIONS:
			case self::PAGE_TOOLS_2FA:
			case self::PAGE_TOOLS_LIVE_TRAFFIC:
			case self::PAGE_TOOLS_COMMENT_SPAM:
			case self::PAGE_TOOLS_WHOIS:
			case self::PAGE_TOOLS_DIAGNOSTICS:
			case self::PAGE_SUPPORT:
				return true;
		}
		return false;
	}
	
	/**
	 * Convenience function for returning the user-displayable label for the given page.
	 * 
	 * @param string $identifier
	 * @return bool|string
	 */
	public static function pageLabel($identifier) {
		$page = new wfPage($identifier);
		return $page->label();
	}
	
	/**
	 * Convenience function for returning the canonical URL for the given page.
	 * 
	 * @param string $identifier
	 * @param string|bool $source The source page identifier to append to the URL if wanted.
	 * @return string
	 */
	public static function pageURL($identifier, $source = false) {
		$page = new wfPage($identifier);
		return $page->url($source);
	}
	
	public function __construct($identifier) {
		$this->_identifier = $identifier;
	}
	
	public function __get($key) {
		switch ($key) {
			case 'identifier':
				return $this->_identifier;
		}
		
		throw new OutOfBoundsException("{$key} is not a valid property");
	}
	
	public function __isset($key) {
		switch ($key) {
			case 'identifier':
				return true;
		}
		return false;
	}
	
	/**
	 * Returns the user-displayable label for the page.
	 * 
	 * @return bool|string
	 */
	public function label() {
		switch ($this->identifier) {
			case self::PAGE_DASHBOARD:
				return __('Dashboard', 'wordfence');
			case self::PAGE_DASHBOARD_OPTIONS:
				return __('Global Options', 'wordfence');
			case self::PAGE_FIREWALL:
				return __('Firewall', 'wordfence');
			case self::PAGE_FIREWALL_OPTIONS:
				return __('Firewall Options', 'wordfence');
			case self::PAGE_BLOCKING:
				return __('Blocking', 'wordfence');
			case self::PAGE_BLOCKING_OPTIONS:
				return __('Blocking Options', 'wordfence');
			case self::PAGE_SCAN:
				return __('Scan', 'wordfence');
			case self::PAGE_SCAN_OPTIONS:
				return __('Scan Options', 'wordfence');
			case self::PAGE_TOOLS_2FA:
				return __('Two Factor Authentication', 'wordfence');
			case self::PAGE_TOOLS_LIVE_TRAFFIC:
				return __('Live Traffic', 'wordfence');
			case self::PAGE_TOOLS_COMMENT_SPAM:
				return __('Comment Spam Filter', 'wordfence');
			case self::PAGE_TOOLS_WHOIS:
				return __('Whois Lookup', 'wordfence');
			case self::PAGE_TOOLS_DIAGNOSTICS:
				return __('Diagnostics', 'wordfence');
			case self::PAGE_SUPPORT:
				return __('Support', 'wordfence');
		}
		
		return false;
	}
	
	/**
	 * Returns the canonical URL for the page.
	 * 
	 * @param string|bool $source The source page identifier to append to the URL if wanted.
	 * @return string
	 */
	public function url($source = false) {
		$page = '';
		$subpage = '';
		$hash = '';
		switch ($this->identifier) {
			case self::PAGE_DASHBOARD:
				$page = 'Wordfence';
				break;
			case self::PAGE_DASHBOARD_OPTIONS:
				$page = 'Wordfence';
				$subpage = 'global_options';
				break;
			case self::PAGE_FIREWALL:
				$page = 'WordfenceWAF';
				break;
			case self::PAGE_FIREWALL_OPTIONS:
				$page = 'WordfenceWAF';
				$subpage = 'waf_options';
				break;
			case self::PAGE_BLOCKING:
				$page = 'WordfenceWAF';
				$hash = '#top#blocking';
				break;
			case self::PAGE_BLOCKING_OPTIONS:
				$page = 'WordfenceWAF';
				$subpage = 'blocking_options';
				break;
			case self::PAGE_SCAN:
				$page = 'WordfenceScan';
				break;
			case self::PAGE_SCAN_OPTIONS:
				$page = 'WordfenceScan';
				$subpage = 'scan_options';
				break;
			case self::PAGE_TOOLS_2FA:
				$page = 'WordfenceTools';
				$subpage = 'twofactor';
				break;
			case self::PAGE_TOOLS_LIVE_TRAFFIC:
				$page = 'WordfenceTools';
				$subpage = 'livetraffic';
				break;
			case self::PAGE_TOOLS_COMMENT_SPAM:
				$page = 'WordfenceTools';
				$subpage = 'commentspam';
				break;
			case self::PAGE_TOOLS_WHOIS:
				$page = 'WordfenceTools';
				$subpage = 'whois';
				break;
			case self::PAGE_TOOLS_DIAGNOSTICS:
				$page = 'WordfenceTools';
				$subpage = 'diagnostics';
				break;
			case self::PAGE_SUPPORT:
				$page = 'WordfenceSupport';
				break;
		}
		
		$baseURL = 'admin.php?';
		$baseURL .= 'page=' . rawurlencode($page);
		if (!empty($subpage)) { $baseURL .= '&subpage=' . rawurlencode($subpage); }
		if (self::isValidPage($source))  { $baseURL .= '&source=' . rawurlencode($source); }
		if (!empty($hash)) { $baseURL .= $this->_hashURLEncode($hash); }
		if (function_exists('network_admin_url') && is_multisite()) {
			return network_admin_url($baseURL);
		}
		
		 return admin_url($baseURL);
	}
	
	/**
	 * Splits a URI hash component and URL-encodes its members.
	 * 
	 * @param string $hash
	 * @return string
	 */
	private function _hashURLEncode($hash) {
		$components = explode('#', $hash);
		foreach ($components as &$c) {
			$c = rawurlencode($c);
		}
		return implode('#', $components);
	}
	
	/**
	 * Returns an ordered array of the pages required to reach this page, this page being the last entry in the array.
	 * 
	 * @return array
	 */
	public function breadcrumbs() {
		switch ($this->identifier) {
			case self::PAGE_DASHBOARD:
				return array($this);
			case self::PAGE_DASHBOARD_OPTIONS:
				return array(new wfPage(wfPage::PAGE_DASHBOARD), $this);
			case self::PAGE_FIREWALL:
				return array($this);
			case self::PAGE_FIREWALL_OPTIONS:
				return array(new wfPage(wfPage::PAGE_FIREWALL), $this);
			case self::PAGE_BLOCKING:
				return array($this);
			case self::PAGE_BLOCKING_OPTIONS:
				return array(new wfPage(wfPage::PAGE_BLOCKING), $this);
			case self::PAGE_SCAN:
				return array($this);
			case self::PAGE_SCAN_OPTIONS:
				return array(new wfPage(wfPage::PAGE_SCAN), $this);
			case self::PAGE_TOOLS_2FA:
				return array($this);
			case self::PAGE_TOOLS_LIVE_TRAFFIC:
				return array($this);
			case self::PAGE_TOOLS_COMMENT_SPAM:
				return array($this);
			case self::PAGE_TOOLS_WHOIS:
				return array($this);
			case self::PAGE_TOOLS_DIAGNOSTICS:
				return array($this);
			case self::PAGE_SUPPORT:
				return array($this);
		}
		
		return array();
	}
}
