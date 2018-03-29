<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Dashboard Notification Options group.
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
						<strong><?php _e('Dashboard Notification Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'notification_updatesNeeded',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('notification_updatesNeeded') ? 1 : 0,
							'title' => __('Updates Needed (Plugin, Theme, or Core)', 'wordfence'),
						))->render();
						?>
					</li>
					<?php if (wfConfig::p()): ?>
						<li>
							<?php
							echo wfView::create('options/option-toggled', array(
								'optionName' => 'notification_securityAlerts',
								'enabledValue' => 1,
								'disabledValue' => 0,
								'value' => wfConfig::get('notification_securityAlerts') ? 1 : 0,
								'title' => __('Security Alerts', 'wordfence'),
								'premium' => true,
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('options/option-toggled', array(
								'optionName' => 'notification_promotions',
								'enabledValue' => 1,
								'disabledValue' => 0,
								'value' => wfConfig::get('notification_promotions') ? 1 : 0,
								'title' => __('Promotions', 'wordfence'),
								'premium' => true,
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('options/option-toggled', array(
								'optionName' => 'notification_blogHighlights',
								'enabledValue' => 1,
								'disabledValue' => 0,
								'value' => wfConfig::get('notification_blogHighlights') ? 1 : 0,
								'title' => __('Blog Highlights', 'wordfence'),
								'premium' => true,
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('options/option-toggled', array(
								'optionName' => 'notification_productUpdates',
								'enabledValue' => 1,
								'disabledValue' => 0,
								'value' => wfConfig::get('notification_productUpdates') ? 1 : 0,
								'title' => __('Product Updates', 'wordfence'),
								'premium' => true,
							))->render();
							?>
						</li>
					<?php endif; ?>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'notification_scanStatus',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('notification_scanStatus') ? 1 : 0,
							'title' => __('Scan Status', 'wordfence'),
						))->render();
						?>
					</li>
					<?php if (!wfConfig::p()): ?>
						<li>
							<ul class="wf-option">
								<li class="wf-option-spacer"></li>
								<li class="wf-flex-vertical wf-flex-align-left">
									<p><?php _e('Dashboard notifications will also be displayed for Security Alerts, Promotions, Blog Highlights, and Product Updates. These notifications can be disabled by upgrading to a premium license.', 'wordfence'); ?></p>
									<p class="wf-no-top"><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="https://www.wordfence.com/gnl1dashboardUpgrade/wordfence-signup/#premium-order-form" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a>&nbsp;&nbsp;<a class="wf-btn wf-btn-callout-subtle wf-btn-default" href="https://www.wordfence.com/gnl1dashboardLearn/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></p>
								</li>
							</ul>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end dashboard options -->