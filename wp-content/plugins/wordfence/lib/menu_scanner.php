<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
$scanner = wfScanner::shared();
$issues = wfIssues::shared();
$dashboard = new wfDashboard();
?>
<?php if (wfConfig::get('liveActivityPauseEnabled')): ?>
	<div id="wfLiveTrafficOverlayAnchor"></div>
	<div id="wfLiveTrafficDisabledMessage">
		<h2>Status Updates Paused<br /><small>Click inside window to resume</small></h2>
	</div>
<?php endif; ?>
<?php
if (wfOnboardingController::shouldShowAttempt3()) {
	echo wfView::create('onboarding/banner')->render();
}
?>
<div class="wrap wordfence">
	<div class="wf-container-fluid">
		<div class="wf-row">
			<div class="wf-col-xs-12">
				<div class="wp-header-end"></div> 
				<?php
				echo wfView::create('common/section-title', array(
					'title' => __('Scan', 'wordfence'),
					'headerID' => 'wf-section-scan',
					'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN),
					'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about the Scanner</span>', 'wordfence'),
					'showIcon' => true,
				))->render();
				?>
			</div>
			<div class="wf-col-xs-12">
				<div class="wf-block wf-active">
					<div class="wf-block-content">
						<ul class="wf-block-list">
							<li>
								<?php
								echo wfView::create('scanner/scanner-status', array(
									'scanner' => $scanner,
									'dashboard' => $dashboard,
								))->render();
								?>
							</li>
							<li>
								<ul class="wf-block-list wf-block-list-horizontal wf-block-list-nowrap wf-scanner-coverage">
									<li>
										<?php
										if (function_exists('network_admin_url') && is_multisite()) { $optionsURL = network_admin_url('admin.php?page=WordfenceScan&subpage=scan_options'); }
										else { $optionsURL = admin_url('admin.php?page=WordfenceScan&subpage=scan_options'); }
										echo wfView::create('common/status-detail', array(
											'id' => 'wf-scanner-type',
											'percentage' => $scanner->scanTypeStatus(),
											'activeColor' => (!$scanner->isEnabled() ? '#ececec' : null /* automatic */),
											'title' => __('Scan Type: ', 'wordfence') . wfScanner::displayScanType($scanner->scanType()),
											'subtitle' => wfScanner::displayScanTypeDetail($scanner->scanType()),
											'link' => $optionsURL,
											'linkLabel' => __('Manage Scan', 'wordfence'),
											'statusTitle' => __('Scan Status', 'wordfence'),
											'statusList' => $scanner->scanTypeStatusList(),
											'helpLink' => __('https://www.wordfence.com/help/scan/#scan-status', 'wordfence'),
										))->render();
										?>
									</li>
									<li>
										<?php
										echo wfView::create('common/status-detail', array(
											'id' => 'wf-scanner-malware-type',
											'percentage' => $scanner->signatureMode() == wfScanner::SIGNATURE_MODE_PREMIUM ? 1.0 : 0.7,
											'activeColor' => (!$scanner->isEnabled() ? '#ececec' : null /* automatic */),
											'title' => __('Malware Signatures: ', 'wordfence') . ($scanner->signatureMode() == wfScanner::SIGNATURE_MODE_PREMIUM ? __('Premium', 'wordfence') : __('Community', 'wordfence')),
											'subtitle' => ($scanner->signatureMode() == wfScanner::SIGNATURE_MODE_PREMIUM ? __('Signatures updated in real-time', 'wordfence') : __('Signature updates delayed by 30 days', 'wordfence')),
											'link' => 'https://www.wordfence.com/gnl1scanUpgrade/wordfence-signup/',
											'linkLabel' => ($scanner->signatureMode() == wfScanner::SIGNATURE_MODE_PREMIUM ? __('Protect More Sites', 'wordfence') : __('Upgrade to Premium', 'wordfence')),
											'linkNewWindow' => true,
											'statusTitle' => __('Malware Signatures Status', 'wordfence'),
											'statusList' => $scanner->signatureMode() == wfScanner::SIGNATURE_MODE_PREMIUM ? array() : array(array(
												'percentage' => 0.30,
												'title'      => __('Enable Premium Scan Signatures.', 'wordfence'),
											)),
											'helpLink' => __('https://www.wordfence.com/help/scan/#scan-status', 'wordfence'),
										))->render();
										?>
									</li>
									<li>
										<?php
										echo wfView::create('common/status-detail', array(
											'id' => 'wf-scanner-reputation',
											'percentage' => $scanner->reputationStatus(),
											'activeColor' => (!$scanner->isEnabled() ? '#ececec' : null /* automatic */),
											'title' => __('Reputation Checks', 'wordfence'),
											'subtitle' => __('Check spam &amp; spamvertising blacklists', 'wordfence'),
											'link' => $optionsURL . '#wf-scanner-options-general',
											'linkLabel' => __('Manage Options', 'wordfence'),
											'statusTitle' => __('Reputation Check Status', 'wordfence'),
											'statusList' => $scanner->reputationStatusList(),
											'helpLink' => __('https://www.wordfence.com/help/scan/#scan-status', 'wordfence'),
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
					<?php if (wfConfig::get('betaThreatDefenseFeed')): ?>
						<ul class="wf-block-banner">
							<li><?php _e('Beta scan signatures are currently enabled. These signatures have not been fully tested yet and may cause false positives or scan stability issues on some sites.', 'wordfence'); ?></li>
							<li><a href="#" class="wf-btn wf-btn-default" id="wf-beta-disable"><?php _e('Turn Off Beta Signatures', 'wordfence'); ?></a></li>
						</ul>
					<?php endif; ?>
					<div class="wf-block-content">
						<ul class="wf-block-list">
							<li>
								<ul class="wf-block-list wf-block-list-horizontal wf-scan-navigation">
									<li>
										<?php
										echo wfView::create('scanner/scan-starter', array(
											'running' => wfScanner::shared()->isRunning(),
										))->render();
										?>
									</li>
									<li>
										<?php
										echo wfView::create('common/block-navigation-option', array(
											'id' => 'wf-scan-option-support',
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
											'id' => 'wf-scan-option-all-options',
											'img' => 'options.svg',
											'title' => __('Scan Options and Scheduling', 'wordfence'),
											'subtitle' => __('Manage scan options including scheduling', 'wordfence'),
											'link' => network_admin_url('admin.php?page=WordfenceScan&subpage=scan_options'),
										))->render();
										?>
									</li>
								</ul>
							</li>
							<li id="wf-scan-progress-bar">
								<?php
								echo wfView::create('scanner/scan-progress', array(
									'scanner' => $scanner,
									'running' => wfScanner::shared()->isRunning(),
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
				<?php
				echo wfView::create('scanner/scan-progress-detailed', array(
					'scanner' => $scanner,
				))->render();
				?>
			</div>
		</div>
		<div class="wf-row">
            <div class="wf-col-xs-12">
			  <?php
			  echo wfView::create('scanner/scan-results', array(
				'scanner' => $scanner, 
				 'issues' => $issues,
			  ))->render();
			  ?>
            </div>
        </div>
		<div class="wf-row">
			<div class="wf-col-xs-12">
				<?php
				echo wfView::create('scanner/site-cleaning-bottom', array(
				))->render();
				?>
			</div>
		</div>
	</div> <!-- end container -->
</div>
<script type="application/javascript">
	(function($) {
		$(function() {
			WFAD.updateActivityLog();
			WFAD.startActivityLogUpdates();
		});
	})(jQuery);
</script>

<script type="text/x-jquery-template" id="wfTmpl_scannerDelete">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Are you sure you want to delete?', 'wordfence'),
		'messageHTML' => '<p class="wf-callout-warning"><i class="wf-fa wf-fa-exclamation-triangle" aria-hidden="true"></i> ' . __('<strong>WARNING:</strong> If you delete the wrong file, it could cause your WordPress website to stop functioning, and you will probably have to restore from a backup.', 'wordfence') . '</p>' . 
			'<p>' . sprintf(__('Do not delete files on your system unless you\'re ABSOLUTELY sure you know what you\'re doing. If you delete the wrong file it could cause your WordPress website to stop functioning and you will probably have to restore from backups. If you\'re unsure, Cancel and work with your hosting provider to clean your system of infected files. If you\'d like to learn more, <a href="%s" target="_blank" rel="noopener noreferrer">click here for our help article</a>.', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_SCAN_BULK_DELETE_WARNING)) . '</p>',
		'primaryButton' => array('id' => 'wf-scanner-prompt-cancel', 'label' => __('Cancel', 'wordfence'), 'link' => '#', 'type' => 'wf-btn-default'),
		'secondaryButtons' => array(array('id' => 'wf-scanner-prompt-confirm', 'label' => __('Delete Files', 'wordfence'), 'link' => '#', 'type' => 'wf-btn-danger')),
	))->render();
	?>
</script>

<script type="text/x-jquery-template" id="wfTmpl_scannerRepair">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Are you sure you want to repair?', 'wordfence'),
		'message' => __('Do not repair files on your system unless you\'re ABSOLUTELY sure you know what you\'re doing. If you repair the wrong file it could cause your WordPress website to stop functioning and you will probably have to restore from backups. If you\'re unsure, Cancel and work with your hosting provider to clean your system of infected files.', 'wordfence'),
		'primaryButton' => array('id' => 'wf-scanner-prompt-cancel', 'label' => __('Cancel', 'wordfence'), 'link' => '#'),
		'secondaryButtons' => array(array('id' => 'wf-scanner-prompt-confirm', 'label' => __('Repair Files', 'wordfence'), 'link' => '#')),
	))->render();
	?>
