<?php

class wfAdminNoticeQueue {
	protected static function _notices() {
		return wfConfig::get_ser('adminNoticeQueue', array());
	}
	
	protected static function _setNotices($notices) {
		wfConfig::set_ser('adminNoticeQueue', $notices);
	}
	
	/**
	 * Adds an admin notice to the display queue.
	 * 
	 * @param string $severity
	 * @param string $messageHTML
	 * @param bool|string $category If not false, notices with the same category will be removed prior to adding this one.
	 * @param bool|array $users If not false, an array of user IDs the notice should show for.
	 */
	public static function addAdminNotice($severity, $messageHTML, $category = false, $users = false) {
		$notices = self::_notices();
		foreach ($notices as $id => $n) {
			$usersMatches = false;
			if (isset($n['users'])) {
				$usersMatches = wfUtils::sets_equal($n['users'], $users);
			}
			else if ($users === false) {
				$usersMatches = true;
			}
			
			$categoryMatches = false;
			if ($category !== false && isset($n['category']) && $n['category'] == $category) {
				$categoryMatches = true;
			}
			
			if ($usersMatches && $categoryMatches) {
				unset($notices[$id]);
			}
		}
		
		$id = wfUtils::uuid();
		$notices[$id] = array(
			'severity' => $severity,
			'messageHTML' => $messageHTML,
		);
		
		if ($category !== false) {
			$notices[$id]['category'] = $category;
		}
		
		if ($users !== false) {
			$notices[$id]['users'] = $users;
		}
		
		self::_setNotices($notices);
	}
	
	public static function removeAdminNotice($id = false, $category = false, $users = false) {
		$notices = self::_notices();
		foreach ($notices as $nid => $n) {
			$idMatches = false;
			if ($id === false || $id == $nid) { //Match any ID if not looking for a specific one
				$idMatches = true;
			}
			
			$categoryMatches = false;
			if (($category === false && !isset($n['category'])) || ($category !== false && isset($n['category']) && $category == $n['category'])) {
				$categoryMatches = true;
			}
			
			$usersMatches = false;
			if (($users === false && !isset($n['users'])) || ($users !== false && isset($n['users']) && wfUtils::sets_equal($users, $n['users']))) {
				$usersMatches = true;
			}
			
			if ($idMatches || ($categoryMatches && $usersMatches)) {
				unset($notices[$nid]);
			}
		}
		self::_setNotices($notices);
	}
	
	public static function enqueueAdminNotices() {
		$user = wp_get_current_user();
		if ($user->ID == 0) {
			return false;
		}
		
		$networkAdmin = is_multisite() && is_network_admin();
		$notices = self::_notices();
		$added = false;
		foreach ($notices as $nid => $n) {
			if (isset($n['users']) && array_search($user->ID, $n['users']) === false) {
				continue;
			}
			
			$notice = new wfAdminNotice($nid, $n['severity'], $n['messageHTML']);
			if ($networkAdmin) {
				add_action('network_admin_notices', array($notice, 'displayNotice'));
			}
			else {
				add_action('admin_notices', array($notice, 'displayNotice'));
			}
			
			$added = true;
		}
		
		return $added;
	}
}

class wfAdminNotice {
	const SEVERITY_CRITICAL = 'critical';
	const SEVERITY_WARNING = 'warning';
	const SEVERITY_INFO = 'info';
	
	private $_id;
	private $_severity;
	private $_messageHTML;
	
	public function __construct($id, $severity, $messageHTML) {
		$this->_id = $id;
		$this->_severity = $severity;
		$this->_messageHTML = $messageHTML;
	}
	
	public function displayNotice() {
		$severityClass = 'notice-info';
		if ($this->_severity == self::SEVERITY_CRITICAL) {
			$severityClass = 'notice-error';
		}
		else if ($this->_severity == self::SEVERITY_WARNING) {
			$severityClass = 'notice-warning';
		}
		
		echo '<div class="wf-admin-notice notice ' . $severityClass . '" data-notice-id="' . esc_attr($this->_id) . '"><p>' . $this->_messageHTML . '</p><p>' . sprintf(__('<a class="wf-btn wf-btn-default wf-btn-sm wf-dismiss-link" href="#" onclick="wordfenceExt.dismissAdminNotice(\'%s\'); return false;">Dismiss</a>', 'wordfence'), esc_attr($this->_id)) . '</p></div>';
	}
}