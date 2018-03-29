<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
if (wfOnboardingController::shouldShowAttempt3()) {
	echo wfView::create('onboarding/banner')->render();
}
?>
<div class="wrap wordfence">
	<div class="wf-container-fluid">
		<?php
		echo wfView::create('common/page-tabbar', array(
			'tabs' => array(
				new wfTab('waf', 'waf', __('Firewall', 'wordfence'), __('Web Application Firewall', 'wordfence')),
				new wfTab('blocking', 'blocking', __('Blocking', 'wordfence'), __('Blocking', 'wordfence')),
			),
		))->render();
		?>
		<div class="wf-row">
			<div class="<?php echo wfStyle::contentClasses(); ?>">
				<div id="waf" class="wf-tab-content" data-title="Web Application Firewall">
					<?php
					echo wfView::create('common/section-title', array(
						'title' => __('Firewall', 'wordfence'),
						'headerID' => 'wf-section-firewall',
						'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF),
						'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about the Firewall</span>', 'wordfence'),
					))->render();
					require('menu_firewall_waf.php');
					?>
				</div> <!-- end waf block -->
				<div id="blocking" class="wf-tab-content" data-title="Blocking">
					<?php
					echo wfView::create('common/section-title', array(
						'title' => __('Blocking', 'wordfence'),
						'headerID' => 'wf-section-blocking',
						'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_BLOCKING),
						'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about Blocking</span>', 'wordfence'),
					))->render();
					require('menu_firewall_blocking.php');
					?>
				</div> <!-- end blocking block -->
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>
