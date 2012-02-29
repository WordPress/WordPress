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
		},
		loader: function() {
			var self = this,
				name;

			if ( this.loading )
				return this.loading;

			name = this.iframe.prop('name');

			this.loading = $('<iframe />', {
				name: name + '-loading-' + this.loaderUuid++
			}).appendTo( this.container );

			this.loading.one( 'load', function() {
				self.iframe.remove();
				self.iframe = self.loading;
				delete self.loading;
				self.iframe.prop( 'name', name );
			});

			return this.loading;
		},
		refresh: function() {
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

		var previewer,
			controls = $('[name^="' + api.settings.prefix + '"]');

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
		$('.control-section h3').click( function() {
			$( this ).siblings('ul').slideToggle( 200 );
			$( this ).toggleClass( 'open' );
			return false;
		});

		// Button bindings.
		$('#save').click( function() {
			previewer.submit();
			return false;
		});

		// Fetch prefixed settings.
		$('[name^="' + api.settings.prefix + '"]').each( function() {
			// console.log( this.name );
		});
	});

})( wp, jQuery );