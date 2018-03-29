<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block wf-block-no-header wf-active">
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('blocking/blocking-status', array(
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block wf-always-active">
			<?php if (!wfConfig::get('firewallEnabled')): ?>
			<ul class="wf-block-banner">
				<li><?php _e('<strong>Note:</strong> Blocking is disabled when the option "Enable Rate Limiting and Advanced Blocking" is off.', 'wordfence'); ?></li>
				<li><a href="#" class="wf-btn wf-btn-default" id="wf-blocking-enable"><?php _e('Turn On', 'wordfence'); ?></a></li>
			</ul>
			<?php endif; ?>
			<?php if (version_compare(phpversion(), '5.4') < 0 && wfConfig::get('isPaid') && wfBlock::hasCountryBlock()): ?>
				<ul class="wf-block-banner">
					<li><?php printf(__('<strong>Note:</strong> The GeoIP database that is required for country blocking is being updated to a new format in April 2018. This new format requires sites to run PHP 5.4 or newer, and this site is on PHP %s. To ensure country blocking continues functioning, please update PHP prior to that date.', 'wordfence'), wfUtils::cleanPHPVersion()); ?></li>
					<li><a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_SCAN_RESULT_GEOIP_UPDATE); ?>" class="wf-btn wf-btn-default" target="_blank" rel="noopener noreferrer"><?php _e('More Information', 'wordfence'); ?></a></li>
				</ul>
			<?php endif; ?>
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong id="wf-block-parameters-title" data-new-title="<?php esc_attr_e('Create a Blocking Rule', 'wordfence'); ?>" data-edit-title="<?php esc_attr_e('Edit Blocking Rule', 'wordfence'); ?>"><?php _e('Create a Blocking Rule', 'wordfence'); ?></strong>
					</div>
				</div>
			</div>
			<div class="wf-block-content">
				<?php
				echo wfView::create('blocking/blocking-create', array(
				))->render();
				?>
			</div>
		</div>
	</div>
</div> <!-- end firewall status -->
<?php
echo wfView::create('blocking/block-list', array(
))->render();
?>
<div id="wf-overlay-wrapper" style="display: none">
	<div class="wf-overlay">
		<div class="wf-overlay-header"></div>
		<div class="wf-overlay-body"></div>
		<span class="wf-overlay-close wf-ion-android-close"></span>
	</div>
</div>
<script type="application/javascript">
	(function($) {
		$(function() {
			$('#wf-blocking-enable').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				WFAD.setOption('firewallEnabled', 1, function() {
					window.location.reload(true);
				});
			});
		});
	})(jQuery);
