<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an option with a token field for value entry.
 *
 * Expects $tokenOptionName, $tokenValue, and $title to be defined. $helpLink may also be defined.
 *
 * @var string $tokenOptionName The option name.
 * @var array $tokenValue The current value of $tokenOptionName. It will be JSON-encoded as an array of strings.
 * @var string $title The title shown for the option.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 * @var bool $premium If defined, the option will be tagged as premium only and not allow its value to change for free users.
 */

$id = 'wf-option-' . preg_replace('/[^a-z0-9]/i', '-', $tokenOptionName);
?>
<ul id="<?php echo esc_attr($id); ?>" class="wf-option wf-option-token<?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' wf-option-premium'; } ?>" data-token-option="<?php echo esc_attr($tokenOptionName); ?>" data-original-token-value="<?php echo esc_attr(json_encode($tokenValue)); ?>">
	<li class="wf-option-spacer"></li>
	<li class="wf-flex-vertical wf-flex-align-left">
		<div class="wf-option-title">
		<?php if (isset($subtitle)): ?>
			<ul class="wf-flex-vertical wf-flex-align-left">
				<li>
		<?php endif; ?>
			<?php echo esc_html($title); ?><?php if (!wfConfig::p() && isset($premium) && $premium) { echo ' <a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link">' . __('Premium Feature', 'wordfence') . '</a>'; } ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?>
		<?php if (isset($subtitle)): ?>
				</li>
				<li class="wf-option-subtitle"><?php echo esc_html($subtitle); ?></li>
			</ul>
		<?php endif; ?>
		</div>
		<select multiple<?php echo (!(!wfConfig::p() && isset($premium) && $premium) ? '' : ' disabled'); ?>>
		<?php foreach ($tokenValue as $o): ?>
			<option value="<?php echo esc_attr($o); ?>" selected><?php echo esc_html($o); ?></option>
		<?php endforeach; ?>
		</select>
		<div class="wf-option-token-tags"></div>
	</li>
</ul>