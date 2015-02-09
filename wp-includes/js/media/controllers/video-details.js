/**
 * The controller for the Video Details state
 *
 * @constructor
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var State = require( './state.js' ),
	l10n = wp.media.view.l10n,
	VideoDetails;

VideoDetails = State.extend({
	defaults: {
		id: 'video-details',
		toolbar: 'video-details',
		title: l10n.videoDetailsTitle,
		content: 'video-details',
		menu: 'video-details',
		router: false,
		priority: 60
	},

	initialize: function( options ) {
		this.media = options.media;
		State.prototype.initialize.apply( this, arguments );
	}
});

module.exports = VideoDetails;
