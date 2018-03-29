<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents multiple boolean options under a single heading with a checkbox toggle control for each.
 *
 * Expects $options and $title to be defined. $options is an array of 
 * 	array(
 * 		'name' => <option name>, 
 * 		'enabledValue' => <value saved if the toggle is enabled>, 
 * 		'disabledValue' => <value saved if the toggle is disabled>,
 * 		'value' => <current value of the option>,
 * 		'title' => <title displayed to label the checkbox>
 * 	)
 * 
 * $helpLink may also be defined.
 *
 * @var array $options The options shown. The structure is defined above.
 * @var string $title The overall title shown for the options.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the options will be tagged as premium only and not allow its values to change for free users.
 */
?>
<ul class="wf-option wf-option-toggled-multiple<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>">
	<li class="wf-option-title"><?php echo esc_html($title); ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?></li>
	<li class="wf-option-checkboxes">
		<?php
		foreach ($options as $o):
			$id = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $o['name']);
		?>
		<ul id="<?php echo esc_attr($id); ?>" data-option="<?php echo esc_attr($o['name']); ?>" data-enabled-value="<?php echo esc_attr($o['enabledValue']); ?>" data-disabled-value="<?php echo esc_attr($o['disabledValue']); ?>" data-original-value="<?php echo esc_attr($o['value'] == $o['enabledValue'] ? $o['enabledValue'] : $o['disabledValue']); ?>">
			<li class="wf-option-checkbox<?php echo ($o['value'] == $o['enabledValue'] ? ' wf-checked' : ''); ?>"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></li>
			<li class="wf-option-title"><?php echo esc_html($o['title']); ?></li>
		</ul>
		<?php endforeach; ?>
	</li>
</ul>