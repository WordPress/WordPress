<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
$dashboardURL = network_admin_url('admin.php?page=Wordfence');
$firewall = new wfFirewall();
$scanner = wfScanner::shared();
$d = new wfDashboard();
?>
<script type="application/javascript">
	(function($) {
		$(function() {
			document.title = "<?php esc_attr_e('Wordfence Global Options', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;

			//Hash-based option block linking
			if (window.location.hash) {
				var hashes = window.location.hash.split('#');
				var hash = hashes[hashes.length - 1];
				var block = $('.wf-block[data-persistence-key="' + hash + '"]');
				if (block) {
					if (!block.hasClass('wf-active')) {
						block.find('.wf-block-content').slideDown({
							always: function() {
								block.addClass('wf-active');
								$('html, body').animate({
									scrollTop: block.offset().top - 100
								}, 1000);
							}
						});

						WFAD.ajax('wordfence_saveDisclosureState', {name: block.data('persistenceKey'), state: true}, function() {});
					}
					else {
						$('html, body').animate({
							scrollTop: block.offset().top - 100
						}, 1000);
					}
					history.replaceState('', document.title, window.location.pathname + window.location.search);
				}
			}
		});
	})(jQuery);
</script>
<div class="wf-options-controls">
	<div class="wf-row">
		<div class="wf-col-xs-12">
			<?php
			echo wfView::create('options/block-controls', array(
				'backLink' => $dashboardURL,
				'backLabelHTML' => __('Back<span class="wf-hidden-xs"> to Dashboard</span>', 'wordfence'),
				'restoreDefaultsSection' => wfConfig::OPTIONS_TYPE_GLOBAL,
				'restoreDefaultsMessage' => __('Are you sure you want to restore the default global settings? This will undo any custom changes you have made to the options on this page. Your configured license key and alert emails will not be changed.', 'wordfence'),
			))->render();
			?>
		</div>
	</div>
