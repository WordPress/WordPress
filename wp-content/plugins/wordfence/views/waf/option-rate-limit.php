<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a rate limit option with popup menus for limit value and action selections.
 *
 * Expects $rateOptionName, $rateOptions, $rateValue, $actionOptionName, $actionOptions, $actionValue, and $title to be defined. $helpLink may also be defined.
 *
 * @var string $rateOptionName The option name for the rate portion.
 * @var array $rateOptions An array of the possible values for $rateOptionName. The array is of the format array(array('value' => <the internal value>, 'label' => <a display label>), ...)
 * @var string $rateValue The current value of $rateOptionName.
 * @var string $actionOptionName The option name for the rate portion.
 * @var array $actionOptions An array of the possible values for $actionOptionName. The array is of the format array(array('value' => <the internal value>, 'label' => <a display label>), ...)
 * @var string $actionValue The current value of $actionOptionName.
 * @var string $title The title shown for the option.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 */

$rateID = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $rateOptionName);
$actionID = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $actionOptionName);
?>
<ul class="wf-option wf-option-rate-limit<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>" data-rate-option="<?php echo esc_attr($rateOptionName); ?>" data-original-rate-value="<?php echo esc_attr($rateValue); ?>" data-action-option="<?php echo esc_attr($actionOptionName); ?>" data-original-action-value="<?php echo esc_attr($actionValue); ?>">
	<li class="wf-option-spacer"></li>
	<li class="wf-option-content">
		<ul>
			<li class="wf-option-title"><?php echo esc_html($title); ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?></li>
			<li class="wf-option-select wf-left-xs wf-padding-add-top-xs-small wf-nowrap">
				<select<?php echo (!(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?> id="<?php echo esc_attr($rateID); ?>" class="wf-rate-limit-rate">
					<?php foreach ($rateOptions as $o): ?>
						<option class="wf-option-select-option" value="<?php echo esc_attr($o['value']); ?>"<?php if ($o['value'] == $rateValue) { echo ' selected'; } ?>><?php echo esc_html($o['label']); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="wf-padding-add-left-small wf-padding-add-right-small wf-padding-add-top-xs-small wf-padding-add-bottom-xs-small wf-inline-block-xs"><?php _e('then', 'wordfence'); ?></span>
				<select<?php echo (!(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?> id="<?php echo esc_attr($actionID); ?>" class="wf-rate-limit-action">
					<?php foreach ($actionOptions as $o): ?>
						<option class="wf-option-select-option" value="<?php echo esc_attr($o['value']); ?>"<?php if ($o['value'] == $actionValue) { echo ' selected'; } ?>><?php echo esc_html($o['label']); ?></option>
					<?php endforeach; ?>
				</select>
			</li>
		</ul>
	</li>
</ul>