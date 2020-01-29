/**
 * @output wp-includes/js/wp-pointer.js
 */

/* global wpPointerL10n */

/**
 * Initializes the wp-pointer widget using jQuery UI Widget Factory.
 */
(function($){
	var identifier = 0,
		zindex = 9999;

	$.widget('wp.pointer',/** @lends $.widget.wp.pointer.prototype */{
		options: {
			pointerClass: 'wp-pointer',
			pointerWidth: 320,
			content: function() {
				return $(this).text();
			},
			buttons: function( event, t ) {
				var close  = ( wpPointerL10n ) ? wpPointerL10n.dismiss : 'Dismiss',
					button = $('<a class="close" href="#">' + close + '</a>');

				return button.bind( 'click.pointer', function(e) {
					e.preventDefault();
					t.element.pointer('close');
				});
			},
			position: 'top',
			show: function( event, t ) {
				t.pointer.show();
				t.opened();
			},
			hide: function( event, t ) {
				t.pointer.hide();
				t.closed();
			},
			document: document
		},

		/**
		 * A class that represents a WordPress pointer.
		 *
		 * @since 3.3.0
		 * @private
		 *
		 * @constructs $.widget.wp.pointer
		 */
		_create: function() {
			var positioning,
				family;

			this.content = $('<div class="wp-pointer-content"></div>');
			this.arrow   = $('<div class="wp-pointer-arrow"><div class="wp-pointer-arrow-inner"></div></div>');

			family = this.element.parents().add( this.element );
			positioning = 'absolute';

			if ( family.filter(function(){ return 'fixed' === $(this).css('position'); }).length )
				positioning = 'fixed';

			this.pointer = $('<div />')
				.append( this.content )
				.append( this.arrow )
				.attr('id', 'wp-pointer-' + identifier++)
				.addClass( this.options.pointerClass )
				.css({'position': positioning, 'width': this.options.pointerWidth+'px', 'display': 'none'})
				.appendTo( this.options.document.body );
		},

		/**
		 * Sets an option on the pointer instance.
		 *
		 * There are 4 special values that do something extra:
		 *
		 * - `document`     will transfer the pointer to the body of the new document
		 *                  specified by the value.
		 * - `pointerClass` will change the class of the pointer element.
		 * - `position`     will reposition the pointer.
		 * - `content`      will update the content of the pointer.
		 *
		 * @since 3.3.0
		 * @private
		 *
		 * @param {string} key   The key of the option to set.
		 * @param {*}      value The value to set the option to.
		 */
		_setOption: function( key, value ) {
			var o   = this.options,
				tip = this.pointer;

			// Handle document transfer.
			if ( key === 'document' && value !== o.document ) {
				tip.detach().appendTo( value.body );

			// Handle class change.
			} else if ( key === 'pointerClass' ) {
				tip.removeClass( o.pointerClass ).addClass( value );
			}

			// Call super method.
			$.Widget.prototype._setOption.apply( this, arguments );

			// Reposition automatically.
			if ( key === 'position' ) {
				this.reposition();

			// Update content automatically if pointer is open.
			} else if ( key === 'content' && this.active ) {
				this.update();
			}
		},

		/**
		 * Removes the pointer element from of the DOM.
		 *
		 * Makes sure that the widget and all associated bindings are destroyed.
		 *
		 * @since 3.3.0
		 */
		destroy: function() {
			this.pointer.remove();
			$.Widget.prototype.destroy.call( this );
		},

		/**
		 * Returns the pointer element.
		 *
		 * @since 3.3.0
		 *
		 * @return {Object} Pointer The pointer object.
		 */
		widget: function() {
			return this.pointer;
		},

		/**
		 * Updates the content of the pointer.
		 *
		 * This function doesn't update the content of the pointer itself. That is done
		 * by the `_update` method. This method will make sure that the `_update` method
		 * is called with the right content.
		 *
		 * The content in the options can either be a string or a callback. If it is a
		 * callback the result of this callback is used as the content.
		 *
		 * @since 3.3.0
		 *
		 * @param {Object} event The event that caused the update.
		 *
		 * @return {Promise} Resolves when the update has been executed.
		 */
		update: function( event ) {
			var self = this,
				o    = this.options,
				dfd  = $.Deferred(),
				content;

			if ( o.disabled )
				return;

			dfd.done( function( content ) {
				self._update( event, content );
			});

			// Either o.content is a string...
			if ( typeof o.content === 'string' ) {
				content = o.content;

			// ...or o.content is a callback.
			} else {
				content = o.content.call( this.element[0], dfd.resolve, event, this._handoff() );
			}

			// If content is set, then complete the update.
			if ( content )
				dfd.resolve( content );

			return dfd.promise();
		},

		/**
		 * Updates the content of the pointer.
		 *
		 * Will make sure that the pointer is correctly positioned.
		 *
		 * @since 3.3.0
		 * @private
		 *
		 * @param {Object} event   The event that caused the update.
		 * @param {*}      content The content object. Either a string or a jQuery tree.
		 */
		_update: function( event, content ) {
			var buttons,
				o = this.options;

			if ( ! content )
				return;

			// Kill any animations on the pointer.
			this.pointer.stop();
			this.content.html( content );

			buttons = o.buttons.call( this.element[0], event, this._handoff() );
			if ( buttons ) {
				buttons.wrap('<div class="wp-pointer-buttons" />').parent().appendTo( this.content );
			}

			this.reposition();
		},

		/**
		 * Repositions the pointer.
		 *
		 * Makes sure the pointer is the correct size for its content and makes sure it
		 * is positioned to point to the right element.
		 *
		 * @since 3.3.0
		 */
		reposition: function() {
			var position;

			if ( this.options.disabled )
				return;

			position = this._processPosition( this.options.position );

			// Reposition pointer.
			this.pointer.css({
				top: 0,
				left: 0,
				zIndex: zindex++ // Increment the z-index so that it shows above other opened pointers.
			}).show().position($.extend({
				of: this.element,
				collision: 'fit none'
			}, position )); // The object comes before this.options.position so the user can override position.of.

			this.repoint();
		},

		/**
		 * Sets the arrow of the pointer to the correct side of the pointer element.
		 *
		 * @since 3.3.0
		 */
		repoint: function() {
			var o = this.options,
				edge;

			if ( o.disabled )
				return;

			edge = ( typeof o.position == 'string' ) ? o.position : o.position.edge;

			// Remove arrow classes.
			this.pointer[0].className = this.pointer[0].className.replace( /wp-pointer-[^\s'"]*/, '' );

			// Add arrow class.
			this.pointer.addClass( 'wp-pointer-' + edge );
		},

		/**
		 * Calculates the correct position based on a position in the settings.
		 *
		 * @since 3.3.0
		 * @private
		 *
		 * @param {string|Object} position Either a side of a pointer or an object
		 *                                 containing a pointer.
		 *
		 * @return {Object} result  An object containing position related data.
		 */
		_processPosition: function( position ) {
			var opposite = {
					top: 'bottom',
					bottom: 'top',
					left: 'right',
					right: 'left'
				},
				result;

			// If the position object is a string, it is shorthand for position.edge.
			if ( typeof position == 'string' ) {
				result = {
					edge: position + ''
				};
			} else {
				result = $.extend( {}, position );
			}

			if ( ! result.edge )
				return result;

			if ( result.edge == 'top' || result.edge == 'bottom' ) {
				result.align = result.align || 'left';

				result.at = result.at || result.align + ' ' + opposite[ result.edge ];
				result.my = result.my || result.align + ' ' + result.edge;
			} else {
				result.align = result.align || 'top';

				result.at = result.at || opposite[ result.edge ] + ' ' + result.align;
				result.my = result.my || result.edge + ' ' + result.align;
			}

			return result;
		},

		/**
		 * Opens the pointer.
		 *
		 * Only opens the pointer widget in case it is closed and not disabled, and
		 * calls 'update' before doing so. Calling update makes sure that the pointer
		 * is correctly sized and positioned.
		 *
		 * @since 3.3.0
		 *
		 * @param {Object} event The event that triggered the opening of this pointer.
		 */
		open: function( event ) {
			var self = this,
				o    = this.options;

			if ( this.active || o.disabled || this.element.is(':hidden') )
				return;

			this.update().done( function() {
				self._open( event );
			});
		},

		/**
		 * Opens and shows the pointer element.
		 *
		 * @since 3.3.0
		 * @private
		 *
		 * @param {Object} event An event object.
		 */
		_open: function( event ) {
			var self = this,
				o    = this.options;

			if ( this.active || o.disabled || this.element.is(':hidden') )
				return;

			this.active = true;

			this._trigger( 'open', event, this._handoff() );

			this._trigger( 'show', event, this._handoff({
				opened: function() {
					self._trigger( 'opened', event, self._handoff() );
				}
			}));
		},

		/**
		 * Closes and hides the pointer element.
		 *
		 * @since 3.3.0
		 *
		 * @param {Object} event An event object.
		 */
		close: function( event ) {
			if ( !this.active || this.options.disabled )
				return;

			var self = this;
			this.active = false;

			this._trigger( 'close', event, this._handoff() );
			this._trigger( 'hide', event, this._handoff({
				closed: function() {
					self._trigger( 'closed', event, self._handoff() );
				}
			}));
		},

		/**
		 * Puts the pointer on top by increasing the z-index.
		 *
		 * @since 3.3.0
		 */
		sendToTop: function() {
			if ( this.active )
				this.pointer.css( 'z-index', zindex++ );
		},

		/**
		 * Toggles the element between shown and hidden.
		 *
		 * @since 3.3.0
		 *
		 * @param {Object} event An event object.
		 */
		toggle: function( event ) {
			if ( this.pointer.is(':hidden') )
				this.open( event );
			else
				this.close( event );
		},

		/**
		 * Extends the pointer and the widget element with the supplied parameter, which
		 * is either an element or a function.
		 *
		 * @since 3.3.0
		 * @private
		 *
		 * @param {Object} extend The object to be merged into the original object.
		 *
		 * @return {Object} The extended object.
		 */
		_handoff: function( extend ) {
			return $.extend({
				pointer: this.pointer,
				element: this.element
			}, extend);
		}
	});
})(jQuery);
