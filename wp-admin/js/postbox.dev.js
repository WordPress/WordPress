var postboxes;
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
			
			$('.columns-prefs input[type="radio"]').click(function(){
				var num = $(this).val(), i, el;
				
				if ( num ) {
					for ( i = 4; ( i > num && i > 1 ); i-- ) {
						$('#' + colname(i-1) + '-sortables').append($('#' + colname(i) + '-sortables').children('.postbox'));
						$('#' + colname(i) + '-sortables').parent().hide();
					}
					for ( i = 1; i <= num; i++ ) {
						el = $('#' + colname(i) + '-sortables')
						if ( el.parent().is(':hidden') )
							el.addClass('temp-border').parent().show();
					}
				}
				$('.postbox-container:visible').css('width', 98/num + '%');
				postboxes.save_order(page);
			});
			
			function colname(n) {
				switch (n) {
					case 1:
						return 'normal';
						break
					case 2:
						return 'side';
						break
					case 3:
						return 'column3';
						break
					case 4:
						return 'column4';
						break
					default:
						return '';
				}
			}

			this.expandSidebar();
			this.init(page,args);
		},

		expandSidebar : function(doIt) {
			if ( ! $('#side-info-column').length )
				return;
			
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
			var fixed = $('#dashboard-widgets').length;
			
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
					if ( fixed )
						return;

					if ( $(document).width() - e.clientX < 300 ) {
						if ( ! $('#post-body').hasClass('has-sidebar') ) {
							var pos = $('#side-sortables').offset();

							$('#side-sortables').append(ui.item)
							$(ui.placeholder).css({'top':pos.top,'left':pos.left}).width($(ui.item).width())
							postboxes.expandSidebar(1);
						}
					}
				},
				stop: function(e,ui) {
					postboxes.save_order(page);
					ui.item.parent().removeClass('temp-border');
					postboxes.expandSidebar();
				}
			} );
		},

		save_state : function(page) {
			var closed = $('.postbox').filter('.closed').map(function() { return this.id; }).get().join(','),
			hidden = $('.postbox').filter(':hidden').map(function() { return this.id; }).get().join(',');
			$.post(postboxL10n.requestFile, {
				action: 'closed-postboxes',
				closed: closed,
				hidden: hidden,
				closedpostboxesnonce: jQuery('#closedpostboxesnonce').val(),
				page: page
			});
			postboxes.expandSidebar();
		},
		
		save_order : function(page) {
			var postVars, page_columns = $('.columns-prefs input:checked').val() || 0;
			postVars = {
				action: 'meta-box-order',
				_ajax_nonce: $('#meta-box-order-nonce').val(),
				page_columns: page_columns,
				page: page
			}
			$('.meta-box-sortables').each( function() {
				postVars["order[" + this.id.split('-')[0] + "]"] = $(this).sortable( 'toArray' ).join(',');
			} );
			$.post( postboxL10n.requestFile, postVars );
		},

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

}(jQuery));
