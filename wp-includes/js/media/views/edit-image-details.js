var View = require( './view.js' ),
	EditImage = require( './edit-image.js' ),
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
		var self = this;

		this.model.fetch().done( function() {
			self.frame.content.mode( 'edit-metadata' );
		});
	}
});

module.exports = Details;