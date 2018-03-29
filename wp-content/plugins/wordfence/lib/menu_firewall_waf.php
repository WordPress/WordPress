<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
$waf = wfWAF::getInstance();
$d = new wfDashboard(); unset($d->countriesNetwork);
$firewall = new wfFirewall();
$config = $waf->getStorageEngine();
$wafConfigURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#configureAutoPrepend');
$wafRemoveURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#removeAutoPrepend');
/** @var array $wafData */
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block wf-active">
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('waf/firewall-status', array(
							'firewall' => $firewall,
							'dashboard' => $d,
						))->render();
						?>
					</li>
					<li>
						<ul class="wf-block-list wf-block-list-horizontal wf-block-list-nowrap wf-waf-coverage">
							<li>
								<?php
								if (function_exists('network_admin_url') && is_multisite()) { $optionsURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options'); }
								else { $optionsURL = admin_url('admin.php?page=WordfenceWAF&subpage=waf_options'); }
								echo wfView::create('common/status-detail', array(
									'id' => 'waf-coverage',
									'percentage' => $firewall->wafStatus(),
									'activeColor' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? '#ececec' : null /* automatic */),
									'title' => __('Web Application Firewall', 'wordfence'),
									'subtitle' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? __('Currently in Learning Mode', 'wordfence') : __('Stops Complex Attacks', 'wordfence')),
									'link' => $optionsURL,
									'linkLabel' => __('Manage WAF', 'wordfence'),
									'statusTitle' => __('Web Application Firewall Status', 'wordfence'),
									'statusList' => $firewall->wafStatusList(),
									'statusExtra' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? wfView::create('waf/status-tooltip-learning-mode')->render() : ''),
									'helpLink' => __('https://www.wordfence.com/help/firewall/#firewall-status', 'wordfence'),
								))->render();
								?>
							</li>
							<li>
								<?php
								echo wfView::create('common/status-detail', array(
									'id' => 'waf-rules',
									'percentage' => $firewall->ruleStatus(),
									'activeColor' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? '#ececec' : null /* automatic */),
									'title' => __('Firewall Rules: ', 'wordfence') . ($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM ? __('Premium', 'wordfence') : __('Community', 'wordfence')),
									'subtitle' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? __('Currently in Learning Mode', 'wordfence') : ($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM ? __('Rules updated in real-time', 'wordfence') : __('Rule updates delayed by 30 days', 'wordfence'))),
									'link' => ($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM ? $optionsURL . '#waf-options-advanced' : 'https://www.wordfence.com/gnl1wafUpgrade/wordfence-signup/'),
									'linkLabel' => ($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM ? __('Manage Firewall Rules', 'wordfence') : __('Upgrade to Premium', 'wordfence')),
									'linkNewWindow' => ($firewall->ruleMode() != wfFirewall::RULE_MODE_PREMIUM),
									'statusTitle' => __('Firewall Rules Status', 'wordfence'),
									'statusList' => $firewall->wafStatusList('rules'),
									'statusExtra' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? wfView::create('waf/status-tooltip-learning-mode')->render() : ''),
									'helpLink' => __('https://www.wordfence.com/help/firewall/#firewall-status', 'wordfence'),
								))->render();
								?>
							</li>
							<li>
								<?php
								echo wfView::create('common/status-detail', array(
									'id' => 'waf-blacklist',
									'percentage' => $firewall->blacklistStatus(),
									'title' => __('Real-Time IP Blacklist: ', 'wordfence') . ($firewall->blacklistMode() == wfFirewall::BLACKLIST_MODE_ENABLED ? __('Enabled', 'wordfence') : __('Disabled', 'wordfence')),
									'subtitle' => __('Blocks requests from known malicious IPs', 'wordfence'),
									'link' => (($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM) ? network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options') : 'https://www.wordfence.com/gnl1wafUpgrade/wordfence-signup/'),
									'linkLabel' => $firewall->firewallMode() == wfFirewall::FIREWALL_MODE_DISABLED ? null : ($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM ? ($firewall->blacklistMode() == wfFirewall::BLACKLIST_MODE_ENABLED ? __('Manage Real-Time IP Blacklist', 'wordfence') : ($firewall->isSubDirectoryInstallation() ? null : __('Enable', 'wordfence'))) : __('Upgrade to Premium', 'wordfence')),
									'linkNewWindow' => ($firewall->ruleMode() != wfFirewall::RULE_MODE_PREMIUM),
									'statusTitle' => __('Blacklist Status', 'wordfence'),
									'statusList' => $firewall->wafStatusList('blacklist'),
									'helpLink' => __('https://www.wordfence.com/help/firewall/#firewall-status', 'wordfence'),
								))->render();
								
								if ($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM && $firewall->blacklistMode() == wfFirewall::BLACKLIST_MODE_DISABLED):
								?>
								<script type="application/javascript">
									(function($) {
										$(function() {
											$('#waf-blacklist a').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.setOption('disableWAFBlacklistBlocking', 0, function() {
													window.location.reload(true);
												});
											});
										});
									})(jQuery);
								</script>
								<?php endif; ?>
							</li>
							<li>
								<?php
								echo wfView::create('common/status-detail', array(
									'id' => 'waf-brute',
									'percentage' => $firewall->bruteForceStatus(),
									'title' => __('Brute Force Protection', 'wordfence') . ($firewall->bruteForceStatus() == 0 ? __(': Disabled', 'wordfence') : ''),
									'subtitle' => __('Stops Password Guessing Attacks', 'wordfence'),
									'link' => network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#waf-options-bruteforce'),
									'linkLabel' => __('Manage Brute Force Protection', 'wordfence'),
									'statusTitle' => __('Brute Force Protection Status', 'wordfence'),
									'statusList' => $firewall->bruteForceStatusList(),
									'helpLink' => __('https://www.wordfence.com/help/firewall/#firewall-status', 'wordfence'),
								))->render();
								?>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block wf-active">
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<ul class="wf-block-list wf-block-list-horizontal wf-waf-navigation">
							<li>
								<?php
								echo wfView::create('common/block-navigation-option', array(
									'id' => 'waf-option-rate-limiting',
									'img' => 'ratelimiting.svg',
									'title' => __('Rate Limiting', 'wordfence'),
									'subtitle' => __('Block crawlers that are using too many resources or stealing content', 'wordfence'),
									'link' => network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#waf-options-ratelimiting'),
								))->render();
								?>
							</li>
							<li>
								<?php
								echo wfView::create('common/block-navigation-option', array(
									'id' => 'waf-option-blocking',
									'img' => 'blocking.svg',
									'title' => __('Blocking', 'wordfence'),
									'subtitle' => __('Block traffic by country, IP, IP range, user agent, referrer, or hostname', 'wordfence'),
									'link' => '#top#blocking',
								))->render();
								?>
							</li>
						</ul>
					</li>
					<li>
						<ul class="wf-block-list wf-block-list-horizontal wf-waf-navigation">
							<li>
								<?php
								echo wfView::create('common/block-navigation-option', array(
									'id' => 'waf-option-support',
									'img' => 'support.svg',
									'title' => __('Help', 'wordfence'),
									'subtitle' => __('Find the documentation and help you need', 'wordfence'),
									'link' => network_admin_url('admin.php?page=WordfenceSupport'),
								))->render();
								?>
							</li>
							<li>
								<?php
								echo wfView::create('common/block-navigation-option', array(
									'id' => 'waf-option-all-options',
									'img' => 'options.svg',
									'title' => __('All Firewall Options', 'wordfence'),
									'subtitle' => __('Manage global and advanced firewall options', 'wordfence'),
									'link' => network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options'),
								))->render();
								?>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="wf-row">
	<div class="wf-col-xs-12 wf-col-sm-6 wf-col-sm-half-padding-right">
		<!-- begin top ips blocked -->
		<?php include(dirname(__FILE__) . '/dashboard/widget_ips.php'); ?>
		<!-- end top ips blocked -->
		<!-- begin countries blocked -->
		<?php include(dirname(__FILE__) . '/dashboard/widget_countries.php'); ?>
		<!-- end countries blocked -->
	</div> <!-- end content block -->
	<div class="wf-col-xs-12 wf-col-sm-6 wf-col-sm-half-padding-left">
		<!-- begin firewall summary site -->
		<?php include(dirname(__FILE__) . '/dashboard/widget_localattacks.php'); ?>
		<!-- end firewall summary site -->
		<!-- begin total attacks blocked network -->
		<?php include(dirname(__FILE__) . '/dashboard/widget_networkattacks.php'); ?>
		<!-- end total attacks blocked network -->
		<!-- begin recent logins -->
		<?php include(dirname(__FILE__) . '/dashboard/widget_logins.php'); ?>
		<!-- end recent logins -->
	</div> <!-- end content block -->
