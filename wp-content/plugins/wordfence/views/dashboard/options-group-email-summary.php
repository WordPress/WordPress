<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Activity Report group.
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
						<strong><?php _e('Activity Report', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-toggled-select', array(
							'toggleOptionName' => 'email_summary_enabled',
							'enabledToggleValue' => 1,
							'disabledToggleValue' => 0,
							'toggleValue' => wfConfig::get('email_summary_enabled') ? 1 : 0,
							'selectOptionName' => 'email_summary_interval',
							'selectOptions' => array(
								array('value' => 'daily', 'label' => __('Once a day', 'wordfence')),
								array('value' => 'weekly', 'label' => __('Once a week', 'wordfence')),
								array('value' => 'monthly', 'label' => __('Once a month', 'wordfence')),
							),
							'selectValue' => wfConfig::get('email_summary_interval'),
							'title' => __('Enable email summary', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'email_summary_excluded_directories',
							'textValue' => wfUtils::cleanupOneEntryPerLine(wfConfig::get('email_summary_excluded_directories')),
							'title' => __('List of directories to exclude from recently modified file list', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'email_summary_dashboard_widget_enabled',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('email_summary_dashboard_widget_enabled') ? 1 : 0,
							'title' => __('Enable activity report widget on the WordPress dashboard', 'wordfence'),
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end email summary options -->