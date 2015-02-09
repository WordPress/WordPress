/**
 * wp.media.view.Attachment.Selection
 *
 * @class
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Attachment = require( '../attachment.js' ),
	Selection;

Selection = Attachment.extend({
	className: 'attachment selection',

	// On click, just select the model, instead of removing the model from
	// the selection.
	toggleSelection: function() {
		this.options.selection.single( this.model );
	}
});

module.exports = Selection;
