<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Advanced Firewall Options group.
 *
 * Expects $firewall, $waf, and $stateKey.
 *
 * @var wfFirewall $firewall
 * @var wfWAF $waf
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 */

$config = $waf->getStorageEngine();

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
						<strong><?php _e('Advanced Firewall Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'disableWAFIPBlocking',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => $config->getConfig('disableWAFIPBlocking') ? 1 : 0,
							'title' => __('Delay IP and Country blocking until after WordPress and plugins have loaded (only process firewall rules early)', 'wordfence'),
							'subtitle' => ($firewall->isSubDirectoryInstallation() ? __('You are currently running the WAF from another WordPress installation. This option can be changed once you configure the firewall to run correctly on this site.', 'wordfence') : ''),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_DELAY_BLOCKING),
							'disabled' => $firewall->isSubDirectoryInstallation(),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'whitelisted',
							'textValue' => wfUtils::cleanupOneEntryPerLine(wfConfig::get('whitelisted')),
							'title' => __('Whitelisted IP addresses that bypass all rules', 'wordfence'),
							'alignTitle' => 'top',
							'subtitleHTML' => __('Whitelisted IPs must be separated by commas or placed on separate lines. You can specify ranges using the following formats: 127.0.0.1/24, 127.0.0.[1-100], or 127.0.0.1 - 127.0.1.100<br/>Wordfence automatically whitelists <a href="http://en.wikipedia.org/wiki/Private_network" target="_blank" rel="noopener noreferrer">private networks</a> because these are not routable on the public Internet.', 'wordfence'),
							'subtitlePosition' => 'value',
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_WHITELISTED_IPS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'bannedURLs',
							'textValue' => wfUtils::cleanupOneEntryPerLine(wfConfig::get('bannedURLs')),
							'title' => __('Immediately block IPs that access these URLs', 'wordfence'),
							'alignTitle' => 'top',
							'subtitle' => __('Separate multiple URLs with commas or place them on separate lines. Asterisks are wildcards, but use with care. If you see an attacker repeatedly probing your site for a known vulnerability you can use this to immediately block them. All URLs must start with a "/" without quotes and must be relative. e.g. /badURLone/, /bannedPage.html, /dont-access/this/URL/, /starts/with-*', 'wordfence'),
							'subtitlePosition' => 'value',
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_URLS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'wafAlertWhitelist',
							'textValue' => wfUtils::cleanupOneEntryPerLine(wfConfig::get('wafAlertWhitelist')),
							'title' => __('Ignored IP addresses for Wordfence Web Application Firewall alerting', 'wordfence'),
							'alignTitle' => 'top',
							'subtitle' => __('Ignored IPs must be separated by commas or placed on separate lines. These addresses will be ignored from any alerts about increased attacks and can be used to ignore things like standalone website security scanners.', 'wordfence'),
							'subtitlePosition' => 'value',
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_IGNORED_ALERT_IPS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('waf/option-rules', array(
							'firewall' => $firewall,
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end advanced firewall options -->
<script type="text/x-jquery-template" id="waf-rules-tmpl">
	<table class="wf-striped-table">
		<thead>
		<tr>
			<th style="width: 5%"></th>
			<th><?php _e('Category', 'wordfence'); ?></th>
			<th><?php _e('Description', 'wordfence'); ?></th>
		</tr>
		</thead>
		<tbody>
		{{each(idx, rule) rules}}
		<tr data-rule-id="${rule.ruleID}" data-original-value="{{if (!disabledRules[rule.ruleID])}}1{{else}}0{{/if}}">
			<td style="text-align: center">
				<div class="wf-rule-toggle wf-boolean-switch{{if (!disabledRules[rule.ruleID])}} wf-active{{/if}}<?php echo ($firewall->isSubDirectoryInstallation() ? ' wf-disabled' : ''); ?>"><a href="#" class="wf-boolean-switch-handle"></a></div>
			</td>
			<td>${rule.category}</td>
			<td>${rule.description}</td>
		</tr>
		{{/each}}
		{{if (rules.length == 0)}}
		<tr>
			<td colspan="4"><?php _e('No rules currently set.', 'wordfence'); ?> <?php if (!($firewall->protectionMode() == wfFirewall::PROTECTION_MODE_EXTENDED && $firewall->isSubDirectoryInstallation())) { _e('<a href="#" onclick="WFAD.wafUpdateRules();return false;">Click here</a> to pull down the latest from the Wordfence servers.', 'wordfence'); } ?>
			</td>
		</tr>
		{{/if}}
		</tbody>
		<tfoot>
		{{if (ruleCount >= 10)}}
		<tr id="waf-show-all-rules">
			<td class="wf-center" colspan="4"><a href="#" id="waf-show-all-rules-button"><?php _e('SHOW ALL RULES', 'wordfence'); ?></a></td>
		</tr>
		{{/if}}
		</tfoot>
	</table>
</script>
<script type="application/javascript">
	(function($) {
		$(window).on('wordfenceWAFConfigPageRender', function() {
			delete WFAD.pendingChanges['wafRules'];

			//Add event handler to rule checkboxes
			$('.wf-rule-toggle.wf-boolean-switch').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					$(this).find('.wf-boolean-switch-handle').trigger('click');
				});

				$(this).find('.wf-boolean-switch-handle').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					var control = $(this).closest('.wf-boolean-switch');
					var row = $(this).closest('tr');
					var ruleID = row.data('ruleId');
					var value = control.hasClass('wf-active') ? 1 : 0;
					if (value) {
						control.removeClass('wf-active');
						value = 0;
					}
					else {
						control.addClass('wf-active');
						value = 1;
					}

					var originalValue = row.data('originalValue');
					if (originalValue == value) {
						delete WFAD.pendingChanges['wafRules'][ruleID];
						if (Object.keys(WFAD.pendingChanges['wafRules']).length == 0) {
							delete WFAD.pendingChanges['wafRules']
						}
					}
					else {
						if (!(WFAD.pendingChanges['wafRules'] instanceof Object)) {
							WFAD.pendingChanges['wafRules'] = {};
						}
						WFAD.pendingChanges['wafRules'][ruleID] = value;
					}

					$(control).trigger('change', [false]);
					WFAD.updatePendingChanges();
				});
			});
		});
	})(jQuery);
</script>