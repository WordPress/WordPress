<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an individual element in the scan progress indicator.
 *
 * Expects $id, $title, and $scanner. $premiumOnly and $status may also be defined.
 *
 * @var string $id The element ID.
 * @var string $title The element title.
 * @var wfScanner $scanner The scanner state.
 * @var string $status One of the wfScanner::STATUS_ constants.
 */

if (!isset($premiumOnly)) { $premiumOnly = false; }

$class = '';
switch ($status) {
	case wfScanner::STATUS_PENDING:
		//No class
		break;
	case wfScanner::STATUS_RUNNING:
	case wfScanner::STATUS_RUNNING_WARNING:
		$class = 'wf-scan-step-running';
		break;
	case wfScanner::STATUS_COMPLETE_SUCCESS:
		$class = 'wf-scan-step-complete-success';
		break;
	case wfScanner::STATUS_COMPLETE_WARNING:
		$class = 'wf-scan-step-complete-warning';
		break;
	case wfScanner::STATUS_PREMIUM:
		$class = 'wf-scan-step-premium';
		break;
	case wfScanner::STATUS_DISABLED:
		$class = 'wf-scan-step-disabled';
		break;
}

?>
<li id="<?php echo esc_attr($id); ?>" class="wf-scan-step<?php if ($class) { echo " {$class}"; } ?>">
	<div class="wf-scan-step-icon">
		<?php if ($status == wfScanner::STATUS_PREMIUM): ?>
			<div class="wf-scan-step-premium"></div>
		<?php endif; ?>
		<div class="wf-scan-step-pending"></div>
		<div class="wf-scan-step-running">
			<?php
			echo wfView::create('common/indeterminate-progress', array(
				'size' => 50,
			))->render();
			?>
		</div>
		<div class="wf-scan-step-complete-success"></div>
		<div class="wf-scan-step-complete-warning"></div>
		<div class="wf-scan-step-disabled"></div>
	</div>
	<div class="wf-scan-step-title"><?php echo esc_attr($title); ?></div>
	<?php if ($status == wfScanner::STATUS_PREMIUM): ?>
		<div class="wf-scan-step-subtitle"><a href="https://www.wordfence.com/gnl1scanUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade', 'wordfence'); ?></a></div>
	<?php endif; ?>
</li>