</script>

<script type="text/x-jquery-template" id="wfTmpl_scannerStop">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => '${title}',
		'message' => '${message}',
		'primaryButton' => array('id' => 'wf-generic-modal-close', 'label' => __('Close', 'wordfence'), 'link' => '#'),
	))->render();
	?>
</script>

<?php
echo wfView::create('scanner/site-cleaning')->render();
echo wfView::create('scanner/site-cleaning-high-sense')->render();
echo wfView::create('scanner/site-cleaning-beta-sigs')->render();
echo wfView::create('scanner/no-issues')->render();
echo wfView::create('scanner/issue-wfUpgrade')->render();
echo wfView::create('scanner/issue-wfPluginUpgrade')->render();
echo wfView::create('scanner/issue-wfThemeUpgrade')->render();
echo wfView::create('scanner/issue-wfPluginRemoved')->render();
echo wfView::create('scanner/issue-wfPluginAbandoned')->render();
echo wfView::create('scanner/issue-wfPluginVulnerable')->render();
echo wfView::create('scanner/issue-file')->render();
echo wfView::create('scanner/issue-knownfile')->render();
echo wfView::create('scanner/issue-configReadable')->render();
echo wfView::create('scanner/issue-publiclyAccessible')->render();
echo wfView::create('scanner/issue-coreUnknown')->render();
echo wfView::create('scanner/issue-dnsChange')->render();
echo wfView::create('scanner/issue-diskSpace')->render();
echo wfView::create('scanner/issue-geoipSupport')->render();
echo wfView::create('scanner/issue-easyPassword')->render();
echo wfView::create('scanner/issue-commentBadURL')->render();
echo wfView::create('scanner/issue-postBadURL')->render();
echo wfView::create('scanner/issue-postBadTitle')->render();
echo wfView::create('scanner/issue-optionBadURL')->render();
echo wfView::create('scanner/issue-database')->render();
echo wfView::create('scanner/issue-checkSpamIP')->render();
echo wfView::create('scanner/issue-spamvertizeCheck')->render();
echo wfView::create('scanner/issue-checkGSB')->render();
echo wfView::create('scanner/issue-checkHowGetIPs')->render();
echo wfView::create('scanner/issue-suspiciousAdminUsers')->render();
echo wfView::create('scanner/issue-timelimit')->render();

