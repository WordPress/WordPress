(function($) {
	postboxes = {
		add_postbox_toggles : function(page,args) {
			$('.postbox h3, .postbox .handlediv').click( function() {
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
					if ( $.isFunction( postboxes.pbshow ) )
						postboxes.pbshow( box );

				} else {
					jQuery('#' + box).hide();
					if ( $.isFunction( postboxes.pbhide ) )
						postboxes.pbhide( box );

				}
				postboxes.save_state(page);
			} );

			this.expandSidebar();
			this.init(page,args);
		},

		expandSidebar : function(doIt) {
			if ( doIt || $('#side-sortables > .postbox:visible').length ) {
				if ( ! $('#post-body').hasClass('has-sidebar') ) {
					$('#post-body').addClass('has-sidebar');
					var h = Math.min( $('#post-body').height(), 300 );
					$('#side-sortables').css({'minHeight':h+'px','height':'auto'});
				}
			} else {
				$('#post-body').removeClass('has-sidebar');
				$('#side-sortables').css({'minHeight':'0'});
				if ( $.browser.msie && $.browser.version.charAt(0) == 7 )
					$('#side-sortables').css({'height':'0'});
			}
		},

		init : function(page, args) {
			$.extend( this, args || {} );
			$('#wpbody-content').css('overflow','hidden');
			$('.meta-box-sortables').sortable( {
				placeholder: 'sortable-placeholder',
				connectWith: [ '.meta-box-sortables' ],
				items: '> .postbox',
				handle: '.hndle',
				distance: 2,
				tolerance: 'pointer',
				toleranceMove: 'tolerance',
				sort: function(e,ui) {
					if ( $(document).width() - e.clientX < 300 ) {
						if ( ! $('#post-body').hasClass('has-sidebar') ) {
							var pos = $('#side-sortables').offset();

							$('#side-sortables').append(ui.item)
							$(ui.placeholder).css({'top':pos.top,'left':pos.left}).width($(ui.item).width())
							postboxes.expandSidebar(1);
						}
					}
				},
				stop: function() {
					var postVars = {
						action: 'meta-box-order',
						_ajax_nonce: $('#meta-box-order-nonce').val(),
						page: page
					}
					$('.meta-box-sortables').each( function() {
						postVars["order[" + this.id.split('-')[0] + "]"] = $(this).sortable( 'toArray' ).join(',');
					} );
					$.post( postboxL10n.requestFile, postVars, function() {
						postboxes.expandSidebar();
					} );
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
			postboxes.expandSidebar();
		},

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

}(jQuery));
