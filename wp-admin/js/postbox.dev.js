var postboxes;

(function($) {
	postboxes = {
		add_postbox_toggles : function(page,args) {
			this.init(page,args);
			$('.postbox h3, .postbox .handlediv').click( function() {
				var p = $(this).parent('.postbox'), id = p.attr('id');
				if ( 'dashboard_browser_nag' == id )
					return;

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
			$('.postbox a.dismiss').click( function(e) {
				var hide_id = $(this).parents('.postbox').attr('id') + '-hide';
				$( '#' + hide_id ).prop('checked', false).triggerHandler('click');
				return false;
			} );
			$('.hide-postbox-tog').click( function() {
				var box = $(this).val();

				if ( $(this).prop('checked') ) {
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
				var num = $(this).val(), ps = $('#poststuff'), wrap = $('.wrap').first();

				if ( num == 'auto' ) {
					setUserSetting('responsive', '1');
					$(document.body).addClass('responsive');
					$(window).bind('resize.responsive', function(){ postboxes.auto_columns(); });

					if ( ps.length )
						wrap.removeClass('columns-1').removeClass('columns-2');

					postboxes.auto_columns();

				} else {
					num = parseInt(num, 10);
					deleteUserSetting('responsive');
					$(document.body).removeClass('responsive');

					if ( ps.length ) { // write pages

						if ( num == 2 ) {
							wrap.removeClass('columns-1').addClass('columns-2');
							ps.addClass('has-right-sidebar');

							if ( !$('#side-info-column #side-sortables').length )
								$('#side-info-column').append( $('#side-sortables') );

						} else if ( num == 1 ) {
							wrap.removeClass('columns-2').addClass('columns-1');
							ps.removeClass('has-right-sidebar');
							$('#normal-sortables').before( $('#side-sortables') );
						}

					} else { // dashboard
						postboxes._dash_columns(num);
					}
				}
				postboxes.save_order(page);
			});
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
				stop: function(e,ui) {
					if ( $(this).find('#dashboard_browser_nag').is(':visible') && 'dashboard_browser_nag' != this.firstChild.id ) {
						$(this).sortable('cancel');
						return;
					}

					postboxes.save_order(page);
					ui.item.parent().removeClass('temp-border');
				},
				receive: function(e,ui) {
					if ( 'dashboard_browser_nag' == ui.item[0].id )
						$(ui.sender).sortable('cancel');
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
		
		auto_columns : function() { // responsive admin
			var pb, dw;

			if ( !$(document.body).hasClass('responsive') )
				return;

			if ( adminpage == 'post-php' ) {
				pb = $('#post-body').width();

				if ( pb < 800 ) {
					$('#poststuff').removeClass('has-right-sidebar');
					$('#normal-sortables').before( $('#side-sortables') );
				}

				if ( pb >= 800 &&  pb < 1150 ) {
					$('#poststuff').addClass('has-right-sidebar');
					$('#side-info-column').append( $('#side-sortables') );
					$(document.body).removeClass('wide-window');
				}

				if ( pb >= 1150 ) {
					$(document.body).addClass('wide-window');
				}

			} else if ( adminpage == 'index-php' ) {
				dw = $('#dashboard-widgets-wrap').width();

				if ( dw < 700 ) {
					this._dash_columns(1)
				}

				if ( dw >= 700 && dw < 1100 ) {
					this._dash_columns(2)
				}

				if ( dw >= 1100 && dw < 1500 ) {
					this._dash_columns(3)
				}

				if ( dw >= 1500 ) {
					this._dash_columns(4)
				}
			}

		},

		_dash_columns : function(n) {

			switch (n) {
				case 1:
					$('#postbox-container-1').append( $('#side-sortables, #column3-sortables, #column4-sortables') );
					$('#postbox-container-2, #postbox-container-3, #postbox-container-4').hide();
					break
				case 2:
					$('#postbox-container-2').append( $('#side-sortables, #column3-sortables, #column4-sortables') ).show();
					$('#postbox-container-3, #postbox-container-4').hide();
					break
				case 3:
					$('#postbox-container-2').append( $('#side-sortables') ).show();
					$('#postbox-container-3').append( $('#column3-sortables, #column4-sortables') ).show();
					$('#postbox-container-4').hide();
					break
				case 4:
					$('#postbox-container-2').append( $('#side-sortables') ).show();
					$('#postbox-container-3').append( $('#column3-sortables') ).show();
					$('#postbox-container-4').append( $('#column4-sortables') ).show();
					break
				default:
					return;
			}

			$('.postbox-container:visible').css('width', 100/n + '%');
		},

		_colname : function(n) {
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
		},

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

	$(document).ready(function(){
		if ( $('#wp_auto_columns').prop('checked') ) {
			setUserSetting('responsive', '1');
			$(document.body).addClass('responsive');
		}
		
		if ( $(document.body).hasClass('responsive') ) {
			$(window).bind('resize.responsive', function(){ postboxes.auto_columns(); });
			postboxes.auto_columns();
		}
	});

}(jQuery));
