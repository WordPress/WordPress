/**
 * wp.media.view.Router
 *
 * @class
 * @augments wp.media.view.Menu
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Menu = require( './menu.js' ),
	RouterItem = require( './router-item.js' ),
	Router;

Router = Menu.extend({
	tagName:   'div',
	className: 'media-router',
	property:  'contentMode',
	ItemView:  RouterItem,
	region:    'router',

	initialize: function() {
		this.controller.on( 'content:render', this.update, this );
		// Call 'initialize' directly on the parent class.
		Menu.prototype.initialize.apply( this, arguments );
	},

	update: function() {
		var mode = this.controller.content.mode();
		if ( mode ) {
			this.select( mode );
		}
	}
});

module.exports = Router;
