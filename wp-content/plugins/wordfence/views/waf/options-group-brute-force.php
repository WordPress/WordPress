<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Brute Force Protection group.
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
						<strong><?php _e('Brute Force Protection', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-switch', array(
							'optionName' => 'loginSecurityEnabled',
							'value' => wfConfig::get('loginSecurityEnabled') ? '1': '0',
							'titleHTML' => '<strong>' . __('Enable brute force protection', 'wordfence') . '</strong>',
							'subtitle' => __('This option enables all "Brute Force Protection" options, including two-factor authentication, strong password enforcement, and invalid login throttling. You can modify individual options below.', 'wordfence'),
							'states' => array(
								array('value' => '0', 'label' => __('Off', 'wordfence')),
								array('value' => '1', 'label' => __('On', 'wordfence')),
							),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_ENABLE_LOGIN_SECURITY),
							'noSpacer' => true,
							'alignment' => 'wf-right',
						))->render();
						?>
					</li>
					<li>
						<?php
						$breakpoints = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 100, 200, 500);
						$options = array();
						foreach ($breakpoints as $b) {
							$options[] = array('value' => $b, 'label' => $b);
						}
						echo wfView::create('options/option-select', array(
							'selectOptionName' => 'loginSec_maxFailures',
							'selectOptions' => $options,
							'selectValue' => wfConfig::get('loginSec_maxFailures'),
							'title' => __('Lock out after how many login failures', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_LOCK_OUT_FAILURE_COUNT),
						))->render();
						?>
					</li>
					<li>
						<?php
						$breakpoints = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 100, 200, 500);
						$options = array();
						foreach ($breakpoints as $b) {
							$options[] = array('value' => $b, 'label' => $b);
						}
						echo wfView::create('options/option-select', array(
							'selectOptionName' => 'loginSec_maxForgotPasswd',
							'selectOptions' => $options,
							'selectValue' => wfConfig::get('loginSec_maxForgotPasswd'),
							'title' => __('Lock out after how many forgot password attempts', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_LOCK_OUT_FORGOT_PASSWORD_COUNT),
						))->render();
						?>
					</li>
					<li>
						<?php
						$breakpoints = array(5, 10, 30, 60, 120, 240, 360, 720, 1440);
						$options = array();
						foreach ($breakpoints as $b) {
							$options[] = array('value' => $b, 'label' => wfUtils::makeDuration($b * 60));
						}
						echo wfView::create('options/option-select', array(
							'selectOptionName' => 'loginSec_countFailMins',
							'selectOptions' => $options,
							'selectValue' => wfConfig::getInt('loginSec_countFailMins'),
							'title' => __('Count failures over what time period', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_COUNT_TIME_PERIOD),
						))->render();
						?>
					</li>
					<li>
						<?php
						$breakpoints = array(5, 10, 30, 60, 120, 240, 360, 720, 1440, 2880, 7200, 14400, 28800, 43200, 86400);
						$options = array();
						foreach ($breakpoints as $b) {
							$options[] = array('value' => $b, 'label' => wfUtils::makeDuration($b * 60));
						}
						echo wfView::create('options/option-select', array(
							'selectOptionName' => 'loginSec_lockoutMins',
							'selectOptions' => $options,
							'selectValue' => wfConfig::getInt('loginSec_lockoutMins'),
							'title' => __('Amount of time a user is locked out', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_LOCKOUT_DURATION),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'loginSec_lockInvalidUsers',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('loginSec_lockInvalidUsers') ? 1 : 0,
							'title' => __('Immediately lock out invalid usernames', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_LOCK_OUT_INVALID_USERS),
						))->render();
						?>
					</li>
					<li>
						<?php
						$blacklist = wfConfig::get('loginSec_userBlacklist', '');
						if (empty($blacklist)) {
							$users = array();
						}
						else {
							$users = explode("\n", wfUtils::cleanupOneEntryPerLine($blacklist));
						}
						
						echo wfView::create('options/option-token', array(
							'tokenOptionName' => 'loginSec_userBlacklist',
							'tokenValue' => $users,
							'title' => __('Immediately block the IP of users who try to sign in as these usernames', 'wordfence'),
							'subtitle' => __('Hit enter to add a username', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_IMMEDIATELY_BLOCK_USERS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled-select', array(
							'toggleOptionName' => 'loginSec_breachPasswds_enabled',
							'enabledToggleValue' => 1,
							'disabledToggleValue' => 0,
							'toggleValue' => !!wfConfig::get('loginSec_breachPasswds_enabled') ? 1 : 0,
							'selectOptionName' => 'loginSec_breachPasswds',
							'selectOptions' => array(array('value' => 'admins', 'label' => __('For admins only', 'wordfence')), array('value' => 'pubs', 'label' => __('For all users with "publish posts" capability', 'wordfence'))),
							'selectValue' => wfConfig::get('loginSec_breachPasswds'),
							'title' => __('Prevent the use of passwords leaked in data breaches', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_PREVENT_BREACH_PASSWORDS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-label', array(
							'titleHTML' => '<strong>' . __('Additional Options', 'wordfence') . '</strong>',
							'noSpacer' => true,
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled-select', array(
							'toggleOptionName' => 'loginSec_strongPasswds_enabled',
							'enabledToggleValue' => 1,
							'disabledToggleValue' => 0,
							'toggleValue' => !!wfConfig::get('loginSec_strongPasswds_enabled') ? 1 : 0,
							'selectOptionName' => 'loginSec_strongPasswds',
							'selectOptions' => array(array('value' => 'pubs', 'label' => __('Force admins and publishers to use strong passwords (recommended)', 'wordfence')), array('value' => 'all', 'label' => __('Force all members to use strong passwords', 'wordfence'))),
							'selectValue' => wfConfig::get('loginSec_strongPasswds'),
							'title' => __('Enforce strong passwords', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_ENFORCE_STRONG_PASSWORDS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'loginSec_maskLoginErrors',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('loginSec_maskLoginErrors') ? 1 : 0,
							'title' => __('Don\'t let WordPress reveal valid users in login errors', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_MASK_LOGIN_ERRORS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'loginSec_blockAdminReg',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('loginSec_blockAdminReg') ? 1 : 0,
							'title' => __('Prevent users registering \'admin\' username if it doesn\'t exist', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_PREVENT_ADMIN_REGISTRATION),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'loginSec_disableAuthorScan',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('loginSec_disableAuthorScan') ? 1 : 0,
							'title' => __('Prevent discovery of usernames through \'/?author=N\' scans, the oEmbed API, and the WordPress REST API', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_PREVENT_AUTHOR_SCAN),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'other_blockBadPOST',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('other_blockBadPOST') ? 1 : 0,
							'title' => __('Block IPs who send POST requests with blank User-Agent and Referer', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_BLOCK_BAD_POST),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'other_pwStrengthOnUpdate',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('other_pwStrengthOnUpdate') ? 1 : 0,
							'title' => __('Check password strength on profile update', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_CHECK_PASSWORD),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'other_WFNet',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('other_WFNet') ? 1 : 0,
							'title' => __('Participate in the Real-Time WordPress Security Network', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_PARTICIPATE_WFSN),
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end brute force protection -->