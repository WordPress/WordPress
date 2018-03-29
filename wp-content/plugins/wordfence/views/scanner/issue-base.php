<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Shared parent view of all scan issues.
 * 
 * Expects $internalType, $displayType, $iconSVG, and $controls.
 * 
 * @var string $internalType The internal issue type used to select the correct template.
 * @var string $displayType A human-readable string for displaying the issue type.
 * @var string $iconSVG The SVG HTML for the issue's icon.
 * @var array $summaryControls An array of summary controls for the issue type.
 * @var array $detailPairs An array of label/value pairs for the issue's detail data. If the entry should only be conditionally shown, the value may be an array of the format array(conditional, displayValue).
 * @var array $detailControls An array of detail controls for the issue type.
 */
?>
<script type="text/x-jquery-template" id="issueTmpl_<?php echo $internalType; ?>">
<ul class="wf-issue wf-issue-<?php echo $internalType; ?> {{if severity == '1'}}wf-issue-severity-critical{{else}}wf-issue-severity-warning{{/if}}" data-issue-id="${id}" data-issue-type="<?php echo $internalType; ?>" data-issue-severity="${severity}" data-high-sensitivity="{{if (data.highSense == '1')}}1{{else}}0{{/if}}" data-beta-signatures="{{if (data.betaSigs == '1')}}1{{else}}0{{/if}}">
	<li class="wf-issue-summary">
		<ul>
			<li class="wf-issue-icon"><?php echo $iconSVG; ?></li>
			<li class="wf-issue-short wf-hidden-xs"><div class="wf-issue-message">${shortMsg}</div><div class="wf-issue-type"><?php echo __('Type:', 'wordfence') . ' ' . $displayType; ?></div></li>
			<li class="wf-issue-stats wf-hidden-xs"><div class="wf-issue-time"><?php _e('Issue Found ', 'wordfence'); ?> ${displayTime}</div>{{if severity == '1'}}<div class="wf-issue-severity-critical"><?php _e('Critical', 'wordfence'); ?></div>{{else}}<div class="wf-issue-severity-warning"><?php _e('Warning', 'wordfence'); ?></div>{{/if}}</li>
			<li class="wf-issue-short-stats wf-hidden-sm wf-hidden-md wf-hidden-lg">
				<div class="wf-issue-message wf-split-word-xs">${shortMsg}</div>
				<div class="wf-issue-type"><?php echo __('Type:', 'wordfence') . ' ' . $displayType; ?></div>
				<div class="wf-issue-time"><?php _e('Found ', 'wordfence'); ?> ${displayTime}</div>
				{{if severity == '1'}}<div class="wf-issue-severity-critical"><?php _e('Critical', 'wordfence'); ?></div>{{else}}<div class="wf-issue-severity-warning"><?php _e('Warning', 'wordfence'); ?></div>{{/if}}
				<div class="wf-issue-controls"><?php echo implode("\n", $summaryControls); ?></div>
			</li>
			<li class="wf-issue-controls wf-hidden-xs"><?php echo implode("\n", $summaryControls); ?></li>
		</ul>
	</li>
	<li class="wf-issue-detail">
		<ul>
			<!--<li><strong><?php _e('Status', 'wordfence'); ?>: </strong>{{if status == 'new' }}<?php _e('New', 'wordfence'); ?>{{/if}}{{if status == 'ignoreP' || status == 'ignoreC' }}<?php _e('Ignored', 'wordfence'); ?>{{/if}}</li>
			<li><strong><?php _e('Issue First Detected', 'wordfence'); ?>: </strong>${timeAgo} <?php _e('ago', 'wordfence'); ?>.</li>-->
		<?php
		foreach ($detailPairs as $label => $value):
			if ($value === null) {
				echo '<li class="wf-issue-detail-spacer"></li>';
				continue;
			}
			
			unset($conditional);
			if (is_array($value)) {
				$conditional = $value[0];
				$value = $value[1];
			}
			
			if (isset($conditional)) { echo '{{if (' . $conditional . ')}}'; }
		?>
			<li><strong><?php echo $label; ?>: </strong><?php echo $value; ?></li>
		<?php
			if (isset($conditional)) { echo '{{/if}}'; }
		endforeach;
		?>
		<?php if (count($detailControls)): ?>
			<li class="wf-issue-detail-controls"><?php echo implode("\n", $detailControls); ?></li>
		<?php endif; ?>
		</ul>
	</li>
</ul>
</script>