//Currently unused
echo wfView::create('scanner/issue-wpscan_fullPathDiscl')->render();
echo wfView::create('scanner/issue-wpscan_directoryList')->render();

if (wfOnboardingController::willShowNewTour(wfOnboardingController::TOUR_SCAN)): ?>
	<script type="application/javascript">
		(function($) {
			$(function() {
				WFAD.tour1 = function() {
					WFAD.tour('wfNewTour1', 'wf-section-scan', 'top', 'left', null, WFAD.tour2);
				};
				WFAD.tour2 = function() {
					WFAD.tour('wfNewTour2', 'wf-scan-option-all-options', 'right', 'right', WFAD.tour1, WFAD.tour3);
				};
				WFAD.tour3 = function() {
					WFAD.tour('wfNewTour3', 'wf-scan-starter', 'left', 'left', WFAD.tour2, WFAD.tourComplete); 
				};
				WFAD.tourComplete = function() { WFAD.tourFinish('<?php echo esc_attr(wfOnboardingController::TOUR_SCAN); ?>'); };

				<?php if (wfOnboardingController::shouldShowNewTour(wfOnboardingController::TOUR_SCAN)): ?>
				if (!WFAD.isSmallScreen) { WFAD.tour1(); }
				<?php endif; ?>
			});
		})(jQuery);
	</script>

	<script type="text/x-jquery-template" id="wfNewTour1">
		<div>
			<h3><?php _e('Scan', 'wordfence'); ?></h3>
			<p><?php _e('A Wordfence scan looks for malware, malicious URLs, and patterns of infections by examining all of the files, posts, and comments on your WordPress website. It also checks your server and monitors your site\'s online reputation.', 'wordfence'); ?></p>
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
	<script type="text/x-jquery-template" id="wfNewTour2">
		<div>
			<h3><?php _e('Manage Scan Settings', 'wordfence'); ?></h3>
			<p class="wf-center"><svg viewBox="0 0 100.11 100.11" class="wf-icon"><path d="M99.59,41.42a2.06,2.06,0,0,0-1.37-.82L86.3,38.78a39.34,39.34,0,0,0-2.67-6.39q1.17-1.63,3.52-4.6t3.32-4.33A2.52,2.52,0,0,0,91,22a2.1,2.1,0,0,0-.46-1.43Q88.18,17.2,79.78,9.45a2.52,2.52,0,0,0-1.63-.65,2.12,2.12,0,0,0-1.57.59l-9.25,7a40.09,40.09,0,0,0-5.87-2.41L59.64,2a1.92,1.92,0,0,0-.75-1.4A2.46,2.46,0,0,0,57.29,0H42.82a2.19,2.19,0,0,0-2.34,1.82,106,106,0,0,0-1.89,12.12,37.62,37.62,0,0,0-5.93,2.48l-9-7A2.78,2.78,0,0,0,22,8.8q-1.44,0-6.16,4.66a64.88,64.88,0,0,0-6.42,7A2.75,2.75,0,0,0,8.8,22a2.44,2.44,0,0,0,.65,1.56q4.37,5.28,7,9a32.38,32.38,0,0,0-2.54,6L1.76,40.34a2,2,0,0,0-1.24.85A2.5,2.5,0,0,0,0,42.69V57.16a2.44,2.44,0,0,0,.52,1.53,2,2,0,0,0,1.37.82l11.93,1.76a31.91,31.91,0,0,0,2.67,6.45Q15.31,69.35,13,72.31T9.65,76.65a2.54,2.54,0,0,0-.07,3q2.54,3.52,10.75,11a2.25,2.25,0,0,0,1.63.71,2.35,2.35,0,0,0,1.63-.59l9.19-7a40.54,40.54,0,0,0,5.87,2.41l1.82,12a1.92,1.92,0,0,0,.75,1.4,2.45,2.45,0,0,0,1.6.55H57.29a2.2,2.2,0,0,0,2.35-1.82,107.41,107.41,0,0,0,1.89-12.12,37.19,37.19,0,0,0,5.93-2.48l9,7a3.18,3.18,0,0,0,1.69.59q1.43,0,6.13-4.62a65.86,65.86,0,0,0,6.45-7,2.16,2.16,0,0,0,.59-1.5,2.51,2.51,0,0,0-.65-1.63q-4.69-5.74-7-9a41.57,41.57,0,0,0,2.54-5.93l12.06-1.82a2,2,0,0,0,1.3-.85,2.52,2.52,0,0,0,.52-1.5V43a2.46,2.46,0,0,0-.52-1.53ZM61.85,61.86a16.08,16.08,0,0,1-11.8,4.89A16.69,16.69,0,0,1,33.37,50.06,16.69,16.69,0,0,1,50.06,33.37,16.69,16.69,0,0,1,66.74,50.06a16.08,16.08,0,0,1-4.89,11.8Zm0,0"></path></svg></p>
			<p><?php _e('Set up the way you want the scan to monitor your site security including custom scan configurations and scheduling.', 'wordfence'); ?></p>
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
	<script type="text/x-jquery-template" id="wfNewTour3">
		<div>
			<h3><?php _e('Start Your First Scan', 'wordfence'); ?></h3>
			<p><?php _e('By default, Wordfence will scan your site daily. Start your first scan now to see if your site has any security issues that need to be addressed. From here you can run manual scans any time you like.', 'wordfence'); ?></p>
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

