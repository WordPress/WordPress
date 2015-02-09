/**
 * wp.media.view.Attachments.Selection
 *
 * @class
 * @augments wp.media.view.Attachments
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Attachments = require( '../attachments.js' ),
	AttachmentSelection = require( '../attachment/selection.js' ),
	Selection;

Selection = Attachments.extend({
	events: {},
	initialize: function() {
		_.defaults( this.options, {
			sortable:   false,
			resize:     false,

			// The single `Attachment` view to be used in the `Attachments` view.
			AttachmentView: AttachmentSelection
		});
		// Call 'initialize' directly on the parent class.
		return Attachments.prototype.initialize.apply( this, arguments );
	}
});

module.exports = Selection;
