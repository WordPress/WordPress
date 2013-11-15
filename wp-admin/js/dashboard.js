/* global pagenow, ajaxurl, postboxes, wpActiveEditor:true */
var ajaxWidgets, ajaxPopulateWidgets, quickPressLoad;

jQuery(document).ready( function($) {
	/* Dashboard Welcome Panel */
	var welcomePanel = $('#welcome-panel'),
		welcomePanelHide = $('#wp_welcome_panel-hide'),
		updateWelcomePanel = function( visible ) {
			$.post( ajaxurl, {
				action: 'update-welcome-panel',
				visible: visible,
				welcomepanelnonce: $('#welcomepanelnonce').val()
			});
		};

	if ( welcomePanel.hasClass('hidden') && welcomePanelHide.prop('checked') ) {
		welcomePanel.removeClass('hidden');
	}

	$('.welcome-panel-close, .welcome-panel-dismiss a', welcomePanel).click( function(e) {
		e.preventDefault();
		welcomePanel.addClass('hidden');
		updateWelcomePanel( 0 );
		$('#wp_welcome_panel-hide').prop('checked', false);
	});

	welcomePanelHide.click( function() {
		welcomePanel.toggleClass('hidden', ! this.checked );
		updateWelcomePanel( this.checked ? 1 : 0 );
	});

	// These widgets are sometimes populated via ajax
	ajaxWidgets = ['dashboard_primary'];

	ajaxPopulateWidgets = function(el) {
		function show(i, id) {
			var p, e = $('#' + id + ' div.inside:visible').find('.widget-loading');
			if ( e.length ) {
				p = e.parent();
				setTimeout( function(){
					p.load( ajaxurl + '?action=dashboard-widgets&widget=' + id + '&pagenow=' + pagenow, '', function() {
						p.hide().slideDown('normal', function(){
							$(this).css('display', '');
						});
					});
				}, i * 500 );
			}
		}

		if ( el ) {
			el = el.toString();
			if ( $.inArray(el, ajaxWidgets) !== -1 ) {
				show(0, el);
			}
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
			$('#dashboard_quick_press #publishing-action .spinner').show();
			$('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').prop('disabled', true);

			$.post( t.attr( 'action' ), t.serializeArray(), function( data ) {
				// Replace the form, and prepend the published post.
				$('#dashboard_quick_press .inside').html( data );
				$('#quick-press').removeClass('initial-form');
				quickPressLoad();
				highlightLatestPost();
				$('#title').focus();
			});

			function highlightLatestPost () {
				var latestPost = $('.drafts ul li').first();
				latestPost.css('background', '#fffbe5');
				setTimeout(function () {
					latestPost.css('background', 'none');
				}, 1000);
			}

			return false;
		} );

		$('#publish').click( function() { act.val( 'post-quickpress-publish' ); } );

		$('#title, #tags-input, #content').each( function() {
			var input = $(this), prompt = $('#' + this.id + '-prompt-text');

			if ( '' === this.value ) {
				prompt.removeClass('screen-reader-text');
			}

			prompt.click( function() {
				$(this).addClass('screen-reader-text');
				input.focus();
			});

			input.blur( function() {
				if ( '' === this.value ) {
					prompt.removeClass('screen-reader-text');
				}
			});

			input.focus( function() {
				prompt.addClass('screen-reader-text');
			});
		});

		$('#quick-press').on( 'click focusin', function() {
			$(this).addClass('quickpress-open');
			$('#description-wrap, p.submit').slideDown(200);
			wpActiveEditor = 'content';
		});
	};
	quickPressLoad();

	// Activity Widget
	$( '.show-more a' ).on( 'click', function(e) {
		$( this ).fadeOut().closest('.activity-block').find( 'li.hidden' ).fadeIn().removeClass( 'hidden' );
		e.preventDefault();
	});

	// Dashboard columns
	jQuery(document).ready(function () {
		// Update main column count on load
		updateColumnCount();
	});

	jQuery(window).resize( _.debounce( function(){
		updateColumnCount();
	}, 30) );

	function updateColumnCount() {
		var cols = 1,
			windowWidth = parseInt(jQuery(window).width(), 10);

		if (799 < windowWidth && 1299 > windowWidth) {
			cols = 2;
		}

		if (1300 < windowWidth && 1799 > windowWidth) {
			cols = 3;
		}

		if (1800 < windowWidth) {
			cols = 4;
		}
		jQuery('.metabox-holder').attr('class', jQuery('.metabox-holder').attr('class').replace(/columns-\d+/, 'columns-' + cols));
	}

} );
