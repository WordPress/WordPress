/*globals wp, _ */

/**
 * wp.media.view.EditImage.Details
 *
 * @class
 * @augments wp.media.view.EditImage
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = wp.media.View,
	EditImage = wp.media.view.EditImage,
	Details;

Details = EditImage.extend({
	initialize: function( options ) {
		this.editor = window.imageEdit;
		this.frame = options.frame;
		this.controller = options.controller;
		View.prototype.initialize.apply( this, arguments );
	},

	back: function() {
		this.frame.content.mode( 'edit-metadata' );
	},

	save: function() {
		this.model.fetch().done( _.bind( function() {
			this.frame.content.mode( 'edit-metadata' );
		}, this ) );
	}
});

module.exports = Details;
