( function( $, undef ){

	// html stuff
	var _before = '<a tabindex="0" class="wp-color-result" />',
		_after = '<div class="wp-picker-holder" />',
		_wrap = '<div class="wp-picker-container" />',
		_button = '<input type="button" class="button button-small hidden" />';

	// jQuery UI Widget constructor
	var ColorPicker = {
		options: {
			defaultColor: false,
			change: false,
			clear: false,
			hide: true,
			palettes: true
		},
		_create: function() {
			// bail early for IE < 8
			if ( $.browser.msie && parseInt( $.browser.version, 10 ) < 8 )
				return;
			var self = this;
			var el = self.element;
			$.extend( self.options, el.data() );

			self.initialValue = el.val();

			// Set up HTML structure, hide things
			el.addClass( 'wp-color-picker' ).hide().wrap( _wrap );
			self.wrap = el.parent();
			self.toggler = $( _before ).insertBefore( el ).css( { backgroundColor: self.initialValue } ).attr( "title", wpColorPickerL10n.pick ).attr( "data-current", wpColorPickerL10n.current );
			self.pickerContainer = $( _after ).insertAfter( el );
			self.button = $( _button );

			if ( self.options.defaultColor )
				self.button.addClass( 'wp-picker-default' ).val( wpColorPickerL10n.defaultString );
			else
				self.button.addClass( 'wp-picker-clear' ).val( wpColorPickerL10n.clear );

			el.wrap('<span class="wp-picker-input-wrap" />').after(self.button);

			el.iris( {
				target: self.pickerContainer,
				hide: true,
				width: 255,
				mode: 'hsv',
				palettes: self.options.palettes,
				change: function( event, ui ) {
					self.toggler.css( { backgroundColor: ui.color.toString() } );
					// check for a custom cb
					if ( $.isFunction( self.options.change ) )
						self.options.change.call( this, event, ui );
				}
			} );
			el.val( self.initialValue );
			self._addListeners();
			if ( ! self.options.hide )
				self.toggler.click();
		},
		_addListeners: function() {
			var self = this;

			self.toggler.click( function( event ){
				event.stopPropagation();
				self.element.toggle().iris( 'toggle' );
				self.button.toggleClass('hidden');
				self.toggler.toggleClass( 'wp-picker-open' );

				// close picker when you click outside it
				if ( self.toggler.hasClass( 'wp-picker-open' ) )
					$( "body" ).on( 'click', { wrap: self.wrap, toggler: self.toggler }, self._bodyListener );
				else
					$( "body" ).off( 'click', self._bodyListener );
			});

			self.element.change(function( event ) {
				var me = $(this),
					val = me.val();
				// Empty = clear
				if ( val === '' || val === '#' ) {
					self.toggler.css('backgroundColor', '');
					// fire clear callback if we have one
					if ( $.isFunction( self.options.clear ) )
						self.options.clear.call( this, event );
				}
			});

			// open a keyboard-focused closed picker with space or enter
			self.toggler.on('keyup', function( e ) {
				if ( e.keyCode === 13 || e.keyCode === 32 ) {
					e.preventDefault();
					self.toggler.trigger('click').next().focus();
				}
			});

			self.button.click( function( event ) {
				var me = $(this);
				if ( me.hasClass( 'wp-picker-clear' ) ) {
					self.element.val( '' );
					self.toggler.css('backgroundColor', '');
					if ( $.isFunction( self.options.clear ) )
						self.options.clear.call( this, event );
				} else if ( me.hasClass( 'wp-picker-default' ) ) {
					self.element.val( self.options.defaultColor ).change();
				}
			});
		},
		_bodyListener: function( event ) {
			if ( ! event.data.wrap.find( event.target ).length )
					event.data.toggler.click();
		},
		// $("#input").wpColorPicker('color') returns the current color
		// $("#input").wpColorPicker('color', '#bada55') to set
		color: function( newColor ) {
			if ( newColor === undef )
				return this.element.iris( "option", "color" );

			this.element.iris( "option", "color", newColor );
		},
		//$("#input").wpColorPicker('defaultColor') returns the current default color
		//$("#input").wpColorPicker('defaultColor', newDefaultColor) to set
		defaultColor: function( newDefaultColor ) {
			if ( newDefaultColor === undef )
				return this.options.defaultColor;

			this.options.defaultColor = newDefaultColor;
		}
	}

	$.widget( 'wp.wpColorPicker', ColorPicker );
}( jQuery ) );