<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-inner">
				<div class="wf-dashboard-item-content">
					<div class="wf-dashboard-item-title">
						<strong>Notifications</strong><span class="wf-dashboard-badge wf-notification-count-container wf-notification-count-value<?php echo (count($d->notifications) == 0 ? ' wf-hidden' : ''); ?>"><?php echo number_format_i18n(count($d->notifications)); ?></span>
					</div>
					<div class="wf-dashboard-item-action"><div class="wf-dashboard-item-action-disclosure"></div></div>
				</div>
			</div>
			<div class="wf-dashboard-item-extra">
				<ul class="wf-dashboard-item-list wf-dashboard-item-list-striped">
					<?php foreach ($d->notifications as $n): ?>
						<li class="wf-notification<?php if ($n->priority % 10 == 1) { echo ' wf-notification-critical'; } else if ($n->priority % 10 == 2) { echo ' wf-notification-warning'; } ?>" data-notification="<?php echo esc_html($n->id); ?>">
							<div class="wf-dashboard-item-list-title"><?php echo $n->html; ?></div>
							<?php foreach ($n->links as $l): ?>
								<div class="wf-dashboard-item-list-action"><a href="<?php echo esc_html($l['link']); ?>"<?php if (preg_match('/^https?:\/\//i', $l['link'])) { echo ' target="_blank" rel="noopener noreferrer"'; } ?>><?php echo esc_html($l['label']); ?></a></div>
							<?php endforeach; ?>
							<div class="wf-dashboard-item-list-dismiss"><a href="#" class="wf-dismiss-notification"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
						</li>
					<?php endforeach; ?>
					<?php if (count($d->notifications) == 0): ?>
						<li class="wf-notifications-empty">No notifications received</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<script type="application/javascript">
	(function($) {
		$('.wf-dismiss-notification').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			var n = $(this).closest('.wf-notification');
			var id = n.data('notification');
			n.fadeOut(400, function() {
				n.remove();
				
				var count = $('.wf-dismiss-notification').length;
				WFDash.updateNotificationCount(count);
			});
			
			WFAD.ajax('wordfence_dismissNotification', {
				id: id
			}, function(res) {
				//Do nothing
			});
		});
	})(jQuery);
</script> 