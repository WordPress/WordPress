<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an option with a boolean on/off toggle checkbox and popup menu for detailed value selection.
 * 
 * Expects $toggleOptionName, $enabledToggleValue, $disabledToggleValue, $toggleValue, $selectOptionName, $selectOptions, $selectValue, and $title to be defined. $helpLink may also be defined.
 * 
 * @var string $toggleOptionName The option name for the toggle portion.
 * @var string $enabledToggleValue The value to save in $toggleOption if the toggle is enabled.
 * @var string $disabledToggleValue The value to save in $toggleOption if the toggle is disabled.
 * @var string $toggleValue The current value of $toggleOptionName.
 * @var string $selectOptionName The option name for the select portion.
 * @var array $selectOptions An array of the possible values for $selectOptionName. The array is of the format array(array('value' => <the internal value>, 'label' => <a display label>), ...)
 * @var string $selectValue The current value of $selectOptionName.
 * @var string $title The title shown for the option.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 */

$toggleID = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $toggleOptionName);
$selectID = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $selectOptionName);
?>
<ul class="wf-option wf-option-toggled-select<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>" data-toggle-option="<?php echo esc_attr($toggleOptionName); ?>" data-enabled-toggle-value="<?php echo esc_attr($enabledToggleValue); ?>" data-disabled-toggle-value="<?php echo esc_attr($disabledToggleValue); ?>" data-original-toggle-value="<?php echo esc_attr($toggleValue == $enabledToggleValue ? $enabledToggleValue : $disabledToggleValue); ?>" data-select-option="<?php echo esc_attr($selectOptionName); ?>" data-original-select-value="<?php echo esc_attr($selectValue); ?>">
	<li id="<?php echo esc_attr($toggleID); ?>" class="wf-option-checkbox<?php echo ($toggleValue == $enabledToggleValue ? ' wf-checked' : ''); ?>"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></li>
	<li class="wf-option-content">
		<ul id="<?php echo esc_attr($selectID); ?>">
			<li class="wf-option-title"><?php echo esc_html($title); ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?></li>
			<li class="wf-option-select wf-padding-add-top-xs-small">
				<select<?php echo ($toggleValue == $enabledToggleValue && !(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?>>
				<?php foreach ($selectOptions as $o): ?>
					<option class="wf-option-select-option" value="<?php echo esc_attr($o['value']); ?>"<?php if ($o['value'] == $selectValue) { echo ' selected'; } ?>><?php echo esc_html($o['label']); ?></option>
				<?php endforeach; ?>
				</select>
			</li>
		</ul>
	</li>
</ul>