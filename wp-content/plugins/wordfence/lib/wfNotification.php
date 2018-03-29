<?php
class wfNotification {
	const PRIORITY_LOW = 1500;
	const PRIORITY_DEFAULT = 1000;
	const PRIORITY_HIGH = 500;
	const PRIORITY_HIGH_CRITICAL = 501;
	const PRIORITY_HIGH_WARNING = 502;
	
	protected $_id;
	protected $_category;
	protected $_priority;
	protected $_ctime;
	protected $_html;
	protected $_links;
	
	public static function notifications($since = 0) {
		global $wpdb;
		$table_wfNotifications = wfDB::networkTable('wfNotifications');
		$rawNotifications = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_wfNotifications} WHERE `new` = 1 AND `ctime` > %d ORDER BY `priority` ASC, `ctime` DESC", $since), ARRAY_A);
		$notifications = array();
		foreach ($rawNotifications as $raw) {
			$notifications[] = new wfNotification($raw['id'], $raw['priority'], $raw['html'], $raw['category'], $raw['ctime'], json_decode($raw['links'], true), true);
		}
		return $notifications;
	}
	
	public static function getNotificationForID($id) {
		global $wpdb;
		$table_wfNotifications = wfDB::networkTable('wfNotifications');
		$rawNotifications = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_wfNotifications} WHERE `id` = %s ORDER BY `priority` ASC, `ctime` DESC", $id), ARRAY_A);
		if (count($rawNotifications) == 1) {
			$raw = $rawNotifications[0];
			return new wfNotification($raw['id'], $raw['priority'], $raw['html'], $raw['category'], $raw['ctime'], json_decode($raw['links'], true), true);
		}
		return null;
	}
	
	public static function getNotificationForCategory($category, $requireNew = true) {
		global $wpdb;
		$table_wfNotifications = wfDB::networkTable('wfNotifications');
		$rawNotifications = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_wfNotifications} WHERE " . ($requireNew ? '`new` = 1 AND ' : '') . "`category` = %s ORDER BY `priority` ASC, `ctime` DESC LIMIT 1", $category), ARRAY_A);
		if (count($rawNotifications) == 1) {
			$raw = $rawNotifications[0];
			return new wfNotification($raw['id'], $raw['priority'], $raw['html'], $raw['category'], $raw['ctime'], json_decode($raw['links'], true), true);
		}
		return null;
	}
	
	public static function reconcileNotificationsWithOptions() {
		$notification_updatesNeeded = wfConfig::get('notification_updatesNeeded');
		$notification_securityAlerts = wfConfig::get('notification_securityAlerts') || !wfConfig::p();
		$notification_promotions = wfConfig::get('notification_promotions') || !wfConfig::p();
		$notification_blogHighlights = wfConfig::get('notification_blogHighlights') || !wfConfig::p();
		$notification_productUpdates = wfConfig::get('notification_productUpdates') || !wfConfig::p();
		$notification_scanStatus = wfConfig::get('notification_scanStatus');
		
		$notifications = self::notifications();
		foreach ($notifications as $n) {
			$category = $n->category;
			
			if (preg_match('/^release/i', $category) && !$notification_productUpdates) { $n->markAsRead(); }
			if (preg_match('/^digest/i', $category) && !$notification_blogHighlights) { $n->markAsRead(); }
			if (preg_match('/^alert/i', $category) && !$notification_securityAlerts) { $n->markAsRead(); }
			if (preg_match('/^promo/i', $category) && !$notification_promotions) { $n->markAsRead(); }
			
			switch ($category) {
				case 'wfplugin_scan':
					if (!$notification_scanStatus) { $n->markAsRead(); }
					break;
				case 'wfplugin_updates':
					if (!$notification_updatesNeeded) { $n->markAsRead(); }
					break;
				case 'wfplugin_keyconflict':
				default:
					//Allow it
					break;
			}
		}
	}
	
	public function __construct($id, $priority, $html, $category = null, $ctime = null, $links = null, $memoryOnly = false) {
		if ($id === null) {
			$id = 'site-' . wfUtils::base32_encode(pack('I', wfConfig::atomicInc('lastNotificationID')));
		}
		
		if ($category === null) {
			$category = '';
		}
		
		if ($ctime === null) {
			$ctime = time();
		} 
		
		if (!is_array($links)) {
			$links = array();
		}
		
		$this->_id = $id;
		$this->_category = $category;
		$this->_priority = $priority;
		$this->_ctime = $ctime;
		$this->_html = $html;
		$this->_links = $links;
		
		global $wpdb;
		if (!$memoryOnly) {
			$linksJSON = json_encode($links);
			
			$notification_updatesNeeded = wfConfig::get('notification_updatesNeeded');
			$notification_securityAlerts = wfConfig::get('notification_securityAlerts') || !wfConfig::p();
			$notification_promotions = wfConfig::get('notification_promotions') || !wfConfig::p();
			$notification_blogHighlights = wfConfig::get('notification_blogHighlights') || !wfConfig::p();
			$notification_productUpdates = wfConfig::get('notification_productUpdates') || !wfConfig::p();
			$notification_scanStatus = wfConfig::get('notification_scanStatus');
			
			if (preg_match('/^release/i', $category) && !$notification_productUpdates) { return; }
			if (preg_match('/^digest/i', $category) && !$notification_blogHighlights) { return; }
			if (preg_match('/^alert/i', $category) && !$notification_securityAlerts) { return; }
			if (preg_match('/^promo/i', $category) && !$notification_promotions) { return; }
			
			switch ($category) {
				case 'wfplugin_scan':
					if (!$notification_scanStatus) { return; }
					break;
				case 'wfplugin_updates':
					if (!$notification_updatesNeeded) { return; }
					break;
				case 'wfplugin_keyconflict':
				default:
					//Allow it
					break;
			}
			
			$table_wfNotifications = wfDB::networkTable('wfNotifications');
			if (!empty($category)) {
				$existing = self::getNotificationForCategory($category);
				if ($existing) {
					$wpdb->query($wpdb->prepare("UPDATE {$table_wfNotifications} SET priority = %d, ctime = %d, html = %s, links = %s WHERE id = %s", $priority, $ctime, $html, $linksJSON, $existing->id));
					return;
				}
			}
			
			$wpdb->query($wpdb->prepare("INSERT IGNORE INTO {$table_wfNotifications} (id, category, priority, ctime, html, links) VALUES (%s, %s, %d, %d, %s, %s)", $id, $category, $priority, $ctime, $html, $linksJSON));
		}
	}
	
	public function __get($key){
		if ($key == 'id') { return $this->_id; }
		else if ($key == 'category') { return $this->_category; }
		else if ($key == 'priority') { return $this->_priority; }
		else if ($key == 'ctime') { return $this->_ctime; }
		else if ($key == 'html') { return $this->_html; }
		else if ($key == 'links') { return $this->_links; }
		throw new InvalidArgumentException();
	}
	
	public function markAsRead() {
		global $wpdb;
		$table_wfNotifications = wfDB::networkTable('wfNotifications');
		$wpdb->query($wpdb->prepare("UPDATE {$table_wfNotifications} SET `new` = 0 WHERE `id` = %s", $this->_id));
	}
}
