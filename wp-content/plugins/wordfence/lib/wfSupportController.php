<?php

class wfSupportController {
	const ITEM_INDEX = 'index';
	const ITEM_FREE = 'free';
	const ITEM_PREMIUM = 'premium';
	
	const ITEM_CHANGELOG = 'changelog';
	
	const ITEM_NOTICE_WAF_INACCESSIBLE_CONFIG = 'notice-waf-inaccessible-config';
	const ITEM_NOTICE_WAF_READ_ONLY_WARNING = 'notice-waf-read-only-warning';
	const ITEM_NOTICE_MISCONFIGURED_HOW_GET_IPS = 'notice-misconfigured-how-get-ips';
	
	const ITEM_LOCKED_OUT = 'locked-out';
	const ITEM_AJAX_BLOCKED = 'ajax-blocked';
	const ITEM_USING_BREACH_PASSWORD = 'using-breach-password';
	
	const ITEM_WIDGET_LOCAL_ATTACKS = 'widget-local-attacks';
	
	const ITEM_DASHBOARD = 'dashboard';
	const ITEM_DASHBOARD_STATUS_FIREWALL = 'dashboard-status-firewall';
	const ITEM_DASHBOARD_STATUS_SCAN = 'dashboard-status-scan';
	const ITEM_DASHBOARD_OPTIONS = 'dashboard-options';
	const ITEM_DASHBOARD_OPTION_API_KEY = 'dashboard-option-api-key';
	const ITEM_DASHBOARD_OPTION_HOW_GET_IPS = 'dashboard-option-how-get-ips';
	const ITEM_DASHBOARD_OPTION_AUTOMATIC_UPDATE = 'dashboard-option-automatic-update';
	const ITEM_DASHBOARD_OPTION_ALERT_EMAILS = 'dashboard-option-alert-emails';
	const ITEM_DASHBOARD_OPTION_HIDE_VERSION = 'dashboard-option-hide-version';
	const ITEM_DASHBOARD_OPTION_DISABLE_UPLOADS_EXECUTION = 'dashboard-option-disable-uploads-execution';
	const ITEM_DASHBOARD_OPTION_DISABLE_COOKIES = 'dashboard-option-disable-cookies';
	const ITEM_DASHBOARD_OPTION_PAUSE_LIVE_UPDATES = 'dashboard-option-pause-live-updates';
	const ITEM_DASHBOARD_OPTION_UPDATE_INTERVAL = 'dashboard-option-refresh-period';
	const ITEM_DASHBOARD_OPTION_LITESPEED_WARNING = 'dashboard-litespeed-warning';
	const ITEM_DASHBOARD_OPTION_BYPASS_LITESPEED_CHECK = 'dashboard-option-bypass-litespeed-check';
	const ITEM_DASHBOARD_OPTION_DELETE_DEACTIVATION = 'dashboard-option-delete-deactivation';
	const ITEM_DASHBOARD_OPTION_EXPORT = 'dashboard-option-export';
	const ITEM_DASHBOARD_OPTION_IMPORT = 'dashboard-option-import';
	
