/**
 * wp.media.view.Attachments.EditSelection
 *
 * @class
 * @augments wp.media.view.Attachment.Selection
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Selection = require( './selection.js' ),
	EditSelection;

EditSelection = Selection.extend({
	buttons: {
		close: true
	}
});

module.exports = EditSelection;
