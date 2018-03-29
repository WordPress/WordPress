<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Email Alert Preferences group.
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
						<strong><?php _e('Email Alert Preferences', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_update',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_update') ? 1 : 0,
							'title' => __('Email me when Wordfence is automatically updated', 'wordfence'),
							'subtitle' => __('If you have automatic updates enabled (see above), you\'ll get an email when an update occurs.', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_wordfenceDeactivated',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_wordfenceDeactivated') ? 1 : 0,
							'title' => __('Email me if Wordfence is deactivated', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_critical',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_critical') ? 1 : 0,
							'title' => __('Alert on critical problems', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_warnings',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_warnings') ? 1 : 0,
							'title' => __('Alert on warnings', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_block',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_block') ? 1 : 0,
							'title' => __('Alert when an IP address is blocked', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_loginLockout',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_loginLockout') ? 1 : 0,
							'title' => __('Alert when someone is locked out from login', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_breachLogin',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_breachLogin') ? 1 : 0,
							'title' => __('Alert when someone is blocked from logging in for using a password found in a breach', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'alertOn_lostPasswdForm',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_lostPasswdForm') ? 1 : 0,
							'title' => __('Alert when the "lost password" form is used for a valid user', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled-sub', array(
							'optionName' => 'alertOn_adminLogin',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_adminLogin') ? 1 : 0,
							'title' => __('Alert me when someone with administrator access signs in', 'wordfence'),
							
							'subOptionName' => 'alertOn_firstAdminLoginOnly',
							'subEnabledValue' => 1,
							'subDisabledValue' => 0,
							'subValue' => wfConfig::get('alertOn_firstAdminLoginOnly') ? 1 : 0,
							'subTitle' => __('Only alert me when that administrator signs in from a new device or location', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled-sub', array(
							'optionName' => 'alertOn_nonAdminLogin',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('alertOn_nonAdminLogin') ? 1 : 0,
							'title' => __('Alert me when a non-admin user signs in', 'wordfence'),
							
							'subOptionName' => 'alertOn_firstNonAdminLoginOnly',
							'subEnabledValue' => 1,
							'subDisabledValue' => 0,
							'subValue' => wfConfig::get('alertOn_firstNonAdminLoginOnly') ? 1 : 0,
							'subTitle' => __('Only alert me when that user signs in from a new device or location', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'wafAlertOnAttacks',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('wafAlertOnAttacks') ? 1 : 0,
							'title' => __('Alert me when there\'s a large increase in attacks detected on my site', 'wordfence'),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-text', array(
							'textOptionName' => 'alert_maxHourly',
							'textValue' => wfConfig::get('alert_maxHourly'),
							'title' => __('Maximum email alerts to send per hour', 'wordfence'),
							'subtitle' => __('0 means unlimited alerts will be sent.', 'wordfence'),
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end alert options -->