</div> <!-- end row -->
<?php if (wfOnboardingController::willShowNewTour(wfOnboardingController::TOUR_FIREWALL)): ?>
	<script type="application/javascript">
		(function($) {
			$(function() {
				WFAD.setUpFirewallTour = function() {
					WFAD.tour1 = function () {
						WFAD.tour('wfWAFNewTour1', 'wf-section-firewall', 'top', 'left', null, WFAD.tour2);
					};
					WFAD.tour2 = function () {
						WFAD.tour('wfWAFNewTour2', 'waf-coverage', 'top', 'left', WFAD.tour1, WFAD.tour3);
					};
					WFAD.tour3 = function () {
						WFAD.tour('wfWAFNewTour3', 'waf-brute', 'right', 'right', WFAD.tour2, WFAD.tour4);
					};
					WFAD.tour4 = function () {
						WFAD.tour('wfWAFNewTour4', 'waf-option-all-options', 'right', 'right', WFAD.tour3, WFAD.tourComplete);
					};
					WFAD.tourComplete = function () {
						WFAD.tourFinish('<?php echo esc_attr(wfOnboardingController::TOUR_FIREWALL); ?>');
					};
				};

				WFAD.wafTourShown = false;
				<?php if (wfOnboardingController::shouldShowNewTour(wfOnboardingController::TOUR_FIREWALL)): ?>
				$(window).on('wfTabChange', function(e, tab) {
					if (tab == 'waf' && !WFAD.wafTourShown) {
						WFAD.wafTourShown = true;
						WFAD.setUpFirewallTour();
						if (!WFAD.isSmallScreen) { WFAD.tour1(); }
					}
				});
				
				if ($('#waf').hasClass('wf-active')) {
					WFAD.wafTourShown = true;
					WFAD.setUpFirewallTour();
					if (!WFAD.isSmallScreen) { WFAD.tour1(); }
				}
				<?php endif; ?>
			});
		})(jQuery);
	</script>

	<script type="text/x-jquery-template" id="wfWAFNewTour1">
		<div>
			<h3><?php _e('The Wordfence firewall protects your sites from attackers', 'wordfence'); ?></h3>
			<p><?php _e('This is where you can monitor the work Wordfence is doing to protect your site and also where you can manage the options to optimize the firewall\'s configuration.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
					<li class="wf-active">&bullet;</li>
					<li>&bullet;</li>
					<li>&bullet;</li>
					<li>&bullet;</li>
				</ul>
				<div id="wf-tour-continue"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Next', 'wordfence'); ?></a></div>
			</div>
			<div id="wf-tour-close"><a href="#"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</script>
	<script type="text/x-jquery-template" id="wfWAFNewTour2">
		<div>
			<h3><?php _e('Web Application Firewall (WAF)', 'wordfence'); ?></h3>
			<p><?php _e('The Wordfence Web Application Firewall blocks known and emerging attacks using firewall rules. When you first install the WAF, it will be in learning mode. This allows Wordfence to learn about your site so that we can understand how to protect it and how to allow normal visitors through the firewall. We recommend you let Wordfence learn for a week before you enable the firewall.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
					<li>&bullet;</li>
					<li class="wf-active">&bullet;</li>
					<li>&bullet;</li>
					<li>&bullet;</li>
				</ul>
				<div id="wf-tour-previous"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-default"><?php _e('Previous', 'wordfence'); ?></a></div>
				<div id="wf-tour-continue"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Next', 'wordfence'); ?></a></div>
			</div>
			<div id="wf-tour-close"><a href="#"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</script>
	<script type="text/x-jquery-template" id="wfWAFNewTour3">
		<div>
			<h3><?php _e('Brute Force Protection', 'wordfence'); ?></h3>
			<p><?php _e('Wordfence protects your site from password-guessing attacks by locking out attackers and helping you avoid weak passwords.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
					<li>&bullet;</li>
					<li>&bullet;</li>
					<li class="wf-active">&bullet;</li>
					<li>&bullet;</li>
				</ul>
				<div id="wf-tour-previous"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-default"><?php _e('Previous', 'wordfence'); ?></a></div>
				<div id="wf-tour-continue"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Next', 'wordfence'); ?></a></div>
			</div>
			<div id="wf-tour-close"><a href="#"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</script>
	<script type="text/x-jquery-template" id="wfWAFNewTour4">
		<div>
			<h3><?php _e('Firewall Options', 'wordfence'); ?></h3>
			<p class="wf-center"><svg viewBox="0 0 100.11 100.11" class="wf-icon"><path d="M99.59,41.42a2.06,2.06,0,0,0-1.37-.82L86.3,38.78a39.34,39.34,0,0,0-2.67-6.39q1.17-1.63,3.52-4.6t3.32-4.33A2.52,2.52,0,0,0,91,22a2.1,2.1,0,0,0-.46-1.43Q88.18,17.2,79.78,9.45a2.52,2.52,0,0,0-1.63-.65,2.12,2.12,0,0,0-1.57.59l-9.25,7a40.09,40.09,0,0,0-5.87-2.41L59.64,2a1.92,1.92,0,0,0-.75-1.4A2.46,2.46,0,0,0,57.29,0H42.82a2.19,2.19,0,0,0-2.34,1.82,106,106,0,0,0-1.89,12.12,37.62,37.62,0,0,0-5.93,2.48l-9-7A2.78,2.78,0,0,0,22,8.8q-1.44,0-6.16,4.66a64.88,64.88,0,0,0-6.42,7A2.75,2.75,0,0,0,8.8,22a2.44,2.44,0,0,0,.65,1.56q4.37,5.28,7,9a32.38,32.38,0,0,0-2.54,6L1.76,40.34a2,2,0,0,0-1.24.85A2.5,2.5,0,0,0,0,42.69V57.16a2.44,2.44,0,0,0,.52,1.53,2,2,0,0,0,1.37.82l11.93,1.76a31.91,31.91,0,0,0,2.67,6.45Q15.31,69.35,13,72.31T9.65,76.65a2.54,2.54,0,0,0-.07,3q2.54,3.52,10.75,11a2.25,2.25,0,0,0,1.63.71,2.35,2.35,0,0,0,1.63-.59l9.19-7a40.54,40.54,0,0,0,5.87,2.41l1.82,12a1.92,1.92,0,0,0,.75,1.4,2.45,2.45,0,0,0,1.6.55H57.29a2.2,2.2,0,0,0,2.35-1.82,107.41,107.41,0,0,0,1.89-12.12,37.19,37.19,0,0,0,5.93-2.48l9,7a3.18,3.18,0,0,0,1.69.59q1.43,0,6.13-4.62a65.86,65.86,0,0,0,6.45-7,2.16,2.16,0,0,0,.59-1.5,2.51,2.51,0,0,0-.65-1.63q-4.69-5.74-7-9a41.57,41.57,0,0,0,2.54-5.93l12.06-1.82a2,2,0,0,0,1.3-.85,2.52,2.52,0,0,0,.52-1.5V43a2.46,2.46,0,0,0-.52-1.53ZM61.85,61.86a16.08,16.08,0,0,1-11.8,4.89A16.69,16.69,0,0,1,33.37,50.06,16.69,16.69,0,0,1,50.06,33.37,16.69,16.69,0,0,1,66.74,50.06a16.08,16.08,0,0,1-4.89,11.8Zm0,0"></path></svg></p>
			<p><?php _e('Set up the way you want the firewall to protect your site including the web application firewall, brute force protection, rate limiting, and blocking.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
					<li>&bullet;</li>
					<li>&bullet;</li>
					<li>&bullet;</li>
					<li class="wf-active">&bullet;</li>
				</ul>
				<div id="wf-tour-previous"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-default"><?php _e('Previous', 'wordfence'); ?></a></div>
				<div id="wf-tour-continue"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Got it', 'wordfence'); ?></a></div>
			</div>
			<div id="wf-tour-close"><a href="#"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</script>
<?php endif; ?>

<?php if (wfOnboardingController::willShowUpgradeTour(wfOnboardingController::TOUR_FIREWALL)): ?>
	<script type="application/javascript">
		(function($) {
			$(function() {
				WFAD.setUpFirewallTour = function() {
					WFAD.tour1 = function() {
						WFAD.tour('wfWAFUpgradeTour1', 'waf-option-all-options', 'right', 'right', null, WFAD.tourComplete);
					};
					WFAD.tourComplete = function() { WFAD.tourFinish('<?php echo esc_attr(wfOnboardingController::TOUR_FIREWALL); ?>'); };
				};

				WFAD.wafTourShown = false;
				<?php if (wfOnboardingController::shouldShowUpgradeTour(wfOnboardingController::TOUR_FIREWALL)): ?>
				$(window).on('wfTabChange', function(e, tab) {
					if (tab == 'waf' && !WFAD.wafTourShown) {
						WFAD.wafTourShown = true;
						WFAD.setUpFirewallTour();
						if (!WFAD.isSmallScreen) { WFAD.tour1(); }
					}
				});

				if ($('#waf').hasClass('wf-active')) {
					WFAD.wafTourShown = true;
					WFAD.setUpFirewallTour();
					if (!WFAD.isSmallScreen) { WFAD.tour1(); }
				}
				<?php endif; ?>
			});
		})(jQuery);
	</script>

	<script type="text/x-jquery-template" id="wfWAFUpgradeTour1">
		<div>
			<h3><?php _e('Firewall Options', 'wordfence'); ?></h3>
			<p class="wf-center"><svg viewBox="0 0 100.11 100.11" class="wf-icon"><path d="M99.59,41.42a2.06,2.06,0,0,0-1.37-.82L86.3,38.78a39.34,39.34,0,0,0-2.67-6.39q1.17-1.63,3.52-4.6t3.32-4.33A2.52,2.52,0,0,0,91,22a2.1,2.1,0,0,0-.46-1.43Q88.18,17.2,79.78,9.45a2.52,2.52,0,0,0-1.63-.65,2.12,2.12,0,0,0-1.57.59l-9.25,7a40.09,40.09,0,0,0-5.87-2.41L59.64,2a1.92,1.92,0,0,0-.75-1.4A2.46,2.46,0,0,0,57.29,0H42.82a2.19,2.19,0,0,0-2.34,1.82,106,106,0,0,0-1.89,12.12,37.62,37.62,0,0,0-5.93,2.48l-9-7A2.78,2.78,0,0,0,22,8.8q-1.44,0-6.16,4.66a64.88,64.88,0,0,0-6.42,7A2.75,2.75,0,0,0,8.8,22a2.44,2.44,0,0,0,.65,1.56q4.37,5.28,7,9a32.38,32.38,0,0,0-2.54,6L1.76,40.34a2,2,0,0,0-1.24.85A2.5,2.5,0,0,0,0,42.69V57.16a2.44,2.44,0,0,0,.52,1.53,2,2,0,0,0,1.37.82l11.93,1.76a31.91,31.91,0,0,0,2.67,6.45Q15.31,69.35,13,72.31T9.65,76.65a2.54,2.54,0,0,0-.07,3q2.54,3.52,10.75,11a2.25,2.25,0,0,0,1.63.71,2.35,2.35,0,0,0,1.63-.59l9.19-7a40.54,40.54,0,0,0,5.87,2.41l1.82,12a1.92,1.92,0,0,0,.75,1.4,2.45,2.45,0,0,0,1.6.55H57.29a2.2,2.2,0,0,0,2.35-1.82,107.41,107.41,0,0,0,1.89-12.12,37.19,37.19,0,0,0,5.93-2.48l9,7a3.18,3.18,0,0,0,1.69.59q1.43,0,6.13-4.62a65.86,65.86,0,0,0,6.45-7,2.16,2.16,0,0,0,.59-1.5,2.51,2.51,0,0,0-.65-1.63q-4.69-5.74-7-9a41.57,41.57,0,0,0,2.54-5.93l12.06-1.82a2,2,0,0,0,1.3-.85,2.52,2.52,0,0,0,.52-1.5V43a2.46,2.46,0,0,0-.52-1.53ZM61.85,61.86a16.08,16.08,0,0,1-11.8,4.89A16.69,16.69,0,0,1,33.37,50.06,16.69,16.69,0,0,1,50.06,33.37,16.69,16.69,0,0,1,66.74,50.06a16.08,16.08,0,0,1-4.89,11.8Zm0,0"></path></svg></p>
			<p><?php _e('All of the Firewall settings are now located here. This includes configuration options for the web application firewall, brute force protection, rate limiting, whitelisted URLs, and blocking.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
					<li class="wf-active">&bullet;</li>
				</ul>
				<div id="wf-tour-continue"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Got it', 'wordfence'); ?></a></div>
			</div>
			<div id="wf-tour-close"><a href="#"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</script>
<?php endif; ?>
