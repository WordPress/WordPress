(function( exports, $ ){
	var api = wp.customize;

	api.Previewer = api.Messenger.extend({
		refreshBuffer: 250,

		/**
		 * Requires params:
		 *  - iframe - a selector or jQuery element
		 *  - form   - a selector or jQuery element
		 *  - url    - the URL of preview frame
		 */
		initialize: function( params, options ) {
			$.extend( this, options || {} );

			this.loaded = $.proxy( this.loaded, this );

			this.loaderUuid = 0;

			/*
			 * Wrap this.refresh to prevent it from hammering the servers:
			 *
			 * If refresh is called once and no other refresh requests are
			 * loading, trigger the request immediately.
			 *
			 * If refresh is called while another refresh request is loading,
			 * debounce the refresh requests:
			 * 1. Stop the loading request (as it is instantly outdated).
			 * 2. Trigger the new request once refresh hasn't been called for
			 *    self.refreshBuffer milliseconds.
			 */
			this.refresh = (function( self ) {
				var refresh  = self.refresh,
					callback = function() {
						timeout = null;
						refresh.call( self );
					},
					timeout;

				return function() {
					if ( typeof timeout !== 'number' ) {
						if ( self.loading ) {
							self.loading.remove();
							delete self.loading;
							self.loader();
						} else {
							return callback();
						}
					}

					clearTimeout( timeout );
					timeout = setTimeout( callback, self.refreshBuffer );
				};
			})( this );

			this.iframe = api.ensure( params.iframe );
			this.form   = api.ensure( params.form );
			this.name   = this.iframe.prop('name');

			this.container = this.iframe.parent();

			api.Messenger.prototype.initialize.call( this, params.url, {
				targetWindow: this.iframe[0].contentWindow
			});

			this._formOriginalProps = {
				target: this.form.prop('target'),
				action: this.form.prop('action')
			};

			this.bind( 'url', function( url ) {
				// Bail if we're navigating to the current url, to a different origin, or wp-admin.
				if ( this.url() == url || 0 !== url.indexOf( this.origin() + '/' ) || -1 !== url.indexOf( 'wp-admin' )  )
					return;

				this.url( url );
				this.refresh();
			});

			this.refresh();

			// Prevent the form from saving when enter is pressed.
			this.form.on( 'keydown', function( e ) {
				if ( 13 === e.which ) // Enter
					e.preventDefault();
			});
		},
		loader: function() {
			if ( this.loading )
				return this.loading;

			this.loading = $('<iframe />', {
				name: this.name + '-loading-' + this.loaderUuid++
			}).appendTo( this.container );

			return this.loading;
		},
		loaded: function() {
			this.iframe.remove();
			this.iframe = this.loading;
			delete this.loading;
			this.iframe.prop( 'name', this.name );
		},
		refresh: function() {
			this.loader().one( 'load', this.loaded );

			this.submit({
				target: this.loader().prop('name'),
				action: this.url()
			});
		},
		submit: function( props ) {
			if ( props )
				this.form.prop( props );
			this.form.submit();
			if ( props )
				this.form.prop( this._formOriginalProps );
		}
	});

	/* =====================================================================
	 * Ready.
	 * ===================================================================== */

	$( function() {
		if ( ! api.settings )
			return;

		var controls = $('[name^="' + api.settings.prefix + '"]'),
			previewer, pickers, validateColor;

		// Initialize Previewer
		previewer = new api.Previewer({
			iframe: '#customize-preview iframe',
			form:   '#customize-controls',
			url:    api.settings.preview
		});

		$.each( api.settings.values, function( id, value ) {
			var elements = controls.filter( '[name="' + api.settings.prefix + id + '"]' ),
				setting = api.set( id, value );

			setting.control = new wp.customize.Element( elements );

			setting.control.link( setting );
			setting.link( setting.control );

			setting.bind( previewer.refresh );
		});

		// Temporary accordion code.
		$('.customize-section-title').click( function() {
			$( this ).parents('.customize-section').toggleClass( 'open' );
			return false;
		});

		// Button bindings.
		$('#save').click( function() {
			previewer.submit();
			return false;
		});

		// Set up color pickers
		pickers = $('.color-picker');
		validateColor = function( to ) {
			return /^[a-fA-F0-9]{3}([a-fA-F0-9]{3})?$/.test( to ) ? to : null;
		};

		$( '.farbtastic-placeholder', pickers ).each( function() {
			var picker = $(this),
				text   = new api.Element( picker.siblings('input') ),
				parent = picker.parent(),
				toggle = parent.siblings('a'),
				value  = api( parent.siblings('input').prop('name').replace( api.settings.prefix, '' ) ),
				farb;

			value.validate = validateColor;
			text.link( value );
			value.link( text );

			farb = $.farbtastic( this, function( color ) {
				value.set( color.replace( '#', '' ) );
			});

			value.bind( function( color ) {
				color = '#' + color;
				toggle.css( 'background', color );
				farb.setColor( color );
			});
		});

		$('.color-picker a').click( function(e) {
			$(this).siblings('div').toggle();
		});

		// Fetch prefixed settings.
		$('[name^="' + api.settings.prefix + '"]').each( function() {
			// console.log( this.name );
		});
	});

})( wp, jQuery );