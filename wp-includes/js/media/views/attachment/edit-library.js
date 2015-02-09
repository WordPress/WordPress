/**
 * wp.media.view.Attachment.EditLibrary
 *
 * @class
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Attachment = require( '../attachment.js' ),
	EditLibrary;

EditLibrary = Attachment.extend({
	buttons: {
		close: true
	}
});

module.exports = EditLibrary;
