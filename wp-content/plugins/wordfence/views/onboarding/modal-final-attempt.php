<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the final onboarding attempt modal.
 */
?>
<div class="wf-modal" id="wf-onboarding-final-attempt">
	<div class="wf-modal-header">
		<div class="wf-modal-header-content">
			<div class="wf-modal-title"><?php _e('Please Complete Wordfence Installation', 'wordfence'); ?></div>
		</div>
		<div class="wf-modal-header-action">
			<div class="wf-padding-add-left-small wf-modal-header-action-close"><a href="<?php echo esc_attr(network_admin_url('admin.php?page=Wordfence')); ?>"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</div>
	<div class="wf-modal-content">
		<div id="wf-onboarding-final-attempt-1" class="wf-onboarding-modal-content"<?php if (wfConfig::get('onboardingAttempt3') == wfOnboardingController::ONBOARDING_THIRD_EMAILS) { echo ' style="display: none;"'; } ?>>
			<h3><?php _e('Tell Us Where Wordfence Can Send Alerts', 'wordfence'); ?></h3>
			<input type="text" id="wf-onboarding-alerts" placeholder="you@example.com" value="<?php echo esc_attr(wfConfig::get('alertEmails')); ?>">
			<div id="wf-onboarding-subscribe"><input type="checkbox" class="wf-option-checkbox wf-small" id="wf-onboarding-email-list" checked> <label for="wf-onboarding-email-list"><?php _e('Also join our WordPress Security Mailing List to receive WordPress Security Alerts and Wordfence news', 'wordfence'); ?></label></div>
			<div style="display: none;"><img src="https://forms.aweber.com/form/displays.htm?id=jCxMHAzMLAzsjA==" alt="" /></div>
		</div>
		<div id="wf-onboarding-final-attempt-2" class="wf-onboarding-modal-content"<?php if (wfConfig::get('onboardingAttempt3') != wfOnboardingController::ONBOARDING_THIRD_EMAILS) { echo ' style="display: none;"'; } ?>>
			<h3><?php _e('Activate Premium', 'wordfence'); ?></h3>
			<p><?php _e('Enter your premium license key to enable real-time protection for your website.', 'wordfence'); ?></p>
			<div id="wf-onboarding-license-status" style="display: none;"></div>
			<div id="wf-onboarding-license"><input type="text" placeholder="<?php _e('Enter Premium Key', 'wordfence'); ?>"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary wf-disabled" id="wf-onboarding-license-install"><?php _e('Install', 'wordfence'); ?></a></div>
			<div id="wf-onboarding-or"><span>or</span></div>
			<p id="wf-onboarding-premium-cta"><?php _e('If you don\'t have one, you can purchase one now.', 'wordfence'); ?></p>
			<div id="wf-onboarding-license-footer">
				<ul>
					<li><a href="https://www.wordfence.com/gnl1onboardingFinalGet/wordfence-signup/#premium-order-form" class="wf-onboarding-btn wf-onboarding-btn-primary" id="wf-onboarding-get" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a></li>
					<li><a href="https://www.wordfence.com/gnl1onboardingFinalLearn/wordfence-signup/" class="wf-onboarding-btn wf-onboarding-btn-default" id="wf-onboarding-learn" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></li>
					<li><a href="#" id="wf-onboarding-no-thanks"><?php _e('No Thanks', 'wordfence'); ?></a></li>
				</ul>
			</div>
			<div id="wf-onboarding-license-finished" style="display: none;">
				<ul>
					<li><a href="<?php echo esc_attr(network_admin_url('admin.php?page=Wordfence')); ?>" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Close', 'wordfence'); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="wf-modal-footer"<?php if (wfConfig::get('onboardingAttempt3') == wfOnboardingController::ONBOARDING_THIRD_EMAILS) { echo ' style="display: none;"'; } ?>>
		<ul class="wf-flex-horizontal wf-full-width wf-flex-align-right">
			<li class="wf-padding-add-right"><?php _e('By clicking continue you are agreeing to our <a href="https://www.wordfence.com/terms-of-use/" target="_blank" rel="noopener noreferrer">terms</a> and <a href="https://www.wordfence.com/privacy-policy/" target="_blank" rel="noopener noreferrer">privacy policy</a>', 'wordfence'); ?></li>
			<li><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary wf-disabled" id="wf-onboarding-continue"><?php _e('Continue', 'wordfence'); ?></a></li>
		</ul>
	</div>
</div>
