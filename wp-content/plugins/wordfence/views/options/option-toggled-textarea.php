<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an option with a boolean on/off toggle checkbox and text area for detailed value entry.
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
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 */

$toggleID = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $toggleOptionName);
$textAreaID = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $textAreaOptionName);
?>
<ul class="wf-option wf-option-toggled-textarea<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>" data-toggle-option="<?php echo esc_attr($toggleOptionName); ?>" data-enabled-toggle-value="<?php echo esc_attr($enabledToggleValue); ?>" data-disabled-toggle-value="<?php echo esc_attr($disabledToggleValue); ?>" data-original-toggle-value="<?php echo esc_attr($toggleValue == $enabledToggleValue ? $enabledToggleValue : $disabledToggleValue); ?>" data-text-area-option="<?php echo esc_attr($textAreaOptionName); ?>" data-original-text-area-value="<?php echo esc_attr($textAreaValue); ?>">
	<li id="<?php echo esc_attr($toggleID); ?>" class="wf-option-checkbox<?php echo ($toggleValue == $enabledToggleValue ? ' wf-checked' : ''); ?>"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></li>
	<li class="wf-option-title"><?php echo esc_html($title); ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?></li>
	<li id="<?php echo esc_attr($textAreaID); ?>" class="wf-option-textarea">
		<select<?php echo ($toggleValue == $enabledToggleValue && !(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?>>
			<textarea<?php echo (!(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?>><?php echo esc_html($textAreaValue); ?></textarea>
		</select>
	</li>
</ul>