<?php if (wfOnboardingController::willShowUpgradeTour(wfOnboardingController::TOUR_SCAN)): ?>
	<script type="application/javascript">
		(function($) {
			$(function() {
				WFAD.tour1 = function() {
					WFAD.tour('wfUpgradeTour1', 'wf-scan-option-all-options', 'right', 'right', null, WFAD.tour2);
				};
				WFAD.tour2 = function() {
					WFAD.tour('wfUpgradeTour2', 'wf-scan-starter', 'left', 'left', WFAD.tour1, WFAD.tourComplete);
				};
				WFAD.tourComplete = function() { WFAD.tourFinish('<?php echo esc_attr(wfOnboardingController::TOUR_SCAN); ?>'); };

				<?php if (wfOnboardingController::shouldShowUpgradeTour(wfOnboardingController::TOUR_SCAN)): ?>
				if (!WFAD.isSmallScreen) { WFAD.tour1(); }
				<?php endif; ?>
			});
		})(jQuery);
	</script>

	<script type="text/x-jquery-template" id="wfUpgradeTour1">
		<div>
			<h3><?php _e('Scan Options &amp; Settings', 'wordfence'); ?></h3>
			<p class="wf-center"><svg viewBox="0 0 100.11 100.11" class="wf-icon"><path d="M99.59,41.42a2.06,2.06,0,0,0-1.37-.82L86.3,38.78a39.34,39.34,0,0,0-2.67-6.39q1.17-1.63,3.52-4.6t3.32-4.33A2.52,2.52,0,0,0,91,22a2.1,2.1,0,0,0-.46-1.43Q88.18,17.2,79.78,9.45a2.52,2.52,0,0,0-1.63-.65,2.12,2.12,0,0,0-1.57.59l-9.25,7a40.09,40.09,0,0,0-5.87-2.41L59.64,2a1.92,1.92,0,0,0-.75-1.4A2.46,2.46,0,0,0,57.29,0H42.82a2.19,2.19,0,0,0-2.34,1.82,106,106,0,0,0-1.89,12.12,37.62,37.62,0,0,0-5.93,2.48l-9-7A2.78,2.78,0,0,0,22,8.8q-1.44,0-6.16,4.66a64.88,64.88,0,0,0-6.42,7A2.75,2.75,0,0,0,8.8,22a2.44,2.44,0,0,0,.65,1.56q4.37,5.28,7,9a32.38,32.38,0,0,0-2.54,6L1.76,40.34a2,2,0,0,0-1.24.85A2.5,2.5,0,0,0,0,42.69V57.16a2.44,2.44,0,0,0,.52,1.53,2,2,0,0,0,1.37.82l11.93,1.76a31.91,31.91,0,0,0,2.67,6.45Q15.31,69.35,13,72.31T9.65,76.65a2.54,2.54,0,0,0-.07,3q2.54,3.52,10.75,11a2.25,2.25,0,0,0,1.63.71,2.35,2.35,0,0,0,1.63-.59l9.19-7a40.54,40.54,0,0,0,5.87,2.41l1.82,12a1.92,1.92,0,0,0,.75,1.4,2.45,2.45,0,0,0,1.6.55H57.29a2.2,2.2,0,0,0,2.35-1.82,107.41,107.41,0,0,0,1.89-12.12,37.19,37.19,0,0,0,5.93-2.48l9,7a3.18,3.18,0,0,0,1.69.59q1.43,0,6.13-4.62a65.86,65.86,0,0,0,6.45-7,2.16,2.16,0,0,0,.59-1.5,2.51,2.51,0,0,0-.65-1.63q-4.69-5.74-7-9a41.57,41.57,0,0,0,2.54-5.93l12.06-1.82a2,2,0,0,0,1.3-.85,2.52,2.52,0,0,0,.52-1.5V43a2.46,2.46,0,0,0-.52-1.53ZM61.85,61.86a16.08,16.08,0,0,1-11.8,4.89A16.69,16.69,0,0,1,33.37,50.06,16.69,16.69,0,0,1,50.06,33.37,16.69,16.69,0,0,1,66.74,50.06a16.08,16.08,0,0,1-4.89,11.8Zm0,0"></path></svg></p>
			<p class="wf-center"><?php _e('All of your scan options, including scheduling, are now located here.', 'wordfence'); ?></p>
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
	<script type="text/x-jquery-template" id="wfUpgradeTour2">
		<div>
			<h3><?php _e('Scan Progress and Activity', 'wordfence'); ?></h3>
			<p><?php _e('Track each scan stage as Wordfence scans your entire site. Along the way you can see the activity log one line at a time or expand the activity log for a more detailed view. Clicking on scan results will reveal detailed scan findings.', 'wordfence'); ?></p>
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

<script type="application/javascript">
	(function($) {
		$(function() {
			$('#wf-beta-disable').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				WFAD.setOption('betaThreatDefenseFeed', 0, function() {
					window.location.reload(true);
				});
			});
		});
	})(jQuery);
</script>