	const ITEM_FIREWALL_WAF = 'firewall-waf';
	const ITEM_FIREWALL_WAF_STATUS_OVERALL = 'firewall-waf-status-overall';
	const ITEM_FIREWALL_WAF_STATUS_RULES = 'firewall-waf-status-rules';
	const ITEM_FIREWALL_WAF_STATUS_BLACKLIST = 'firewall-waf-status-blacklist';
	const ITEM_FIREWALL_WAF_STATUS_BRUTE_FORCE = 'firewall-waf-status-brute-force';
	const ITEM_FIREWALL_WAF_INSTALL_MANUALLY = 'firewall-waf-install-manually';
	const ITEM_FIREWALL_WAF_INSTALL_NGINX = 'firewall-waf-install-nginx';
	const ITEM_FIREWALL_WAF_REMOVE_MANUALLY = 'firewall-waf-remove-manually';
	const ITEM_FIREWALL_WAF_LEARNING_MODE = 'firewall-waf-learning-mode';
	const ITEM_FIREWALL_WAF_RULES = 'firewall-waf-rules';
	const ITEM_FIREWALL_WAF_WHITELIST = 'firewall-waf-whitelist';
	const ITEM_FIREWALL_WAF_OPTION_DELAY_BLOCKING = 'firewall-waf-option-delay-blocking';
	const ITEM_FIREWALL_WAF_OPTION_WHITELISTED_IPS = 'firewall-waf-option-whitelisted-ips';
	const ITEM_FIREWALL_WAF_IGNORED_ALERT_IPS = 'firewall-waf-option-ignored-alert-ips';
	const ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_URLS = 'firewall-waf-option-immediately-block-urls';
	const ITEM_FIREWALL_WAF_OPTION_ENABLE_LOGIN_SECURITY = 'firewall-waf-option-enable-login-security';
	const ITEM_FIREWALL_WAF_OPTION_LOCK_OUT_FAILURE_COUNT = 'firewall-waf-option-lock-out-failure-count';
	const ITEM_FIREWALL_WAF_OPTION_LOCK_OUT_FORGOT_PASSWORD_COUNT = 'firewall-waf-option-lock-out-forgot-password-count';
	const ITEM_FIREWALL_WAF_OPTION_COUNT_TIME_PERIOD = 'firewall-waf-option-count-time-period';
	const ITEM_FIREWALL_WAF_OPTION_LOCKOUT_DURATION = 'firewall-waf-option-lockout-duration';
	const ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_LOCK_OUT_INVALID_USERS = 'firewall-waf-option-immediately-lock-out-invalid-users';
	const ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_USERS = 'firewall-waf-option-immediately-block-users';
	const ITEM_FIREWALL_WAF_OPTION_ENFORCE_STRONG_PASSWORDS = 'firewall-waf-option-enforce-strong-passwords';
	const ITEM_FIREWALL_WAF_OPTION_PREVENT_BREACH_PASSWORDS = 'firewall-waf-option-prevent-breach-passwords';
	const ITEM_FIREWALL_WAF_OPTION_MASK_LOGIN_ERRORS = 'firewall-waf-option-mask-login-errors';
	const ITEM_FIREWALL_WAF_OPTION_PREVENT_ADMIN_REGISTRATION = 'firewall-waf-option-prevent-admin-registration';
	const ITEM_FIREWALL_WAF_OPTION_PREVENT_AUTHOR_SCAN = 'firewall-waf-option-prevent-author-scan';
	const ITEM_FIREWALL_WAF_OPTION_BLOCK_BAD_POST = 'firewall-waf-option-block-bad-post';
	const ITEM_FIREWALL_WAF_OPTION_CHECK_PASSWORD = 'firewall-waf-option-check-password';
	const ITEM_FIREWALL_WAF_OPTION_PARTICIPATE_WFSN = 'firewall-waf-option-participate-wfsn';
	const ITEM_FIREWALL_WAF_OPTION_ENABLE_ADVANCED_BLOCKING = 'firewall-waf-option-enable-advanced-blocking';
	const ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_FAKE_GOOGLE = 'firewall-waf-option-immediately-block-fake-google';
	const ITEM_FIREWALL_WAF_OPTION_GOOGLE_ACTION = 'firewall-waf-option-google-action';
	const ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_ANY = 'firewall-waf-option-rate-limit-any';
	const ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_CRAWLER = 'firewall-waf-option-rate-limit-crawler';
	const ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_CRAWLER_404 = 'firewall-waf-option-rate-limit-crawler-404';
	const ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_HUMAN = 'firewall-waf-option-rate-limit-human';
	const ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_HUMAN_404 = 'firewall-waf-option-rate-limit-human-404';
	const ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_ANY_404 = 'firewall-waf-option-rate-limit-any-404';
	const ITEM_FIREWALL_WAF_OPTION_AUTOMATIC_BLOCK_DURATION = 'firewall-waf-option-automatic-block-duration';
	const ITEM_FIREWALL_WAF_OPTION_WHITELISTED_404 = 'firewall-waf-option-whitelisted-404';
	const ITEM_FIREWALL_WAF_OPTION_MONITOR_AJAX = 'firewall-waf-option-monitor-ajax';
	
