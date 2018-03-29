<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Advanced Scan Options group.
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
						<strong><?php _e('Advanced Scan Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'scan_exclude',
							'textValue' => wfUtils::cleanupOneEntryPerLine(wfConfig::get('scan_exclude')),
							'title' => __('Exclude files from scan that match these wildcard patterns (one per line)', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_EXCLUDE_PATTERNS),
							'noSpacer' => true,
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'scan_include_extra',
							'textValue' => wfConfig::get('scan_include_extra'),
							'title' => __('Additional scan signatures (one per line)', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_CUSTOM_MALWARE_SIGNATURES),
							'noSpacer' => true,
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end custom scan options -->