<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php if(! wfUtils::isAdmin()){ exit(); } ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel='stylesheet' id='wordfence-main-style-css'  href='<?php echo wfUtils::getBaseURL() . wfUtils::versionedAsset('css/fullLog.css'); ?>?ver=<?php echo WORDFENCE_VERSION; ?>' type='text/css' media='all' />
<style type="text/css">

</style>
<body>
<h1>Wordfence Full Activity Log</h1>
<?php
$db = new wfDB();
global $wpdb;
$debugOn = wfConfig::get('debugOn', 0);
$table = wfDB::networkTable('wfStatus');
$offset = 0;
$timeOffset = 3600 * get_option('gmt_offset');
$q = $db->querySelect("SELECT ctime, level, type, msg FROM {$table} ORDER BY ctime DESC LIMIT %d, 100", $offset);
while (is_array($q) && count($q) > 0) {
	foreach($q as $r){
		if($r['level'] < 4 || $debugOn){
			echo '<div' . ($r['type'] == 'error' ? ' class="error"' : '') . '>[' . date('M d H:i:s', $r['ctime'] + $timeOffset) . ':' . $r['ctime'] . ':' . $r['level'] . ':' . $r['type'] . ']&nbsp;' . esc_html($r['msg']) . "</div>\n";
		}
	}
	$offset += count($q);
	$q = $db->querySelect("SELECT ctime, level, type, msg FROM {$table} ORDER BY ctime DESC LIMIT %d, 100", $offset);
}
?>
</body>
</html>
<?php exit(0); ?>
