/* eslint consistent-this: [ "error", "control" ] */
(function( component, $ ) {
	'use strict';

	var ImageWidgetModel, ImageWidgetControl;

	/**
	 * Image widget model.
	 *
	 * See WP_Widget_Media_Image::enqueue_admin_scripts() for amending prototype from PHP exports.
	 *
	 * @class ImageWidgetModel
	 * @constructor
	 */
	ImageWidgetModel = component.MediaWidgetModel.extend({});

	/**
	 * Image widget control.
	 *
	 * See WP_Widget_Media_Image::enqueue_admin_scripts() for amending prototype from PHP exports.
	 *
	 * @class ImageWidgetModel
	 * @constructor
	 */
	ImageWidgetControl = component.MediaWidgetControl.extend({

		/**
		 * Render preview.
		 *
		 * @returns {void}
		 */
		renderPreview: function renderPreview() {
			var control = this, previewContainer, previewTemplate;
			if ( ! control.model.get( 'attachment_id' ) && ! control.model.get( 'url' ) ) {
				return;
			}

			previewContainer = control.$el.find( '.media-widget-preview' );
			previewTemplate = wp.template( 'wp-media-widget-image-preview' );
			previewContainer.html( previewTemplate( _.extend( control.previewTemplateProps.toJSON() ) ) );
		},

		/**
		 * Open the media image-edit frame to modify the selected item.
		 *
		 * @returns {void}
		 */
		editMedia: function editMedia() {
			var control = this, mediaFrame, updateCallback, defaultSync, metadata;

			metadata = control.mapModelToMediaFrameProps( control.model.toJSON() );

			// Needed or else none will not be selected if linkUrl is not also empty.
			if ( 'none' === metadata.link ) {
				metadata.linkUrl = '';
			}

			// Set up the media frame.
			mediaFrame = wp.media({
				frame: 'image',
				state: 'image-details',
				metadata: metadata
			});
			mediaFrame.$el.addClass( 'media-widget' );

			updateCallback = function() {
				var mediaProps;

				// Update cached attachment object to avoid having to re-fetch. This also triggers re-rendering of preview.
				mediaProps = mediaFrame.state().attributes.image.toJSON();
				control.selectedAttachment.set( mediaProps );

				control.model.set( _.extend(
					control.mapMediaToModelProps( mediaProps ),
					{ error: false }
				) );
			};

			mediaFrame.state( 'image-details' ).on( 'update', updateCallback );
			mediaFrame.state( 'replace-image' ).on( 'replace', updateCallback );

			// Disable syncing of attachment changes back to server. See <https://core.trac.wordpress.org/ticket/40403>.
			defaultSync = wp.media.model.Attachment.prototype.sync;
			wp.media.model.Attachment.prototype.sync = function rejectedSync() {
				return $.Deferred().rejectWith( this ).promise();
			};
			mediaFrame.on( 'close', function onClose() {
				mediaFrame.detach();
				wp.media.model.Attachment.prototype.sync = defaultSync;
			});

			mediaFrame.open();
		},

		/**
		 * Get props which are merged on top of the model when an embed is chosen (as opposed to an attachment).
		 *
		 * @returns {Object} Reset/override props.
		 */
		getEmbedResetProps: function getEmbedResetProps() {
			return _.extend(
				component.MediaWidgetControl.prototype.getEmbedResetProps.call( this ),
				{
					size: 'full',
					width: 0,
					height: 0
				}
			);
		},

		/**
		 * Map model props to preview template props.
		 *
		 * @returns {Object} Preview template props.
		 */
		mapModelToPreviewTemplateProps: function mapModelToPreviewTemplateProps() {
			var control = this, mediaFrameProps, url;
			url = control.model.get( 'url' );
			mediaFrameProps = component.MediaWidgetControl.prototype.mapModelToPreviewTemplateProps.call( control );
			mediaFrameProps.currentFilename = url ? url.replace( /\?.*$/, '' ).replace( /^.+\//, '' ) : '';
			return mediaFrameProps;
		}
	});

	// Exports.
	component.controlConstructors.media_image = ImageWidgetControl;
	component.modelConstructors.media_image = ImageWidgetModel;

})( wp.mediaWidgets, jQuery );
