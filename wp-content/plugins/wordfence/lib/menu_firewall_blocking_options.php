<?php
if (!defined('WORDFENCE_VERSION')) { exit; }

$backPage = new wfPage(wfPage::PAGE_BLOCKING);
if (isset($_GET['source']) && wfPage::isValidPage($_GET['source'])) {
	$backPage = new wfPage($_GET['source']);
}
?>
<script type="application/javascript">
	(function($) {
		$(function() {
			document.title = "<?php esc_attr_e('Blocking Options', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;

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
				'restoreDefaultsSection' => wfConfig::OPTIONS_TYPE_BLOCKING,
				'restoreDefaultsMessage' => __('Are you sure you want to restore the default Blocking settings? This will undo any custom changes you have made to the options on this page. Any existing blocks will be preserved.', 'wordfence'),
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
						'title' => __('Blocking Options', 'wordfence'),
						'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_BLOCKING),
						'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about Blocking</span>', 'wordfence'),
						'showIcon' => true,
					))->render();
					?>
					<div class="wf-row">
						<div class="wf-col-xs-12">
							<div class="wf-block wf-always-active">
								<div class="wf-block-header">
									<div class="wf-block-header-content">
										<div class="wf-block-title">
											<strong><?php _e('General', 'wordfence'); ?></strong>
										</div>
										<div class="wf-block-header-action"></div>
									</div>
								</div>
								<div class="wf-block-content">
									<ul class="wf-block-list">
										<li>
											<?php
											echo wfView::create('options/option-toggled', array(
												'optionName' => 'displayTopLevelBlocking',
												'enabledValue' => 1,
												'disabledValue' => 0,
												'value' => wfConfig::get('displayTopLevelBlocking') ? 1 : 0,
												'title' => __('Display Blocking menu option', 'wordfence'),
											))->render();
											?>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div> <!-- end general options -->
					<?php
					echo wfView::create('blocking/options-group-advanced-country', array(
						'stateKey' => 'blocking-options-country',
						'collapseable' => false,
					))->render();
					?>
				</div> <!-- end blocking options block -->
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>
