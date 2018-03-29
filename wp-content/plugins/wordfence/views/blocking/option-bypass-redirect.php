<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<ul class="wf-option wf-option-bypass-redirect">
	<li class="wf-option-spacer"></li>
	<li class="wf-option-content">
		<ul>
			<li class="wf-option-title"><?php _e('Bypass Redirect', 'wordfence'); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_BLOCKING_BYPASS_REDIRECT); ?>" target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a></li>
			<li class="wf-option-fields">
				<table class="wf-full-width">
					<tr>
						<td class="wf-right wf-padding-add-right"><?php _e('If user hits the relative URL', 'wordfence'); ?></td>
						<td id="wf-option-cbl-bypassRedirURL" class="wf-option-text"><input id="wf-bypass-redir-url" type="text" value="<?php echo esc_attr(wfConfig::get('cbl_bypassRedirURL'), array()); ?>" placeholder="<?php esc_attr_e('/bypassurl/', 'wordfence'); ?>" data-option="cbl_bypassRedirURL" data-original-value="<?php echo esc_attr(wfConfig::get('cbl_bypassRedirURL')); ?>"></td>
					</tr>
					<tr>
						<td class="wf-right wf-padding-add-right wf-padding-add-top-small"><?php _e('then redirect that user to', 'wordfence'); ?></td>
						<td id="wf-option-cbl-bypassRedirDest" class="wf-option-text wf-padding-add-top-small"><input id="wf-bypass-redir-dest" type="text" value="<?php echo esc_attr(wfConfig::get('cbl_bypassRedirDest')); ?>" placeholder="<?php esc_attr_e('/page-name/', 'wordfence'); ?>" data-option="cbl_bypassRedirDest" data-original-value="<?php echo esc_attr(wfConfig::get('cbl_bypassRedirDest')); ?>"></td>
					</tr>
					<tr>
						<td></td>
						<td class="wf-padding-add-top-small"><?php _e('and set a cookie that will bypass all country blocking.', 'wordfence'); ?></td>
					</tr>
				</table>
				<script type="application/javascript">
					(function($) {
						$(function() {
							$('#wf-bypass-redir-url, #wf-bypass-redir-dest').on('keyup', function() {
								var option = $(this).data('option');
								var value = $(this).val();
		
								var originalValue = $(this).data('originalValue');
								if (originalValue == value) {
									delete WFAD.pendingChanges[option];
								}
								else {
									WFAD.pendingChanges[option] = value;
								}
		
								WFAD.updatePendingChanges();
							});
							
							$(window).on('wfOptionsReset', function() {
								$('#wf-bypass-redir-url, #wf-bypass-redir-dest').each(function() {
									var originalValue = $(this).data('originalValue');
									$(this).val(originalValue);
								});
							});
						});
					})(jQuery);
				</script>
			</li>
		</ul>
	</li>
</ul>