<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a boolean option with a switch toggle control.
 *
 * Expects $optionName, $enabledValue, $disabledValue, $value, and $title to be defined. $helpLink may also be defined.
 *
 * @var string $optionName The option name.
 * @var string $enabledValue The value to save in $option if the toggle is enabled.
 * @var string $disabledValue The value to save in $option if the toggle is disabled.
 * @var string $value The current value of $optionName.
 * @var string $title The title shown for the option.
 * @var string $htmlTitle The unescaped title shown for the option.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 * @var bool $disabled If defined and truthy, the option will start out disabled.
 */

if (isset($subtitle) && !isset($subtitleHTML)) {
	$subtitleHTML = esc_html($subtitle);
}

$id = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $optionName);
?>
<ul id="<?php echo esc_attr($id); ?>" class="wf-option wf-option-toggled-boolean-switch<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?><?php if (isset($disabled) && $disabled) { echo ' wf-disabled'; } ?>" data-option="<?php echo esc_attr($optionName); ?>" data-enabled-value="<?php echo esc_attr($enabledValue); ?>" data-disabled-value="<?php echo esc_attr($disabledValue); ?>" data-original-value="<?php echo esc_attr($value == $enabledValue ? $enabledValue : $disabledValue); ?>">
	<li class="wf-boolean-switch<?php echo ($value == $enabledValue ? ' wf-active' : ''); ?>"><a href="#" class="wf-boolean-switch-handle"></a></li>
	<li class="wf-option-title">
		<?php if (isset($subtitleHTML)): ?>
		<ul class="wf-flex-vertical wf-flex-align-left">
			<li>
				<?php endif; ?>
				<?php echo (!empty($title)) ? esc_html($title) : ''; echo (!empty($htmlTitle)) ? wp_kses($htmlTitle, 'post') : ''; ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?>
				<?php if (isset($subtitleHTML)): ?>
			</li>
			<li class="wf-option-subtitle"><?php echo $subtitleHTML; ?></li>
		</ul>
	<?php endif; ?>
	</li>
</ul>