	const ITEM_FIREWALL_BLOCKING = 'firewall-blocking';
	const ITEM_FIREWALL_BLOCKING_OPTION_WHAT_TO_DO = 'firewall-blocking-option-what-to-do';
	const ITEM_FIREWALL_BLOCKING_OPTION_REDIRECT = 'firewall-blocking-option-redirect';
	const ITEM_FIREWALL_BLOCKING_OPTION_BLOCK_LOGGED_IN = 'firewall-blocking-option-block-logged-in';
	const ITEM_FIREWALL_BLOCKING_BYPASS_COOKIE = 'firewall-blocking-bypass-cookie';
	const ITEM_FIREWALL_BLOCKING_BYPASS_REDIRECT = 'firewall-blocking-bypass-redirect';
	const ITEM_FIREWALL_BLOCKING_FULL_SITE = 'firewall-blocking-full-site';
	
	const ITEM_SCAN = 'scan';
	const ITEM_SCAN_STATUS_OVERALL = 'scan-status-overall';
	const ITEM_SCAN_STATUS_MALWARE = 'scan-status-malware';
	const ITEM_SCAN_STATUS_REPUTATION = 'scan-status-reputation';
	const ITEM_SCAN_OPTION_CHECK_SITE_BLACKLISTED = 'scan-option-check-site-blacklisted';
	const ITEM_SCAN_OPTION_CHECK_SITE_SPAMVERTIZED = 'scan-option-check-site-spamvertized';
	const ITEM_SCAN_OPTION_CHECK_IP_SPAMMING = 'scan-option-ip-spamming';
	const ITEM_SCAN_OPTION_CHECK_MISCONFIGURED_HOW_GET_IPS = 'scan-option-misconfigured-how-get-ips';
	const ITEM_SCAN_OPTION_PUBLIC_CONFIG = 'scan-option-public-config';
	const ITEM_SCAN_OPTION_PUBLIC_QUARANTINED = 'scan-option-public-quarantined';
	const ITEM_SCAN_OPTION_CORE_CHANGES = 'scan-option-core-changes';
	const ITEM_SCAN_OPTION_THEME_CHANGES = 'scan-option-theme-changes';
	const ITEM_SCAN_OPTION_PLUGIN_CHANGES = 'scan-option-plugin-changes';
	const ITEM_SCAN_OPTION_UNKNOWN_CORE = 'scan-option-unknown-core';
	const ITEM_SCAN_OPTION_MALWARE_HASHES = 'scan-option-malware-hashes';
	const ITEM_SCAN_OPTION_MALWARE_SIGNATURES = 'scan-option-malware-signatures';
	const ITEM_SCAN_OPTION_MALWARE_URLS = 'scan-option-malware-urls';
	const ITEM_SCAN_OPTION_POST_URLS = 'scan-option-post-urls';
	const ITEM_SCAN_OPTION_COMMENT_URLS = 'scan-option-comment-urls';
	const ITEM_SCAN_OPTION_MALWARE_OPTIONS = 'scan-option-malware-options';
	const ITEM_SCAN_OPTION_UPDATES = 'scan-option-updates';
	const ITEM_SCAN_OPTION_UNKNOWN_ADMINS = 'scan-option-unknown-admins';
	const ITEM_SCAN_OPTION_PASSWORD_STRENGTH = 'scan-option-password-strength';
	const ITEM_SCAN_OPTION_DISK_SPACE = 'scan-option-disk-space';
	const ITEM_SCAN_OPTION_DNS_CHANGES = 'scan-option-dns-changes';
	const ITEM_SCAN_OPTION_OUTSIDE_WORDPRESS = 'scan-option-outside-wordpress';
	const ITEM_SCAN_OPTION_IMAGES_EXECUTABLE = 'scan-option-images-executable';
	const ITEM_SCAN_OPTION_HIGH_SENSITIVITY = 'scan-option-high-sensitivity';
	const ITEM_SCAN_OPTION_LOW_RESOURCE = 'scan-option-low-resource';
	const ITEM_SCAN_OPTION_LIMIT_ISSUES = 'scan-option-limit-issues';
	const ITEM_SCAN_OPTION_OVERALL_TIME_LIMIT = 'scan-option-overall-time-limit';
	const ITEM_SCAN_OPTION_MEMORY_LIMIT = 'scan-option-memory-limit';
	const ITEM_SCAN_OPTION_STAGE_TIME_LIMIT = 'scan-option-stage-time-limit';
	const ITEM_SCAN_OPTION_EXCLUDE_PATTERNS = 'scan-option-exclude-patterns';
	const ITEM_SCAN_OPTION_CUSTOM_MALWARE_SIGNATURES = 'scan-option-custom-malware-signatures';
	const ITEM_SCAN_TIME_LIMIT = 'scan-time-limit';
	const ITEM_SCAN_FAILS = 'scan-fails';
	const ITEM_SCAN_BULK_DELETE_WARNING = 'scan-bulk-delete-warning';
	const ITEM_SCAN_SCHEDULING = 'scan-scheduling';
	const ITEM_SCAN_RESULT_PUBLIC_CONFIG = 'scan-result-public-config';
	const ITEM_SCAN_RESULT_PLUGIN_ABANDONED = 'scan-result-plugin-abandoned';
	const ITEM_SCAN_RESULT_PLUGIN_REMOVED = 'scan-result-plugin-removed';
	const ITEM_SCAN_RESULT_OPTION_MALWARE_URL = 'scan-result-option-malware-url';
	const ITEM_SCAN_RESULT_GEOIP_UPDATE = 'scan-result-geoip-update';
	
