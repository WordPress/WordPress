if ( typeof wp === 'undefined' )
	var wp = {};

(function( exports, $ ) {
	/*
	 * An object that helps create a WordPress uploader using plupload.
	 *
	 * @param options - object - The options passed to the new plupload instance.
	 *    Requires the following parameters:
	 *    - container - The id of uploader container.
	 *    - browser   - The id of button to trigger the file select.
	 *    - dropzone  - The id of file drop target.
	 *    - plupload  - An object of parameters to pass to the plupload instance.
	 *    - params    - An object of parameters to pass to $_POST when uploading the file.
	 *                  Extends this.plupload.multipart_params under the hood.
	 *
	 * @param attributes - object - Attributes and methods for this specific instance.
	 */
	var Uploader = function( options ) {
		var self = this,
			elements = {
				container: 'container',
				browser:   'browse_button',
				dropzone:  'drop_element'
			},
			key;

		this.plupload = $.extend( { multipart_params: {} }, wpPluploadDefaults );
		this.container = document.body; // Set default container.

		// Extend the instance with options
		//
		// Use deep extend to allow options.plupload to override individual
		// default plupload keys.
		$.extend( true, this, options );

		// Proxy all methods so this always refers to the current instance.
		for ( key in this ) {
			if ( $.isFunction( this[ key ] ) )
				this[ key ] = $.proxy( this[ key ], this );
		}

		// Ensure all elements are jQuery elements and have id attributes
		// Then set the proper plupload arguments to the ids.
		for ( key in elements ) {
			if ( ! this[ key ] )
				continue;

			this[ key ] = $( this[ key ] ).first();
			if ( ! this[ key ].prop('id') )
				this[ key ].prop( 'id', '__wp-uploader-id-' + Uploader.uuid++ );
			this.plupload[ elements[ key ] ] = this[ key ].prop('id');
		}

		this.uploader = new plupload.Uploader( this.plupload );
		delete this.plupload;

		// Set default params and remove this.params alias.
		this.param( this.params || {} );
		delete this.params;

		this.uploader.bind( 'Init', this.init );

		this.uploader.init();

		this.uploader.bind( 'UploadProgress', this.progress );

		this.uploader.bind( 'FileUploaded', function( up, file, response ) {
			try {
				response = JSON.parse( response.response );
			} catch ( e ) {
				return self.error( pluploadL10n.default_error, e );
			}

			if ( ! response || ! response.type || ! response.data )
				return self.error( pluploadL10n.default_error );

			if ( 'error' === response.type )
				return self.error( response.data.message, response.data );

			if ( 'success' === response.type )
				return self.success( response.data );

		});

		this.uploader.bind( 'Error', function( up, error ) {
			var message = pluploadL10n.default_error,
				key;

			// Check for plupload errors.
			for ( key in Uploader.errorMap ) {
				if ( error.code === plupload[ key ] ) {
					message = Uploader.errorMap[ key ];
					break;
				}
			}

			self.error( message, error );
			up.refresh();
		});

		this.uploader.bind( 'FilesAdded', function( up, files ) {
			$.each( files, function() {
				self.added( this );
			});

			up.refresh();
			up.start();
		});
	};

	Uploader.uuid = 0;

	Uploader.errorMap = {
		'FAILED':                 pluploadL10n.upload_failed,
		'FILE_EXTENSION_ERROR':   pluploadL10n.invalid_filetype,
		// 'FILE_SIZE_ERROR': '',
		'IMAGE_FORMAT_ERROR':     pluploadL10n.not_an_image,
		'IMAGE_MEMORY_ERROR':     pluploadL10n.image_memory_exceeded,
		'IMAGE_DIMENSIONS_ERROR': pluploadL10n.image_dimensions_exceeded,
		'GENERIC_ERROR':          pluploadL10n.upload_failed,
		'IO_ERROR':               pluploadL10n.io_error,
		'HTTP_ERROR':             pluploadL10n.http_error,
		'SECURITY_ERROR':         pluploadL10n.security_error
	};

	$.extend( Uploader.prototype, {
		/**
		 * Acts as a shortcut to extending the uploader's multipart_params object.
		 *
		 * param( key )
		 *    Returns the value of the key.
		 *
		 * param( key, value )
		 *    Sets the value of a key.
		 *
		 * param( map )
		 *    Sets values for a map of data.
		 */
		param: function( key, value ) {
			if ( arguments.length === 1 && typeof key === 'string' )
				return this.uploader.settings.multipart_params[ key ];

			if ( arguments.length > 1 ) {
				this.uploader.settings.multipart_params[ key ] = value;
			} else {
				$.extend( this.uploader.settings.multipart_params, key );
			}
		},

		init:     function() {},
		error:    function() {},
		success:  function() {},
		added:    function() {},
		progress: function() {},
		complete: function() {}
	});

	exports.Uploader = Uploader;
})( wp, jQuery );