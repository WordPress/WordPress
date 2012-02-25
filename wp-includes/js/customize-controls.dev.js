(function( exports, $ ){
	var api = wp.customize;

	api.Previewer = api.Messenger.extend({
		/**
		 * Requires params:
		 *  - iframe - a selector or jQuery element
		 *  - form   - a selector or jQuery element
		 *  - url    - the URL of preview frame
		 */
		initialize: function( params, options ) {
			$.extend( this, options || {} );

			this.iframe = api.ensure( params.iframe );
			this.form   = api.ensure( params.form );

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
		refresh: function() {
			this.submit({
				target: this.iframe.prop('name'),
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

		$('#refresh').click( function() {
			previewer.refresh();
			return false;
		});

		// Fetch prefixed settings.
		$('[name^="' + api.settings.prefix + '"]').each( function() {
			// console.log( this.name );
		});
	});

})( wp, jQuery );