<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Displays the scan scheduling controls.
 */
?>
<li>
	<?php
	echo wfView::create('options/option-switch', array(
		'optionName' => 'scheduledScansEnabled',
		'value' => wfConfig::get('scheduledScansEnabled') ? '1': '0',
		'title' => __('Schedule Wordfence Scans', 'wordfence'),
		'states' => array(
			array('value' => '0', 'label' => __('Disabled', 'wordfence')),
			array('value' => '1', 'label' => __('Enabled', 'wordfence')),
		),
		'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_SCHEDULING),
	))->render();
	?>
</li>
<li>
<ul class="wf-scan-scheduling" data-option="schedMode" data-original-value="<?php echo esc_attr($scanner->schedulingMode()); ?>">
	<li>
		<ul class="wf-option wf-option-scan-schedule-mode<?php if (!$scanner->isEnabled()) { echo ' wf-disabled'; } ?>" data-option-value="<?php echo esc_attr(wfScanner::SCAN_SCHEDULING_MODE_AUTOMATIC); ?>">
			<li class="wf-option-radio-container">
				<input class="wf-option-radio" type="radio" name="wf-scheduling-mode" id="wf-scheduling-mode-automatic" value="<?php echo esc_attr(wfScanner::SCAN_SCHEDULING_MODE_AUTOMATIC); ?>" <?php echo ($scanner->schedulingMode() == wfScanner::SCAN_SCHEDULING_MODE_AUTOMATIC ? ' checked' : ''); ?><?php if (!$scanner->isEnabled()) { echo ' disabled'; } ?>>
				<label for="wf-scheduling-mode-automatic"></label>
			</li>
			<li class="wf-option-title"><?php _e('Let Wordfence choose when to scan my site (recommended)', 'wordfence'); ?></li>
		</ul>
	</li>
	<li>
		<ul class="wf-option wf-option-scan-schedule-mode<?php if (!wfConfig::p()) { echo ' wf-option-premium'; } ?><?php if (!$scanner->isEnabled()) { echo ' wf-disabled'; } ?>" data-option-value="<?php echo esc_attr(wfScanner::SCAN_SCHEDULING_MODE_MANUAL); ?>" data-show=".wf-scan-scheduling-manual">
			<li class="wf-option-radio-container">
				<input class="wf-option-radio" type="radio" name="wf-scheduling-mode" id="wf-scheduling-mode-manual" value="<?php echo esc_attr(wfScanner::SCAN_SCHEDULING_MODE_MANUAL); ?>" <?php echo ($scanner->schedulingMode() == wfScanner::SCAN_SCHEDULING_MODE_MANUAL ? ' checked' : ''); ?><?php if (!wfConfig::p() || !$scanner->isEnabled()) { echo ' disabled'; } ?>>
				<label for="wf-scheduling-mode-manual"></label>
			</li>
			<li class="wf-option-title"><?php _e('Manually schedule scans', 'wordfence'); ?><?php if (!wfConfig::p()) { ?>&nbsp;&nbsp;<a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link"><?php _e('Premium Feature', 'wordfence'); ?></a><?php } ?></li>
		</ul>
		<ul class="wf-scan-scheduling-manual<?php echo ($scanner->schedulingMode() == wfScanner::SCAN_SCHEDULING_MODE_MANUAL ? ' wf-active' : ''); ?>">
			<li>
				<strong class="wf-scan-scheduling-manual-presets-label">Shortcuts</strong> 
				<ul class="wf-scan-scheduling-manual-presets wf-overflow-x-auto-xs" data-option-name="manualScanType" data-original-value="<?php echo esc_attr(wfConfig::get('manualScanType')); ?>">
					<li class="wf-nowrap <?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_ONCE_DAILY ? 'wf-active' : ''); ?>" data-option-value="<?php echo esc_attr(wfScanner::MANUAL_SCHEDULING_ONCE_DAILY); ?>" data-show=".wf-scan-scheduling-manual-preset-options" data-hide=".wf-scan-scheduling-manual-custom-options"><?php _e('Once Daily', 'wordfence'); ?></li>
					<li class="wf-nowrap <?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_TWICE_DAILY ? 'wf-active' : ''); ?>" data-option-value="<?php echo esc_attr(wfScanner::MANUAL_SCHEDULING_TWICE_DAILY); ?>" data-show=".wf-scan-scheduling-manual-preset-options" data-hide=".wf-scan-scheduling-manual-custom-options"><?php _e('Twice Daily', 'wordfence'); ?></li>
					<li class="wf-nowrap <?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_EVERY_OTHER_DAY ? 'wf-active' : ''); ?>" data-option-value="<?php echo esc_attr(wfScanner::MANUAL_SCHEDULING_EVERY_OTHER_DAY); ?>" data-show=".wf-scan-scheduling-manual-preset-options" data-hide=".wf-scan-scheduling-manual-custom-options"><?php _e('Every Other Day', 'wordfence'); ?></li>
					<li class="wf-nowrap <?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_WEEKDAYS ? 'wf-active' : ''); ?>" data-option-value="<?php echo esc_attr(wfScanner::MANUAL_SCHEDULING_WEEKDAYS); ?>" data-show=".wf-scan-scheduling-manual-preset-options" data-hide=".wf-scan-scheduling-manual-custom-options"><?php _e('Weekdays', 'wordfence'); ?></li>
					<li class="wf-nowrap <?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_WEEKENDS ? 'wf-active' : ''); ?>" data-option-value="<?php echo esc_attr(wfScanner::MANUAL_SCHEDULING_WEEKENDS); ?>" data-show=".wf-scan-scheduling-manual-preset-options" data-hide=".wf-scan-scheduling-manual-custom-options"><?php _e('Weekends', 'wordfence'); ?></li>
					<li class="wf-nowrap <?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_ODD_DAYS_WEEKENDS ? 'wf-active' : ''); ?>" data-option-value="<?php echo esc_attr(wfScanner::MANUAL_SCHEDULING_ODD_DAYS_WEEKENDS); ?>" data-show=".wf-scan-scheduling-manual-preset-options" data-hide=".wf-scan-scheduling-manual-custom-options"><?php _e('Odd Days & Weekends', 'wordfence'); ?></li>
					<li class="wf-nowrap <?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_CUSTOM ? 'wf-active' : ''); ?>" data-option-value="<?php echo esc_attr(wfScanner::MANUAL_SCHEDULING_CUSTOM); ?>" data-show=".wf-scan-scheduling-manual-custom-options" data-hide=".wf-scan-scheduling-manual-preset-options"><?php _e('Custom', 'wordfence'); ?></li>
				</ul>
				<script type="application/javascript">
					(function($) {
						$(function() {
							$('.wf-option-scan-schedule-mode').each(function() {
								$(this).find('.wf-option-radio').on('click', function(e) {
									var optionElement = $(this).closest('.wf-option');
									if (optionElement.hasClass('wf-option-premium') || optionElement.hasClass('wf-disabled')) {
										return;
									}

									var groupElement = $(this).closest('.wf-scan-scheduling');
									var option = groupElement.data('option');
									var value = false;
									var isActive = $(this).is(':checked');
									if (isActive) {
										groupElement.find('.wf-option-scan-schedule-mode').each(function() {
											var toHide = $(this).data('show');
											if (toHide) {
												$(toHide).removeClass('wf-active');
											}
										});
										
										value = optionElement.data('optionValue');
										var toShow = optionElement.data('show');
										if (toShow) {
											$(toShow).addClass('wf-active');
										}
									}

									var originalValue = groupElement.data('originalValue');
									if (originalValue == value) {
										delete WFAD.pendingChanges[option];
									}
									else {
										WFAD.pendingChanges[option] = value;
									}

									WFAD.updatePendingChanges();
								});
							});
							
							$('.wf-scan-scheduling-manual-presets > li').each(function(index, element) {
								$(element).on('click', function(e) {
									e.preventDefault();
									e.stopPropagation();

									var control = $(this).closest('ul');
									var optionName = control.data('optionName');
									var originalValue = control.data('originalValue');
									var value = $(this).data('optionValue');

									control.find('li').each(function() {
										$(this).toggleClass('wf-active', value == $(this).data('optionValue'));
									});

									var toShow = $(this).data('show');
									if (toShow) {
										$(toShow).addClass('wf-active');
									}

									var toHide = $(this).data('hide'); 
									if (toHide) {
										$(toHide).removeClass('wf-active');
									}

									if (originalValue == value) {
										delete WFAD.pendingChanges[optionName];
									}
									else {
										WFAD.pendingChanges[optionName] = value;
									}

									WFAD.updatePendingChanges();
								});
							});

							$('.wf-option[data-option-name="scheduledScansEnabled"]').on('change', function() {
								var scheduledScansEnabled = !!$('.wf-option[data-option-name="scheduledScansEnabled"] .wf-switch li.wf-active').data('optionValue');
								$('.wf-option-scan-schedule-mode').toggleClass('wf-disabled', !scheduledScansEnabled);
								$('.wf-option-scan-schedule-mode .wf-option-radio').prop('disabled', !scheduledScansEnabled);
							});

							$(window).on('wfOptionsReset', function() {
								$('.wf-option-scan-schedule-mode').each(function() {
									var groupElement = $(this).closest('.wf-scan-scheduling');
									var originalValue = groupElement.data('originalValue');
									var option = groupElement.data('option');
									var value = $(this).data('optionValue');
									
									$(this).find('.wf-option-radio').prop('checked', value == originalValue);
									var toHideShow = $(this).data('show');
									if (toHideShow) {
										$(toHideShow).toggleClass('wf-active', value == originalValue);
									}
								});
								
								$('.wf-scan-scheduling-manual-presets').each(function() {
									var originalValue = $(this).data('originalValue');
									$(this).find('li').each(function() {
										$(this).toggleClass('wf-active', originalValue == $(this).data('optionValue'));
										if (originalValue == $(this).data('optionValue')) {
											var toShow = $(this).data('show');
											if (toShow) {
												$(toShow).addClass('wf-active');
											}

											var toHide = $(this).data('hide'); 
											if (toHide) {
												$(toHide).removeClass('wf-active');
											}
										}
									});
								});
							});
						});
					})(jQuery);
				</script>
			</li>
			<li class="wf-scan-scheduling-manual-preset-options<?php echo ($scanner->manualSchedulingType() != wfScanner::MANUAL_SCHEDULING_CUSTOM ? ' wf-active' : ''); ?>">
				<ul class="wf-option wf-option-select" data-select-option="schedStartHour" data-original-select-value="<?php echo esc_attr($scanner->manualSchedulingStartHour()); ?>">
					<li class="wf-option-title"><span class="wf-hidden-xs"><?php _e('Use preferred start time', 'wordfence'); ?></span><span class="wf-visible-xs"><?php _e('Start time', 'wordfence'); ?></span></li>
					<li class="wf-option-select">
						<select<?php echo (!(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?> data-preferred-width="100px"> 
							<?php
							$selectOptions = array();
							for ($i = 1; $i <= 24; $i++) {
								$label = $i . ':00 ';
								if ($i > 12) {
									$label = ($i - 12) . ':00 ';
								}
								
								if ($i < 12 || $i > 23) {
									$label .= __('AM', 'wordfence');
								}
								else {
									$label .= __('PM', 'wordfence');
								}
								
								$selectOptions[] = array('label' => $label, 'value' => $i);
							}
							
							foreach ($selectOptions as $o):
							?>
								<option class="wf-option-select-option" value="<?php echo esc_attr($o['value']); ?>"<?php if ($o['value'] == $scanner->manualSchedulingStartHour()) { echo ' selected'; } ?>><?php echo esc_html($o['label']); ?></option>
							<?php endforeach; ?>
						</select>
					</li>
				</ul>
			</li>
			<li class="wf-scan-scheduling-manual-custom-options wf-overflow-x-auto-xs<?php echo ($scanner->manualSchedulingType() == wfScanner::MANUAL_SCHEDULING_CUSTOM ? ' wf-active' : ''); ?>">
				<table class="wf-scan-schedule" data-original-value="<?php echo esc_attr(@json_encode($scanner->customSchedule(), JSON_FORCE_OBJECT)); ?>">
				<?php
				$daysOfWeek = array(
					array(1, __('Monday', 'wordfence')),
					array(2, __('Tuesday', 'wordfence')),
					array(3, __('Wednesday', 'wordfence')),
					array(4, __('Thursday', 'wordfence')),
					array(5, __('Friday', 'wordfence')),
					array(6, __('Saturday', 'wordfence')),
					array(0, __('Sunday', 'wordfence')),
				);
				$sched = $scanner->customSchedule();
				foreach ($daysOfWeek as $d) :
					list($dayNumber, $dayName) = $d;
				?>
					<tr class="wf-visible-xs">
						<th><?php echo $dayName; ?></th>
					</tr>
					<tr class="wf-schedule-day" data-day="<?php echo $dayNumber; ?>">
						<th class="wf-hidden-xs"><?php echo $dayName; ?></th>
						<td>
							<div class="wf-schedule-times-wrapper">
								<div class="wf-schedule-period"><?php _e('AM', 'wordfence'); ?></div>
								<ul class="wf-schedule-times">
									<?php
									for ($h = 0; $h < 12; $h++) {
										$active = (isset($sched[$dayNumber]) && $sched[$dayNumber][$h] ? ' wf-active' : '');
										echo '<li class="wf-schedule-time' . $active . '" data-hour="' . $h . '">' . str_pad($h, 2, '0', STR_PAD_LEFT) . '</li>';
									}
									?>
								</ul>
							</div>
							<div class="wf-schedule-times-wrapper">
								<div class="wf-schedule-period"><?php _e('PM', 'wordfence'); ?></div>
								<ul class="wf-schedule-times">
									<?php
									for ($i = 0; $i < 12; $i++) {
										$h = $i;
										if ($h == 0) { $h = 12; }
										$active = (isset($sched[$dayNumber]) && $sched[$dayNumber][$i + 12] ? ' wf-active' : '');
										echo '<li class="wf-schedule-time' . $active . '" data-hour="' . ($i + 12) . '">' . str_pad($h, 2, '0', STR_PAD_LEFT) . '</li>';
									}
									?>
								</ul>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
				<script type="application/javascript">
					(function($) {
						$(function() {
							$('.wf-schedule-time').on('click', function(e) {
								e.preventDefault();
								e.stopPropagation();
								
								var isActive = $(this).hasClass('wf-active');
								$(this).toggleClass('wf-active', !isActive);
								var originalValue = $('.wf-scan-schedule').data('originalValue');
								var customSchedule = WFAD.pendingChanges['scanSched'];
								if (!customSchedule) {
									customSchedule = JSON.parse(JSON.stringify(originalValue));
								}
								
								var day = $(this).closest('.wf-schedule-day').data('day');
								var hour = $(this).data('hour');
								customSchedule[day][hour] = isActive ? 0 : 1;
								
								var isOriginal = true;
								var dayKeys = Object.keys(originalValue);
								scheduleEqualityCheck:
								for (var i = 0; i < dayKeys.length; i++) {
									var d = dayKeys[i];
									var originalDay = originalValue[d];
									var currentDay = customSchedule[d];
									var hourKeys = Object.keys(originalDay);
									for (var n = 0; n < hourKeys.length; n++) {
										var h = hourKeys[n];
										if (originalDay[h] != currentDay[h]) {
											isOriginal = false;
											break scheduleEqualityCheck;
										}
									}
								}

								if (isOriginal) {
									delete WFAD.pendingChanges['scanSched'];
								}
								else {
									WFAD.pendingChanges['scanSched'] = customSchedule;
								}

								WFAD.updatePendingChanges();
							});

							$(window).on('wfOptionsReset', function() {
								var originalValue = $('.wf-scan-schedule').data('originalValue');
								$('.wf-schedule-time').each(function() {
									var day = $(this).closest('.wf-schedule-day').data('day');
									var hour = $(this).data('hour');
									$(this).toggleClass('wf-active', !!originalValue[day][hour]);
								});
							});
						});
					})(jQuery);
				</script>
			</li>
		</ul>
	</li>
</ul>
</li>
