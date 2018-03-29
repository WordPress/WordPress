<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the scanner status.
 *
 * Expects $scanner to be defined.
 *
 * @var wfScanner $scanner The scanner state.
 * @var wfDashboard $dashboard Dashboard statistics.
 */
?>
<ul class="wf-block-list wf-block-list-horizontal">
	<?php if ($scanner->isEnabled() == 'enabled' && $scanner->signatureMode() == wfScanner::SIGNATURE_MODE_PREMIUM): ?>
		<li>
			<div class="wf-block-labeled-value wf-scan-status wf-scan-status-full-enabled">
				<div class="wf-block-labeled-value-label"><?php _e('Wordfence Scan &amp; Premium Enabled', 'wordfence'); ?></div>
			</div>
		</li>
	<?php else: ?>
		<li>
			<?php if (!$scanner->isEnabled()): ?>
				<div class="wf-scan-status-disabled">
					<p><h3><?php _e('Wordfence Scan Deactivated', 'wordfence'); ?></h3></p>
					<p><?php _e('A Wordfence scan examines all files, posts, pages, and comments on your WordPress website looking for malware, known malicious URLs, and known patterns of infections. It also does several other reputation and server checks.', 'wordfence'); ?></p>
					<p>
						<a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="#" target="_blank" rel="noopener noreferrer" id="wf-scan-top-enable-scans"><?php _e('Enable Automatic Scans', 'wordfence'); ?></a>
						<script type="application/javascript">
							(function($) {
								$(function() {
									$('#wf-scan-top-enable-scans').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();

										WFAD.setOption('scheduledScansEnabled', 1, function() {
											window.location.reload(true);
										});
									});
								});
							})(jQuery);
						</script>
					</p>
				</div>
			<?php else: ?>
				<div class="wf-block-labeled-value wf-scan-status wf-scan-status-enabled">
					<div class="wf-block-labeled-value-value"><i class="wf-fa wf-fa-check" aria-hidden="true"></i></div>
					<div class="wf-block-labeled-value-label"><?php _e('Wordfence Scan Enabled', 'wordfence'); ?></div>
				</div>
			<?php endif; ?>
		</li>
		<li>
			<?php if ($scanner->signatureMode() == wfScanner::SIGNATURE_MODE_COMMUNITY): ?>
				<div>
					<p><h3><?php _e('Premium Protection Disabled', 'wordfence'); ?></h3></p>
					<p><?php printf(__('As a free Wordfence user, you are currently using the Community version of the Threat Defense Feed. Premium users are protected by an additional %d firewall rules and malware signatures as well as the Wordfence real-time IP blacklist. Upgrade to Premium today to improve your protection.', 'wordfence'), ($dashboard->tdfPremium - $dashboard->tdfCommunity)); ?></p>
					<p><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="https://www.wordfence.com/gnl1scanUpgrade/wordfence-signup/#premium-order-form" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a>&nbsp;&nbsp;<a class="wf-btn wf-btn-callout-subtle wf-btn-default" href="https://www.wordfence.com/gnl1scanLearn/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></p>
				</div>
			<?php else: ?>
				<div class="wf-block-labeled-value wf-protection-status wf-protection-status-<?php echo esc_attr($scanner->signatureMode()); ?>">
					<div class="wf-block-labeled-value-value"><i class="wf-fa wf-fa-check" aria-hidden="true"></i></div>
					<div class="wf-block-labeled-value-label"><?php _e('Premium Protection Enabled', 'wordfence'); ?></div>
				</div>
			<?php endif; ?>
		</li>
	<?php endif; ?>
</ul>
