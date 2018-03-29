<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * @var string $id
 */
?>
<script type="text/x-jquery-template" id="<?php echo esc_attr($id); ?>-tmpl">
	<div class="wf-circle-tooltip-block">
		<div class="wf-circle-tooltip-header"><h4><?php echo esc_html($title) ?></h4></div>
		<div class="wf-circle-tooltip-body wf-flex-vertical wf-flex-align-left wf-flex-full-width">
			<?php if (isset($statusExtra) && !empty($statusExtra)) { echo $statusExtra; } ?>
			<div class="wf-flex-row">
				<div class="wf-tooltip-status-circle wf-flex-row-0">
					{{html statusCircle}}
				</div>
				<div class="wf-flex-row-1">
					<?php if (empty($statusList)): ?>
						<p><?php _e('<strong>Congratulations!</strong> You\'ve optimized configurations for this feature! If you want to learn more about how this score is determined, click the link below.') ?></p>
						<p><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($helpLink) ?>"><?php _e('How does Wordfence determine this?', 'wordfence') ?></a></p>
					<?php else: ?>
						<h4><?php _e('How do I get to 100%?', 'wordfence') ?></h4>
						<ul>
							<?php foreach ($statusList as $listItem): ?>
								<li class="wf-flex-row">
									<strong class="wf-flex-row-0"><?php echo $listItem['percentage'] * 100 ?>%</strong>
									<span class="wf-flex-row-1"><?php echo esc_html($listItem['title']) ?></span>
								</li>
							<?php endforeach ?>
						</ul>
						<p><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($helpLink) ?>"><?php _e('How does Wordfence determine this?', 'wordfence') ?></a></p>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</script>
