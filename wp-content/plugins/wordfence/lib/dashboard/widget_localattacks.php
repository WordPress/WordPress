<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-inner">
				<div class="wf-dashboard-item-content">
					<div class="wf-dashboard-item-title">
						<strong><?php _e('Firewall Summary:', 'wordfence'); ?> </strong><?php printf(__('Attacks Blocked for %s', 'wordfence'), esc_html(preg_replace('/^[^:]+:\/\//', '', network_site_url()))); ?>
					</div>
					<div class="wf-dashboard-item-action"><div class="wf-dashboard-item-action-disclosure"></div></div>
				</div>
			</div>
			<div class="wf-dashboard-item-extra">
				<?php if ($firewall->learningModeStatus() !== false): ?> 
					<div class="wf-widget-learning-mode"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100.11 100.44"><path d="M96.14,30.67a50.7,50.7,0,0,0-10.66-16A50,50,0,0,0,69.51,4,49.57,49.57,0,0,0,30.6,4a50,50,0,0,0-16,10.69A50.69,50.69,0,0,0,4,30.67,50,50,0,0,0,4,69.74a50.62,50.62,0,0,0,10.66,16,50,50,0,0,0,16,10.69,49.54,49.54,0,0,0,38.91,0,50,50,0,0,0,16-10.69,50.56,50.56,0,0,0,10.66-16,50,50,0,0,0,0-39.07Zm-75.74,39a35.77,35.77,0,0,1-1-37.35,35.21,35.21,0,0,1,12.91-13A34.65,34.65,0,0,1,50.06,14.6a34.22,34.22,0,0,1,19.55,5.93ZM82.71,64a35.4,35.4,0,0,1-7.56,11.37A36,36,0,0,1,63.84,83a34.32,34.32,0,0,1-13.79,2.84A34.85,34.85,0,0,1,30.7,80L79.84,31a34.57,34.57,0,0,1,5.67,19.23A35.17,35.17,0,0,1,82.71,64Zm0,0"/></svg><span><?php _e('No Data Available During Learning Mode', 'wordfence'); ?></span></div>
				<?php else: ?>
				<ul class="wf-dashboard-item-list">
					<li class="wf-flex-vertical wf-flex-full-width">
						<?php
						$hasSome = false;
						foreach ($d->localBlocks as $row) {
							if ($row['24h'] > 0 || $row['7d'] > 0 || $row['30d'] > 0) {
								$hasSome = true;
								break;
							}
						}
						
						if (!$hasSome):
						?>
							<div class="wf-dashboard-item-list-text"><p><em><?php _e('No blocks have been recorded.', 'wordfence'); ?></em></p></div>
						<?php else: ?>
							<table class="wf-blocks-summary">
								<thead>
								<tr>
									<th><?php _e('<span class="wf-hidden-xs">Block </span>Type', 'wordfence'); ?></th>
								<?php
								$totals = array('24h' => 0, '7d' => 0, '30d' => 0);
								foreach ($d->localBlocks as $row): ?>
									<th width="25%"<?php if ($row['type'] == wfActivityReport::BLOCK_TYPE_BLACKLIST && !wfConfig::get('isPaid')) { echo ' class="wf-premium"'; } ?>><?php echo esc_html($row['title']); ?></th>
									<?php $totals['24h'] += $row['24h']; $totals['7d'] += $row['7d']; $totals['30d'] += $row['30d']; ?>
								<?php endforeach; ?>
									<th width="25%"><?php _e('Total', 'wordfence'); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php
								$keys = array('24h' => __('Today', 'wordfence'), '7d' => __('Week', 'wordfence'), '30d' => __('Month', 'wordfence'));
								foreach ($keys as $k => $title): ?>
									<tr>
										<th><?php echo esc_html($title); ?></th>
								<?php foreach ($d->localBlocks as $row): ?>
										<td<?php if ($row['type'] == wfActivityReport::BLOCK_TYPE_BLACKLIST && !wfConfig::get('isPaid')) { echo ' class="wf-premium"'; } ?>><?php echo ($row['type'] == wfActivityReport::BLOCK_TYPE_BLACKLIST && !wfConfig::get('isPaid')) ? '&mdash;' : esc_html(number_format_i18n($row[$k])); ?></td> 
								<?php endforeach; ?>
										<td><?php echo esc_html(number_format_i18n($totals[$k])); ?></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<th></th>
										<?php foreach ($d->localBlocks as $row): ?>
											<td<?php if ($row['type'] == wfActivityReport::BLOCK_TYPE_BLACKLIST && !wfConfig::get('isPaid')) { echo ' class="wf-premium"'; } ?>><?php if ($row['type'] == wfActivityReport::BLOCK_TYPE_BLACKLIST && !wfConfig::get('isPaid')) { _e('Premium', 'wordfence'); } ?></td>
										<?php endforeach; ?>
										<td></td>
									</tr>
								</tfoot>
							</table>
							<p class="wf-right wf-no-top"><a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_WIDGET_LOCAL_ATTACKS); ?>" target="_blank" rel="noopener noreferrer"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i> <?php _e('How are these categorized?', 'wordfence'); ?></a></p>
						<?php endif; ?>
					</li>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>