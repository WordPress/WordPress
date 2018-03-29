<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * No issues found template view.
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
<script type="text/x-jquery-template" id="issueTmpl_noneFound">
	<ul class="wf-issue wf-issue-severity-good" data-issue-id="${id}">
		<li class="wf-issue-summary"> 
			<ul>
				<li class="wf-issue-short"><div class="wf-issue-message">${shortMsg}</div></li>
				<li class="wf-issue-stats"></li>
				<li class="wf-issue-controls"></li>
			</ul>
		</li>
	</ul>
</script>
