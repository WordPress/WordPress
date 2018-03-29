<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the View Customization group.
 *
 * Expects $stateKey.
 *
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
						<strong><?php _e('View Customization', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'displayTopLevelOptions',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('displayTopLevelOptions') ? 1 : 0,
							'title' => __('Display "All Options" menu item', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'displayTopLevelBlocking',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('displayTopLevelBlocking') ? 1 : 0,
							'title' => __('Display "Blocking" menu item', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'displayTopLevelLiveTraffic',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('displayTopLevelLiveTraffic') ? 1 : 0,
							'title' => __('Display "Live Traffic" menu item', 'wordfence'),
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end custom scan options -->