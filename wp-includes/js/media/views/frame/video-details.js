/*globals wp, _ */

/**
 * wp.media.view.MediaFrame.VideoDetails
 *
 * @class
 * @augments wp.media.view.MediaFrame.MediaDetails
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
var MediaDetails = wp.media.view.MediaFrame.MediaDetails,
	MediaLibrary = wp.media.controller.MediaLibrary,
	VideoDetailsView = require( '../video-details.js' ),
	VideoDetailsController = require( '../../controllers/video-details.js' ),
	l10n = wp.media.view.l10n,
	VideoDetails;

VideoDetails = MediaDetails.extend({
	defaults: {
		id:      'video',
		url:     '',
		menu:    'video-details',
		content: 'video-details',
		toolbar: 'video-details',
		type:    'link',
		title:    l10n.videoDetailsTitle,
		priority: 120
	},

	initialize: function( options ) {
		options.DetailsView = VideoDetailsView;
		options.cancelText = l10n.videoDetailsCancel;
		options.addText = l10n.videoAddSourceTitle;

		MediaDetails.prototype.initialize.call( this, options );
	},

	bindHandlers: function() {
		MediaDetails.prototype.bindHandlers.apply( this, arguments );

		this.on( 'toolbar:render:replace-video', this.renderReplaceToolbar, this );
		this.on( 'toolbar:render:add-video-source', this.renderAddSourceToolbar, this );
		this.on( 'toolbar:render:select-poster-image', this.renderSelectPosterImageToolbar, this );
		this.on( 'toolbar:render:add-track', this.renderAddTrackToolbar, this );
	},

	createStates: function() {
		this.states.add([
			new VideoDetailsController({
				media: this.media
			}),

			new MediaLibrary( {
				type: 'video',
				id: 'replace-video',
				title: l10n.videoReplaceTitle,
				toolbar: 'replace-video',
				media: this.media,
				menu: 'video-details'
			} ),

			new MediaLibrary( {
				type: 'video',
				id: 'add-video-source',
				title: l10n.videoAddSourceTitle,
				toolbar: 'add-video-source',
				media: this.media,
				menu: false
			} ),

			new MediaLibrary( {
				type: 'image',
				id: 'select-poster-image',
				title: l10n.videoSelectPosterImageTitle,
				toolbar: 'select-poster-image',
				media: this.media,
				menu: 'video-details'
			} ),

			new MediaLibrary( {
				type: 'text',
				id: 'add-track',
				title: l10n.videoAddTrackTitle,
				toolbar: 'add-track',
				media: this.media,
				menu: 'video-details'
			} )
		]);
	},

	renderSelectPosterImageToolbar: function() {
		this.setPrimaryButton( l10n.videoSelectPosterImageTitle, function( controller, state ) {
			var urls = [], attachment = state.get( 'selection' ).single();

			controller.media.set( 'poster', attachment.get( 'url' ) );
			state.trigger( 'set-poster-image', controller.media.toJSON() );

			_.each( wp.media.view.settings.embedExts, function (ext) {
				if ( controller.media.get( ext ) ) {
					urls.push( controller.media.get( ext ) );
				}
			} );

			wp.ajax.send( 'set-attachment-thumbnail', {
				data : {
					urls: urls,
					thumbnail_id: attachment.get( 'id' )
				}
			} );
		} );
	},

	renderAddTrackToolbar: function() {
		this.setPrimaryButton( l10n.videoAddTrackTitle, function( controller, state ) {
			var attachment = state.get( 'selection' ).single(),
				content = controller.media.get( 'content' );

			if ( -1 === content.indexOf( attachment.get( 'url' ) ) ) {
				content += [
					'<track srclang="en" label="English"kind="subtitles" src="',
					attachment.get( 'url' ),
					'" />'
				].join('');

				controller.media.set( 'content', content );
			}
			state.trigger( 'add-track', controller.media.toJSON() );
		} );
	}
});

module.exports = VideoDetails;
