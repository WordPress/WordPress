/* global ajaxurl */

var postboxes;

(function($) {
	var $document = $( document );

	postboxes = {
		handle_click : function () {
			var $el = $( this ),
				p = $el.parent( '.postbox' ),
				id = p.attr( 'id' ),
				ariaExpandedValue;

			if ( 'dashboard_browser_nag' === id ) {
				return;
			}

			p.toggleClass( 'closed' );

			ariaExpandedValue = ! p.hasClass( 'closed' );

			if ( $el.hasClass( 'handlediv' ) ) {
				// The handle button was clicked.
				$el.attr( 'aria-expanded', ariaExpandedValue );
			} else {
				// The handle heading was clicked.
				$el.closest( '.postbox' ).find( 'button.handlediv' )
					.attr( 'aria-expanded', ariaExpandedValue );
			}

			if ( postboxes.page !== 'press-this' ) {
				postboxes.save_state( postboxes.page );
			}

			if ( id ) {
				if ( !p.hasClass('closed') && $.isFunction( postboxes.pbshow ) ) {
					postboxes.pbshow( id );
				} else if ( p.hasClass('closed') && $.isFunction( postboxes.pbhide ) ) {
					postboxes.pbhide( id );
				}
			}

			$document.trigger( 'postbox-toggled', p );
		},

		add_postbox_toggles : function (page, args) {
			var $handles = $( '.postbox .hndle, .postbox .handlediv' );

			this.page = page;
			this.init( page, args );

			$handles.on( 'click.postboxes', this.handle_click );

			$('.postbox .hndle a').click( function(e) {
				e.stopPropagation();
			});

			$( '.postbox a.dismiss' ).on( 'click.postboxes', function( e ) {
				var hide_id = $(this).parents('.postbox').attr('id') + '-hide';
				e.preventDefault();
				$( '#' + hide_id ).prop('checked', false).triggerHandler('click');
			});

			$('.hide-postbox-tog').bind('click.postboxes', function() {
				var $el = $(this),
					boxId = $el.val(),
					$postbox = $( '#' + boxId );

				if ( $el.prop( 'checked' ) ) {
					$postbox.show();
					if ( $.isFunction( postboxes.pbshow ) ) {
						postboxes.pbshow( boxId );
					}
				} else {
					$postbox.hide();
					if ( $.isFunction( postboxes.pbhide ) ) {
						postboxes.pbhide( boxId );
					}
				}
				postboxes.save_state( page );
				postboxes._mark_area();
				$document.trigger( 'postbox-toggled', $postbox );
			});

			$('.columns-prefs input[type="radio"]').bind('click.postboxes', function(){
				var n = parseInt($(this).val(), 10);

				if ( n ) {
					postboxes._pb_edit(n);
					postboxes.save_order( page );
				}
			});
		},

		init : function(page, args) {
			var isMobile = $( document.body ).hasClass( 'mobile' ),
				$handleButtons = $( '.postbox .handlediv' );

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
				helper: function( event, element ) {
					// `helper: 'clone'` is equilavalent to `return element.clone();`
					// Cloning a checked radio and then inserting that clone next to the original
					// radio unchecks the original radio (since only one of the two can be checked).
					// We get around this by renaming the helper's inputs' name attributes so that,
					// when the helper is inserted into the DOM for the sortable, no radios are
					// duplicated, and no original radio gets unchecked.
					return element.clone()
						.find( ':input' )
							.attr( 'name', function( i, currentName ) {
								return 'sort_' + parseInt( Math.random() * 100000, 10 ).toString() + '_' + currentName;
							} )
						.end();
				},
				opacity: 0.65,
				stop: function() {
					var $el = $( this );

					if ( $el.find( '#dashboard_browser_nag' ).is( ':visible' ) && 'dashboard_browser_nag' != this.firstChild.id ) {
						$el.sortable('cancel');
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

			// Set the handle buttons `aria-expanded` attribute initial value on page load.
			$handleButtons.each( function () {
				var $el = $( this );
				$el.attr( 'aria-expanded', ! $el.parent( '.postbox' ).hasClass( 'closed' ) );
			});
		},

		save_state : function(page) {
			var closed, hidden;

			// Return on the nav-menus.php screen, see #35112.
			if ( 'nav-menus' === page ) {
				return;
			}

			closed = $( '.postbox' ).filter( '.closed' ).map( function() { return this.id; } ).get().join( ',' );
			hidden = $( '.postbox' ).filter( ':hidden' ).map( function() { return this.id; } ).get().join( ',' );

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

				if ( visible == 1 || t.children('.postbox:visible').length ) {
					t.removeClass('empty-container');
				}
				else {
					t.addClass('empty-container');
					t.attr('data-emptyString', postBoxL10n.postBoxEmptyString);
				}
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
