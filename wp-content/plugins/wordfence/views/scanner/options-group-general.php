<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the General Options group.
 *
 * Expects $scanner and $stateKey.
 *
 * @var wfScanner $scanner
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 */

if (!isset($collapseable)) {
	$collapseable = true;
}
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block<?php if (!$collapseable) { echo ' wf-always-active'; } else { echo (wfPersistenceController::shared()->isActive($stateKey) ? ' wf-active' : ''); } ?>" data-persistence-key="<?php echo esc_attr($stateKey); ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('General Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<?php
					$options = array(
						array('key' => 'scansEnabled_checkGSB', 'label' => __('Check if this website is on a domain blacklist', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_CHECK_SITE_BLACKLISTED), 'premium' => true, 'subtitleHTML' => __('<em>Reputation check</em>', 'wordfence')),
						array('key' => 'spamvertizeCheck', 'label' => __('Check if this website is being "Spamvertised"', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_CHECK_SITE_SPAMVERTIZED), 'premium' => true, 'subtitleHTML' => __('<em>Reputation check</em>', 'wordfence')),
						array('key' => 'checkSpamIP', 'label' => __('Check if this website IP is generating spam', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_CHECK_IP_SPAMMING), 'premium' => true, 'subtitleHTML' => __('<em>Reputation check</em>', 'wordfence')),
						array('key' => 'scansEnabled_checkHowGetIPs', 'label' => __('Scan for misconfigured How does Wordfence get IPs', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_CHECK_MISCONFIGURED_HOW_GET_IPS)),
						array('key' => 'scansEnabled_checkReadableConfig', 'label' => __('Scan for publicly accessible configuration, backup, or log files', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_PUBLIC_CONFIG)),
						array('key' => 'scansEnabled_suspectedFiles', 'label' => __('Scan for publicly accessible quarantined files', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_PUBLIC_QUARANTINED)),
						array('key' => 'scansEnabled_core', 'label' => __('Scan core files against repository versions for changes', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_CORE_CHANGES)),
						array('key' => 'scansEnabled_themes', 'label' => __('Scan theme files against repository versions for changes', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_THEME_CHANGES)),
						array('key' => 'scansEnabled_plugins', 'label' => __('Scan plugin files against repository versions for changes', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_PLUGIN_CHANGES)),
						array('key' => 'scansEnabled_coreUnknown', 'label' => __('Scan wp-admin and wp-includes for files not bundled with WordPress', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_UNKNOWN_CORE)),
						array('key' => 'scansEnabled_malware', 'label' => __('Scan for signatures of known malicious files', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_MALWARE_HASHES)),
						array('key' => 'scansEnabled_fileContents', 'label' => __('Scan file contents for backdoors, trojans and suspicious code', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_MALWARE_SIGNATURES)),
						array('key' => 'scansEnabled_fileContentsGSB', 'label' => __('Scan file contents for malicious URLs', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_MALWARE_URLS)),
						array('key' => 'scansEnabled_posts', 'label' => __('Scan posts for known dangerous URLs and suspicious content', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_POST_URLS)),
						array('key' => 'scansEnabled_comments', 'label' => __('Scan comments for known dangerous URLs and suspicious content', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_COMMENT_URLS)),
						array('key' => 'scansEnabled_suspiciousOptions', 'label' => __('Scan WordPress core, plugin, and theme options for known dangerous URLs and suspicious content', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_MALWARE_OPTIONS)),
						array('key' => 'scansEnabled_oldVersions', 'label' => __('Scan for out of date, abandoned, and vulnerable plugins, themes, and WordPress versions', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_UPDATES)),
						array('key' => 'scansEnabled_suspiciousAdminUsers', 'label' => __('Scan for admin users created outside of WordPress', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_UNKNOWN_ADMINS)),
						array('key' => 'scansEnabled_passwds', 'label' => __('Check the strength of passwords', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_PASSWORD_STRENGTH)),
						array('key' => 'scansEnabled_diskSpace', 'label' => __('Monitor disk space', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_DISK_SPACE)),
						array('key' => 'scansEnabled_dns', 'label' => __('Scan for unauthorized DNS changes', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_DNS_CHANGES)),
						array('key' => 'other_scanOutside', 'label' => __('Scan files outside your WordPress installation', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_OUTSIDE_WORDPRESS)),
						array('key' => 'scansEnabled_scanImages', 'label' => __('Scan images, binary, and other files as if they were executable', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_IMAGES_EXECUTABLE)),
						array('key' => 'scansEnabled_highSense', 'label' => __('Enable HIGH SENSITIVITY scanning (may give false positives)', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_HIGH_SENSITIVITY)),
					);
					foreach ($options as $o):
						?>
						<li>
							<?php
							if (isset($o['view'])) {
								echo wfView::create($o['view'], array(
									'optionName' => $o['key'],
									'value' => wfConfig::get($o['key']) ? 1 : 0,
									'title' => $o['label'],
									'helpLink' => $o['helpLink'],
									'premium' => isset($o['premium']) && $o['premium'],
								))->render();
							}
							else {
								echo wfView::create('options/option-toggled', array(
									'optionName' => $o['key'],
									'enabledValue' => 1,
									'disabledValue' => 0,
									'value' => wfConfig::get($o['key']) ? 1 : 0,
									'title' => $o['label'],
									'subtitleHTML' => isset($o['subtitleHTML']) ? $o['subtitleHTML'] : null,
									'helpLink' => $o['helpLink'],
									'premium' => isset($o['premium']) && $o['premium'],
								))->render();
							}
							?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end general options -->