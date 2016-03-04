/*
 * Script run inside a Customizer preview frame.
 */
(function( exports, $ ){
	var api = wp.customize,
		debounce;

	/**
	 * Returns a debounced version of the function.
	 *
	 * @todo Require Underscore.js for this file and retire this.
	 */
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

	/**
	 * @constructor
	 * @augments wp.customize.Messenger
	 * @augments wp.customize.Class
	 * @mixes wp.customize.Events
	 */
	api.Preview = api.Messenger.extend({
		/**
		 * @param {object} params  - Parameters to configure the messenger.
		 * @param {object} options - Extend any instance parameter or method with this object.
		 */
		initialize: function( params, options ) {
			var self = this;

			api.Messenger.prototype.initialize.call( this, params, options );

			this.body = $( document.body );
			this.body.on( 'click.preview', 'a', function( event ) {
				var link, isInternalJumpLink;
				link = $( this );
				isInternalJumpLink = ( '#' === link.attr( 'href' ).substr( 0, 1 ) );
				event.preventDefault();

				if ( isInternalJumpLink && '#' !== link.attr( 'href' ) ) {
					$( link.attr( 'href' ) ).each( function() {
						this.scrollIntoView();
					} );
				}

				/*
				 * Note the shift key is checked so shift+click on widgets or
				 * nav menu items can just result on focusing on the corresponding
				 * control instead of also navigating to the URL linked to.
				 */
				if ( event.shiftKey || isInternalJumpLink ) {
					return;
				}
				self.send( 'scroll', 0 );
				self.send( 'url', link.prop( 'href' ) );
			});

			// You cannot submit forms.
			// @todo: Allow form submissions by mixing $_POST data with the customize setting $_POST data.
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
		var bg, setValue;

		api.settings = window._wpCustomizeSettings;
		if ( ! api.settings ) {
			return;
		}

		api.preview = new api.Preview({
			url: window.location.href,
			channel: api.settings.channel
		});

		/**
		 * Create/update a setting value.
		 *
		 * @param {string}  id            - Setting ID.
		 * @param {*}       value         - Setting value.
		 * @param {boolean} [createDirty] - Whether to create a setting as dirty. Defaults to false.
		 */
		setValue = function( id, value, createDirty ) {
			var setting = api( id );
			if ( setting ) {
				setting.set( value );
			} else {
				createDirty = createDirty || false;
				setting = api.create( id, value, {
					id: id
				} );

				// Mark dynamically-created settings as dirty so they will get posted.
				if ( createDirty ) {
					setting._dirty = true;
				}
			}
		};

		api.preview.bind( 'settings', function( values ) {
			$.each( values, setValue );
		});

		api.preview.trigger( 'settings', api.settings.values );

		$.each( api.settings._dirty, function( i, id ) {
			var setting = api( id );
			if ( setting ) {
				setting._dirty = true;
			}
		} );

		api.preview.bind( 'setting', function( args ) {
			var createDirty = true;
			setValue.apply( null, args.concat( createDirty ) );
		});

		api.preview.bind( 'sync', function( events ) {
			$.each( events, function( event, args ) {
				api.preview.trigger( event, args );
			});
			api.preview.send( 'synced' );
		});

		api.preview.bind( 'active', function() {
			api.preview.send( 'nonce', api.settings.nonce );

			api.preview.send( 'documentTitle', document.title );
		});

		api.preview.bind( 'saved', function( response ) {
			api.trigger( 'saved', response );
		} );

		api.bind( 'saved', function() {
			api.each( function( setting ) {
				setting._dirty = false;
			} );
		} );

		api.preview.bind( 'nonce-refresh', function( nonce ) {
			$.extend( api.settings.nonce, nonce );
		} );

		/*
		 * Send a message to the parent customize frame with a list of which
		 * containers and controls are active.
		 */
		api.preview.send( 'ready', {
			activePanels: api.settings.activePanels,
			activeSections: api.settings.activeSections,
			activeControls: api.settings.activeControls
		} );

		// Display a loading indicator when preview is reloading, and remove on failure.
		api.preview.bind( 'loading-initiated', function () {
			$( 'body' ).addClass( 'wp-customizer-unloading' );
		});
		api.preview.bind( 'loading-failed', function () {
			$( 'body' ).removeClass( 'wp-customizer-unloading' );
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
					css += 'background-attachment: ' + attachment() + ';';
				}

				// Refresh the stylesheet by removing and recreating it.
				style.remove();
				style = $('<style type="text/css" id="custom-background-css">body.custom-background { ' + css + ' }</style>').appendTo( head );
			};

			$.each( arguments, function() {
				this.bind( update );
			});
		});

		/**
		 * Custom Logo
		 *
		 * The custom logo setting only contains the attachment ID. To avoid having to send an AJAX request to get more
		 * data, we send a separate message with the attachment data we get from the Customizer's media modal.
		 * Therefore first callback handles only the event of a new logo being selected.
		 *
		 * We don't need any information about a removed logo, so the second callback only handles that.
		 *
		 * @since 4.5.0
		 */
		api.preview.bind( 'custom_logo-attachment-data', function( attachment ) {
			var $logo  = $( '.custom-logo' ),
				size   = $logo.data( 'size' ),
				srcset = [];

			// If the source was smaller than the size required by the theme, give the biggest we've got.
			if ( ! attachment.sizes[ size ] ) {
				size = 'full';
			}

			_.each( attachment.sizes, function( size ) {
				srcset.push( size.url + ' ' + size.width + 'w' );
			} );

			$logo.attr( {
				height: attachment.sizes[ size ].height,
				width:  attachment.sizes[ size ].width,
				src:    attachment.sizes[ size ].url,
				srcset: srcset
			} );

			$( '.custom-logo-link' ).show();
		} );

		api( 'custom_logo', function( setting ) {
			setting.bind( function( newValue ) {
				if ( ! newValue ) {
					$( '.custom-logo-link' ).hide();
				}
			} );

			// Focus on the control when the logo is clicked, if there is no custom_logo partial.
			if ( ! api.selectiveRefresh || ! api.selectiveRefresh.partial.has( 'custom_logo' ) ) {
				$( document.body ).on( 'click', '.custom-logo-link', function( e ) {
					if ( ! e.shiftKey ) {
						return;
					}
					api.preview.send( 'focus-control-for-setting', 'custom_logo' );
				} );
				$( '.custom-logo-link' ).attr( 'title', api.settings.l10n.shiftClickToEdit );
			}
		} );

		api.trigger( 'preview-ready' );
	});

})( wp, jQuery );
