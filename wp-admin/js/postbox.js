/* global ajaxurl */

var postboxes;

(function($) {
	var $document = $( document );

	postboxes = {
		add_postbox_toggles : function(page, args) {
			var self = this;

			self.init(page, args);

			$('.postbox .hndle, .postbox .handlediv').bind('click.postboxes', function( e ) {
				var p = $(this).parent('.postbox'), id = p.attr('id');

				if ( 'dashboard_browser_nag' == id )
					return;

				e.preventDefault();

				p.toggleClass( 'closed' );
				$(this).attr( 'aria-expanded', ! p.hasClass( 'closed' ) );

				if ( page != 'press-this' )
					self.save_state(page);

				if ( id ) {
					if ( !p.hasClass('closed') && $.isFunction(postboxes.pbshow) )
						self.pbshow(id);
					else if ( p.hasClass('closed') && $.isFunction(postboxes.pbhide) )
						self.pbhide(id);
				}

				$document.trigger( 'postbox-toggled', p );
			});

			$('.postbox .hndle a').click( function(e) {
				e.stopPropagation();
			});

			$( '.postbox a.dismiss' ).bind( 'click.postboxes', function() {
				var hide_id = $(this).parents('.postbox').attr('id') + '-hide';
				$( '#' + hide_id ).prop('checked', false).triggerHandler('click');
				return false;
			});

			$('.hide-postbox-tog').bind('click.postboxes', function() {
				var boxId = $(this).val(),
					$postbox = $( '#' + boxId );

				if ( $(this).prop('checked') ) {
					$postbox.show();
					if ( $.isFunction( postboxes.pbshow ) )
						self.pbshow( boxId );
				} else {
					$postbox.hide();
					if ( $.isFunction( postboxes.pbhide ) )
						self.pbhide( boxId );
				}
				self.save_state(page);
				self._mark_area();
				$document.trigger( 'postbox-toggled', $postbox );
			});

			$('.columns-prefs input[type="radio"]').bind('click.postboxes', function(){
				var n = parseInt($(this).val(), 10);

				if ( n ) {
					self._pb_edit(n);
					self.save_order(page);
				}
			});
		},

		init : function(page, args) {
			var isMobile = $(document.body).hasClass('mobile');

			$.extend( this, args || {} );
			$('#wpbody-content').css('overflow','hidden');
			$('.meta-box-sortables').sortable({
				placeholder: 'sortable-placeholder',
				connectWith: '.meta-box-sortables',
				items: '.postbox',
				handle: '.hndle',
				cursor: 'move',
				delay: ( isMobile ? 200 : 0 ),
				distance: 2,
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				stop: function() {
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

			if ( isMobile ) {
				$(document.body).bind('orientationchange.postboxes', function(){ postboxes._pb_change(); });
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
			};
			$('.meta-box-sortables').each( function() {
				postVars[ 'order[' + this.id.split( '-' )[0] + ']' ] = $( this ).sortable( 'toArray' ).join( ',' );
			} );
			$.post( ajaxurl, postVars );
		},

		_mark_area : function() {
			var visible = $('div.postbox:visible').length, side = $('#post-body #side-sortables');

			$( '#dashboard-widgets .meta-box-sortables:visible' ).each( function() {
				var t = $(this);

				if ( visible == 1 || t.children('.postbox:visible').length )
					t.removeClass('empty-container');
				else
					t.addClass('empty-container');
			});

			if ( side.length ) {
				if ( side.children('.postbox:visible').length )
					side.removeClass('empty-container');
				else if ( $('#postbox-container-1').css('width') == '280px' )
					side.addClass('empty-container');
			}
		},

		_pb_edit : function(n) {
			var el = $('.metabox-holder').get(0);

			if ( el ) {
				el.className = el.className.replace(/columns-\d+/, 'columns-' + n);
			}

			$( document ).trigger( 'postboxes-columnchange' );
		},

		_pb_change : function() {
			var check = $( 'label.columns-prefs-1 input[type="radio"]' );

			switch ( window.orientation ) {
				case 90:
				case -90:
					if ( !check.length || !check.is(':checked') )
						this._pb_edit(2);
					break;
				case 0:
				case 180:
					if ( $('#poststuff').length ) {
						this._pb_edit(1);
					} else {
						if ( !check.length || !check.is(':checked') )
							this._pb_edit(2);
					}
					break;
			}
		},

		/* Callbacks */
		pbshow : false,

		pbhide : false
	};

}(jQuery));
