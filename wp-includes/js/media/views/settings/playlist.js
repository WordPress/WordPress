/**
 * wp.media.view.Settings.Playlist
 *
 * @class
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Settings = require( '../settings.js' ),
	Playlist;

Playlist = Settings.extend({
	className: 'collection-settings playlist-settings',
	template:  wp.template('playlist-settings')
});

module.exports = Playlist;
