<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a switch option.
 *
 * Expects $optionName, $value, $states, and $title (or $titleHTML) to be defined. $helpLink may also be defined.
 *
 * @var string $optionName The option name for the switch.
 * @var string $value The current value of $optionName.
 * @var string $title The title shown for the option.
 * @var string $titleHTML The raw HTML title shown for the option. This supersedes $title.
 * @var array $states An array of the possible states for the switch. The array matches the format array('value' => <value>, 'label' => <label>)
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 */

if (!isset($titleHTML)) {
	$titleHTML = esc_html($title);
}

$id = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $optionName);
?>
<ul id="<?php echo esc_attr($id); ?>" class="wf-option wf-option-switch<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>" data-option-name="<?php echo esc_attr($optionName); ?>" data-original-value="<?php echo esc_attr($value); ?>">
	<?php if (!isset($noSpacer) || !$noSpacer): ?>
	<li class="wf-option-spacer"></li>
	<?php endif; ?>
	<li class="wf-option-content">
		<ul>
			<li class="wf-option-title">
			<?php if (isset($subtitle)): ?>
				<ul class="wf-flex-vertical wf-flex-align-left">
					<li>
			<?php endif; ?>
					<?php echo $titleHTML; ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?>
			<?php if (isset($subtitle)): ?>
					</li>
					<li class="wf-option-subtitle"><?php echo esc_html($subtitle); ?></li>
				</ul>
			<?php endif; ?>
			</li>
			<li class="wf-option-switch<?php if (isset($alignment)) { echo ' ' . $alignment; } ?> wf-padding-add-top-xs-small">
				<ul class="wf-switch<?php echo (!(!wfConfig::p() && isset($premium) && $premium) ? '' : ' wf-disabled'); ?>">
				<?php foreach ($states as $s): ?>
					<li<?php if ($s['value'] == $value) { echo ' class="wf-active"'; } ?> data-option-value="<?php echo esc_attr($s['value']); ?>"><?php echo esc_html($s['label']); ?></li>
				<?php endforeach; ?>
				</ul>
			</li>
		</ul>
	</li>
</ul>