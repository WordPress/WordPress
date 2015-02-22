/*globals wp */

/**
 * wp.media.controller.GalleryEdit
 *
 * A state for editing a gallery's images and settings.
 *
 * @class
 * @augments wp.media.controller.Library
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 *
 * @param {object}                     [attributes]                       The attributes hash passed to the state.
 * @param {string}                     [attributes.id=gallery-edit]       Unique identifier.
 * @param {string}                     [attributes.title=Edit Gallery]    Title for the state. Displays in the frame's title region.
 * @param {wp.media.model.Attachments} [attributes.library]               The collection of attachments in the gallery.
 *                                                                        If one is not supplied, an empty media.model.Selection collection is created.
 * @param {boolean}                    [attributes.multiple=false]        Whether multi-select is enabled.
 * @param {boolean}                    [attributes.searchable=false]      Whether the library is searchable.
 * @param {boolean}                    [attributes.sortable=true]         Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
 * @param {string|false}               [attributes.content=browse]        Initial mode for the content region.
 * @param {string|false}               [attributes.toolbar=image-details] Initial mode for the toolbar region.
 * @param {boolean}                    [attributes.describe=true]         Whether to offer UI to describe attachments - e.g. captioning images in a gallery.
 * @param {boolean}                    [attributes.displaySettings=true]  Whether to show the attachment display settings interface.
 * @param {boolean}                    [attributes.dragInfo=true]         Whether to show instructional text about the attachments being sortable.
 * @param {int}                        [attributes.idealColumnWidth=170]  The ideal column width in pixels for attachments.
 * @param {boolean}                    [attributes.editing=false]         Whether the gallery is being created, or editing an existing instance.
 * @param {int}                        [attributes.priority=60]           The priority for the state link in the media menu.
 * @param {boolean}                    [attributes.syncSelection=false]   Whether the Attachments selection should be persisted from the last state.
 *                                                                        Defaults to false for this state, because the library passed in  *is* the selection.
 * @param {view}                       [attributes.AttachmentView]        The single `Attachment` view to be used in the `Attachments`.
 *                                                                        If none supplied, defaults to wp.media.view.Attachment.EditLibrary.
 */
var Library = require( './library.js' ),
	EditLibraryView = require( '../views/attachment/edit-library.js' ),
	GallerySettingsView = require( '../views/settings/gallery.js' ),
	l10n = wp.media.view.l10n,
	GalleryEdit;

GalleryEdit = Library.extend({
	defaults: {
		id:               'gallery-edit',
		title:            l10n.editGalleryTitle,
		multiple:         false,
		searchable:       false,
		sortable:         true,
		display:          false,
		content:          'browse',
		toolbar:          'gallery-edit',
		describe:         true,
		displaySettings:  true,
		dragInfo:         true,
		idealColumnWidth: 170,
		editing:          false,
		priority:         60,
		syncSelection:    false
	},

	/**
	 * @since 3.5.0
	 */
	initialize: function() {
		// If we haven't been provided a `library`, create a `Selection`.
		if ( ! this.get('library') ) {
			this.set( 'library', new wp.media.model.Selection() );
		}

		// The single `Attachment` view to be used in the `Attachments` view.
		if ( ! this.get('AttachmentView') ) {
			this.set( 'AttachmentView', EditLibraryView );
		}

		Library.prototype.initialize.apply( this, arguments );
	},

	/**
	 * @since 3.5.0
	 */
	activate: function() {
		var library = this.get('library');

		// Limit the library to images only.
		library.props.set( 'type', 'image' );

		// Watch for uploaded attachments.
		this.get('library').observe( wp.Uploader.queue );

		this.frame.on( 'content:render:browse', this.gallerySettings, this );

		Library.prototype.activate.apply( this, arguments );
	},

	/**
	 * @since 3.5.0
	 */
	deactivate: function() {
		// Stop watching for uploaded attachments.
		this.get('library').unobserve( wp.Uploader.queue );

		this.frame.off( 'content:render:browse', this.gallerySettings, this );

		Library.prototype.deactivate.apply( this, arguments );
	},

	/**
	 * @since 3.5.0
	 *
	 * @param browser
	 */
	gallerySettings: function( browser ) {
		if ( ! this.get('displaySettings') ) {
			return;
		}

		var library = this.get('library');

		if ( ! library || ! browser ) {
			return;
		}

		library.gallery = library.gallery || new Backbone.Model();

		browser.sidebar.set({
			gallery: new GallerySettingsView({
				controller: this,
				model:      library.gallery,
				priority:   40
			})
		});

		browser.toolbar.set( 'reverse', {
			text:     l10n.reverseOrder,
			priority: 80,

			click: function() {
				library.reset( library.toArray().reverse() );
			}
		});
	}
});

module.exports = GalleryEdit;
