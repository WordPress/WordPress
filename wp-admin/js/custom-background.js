(function($) {
	$(document).ready(function() {
		var bgImage = $("#custom-background-image"),
			frame;

		$('#background-color').wpColorPicker({
			change: function( event, ui ) {
				bgImage.css('background-color', ui.color.toString());
			},
			clear: function() {
				bgImage.css('background-color', '');
			}
		});

		$('input[name="background-position-x"]').change(function() {
			bgImage.css('background-position', $(this).val() + ' top');
		});

		$('input[name="background-repeat"]').change(function() {
			bgImage.css('background-repeat', $(this).val());
		});

		$('#choose-from-library-link').click( function( event ) {
			var $el = $(this);

			event.preventDefault();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media({
				title:     $el.data('choose'),
				library:   {
					type: 'image'
				}
			});

			frame.toolbar.on( 'activate:select', function() {
				frame.toolbar.view().set({
					select: {
						style: 'primary',
						text:  $el.data('update'),

						click: function() {
							var attachment = frame.state().get('selection').first();
							$.post( ajaxurl, {
								action: 'set-background-image',
								attachment_id: attachment.id,
								size: 'full'
							}, function() {
								window.location.reload();
							});
						}
					}
				});
			});

			frame.state('library');
		});
	});
})(jQuery);