</div>
<div class="wf-options-controls-spacer"></div>
<?php
if (wfOnboardingController::shouldShowAttempt3()) {
	echo wfView::create('onboarding/banner')->render();
}
?>
<div class="wrap wordfence" id="wf-global-options">
	<div class="wf-container-fluid">
		<div class="wf-row">
			<div class="wf-col-xs-12">
				<div class="wp-header-end"></div>
			</div>
		</div>
		<div class="wf-row">
			<div class="<?php echo wfStyle::contentClasses(); ?>">
				<div id="waf-options" class="wf-fixed-tab-content">
					<?php
					echo wfView::create('common/section-title', array(
						'title' => __('Wordfence Global Options', 'wordfence'),
						'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_DASHBOARD_OPTIONS),
						'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about Global Options</span>', 'wordfence'),
						'showIcon' => true,
					))->render();
					?>
					<div class="wf-row">
						<div class="wf-col-xs-12">
							<div class="wf-block wf-active">
								<div class="wf-block-content">
									<ul class="wf-block-list">
										<li>
											<ul class="wf-block-list wf-block-list-horizontal wf-block-list-nowrap wf-waf-coverage">
												<li>
													<?php
													echo wfView::create('common/status-detail', array(
														'id' => 'waf-coverage',
														'percentage' => $firewall->overallStatus(),
														'activeColor' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? '#ececec' : null /* automatic */),
														'title' => __('Firewall', 'wordfence'),
														'subtitle' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? __('WAF Currently in Learning Mode', 'wordfence') : __('Protection from known and emerging threats', 'wordfence')),
														'link' => wfPage::pageURL(wfPage::PAGE_FIREWALL_OPTIONS, wfPage::PAGE_DASHBOARD_OPTIONS),
														'linkLabel' => __('Manage Firewall', 'wordfence'),
														'statusTitle' => __('Firewall Status', 'wordfence'),
														'statusList' => $firewall->statusList(),
														'statusExtra' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? wfView::create('waf/status-tooltip-learning-mode')->render() : ''),
														'helpLink' => __('https://www.wordfence.com/help/dashboard/#dashboard-status', 'wordfence'),
													))->render();
													?>
												</li>
												<li>
													<?php
													echo wfView::create('common/status-detail', array(
														'id' => 'wf-scanner-type',
														'percentage' => $scanner->scanTypeStatus(),
														'activeColor' => (!$scanner->isEnabled() ? '#ececec' : null /* automatic */),
														'title' => __('Scan', 'wordfence'),
														'subtitle' => __('Detection of security issues', 'wordfence'),
														'link' => wfPage::pageURL(wfPage::PAGE_SCAN_OPTIONS, wfPage::PAGE_DASHBOARD_OPTIONS),
														'linkLabel' => __('Manage Scan', 'wordfence'),
														'statusTitle' => __('Scan Status', 'wordfence'),
														'statusList' => $scanner->scanTypeStatusList(),
														'helpLink' => __('https://www.wordfence.com/help/dashboard/#dashboard-status', 'wordfence'),
													))->render();
													?>
												</li>
												<li>
													<?php if (wfConfig::get('hasKeyConflict')): ?>
														<?php
														echo wfView::create('common/status-critical', array(
															'id' => 'wf-premium-alert',
															'title' => __('Premium License Conflict', 'wordfence'),
															'subtitle' => __('License already in use', 'wordfence'),
															'link' => 'https://www.wordfence.com/gnl1manageConflict/manage-wordfence-api-keys/',
															'linkLabel' => __('Reset License', 'wordfence'),
															'linkNewWindow' => true,
														))->render();
														?>
													<?php elseif (wfConfig::get('keyType') == wfAPI::KEY_TYPE_PAID_EXPIRED): ?>
														<?php
														echo wfView::create('common/status-critical', array(
															'id' => 'wf-premium-alert',
															'title' => __('Premium Protection Disabled', 'wordfence'),
															'subtitle' => __('License is expired', 'wordfence'),
															'link' => 'https://www.wordfence.com/gnl1renewExpired/manage-wordfence-api-keys/',
															'linkLabel' => __('Renew License', 'wordfence'),
															'linkNewWindow' => true,
														))->render();
														?>
													<?php elseif (wfConfig::get('keyType') == wfAPI::KEY_TYPE_FREE || wfConfig::get('keyType') === false): ?>
														<div>
															<p><h3><?php _e('Premium Protection Disabled', 'wordfence'); ?></h3></p>
															<p><?php printf(__('As a free Wordfence user, you are currently using the Community version of the Threat Defense Feed. Premium users are protected by an additional %d firewall rules and malware signatures. Upgrade to Premium today to improve your protection.', 'wordfence'), ($d->tdfPremium - $d->tdfCommunity)); ?></p>
															<p><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="https://www.wordfence.com/gnl1dashboardUpgrade/wordfence-signup/#premium-order-form" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a>&nbsp;&nbsp;<a class="wf-btn wf-btn-callout-subtle wf-btn-default" href="https://www.wordfence.com/gnl1dashboardLearn/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></p>
														</div>
													<?php elseif (wfConfig::get('keyExpDays') < 30 && (wfConfig::get('premiumAutoRenew', null) === '0' || wfConfig::get('premiumAutoRenew', null) === 0)): ?>
														<?php
														echo wfView::create('common/status-critical', array(
															'id' => 'wf-premium-alert',
															'title' => __('Premium License Expiring', 'wordfence'),
															'subtitle' => __('Auto-renew is disabled', 'wordfence'),
															'link' => 'https://www.wordfence.com/gnl1renewExpiring/manage-wordfence-api-keys/',
															'linkLabel' => __('Renew License', 'wordfence'),
															'linkNewWindow' => true,
														))->render();
														?>
													<?php elseif (wfConfig::get('keyExpDays') < 30): ?>
														<?php
														if (wfConfig::get('premiumPaymentExpiring')) {
															$title = __('Payment Method Expiring', 'wordfence');
														}
														else if (wfConfig::get('premiumPaymentExpired')) {
															$title = __('Payment Method Expired', 'wordfence');
														}
														else if (wfConfig::get('premiumPaymentMissing')) {
															$title = __('Payment Method Missing', 'wordfence');
														}
														else if (wfConfig::get('premiumPaymentHold')) {
															$title = __('Payment Method Invalid', 'wordfence');
														}
														
														if (isset($title)) {
															$days = floor(((int) wfConfig::get('premiumNextRenew') - time()) / 86400);
															if ($days <= 0) {
																$days = __('today', 'wordfence');
															}
															else if ($days == 1) {
																$days = __('tomorrow', 'wordfence');
															}
															else {
																$days = sprintf(__('in %d days', 'wordfence'), $days);
															}
															
															echo wfView::create('dashboard/status-payment-expiring', array(
																'id' => 'wf-premium-alert',
																'title' => $title,
																'subtitle' => sprintf(__('License renews %s', 'wordfence'), $days),
																'link' => 'https://www.wordfence.com/gnl1renewExpiring/manage-wordfence-api-keys/',
																'linkLabel' => __('Update Payment Method', 'wordfence'),
																'linkNewWindow' => true,
															))->render();
														}
														else {
															$days = floor(((int) wfConfig::get('premiumNextRenew') - time()) / 86400);
															if ($days == 0) {
																$days = __('today', 'wordfence');
															}
															else if ($days == 1) {
																$days = __('in 1 day', 'wordfence');
															}
															else {
																$days = sprintf(__('in %d days', 'wordfence'), $days);
															}
															
															echo wfView::create('dashboard/status-renewing', array(
																'id' => 'wf-premium-alert',
																'title' => __('Premium License Expiring', 'wordfence'),
																'subtitle' => sprintf(__('License renews %s', 'wordfence'), $days),
																'link' => 'https://www.wordfence.com/gnl1reviewExpiring/manage-wordfence-api-keys/',
																'linkLabel' => __('Review Payment Method', 'wordfence'),
																'linkNewWindow' => true,
															))->render();
														}
														?>
													<?php elseif (wfConfig::get('keyType') == wfAPI::KEY_TYPE_PAID_CURRENT): ?>
														<div class="wf-block-labeled-value wf-protection-status wf-protection-status-<?php echo esc_attr($firewall->ruleMode()); ?>">
															<div class="wf-block-labeled-value-value"><i class="wf-fa wf-fa-check" aria-hidden="true"></i></div>
															<div class="wf-block-labeled-value-label"><?php _e('Wordfence Premium Enabled', 'wordfence'); ?></div>
														</div>
													<?php endif; ?>
												</li>
											</ul>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<?php
					echo wfView::create('dashboard/options-group-license', array(
						'stateKey' => 'global-options-license',
					))->render();
					
					echo wfView::create('dashboard/options-group-view-customization', array(
						'stateKey' => 'global-options-view-customization',
					))->render();
					
					echo wfView::create('dashboard/options-group-general', array(
						'stateKey' => 'global-options-general',
					))->render();
					
					echo wfView::create('dashboard/options-group-dashboard', array(
						'stateKey' => 'global-options-dashboard',
					))->render();
					
					echo wfView::create('dashboard/options-group-alert', array(
						'stateKey' => 'global-options-alert',
					))->render();
					
					echo wfView::create('dashboard/options-group-email-summary', array(
						'stateKey' => 'global-options-email-summary',
					))->render();
					
					echo wfView::create('dashboard/options-group-import', array(
						'stateKey' => 'global-options-import',
					))->render();
					?>
					
					
					
					
				</div> <!-- end options block -->
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>
