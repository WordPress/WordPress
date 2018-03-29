<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the global option howGetIPs with a value select menu and text area (hidden by default) for trusted proxies.
 */

$selectOptions = array(
	array('value' => '', 'label' => esc_html__('Let Wordfence use the most secure method to get visitor IP addresses. Prevents spoofing and works with most sites.', 'wordfence') . ' <strong>' . esc_html__('(Recommended)', 'wordfence') . '</strong>'),
	array('value' => 'REMOTE_ADDR', 'label' => esc_html__('Use PHP\'s built in REMOTE_ADDR and don\'t use anything else. Very secure if this is compatible with your site.', 'wordfence')),
	array('value' => 'HTTP_X_FORWARDED_FOR', 'label' => esc_html__('Use the X-Forwarded-For HTTP header. Only use if you have a front-end proxy or spoofing may result.', 'wordfence')),
	array('value' => 'HTTP_X_REAL_IP', 'label' => esc_html__('Use the X-Real-IP HTTP header. Only use if you have a front-end proxy or spoofing may result.', 'wordfence')),
	array('value' => 'HTTP_CF_CONNECTING_IP', 'label' => esc_html__('Use the Cloudflare "CF-Connecting-IP" HTTP header to get a visitor IP. Only use if you\'re using Cloudflare.', 'wordfence')),
);
?>
<ul class="wf-flex-vertical wf-flex-full-width">
	<li>
		<ul id="wf-option-howGetIPs" class="wf-option wf-option-howgetips" data-option="howGetIPs" data-original-value="<?php echo esc_attr(wfConfig::get('howGetIPs')); ?>" data-text-area-option="howGetIPs_trusted_proxies" data-original-text-area-value="<?php echo esc_attr(wfConfig::get('howGetIPs_trusted_proxies')); ?>">
			<li class="wf-option-spacer"></li>
			<li class="wf-option-content">
				<ul class="wf-flex-vertical wf-flex-align-left">
					<li class="wf-option-title"><?php _e('How does Wordfence get IPs', 'wordfence'); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_HOW_GET_IPS); ?>"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a></li>
					<li>
						<ul class="wf-flex-vertical wf-flex-align-left">
							<li class="wf-padding-add-left">
								<ul class="wf-flex-vertical wf-flex-align-left">
								<?php foreach ($selectOptions as $o): ?>
									<li class="wf-padding-add-top-small"><input type="radio" class="wf-option-radio" name="wf-howgetIPs" value="<?php echo esc_attr($o['value']); ?>" id="wf-howgetIPs-<?php echo esc_attr(preg_replace('/[^a-z0-9]/i', '-', $o['value'])); ?>"<?php if ($o['value'] == wfConfig::get('howGetIPs')) { echo ' checked'; } ?>><label for="wf-howgetIPs-<?php echo esc_attr(preg_replace('/[^a-z0-9]/i', '-', $o['value'])); ?>">&nbsp;&nbsp;</label><?php echo $o['label']; ?></li>
								<?php endforeach; ?>
								</ul>
							</li>
							<li class="wf-option-howgetips-details wf-padding-add-top-small">
								<div class="wf-left">Detected IP(s): <span id="howGetIPs-preview-all"><?php echo wfUtils::getIPPreview(); ?></span></div>
								<div class="wf-left">Your IP with this setting: <span id="howGetIPs-preview-single"><?php echo wfUtils::getIP(); ?></span></div>
								<div class="wf-left"><a href="#" id="howGetIPs-trusted-proxies-show">+ Edit trusted proxies</a></div>
							</li>
						</ul>
					</li>
				</ul>
			</li>
			<!-- <li class="wf-option-disclosure"><svg width="12px" height="12px" viewBox="0 0 12 12"><path id="disclosure-closed" d="M 6 0 l 6 6 -6 6 0 -12" fill="#777"/></svg></li> -->
		</ul>
	</li>
	<li id="howGetIPs-trusted-proxies">
		<ul id="wf-option-howGetIPs-trusted-proxies" class="wf-option wf-option-textarea" data-text-option="howGetIPs_trusted_proxies" data-original-text-value="<?php echo esc_attr(wfConfig::get('howGetIPs_trusted_proxies')); ?>">
			<li class="wf-option-spacer"></li>
			<li class="wf-option-content">
				<ul>
					<li class="wf-option-title">
						<ul class="wf-flex-vertical wf-flex-align-left">
							<li><?php _e('Trusted Proxies', 'wordfence'); ?></li>
							<li class="wf-option-subtitle"><?php _e('These IPs (or CIDR ranges) will be ignored when determining the requesting IP via the X-Forwarded-For HTTP header. Enter one IP or CIDR range per line.', 'wordfence'); ?></li>
						</ul>
					</li>
					<li class="wf-option-textarea">
						<textarea spellcheck="false" autocapitalize="none" autocomplete="off" name="howGetIPs_trusted_proxies"><?php echo esc_html(wfConfig::get('howGetIPs_trusted_proxies')); ?></textarea>
					</li>
				</ul>
			</li>
		</ul>
	</li>
