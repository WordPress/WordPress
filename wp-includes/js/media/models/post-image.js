/*globals Backbone */

/**
 * wp.media.model.PostImage
 *
 * An instance of an image that's been embedded into a post.
 *
 * Used in the embedded image attachment display settings modal - @see wp.media.view.MediaFrame.ImageDetails.
 *
 * @class
 * @augments Backbone.Model
 *
 * @param {int} [attributes]               Initial model attributes.
 * @param {int} [attributes.attachment_id] ID of the attachment.
 **/
var Attachment = require( './attachment' ),
	PostImage;

PostImage = Backbone.Model.extend({

	initialize: function( attributes ) {
		this.attachment = false;

		if ( attributes.attachment_id ) {
			this.attachment = Attachment.get( attributes.attachment_id );
			if ( this.attachment.get( 'url' ) ) {
				this.dfd = jQuery.Deferred();
				this.dfd.resolve();
			} else {
				this.dfd = this.attachment.fetch();
			}
			this.bindAttachmentListeners();
		}

		// keep url in sync with changes to the type of link
		this.on( 'change:link', this.updateLinkUrl, this );
		this.on( 'change:size', this.updateSize, this );

		this.setLinkTypeFromUrl();
		this.setAspectRatio();

		this.set( 'originalUrl', attributes.url );
	},

	bindAttachmentListeners: function() {
		this.listenTo( this.attachment, 'sync', this.setLinkTypeFromUrl );
		this.listenTo( this.attachment, 'sync', this.setAspectRatio );
		this.listenTo( this.attachment, 'change', this.updateSize );
	},

	changeAttachment: function( attachment, props ) {
		this.stopListening( this.attachment );
		this.attachment = attachment;
		this.bindAttachmentListeners();

		this.set( 'attachment_id', this.attachment.get( 'id' ) );
		this.set( 'caption', this.attachment.get( 'caption' ) );
		this.set( 'alt', this.attachment.get( 'alt' ) );
		this.set( 'size', props.get( 'size' ) );
		this.set( 'align', props.get( 'align' ) );
		this.set( 'link', props.get( 'link' ) );
		this.updateLinkUrl();
		this.updateSize();
	},

	setLinkTypeFromUrl: function() {
		var linkUrl = this.get( 'linkUrl' ),
			type;

		if ( ! linkUrl ) {
			this.set( 'link', 'none' );
			return;
		}

		// default to custom if there is a linkUrl
		type = 'custom';

		if ( this.attachment ) {
			if ( this.attachment.get( 'url' ) === linkUrl ) {
				type = 'file';
			} else if ( this.attachment.get( 'link' ) === linkUrl ) {
				type = 'post';
			}
		} else {
			if ( this.get( 'url' ) === linkUrl ) {
				type = 'file';
			}
		}

		this.set( 'link', type );
	},

	updateLinkUrl: function() {
		var link = this.get( 'link' ),
			url;

		switch( link ) {
			case 'file':
				if ( this.attachment ) {
					url = this.attachment.get( 'url' );
				} else {
					url = this.get( 'url' );
				}
				this.set( 'linkUrl', url );
				break;
			case 'post':
				this.set( 'linkUrl', this.attachment.get( 'link' ) );
				break;
			case 'none':
				this.set( 'linkUrl', '' );
				break;
		}
	},

	updateSize: function() {
		var size;

		if ( ! this.attachment ) {
			return;
		}

		if ( this.get( 'size' ) === 'custom' ) {
			this.set( 'width', this.get( 'customWidth' ) );
			this.set( 'height', this.get( 'customHeight' ) );
			this.set( 'url', this.get( 'originalUrl' ) );
			return;
		}

		size = this.attachment.get( 'sizes' )[ this.get( 'size' ) ];

		if ( ! size ) {
			return;
		}

		this.set( 'url', size.url );
		this.set( 'width', size.width );
		this.set( 'height', size.height );
	},

	setAspectRatio: function() {
		var full;

		if ( this.attachment && this.attachment.get( 'sizes' ) ) {
			full = this.attachment.get( 'sizes' ).full;

			if ( full ) {
				this.set( 'aspectRatio', full.width / full.height );
				return;
			}
		}

		this.set( 'aspectRatio', this.get( 'customWidth' ) / this.get( 'customHeight' ) );
	}
});

module.exports = PostImage;
