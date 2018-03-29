<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the fresh install modal.
 */
?>
<div id="wf-onboarding-fresh-install" class="wf-onboarding-modal">
	<div id="wf-onboarding-fresh-install-1" class="wf-onboarding-modal-content"<?php if (wfConfig::get('onboardingAttempt1') == wfOnboardingController::ONBOARDING_FIRST_EMAILS) { echo ' style="display: none;"'; } ?>>
		<div class="wf-onboarding-logo"><img src="<?php echo esc_attr(wfUtils::getBaseURL() . 'images/logo.png'); ?>" alt="<?php _e('Wordfence - Securing your WordPress Website', 'wordfence'); ?>"></div>
		<h3><?php printf(__('You have successfully installed Wordfence %s', 'wordfence'), WORDFENCE_VERSION); ?></h3>
		<h4><?php _e('To get started, tell us where Wordfence should send you alerts:', 'wordfence'); ?></h4>
		<input type="text" id="wf-onboarding-alerts" placeholder="you@example.com" value="<?php echo esc_attr(wfConfig::get('alertEmails')); ?>">
		<div id="wf-onboarding-subscribe"><input type="checkbox" class="wf-option-checkbox wf-small" id="wf-onboarding-email-list" checked> <label for="wf-onboarding-email-list"><?php _e('Also join our WordPress Security Mailing List to receive WordPress Security Alerts and Wordfence news', 'wordfence'); ?></label></div>
		<div id="wf-onboarding-footer">
			<ul>
				<li><?php _e('By clicking continue you are agreeing to our <a href="https://www.wordfence.com/terms-of-use/" target="_blank" rel="noopener noreferrer">terms</a> and <a href="https://www.wordfence.com/privacy-policy/" target="_blank" rel="noopener noreferrer">privacy policy</a>', 'wordfence'); ?></li>
				<li><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary wf-disabled" id="wf-onboarding-continue"><?php _e('Continue', 'wordfence'); ?></a></li>
			</ul>
		</div>
		<div style="display: none;"><img src="https://forms.aweber.com/form/displays.htm?id=jCxMHAzMLAzsjA==" alt="" /></div>
	</div>
	<div id="wf-onboarding-fresh-install-2" class="wf-onboarding-modal-content"<?php if (wfConfig::get('onboardingAttempt1') != wfOnboardingController::ONBOARDING_FIRST_EMAILS) { echo ' style="display: none;"'; } ?>>
		<div class="wf-onboarding-logo"><img src="<?php echo esc_attr(wfUtils::getBaseURL() . 'images/logo.png'); ?>" alt="<?php _e('Wordfence - Securing your WordPress Website', 'wordfence'); ?>"></div>
		<h3><?php _e('Enter Premium License Key', 'wordfence'); ?></h3>
		<p><?php _e('Enter your premium license key to enable real-time protection for your website.', 'wordfence'); ?></p>
		<div id="wf-onboarding-license"><input type="text" placeholder="<?php _e('Enter Premium Key', 'wordfence'); ?>"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary wf-disabled" id="wf-onboarding-license-install"><?php _e('Install', 'wordfence'); ?></a></div>
		<div id="wf-onboarding-or"><span>or</span></div>
		<p><?php _e('If you don\'t have one, you can purchase one now.', 'wordfence'); ?></p>
		<div id="wf-onboarding-license-footer">
			<ul>
				<li><a href="https://www.wordfence.com/gnl1onboardingOverlayGet/wordfence-signup/#premium-order-form" class="wf-onboarding-btn wf-onboarding-btn-primary" id="wf-onboarding-get" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a></li>
				<li><a href="https://www.wordfence.com/gnl1onboardingOverlayLearn/wordfence-signup/" class="wf-onboarding-btn wf-onboarding-btn-default" id="wf-onboarding-learn" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></li>
				<li><a href="#" id="wf-onboarding-no-thanks"><?php _e('No Thanks', 'wordfence'); ?></a></li>
			</ul>
		</div>
	</div>
