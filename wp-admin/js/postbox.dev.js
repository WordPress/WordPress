var postboxes, wp_auto_columns, wpAutoColumns = false;

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
				var num = $(this).val(), i, el, p = $('#poststuff');

				if ( num === '0' ) {
					if ( typeof(wp_auto_columns) == 'function' ) {
						wpAutoColumns = true;
						wp_auto_columns();
					}
				} else {
					if ( p.length ) { // write pages
						if ( num == 2 ) {
							p.addClass('has-right-sidebar');
							$('#side-info-column').append( $('#side-sortables') );
							$(document.body).removeClass('responsive');
						} else if ( num == 1 ) {
							p.removeClass('has-right-sidebar');
							$('#normal-sortables').before( $('#side-sortables') );
							$(document.body).removeClass('responsive');
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
						$('.postbox-container:visible').css('width', 100/num + '%');
					}
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

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

	$(document).ready(function(){

		// responsive admin
		wpAutoColumns = $('#wp_auto_columns').prop('checked');

		wp_auto_columns = function() {
			var w = $(window).width(), pb, dw, num = 1;
			
			if ( !wpAutoColumns )
				return;

			if ( w < 681 )
				$(document.body).addClass('folded');

			if ( w > 680 && getUserSetting('mfold') != 'f' )
				$(document.body).removeClass('folded');

			if ( adminpage == 'post-php' ) {
				pb = $('#post-body').width();

				if ( pb < 801 ) {
					$('#poststuff').removeClass('has-right-sidebar');
					$('#normal-sortables').before( $('#side-sortables') );
				}

				if ( pb > 800 &&  pb < 1150 ) {
					$('#poststuff').addClass('has-right-sidebar');
					$('#side-info-column').append( $('#side-sortables') );
					$(document.body).removeClass('wide-screen');
				}

				if ( pb > 1149 ) {
					$(document.body).addClass('wide-screen');
				}

			} else if ( adminpage == 'index-php' ) {
				dw = $('#dashboard-widgets').width();

				if ( dw < 801 ) {
					$('#postbox-container-2').hide();
					$('#normal-sortables').after( $('#side-sortables') );
					num = 1;
				}

				if ( dw > 800 && dw < 1201 ) {
					$('#postbox-container-2').show().append( $('#side-sortables') );
					$('#postbox-container-3').hide();
					$('#side-sortables').after( $('#column3-sortables') );
					num = 2;
				}

				if ( dw > 1200 && dw < 1601 ) {
					$('#postbox-container-3').show().append( $('#column3-sortables') );
					$('#postbox-container-4').hide();
					$('#column3-sortables').after( $('#column4-sortables') );
					num = 3;
				}

				if ( dw > 1600 ) {
					$('#postbox-container-4').show().append( $('#column4-sortables') );
					num = 4;
				}

				$('.postbox-container:visible').css('width', 100/num + '%');
			}
		}

		$(window).resize(function(){ wp_auto_columns(); });
		wp_auto_columns();
	});

}(jQuery));
