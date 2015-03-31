/*globals wp */

/**
 * wp.media.view.Attachment.EditLibrary
 *
 * @class
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var EditLibrary = wp.media.view.Attachment.extend({
	buttons: {
		close: true
	}
});

module.exports = EditLibrary;