</ul>
<script type="application/javascript">
	(function($) {
		$(function() {
			var updateIPPreview = function() {
				WFAD.updateIPPreview({howGetIPs: $('input[name="wf-howgetIPs"]:checked').val(), 'howGetIPs_trusted_proxies': $('#howGetIPs-trusted-proxies textarea').val()}, function(ret) {
					if (ret && ret.ok) {
						$('#howGetIPs-preview-all').html(ret.ipAll);
						$('#howGetIPs-preview-single').html(ret.ip);
					}
					else {
						//TODO: implementing testing whether or not this setting will lock them out and show the error saying that they'd lock themselves out
					}
				});
			};

			$('input[name="wf-howgetIPs"]').on('change', function() {
				var optionElement = $(this).closest('.wf-option.wf-option-howgetips');
				var option = optionElement.data('option');
				var value = $('input[name="wf-howgetIPs"]:checked').val();

				var originalValue = optionElement.data('originalValue');
				if (originalValue == value) {
					delete WFAD.pendingChanges[option];
				}
				else {
					WFAD.pendingChanges[option] = value;
				}

				WFAD.updatePendingChanges();
				
				updateIPPreview();
			});

			var coalescingUpdateTimer;
			$('#howGetIPs-trusted-proxies textarea').on('keyup', function() {
				clearTimeout(coalescingUpdateTimer);
				coalescingUpdateTimer = setTimeout(updateIPPreview, 1000);

				var optionElement = $(this).closest('.wf-option.wf-option-textarea');
				var option = optionElement.data('textOption');
				var value = $(this).val();

				var originalValue = optionElement.data('originalTextValue');
				if (originalValue == value) {
					delete WFAD.pendingChanges[option];
				}
				else {
					WFAD.pendingChanges[option] = value;
				}

				WFAD.updatePendingChanges();
			});

			$(window).on('wfOptionsReset', function() {
				$('input[name="wf-howgetIPs"]').each(function() {
					var optionElement = $(this).closest('.wf-option.wf-option-howgetips');
					var option = optionElement.data('option');
					var originalValue = optionElement.data('originalValue');
					
					$(this).attr('checked', originalValue == $(this).attr('value'));
				});
						
				$('#howGetIPs-trusted-proxies textarea').each(function() {
					var optionElement = $(this).closest('.wf-option.wf-option-textarea');
					var originalValue = optionElement.data('originalTextAreaValue');
					$(this).val(originalValue);
				});

				updateIPPreview();
			});

			$('#howGetIPs-trusted-proxies-show').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					var isActive = $('#howGetIPs-trusted-proxies').hasClass('wf-active');
					if (isActive) {
						$('#howGetIPs-trusted-proxies').slideUp({
							always: function() {
								$('#howGetIPs-trusted-proxies').removeClass('wf-active');
							}
						});
					}
					else {
						$(this).parent().slideUp(); 
						$('#howGetIPs-trusted-proxies').slideDown({
							always: function() {
								$('#howGetIPs-trusted-proxies').addClass('wf-active');
							}
						});
					}
				});
			});
		});
	})(jQuery);
</script> 