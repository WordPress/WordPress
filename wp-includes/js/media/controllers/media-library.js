/**
 * wp.media.controller.MediaLibrary
 *
 * @class
 * @augments wp.media.controller.Library
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var Library = require( './library.js' ),
	MediaLibrary;

MediaLibrary = Library.extend({
	defaults: _.defaults({
		// Attachments browser defaults. @see media.view.AttachmentsBrowser
		filterable:      'uploaded',

		displaySettings: false,
		priority:        80,
		syncSelection:   false
	}, Library.prototype.defaults ),

	/**
	 * @since 3.9.0
	 *
	 * @param options
	 */
	initialize: function( options ) {
		this.media = options.media;
		this.type = options.type;
		this.set( 'library', wp.media.query({ type: this.type }) );

		Library.prototype.initialize.apply( this, arguments );
	},

	/**
	 * @since 3.9.0
	 */
	activate: function() {
		// @todo this should use this.frame.
		if ( wp.media.frame.lastMime ) {
			this.set( 'library', wp.media.query({ type: wp.media.frame.lastMime }) );
			delete wp.media.frame.lastMime;
		}
		Library.prototype.activate.apply( this, arguments );
	}
});

module.exports = MediaLibrary;
