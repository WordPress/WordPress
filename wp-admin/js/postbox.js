/**
 * Contains the postboxes logic, opening and closing postboxes, reordering and saving
 * the state and ordering to the database.
 *
 * @since 2.5.0
 * @requires jQuery
 * @output wp-admin/js/postbox.js
 */

/* global ajaxurl, postboxes */

(function($) {
	var $document = $( document ),
		__ = wp.i18n.__;

	/**
	 * This object contains all function to handle the behaviour of the post boxes. The post boxes are the boxes you see
	 * around the content on the edit page.
	 *
	 * @since 2.7.0
	 *
	 * @namespace postboxes
	 *
	 * @type {Object}
	 */
	window.postboxes = {

		/**
		 * Handles a click on either the postbox heading or the postbox open/close icon.
		 *
		 * Opens or closes the postbox. Expects `this` to equal the clicked element.
		 * Calls postboxes.pbshow if the postbox has been opened, calls postboxes.pbhide
		 * if the postbox has been closed.
		 *
		 * @since 4.4.0
		 *
		 * @memberof postboxes
		 *
		 * @fires postboxes#postbox-toggled
		 *
		 * @return {void}
		 */
		handle_click : function () {
			var $el = $( this ),
				p = $el.closest( '.postbox' ),
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

			/**
			 * Fires when a postbox has been opened or closed.
			 *
			 * Contains a jQuery object with the relevant postbox element.
			 *
			 * @since 4.0.0
			 * @ignore
			 *
			 * @event postboxes#postbox-toggled
			 * @type {Object}
			 */
			$document.trigger( 'postbox-toggled', p );
		},

		/**
		 * Handles clicks on the move up/down buttons.
		 *
		 * @since 5.5.0
		 *
		 * @return {void}
		 */
		handleOrder: function() {
			var button = $( this ),
				postbox = button.closest( '.postbox' ),
				postboxId = postbox.attr( 'id' ),
				postboxesWithinSortables = postbox.closest( '.meta-box-sortables' ).find( '.postbox:visible' ),
				postboxesWithinSortablesCount = postboxesWithinSortables.length,
				postboxWithinSortablesIndex = postboxesWithinSortables.index( postbox ),
				firstOrLastPositionMessage;

			if ( 'dashboard_browser_nag' === postboxId ) {
				return;
			}

			// If on the first or last position, do nothing and send an audible message to screen reader users.
			if ( 'true' === button.attr( 'aria-disabled' ) ) {
				firstOrLastPositionMessage = button.hasClass( 'handle-order-higher' ) ?
					__( 'The box is on the first position' ) :
					__( 'The box is on the last position' );

				wp.a11y.speak( firstOrLastPositionMessage );
				return;
			}

			// Move a postbox up.
			if ( button.hasClass( 'handle-order-higher' ) ) {
				// If the box is first within a sortable area, move it to the previous sortable area.
				if ( 0 === postboxWithinSortablesIndex ) {
					postboxes.handleOrderBetweenSortables( 'previous', button, postbox );
					return;
				}

				postbox.prevAll( '.postbox:visible' ).eq( 0 ).before( postbox );
				button.focus();
				postboxes.updateOrderButtonsProperties();
				postboxes.save_order( postboxes.page );
			}

			// Move a postbox down.
			if ( button.hasClass( 'handle-order-lower' ) ) {
				// If the box is last within a sortable area, move it to the next sortable area.
				if ( postboxWithinSortablesIndex + 1 === postboxesWithinSortablesCount ) {
					postboxes.handleOrderBetweenSortables( 'next', button, postbox );
					return;
				}

				postbox.nextAll( '.postbox:visible' ).eq( 0 ).after( postbox );
				button.focus();
				postboxes.updateOrderButtonsProperties();
				postboxes.save_order( postboxes.page );
			}

		},

		/**
		 * Moves postboxes between the sortables areas.
		 *
		 * @since 5.5.0
		 *
		 * @param {string} position The "previous" or "next" sortables area.
		 * @param {Object} button   The jQuery object representing the button that was clicked.
		 * @param {Object} postbox  The jQuery object representing the postbox to be moved.
		 *
		 * @return {void}
		 */
		handleOrderBetweenSortables: function( position, button, postbox ) {
			var closestSortablesId = button.closest( '.meta-box-sortables' ).attr( 'id' ),
				sortablesIds = [],
				sortablesIndex,
				detachedPostbox;

			// Get the list of sortables within the page.
			$( '.meta-box-sortables:visible' ).each( function() {
				sortablesIds.push( $( this ).attr( 'id' ) );
			});

			// Return if there's only one visible sortables area, e.g. in the block editor page.
			if ( 1 === sortablesIds.length ) {
				return;
			}

			// Find the index of the current sortables area within all the sortable areas.
			sortablesIndex = $.inArray( closestSortablesId, sortablesIds );
			// Detach the postbox to be moved.
			detachedPostbox = postbox.detach();

			// Move the detached postbox to its new position.
			if ( 'previous' === position ) {
				$( detachedPostbox ).appendTo( '#' + sortablesIds[ sortablesIndex - 1 ] );
			}

			if ( 'next' === position ) {
				$( detachedPostbox ).prependTo( '#' + sortablesIds[ sortablesIndex + 1 ] );
			}

			postboxes._mark_area();
			button.focus();
			postboxes.updateOrderButtonsProperties();
			postboxes.save_order( postboxes.page );
		},

		/**
		 * Update the move buttons properties depending on the postbox position.
		 *
		 * @since 5.5.0
		 *
		 * @return {void}
		 */
		updateOrderButtonsProperties: function() {
			var firstSortablesId = $( '.meta-box-sortables:visible:first' ).attr( 'id' ),
				lastSortablesId = $( '.meta-box-sortables:visible:last' ).attr( 'id' ),
				firstPostbox = $( '.postbox:visible:first' ),
				lastPostbox = $( '.postbox:visible:last' ),
				firstPostboxId = firstPostbox.attr( 'id' ),
				lastPostboxId = lastPostbox.attr( 'id' ),
				firstPostboxSortablesId = firstPostbox.closest( '.meta-box-sortables' ).attr( 'id' ),
				lastPostboxSortablesId = lastPostbox.closest( '.meta-box-sortables' ).attr( 'id' ),
				moveUpButtons = $( '.handle-order-higher' ),
				moveDownButtons = $( '.handle-order-lower' );

			// Enable all buttons as a reset first.
			moveUpButtons
				.attr( 'aria-disabled', 'false' )
				.removeClass( 'hidden' );
			moveDownButtons
				.attr( 'aria-disabled', 'false' )
				.removeClass( 'hidden' );

			// When there's only one "sortables" area (e.g. in the block editor) and only one visible postbox, hide the buttons.
			if ( firstSortablesId === lastSortablesId && firstPostboxId === lastPostboxId ) {
				moveUpButtons.addClass( 'hidden' );
				moveDownButtons.addClass( 'hidden' );
			}

			// Set an aria-disabled=true attribute on the first visible "move" buttons.
			if ( firstSortablesId === firstPostboxSortablesId ) {
				$( firstPostbox ).find( '.handle-order-higher' ).attr( 'aria-disabled', 'true' );
			}

			// Set an aria-disabled=true attribute on the last visible "move" buttons.
			if ( lastSortablesId === lastPostboxSortablesId ) {
				$( '.postbox:visible .handle-order-lower' ).last().attr( 'aria-disabled', 'true' );
			}
		},

		/**
		 * Adds event handlers to all postboxes and screen option on the current page.
		 *
		 * @since 2.7.0
		 *
		 * @memberof postboxes
		 *
		 * @param {string} page The page we are currently on.
		 * @param {Object} [args]
		 * @param {Function} args.pbshow A callback that is called when a postbox opens.
		 * @param {Function} args.pbhide A callback that is called when a postbox closes.
		 * @return {void}
		 */
		add_postbox_toggles : function (page, args) {
			var $handles = $( '.postbox .hndle, .postbox .handlediv' ),
				$orderButtons = $( '.postbox .handle-order-higher, .postbox .handle-order-lower' );

			this.page = page;
			this.init( page, args );

			$handles.on( 'click.postboxes', this.handle_click );

			// Handle the order of the postboxes.
			$orderButtons.on( 'click.postboxes', this.handleOrder );

			/**
			 * @since 2.7.0
			 */
			$('.postbox .hndle a').click( function(e) {
				e.stopPropagation();
			});

			/**
			 * Hides a postbox.
			 *
			 * Event handler for the postbox dismiss button. After clicking the button
			 * the postbox will be hidden.
			 *
			 * As of WordPress 5.5, this is only used for the browser update nag.
			 *
			 * @since 3.2.0
			 *
			 * @return {void}
			 */
			$( '.postbox a.dismiss' ).on( 'click.postboxes', function( e ) {
				var hide_id = $(this).parents('.postbox').attr('id') + '-hide';
				e.preventDefault();
				$( '#' + hide_id ).prop('checked', false).triggerHandler('click');
			});

			/**
			 * Hides the postbox element
			 *
			 * Event handler for the screen options checkboxes. When a checkbox is
			 * clicked this function will hide or show the relevant postboxes.
			 *
			 * @since 2.7.0
			 * @ignore
			 *
			 * @fires postboxes#postbox-toggled
			 *
			 * @return {void}
			 */
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

				/**
				 * @since 4.0.0
				 * @see postboxes.handle_click
				 */
				$document.trigger( 'postbox-toggled', $postbox );
			});

			/**
			 * Changes the amount of columns based on the layout preferences.
			 *
			 * @since 2.8.0
			 *
			 * @return {void}
			 */
			$('.columns-prefs input[type="radio"]').bind('click.postboxes', function(){
				var n = parseInt($(this).val(), 10);

				if ( n ) {
					postboxes._pb_edit(n);
					postboxes.save_order( page );
				}
			});
		},

		/**
		 * Initializes all the postboxes, mainly their sortable behaviour.
		 *
		 * @since 2.7.0
		 *
		 * @memberof postboxes
		 *
		 * @param {string} page The page we are currently on.
		 * @param {Object} [args={}] The arguments for the postbox initializer.
		 * @param {Function} args.pbshow A callback that is called when a postbox opens.
		 * @param {Function} args.pbhide A callback that is called when a postbox
		 *                               closes.
		 *
		 * @return {void}
		 */
		init : function(page, args) {
			var isMobile = $( document.body ).hasClass( 'mobile' ),
				$handleButtons = $( '.postbox .handlediv' );

			$.extend( this, args || {} );
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
					/* `helper: 'clone'` is equivalent to `return element.clone();`
					 * Cloning a checked radio and then inserting that clone next to the original
					 * radio unchecks the original radio (since only one of the two can be checked).
					 * We get around this by renaming the helper's inputs' name attributes so that,
					 * when the helper is inserted into the DOM for the sortable, no radios are
					 * duplicated, and no original radio gets unchecked.
					 */
					return element.clone()
						.find( ':input' )
							.attr( 'name', function( i, currentName ) {
								return 'sort_' + parseInt( Math.random() * 100000, 10 ).toString() + '_' + currentName;
							} )
						.end();
				},
				opacity: 0.65,
				start: function() {
					$( 'body' ).addClass( 'is-dragging-metaboxes' );
					// Refresh the cached positions of all the sortable items so that the min-height set while dragging works.
					$( '.meta-box-sortables' ).sortable( 'refreshPositions' );
				},
				stop: function() {
					var $el = $( this );

					$( 'body' ).removeClass( 'is-dragging-metaboxes' );

					if ( $el.find( '#dashboard_browser_nag' ).is( ':visible' ) && 'dashboard_browser_nag' != this.firstChild.id ) {
						$el.sortable('cancel');
						return;
					}

					postboxes.updateOrderButtonsProperties();
					postboxes.save_order(page);
				},
				receive: function(e,ui) {
					if ( 'dashboard_browser_nag' == ui.item[0].id )
						$(ui.sender).sortable('cancel');

					postboxes._mark_area();
					$document.trigger( 'postbox-moved', ui.item );
				}
			});

			if ( isMobile ) {
				$(document.body).bind('orientationchange.postboxes', function(){ postboxes._pb_change(); });
				this._pb_change();
			}

			this._mark_area();

			// Update the "move" buttons properties.
			this.updateOrderButtonsProperties();
			$document.on( 'postbox-toggled', this.updateOrderButtonsProperties );

			// Set the handle buttons `aria-expanded` attribute initial value on page load.
			$handleButtons.each( function () {
				var $el = $( this );
				$el.attr( 'aria-expanded', ! $el.closest( '.postbox' ).hasClass( 'closed' ) );
			});
		},

		/**
		 * Saves the state of the postboxes to the server.
		 *
		 * It sends two lists, one with all the closed postboxes, one with all the
		 * hidden postboxes.
		 *
		 * @since 2.7.0
		 *
		 * @memberof postboxes
		 *
		 * @param {string} page The page we are currently on.
		 * @return {void}
		 */
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

		/**
		 * Saves the order of the postboxes to the server.
		 *
		 * Sends a list of all postboxes inside a sortable area to the server.
		 *
		 * @since 2.8.0
		 *
		 * @memberof postboxes
		 *
		 * @param {string} page The page we are currently on.
		 * @return {void}
		 */
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

			$.post(
				ajaxurl,
				postVars,
				function( response ) {
					if ( response.success ) {
						wp.a11y.speak( __( 'The boxes order has been saved.' ) );
					}
				}
			);
		},

		/**
		 * Marks empty postbox areas.
		 *
		 * Adds a message to empty sortable areas on the dashboard page. Also adds a
		 * border around the side area on the post edit screen if there are no postboxes
		 * present.
		 *
		 * @since 3.3.0
		 * @access private
		 *
		 * @memberof postboxes
		 *
		 * @return {void}
		 */
		_mark_area : function() {
			var visible = $( 'div.postbox:visible' ).length,
				visibleSortables = $( '#dashboard-widgets .meta-box-sortables:visible, #post-body .meta-box-sortables:visible' ),
				areAllVisibleSortablesEmpty = true;

			visibleSortables.each( function() {
				var t = $(this);

				if ( visible == 1 || t.children( '.postbox:visible' ).length ) {
					t.removeClass('empty-container');
					areAllVisibleSortablesEmpty = false;
				}
				else {
					t.addClass('empty-container');
				}
			});

			postboxes.updateEmptySortablesText( visibleSortables, areAllVisibleSortablesEmpty );
		},

		/**
		 * Updates the text for the empty sortable areas on the Dashboard.
		 *
		 * @since 5.5.0
		 *
		 * @param {Object}  visibleSortables            The jQuery object representing the visible sortable areas.
		 * @param {boolean} areAllVisibleSortablesEmpty Whether all the visible sortable areas are "empty".
		 *
		 * @return {void}
		 */
		updateEmptySortablesText: function( visibleSortables, areAllVisibleSortablesEmpty ) {
			var isDashboard = $( '#dashboard-widgets' ).length,
				emptySortableText = areAllVisibleSortablesEmpty ?  __( 'Add boxes from the Screen Options menu' ) : __( 'Drag boxes here' );

			if ( ! isDashboard ) {
				return;
			}

			visibleSortables.each( function() {
				if ( $( this ).hasClass( 'empty-container' ) ) {
					$( this ).attr( 'data-emptyString', emptySortableText );
				}
			} );
		},

		/**
		 * Changes the amount of columns on the post edit page.
		 *
		 * @since 3.3.0
		 * @access private
		 *
		 * @memberof postboxes
		 *
		 * @fires postboxes#postboxes-columnchange
		 *
		 * @param {number} n The amount of columns to divide the post edit page in.
		 * @return {void}
		 */
		_pb_edit : function(n) {
			var el = $('.metabox-holder').get(0);

			if ( el ) {
				el.className = el.className.replace(/columns-\d+/, 'columns-' + n);
			}

			/**
			 * Fires when the amount of columns on the post edit page has been changed.
			 *
			 * @since 4.0.0
			 * @ignore
			 *
			 * @event postboxes#postboxes-columnchange
			 */
			$( document ).trigger( 'postboxes-columnchange' );
		},

		/**
		 * Changes the amount of columns the postboxes are in based on the current
		 * orientation of the browser.
		 *
		 * @since 3.3.0
		 * @access private
		 *
		 * @memberof postboxes
		 *
		 * @return {void}
		 */
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
					if ( $( '#poststuff' ).length ) {
						this._pb_edit(1);
					} else {
						if ( !check.length || !check.is(':checked') )
							this._pb_edit(2);
					}
					break;
			}
		},

		/* Callbacks */

		/**
		 * @since 2.7.0
		 * @access public
		 *
		 * @property {Function|boolean} pbshow A callback that is called when a postbox
		 *                                     is opened.
		 * @memberof postboxes
		 */
		pbshow : false,

		/**
		 * @since 2.7.0
		 * @access public
		 * @property {Function|boolean} pbhide A callback that is called when a postbox
		 *                                     is closed.
		 * @memberof postboxes
		 */
		pbhide : false
	};

}(jQuery));
