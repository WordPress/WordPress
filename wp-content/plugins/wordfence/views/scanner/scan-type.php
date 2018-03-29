<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Displays the scan type selector and automatic scan toggle.
 */

function _wfAllowOnlyBoolean($value) {
	return ($value === false || $value === true);
}

$limitedOptions = array_filter(wfScanner::limitedScanTypeOptions(), '_wfAllowOnlyBoolean');
$standardOptions = array_filter(wfScanner::standardScanTypeOptions(), '_wfAllowOnlyBoolean');
$highSensitivityOptions = array_filter(wfScanner::highSensitivityScanTypeOptions(), '_wfAllowOnlyBoolean');
?>
<ul id="wf-option-scanType" class="wf-scan-type-controls">
	<li class="wf-scan-type-selector wf-overflow-x-auto-xs">
		<ul class="wf-scan-type" data-option-name="scanType" data-original-value="<?php echo esc_attr($scanner->scanType()); ?>">
			<li>
				<ul class="wf-scan-type-option<?php if ($scanner->scanType() == wfScanner::SCAN_TYPE_LIMITED) { echo ' wf-active'; } ?>" data-option-value="<?php echo esc_attr(wfScanner::SCAN_TYPE_LIMITED); ?>" data-selected-options="<?php echo esc_attr(json_encode($limitedOptions)); ?>">
					<li class="wf-scan-type-option-name"><div class="wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div><span><?php _e('Limited Scan', 'wordfence'); ?></span></li>
					<li class="wf-scan-type-option-description"><?php _e('For entry-level hosting plans. Provides limited detection capability with very low resource utilization.', 'wordfence'); ?></li>
				</ul>
			</li>
			<li>
				<ul class="wf-scan-type-option<?php if ($scanner->scanType() == wfScanner::SCAN_TYPE_STANDARD) { echo ' wf-active'; } ?>" data-option-value="<?php echo esc_attr(wfScanner::SCAN_TYPE_STANDARD); ?>" data-selected-options="<?php echo esc_attr(json_encode($standardOptions)); ?>">
					<li class="wf-scan-type-option-name"><div class="wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div><span><?php _e('Standard Scan', 'wordfence'); ?></span></li>
					<li class="wf-scan-type-option-description"><?php _e('Our recommendation for all websites. Provides the best detection capability in the industry.', 'wordfence'); ?></li>
				</ul>
			</li>
			<li>
				<ul class="wf-scan-type-option<?php if ($scanner->scanType() == wfScanner::SCAN_TYPE_HIGH_SENSITIVITY) { echo ' wf-active'; } ?>" data-option-value="<?php echo esc_attr(wfScanner::SCAN_TYPE_HIGH_SENSITIVITY); ?>" data-selected-options="<?php echo esc_attr(json_encode($highSensitivityOptions)); ?>">
					<li class="wf-scan-type-option-name"><div class="wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div><span><?php _e('High Sensitivity', 'wordfence'); ?></span></li>
					<li class="wf-scan-type-option-description"><?php _e('For site owners who think they may have been hacked. More thorough but may produce false positives.', 'wordfence'); ?></li>
				</ul>
			</li>
			<li>
				<ul class="wf-scan-type-option wf-scan-type-option-custom<?php if ($scanner->scanType() == wfScanner::SCAN_TYPE_CUSTOM) { echo ' wf-active'; } ?>" data-option-value="<?php echo esc_attr(wfScanner::SCAN_TYPE_CUSTOM); ?>">
					<li class="wf-scan-type-option-name"><span><?php _e('Custom Scan', 'wordfence'); ?></span></li>
					<li class="wf-scan-type-option-description"><?php _e('Selected automatically when General Options have been customized for this website.', 'wordfence'); ?></li>
				</ul>
			</li>
		</ul>
		<script type="application/javascript">
			(function($) {
				$(function() {
					//Set initial state
					var currentScanType = $('.wf-scan-type-option.wf-active');
					if (!currentScanType.hasClass('wf-scan-type-option-custom')) {
						var selectedOptions = currentScanType.data('selectedOptions');
						var keys = Object.keys(selectedOptions);
						for (var i = 0; i < keys.length; i++) {
							$('.wf-option.wf-option-toggled[data-option="' + keys[i] + '"]').find('.wf-option-checkbox').toggleClass('wf-checked', selectedOptions[keys[i]]); //Currently all checkboxes
						}
					}
					
					$('.wf-scan-type-option').each(function(index, element) {
						$(element).on('click', function(e) {
							if ($(element).hasClass('wf-scan-type-option-custom')) {
								return;
							}
							
							e.preventDefault();
							e.stopPropagation();

							var control = $(this).closest('.wf-scan-type');
							var optionName = control.data('optionName');
							var originalValue = control.data('originalValue');
							var value = $(this).data('optionValue');

							control.find('.wf-scan-type-option').each(function() {
								$(this).toggleClass('wf-active', value == $(this).data('optionValue'));
							});

							if (originalValue == value) {
								delete WFAD.pendingChanges[optionName];
							}
							else {
								WFAD.pendingChanges[optionName] = value;
							}
							
							var selectedOptions = $(this).data('selectedOptions');
							var keys = Object.keys(selectedOptions);
							for (var i = 0; i < keys.length; i++) {
								delete WFAD.pendingChanges[keys[i]];
								$('.wf-option.wf-option-toggled[data-option="' + keys[i] + '"]').find('.wf-option-checkbox').toggleClass('wf-checked', selectedOptions[keys[i]]); //Currently all checkboxes
							}

							WFAD.updatePendingChanges();
						});
					});

					$(window).on('wfOptionsReset', function() {
						$('.wf-scan-type').each(function() {
							var originalValue = $(this).data('originalValue');
							$(this).find('.wf-scan-type-option').each(function() {
								var isSelected = (originalValue == $(this).data('optionValue'));
								$(this).toggleClass('wf-active', isSelected);
								if (!$(this).hasClass('wf-scan-type-option-custom') && isSelected) {
									var selectedOptions = $(this).data('selectedOptions');
									var keys = Object.keys(selectedOptions);
									for (var i = 0; i < keys.length; i++) {
										$('.wf-option.wf-option-toggled[data-option="' + keys[i] + '"]').find('.wf-option-checkbox').toggleClass('wf-checked', selectedOptions[keys[i]]); //Currently all checkboxes
									}
								}
							});
						});
					});

					//Hook up change events on individual checkboxes
					var availableOptions = <?php echo json_encode(array_keys($highSensitivityOptions)); ?>;
					for (var i = 0; i < availableOptions.length; i++) {
						$('.wf-option.wf-option-toggled[data-option="' + availableOptions[i] + '"]').on('change', function(e, isReset) { //Currently all checkboxes
							if (isReset) {
								return;
							}
							
							var currentScanType = $('.wf-scan-type-option.wf-active');
							if (!currentScanType.hasClass('wf-scan-type-option-custom')) {
								currentScanType.removeClass('wf-active');
								$('.wf-scan-type-option.wf-scan-type-option-custom').addClass('wf-active');

								if ($('.wf-scan-type').data('originalValue') == '<?php echo esc_attr(wfScanner::SCAN_TYPE_CUSTOM); ?>') {
									delete WFAD.pendingChanges['scanType'];
								}
								else {
									WFAD.pendingChanges['scanType'] = '<?php echo esc_attr(wfScanner::SCAN_TYPE_CUSTOM); ?>';
								}

								var selectedOptions = currentScanType.data('selectedOptions');
								var keys = Object.keys(selectedOptions);
								for (var i = 0; i < keys.length; i++) {
									if (keys[i] == $(this).data('option')) {
										continue;
									}
									
									var option = $('.wf-option.wf-option-toggled[data-option="' + keys[i] + '"]'); 
									option.find('.wf-option-checkbox').toggleClass('wf-checked', selectedOptions[keys[i]]); //Currently all checkboxes
									var value = (selectedOptions[keys[i]] ? option.data('enabledValue') : option.data('disabledValue'));
									var originalValue = option.data('originalValue');
									if (originalValue == value) {
										delete WFAD.pendingChanges[keys[i]];
									}
									else {
										WFAD.pendingChanges[keys[i]] = value;
									}
								}
								WFAD.updatePendingChanges();
							}
						});
					}
				});
			})(jQuery);
		</script>
	</li>
</ul>
