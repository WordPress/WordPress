(function( exports, $ ){
	var api = wp.customize;

	/*
	 * @param options
	 * - previewer - The Previewer instance to sync with.
	 * - method    - The method to use for syncing. Supports 'refresh' and 'postMessage'.
	 */
	api.Control = api.Value.extend({
		initialize: function( id, value, options ) {
			var name = '[name="' + api.settings.prefix + id + '"]';

			this.params = {};
			api.Value.prototype.initialize.call( this, value, options );

			this.id = id;
			this.container = $( '#customize-control-' + id );
			this.element = this.element || new api.Element( this.container.find( name ) );

			this.method = this.method || 'refresh';

			this.element.link( this );
			this.link( this.element );

			this.bind( this.sync );
		},
		sync: function() {
			switch ( this.method ) {
				case 'refresh':
					return this.previewer.refresh();
				case 'postMessage':
					return this.previewer.send( 'setting', [ this.id, this() ] );
			}
		}
	});

	api.ColorControl = api.Control.extend({
		initialize: function( id, value, options ) {
			var self = this,
				picker, ui, text, toggle, update;

			api.Control.prototype.initialize.call( this, id, value, options );

			picker = this.container.find( '.color-picker' );
			ui     = picker.find( '.color-picker-controls' );
			toggle = picker.find( 'a' );
			update = function( color ) {
				color = '#' + color;
				toggle.css( 'background', color );
				self.farbtastic.setColor( color );
			};

			this.input = new api.Element( ui.find( 'input' ) ); // Find text input.

			this.link( this.input );
			this.input.link( this );

			picker.on( 'click', 'a', function() {
				ui.toggle();
			});

			this.farbtastic = $.farbtastic( picker.find('.farbtastic-placeholder'), function( color ) {
				self.set( color.replace( '#', '' ) );
			});

			this.bind( update );
			update( this() );
		},
		validate: function( to ) {
			return /^[a-fA-F0-9]{3}([a-fA-F0-9]{3})?$/.test( to ) ? to : null;
		}
	});

	api.UploadControl = api.Control.extend({
		initialize: function( id, value, options ) {
			var control = this;

			api.Control.prototype.initialize.call( this, id, value, options );
			this.params.removed = this.params.removed || '';

			this.uploader = new wp.Uploader({
				browser: this.container.find('.upload'),
				success: function( attachment ) {
					control.set( attachment.url );
				}
			});

			this.remover = this.container.find('.remove');
			this.remover.click( function( event ) {
				control.set( control.params.removed );
				event.preventDefault();
			});

			this.bind( this.removerVisibility );
			this.removerVisibility( this.get() );

			if ( this.params.context )
				control.uploader.param( 'post_data[context]', this.params.context );
		},
		removerVisibility: function( to ) {
			this.remover.toggle( to != this.params.removed );
		}
	});

	api.ImageControl = api.UploadControl.extend({
		initialize: function( id, value, options ) {
			api.UploadControl.prototype.initialize.call( this, id, value, options );

			this.thumbnail = this.container.find('.thumbnail img');
			this.bind( this.thumbnailSrc );
		},
		thumbnailSrc: function( to ) {
			if ( /^(http:\/\/|https:\/\/|\/\/)/.test( to ) )
				this.thumbnail.prop( 'src', to ).show();
			else
				this.thumbnail.hide();
		}
	});

	// Change objects contained within the main customize object to Settings.
	api.defaultConstructor = api.Setting;

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

			api.Messenger.prototype.initialize.call( this, params.url, this.iframe[0].contentWindow );

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
			this.targetWindow( this.iframe[0].contentWindow );
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

	api.controls = {
		color:  api.ColorControl,
		upload: api.UploadControl,
		image:  api.ImageControl
	};

	$( function() {
		if ( ! api.settings )
			return;

		// Initialize Previewer
		var previewer = new api.Previewer({
			iframe: '#customize-preview iframe',
			form:   '#customize-controls',
			url:    api.settings.preview
		});

		$.each( api.settings.controls, function( id, data ) {
			var constructor = api.controls[ data.control ] || api.Control,
				control;

			control = api.add( id, new constructor( id, data.value, {
				params: data.params,
				previewer: previewer
			} ) );

			if ( data.visibility ) {
				api( data.visibility.id, function( other ) {
					if ( 'boolean' === typeof data.visibility.value ) {
						other.bind( function( to ) {
							control.container.toggle( !! to == data.visibility.value );
						});
					} else {
						other.bind( function( to ) {
							control.container.toggle( to == data.visibility.value );
						});
					}
				});
			}
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

		// Background color uses postMessage by default
		api( 'background_color', function( control ) {
			control.method = 'postMessage';
		});
	});

})( wp, jQuery );