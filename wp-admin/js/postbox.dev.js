var postboxes;
(function($) {
	postboxes = {
		add_postbox_toggles : function(page,args) {
			this.init(page,args);
			$('.postbox h3, .postbox .handlediv').click( function() {
				var p = $(this).parent('.postbox'), id = p.attr('id');
				p.toggleClass('closed');
				postboxes.save_state(page);
				if ( id ) {
					if ( !p.hasClass('closed') && $.isFunction(postboxes.pbshow) )
						postboxes.pbshow(id);
					else if ( p.hasClass('closed') && $.isFunction(postboxes.pbhide) )
						postboxes.pbhide(id);
				}
			} );
			$('.postbox h3 a').click( function(e) {
				e.stopPropagation();
			} );
			$('.hide-postbox-tog').click( function() {
				var box = $(this).val();
				if ( $(this).attr('checked') ) {
					$('#' + box).show();
					if ( $.isFunction( postboxes.pbshow ) )
						postboxes.pbshow( box );
				} else {
					$('#' + box).hide();
					if ( $.isFunction( postboxes.pbhide ) )
						postboxes.pbhide( box );
				}
				postboxes.save_state(page);
			} );
			$('.columns-prefs input[type="radio"]').click(function(){
				var num = $(this).val(), i, el, p = $('#poststuff');

				if ( p.length ) { // write pages
					if ( num == 2 ) {
						p.addClass('has-right-sidebar');
						$('#side-sortables').addClass('temp-border');
					} else if ( num == 1 ) {
						p.removeClass('has-right-sidebar');
						$('#normal-sortables').append($('#side-sortables').children('.postbox'));
					}
				} else { // dashboard
					for ( i = 4; ( i > num && i > 1 ); i-- ) {
						el = $('#' + colname(i) + '-sortables');
						$('#' + colname(i-1) + '-sortables').append(el.children('.postbox'));
						el.parent().hide();
					}
					for ( i = 1; i <= num; i++ ) {
						el = $('#' + colname(i) + '-sortables');
						if ( el.parent().is(':hidden') )
							el.addClass('temp-border').parent().show();
					}
					$('.postbox-container:visible').css('width', 98/num + '%');
				}
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
		},

		init : function(page, args) {
			$.extend( this, args || {} );
			$('#wpbody-content').css('overflow','hidden');
			$('.meta-box-sortables').sortable({
				placeholder: 'sortable-placeholder',
				connectWith: '.meta-box-sortables',
				items: '.postbox',
				handle: '.hndle',
				cursor: 'move',
				distance: 2,
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				start: function(e,ui) {
					$('body').css({
						WebkitUserSelect: 'none',
						KhtmlUserSelect: 'none'
					});
					/*
					if ( $.browser.msie )
						return;
					ui.item.addClass('noclick');
					*/
				},
				stop: function(e,ui) {
					postboxes.save_order(page);
					ui.item.parent().removeClass('temp-border');
					$('body').css({
						WebkitUserSelect: '',
						KhtmlUserSelect: ''
					});
				}
			});
		},

		save_state : function(page) {
			var closed = $('.postbox').filter('.closed').map(function() { return this.id; }).get().join(','),
			hidden = $('.postbox').filter(':hidden').map(function() { return this.id; }).get().join(',');
			$.post(ajaxurl, {
				action: 'closed-postboxes',
				closed: closed,
				hidden: hidden,
				closedpostboxesnonce: jQuery('#closedpostboxesnonce').val(),
				page: page
			});
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
			$.post( ajaxurl, postVars );
		},

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

}(jQuery));
