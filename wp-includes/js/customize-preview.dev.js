(function( exports, $ ){
	var api = wp.customize;

	api.Preview = api.Messenger.extend({
		/**
		 * Requires params:
		 *  - url    - the URL of preview frame
		 *
		 * @todo: Perhaps add a window.onbeforeunload dialog in case the theme
		 *        somehow attempts to leave the page and we don't catch it
		 *        (which really shouldn't happen).
		 */
		initialize: function( url, options ) {
			var self = this;

			api.Messenger.prototype.initialize.call( this, url, null, options );

			this.body = $( document.body );
			this.body.on( 'click.preview', 'a', function( event ) {
				event.preventDefault();
				self.send( 'url', $(this).attr('href') );
			});

			// You cannot submit forms.
			// @todo: Namespace customizer settings so that we can mix the
			//        $_POST data with the customize setting $_POST data.
			this.body.on( 'submit.preview', 'form', function( event ) {
				event.preventDefault();
			});
		}
	});

	$( function() {
		if ( ! api.settings )
			return;

		var preview, body;

		preview = new api.Preview( api.settings.parent );

		$.each( api.settings.values, function( id, value ) {
			api.set( id, value );
		});

		preview.bind( 'setting', function( args ) {
			api.set.apply( api, args );
		});

		body = $(document.body);
		// Auto update background color by default
		api( 'background_color', function( value ) {
			value.bind( function( to ) {
				body.css( 'background-color', '#' + to );
			});
		});
	});

})( wp, jQuery );