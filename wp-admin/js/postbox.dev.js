var postboxes, is_iPad = navigator.userAgent.match(/iPad/);

(function($) {
	postboxes = {
		add_postbox_toggles : function(page, args) {
			this.init(page, args);

			$('.postbox h3, .postbox .handlediv').bind('click.postboxes', function() {
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
			});

			$('.postbox h3 a').click( function(e) {
				e.stopPropagation();
			});

			$('.postbox a.dismiss').bind('click.postboxes', function(e) {
				var hide_id = $(this).parents('.postbox').attr('id') + '-hide';
				$( '#' + hide_id ).prop('checked', false).triggerHandler('click');
				return false;
			});

			$('.hide-postbox-tog').bind('click.postboxes', function() {
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
				postboxes._mark_area();
			});

			$('.columns-prefs input[type="radio"]').bind('click.postboxes', function(){
				var n = parseInt($(this).val(), 10), pb = postboxes;

				if ( n ) {
					pb._pb_edit(n);
					pb.save_order(page);
				}
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
				},
				receive: function(e,ui) {
					if ( 'dashboard_browser_nag' == ui.item[0].id )
						$(ui.sender).sortable('cancel');

					postboxes._mark_area();
				}
			});

			if ( navigator.userAgent.match(/iPad/) ) {
				$(document.body).bind('orientationchange', function(){ postboxes._pb_change(); });
				this._pb_change();
			}

			this._mark_area();
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

		_mark_area : function() {
			$('#side-info-column .meta-box-sortables:visible, #dashboard-widgets .meta-box-sortables:visible').each(function(n, el){
				var t = $(this);

				if ( !t.children('.postbox:visible').length )
					t.addClass('empty-container');
				else
					t.removeClass('empty-container');
			});
		},

		_pb_edit : function(n) {
			var ps = $('#poststuff'), i, el, done, pb = postboxes, visible = $('.postbox-container:visible').length;

			if ( n == visible )
				return;

			if ( ps.length ) {
				if ( n == 2 ) {
					$('.wrap').removeClass('columns-1').addClass('columns-2');
					ps.addClass('has-right-sidebar');

					if ( !$('#side-info-column #side-sortables').length )
						$('#side-info-column').append( $('#side-sortables') );

				} else if ( n == 1 ) {
					$('.wrap').removeClass('columns-2').addClass('columns-1');
					ps.removeClass('has-right-sidebar');

					if ( !$('#post-body-content #side-sortables').length )
						$('#normal-sortables').before( $('#side-sortables') );
				}
			} else {
				for ( i = 4; ( i > n && i > 1 ); i-- ) {
					el = $('#' + postboxes._colname(i) + '-sortables');
					$('#' + postboxes._colname(i-1) + '-sortables').append(el.children('.postbox'));
					el.parent().hide();
				}

				for ( i = n; i > 0; i-- ) {
					el = $('#' + postboxes._colname(i) + '-sortables');
					done = false;

					if ( el.parent().is(':hidden') ) {
						switch ( i ) {
							case 4:
								done = pb._move_one( el, $('.postbox:visible', $('#column3-sortables')) );
							case 3:
								if ( !done )
									done = pb._move_one( el, $('.postbox:visible', $('#side-sortables')) );
							case 2:
								if ( !done )
									done = pb._move_one( el, $('.postbox:visible', $('#normal-sortables')) );
							default:
								if ( !done )
									el.addClass('empty-container')
						}

						el.parent().show();
					}
				}

				$('.postbox-container:visible').css('width', 100/n + '%');
			}
		},

		_pb_change : function() {
			switch ( window.orientation ) {
				case 90:
				case -90:
					this._pb_edit(2);
					break;
				case 0:
				case 180:
					if ( $('#poststuff').length )
						this._pb_edit(1);
					else
						this._pb_edit(2);
					break;
			}
		},

		_move_one : function(el, move) {
			if ( move.length > 1 ) {
				el.append( move.last() );
				return true;
			}
			return false;
		},

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

}(jQuery));
