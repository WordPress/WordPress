<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<p><?php printf(__('This email was sent from your website "%s" by the Wordfence plugin.', 'wordfence'), esc_html(get_bloginfo('name', 'raw'))); ?></p>

<p><?php printf(__('Wordfence found the following new issues on "%s".', 'wordfence'), esc_html(get_bloginfo('name', 'raw'))); ?></p>

<p><?php printf(__('Alert generated at %s', 'wordfence'), esc_html(wfUtils::localHumanDate())); ?></p>

<?php if (wfConfig::get('scansEnabled_highSense')): ?>
	<div style="margin: 12px 0;padding: 8px; background-color: #ffffe0; border: 1px solid #ffd975; border-width: 1px 1px 1px 10px;">
		<em><?php _e('HIGH SENSITIVITY scanning is enabled, it may produce false positives', 'wordfence'); ?></em>
	</div>
<?php endif ?>

<?php if (wfConfig::get('betaThreatDefenseFeed')): ?>
	<div style="margin: 12px 0;padding: 8px; background-color: #ffffe0; border: 1px solid #ffd975; border-width: 1px 1px 1px 10px;">
		<?php _e('Beta scan signatures are currently enabled. These signatures have not been fully tested yet and may cause false positives or scan stability issues on some sites.', 'wordfence'); echo ' '; _e('The Beta option can be turned off at the bottom of the Diagnostics page.', 'wordfence'); ?>
	</div>
<?php endif; ?>

<?php if ($timeLimitReached): ?>
	<div style="margin: 12px 0;padding: 8px; background-color: #ffffe0; border: 1px solid #ffd975; border-width: 1px 1px 1px 10px;">
		<em><?php printf(__('The scan was terminated early because it reached the time limit for scans. If you would like to allow your scans to run longer, you can customize the limit on the options page: <a href="%s">%s</a> or read more about scan options to improve scan speed here: <a href="%s">%s</a>', 'wordfence'), esc_attr(network_admin_url('admin.php?page=WordfenceScan&subpage=scan_options#wf-scanner-options-performance')), esc_attr(network_admin_url('admin.php?page=WordfenceScan&subpage=scan_options')), wfSupportController::esc_supportURL(wfSupportController::ITEM_SCAN_TIME_LIMIT), esc_html(wfSupportController::supportURL(wfSupportController::ITEM_SCAN_TIME_LIMIT))); ?></em>
	</div>
<?php endif ?>

<?php if($totalCriticalIssues > 0){ ?>
<p><?php _e('Critical Problems:', 'wordfence'); ?></p>

<?php foreach($issues as $i){ if($i['severity'] == 1){ ?>
<p>* <?php echo htmlspecialchars($i['shortMsg']) ?></p>
<?php
	if ((isset($i['tmplData']['wpRemoved']) && $i['tmplData']['wpRemoved']) || (isset($i['tmplData']['abandoned']) && $i['tmplData']['abandoned'])) {
		if (isset($i['tmplData']['vulnerable']) && $i['tmplData']['vulnerable']) {
			echo '<p><strong>' . __('Plugin contains an unpatched security vulnerability.', 'wordfence') . '</strong>';
			if (isset($i['tmplData']['vulnerabilityLink'])) {
				echo ' <a href="' . $i['tmplData']['vulnerabilityLink'] . '" target="_blank" rel="nofollow noreferer noopener">' . __('Vulnerability Information', 'wordfence') . '</a>';
			}
			echo '</p>';
		}
	}
	else if (isset($i['tmplData']['wpURL'])) {
		echo '<p>';
		if (isset($i['tmplData']['vulnerable']) && $i['tmplData']['vulnerable']) {
			echo '<strong>' . __('Update includes security-related fixes.', 'wordfence') . '</strong> ';
			if (isset($i['tmplData']['vulnerabilityLink'])) {
				echo '<a href="' . $i['tmplData']['vulnerabilityLink'] . '" target="_blank" rel="nofollow noreferer noopener">' . __('Vulnerability Information', 'wordfence') . '</a> ';
			}
		}
		echo $i['tmplData']['wpURL'] . '/#developers</p>';
	}
	else if (isset($i['tmplData']['vulnerable']) && $i['tmplData']['vulnerable']) {
		echo '<p><strong>' . __('Update includes security-related fixes.', 'wordfence') . '</strong>';
		if (isset($i['tmplData']['vulnerabilityLink'])) {
			echo ' <a href="' . $i['tmplData']['vulnerabilityLink'] . '" target="_blank" rel="nofollow noreferer noopener">' . __('Vulnerability Information', 'wordfence') . '</a>';
		}
		echo '</p>';
	}
?>
<?php if (!empty($i['tmplData']['badURL'])): ?>
<p><img src="<?php echo WORDFENCE_API_URL_BASE_NONSEC . "?" . http_build_query(array(
		'v' => wfUtils::getWPVersion(), 
		's' => home_url(),
		'k' => wfConfig::get('apiKey'),
		'action' => 'image',
		'txt' => base64_encode($i['tmplData']['badURL'])
	), '', '&') ?>" alt="" /></p>
<?php endif ?>

<?php } } } ?>

<?php if($level == 2 && $totalWarningIssues > 0){ ?>
<p><?php _e('Warnings:', 'wordfence'); ?></p>

<?php foreach($issues as $i){ if($i['severity'] == 2){  ?>
<p>* <?php echo htmlspecialchars($i['shortMsg']) ?></p>
		<?php if (isset($i['tmplData']['wpURL'])): ?>
			<p><?php echo $i['tmplData']['wpURL']; ?>/#developers</p>
		<?php endif ?>

<?php } } } ?>

<?php if ($issuesNotShown > 0) { ?>
<p><?php printf(($issuesNotShown == 1 ? __('%d issue was omitted from this email.', 'wordfence') : __('%d issues were omitted from this email.', 'wordfence')), $issuesNotShown); echo ' '; _e('View every issue:', 'wordfence'); ?> <a href="<?php echo esc_attr(network_admin_url('admin.php?page=WordfenceScan')); ?>"><?php echo esc_html(network_admin_url('admin.php?page=WordfenceScan')); ?></a></p>
<?php } ?>


<?php if(! $isPaid){ ?>
	<p><?php _e('NOTE: You are using the free version of Wordfence. Upgrade today:', 'wordfence'); ?></p>
	
	<ul>
		<li><?php _e('Receive real-time Firewall and Scan engine rule updates for protection as threats emerge', 'wordfence'); ?></li>
		<li><?php _e('Real-time IP Blacklist blocks the most malicious IPs from accessing your site', 'wordfence'); ?></li>
		<li><?php _e('Country blocking', 'wordfence'); ?></li>
		<li><?php _e('Two factor authentication', 'wordfence'); ?></li>
		<li><?php _e('IP reputation monitoring', 'wordfence'); ?></li>
		<li><?php _e('Advanced comment spam filter', 'wordfence'); ?></li>
		<li><?php _e('Schedule scans to run more frequently and at optimal times', 'wordfence'); ?></li>
		<li><?php _e('Access to Premium Support', 'wordfence'); ?></li>
		<li><?php _e('Discounts for multi-license purchases', 'wordfence'); ?></li>
	</ul>

	<p><?php _e('Click here to upgrade to Wordfence Premium:', 'wordfence'); ?><br><a href="https://www.wordfence.com/zz2/wordfence-signup/">https://www.wordfence.com/zz2/wordfence-signup/</a></p>
<?php } ?>



