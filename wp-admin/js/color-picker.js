/* global wpColorPickerL10n */
( function( $, undef ){

	var ColorPicker,
		_before = '<a tabindex="0" class="wp-color-result" />',
		_after = '<div class="wp-picker-holder" />',
		_wrap = '<div class="wp-picker-container" />',
		_button = '<input type="button" class="button button-small hidden" />';

	/**
	 * @summary Creates a jQuery UI color picker.
	 *
	 * Creates a jQuery UI color picker that is used in the theme customizer.
	 *
	 * @since 3.5.0
	 */
	ColorPicker = {
		options: {
			defaultColor: false,
			change: false,
			clear: false,
			hide: true,
			palettes: true,
			width: 255,
			mode: 'hsv',
			type: 'full',
			slider: 'horizontal'
		},
		/**
		 * @summary Creates a color picker that only allows you to adjust the hue.
		 *
		 * @since 3.5.0
		 *
		 * @access private
		 *
		 * @returns {void}
		 */
		_createHueOnly: function() {
			var self = this,
				el = self.element,
				color;

			el.hide();

			// Set the saturation to the maximum level.
			color = 'hsl(' + el.val() + ', 100, 50)';

			// Create an instance of the color picker, using the hsl mode.
			el.iris( {
				mode: 'hsl',
				type: 'hue',
				hide: false,
				color: color,
				/**
				 * @summary Handles the onChange event if one has been defined in the options.
				 *
				 * @param {Event} event    The event that's being called.
				 * @param {HTMLElement} ui The HTMLElement containing the color picker.
				 *
				 * @returns {void}
				 */
				change: function( event, ui ) {
					if ( $.isFunction( self.options.change ) ) {
						self.options.change.call( this, event, ui );
					}
				},
				width: self.options.width,
				slider: self.options.slider
			} );
		},
		/**
		 * @summary Creates the color picker.
		 *
		 * Creates the color picker, sets default values, css classes and wraps it all in HTML.
		 *
		 * @since 3.5.0
		 *
		 * @access private
		 *
		 * @returns {void}
		 */
		_create: function() {
			// Return early if Iris support is missing.
			if ( ! $.support.iris ) {
				return;
			}

			var self = this,
				el = self.element;

			// Override default options with options bound to the element.
			$.extend( self.options, el.data() );

			// Create a color picker which only allows adjustments to the hue.
			if ( self.options.type === 'hue' ) {
				return self._createHueOnly();
			}

			// Bind the close event.
			self.close = $.proxy( self.close, self );

			self.initialValue = el.val();

			// Set up HTML structure and hide the color picker.
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

			el.wrap( '<span class="wp-picker-input-wrap" />' ).after( self.button );

			el.iris( {
				target: self.pickerContainer,
				hide: self.options.hide,
				width: self.options.width,
				mode: self.options.mode,
				palettes: self.options.palettes,
				/**
				 * @summary Handles the onChange event if one has been defined in the options.
				 *
				 * Handles the onChange event if one has been defined in the options and additionally
				 * sets the background color for the toggler element.
				 *
				 * @since 3.5.0
				 *
				 * @param {Event} event    The event that's being called.
				 * @param {HTMLElement} ui The HTMLElement containing the color picker.
				 *
				 * @returns {void}
				 */
				change: function( event, ui ) {
					self.toggler.css( { backgroundColor: ui.color.toString() } );

					if ( $.isFunction( self.options.change ) ) {
						self.options.change.call( this, event, ui );
					}
				}
			} );

			el.val( self.initialValue );
			self._addListeners();

			// Force the color picker to always be closed on initial load.
			if ( ! self.options.hide ) {
				self.toggler.click();
			}
		},
		/**
		 * @summary Binds event listeners to the color picker.
		 *
		 * @since 3.5.0
		 *
		 * @access private
		 *
		 * @returns {void}
		 */
		_addListeners: function() {
			var self = this;

			/**
			 * @summary Prevent any clicks inside this widget from leaking to the top and closing it.
			 *
			 * @since 3.5.0
			 *
			 * @param {Event} event The event that's being called.
			 *
			 * @returs {void}
			 */
			self.wrap.on( 'click.wpcolorpicker', function( event ) {
				event.stopPropagation();
			});

			/**
			 * @summary Open or close the color picker depending on the class.
			 *
			 * @since 3.5
			 */
			self.toggler.click( function(){
				if ( self.toggler.hasClass( 'wp-picker-open' ) ) {
					self.close();
				} else {
					self.open();
				}
			});

			/**
			 * @summary Checks if value is empty when changing the color in the color picker.
			 *
			 * Checks if value is empty when changing the color in the color picker.
			 * If so, the background color is cleared.
			 *
			 * @since 3.5.0
			 *
			 * @param {Event} event The event that's being called.
			 *
			 * @returns {void}
			 */
			self.element.change( function( event ) {
				var me = $( this ),
					val = me.val();

				if ( val === '' || val === '#' ) {
					self.toggler.css( 'backgroundColor', '' );
					// Fire clear callback if we have one.
					if ( $.isFunction( self.options.clear ) ) {
						self.options.clear.call( this, event );
					}
				}
			});

			/**
			 * @summary Enables the user to open the color picker with their keyboard.
			 *
			 * Enables the user to open the color picker with their keyboard.
			 * This is possible by using the space or enter key.
			 *
			 * @since 3.5.0
			 *
			 * @param {Event} event The event that's being called.
			 *
			 * @returns {void}
			 */
			self.toggler.on( 'keyup', function( event ) {
				if ( event.keyCode === 13 || event.keyCode === 32 ) {
					event.preventDefault();
					self.toggler.trigger( 'click' ).next().focus();
				}
			});

			/**
			 * @summary Enables the user to clear or revert the color in the color picker.
			 *
			 * Enables the user to either clear the color in the color picker or revert back to the default color.
			 *
			 * @since 3.5.0
			 *
			 * @param {Event} event The event that's being called.
			 *
			 * @returns {void}
			 */
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
		/**
		 * @summary Opens the color picker dialog.
		 *
		 * @since 3.5.0
		 *
		 * @returns {void}
		 */
		open: function() {
			this.element.show().iris( 'toggle' ).focus();
			this.button.removeClass( 'hidden' );
			this.wrap.addClass( 'wp-picker-active' );
			this.toggler.addClass( 'wp-picker-open' );
			$( 'body' ).trigger( 'click.wpcolorpicker' ).on( 'click.wpcolorpicker', this.close );
		},
		/**
		 * @summary Closes the color picker dialog.
		 *
		 * @since 3.5.0
		 *
		 * @returns {void}
		 */
		close: function() {
			this.element.hide().iris( 'toggle' );
			this.button.addClass( 'hidden' );
			this.wrap.removeClass( 'wp-picker-active' );
			this.toggler.removeClass( 'wp-picker-open' );
			$( 'body' ).off( 'click.wpcolorpicker', this.close );
		},
		/**
		 * @summary Returns iris object or sets new color.
		 *
		 * Returns the iris object if no new color is provided. If a new color is provided, it sets the new color.
		 *
		 * @param newColor {string|*} The new color to use. Can be undefined.
		 *
		 * @since 3.5.0
		 *
		 * @returns {string} The element's color
		 */
		color: function( newColor ) {
			if ( newColor === undef ) {
				return this.element.iris( 'option', 'color' );
			}
			this.element.iris( 'option', 'color', newColor );
		},
		/**
		 * @summary Returns iris object or sets new default color.
		 *
		 * Returns the iris object if no new default color is provided.
		 * If a new default color is provided, it sets the new default color.
		 *
		 * @param newDefaultColor {string|*} The new default color to use. Can be undefined.
		 *
		 * @since 3.5.0
		 *
		 * @returns {boolean|string} The element's color.
		 */
		defaultColor: function( newDefaultColor ) {
			if ( newDefaultColor === undef ) {
				return this.options.defaultColor;
			}

			this.options.defaultColor = newDefaultColor;
		}
	};

	// Register the color picker as a widget.
	$.widget( 'wp.wpColorPicker', ColorPicker );
}( jQuery ) );
