(function( exports, $ ){
	var api = wp.customize,
		debounce;

	debounce = function( fn, delay, context ) {
		var timeout;
		return function() {
			var args = arguments;

			context = context || this;

			clearTimeout( timeout );
			timeout = setTimeout( function() {
				timeout = null;
				fn.apply( context, args );
			}, delay );
		};
	};

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
				self.send( 'scroll', 0 );
				self.send( 'url', $(this).prop('href') );
			});

			// You cannot submit forms.
			// @todo: Namespace customizer settings so that we can mix the
			//        $_POST data with the customize setting $_POST data.
			this.body.on( 'submit.preview', 'form', function( event ) {
				event.preventDefault();
			});

			this.window = $( window );
			this.window.on( 'scroll.preview', debounce( function() {
				self.send( 'scroll', self.window.scrollTop() );
			}, 200 ));

			this.bind( 'scroll', function( distance ) {
				self.window.scrollTop( distance );
			});
		}
	});

	$( function() {
		api.settings = window._wpCustomizeSettings;
		if ( ! api.settings )
			return;

		var preview, bg;

		preview = new api.Preview( window.location.href );

		$.each( api.settings.values, function( id, value ) {
			api.create( id, value );
		});

		preview.bind( 'setting', function( args ) {
			var value = api( args.shift() );
			if ( value )
				value.set.apply( value, args );
		});

		/* Custom Backgrounds */
		bg = $.map(['color', 'image', 'position_x', 'repeat', 'attachment'], function( prop ) {
			return 'background_' + prop;
		});

		api.when.apply( api, bg ).done( function( color, image, position_x, repeat, attachment ) {
			var body = $(document.body),
				head = $('head'),
				style = $('#custom-background-css'),
				update;

			// If custom backgrounds are active and we can't find the
			// default output, bail.
			if ( body.hasClass('custom-background') && ! style.length )
				return;

			update = function() {
				var css = '';

				// The body will support custom backgrounds if either
				// the color or image are set.
				//
				// See get_body_class() in /wp-includes/post-template.php
				body.toggleClass( 'custom-background', !! ( color() || image() ) );

				if ( color() )
					css += 'background-color: ' + color() + ';';

				if ( image() ) {
					css += 'background-image: url("' + image() + '");';
					css += 'background-position: top ' + position_x() + ';';
					css += 'background-repeat: ' + repeat() + ';';
					css += 'background-position: top ' + attachment() + ';';
				}

				// Refresh the stylesheet by removing and recreating it.
				style.remove();
				style = $('<style type="text/css" id="custom-background-css">body.custom-background { ' + css + ' }</style>').appendTo( head );
			};

			$.each( arguments, function() {
				this.bind( update );
			});
		});
	});

})( wp, jQuery );
