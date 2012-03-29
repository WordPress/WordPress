(function( exports, $ ){
	var api = wp.customize;

	/*
	 * @param options
	 * - previewer - The Previewer instance to sync with.
	 * - method    - The method to use for syncing. Supports 'refresh' and 'postMessage'.
	 */
	api.Setting = api.Value.extend({
		initialize: function( id, value, options ) {
			var element;

			api.Value.prototype.initialize.call( this, value, options );

			this.id = id;
			this.method = this.method || 'refresh';

			element = $( '<input />', {
				type:  'hidden',
				value: this.get(),
				name:  api.settings.prefix + id
			});

			element.appendTo( this.previewer.form );
			this.element = new api.Element( element );

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

	api.Control = api.Class.extend({
		initialize: function( id, options ) {
			var control = this,
				nodes, radios, settings;

			this.params = {};
			$.extend( this, options || {} );

			this.id = id;
			this.selector = '#customize-control-' + id.replace( ']', '' ).replace( '[', '-' );
			this.container = $( this.selector );

			settings = $.map( this.params.settings, function( value ) {
				return value;
			});

			api.apply( api, settings.concat( function() {
				var key;

				control.settings = {};
				for ( key in control.params.settings ) {
					control.settings[ key ] = api( control.params.settings[ key ] );
				}

				control.setting = control.settings['default'] || null;
				control.ready();
			}) );

			control.elements = [];

			nodes  = this.container.find('[data-customize-setting-link]');
			radios = {};

			nodes.each( function() {
				var node = $(this),
					name;

				if ( node.is(':radio') ) {
					name = node.prop('name');
					if ( radios[ name ] )
						return;

					radios[ name ] = true;
					node = nodes.filter( '[name="' + name + '"]' );
				}

				api( node.data('customizeSettingLink'), function( setting ) {
					var element = new api.Element( node );
					control.elements.push( element );
					element.link( setting ).bind( function( to ) {
						setting( to );
					});
				});
			});
		},
		ready: function() {}
	});

	api.ColorControl = api.Control.extend({
		ready: function() {
			var control = this,
				picker, ui, text, toggle, update;

			picker = this.container.find( '.color-picker' );
			ui     = picker.find( '.color-picker-controls' );
			toggle = picker.find( 'a' );
			update = function( color ) {
				color = '#' + color;
				toggle.css( 'background', color );
				control.farbtastic.setColor( color );
			};

			picker.on( 'click', 'a', function() {
				ui.toggle();
			});

			this.farbtastic = $.farbtastic( picker.find('.farbtastic-placeholder'), function( color ) {
				control.setting.set( color.replace( '#', '' ) );
			});

			this.setting.bind( update );
			update( this.setting() );
		}
		// ,
		// 		validate: function( to ) {
		// 			return /^[a-fA-F0-9]{3}([a-fA-F0-9]{3})?$/.test( to ) ? to : null;
		// 		}
	});

	api.UploadControl = api.Control.extend({
		ready: function() {
			var control = this;

			this.params.removed = this.params.removed || '';

			this.uploader = new wp.Uploader({
				browser: this.container.find('.upload'),
				success: function( attachment ) {
					control.setting.set( attachment.url );
				}
			});

			this.remover = this.container.find('.remove');
			this.remover.click( function( event ) {
				control.setting.set( control.params.removed );
				event.preventDefault();
			});

			this.removerVisibility = $.proxy( this.removerVisibility, this );
			this.setting.bind( this.removerVisibility );
			this.removerVisibility( this.setting.get() );

			if ( this.params.context )
				control.uploader.param( 'post_data[context]', this.params.context );
		},
		removerVisibility: function( to ) {
			this.remover.toggle( to != this.params.removed );
		}
	});

	api.ImageControl = api.UploadControl.extend({
		ready: function() {
			var control = this;

			api.UploadControl.prototype.ready.call( this );

			this.thumbnail    = this.container.find('.thumbnail img');
			this.thumbnailSrc = $.proxy( this.thumbnailSrc, this );
			this.setting.bind( this.thumbnailSrc );

			this.library = this.container.find('.library');
			this.changer = this.container.find('.change');

			this.changer.click( function( event ) {
				control.library.toggle();
				event.preventDefault();
			});

			this.library.on( 'click', 'li', function( event ) {
				var tab = $(this),
					id = tab.data('customizeTab');

				event.preventDefault();

				if ( tab.hasClass('library-selected') )
					return;

				tab.siblings('.library-selected').removeClass('library-selected');
				tab.addClass('library-selected');

				control.library.find('div').hide().filter( function() {
					return $(this).data('customizeTab') === id;
				}).show();
			});

			this.library.on( 'click', 'a', function( event ) {
				control.setting.set( $(this).attr('href') );
				event.preventDefault();
			});
		},
		thumbnailSrc: function( to ) {
			if ( /^(https?:)?\/\//.test( to ) )
				this.thumbnail.prop( 'src', to ).show();
			else
				this.thumbnail.hide();
		}
	});

	// Change objects contained within the main customize object to Settings.
	api.defaultConstructor = api.Setting;

	// Create the collection of Control objects.
	api.control = new api.Values({ defaultConstructor: api.Control });

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

	api.controlConstructor = {
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

		$.each( api.settings.settings, function( id, data ) {
			api.set( id, id, data.value, {
				previewer: previewer
			} );
		});

		$.each( api.settings.controls, function( id, data ) {
			var constructor = api.controlConstructor[ data.type ] || api.Control,
				control;

			control = api.control.add( id, new constructor( id, {
				params: data,
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
		api( 'background_color', function( setting ) {
			setting.method = 'postMessage';
		});

		api.control( 'display_header_text', function( control ) {
			var last = '';

			control.elements[0].unlink();

			control.element = new api.Element( control.container.find('input') );
			control.element.set( 'blank' !== control.setting() );

			control.element.bind( function( to ) {
				if ( ! to )
					last = api.get( 'header_textcolor' );

				control.setting.set( to ? last : 'blank' );
			});

			control.setting.bind( function( to ) {
				control.element.set( 'blank' !== to );
			});
		});
	});

})( wp, jQuery );