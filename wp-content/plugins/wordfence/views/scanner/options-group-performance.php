<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Performance Options group.
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
						<strong><?php _e('Performance Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<?php
					$options = array(
						array('key' => 'lowResourceScansEnabled', 'label' => __('Use low resource scanning (reduces server load by lengthening the scan duration)', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_LOW_RESOURCE)),
						array('key' => 'scan_maxIssues', 'label' => __('Limit the number of issues sent in the scan results email', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_LIMIT_ISSUES), 'view' => 'options/option-text', 'parameters' => array('subtitle' => __('0 or empty means unlimited issues will be sent', 'wordfence'))),
						array('key' => 'scan_maxDuration', 'label' => __('Time limit that a scan can run in seconds', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_OVERALL_TIME_LIMIT), 'view' => 'options/option-text', 'parameters' => array('subtitle' => sprintf(__('0 or empty means the default of %s will be used', 'wordfence'), wfUtils::makeDuration(WORDFENCE_DEFAULT_MAX_SCAN_TIME)))),
						array('key' => 'maxMem', 'label' => __('How much memory should Wordfence request when scanning', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_MEMORY_LIMIT), 'view' => 'options/option-text', 'parameters' => array('subtitle' => __('Memory size in megabytes', 'wordfence'))),
						array('key' => 'maxExecutionTime', 'label' => __('Maximum execution time for each scan stage ', 'wordfence'), 'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_STAGE_TIME_LIMIT), 'view' => 'options/option-text', 'parameters' => array('subtitle' => sprintf(__('0 for default. Must be greater than %d and 10-20 or higher is recommended for most servers', 'wordfence'), intval(WORDFENCE_SCAN_MIN_EXECUTION_TIME) - 1))),
					);
					foreach ($options as $o):
						?>
						<li>
							<?php
							if (isset($o['view']) && $o['view'] == 'options/option-text') {
								if (!isset($o['parameters'])) { $o['parameters'] = array(); }
								echo wfView::create($o['view'], array_merge(array(
									'textOptionName' => $o['key'],
									'textValue' => wfConfig::get($o['key']),
									'title' => $o['label'],
									'helpLink' => $o['helpLink'],
								), $o['parameters']))->render();
							}
							else {
								echo wfView::create('options/option-toggled', array(
									'optionName' => $o['key'],
									'enabledValue' => 1,
									'disabledValue' => 0,
									'value' => wfConfig::get($o['key']) ? 1 : 0,
									'title' => $o['label'],
									'helpLink' => $o['helpLink'],
									'disabled' => isset($o['disabled']) ? $o['disabled'] : false,
								))->render();
							}
							?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end performance options -->