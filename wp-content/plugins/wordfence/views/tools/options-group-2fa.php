<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Two Factor Authentication Options group.
 *
 * Expects $stateKey.
 *
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 */

if (!isset($collapseable)) {
	$collapseable = true;
}

$helpLink = wfSupportController::supportURL(wfSupportController::ITEM_TOOLS_TWO_FACTOR);
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block<?php if (!$collapseable) { echo ' wf-always-active'; } else { echo (wfPersistenceController::shared()->isActive($stateKey) ? ' wf-active' : ''); } ?>" data-persistence-key="<?php echo esc_attr($stateKey); ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Two Factor Authentication Options', 'wordfence') ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<?php if (!wfConfig::get('isPaid')): ?>
				<h3><?php _e("Take Login Security to the next level with Two Factor Authentication", 'wordfence') ?></h3>
				<p><?php _e('Used by banks, government agencies, and military worldwide, two factor authentication is one of the most secure forms of remote system authentication available. With it enabled, an attacker needs to know your username, password, <em>and</em> have control of your phone to log into your site. Upgrade to Premium now to enable this powerful feature.', 'wordfence') ?></p>

				<p class="wf-nowrap wf-center">
					<img id="wf-two-factor-img1" src="<?php echo wfUtils::getBaseURL() . 'images/2fa1.svg' ?>" alt="">
					<img id="wf-two-factor-img2" src="<?php echo wfUtils::getBaseURL() . 'images/2fa2.svg' ?>" alt="">
				</p>

				<p class="wf-center wf-padding-add-bottom">
					<a class="wf-btn wf-btn-primary wf-btn-callout" href="https://www.wordfence.com/gnl1twoFac1/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence') ?></a>
				</p>
				<?php else: ?>
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName'    => 'loginSec_requireAdminTwoFactor',
							'enabledValue'  => 1,
							'disabledValue' => 0,
							'value'         => wfConfig::get('loginSec_requireAdminTwoFactor') ? 1 : 0,
							'htmlTitle'     => sprintf(__('<strong>Require Cellphone Sign-in for all Administrators<a href="%s" target="_blank" rel="noopener noreferrer" class="wfhelp wf-inline-help"></a></strong><br><em>Note:</em> This setting requires at least one administrator to have Cellphone Sign-in enabled. On multisite, this option applies only to super admins.', 'wordfence'), esc_url($helpLink)),
						))->render();
						?>
					</li>
					<li>
						<?php
						$allowSeparatePrompt = ini_get('output_buffering') > 0;
						echo wfView::create('options/option-toggled', array(
							'optionName'    => 'loginSec_enableSeparateTwoFactor',
							'enabledValue'  => 1,
							'disabledValue' => 0,
							'value'         => wfConfig::get('loginSec_enableSeparateTwoFactor') ? 1 : 0,
							'htmlTitle'     => sprintf(__('<strong>Enable Separate Prompt for Two Factor Code<a href="%s" target="_blank" rel="noopener noreferrer" class="wfhelp wf-inline-help"></a></strong><br><em>Note:</em> This setting changes the behavior for obtaining the two factor authentication code from using the password field to showing a separate prompt. If your theme overrides the default login page, you may not be able to use this option.', 'wordfence'), $helpLink) .
								($allowSeparatePrompt ? '' : __('<br><strong>This setting will be ignored because the PHP configuration option <code>output_buffering</code> is off.</strong>', 'wordfence')),
						))->render();
						?>
					</li>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>