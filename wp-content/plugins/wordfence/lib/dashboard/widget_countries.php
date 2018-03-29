<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php //$d is defined here as a wfDashboard instance ?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-inner">
				<div class="wf-dashboard-item-content">
					<div class="wf-dashboard-item-title">
						<strong>Top Countries by Number of Attacks - Last 7 Days</strong>
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
							<?php if (isset($d->countriesNetwork) && count($d->countriesNetwork) > 0): ?>
							<div class="wf-dashboard-toggle-btns">
								<ul class="wf-pagination wf-pagination-sm">
									<li class="wf-active"><a href="#" class="wf-dashboard-countries" data-grouping="local">Local Site</a></li>
									<li><a href="#" class="wf-dashboard-countries" data-grouping="network">Wordfence Network</a></li>
								</ul>
							</div>
							<?php endif; ?>
							<div class="wf-countries wf-countries-local">
								<?php if (!isset($d->countriesLocal) || count($d->countriesLocal) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->countriesLocal, 0, min(10, count($d->countriesLocal)), true); include(dirname(__FILE__) . '/widget_content_countries.php'); ?>
								<?php endif; ?>
							</div>
							<div class="wf-countries wf-countries-network wf-hidden">
								<?php if (!isset($d->countriesNetwork) || count($d->countriesNetwork) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->countriesNetwork, 0, min(10, count($d->countriesNetwork)), true); include(dirname(__FILE__) . '/widget_content_countries.php'); ?>
								<?php endif; ?>
							</div>
							<script type="application/javascript">
								(function($) {
									$('.wf-dashboard-countries').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();

										$(this).closest('ul').find('li').removeClass('wf-active');
										$(this).closest('li').addClass('wf-active');

										$('.wf-countries').addClass('wf-hidden');
										$('.wf-countries-' + $(this).data('grouping')).removeClass('wf-hidden');
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