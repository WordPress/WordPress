<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a boolean option with a checkbox toggle control and a sub-option toggle.
 *
 * Expects $optionName, $enabledValue, $disabledValue, $value, and $title to be defined for the primary option. $helpLink may also be defined.
 * Expects $subOptionName, $subEnabledValue, $subDisabledValue, $subValue, and $subTitle to be defined for the sub-option. $subHelpLink may also be defined.
 *
 * @var string $optionName The option name.
 * @var string $enabledValue The value to save in $optionName if the toggle is enabled.
 * @var string $disabledValue The value to save in $optionName if the toggle is disabled.
 * @var string $value The current value of $optionName.
 * @var string $title The title shown for the option.
 * @var string $htmlTitle The unescaped title shown for the option.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 * 
 * @var string $subOptionName The sub-option name.
 * @var string $subEnabledValue The value to save in $subOptionName if the toggle is enabled.
 * @var string $subDisabledValue The value to save in $subOptionName if the toggle is disabled.
 * @var string $subValue The current value of $subOptionName.
 * @var string $subTitle The title shown for the sub-option.
 * @var string $subHtmlTitle The unescaped title shown for the sub-option.
 * @var string $subHelpLink If defined, the link to the corresponding external help page for the sub-option.
 * @var bool $subPremium If defined, the sub-option will be tagged as premium only and not allow its value to change for free users.
 */

if (isset($title) && !isset($htmlTitle)) {
	$htmlTitle = esc_html($title);
}

if (isset($subTitle) && !isset($subHtmlTitle)) {
	$subHtmlTitle = esc_html($subTitle);
}

$id = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $optionName);
$subID = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $subOptionName);
?>
<ul class="wf-flex-vertical wf-flex-full-width">
	<li>
		<ul id="<?php echo esc_attr($id); ?>" class="wf-option wf-option-toggled<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>" data-option="<?php echo esc_attr($optionName); ?>" data-enabled-value="<?php echo esc_attr($enabledValue); ?>" data-disabled-value="<?php echo esc_attr($disabledValue); ?>" data-original-value="<?php echo esc_attr($value == $enabledValue ? $enabledValue : $disabledValue); ?>">
			<li class="wf-option-checkbox<?php echo ($value == $enabledValue ? ' wf-checked' : ''); ?>"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></li>
			<li class="wf-option-title">
				<?php echo $htmlTitle; ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?>
			</li>
		</ul>
	</li>
	<li class="wf-option-sub">
		<ul id="<?php echo esc_attr($subID); ?>" class="wf-option wf-option-toggled<?php if (!wfConfig::p() && isset($subPremium) && $subPremium) { echo ' wf-option-premium'; } ?>" data-option="<?php echo esc_attr($subOptionName); ?>" data-enabled-value="<?php echo esc_attr($subEnabledValue); ?>" data-disabled-value="<?php echo esc_attr($subDisabledValue); ?>" data-original-value="<?php echo esc_attr($subValue == $subEnabledValue ? $subEnabledValue : $subDisabledValue); ?>">
			<li class="wf-option-checkbox<?php echo ($subValue == $subEnabledValue ? ' wf-checked' : ''); ?>"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></li>
			<li class="wf-option-title">
				<?php echo $subHtmlTitle; ?><?php if (!wfConfig::p() && isset($subPremium) && $subPremium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($subHelpLink)) { echo ' <a href="' . esc_attr($subHelpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?>
			</li>
		</ul>
	</li>
</ul>