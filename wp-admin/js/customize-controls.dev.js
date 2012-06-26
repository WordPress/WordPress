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
				rhex, spot, input, text, update;

			rhex   = /^#([A-Fa-f0-9]{3}){0,2}$/;
			spot   = this.container.find('.dropdown-content');
			input  = new api.Element( this.container.find('.color-picker-hex') );
			update = function( color ) {
				spot.css( 'background', color );
				control.farbtastic.setColor( color );
			};

			this.farbtastic = $.farbtastic( this.container.find('.farbtastic-placeholder'), control.setting.set );

			// Only pass through values that are valid hexes/empty.
			input.sync( this.setting ).validate = function( to ) {
				return rhex.test( to ) ? to : null;
			};

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

			this.uploader = $.extend({
				container: this.container,
				browser:   this.container.find('.upload'),
				dropzone:  this.container.find('.upload-dropzone'),
				success:   this.success
			}, this.uploader || {} );

			if ( this.uploader.supported ) {
				if ( control.params.context )
					control.uploader.param( 'post_data[context]', this.params.context );

				control.uploader.param( 'post_data[theme]', api.settings.theme.stylesheet );
			}

			this.uploader = new wp.Uploader( this.uploader );

			this.remover = this.container.find('.remove');
			this.remover.click( function( event ) {
				control.setting.set( control.params.removed );
				event.preventDefault();
			});

			this.removerVisibility = $.proxy( this.removerVisibility, this );
			this.setting.bind( this.removerVisibility );
			this.removerVisibility( this.setting.get() );
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

			this.uploader = {
				init: function( up ) {
					var fallback, button;

					if ( this.supports.dragdrop )
						return;

					// Maintain references while wrapping the fallback button.
					fallback = control.container.find( '.upload-fallback' );
					button   = fallback.children().detach();

					this.browser.detach().empty().append( button );
					fallback.append( this.browser ).show();
				}
			};

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

			// Bind events to switch image urls.
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

				attachment.element = $( '<a href="#" class="thumbnail"></a>' )
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

	api.PreviewFrame = api.Messenger.extend({
		sensitivity: 2000,

		initialize: function( params, options ) {
			var deferred = $.Deferred(),
				self     = this;

			// This is the promise object.
			deferred.promise( this );

			this.container = params.container;
			this.signature = params.signature;

			$.extend( params, { channel: api.PreviewFrame.uuid() });

			api.Messenger.prototype.initialize.call( this, params, options );

			this.add( 'previewUrl', params.previewUrl );

			this.query = $.extend( params.query || {}, { customize_messenger_channel: this.channel() });

			this.run( deferred );
		},

		run: function( deferred ) {
			var self   = this,
				loaded = false,
				ready  = false;

			if ( this._ready )
				this.unbind( 'ready', this._ready );

			this._ready = function() {
				ready = true;

				if ( loaded )
					deferred.resolveWith( self );
			};

			this.bind( 'ready', this._ready );

			this.request = $.ajax( this.previewUrl(), {
				type: 'POST',
				data: this.query,
				xhrFields: {
					withCredentials: true
				}
			} );

			this.request.fail( function() {
				deferred.rejectWith( self, [ 'request failure' ] );
			});

			this.request.done( function( response ) {
				var location = self.request.getResponseHeader('Location'),
					signature = self.signature,
					index;

				// Check if the location response header differs from the current URL.
				// If so, the request was redirected; try loading the requested page.
				if ( location && location != self.previewUrl() ) {
					deferred.rejectWith( self, [ 'redirect', location ] );
					return;
				}

				// Check if the user is not logged in.
				if ( '0' === response ) {
					self.login( deferred );
					return;
				}

				// Check for cheaters.
				if ( '-1' === response ) {
					deferred.rejectWith( self, [ 'cheatin' ] );
					return;
				}

				// Check for a signature in the request.
				index = response.lastIndexOf( signature );
				if ( -1 === index || index < response.lastIndexOf('</html>') ) {
					deferred.rejectWith( self, [ 'unsigned' ] );
					return;
				}

				// Strip the signature from the request.
				response = response.slice( 0, index ) + response.slice( index + signature.length );

				// Create the iframe and inject the html content.
				self.iframe = $('<iframe />').appendTo( self.container );

				// Bind load event after the iframe has been added to the page;
				// otherwise it will fire when injected into the DOM.
				self.iframe.one( 'load', function() {
					loaded = true;

					if ( ready ) {
						deferred.resolveWith( self );
					} else {
						setTimeout( function() {
							deferred.rejectWith( self, [ 'ready timeout' ] );
						}, self.sensitivity );
					}
				});

				self.targetWindow( self.iframe[0].contentWindow );

				self.targetWindow().document.open();
				self.targetWindow().document.write( response );
				self.targetWindow().document.close();
			});
		},

		login: function( deferred ) {
			var self = this,
				reject;

			reject = function() {
				deferred.rejectWith( self, [ 'logged out' ] );
			};

			if ( this.triedLogin )
				return reject();

			// Check if we have an admin cookie.
			$.get( api.settings.url.ajax, {
				action: 'logged-in'
			}).fail( reject ).done( function( response ) {
				var iframe;

				if ( '1' !== response )
					reject();

				iframe = $('<iframe src="' + self.previewUrl() + '" />').hide();
				iframe.appendTo( self.container );
				iframe.load( function() {
					self.triedLogin = true;

					iframe.remove();
					self.run( deferred );
				});
			});
		},

		destroy: function() {
			api.Messenger.prototype.destroy.call( this );
			this.request.abort();

			if ( this.iframe )
				this.iframe.remove();

			delete this.request;
			delete this.iframe;
			delete this.targetWindow;
		}
	});

	(function(){
		var uuid = 0;
		api.PreviewFrame.uuid = function() {
			return 'preview-' + uuid++;
		};
	}());

	api.Previewer = api.Messenger.extend({
		refreshBuffer: 250,

		/**
		 * Requires params:
		 *  - container  - a selector or jQuery element
		 *  - previewUrl - the URL of preview frame
		 */
		initialize: function( params, options ) {
			var self = this,
				rscheme = /^https?/,
				url;

			$.extend( this, options || {} );

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
							self.abort();
						} else {
							return callback();
						}
					}

					clearTimeout( timeout );
					timeout = setTimeout( callback, self.refreshBuffer );
				};
			})( this );

			this.container   = api.ensure( params.container );
			this.allowedUrls = params.allowedUrls;
			this.signature   = params.signature;

			params.url = window.location.href;

			api.Messenger.prototype.initialize.call( this, params );

			this.add( 'scheme', this.origin() ).link( this.origin ).setter( function( to ) {
				var match = to.match( rscheme );
				return match ? match[0] : '';
			});

			// Limit the URL to internal, front-end links.
			//
			// If the frontend and the admin are served from the same domain, load the
			// preview over ssl if the customizer is being loaded over ssl. This avoids
			// insecure content warnings. This is not attempted if the admin and frontend
			// are on different domains to avoid the case where the frontend doesn't have
			// ssl certs.

			this.add( 'previewUrl', params.previewUrl ).setter( function( to ) {
				var result;

				// Check for URLs that include "/wp-admin/" or end in "/wp-admin".
				// Strip hashes and query strings before testing.
				if ( /\/wp-admin(\/|$)/.test( to.replace(/[#?].*$/, '') ) )
					return null;

				// Attempt to match the URL to the control frame's scheme
				// and check if it's allowed. If not, try the original URL.
				$.each([ to.replace( rscheme, self.scheme() ), to ], function( i, url ) {
					$.each( self.allowedUrls, function( i, allowed ) {
						if ( 0 === url.indexOf( allowed ) ) {
							result = url;
							return false;
						}
					});
					if ( result )
						return false;
				});

				// If we found a matching result, return it. If not, bail.
				return result ? result : null;
			});

			// Refresh the preview when the URL is changed (but not yet).
			this.previewUrl.bind( this.refresh );

			this.scroll = 0;
			this.bind( 'scroll', function( distance ) {
				this.scroll = distance;
			});

			// Update the URL when the iframe sends a URL message.
			this.bind( 'url', this.previewUrl );
		},

		query: function() {},

		abort: function() {
			if ( this.loading ) {
				this.loading.destroy();
				delete this.loading;
			}
		},

		refresh: function() {
			var self = this;

			this.abort();

			this.loading = new api.PreviewFrame({
				url:        this.url(),
				previewUrl: this.previewUrl(),
				query:      this.query() || {},
				container:  this.container,
				signature:  this.signature
			});

			this.loading.done( function() {
				// 'this' is the loading frame
				this.bind( 'synced', function() {
					if ( self.preview )
						self.preview.destroy();
					self.preview = this;
					delete self.loading;

					self.targetWindow( this.targetWindow() );
					self.channel( this.channel() );

					self.send( 'active' );
				});

				this.send( 'sync', {
					scroll:   self.scroll,
					settings: api.get()
				});
			});

			this.loading.fail( function( reason, location ) {
				if ( 'redirect' === reason && location )
					self.previewUrl( location );

				if ( 'logged out' === reason ) {
					if ( self.preview ) {
						self.preview.destroy();
						delete self.preview;
					}

					self.login().done( self.refresh );
				}

				if ( 'cheatin' === reason )
					self.cheatin();
			});
		},

		login: function() {
			var previewer = this,
				deferred, messenger, iframe;

			if ( this._login )
				return this._login;

			deferred = $.Deferred();
			this._login = deferred.promise();

			messenger = new api.Messenger({
				channel: 'login',
				url:     api.settings.url.login
			});

			iframe = $('<iframe src="' + api.settings.url.login + '" />').appendTo( this.container );

			messenger.targetWindow( iframe[0].contentWindow );

			messenger.bind( 'login', function() {
				iframe.remove();
				messenger.destroy();
				delete previewer._login;
				deferred.resolve();
			});

			return this._login;
		},

		cheatin: function() {
			$( document.body ).empty().addClass('cheatin').append( '<p>' + api.l10n.cheatin + '</p>' );
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
		api.l10n = window._wpCustomizeControlsL10n;

		// Check if we can run the customizer.
		if ( ! api.settings )
			return;

		// Redirect to the fallback preview if any incompatibilities are found.
		if ( ! $.support.postMessage || ( ! $.support.cors && api.settings.isCrossDomain ) )
			return window.location = api.settings.url.fallback;

		var body = $( document.body ),
			overlay = body.children('.wp-full-overlay'),
			query, previewer, parent;

		// Prevent the form from saving when enter is pressed.
		$('#customize-controls').on( 'keydown', function( e ) {
			if ( $( e.target ).is('textarea') )
				return;

			if ( 13 === e.which ) // Enter
				e.preventDefault();
		});

		// Initialize Previewer
		previewer = new api.Previewer({
			container:   '#customize-preview',
			form:        '#customize-controls',
			previewUrl:  api.settings.url.preview,
			allowedUrls: api.settings.url.allowed,
			signature:   'WP_CUSTOMIZER_SIGNATURE'
		}, {

			nonce: api.settings.nonce,

			query: function() {
				return {
					wp_customize: 'on',
					theme:        api.settings.theme.stylesheet,
					customized:   JSON.stringify( api.get() ),
					nonce:        this.nonce.preview
				};
			},

			save: function() {
				var self  = this,
					query = $.extend( this.query(), {
						action: 'customize_save',
						nonce:  this.nonce.save
					}),
					request = $.post( api.settings.url.ajax, query );

				api.trigger( 'save', request );

				body.addClass('saving');

				request.always( function() {
					body.removeClass('saving');
				});

				request.done( function( response ) {
					// Check if the user is logged out.
					if ( '0' === response ) {
						self.preview.iframe.hide();
						self.login().done( function() {
							self.save();
							self.preview.iframe.show();
						});
						return;
					}

					// Check for cheaters.
					if ( '-1' === response ) {
						self.cheatin();
						return;
					}

					api.trigger( 'saved' );
				});
			}
		});

		// Refresh the nonces if the preview sends updated nonces over.
 		previewer.bind( 'nonce', function( nonce ) {
 			$.extend( this.nonce, nonce );
 		});

		$.each( api.settings.settings, function( id, data ) {
			api.create( id, id, data.value, {
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

		// Check if preview url is valid and load the preview frame.
		if ( previewer.previewUrl() )
			previewer.refresh();
		else
			previewer.previewUrl( api.settings.url.home );

		// Save and activated states
		(function() {
			var state = new api.Values(),
				saved = state.create('saved'),
				activated = state.create('activated');

			state.bind( 'change', function() {
				var save = $('#save'),
					back = $('.back');

				if ( ! activated() ) {
					save.val( api.l10n.activate ).prop( 'disabled', false );
					back.text( api.l10n.cancel );

				} else if ( saved() ) {
					save.val( api.l10n.saved ).prop( 'disabled', true );
					back.text( api.l10n.close );

				} else {
					save.val( api.l10n.save ).prop( 'disabled', false );
					back.text( api.l10n.cancel );
				}
			});

			// Set default states.
			saved( true );
			activated( api.settings.theme.active );

			api.bind( 'change', function() {
				state('saved').set( false );
			});

			api.bind( 'saved', function() {
				state('saved').set( true );
				state('activated').set( true );
			});

			activated.bind( function( to ) {
				if ( to )
					api.trigger( 'activated' );
			});

			// Expose states to the API.
			api.state = state;
		}());

		// Temporary accordion code.
		$('.customize-section-title').click( function( event ) {
			var clicked = $( this ).parents( '.customize-section' );

			if ( clicked.hasClass('cannot-expand') )
				return;

			$( '.customize-section' ).not( clicked ).removeClass( 'open' );
			clicked.toggleClass( 'open' );
			event.preventDefault();
		});

		// Button bindings.
		$('#save').click( function( event ) {
			previewer.save();
			event.preventDefault();
		});

		$('.collapse-sidebar').click( function( event ) {
			overlay.toggleClass( 'collapsed' ).toggleClass( 'expanded' );
			event.preventDefault();
		});

		// Create a potential postMessage connection with the parent frame.
		parent = new api.Messenger({
			url: api.settings.url.parent,
			channel: 'loader'
		});

		// If we receive a 'back' event, we're inside an iframe.
		// Send any clicks to the 'Return' link to the parent page.
		parent.bind( 'back', function() {
			$('.back').on( 'click.back', function( event ) {
				event.preventDefault();
				parent.send( 'close' );
			});
		});

		// Pass events through to the parent.
		api.bind( 'saved', function() {
			parent.send( 'saved' );
		});

		// When activated, let the loader handle redirecting the page.
		// If no loader exists, redirect the page ourselves (if a url exists).
		api.bind( 'activated', function() {
			if ( parent.targetWindow() )
				parent.send( 'activated', api.settings.url.activated );
			else if ( api.settings.url.activated )
				window.location = api.settings.url.activated;
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
					last = api( 'header_textcolor' ).get();

				control.setting.set( to ? last : 'blank' );
			});

			control.setting.bind( function( to ) {
				control.element.set( 'blank' !== to );
			});
		});

		// Handle header image data
		api.control( 'header_image', function( control ) {
			control.setting.bind( function( to ) {
				if ( to === control.params.removed )
					control.settings.data.set( false );
			});

			control.library.on( 'click', 'a', function( event ) {
				control.settings.data.set( $(this).data('customizeHeaderImageData') );
			});

			control.uploader.success = function( attachment ) {
				var data;

				api.ImageControl.prototype.success.call( control, attachment );

				data = {
					attachment_id: attachment.id,
					url:           attachment.url,
					thumbnail_url: attachment.url,
					height:        attachment.meta.height,
					width:         attachment.meta.width
				};

				attachment.element.data( 'customizeHeaderImageData', data );
				control.settings.data.set( data );
			}
		});

		api.trigger( 'ready' );
	});

})( wp, jQuery );