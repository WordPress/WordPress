<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php
//$d is defined here as a wfDashboard instance

$initial = false;
if (!isset($limit)) { $limit = 10; $initial = true; }
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-inner">
				<div class="wf-dashboard-item-content">
					<div class="wf-dashboard-item-title">
						<strong>Top IPs Blocked</strong>
					</div>
					<div class="wf-dashboard-item-action"><div class="wf-dashboard-item-action-disclosure"></div></div>
				</div>
			</div>
			<div class="wf-dashboard-item-extra">
				<?php if ($firewall->learningModeStatus() !== false): ?>
					<div class="wf-widget-learning-mode"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100.11 100.44"><path d="M96.14,30.67a50.7,50.7,0,0,0-10.66-16A50,50,0,0,0,69.51,4,49.57,49.57,0,0,0,30.6,4a50,50,0,0,0-16,10.69A50.69,50.69,0,0,0,4,30.67,50,50,0,0,0,4,69.74a50.62,50.62,0,0,0,10.66,16,50,50,0,0,0,16,10.69,49.54,49.54,0,0,0,38.91,0,50,50,0,0,0,16-10.69,50.56,50.56,0,0,0,10.66-16,50,50,0,0,0,0-39.07Zm-75.74,39a35.77,35.77,0,0,1-1-37.35,35.21,35.21,0,0,1,12.91-13A34.65,34.65,0,0,1,50.06,14.6a34.22,34.22,0,0,1,19.55,5.93ZM82.71,64a35.4,35.4,0,0,1-7.56,11.37A36,36,0,0,1,63.84,83a34.32,34.32,0,0,1-13.79,2.84A34.85,34.85,0,0,1,30.7,80L79.84,31a34.57,34.57,0,0,1,5.67,19.23A35.17,35.17,0,0,1,82.71,64Zm0,0"/></svg><span><?php _e('No Data Available During Learning Mode', 'wordfence'); ?></span></div>
				<?php else: ?>
				<ul class="wf-dashboard-item-list">
					<li>
						<div>
							<div class="wf-dashboard-toggle-btns">
								<ul class="wf-pagination wf-pagination-sm">
									<li class="wf-active"><a href="#" class="wf-dashboard-ips" data-grouping="24h">24 Hours</a></li>
									<li><a href="#" class="wf-dashboard-ips" data-grouping="7d">7 Days</a></li>
									<li><a href="#" class="wf-dashboard-ips" data-grouping="30d">30 Days</a></li>
								</ul>
							</div>
							<div class="wf-ips wf-ips-24h">
								<?php if (count($d->ips24h) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->ips24h, 0, min($limit, count($d->ips24h)), true); include(dirname(__FILE__) . '/widget_content_ips.php'); ?>
									<?php if (count($d->ips24h) > $limit && $initial): ?>
										<div class="wf-dashboard-item-list-text"><div class="wf-dashboard-show-more" data-grouping="ips" data-period="24h"><a href="#">Show more</a></div></div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							<div class="wf-ips wf-ips-7d wf-hidden">
								<?php if (count($d->ips7d) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->ips7d, 0, min($limit, count($d->ips7d)), true); include(dirname(__FILE__) . '/widget_content_ips.php'); ?>
									<?php if (count($d->ips7d) > $limit && $initial): ?>
										<div class="wf-dashboard-item-list-text"><div class="wf-dashboard-show-more" data-grouping="ips" data-period="7d"><a href="#">Show more</a></div></div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							<div class="wf-ips wf-ips-30d wf-hidden">
								<?php if (count($d->ips30d) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->ips30d, 0, min($limit, count($d->ips30d)), true); include(dirname(__FILE__) . '/widget_content_ips.php'); ?>
									<?php if (count($d->ips30d) > $limit && $initial): ?>
										<div class="wf-dashboard-item-list-text"><div class="wf-dashboard-show-more" data-grouping="ips" data-period="30d"><a href="#">Show more</a></div></div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							<script type="application/javascript">
								(function($) {
									$('.wf-dashboard-ips').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();

										$(this).closest('ul').find('li').removeClass('wf-active');
										$(this).closest('li').addClass('wf-active');

										$('.wf-ips').addClass('wf-hidden');
										$('.wf-ips-' + $(this).data('grouping')).removeClass('wf-hidden');
									});
									
									$('.wf-ips .wf-dashboard-show-more a').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();
										
										var grouping = $(this).parent().data('grouping');
										var period = $(this).parent().data('period');
										
										$(this).closest('.wf-dashboard-item-list-text').fadeOut();

										var self = this;
										WFAD.ajax('wordfence_dashboardShowMore', {
											grouping: grouping,
											period: period
										}, function(res) {
											if (res.ok) {
												var table = $('#ips-data-template').tmpl(res);
												$(self).closest('.wf-ips').css('overflow-y', 'auto');
												$(self).closest('.wf-ips').find('table').replaceWith(table);
											}
											else {
												WFAD.colorboxModal('300px', 'An error occurred', 'We encountered an error trying load more data.');
												$(this).closest('.wf-dashboard-item-list-text').fadeIn();
											}
										});
									});
								})(jQuery);
							</script>
						</div>
					</li>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<script type="text/x-jquery-template" id="ips-data-template">
	<table class="wf-table wf-table-hover">
		<thead>
		<tr>
			<th>IP</th>
			<th colspan="2">Country</th>
			<th>Block Count</th>
		</tr>
		</thead>
		<tbody>
		{{each(idx, d) data}}
		<tr>
			<td>${d.IP}</td>
			<td>${d.countryName}</td>
			<td><img src="${d.countryFlag}" class="wfFlag" height="11" width="16" alt="${d.countryName}" title="${d.countryName}"></td>
			<td>${d.blockCount}</td>
		</tr>
		{{/each}}
		</tbody>
	</table>
</script>