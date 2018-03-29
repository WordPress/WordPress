<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
$scanner = wfScanner::shared();
$scanOptions = $scanner->scanOptions();

$backPage = new wfPage(wfPage::PAGE_SCAN);
if (isset($_GET['source']) && wfPage::isValidPage($_GET['source'])) {
	$backPage = new wfPage($_GET['source']);
}
?>
<script type="application/javascript">
	(function($) {
		$(function() {
			document.title = "<?php esc_attr_e('Scanner Options', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;
			
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
				'backLink' => $backPage->url(),
				'backLabelHTML' => sprintf(__('<span class="wf-hidden-xs">Back to </span>%s', 'wordfence'), $backPage->label()),
				'restoreDefaultsSection' => wfConfig::OPTIONS_TYPE_SCANNER,
				'restoreDefaultsMessage' => __('Are you sure you want to restore the default Scan settings? This will undo any custom changes you have made to the options on this page.', 'wordfence'),
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
		<div class="wf-row">
			<div class="wf-col-xs-12">
				<div class="wp-header-end"></div>
			</div>
		</div>
		<div class="wf-row">
			<div class="<?php echo wfStyle::contentClasses(); ?>">
				<div id="wf-scan-options" class="wf-fixed-tab-content">
					<?php
					echo wfView::create('common/section-title', array(
						'title' => __('Scan Options and Scheduling', 'wordfence'),
						'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN),
						'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about Scanning</span>', 'wordfence'),
						'showIcon' => true,
					))->render();
					?>
					<div class="wf-row">
						<div class="wf-col-xs-12">
							<div class="wf-block wf-active">
								<div class="wf-block-content">
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
												'linkLabel' => null,
												'statusTitle' => __('Scan Status', 'wordfence'),
												'statusList' => $scanner->scanTypeStatusList(),
												'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_STATUS_OVERALL),
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
												'linkLabel' => null,
												'statusTitle' => __('Malware Signatures Status', 'wordfence'),
												'statusList' => $scanner->signatureMode() == wfScanner::SIGNATURE_MODE_PREMIUM ? array() : array(array(
													'percentage' => 0.30,
													'title'      => __('Enable Premium Scan Signatures.', 'wordfence'),
												)),
												'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_STATUS_MALWARE),
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
												'linkLabel' => null,
												'statusTitle' => __('Reputation Check Status', 'wordfence'),
												'statusList' => $scanner->reputationStatusList(),
												'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_STATUS_REPUTATION),
											))->render();
											?>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<?php
					echo wfView::create('scanner/options-group-scan-schedule', array(
						'scanner' => $scanner,
						'stateKey' => 'wf-scanner-options-schedule',
					))->render();
					
					echo wfView::create('scanner/options-group-basic', array(
						'scanner' => $scanner,
						'stateKey' => 'wf-scanner-options-basic',
						'collapseable' => false,
					))->render();
					
					echo wfView::create('scanner/options-group-general', array(
						'scanner' => $scanner,
						'stateKey' => 'wf-scanner-options-general',
					))->render();
					
					echo wfView::create('scanner/options-group-performance', array(
						'scanner' => $scanner,
						'stateKey' => 'wf-scanner-options-performance',
					))->render();
					
					echo wfView::create('scanner/options-group-advanced', array(
						'scanner' => $scanner,
						'stateKey' => 'wf-scanner-options-custom',
					))->render();
					?>
				</div> <!-- end wf-scan-options block -->
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>
<script type="application/javascript">
	(function($) {
		$(function() {
			var updatePendingCircles = function() {
				$('#circle-wf-scanner-type, #circle-wf-scanner-reputation').wfCircularProgress({pendingOverlay: Object.keys(WFAD.pendingChanges).length > 0});
			};
			var coalescingUpdateTimer = false;

			$('.wf-option').on('change', function() {
				clearTimeout(coalescingUpdateTimer);
				coalescingUpdateTimer = setTimeout(updatePendingCircles, 100);
			});
		});
	})(jQuery);
</script>
