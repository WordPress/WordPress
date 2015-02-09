/**
 * wp.media.controller.EditAttachmentMetadata
 *
 * A state for editing an attachment's metadata.
 *
 * @constructor
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var State = require( './state.js' ),
	l10n = wp.media.view.l10n,
	EditAttachmentMetadata;

EditAttachmentMetadata = State.extend({
	defaults: {
		id:      'edit-attachment',
		// Title string passed to the frame's title region view.
		title:   l10n.attachmentDetails,
		// Region mode defaults.
		content: 'edit-metadata',
		menu:    false,
		toolbar: false,
		router:  false
	}
});

module.exports = EditAttachmentMetadata;
