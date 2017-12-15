/* eslint consistent-this: [ "error", "control" ] */
(function( component, $ ) {
	'use strict';

	var ImageWidgetModel, ImageWidgetControl;

	/**
	 * Image widget model.
	 *
	 * See WP_Widget_Media_Image::enqueue_admin_scripts() for amending prototype from PHP exports.
	 *
	 * @class    wp.mediaWidgets.modelConstructors.media_image
	 * @augments wp.mediaWidgets.MediaWidgetModel
	 */
	ImageWidgetModel = component.MediaWidgetModel.extend({});

	/**
	 * Image widget control.
	 *
	 * See WP_Widget_Media_Image::enqueue_admin_scripts() for amending prototype from PHP exports.
	 *
	 * @class    wp.mediaWidgets.controlConstructors.media_audio
	 * @augments wp.mediaWidgets.MediaWidgetControl
	 */
	ImageWidgetControl = component.MediaWidgetControl.extend(/** @lends wp.mediaWidgets.controlConstructors.media_image.prototype */{

		/**
		 * View events.
		 *
		 * @type {object}
		 */
		events: _.extend( {}, component.MediaWidgetControl.prototype.events, {
			'click .media-widget-preview.populated': 'editMedia'
		} ),

		/**
		 * Render preview.
		 *
		 * @returns {void}
		 */
		renderPreview: function renderPreview() {
			var control = this, previewContainer, previewTemplate, fieldsContainer, fieldsTemplate, linkInput;
			if ( ! control.model.get( 'attachment_id' ) && ! control.model.get( 'url' ) ) {
				return;
			}

			previewContainer = control.$el.find( '.media-widget-preview' );
			previewTemplate = wp.template( 'wp-media-widget-image-preview' );
			previewContainer.html( previewTemplate( control.previewTemplateProps.toJSON() ) );
			previewContainer.addClass( 'populated' );

			linkInput = control.$el.find( '.link' );
			if ( ! linkInput.is( document.activeElement ) ) {
				fieldsContainer = control.$el.find( '.media-widget-fields' );
				fieldsTemplate = wp.template( 'wp-media-widget-image-fields' );
				fieldsContainer.html( fieldsTemplate( control.previewTemplateProps.toJSON() ) );
			}
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
				var mediaProps, linkType;

				// Update cached attachment object to avoid having to re-fetch. This also triggers re-rendering of preview.
				mediaProps = mediaFrame.state().attributes.image.toJSON();
				linkType = mediaProps.link;
				mediaProps.link = mediaProps.linkUrl;
				control.selectedAttachment.set( mediaProps );
				control.displaySettings.set( 'link', linkType );

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
		 * Get the instance props from the media selection frame.
		 *
		 * Prevent the image_title attribute from being initially set when adding an image from the media library.
		 *
		 * @param {wp.media.view.MediaFrame.Select} mediaFrame - Select frame.
		 * @returns {Object} Props.
		 */
		getModelPropsFromMediaFrame: function getModelPropsFromMediaFrame( mediaFrame ) {
			var control = this;
			return _.omit(
				component.MediaWidgetControl.prototype.getModelPropsFromMediaFrame.call( control, mediaFrame ),
				'image_title'
			);
		},

		/**
		 * Map model props to preview template props.
		 *
		 * @returns {Object} Preview template props.
		 */
		mapModelToPreviewTemplateProps: function mapModelToPreviewTemplateProps() {
			var control = this, previewTemplateProps, url;
			url = control.model.get( 'url' );
			previewTemplateProps = component.MediaWidgetControl.prototype.mapModelToPreviewTemplateProps.call( control );
			previewTemplateProps.currentFilename = url ? url.replace( /\?.*$/, '' ).replace( /^.+\//, '' ) : '';
			previewTemplateProps.link_url = control.model.get( 'link_url' );
			return previewTemplateProps;
		}
	});

	// Exports.
	component.controlConstructors.media_image = ImageWidgetControl;
	component.modelConstructors.media_image = ImageWidgetModel;

})( wp.mediaWidgets, jQuery );
