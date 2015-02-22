/*globals wp, _, jQuery */

/**
 * Creates a dropzone on WP editor instances (elements with .wp-editor-wrap
 * or #wp-fullscreen-body) and relays drag'n'dropped files to a media workflow.
 *
 * wp.media.view.EditorUploader
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( '../view.js' ),
	l10n = wp.media.view.l10n,
	$ = jQuery,
	EditorUploader;

EditorUploader = View.extend({
	tagName:   'div',
	className: 'uploader-editor',
	template:  wp.template( 'uploader-editor' ),

	localDrag: false,
	overContainer: false,
	overDropzone: false,
	draggingFile: null,

	/**
	 * Bind drag'n'drop events to callbacks.
	 */
	initialize: function() {
		this.initialized = false;

		// Bail if not enabled or UA does not support drag'n'drop or File API.
		if ( ! window.tinyMCEPreInit || ! window.tinyMCEPreInit.dragDropUpload || ! this.browserSupport() ) {
			return this;
		}

		this.$document = $(document);
		this.dropzones = [];
		this.files = [];

		this.$document.on( 'drop', '.uploader-editor', _.bind( this.drop, this ) );
		this.$document.on( 'dragover', '.uploader-editor', _.bind( this.dropzoneDragover, this ) );
		this.$document.on( 'dragleave', '.uploader-editor', _.bind( this.dropzoneDragleave, this ) );
		this.$document.on( 'click', '.uploader-editor', _.bind( this.click, this ) );

		this.$document.on( 'dragover', _.bind( this.containerDragover, this ) );
		this.$document.on( 'dragleave', _.bind( this.containerDragleave, this ) );

		this.$document.on( 'dragstart dragend drop', _.bind( function( event ) {
			this.localDrag = event.type === 'dragstart';
		}, this ) );

		this.initialized = true;
		return this;
	},

	/**
	 * Check browser support for drag'n'drop.
	 *
	 * @return Boolean
	 */
	browserSupport: function() {
		var supports = false, div = document.createElement('div');

		supports = ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div );
		supports = supports && !! ( window.File && window.FileList && window.FileReader );
		return supports;
	},

	isDraggingFile: function( event ) {
		if ( this.draggingFile !== null ) {
			return this.draggingFile;
		}

		if ( _.isUndefined( event.originalEvent ) || _.isUndefined( event.originalEvent.dataTransfer ) ) {
			return false;
		}

		this.draggingFile = _.indexOf( event.originalEvent.dataTransfer.types, 'Files' ) > -1 &&
			_.indexOf( event.originalEvent.dataTransfer.types, 'text/plain' ) === -1;

		return this.draggingFile;
	},

	refresh: function( e ) {
		var dropzone_id;
		for ( dropzone_id in this.dropzones ) {
			// Hide the dropzones only if dragging has left the screen.
			this.dropzones[ dropzone_id ].toggle( this.overContainer || this.overDropzone );
		}

		if ( ! _.isUndefined( e ) ) {
			$( e.target ).closest( '.uploader-editor' ).toggleClass( 'droppable', this.overDropzone );
		}

		if ( ! this.overContainer && ! this.overDropzone ) {
			this.draggingFile = null;
		}

		return this;
	},

	render: function() {
		if ( ! this.initialized ) {
			return this;
		}

		View.prototype.render.apply( this, arguments );
		$( '.wp-editor-wrap, #wp-fullscreen-body' ).each( _.bind( this.attach, this ) );
		return this;
	},

	attach: function( index, editor ) {
		// Attach a dropzone to an editor.
		var dropzone = this.$el.clone();
		this.dropzones.push( dropzone );
		$( editor ).append( dropzone );
		return this;
	},

	/**
	 * When a file is dropped on the editor uploader, open up an editor media workflow
	 * and upload the file immediately.
	 *
	 * @param  {jQuery.Event} event The 'drop' event.
	 */
	drop: function( event ) {
		var $wrap = null, uploadView;

		this.containerDragleave( event );
		this.dropzoneDragleave( event );

		this.files = event.originalEvent.dataTransfer.files;
		if ( this.files.length < 1 ) {
			return;
		}

		// Set the active editor to the drop target.
		$wrap = $( event.target ).parents( '.wp-editor-wrap' );
		if ( $wrap.length > 0 && $wrap[0].id ) {
			window.wpActiveEditor = $wrap[0].id.slice( 3, -5 );
		}

		if ( ! this.workflow ) {
			this.workflow = wp.media.editor.open( 'content', {
				frame:    'post',
				state:    'insert',
				title:    l10n.addMedia,
				multiple: true
			});
			uploadView = this.workflow.uploader;
			if ( uploadView.uploader && uploadView.uploader.ready ) {
				this.addFiles.apply( this );
			} else {
				this.workflow.on( 'uploader:ready', this.addFiles, this );
			}
		} else {
			this.workflow.state().reset();
			this.addFiles.apply( this );
			this.workflow.open();
		}

		return false;
	},

	/**
	 * Add the files to the uploader.
	 */
	addFiles: function() {
		if ( this.files.length ) {
			this.workflow.uploader.uploader.uploader.addFile( _.toArray( this.files ) );
			this.files = [];
		}
		return this;
	},

	containerDragover: function( event ) {
		if ( this.localDrag || ! this.isDraggingFile( event ) ) {
			return;
		}

		this.overContainer = true;
		this.refresh();
	},

	containerDragleave: function() {
		this.overContainer = false;

		// Throttle dragleave because it's called when bouncing from some elements to others.
		_.delay( _.bind( this.refresh, this ), 50 );
	},

	dropzoneDragover: function( event ) {
		if ( this.localDrag || ! this.isDraggingFile( event ) ) {
			return;
		}

		this.overDropzone = true;
		this.refresh( event );
		return false;
	},

	dropzoneDragleave: function( e ) {
		this.overDropzone = false;
		_.delay( _.bind( this.refresh, this, e ), 50 );
	},

	click: function( e ) {
		// In the rare case where the dropzone gets stuck, hide it on click.
		this.containerDragleave( e );
		this.dropzoneDragleave( e );
		this.localDrag = false;
	}
});

module.exports = EditorUploader;
