(function($) {
	postboxes = {
		add_postbox_toggles : function(page,args) {
			$('.postbox h3').click( function() {
				$($(this).parent().get(0)).toggleClass('closed');
				postboxes.save_state(page);
			} );
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

			this.makeItTall();
			this.init(page,args);
		},
		
		makeItTall : function() {
			var t = $('#make-it-tall').remove();

			if ( t.length < 1 )
				t = $.browser.mozilla ? '<div id="make-it-tall" style="margin-bottom: -2000px; padding-bottom: 2001px"></div>' : '<div id="make-it-tall"> <br /> <br /></div>';
			
			$('#side-sortables').append(t);
			
			if ( $('#side-sortables').children().length > 1 )
				$('#side-sortables').css({'minHeight':'300px'});

			$('#wpbody-content').css( 'overflow', 'hidden' );
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
				placeholder: 'sortable-placeholder',
				connectWith: [ '.meta-box-sortables' ],
				items: '> .postbox',
				handle: '.hndle',
				distance: 2,
				tolerance: 'pointer',
				receive: function() {
					postboxes.makeItTall();
				},
				stop: function() {
					if ( $('#side-sortables').children().length < 2 )
						$('#side-sortables').css({'minHeight':''});

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