</div>
<script type="application/javascript">
	(function($) {
		$(function() {
			$('#wf-onboarding-alerts').on('change paste keyup', function() {
				setTimeout(function() {
					$('#wf-onboarding-continue').toggleClass('wf-disabled', wordfenceExt.parseEmails($('#wf-onboarding-alerts').val()).length == 0);
				}, 100);
			}).trigger('change');
			
			$('#wf-onboarding-continue').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				var emails = wordfenceExt.parseEmails($('#wf-onboarding-alerts').val());
				if (emails.length > 0) {
					var subscribe = !!$('#wf-onboarding-email-list').is(':checked');
					wordfenceExt.onboardingProcessEmails(emails, subscribe);
					
					<?php if (wfConfig::get('isPaid')): ?>
					$('#wf-onboarding-dismiss').trigger('click');
					wordfenceExt.setOption('onboardingAttempt1', '<?php echo esc_attr(wfOnboardingController::ONBOARDING_FIRST_LICENSE); ?>');
					$('#wf-onboarding-plugin-header').slideUp();
					
					var html = '<div class="wf-modal wf-modal-success"><div class="wf-model-success-wrapper"><div class="wf-modal-header"><div class="wf-modal-header-content"><div class="wf-modal-title"><?php _e('Configuration Complete', 'wordfence'); ?></div></div></div><div class="wf-modal-content"><?php _e('Congratulations! Configuration is complete and Wordfence Premium is active on your website.', 'wordfence'); ?></div></div><div class="wf-modal-footer"><ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width"><li><a href="<?php echo esc_url(network_admin_url('admin.php?page=Wordfence')); ?>" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Go To Dashboard', 'wordfence'); ?></a></li><li class="wf-padding-add-left-small"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-default" onclick="jQuery.wfcolorbox.close(); return false;"><?php _e('Close', 'wordfence'); ?></a></li></ul></div></div>';
					$.wfcolorbox({
						width: (wordfenceExt.isSmallScreen ? '300px' : '500px'),
						html: html,
						overlayClose: true,
						closeButton: false,
						className: 'wf-modal'
					});
					<?php else: ?>
					wordfenceExt.setOption('onboardingAttempt1', '<?php echo esc_attr(wfOnboardingController::ONBOARDING_FIRST_EMAILS); ?>');
					$('#wf-onboarding-fresh-install-1').fadeOut(400, function() {
						$('#wf-onboarding-fresh-install-2').fadeIn();
					});
					<?php endif; ?>
				}
			});

			$('#wf-onboarding-license input').on('change paste keyup', function() {
				setTimeout(function() {
					$('#wf-onboarding-license-install').toggleClass('wf-disabled', $('#wf-onboarding-license input').val().length == 0);
				}, 100);
			}).trigger('change');
			
			$('#wf-onboarding-license-install').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				var license = $('#wf-onboarding-license input').val();
				wordfenceExt.onboardingInstallLicense(license, 
					function(res) { //Success
						if (res.isPaid) {
							$('#wf-onboarding-dismiss').trigger('click');
							var html = '<div class="wf-modal wf-modal-success"><div class="wf-model-success-wrapper"><div class="wf-modal-header"><div class="wf-modal-header-content"><div class="wf-modal-title"><?php _e('Premium License Installed', 'wordfence'); ?></div></div></div><div class="wf-modal-content"><?php _e('Congratulations! Wordfence Premium is now active on your website. Please note that some Premium features are not enabled by default.', 'wordfence'); ?></div></div><div class="wf-modal-footer"><ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width"><li><a href="<?php echo esc_url(network_admin_url('admin.php?page=Wordfence')); ?>" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Go To Dashboard', 'wordfence'); ?></a></li><li class="wf-padding-add-left-small"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-default" onclick="jQuery.wfcolorbox.close(); return false;"><?php _e('Close', 'wordfence'); ?></a></li></ul></div></div>';
							$.wfcolorbox({
								width: (wordfenceExt.isSmallScreen ? '300px' : '500px'),
								html: html,
								overlayClose: true,
								closeButton: false,
								className: 'wf-modal'
							});
							<?php
								//Congratulations! Wordfence Premium is now active on your website. Please note that some Premium features are not enabled by default. Read this brief article to learn more about <a href="#todo" target="_blank" rel="noopener noreferrer">getting the most out of Wordfence Premium</a>.
							?>
						}
						else { //Unlikely to happen but possible
							var html = '<div class="wf-modal"><div class="wf-modal-header"><div class="wf-modal-header-content"><div class="wf-modal-title"><strong><?php _e('Free License Installed', 'wordfence'); ?></strong></div></div></div><div class="wf-modal-content"><?php _e('Free License Installed', 'wordfence'); ?></div><div class="wf-modal-footer"><ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width"><li><a href="<?php echo esc_url(network_admin_url('admin.php?page=Wordfence')); ?>" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php _e('Go To Dashboard', 'wordfence'); ?></a></li><li class="wf-padding-add-left-small"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-default" onclick="jQuery.wfcolorbox.close(); return false;"><?php _e('Close', 'wordfence'); ?></a></li></ul></div></div>';
							$.wfcolorbox({
								width: (wordfenceExt.isSmallScreen ? '300px' : '500px'),
								html: html,
								overlayClose: true,
								closeButton: false,
								className: 'wf-modal'
							});
						}
					},
					function(res) { //Error
						var html = '<div class="wf-modal"><div class="wf-modal-header"><div class="wf-modal-header-content"><div class="wf-modal-title"><strong><?php _e('Error Installing License', 'wordfence'); ?></strong></div></div></div><div class="wf-modal-content">' + res.error + '</div><div class="wf-modal-footer"><ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width"><li><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary" onclick="jQuery.wfcolorbox.close(); return false;"><?php _e('Close', 'wordfence'); ?></a></li></ul></div></div>';
						$.wfcolorbox({
							width: (wordfenceExt.isSmallScreen ? '300px' : '500px'),
							html: html,
							overlayClose: true, 
							closeButton: false, 
							className: 'wf-modal'
						});
					});
			});
			
			$('#wf-onboarding-no-thanks').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				$('#wf-onboarding-dismiss').trigger('click');
			});
			
			$('#wf-onboarding-fresh-install').on('click', function(e) {
				e.stopPropagation();
			});

			$(window).on('wfOnboardingDismiss', function() {
				if ($('#wf-onboarding-fresh-install-1').is(':visible')) {
					wordfenceExt.setOption('onboardingAttempt1', '<?php echo esc_attr(wfOnboardingController::ONBOARDING_FIRST_SKIPPED); ?>');
				}
				else {
					wordfenceExt.setOption('onboardingAttempt1', '<?php echo esc_attr(wfOnboardingController::ONBOARDING_FIRST_LICENSE); ?>');
					$('#wf-onboarding-plugin-header').slideUp();
				}
			});
		});
	})(jQuery);
</script>
