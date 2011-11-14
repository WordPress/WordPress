/**
 * Pointer jQuery widget.
 */
(function($){
	var identifier = 0,
		zindex = 9999;

	$.widget("wp.pointer", {
		options: {
			pointerClass: 'wp-pointer',
			pointerWidth: 320,
			content: function( respond, event, t ) {
				return $(this).text();
			},
			buttons: function( event, t ) {
				var close  = ( wpPointerL10n ) ? wpPointerL10n.dismiss : 'Dismiss',
					button = $('<a class="close" href="#">' + close + '</a>');

				return button.bind( 'click.pointer', function() {
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

		_setOption: function( key, value ) {
			var o   = this.options,
				tip = this.pointer;

			// Handle document transfer
			if ( key === "document" && value !== o.document ) {
				tip.detach().appendTo( value.body );

			// Handle class change
			} else if ( key === "pointerClass" ) {
				tip.removeClass( o.pointerClass ).addClass( value );
			}

			// Call super method.
			$.Widget.prototype._setOption.apply( this, arguments );

			// Reposition automatically
			if ( key === "position" ) {
				this.reposition();

			// Update content automatically if pointer is open
			} else if ( key === "content" && this.active ) {
				this.update();
			}
		},

		destroy: function() {
			this.pointer.remove();
			$.Widget.prototype.destroy.call( this );
		},

		widget: function() {
			return this.pointer;
		},

		update: function( event ) {
			var self = this,
				o    = this.options,
				dfd  = $.Deferred(),
				content;

			if ( o.disabled )
				return;

			dfd.done( function( content ) {
				self._update( event, content );
			})

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
		 * Update is separated into two functions to allow events to defer
		 * updating the pointer (e.g. fetch content with ajax, etc).
		 */
		_update: function( event, content ) {
			var buttons,
				o = this.options;

			if ( ! content )
				return;

			this.pointer.stop(); // Kill any animations on the pointer.
			this.content.html( content );

			buttons = o.buttons.call( this.element[0], event, this._handoff() );
			if ( buttons ) {
				buttons.wrap('<div class="wp-pointer-buttons" />').parent().appendTo( this.content );
			}

			this.reposition();
		},

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
				of: this.element
			}, position )); // the object comes before this.options.position so the user can override position.of.

			this.repoint();
		},

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

		open: function( event ) {
			var self = this,
				o    = this.options;

			if ( this.active || o.disabled )
				return;

			this.update().done( function() {
				self._open( event );
			});
		},

		_open: function( event ) {
			var self = this,
				o    = this.options;

			if ( this.active || o.disabled )
				return;

			this.active = true;

			this._trigger( "open", event, this._handoff() );

			this._trigger( "show", event, this._handoff({
				opened: function() {
					self._trigger( "opened", event, self._handoff() );
				}
			}));
		},

		close: function( event ) {
			if ( !this.active || this.options.disabled )
				return;

			var self = this;
			this.active = false;

			this._trigger( "close", event, this._handoff() );
			this._trigger( "hide", event, this._handoff({
				closed: function() {
					self._trigger( "closed", event, self._handoff() );
				}
			}));
		},

		sendToTop: function( event ) {
			if ( this.active )
				this.pointer.css( 'z-index', zindex++ );
		},

		toggle: function( event ) {
			if ( this.pointer.is(':hidden') )
				this.open( event );
			else
				this.close( event );
		},

		_handoff: function( extend ) {
			return $.extend({
				pointer: this.pointer,
				element: this.element
			}, extend);
		}
	});
})(jQuery);
