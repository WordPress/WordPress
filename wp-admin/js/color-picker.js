/* global wpColorPickerL10n */
( function( $, undef ){

	var ColorPicker,
		// html stuff
		_before = '<a tabindex="0" class="wp-color-result" />',
		_after = '<div class="wp-picker-holder" />',
		_wrap = '<div class="wp-picker-container" />',
		_button = '<input type="button" class="button button-small hidden" />';

	// jQuery UI Widget constructor
	ColorPicker = {
		options: {
			defaultColor: false,
			change: false,
			clear: false,
			hide: true,
			palettes: true
		},
		_create: function() {
			// bail early for unsupported Iris.
			if ( ! $.support.iris ) {
				return;
			}

			var self = this,
				el = self.element;

			$.extend( self.options, el.data() );

			// keep close bound so it can be attached to a body listener
			self.close = $.proxy( self.close, self );

			self.initialValue = el.val();

			// Set up HTML structure, hide things
			el.addClass( 'wp-color-picker' ).hide().wrap( _wrap );
			self.wrap = el.parent();
			self.toggler = $( _before ).insertBefore( el ).css( { backgroundColor: self.initialValue } ).attr( 'title', wpColorPickerL10n.pick ).attr( 'data-current', wpColorPickerL10n.current );
			self.pickerContainer = $( _after ).insertAfter( el );
			self.button = $( _button );

			if ( self.options.defaultColor ) {
				self.button.addClass( 'wp-picker-default' ).val( wpColorPickerL10n.defaultString );
			} else {
				self.button.addClass( 'wp-picker-clear' ).val( wpColorPickerL10n.clear );
			}

			el.wrap( '<span class="wp-picker-input-wrap" />' ).after(self.button);

			el.iris( {
				target: self.pickerContainer,
				hide: true,
				width: 255,
				mode: 'hsv',
				palettes: self.options.palettes,
				change: function( event, ui ) {
					self.toggler.css( { backgroundColor: ui.color.toString() } );
					// check for a custom cb
					if ( $.isFunction( self.options.change ) ) {
						self.options.change.call( this, event, ui );
					}
				}
			} );

			el.val( self.initialValue );
			self._addListeners();
			if ( ! self.options.hide ) {
				self.toggler.click();
			}
		},
		_addListeners: function() {
			var self = this;

			// prevent any clicks inside this widget from leaking to the top and closing it
			self.wrap.on( 'click.wpcolorpicker', function( event ) {
				event.stopPropagation();
			});

			self.toggler.click( function(){
				if ( self.toggler.hasClass( 'wp-picker-open' ) ) {
					self.close();
				} else {
					self.open();
				}
			});

			self.element.change( function( event ) {
				var me = $( this ),
					val = me.val();
				// Empty = clear
				if ( val === '' || val === '#' ) {
					self.toggler.css( 'backgroundColor', '' );
					// fire clear callback if we have one
					if ( $.isFunction( self.options.clear ) ) {
						self.options.clear.call( this, event );
					}
				}
			});

			// open a keyboard-focused closed picker with space or enter
			self.toggler.on( 'keyup', function( event ) {
				if ( event.keyCode === 13 || event.keyCode === 32 ) {
					event.preventDefault();
					self.toggler.trigger( 'click' ).next().focus();
				}
			});

			self.button.click( function( event ) {
				var me = $( this );
				if ( me.hasClass( 'wp-picker-clear' ) ) {
					self.element.val( '' );
					self.toggler.css( 'backgroundColor', '' );
					if ( $.isFunction( self.options.clear ) ) {
						self.options.clear.call( this, event );
					}
				} else if ( me.hasClass( 'wp-picker-default' ) ) {
					self.element.val( self.options.defaultColor ).change();
				}
			});
		},
		open: function() {
			this.element.show().iris( 'toggle' ).focus();
			this.button.removeClass( 'hidden' );
			this.toggler.addClass( 'wp-picker-open' );
			$( 'body' ).trigger( 'click.wpcolorpicker' ).on( 'click.wpcolorpicker', this.close );
		},
		close: function() {
			this.element.hide().iris( 'toggle' );
			this.button.addClass( 'hidden' );
			this.toggler.removeClass( 'wp-picker-open' );
			$( 'body' ).off( 'click.wpcolorpicker', this.close );
		},
		// $("#input").wpColorPicker('color') returns the current color
		// $("#input").wpColorPicker('color', '#bada55') to set
		color: function( newColor ) {
			if ( newColor === undef ) {
				return this.element.iris( 'option', 'color' );
			}

			this.element.iris( 'option', 'color', newColor );
		},
		//$("#input").wpColorPicker('defaultColor') returns the current default color
		//$("#input").wpColorPicker('defaultColor', newDefaultColor) to set
		defaultColor: function( newDefaultColor ) {
			if ( newDefaultColor === undef ) {
				return this.options.defaultColor;
			}

			this.options.defaultColor = newDefaultColor;
		}
	};

	$.widget( 'wp.wpColorPicker', ColorPicker );
}( jQuery ) );
