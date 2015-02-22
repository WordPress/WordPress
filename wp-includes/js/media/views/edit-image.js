/*globals wp, _ */

/**
 * wp.media.view.EditImage
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	EditImage;

EditImage = View.extend({
	className: 'image-editor',
	template: wp.template('image-editor'),

	initialize: function( options ) {
		this.editor = window.imageEdit;
		this.controller = options.controller;
		View.prototype.initialize.apply( this, arguments );
	},

	prepare: function() {
		return this.model.toJSON();
	},

	loadEditor: function() {
		var dfd = this.editor.open( this.model.get('id'), this.model.get('nonces').edit, this );
		dfd.done( _.bind( this.focus, this ) );
	},

	focus: function() {
		this.$( '.imgedit-submit .button' ).eq( 0 ).focus();
	},

	back: function() {
		var lastState = this.controller.lastState();
		this.controller.setState( lastState );
	},

	refresh: function() {
		this.model.fetch();
	},

	save: function() {
		var lastState = this.controller.lastState();

		this.model.fetch().done( _.bind( function() {
			this.controller.setState( lastState );
		}, this ) );
	}

});

module.exports = EditImage;
