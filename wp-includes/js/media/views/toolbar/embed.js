/*globals wp, _ */

/**
 * wp.media.view.Toolbar.Embed
 *
 * @class
 * @augments wp.media.view.Toolbar.Select
 * @augments wp.media.view.Toolbar
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Select = require( './select.js' ),
	l10n = wp.media.view.l10n,
	Embed;

Embed = Select.extend({
	initialize: function() {
		_.defaults( this.options, {
			text: l10n.insertIntoPost,
			requires: false
		});
		// Call 'initialize' directly on the parent class.
		Select.prototype.initialize.apply( this, arguments );
	},

	refresh: function() {
		var url = this.controller.state().props.get('url');
		this.get('select').model.set( 'disabled', ! url || url === 'http://' );
		/**
		 * call 'refresh' directly on the parent class
		 */
		Select.prototype.refresh.apply( this, arguments );
	}
});

module.exports = Embed;
