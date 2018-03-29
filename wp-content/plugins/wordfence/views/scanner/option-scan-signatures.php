<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the scan option scansEnabled_fileContents with a boolean on/off toggle checkbox and text area (hidden by default) for custom scan signatures.
 *
 * Expects $toggleOptionName, $enabledToggleValue, $disabledToggleValue, $toggleValue, $textAreaOptionName, $textAreaValue, and $title to be defined. $helpLink may also be defined.
 *
 * @var string $toggleOptionName The option name for the toggle portion.
 * @var string $enabledToggleValue The value to save in $toggleOption if the toggle is enabled.
 * @var string $disabledToggleValue The value to save in $toggleOption if the toggle is disabled.
 * @var string $toggleValue The current value of $toggleOptionName.
 * @var string $textAreaOptionName The option name for the text area portion.
 * @var string $textAreaValue The current value of $textAreaOptionName.
 * @var string $title The title shown for the option.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 */
?>
<ul class="wf-flex-vertical wf-flex-full-width">
	<li>
		<ul id="wf-option-scansEnabled-fileContents" class="wf-option wf-option-scan-signatures" data-toggle-option="scansEnabled_fileContents" data-enabled-toggle-value="1" data-disabled-toggle-value="0" data-original-toggle-value="<?php echo wfConfig::get('scansEnabled_fileContents') ? 1 : 0; ?>" data-text-area-option="scan_include_extra" data-original-text-area-value="<?php echo esc_attr(wfConfig::get('scan_include_extra')); ?>">
			<li class="wf-option-checkbox<?php echo (wfConfig::get('scansEnabled_fileContents') ? ' wf-checked' : ''); ?>"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></li>
			<li class="wf-option-title"><?php echo esc_html($title); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_SCAN_OPTION_MALWARE_SIGNATURES); ?>"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a></li>
			<li class="wf-option-disclosure"><svg width="12px" height="12px" viewBox="0 0 12 12"><path id="disclosure-closed" d="M 6 0 l 6 6 -6 6 0 -12" fill="#777"/></svg></li>
		</ul>
	</li>
	<li id="wf-scan-additional-signatures">
		<h4>Add Additional Signatures</h4>
		<textarea id="wf-option-scan-include-extra" spellcheck="false" autocapitalize="none" autocomplete="off"><?php echo esc_html(wfConfig::get('scan_include_extra')); ?></textarea>
	</li>
</ul>
<script type="application/javascript">
	(function($) {
		$(function() {
			$('.wf-option.wf-option-scan-signatures .wf-option-checkbox').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					var optionElement = $(this).closest('.wf-option');
					var option = optionElement.data('toggleOption');
					var value = false;
					var isActive = $(this).hasClass('wf-checked');
					if (isActive) {
						$(this).removeClass('wf-checked');
						value = optionElement.data('disabledToggleValue');
					}
					else {
						$(this).addClass('wf-checked');
						value = optionElement.data('enabledToggleValue');
					}

					var originalValue = optionElement.data('originalToggleValue');
					if (originalValue == value) {
						delete WFAD.pendingChanges[option];
					}
					else {
						WFAD.pendingChanges[option] = value;
					}

					WFAD.updatePendingChanges();
				});
			});

			$('#wf-scan-additional-signatures textarea').on('keyup', function() {
				var optionElement = $(this).closest('ul').find('.wf-option.wf-option-scan-signatures');
				var option = optionElement.data('textAreaOption');
				var value = $(this).val();

				var originalValue = optionElement.data('originalTextAreaValue');
				if (originalValue == value) {
					delete WFAD.pendingChanges[option];
				}
				else {
					WFAD.pendingChanges[option] = value;
				}

				WFAD.updatePendingChanges();
			});

			$(window).on('wfOptionsReset', function() {
				$('.wf-option.wf-option-scan-signatures .wf-option-checkbox').each(function() {
					var optionElement = $(this).closest('.wf-option');
					$(this).toggleClass('wf-checked', !!parseInt(optionElement.data('originalToggleValue')));
				});

				$('#wf-scan-additional-signatures textarea').each(function() {
					var optionElement = $(this).closest('ul').find('.wf-option.wf-option-scan-signatures');
					var originalValue = optionElement.data('originalTextAreaValue');
					$(this).val(originalValue);
				});
			});
			
			$('.wf-option.wf-option-scan-signatures .wf-option-disclosure').each(function() {
				var disclosure = $(this).find('svg');
				
				$(this).closest('.wf-option').css('cursor', 'pointer');
				$(this).closest('.wf-option').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
		
					var isActive = $('#wf-scan-additional-signatures').hasClass('wf-active');
					if (isActive) {
						disclosure.css('transform', 'rotate(0deg)');
						$('#wf-scan-additional-signatures').slideUp({
							always: function() {
								$('#wf-scan-additional-signatures').removeClass('wf-active');
							}
						});
					}
					else {
						disclosure.css('transform', 'rotate(90deg)');
						$('#wf-scan-additional-signatures').slideDown({
							always: function() {
								$('#wf-scan-additional-signatures').addClass('wf-active');
							}
						});
					}
				});
			});
		});
	})(jQuery);
</script> 