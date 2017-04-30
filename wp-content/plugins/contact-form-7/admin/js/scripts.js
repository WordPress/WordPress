(function($) {

	$(function() {
		try {
			var welcomePanel = $('#welcome-panel');
			var updateWelcomePanel;

			updateWelcomePanel = function( visible ) {
				$.post( ajaxurl, {
					action: 'wpcf7-update-welcome-panel',
					visible: visible,
					welcomepanelnonce: $( '#welcomepanelnonce' ).val()
				});
			};

			$('a.welcome-panel-close', welcomePanel).click(function(event) {
				event.preventDefault();
				welcomePanel.addClass('hidden');
				updateWelcomePanel( 0 );
			});

			$('div.cf7com-links').insertAfter($('div.wrap h2:first'));

			$.extend($.tgPanes, _wpcf7.tagGenerators);
			$('#taggenerator').tagGenerator(_wpcf7.generateTag, {
				dropdownIconUrl: _wpcf7.pluginUrl + '/admin/images/dropdown.gif',
				fadebuttImageUrl: _wpcf7.pluginUrl + '/admin/images/fade-butt.png' });

			$('input#wpcf7-title:disabled').css({cursor: 'default'});

			$('input#wpcf7-title').mouseover(function() {
				$(this).not('.focus').addClass('mouseover');
			});

			$('input#wpcf7-title').mouseout(function() {
				$(this).removeClass('mouseover');
			});

			$('input#wpcf7-title').focus(function() {
				$(this).addClass('focus').removeClass('mouseover');
			});

			$('input#wpcf7-title').blur(function() {
				$(this).removeClass('focus');
			});

			$('input#wpcf7-title').change(function() {
				updateTag();
			});

			updateTag();

			$('.check-if-these-fields-are-active').each(function(index) {
				if (! $(this).is(':checked'))
					$(this).parent().siblings('.mail-fields').hide();

				$(this).click(function() {
					if ($(this).parent().siblings('.mail-fields').is(':hidden')
					&& $(this).is(':checked')) {
						$(this).parent().siblings('.mail-fields').slideDown('fast');
					} else if ($(this).parent().siblings('.mail-fields').is(':visible')
					&& $(this).not(':checked')) {
						$(this).parent().siblings('.mail-fields').slideUp('fast');
					}
				});
			});

			postboxes.add_postbox_toggles(_wpcf7.screenId);

		} catch (e) {
		}
	});

	function updateTag() {
		var title = $('input#wpcf7-title').val();

		if (title)
			title = title.replace(/["'\[\]]/g, '');

		$('input#wpcf7-title').val(title);
		var postId = $('input#post_ID').val();
		var tag = '[contact-form-7 id="' + postId + '" title="' + title + '"]';
		$('input#contact-form-anchor-text').val(tag);

		var oldId = $('input#wpcf7-id').val();

		if (0 != oldId) {
			var tagOld = '[contact-form ' + oldId + ' "' + title + '"]';
			$('input#contact-form-anchor-text-old').val(tagOld).parent('p.tagcode').show();
		}
	}

})(jQuery);