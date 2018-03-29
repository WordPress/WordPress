<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a block list element specifically for the start scan button.
 *
 * Expects $running.
 *
 * @var bool $running Whether or not the scan is currently running.
 */
?>
<div id="wf-scan-starter" class="wf-block-navigation-option">
	<div class="wf-block-navigation-option-content">
		<a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-scan-starter-idle" style="<?php if ($running) { echo 'display: none;'; } ?>"><?php _e('Start New Scan', 'wordfence'); ?></a>
		<a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-scan-starter-running" style="<?php if (!$running) { echo 'display: none;'; } ?>;"><?php _e('Stop Scan', 'wordfence'); ?></a> 
	</div>
</div>
<script type="application/javascript">
	(function($) {
		$('#wf-scan-starter a').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			if ($(this).hasClass('wf-scan-starter-idle')) {
				WFAD.startScan();
				$('#wf-scan-running-bar').show();
			}
			else {
				WFAD.killScan(function(success) {
					WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), success ? '<?php esc_attr_e('Scan Stopping', 'wordfence'); ?>' : '<?php esc_attr_e('Stop Failed', 'wordfence'); ?>', success ? '<?php esc_attr_e('A termination request has been sent to stop any running scans.', 'wordfence'); ?>' : '<?php esc_attr_e('We failed to send a termination request.', 'wordfence'); ?>');
				});
				$('#wf-scan-running-bar').hide();
			}
		});
		
		$(window).on('wfScanUpdateButtons', function() {
			if (WFAD.scanRunning) {
				$('.wf-scan-starter-idle').hide();
				$('.wf-scan-starter-running').show();
			}
			else {
				$('.wf-scan-starter-idle').show().toggleClass('wf-disabled', WFAD.scanFailed);
				$('.wf-scan-starter-running').hide();
			}
		})
	})(jQuery);
</script>