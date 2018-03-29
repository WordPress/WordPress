<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the global status.
 *
 * Expects $firewall, $scanner, and $dashboard to be defined.
 *
 * @var wfFirewall $firewall The firewall state.
 * @var wfScanner $scanner The scanner state.
 * @var wfDashboard $dashboard Dashboard statistics.
 */
?>
<ul class="wf-block-list wf-block-list-horizontal">
	<li id="wfStatusTourMarker">
		<div class="wf-block-labeled-value wf-global-status wf-global-status-full-enabled">
			<div class="wf-block-labeled-value-label"><?php _e('Wordfence Protection Activated', 'wordfence'); ?></div>
		</div>
	</li>
</ul>
