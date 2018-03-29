<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Live Traffic Options group.
 *
 * Expects $stateKey.
 *
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 * @var bool $showControls If defined, specifies whether or not the save/cancel/restore controls are shown. Defaults to false.
 * @var bool $hideShowMenuItem If defined, specifies whether or not the show menu item option is shown. Defaults to false.
 */

if (!isset($collapseable)) {
	$collapseable = true;
}

if (!isset($showControls)) {
	$showControls = false;
}

if (!isset($hideShowMenuItem)) {
	$hideShowMenuItem = false;
}
?>
<div id="wf-live-traffic-options" class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block<?php if (!$collapseable) { echo ' wf-always-active'; } else { echo (wfPersistenceController::shared()->isActive($stateKey) ? ' wf-active' : ''); } ?>" data-persistence-key="<?php echo esc_attr($stateKey); ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Live Traffic Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix">
				<?php if ($showControls): ?>
				<p>
					<?php _e('These options let you ignore certain types of visitors, based on their level of access, usernames, IP address or browser type. If you run a very high traffic website where it is not feasible to see your visitors in real-time, simply un-check the live traffic option and nothing will be written to the Wordfence tracking tables.', 'wordfence') ?>
				</p>
				
				<div class="wf-row">
					<div class="wf-col-xs-12">
						<?php
						echo wfView::create('options/block-controls', array(
							'suppressLogo' => true,
							'restoreDefaultsSection' => wfConfig::OPTIONS_TYPE_LIVE_TRAFFIC,
							'restoreDefaultsMessage' => __('Are you sure you want to restore the default Live Traffic settings? This will undo any custom changes you have made to the options on this page.', 'wordfence'),
						))->render();
						?>
					</div>
				</div>
				<?php endif; ?>
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-switch', array(
							'optionName' => 'liveTrafficEnabled',
							'value' => wfConfig::get('liveTrafficEnabled') ? '1': '0',
							'title' => __('Enable live traffic logging', 'wordfence'),
							'states' => array(
								array('value' => '0', 'label' => __('Off', 'wordfence')),
								array('value' => '1', 'label' => __('On', 'wordfence')),
							),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_TOOLS_LIVE_TRAFFIC_OPTION_ENABLE),
							'alignment' => 'wf-right',
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName'    => 'liveTraf_ignorePublishers',
							'enabledValue'  => 1,
							'disabledValue' => 0,
							'value'         => wfConfig::get('liveTraf_ignorePublishers') ? 1 : 0,
							'title'         => __("Don't log signed-in users with publishing access.", 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-text', array(
							'textOptionName' => 'liveTraf_ignoreUsers',
							'textValue'      => wfConfig::get('liveTraf_ignoreUsers'),
							'title'          => __('List of comma separated usernames to ignore.', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-text', array(
							'textOptionName' => 'liveTraf_ignoreIPs',
							'textValue'      => wfConfig::get('liveTraf_ignoreIPs'),
							'title'          => __('List of comma separated IP addresses to ignore.', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-text', array(
							'textOptionName' => 'liveTraf_ignoreUA',
							'textValue'      => wfConfig::get('liveTraf_ignoreUA'),
							'title'          => __('Browser user-agent to ignore.', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-text', array(
							'textOptionName' => 'liveTraf_maxRows',
							'textValue'      => wfConfig::get('liveTraf_maxRows'),
							'title'          => __('Amount of Live Traffic data to store (number of rows).', 'wordfence'),
						))->render();
						?>
					</li>
					<?php if (!$hideShowMenuItem): ?>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'displayTopLevelLiveTraffic',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('displayTopLevelLiveTraffic') ? 1 : 0,
							'title' => __('Display Live Traffic menu option', 'wordfence'),
						))->render();
						?>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>