</script>
<?php if (wfOnboardingController::willShowNewTour(wfOnboardingController::TOUR_BLOCKING)): ?>
	<script type="application/javascript">
		(function($) {
			$(function() {
				WFAD.setUpBlockingTour = function() {
					WFAD.tour1 = function () {
						WFAD.tour('wfBlockingNewTour1', 'wf-section-blocking', 'top', 'left', null, WFAD.tour2);
					};
					WFAD.tour2 = function () {
						WFAD.tour('wfBlockingNewTour2', 'wf-create-block', 'top', 'top', WFAD.tour1, WFAD.tour3);
					};
					WFAD.tour3 = function () {
						WFAD.tour('wfBlockingNewTour3', 'wf-blocks-wrapper', 'bottom', 'bottom', WFAD.tour2, WFAD.tourComplete);
					};
					WFAD.tourComplete = function () {
						WFAD.tourFinish('<?php echo esc_attr(wfOnboardingController::TOUR_BLOCKING); ?>');
					};
				}

				WFAD.blockingTourShown = false;
				<?php if (wfOnboardingController::shouldShowNewTour(wfOnboardingController::TOUR_BLOCKING)): ?>
				$(window).on('wfTabChange', function(e, tab) {
					if (tab == 'blocking' && !WFAD.blockingTourShown) {
						WFAD.blockingTourShown = true;
						WFAD.setUpBlockingTour();
						if (!WFAD.isSmallScreen) { WFAD.tour1(); }
					}
				});

				if ($('#blocking').hasClass('wf-active')) {
					WFAD.blockingTourShown = true;
					WFAD.setUpBlockingTour();
					if (!WFAD.isSmallScreen) { WFAD.tour1(); }
				}
				<?php endif; ?>
			});
		})(jQuery);
	</script>

	<script type="text/x-jquery-template" id="wfBlockingNewTour1">
		<div>
			<h3><?php _e('Blocking', 'wordfence'); ?></h3>
			<p><?php _e('Wordfence lets you take control of protecting your site with powerful blocking features. Block traffic based on IP, IP range, hostname, browser, or referrer. Country blocking is available for Premium customers.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
					<li class="wf-active">&bullet;</li>
					<li>&bullet;</li>
					<li>&bullet;</li>
				</ul>
				<div id="wf-tour-continue"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Next', 'wordfence'); ?></a></div>
			</div>
			<div id="wf-tour-close"><a href="#"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</script>
	<script type="text/x-jquery-template" id="wfBlockingNewTour2">
		<div>
			<h3><?php _e('Blocking Builder', 'wordfence'); ?></h3>
			<p><?php _e('All of your blocking rules are in one central location. Choose the Block Type, then enter the details for the rule. Once it has been added, you\'ll see it saved as a rule for your site.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
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
	<script type="text/x-jquery-template" id="wfBlockingNewTour3">
		<div>
			<h3><?php _e('Manage Blocking Rules', 'wordfence'); ?></h3>
			<p><?php _e('Here\'s where you\'ll see all the blocking rules you\'ve created. You can also manage them as well as remove or modify them from this table.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
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

<?php if (wfOnboardingController::willShowUpgradeTour(wfOnboardingController::TOUR_BLOCKING)): ?>
	<script type="application/javascript">
		(function($) {
			$(function() {
				WFAD.setUpBlockingTour = function() {
					WFAD.tour1 = function () {
						WFAD.tour('wfBlockingUpgradeTour1', 'wf-create-block', 'top', 'top', null, WFAD.tour2);
					};
					WFAD.tour2 = function () {
						WFAD.tour('wfBlockingUpgradeTour2', 'wf-blocks-wrapper', 'bottom', 'bottom', WFAD.tour1, WFAD.tourComplete);
					};
					WFAD.tourComplete = function () {
						WFAD.tourFinish('<?php echo esc_attr(wfOnboardingController::TOUR_BLOCKING); ?>');
					};
				};

				WFAD.blockingTourShown = false;
				<?php if (wfOnboardingController::shouldShowUpgradeTour(wfOnboardingController::TOUR_BLOCKING)): ?>
				$(window).on('wfTabChange', function(e, tab) {
					if (tab == 'blocking' && !WFAD.blockingTourShown) {
						WFAD.blockingTourShown = true;
						WFAD.setUpBlockingTour();
						if (!WFAD.isSmallScreen) { WFAD.tour1(); }
					}
				});

				if ($('#blocking').hasClass('wf-active')) {
					WFAD.blockingTourShown = true;
					WFAD.setUpBlockingTour();
					if (!WFAD.isSmallScreen) { WFAD.tour1(); }
				}
				<?php endif; ?>
			});
		})(jQuery);
	</script>

	<script type="text/x-jquery-template" id="wfBlockingUpgradeTour1">
		<div>
			<h3><?php _e('Blocking Builder', 'wordfence'); ?></h3>
			<p><?php _e('All of the blocking rules you create are now in one central location. Simply choose the block type and enter the details for the rule you want to create. Premium users have access to advanced country blocking options, found via the <strong>Options</strong> link.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
					<li class="wf-active">&bullet;</li>
					<li>&bullet;</li>
				</ul>
				<div id="wf-tour-continue"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Next', 'wordfence'); ?></a></div>
			</div>
			<div id="wf-tour-close"><a href="#"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</script>
	<script type="text/x-jquery-template" id="wfBlockingUpgradeTour2">
		<div>
			<h3><?php _e('Manage Blocking Rules', 'wordfence'); ?></h3>
			<p><?php _e('All blocking rules you create will show here. You can manage them as well as remove or modify them from the same location.', 'wordfence'); ?></p>
			<div class="wf-pointer-footer">
				<ul class="wf-tour-pagination">
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
