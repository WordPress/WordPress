if (!window['WFDash']) {
	window['WFDash'] = {
		updateNotificationCount: function(count) {
			(function($) {
				$('.wf-notification-count-value').html(count);
				if (count == 0) {
					$('.wf-notification-count-container').addClass('wf-hidden');
				}
				else {
					$('.wf-notification-count-container').removeClass('wf-hidden');
				}
			})(jQuery);
		}
	}
}
