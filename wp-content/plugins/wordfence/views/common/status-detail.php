<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Expects $id, $percentage (as decimal value), $title, $subtitle, $link, $linkLabel, and $statusList to be defined.
 * If $linkLabel is null, the link will be hidden.
 * $linkNewWindow can optionally be defined and defaults to false.
 * $activeColor can optionally be defined. If not defined, the color is based on $percentage.
 */

if (!isset($activeColor)) {
	$activeColor = '#fcb214';
	if ($percentage == 0) {
		$activeColor = '#ececec';
	}
	else if ($percentage <= 0.50) {
		$activeColor = '#9e0000';
	}
	else if ($percentage == 1) {
		$activeColor = '#16bc9b';
	}
}

if (!isset($linkNewWindow)) { $linkNewWindow = false; }
?>
<div id="<?php echo esc_attr($id); ?>" class="wf-status-detail">
	<?php
	echo wfView::create('common/status-circular', array(
		'id' => 'circle-' . $id,
		'diameter' => 100,
		'percentage' => $percentage,
		'inactiveColor' => '#ececec',
		'activeColor' => $activeColor,
	))->render();
	?>
	<p class="wf-status-detail-title"><?php echo esc_html($title); ?></p>
	<p class="wf-status-detail-subtitle"><?php echo esc_html($subtitle); ?></p>
	<p class="wf-status-detail-link"><?php if ($linkLabel !== null): ?><a href="<?php echo esc_attr($link); ?>"<?php echo ($linkNewWindow ? ' target="_blank" rel="noopener noreferrer"' : ''); ?>><?php echo esc_html($linkLabel); ?></a><?php endif; ?></p>
	<?php
	echo wfView::create('common/status-tooltip', array(
		'id' => 'tooltip-circle-' . $id,
		'title' => $statusTitle,
		'statusList' => $statusList,
		'statusExtra' => (isset($statusExtra) ? $statusExtra : ''),
		'helpLink' => $helpLink,
	))->render();
	?> 
</div>
