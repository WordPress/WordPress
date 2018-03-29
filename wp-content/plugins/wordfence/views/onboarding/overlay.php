<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the overlay.
 *
 * Expects $contentHTML to be defined.
 *
 * @var string $contentHTML The HTML content to show on the overlay.
 */
?>
<div id="wf-onboarding-plugin-overlay">
	<a href="#" id="wf-onboarding-dismiss">&times;</a>
	<?php echo $contentHTML; ?>
	<script type="application/javascript">
		(function($) {
			$(function() {
				$('#wf-onboarding-dismiss, #wf-onboarding-plugin-overlay').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					
					$(window).trigger('wfOnboardingDismiss');
					$('#wf-onboarding-plugin-overlay').fadeOut(400, function() {
						$('#wf-onboarding-plugin-overlay').remove();
					});
				});

				$(document).keyup(function(e) {
					if (e.keyCode == 27) { //esc
						$('#wf-onboarding-dismiss').trigger('click');
					}
				});
			});
		})(jQuery);
	</script>
</div>