	const ITEM_TOOLS_TWO_FACTOR = 'tools-two-factor';
	const ITEM_TOOLS_LIVE_TRAFFIC = 'tools-live-traffic';
	const ITEM_TOOLS_LIVE_TRAFFIC_OPTION_ENABLE = 'tools-live-traffic-option-enable';
	const ITEM_TOOLS_COMMENT_SPAM = 'tools-comment-spam';
	const ITEM_TOOLS_COMMENT_SPAM_OPTION_HOLD_ANONYMOUS = 'tools-comment-spam-option-hold-anonymous';
	const ITEM_TOOLS_COMMENT_SPAM_OPTION_SCAN = 'tools-comment-spam-option-scan';
	const ITEM_TOOLS_COMMENT_SPAM_OPTION_ADVANCED = 'tools-comment-spam-option-advanced';
	const ITEM_TOOLS_WHOIS_LOOKUP = 'tools-whois-lookup';
	
	const ITEM_DIAGNOSTICS_SYSTEM_CONFIGURATION = 'diagnostics-system-configuration';
	const ITEM_DIAGNOSTICS_TEST_MEMORY = 'diagnostics-test-memory';
	const ITEM_DIAGNOSTICS_TEST_EMAIL = 'diagnostics-test-email';
	const ITEM_DIAGNOSTICS_TEST_ACTIVITY_REPORT = 'diagnostics-test-activity-report';
	const ITEM_DIAGNOSTICS_OPTION_DEBUGGING_MODE = 'diagnostics-option-debugging-mode';
	const ITEM_DIAGNOSTICS_OPTION_REMOTE_SCANS = 'diagnostics-option-remote-scans';
	const ITEM_DIAGNOSTICS_OPTION_SSL_VERIFICATION = 'diagnostics-option-ssl-verification';
	const ITEM_DIAGNOSTICS_OPTION_BETA_TDF = 'diagnostics-option-beta-tdf';
	
