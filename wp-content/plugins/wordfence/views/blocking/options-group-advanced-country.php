<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Advanced Country Blocking Options group.
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
						<strong><?php _e('Advanced Country Blocking Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<?php if (wfConfig::get('isPaid')): ?>
					<ul class="wf-block-list">
						<li>
							<?php
							echo wfView::create('options/option-select', array(
								'selectOptionName' => 'cbl_action',
								'selectOptions' => array(
									array('value' => 'block', 'label' => 'Show the standard Wordfence blocked message'),
									array('value' => 'redir', 'label' => 'Redirect to the URL below'),
								),
								'selectValue' => wfConfig::get('cbl_action'),
								'title' => __('What to do when we block someone', 'wordfence'),
								'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_BLOCKING_OPTION_WHAT_TO_DO),
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('options/option-text', array(
								'textOptionName' => 'cbl_redirURL',
								'textValue' => wfConfig::get('cbl_redirURL'),
								'title' => __('URL to redirect blocked users to', 'wordfence'),
								'placeholder' => __('Enter a full URL (e.g., http://example.com/blocked/)', 'wordfence'),
								'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_BLOCKING_OPTION_REDIRECT),
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('options/option-toggled', array(
								'optionName' => 'cbl_loggedInBlocked',
								'enabledValue' => 1,
								'disabledValue' => 0,
								'value' => wfConfig::get('cbl_loggedInBlocked') ? 1 : 0,
								'title' => __('Block countries even if they are logged in', 'wordfence'),
								'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_BLOCKING_OPTION_BLOCK_LOGGED_IN),
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('blocking/option-bypass-redirect', array(
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('blocking/option-bypass-cookie', array(
							))->render();
							?>
						</li>
					</ul>
				<?php else: ?>
					<ul class="wf-flex-vertical wf-padding-add-right-large wf-padding-add-bottom-large">
						<li><h3><?php _e('Put Geographic Protection In Place With Country Blocking', 'wordfence'); ?></h3></li>
						<li><p class="wf-no-top"><?php _e('Wordfence country blocking is designed to stop an attack, prevent content theft, or end malicious activity that originates from a geographic region in less than 1/300,000th of a second. Blocking countries who are regularly creating failed logins, a large number of page not found errors, and are clearly engaged in malicious activity is an effective way to protect your site during an attack.', 'wordfence'); ?></p></li>
						<li><?php echo wfView::create('blocking/country-block-map')->render(); ?></li>
						<li><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="https://www.wordfence.com/gnl1countryBlockUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div> <!-- end country blocking -->