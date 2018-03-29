<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the General Options group.
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
						<strong><?php _e('General Wordfence Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						$subtitle = __('Automatically updates Wordfence to the newest version within 24 hours of a new release.', 'wordfence');
						if (!wfConfig::get('other_bypassLitespeedNoabort', false) && getenv('noabort') != '1' && stristr($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false) {
							$subtitle .= '<br><br>';
							$subtitle .= __('<span class="wf-red-dark">Warning:</span> You are running the LiteSpeed web server and Wordfence can\'t determine whether "noabort" is set. Please verify that the environmental variable "noabort" is set for the local site, or the server\'s global External Application Abort is set to "No Abort".', 'wordfence');
							$subtitle .= '<br>';
							$subtitle .= '<a href="' . wfSupportController::esc_supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_LITESPEED_WARNING) . '" target="_blank" rel="noopener noreferrer">' . __('Please read this article in our FAQ to make an important change that will ensure your site stability during an update.', 'wordfence') . '</a>';
						}
						
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'autoUpdate',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('autoUpdate') ? 1 : 0,
							'title' => __('Update Wordfence automatically when a new version is released?', 'wordfence'),
							'subtitleHTML' => $subtitle,
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_AUTOMATIC_UPDATE),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-text', array(
							'textOptionName' => 'alertEmails',
							'textValue' => wfConfig::get('alertEmails'),
							'title' => __('Where to email alerts', 'wordfence'),
							'placeholder' => __('Separate multiple addresses with commas', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_ALERT_EMAILS),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('dashboard/option-howgetips')->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'other_hideWPVersion',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('other_hideWPVersion') ? 1 : 0,
							'title' => __('Hide WordPress version', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_HIDE_VERSION),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'disableCodeExecutionUploads',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('disableCodeExecutionUploads') ? 1 : 0,
							'title' => __('Disable Code Execution for Uploads directory', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_DISABLE_UPLOADS_EXECUTION),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'disableCookies',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('disableCookies') ? 1 : 0,
							'title' => __('Disable Wordfence Cookies', 'wordfence'),
							'subtitle' => __('When enabled, all visits in live traffic will appear to be new visits.', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_DISABLE_COOKIES),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'liveActivityPauseEnabled',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('liveActivityPauseEnabled') ? 1 : 0,
							'title' => __('Pause live updates when window loses focus', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_PAUSE_LIVE_UPDATES),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-text', array(
							'textOptionName' => 'actUpdateInterval',
							'textValue' => wfConfig::get('actUpdateInterval'),
							'title' => __('Update interval in seconds', 'wordfence'),
							'subtitle' => __('Setting higher will reduce browser traffic but slow scan starts, live traffic &amp; status updates.', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_UPDATE_INTERVAL),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'other_bypassLitespeedNoabort',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('other_bypassLitespeedNoabort') ? 1 : 0,
							'title' => __('Bypass the LiteSpeed "noabort" check', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_BYPASS_LITESPEED_CHECK),
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-toggled', array(
							'optionName' => 'deleteTablesOnDeact',
							'enabledValue' => 1,
							'disabledValue' => 0,
							'value' => wfConfig::get('deleteTablesOnDeact') ? 1 : 0,
							'title' => __('Delete Wordfence tables and data on deactivation', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_DELETE_DEACTIVATION),
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end general options -->