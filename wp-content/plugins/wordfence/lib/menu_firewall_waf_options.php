<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
$waf = wfWAF::getInstance();
$d = new wfDashboard(); unset($d->countriesNetwork);
$firewall = new wfFirewall();
$config = $waf->getStorageEngine();
$wafURL = wfPage::pageURL(wfPage::PAGE_FIREWALL);
$wafConfigURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#configureAutoPrepend');
$wafRemoveURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#removeAutoPrepend');
/** @var array $wafData */

$backPage = new wfPage(wfPage::PAGE_FIREWALL);
if (isset($_GET['source']) && wfPage::isValidPage($_GET['source'])) {
	$backPage = new wfPage($_GET['source']);
}
?>
<script type="application/javascript">
	(function($) {
		WFAD.wafData = <?php echo json_encode($wafData); ?>;
		WFAD.restoreWAFData = JSON.parse(JSON.stringify(WFAD.wafData)); //Copied into wafData when canceling changes

		$(function() {
			document.title = "<?php esc_attr_e('Firewall Options', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;
			
			WFAD.wafConfigPageRender();

			//Hash-based option block linking
			if (window.location.hash) {
				var hashes = window.location.hash.split('#');
				var hash = hashes[hashes.length - 1];
				var block = $('.wf-block[data-persistence-key="' + hash + '"]');
				if (block.length) {
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

			var updatePendingCircles = function() {
				$('#circle-waf-coverage, #circle-waf-rules, #circle-waf-blacklist, #circle-waf-brute').wfCircularProgress({pendingOverlay: Object.keys(WFAD.pendingChanges).length > 0});
			};
			var coalescingUpdateTimer = false;

			$('.wf-option, .wf-rule-toggle').on('change', function() {
				clearTimeout(coalescingUpdateTimer);
				coalescingUpdateTimer = setTimeout(updatePendingCircles, 100);
			});
		});

		$(window).on('wfOptionsReset', function() {
			WFAD.wafData = JSON.parse(JSON.stringify(WFAD.restoreWAFData));
			WFAD.wafConfigPageRender();
		});
	})(jQuery);
</script>
<div class="wf-options-controls">
	<div class="wf-row">
		<div class="wf-col-xs-12">
			<?php
			echo wfView::create('options/block-controls', array(
				'backLink' => $backPage->url(),
				'backLabelHTML' => sprintf(__('<span class="wf-hidden-xs">Back to </span>%s', 'wordfence'), $backPage->label()), 
				'restoreDefaultsSection' => wfConfig::OPTIONS_TYPE_FIREWALL,
				'restoreDefaultsMessage' => __('Are you sure you want to restore the default Firewall settings? This will undo any custom changes you have made to the options on this page. If you have manually disabled any rules or added any custom whitelisted URLs, those changes will not be overwritten.', 'wordfence'),
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
<div class="wrap wordfence">
	<div class="wf-container-fluid">
		<?php
		if (function_exists('network_admin_url') && is_multisite()) {
			$firewallURL = network_admin_url('admin.php?page=WordfenceWAF#top#waf');
			$blockingURL = network_admin_url('admin.php?page=WordfenceWAF#top#blocking');
		}
		else {
			$firewallURL = admin_url('admin.php?page=WordfenceWAF#top#waf');
			$blockingURL = admin_url('admin.php?page=WordfenceWAF#top#blocking');
		}
		?>
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
						'title' => __('Firewall Options', 'wordfence'),
						'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF),
						'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about the Firewall</span>', 'wordfence'),
						'showIcon' => true,
					))->render();
					?>
					<div class="wf-row">
						<div class="wf-col-xs-12">
							<div class="wf-block wf-active">
								<div class="wf-block-content">
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
												'linkLabel' => null,
												'statusTitle' => __('Web Application Firewall Status', 'wordfence'),
												'statusList' => $firewall->wafStatusList(),
												'statusExtra' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? wfView::create('waf/status-tooltip-learning-mode')->render() : ''),
												'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_STATUS_OVERALL),
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
												'link' => 'https://www.wordfence.com/gnl1wafUpgrade/wordfence-signup/',
												'linkLabel' => null,
												'linkNewWindow' => true,
												'statusTitle' => __('Firewall Rules Status', 'wordfence'),
												'statusList' => $firewall->wafStatusList('rules'),
												'statusExtra' => ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING ? wfView::create('waf/status-tooltip-learning-mode')->render() : ''),
												'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_STATUS_RULES),
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
												'link' => (($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM && $firewall->blacklistMode() == wfFirewall::BLACKLIST_MODE_DISABLED) ? network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#waf-options-advanced') : 'https://www.wordfence.com/gnl1wafUpgrade/wordfence-signup/'),
												'linkLabel' => null,
												'linkNewWindow' => !($firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM && $firewall->blacklistMode() == wfFirewall::BLACKLIST_MODE_DISABLED),
												'statusTitle' => __('Blacklist Status', 'wordfence'),
												'statusList' => $firewall->wafStatusList('blacklist'),
												'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_STATUS_BLACKLIST),
											))->render();
											?>
										</li>
										<li>
											<?php
											echo wfView::create('common/status-detail', array(
												'id' => 'waf-brute',
												'percentage' => $firewall->bruteForceStatus(),
												'title' => __('Brute Force Protection', 'wordfence') . ($firewall->bruteForceStatus() == 0 ? __(': Disabled', 'wordfence') : ''),
												'subtitle' => __('Stops Password Guessing Attacks', 'wordfence'),
												'link' => network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#waf-options-bruteforce'),
												'linkLabel' => null,
												'statusTitle' => __('Brute Force Protection Status', 'wordfence'),
												'statusList' => $firewall->bruteForceStatusList(),
												'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_STATUS_BRUTE_FORCE),
											))->render();
											?>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<?php
					echo wfView::create('waf/options-group-basic-firewall', array(
						'firewall' => $firewall,
						'waf' => $waf,
						'stateKey' => 'waf-options-basic',
						'collapseable' => false,
					))->render();
					?>
					<?php
					echo wfView::create('waf/options-group-advanced-firewall', array(
						'firewall' => $firewall,
						'waf' => $waf,
						'stateKey' => 'waf-options-advanced',
					))->render();
					?>
					<?php
					echo wfView::create('waf/options-group-brute-force', array(
						'firewall' => $firewall,
						'waf' => $waf,
						'stateKey' => 'waf-options-bruteforce',
					))->render();
					?>
					<?php
					echo wfView::create('waf/options-group-rate-limiting', array(
						'firewall' => $firewall,
						'waf' => $waf,
						'stateKey' => 'waf-options-ratelimiting',
					))->render();
					?>
					<?php
					echo wfView::create('waf/options-group-whitelisted', array(
						'firewall' => $firewall,
						'waf' => $waf,
						'stateKey' => 'waf-options-whitelisted',
					))->render();
					?>
				</div> <!-- end waf options block -->
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>