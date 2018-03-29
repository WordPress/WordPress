<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<ul id="wf-option-cbl-bypassViewURL" class="wf-option wf-option-bypass-cookie">
	<li class="wf-option-spacer"></li>
	<li class="wf-option-content">
		<ul>
			<li class="wf-option-title"><?php _e('Bypass Cookie', 'wordfence'); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_BLOCKING_BYPASS_COOKIE); ?>" target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a></li>
			<li class="wf-option-fields">
				<table class="wf-full-width">
					<tr>
						<td class="wf-right wf-padding-add-right"><?php _e('If user who is allowed to access the site views the relative URL', 'wordfence'); ?></td>
						<td class="wf-option-text"><input id="wf-bypass-view-url" type="text" value="<?php echo esc_attr(wfConfig::get('cbl_bypassViewURL')); ?>" placeholder="<?php esc_attr_e('/set-country-bypass/', 'wordfence'); ?>" data-option="cbl_bypassViewURL" data-original-value="<?php echo esc_attr(wfConfig::get('cbl_bypassViewURL')); ?>"></td>
					</tr>
					<tr>
						<td colspan="2" class="wf-right wf-padding-add-top-small"><?php _e('then set a cookie that will bypass country blocking in future in case that user hits the site from a blocked country.', 'wordfence'); ?></td>
					</tr>
				</table>
				<script type="application/javascript">
					(function($) {
						$(function() {
							$('#wf-bypass-view-url').on('keyup', function() {
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
								$('#wf-bypass-view-url').each(function() {
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