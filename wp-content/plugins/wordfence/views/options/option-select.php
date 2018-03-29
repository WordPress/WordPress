<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an option with a popup menu for detailed value selection.
 *
 * Expects $selectOptionName, $selectOptions, $selectValue, and $title to be defined. $helpLink may also be defined.
 *
 * @var string $selectOptionName The option name for the select portion.
 * @var array $selectOptions An array of the possible values for $selectOptionName. The array is of the format array(array('value' => <the internal value>, 'label' => <a display label>), ...)
 * @var string $selectValue The current value of $selectOptionName.
 * @var string $title The title shown for the option.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 */

$id = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $selectOptionName);
?>
<ul id="<?php echo esc_attr($id); ?>" class="wf-option wf-option-select<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>" data-select-option="<?php echo esc_attr($selectOptionName); ?>" data-original-select-value="<?php echo esc_attr($selectValue); ?>">
	<li class="wf-option-spacer"></li>
	<li class="wf-option-content">
		<ul>
			<li class="wf-option-title"><?php echo esc_html($title); ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?></li>
			<li class="wf-option-select wf-padding-add-top-xs-small">
				<select<?php echo (!(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?>>
					<?php foreach ($selectOptions as $o): ?>
						<option class="wf-option-select-option" value="<?php echo esc_attr($o['value']); ?>"<?php if ($o['value'] == $selectValue) { echo ' selected'; } ?>><?php echo esc_html($o['label']); ?></option>
					<?php endforeach; ?>
				</select>
			</li>
		</ul>
	</li>
</ul>