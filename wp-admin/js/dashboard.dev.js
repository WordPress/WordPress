var ajaxWidgets, ajaxPopulateWidgets, quickPressLoad;

jQuery(document).ready( function($) {
	/* Dashboard Welcome Panel */
	var welcomePanel = $('#welcome-panel'),
	 	updateWelcomePanel = function( visible ) {
			$.post( ajaxurl, {
				action: 'update-welcome-panel',
				visible: visible,
				welcomepanelnonce: $('#welcomepanelnonce').val()
			});
		};

	$('.welcome-panel-close', welcomePanel).click( function() {
		welcomePanel.addClass('hidden');
		updateWelcomePanel( 0 );
		$('#wp_welcome_panel-hide').prop('checked', false);
	});

	$('#wp_welcome_panel-hide').click( function() {
		welcomePanel.toggleClass('hidden', ! this.checked );
		updateWelcomePanel( this.checked ? 1 : 0 );
	});

	// These widgets are sometimes populated via ajax
	ajaxWidgets = [
		'dashboard_incoming_links',
		'dashboard_primary',
		'dashboard_secondary',
		'dashboard_plugins'
	];

	ajaxPopulateWidgets = function(el) {
		function show(i, id) {
			var p, e = $('#' + id + ' div.inside:visible').find('.widget-loading');
			if ( e.length ) {
				p = e.parent();
				setTimeout( function(){
					p.load( ajaxurl.replace( '/admin-ajax.php', '' ) + '/index-extra.php?jax=' + id, '', function() {
						p.hide().slideDown('normal', function(){
							$(this).css('display', '');
						});
					});
				}, i * 500 );
			}
		}

		if ( el ) {
			el = el.toString();
			if ( $.inArray(el, ajaxWidgets) != -1 )
				show(0, el);
		} else {
			$.each( ajaxWidgets, show );
		}
	};
	ajaxPopulateWidgets();

	postboxes.add_postbox_toggles(pagenow, { pbshow: ajaxPopulateWidgets } );

	/* QuickPress */
	quickPressLoad = function() {
		var act = $('#quickpost-action'), t;
		t = $('#quick-press').submit( function() {
			$('#dashboard_quick_press #publishing-action img.waiting').css('visibility', 'visible');
			$('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').prop('disabled', true);

			if ( 'post' == act.val() ) {
				act.val( 'post-quickpress-publish' );
			}

			$('#dashboard_quick_press div.inside').load( t.attr( 'action' ), t.serializeArray(), function() {
				$('#dashboard_quick_press #publishing-action img.waiting').css('visibility', 'hidden');
				$('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').prop('disabled', false);
				$('#dashboard_quick_press ul').next('p').remove();
				$('#dashboard_quick_press ul').find('li').each( function() {
					$('#dashboard_recent_drafts ul').prepend( this );
				} ).end().remove();
				quickPressLoad();
			} );
			return false;
		} );

		$('#publish').click( function() { act.val( 'post-quickpress-publish' ); } );

	};
	quickPressLoad();

} );
