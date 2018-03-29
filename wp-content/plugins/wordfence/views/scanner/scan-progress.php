<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a block list element specifically for the scan progress indicator.
 *
 * Expects $scanner.
 *
 * @var wfScanner $scanner The scanner state.
 */

$status = $scanner->stageStatus();
?>
<ul class="wf-scanner-progress">
	<?php
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-spamvertising',
		'title' => __('Spamvertising Checks', 'wordfence'),
		'status' => $status[wfScanner::STAGE_SPAMVERTISING_CHECKS],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-spam',
		'title' => __('Spam Check', 'wordfence'),
		'status' => $status[wfScanner::STAGE_SPAM_CHECK],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-blacklist',
		'title' => __('Blacklist Check', 'wordfence'),
		'status' => $status[wfScanner::STAGE_BLACKLIST_CHECK],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-server',
		'title' => __('Server State', 'wordfence'),
		'status' => $status[wfScanner::STAGE_SERVER_STATE],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-changes',
		'title' => __('File Changes', 'wordfence'),
		'status' => $status[wfScanner::STAGE_FILE_CHANGES],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-malware',
		'title' => __('Malware Scan', 'wordfence'),
		'status' => $status[wfScanner::STAGE_MALWARE_SCAN],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-content',
		'title' => __('Content Safety', 'wordfence'),
		'status' => $status[wfScanner::STAGE_CONTENT_SAFETY],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-public',
		'title' => __('Public Files', 'wordfence'),
		'status' => $status[wfScanner::STAGE_PUBLIC_FILES],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-password',
		'title' => __('Password Strength', 'wordfence'),
		'status' => $status[wfScanner::STAGE_PASSWORD_STRENGTH],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-vulnerability',
		'title' => __('Vulnerability Scan', 'wordfence'),
		'status' => $status[wfScanner::STAGE_VULNERABILITY_SCAN],
	))->render();
	
	echo wfView::create('scanner/scan-progress-element', array(
		'scanner' => $scanner,
		'id' => 'wf-scan-options',
		'title' => __('User & Option Audit', 'wordfence'),
		'status' => $status[wfScanner::STAGE_OPTIONS_AUDIT],
	))->render();
	?>
</ul>