	public static function esc_supportURL($item = self::ITEM_INDEX) {
		return esc_url(self::supportURL($item));
	}
	
	public static function supportURL($item = self::ITEM_INDEX) {
		$base = 'https://www.wordfence.com/help/';
		switch ($item) {
			case self::ITEM_INDEX:
				return 'https://www.wordfence.com/help/';
			case self::ITEM_FREE:
				return 'https://wordpress.org/support/plugin/wordfence/';
			case self::ITEM_PREMIUM:
				return 'https://support.wordfence.com/';
			
			//These all fall through to the query format
				
			case self::ITEM_NOTICE_WAF_INACCESSIBLE_CONFIG:
			case self::ITEM_NOTICE_WAF_READ_ONLY_WARNING:
			case self::ITEM_NOTICE_MISCONFIGURED_HOW_GET_IPS:
				
			case self::ITEM_LOCKED_OUT:
			case self::ITEM_AJAX_BLOCKED:
			case self::ITEM_USING_BREACH_PASSWORD:
				
			case self::ITEM_WIDGET_LOCAL_ATTACKS:
				
			case self::ITEM_DASHBOARD:
			case self::ITEM_DASHBOARD_STATUS_FIREWALL:
			case self::ITEM_DASHBOARD_STATUS_SCAN:
			case self::ITEM_DASHBOARD_OPTIONS:
			case self::ITEM_DASHBOARD_OPTION_API_KEY:
			case self::ITEM_DASHBOARD_OPTION_HOW_GET_IPS:
			case self::ITEM_DASHBOARD_OPTION_AUTOMATIC_UPDATE:
			case self::ITEM_DASHBOARD_OPTION_ALERT_EMAILS:
			case self::ITEM_DASHBOARD_OPTION_HIDE_VERSION:
			case self::ITEM_DASHBOARD_OPTION_DISABLE_UPLOADS_EXECUTION:
			case self::ITEM_DASHBOARD_OPTION_DISABLE_COOKIES:
			case self::ITEM_DASHBOARD_OPTION_PAUSE_LIVE_UPDATES:
			case self::ITEM_DASHBOARD_OPTION_UPDATE_INTERVAL:
			case self::ITEM_DASHBOARD_OPTION_LITESPEED_WARNING:
			case self::ITEM_DASHBOARD_OPTION_BYPASS_LITESPEED_CHECK:
			case self::ITEM_DASHBOARD_OPTION_DELETE_DEACTIVATION:
			case self::ITEM_DASHBOARD_OPTION_EXPORT:
			case self::ITEM_DASHBOARD_OPTION_IMPORT:

			case self::ITEM_FIREWALL_WAF:
			case self::ITEM_FIREWALL_WAF_STATUS_OVERALL:
			case self::ITEM_FIREWALL_WAF_STATUS_RULES:
			case self::ITEM_FIREWALL_WAF_STATUS_BLACKLIST:
			case self::ITEM_FIREWALL_WAF_STATUS_BRUTE_FORCE:
			case self::ITEM_FIREWALL_WAF_INSTALL_MANUALLY:
			case self::ITEM_FIREWALL_WAF_INSTALL_NGINX:
			case self::ITEM_FIREWALL_WAF_REMOVE_MANUALLY:
			case self::ITEM_FIREWALL_WAF_LEARNING_MODE:
			case self::ITEM_FIREWALL_WAF_RULES:
			case self::ITEM_FIREWALL_WAF_WHITELIST:
			case self::ITEM_FIREWALL_WAF_OPTION_DELAY_BLOCKING:
			case self::ITEM_FIREWALL_WAF_OPTION_WHITELISTED_IPS:
			case self::ITEM_FIREWALL_WAF_IGNORED_ALERT_IPS:
			case self::ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_URLS:
			case self::ITEM_FIREWALL_WAF_OPTION_ENABLE_LOGIN_SECURITY:
			case self::ITEM_FIREWALL_WAF_OPTION_LOCK_OUT_FAILURE_COUNT:
			case self::ITEM_FIREWALL_WAF_OPTION_LOCK_OUT_FORGOT_PASSWORD_COUNT:
			case self::ITEM_FIREWALL_WAF_OPTION_COUNT_TIME_PERIOD:
			case self::ITEM_FIREWALL_WAF_OPTION_LOCKOUT_DURATION:
			case self::ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_LOCK_OUT_INVALID_USERS:
			case self::ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_USERS:
			case self::ITEM_FIREWALL_WAF_OPTION_ENFORCE_STRONG_PASSWORDS:
			case self::ITEM_FIREWALL_WAF_OPTION_PREVENT_BREACH_PASSWORDS:
			case self::ITEM_FIREWALL_WAF_OPTION_MASK_LOGIN_ERRORS:
			case self::ITEM_FIREWALL_WAF_OPTION_PREVENT_ADMIN_REGISTRATION:
			case self::ITEM_FIREWALL_WAF_OPTION_PREVENT_AUTHOR_SCAN:
			case self::ITEM_FIREWALL_WAF_OPTION_BLOCK_BAD_POST:
			case self::ITEM_FIREWALL_WAF_OPTION_CHECK_PASSWORD:
			case self::ITEM_FIREWALL_WAF_OPTION_PARTICIPATE_WFSN:
			case self::ITEM_FIREWALL_WAF_OPTION_ENABLE_ADVANCED_BLOCKING:
			case self::ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_FAKE_GOOGLE:
			case self::ITEM_FIREWALL_WAF_OPTION_GOOGLE_ACTION:
			case self::ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_ANY:
			case self::ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_CRAWLER:
			case self::ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_CRAWLER_404:
			case self::ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_HUMAN:
			case self::ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_HUMAN_404:
			case self::ITEM_FIREWALL_WAF_OPTION_RATE_LIMIT_ANY_404:
			case self::ITEM_FIREWALL_WAF_OPTION_AUTOMATIC_BLOCK_DURATION:
			case self::ITEM_FIREWALL_WAF_OPTION_WHITELISTED_404:
			case self::ITEM_FIREWALL_WAF_OPTION_MONITOR_AJAX:
				
			case self::ITEM_FIREWALL_BLOCKING:
			case self::ITEM_FIREWALL_BLOCKING_OPTION_WHAT_TO_DO:
			case self::ITEM_FIREWALL_BLOCKING_OPTION_REDIRECT:
			case self::ITEM_FIREWALL_BLOCKING_OPTION_BLOCK_LOGGED_IN:
			case self::ITEM_FIREWALL_BLOCKING_BYPASS_COOKIE:
			case self::ITEM_FIREWALL_BLOCKING_BYPASS_REDIRECT:
			case self::ITEM_FIREWALL_BLOCKING_FULL_SITE:
				
			case self::ITEM_SCAN:
			case self::ITEM_SCAN_STATUS_OVERALL:
			case self::ITEM_SCAN_STATUS_MALWARE:
			case self::ITEM_SCAN_STATUS_REPUTATION:
			case self::ITEM_SCAN_TIME_LIMIT:
			case self::ITEM_SCAN_FAILS:
			case self::ITEM_SCAN_BULK_DELETE_WARNING:
			case self::ITEM_SCAN_SCHEDULING:
			case self::ITEM_SCAN_OPTION_CHECK_SITE_BLACKLISTED:
			case self::ITEM_SCAN_OPTION_CHECK_SITE_SPAMVERTIZED:
			case self::ITEM_SCAN_OPTION_CHECK_IP_SPAMMING:
			case self::ITEM_SCAN_OPTION_CHECK_MISCONFIGURED_HOW_GET_IPS:
			case self::ITEM_SCAN_OPTION_PUBLIC_CONFIG:
			case self::ITEM_SCAN_OPTION_PUBLIC_QUARANTINED:
			case self::ITEM_SCAN_OPTION_CORE_CHANGES:
			case self::ITEM_SCAN_OPTION_THEME_CHANGES:
			case self::ITEM_SCAN_OPTION_PLUGIN_CHANGES:
			case self::ITEM_SCAN_OPTION_UNKNOWN_CORE:
			case self::ITEM_SCAN_OPTION_MALWARE_HASHES:
			case self::ITEM_SCAN_OPTION_MALWARE_SIGNATURES:
			case self::ITEM_SCAN_OPTION_MALWARE_URLS:
			case self::ITEM_SCAN_OPTION_POST_URLS:
			case self::ITEM_SCAN_OPTION_COMMENT_URLS:
			case self::ITEM_SCAN_OPTION_MALWARE_OPTIONS:
			case self::ITEM_SCAN_OPTION_UPDATES:
			case self::ITEM_SCAN_OPTION_UNKNOWN_ADMINS:
			case self::ITEM_SCAN_OPTION_PASSWORD_STRENGTH:
			case self::ITEM_SCAN_OPTION_DISK_SPACE:
			case self::ITEM_SCAN_OPTION_DNS_CHANGES:
			case self::ITEM_SCAN_OPTION_OUTSIDE_WORDPRESS:
			case self::ITEM_SCAN_OPTION_IMAGES_EXECUTABLE:
			case self::ITEM_SCAN_OPTION_HIGH_SENSITIVITY:
			case self::ITEM_SCAN_OPTION_LOW_RESOURCE:
			case self::ITEM_SCAN_OPTION_LIMIT_ISSUES:
			case self::ITEM_SCAN_OPTION_OVERALL_TIME_LIMIT:
			case self::ITEM_SCAN_OPTION_MEMORY_LIMIT:
			case self::ITEM_SCAN_OPTION_STAGE_TIME_LIMIT:
			case self::ITEM_SCAN_OPTION_EXCLUDE_PATTERNS:
			case self::ITEM_SCAN_OPTION_CUSTOM_MALWARE_SIGNATURES:
			case self::ITEM_SCAN_RESULT_PUBLIC_CONFIG:
			case self::ITEM_SCAN_RESULT_PLUGIN_ABANDONED:
			case self::ITEM_SCAN_RESULT_PLUGIN_REMOVED:
			case self::ITEM_SCAN_RESULT_OPTION_MALWARE_URL:
			case self::ITEM_SCAN_RESULT_GEOIP_UPDATE:
				
			case self::ITEM_TOOLS_TWO_FACTOR:
			case self::ITEM_TOOLS_LIVE_TRAFFIC:
			case self::ITEM_TOOLS_LIVE_TRAFFIC_OPTION_ENABLE:
			case self::ITEM_TOOLS_COMMENT_SPAM:
			case self::ITEM_TOOLS_COMMENT_SPAM_OPTION_HOLD_ANONYMOUS:
			case self::ITEM_TOOLS_COMMENT_SPAM_OPTION_SCAN:
			case self::ITEM_TOOLS_COMMENT_SPAM_OPTION_ADVANCED:
			case self::ITEM_TOOLS_WHOIS_LOOKUP:
				
			case self::ITEM_DIAGNOSTICS_SYSTEM_CONFIGURATION:
			case self::ITEM_DIAGNOSTICS_TEST_MEMORY:
			case self::ITEM_DIAGNOSTICS_TEST_EMAIL:
			case self::ITEM_DIAGNOSTICS_TEST_ACTIVITY_REPORT:
			case self::ITEM_DIAGNOSTICS_OPTION_DEBUGGING_MODE:
			case self::ITEM_DIAGNOSTICS_OPTION_REMOTE_SCANS:
			case self::ITEM_DIAGNOSTICS_OPTION_SSL_VERIFICATION:
			case self::ITEM_DIAGNOSTICS_OPTION_BETA_TDF:
				return $base . '?query=' . $item;
		}
		
		return '';
	}
}