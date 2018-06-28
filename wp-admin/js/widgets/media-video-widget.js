/**
 * @output wp-admin/js/widgets/media-video-widget.js
 */

/* eslint consistent-this: [ "error", "control" ] */
(function( component ) {
	'use strict';

	var VideoWidgetModel, VideoWidgetControl, VideoDetailsMediaFrame;

	/**
	 * Custom video details frame that removes the replace-video state.
	 *
	 * @class    wp.mediaWidgets.controlConstructors~VideoDetailsMediaFrame
	 * @augments wp.media.view.MediaFrame.VideoDetails
	 *
	 * @private
	 */
	VideoDetailsMediaFrame = wp.media.view.MediaFrame.VideoDetails.extend(/** @lends wp.mediaWidgets.controlConstructors~VideoDetailsMediaFrame.prototype */{

		/**
		 * Create the default states.
		 *
		 * @returns {void}
		 */
		createStates: function createStates() {
			this.states.add([
				new wp.media.controller.VideoDetails({
					media: this.media
				}),

				new wp.media.controller.MediaLibrary({
					type: 'video',
					id: 'add-video-source',
					title: wp.media.view.l10n.videoAddSourceTitle,
					toolbar: 'add-video-source',
					media: this.media,
					menu: false
				}),

				new wp.media.controller.MediaLibrary({
					type: 'text',
					id: 'add-track',
					title: wp.media.view.l10n.videoAddTrackTitle,
					toolbar: 'add-track',
					media: this.media,
					menu: 'video-details'
				})
			]);
		}
	});

	/**
	 * Video widget model.
	 *
	 * See WP_Widget_Video::enqueue_admin_scripts() for amending prototype from PHP exports.
	 *
	 * @class    wp.mediaWidgets.modelConstructors.media_video
	 * @augments wp.mediaWidgets.MediaWidgetModel
	 */
	VideoWidgetModel = component.MediaWidgetModel.extend({});

	/**
	 * Video widget control.
	 *
	 * See WP_Widget_Video::enqueue_admin_scripts() for amending prototype from PHP exports.
	 *
	 * @class    wp.mediaWidgets.controlConstructors.media_video
	 * @augments wp.mediaWidgets.MediaWidgetControl
	 */
	VideoWidgetControl = component.MediaWidgetControl.extend(/** @lends wp.mediaWidgets.controlConstructors.media_video.prototype */{

		/**
		 * Show display settings.
		 *
		 * @type {boolean}
		 */
		showDisplaySettings: false,

		/**
		 * Cache of oembed responses.
		 *
		 * @type {Object}
		 */
		oembedResponses: {},

		/**
		 * Map model props to media frame props.
		 *
		 * @param {Object} modelProps - Model props.
		 * @returns {Object} Media frame props.
		 */
		mapModelToMediaFrameProps: function mapModelToMediaFrameProps( modelProps ) {
			var control = this, mediaFrameProps;
			mediaFrameProps = component.MediaWidgetControl.prototype.mapModelToMediaFrameProps.call( control, modelProps );
			mediaFrameProps.link = 'embed';
			return mediaFrameProps;
		},

		/**
		 * Fetches embed data for external videos.
		 *
		 * @returns {void}
		 */
		fetchEmbed: function fetchEmbed() {
			var control = this, url;
			url = control.model.get( 'url' );

			// If we already have a local cache of the embed response, return.
			if ( control.oembedResponses[ url ] ) {
				return;
			}

			// If there is an in-flight embed request, abort it.
			if ( control.fetchEmbedDfd && 'pending' === control.fetchEmbedDfd.state() ) {
				control.fetchEmbedDfd.abort();
			}

			control.fetchEmbedDfd = wp.apiRequest({
				url: wp.media.view.settings.oEmbedProxyUrl,
				data: {
					url: control.model.get( 'url' ),
					maxwidth: control.model.get( 'width' ),
					maxheight: control.model.get( 'height' ),
					discover: false
				},
				type: 'GET',
				dataType: 'json',
				context: control
			});

			control.fetchEmbedDfd.done( function( response ) {
				control.oembedResponses[ url ] = response;
				control.renderPreview();
			});

			control.fetchEmbedDfd.fail( function() {
				control.oembedResponses[ url ] = null;
			});
		},

		/**
		 * Whether a url is a supported external host.
		 *
		 * @deprecated since 4.9.
		 *
		 * @returns {boolean} Whether url is a supported video host.
		 */
		isHostedVideo: function isHostedVideo() {
			return true;
		},

		/**
		 * Render preview.
		 *
		 * @returns {void}
		 */
		renderPreview: function renderPreview() {
			var control = this, previewContainer, previewTemplate, attachmentId, attachmentUrl, poster, html = '', isOEmbed = false, mime, error, urlParser, matches;
			attachmentId = control.model.get( 'attachment_id' );
			attachmentUrl = control.model.get( 'url' );
			error = control.model.get( 'error' );

			if ( ! attachmentId && ! attachmentUrl ) {
				return;
			}

			// Verify the selected attachment mime is supported.
			mime = control.selectedAttachment.get( 'mime' );
			if ( mime && attachmentId ) {
				if ( ! _.contains( _.values( wp.media.view.settings.embedMimes ), mime ) ) {
					error = 'unsupported_file_type';
				}
			} else if ( ! attachmentId ) {
				urlParser = document.createElement( 'a' );
				urlParser.href = attachmentUrl;
				matches = urlParser.pathname.toLowerCase().match( /\.(\w+)$/ );
				if ( matches ) {
					if ( ! _.contains( _.keys( wp.media.view.settings.embedMimes ), matches[1] ) ) {
						error = 'unsupported_file_type';
					}
				} else {
					isOEmbed = true;
				}
			}

			if ( isOEmbed ) {
				control.fetchEmbed();
				if ( control.oembedResponses[ attachmentUrl ] ) {
					poster = control.oembedResponses[ attachmentUrl ].thumbnail_url;
					html = control.oembedResponses[ attachmentUrl ].html.replace( /\swidth="\d+"/, ' width="100%"' ).replace( /\sheight="\d+"/, '' );
				}
			}

			previewContainer = control.$el.find( '.media-widget-preview' );
			previewTemplate = wp.template( 'wp-media-widget-video-preview' );

			previewContainer.html( previewTemplate({
				model: {
					attachment_id: attachmentId,
					html: html,
					src: attachmentUrl,
					poster: poster
				},
				is_oembed: isOEmbed,
				error: error
			}));
			wp.mediaelement.initialize();
		},

		/**
		 * Open the media image-edit frame to modify the selected item.
		 *
		 * @returns {void}
		 */
		editMedia: function editMedia() {
			var control = this, mediaFrame, metadata, updateCallback;

			metadata = control.mapModelToMediaFrameProps( control.model.toJSON() );

			// Set up the media frame.
			mediaFrame = new VideoDetailsMediaFrame({
				frame: 'video',
				state: 'video-details',
				metadata: metadata
			});
			wp.media.frame = mediaFrame;
			mediaFrame.$el.addClass( 'media-widget' );

			updateCallback = function( mediaFrameProps ) {

				// Update cached attachment object to avoid having to re-fetch. This also triggers re-rendering of preview.
				control.selectedAttachment.set( mediaFrameProps );

				control.model.set( _.extend(
					_.omit( control.model.defaults(), 'title' ),
					control.mapMediaToModelProps( mediaFrameProps ),
					{ error: false }
				) );
			};

			mediaFrame.state( 'video-details' ).on( 'update', updateCallback );
			mediaFrame.state( 'replace-video' ).on( 'replace', updateCallback );
			mediaFrame.on( 'close', function() {
				mediaFrame.detach();
			});

			mediaFrame.open();
		}
	});

	// Exports.
	component.controlConstructors.media_video = VideoWidgetControl;
	component.modelConstructors.media_video = VideoWidgetModel;

})( wp.mediaWidgets );
