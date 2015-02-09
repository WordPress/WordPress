/**
 * wp.media.view.Sidebar
 *
 * @class
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var PriorityList = require( './priority-list.js' ),
	Sidebar;

Sidebar = PriorityList.extend({
	className: 'media-sidebar'
});

module.exports = Sidebar;
