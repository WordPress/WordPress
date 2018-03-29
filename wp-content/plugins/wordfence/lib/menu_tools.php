<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * @var string $subpage
 */
?>
<?php
if (wfOnboardingController::shouldShowAttempt3()) {
	echo wfView::create('onboarding/banner')->render();
}
?>
<div class="wrap wordfence">
	<div class="wf-container-fluid">
		<?php
		$tabsArray = array(
			array('twofactor', __('Two Factor Authentication')),
			array('livetraffic', __('Live Traffic')),
			array('commentspam', __('Comment Spam Filter')),
			array('whois', __('Whois Lookup')),
			array('diagnostics', __('Diagnostics')),
		);

		$tabs = array();
		foreach ($tabsArray as $tab) {
			list($tabID, $tabLabel) = $tab;
			$tabs[] = new wfTab($tabID,
				network_admin_url('admin.php?page=WordfenceTools&subpage=' . rawurlencode($tabID)),
				$tabLabel, $tabLabel, $subpage === $tabID);
		}

		echo wfView::create('common/page-fixed-tabbar', array(
			'tabs' => $tabs,
		))->render();
		?>
		<div class="wf-row">
			<div class="<?php echo wfStyle::contentClasses(); ?>">
				<div class="wf-tab-content wf-active">
					<?php echo $content ?>
				</div>
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>