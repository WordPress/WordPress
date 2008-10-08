(function($) {
	postboxes = {
		add_postbox_toggles : function(page,args) {
			$('.postbox h3').before('<a class="togbox">+</a> ');
			$('.postbox h3, .postbox a.togbox').click( function() {
				$($(this).parent().get(0)).toggleClass('closed');
				postboxes.save_state(page);
			});
			$('.postbox h3 a').click( function(e) {
				e.stopPropagation();
			} );

			$('.hide-postbox-tog').click( function() {
				var box = jQuery(this).val();
				if ( jQuery(this).attr('checked') ) {
					jQuery('#' + box).show();
					if ( $.isFunction( postboxes.onShow ) ) {
						postboxes.onShow( box );
					}
				} else {
					jQuery('#' + box).hide();
				}
				postboxes.save_state(page);
			} );

			if ( $.browser.msie ) {
				$('#side-sortables').append( '<div id="make-it-tall"></div>' );
			} else {
				$('#side-sortables').append( '<div id="make-it-tall" style="margin-bottom: -2000px; padding-bottom: 2001px"></div>' );
			}
			$('#wpbody-content').css( 'overflow', 'hidden' );
			
			this.init(page,args);
		},

		expandSidebar : function( doIt ) {
			if ( doIt || $.trim( $( '#side-info-column' ).text() ) ) {
				$( '#post-body' ).addClass( 'has-sidebar' );
				$( '#side-sortables' ).css('z-index','0');
			} else {
				$( '#post-body' ).removeClass( 'has-sidebar' );
				$( '#side-sortables' ).css('z-index','-1');
			}
		},

		init : function(page,args) {
			$.extend( this, args || {} );
			jQuery('.meta-box-sortables').sortable( {
				connectWith: [ '.meta-box-sortables' ],
				items: '> .postbox',
				handle: '.hndle',
				distance: 2,
				stop: function() {
					if ( 'side-sortables' == this.id ) { // doing this with jQuery doesn't work for some reason: make-it-tall gets duplicated
						var makeItTall = document.getElementById( 'make-it-tall' );
						var sideSort = makeItTall.parentNode;
						sideSort.removeChild( makeItTall );
						sideSort.appendChild( makeItTall );
						
					}
					var postVars = {
						action: 'meta-box-order',
						_ajax_nonce: jQuery('#meta-box-order-nonce').val(),
						page: page
					}
					jQuery('.meta-box-sortables').each( function() {
						postVars["order[" + this.id.split('-')[0] + "]"] = jQuery(this).sortable( 'toArray' ).join(',');
					} );
					jQuery.post( postboxL10n.requestFile, postVars, function() {
						postboxes.expandSidebar();
					} );
				},
				over: function(e, ui) {
					if ( !ui.element.is( '#side-sortables' ) )
						return;
					postboxes.expandSidebar( true );
				}
			} );
		},

		save_state : function(page) {
			var closed = $('.postbox').filter('.closed').map(function() { return this.id; }).get().join(',');
			var hidden = $('.postbox').filter(':hidden').map(function() { return this.id; }).get().join(',');
			$.post(postboxL10n.requestFile, {
				action: 'closed-postboxes',
				closed: closed,
				hidden: hidden,
				closedpostboxesnonce: jQuery('#closedpostboxesnonce').val(),
				page: page
			});
		},

		/* Callbacks */
		onShow : false
	};

	$(document).ready(function(){postboxes.expandSidebar();});

}(jQuery));
