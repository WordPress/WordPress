/**
 * wp.media.view.Attachment.Library
 *
 * @class
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Attachment = require( '../attachment.js' ),
	Library;

Library = Attachment.extend({
	buttons: {
		check: true
	}
});

module.exports = Library;
