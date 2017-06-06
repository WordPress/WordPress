/**
 * wp-color-picker-alpha
 *
 * Overwrite Automattic Iris for enabled Alpha Channel in wpColorPicker
 * Only run in input and is defined data alpha in true
 *
 * Version: 1.1
 * https://github.com/23r9i0/wp-color-picker-alpha
 * Copyright (c) 2015 Sergio P.A. (23r9i0).
 * Licensed under the GPLv2 license.
 */
( function( $ ) {
	// Variable for some backgrounds
	var image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==';
	// html stuff for wpColorPicker copy of the original color-picker.js
	var	_before = '<a tabindex="0" class="wp-color-result" />',
		_after = '<div class="wp-picker-holder" />',
		_wrap = '<div class="wp-picker-container" />',
		_button = '<input type="button" class="button button-small hidden" />';

	/**
	 * Overwrite Color
	 * for enable support rbga
	 */
	Color.fn.toString = function() {
		if ( this._alpha < 1 )
			return this.toCSS( 'rgba', this._alpha ).replace( /\s+/g, '' );

		var hex = parseInt( this._color, 10 ).toString( 16 );

		if ( this.error )
			return '';

		if ( hex.length < 6 ) {
			for ( var i = 6 - hex.length - 1; i >= 0; i-- ) {
				hex = '0' + hex;
			}
		}

		return '#' + hex;
	};

	/**
	 * Overwrite wpColorPicker
	 */
	$.widget( 'wp.wpColorPicker', $.wp.wpColorPicker, {
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
				hide: self.options.hide,
				width: self.options.width,
				mode: self.options.mode,
				palettes: self.options.palettes,
				change: function( event, ui ) {
					if ( self.options.alpha ) {
						self.toggler.css( { 'background-image': 'url(' + image + ')' } ).html('<span />');
						self.toggler.find('span').css({
							'width': '100%',
							'height': '100%',
							'position': 'absolute',
							'top': 0,
							'left': 0,
							'border-top-left-radius': '3px',
							'border-bottom-left-radius': '3px',
							'background': ui.color.toString()
						});
					} else {
						self.toggler.css( { backgroundColor: ui.color.toString() } );
					}
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
				// Empty or Error = clear
				if ( val === '' || self.element.hasClass('iris-error') ) {
					if ( self.options.alpha ) {
						self.toggler.removeAttr('style');
						self.toggler.find('span').css( 'backgroundColor', '' );
					} else {
						self.toggler.css( 'backgroundColor', '' );
					}
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
					if ( self.options.alpha ) {
						self.toggler.removeAttr('style');
						self.toggler.find('span').css( 'backgroundColor', '' );
					} else {
						self.toggler.css( 'backgroundColor', '' );
					}
					if ( $.isFunction( self.options.clear ) ) {
						self.options.clear.call( this, event );
					}
				} else if ( me.hasClass( 'wp-picker-default' ) ) {
					self.element.val( self.options.defaultColor ).change();
				}
			});
		}
	});

	/**
	 * Overwrite iris
	 */
	$.widget( 'a8c.iris', $.a8c.iris, {
		_create: function() {
			this._super();

			// Global option for check is mode rbga is enabled
			this.options.alpha = this.element.data( 'alpha' ) || false;

			// Is not input disabled
			if ( ! this.element.is( ':input' ) ) {
				this.options.alpha = false;
			}

			if ( typeof this.options.alpha !== 'undefined' && this.options.alpha ) {
				var self = this,
					el = self.element,
					_html = '<div class="iris-strip iris-slider iris-alpha-slider"><div class="iris-slider-offset iris-slider-offset-alpha"></div></div>',
					aContainer = $( _html ).appendTo( self.picker.find( '.iris-picker-inner' ) ),
					aSlider = aContainer.find( '.iris-slider-offset-alpha' ),
					controls = {
						aContainer: aContainer,
						aSlider: aSlider
					};

				// Set default width for input reset
				self.options.defaultWidth = el.width();

				// Update width for input
				if ( self._color._alpha < 1 || self._color.toString().indexOf('rgb') != 1 ) {
					el.width( parseInt( self.options.defaultWidth+100 ) );
				}

				// Push new controls
				$.each( controls, function( k, v ){
					self.controls[k] = v;
				});

				// Change size strip and add margin for sliders
				self.controls.square.css({'margin-right': '0'});
				var emptyWidth = ( self.picker.width() - self.controls.square.width() - 20 ),
					stripsMargin = emptyWidth/6,
					stripsWidth = (emptyWidth/2) - stripsMargin;

				$.each( [ 'aContainer', 'strip' ], function( k, v ) {
					self.controls[v].width( stripsWidth ).css({ 'margin-left': stripsMargin + 'px' });
				});

				// Add new slider
				self._initControls();

				// For updated widget
				self._change();
			}
		},
		_initControls: function() {
			this._super();

			if ( this.options.alpha ) {
				var self = this,
					controls = self.controls;

				controls.aSlider.slider({
					orientation: 'vertical',
					min: 0,
					max: 100,
					step: 1,
					value: parseInt( self._color._alpha*100 ),
					slide: function( event, ui ) {
						// Update alpha value
						self._color._alpha = parseFloat( ui.value/100 );
						self._change.apply( self, arguments );
					}
				});
			}
		},
		_change: function() {
			this._super();
			var self = this,
				el = self.element;

			if ( this.options.alpha ) {
				var	controls = self.controls,
					alpha = parseInt( self._color._alpha*100 ),
					color = self._color.toRgb(),
					gradient = [
						'rgb(' + color.r + ',' + color.g + ',' + color.b + ') 0%',
						'rgba(' + color.r + ',' + color.g + ',' + color.b + ', 0) 100%'
					],
					defaultWidth = self.options.defaultWidth,
					target = self.picker.closest('.wp-picker-container').find( '.wp-color-result' );

				// Generate background slider alpha, only for CSS3 old browser fuck!! :)
				controls.aContainer.css({ 'background': 'linear-gradient(to bottom, ' + gradient.join( ', ' ) + '), url(' + image + ')' });

				if ( target.hasClass('wp-picker-open') ) {
					// Update alpha value
					controls.aSlider.slider( 'value', alpha );

					/**
					 * Disabled change opacity in default slider Saturation ( only is alpha enabled )
					 * and change input width for view all value
					 */
					if ( self._color._alpha < 1 ) {
						var style = controls.strip.attr( 'style' ).replace( /rgba\(([0-9]+,)(\s+)?([0-9]+,)(\s+)?([0-9]+)(,(\s+)?[0-9\.]+)\)/g, 'rgb($1$3$5)' );

						controls.strip.attr( 'style', style );

						el.width( parseInt( defaultWidth+100 ) );
					} else {
						el.width( defaultWidth );
					}
				}
			}

			var reset = el.data('reset-alpha') || false;
			if ( reset ) {
				self.picker.find( '.iris-palette-container' ).on( 'click.palette', '.iris-palette', function() {
					self._color._alpha = 1;
					self.active = 'external';
					self._change();
				});
			}
		},
		_addInputListeners: function( input ) {
			var self = this,
				debounceTimeout = 700, // originally set to 100, but some user perceive it as "jumps to random colors at third digit"
				callback = function( event ){
					var color = new Color( input.val() ),
						val = input.val();

					input.removeClass( 'iris-error' );
					// we gave a bad color
					if ( color.error ) {
						// don't error on an empty input
						if ( val !== '' ) {
							input.addClass( 'iris-error' );
						}
					} else {
						if ( color.toString() !== self._color.toString() ) {
							// let's not do this on keyup for hex shortcodes
							if ( ! ( event.type === 'keyup' && val.match( /^[0-9a-fA-F]{3}$/ ) ) ) {
								self._setOption( 'color', color.toString() );
							}
						}
					}
				};

			input.on( 'change', callback ).on( 'keyup', self._debounce( callback, debounceTimeout ) );

			// If we initialized hidden, show on first focus. The rest is up to you.
			if ( self.options.hide ) {
				input.one( 'focus', function() {
					self.show();
				});
			}
		}
	} );
}( jQuery ) );

// Auto Call plugin is class is color-picker
jQuery( document ).ready( function( $ ) {
  $( '.color-picker' ).wpColorPicker();
} );