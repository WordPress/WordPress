/**
 * A similar view to media.view.Attachment.Details
 * for use in the Edit Attachment modal.
 *
 * @constructor
 * @augments wp.media.view.Attachment.Details
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Details = require( './details.js' ),
	MediaDetails = require( '../media-details.js' ),
	TwoColumn;

TwoColumn = Details.extend({
	template: wp.template( 'attachment-details-two-column' ),

	editAttachment: function( event ) {
		event.preventDefault();
		this.controller.content.mode( 'edit-image' );
	},

	/**
	 * Noop this from parent class, doesn't apply here.
	 */
	toggleSelectionHandler: function() {},

	render: function() {
		Details.prototype.render.apply( this, arguments );

		wp.media.mixin.removeAllPlayers();
		this.$( 'audio, video' ).each( function (i, elem) {
			var el = MediaDetails.prepareSrc( elem );
			new window.MediaElementPlayer( el, wp.media.mixin.mejsSettings );
		} );
	}
});

module.exports = TwoColumn;
