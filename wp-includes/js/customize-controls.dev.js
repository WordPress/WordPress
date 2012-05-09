(function( exports, $ ){
	var api = wp.customize;

	/*
	 * @param options
	 * - previewer - The Previewer instance to sync with.
	 * - transport - The transport to use for previewing. Supports 'refresh' and 'postMessage'.
	 */
	api.Setting = api.Value.extend({
		initialize: function( id, value, options ) {
			var element;

			api.Value.prototype.initialize.call( this, value, options );

			this.id = id;
			this.transport = this.transport || 'refresh';

			this.bind( this.preview );
		},
		preview: function() {
			switch ( this.transport ) {
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
					element.sync( setting );
					element.set( setting() );
				});
			});
		},

		ready: function() {},

		dropdownInit: function() {
			var control  = this,
				statuses = this.container.find('.dropdown-status'),
				params   = this.params,
				update   = function( to ) {
					if ( typeof	to === 'string' && params.statuses && params.statuses[ to ] )
						statuses.html( params.statuses[ to ] ).show();
					else
						statuses.hide();
				};

			// Support the .dropdown class to open/close complex elements
			this.container.on( 'click', '.dropdown', function( event ) {
				event.preventDefault();
				control.container.toggleClass('open');
			});

			this.setting.bind( update );
			update( this.setting() );
		}
	});

	api.ColorControl = api.Control.extend({
		ready: function() {
			var control = this,
				spot, text, update;

			spot   = this.container.find('.dropdown-content');
			update = function( color ) {
				color = color ? '#' + color : '';
				spot.css( 'background', color );
				control.farbtastic.setColor( color );
			};

			this.farbtastic = $.farbtastic( this.container.find('.farbtastic-placeholder'), function( color ) {
				control.setting.set( color.replace( '#', '' ) );
			});

			this.setting.bind( update );
			update( this.setting() );

			this.dropdownInit();
		}
	});

	api.UploadControl = api.Control.extend({
		ready: function() {
			var control = this;

			this.params.removed = this.params.removed || '';

			this.success = $.proxy( this.success, this );

			this.uploader = new wp.Uploader({
				container: this.container,
				browser:   this.container.find('.upload'),
				dropzone:  this.container.find('.upload-dropzone'),
				success:   this.success
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
		success: function( attachment ) {
			this.setting.set( attachment.url );
		},
		removerVisibility: function( to ) {
			this.remover.toggle( to != this.params.removed );
		}
	});

	api.ImageControl = api.UploadControl.extend({
		ready: function() {
			var control = this,
				panels;

			api.UploadControl.prototype.ready.call( this );

			this.thumbnail    = this.container.find('.preview-thumbnail img');
			this.thumbnailSrc = $.proxy( this.thumbnailSrc, this );
			this.setting.bind( this.thumbnailSrc );

			this.library = this.container.find('.library');

			// Generate tab objects
			this.tabs = {};
			panels    = this.library.find('.library-content');

			this.library.children('ul').children('li').each( function() {
				var link  = $(this),
					id    = link.data('customizeTab'),
					panel = panels.filter('[data-customize-tab="' + id + '"]');

				control.tabs[ id ] = {
					both:  link.add( panel ),
					link:  link,
					panel: panel
				};
			});

			// Select a tab
			this.selected = this.tabs[ panels.first().data('customizeTab') ];
			this.selected.both.addClass('library-selected');

			// Bind tab switch events
			this.library.children('ul').on( 'click', 'li', function( event ) {
				var id  = $(this).data('customizeTab'),
					tab = control.tabs[ id ];

				event.preventDefault();

				if ( tab.link.hasClass('library-selected') )
					return;

				control.selected.both.removeClass('library-selected');
				control.selected = tab;
				control.selected.both.addClass('library-selected');
			});

			this.library.on( 'click', 'a', function( event ) {
				var value = $(this).data('customizeImageValue');

				if ( value ) {
					control.setting.set( value );
					event.preventDefault();
				}
			});

			if ( this.tabs.uploaded ) {
				this.tabs.uploaded.target = this.library.find('.uploaded-target');
				if ( ! this.tabs.uploaded.panel.find('.thumbnail').length )
					this.tabs.uploaded.both.addClass('hidden');
			}

			this.dropdownInit();
		},
		success: function( attachment ) {
			api.UploadControl.prototype.success.call( this, attachment );

			// Add the uploaded image to the uploaded tab.
			if ( this.tabs.uploaded && this.tabs.uploaded.target.length ) {
				this.tabs.uploaded.both.removeClass('hidden');

				$( '<a href="#" class="thumbnail"></a>' )
					.data( 'customizeImageValue', attachment.url )
					.append( '<img src="' +  attachment.url+ '" />' )
					.appendTo( this.tabs.uploaded.target );
			}
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
		 *  - container - a selector or jQuery element
		 *  - url       - the URL of preview frame
		 */
		initialize: function( params, options ) {
			var self = this;

			$.extend( this, options || {} );

			this.loaded = $.proxy( this.loaded, this );

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

			this.container = api.ensure( params.container );

			api.Messenger.prototype.initialize.call( this, params.url );

			this.scroll = 0;
			this.bind( 'scroll', function( distance ) {
				this.scroll = distance;
			});

			// We're dynamically generating the iframe, so the origin is set
			// to the current window's location, not the url's.
			this.origin.unlink( this.url ).set( window.location.href );

			this.bind( 'url', function( url ) {
				// Bail if we're navigating to the current url, to a different origin, or wp-admin.
				if ( this.url() == url || 0 !== url.indexOf( this.origin() + '/' ) || -1 !== url.indexOf( 'wp-admin' ) )
					return;

				this.url( url );
				this.refresh();
			});
		},
		loader: function() {
			if ( this.loading )
				return this.loading;

			this.loading = $('<iframe />').appendTo( this.container );

			return this.loading;
		},
		loaded: function() {
			if ( this.iframe )
				this.iframe.remove();

			this.iframe = this.loading;
			delete this.loading;

			this.targetWindow( this.iframe[0].contentWindow );
			this.send( 'scroll', this.scroll );
		},
		query: function() {},
		refresh: function() {
			var self = this;

			if ( this.request )
				this.request.abort();

			this.request = $.ajax( this.url(), {
				type: 'POST',
				data: this.query() || {},
				success: function( response ) {
					var iframe = self.loader()[0].contentWindow;

					self.loader().one( 'load', self.loaded );

					iframe.document.open();
					iframe.document.write( response );
					iframe.document.close();
				},
				xhrFields: {
					withCredentials: true
				}
			} );
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
		api.settings = window._wpCustomizeSettings;
		if ( ! api.settings )
			return;

		// Initialize Previewer
		var body = $( document.body ),
			query, previewer, parent;

		// Prevent the form from saving when enter is pressed.
		$('#customize-controls').on( 'keydown', function( e ) {
			if ( 13 === e.which ) // Enter
				e.preventDefault();
		});

		previewer = new api.Previewer({
			container: '#customize-preview',
			form:      '#customize-controls',
			url:       api.settings.preview
		}, {
			query: function() {
				return {
					customize:  'on',
					theme:      api.settings.theme,
					customized: JSON.stringify( api.get() )
				};
			},

			nonce: $('#_wpnonce').val(),

			save: function() {
				var query = $.extend( this.query(), {
						action: 'customize_save',
						nonce:  this.nonce
					}),
					request = $.post( api.settings.ajax, query );

				body.addClass('saving');
				request.always( function() {
					body.removeClass('saving');
				});
			}
		});

		$.each( api.settings.settings, function( id, data ) {
			api.set( id, id, data.value, {
				transport: data.transport,
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
		});

		// Load the preview frame.
		previewer.refresh();

		// Temporary accordion code.
		$('.customize-section-title').click( function() {
			$( this ).parents('.customize-section').toggleClass( 'open' );
			return false;
		});

		// Button bindings.
		$('#save').click( function( event ) {
			previewer.save();
			event.preventDefault();
		});

		$('.collapse-sidebar').click( function( event ) {
			body.toggleClass( 'collapsed' );
			event.preventDefault();
		});

		// Create a potential postMessage connection with the parent frame.
		parent = new api.Messenger( api.settings.parent );

		// If we receive a 'back' event, we're inside an iframe.
		// Send any clicks to the 'Return' link to the parent page.
		parent.bind( 'back', function( text ) {
			$('.back').text( text ).click( function( event ) {
				event.preventDefault();
				parent.send( 'close' );
			});
		});

		// Initialize the connection with the parent frame.
		parent.send( 'ready' );

		// Control visibility for default controls
		$.each({
			'background_image': {
				controls: [ 'background_repeat', 'background_position_x', 'background_attachment' ],
				callback: function( to ) { return !! to }
			},
			'show_on_front': {
				controls: [ 'page_on_front', 'page_for_posts' ],
				callback: function( to ) { return 'page' === to }
			},
			'header_textcolor': {
				controls: [ 'header_textcolor' ],
				callback: function( to ) { return 'blank' !== to }
			}
		}, function( settingId, o ) {
			api( settingId, function( setting ) {
				$.each( o.controls, function( i, controlId ) {
					api.control( controlId, function( control ) {
						var visibility = function( to ) {
							control.container.toggle( o.callback( to ) );
						};

						visibility( setting.get() );
						setting.bind( visibility );
					});
				});
			});
		});

		// Juggle the two controls that use header_textcolor
		api.control( 'display_header_text', function( control ) {
			var last = '';

			control.elements[0].unsync( api( 'header_textcolor' ) );

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