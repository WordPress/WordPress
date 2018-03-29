<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<div class="wf-live-activity" data-mode="auto">
	<div class="wf-live-activity-inner">
		<div class="wf-live-activity-content">
			<div class="wf-live-activity-title">Wordfence Live Activity:</div>
			<div class="wf-live-activity-message"></div>
		</div>
		<?php if (wfConfig::get('liveActivityPauseEnabled')): ?>
		<div class="wf-live-activity-state"><p>Live Updates Paused &mdash; Click inside window to resume</p></div>
		<?php endif; ?>
	</div>
</div>